import 'visitor_gps_reader_stub.dart'
    if (dart.library.io) 'visitor_gps_reader_io.dart'
    if (dart.library.html) 'visitor_gps_reader_web.dart' as platform;

Future<void> configureVisitorGpsReader() => platform.configureGpsReader();
