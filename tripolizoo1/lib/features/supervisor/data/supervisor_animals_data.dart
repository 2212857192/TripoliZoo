/// حيوانات المجموعة — بيانات تجريبية لقوائم الاختيار.
class SupervisorAnimal {
  const SupervisorAnimal({
    required this.id,
    required this.name,
    this.type,
  });

  final String id;
  final String name;
  final String? type;

  String get label => type != null ? '$id — $type' : id;
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
