import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/shared/api/api_config.dart';
import 'package:tripolizoo/shared/api/api_debug_log.dart';
import 'package:tripolizoo/shared/api/token_storage.dart';

class MultipartUpload {
  const MultipartUpload({
    required this.bytes,
    required this.filename,
  });

  final List<int> bytes;
  final String filename;
}

class MultipartPathUpload {
  const MultipartPathUpload({
    required this.path,
    required this.filename,
  });

  final String path;
  final String filename;
}

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

  Future<Map<String, dynamic>> postMultipart(
    String path, {
    required Map<String, String> fields,
    Map<String, MultipartUpload> files = const {},
    Map<String, MultipartPathUpload> filePaths = const {},
    bool auth = true,
  }) async {
    final headers = <String, String>{
      'Accept': 'application/json',
    };

    if (auth) {
      final token = await _tokenStorage.readToken();
      if (token == null || token.isEmpty) {
        throw const AuthException('يجب تسجيل الدخول أولاً');
      }
      headers['Authorization'] = 'Bearer $token';
    }

    final uri = ApiConfig.uri(path);
    final request = http.MultipartRequest('POST', uri);
    request.headers.addAll(headers);
    request.fields.addAll(fields);

    for (final entry in filePaths.entries) {
      request.files.add(
        await http.MultipartFile.fromPath(
          entry.key,
          entry.value.path,
          filename: entry.value.filename,
          contentType: _imageContentType(entry.value.filename),
        ),
      );
    }

    for (final entry in files.entries) {
      request.files.add(
        http.MultipartFile.fromBytes(
          entry.key,
          entry.value.bytes,
          filename: entry.value.filename,
          contentType: _imageContentType(entry.value.filename),
        ),
      );
    }

    ApiDebugLog.info('ApiClient', 'POST multipart $uri');

    late http.Response response;
    try {
      final streamed = await _http.send(request);
      response = await http.Response.fromStream(streamed);
    } on AuthException {
      rethrow;
    } catch (e) {
      ApiDebugLog.error('ApiClient', 'فشل الاتصال بـ $uri', e);
      throw const AuthException(
        'تعذّر الاتصال بالخادم. تحقق من الإنترنت أو عنوان API.',
      );
    }

    ApiDebugLog.info(
      'ApiClient',
      '← ${response.statusCode} ${response.body.length} bytes',
    );

    if (kDebugMode && response.statusCode >= 400) {
      final preview = response.body.length > 300
          ? '${response.body.substring(0, 300)}...'
          : response.body;
      ApiDebugLog.error('ApiClient', 'استجابة الخطأ: $preview');
    }

    return _decodeResponse(response, uri);
  }

  Future<Map<String, dynamic>> get(
    String path, {
    bool auth = true,
  }) async {
    return _request('GET', path, auth: auth);
  }

  Future<Map<String, dynamic>> delete(
    String path, {
    Map<String, dynamic>? body,
    bool auth = true,
  }) async {
    return _request('DELETE', path, body: body, auth: auth);
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

    ApiDebugLog.info('ApiClient', '$method $uri');

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
        case 'DELETE':
          response = await _http.delete(
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
    } catch (e) {
      ApiDebugLog.error('ApiClient', 'فشل الاتصال بـ $uri', e);
      throw const AuthException(
        'تعذّر الاتصال بالخادم. تحقق من الإنترنت أو عنوان API.',
      );
    }

    ApiDebugLog.info(
      'ApiClient',
      '← ${response.statusCode} ${response.body.length} bytes',
    );

    if (kDebugMode && response.statusCode >= 400) {
      final preview = response.body.length > 300
          ? '${response.body.substring(0, 300)}...'
          : response.body;
      ApiDebugLog.error('ApiClient', 'استجابة الخطأ: $preview');
    }

    return _decodeResponse(response, uri);
  }

  Future<Map<String, dynamic>> _decodeResponse(
    http.Response response,
    Uri uri,
  ) async {
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

  /// آخر تفاصيل للتشخيص (يُعرض في الواجهة عند الخطأ).
  static String formatError(Object error, String path) {
    final uri = ApiConfig.uri(path);
    if (error is AuthException) {
      return '${error.message}\n\nالرابط: $uri';
    }
    return '$error\n\nالرابط: $uri';
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

  MediaType _imageContentType(String filename) {
    final lower = filename.toLowerCase();
    if (lower.endsWith('.png')) {
      return MediaType('image', 'png');
    }
    if (lower.endsWith('.webp')) {
      return MediaType('image', 'webp');
    }
    return MediaType('image', 'jpeg');
  }
}
