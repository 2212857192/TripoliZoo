import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/data/receiving_tasks_mock_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/domain/receiving_task.dart';

class ReceivingTasksProvider extends ChangeNotifier {
  ReceivingTasksProvider() {
    _tasks = ReceivingTasksMockRepository.seedTasks();
  }

  List<ReceivingTask> _tasks = [];

  int get pendingCount => _tasks
      .where((t) => t.status == ReceivingTaskStatus.pending)
      .length;

  List<ReceivingTask> get allTasks {
    final sorted = List<ReceivingTask>.from(_tasks)
      ..sort((a, b) => b.createdAt.compareTo(a.createdAt));
    return sorted;
  }

  ReceivingTask? findById(String id) {
    try {
      return _tasks.firstWhere((t) => t.id == id);
    } catch (_) {
      return null;
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

  void confirmReceipt(String taskId, {String? note}) {
    final index = _tasks.indexWhere((t) => t.id == taskId);
    if (index == -1) return;
    _tasks[index] = _tasks[index].copyWith(
      status: ReceivingTaskStatus.received,
      receiptNote: _nullIfEmpty(note),
      clearDelay: true,
    );
    notifyListeners();
  }

  void recordTemporaryDelay(
    String taskId, {
    required String reason,
    String? extraNote,
  }) {
    final index = _tasks.indexWhere((t) => t.id == taskId);
    if (index == -1) return;
    _tasks[index] = _tasks[index].copyWith(
      status: ReceivingTaskStatus.temporarilyUnable,
      delayReason: reason.trim(),
      delayExtraNote: _nullIfEmpty(extraNote),
      delayRecordedAt: DateTime.now(),
    );
    notifyListeners();
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }
}
