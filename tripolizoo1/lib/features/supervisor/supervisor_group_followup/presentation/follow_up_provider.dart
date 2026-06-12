import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/follow_up_mock_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';

class FollowUpProvider extends ChangeNotifier {
  FollowUpProvider() {
    _entries = FollowUpMockRepository.seedEntries();
  }

  List<FollowUpEntry> _entries = [];

  List<FollowUpEntry> forDate(DateTime date) {
    final day = DateTime(date.year, date.month, date.day);
    return _entries
        .where((e) {
          final registered = DateTime(
            e.registeredAt.year,
            e.registeredAt.month,
            e.registeredAt.day,
          );
          return registered == day;
        })
        .toList()
      ..sort((a, b) => b.registeredAt.compareTo(a.registeredAt));
  }

  void addHealth({
    required String animalId,
    String? animalType,
    required String description,
    required HealthFollowUpKind followUpKind,
    String? fullDescription,
    String? extraNotes,
    bool hasAttachment = false,
  }) {
    _entries.insert(
      0,
      HealthFollowUpEntry(
        id: 'h_${DateTime.now().millisecondsSinceEpoch}',
        registeredAt: DateTime.now(),
        animalId: animalId,
        animalType: _nullIfEmpty(animalType),
        description: description,
        followUpKind: followUpKind,
        fullDescription: _nullIfEmpty(fullDescription),
        extraNotes: _nullIfEmpty(extraNotes),
        hasAttachment: hasAttachment,
      ),
    );
    notifyListeners();
  }

  void addBirth({
    required String motherId,
    String? animalType,
    required DateTime birthDate,
    required int birthCount,
    required List<NewbornRecord> newborns,
  }) {
    _entries.insert(
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
    notifyListeners();
  }

  void addMortality({
    required MortalityVictimKind victimKind,
    required String animalId,
    String? animalType,
    String? deathCause,
    String? extraNotes,
    bool hasAttachment = false,
  }) {
    _entries.insert(
      0,
      MortalityFollowUpEntry(
        id: 'm_${DateTime.now().millisecondsSinceEpoch}',
        registeredAt: DateTime.now(),
        victimKind: victimKind,
        animalId: animalId,
        animalType: _nullIfEmpty(animalType),
        deathCause: _nullIfEmpty(deathCause),
        extraNotes: _nullIfEmpty(extraNotes),
        hasAttachment: hasAttachment,
      ),
    );
    notifyListeners();
  }

  void addOperationalNote({
    required OperationalNoteKind noteKind,
    required String summary,
    String? fullText,
    String? extraNotes,
    bool hasAttachment = false,
  }) {
    _entries.insert(
      0,
      OperationalNoteEntry(
        id: 'o_${DateTime.now().millisecondsSinceEpoch}',
        registeredAt: DateTime.now(),
        noteKind: noteKind,
        summary: summary,
        fullText: _nullIfEmpty(fullText),
        extraNotes: _nullIfEmpty(extraNotes),
        hasAttachment: hasAttachment,
      ),
    );
    notifyListeners();
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }
}
