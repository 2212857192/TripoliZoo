import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/shared/constants/animal_groups.dart';
import 'package:tripolizoo/shared/data/animal_group_repository.dart';
import 'package:tripolizoo/shared/domain/animal_group.dart';

void main() {
  group('orderedAnimalGroupNames', () {
    test('keeps api order for groups present on the map', () {
      final catalog = [
        const AnimalGroup(
          id: 2,
          name: 'الطيور',
          codePrefix: 'B',
          sortOrder: 2,
        ),
        const AnimalGroup(
          id: 1,
          name: 'القططية',
          codePrefix: 'C',
          sortOrder: 1,
        ),
      ];

      final names = orderedAnimalGroupNames(
        catalog,
        {'القططية', 'الطيور'},
      );

      expect(names, ['الطيور', 'القططية']);
    });

    test('appends unknown map groups without breaking filters', () {
      final catalog = AnimalGroups.fallbackRecords;

      final names = orderedAnimalGroupNames(
        catalog,
        {'القططية', 'مجموعة جديدة'},
      );

      expect(names.first, 'القططية');
      expect(names, contains('مجموعة جديدة'));
    });
  });

  group('StaticAnimalGroupRepository', () {
    test('returns legacy fallback groups with ids', () async {
      final repo = StaticAnimalGroupRepository();
      final groups = await repo.fetchActive();

      expect(groups, hasLength(8));
      expect(groups.first.id, 1);
      expect(groups.first.name, 'القططية');
    });
  });

  group('AnimalGroup.fromJson', () {
    test('parses api payload fields', () {
      final group = AnimalGroup.fromJson({
        'id': 9,
        'name': 'الخيول',
        'code_prefix': 'H',
        'sort_order': 9,
      });

      expect(group.id, 9);
      expect(group.name, 'الخيول');
      expect(group.codePrefix, 'H');
      expect(group.sortOrder, 9);
    });
  });
}
