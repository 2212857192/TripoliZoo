/// حيوانات المجموعة — نموذج اختيار من الـ API.
class SupervisorAnimal {
  const SupervisorAnimal({
    required this.id,
    required this.name,
    this.type,
    this.customLabel,
  });

  final String id;
  final String name;
  final String? type;
  final String? customLabel;

  String get label =>
      customLabel ??
      (name.isNotEmpty
          ? type != null
              ? '$id — $name ($type)'
              : '$id — $name'
          : type != null
              ? '$id — $type'
              : id);

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is SupervisorAnimal && other.id == id;

  @override
  int get hashCode => id.hashCode;
}

abstract final class SupervisorAnimalsData {
  static const groupAnimals = [
    SupervisorAnimal(id: 'A-088', name: 'غزال 1', type: 'غزال'),
    SupervisorAnimal(id: 'A-102', name: 'نمر', type: 'نمر'),
    SupervisorAnimal(id: 'A-055', name: 'غزال أم', type: 'غزال'),
    SupervisorAnimal(id: 'A-099', name: 'قرد', type: 'قرد'),
    SupervisorAnimal(id: 'A-078', name: 'فهد', type: 'فهد'),
  ];

  static const newbornAnimals = [
    SupervisorAnimal(id: 'N-012', name: 'مولود غزال', type: 'غزال'),
    SupervisorAnimal(id: 'N-018', name: 'مولود نمر', type: 'نمر'),
  ];

  /// إناث المجموعة المؤهلة كأمهات.
  static const mothers = [
    SupervisorAnimal(id: 'A-055', name: 'غزال أم', type: 'غزال'),
    SupervisorAnimal(id: 'A-088', name: 'غزالة', type: 'غزال'),
    SupervisorAnimal(id: 'A-102', name: 'نمرة', type: 'نمر'),
  ];
}
