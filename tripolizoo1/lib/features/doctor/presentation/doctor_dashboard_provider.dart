import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/doctor/data/doctor_dashboard_api_repository.dart';

class DoctorDashboardProvider extends ChangeNotifier {
  DoctorDashboardProvider({DoctorDashboardApiRepository? repository})
      : _repository = repository ?? DoctorDashboardApiRepository();

  final DoctorDashboardApiRepository _repository;

  DoctorDashboardData? _data;
  bool _loading = false;
  String? _error;

  DoctorDashboardData? get data => _data;
  bool get isLoading => _loading;
  String? get error => _error;

  String? get errorMessage {
    final value = _error;
    if (value == null) return null;
    if (value.startsWith('AuthException: ')) {
      return value.replaceFirst('AuthException: ', '');
    }
    return value;
  }

  int get quarantineActiveCount => _data?.quarantineActiveCount ?? 0;
  int get activeFieldCasesCount => _data?.activeFieldCasesCount ?? 0;
  int get activeHospitalCasesCount => _data?.activeHospitalCasesCount ?? 0;
  int get unreadNotifications => _data?.unreadNotifications ?? 0;
  int get pendingHealthReportsCount => _data?.pendingHealthReportsCount ?? 0;
  List<DoctorDashboardAlert> get alerts => _data?.alerts ?? const [];

  Future<void> load() async {
    _loading = true;
    _error = null;
    notifyListeners();

    try {
      _data = await _repository.fetchDashboard();
    } catch (e) {
      _error = e.toString();
      _data = null;
    } finally {
      _loading = false;
      notifyListeners();
    }
  }
}
