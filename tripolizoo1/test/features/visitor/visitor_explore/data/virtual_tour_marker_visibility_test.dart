import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/virtual_tour_data.dart';

void main() {
  group('VirtualTourData.markerDisplayLabel', () {
    test('hides generic navigation labels only', () {
      expect(
        VirtualTourData.markerDisplayLabel(
          const TourMarker(
            type: TourMarkerType.next,
            latitude: 0,
            longitude: 0,
            label: 'انتقل',
          ),
        ),
        isNull,
      );
      expect(
        VirtualTourData.markerDisplayLabel(
          const TourMarker(
            type: TourMarkerType.back,
            latitude: 0,
            longitude: 0,
            label: 'رجوع',
          ),
        ),
        isNull,
      );
      expect(
        VirtualTourData.markerDisplayLabel(
          const TourMarker(
            type: TourMarkerType.next,
            latitude: 0,
            longitude: 0,
            label: 'منطقة الخيول',
          ),
        ),
        'منطقة الخيول',
      );
      expect(
        VirtualTourData.markerDisplayLabel(
          const TourMarker(
            type: TourMarkerType.animalArea,
            latitude: 0,
            longitude: 0,
            label: 'الحصان الأول',
          ),
        ),
        'الحصان الأول',
      );
    });
  });

  group('VirtualTourData.filterVisibleMarkers', () {
    test('scene_5 hides horse branch re-entry after returning from scene_6', () {
      final markers = VirtualTourData.sceneById('scene_5').manualMarkers;
      final visible = VirtualTourData.filterVisibleMarkers(
        'scene_5',
        markers,
        arrivedFromSceneId: 'scene_6',
      );

      expect(
        visible.any(
          (m) => m.type == TourMarkerType.next && m.targetSceneId == 'scene_6',
        ),
        isFalse,
      );
      expect(
        visible.any(
          (m) => m.type == TourMarkerType.next && m.targetSceneId == 'scene_9',
        ),
        isTrue,
      );
    });

    test('scene_8 hides wrong back when returning from scene_9', () {
      final markers = VirtualTourData.sceneById('scene_8').manualMarkers;
      final visible = VirtualTourData.filterVisibleMarkers(
        'scene_8',
        markers,
        arrivedFromSceneId: 'scene_9',
      );

      expect(
        visible.any(
          (m) => m.type == TourMarkerType.back && m.targetSceneId == 'scene_7',
        ),
        isFalse,
      );
      expect(
        visible.any(
          (m) => m.type == TourMarkerType.next && m.targetSceneId == 'scene_9',
        ),
        isTrue,
      );
    });

    test('big cats route keeps forward navigation after returning', () {
      final markers = VirtualTourData.sceneById('scene_26').manualMarkers;
      final visible = VirtualTourData.filterVisibleMarkers(
        'scene_26',
        markers,
        arrivedFromSceneId: 'scene_27',
      );

      expect(
        visible.any(
          (m) => m.type == TourMarkerType.next && m.targetSceneId == 'scene_27',
        ),
        isTrue,
      );
    });

    test('view-only animal markers are excluded from hotspots', () {
      final markers = VirtualTourData.sceneById('scene_26').manualMarkers;
      expect(
        markers.where(VirtualTourData.isViewOnlyAnimalMarker).length,
        1,
      );
    });

    test('scene_7 shows only الساحة forward navigation', () {
      final markers = VirtualTourData.sceneById('scene_7').manualMarkers;
      final visible = VirtualTourData.filterVisibleMarkers(
        'scene_7',
        markers,
        arrivedFromSceneId: 'scene_6',
      );

      expect(
        visible.any((m) => m.type == TourMarkerType.back),
        isFalse,
      );
      expect(
        visible.any(
          (m) =>
              m.type == TourMarkerType.next &&
              m.targetSceneId == 'scene_8' &&
              m.label == 'الساحة',
        ),
        isTrue,
      );
    });
  });

  group('VirtualTourData.walkthrough', () {
    test('scene_7 continues to الساحة in auto tour', () {
      expect(
        VirtualTourData.walkthroughNextSceneId('scene_7'),
        'scene_8',
      );
      expect(
        VirtualTourData.walkthroughExitMarker('scene_7', 'scene_8')?.label,
        'الساحة',
      );
      expect(VirtualTourData.walkthroughAnimalMarkers('scene_7'), isEmpty);
    });
  });
}
