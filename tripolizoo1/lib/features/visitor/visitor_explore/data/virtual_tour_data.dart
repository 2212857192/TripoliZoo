enum TourMarkerType { next, back, animalArea }

class TourMarker {
  const TourMarker({
    required this.type,
    required this.latitude,
    required this.longitude,
    this.targetSceneId,
    this.label,
  });

  final TourMarkerType type;
  final double latitude;
  final double longitude;
  final String? targetSceneId;
  final String? label;
}

class TourScene {
  const TourScene({
    required this.id,
    required this.title,
    required this.imageAsset,
    this.markerReferenceAsset,
    this.animalAreaLabel,
    this.manualMarkers = const [],
  });

  final String id;
  final String title;

  /// صورة أصلية نظيفة — للعرض في الجولة
  final String imageAsset;

  /// صورة marked/ بنفس الرقم — للإحداثيات فقط (لا تظهر)
  final String? markerReferenceAsset;
  final String? animalAreaLabel;

  /// إحداثيات مستخرجة من marked/ — لها الأولوية
  final List<TourMarker> manualMarkers;
}

/// 🔴 أمام | 🟢 رجوع | 🟠/🟡 حيوانات
///
/// **الملفات:**
/// - `panorama/1.JPG … 8.JPG` → صور أصلية نظيفة (عرض)
/// - `panorama/marked/1.JPG … 8.JPG` → نفس الأرقام + دوائر ملونة (إحداثيات)
abstract final class VirtualTourData {
  static const String startSceneId = 'scene_1';
  static const String markedFolder = 'assets/images/panorama/marked/';

  static String panorama(int n) => 'assets/images/panorama/$n.JPG';
  static String marked(int n) => '$markedFolder$n.JPG';

