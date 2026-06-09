import 'dart:async';
import 'dart:math' as math;

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:panorama_viewer/panorama_viewer.dart';
import 'package:tripolizoo/debug/agent_log.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/tour_marker_detector.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/virtual_tour_data.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class VirtualTourScreen extends StatefulWidget {
  const VirtualTourScreen({super.key});

  @override
  State<VirtualTourScreen> createState() => _VirtualTourScreenState();
}

class _VirtualTourScreenState extends State<VirtualTourScreen> {
  String _currentSceneId = VirtualTourData.startSceneId;
  final PanoramaController _panoramaController = PanoramaController();
  List<TourMarker> _markers = [];
  bool _walkthrough = false;
  int _walkthroughGen = 0;
  double _viewLat = 0;
  double _viewLon = 0;
  bool _ready = false;
  bool _pendingSnap = false;
  Completer<void>? _sceneLoadCompleter;
  double _fadeOpacity = 0;
  String? _walkthroughAnimalLabel;
  String _walkthroughPhase = '';
  bool _walkthroughComplete = false;
  final Map<String, ImageProvider> _imageProviders = {};
  List<Hotspot> _hotspots = [];

  TourScene get _scene => VirtualTourData.sceneById(_currentSceneId);
  int get _sceneIndex => VirtualTourData.indexOf(_currentSceneId);
  int get _sceneCount => VirtualTourData.scenes.length;

  @override
  void initState() {
    super.initState();
    _preload();
  }

  @override
  void dispose() {
    _stopWalkthrough();
    super.dispose();
  }

  Future<void> _preload() async {
    final markers = await TourMarkerDetector.detectForScene(_scene);
    // #region agent log
    agentLog(
      location: 'virtual_tour_screen.dart:_preload',
      message: 'initial scene markers loaded',
      hypothesisId: 'E',
      data: {
        'runId': 'orange-labels-debug',
        'sceneId': _currentSceneId,
        'markers': markers
            .map(
              (m) => {
                'type': m.type.name,
                'label': m.label,
                'targetSceneId': m.targetSceneId,
              },
            )
            .toList(),
      },
    );
    // #endregion
    _applySceneView(
      markers,
      sceneId: _currentSceneId,
      fromForward: true,
    );
    if (!mounted) return;
    setState(() {
      _markers = markers;
      _hotspots = _buildHotspots(markers);
      _ready = true;
    });
    _precacheScene(_scene);
    _pendingSnap = true;
    _snapCameraToArrow();
  }

  double _normalizeLon(double lon) {
    var l = lon;
    while (l > 180) {
      l -= 360;
    }
    while (l < -180) {
      l += 360;
    }
    return l;
  }

  /// يضبط lat/lon مع أقصر مسار لـ longitude لتجنب دوران 360°
  void _setViewTo(double targetLat, double targetLon) {
    final lonDelta = _shortestLonDelta(_viewLon, _normalizeLon(targetLon));
    _viewLat = targetLat;
    _viewLon = _viewLon + lonDelta;
  }

  /// زاوية الكاميرا — للأمام: زر التنقل التالي
  void _applySceneView(
    List<TourMarker> markers, {
    required String sceneId,
    required bool fromForward,
    String? fromSceneId,
  }) {
    TourMarker? chosen;
    String? chosenSource;
    for (final m in markers) {
      if (m.type == TourMarkerType.next) {
        chosen = m;
        chosenSource = 'next';
        break;
      }
    }
    if (chosen == null) {
      for (final m in markers) {
        if (m.type == TourMarkerType.animalArea) {
          chosen = m;
          chosenSource = 'animalArea';
          break;
        }
      }
    }
    if (chosen != null) {
      _setViewTo(chosen.latitude, chosen.longitude);
    } else {
      _setViewTo(0, 0);
    }
    // #region agent log
    agentLog(
      location: 'virtual_tour_screen.dart:_applySceneView',
      message: 'camera view applied',
      hypothesisId: 'A',
      data: {
        'sceneId': sceneId,
        'fromForward': fromForward,
        'fromSceneId': fromSceneId,
        'chosenType': chosen?.type.name,
        'chosenSource': chosenSource,
        'chosenLabel': chosen?.label,
        'chosenTarget': chosen?.targetSceneId,
        'viewLat': _viewLat,
        'viewLon': _viewLon,
        'isHorseRoute': VirtualTourData.isHorseRoute(sceneId),
      },
    );
    // #endregion
  }

  void _snapCameraToArrow() {
    if (!mounted) return;
    // #region agent log
    agentLog(
      location: 'virtual_tour_screen.dart:_snapCameraToArrow',
      message: 'camera snap',
      hypothesisId: 'C',
      data: {
        'sceneId': _currentSceneId,
        'viewLat': _viewLat,
        'viewLon': _viewLon,
        'pendingSnap': _pendingSnap,
      },
    );
    // #endregion
    _panoramaController.setAnimSpeed(0);
    _panoramaController.setView(_viewLat, _viewLon);
  }

