import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_dashboard_data.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/data/health_reports_mock_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/domain/health_report.dart';

class HealthReportsProvider extends ChangeNotifier {
  HealthReportsProvider() {
    _reports = HealthReportsMockRepository.seedReports();
  }

  List<HealthReport> _reports = [];

  List<HealthReport> get allReports {
    final sorted = List<HealthReport>.from(_reports)
      ..sort((a, b) => b.sentAt.compareTo(a.sentAt));
    return sorted;
  }

  HealthReport? findById(String id) {
    try {
      return _reports.firstWhere((r) => r.id == id);
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

  HealthReport addReport({
    required String animalId,
    required String animalType,
    required String description,
    bool hasAttachment = false,
  }) {
    final report = HealthReport(
      id: 'hr_${DateTime.now().millisecondsSinceEpoch}',
      reportNumber: HealthReportsMockRepository.nextReportNumber(_reports),
      animalId: animalId,
      animalType: animalType,
      groupName: SupervisorDashboardData.mock.groupName,
      description: description.trim(),
      sentAt: DateTime.now(),
      status: HealthReportStatus.sent,
      hasAttachment: hasAttachment,
    );
    _reports.insert(0, report);
    notifyListeners();
    return report;
  }

  void markAsReceived(String id, String doctorName) {
    final index = _reports.indexWhere((r) => r.id == id);
    if (index != -1) {
      final old = _reports[index];
      if (old.status == HealthReportStatus.sent) {
        _reports[index] = HealthReport(
          id: old.id,
          reportNumber: old.reportNumber,
          animalId: old.animalId,
          animalType: old.animalType,
          groupName: old.groupName,
          description: old.description,
          sentAt: old.sentAt,
          status: HealthReportStatus.received,
          assignedDoctorName: doctorName,
          doctorNote: old.doctorNote,
          doctorUpdatedAt: DateTime.now(),
          fieldCaseOpened: old.fieldCaseOpened,
          hasAttachment: old.hasAttachment,
        );
        notifyListeners();
      }
    }
  }

  void closeReport(String id, {required String note, required String doctorName}) {
    final index = _reports.indexWhere((r) => r.id == id);
    if (index != -1) {
      final old = _reports[index];
      _reports[index] = HealthReport(
        id: old.id,
        reportNumber: old.reportNumber,
        animalId: old.animalId,
        animalType: old.animalType,
        groupName: old.groupName,
        description: old.description,
        sentAt: old.sentAt,
        status: HealthReportStatus.closed,
        assignedDoctorName: doctorName,
        doctorNote: note,
        doctorUpdatedAt: DateTime.now(),
        fieldCaseOpened: old.fieldCaseOpened,
        hasAttachment: old.hasAttachment,
      );
      notifyListeners();
    }
  }
}
