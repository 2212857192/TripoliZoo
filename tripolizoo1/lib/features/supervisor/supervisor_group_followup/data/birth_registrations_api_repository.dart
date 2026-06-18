import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:image_picker/image_picker.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class BirthRegistrationsApiRepository {
  BirthRegistrationsApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<SupervisorAnimal>> fetchMothers() async {
    final json = await _client.get(ApiConfig.supervisorAnimalMothers);
    return _parseAnimals(json['data']);
  }

  Future<List<SupervisorAnimal>> fetchNewborns() async {
    final json = await _client.get(ApiConfig.supervisorAnimalNewborns);
    return _parseAnimals(json['data']);
  }

  Future<List<BirthFollowUpEntry>> fetchRegistrations({DateTime? date}) async {
    var path = ApiConfig.supervisorBirthRegistrations;
    if (date != null) {
      final formatted =
          '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
      path = '$path?date=$formatted';
    }

    final json = await _client.get(path);
    final list = json['data'] as List<dynamic>? ?? [];

    return list
        .map((item) => _parseRegistration(item as Map<String, dynamic>))
        .toList();
  }

  Future<BirthFollowUpEntry> createRegistration({
    required String motherCode,
    required DateTime birthDate,
    required List<NewbornRecord> newborns,
  }) async {
    final formattedDate =
        '${birthDate.year}-${birthDate.month.toString().padLeft(2, '0')}-${birthDate.day.toString().padLeft(2, '0')}';

    final newbornsPayload = newborns
        .map(
          (newborn) => {
            'gender': newborn.gender.name,
            if (newborn.distinguishingMark != null &&
                newborn.distinguishingMark!.trim().isNotEmpty)
              'distinguishing_mark': newborn.distinguishingMark!.trim(),
            if (newborn.note != null && newborn.note!.trim().isNotEmpty)
              'note': newborn.note!.trim(),
          },
        )
        .toList();

    final fields = <String, String>{
      'mother_code': motherCode,
      'birth_date': formattedDate,
      'birth_count': '${newborns.length}',
      'newborns': jsonEncode(newbornsPayload),
    };

    final files = <String, MultipartUpload>{};

    for (var i = 0; i < newborns.length; i++) {
      final photo = newborns[i].photo;
      if (photo == null) continue;

      final bytes = await photo.readAsBytes();
      files['newborn_photos[$i]'] = MultipartUpload(
        bytes: bytes,
        filename: _photoFilename(photo, i),
      );
    }

    final json = await _client.postMultipart(
      ApiConfig.supervisorBirthRegistrations,
      fields: fields,
      files: files,
    );

    final data = json['data'];
    if (data is! Map<String, dynamic>) {
      throw const AuthException('استجابة غير متوقعة من الخادم.');
    }

    return _parseRegistration(data);
  }

  String _photoFilename(XFile photo, int index) {
    final name = photo.name.trim();
    if (name.isNotEmpty) {
      final lower = name.toLowerCase();
      if (lower.endsWith('.jpg') ||
          lower.endsWith('.jpeg') ||
          lower.endsWith('.png') ||
          lower.endsWith('.webp')) {
        return name;
      }
      return '$name.jpg';
    }

    return 'newborn_$index.jpg';
  }

  List<SupervisorAnimal> _parseAnimals(dynamic data) {
    if (data is! List) return [];

    return data
        .whereType<Map<String, dynamic>>()
        .map(
          (item) => SupervisorAnimal(
            id: item['id']?.toString() ?? '',
            name: item['name']?.toString() ?? '',
            type: item['type']?.toString(),
            customLabel: item['label']?.toString(),
          ),
        )
        .toList();
  }

  BirthFollowUpEntry _parseRegistration(Map<String, dynamic> data) {
    final newbornsRaw = data['newborns'] as List<dynamic>? ?? [];

    return BirthFollowUpEntry(
      id: data['registration_number']?.toString() ??
          data['id']?.toString() ??
          '',
      registeredAt: DateTime.tryParse(data['registered_at']?.toString() ?? '') ??
          DateTime.now(),
      motherId: data['mother_id']?.toString() ?? '',
      animalType: _nullIfEmpty(data['animal_type']?.toString()),
      birthDate: DateTime.tryParse(data['birth_date']?.toString() ?? '') ??
          DateTime.now(),
      birthCount: (data['birth_count'] as num?)?.toInt() ?? newbornsRaw.length,
      newborns: newbornsRaw
          .whereType<Map<String, dynamic>>()
          .map(
            (item) => NewbornRecord(
              gender: item['gender']?.toString() == 'أنثى'
                  ? NewbornGender.female
                  : NewbornGender.male,
              distinguishingMark: _nullIfEmpty(
                item['distinguishing_mark']?.toString(),
              ),
              note: _nullIfEmpty(item['note']?.toString()),
              photoUrl: _nullIfEmpty(item['photo_url']?.toString()),
            ),
          )
          .toList(),
    );
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }
}
