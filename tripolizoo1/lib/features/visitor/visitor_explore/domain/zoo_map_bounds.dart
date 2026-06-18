class ZooMapBounds {
  const ZooMapBounds({
    required this.north,
    required this.south,
    required this.west,
    required this.east,
    required this.imageWidth,
    required this.imageHeight,
  });

  final double north;
  final double south;
  final double west;
  final double east;
  final double imageWidth;
  final double imageHeight;

  static const defaults = ZooMapBounds(
    north: 32.8901,
    south: 32.8850,
    west: 13.1721,
    east: 13.1789,
    imageWidth: 4516,
    imageHeight: 3374,
  );

  factory ZooMapBounds.fromNavigationJson(Map<String, dynamic> json) {
    final bounds = json['bounds'] as Map<String, dynamic>? ?? {};

    return ZooMapBounds(
      north: (bounds['north'] as num?)?.toDouble() ?? defaults.north,
      south: (bounds['south'] as num?)?.toDouble() ?? defaults.south,
      west: (bounds['west'] as num?)?.toDouble() ?? defaults.west,
      east: (bounds['east'] as num?)?.toDouble() ?? defaults.east,
      imageWidth:
          (json['image_width'] as num?)?.toDouble() ?? defaults.imageWidth,
      imageHeight:
          (json['image_height'] as num?)?.toDouble() ?? defaults.imageHeight,
    );
  }
}
