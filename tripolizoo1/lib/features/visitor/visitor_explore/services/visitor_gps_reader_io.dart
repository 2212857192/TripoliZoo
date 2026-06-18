import 'dart:async';

import 'package:geolocator/geolocator.dart';

import 'visitor_gps_reader.dart';

Future<void> configureGpsReader() async {
  GpsReader.ensurePermission = _ensurePermission;
  GpsReader.currentPosition = _currentPosition;
  GpsReader.watchPosition = _watchPosition;
}

Future<bool> _ensurePermission() async {
  var permission = await Geolocator.checkPermission();
  if (permission == LocationPermission.denied) {
    permission = await Geolocator.requestPermission();
  }

  return permission == LocationPermission.always ||
      permission == LocationPermission.whileInUse;
}

Future<GpsReading?> _currentPosition() async {
  final position = await Geolocator.getCurrentPosition(
    locationSettings: const LocationSettings(
      accuracy: LocationAccuracy.high,
    ),
  );

  return GpsReading(
    latitude: position.latitude,
    longitude: position.longitude,
  );
}

Stream<GpsReading> _watchPosition({Duration interval = const Duration(seconds: 2)}) {
  return Geolocator.getPositionStream(
    locationSettings: LocationSettings(
      accuracy: LocationAccuracy.high,
      distanceFilter: 3,
    ),
  ).map(
    (position) => GpsReading(
      latitude: position.latitude,
      longitude: position.longitude,
    ),
  );
}
