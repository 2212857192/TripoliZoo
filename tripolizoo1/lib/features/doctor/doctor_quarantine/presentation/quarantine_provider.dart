import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/data/quarantine_mock_repository.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/domain/quarantine_record.dart';

class QuarantineProvider extends ChangeNotifier {
  QuarantineProvider() {
    _records = QuarantineMockRepository.seedRecords();
  }

  List<QuarantineRecord> _records = [];

  int get activeCount => _records
      .where((r) => r.status == QuarantineStatus.underFollowUp)
      .length;

  List<QuarantineRecord> get allRecords {
    final sorted = List<QuarantineRecord>.from(_records)
      ..sort((a, b) => b.entryDate.compareTo(a.entryDate));
    return sorted;
  }

  QuarantineRecord? findById(String id) {
    try {
      return _records.firstWhere((r) => r.id == id);
    } catch (_) {
      return null;
    }
  }

  List<QuarantineRecord> filtered({String query = ''}) {
    return allRecords.where((r) => r.matchesQuery(query)).toList();
  }
}
