import 'package:flutter/foundation.dart';
import 'package:image_picker/image_picker.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class HealthCaseApiResult {
  const HealthCaseApiResult({
    required this.caseNumber,
    required this.animalId,
    required this.description,
    required this.followUpKind,
    required this.hasAttachment,
  });

  final String caseNumber;
  final String animalId;
  final String description;
  final String followUpKind;
  final bool hasAttachment;
}

class HealthCasesApiRepository {
  HealthCasesApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<HealthFollowUpEntry>> fetchCases({DateTime? date}) async {
    var path = ApiConfig.supervisorHealthCases;
    if (date != null) {
      final formatted =
          '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
      path = '$path?date=$formatted';
    }

    final json = await _client.get(path);
    final list = json['data'] as List<dynamic>? ?? [];

    return list
        .map((item) => _parseHealthCase(item as Map<String, dynamic>))
        .toList();
  }

  Future<HealthCaseApiResult> createCase({
    required String animalCode,
    required String description,
    required String followUpKind,
    String? animalNotes,
    XFile? attachment,
  }) async {
    final fields = <String, String>{
      'animal_code': animalCode,
      'description': description.trim(),
      'follow_up_kind': followUpKind,
    };

    if (animalNotes != null && animalNotes.trim().isNotEmpty) {
      fields['animal_notes'] = animalNotes.trim();
    }

    final Map<String, dynamic> json;
    if (attachment != null) {
      final filename = attachment.name.trim().isNotEmpty
          ? attachment.name
          : 'attachment.jpg';

      if (!kIsWeb && attachment.path.isNotEmpty) {
        json = await _client.postMultipart(
          ApiConfig.supervisorHealthCases,
          fields: fields,
          filePaths: {
            'attachment': MultipartPathUpload(
              path: attachment.path,
              filename: filename,
            ),
          },
        );
      } else {
        final bytes = await attachment.readAsBytes();
        json = await _client.postMultipart(
          ApiConfig.supervisorHealthCases,
          fields: fields,
          files: {
            'attachment': MultipartUpload(
              bytes: bytes,
              filename: filename,
            ),
          },
        );
      }
    } else {
      json = await _client.post(
        ApiConfig.supervisorHealthCases,
        body: {
          'animal_code': animalCode,
          'description': description.trim(),
          'follow_up_kind': followUpKind,
          if (animalNotes != null && animalNotes.trim().isNotEmpty)
            'animal_notes': animalNotes.trim(),
        },
      );
    }

    final data = json['data'] as Map<String, dynamic>;

    return HealthCaseApiResult(
      caseNumber: data['case_number']?.toString() ?? '',
      animalId: data['animal_id']?.toString() ?? animalCode,
      description: data['description']?.toString() ?? description.trim(),
      followUpKind: data['follow_up_kind']?.toString() ?? followUpKind,
      hasAttachment: data['has_attachment'] == true,
    );
  }

  HealthFollowUpEntry _parseHealthCase(Map<String, dynamic> data) {
    final followUpKind = data['follow_up_kind']?.toString();

    return HealthFollowUpEntry(
      id: data['case_number']?.toString() ?? data['id']?.toString() ?? '',
      registeredAt:
          DateTime.tryParse(data['registered_at']?.toString() ?? '') ??
              DateTime.now(),
      animalId: data['animal_id']?.toString() ?? '',
      animalType: _nullIfEmpty(data['animal_type']?.toString()),
      description: data['description']?.toString() ?? '',
      followUpKind: followUpKind == 'needs_referral'
          ? HealthFollowUpKind.needsReferral
          : HealthFollowUpKind.noReferral,
      hasAttachment: data['has_attachment'] == true,
      attachmentUrl: ApiConfig.resolveAssetUrl(
        _nullIfEmpty(data['attachment_url']?.toString()),
      ),
    );
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }
}
