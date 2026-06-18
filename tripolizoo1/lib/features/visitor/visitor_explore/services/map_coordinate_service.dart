import 'dart:ui';

import 'package:tripolizoo/features/visitor/visitor_explore/domain/zoo_map_bounds.dart';

class MapCoordinateService {
  const MapCoordinateService({this.bounds = ZooMapBounds.defaults});

  final ZooMapBounds bounds;

  Offset gpsToNormalized(double latitude, double longitude) {
    final lngSpan = bounds.east - bounds.west;
    final latSpan = bounds.north - bounds.south;

    final x = lngSpan == 0
        ? 0.5
        : (longitude - bounds.west) / lngSpan;
    final y = latSpan == 0
        ? 0.5
        : (bounds.north - latitude) / latSpan;

    return Offset(x.clamp(0.0, 1.0), y.clamp(0.0, 1.0));
  }

  Offset normalizedToPixel(Offset normalized) {
    return Offset(
      normalized.dx * bounds.imageWidth,
      normalized.dy * bounds.imageHeight,
    );
  }

  Offset gpsToPixel(double latitude, double longitude) {
    return normalizedToPixel(gpsToNormalized(latitude, longitude));
  }
}