  void _onPanoramaImageLoaded() {
    if (_pendingSnap) {
      _pendingSnap = false;
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (mounted) _snapCameraToArrow();
      });
    }
    if (_sceneLoadCompleter != null && !_sceneLoadCompleter!.isCompleted) {
      _sceneLoadCompleter!.complete();
    }
  }

  Future<void> _navigateToScene(
    String sceneId, {
    required bool fromForward,
    TourMarker? viaMarker,
  }) async {
    // #region agent log
    agentLog(
      location: 'virtual_tour_screen.dart:_navigateToScene',
      message: 'navigation start',
      hypothesisId: 'E',
      data: {
        'fromSceneId': _currentSceneId,
        'toSceneId': sceneId,
        'fromForward': fromForward,
        'viaMarkerLat': viaMarker?.latitude,
        'viaMarkerLon': viaMarker?.longitude,
        'viaMarkerTarget': viaMarker?.targetSceneId,
      },
    );
    // #endregion
    final nextScene = VirtualTourData.sceneById(sceneId);
    await _precacheScene(nextScene);
    if (!mounted) return;
    _sceneLoadCompleter = Completer<void>();
    await _goToScene(
      sceneId,
      fromForward: fromForward,
      viaMarker: viaMarker,
    );
    if (!mounted) return;
    await _sceneLoadCompleter!.future.timeout(
      const Duration(seconds: 4),
      onTimeout: () {},
    );
    _sceneLoadCompleter = null;
    // #region agent log
    agentLog(
      location: 'virtual_tour_screen.dart:_navigateToScene',
      message: 'navigation done',
      hypothesisId: 'E',
      data: {
        'sceneId': _currentSceneId,
        'viewLat': _viewLat,
        'viewLon': _viewLon,
      },
    );
    // #endregion
  }

  int get _panoramaCacheWidth => kIsWeb ? 2048 : 4096;

  ImageProvider _sceneImageProvider(TourScene scene) {
    return _imageProviders.putIfAbsent(
      scene.imageAsset,
      () => ResizeImage(AssetImage(scene.imageAsset), width: _panoramaCacheWidth),
    );
  }

  Future<void> _precacheScene(TourScene scene) async {
    if (!mounted) return;
    await precacheImage(_sceneImageProvider(scene), context);
  }

  Future<void> _precacheHorseRoute() async {
    for (final n in [5, 6, 7, 8, 9, 10, 11]) {
      if (!mounted) return;
      await _precacheScene(VirtualTourData.sceneById('scene_$n'));
    }
  }

  Future<void> _precacheDuckRoute() async {
    for (final n in [12, 13, 14, 15, 16, 17]) {
      if (!mounted) return;
      await _precacheScene(VirtualTourData.sceneById('scene_$n'));
    }
  }

  Future<void> _precacheReptileRoute() async {
    for (final n in [18, 19, 20, 21, 22, 23]) {
      if (!mounted) return;
      await _precacheScene(VirtualTourData.sceneById('scene_$n'));
    }
  }

  Future<void> _precacheBigCatsRoute() async {
    for (final n in [24, 25, 26, 27, 28, 29, 30]) {
      if (!mounted) return;
      await _precacheScene(VirtualTourData.sceneById('scene_$n'));
    }
  }

  Future<void> _goToScene(
    String sceneId, {
    required bool fromForward,
    bool applyArrowView = true,
    TourMarker? viaMarker,
  }) async {
    if (sceneId == _currentSceneId) return;

    final fromSceneId = _currentSceneId;
    final nextScene = VirtualTourData.sceneById(sceneId);
    TourMarkerDetector.invalidateScene(sceneId);
    final markers = await TourMarkerDetector.detectForScene(nextScene);
    // #region agent log
    agentLog(
      location: 'virtual_tour_screen.dart:_goToScene',
      message: 'scene transition start',
      hypothesisId: 'B',
      data: {
        'fromSceneId': fromSceneId,
        'toSceneId': sceneId,
        'fromForward': fromForward,
        'applyArrowView': applyArrowView,
        'viaMarkerLon': viaMarker?.longitude,
      },
    );
    // #endregion
    if (applyArrowView) {
      if (fromForward) {
        _applySceneView(
          markers,
          sceneId: sceneId,
          fromForward: true,
          fromSceneId: fromSceneId,
        );
      } else if (viaMarker != null) {
        _setViewTo(viaMarker.latitude, viaMarker.longitude);
        // #region agent log
        agentLog(
          location: 'virtual_tour_screen.dart:_goToScene',
          message: 'back view from tapped marker',
          hypothesisId: 'A',
          data: {
            'toSceneId': sceneId,
            'fromSceneId': fromSceneId,
            'viewLat': _viewLat,
            'viewLon': _viewLon,
            'markerTarget': viaMarker.targetSceneId,
          },
        );
        // #endregion
      }
    }

    // نحمّل الصورة التالية فقط ثم نغيّر المشهد فوراً
    await _precacheScene(nextScene);
    if (!mounted) return;
    _pendingSnap = applyArrowView;
    setState(() {
      _currentSceneId = sceneId;
      _markers = markers;
      _hotspots = _buildHotspots(markers);
    });
    // #region agent log
    agentLog(
      location: 'virtual_tour_screen.dart:_goToScene',
      message: 'scene transition applied',
      hypothesisId: 'D',
      data: {
        'runId': 'orange-labels-debug',
        'fromSceneId': fromSceneId,
        'toSceneId': sceneId,
        'fromForward': fromForward,
        'isHorseRoute': VirtualTourData.isHorseRoute(sceneId),
        'isWalkthrough': _walkthrough,
        'viewLat': _viewLat,
        'viewLon': _viewLon,
        'backMarker': markers
            .where((m) => m.type == TourMarkerType.back)
            .map((m) => {'lat': m.latitude, 'lon': m.longitude, 'target': m.targetSceneId, 'label': m.label})
            .toList(),
        'nextMarker': markers
            .where((m) => m.type == TourMarkerType.next)
            .map((m) => {'lat': m.latitude, 'lon': m.longitude, 'target': m.targetSceneId, 'label': m.label})
            .toList(),
        'animalMarkers': markers
            .where((m) => m.type == TourMarkerType.animalArea)
            .map((m) => {'lat': m.latitude, 'lon': m.longitude, 'target': m.targetSceneId, 'label': m.label})
            .toList(),
      },
    );
    // #endregion

    // نحمّل باقي الصور في الخلفية بدون انتظار
    if (VirtualTourData.isHorseRoute(sceneId)) {
      _precacheHorseRoute();
    }
    if (VirtualTourData.isDuckRoute(sceneId)) {
      _precacheDuckRoute();
    }
    if (VirtualTourData.isReptileRoute(sceneId)) {
      _precacheReptileRoute();
    }
    if (VirtualTourData.isBigCatsRoute(sceneId)) {
      _precacheBigCatsRoute();
    }
  }

  bool get _isHorseRoute => VirtualTourData.isHorseRoute(_currentSceneId);
  bool get _isDuckRoute => VirtualTourData.isDuckRoute(_currentSceneId);
  bool get _isReptileRoute => VirtualTourData.isReptileRoute(_currentSceneId);
  bool get _isBigCatsRoute => VirtualTourData.isBigCatsRoute(_currentSceneId);

  void _toggleWalkthrough() {
    if (_walkthrough) {
      _stopWalkthrough();
    } else {
      setState(() => _walkthrough = true);
      _runWalkthrough();
    }
  }

  void _stopWalkthrough() {
    if (!_walkthrough) return;
    _walkthroughGen++;
    _panoramaController.setAnimSpeed(0);
    setState(() {
      _walkthrough = false;
      _walkthroughPhase = '';
      _walkthroughAnimalLabel = null;
    });
  }

  double _shortestLonDelta(double from, double to) {
    var delta = to - from;
    while (delta > 180) {
      delta -= 360;
    }
    while (delta < -180) {
      delta += 360;
    }
    return delta;
  }

  double _easeInOutCubic(double t) {
    return t < 0.5 ? 4 * t * t * t : 1 - (-2 * t + 2) * (-2 * t + 2) * (-2 * t + 2) / 2;
  }

  Future<void> _animateViewTo(
    double targetLat,
    double targetLon,
    Duration duration, {
    required int gen,
  }) async {
    final startLat = _viewLat;
    final startLon = _viewLon;
    final lonDelta = _shortestLonDelta(startLon, _normalizeLon(targetLon));
    final steps = (duration.inMilliseconds / 36).round().clamp(8, 72);

    for (var i = 1; i <= steps; i++) {
      if (!_walkthrough || !mounted || gen != _walkthroughGen) return;
      final t = _easeInOutCubic(i / steps);
      final lat = startLat + (targetLat - startLat) * t;
      final lon = _normalizeLon(startLon + lonDelta * t);
      _viewLat = lat;
      _viewLon = lon;
      _panoramaController.setAnimSpeed(0);
      _panoramaController.setView(lat, lon);
      if (mounted) setState(() {});
      await Future.delayed(Duration(milliseconds: duration.inMilliseconds ~/ steps));
    }
  }

  void _setWalkthroughPhase(String phase) {
    if (!mounted) return;
    setState(() => _walkthroughPhase = phase);
  }

  Future<void> _fadeTo(double target, {int steps = 12}) async {
    final start = _fadeOpacity;
    for (var i = 1; i <= steps; i++) {
      if (!mounted) return;
      setState(() => _fadeOpacity = start + (target - start) * (i / steps));
      await Future.delayed(const Duration(milliseconds: 28));
    }
  }

  Future<void> _fadeTransition(Future<void> Function() action) async {
    await _fadeTo(1);
    _sceneLoadCompleter = Completer<void>();
    await action();
    if (!mounted) return;
    await _sceneLoadCompleter!.future.timeout(
      const Duration(seconds: 4),
      onTimeout: () {},
    );
    _sceneLoadCompleter = null;
    if (!mounted) return;
    _snapCameraToArrow();
    await _fadeTo(0);
  }

  Future<void> _lookAround(int gen, {Duration duration = const Duration(seconds: 5)}) async {
    _setWalkthroughPhase('\u0646\u062a\u0644\u0627\u062d\u0638 \u062d\u0648\u0644 \u0627\u0644\u0645\u0643\u0627\u0646...');
    final baseLat = _viewLat;
    final baseLon = _viewLon;
    final half = Duration(milliseconds: duration.inMilliseconds ~/ 2);
    await _animateViewTo(baseLat, _normalizeLon(baseLon - 32), half, gen: gen);
    if (!_walkthrough || gen != _walkthroughGen) return;
    await _animateViewTo(baseLat, _normalizeLon(baseLon + 32), half, gen: gen);
    if (!_walkthrough || gen != _walkthroughGen) return;
    await _animateViewTo(baseLat, baseLon, const Duration(milliseconds: 900), gen: gen);
  }

  void _applyWalkthroughEntry(String fromSceneId, List<TourMarker> markers) {
    final entry = VirtualTourData.entryMarker(_currentSceneId, fromSceneId);
    if (entry != null) {
      _viewLat = entry.latitude;
      _viewLon = _normalizeLon(entry.longitude);
    } else {
      _viewLat = 0;
      _viewLon = 0;
    }
  }

  Future<void> _visitAnimalDuringWalk(TourMarker marker, int gen) async {
    _setWalkthroughPhase('نقترب من ${marker.label ?? 'الحيوانات'}...');
    setState(() => _walkthroughAnimalLabel = marker.label);
    await _animateViewTo(marker.latitude, marker.longitude, const Duration(milliseconds: 2000), gen: gen);
    if (!_walkthrough || gen != _walkthroughGen) return;
    _setWalkthroughPhase('${marker.label ?? 'منطقة الحيوانات'} — نتأمل...');
    await Future.delayed(const Duration(seconds: 3));
    if (!mounted || gen != _walkthroughGen) return;
    setState(() => _walkthroughAnimalLabel = null);
  }

  Future<void> _showWalkthroughComplete(int gen) async {
    if (!mounted || gen != _walkthroughGen) return;
    setState(() => _walkthroughComplete = true);
    _setWalkthroughPhase('انتهت الجولة');
    await Future.delayed(const Duration(seconds: 4));
    if (mounted && gen == _walkthroughGen) {
      setState(() => _walkthroughComplete = false);
    }
  }

  Future<void> _runWalkthrough() async {
    final gen = _walkthroughGen;
    setState(() => _walkthroughComplete = false);

    while (_walkthrough && mounted && gen == _walkthroughGen) {
      // 1) استكشاف المشهد — كأننا وقفنا وننظر حولنا
      await _lookAround(gen);
      if (!_walkthrough || !mounted || gen != _walkthroughGen) return;

      // 2) زيارة مناطق الحيوانات
      for (final animal in VirtualTourData.animalMarkers(_currentSceneId)) {
        await _visitAnimalDuringWalk(animal, gen);
        if (!_walkthrough || !mounted || gen != _walkthroughGen) return;
      }

      // 3) تحديد المسار التالي
      final nextId = VirtualTourData.walkthroughNextSceneId(_currentSceneId);
      if (nextId == null) {
        await _showWalkthroughComplete(gen);
        _stopWalkthrough();
        return;
      }

      final exitMarker = VirtualTourData.walkthroughExitMarker(_currentSceneId, nextId);
      final nextTitle = VirtualTourData.sceneById(nextId).title;

      // 4) نتجه نحو الممر — مشي
      await _walkIntoScene(nextId, exitMarker: exitMarker, nextTitle: nextTitle, gen: gen);
    }
  }

  Future<void> _walkIntoScene(
    String nextId, {
    required TourMarker? exitMarker,
    required String nextTitle,
    required int gen,
  }) async {
    _setWalkthroughPhase('نمشي نحو $nextTitle...');
    if (exitMarker != null) {
      await _animateViewTo(
        exitMarker.latitude,
        exitMarker.longitude,
        const Duration(milliseconds: 2400),
        gen: gen,
      );
    }
    if (!_walkthrough || !mounted || gen != _walkthroughGen) return;

    await Future.delayed(const Duration(milliseconds: 700));

    final fromSceneId = _currentSceneId;
    await _fadeTransition(() async {
      await _goToScene(nextId, fromForward: true, applyArrowView: false);
      _applyWalkthroughEntry(fromSceneId, _markers);
    });
    if (!_walkthrough || !mounted || gen != _walkthroughGen) return;

    _setWalkthroughPhase('ندخل $nextTitle...');
    TourMarker? forward;
    for (final m in _markers) {
      if (m.type == TourMarkerType.next) {
        forward = m;
        break;
      }
    }
    if (forward != null) {
      await _animateViewTo(
        forward.latitude,
        forward.longitude,
        const Duration(milliseconds: 2800),
        gen: gen,
      );
    } else {
      await _animateViewTo(_viewLat, _viewLon, const Duration(milliseconds: 1200), gen: gen);
    }
    if (!_walkthrough || !mounted || gen != _walkthroughGen) return;

    await Future.delayed(const Duration(milliseconds: 500));
  }

  void _onMarkerTap(TourMarker marker) {
    // #region agent log
    agentLog(
      location: 'virtual_tour_screen.dart:_onMarkerTap',
      message: 'marker tapped',
      hypothesisId: 'A',
      data: {
        'currentSceneId': _currentSceneId,
        'markerType': marker.type.name,
        'markerLabel': marker.label,
        'targetSceneId': marker.targetSceneId,
        'markerLat': marker.latitude,
        'markerLon': marker.longitude,
      },
    );
    // #endregion
    switch (marker.type) {
      case TourMarkerType.next:
        if (marker.targetSceneId != null) {
          _stopWalkthrough();
          _navigateToScene(
            marker.targetSceneId!,
            fromForward: true,
            viaMarker: marker,
          );
        }
      case TourMarkerType.back:
        if (marker.targetSceneId != null) {
          // #region agent log
          agentLog(
            location: 'virtual_tour_screen.dart:_onMarkerTap:back',
            message: 'back navigation',
            hypothesisId: 'A',
            data: {
              'fromSceneId': _currentSceneId,
              'toSceneId': marker.targetSceneId,
              'markerLon': marker.longitude,
              'isHorseLoop': _currentSceneId == 'scene_7' &&
                  marker.targetSceneId == 'scene_8',
            },
          );
          // #endregion
          _stopWalkthrough();
          _navigateToScene(
            marker.targetSceneId!,
            fromForward: false,
            viaMarker: marker,
          );
        }
      case TourMarkerType.animalArea:
        final target = marker.targetSceneId;
        // #region agent log
        agentLog(
          location: 'virtual_tour_screen.dart:_onMarkerTap:animalArea',
          message: 'animalArea branch',
          hypothesisId: target != null ? 'B' : 'A',
          data: {
            'label': marker.label,
            'targetSceneId': target,
            'branch': target != null ? 'goToScene' : 'showAnimalSheet',
          },
        );
        // #endregion
        if (target != null) {
          _stopWalkthrough();
          _navigateToScene(
            target,
            fromForward: true,
            viaMarker: marker,
          );
        } else {
          // #region agent log
          agentLog(
            location: 'virtual_tour_screen.dart:_onMarkerTap:animalArea',
            message: 'animalArea label-only mode (no sheet)',
            hypothesisId: 'A',
            data: {
              'label': marker.label,
              'targetSceneId': target,
            },
          );
          // #endregion
        }
    }
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_ready) _precacheScene(_scene);
  }

  List<Hotspot> _buildHotspots(List<TourMarker> markers) {
    return markers.map((m) {
      final isAnimal = m.type == TourMarkerType.animalArea;
      return Hotspot(
        latitude: m.latitude,
        longitude: m.longitude,
        orgin: const Offset(0.5, 0.5),
        width: isAnimal ? 108 : 120,
        height: isAnimal ? 96 : 112,
        widget: _MarkerWidget(
          type: m.type,
          label: m.label,
          onTap: () => _onMarkerTap(m),
        ),
      );
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.black54, borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.arrow_back_ios_new, color: Colors.white, size: 18),
          ),
          onPressed: () => context.pop(),
        ),
        title: Column(
          children: [
            const Text('جولة 360°',
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            Text(
              _scene.title,
              style: TextStyle(color: Colors.white.withValues(alpha: 0.8), fontSize: 12, fontWeight: FontWeight.w600),
            ),
            if (_isHorseRoute) ...[
              const SizedBox(height: 2),
              Container(
                margin: const EdgeInsets.only(top: 2),
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: const Color(0xFF8D6E63).withValues(alpha: 0.9),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text('🐴', style: TextStyle(fontSize: 10)),
                    SizedBox(width: 4),
                    Text(
                      'طريق الخيول',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
            ],
            if (_isDuckRoute) ...[
              const SizedBox(height: 2),
              Container(
                margin: const EdgeInsets.only(top: 2),
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: const Color(0xFF0277BD).withValues(alpha: 0.9),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text('🦆', style: TextStyle(fontSize: 10)),
                    SizedBox(width: 4),
                    Text(
                      'طريق البط والبجع',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
            ],
            if (_isReptileRoute) ...[
              const SizedBox(height: 2),
              Container(
                margin: const EdgeInsets.only(top: 2),
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: const Color(0xFF558B2F).withValues(alpha: 0.9),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text('🦎', style: TextStyle(fontSize: 10)),
                    SizedBox(width: 4),
                    Text(
                      'طريق الزواحف',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
            ],
            if (_isBigCatsRoute) ...[
              const SizedBox(height: 2),
              Container(
                margin: const EdgeInsets.only(top: 2),
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: const Color(0xFF6D4C41).withValues(alpha: 0.9),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text('🦁', style: TextStyle(fontSize: 10)),
                    SizedBox(width: 4),
                    Text(
                      'طريق الأسود والنمور',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ],
        ),
        centerTitle: true,
        actions: [
          IconButton(
            tooltip: _walkthrough ? 'إيقاف الجولة' : 'جولة تلقائية',
            onPressed: _toggleWalkthrough,
            icon: Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: _walkthrough
                    ? AppColors.primary.withValues(alpha: 0.85)
                    : Colors.black54,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(
                _walkthrough ? Icons.pause_rounded : Icons.play_arrow_rounded,
                color: Colors.white,
                size: 20,
              ),
            ),
          ),
        ],
      ),
      body: Stack(
        fit: StackFit.expand,
        children: [
          if (!_ready)
            const Center(
              child: CircularProgressIndicator(color: Colors.white54),
            )
          else
            PanoramaViewer(
              latitude: _viewLat,
              longitude: _viewLon,
              animSpeed: 0,
              onImageLoad: _onPanoramaImageLoaded,
              panoramaController: _panoramaController,
              sensorControl: SensorControl.none,
              hotspots: _walkthrough ? const [] : _hotspots,
              child: Image(
                image: _sceneImageProvider(_scene),
                gaplessPlayback: true,
                filterQuality: FilterQuality.medium,
              ),
            ),
          if (_walkthrough)
            Positioned(
              top: MediaQuery.of(context).padding.top + 64,
              left: 0,
              right: 0,
              child: Center(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withValues(alpha: 0.9),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(Icons.directions_walk_rounded, color: Colors.white, size: 16),
                      const SizedBox(width: 8),
                      Flexible(
                        child: Text(
                          _walkthroughPhase.isNotEmpty
                              ? _walkthroughPhase
                              : 'جولة تلقائية  ${_sceneIndex + 1}/$_sceneCount',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 12,
                            fontWeight: FontWeight.w700,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          if (_walkthroughAnimalLabel != null)
            Positioned(
              bottom: 88,
              left: 24,
              right: 24,
              child: IgnorePointer(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.95),
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.15),
                        blurRadius: 12,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: AppColors.accent.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Icon(Icons.pets_rounded, color: AppColors.accent, size: 20),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              _walkthroughAnimalLabel!,
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w900,
                                color: AppColors.primaryDark,
                              ),
                            ),
                            const Text(
                              'نتوقف للمشاهدة...',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textSecondary,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          if (_walkthroughComplete)
            Positioned.fill(
              child: IgnorePointer(
                child: Container(
                  color: Colors.black.withValues(alpha: 0.55),
                  alignment: Alignment.center,
                  child: Container(
                    margin: const EdgeInsets.symmetric(horizontal: 32),
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.check_circle_rounded, color: AppColors.primary, size: 48),
                        const SizedBox(height: 12),
                        const Text(
                          'انتهت الجولة',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w900,
                            color: AppColors.primaryDark,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'جولة كاملة من $_sceneCount محطات داخل الحديقة',
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          if (_fadeOpacity > 0)
            Positioned.fill(
              child: IgnorePointer(
                child: ColoredBox(color: Colors.black.withValues(alpha: _fadeOpacity)),
              ),
            ),
          Positioned(
            bottom: 24,
            left: 20,
            right: 20,
            child: IgnorePointer(
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                decoration: BoxDecoration(
                  color: Colors.black.withValues(alpha: 0.5),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      _walkthrough ? Icons.pause_circle_outline : Icons.touch_app_outlined,
                      color: Colors.white70,
                      size: 16,
                    ),
                    const SizedBox(width: 8),
                    Text(
                      _walkthrough
                          ? (_walkthroughPhase.isNotEmpty
                              ? _walkthroughPhase
                              : 'جولة تلقائية — اضغط ⏸ للإيقاف')
                              : _isHorseRoute
                                  ? 'طريق الخيول  •  انتقل أو منطقة الخيول'
                              : _isDuckRoute
                                  ? 'طريق البط والبجع  •  البرتقالي للبركة'
                                  : _isReptileRoute
                                      ? 'طريق الزواحف  •  البوابات الخضراء للانتقال'
                                      : _isBigCatsRoute
                                          ? 'طريق الأسود والنمور  •  البرتقالي للأقفاص'
                                      : 'اضغط البوابة الأرضية للانتقال  •  البرتقالي للحيوانات',
                      style: const TextStyle(
                        color: Colors.white70,
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _MarkerWidget extends StatefulWidget {
  const _MarkerWidget({
    required this.type,
    required this.onTap,
    this.label,
  });

  final TourMarkerType type;
  final VoidCallback onTap;
  final String? label;

  @override
  State<_MarkerWidget> createState() => _MarkerWidgetState();
}

class _MarkerWidgetState extends State<_MarkerWidget>
    with TickerProviderStateMixin {
  late final AnimationController _pulse;
  late final AnimationController _ring;

  @override
  void initState() {
    super.initState();
    _pulse = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1600),
    )..repeat(reverse: true);
    _ring = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 2400),
    )..repeat();
  }

  @override
  void dispose() {
    _pulse.dispose();
    _ring.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: widget.onTap,
      behavior: HitTestBehavior.opaque,
      child: AnimatedBuilder(
        animation: Listenable.merge([_pulse, _ring]),
        builder: (_, __) {
          return switch (widget.type) {
            TourMarkerType.next => _NavPortal(
              isForward: true,
              label: widget.label,
              pulse: _pulse.value,
              ring: _ring.value,
            ),
            TourMarkerType.back => _NavPortal(
              isForward: false,
              label: widget.label,
              pulse: _pulse.value,
              ring: _ring.value,
            ),
            TourMarkerType.animalArea => _AnimalSpot(
              label: widget.label,
              pulse: _pulse.value,
              ring: _ring.value,
            ),
          };
        },
      ),
    );
  }
}

/// بوابة أرضية — بدون أسهم، أسلوب Matterport / Street View
class _NavPortal extends StatelessWidget {
  const _NavPortal({
    required this.isForward,
    required this.pulse,
    required this.ring,
    this.label,
  });

  final bool isForward;
  final String? label;
  final double pulse;
  final double ring;

  Color get _accent =>
      isForward ? AppColors.primary : const Color(0xFF607D8B);

  String get _defaultLabel => isForward ? 'انتقل' : 'رجوع';

  IconData get _labelIcon => isForward
      ? Icons.explore_rounded
      : Icons.restart_alt_rounded;

  @override
  Widget build(BuildContext context) {
    final scale = 1.0 + pulse * 0.04;
    final outerRing = 48 + ring * 32;
    final midRing = 36 + ring * 16;

    return SizedBox(
      width: label != null ? 120 : 84,
      height: label != null ? 112 : 92,
      child: Stack(
        alignment: Alignment.bottomCenter,
        clipBehavior: Clip.none,
        children: [
          Positioned(
            top: 0,
            child: _HotspotLabel(
              text: label ?? _defaultLabel,
              color: _accent,
              icon: _labelIcon,
            ),
          ),
          Positioned(
            bottom: 6,
            child: Transform.scale(
              scale: scale,
              child: SizedBox(
                width: isForward ? 80 : 68,
                height: isForward ? 80 : 68,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    // حلقة نبض خارجية
                    Container(
                      width: outerRing,
                      height: outerRing * 0.42,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(999),
                        border: Border.all(
                          color: _accent.withValues(alpha: (1 - ring) * 0.55),
                          width: 2,
                        ),
                      ),
                    ),
                    // حلقة أرضية ثابتة
                    Container(
                      width: midRing,
                      height: midRing * 0.38,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(999),
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [
                            Colors.white.withValues(alpha: 0.95),
                            Colors.white.withValues(alpha: 0.72),
                          ],
                        ),
                        border: Border.all(color: _accent.withValues(alpha: 0.7), width: 2),
                        boxShadow: [
                          BoxShadow(
                            color: _accent.withValues(alpha: 0.4),
                            blurRadius: 16,
                            spreadRadius: 1,
                          ),
                        ],
                      ),
                    ),
                    // نواة متوهجة — نقطة فقط بدون سهم
                    CustomPaint(
                      size: Size(isForward ? 80 : 68, isForward ? 80 : 68),
                      painter: _BeaconCorePainter(
                        color: _accent,
                        intensity: 0.55 + pulse * 0.35,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          Positioned(
            bottom: 0,
            child: CustomPaint(
              size: Size(isForward ? 64 : 52, 20),
              painter: _GroundShadowPainter(color: _accent),
            ),
          ),
        ],
      ),
    );
  }
}

/// نواة البوابة — توهج دائري بدون أي سهم
class _BeaconCorePainter extends CustomPainter {
  const _BeaconCorePainter({required this.color, required this.intensity});

  final Color color;
  final double intensity;

  @override
  void paint(Canvas canvas, Size size) {
    final cx = size.width / 2;
    final cy = size.height / 2 + 4;

    // توهج خارجي
    canvas.drawCircle(
      Offset(cx, cy),
      14,
      Paint()
        ..shader = RadialGradient(
          colors: [
            color.withValues(alpha: intensity * 0.55),
            color.withValues(alpha: 0),
          ],
        ).createShader(Rect.fromCircle(center: Offset(cx, cy), radius: 14)),
    );

    // نقطة مركزية
    canvas.drawCircle(
      Offset(cx, cy),
      6,
      Paint()..color = color,
    );
    canvas.drawCircle(
      Offset(cx, cy),
      6,
      Paint()
        ..color = Colors.white.withValues(alpha: 0.55)
        ..style = PaintingStyle.stroke
        ..strokeWidth = 1.5,
    );

    // خطوط شعاعية خفيفة — كبوابة وليس سهم
    for (var i = 0; i < 8; i++) {
      final angle = i * math.pi / 4;
      final dx = cx + 10 * math.cos(angle);
      final dy = cy + 10 * math.sin(angle) * 0.35;
      canvas.drawCircle(
        Offset(dx, dy),
        1.2,
        Paint()..color = color.withValues(alpha: intensity * 0.5),
      );
    }
  }

  @override
  bool shouldRepaint(covariant _BeaconCorePainter old) =>
      old.color != color || old.intensity != intensity;
}

/// نقطة اكتشاف حيوانات
class _AnimalSpot extends StatelessWidget {
  const _AnimalSpot({
    required this.pulse,
    required this.ring,
    this.label,
  });

  final String? label;
  final double pulse;
  final double ring;

  @override
  Widget build(BuildContext context) {
    final scale = 1.0 + pulse * 0.05;
    final ringSize = 50 + ring * 26;
    final ringOpacity = (1 - ring) * 0.4;

    return SizedBox(
      width: 108,
      height: 96,
      child: Stack(
        alignment: Alignment.bottomCenter,
        clipBehavior: Clip.none,
        children: [
          if (label != null)
            Positioned(
              top: 0,
              child: _HotspotLabel(
                text: label!,
                color: AppColors.accent,
                icon: Icons.pets_rounded,
              ),
            ),
          Positioned(
            bottom: 8,
            child: Transform.scale(
              scale: scale,
              child: SizedBox(
                width: 68,
                height: 68,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    Container(
                      width: ringSize,
                      height: ringSize,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: AppColors.accent.withValues(alpha: ringOpacity),
                          width: 2,
                        ),
                      ),
                    ),
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        gradient: AppColors.accentGradient,
                        border: Border.all(color: Colors.white, width: 2.5),
                        boxShadow: [
                          BoxShadow(
                            color: AppColors.accent.withValues(alpha: 0.45),
                            blurRadius: 14,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: const Icon(
                        Icons.pets_rounded,
                        color: Colors.white,
                        size: 22,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          Positioned(
            bottom: 0,
            child: CustomPaint(
              size: const Size(52, 16),
              painter: _GroundShadowPainter(color: AppColors.accent),
            ),
          ),
        ],
      ),
    );
  }
}

class _HotspotLabel extends StatelessWidget {
  const _HotspotLabel({
    required this.text,
    required this.color,
    this.icon,
  });

  final String text;
  final Color color;
  final IconData? icon;

  @override
  Widget build(BuildContext context) {
    return Container(
      constraints: const BoxConstraints(maxWidth: 108),
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.96),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withValues(alpha: 0.25)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.14),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) ...[
            Icon(icon, size: 12, color: color),
            const SizedBox(width: 4),
          ],
          Flexible(
            child: Text(
              text,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w800,
                color: color,
                letterSpacing: 0.2,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// ظل أرضي للعمق — كأن النقطة على الأرض
class _GroundShadowPainter extends CustomPainter {
  const _GroundShadowPainter({required this.color});

  final Color color;

  @override
  void paint(Canvas canvas, Size size) {
    final rect = Rect.fromCenter(
      center: Offset(size.width / 2, size.height / 2),
      width: size.width,
      height: size.height,
    );
    canvas.drawOval(
      rect,
      Paint()
        ..shader = RadialGradient(
          colors: [
            color.withValues(alpha: 0.35),
            color.withValues(alpha: 0.0),
          ],
        ).createShader(rect),
    );
  }

  @override
  bool shouldRepaint(covariant _GroundShadowPainter old) => old.color != color;
}
