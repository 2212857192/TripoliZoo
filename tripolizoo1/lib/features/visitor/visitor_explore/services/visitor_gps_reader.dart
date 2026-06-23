class GpsReading {
  const GpsReading({
    required this.latitude,
    required this.longitude,
    this.accuracyMeters,
  });

  final double latitude;
  final double longitude;
  final double? accuracyMeters;
}

abstract class GpsReader {
  static Future<bool> Function() ensurePermission = () async => false;
  static Future<GpsReading?> Function() currentPosition = () async => null;
  static Stream<GpsReading> Function({Duration interval}) watchPosition =
      ({Duration interval = const Duration(seconds: 2)}) =>
          const Stream.empty();
}
