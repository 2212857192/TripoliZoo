import 'package:tripolizoo/features/visitor/visitor_explore/domain/zoo_map_bounds.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/map_pathfinder.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class VisitorMapData {
  const VisitorMapData({
    required this.imageUrl,
    required this.locations,
    required this.bounds,
    required this.navigation,
  });

  final String imageUrl;
  final List<VisitorMapLocation> locations;
  final ZooMapBounds bounds;
  final MapNavigationGraph navigation;

  factory VisitorMapData.fromJson(Map<String, dynamic> json) {
    final locations = json['locations'];
    final navigation = json['navigation'];

    return VisitorMapData(
      imageUrl: ApiConfig.resolveAssetUrl(json['image_url']?.toString()) ?? '',
      locations: locations is List
          ? locations
              .whereType<Map<String, dynamic>>()
              .map(VisitorMapLocation.fromJson)
              .toList()
          : const [],
      bounds: navigation is Map<String, dynamic>
          ? ZooMapBounds.fromNavigationJson(navigation)
          : ZooMapBounds.defaults,
      navigation: navigation is Map<String, dynamic>
          ? MapNavigationGraph.fromJson(navigation)
          : const MapNavigationGraph(nodes: [], edges: []),
    );
  }
}

class VisitorMapLocation {
  const VisitorMapLocation({
    required this.id,
    required this.name,
    required this.category,
    required this.description,
    required this.x,
    required this.y,
    this.animalProfileId,
    this.animalName,
    this.animalCode,
    this.animalPhotoUrl,
  });

  final int id;
  final String name;
  final String category;
  final String description;
  final double x;
  final double y;
  final int? animalProfileId;
  final String? animalName;
  final String? animalCode;
  final String? animalPhotoUrl;

  factory VisitorMapLocation.fromJson(Map<String, dynamic> json) {
    return VisitorMapLocation(
      id: (json['id'] as num).toInt(),
      name: json['name']?.toString() ?? 'موقع',
      category: json['category']?.toString() ?? 'service',
      description: json['description']?.toString() ?? '',
      x: ((json['x'] as num?)?.toDouble() ?? 0.5).clamp(0.0, 1.0).toDouble(),
      y: ((json['y'] as num?)?.toDouble() ?? 0.5).clamp(0.0, 1.0).toDouble(),
      animalProfileId: (json['animal_profile_id'] as num?)?.toInt(),
      animalName: json['animal_name']?.toString(),
      animalCode: json['animal_code']?.toString(),
      animalPhotoUrl:
          ApiConfig.resolveAssetUrl(json['animal_photo_url']?.toString()),
    );
  }
}

class VisitorMapRepository {
  VisitorMapRepository({ApiClient? apiClient})
      : _apiClient = apiClient ?? ApiClient();

  final ApiClient _apiClient;

  Future<VisitorMapData> getMap() async {
    final response = await _apiClient.get(ApiConfig.map, auth: false);
    final data = response['data'];
    if (data is Map<String, dynamic>) {
      return VisitorMapData.fromJson(data);
    }

    throw const FormatException('بيانات الخريطة غير صحيحة');
  }
}
