import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_dashboard_data.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class SupervisorDashboardApiRepository {
  SupervisorDashboardApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<SupervisorDashboardData> fetchDashboard() async {
    final json = await _client.get(ApiConfig.supervisorDashboard);

    final recommendations = (json['diet_recommendations'] as List<dynamic>? ?? [])
        .map((item) => _parseDietRecommendation(item as Map<String, dynamic>))
        .toList();

    return SupervisorDashboardData(
      supervisorName: json['supervisor_name']?.toString() ?? '',
      groupName: json['group_name']?.toString() ?? '—',
      pendingReceivingTasks: _asInt(json['pending_receiving_tasks']),
      unreadNotifications: _asInt(json['unread_notifications']),
      pendingHealthReportsCount: _asInt(json['pending_health_reports_count']),
      activeDietRecommendations: _asInt(
        json['active_diet_recommendations'],
        fallback: recommendations.length,
      ),
      dietRecommendations: recommendations,
    );
  }

  DietRecommendation _parseDietRecommendation(Map<String, dynamic> json) {
    return DietRecommendation(
      animalId: json['animal_id']?.toString() ?? '',
      animalName: json['animal_name']?.toString() ?? '—',
      daysRemaining: _asInt(json['days_remaining']),
      instruction: json['instruction']?.toString() ?? '',
      doctorNote: json['doctor_note']?.toString() ?? '—',
    );
  }

  int _asInt(dynamic value, {int fallback = 0}) {
    if (value is int) return value;
    return int.tryParse('$value') ?? fallback;
  }
}

class SupervisorDashboardProvider extends ChangeNotifier {
  SupervisorDashboardProvider({SupervisorDashboardApiRepository? repository})
      : _repository = repository ?? SupervisorDashboardApiRepository();

  final SupervisorDashboardApiRepository _repository;

  SupervisorDashboardData? _data;
  bool _loading = false;

  SupervisorDashboardData? get data => _data;
  bool get isLoading => _loading;

  int get pendingReceivingTasks => _data?.pendingReceivingTasks ?? 0;
  int get unreadNotifications => _data?.unreadNotifications ?? 0;
  int get pendingHealthReportsCount => _data?.pendingHealthReportsCount ?? 0;
  int get activeDietRecommendations => _data?.activeDietRecommendations ?? 0;
  List<DietRecommendation> get dietRecommendations =>
      _data?.dietRecommendations ?? const [];

  Future<void> load() async {
    _loading = true;
    notifyListeners();

    try {
      _data = await _repository.fetchDashboard();
    } catch (_) {
      _data = null;
    } finally {
      _loading = false;
      notifyListeners();
    }
  }
}
