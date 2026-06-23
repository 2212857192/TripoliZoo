import 'dart:convert';

import 'package:mobile_scanner/mobile_scanner.dart';

/// Reads the best available scan payload from a detected barcode.
String? readBarcodeValue(Barcode barcode) {
  final url = barcode.url?.url;
  if (url != null && url.trim().isNotEmpty) {
    return url.trim();
  }

  final raw = barcode.rawValue?.trim();
  if (raw != null && raw.isNotEmpty) {
    return raw;
  }

  final display = barcode.displayValue?.trim();
  if (display != null && display.isNotEmpty) {
    return display;
  }

  return null;
}

/// Converts a scanned QR payload into an animal lookup identifier
/// (profile id, animal code, or plain code).
String parseAnimalIdentifierFromQr(String code) {
  var trimmed = code.trim().replaceFirst(RegExp(r'^\uFEFF'), '');
  if (trimmed.isEmpty) return trimmed;

  try {
    final parsed = jsonDecode(trimmed);
    if (parsed is Map<String, dynamic>) {
      final profileId = parsed['profile_id']?.toString();
      if (profileId != null && profileId.isNotEmpty) return profileId;

      final animalCode = parsed['animal_code']?.toString();
      if (animalCode != null && animalCode.isNotEmpty) return animalCode;
    }
  } catch (_) {
    // QR codes can also be plain animal codes or visitor URLs.
  }

  final fromUri = _identifierFromUri(trimmed);
  if (fromUri != null) return fromUri;

  if (!trimmed.contains('://')) {
    final withScheme = _identifierFromUri('https://$trimmed');
    if (withScheme != null) return withScheme;
  }

  return trimmed;
}

/// Builds the in-app animal details route from a scanned QR payload.
/// Mirrors the website path `/app/animals/{profile}`.
String? animalDetailPathFromQr(String code) {
  final identifier = parseAnimalIdentifierFromQr(code);
  if (identifier.isEmpty) return null;
  return '/animals/$identifier';
}

String? _identifierFromUri(String value) {
  final uri = Uri.tryParse(value);
  if (uri == null) return null;

  final pathMatch = RegExp(r'/animals/([^/?#]+)').firstMatch(uri.path);
  if (pathMatch != null) {
    return Uri.decodeComponent(pathMatch.group(1)!);
  }

  final segments = uri.pathSegments;
  final animalsIndex = segments.lastIndexOf('animals');
  if (animalsIndex != -1 && animalsIndex + 1 < segments.length) {
    return Uri.decodeComponent(segments[animalsIndex + 1]);
  }

  return null;
}
