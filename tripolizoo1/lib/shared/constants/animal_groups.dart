import 'package:tripolizoo/shared/domain/animal_group.dart';

abstract final class AnimalGroups {
  static const List<String> all = [
    'القططية',
    'الطيور',
    'الزواحف',
    'القرود',
    'الغزلان',
    'الثدييات الكبيرة',
    'الثدييات الصغيرة',
    'الدب واللامة',
  ];

  static const Map<String, String> prefixes = {
    'القططية': 'C',
    'الطيور': 'B',
    'الزواحف': 'R',
    'القرود': 'M',
    'الغزلان': 'G',
    'الثدييات الكبيرة': 'L',
    'الثدييات الصغيرة': 'S',
    'الدب واللامة': 'D',
  };

  static List<AnimalGroup> get fallbackRecords {
    return List<AnimalGroup>.generate(all.length, (index) {
      final name = all[index];
      return AnimalGroup(
        id: index + 1,
        name: name,
        codePrefix: prefixes[name] ?? '',
        sortOrder: index + 1,
      );
    });
  }
}
