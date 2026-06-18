import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/data/medical_cases_api_repository.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';

class MedicalCasesProvider extends ChangeNotifier {
  MedicalCasesProvider({MedicalCasesApiRepository? repository})
      : _repository = repository ?? MedicalCasesApiRepository();

  final MedicalCasesApiRepository _repository;

  List<MedicalCase> _cases = [];
  bool _loading = false;
  String? _error;

  List<MedicalCase> get cases => _cases;
  bool get isLoading => _loading;
  String? get error => _error;

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

  Future<void> load() async {
    _loading = true;
    _error = null;
    notifyListeners();

    try {
      _cases = await _repository.fetchCases();
    } catch (_) {
      _cases = [];
      _error = 'تعذّر تحميل الحالات الطبية';
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<MedicalCase?> fetchCase(String id) async {
    try {
      final medicalCase = await _repository.fetchCase(id);
      _upsertCase(medicalCase);
      notifyListeners();
      return medicalCase;
    } catch (_) {
      return null;
    }
  }

  Future<MedicalCase?> registerProcedure({
    required String caseId,
    required String diagnosis,
    required String treatment,
    required MedicalCaseResult caseResult,
    String? note,
    String? nutritionText,
    DateTime? nutritionStart,
    DateTime? nutritionEnd,
    String? nutritionNote,
  }) async {
    try {
      final medicalCase = await _repository.registerProcedure(
        caseId: caseId,
        diagnosis: diagnosis,
        treatment: treatment,
        caseResult: caseResult,
        note: note,
        nutritionText: nutritionText,
        nutritionStart: nutritionStart,
        nutritionEnd: nutritionEnd,
        nutritionNote: nutritionNote,
      );
      _upsertCase(medicalCase);
      notifyListeners();
      return medicalCase;
    } catch (_) {
      rethrow;
    }
  }

  Future<MedicalCase?> closeFieldCase({
    required String caseId,
  }) async {
    try {
      final medicalCase = await _repository.closeFieldCase(caseId: caseId);
      _upsertCase(medicalCase);
      notifyListeners();
      return medicalCase;
    } catch (_) {
      rethrow;
    }
  }

  void _upsertCase(MedicalCase medicalCase) {
    final index = _cases.indexWhere((c) => c.id == medicalCase.id);
    if (index >= 0) {
      _cases[index] = medicalCase;
    } else {
      _cases.insert(0, medicalCase);
    }
  }

  Future<MedicalCase?> openFieldCase({
    required String animalId,
    required String openReason,
    String? initialNote,
  }) async {
    try {
      final medicalCase = await _repository.openFieldCase(
        animalCode: animalId,
        openReason: openReason,
        initialNote: initialNote,
      );
      _cases.insert(0, medicalCase);
      notifyListeners();
      return medicalCase;
    } catch (_) {
      rethrow;
    }
  }
}
