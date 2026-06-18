import 'package:flutter/foundation.dart';
import 'package:image_picker/image_picker.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class MortalityCaseApiResult {
  const MortalityCaseApiResult({
    required this.caseNumber,
    required this.animalId,
    required this.deathCause,
    required this.hasAttachment,
  });

  final String caseNumber;
  final String animalId;
  final String deathCause;
  final bool hasAttachment;
}

class MortalityCasesApiRepository {
  MortalityCasesApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<MortalityFollowUpEntry>> fetchCases({DateTime? date}) async {
    var path = ApiConfig.supervisorMortalityCases;
    if (date != null) {
      final formatted =
          '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
      path = '$path?date=$formatted';
    }

    final json = await _client.get(path);
    final list = json['data'] as List<dynamic>? ?? [];

    return list
        .map((item) => _parseMortalityCase(item as Map<String, dynamic>))
        .toList();
  }

  Future<MortalityCaseApiResult> createCase({
    required String animalCode,
    String? victimKind,
    String? animalType,
    String? deathCause,
    String? notes,
    XFile? attachment,
  }) async {
    final fields = <String, String>{
      'animal_code': animalCode,
      if (victimKind != null && victimKind.trim().isNotEmpty)
        'victim_kind': victimKind.trim(),
      if (animalType != null && animalType.trim().isNotEmpty)
        'animal_type': animalType.trim(),
      if (deathCause != null && deathCause.trim().isNotEmpty)
        'death_cause': deathCause.trim(),
      if (notes != null && notes.trim().isNotEmpty) 'notes': notes.trim(),
    };

    final Map<String, dynamic> json;
    if (attachment != null) {
      final filename = attachment.name.trim().isNotEmpty
          ? attachment.name
          : 'attachment.jpg';

      if (!kIsWeb && attachment.path.isNotEmpty) {
        json = await _client.postMultipart(
          ApiConfig.supervisorMortalityCases,
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
          ApiConfig.supervisorMortalityCases,
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
        ApiConfig.supervisorMortalityCases,
        body: fields,
      );
    }

    final data = json['data'] as Map<String, dynamic>;

    return MortalityCaseApiResult(
      caseNumber: data['case_number']?.toString() ?? '',
      animalId: data['animal_id']?.toString() ?? animalCode,
      deathCause: data['death_cause']?.toString() ?? 'غير ظاهر',
      hasAttachment: data['has_attachment'] == true,
    );
  }

  MortalityFollowUpEntry _parseMortalityCase(Map<String, dynamic> data) {
    final victimKind = data['victim_kind']?.toString();

    return MortalityFollowUpEntry(
      id: data['case_number']?.toString() ?? data['id']?.toString() ?? '',
      registeredAt: DateTime.tryParse(data['registered_at']?.toString() ?? '') ??
          DateTime.now(),
      victimKind: victimKind == 'newborn_under_follow_up'
          ? MortalityVictimKind.newbornUnderFollowUp
          : MortalityVictimKind.zooAnimal,
      animalId: data['animal_id']?.toString() ?? '',
      animalType: _nullIfEmpty(data['animal_type']?.toString()),
      deathCause: _nullIfEmpty(data['death_cause']?.toString()),
      extraNotes: _nullIfEmpty(data['notes']?.toString()),
      hasAttachment: data['has_attachment'] == true,
    );
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }
}
