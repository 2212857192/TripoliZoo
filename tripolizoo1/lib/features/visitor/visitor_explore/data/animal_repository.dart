import 'package:tripolizoo/features/visitor/visitor_explore/domain/animal.dart';

abstract class AnimalRepository {
  Future<List<Animal>> getAll();
  Future<Animal?> getByQrCode(String code);
}

class MockAnimalRepository implements AnimalRepository {
  static const List<Animal> _animals = [
    Animal(
      id: 1,
      name: 'الأسد الأفريقي',
      sciName: 'Panthera leo',
      category: 'predators',
      image: 'assets/images/lion.jpg',
      desc:
          'يُلقب بملك الغابة، وهو الحيوان الوحيد من فصيلة السنوريات الذي يعيش في مجموعات اجتماعية معقدة تسمى "الكبرياء".',
      habitat: 'السافانا المفتوحة والأراضي العشبية في أفريقيا جنوب الصحراء الكبرى.',
      stats: {'العمر': '12-16 سنة', 'الوزن': '190 كجم', 'الغذاء': 'لحوم'},
      facts: [
        'يمكن سماع زئير الأسد من مسافة تصل إلى 8 كيلومترات.',
        'تنام الأسود لمدة تصل إلى 20 ساعة يومياً.',
        'تعتبر الإناث هي الصياد الرئيسي في المجموعة.',
      ],
      location: 'مملكة الأسود',
      qrCode: 'ANIMAL-LION-001',
    ),
    Animal(
      id: 2,
      name: 'النمر البنغالي',
      sciName: 'Panthera tigris',
      category: 'predators',
      image: 'assets/images/tiger.jpg',
      desc:
          'أكبر قط في العالم، يتميز بجماله الفائق ونمط خطوطه الفريد الذي لا يتكرر في نمر آخر.',
      habitat: 'الغابات الاستوائية المطيرة والمناطق العشبية الطويلة.',
      stats: {'العمر': '15-20 سنة', 'الوزن': '220 كجم', 'الغذاء': 'لحوم'},
      facts: [
        'النمور سباحة ماهرة جداً وتحب الماء.',
        'خطوط النمر موجودة على جلده أيضاً.',
        'يمكن للنمر القفز لمسافة تصل إلى 10 أمتار.',
      ],
      location: 'وادي النمور',
      qrCode: 'ANIMAL-TIGER-002',
    ),
    Animal(
      id: 3,
      name: 'الدب البني',
      sciName: 'Ursus arctos',
      category: 'mammals',
      image: 'assets/images/bear.jpg',
      desc:
          'حيوان ثديي ضخم وقوي، يتميز بفروه الكثيف وقدرته العالية على التكيف.',
      habitat: 'الغابات الكثيفة، المناطق الجبلية، وضفاف الأنهار.',
      stats: {'العمر': '20-30 سنة', 'الوزن': '600 كجم', 'الغذاء': 'متنوع'},
      facts: [
        'تتمتع الدببة بحاسة شم أقوى بـ 2100 مرة من الإنسان.',
        'يمكن للدب البني الركض بسرعة 48 كم/س.',
        'تقضي الدببة فترة الشتاء في سبات عميق.',
      ],
      location: 'منطقة الغابات',
      qrCode: 'ANIMAL-BEAR-003',
    ),
  ];

  @override
  Future<List<Animal>> getAll() async {
    await Future.delayed(const Duration(milliseconds: 200));
    return _animals;
  }

  @override
  Future<Animal?> getByQrCode(String code) async {
    await Future.delayed(const Duration(milliseconds: 150));
    try {
      return _animals.firstWhere((a) => a.qrCode == code);
    } catch (_) {
      return null;
    }
  }
}
