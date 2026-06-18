import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/animal.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';

class AnimalDetailScreen extends StatelessWidget {
  const AnimalDetailScreen({super.key, required this.animal});

  final Animal animal;

  String _categoryLabel(BuildContext context, String category) {
    return switch (category) {
      'predators' => context.localized(ar: 'مفترس', en: 'Predator'),
      'birds' => context.localized(ar: 'طائر', en: 'Bird'),
      'mammals' => context.localized(ar: 'ثديي', en: 'Mammal'),
      'reptiles' => context.localized(ar: 'زاحف', en: 'Reptile'),
      _ => context.localized(ar: 'حيوان', en: 'Animal'),
    };
  }

  void _openOnMap(BuildContext context) {
    if (!animal.hasMapLocation) return;
    context.push('/map?focus=${animal.mapLocationId}');
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: const Color(0xFFF8FAFC),
        body: SingleChildScrollView(
          child: Column(
            children: [
              // ── Image & Header Section ──────────────────────────────────
              SizedBox(
                height: MediaQuery.of(context).size.height * 0.48,
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    _AnimalCoverImage(animal: animal),
                    
                    // Gradient overlay
                    Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [
                            Colors.transparent,
                            Colors.black.withValues(alpha: 0.15),
                            Colors.black.withValues(alpha: 0.85),
                          ],
                          stops: const [0.4, 0.7, 1.0],
                        ),
                      ),
                    ),
                    
                    // Back Button
                    Positioned(
                      top: topPad + 12,
                      right: 16,
                      child: CircleAvatar(
                        backgroundColor: Colors.white.withValues(alpha: 0.95),
                        radius: 20,
                        child: IconButton(
                          padding: const EdgeInsets.only(left: 4), // center the iOS back arrow visually
                          icon: const Icon(
                            Icons.arrow_forward_ios_rounded,
                            size: 18,
                            color: Color(0xFF1E293B),
                          ),
                          onPressed: () {
                            if (context.canPop()) {
                              context.pop();
                            } else {
                              context.go('/home');
                            }
                          },
                        ),
                      ),
                    ),
                    
                    // Titles & Category
                    Positioned(
                      bottom: 40, // leaving space for the card to overlap slightly if we want, or just spacing
                      right: 20,
                      left: 20,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            decoration: BoxDecoration(
                              color: const Color(0xFFDC2626),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              _categoryLabel(context, animal.category),
                              style: GoogleFonts.cairo(
                                color: Colors.white,
                                fontWeight: FontWeight.w800,
                                fontSize: 11,
                              ),
                            ),
                          ),
                          const SizedBox(height: 10),
                          Text(
                            animal.name,
                            style: GoogleFonts.cairo(
                              color: Colors.white,
                              fontSize: 34,
                              fontWeight: FontWeight.w900,
                              height: 1.1,
                            ),
                          ),
                          if (animal.sciName.isNotEmpty) ...[
                            const SizedBox(height: 2),
                            Text(
                              animal.sciName,
                              style: GoogleFonts.cairo(
                                color: Colors.white.withValues(alpha: 0.85),
                                fontSize: 16,
                                fontStyle: FontStyle.italic,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              // ── Details Card ──────────────────────────────────────────
              Transform.translate(
                offset: const Offset(0, -20),
                child: Container(
                  margin: const EdgeInsets.symmetric(horizontal: 16),
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(24),
                    border: Border.all(color: const Color(0xFFF1F5F9), width: 1.5),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.04),
                        blurRadius: 24,
                        offset: const Offset(0, 12),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Description
                      if (animal.desc.isNotEmpty) ...[
                        Text(
                          animal.desc,
                          style: GoogleFonts.cairo(
                            fontSize: 15,
                            color: const Color(0xFF334155),
                            height: 1.9,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ],

                    ],
                  ),
                ),
              ),
              const SizedBox(height: 32), // bottom padding
            ],
          ),
        ),
      ),
    );
  }
}

class _AnimalCoverImage extends StatelessWidget {
  const _AnimalCoverImage({
    required this.animal,
    this.iconSize = 80,
  });

  final Animal animal;
  final double iconSize;

  @override
  Widget build(BuildContext context) {
    Widget fallback() => Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Color(0xFF1E3A1E), Color(0xFF2D5A27), Color(0xFF4A8F40)],
            ),
          ),
          child: Center(
            child: Icon(Icons.pets, color: Colors.white30, size: iconSize),
          ),
        );

    if (animal.image.isEmpty) return fallback();

    if (animal.hasNetworkImage) {
      return Image.network(
        animal.image,
        fit: BoxFit.cover,
        errorBuilder: (_, __, ___) => fallback(),
      );
    }

    return Image.asset(
      animal.image,
      fit: BoxFit.cover,
      errorBuilder: (_, __, ___) => fallback(),
    );
  }
}
