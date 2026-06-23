import 'dart:math' as math;
import 'dart:ui';

class MapGpsCalibrationPoint {
  const MapGpsCalibrationPoint({
    required this.lat,
    required this.lng,
    required this.pixelX,
    required this.pixelY,
    this.label,
  });

  final double lat;
  final double lng;
  final double pixelX;
  final double pixelY;
  final String? label;

  factory MapGpsCalibrationPoint.fromJson(Map<String, dynamic> json) {
    return MapGpsCalibrationPoint(
      lat: (json['lat'] as num).toDouble(),
      lng: (json['lng'] as num).toDouble(),
      pixelX: (json['pixel_x'] as num).toDouble(),
      pixelY: (json['pixel_y'] as num).toDouble(),
      label: json['label']?.toString(),
    );
  }
}

class MapGpsCalibration {
  const MapGpsCalibration({
    required this.points,
    required this.boundaryPolygon,
  });

  final List<MapGpsCalibrationPoint> points;
  final List<({double lat, double lng})> boundaryPolygon;

  factory MapGpsCalibration.fromJson(Map<String, dynamic> json) {
    final rawPoints = json['points'] as List? ?? const [];
    final rawBoundary = json['boundary_polygon'] as List? ?? const [];

    return MapGpsCalibration(
      points: rawPoints
          .whereType<Map<String, dynamic>>()
          .map(MapGpsCalibrationPoint.fromJson)
          .toList(),
      boundaryPolygon: rawBoundary
          .whereType<Map<String, dynamic>>()
          .map(
            (point) => (
              lat: (point['lat'] as num).toDouble(),
              lng: (point['lng'] as num).toDouble(),
            ),
          )
          .toList(),
    );
  }

  static const empty = MapGpsCalibration(points: [], boundaryPolygon: []);
}

/// Converts GPS readings to normalized map coordinates using calibration
/// triangles and validates geofencing against the zoo boundary polygon.
class MapGpsLocator {
  const MapGpsLocator(this.calibration);

  final MapGpsCalibration calibration;

  bool get hasCalibration => calibration.points.length >= 3;

  bool isInsideBoundary(double lat, double lng) {
    final polygon = calibration.boundaryPolygon;
    if (polygon.length < 3) {
      return true;
    }

    var inside = false;
    for (var i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
      final yi = polygon[i].lat;
      final xi = polygon[i].lng;
      final yj = polygon[j].lat;
      final xj = polygon[j].lng;

      final intersects = ((yi > lat) != (yj > lat)) &&
          (lng <
              (xj - xi) * (lat - yi) / ((yj - yi) == 0 ? 1e-12 : (yj - yi)) +
                  xi);
      if (intersects) inside = !inside;
    }

    return inside;
  }

  /// Returns normalized (0–1) map position, or null when GPS is outside
  /// the calibrated triangulation area.
  Offset? gpsToNormalized(double lat, double lng) {
    final points = calibration.points;
    if (points.length < 3) {
      return null;
    }

    for (var i = 1; i < points.length - 1; i++) {
      final a = points[0];
      final b = points[i];
      final c = points[i + 1];
      final weights = _barycentric(
        lat,
        lng,
        a.lat,
        a.lng,
        b.lat,
        b.lng,
        c.lat,
        c.lng,
      );
      if (weights == null) continue;

      final (u, v, w) = weights;
      if (u < -1e-6 || v < -1e-6 || w < -1e-6) continue;

      return Offset(
        (u * a.pixelX + v * b.pixelX + w * c.pixelX).clamp(0.0, 1.0),
        (u * a.pixelY + v * b.pixelY + w * c.pixelY).clamp(0.0, 1.0),
      );
    }

    return null;
  }

  (double u, double v, double w)? _barycentric(
    double px,
    double py,
    double ax,
    double ay,
    double bx,
    double by,
    double cx,
    double cy,
  ) {
    final denominator = ((by - cy) * (ax - cx)) + ((cx - bx) * (ay - cy));
    if (denominator.abs() < 1e-12) {
      return null;
    }

    final u = (((by - cy) * (px - cx)) + ((cx - bx) * (py - cy))) / denominator;
    final v = (((cy - ay) * (px - cx)) + ((ax - cx) * (py - cy))) / denominator;
    final w = 1 - u - v;
    return (u, v, w);
  }

  double pixelDistanceToNearestNode({
    required double tapPixelX,
    required double tapPixelY,
    required List<({double x, double y})> nodes,
    required double imageWidth,
    required double imageHeight,
  }) {
    if (nodes.isEmpty) return double.infinity;

    var best = double.infinity;
    for (final node in nodes) {
      final dx = tapPixelX - (node.x * imageWidth);
      final dy = tapPixelY - (node.y * imageHeight);
      best = math.min(best, math.sqrt((dx * dx) + (dy * dy)));
    }
    return best;
  }
}
