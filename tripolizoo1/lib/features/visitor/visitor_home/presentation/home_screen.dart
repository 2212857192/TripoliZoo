import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> with TickerProviderStateMixin {
  final PageController _pageController = PageController();
  int _currentPage = 0;
  Timer? _timer;

  late final AnimationController _fadeController;
  late final Animation<double> _fadeAnimation;

  final List<Map<String, String>> _slides = [
    {
      'image': 'assets/images/lion.jpg',
      'title': 'الأسد الأفريقي',
      'label': 'القطط الكبرى',
    },
    {
      'image': 'assets/images/tiger.jpg',
      'title': 'النمر البنغالي',
      'label': 'القطط الكبرى',
    },
    {
      'image': 'assets/images/bear.jpg',
      'title': 'الدب البني',
      'label': 'الثدييات',
    },
    {
      'image': 'assets/images/flamengo.jpg',
      'title': 'الفلامينغو',
      'label': 'الطيور',
    },
  ];

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _fadeAnimation = CurvedAnimation(parent: _fadeController, curve: Curves.easeOut);
    _fadeController.forward();

    _timer = Timer.periodic(const Duration(seconds: 4), (_) {
      if (!mounted || !_pageController.hasClients) return;
      final next = (_currentPage + 1) % _slides.length;
      _pageController.animateToPage(
        next,
        duration: const Duration(milliseconds: 800),
        curve: Curves.easeInOutCubic,
      );
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    _fadeController.dispose();
    super.dispose();
  }

  Future<void> _makeCall(String number) async {
    final uri = Uri(scheme: 'tel', path: number);
    if (await canLaunchUrl(uri)) await launchUrl(uri);
  }

  Future<void> _launchMaps() async {
    final uri = Uri.parse(
      'https://www.google.com/maps/search/?api=1&query=${AppConstants.zooLatitude},${AppConstants.zooLongitude}',
    );
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    final locale = context.watch<LocaleProvider>();
    final size = MediaQuery.of(context).size;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.light,
      child: Scaffold(
        backgroundColor: const Color(0xFFF7F9F7),
        body: FadeTransition(
          opacity: _fadeAnimation,
          child: CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              // ═══ HERO SECTION ═══
              SliverToBoxAdapter(
                child: Stack(
                  children: [
                    // Sliding images
                    SizedBox(
                      height: size.height * 0.52,
                      child: PageView.builder(
                        controller: _pageController,
                        onPageChanged: (i) => setState(() => _currentPage = i),
                        itemCount: _slides.length,
                        itemBuilder: (context, index) {
                          return Stack(
                            fit: StackFit.expand,
                            children: [
                              Image.asset(
                                _slides[index]['image']!,
                                fit: BoxFit.cover,
                                cacheWidth: 1000,
                              ),
                              // Dark gradient overlay
                              Container(
                                decoration: const BoxDecoration(
                                  gradient: LinearGradient(
                                    begin: Alignment.topCenter,
                                    end: Alignment.bottomCenter,
                                    colors: [
                                      Color(0x880F3D24),
                                      Color(0xCC0B2E1A),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          );
                        },
                      ),
                    ),

                    // Content on top of image
                    Positioned.fill(
                      child: SafeArea(
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 22),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const SizedBox(height: 8),

                              // ── Top Bar ──
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  // Lang switcher
                                  GestureDetector(
                                    onTap: locale.cycleLocale,
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
                                      decoration: BoxDecoration(
                                        color: Colors.white.withValues(alpha: 0.18),
                                        borderRadius: BorderRadius.circular(30),
                                        border: Border.all(color: Colors.white.withValues(alpha: 0.3)),
                                      ),
                                      child: Row(
                                        children: [
                                          Text(
                                            locale.code,
                                            style: const TextStyle(
                                              color: Colors.white,
                                              fontWeight: FontWeight.w800,
                                              fontSize: 13,
                                            ),
                                          ),
                                          const SizedBox(width: 5),
                                          const Icon(Icons.language_rounded, color: Colors.white70, size: 16),
                                        ],
                                      ),
                                    ),
                                  ),

                                  // Logo + name
                                  Row(
                                    children: [
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.end,
                                        children: [
                                          const Text(
                                            'حديقة طرابلس',
                                            style: TextStyle(
                                              color: Colors.white,
                                              fontSize: 17,
                                              fontWeight: FontWeight.w900,
                                              letterSpacing: 0.3,
                                            ),
                                          ),
                                          Text(
                                            'TRIPOLI ZOO',
                                            style: TextStyle(
                                              color: Colors.white.withValues(alpha: 0.65),
                                              fontSize: 10,
                                              fontWeight: FontWeight.w600,
                                              letterSpacing: 2.5,
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(width: 10),
                                      Container(
                                        width: 42,
                                        height: 42,
                                        decoration: BoxDecoration(
                                          shape: BoxShape.circle,
                                          color: Colors.white.withValues(alpha: 0.15),
                                          border: Border.all(color: Colors.white.withValues(alpha: 0.3)),
                                        ),
                                        child: ClipOval(
                                          child: Image.asset(
                                            'assets/images/شعار.jpg',
                                            fit: BoxFit.cover,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),

                              const Spacer(),

                              // ── Animal Label ──
                              AnimatedSwitcher(
                                duration: const Duration(milliseconds: 400),
                                child: Container(
                                  key: ValueKey(_currentPage),
                                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                                  decoration: BoxDecoration(
                                    color: AppColors.accent.withValues(alpha: 0.85),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Text(
                                    _slides[_currentPage]['label']!,
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 11,
                                      fontWeight: FontWeight.w800,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(height: 8),

                              // ── Animal Name ──
                              AnimatedSwitcher(
                                duration: const Duration(milliseconds: 400),
                                child: Text(
                                  key: ValueKey('name_$_currentPage'),
                                  _slides[_currentPage]['title']!,
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 30,
                                    fontWeight: FontWeight.w900,
                                    letterSpacing: 0.2,
                                    height: 1.2,
                                  ),
                                ),
                              ),
                              const SizedBox(height: 18),

                              // ── Open Status + Dots ──
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  // Open badge
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                    decoration: BoxDecoration(
                                      color: Colors.white.withValues(alpha: 0.18),
                                      borderRadius: BorderRadius.circular(20),
                                      border: Border.all(color: Colors.white.withValues(alpha: 0.25)),
                                    ),
                                    child: Row(
                                      children: [
                                        Container(
                                          width: 7,
                                          height: 7,
                                          decoration: const BoxDecoration(
                                            color: Color(0xFF4ADE80),
                                            shape: BoxShape.circle,
                                          ),
                                        ),
                                        const SizedBox(width: 7),
                                        const Text(
                                          'مفتوح  •  10:00 – 18:00',
                                          style: TextStyle(
                                            color: Colors.white,
                                            fontSize: 12,
                                            fontWeight: FontWeight.w700,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),

                                  // Slide dots
                                  Row(
                                    children: List.generate(_slides.length, (i) {
                                      return AnimatedContainer(
                                        duration: const Duration(milliseconds: 300),
                                        margin: const EdgeInsets.symmetric(horizontal: 3),
                                        width: _currentPage == i ? 22 : 7,
                                        height: 7,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(4),
                                          color: _currentPage == i
                                              ? Colors.white
                                              : Colors.white.withValues(alpha: 0.35),
                                        ),
                                      );
                                    }),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 22),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // ═══ BODY CONTENT ═══
              SliverToBoxAdapter(
                child: Container(
                  decoration: const BoxDecoration(
                    color: Color(0xFFF7F9F7),
                    borderRadius: BorderRadius.only(
                      topLeft: Radius.circular(30),
                      topRight: Radius.circular(30),
                    ),
                  ),
                  transform: Matrix4.translationValues(0, -28, 0),
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 28, 20, 120),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [

                        // ── Quick Actions Row ──
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: [
                            _QuickAction(
                              icon: Icons.confirmation_num_rounded,
                              label: 'التذاكر',
                              color: const Color(0xFF0B5913),
                              onTap: () => context.go('/tickets'),
                            ),
                            _QuickAction(
                              icon: Icons.pets_rounded,
                              label: 'الحيوانات',
                              color: const Color(0xFF1B8A2A),
                              onTap: () => context.push('/animals'),
                            ),
                            _QuickAction(
                              icon: Icons.qr_code_scanner_rounded,
                              label: 'مسح QR',
                              color: const Color(0xFF2E7D32),
                              onTap: () => context.push('/qr-scanner'),
                            ),
                            _QuickAction(
                              icon: Icons.map_rounded,
                              label: 'الخريطة',
                              color: const Color(0xFF388E3C),
                              onTap: () => context.go('/map'),
                            ),
                          ],
                        ),

                        const SizedBox(height: 32),

                        // ── Section Title ──
                        const Text(
                          'استكشف الحديقة',
                          style: TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.w900,
                            color: Color(0xFF0F3D24),
                          ),
                        ),
                        const SizedBox(height: 16),

                        // ── Featured 2-col grid ──
                        Row(
                          children: [
                            Expanded(
                              flex: 5,
                              child: _FeaturedCard(
                                title: 'الجولة\nالافتراضية 360°',
                                subtitle: 'تجربة غامرة',
                                icon: Icons.threesixty_rounded,
                                gradientColors: const [Color(0xFF0B5913), Color(0xFF1B8A2A)],
                                height: 180,
                                onTap: () => context.push('/virtual-tour'),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              flex: 4,
                              child: Column(
                                children: [
                                  _FeaturedCard(
                                    title: 'دليل الزيارة',
                                    subtitle: 'المواعيد والأسعار',
                                    icon: Icons.info_outline_rounded,
                                    gradientColors: const [Color(0xFFF57C00), Color(0xFFFF9800)],
                                    height: 84,
                                    onTap: () => context.push('/visit-info'),
                                  ),
                                  const SizedBox(height: 12),
                                  _FeaturedCard(
                                    title: 'موقع الحديقة',
                                    subtitle: 'خريطة الطريق',
                                    icon: Icons.location_on_rounded,
                                    gradientColors: const [Color(0xFF0891B2), Color(0xFF0E7490)],
                                    height: 84,
                                    onTap: _launchMaps,
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),

                        const SizedBox(height: 32),

                        // ── Emergency ──
                        const Text(
                          'المساعدة السريعة',
                          style: TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.w900,
                            color: Color(0xFF0F3D24),
                          ),
                        ),
                        const SizedBox(height: 14),
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(24),
                            border: Border.all(color: const Color(0xFFFFE4E4)),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.red.withValues(alpha: 0.06),
                                blurRadius: 20,
                                offset: const Offset(0, 6),
                              ),
                            ],
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: _EmergencyButton(
                                  label: 'الإسعاف',
                                  icon: Icons.medical_services_rounded,
                                  color: const Color(0xFFDC2626),
                                  onTap: () => _makeCall(AppConstants.emergencyMedical),
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: _EmergencyButton(
                                  label: 'الأمن',
                                  icon: Icons.security_rounded,
                                  color: const Color(0xFF0F3D24),
                                  onTap: () => _makeCall(AppConstants.emergencySecurity),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ═══════════════════════════════════════════
// WIDGETS
// ═══════════════════════════════════════════

class _QuickAction extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  const _QuickAction({
    required this.icon,
    required this.label,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            width: 58,
            height: 58,
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: color.withValues(alpha: 0.15)),
            ),
            child: Icon(icon, color: color, size: 26),
          ),
          const SizedBox(height: 8),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w800,
              color: const Color(0xFF1A1A1A).withValues(alpha: 0.8),
            ),
          ),
        ],
      ),
    );
  }
}

class _FeaturedCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final List<Color> gradientColors;
  final double height;
  final VoidCallback onTap;

  const _FeaturedCard({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.gradientColors,
    required this.height,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final isLarge = height > 120;
    return GestureDetector(
      onTap: onTap,
      child: Container(
        height: height,
        padding: EdgeInsets.all(isLarge ? 18 : 14),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topRight,
            end: Alignment.bottomLeft,
            colors: gradientColors,
          ),
          borderRadius: BorderRadius.circular(22),
          boxShadow: [
            BoxShadow(
              color: gradientColors.first.withValues(alpha: 0.3),
              blurRadius: 18,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        child: isLarge
            ? Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(icon, color: Colors.white.withValues(alpha: 0.9), size: 32),
                  const Spacer(),
                  Text(
                    title,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                      height: 1.25,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    subtitle,
                    style: TextStyle(
                      color: Colors.white.withValues(alpha: 0.7),
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              )
            : Row(
                children: [
                  Icon(icon, color: Colors.white.withValues(alpha: 0.9), size: 22),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          title,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 13,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          subtitle,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(
                            color: Colors.white.withValues(alpha: 0.7),
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
      ),
    );
  }
}

class _EmergencyButton extends StatelessWidget {
  final String label;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  const _EmergencyButton({
    required this.label,
    required this.icon,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.08),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withValues(alpha: 0.2)),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: color, size: 20),
            const SizedBox(width: 8),
            Text(
              label,
              style: TextStyle(
                color: color,
                fontWeight: FontWeight.w800,
                fontSize: 15,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
