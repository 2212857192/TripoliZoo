import 'dart:math' as math;
import 'dart:ui';

/// Helpers for live navigation along a drawn route polyline.
abstract final class MapRouteUtils {
  /// Same conversion factor used when importing GeoJSON edges.
  static const double metersPerPixel = 0.35;

  /// Shortest perpendicular distance from [point] to any segment of [polyline],
  /// returned in approximate real-world metres.
  static double perpendicularDistanceMeters(
    Offset point,
    List<Offset> polyline, {
    required double imageWidth,
    required double imageHeight,
    double metersPerPixel = metersPerPixel,
  }) {
    if (polyline.length < 2) {
      return double.infinity;
    }

    final px = point.dx * imageWidth;
    final py = point.dy * imageHeight;
    var best = double.infinity;

    for (var i = 0; i < polyline.length - 1; i++) {
      final ax = polyline[i].dx * imageWidth;
      final ay = polyline[i].dy * imageHeight;
      final bx = polyline[i + 1].dx * imageWidth;
      final by = polyline[i + 1].dy * imageHeight;
      best = math.min(best, _pointToSegmentDistance(px, py, ax, ay, bx, by));
    }

    return best * metersPerPixel;
  }

  static double _pointToSegmentDistance(
    double px,
    double py,
    double ax,
    double ay,
    double bx,
    double by,
  ) {
    final dx = bx - ax;
    final dy = by - ay;
    if (dx == 0 && dy == 0) {
      return math.sqrt(((px - ax) * (px - ax)) + ((py - ay) * (py - ay)));
    }

    final t = (((px - ax) * dx) + ((py - ay) * dy)) / ((dx * dx) + (dy * dy));
    final clamped = t.clamp(0.0, 1.0);
    final cx = ax + (clamped * dx);
    final cy = ay + (clamped * dy);
    return math.sqrt(((px - cx) * (px - cx)) + ((py - cy) * (py - cy)));
  }
}
