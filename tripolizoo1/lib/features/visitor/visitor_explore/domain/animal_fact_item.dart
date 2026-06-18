class AnimalFactItem {
  const AnimalFactItem({
    required this.label,
    required this.value,
  });

  final String label;
  final String value;

  factory AnimalFactItem.fromJson(Map<String, dynamic> json) => AnimalFactItem(
        label: json['label']?.toString() ?? '',
        value: json['value']?.toString() ?? '',
      );
}
