import 'dart:async';
import 'dart:math' as math;
import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/visitor_map_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/map_location_bottom_sheet.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/map_coordinate_service.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/map_pathfinder.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/map_route_utils.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/visitor_gps_service.dart';
import 'package:tripolizoo/shared/data/animal_group_repository.dart';
import 'package:tripolizoo/shared/domain/animal_group.dart';

enum _MapFilterMode { all, group, dining, service }

class _InteractiveMapScreenData {
  const _InteractiveMapScreenData({
    required this.map,
    required this.groups,
  });

  final VisitorMapData map;
  final List<AnimalGroup> groups;
}

class InteractiveMapScreen extends StatefulWidget {
  const InteractiveMapScreen({
    super.key,
    this.focusLocationId,
    this.autoNavigate = false,
  });

  final int? focusLocationId;
  final bool autoNavigate;

  @override
  State<InteractiveMapScreen> createState() => _InteractiveMapScreenState();
}

class _InteractiveMapScreenState extends State<InteractiveMapScreen> {
  final _repo = VisitorMapRepository();
  final _groupRepo = ApiAnimalGroupRepository();
  final _gpsService = VisitorGpsService();
  final _transformationController = TransformationController();
  final _searchController = TextEditingController();
  late Future<_InteractiveMapScreenData> _future;
  _MapFilterMode _filterMode = _MapFilterMode.all;
  String? _selectedGroup;
  String _searchQuery = '';
  VisitorMapLocation? _selectedLocation;
  VisitorMapData? _cachedMapData;
  StreamSubscription<VisitorGpsPosition>? _gpsSubscription;
  Offset? _userNormalized;
  List<Offset> _routePoints = const [];
  bool _isNavigating = false;
  bool _isInitialZoomSet = false;
  bool _focusApplied = false;
  bool _pickingLocation = false;
  VisitorMapLocation? _pendingNavigationDestination;
  int? _routeDistanceMeters;
  int? _routeWalkMinutes;
  List<Offset> _gpsSmoothingBuffer = [];
  int _outsideGeofenceStreak = 0;
  int _offRouteStreak = 0;
  BoxConstraints? _lastMapConstraints;
  double _lastImageW = 4516;
  double _lastImageH = 3374;
  double _lastMinScale = 1;

  static const _gpsTimeout = Duration(seconds: 10);
  static const _maxManualNodeDistancePx = 250.0;
  static const _maxGpsAccuracyMeters = 50.0;
  static const _gpsSmoothingWindow = 5;
  static const _offRouteThresholdMeters = 30.0;
  static const _offRouteStreakRequired = 3;

  MapRouteOptions get _routeOptions => const MapRouteOptions();

  @override
  void initState() {
    super.initState();
    _future = _load();
    _searchController.addListener(() {
      final query = _searchController.text.trim();
      if (query == _searchQuery) return;
      setState(() => _searchQuery = query);
    });
  }

  Future<_InteractiveMapScreenData> _load() async {
    final results = await Future.wait([
      _repo.getMap(),
      _groupRepo.fetchActive(),
    ]);

    return _InteractiveMapScreenData(
      map: results[0] as VisitorMapData,
      groups: results[1] as List<AnimalGroup>,
    );
  }

  @override
  void dispose() {
    _searchController.dispose();
    _gpsSubscription?.cancel();
    _transformationController.dispose();
    super.dispose();
  }

  List<String> _availableGroups(
    List<VisitorMapLocation> locations,
    List<AnimalGroup> catalog,
  ) {
    final present = locations
        .map((location) => location.animalGroup)
        .whereType<String>()
        .where((group) => group.isNotEmpty)
        .toSet();

    return orderedAnimalGroupNames(catalog, present);
  }

  List<VisitorMapLocation> _filtered(List<VisitorMapLocation> locations) {
    return locations.where((location) {
      if (_searchQuery.isNotEmpty) {
        final query = _searchQuery;
        final haystack = [
          location.name,
          location.animalName ?? '',
          location.description,
        ].join(' ');
        if (!haystack.contains(query)) return false;
      }

      return switch (_filterMode) {
        _MapFilterMode.all => true,
        _MapFilterMode.group => location.animalGroup == _selectedGroup,
        _MapFilterMode.dining => location.category == 'dining',
        _MapFilterMode.service => location.category == 'service',
      };
    }).toList();
  }

  void _selectGroup(String? group) {
    setState(() {
      _filterMode = group == null ? _MapFilterMode.all : _MapFilterMode.group;
      _selectedGroup = group;
      if (_selectedLocation != null && group != null) {
        if (_selectedLocation!.animalGroup != group) {
          _closeSheet();
        }
      }
    });
  }

  void _selectUtilityFilter(_MapFilterMode mode) {
    setState(() {
      _filterMode = _filterMode == mode ? _MapFilterMode.all : mode;
      _selectedGroup = null;
      if (_selectedLocation != null) {
        final location = _selectedLocation!;
        final visible = switch (_filterMode) {
          _MapFilterMode.all => true,
          _MapFilterMode.group => location.animalGroup == _selectedGroup,
          _MapFilterMode.dining => location.category == 'dining',
          _MapFilterMode.service => location.category == 'service',
        };
        if (!visible) _closeSheet();
      }
    });
  }

