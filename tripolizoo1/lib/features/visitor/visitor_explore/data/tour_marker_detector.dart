import 'dart:ui' as ui;

import 'package:flutter/services.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/virtual_tour_data.dart';

/// يكتشف العلامات الملونة (أحمر/أخضر/برتقالي) على صور الجولة
/// ويحوّلها لإحداثيات panorama + نوع السهم.
abstract final class TourMarkerDetector {
  static final Map<String, List<TourMarker>> _cache = {};

  static void invalidateScene(String sceneId) => _cache.remove(sceneId);

  static Future<List<TourMarker>> detectForScene(TourScene scene) async {
    if (_cache.containsKey(scene.id)) return _cache[scene.id]!;

    // الإحداثيات اليدوية لها الأولوية — أدق من الاكتشاف التلقائي
    if (scene.manualMarkers.isNotEmpty) {
      _cache[scene.id] = scene.manualMarkers;
      return scene.manualMarkers;
    }

    final asset = await _resolveAsset(scene.markerReferenceAsset ?? scene.imageAsset);
    if (asset == null) return [];

    try {
      final data = await rootBundle.load(asset);
      final markers = await _detect(
        data.buffer.asUint8List(),
        sceneIndex: VirtualTourData.indexOf(scene.id),
        scene: scene,
      );
      if (markers.isNotEmpty) {
        _cache[scene.id] = markers;
        return markers;
      }
    } catch (_) {}

    // جرّب صورة العرض إذا ما وُجدت marked
    if (scene.markerReferenceAsset != null &&
        scene.markerReferenceAsset != scene.imageAsset) {
      try {
        final data = await rootBundle.load(scene.imageAsset);
        final markers = await _detect(
          data.buffer.asUint8List(),
          sceneIndex: VirtualTourData.indexOf(scene.id),
          scene: scene,
        );
        if (markers.isNotEmpty) {
          _cache[scene.id] = markers;
          return markers;
        }
      } catch (_) {}
    }

    return [];
  }

  /// يجرّب أسماء بديلة (مثل 1.JPG.jpg)
  static Future<String?> _resolveAsset(String path) async {
    final base = path.replaceAll('.JPG', '');
    final candidates = [
      path,
      '$base.jpg',
      '$path.jpg',
      path.replaceAll('.JPG', '.JPG.jpg'),
    ];
    for (final candidate in candidates) {
      try {
        await rootBundle.load(candidate);
        return candidate;
      } catch (_) {}
    }
    return null;
  }

  static Future<List<TourMarker>> _detect(
    Uint8List bytes, {
    required int sceneIndex,
    required TourScene scene,
  }) async {
    final codec = await ui.instantiateImageCodec(
      bytes,
      targetWidth: 1984,
    );
    final frame = await codec.getNextFrame();
    final image = frame.image;
    final byteData = await image.toByteData(format: ui.ImageByteFormat.rawRgba);
    if (byteData == null) return [];

    final w = image.width;
    final h = image.height;
    final pixels = byteData.buffer.asUint8List();

    final scenes = VirtualTourData.scenes;
    final detected = <TourMarker>[];

    for (final color in _MarkerColor.values) {
      final center = _findColorCenter(pixels, w, h, color);
      if (center == null) continue;

      final lon = (center.dx / w - 0.5) * 360;
      final lat = (0.5 - center.dy / h) * 180;

      switch (color) {
        case _MarkerColor.red:
          if (sceneIndex + 1 < scenes.length) {
            detected.add(TourMarker(
              type: TourMarkerType.next,
              latitude: lat,
              longitude: lon,
              targetSceneId: scenes[sceneIndex + 1].id,
            ));
          }
        case _MarkerColor.green:
          if (sceneIndex > 0) {
            detected.add(TourMarker(
              type: TourMarkerType.back,
              latitude: lat,
              longitude: lon,
              targetSceneId: scenes[sceneIndex - 1].id,
            ));
          }
        case _MarkerColor.orange:
          detected.add(TourMarker(
            type: TourMarkerType.animalArea,
            latitude: lat,
            longitude: lon,
            label: scene.animalAreaLabel ?? 'منطقة الحيوانات',
          ));
      }
    }

    image.dispose();
    return detected;
  }

  static ui.Offset? _findColorCenter(
    Uint8List pixels,
    int w,
    int h,
    _MarkerColor color,
  ) {
    final minY = (h * 0.15).round();
    final xs = <double>[];
    final ys = <double>[];

    for (var y = minY; y < h; y++) {
      for (var x = 0; x < w; x++) {
        final i = (y * w + x) * 4;
        final r = pixels[i];
        final g = pixels[i + 1];
        final b = pixels[i + 2];
        if (_matches(color, r, g, b)) {
          xs.add(x.toDouble());
          ys.add(y.toDouble());
        }
      }
    }

    if (xs.length < 40) return null;
    xs.sort();
    ys.sort();
    // median = robust against outliers from scenery
    return ui.Offset(xs[xs.length ~/ 2], ys[ys.length ~/ 2]);
  }

  static bool _matches(_MarkerColor color, int r, int g, int b) {
    return switch (color) {
      _MarkerColor.red => r > 200 && g < 110 && b < 110 && r - g > 80,
      _MarkerColor.green => g > 180 && r < 120 && b < 120 && g - r > 70,
      _MarkerColor.orange =>
        r > 200 && g > 90 && g < 200 && b < 90 && r > g && g > b,
    };
  }
}

enum _MarkerColor { red, green, orange }
