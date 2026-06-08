import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/animal.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/animals_explore_screen.dart';

class InteractiveMapScreen extends StatefulWidget {
  const InteractiveMapScreen({super.key});

  @override
  State<InteractiveMapScreen> createState() => _InteractiveMapScreenState();
}

class _InteractiveMapScreenState extends State<InteractiveMapScreen> {
  final TransformationController _transformationController = TransformationController();
  String _selectedCategory = 'الكل';

  final List<String> _categories = ['الكل', 'مفترسات', 'طيور', 'ثدييات', 'زواحف'];

  // تفاصيل الدبابيس التفاعلية المتطابقة مع الصورة
  final List<Map<String, dynamic>> _pins = [
    {
      'id': 'lion',
      'name': 'الأسد الإفريقي',
      'category': 'مفترسات',
      'x': 0.50,
      'y': 0.22,
      'image': 'assets/images/lion.jpg',
      'description': 'ملك الغابة، أحد أقوى المفترسات في الحديقة، يعيش في مساحة مفتوحة تشبه موطنه الأصلي السافانا.',
      'diet': 'لحوم',
      'status': 'نشط الآن',
    },
    {
      'id': 'parrot',
      'name': 'الببغاء الملون',
      'category': 'طيور',
      'x': 0.68,
      'y': 0.20,
      'image': 'assets/images/flamengo.jpg',
      'description': 'طائر استوائي ذكي ومحبوب، يتميز بريشه الملون الخلاب وقدرته المدهشة على تقليد الأصوات والكلمات.',
      'diet': 'فواكه وبذور',
      'status': 'نشط الآن',
    },
    {
      'id': 'elephant',
      'name': 'الفيل الآسيوي',
      'category': 'ثدييات',
      'x': 0.18,
      'y': 0.32,
      'image': 'assets/images/bear.jpg',
      'description': 'أضخم الثدييات البرية في الحديقة، يتمتع بذكاء فائق وذاكرة قوية، ويفضل اللعب بالماء والاستحمام يومياً.',
      'diet': 'عشبي',
      'status': 'وقت الاستحمام',
    },
    {
      'id': 'tiger',
      'name': 'النمر السيبيري',
      'category': 'مفترسات',
      'x': 0.31,
      'y': 0.51,
      'image': 'assets/images/tiger.jpg',
      'description': 'أكبر القطط الكبيرة في العالم، يشتهر بفرائه المخطط المهيب وبنيته العضلية القوية ومهاراته العالية في الصيد والتسلل.',
      'diet': 'لحوم',
      'status': 'يستريح الآن',
    },
    {
      'id': 'panda',
      'name': 'الباندا العملاق',
      'category': 'ثدييات',
      'x': 0.53,
      'y': 0.78,
      'image': 'assets/images/Hello2.jpg',
      'description': 'من أندر حيوانات الحديقة وأكثرها شعبية، يقضي معظم يومه في تناول عيدان الخيزران الطازجة واللعب في بركته الخاصة.',
      'diet': 'أوراق الخيزران',
      'status': 'نشط الآن',
    },
    {
      'id': 'red_panda',
      'name': 'الباندا الأحمر',
      'category': 'ثدييات',
      'x': 0.30,
      'y': 0.89,
      'image': 'assets/images/Hello3.jpg',
      'description': 'حيوان صغير الحجم يعيش في أعالي الأشجار، يشبه الثعالب في لونه المحمر الجذاب وذيله الكثيف المخطط.',
      'diet': 'بامبو وفواكه',
      'status': 'نائم الآن',
    },
  ];

  @override
  void dispose() {
    _transformationController.dispose();
    super.dispose();
  }

  void _resetZoom() {
    _transformationController.value = Matrix4.identity();
  }