  Future<Offset?> _resolveGpsPosition(VisitorMapData data) async {
    final allowed = await _gpsService.ensurePermission();
    if (!allowed) return null;

    VisitorGpsPosition? gps;
    try {
      gps = await _gpsService.currentPosition().timeout(_gpsTimeout);
    } catch (_) {
      return null;
    }
    if (gps == null) return null;

    final locator = data.gpsLocator;
    if (locator.hasCalibration) {
      if (!locator.isInsideBoundary(gps.latitude, gps.longitude)) {
        _outsideGeofenceStreak++;
        if (_outsideGeofenceStreak >= 3 && mounted) {
          _showOutsideGeofenceDialog();
        }
        return null;
      }
      _outsideGeofenceStreak = 0;

      final calibrated = locator.gpsToNormalized(gps.latitude, gps.longitude);
      if (calibrated == null) {
        return null;
      }

      if ((gps.accuracyMeters ?? 0) > _maxGpsAccuracyMeters) {
        if (mounted) _showLowAccuracyDialog();
        return null;
      }

      return _smoothGpsPosition(calibrated);
    }

    final coordinates = MapCoordinateService(bounds: data.bounds);
    return _smoothGpsPosition(
      coordinates.gpsToNormalized(gps.latitude, gps.longitude),
    );
  }

  Offset _smoothGpsPosition(Offset raw) {
    _gpsSmoothingBuffer = [..._gpsSmoothingBuffer, raw];
    if (_gpsSmoothingBuffer.length > _gpsSmoothingWindow) {
      _gpsSmoothingBuffer = _gpsSmoothingBuffer.sublist(
        _gpsSmoothingBuffer.length - _gpsSmoothingWindow,
      );
    }

    var x = 0.0;
    var y = 0.0;
    for (final point in _gpsSmoothingBuffer) {
      x += point.dx;
      y += point.dy;
    }
    final count = _gpsSmoothingBuffer.length;
    return Offset(x / count, y / count);
  }