  static final List<TourScene> scenes = [
    // 1 — 🔴 lon=39.7 lat=1.1
    TourScene(
      id: 'scene_1',
      title: 'المدخل الرئيسي',
      imageAsset: panorama(1),
      markerReferenceAsset: marked(1),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.next,
          latitude: 1.1,
          longitude: 39.7,
          targetSceneId: 'scene_2',
        ),
      ],
    ),
    // 2 — 🟢 lon=-4.5 lat=1.6 | 🔴 lon=165.3 lat=-2.7
    TourScene(
      id: 'scene_2',
      title: 'بوابة الدخول',
      imageAsset: panorama(2),
      markerReferenceAsset: marked(2),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: 1.6,
          longitude: -4.5,
          targetSceneId: 'scene_1',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -2.7,
          longitude: 165.3,
          targetSceneId: 'scene_3',
        ),
      ],
    ),
    // 3 — 🔴 lon=-14.2 lat=-0.4 | 🟢 lon=176.4 lat=1.8
    TourScene(
      id: 'scene_3',
      title: 'الممر الرئيسي',
      imageAsset: panorama(3),
      markerReferenceAsset: marked(3),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.next,
          latitude: -0.4,
          longitude: -14.2,
          targetSceneId: 'scene_4',
        ),
        TourMarker(
          type: TourMarkerType.back,
          latitude: 1.8,
          longitude: 176.4,
          targetSceneId: 'scene_2',
        ),
      ],
    ),
    // 4 — 🔴 lon=-6.7 lat=-0.4 | 🟢 lon=176.4 lat=0.2 | 🟡 lon=134.5 lat=-8.3
    TourScene(
      id: 'scene_4',
      title: 'منطقة الطيور',
      imageAsset: panorama(4),
      markerReferenceAsset: marked(4),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.next,
          latitude: -0.4,
          longitude: -6.7,
          targetSceneId: 'scene_5',
        ),
        TourMarker(
          type: TourMarkerType.back,
          latitude: 0.2,
          longitude: 176.4,
          targetSceneId: 'scene_3',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: -8.3,
          longitude: 134.5,
          label: 'منطقة الطيور',
        ),
      ],
    ),
    // 5 — 🟡 lon=-63.9 lat=-1.8 | 🟢 lon=175.8 lat=-3.1
    TourScene(
      id: 'scene_5',
      title: 'بداية طريق الخيول',
      imageAsset: panorama(5),
      markerReferenceAsset: marked(5),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.next,
          latitude: -1.8,
          longitude: -63.9,
          targetSceneId: 'scene_6',
          label: 'طريق الخيول',
        ),
        TourMarker(
          type: TourMarkerType.back,
          latitude: -3.1,
          longitude: 175.8,
          targetSceneId: 'scene_4',
        ),
      ],
    ),
    // 6 — صورة أصلية 5952×2976 | 🟢 lon=-16.3 lat=0.5 | 🔴 lon=46.6 lat=4.4
    TourScene(
      id: 'scene_6',
      title: 'طريق الخيول',
      imageAsset: panorama(6),
      markerReferenceAsset: marked(6),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: 0.5,
          longitude: -16.3,
          targetSceneId: 'scene_5',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: 4.4,
          longitude: 46.6,
          targetSceneId: 'scene_7',
          label: 'حصان 2',
        ),
      ],
    ),
    // 7 — صورة أصلية 5952×2976 | 🟢 lon=-102.7 lat=-0.2 | 🟠 lon=162.9 lat=-2.2
    // لا يوجد سهم أمام → الكاميرا تبدأ من المركز (الخيول مباشرة أمامك)
    TourScene(
      id: 'scene_7',
      title: 'منطقة الخيول',
      imageAsset: panorama(7),
      markerReferenceAsset: marked(7),
      animalAreaLabel: 'حصان 1',
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -0.2,
          longitude: -102.7,
          targetSceneId: 'scene_8',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: -2.2,
          longitude: 162.9,
          label: 'حصان 1',
        ),
      ],
    ),
    // 8 — صورة أصلية 5952×2976 | 🟢 lon=175.8 lat=-3.1 | 🔴 lon=-5.8 lat=-2.4
    TourScene(
      id: 'scene_8',
      title: 'الساحة — العودة',
      imageAsset: panorama(8),
      markerReferenceAsset: marked(8),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -3.1,
          longitude: 175.8,
          targetSceneId: 'scene_7',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -2.4,
          longitude: -5.8,
          targetSceneId: 'scene_5',
        ),
      ],
    ),
  ];

  static TourScene sceneById(String id) {
    return scenes.firstWhere(
      (scene) => scene.id == id,
      orElse: () => scenes.first,
    );
  }

  static int indexOf(String id) {
    return scenes.indexWhere((scene) => scene.id == id);
  }

  static bool isHorseRoute(String sceneId) {
    return sceneId == 'scene_5' ||
        sceneId == 'scene_6' ||
        sceneId == 'scene_7' ||
        sceneId == 'scene_8';
  }

  /// مسار الجولة التلقائية الكامل — من المدخل إلى الساحة
  static const List<String> walkthroughRoute = [
    'scene_1',
    'scene_2',
    'scene_3',
    'scene_4',
    'scene_5',
    'scene_6',
    'scene_7',
    'scene_8',
  ];

  /// المشهد التالي في الجولة التلقائية
  static String? walkthroughNextSceneId(String currentId) {
    final idx = walkthroughRoute.indexOf(currentId);
    if (idx == -1 || idx >= walkthroughRoute.length - 1) return null;
    return walkthroughRoute[idx + 1];
  }

  /// نقطة الخروج للمشهد التالي (اتجاه المشي)
  static TourMarker? walkthroughExitMarker(String currentId, String nextId) {
    for (final m in sceneById(currentId).manualMarkers) {
      if (m.type == TourMarkerType.next && m.targetSceneId == nextId) {
        return m;
      }
    }
    if (currentId == 'scene_7' && nextId == 'scene_8') {
      return const TourMarker(
        type: TourMarkerType.next,
        latitude: -0.2,
        longitude: -102.7,
        targetSceneId: 'scene_8',
        label: 'الساحة',
      );
    }
    return null;
  }

  /// نقطة الدخول — من أين أتينا (🟢 يشير للمشهد السابق)
  static TourMarker? entryMarker(String sceneId, String fromSceneId) {
    for (final m in sceneById(sceneId).manualMarkers) {
      if (m.type == TourMarkerType.back && m.targetSceneId == fromSceneId) {
        return m;
      }
    }
    return null;
  }

  static List<TourMarker> animalMarkers(String sceneId) {
    return sceneById(sceneId)
        .manualMarkers
        .where((m) => m.type == TourMarkerType.animalArea)
        .toList();
  }
}
