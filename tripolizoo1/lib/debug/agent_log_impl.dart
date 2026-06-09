import 'dart:convert';

import 'package:flutter/foundation.dart';

void agentLogImpl({
  required String location,
  required String message,
  required Map<String, dynamic> data,
  required String hypothesisId,
}) {
  debugPrint(
    'AGENT_LOG:${jsonEncode({
      'sessionId': '05cc00',
      'timestamp': DateTime.now().millisecondsSinceEpoch,
      'location': location,
      'message': message,
      'data': data,
      'hypothesisId': hypothesisId,
    })}',
  );
}
