import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/animal.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/animal_repository.dart';

// ─────────────────────────────────────────────
//  Category model with icon + colour
// ─────────────────────────────────────────────
class _Category {
  final String id;
  final String label;
  final IconData icon;
  final Color color;
  const _Category(this.id, this.label, this.icon, this.color);
}

const _categories = [
  _Category('all',       'الكل',      Icons.pets,                  Color(0xFF0F3D24)),
  _Category('predators', 'مفترسات',   Icons.crisis_alert_rounded,  Color(0xFFB71C1C)),
  _Category('mammals',   'ثدييات',    Icons.nature_people_rounded,  Color(0xFF4527A0)),
  _Category('birds',     'طيور',      Icons.flutter_dash,           Color(0xFF01579B)),
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
    super.dispose();
  }

  Future<void> _load() async {
    final data = await _repo.getAll();
    if (mounted) {
      setState(() { _animals = data; _loading = false; });
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
    return Scaffold(
      backgroundColor: const Color(0xFFF0F4F2),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F3D24),
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 20),
          onPressed: () => context.pop(),
        ),
        title: const Text(
          'مستكشف الحيوانات',
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18),
        ),
        centerTitle: true,
      ),
      body: _loading
          ? const _LoadingView()
          : FadeTransition(
              opacity: _fadeAnim,
              child: CustomScrollView(
                slivers: [
                  _buildSearchBar(),
                  _buildCategoryRow(),
                  _buildGrid(),
                ],
              ),
            ),
    );
  }

  // ── Search bar ────────────────────────────────────
  Widget _buildSearchBar() {
    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(20, 20, 20, 0),
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(18),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.06),
                blurRadius: 12,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: TextField(
            textAlign: TextAlign.right,
            onChanged: (v) => setState(() => _search = v),
            decoration: InputDecoration(
              hintText: 'ابحث عن حيوان...',
              hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 14),
              prefixIcon: const Icon(Icons.search_rounded, color: Color(0xFF1B6B35)),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            ),
          ),
        ),
      ),
    );
  }

  // ── Category chips ────────────────────────────────
  Widget _buildCategoryRow() {
    return SliverToBoxAdapter(
      child: SizedBox(
        height: 64,
        child: ListView.builder(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
          itemCount: _categories.length,
          itemBuilder: (_, i) {
            final cat = _categories[i];
            final active = _category == cat.id;
            return GestureDetector(
              onTap: () => setState(() => _category = cat.id),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 220),
                curve: Curves.easeInOut,
                margin: const EdgeInsets.only(left: 10),
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                decoration: BoxDecoration(
                  color: active ? cat.color : Colors.white,
                  borderRadius: BorderRadius.circular(24),
                  boxShadow: [
                    BoxShadow(
                      color: active
                          ? cat.color.withValues(alpha: 0.35)
                          : Colors.black.withValues(alpha: 0.04),
                      blurRadius: active ? 10 : 4,
                      offset: const Offset(0, 3),
                    ),
                  ],
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      cat.label,
                      style: TextStyle(
                        color: active ? Colors.white : Colors.grey.shade600,
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                      ),
                    ),
                    const SizedBox(width: 6),
                    Icon(cat.icon, size: 16,
                        color: active ? Colors.white : Colors.grey.shade500),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  // ── Animal grid ───────────────────────────────────
  Widget _buildGrid() {
    final items = _filtered;
    if (items.isEmpty) {
      return const SliverFillRemaining(child: _EmptyState());
    }
    return SliverPadding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
      sliver: SliverGrid(
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 14,
          crossAxisSpacing: 14,
          childAspectRatio: 0.72,
        ),
        delegate: SliverChildBuilderDelegate(
          (ctx, i) => _AnimalCard(animal: items[i], index: i),
          childCount: items.length,
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  Animal card
// ─────────────────────────────────────────────
class _AnimalCard extends StatefulWidget {
  final Animal animal;
  final int index;
  const _AnimalCard({required this.animal, required this.index});

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
    _ctrl = AnimationController(vsync: this, duration: const Duration(milliseconds: 180));
    _scaleAnim = Tween<double>(begin: 1, end: 0.96)
        .animate(CurvedAnimation(parent: _ctrl, curve: Curves.easeInOut));
  }

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  // Category accent colour
  Color get _accentColor {
    switch (widget.animal.category) {
      case 'predators': return const Color(0xFFB71C1C);
      case 'birds':     return const Color(0xFF01579B);
      case 'mammals':   return const Color(0xFF4527A0);
      default:          return const Color(0xFF0F3D24);
    }
  }

  String get _categoryLabel {
    switch (widget.animal.category) {
      case 'predators': return 'مفترس';
      case 'birds':     return 'طائر';
      case 'mammals':   return 'ثديي';
      default:          return '';
    }
  }

  @override
  Widget build(BuildContext context) {
    return ScaleTransition(
      scale: _scaleAnim,
      child: GestureDetector(
        onTapDown: (_) => _ctrl.forward(),
        onTapUp: (_) {
          _ctrl.reverse();
          Navigator.push(
            context,
            PageRouteBuilder(
              pageBuilder: (_, anim, __) => FadeTransition(
                opacity: anim,
                child: AnimalDetailScreen(animal: widget.animal),
              ),
              transitionDuration: const Duration(milliseconds: 400),
            ),
          );
        },
        onTapCancel: () => _ctrl.reverse(),
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.12),
                blurRadius: 16,
                offset: const Offset(0, 6),
              ),
            ],
          ),
          child: ClipRRect(
              borderRadius: BorderRadius.circular(24),
              child: Stack(
                fit: StackFit.expand,
                children: [
                  // Animal image
                  Image.asset(
                    widget.animal.image,
                    fit: BoxFit.cover,
                    cacheWidth: 400,
                    errorBuilder: (_, __, ___) => Container(
                      color: const Color(0xFF1B3A2A),
                      child: const Icon(Icons.pets, color: Colors.white30, size: 48),
                    ),
                  ),

                  // Gradient overlay
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        stops: const [0.3, 1.0],
                        colors: [
                          Colors.transparent,
                          Colors.black.withValues(alpha: 0.82),
                        ],
                      ),
                    ),
                  ),

                  // Category badge (top left)
                  Positioned(
                    top: 12,
                    left: 12,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: _accentColor,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Text(
                        _categoryLabel,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),

                  // Name + location (bottom)
                  Positioned(
                    left: 12,
                    right: 12,
                    bottom: 14,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          widget.animal.name,
                          textAlign: TextAlign.right,
                          style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                            shadows: [Shadow(blurRadius: 6, color: Colors.black45)],
                          ),
                        ),
                        const SizedBox(height: 4),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            Text(
                              widget.animal.location,
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.75),
                                fontSize: 11,
                              ),
                            ),
                            const SizedBox(width: 4),
                            const Icon(Icons.location_on_rounded,
                                color: Color(0xFF4CAF50), size: 13),
                          ],
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

  Color get _categoryColor {
    switch (animal.category) {
      case 'predators': return const Color(0xFFB71C1C);
      case 'birds':     return const Color(0xFF01579B);
      case 'mammals':   return const Color(0xFF4527A0);
      default:          return const Color(0xFF0F3D24);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF0F4F2),
      body: CustomScrollView(
        slivers: [
          // ── Hero image header ──────────────────────────
          SliverAppBar(
            expandedHeight: 340,
            pinned: true,
            stretch: true,
            backgroundColor: const Color(0xFF0A3018),
            systemOverlayStyle: SystemUiOverlayStyle.light,
            automaticallyImplyLeading: false,
            leading: Padding(
              padding: const EdgeInsets.all(8),
              child: CircleAvatar(
                backgroundColor: const Color(0xFFF57C00),
                child: IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new_rounded,
                      color: Colors.white, size: 18),
                  onPressed: () => Navigator.pop(context),
                ),
              ),
            ),
            flexibleSpace: FlexibleSpaceBar(
              stretchModes: const [StretchMode.zoomBackground, StretchMode.blurBackground],
              background: Stack(
                  fit: StackFit.expand,
                  children: [
                    Image.asset(
                      animal.image,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => Container(
                        color: const Color(0xFF1B3A2A),
                        child: const Icon(Icons.pets, color: Colors.white30, size: 80),
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
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                            decoration: BoxDecoration(
                              color: _categoryColor,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              animal.category == 'predators' ? 'مفترس'
                                  : animal.category == 'birds' ? 'طائر'
                                  : animal.category == 'mammals' ? 'ثديي' : '',
                              style: const TextStyle(color: Colors.white, fontSize: 11,
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
                              shadows: [Shadow(blurRadius: 8, color: Colors.black54)],
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
                  // Stats row
                  if (animal.stats.isNotEmpty)
                    _StatsCard(stats: animal.stats),
                  const SizedBox(height: 20),

                  // About
                  _InfoSection(
                    title: 'نبذة عن الحيوان',
                    body: animal.desc,
                    icon: Icons.info_outline_rounded,
                  ),

                  // Habitat
                  _InfoSection(
                    title: 'الموطن الطبيعي',
                    body: animal.habitat,
                    icon: Icons.forest_rounded,
                  ),

                  // Facts
                  if (animal.facts.isNotEmpty) ...[
                    const _SectionTitle(
                      title: 'حقائق مدهشة',
                      icon: Icons.auto_awesome_rounded,
                    ),
                    const SizedBox(height: 12),
                    ...animal.facts.asMap().entries.map((e) =>
                        _FactTile(fact: e.value, index: e.key)),
                    const SizedBox(height: 8),
                  ],

                  // Location button
                  const SizedBox(height: 8),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: () => context.push('/map'),
                      icon: const Icon(Icons.location_on_rounded),
                      label: const Text('عرض موقعه على الخريطة'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF0F3D24),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16)),
                        elevation: 0,
                        textStyle: const TextStyle(fontSize: 15,
                            fontWeight: FontWeight.bold),
                      ),
                    ),
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

// ─────────────────────────────────────────────
//  Stats card
// ─────────────────────────────────────────────
class _StatsCard extends StatelessWidget {
  final Map<String, String> stats;
  const _StatsCard({required this.stats});

  static const _green = Color(0xFF0F3D24);

  IconData _iconFor(String label) {
    if (label.contains('عمر') || label.contains('سنة')) return Icons.cake_rounded;
    if (label.contains('وزن') || label.contains('كجم')) return Icons.monitor_weight_rounded;
    if (label.contains('غذاء') || label.contains('لحوم') || label.contains('أعشاب')) return Icons.restaurant_rounded;
    if (label.contains('طول') || label.contains('حجم')) return Icons.straighten_rounded;
    if (label.contains('سرعة')) return Icons.speed_rounded;
    return Icons.info_rounded;
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.06),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: stats.entries.map((e) => _StatItem(
          label: e.key,
          value: e.value,
          icon: _iconFor(e.key),
        )).toList(),
      ),
    );
  }
}

class _StatItem extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  const _StatItem({required this.label, required this.value, required this.icon});

  static const _green = Color(0xFF0F3D24);

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: const Color(0xFFE8F5E9),
            borderRadius: BorderRadius.circular(14),
          ),
          child: Icon(icon, color: _green, size: 20),
        ),
        const SizedBox(height: 8),
        Text(value,
            style: const TextStyle(
                fontSize: 15, fontWeight: FontWeight.bold, color: _green)),
        const SizedBox(height: 2),
        Text(label,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 11, color: Colors.grey)),
      ],
    );
  }
}

