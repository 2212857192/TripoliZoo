import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/shared/api/api_config.dart';
import 'package:tripolizoo/shared/api/token_storage.dart';

class ApiClient {
  ApiClient({TokenStorage? tokenStorage, http.Client? httpClient})
      : _tokenStorage = tokenStorage ?? TokenStorage(),
        _http = httpClient ?? http.Client();

  final TokenStorage _tokenStorage;
  final http.Client _http;

  Future<Map<String, dynamic>> post(
    String path, {
    Map<String, dynamic>? body,
    bool auth = true,
  }) async {
    return _request('POST', path, body: body, auth: auth);
  }

  Future<Map<String, dynamic>> get(
    String path, {
    bool auth = true,
  }) async {
    return _request('GET', path, auth: auth);
  }

  Future<Map<String, dynamic>> _request(
    String method,
    String path, {
    Map<String, dynamic>? body,
    required bool auth,
  }) async {
    final headers = <String, String>{
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    if (auth) {
      final token = await _tokenStorage.readToken();
      if (token == null || token.isEmpty) {
        throw const AuthException('يجب تسجيل الدخول أولاً');
      }
      headers['Authorization'] = 'Bearer $token';
    }

    final uri = ApiConfig.uri(path);
    late http.Response response;

    try {
      switch (method) {
        case 'GET':
          response = await _http.get(uri, headers: headers);
          break;
        case 'POST':
          response = await _http.post(
            uri,
            headers: headers,
            body: body == null ? null : jsonEncode(body),
          );
          break;
        default:
          throw const AuthException('طلب غير مدعوم');
      }
    } on AuthException {
      rethrow;
    } catch (_) {
      throw const AuthException(
        'تعذّر الاتصال بالخادم. تحقق من الإنترنت أو عنوان API.',
      );
    }

    Map<String, dynamic>? decoded;
    if (response.body.isNotEmpty) {
      final parsed = jsonDecode(response.body);
      if (parsed is Map<String, dynamic>) {
        decoded = parsed;
      }
    }

    if (response.statusCode >= 200 && response.statusCode < 300) {
      return decoded ?? <String, dynamic>{};
    }

    throw AuthException(_messageFromResponse(response.statusCode, decoded));
  }

  Future<void> saveToken(String token) => _tokenStorage.saveToken(token);

  Future<void> clearToken() => _tokenStorage.clearToken();

  String _messageFromResponse(int statusCode, Map<String, dynamic>? json) {
    if (json == null) {
      return statusCode == 401 || statusCode == 403
          ? 'انتهت الجلسة أو لا تملك صلاحية الدخول'
          : 'حدث خطأ من الخادم ($statusCode)';
    }

    final errors = json['errors'];
    if (errors is Map) {
      for (final entry in errors.entries) {
        final value = entry.value;
        if (value is List && value.isNotEmpty) {
          return value.first.toString();
        }
      }
    }

    final message = json['message'];
    if (message is String && message.isNotEmpty) {
      return message;
    }

    return 'حدث خطأ غير متوقع، حاول مرة أخرى';
  }
}
