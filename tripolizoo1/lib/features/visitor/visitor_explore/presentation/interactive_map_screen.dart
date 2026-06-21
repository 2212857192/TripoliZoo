import 'dart:async';
import 'dart:math' as math;
import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/visitor_map_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/map_location_bottom_sheet.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/map_coordinate_service.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/visitor_gps_service.dart';

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
  final _gpsService = VisitorGpsService();
  final _transformationController = TransformationController();
  late Future<VisitorMapData> _future;
  String _selectedCategory = 'all';
  VisitorMapLocation? _selectedLocation;
  VisitorMapData? _cachedMapData;
  StreamSubscription<VisitorGpsPosition>? _gpsSubscription;
  Offset? _userNormalized;
  List<Offset> _routePoints = const [];
  bool _isNavigating = false;
  bool _isInitialZoomSet = false;
  bool _focusApplied = false;
  BoxConstraints? _lastMapConstraints;
  double _lastImageW = 4516;
  double _lastImageH = 3374;
  double _lastMinScale = 1;

  static const _categories = [
    _MapCategory('all', 'الكل', Icons.apps_rounded),
    _MapCategory('enclosure', 'حيوانات', Icons.pets_rounded),
    _MapCategory('service', 'خدمات', Icons.room_service_rounded),
    _MapCategory('dining', 'مطاعم', Icons.restaurant_rounded),
  ];

  @override
  void initState() {
    super.initState();
    _future = _repo.getMap();
  }

  @override
  void dispose() {
    _gpsSubscription?.cancel();
    _transformationController.dispose();
    super.dispose();
  }

  List<VisitorMapLocation> _filtered(List<VisitorMapLocation> locations) {
    if (_selectedCategory == 'all') return locations;
    return locations
        .where((location) => location.category == _selectedCategory)
        .toList();
  }

  void _reload() {
    _gpsSubscription?.cancel();
    setState(() {
      _selectedLocation = null;
      _userNormalized = null;
      _routePoints = const [];
      _isNavigating = false;
      _focusApplied = false;
      _isInitialZoomSet = false;
      _cachedMapData = null;
      _future = _repo.getMap();
    });
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

    final coordinates = MapCoordinateService(bounds: data.bounds);
    final graph = data.navigation;

    var userPosition = _userNormalized;
    if (userPosition == null) {
      final gps = await _gpsService.currentPosition();
      if (gps != null) {
        userPosition = coordinates.gpsToNormalized(
          gps.latitude,
          gps.longitude,
        );
      }
    }

    final destinationNode = graph.nodeForLocation(destination.id) ??
        graph.nearestNode(Offset(destination.x, destination.y));
    if (destinationNode == null) return;

    final startNode = userPosition != null
        ? graph.nearestNode(userPosition)
        : graph.nearestNode(const Offset(0.5, 0.92));
    if (startNode == null) return;

    final path = graph.shortestPathOffsets(startNode.id, destinationNode.id);

    setState(() {
      _isNavigating = true;
      _selectedLocation = destination;
      _userNormalized = userPosition;
      _routePoints = userPosition != null ? [userPosition, ...path] : path;
    });

    _fitRouteInView();

    await _gpsSubscription?.cancel();
    try {
      _gpsSubscription = _gpsService.watchPosition().listen(
        (gps) => _onGpsUpdate(gps, destination, data),
        onError: (_) {},
      );
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('تعذر تتبع الموقع مباشرة، يُعرض المسار من نقطة البداية'),
        ),
      );
    }
  }

  void _onGpsUpdate(
    VisitorGpsPosition gps,
    VisitorMapLocation destination,
    VisitorMapData data,
  ) {
    final coordinates = MapCoordinateService(bounds: data.bounds);
    final graph = data.navigation;
    final userPosition = coordinates.gpsToNormalized(gps.latitude, gps.longitude);
    final destinationNode = graph.nodeForLocation(destination.id) ??
        graph.nearestNode(Offset(destination.x, destination.y));
    if (destinationNode == null) return;

    final startNode = graph.nearestNode(userPosition);
    if (startNode == null) return;
    final path = graph.shortestPathOffsets(startNode.id, destinationNode.id);

    if (!mounted) return;
    setState(() {
      _userNormalized = userPosition;
      _routePoints = [userPosition, ...path];
    });
    _fitRouteInView(animate: false);
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
      _userNormalized = null;
    });
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

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: const Color(0xFF1a2e1a),
        body: FutureBuilder<VisitorMapData>(
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

            final data = snapshot.data!;
            _cachedMapData = data;
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
                              if (_routePoints.isNotEmpty)
                                Positioned.fill(
                                  child: CustomPaint(
                                    key: ValueKey(
                                      'route-${_routePoints.length}-${_routePoints.first}-${_routePoints.last}',
                                    ),
                                    painter: _MapNavigationPathPainter(
                                      points: _routePoints,
                                      imageWidth: imageW,
                                    ),
                                  ),
                                ),
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

                // ── Top overlay: Search bar + filters ──────────────────
                Positioned(
                  top: topPad + 12,
                  left: 16,
                  right: 16,
                  child: Column(
                    children: [
                      _TopBar(
                        onBack: () => context.canPop()
                            ? context.pop()
                            : context.go('/home'),
                        onResetZoom: () => setState(() => _isInitialZoomSet = false),
                      ),
                      const SizedBox(height: 10),
                      _CategoryFilters(
                        categories: _categories,
                        selected: _selectedCategory,
                        onSelected: (value) => setState(() {
                          _selectedCategory = value;
                          if (_selectedLocation != null &&
                              value != 'all' &&
                              _selectedLocation!.category != value) {
                            _closeSheet();
                          }
                        }),
                      ),
                    ],
                  ),
                ),

                // ── Bottom card ────────────────────────────────────────
                if (_selectedLocation != null)
                  Positioned(
                    left: _isNavigating ? 0 : 0,
                    right: 0,
                    bottom: bottomPad + 88,
                    child: MapLocationBottomSheet(
                      location: _selectedLocation!,
                      isNavigating: _isNavigating,
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

// ── Data model ──────────────────────────────────────────────────────────────

class _MapCategory {
  const _MapCategory(this.id, this.label, this.icon);
  final String id;
  final String label;
  final IconData icon;
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
  const _TopBar({required this.onBack, required this.onResetZoom});
  final VoidCallback onBack;
  final VoidCallback onResetZoom;

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
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.park_rounded, size: 18, color: Colors.green.shade700),
                const SizedBox(width: 6),
                Text(
                  'خريطة الحديقة',
                  style: GoogleFonts.cairo(
                    fontSize: 15,
                    fontWeight: FontWeight.w900,
                    color: const Color(0xFF1F2937),
                  ),
                ),
              ],
            ),
          ),
          IconButton(
            tooltip: 'إعادة الضبط',
            icon: const Icon(Icons.my_location_rounded, size: 22),
            color: const Color(0xFF1B4332),
            onPressed: onResetZoom,
          ),
        ],
      ),
    );
  }
}

