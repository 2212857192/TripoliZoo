import 'package:flutter/foundation.dart';

import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/birth_registrations_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/health_cases_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/mortality_cases_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/operational_notes_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';

class FollowUpProvider extends ChangeNotifier {
  FollowUpProvider({
    HealthCasesApiRepository? healthRepository,
    MortalityCasesApiRepository? mortalityRepository,
    OperationalNotesApiRepository? operationalNotesRepository,
    BirthRegistrationsApiRepository? birthRegistrationsRepository,
  })  : _healthRepository = healthRepository ?? HealthCasesApiRepository(),
        _mortalityRepository =
            mortalityRepository ?? MortalityCasesApiRepository(),
        _operationalNotesRepository =
            operationalNotesRepository ?? OperationalNotesApiRepository(),
        _birthRegistrationsRepository =
            birthRegistrationsRepository ?? BirthRegistrationsApiRepository();

  final HealthCasesApiRepository _healthRepository;
  final MortalityCasesApiRepository _mortalityRepository;
  final OperationalNotesApiRepository _operationalNotesRepository;
  final BirthRegistrationsApiRepository _birthRegistrationsRepository;

  final List<FollowUpEntry> _localEntries = [];

  List<HealthFollowUpEntry> _healthEntries = [];
  List<MortalityFollowUpEntry> _mortalityEntries = [];
  List<OperationalNoteEntry> _operationalNoteEntries = [];
  List<BirthFollowUpEntry> _birthEntries = [];

  List<FollowUpEntry> _entries = [];
  DateTime? _loadedDate;
  bool _loading = false;
  String? _error;

  List<FollowUpEntry> get entries => _entries;
  bool get isLoading => _loading;
  String? get error => _error;

  Future<void> loadForDate(DateTime date) async {
    final day = DateTime(date.year, date.month, date.day);
    _loadedDate = day;
    _loading = true;
    _error = null;
    notifyListeners();

    try {
      final results = await Future.wait([
        _healthRepository.fetchCases(date: day),
        _mortalityRepository.fetchCases(date: day),
        _operationalNotesRepository.fetchNotes(date: day),
        _birthRegistrationsRepository.fetchRegistrations(date: day),
      ]);

      _healthEntries = results[0] as List<HealthFollowUpEntry>;
      _mortalityEntries = results[1] as List<MortalityFollowUpEntry>;
      _operationalNoteEntries = results[2] as List<OperationalNoteEntry>;
      _birthEntries = results[3] as List<BirthFollowUpEntry>;
      _rebuildEntries();
    } catch (_) {
      _healthEntries = [];
      _mortalityEntries = [];
      _operationalNoteEntries = [];
      _birthEntries = [];
      _entries = _localEntriesForDay(day);
      _error = 'تعذّر تحميل سجل المتابعة';
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<void> refresh() async {
    if (_loadedDate == null) return;
    await loadForDate(_loadedDate!);
  }

  void addBirth({
    required String motherId,
    String? animalType,
    required DateTime birthDate,
    required int birthCount,
    required List<NewbornRecord> newborns,
  }) {
    _localEntries.insert(
      0,
      BirthFollowUpEntry(
        id: 'b_${DateTime.now().millisecondsSinceEpoch}',
        registeredAt: DateTime.now(),
        motherId: motherId,
        animalType: _nullIfEmpty(animalType),
        birthDate: birthDate,
        birthCount: birthCount,
        newborns: newborns,
      ),
    );
    _rebuildEntries();
    notifyListeners();
  }

  void _rebuildEntries() {
    final day = _loadedDate;
    if (day == null) {
      _entries = [];
      return;
    }

    final local = _localEntriesForDay(day);
    _entries = [
      ..._healthEntries,
      ..._mortalityEntries,
      ..._operationalNoteEntries,
      ..._birthEntries,
      ...local,
    ]..sort((a, b) => b.registeredAt.compareTo(a.registeredAt));
  }

  List<FollowUpEntry> _localEntriesForDay(DateTime day) {
    return _localEntries
        .where((entry) => _isSameDay(entry.registeredAt, day))
        .toList();
  }

  bool _isSameDay(DateTime a, DateTime b) {
    return a.year == b.year && a.month == b.month && a.day == b.day;
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }
}
