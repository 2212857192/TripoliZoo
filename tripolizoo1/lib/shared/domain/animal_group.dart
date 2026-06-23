class AnimalGroup {
  const AnimalGroup({
    required this.id,
    required this.name,
    required this.codePrefix,
    required this.sortOrder,
  });

  final int id;
  final String name;
  final String codePrefix;
  final int sortOrder;

  factory AnimalGroup.fromJson(Map<String, dynamic> json) {
    return AnimalGroup(
      id: (json['id'] as num?)?.toInt() ?? 0,
      name: json['name']?.toString() ?? '',
      codePrefix: json['code_prefix']?.toString() ?? '',
      sortOrder: (json['sort_order'] as num?)?.toInt() ?? 0,
    );
  }
}
