import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/data/medical_cases_mock_repository.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';

class MedicalCasesProvider extends ChangeNotifier {
  MedicalCasesProvider() {
    _cases = MedicalCasesMockRepository.seedCases();
  }

  List<MedicalCase> _cases = [];

  int get activeFieldCount => _cases
      .where(
        (c) =>
            c.type == MedicalCaseType.field &&
            c.status == MedicalCaseStatus.active,
      )
      .length;

  int get activeHospitalCount => _cases
      .where(
        (c) =>
            c.type == MedicalCaseType.hospital &&
            c.status == MedicalCaseStatus.active,
      )
      .length;

  List<MedicalCase> get allCases {
    final sorted = List<MedicalCase>.from(_cases)
      ..sort((a, b) => b.updatedAt.compareTo(a.updatedAt));
    return sorted;
  }

  MedicalCase? findById(String id) {
    try {
      return _cases.firstWhere((c) => c.id == id);
    } catch (_) {
      return null;
    }
  }

  List<MedicalCase> filtered({
    MedicalCaseFilter filter = MedicalCaseFilter.all,
    String query = '',
  }) {
    final type = filter.type;
    return allCases.where((c) {
      final typeOk = type == null || c.type == type;
      return typeOk && c.matchesQuery(query);
    }).toList();
  }

  MedicalCase openFieldCase({
    required String animalId,
    required String openReason,
    String? initialNote,
  }) {
    final now = DateTime.now();
    final medicalCase = MedicalCase(
      id: 'mc_${now.millisecondsSinceEpoch}',
      caseNumber: MedicalCasesMockRepository.nextCaseNumber(
        _cases,
        MedicalCaseType.field,
      ),
      type: MedicalCaseType.field,
      status: MedicalCaseStatus.active,
      animalId: animalId.toUpperCase(),
      animalType: _guessAnimalType(animalId),
      animalGroup: 'غير محدد',
      openReason: openReason.trim(),
      initialNote: _nullIfEmpty(initialNote),
      openedAt: now,
      updatedAt: now,
      sourceLabel: 'فتح يدوي',
    );
    _cases.insert(0, medicalCase);
    notifyListeners();
    return medicalCase;
  }

  String _guessAnimalType(String animalId) {
    const known = {
      'A-217': 'غزال',
      'A-330': 'نعامة',
      'A-088': 'نمر',
      'A-102': 'قط بري',
    };
    return known[animalId.toUpperCase()] ?? 'حيوان';
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }
}
