import 'package:flutter/foundation.dart';
import 'package:image_picker/image_picker.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/data/health_reports_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/domain/health_report.dart';

enum HealthReportsAudience { supervisor, doctor }

class HealthReportsProvider extends ChangeNotifier {
  HealthReportsProvider({HealthReportsApiRepository? repository})
      : _repository = repository ?? HealthReportsApiRepository();

  final HealthReportsApiRepository _repository;

  List<HealthReport> _reports = [];
  List<SupervisorAnimal> _animals = [];
  bool _loading = false;
  String? _error;

  List<HealthReport> get allReports {
    final sorted = List<HealthReport>.from(_reports)
      ..sort((a, b) => b.sentAt.compareTo(a.sentAt));
    return sorted;
  }

  List<SupervisorAnimal> get groupAnimals => _animals;
  bool get isLoading => _loading;
  String? get error => _error;

  HealthReport? findById(String id) {
    try {
      return _reports.firstWhere((r) => r.id == id);
    } catch (_) {
      return null;
    }
  }

  HealthReport? findByReportNumber(String reportNumber) {
    try {
      return _reports.firstWhere((r) => r.reportNumber == reportNumber);
    } catch (_) {
      return null;
    }
  }

  List<HealthReport> filtered({
    HealthReportStatus? status,
    String query = '',
  }) {
    return allReports.where((report) {
      final statusOk = status == null || report.status == status;
      return statusOk && report.matchesQuery(query);
    }).toList();
  }

  Future<void> load({
    required HealthReportsAudience audience,
    HealthReportStatus? status,
  }) async {
    _loading = true;
    _error = null;
    notifyListeners();

    try {
      if (audience == HealthReportsAudience.supervisor) {
        _reports = await _repository.fetchSupervisorReports(status: status);
        _animals = await _repository.fetchSupervisorAnimals();
      } else {
        _reports = await _repository.fetchDoctorReports(status: status);
      }
    } catch (e) {
      _error = e.toString();
      _reports = [];
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<HealthReport?> addReport({
    required String animalId,
    required String animalType,
    required String description,
    XFile? attachment,
    bool isUrgent = false,
  }) async {
    try {
      final report = await _repository.createReport(
        animalCode: animalId,
        description: description,
        attachment: attachment,
        isUrgent: isUrgent,
      );
      _reports.insert(0, report);
      notifyListeners();
      return report;
    } catch (_) {
      return null;
    }
  }

  Future<HealthReport?> openDoctorReport(String reportNumber) async {
    try {
      final report = await _repository.openDoctorReport(reportNumber);
      _upsertReport(report);
      notifyListeners();
      return report;
    } catch (_) {
      return null;
    }
  }

  Future<bool> closeReport({
    required String reportNumber,
    required String note,
    bool fieldCaseOpened = false,
  }) async {
    try {
      final report = await _repository.closeDoctorReport(
        reportNumber: reportNumber,
        note: note,
        fieldCaseOpened: fieldCaseOpened,
      );
      _upsertReport(report);
      notifyListeners();
      return true;
    } catch (_) {
      return false;
    }
  }

  void _upsertReport(HealthReport report) {
    final index = _reports.indexWhere((r) => r.id == report.id);
    if (index == -1) {
      _reports.insert(0, report);
    } else {
      _reports[index] = report;
    }
  }
}
