import 'animal_fact_item.dart';

class Animal {
  final int id;
  final String name;
  final String sciName;
  final String category;
  final String image;
  final String desc;
  final Map<String, String> stats;
  final List<String> facts;
  final List<AnimalFactItem> factItems;
  final String location;
  final String habitat;
  final String? qrCode;
  final int? mapLocationId;
  final double? mapLatitude;
  final double? mapLongitude;

  const Animal({
    required this.id,
    required this.name,
    required this.sciName,
    required this.category,
    required this.image,
    required this.desc,
    required this.stats,
    required this.facts,
    this.factItems = const [],
    required this.location,
    required this.habitat,
    this.qrCode,
    this.mapLocationId,
    this.mapLatitude,
    this.mapLongitude,
  });

  factory Animal.fromJson(Map<String, dynamic> json) {
    final mapLocation = json['map_location'];
    final factItems = (json['fact_items'] as List? ?? const [])
        .whereType<Map<String, dynamic>>()
        .map(AnimalFactItem.fromJson)
        .where((item) => item.label.isNotEmpty && item.value.isNotEmpty)
        .toList();

    return Animal(
      id: (json['id'] as num).toInt(),
      name: json['name']?.toString() ?? 'حيوان',
      sciName: json['sci_name']?.toString() ?? '',
      category: json['category']?.toString() ?? 'mammals',
      image: json['image']?.toString() ?? '',
      desc: json['desc']?.toString() ?? '',
      stats: (json['stats'] as Map? ?? const {})
          .map((key, value) => MapEntry(key.toString(), value.toString())),
      facts: (json['facts'] as List? ?? const [])
          .map((value) => value.toString())
          .toList(),
      factItems: factItems,
      location: json['location']?.toString() ?? '',
      habitat: json['habitat']?.toString() ?? '',
      qrCode: json['qr_code']?.toString(),
      mapLocationId: mapLocation is Map<String, dynamic>
          ? (mapLocation['id'] as num?)?.toInt()
          : null,
      mapLatitude: mapLocation is Map<String, dynamic>
          ? (mapLocation['latitude'] as num?)?.toDouble()
          : null,
      mapLongitude: mapLocation is Map<String, dynamic>
          ? (mapLocation['longitude'] as num?)?.toDouble()
          : null,
    );
  }

  bool get hasNetworkImage =>
      image.startsWith('http://') || image.startsWith('https://');

  bool get hasMapLocation => mapLocationId != null;

  List<AnimalFactItem> get displayFactItems {
    if (factItems.isNotEmpty) return factItems;
    return facts
        .where((fact) => fact.trim().isNotEmpty)
        .map((fact) => AnimalFactItem(label: 'معلومة', value: fact))
        .toList();
  }
}