  void _showAnimalDetails(BuildContext context, Map<String, dynamic> pin) {
    String mappedCategory = 'mammals';
    if (pin['category'] == 'مفترسات') mappedCategory = 'predators';
    if (pin['category'] == 'طيور') mappedCategory = 'birds';
    
    final animal = Animal(
      id: pin['id'].hashCode,
      name: pin['name'],
      sciName: '',
      category: mappedCategory,
      image: pin['image'],
      desc: pin['description'],
      stats: {'الغذاء': pin['diet']},
      facts: [],
      location: 'منطقة A',
      habitat: '',
    );

    Navigator.push(
      context,
      PageRouteBuilder(
        pageBuilder: (_, anim, __) => FadeTransition(
          opacity: anim,
          child: AnimalDetailScreen(animal: animal),
        ),
        transitionDuration: const Duration(milliseconds: 400),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: const Color(0xFFF4F3ED),
        body: Stack(
          children: [
            // ══════════════════════════════════════
            // خريطة تفاعلية زووم وسكرول
            // ══════════════════════════════════════
            Positioned.fill(
              child: InteractiveViewer(
                transformationController: _transformationController,
                maxScale: 4.0,
                minScale: 1.0,
                child: Stack(
                  children: [
                    Image.asset(
                      'assets/images/zoo_map.png',
                      fit: BoxFit.cover,
                      width: double.infinity,
                      height: double.infinity,
                    ),
                    // ─ دبابيس الحيوانات التفاعلية ─
                    Positioned.fill(
                      child: LayoutBuilder(
                        builder: (context, constraints) {
                          final w = constraints.maxWidth;
                          final h = constraints.maxHeight;

                          return Stack(
                            children: _pins.map((pin) {
                              // تصفية الدبابيس حسب الفئة المحددة
                              final showPin = _selectedCategory == 'الكل' ||
                                  pin['category'] == _selectedCategory;

                              if (!showPin) return const SizedBox.shrink();

                              return Positioned(
                                left: w * pin['x'] - 20,
                                top: h * pin['y'] - 20,
                                child: _PulseTarget(
                                  onTap: () => _showAnimalDetails(context, pin),
                                ),
                              );
                            }).toList(),
                          );
                        },
                      ),
                    ),
                  ],
                ),
              ),
            ),

            // ══════════════════════════════════════
            // شريط البحث العلوي والفلاتر والجوائز
            // ══════════════════════════════════════
            Positioned(
              top: topPad + 12,
              left: 20,
              right: 20,
              child: Column(
                children: [
                  // ─ شريط البحث ─
                  Container(
                    height: 50,
                    decoration: BoxDecoration(
                      color: const Color(0xFFE5E5E0).withValues(alpha: 0.96),
                      borderRadius: BorderRadius.circular(25),
                      border: Border.all(
                        color: Colors.black.withValues(alpha: 0.05),
                        width: 1,
                      ),
                    ),
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Row(
                      children: [
                        IconButton(
                          icon: const Icon(Icons.arrow_back_ios_new_rounded,
                              color: Color(0xFF1F2937), size: 18),
                          onPressed: () => context.pop(),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: TextField(
                            style: GoogleFonts.cairo(
                                fontSize: 14, fontWeight: FontWeight.w600),
                            decoration: InputDecoration(
                              hintText: 'بحث...',
                              hintStyle: GoogleFonts.cairo(
                                  color: const Color(0xFF555550),
                                  fontWeight: FontWeight.w600),
                              border: InputBorder.none,
                            ),
                          ),
                        ),
                        const Icon(Icons.search_rounded,
                            color: Color(0xFF555550), size: 24),
                      ],
                    ),
                  ),
                  const SizedBox(height: 10),
                  // ─ فئات الفلترة الدائرية ─
                  SizedBox(
                    height: 38,
                    child: ListView.separated(
                      scrollDirection: Axis.horizontal,
                      itemCount: _categories.length,
                      separatorBuilder: (_, __) => const SizedBox(width: 8),
                      itemBuilder: (context, index) {
                        final cat = _categories[index];
                        final isSelected = _selectedCategory == cat;
                        return GestureDetector(
                          onTap: () {
                            setState(() => _selectedCategory = cat);
                          },
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 18),
                            decoration: BoxDecoration(
                              color: isSelected
                                  ? const Color(0xFF1B4332)
                                  : const Color(0xFF8C733E).withValues(alpha: 0.85),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            alignment: Alignment.center,
                            child: Text(
                              cat,
                              style: GoogleFonts.cairo(
                                fontSize: 13,
                                fontWeight: isSelected
                                    ? FontWeight.w800
                                    : FontWeight.w700,
                                color: Colors.white,
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),

            // ══════════════════════════════════════
            // أزرار التحكم الجانبية السفلية (على اليمين)
            // ══════════════════════════════════════
            Positioned(
              bottom: bottomPad + 90,
              right: 20,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  // ─ زر المطاعم ─
                  _buildSideRoundButton(
                    icon: Icons.restaurant_rounded,
                    onTap: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(
                            'عرض مواقع المطاعم والاستراحات على الخريطة 🍔',
                            style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
                          ),
                          backgroundColor: const Color(0xFF8C733E),
                        ),
                      );
                    },
                  ),
                  const SizedBox(height: 10),
                  // ─ زر دورات المياه والخدمات ─
                  _buildSideRoundButton(
                    icon: Icons.wc_rounded,
                    onTap: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(
                            'عرض مواقع دورات المياه والخدمات على الخريطة 🚻',
                            style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
                          ),
                          backgroundColor: const Color(0xFF8C733E),
                        ),
                      );
                    },
                  ),
                  const SizedBox(height: 10),
                  // ─ زر الموقع الحالي (GPS) ─
                  _buildSideRoundButton(
                    icon: Icons.my_location_rounded,
                    onTap: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(
                            'جاري تحديد موقعك الجغرافي داخل الحديقة... 📍',
                            style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
                          ),
                          backgroundColor: const Color(0xFF1B4332),
                        ),
                      );
                    },
                  ),
                ],
              ),
            ),

          ],
        ),
      ),
    );
  }

  Widget _buildSideRoundButton({
    required IconData icon,
    required VoidCallback onTap,
  }) {
    return Container(
      width: 48,
      height: 48,
      decoration: BoxDecoration(
        color: const Color(0xFF8C733E).withValues(alpha: 0.95),
        shape: BoxShape.circle,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.15),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipOval(
        child: Material(
          color: Colors.transparent,
          child: InkWell(
            onTap: onTap,
            child: Icon(
              icon,
              color: const Color(0xFFF4F3ED),
              size: 22,
            ),
          ),
        ),
      ),
    );
  }
}

// ─ هدف تفاعلي نابض فوق الدبابيس المدمجة بالخريطة ─
class _PulseTarget extends StatefulWidget {
  const _PulseTarget({required this.onTap});

  final VoidCallback onTap;

  @override
  State<_PulseTarget> createState() => _PulseTargetState();
}

class _PulseTargetState extends State<_PulseTarget>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: widget.onTap,
      child: Stack(
        alignment: Alignment.center,
        children: [
          // الحلقة الخارجية النابضة
          AnimatedBuilder(
            animation: _controller,
            builder: (context, child) {
              return Container(
                width: 48 * _controller.value,
                height: 48 * _controller.value,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: const Color(0xFFC5D639).withValues(
                    alpha: 1.0 - _controller.value,
                  ),
                ),
              );
            },
          ),
          // الدائرة الداخلية التفاعلية (شفافة وتغطي الدبوس بالخلفية)
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: Colors.white.withValues(alpha: 0.1),
              border: Border.all(
                color: const Color(0xFFC5D639),
                width: 1.5,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
