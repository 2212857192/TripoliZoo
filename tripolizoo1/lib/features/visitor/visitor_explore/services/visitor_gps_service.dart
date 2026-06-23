import 'dart:async';

import 'visitor_gps_reader.dart';

class VisitorGpsPosition {
  const VisitorGpsPosition({
    required this.latitude,
    required this.longitude,
    this.accuracyMeters,
  });

  final double latitude;
  final double longitude;
  final double? accuracyMeters;
}

class VisitorGpsService {
  Future<bool> ensurePermission() => GpsReader.ensurePermission();

  Future<VisitorGpsPosition?> currentPosition() async {
    final allowed = await ensurePermission();
    if (!allowed) return null;

    try {
      final position = await GpsReader.currentPosition();
      if (position == null) return null;

      return VisitorGpsPosition(
        latitude: position.latitude,
        longitude: position.longitude,
        accuracyMeters: position.accuracyMeters,
      );
    } catch (_) {
      return null;
    }
  }

  Stream<VisitorGpsPosition> watchPosition({
    Duration interval = const Duration(seconds: 2),
  }) async* {
    final allowed = await ensurePermission();
    if (!allowed) {
      return;
    }

    yield* GpsReader.watchPosition(interval: interval).map(
      (position) => VisitorGpsPosition(
        latitude: position.latitude,
        longitude: position.longitude,
        accuracyMeters: position.accuracyMeters,
      ),
    );
  }
}
