import 'dart:convert';

import 'package:flutter/foundation.dart';
// ignore: avoid_web_libraries_in_flutter
import 'dart:html' as html;

void agentLogImpl({
  required String location,
  required String message,
  required Map<String, dynamic> data,
  required String hypothesisId,
}) {
  final payload = jsonEncode({
    'sessionId': '05cc00',
    'timestamp': DateTime.now().millisecondsSinceEpoch,
    'location': location,
    'message': message,
    'data': data,
    'hypothesisId': hypothesisId,
  });
  debugPrint('AGENT_LOG:$payload');
  try {
    html.HttpRequest.request(
      'http://127.0.0.1:7502/ingest/ffe11af6-33e6-45a1-b649-d5a2308cc175',
      method: 'POST',
      sendData: payload,
      requestHeaders: {
        'Content-Type': 'application/json',
        'X-Debug-Session-Id': '05cc00',
      },
    );
  } catch (_) {}
}