// ── Category filter chips ─────────────────────────────────────────────────────

class _CategoryFilters extends StatelessWidget {
  const _CategoryFilters({
    required this.categories,
    required this.selected,
    required this.onSelected,
  });

  final List<_MapCategory> categories;
  final String selected;
  final ValueChanged<String> onSelected;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 40,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemCount: categories.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (context, index) {
          final cat = categories[index];
          final active = selected == cat.id;

          return GestureDetector(
            onTap: () => onSelected(cat.id),
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
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    cat.icon,
                    size: 14,
                    color: active ? Colors.white : const Color(0xFF374151),
                  ),
                  const SizedBox(width: 5),
                  Text(
                    cat.label,
                    style: GoogleFonts.cairo(
                      color: active ? Colors.white : const Color(0xFF374151),
                      fontSize: 12.5,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

// ── Map Pin ───────────────────────────────────────────────────────────────────

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
    return Icons.pets_rounded;
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

class _MapNavigationPathPainter extends CustomPainter {
  const _MapNavigationPathPainter({
    required this.points,
    required this.imageWidth,
  });

  final List<Offset> points;
  final double imageWidth;

  @override
  void paint(Canvas canvas, Size size) {
    if (points.length < 2) return;

    final stroke = math.max(28.0, imageWidth * 0.012);

    final outline = Paint()
      ..color = Colors.white
      ..strokeWidth = stroke + 10
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round
      ..strokeJoin = StrokeJoin.round;

    final paint = Paint()
      ..color = const Color(0xFFE8651A)
      ..strokeWidth = stroke
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round
      ..strokeJoin = StrokeJoin.round;

    final path = Path();
    final first = Offset(
      points.first.dx * size.width,
      points.first.dy * size.height,
    );
    path.moveTo(first.dx, first.dy);

    for (var i = 1; i < points.length; i++) {
      final point = Offset(
        points[i].dx * size.width,
        points[i].dy * size.height,
      );
      path.lineTo(point.dx, point.dy);
    }

    canvas.drawPath(path, outline);
    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(covariant _MapNavigationPathPainter oldDelegate) {
    return oldDelegate.points != points ||
        oldDelegate.imageWidth != imageWidth;
  }
}

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
