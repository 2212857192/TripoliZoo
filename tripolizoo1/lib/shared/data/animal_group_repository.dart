import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';
import 'package:tripolizoo/shared/constants/animal_groups.dart';
import 'package:tripolizoo/shared/domain/animal_group.dart';

abstract class AnimalGroupRepository {
  Future<List<AnimalGroup>> fetchActive();
}

class ApiAnimalGroupRepository implements AnimalGroupRepository {
  ApiAnimalGroupRepository({
    ApiClient? apiClient,
    AnimalGroupRepository? fallback,
  })  : _apiClient = apiClient ?? ApiClient(),
        _fallback = fallback ?? StaticAnimalGroupRepository();

  final ApiClient _apiClient;
  final AnimalGroupRepository _fallback;

  @override
  Future<List<AnimalGroup>> fetchActive() async {
    try {
      final response = await _apiClient.get(ApiConfig.animalGroups, auth: false);
      final data = response['data'];
      if (data is List && data.isNotEmpty) {
        final groups = data
            .whereType<Map<String, dynamic>>()
            .map(AnimalGroup.fromJson)
            .where((group) => group.id > 0 && group.name.isNotEmpty)
            .toList()
          ..sort((a, b) {
            final order = a.sortOrder.compareTo(b.sortOrder);
            if (order != 0) return order;
            return a.id.compareTo(b.id);
          });

        if (groups.isNotEmpty) {
          return groups;
        }
      }
    } catch (_) {
      return _fallback.fetchActive();
    }

    return _fallback.fetchActive();
  }
}

class StaticAnimalGroupRepository implements AnimalGroupRepository {
  @override
  Future<List<AnimalGroup>> fetchActive() async => AnimalGroups.fallbackRecords;
}

List<String> orderedAnimalGroupNames(
  List<AnimalGroup> catalog,
  Set<String> presentNames,
) {
  final ordered = catalog
      .map((group) => group.name)
      .where(presentNames.contains)
      .toList();

  final extras = presentNames
      .where((name) => !ordered.contains(name))
      .toList()
    ..sort();

  return [...ordered, ...extras];
}
