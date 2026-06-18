import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class DeviceTokenService {
  DeviceTokenService({ApiClient? client}) : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<void> register({
    required String token,
    required String platform,
  }) async {
    await _client.post(
      ApiConfig.deviceTokens,
      body: {
        'token': token,
        'platform': platform,
      },
    );
  }

  Future<void> unregister({required String token}) async {
    await _client.delete(
      ApiConfig.deviceTokens,
      body: {'token': token},
    );
  }
}
