import 'dart:convert';
import 'dart:io';

import 'package:flutter/foundation.dart';

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
    File(r'd:\TripoliZoo\debug-05cc00.log').writeAsStringSync(
      '$payload\n',
      mode: FileMode.append,
    );
  } catch (_) {}
}
