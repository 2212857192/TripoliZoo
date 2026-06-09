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
/// - `panorama/1.JPG … 30.JPG` → صور أصلية نظيفة (عرض)
/// - `panorama/marked/1.JPG … 30.JPG` → نفس الأرقام + دوائر ملونة (إحداثيات)
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
    // 4 — 🔴 lon=-6.7 lat=-0.4 | 🟢 lon=176.4 lat=0.2
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
      ],
    ),
    // 5 — 🟢 رجوع→4 lon=175.8 | 🔴 انتقل→9 lon=-5.8 | 🟡 خيول→6 lon=-63.9
    TourScene(
      id: 'scene_5',
      title: 'بداية طريق الخيول',
      imageAsset: panorama(5),
      markerReferenceAsset: marked(5),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -3.1,
          longitude: 175.8,
          targetSceneId: 'scene_4',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -2.4,
          longitude: -5.8,
          targetSceneId: 'scene_9',
          label: 'انتقل',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -1.8,
          longitude: -63.9,
          targetSceneId: 'scene_6',
          label: 'منطقة الخيول',
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
    // 7 — 🟢 رجوع→6 lon=46.6 lat=4.4 | 🔴 ساحة→8 lon=-102.7 lat=-0.2 | 🟠 حصان 1 lon=162.9 lat=-2.2
    TourScene(
      id: 'scene_7',
      title: 'منطقة الخيول',
      imageAsset: panorama(7),
      markerReferenceAsset: marked(7),
      animalAreaLabel: 'حصان 1',
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: 4.4,
          longitude: 46.6,
          targetSceneId: 'scene_6',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -0.2,
          longitude: -102.7,
          targetSceneId: 'scene_8',
          label: 'الساحة',
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
          targetSceneId: 'scene_9',
        ),
      ],
    ),
    // 9 — 🔴 قدام lon=0.2 lat=-2.2 | 🟡 منطقة الخيول lon=-128.1 lat=-0.5 | 🟢 رجوع lon=177.1 lat=-3.1
    TourScene(
      id: 'scene_9',
      title: 'منطقة التمساح',
      imageAsset: panorama(9),
      markerReferenceAsset: marked(9),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -3.1,
          longitude: 177.1,
          targetSceneId: 'scene_8',
        ),
        TourMarker(
          type: TourMarkerType.back,
          latitude: -2.2,
          longitude: 0.2,
          targetSceneId: 'scene_5',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -2.2,
          longitude: 8.0,
          targetSceneId: 'scene_10',
          label: 'انتقل',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -0.5,
          longitude: -128.1,
          targetSceneId: 'scene_5',
          label: 'منطقة الخيول',
        ),
      ],
    ),
    // 10 — 🔴 قدام lon=-5.1 lat=-1.3 | 🟡 منطقة الخيول lon=-136.5 lat=-0.2 | 🟢 رجوع lon=174.0 lat=2.7
    TourScene(
      id: 'scene_10',
      title: 'الساحة الوسطى',
      imageAsset: panorama(10),
      markerReferenceAsset: marked(10),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: 2.7,
          longitude: 174.0,
          targetSceneId: 'scene_9',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -1.3,
          longitude: -5.1,
          targetSceneId: 'scene_11',
          label: 'انتقل',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -0.2,
          longitude: -136.5,
          targetSceneId: 'scene_5',
          label: 'منطقة الخيول',
        ),
      ],
    ),
    // 11 — 🟢 lon=175.8 lat=2.0 | 🟡×3 مناطق حيوانات
    TourScene(
      id: 'scene_11',
      title: 'ممر المناطق',
      imageAsset: panorama(11),
      markerReferenceAsset: marked(11),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: 2.0,
          longitude: 175.8,
          targetSceneId: 'scene_10',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: -1.3,
          longitude: -44.8,
          label: 'بركة البط والبجع',
          targetSceneId: 'scene_12',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 1.5,
          longitude: 14.9,
          label: 'منطقة الزواحف',
          targetSceneId: 'scene_18',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 4.5,
          longitude: 59.5,
          label: 'منطقة الأسود والنمور',
          targetSceneId: 'scene_24',
        ),
      ],
    ),
    // 12 — 🟢 lon=-161.3 lat=-5.1 | 🔴 lon=0.7 lat=0.0
    TourScene(
      id: 'scene_12',
      title: 'بداية طريق البط والبجع',
      imageAsset: panorama(12),
      markerReferenceAsset: marked(12),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -5.1,
          longitude: -161.3,
          targetSceneId: 'scene_11',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: 0.0,
          longitude: 0.7,
          targetSceneId: 'scene_13',
          label: 'طريق البط والبجع',
        ),
      ],
    ),
    // 13 — 🟢 lon=158.0 lat=-3.8 | 🔴 lon=5.4 lat=-0.7 | 🟠 بركة البط والبجع
    TourScene(
      id: 'scene_13',
      title: 'طريق البط والبجع',
      imageAsset: panorama(13),
      markerReferenceAsset: marked(13),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -3.8,
          longitude: 158.0,
          targetSceneId: 'scene_12',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -0.7,
          longitude: 5.4,
          targetSceneId: 'scene_14',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 2.5,
          longitude: -37.4,
          label: 'بركة البط والبجع',
        ),
      ],
    ),
    // 14 — 🟢 lon=-159.9 lat=-6.2 | 🔴 lon=33.9 lat=0.9 | 🟠 lon=13.6 lat=-3.3
    TourScene(
      id: 'scene_14',
      title: 'ممر البط والبجع',
      imageAsset: panorama(14),
      markerReferenceAsset: marked(14),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -6.2,
          longitude: -159.9,
          targetSceneId: 'scene_13',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: 0.9,
          longitude: 33.9,
          targetSceneId: 'scene_15',
        ),
      ],
    ),
    // 15 — 🟢 lon=173.6 lat=-2.0 | 🟡 بركة البط والبجع تظهر أمامك → scene_16
    // ملاحظة: الأحمر lon=-114.9 هو صنبور حريق (false positive) — لا يُستخدم
    TourScene(
      id: 'scene_15',
      title: 'ممر البط والبجع',
      imageAsset: panorama(15),
      markerReferenceAsset: marked(15),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -2.0,
          longitude: 173.6,
          targetSceneId: 'scene_14',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 0.5,
          longitude: -12.7,
          targetSceneId: 'scene_16',
          label: 'بركة البط والبجع',
        ),
      ],
    ),
    // 16 — 🟢 lon=-145.9 lat=0.9 | 🔴 lon=41.2 lat=-4.5 | 🟠 lon=43.5 lat=-3.6
    TourScene(
      id: 'scene_16',
      title: 'قرب بركة البط',
      imageAsset: panorama(16),
      markerReferenceAsset: marked(16),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: 0.9,
          longitude: -145.9,
          targetSceneId: 'scene_15',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -4.5,
          longitude: 41.2,
          targetSceneId: 'scene_17',
        ),
      ],
    ),
    // 17 — 🟢 lon=-144.1 lat=-1.6 | بركة البط والبجع
    TourScene(
      id: 'scene_17',
      title: 'بركة البط والبجع',
      imageAsset: panorama(17),
      markerReferenceAsset: marked(17),
      animalAreaLabel: 'بركة البط والبجع',
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -1.6,
          longitude: -144.1,
          targetSceneId: 'scene_16',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 9.1,
          longitude: 0.0,
          label: 'بركة البط والبجع',
        ),
      ],
    ),
    // 18 — 🟢 lon=-169.1 lat=-1.8 | 🔴 lon=-45.5 lat=0.2 (المسار الأزرق — ليس المظلة الحمراء)
    TourScene(
      id: 'scene_18',
      title: 'بداية طريق الزواحف',
      imageAsset: panorama(18),
      markerReferenceAsset: marked(18),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -1.8,
          longitude: -169.1,
          targetSceneId: 'scene_11',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: 0.2,
          longitude: -45.5,
          targetSceneId: 'scene_19',
          label: 'طريق الزواحف',
        ),
      ],
    ),
    // 19 — 🟢 lon=-170.9 lat=-2.5 | 🟡 باب بيت الزواحف → scene_20
    TourScene(
      id: 'scene_19',
      title: 'بيت الزواحف — المدخل',
      imageAsset: panorama(19),
      markerReferenceAsset: marked(19),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -2.5,
          longitude: -170.9,
          targetSceneId: 'scene_18',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 4.9,
          longitude: -4.2,
          targetSceneId: 'scene_20',
        ),
      ],
    ),
    // 20 — 🟢 lon=158.8 lat=-1.8 | 🔴 lon=-35.0 lat=-2.9
    TourScene(
      id: 'scene_20',
      title: 'داخل بيت الزواحف',
      imageAsset: panorama(20),
      markerReferenceAsset: marked(20),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -1.8,
          longitude: 158.8,
          targetSceneId: 'scene_19',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -2.9,
          longitude: -35.0,
          targetSceneId: 'scene_21',
        ),
      ],
    ),
    // 21 — 🟢 lon=153.1 lat=-0.5 | 🔴 lon=23.2 lat=-1.6
    TourScene(
      id: 'scene_21',
      title: 'ممر الزواحف',
      imageAsset: panorama(21),
      markerReferenceAsset: marked(21),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -0.5,
          longitude: 153.1,
          targetSceneId: 'scene_20',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -1.6,
          longitude: 23.2,
          targetSceneId: 'scene_22',
        ),
      ],
    ),
    // 22 — 🟢 lon=158.4 lat=-2.0 | 🟡 الأفعى السوداء → scene_23
    TourScene(
      id: 'scene_22',
      title: 'معرض الزواحف',
      imageAsset: panorama(22),
      markerReferenceAsset: marked(22),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -2.0,
          longitude: 158.4,
          targetSceneId: 'scene_21',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 1.5,
          longitude: -29.8,
          label: 'الأفعى السوداء',
          targetSceneId: 'scene_23',
        ),
      ],
    ),
    // 23 — 🟢 lon=-127.6 lat=-7.6 → scene_18 (خروج)
    TourScene(
      id: 'scene_23',
      title: 'نهاية طريق الزواحف',
      imageAsset: panorama(23),
      markerReferenceAsset: marked(23),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -7.6,
          longitude: -127.6,
          targetSceneId: 'scene_18',
        ),
      ],
    ),
    // 24 — 🟢 lon=176.2 lat=-0.7 | 🔴 lon=-2.9 lat=-1.3
    TourScene(
      id: 'scene_24',
      title: 'منطقة الأسود والنمور',
      imageAsset: panorama(24),
      markerReferenceAsset: marked(24),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -0.7,
          longitude: 176.2,
          targetSceneId: 'scene_11',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -1.3,
          longitude: -2.9,
          targetSceneId: 'scene_25',
          label: 'ممر الأسود والنمور',
        ),
      ],
    ),
    // 25 — 🟢 lon=167.8 lat=-2.4 | 🔴 lon=-16.9 lat=-1.6 | 🟡 النمر
    TourScene(
      id: 'scene_25',
      title: 'ممر الأسود',
      imageAsset: panorama(25),
      markerReferenceAsset: marked(25),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -2.4,
          longitude: 167.8,
          targetSceneId: 'scene_24',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -1.6,
          longitude: -16.9,
          targetSceneId: 'scene_26',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: -1.5,
          longitude: -63.1,
          label: 'النمر',
        ),
      ],
    ),
    // 26 — 🟢 lon=-151.7 lat=-16.0 | 🔴 lon=70.2 lat=-2.4 | قفص نمور
    TourScene(
      id: 'scene_26',
      title: 'قفص نمور',
      imageAsset: panorama(26),
      markerReferenceAsset: marked(26),
      animalAreaLabel: 'قفص نمور',
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -16.0,
          longitude: -151.7,
          targetSceneId: 'scene_25',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: -2.4,
          longitude: 70.2,
          targetSceneId: 'scene_27',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 26.3,
          longitude: -6.4,
          label: 'قفص نمور',
        ),
      ],
    ),
    // 27 — 🟢 lon=-168.4 lat=0.7 | 🔴 lon=-10.7 lat=4.4 | 🟠 النمر
    TourScene(
      id: 'scene_27',
      title: 'قرب عرين الأسود',
      imageAsset: panorama(27),
      markerReferenceAsset: marked(27),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: 0.7,
          longitude: -168.4,
          targetSceneId: 'scene_26',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: 4.4,
          longitude: -10.7,
          targetSceneId: 'scene_28',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 11.2,
          longitude: -103.4,
          label: 'النمر',
        ),
      ],
    ),
    // 28 — 🟢 lon=174.7 lat=-3.8 | 🔴 lon=-33.4 lat=2.7
    TourScene(
      id: 'scene_28',
      title: 'قرب عرين النمور',
      imageAsset: panorama(28),
      markerReferenceAsset: marked(28),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -3.8,
          longitude: 174.7,
          targetSceneId: 'scene_27',
        ),
        TourMarker(
          type: TourMarkerType.next,
          latitude: 2.7,
          longitude: -33.4,
          targetSceneId: 'scene_29',
        ),
      ],
    ),
    // 29 — 🟢 lon=-100.7 lat=1.6 | 🟠 الأشبال → scene_30 (لا يوجد 🔴)
    TourScene(
      id: 'scene_29',
      title: 'الأشبال',
      imageAsset: panorama(29),
      markerReferenceAsset: marked(29),
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: 1.6,
          longitude: -100.7,
          targetSceneId: 'scene_28',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 6.2,
          longitude: -52.0,
          targetSceneId: 'scene_30',
          label: 'الأشبال',
        ),
      ],
    ),
    // 30 — 🟢 lon=165.7 lat=-6.7 | الشبل
    TourScene(
      id: 'scene_30',
      title: 'الشبل',
      imageAsset: panorama(30),
      markerReferenceAsset: marked(30),
      animalAreaLabel: 'الشبل',
      manualMarkers: [
        TourMarker(
          type: TourMarkerType.back,
          latitude: -6.7,
          longitude: 165.7,
          targetSceneId: 'scene_24',
        ),
        TourMarker(
          type: TourMarkerType.animalArea,
          latitude: 2.4,
          longitude: -2.9,
          label: 'الشبل',
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
        sceneId == 'scene_8' ||
        sceneId == 'scene_9' ||
        sceneId == 'scene_10' ||
        sceneId == 'scene_11';
  }

  static bool isDuckRoute(String sceneId) {
    return sceneId == 'scene_12' ||
        sceneId == 'scene_13' ||
        sceneId == 'scene_14' ||
        sceneId == 'scene_15' ||
        sceneId == 'scene_16' ||
        sceneId == 'scene_17';
  }

  static bool isReptileRoute(String sceneId) {
    return sceneId == 'scene_18' ||
        sceneId == 'scene_19' ||
        sceneId == 'scene_20' ||
        sceneId == 'scene_21' ||
        sceneId == 'scene_22' ||
        sceneId == 'scene_23';
  }

  static bool isBigCatsRoute(String sceneId) {
    return sceneId == 'scene_24' ||
        sceneId == 'scene_25' ||
        sceneId == 'scene_26' ||
        sceneId == 'scene_27' ||
        sceneId == 'scene_28' ||
        sceneId == 'scene_29' ||
        sceneId == 'scene_30';
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
    'scene_9',
    'scene_10',
    'scene_11',
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
      if (m.targetSceneId == nextId &&
          (m.type == TourMarkerType.next ||
              m.type == TourMarkerType.animalArea)) {
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

  /// نقطة الدخول — البوابة نحو المشهد الذي أتينا منه (back أو next)
  static TourMarker? entryMarker(String sceneId, String fromSceneId) {
    TourMarker? back;
    TourMarker? other;
    for (final m in sceneById(sceneId).manualMarkers) {
      if (m.targetSceneId != fromSceneId) continue;
      if (m.type == TourMarkerType.back) back = m;
      other ??= m;
    }
    return back ?? other;
  }

  static List<TourMarker> animalMarkers(String sceneId) {
    return sceneById(sceneId)
        .manualMarkers
        .where(
          (m) =>
              m.type == TourMarkerType.animalArea && m.targetSceneId == null,
        )
        .toList();
  }
}
