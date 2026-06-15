import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/animal.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/animal_repository.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';
import 'package:tripolizoo/shared/widgets/white_pinned_sliver_header.dart';

// ─────────────────────────────────────────────
//  Category model
// ─────────────────────────────────────────────
class _Category {
  final String id;
  final String label;
  final IconData icon;
  const _Category(this.id, this.label, this.icon);
}

const _categories = [
  _Category('all', 'الكل', Icons.apps_rounded),
  _Category('predators', 'مفترسات', Icons.crisis_alert_rounded),
  _Category('birds', 'طيور', Icons.flutter_dash),
  _Category('mammals', 'ثدييات', Icons.nature_people_rounded),
];

// ─────────────────────────────────────────────
//  Main screen
// ─────────────────────────────────────────────
class AnimalsExploreScreen extends StatefulWidget {
  const AnimalsExploreScreen({super.key});

  @override
  State<AnimalsExploreScreen> createState() => _AnimalsExploreScreenState();
}

class _AnimalsExploreScreenState extends State<AnimalsExploreScreen>
    with TickerProviderStateMixin {
  final _repo = MockAnimalRepository();
  List<Animal> _animals = [];
  String _category = 'all';
  String _search = '';
  bool _loading = true;
  final _searchCtrl = TextEditingController();

  late final AnimationController _fadeCtrl = AnimationController(
    vsync: this,
    duration: const Duration(milliseconds: 500),
  );
  late final Animation<double> _fadeAnim = CurvedAnimation(
    parent: _fadeCtrl,
    curve: Curves.easeOut,
  );

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _fadeCtrl.dispose();
    _searchCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    final data = await _repo.getAll();
    if (mounted) {
      setState(() {
        _animals = data;
        _loading = false;
      });
      _fadeCtrl.forward();
    }
  }

  List<Animal> get _filtered => _animals.where((a) {
        final cat = _category == 'all' || a.category == _category;
        final s = _search.isEmpty ||
            a.name.contains(_search) ||
            a.desc.contains(_search);
        return cat && s;
      }).toList();

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Scaffold(
        backgroundColor: Colors.white,
        body: CustomScrollView(
          slivers: [
            WhitePinnedSliverHeader(
              toolbarHeight: 72,
              child: _buildHeader(),
            ),
            if (_loading)
              const SliverFillRemaining(
                hasScrollBody: false,
                child: _LoadingView(),
              )
            else ...[
              SliverFadeTransition(
                opacity: _fadeAnim,
                sliver: _buildSearchBar(),
              ),
              SliverFadeTransition(
                opacity: _fadeAnim,
                sliver: _buildCategoryRow(),
              ),
              SliverFadeTransition(
                opacity: _fadeAnim,
                sliver: _buildStaggeredGrid(),
              ),
              const SliverToBoxAdapter(child: SizedBox(height: 24)),
            ],
          ],
        ),
      ),
    );
  }

  // ── Header — نفس أسلوب التذاكر / حسابي ─────────────
  Widget _buildHeader() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      child: CenteredPageHeader(
        title: context.localized(
          ar: 'اكتشف الحيوانات',
          en: 'Explore Animals',
        ),
        leading: GestureDetector(
          onTap: () => context.pop(),
          child: Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              color: Colors.grey.shade100,
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.arrow_forward_ios_rounded,
              size: 16,
              color: Colors.black87,
            ),
          ),
        ),
      ),
    );
  }

  // ── Search bar ─────────────────────────────────────
  Widget _buildSearchBar() {
    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 4),
        child: Container(
          decoration: BoxDecoration(
            color: Colors.grey.shade50,
            borderRadius: BorderRadius.circular(50),
            border: Border.all(color: Colors.grey.shade200),
          ),
          child: TextField(
            controller: _searchCtrl,
            textAlign: TextAlign.right,
            textDirection: TextDirection.rtl,
            onChanged: (v) => setState(() => _search = v),
            style:
                GoogleFonts.cairo(fontSize: 14, color: const Color(0xFF1A1A1A)),
            decoration: InputDecoration(
              hintText: 'ابحث عن نوع...',
              hintStyle: GoogleFonts.cairo(
                color: Colors.grey.shade400,
                fontSize: 14,
              ),
              prefixIcon:
                  Icon(Icons.search_rounded, color: Colors.grey.shade400),
              suffixIcon: _search.isNotEmpty
                  ? IconButton(
                      icon: Icon(Icons.clear_rounded,
                          color: Colors.grey.shade400, size: 18),
                      onPressed: () {
                        _searchCtrl.clear();
                        setState(() => _search = '');
                      },
                    )
                  : null,
              border: InputBorder.none,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
            ),
          ),
        ),
      ),
    );
  }

  // ── Category chips (RTL, scrollable) ───────────────
  Widget _buildCategoryRow() {
    return SliverToBoxAdapter(
      child: SizedBox(
        height: 56,
        child: ListView.builder(
          scrollDirection: Axis.horizontal,
          reverse: true,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          itemCount: _categories.length,
          itemBuilder: (_, i) {
            final cat = _categories[i];
            final active = _category == cat.id;
            return GestureDetector(
              onTap: () => setState(() => _category = cat.id),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 220),
                curve: Curves.easeInOut,
                margin: const EdgeInsets.only(right: 10),
                padding:
                    const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                decoration: BoxDecoration(
                  color: active ? AppColors.primary : Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(50),
                  border: Border.all(
                    color: active ? AppColors.primary : Colors.grey.shade200,
                    width: 1.5,
                  ),
                  boxShadow: active
                      ? [
                          BoxShadow(
                            color: AppColors.primary.withValues(alpha: 0.28),
                            blurRadius: 8,
                            offset: const Offset(0, 3),
                          ),
                        ]
                      : [],
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      cat.label,
                      style: GoogleFonts.cairo(
                        color: active ? Colors.white : Colors.grey.shade700,
                        fontWeight: FontWeight.w700,
                        fontSize: 13,
                      ),
                    ),
                    const SizedBox(width: 6),
                    Icon(
                      cat.icon,
                      size: 15,
                      color: active ? Colors.white : Colors.grey.shade500,
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  // ── Staggered masonry grid ──────────────────────────
  Widget _buildStaggeredGrid() {
    final items = _filtered;
    if (items.isEmpty) {
      return const SliverFillRemaining(child: _EmptyState());
    }

    // Group into chunks of 3: 1 tall (left) + 2 short stacked (right)
    final groups = <List<Animal>>[];
    for (int i = 0; i < items.length; i += 3) {
      final end = (i + 3) > items.length ? items.length : i + 3;
      groups.add(items.sublist(i, end));
    }

    return SliverList(
      delegate: SliverChildBuilderDelegate(
        (ctx, i) {
          final group = groups[i];
          final tall = group[0];
          final short1 = group.length > 1 ? group[1] : null;
          final short2 = group.length > 2 ? group[2] : null;

          return Padding(
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Tall card (left column ~55%)
                Expanded(
                  flex: 55,
                  child: _AnimalCard(animal: tall, height: 362),
                ),
                const SizedBox(width: 12),
                // Two short cards stacked (right column ~45%)
                Expanded(
                  flex: 45,
                  child: Column(
                    children: [
                      if (short1 != null)
                        _AnimalCard(animal: short1, height: 175),
                      if (short1 != null && short2 != null)
                        const SizedBox(height: 12),
                      if (short2 != null)
                        _AnimalCard(animal: short2, height: 175),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
        childCount: groups.length,
        addAutomaticKeepAlives: false,
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  Animal card — Discover style
// ─────────────────────────────────────────────
class _AnimalCard extends StatefulWidget {
  final Animal animal;
  final double height;
  const _AnimalCard({required this.animal, required this.height});

  @override
  State<_AnimalCard> createState() => _AnimalCardState();
}

class _AnimalCardState extends State<_AnimalCard>
    with SingleTickerProviderStateMixin {
  late final AnimationController _ctrl;
  late final Animation<double> _scaleAnim;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 150));
    _scaleAnim = Tween<double>(begin: 1, end: 0.97)
        .animate(CurvedAnimation(parent: _ctrl, curve: Curves.easeInOut));
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  String get _categoryArabic {
    switch (widget.animal.category) {
      case 'predators':
        return 'مفترسات';
      case 'birds':
        return 'طيور';
      case 'mammals':
        return 'ثدييات';
      default:
        return '';
    }
  }

  @override
  Widget build(BuildContext context) {
    final isTall = widget.height > 250;
    return ScaleTransition(
      scale: _scaleAnim,
      child: GestureDetector(
        onTapDown: (_) => _ctrl.forward(),
        onTapUp: (_) {
          _ctrl.reverse();
          Navigator.of(context, rootNavigator: true).push(
            MaterialPageRoute<void>(
              builder: (_) => AnimalDetailScreen(animal: widget.animal),
            ),
          );
        },
        onTapCancel: () => _ctrl.reverse(),
        child: SizedBox(
          height: widget.height,
          child: ClipRRect(
            borderRadius: BorderRadius.circular(22),
            child: Stack(
              fit: StackFit.expand,
              children: [
                // ── Animal image
                Image.asset(
                  widget.animal.image,
                  fit: BoxFit.cover,
                  errorBuilder: (_, __, ___) => Container(
                    color: const Color(0xFF1B3A2A),
                    child:
                        const Icon(Icons.pets, color: Colors.white24, size: 48),
                  ),
                ),

                // ── Dark gradient overlay at bottom
                Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      stops: const [0.35, 1.0],
                      colors: [
                        Colors.transparent,
                        Colors.black.withValues(alpha: 0.80),
                      ],
                    ),
                  ),
                ),

                // ── Bottom text: category label + animal name
                Positioned(
                  left: 12,
                  right: 12,
                  bottom: 14,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      if (_categoryArabic.isNotEmpty)
                        Text(
                          _categoryArabic,
                          style: GoogleFonts.cairo(
                            fontSize: 10,
                            fontWeight: FontWeight.w700,
                            color: Colors.white.withValues(alpha: 0.72),
                            letterSpacing: 1.2,
                          ),
                        ),
                      const SizedBox(height: 2),
                      Text(
                        widget.animal.name,
                        style: GoogleFonts.cairo(
                          fontSize: isTall ? 17 : 13,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                          height: 1.2,
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
    );
  }
}

// ─────────────────────────────────────────────
//  Animal Detail Screen
// ─────────────────────────────────────────────
class AnimalDetailScreen extends StatelessWidget {
  final Animal animal;
  const AnimalDetailScreen({super.key, required this.animal});

  String _categoryLabel(BuildContext context) {
    return switch (animal.category) {
      'predators' => context.localized(ar: 'مفترس', en: 'Predator'),
      'birds' => context.localized(ar: 'طائر', en: 'Bird'),
      'mammals' => context.localized(ar: 'ثديي', en: 'Mammal'),
      _ => '',
    };
  }

  String _informationText(BuildContext context) {
    final stats = animal.stats.entries
        .map((entry) => '${entry.key}: ${entry.value}')
        .join(' · ');

    return [
      animal.desc,
      '${context.localized(ar: 'الموطن', en: 'Habitat')}: ${animal.habitat}',
      '${context.localized(ar: 'الموقع', en: 'Location')}: ${animal.location}',
      if (stats.isNotEmpty)
        '${context.localized(ar: 'معلومات سريعة', en: 'Quick facts')}: $stats',
    ].join('\n\n');
  }

  Color get _categoryColor {
    switch (animal.category) {
      case 'predators':
        return const Color(0xFFB71C1C);
      case 'birds':
        return const Color(0xFF01579B);
      case 'mammals':
        return const Color(0xFF4527A0);
      default:
        return AppColors.primary;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: CustomScrollView(
        slivers: [
          // ── Hero image header ──────────────────────────
          SliverAppBar(
            expandedHeight: 340,
            pinned: true,
            stretch: true,
            backgroundColor: Colors.white,
            foregroundColor: AppColors.textPrimary,
            surfaceTintColor: Colors.white,
            systemOverlayStyle: SystemUiOverlayStyle.dark,
            automaticallyImplyLeading: false,
            leading: Padding(
              padding: const EdgeInsets.all(8),
              child: CircleAvatar(
                backgroundColor: Colors.white.withValues(alpha: 0.9),
                child: IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new_rounded,
                      color: AppColors.textPrimary, size: 18),
                  onPressed: () => Navigator.pop(context),
                ),
              ),
            ),
            flexibleSpace: FlexibleSpaceBar(
              centerTitle: true,
              title: _CollapsedAnimalTitle(title: animal.name),
              titlePadding: const EdgeInsets.only(bottom: 16),
              stretchModes: const [
                StretchMode.zoomBackground,
                StretchMode.blurBackground
              ],
              background: Stack(
                fit: StackFit.expand,
                children: [
                  Image.asset(
                    animal.image,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(
                      color: const Color(0xFF1B3A2A),
                      child: const Icon(Icons.pets,
                          color: Colors.white30, size: 80),
                    ),
                  ),
                  // Bottom gradient
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        stops: const [0.5, 1.0],
                        colors: [
                          Colors.transparent,
                          Colors.black.withValues(alpha: 0.7),
                        ],
                      ),
                    ),
                  ),
                  // Name overlay at bottom of image
                  Positioned(
                    left: 20,
                    right: 20,
                    bottom: 20,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: _categoryColor,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            _categoryLabel(context),
                            style: const TextStyle(
                                color: Colors.white,
                                fontSize: 11,
                                fontWeight: FontWeight.bold),
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          animal.name,
                          textAlign: TextAlign.right,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 28,
                            fontWeight: FontWeight.w900,
                            shadows: [
                              Shadow(blurRadius: 8, color: Colors.black54)
                            ],
                          ),
                        ),
                        Text(
                          animal.sciName,
                          textAlign: TextAlign.right,
                          style: TextStyle(
                            color: Colors.white.withValues(alpha: 0.75),
                            fontSize: 14,
                            fontStyle: FontStyle.italic,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          // ── Content ────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  _AnimalInformationCard(
                    information: _informationText(context),
                  ),
                  const SizedBox(height: 40),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _CollapsedAnimalTitle extends StatelessWidget {
  final String title;

  const _CollapsedAnimalTitle({required this.title});

  @override
  Widget build(BuildContext context) {
    final settings =
        context.dependOnInheritedWidgetOfExactType<FlexibleSpaceBarSettings>();
    final minExtent = settings?.minExtent ?? kToolbarHeight;
    final maxExtent = settings?.maxExtent ?? minExtent;
    final currentExtent = settings?.currentExtent ?? maxExtent;
    final range = maxExtent - minExtent;
    final expandedProgress = range == 0
        ? 0.0
        : ((currentExtent - minExtent) / range).clamp(0.0, 1.0);

    return Opacity(
      opacity: 1 - expandedProgress,
      child: Text(
        title,
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
        style: const TextStyle(
          color: AppColors.textPrimary,
          fontSize: 20,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  Animal information card
// ─────────────────────────────────────────────
class _AnimalInformationCard extends StatelessWidget {
  final String information;

  const _AnimalInformationCard({required this.information});

  @override
  Widget build(BuildContext context) {
    return Container(
      key: const ValueKey('animal-information-card'),
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFE4EEE6)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.06),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Text(
        information,
        key: const ValueKey('animal-information-text'),
        textAlign: Localizations.localeOf(context).languageCode == 'ar'
            ? TextAlign.right
            : TextAlign.left,
        style: GoogleFonts.cairo(
          height: 1.9,
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: const Color(0xFF34453A),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  Helper widgets
// ─────────────────────────────────────────────
class _LoadingView extends StatelessWidget {
  const _LoadingView();
  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          CircularProgressIndicator(color: Color(0xFF1B6B35), strokeWidth: 3),
          SizedBox(height: 16),
          Text('جاري التحميل...', style: TextStyle(color: Colors.grey)),
        ],
      ),
    );
  }
}

class _EmptyState extends StatelessWidget {
  const _EmptyState();
  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.search_off_rounded, size: 64, color: Colors.grey),
          SizedBox(height: 12),
          Text('لا توجد نتائج',
              style: TextStyle(color: Colors.grey, fontSize: 16)),
        ],
      ),
    );
  }
}
