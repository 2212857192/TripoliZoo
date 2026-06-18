import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/data/quarantine_api_repository.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/domain/quarantine_record.dart';
import 'package:tripolizoo/shared/api/api_config.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_debug_log.dart';

class QuarantineProvider extends ChangeNotifier {
  QuarantineProvider({QuarantineApiRepository? repository})
      : _repository = repository ?? QuarantineApiRepository();

  final QuarantineApiRepository _repository;

  List<QuarantineRecord> _records = [];
  bool _loading = false;
  String? _error;
  String? _debugDetail;

  bool get isLoading => _loading;
  String? get error => _error;
  String? get debugDetail => _debugDetail;

  int get activeCount => _records
      .where((r) => r.status == QuarantineStatus.underFollowUp)
      .length;

  List<QuarantineRecord> get allRecords {
    final sorted = List<QuarantineRecord>.from(_records)
      ..sort((a, b) => b.entryDate.compareTo(a.entryDate));
    return sorted;
  }

  Future<void> load() async {
    _loading = true;
    _error = null;
    _debugDetail = null;
    notifyListeners();

    ApiDebugLog.info('QuarantineProvider', 'بدء تحميل قائمة الحجر…');

    try {
      _records = await _repository.fetchQuarantines();
      ApiDebugLog.info(
        'QuarantineProvider',
        'تم التحميل: ${_records.length} سجل',
      );
    } catch (e, stack) {
      _error = _messageFrom(e);
      _debugDetail = ApiClient.formatError(e, ApiConfig.doctorQuarantines);
      _records = [];
      ApiDebugLog.error('QuarantineProvider', _error!, stack);
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<QuarantineRecord?> loadDetail(String caseNumber) async {
    try {
      final record = await _repository.fetchQuarantine(caseNumber);
      if (record == null) return null;
      _upsert(record);
      return record;
    } catch (e) {
      _error = _messageFrom(e);
      notifyListeners();
      return findById(caseNumber);
    }
  }

  Future<QuarantineRecord?> addNote(String caseNumber, String note) async {
    try {
      final record = await _repository.addNote(caseNumber, note);
      _upsert(record);
      return record;
    } catch (e) {
      _error = _messageFrom(e);
      notifyListeners();
      rethrow;
    }
  }

  Future<QuarantineRecord?> addVaccine({
    required String caseNumber,
    required String name,
    required DateTime administeredAt,
    String? note,
  }) async {
    try {
      final record = await _repository.addVaccine(
        caseNumber: caseNumber,
        name: name,
        administeredAt: administeredAt,
        note: note,
      );
      _upsert(record);
      return record;
    } catch (e) {
      _error = _messageFrom(e);
      notifyListeners();
      rethrow;
    }
  }

  Future<String> release(String caseNumber) async {
    final message = await _repository.release(caseNumber);
    _records.removeWhere((r) => r.id == caseNumber || r.tempNumber == caseNumber);
    notifyListeners();
    return message;
  }

  Future<String> closeCase({
    required String caseNumber,
    required String closeReason,
    String? closeNotes,
  }) async {
    final message = await _repository.close(
      caseNumber: caseNumber,
      closeReason: closeReason,
      closeNotes: closeNotes,
    );
    _records.removeWhere((r) => r.id == caseNumber || r.tempNumber == caseNumber);
    notifyListeners();
    return message;
  }

  void _upsert(QuarantineRecord record) {
    final index = _records.indexWhere(
      (r) => r.id == record.id || r.tempNumber == record.tempNumber,
    );
    if (index != -1) {
      _records[index] = record;
    } else {
      _records.add(record);
    }
    notifyListeners();
  }

  QuarantineRecord? findById(String id) {
    try {
      return _records.firstWhere((r) => r.id == id || r.tempNumber == id);
    } catch (_) {
      return null;
    }
  }

  List<QuarantineRecord> filtered({String query = ''}) {
    return allRecords.where((r) => r.matchesQuery(query)).toList();
  }

  String _messageFrom(Object e) {
    final text = e.toString();
    if (text.startsWith('AuthException: ')) {
      return text.replaceFirst('AuthException: ', '');
    }
    return text;
  }
}