// ─────────────────────────────────────────────
//  Info section
// ─────────────────────────────────────────────
class _InfoSection extends StatelessWidget {
  final String title;
  final String body;
  final IconData icon;
  const _InfoSection({required this.title, required this.body, required this.icon});

  static const _green = Color(0xFF0F3D24);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.start,
            children: [
              Icon(icon, color: _green, size: 20),
              const SizedBox(width: 8),
              Text(title,
                  style: const TextStyle(
                      fontSize: 17, fontWeight: FontWeight.bold, color: _green)),
            ],
          ),
          const SizedBox(height: 10),
          Text(body,
              textAlign: TextAlign.right,
              style: const TextStyle(
                  height: 1.8, fontSize: 14, color: Color(0xFF444444))),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  Section title
// ─────────────────────────────────────────────
class _SectionTitle extends StatelessWidget {
  final String title;
  final IconData icon;
  const _SectionTitle({required this.title, required this.icon});

  static const _green = Color(0xFF0F3D24);

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.start,
      children: [
        Icon(icon, color: _green, size: 22),
        const SizedBox(width: 8),
        Text(title,
            style: const TextStyle(
                fontSize: 18, fontWeight: FontWeight.bold, color: _green)),
      ],
    );
  }
}

// ─────────────────────────────────────────────
//  Fact tile
// ─────────────────────────────────────────────
class _FactTile extends StatelessWidget {
  final String fact;
  final int index;
  const _FactTile({required this.fact, required this.index});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FFF9),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFD0EDD8)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Text(fact,
                textAlign: TextAlign.right,
                style: const TextStyle(
                    fontSize: 14, height: 1.6, color: Color(0xFF2D4A35))),
          ),
          const SizedBox(width: 10),
          Container(
            width: 26,
            height: 26,
            decoration: const BoxDecoration(
              color: Color(0xFF0F3D24),
              shape: BoxShape.circle,
            ),
            child: Center(
              child: const Icon(Icons.star_rounded, color: Colors.amber, size: 14),
            ),
          ),
        ],
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
          Text('جاري تحميل الحيوانات...', style: TextStyle(color: Colors.grey)),
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
          Text('لا توجد نتائج', style: TextStyle(color: Colors.grey, fontSize: 16)),
        ],
      ),
    );
  }
}
