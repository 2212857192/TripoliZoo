import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/data/receiving_tasks_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/domain/receiving_task.dart';

class ReceivingTasksProvider extends ChangeNotifier {
  ReceivingTasksProvider({ReceivingTasksApiRepository? repository})
      : _repository = repository ?? ReceivingTasksApiRepository();

  final ReceivingTasksApiRepository _repository;

  List<ReceivingTask> _tasks = [];
  bool _loading = false;
  String? _error;

  bool get isLoading => _loading;
  String? get error => _error;

  int get pendingCount => _tasks
      .where((t) => t.status == ReceivingTaskStatus.pending)
      .length;

  List<ReceivingTask> get allTasks {
    final sorted = List<ReceivingTask>.from(_tasks)
      ..sort((a, b) => b.createdAt.compareTo(a.createdAt));
    return sorted;
  }

  Future<void> load() async {
    _loading = true;
    _error = null;
    notifyListeners();

    try {
      _tasks = await _repository.fetchTasks();
    } catch (e) {
      _error = e.toString();
      _tasks = [];
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  ReceivingTask? findById(String id) {
    try {
      return _tasks.firstWhere((t) => t.id == id);
    } catch (_) {
      try {
        return _tasks.firstWhere((t) => t.taskNumber == id);
      } catch (_) {
        return null;
      }
    }
  }

  List<ReceivingTask> filtered({
    ReceivingTaskStatus? status,
    String query = '',
  }) {
    return allTasks.where((task) {
      final statusOk = status == null || task.status == status;
      return statusOk && task.matchesQuery(query);
    }).toList();
  }

  Future<bool> confirmReceipt(String taskId, {String? note}) async {
    final task = findById(taskId);
    if (task == null) return false;

    try {
      await _repository.confirmReceipt(task.taskNumber, note: note);
      final index = _tasks.indexWhere((t) => t.id == task.id);
      if (index != -1) {
        _tasks[index] = _tasks[index].copyWith(
          status: ReceivingTaskStatus.received,
          receiptNote: _nullIfEmpty(note),
          clearDelay: true,
        );
        notifyListeners();
      }
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<bool> recordTemporaryDelay(
    String taskId, {
    required String reason,
    String? extraNote,
  }) async {
    final task = findById(taskId);
    if (task == null) return false;

    try {
      await _repository.recordTemporaryDelay(
        task.taskNumber,
        reason: reason,
        extraNote: extraNote,
      );
      final index = _tasks.indexWhere((t) => t.id == task.id);
      if (index != -1) {
        _tasks[index] = _tasks[index].copyWith(
          status: ReceivingTaskStatus.temporarilyUnable,
          delayReason: reason.trim(),
          delayExtraNote: _nullIfEmpty(extraNote),
          delayRecordedAt: DateTime.now(),
        );
        notifyListeners();
      }
      return true;
    } catch (_) {
      return false;
    }
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }
}
