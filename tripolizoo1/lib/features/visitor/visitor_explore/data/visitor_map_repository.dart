import 'package:tripolizoo/features/visitor/visitor_explore/domain/zoo_map_bounds.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/map_gps_locator.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/map_pathfinder.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class VisitorMapData {
  const VisitorMapData({
    required this.imageUrl,
    required this.locations,
    required this.bounds,
    required this.navigation,
    required this.calibration,
  });

  final String imageUrl;
  final List<VisitorMapLocation> locations;
  final ZooMapBounds bounds;
  final MapNavigationGraph navigation;
  final MapGpsCalibration calibration;

  MapGpsLocator get gpsLocator => MapGpsLocator(calibration);

  factory VisitorMapData.fromJson(
    Map<String, dynamic> json, {
    MapGpsCalibration calibration = MapGpsCalibration.empty,
  }) {
    final locations = json['locations'];
    final navigation = json['navigation'];
    final embeddedCalibration = json['calibration'];

    final resolvedCalibration = embeddedCalibration is Map<String, dynamic>
        ? MapGpsCalibration.fromJson(embeddedCalibration)
        : calibration;

    final navMap = navigation is Map<String, dynamic>
        ? navigation
        : <String, dynamic>{
            'bounds': {
              'north': ZooMapBounds.defaults.north,
              'south': ZooMapBounds.defaults.south,
              'west': ZooMapBounds.defaults.west,
              'east': ZooMapBounds.defaults.east,
            },
            'image_width': json['image_width'],
            'image_height': json['image_height'],
            'nodes': json['nodes'],
            'edges': json['edges'],
          };

    return VisitorMapData(
      imageUrl: ApiConfig.resolveAssetUrl(json['image_url']?.toString()) ?? '',
      locations: locations is List
          ? locations
              .whereType<Map<String, dynamic>>()
              .map(VisitorMapLocation.fromJson)
              .toList()
          : const [],
      bounds: ZooMapBounds.fromNavigationJson(navMap),
      navigation: MapNavigationGraph.fromJson(navMap),
      calibration: resolvedCalibration,
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
    this.nearestNodeId,
    this.nearestNodeKey,
    this.animalProfileId,
    this.animalName,
    this.animalCode,
    this.animalGroup,
    this.animalPhotoUrl,
  });

  final int id;
  final String name;
  final String category;
  final String description;
  final double x;
  final double y;
  final int? nearestNodeId;
  final String? nearestNodeKey;
  final int? animalProfileId;
  final String? animalName;
  final String? animalCode;
  final String? animalGroup;
  final String? animalPhotoUrl;

  factory VisitorMapLocation.fromJson(Map<String, dynamic> json) {
    return VisitorMapLocation(
      id: (json['id'] as num).toInt(),
      name: json['name']?.toString() ?? 'موقع',
      category: json['category']?.toString() ?? 'service',
      description: json['description']?.toString() ?? '',
      x: ((json['x'] as num?)?.toDouble() ?? 0.5).clamp(0.0, 1.0).toDouble(),
      y: ((json['y'] as num?)?.toDouble() ?? 0.5).clamp(0.0, 1.0).toDouble(),
      nearestNodeId: (json['nearest_node_id'] as num?)?.toInt(),
      nearestNodeKey: json['nearest_node_key']?.toString(),
      animalProfileId: (json['animal_profile_id'] as num?)?.toInt(),
      animalName: json['animal_name']?.toString(),
      animalCode: json['animal_code']?.toString(),
      animalGroup: json['animal_group']?.toString(),
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
    Map<String, dynamic>? mapJson;
    MapGpsCalibration calibration = MapGpsCalibration.empty;

    try {
      final active = await _apiClient.get(ApiConfig.mapActive, auth: false);
      if (active is Map<String, dynamic>) {
        mapJson = active['data'] is Map<String, dynamic>
            ? active['data'] as Map<String, dynamic>
            : active;
      }
    } catch (_) {
      // Fall back to legacy endpoint below.
    }

    if (mapJson == null) {
      final legacy = await _apiClient.get(ApiConfig.map, auth: false);
      if (legacy is Map<String, dynamic>) {
        mapJson = legacy['data'] is Map<String, dynamic>
            ? legacy['data'] as Map<String, dynamic>
            : legacy;
      }
    }

    if (mapJson == null) {
      throw const FormatException('بيانات الخريطة غير صحيحة');
    }

    if (mapJson['calibration'] is! Map<String, dynamic>) {
      try {
        final calibrationJson =
            await _apiClient.get(ApiConfig.calibrationPoints, auth: false);
        if (calibrationJson is Map<String, dynamic>) {
          calibration = MapGpsCalibration.fromJson(calibrationJson);
        }
      } catch (_) {
        calibration = MapGpsCalibration.empty;
      }
    }

    return VisitorMapData.fromJson(mapJson, calibration: calibration);
  }
}