  void _showGpsFailureSnack({bool offerManual = true}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: const Text(
          'لم نتمكن من تحديد موقعك تلقائياً. حدّد موقعك يدوياً للوصول إلى وجهتك',
        ),
        action: offerManual
            ? SnackBarAction(
                label: 'حدد يدوياً',
                onPressed: _enterPickLocationMode,
              )
            : null,
      ),
    );
  }

  Future<void> _showOutsideGeofenceDialog() async {
    if (!mounted) return;
    await showDialog<void>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('أنت لست داخل الحديقة حالياً'),
        content: const Text(
          'لا يمكننا تحديد موقعك تلقائياً بدقة. حدّد موقعك يدوياً على الخريطة للوصول إلى وجهتك.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('إغلاق'),
          ),
          FilledButton(
            onPressed: () {
              Navigator.pop(context);
              _enterPickLocationMode();
            },
            child: const Text('حدد موقعي يدوياً'),
          ),
        ],
      ),
    );
  }

  Future<void> _showLowAccuracyDialog() async {
    if (!mounted) return;
    await showDialog<void>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('دقة الموقع ضعيفة'),
        content: const Text(
          'دقة موقعك الحالية ضعيفة. تأكد أنك بمنطقة مفتوحة، أو حدّد موقعك يدوياً.',
        ),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              _centerOnUser();
            },
            child: const Text('إعادة المحاولة'),
          ),
          FilledButton(
            onPressed: () {
              Navigator.pop(context);
              _enterPickLocationMode();
            },
            child: const Text('حدد يدوياً'),
          ),
        ],
      ),
    );
  }

  MapPathNode? _destinationNode(
    MapNavigationGraph graph,
    VisitorMapLocation destination,
  ) {
    // Route to the nearest pathway node, not the location pin itself.
    if (destination.nearestNodeId != null) {
      final linked = graph.nodeById(destination.nearestNodeId!);
      if (linked != null) return linked;
    }

    if (destination.nearestNodeKey != null) {
      final byKey = graph.nodeByKey(destination.nearestNodeKey);
      if (byKey != null) return byKey;
    }

    return graph.nodeForLocation(destination.id) ??
        graph.nearestNode(Offset(destination.x, destination.y));
  }

  MapPathNode? _startNodeForUser(
    MapNavigationGraph graph,
    Offset userPosition,
  ) {
    return graph.nearestNode(userPosition);
  }

  Future<void> _centerOnUser() async {
    final data = _cachedMapData;
    final constraints = _lastMapConstraints;
    if (data == null || constraints == null) return;

    final userPosition = await _resolveGpsPosition(data);
    if (!mounted) return;

    if (userPosition == null) {
      _showGpsFailureSnack();
      return;
    }

    setState(() => _userNormalized = userPosition);
    _centerOnPoint(userPosition, constraints, scaleMultiplier: 2.4);
  }

  void _centerOnPoint(
    Offset point,
    BoxConstraints constraints, {
    double scaleMultiplier = 2.8,
  }) {
    final targetScale = (_lastMinScale * scaleMultiplier)
        .clamp(_lastMinScale, _lastMinScale * 5);
    final pinX = _lastImageW * point.dx;
    final pinY = _lastImageH * point.dy;
    final dx = constraints.maxWidth / 2 - pinX * targetScale;
    final dy = constraints.maxHeight / 2 - pinY * targetScale;

    _transformationController.value = Matrix4.identity()
      ..translate(dx, dy)
      ..scale(targetScale);
  }

  void _reload() {
    _gpsSubscription?.cancel();
    setState(() {
      _selectedLocation = null;
      _userNormalized = null;
      _routePoints = const [];
      _routeDistanceMeters = null;
      _routeWalkMinutes = null;
      _pendingNavigationDestination = null;
      _gpsSmoothingBuffer = [];
      _outsideGeofenceStreak = 0;
      _offRouteStreak = 0;
      _isNavigating = false;
      _focusApplied = false;
      _isInitialZoomSet = false;
      _cachedMapData = null;
      _future = _load();
    });
  }

  void _applyNavigationRoute({
    required Offset userPosition,
    required MapPathResult pathResult,
    required VisitorMapLocation destination,
    required VisitorMapData data,
    bool fitView = false,
    bool startGpsWatch = false,
  }) {
    final path = pathResult.offsets;

    // Draw the route only along pathway nodes/geometry. Do not connect a long
    // straight segment from an off-path user/GPS point to the network — that
    // is what causes misleading lines across the map (see guide §7).
    final List<Offset> routePoints = path.isNotEmpty ? path : [userPosition];

    setState(() {
      _isNavigating = true;
      _selectedLocation = destination;
      _pendingNavigationDestination = null;
      _userNormalized = userPosition;
      _routePoints = routePoints;
      _routeDistanceMeters = pathResult.totalDistanceMeters;
      _routeWalkMinutes = pathResult.estimatedWalkMinutes;
      _offRouteStreak = 0;
    });

    if (fitView) {
      _fitRouteInView();
    }

    if (!startGpsWatch) return;

    _gpsSubscription?.cancel();
    try {
      _gpsSubscription = _gpsService.watchPosition().listen(
        (gps) => _onGpsUpdate(gps, destination, data),
        onError: (_) {},
      );
    } catch (_) {
      // Live tracking unavailable — static route is already shown.
    }
  }

  void _recalculateRouteFromPosition({
    required Offset userPosition,
    required VisitorMapLocation destination,
    required VisitorMapData data,
  }) {
    final graph = data.navigation;
    final destinationNode = _destinationNode(graph, destination);
    if (destinationNode == null) return;

    final startNode = _startNodeForUser(graph, userPosition);
    if (startNode == null) return;

    if (startNode.id == destinationNode.id) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('أنت بالفعل عند هذه الوجهة')),
      );
      return;
    }

    final pathResult = graph.shortestPathResult(
      startNode.id,
      destinationNode.id,
      options: _routeOptions,
    );
    if (pathResult == null) return;

    _applyNavigationRoute(
      userPosition: userPosition,
      pathResult: pathResult,
      destination: destination,
      data: data,
      fitView: true,
    );

    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('تم تحديث المسار بناءً على موقعك الحالي')),
    );
  }

  Future<void> _startNavigation(VisitorMapLocation destination) async {
    final data = _cachedMapData;
    if (data == null) return;

    if (data.navigation.nodes.isEmpty) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('شبكة المسارات غير متوفرة حالياً')),
      );
      return;
    }

    final graph = data.navigation;

    var userPosition = _userNormalized;
    if (userPosition == null) {
      userPosition = await _resolveGpsPosition(data);
    }

    if (!mounted) return;

    if (userPosition == null) {
      setState(() {
        _selectedLocation = destination;
        _pendingNavigationDestination = destination;
      });
      _enterPickLocationMode();
      _showGpsFailureSnack();
      return;
    }

    final destinationNode = _destinationNode(graph, destination);
    if (destinationNode == null) return;

    final startNode = _startNodeForUser(graph, userPosition);
    if (startNode == null) return;

    if (startNode.id == destinationNode.id) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('أنت بالفعل عند هذه الوجهة')),
      );
      return;
    }

    final pathResult = graph.shortestPathResult(
      startNode.id,
      destinationNode.id,
      options: _routeOptions,
    );
    if (pathResult == null || pathResult.offsets.isEmpty) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            'تعذّر إيجاد مسار لهذه الوجهة حالياً، قد يكون الممر مغلقاً للصيانة',
          ),
        ),
      );
      return;
    }

    _applyNavigationRoute(
      userPosition: userPosition,
      pathResult: pathResult,
      destination: destination,
      data: data,
      fitView: true,
      startGpsWatch: true,
    );
  }

  void _onGpsUpdate(
    VisitorGpsPosition gps,
    VisitorMapLocation destination,
    VisitorMapData data,
  ) {
    Offset? userPosition;

    if (data.gpsLocator.hasCalibration) {
      if (!data.gpsLocator.isInsideBoundary(gps.latitude, gps.longitude)) {
        _outsideGeofenceStreak++;
        return;
      }
      _outsideGeofenceStreak = 0;
      userPosition =
          data.gpsLocator.gpsToNormalized(gps.latitude, gps.longitude);
    } else {
      final coordinates = MapCoordinateService(bounds: data.bounds);
      userPosition =
          coordinates.gpsToNormalized(gps.latitude, gps.longitude);
    }

    if (userPosition == null) return;
    userPosition = _smoothGpsPosition(userPosition);

    final distanceFromRoute = MapRouteUtils.perpendicularDistanceMeters(
      userPosition,
      _routePoints,
      imageWidth: data.bounds.imageWidth,
      imageHeight: data.bounds.imageHeight,
    );

    if (distanceFromRoute > _offRouteThresholdMeters) {
      _offRouteStreak++;
    } else {
      _offRouteStreak = 0;
    }

    if (!mounted) return;

    if (_offRouteStreak >= _offRouteStreakRequired) {
      _recalculateRouteFromPosition(
        userPosition: userPosition,
        destination: destination,
        data: data,
      );
      return;
    }

    setState(() => _userNormalized = userPosition);
  }

  void _fitRouteInView({bool animate = true}) {
    final constraints = _lastMapConstraints;
    if (constraints == null || _routePoints.length < 2) return;

    final points = [
      ..._routePoints,
      if (_selectedLocation != null)
        Offset(_selectedLocation!.x, _selectedLocation!.y),
    ];

    var minX = points.first.dx;
    var maxX = points.first.dx;
    var minY = points.first.dy;
    var maxY = points.first.dy;

    for (final point in points.skip(1)) {
      minX = math.min(minX, point.dx);
      maxX = math.max(maxX, point.dx);
      minY = math.min(minY, point.dy);
      maxY = math.max(maxY, point.dy);
    }

    const padding = 0.1;
    minX = (minX - padding).clamp(0.0, 1.0);
    maxX = (maxX + padding).clamp(0.0, 1.0);
    minY = (minY - padding).clamp(0.0, 1.0);
    maxY = (maxY + padding).clamp(0.0, 1.0);

    final boxW = math.max((maxX - minX) * _lastImageW, 120.0);
    final boxH = math.max((maxY - minY) * _lastImageH, 120.0);
    final centerX = ((minX + maxX) / 2) * _lastImageW;
    final centerY = ((minY + maxY) / 2) * _lastImageH;

    final scaleX = constraints.maxWidth / boxW;
    final scaleY = (constraints.maxHeight * 0.62) / boxH;
    final targetScale = math.min(scaleX, scaleY).clamp(
      _lastMinScale,
      _lastMinScale * 4.5,
    );

    final dx = constraints.maxWidth / 2 - centerX * targetScale;
    final dy = (constraints.maxHeight * 0.38) - centerY * targetScale;

    final end = Matrix4.identity()
      ..translate(dx, dy)
      ..scale(targetScale);

    if (!animate) {
      _transformationController.value = end;
      return;
    }

    _transformationController.value = end;
  }

  void _closeSheet() {
    _gpsSubscription?.cancel();
    setState(() {
      _selectedLocation = null;
      _isNavigating = false;
      _routePoints = const [];
      _routeDistanceMeters = null;
      _routeWalkMinutes = null;
      _userNormalized = null;
      _pickingLocation = false;
      _pendingNavigationDestination = null;
    });
  }

  /// Enters manual location-pick mode: the next map tap sets the user position.
  void _enterPickLocationMode() {
    setState(() {
      _pickingLocation = true;
      _isNavigating = false;
      _routePoints = const [];
      _routeDistanceMeters = null;
      _routeWalkMinutes = null;
    });
  }

  /// Called when the user taps the map while [_pickingLocation] is true.
  Future<void> _onMapTap(double imageX, double imageY) async {
    if (!_pickingLocation) return;
    final data = _cachedMapData;
    if (data == null) return;

    final imageW = data.bounds.imageWidth;
    final imageH = data.bounds.imageHeight;
    if (imageX < 0 ||
        imageY < 0 ||
        imageX > imageW ||
        imageY > imageH) {
      return;
    }

    final graph = data.navigation;
    final locator = data.gpsLocator;
    final nodePixels = graph.nodes
        .map((node) => (x: node.x, y: node.y))
        .toList(growable: false);
    final nearestDistance = locator.pixelDistanceToNearestNode(
      tapPixelX: imageX,
      tapPixelY: imageY,
      nodes: nodePixels,
      imageWidth: imageW,
      imageHeight: imageH,
    );

    if (nearestDistance > _maxManualNodeDistancePx) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'هذا المكان بعيد عن أي ممر معروف. حاول الضغط بالقرب من أحد الممرات',
          ),
        ),
      );
      return;
    }

    final tapNormalized = Offset(
      (imageX / imageW).clamp(0.0, 1.0),
      (imageY / imageH).clamp(0.0, 1.0),
    );
    final nearestNode = graph.nearestNode(tapNormalized);
    if (nearestNode == null) return;

    final snapped = nearestNode.position;

    setState(() {
      _userNormalized = snapped;
      _pickingLocation = false;
    });

    if (!mounted) return;

    final confirmed = await showModalBottomSheet<bool>(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (_) => const _LocationConfirmSheet(),
    );

    if (!mounted) return;

    if (confirmed == true) {
      final destination =
          _pendingNavigationDestination ?? _selectedLocation;
      if (destination != null) {
        _startNavigation(destination);
      }
    } else {
      setState(() {
        _userNormalized = null;
        _pickingLocation = true;
      });
    }
  }

  void _applyFocus(
    List<VisitorMapLocation> locations,
    BoxConstraints constraints,
    double imageW,
    double imageH,
    double minScale,
  ) {
    VisitorMapLocation? match;
    for (final location in locations) {
      if (location.id == widget.focusLocationId) {
        match = location;
        break;
      }
    }

    if (match == null) {
      final dx = (constraints.maxWidth - imageW * minScale) / 2;
      final dy = (constraints.maxHeight - imageH * minScale) / 2;
      _transformationController.value = Matrix4.identity()
        ..translate(dx, dy)
        ..scale(minScale);
      return;
    }

    _focusApplied = true;
    final targetScale = minScale * 2.8;
    final pinX = imageW * match.x;
    final pinY = imageH * match.y;
    final dx = constraints.maxWidth / 2 - pinX * targetScale;
    final dy = constraints.maxHeight / 2 - pinY * targetScale;

    setState(() {
      _selectedLocation = match;
    });

    _transformationController.value = Matrix4.identity()
      ..translate(dx, dy)
      ..scale(targetScale);

    if (widget.autoNavigate) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        _startNavigation(match!);
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    final isArabic = Localizations.localeOf(context).languageCode == 'ar';

    return Directionality(
      textDirection: isArabic ? TextDirection.rtl : TextDirection.ltr,
      child: Scaffold(
        backgroundColor: const Color(0xFF1a2e1a),
        body: FutureBuilder<_InteractiveMapScreenData>(
          future: _future,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(
                child: CircularProgressIndicator(color: Color(0xFF1B4332)),
              );
            }

            if (snapshot.hasError || !snapshot.hasData) {
              return _MapErrorView(
                onBack: () =>
                    context.canPop() ? context.pop() : context.go('/home'),
                onRetry: _reload,
              );
            }

            final screenData = snapshot.data!;
            final data = screenData.map;
            _cachedMapData = data;
            final groups = _availableGroups(data.locations, screenData.groups);
            final locations = _filtered(data.locations);
            final imageW = data.bounds.imageWidth;
            final imageH = data.bounds.imageHeight;

            return Stack(
              children: [
                // ── Map + Pins ─────────────────────────────────────────
                Positioned.fill(
                  child: LayoutBuilder(
                    builder: (context, constraints) {
                      _lastMapConstraints = constraints;
                      _lastImageW = imageW;
                      _lastImageH = imageH;
                      final minScale = math.max(
                          constraints.maxWidth / imageW,
                          constraints.maxHeight / imageH);
                      _lastMinScale = minScale;

                      if (!_isInitialZoomSet) {
                        _isInitialZoomSet = true;
                        final dx =
                            (constraints.maxWidth - imageW * minScale) / 2;
                        final dy =
                            (constraints.maxHeight - imageH * minScale) / 2;

                        WidgetsBinding.instance.addPostFrameCallback((_) {
                          if (!_focusApplied &&
                              widget.focusLocationId != null) {
                            _applyFocus(
                              data.locations,
                              constraints,
                              imageW,
                              imageH,
                              minScale,
                            );
                          } else {
                            _transformationController.value =
                                Matrix4.identity()
                                  ..translate(dx, dy)
                                  ..scale(minScale);
                          }
                        });
                      }

                      return InteractiveViewer(
                        transformationController: _transformationController,
                        constrained: false,
                        minScale: minScale,
                        maxScale: minScale * 5,
                        boundaryMargin: EdgeInsets.zero,
                        child: SizedBox(
                          width: imageW,
                          height: imageH,
                          child: Stack(
                            children: [
                              _MapImage(
                                imageUrl: data.imageUrl,
                                width: imageW,
                                height: imageH,
                              ),
                              // Location-pick tap catcher (below pins so it
                              // activates only when no pin is tapped)
                              if (_pickingLocation)
                                Positioned.fill(
                                  child: GestureDetector(
                                    behavior: HitTestBehavior.translucent,
                                    onTapUp: (d) => _onMapTap(
                                      d.localPosition.dx,
                                      d.localPosition.dy,
                                    ),
                                    child: const ColoredBox(
                                      color: Color(0x22000000),
                                    ),
                                  ),
                                ),
                              // Route is drawn BELOW pins so pins stay tappable
                              if (_routePoints.isNotEmpty)
                                Positioned.fill(
                                  child: AnimatedBuilder(
                                    animation: _transformationController,
                                    builder: (context, _) {
                                      final scale = _transformationController
                                          .value
                                          .getMaxScaleOnAxis();
                                      return CustomPaint(
                                        painter: _MapNavigationPathPainter(
                                          points: _routePoints,
                                          scale: scale,
                                        ),
                                      );
                                    },
                                  ),
                                ),
                              ...locations.map((location) {
                                final isSelected =
                                    _selectedLocation?.id == location.id;
                                const pinSize = 32.0;
                                const selectedPinSize = 40.0;
                                final activePinSize =
                                    isSelected ? selectedPinSize : pinSize;
                                final pinWidth = activePinSize + 4;
                                return Positioned(
                                  left: imageW * location.x,
                                  top: imageH * location.y,
                                  child: Transform.translate(
                                    offset: Offset(
                                      -(pinWidth / 2),
                                      -(activePinSize / 2),
                                    ),
                                    child: AnimatedBuilder(
                                      animation: _transformationController,
                                      builder: (context, child) {
                                        final scale = _transformationController
                                            .value
                                            .getMaxScaleOnAxis();
                                        return Transform.scale(
                                          scale: 1.0 / scale,
                                          alignment: Alignment.center,
                                          child: child,
                                        );
                                      },
                                      child: _MapPin(
                                        location: location,
                                        selected: isSelected,
                                        onTap: () => setState(
                                          () => _selectedLocation = location,
                                        ),
                                      ),
                                    ),
                                  ),
                                );
                              }),
                              if (_userNormalized != null)
                                Positioned(
                                  left: imageW * _userNormalized!.dx - 14,
                                  top: imageH * _userNormalized!.dy - 14,
                                  child: AnimatedBuilder(
                                    animation: _transformationController,
                                    builder: (context, child) {
                                      final scale = _transformationController
                                          .value
                                          .getMaxScaleOnAxis();
                                      return Transform.scale(
                                        scale: 1.0 / scale,
                                        child: child,
                                      );
                                    },
                                    child: Container(
                                      width: 28,
                                      height: 28,
                                      decoration: BoxDecoration(
                                        color: const Color(0xFF2563EB),
                                        shape: BoxShape.circle,
                                        border: Border.all(
                                          color: Colors.white,
                                          width: 3,
                                        ),
                                        boxShadow: [
                                          BoxShadow(
                                            color: const Color(0xFF2563EB)
                                                .withValues(alpha: 0.45),
                                            blurRadius: 12,
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),

                // ── Top overlay: Search bar + group filters ────────────
                Positioned(
                  top: topPad + 12,
                  left: 16,
                  right: 16,
                  child: Column(
                    children: [
                      _TopBar(
                        controller: _searchController,
                        onBack: () => context.canPop()
                            ? context.pop()
                            : context.go('/home'),
                      ),
                      const SizedBox(height: 10),
                      _GroupFilters(
                        groups: groups,
                        filterMode: _filterMode,
                        selectedGroup: _selectedGroup,
                        onAllSelected: () => _selectGroup(null),
                        onGroupSelected: _selectGroup,
                      ),
                    ],
                  ),
                ),

                // ── Location-pick hint banner ──────────────────────────
                if (_pickingLocation)
                  Positioned(
                    top: topPad + 80,
                    left: 24,
                    right: 24,
                    child: _LocationPickBanner(
                      onCancel: () =>
                          setState(() => _pickingLocation = false),
                    ),
                  ),

                // ── Bottom-right utility buttons ───────────────────────
                Positioned(
                  right: 12,
                  bottom: bottomPad + (_selectedLocation != null ? 128 : 88),
                  child: _MapUtilityButtons(
                    activeMode: _filterMode,
                    onGps: _centerOnUser,
                    onManual: _enterPickLocationMode,
                    manualActive: _pickingLocation,
                    onDining: () => _selectUtilityFilter(_MapFilterMode.dining),
                    onService: () => _selectUtilityFilter(_MapFilterMode.service),
                  ),
                ),

                // ── Bottom card ────────────────────────────────────────
                if (_selectedLocation != null)
                  Positioned(
                    left: 0,
                    right: 0,
                    bottom: bottomPad + 20,
                    child: MapLocationBottomSheet(
                      location: _selectedLocation!,
                      isNavigating: _isNavigating,
                      routeDistanceMeters: _routeDistanceMeters,
                      routeWalkMinutes: _routeWalkMinutes,
                      onClose: _closeSheet,
                      onShowRoute: () => _startNavigation(_selectedLocation!),
                    ),
                  ),
              ],
            );
          },
        ),
      ),
    );
  }
}

// ── Map image ────────────────────────────────────────────────────────────────

class _MapImage extends StatelessWidget {
  const _MapImage({
    required this.imageUrl,
    required this.width,
    required this.height,
  });

  final String imageUrl;
  final double width;
  final double height;

  @override
  Widget build(BuildContext context) {
    Widget fallback() => Image.asset(
          'assets/images/map.PNG',
          width: width,
          height: height,
          fit: BoxFit.fill,
          alignment: Alignment.topLeft,
        );

    if (imageUrl.isEmpty) return fallback();

    return Image.network(
      imageUrl,
      width: width,
      height: height,
      fit: BoxFit.fill,
      alignment: Alignment.topLeft,
      errorBuilder: (_, __, ___) => fallback(),
    );
  }
}

// ── Top bar ──────────────────────────────────────────────────────────────────

class _TopBar extends StatelessWidget {
  const _TopBar({
    required this.controller,
    required this.onBack,
  });

  final TextEditingController controller;
  final VoidCallback onBack;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 52,
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.96),
        borderRadius: BorderRadius.circular(26),
        border: Border.all(color: Colors.black.withValues(alpha: 0.06)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.12),
            blurRadius: 20,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      padding: const EdgeInsets.symmetric(horizontal: 6),
      child: Row(
        children: [
          IconButton(
            icon: const Icon(Icons.arrow_forward_ios_rounded, size: 18),
            color: const Color(0xFF1B4332),
            onPressed: onBack,
          ),
          Expanded(
            child: TextField(
              controller: controller,
              textDirection: TextDirection.rtl,
              decoration: InputDecoration(
                hintText: 'بحث',
                hintStyle: GoogleFonts.cairo(
                  color: const Color(0xFF9CA3AF),
                  fontWeight: FontWeight.w600,
                  fontSize: 15,
                ),
                border: InputBorder.none,
                isDense: true,
                contentPadding: const EdgeInsets.symmetric(vertical: 12),
              ),
              style: GoogleFonts.cairo(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF1F2937),
              ),
            ),
          ),
          const Icon(Icons.search_rounded, color: Color(0xFF9CA3AF), size: 22),
          const SizedBox(width: 8),
        ],
      ),
    );
  }
}

class _GroupFilters extends StatelessWidget {
  const _GroupFilters({
    required this.groups,
    required this.filterMode,
    required this.selectedGroup,
    required this.onAllSelected,
    required this.onGroupSelected,
  });

  final List<String> groups;
  final _MapFilterMode filterMode;
  final String? selectedGroup;
  final VoidCallback onAllSelected;
  final ValueChanged<String> onGroupSelected;

  @override
  Widget build(BuildContext context) {
    final items = ['الكل', ...groups];

    return SizedBox(
      height: 40,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (context, index) {
          final label = items[index];
          final isAll = index == 0;
          final active = isAll
              ? filterMode == _MapFilterMode.all
              : filterMode == _MapFilterMode.group && selectedGroup == label;

          return GestureDetector(
            onTap: () => isAll ? onAllSelected() : onGroupSelected(label),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
              decoration: BoxDecoration(
                color: active
                    ? const Color(0xFF1B4332)
                    : Colors.white.withValues(alpha: 0.92),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: active
                      ? Colors.transparent
                      : Colors.black.withValues(alpha: 0.08),
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.10),
                    blurRadius: 8,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              alignment: Alignment.center,
              child: Text(
                label,
                style: GoogleFonts.cairo(
                  color: active ? Colors.white : const Color(0xFF374151),
                  fontSize: 12.5,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

class _MapUtilityButtons extends StatelessWidget {
  const _MapUtilityButtons({
    required this.activeMode,
    required this.onGps,
    required this.onManual,
    required this.manualActive,
    required this.onDining,
    required this.onService,
  });

  final _MapFilterMode activeMode;
  final VoidCallback onGps;
  final VoidCallback onManual;
  final bool manualActive;
  final VoidCallback onDining;
  final VoidCallback onService;

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        _UtilityButton(
          icon: Icons.restaurant_rounded,
          active: activeMode == _MapFilterMode.dining,
          onTap: onDining,
        ),
        const SizedBox(height: 10),
        _UtilityButton(
          icon: Icons.wc_rounded,
          active: activeMode == _MapFilterMode.service,
          onTap: onService,
        ),
        const SizedBox(height: 10),
        _UtilityButton(
          icon: Icons.touch_app_rounded,
          active: manualActive,
          onTap: onManual,
        ),
        const SizedBox(height: 10),
        _UtilityButton(
          icon: Icons.my_location_rounded,
          active: false,
          onTap: onGps,
        ),
      ],
    );
  }
}

class _UtilityButton extends StatelessWidget {
  const _UtilityButton({
    required this.icon,
    required this.active,
    required this.onTap,
  });

  final IconData icon;
  final bool active;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: active ? const Color(0xFF1B4332) : const Color(0xFF111827),
      borderRadius: BorderRadius.circular(10),
      elevation: 6,
      shadowColor: Colors.black.withValues(alpha: 0.28),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(10),
        child: SizedBox(
          width: 46,
          height: 46,
          child: Icon(
            icon,
            color: Colors.white,
            size: 22,
          ),
        ),
      ),
    );
  }
}

// ── Map navigation path ───────────────────────────────────────────────────────

class _MapPin extends StatefulWidget {
  const _MapPin({
    required this.location,
    required this.selected,
    required this.onTap,
  });

  final VisitorMapLocation location;
  final bool selected;
  final VoidCallback onTap;

  @override
  State<_MapPin> createState() => _MapPinState();
}

class _MapPinState extends State<_MapPin>
    with SingleTickerProviderStateMixin {
  late final AnimationController _pulse;

  @override
  void initState() {
    super.initState();
    _pulse = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _pulse.dispose();
    super.dispose();
  }

  Color get _bgColor {
    return switch (widget.location.category) {
      'enclosure' => const Color(0xFF1B4332),
      'dining' => const Color(0xFFB45309),
      'service' => const Color(0xFF1D4ED8),
      _ => const Color(0xFF1B4332),
    };
  }

  IconData get _icon {
    return switch (widget.location.category) {
      'dining' => Icons.restaurant_rounded,
      'service' => Icons.wc_rounded,
      _ => Icons.pets_rounded,
    };
  }

  @override
  Widget build(BuildContext context) {
    final hasPhoto = widget.location.animalPhotoUrl != null &&
        widget.location.animalPhotoUrl!.isNotEmpty;
    final isSelected = widget.selected;
    final pinSize = isSelected ? 40.0 : 32.0;

    return GestureDetector(
      onTap: widget.onTap,
      behavior: HitTestBehavior.opaque,
      child: SizedBox(
        width: pinSize + 4,
        height: pinSize + 20, // extra room for name label
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // ── Pulse ring ───────────────────────────────────────────
            AnimatedBuilder(
              animation: _pulse,
              builder: (context, child) {
                final scale = isSelected
                    ? 1.0 + 0.08 * _pulse.value
                    : 1.0;
                return Transform.scale(
                  scale: scale,
                  child: child,
                );
              },
              child: Container(
                width: pinSize,
                height: pinSize,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: _bgColor,
                  border: Border.all(
                    color: Colors.white,
                    width: isSelected ? 2.5 : 1.5,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: _bgColor.withValues(alpha: isSelected ? 0.55 : 0.40),
                      blurRadius: isSelected ? 14 : 8,
                      offset: const Offset(0, 4),
                    ),
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.15),
                      blurRadius: 4,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: ClipOval(
                  child: hasPhoto
                      ? Image.network(
                          widget.location.animalPhotoUrl!,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) => _PinIcon(
                              icon: _icon, size: isSelected ? 26 : 22),
                        )
                      : _PinIcon(icon: _icon, size: isSelected ? 26 : 22),
                ),
              ),
            ),
            // ── Name label ─────────────────────────────────────────
            const SizedBox(height: 3),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.92),
                borderRadius: BorderRadius.circular(8),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.15),
                    blurRadius: 4,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Text(
                widget.location.name,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                textDirection: TextDirection.rtl,
                style: GoogleFonts.cairo(
                  fontSize: 9,
                  fontWeight: FontWeight.w800,
                  color: _bgColor,
                  height: 1.1,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _PinIcon extends StatelessWidget {
  const _PinIcon({required this.icon, required this.size});
  final IconData icon;
  final double size;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Icon(icon, color: Colors.white, size: size),
    );
  }
}

// ── Error view ───────────────────────────────────────────────────────────────

/// Draws the navigation route as a clean, scale-aware polyline.
///
/// The stroke is expressed in target screen pixels and divided by [scale]
/// so it appears the same visual width at any zoom level — exactly like
/// professional map apps (Google Maps, Apple Maps).
class _MapNavigationPathPainter extends CustomPainter {
  const _MapNavigationPathPainter({
    required this.points,
    required this.scale,
  });

  final List<Offset> points;

  /// Current InteractiveViewer scale (from TransformationController).
  final double scale;

  // Target visual widths in screen pixels
  static const double _routeScreenPx = 5.0;
  static const double _casingScreenPx = 9.0;

  static const Color _routeColor = Color(0xFF1A6EFF); // Google-Maps-style blue
  static const Color _casingColor = Color(0xFFFFFFFF);

  @override
  void paint(Canvas canvas, Size size) {
    if (points.length < 2) return;

    // Convert target screen pixels → canvas pixels at current zoom
    final safeScale = math.max(scale, 0.01);
    final routeW = (_routeScreenPx / safeScale).clamp(2.0, 80.0);
    final casingW = (_casingScreenPx / safeScale).clamp(4.0, 140.0);

    // Build the polyline path in canvas (image) coordinates
    final path = Path();
    path.moveTo(
      points.first.dx * size.width,
      points.first.dy * size.height,
    );
    for (var i = 1; i < points.length; i++) {
      path.lineTo(
        points[i].dx * size.width,
        points[i].dy * size.height,
      );
    }

    // 1. White casing (drawn first, behind the colour)
    canvas.drawPath(
      path,
      Paint()
        ..color = _casingColor
        ..strokeWidth = casingW
        ..style = PaintingStyle.stroke
        ..strokeCap = StrokeCap.round
        ..strokeJoin = StrokeJoin.round,
    );

    // 2. Coloured route line
    canvas.drawPath(
      path,
      Paint()
        ..color = _routeColor
        ..strokeWidth = routeW
        ..style = PaintingStyle.stroke
        ..strokeCap = StrokeCap.round
        ..strokeJoin = StrokeJoin.round,
    );
  }

  @override
  bool shouldRepaint(covariant _MapNavigationPathPainter old) =>
      old.points != points || old.scale != scale;
}

// ── Location pick banner ──────────────────────────────────────────────────────

class _LocationPickBanner extends StatelessWidget {
  const _LocationPickBanner({required this.onCancel});
  final VoidCallback onCancel;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: const Color(0xFF1B4332),
          borderRadius: BorderRadius.circular(14),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.30),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            const Icon(Icons.touch_app_rounded, color: Colors.white, size: 20),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                'اضغط على موقعك في الخريطة',
                style: GoogleFonts.cairo(
                  color: Colors.white,
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
            GestureDetector(
              onTap: onCancel,
              child: const Icon(Icons.close_rounded,
                  color: Colors.white70, size: 20),
            ),
          ],
        ),
      ),
    );
  }
}

// ── Location confirm sheet ────────────────────────────────────────────────────

class _LocationConfirmSheet extends StatelessWidget {
  const _LocationConfirmSheet();

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 0, 16, 24),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.14),
            blurRadius: 20,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 36,
            height: 4,
            decoration: BoxDecoration(
              color: Colors.grey.shade300,
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(height: 16),
          const Icon(Icons.location_on_rounded,
              color: Color(0xFF1B4332), size: 36),
          const SizedBox(height: 10),
          Text(
            'تم تحديد موقعك',
            style: GoogleFonts.cairo(
              fontSize: 17,
              fontWeight: FontWeight.w900,
              color: const Color(0xFF172317),
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'هل هذا الموقع الصحيح؟',
            style: GoogleFonts.cairo(
              fontSize: 13,
              color: const Color(0xFF5F6B5F),
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 20),
          Row(
            children: [
              Expanded(
                child: OutlinedButton(
                  onPressed: () => Navigator.of(context).pop(false),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: Color(0xFF1B4332)),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10)),
                    padding: const EdgeInsets.symmetric(vertical: 12),
                  ),
                  child: Text(
                    'إعادة التحديد',
                    style: GoogleFonts.cairo(
                      color: const Color(0xFF1B4332),
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: ElevatedButton(
                  onPressed: () => Navigator.of(context).pop(true),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF1B4332),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10)),
                    padding: const EdgeInsets.symmetric(vertical: 12),
                  ),
                  child: Text(
                    'نعم، استمر',
                    style: GoogleFonts.cairo(
                      color: Colors.white,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────

class _MapErrorView extends StatelessWidget {
  const _MapErrorView({required this.onBack, required this.onRetry});
  final VoidCallback onBack;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(28),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.map_outlined, size: 58, color: Color(0xFF8C733E)),
            const SizedBox(height: 14),
            Text(
              'تعذر تحميل الخريطة',
              style: GoogleFonts.cairo(
                fontSize: 18,
                fontWeight: FontWeight.w900,
                color: const Color(0xFF172317),
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'تأكد من تشغيل الخادم ثم حاول مرة أخرى.',
              textAlign: TextAlign.center,
              style: GoogleFonts.cairo(
                color: const Color(0xFF5F6B5F),
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 18),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                OutlinedButton(
                  onPressed: onBack,
                  child: const Text('رجوع'),
                ),
                const SizedBox(width: 10),
                ElevatedButton(
                  onPressed: onRetry,
                  child: const Text('إعادة المحاولة'),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
