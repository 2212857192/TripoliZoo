import 'dart:async';
import 'dart:html' as html;

import 'visitor_gps_reader.dart';

Future<void> configureGpsReader() async {
  GpsReader.ensurePermission = _ensurePermission;
  GpsReader.currentPosition = _currentPosition;
  GpsReader.watchPosition = _watchPosition;
}

Future<bool> _ensurePermission() async {
  return true;
}

Future<GpsReading?> _currentPosition() async {
  try {
    final position = await html.window.navigator.geolocation!.getCurrentPosition();
    final coords = position.coords;
    if (coords == null) return null;

    final latitude = coords.latitude;
    final longitude = coords.longitude;
    if (latitude == null || longitude == null) return null;

    return GpsReading(
      latitude: latitude.toDouble(),
      longitude: longitude.toDouble(),
    );
  } catch (_) {
    return null;
  }
}

Stream<GpsReading> _watchPosition({Duration interval = const Duration(seconds: 2)}) async* {
  while (true) {
    final position = await _currentPosition();
    if (position != null) {
      yield position;
    }
    await Future<void>.delayed(interval);
  }
}
