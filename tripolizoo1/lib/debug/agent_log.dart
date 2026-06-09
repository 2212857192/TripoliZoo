import 'agent_log_impl.dart'
    if (dart.library.html) 'agent_log_web.dart'
    if (dart.library.io) 'agent_log_io.dart';

void agentLog({
  required String location,
  required String message,
  required Map<String, dynamic> data,
  required String hypothesisId,
}) {
  agentLogImpl(
    location: location,
    message: message,
    data: data,
    hypothesisId: hypothesisId,
  );
}
