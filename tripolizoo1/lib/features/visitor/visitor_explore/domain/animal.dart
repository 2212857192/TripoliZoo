class Animal {
  final int id;
  final String name;
  final String sciName;
  final String category;
  final String image;
  final String desc;
  final Map<String, String> stats;
  final List<String> facts;
  final String location;
  final String habitat;
  final String? qrCode;

  const Animal({
    required this.id,
    required this.name,
    required this.sciName,
    required this.category,
    required this.image,
    required this.desc,
    required this.stats,
    required this.facts,
    required this.location,
    required this.habitat,
    this.qrCode,
  });

  factory Animal.fromJson(Map<String, dynamic> json) => Animal(
        id: json['id'] as int,
        name: json['name'] as String,
        sciName: json['sci_name'] as String,
        category: json['category'] as String,
        image: json['image'] as String,
        desc: json['desc'] as String,
        stats: Map<String, String>.from(json['stats'] as Map),
        facts: List<String>.from(json['facts'] as List),
        location: json['location'] as String,
        habitat: json['habitat'] as String,
        qrCode: json['qr_code'] as String?,
      );
}
