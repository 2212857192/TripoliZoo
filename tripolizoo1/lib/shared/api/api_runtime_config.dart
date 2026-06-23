import 'package:flutter/services.dart';

/// Loads optional runtime API host from [assets/config/api_host.txt].
///
/// Use the same LAN IP as [VISITOR_PUBLIC_URL] on the Laravel server.
/// Leave the file empty or set `emulator` to use Android emulator defaults.
abstract final class ApiRuntimeConfig {
  static String? _host;

  static String? get host => _host;

  static Future<void> load() async {
    try {
      final raw = await rootBundle.loadString('assets/config/api_host.txt');
      final line = raw
          .split(RegExp(r'\r?\n'))
          .map((value) => value.trim())
          .firstWhere(
            (value) => value.isNotEmpty && !value.startsWith('#'),
            orElse: () => '',
          );

      if (line.isEmpty || line.toLowerCase() == 'emulator') {
        _host = null;
        return;
      }

      _host = line.replaceFirst(RegExp(r'^https?://'), '').split(':').first;
    } catch (_) {
      _host = null;
    }
  }
}
