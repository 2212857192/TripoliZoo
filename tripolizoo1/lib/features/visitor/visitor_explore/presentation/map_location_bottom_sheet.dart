import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/visitor_map_repository.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';

class MapLocationBottomSheet extends StatelessWidget {
  const MapLocationBottomSheet({
    super.key,
    required this.location,
    required this.onClose,
    required this.onShowRoute,
    this.isNavigating = false,
  });

  final VisitorMapLocation location;
  final VoidCallback onClose;
  final VoidCallback onShowRoute;
  final bool isNavigating;

  String get _displayName =>
      location.animalName?.isNotEmpty == true ? location.animalName! : location.name;

  @override
  Widget build(BuildContext context) {
    if (isNavigating) {
      return _NavigationBar(
        title: _displayName,
        onStop: onClose,
      );
    }

    final hasPhoto =
        location.animalPhotoUrl != null && location.animalPhotoUrl!.isNotEmpty;

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Material(
        color: Colors.white,
        elevation: 16,
        shadowColor: Colors.black.withValues(alpha: 0.22),
        borderRadius: BorderRadius.circular(4),
        clipBehavior: Clip.antiAlias,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Stack(
              children: [
                AspectRatio(
                  aspectRatio: 16 / 9,
                  child: hasPhoto
                      ? Image.network(
                          location.animalPhotoUrl!,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) => _fallbackHero(),
                        )
                      : _fallbackHero(),
                ),
                Positioned(
                  top: 10,
                  right: 10,
                  child: Material(
                    color: Colors.white.withValues(alpha: 0.94),
                    shape: const CircleBorder(),
                    elevation: 4,
                    child: InkWell(
                      customBorder: const CircleBorder(),
                      onTap: onClose,
                      child: const SizedBox(
                        width: 36,
                        height: 36,
                        child: Icon(
                          Icons.close_rounded,
                          size: 22,
                          color: Color(0xFF374151),
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 18, 20, 20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Text(
                    _displayName,
                    textAlign: TextAlign.center,
                    style: GoogleFonts.cairo(
                      fontSize: 24,
                      fontWeight: FontWeight.w900,
                      color: const Color(0xFF111827),
                    ),
                  ),
                  if (location.description.isNotEmpty) ...[
                    const SizedBox(height: 8),
                    Text(
                      location.description,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      textAlign: TextAlign.center,
                      style: GoogleFonts.cairo(
                        fontSize: 13,
                        height: 1.6,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF64748B),
                      ),
                    ),
                  ],
                  const SizedBox(height: 16),
                  const Divider(height: 1, color: Color(0xFFE5E7EB)),
                  const SizedBox(height: 16),
                  FilledButton.icon(
                    key: const ValueKey('map-show-route-button'),
                    onPressed: onShowRoute,
                    style: FilledButton.styleFrom(
                      backgroundColor: const Color(0xFF1B4332),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                    icon: const Icon(Icons.directions_rounded, size: 20),
                    label: Text(
                      context.localized(
                        ar: 'أظهر الطريق',
                        en: 'Show the way',
                      ),
                      style: GoogleFonts.cairo(
                        fontSize: 15,
                        fontWeight: FontWeight.w900,
                      ),
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

  Widget _fallbackHero() {
    return Container(
      color: const Color(0xFFE8F3E6),
      child: const Center(
        child: Icon(Icons.pets_rounded, size: 56, color: Color(0xFF2D5A27)),
      ),
    );
  }
}

class _NavigationBar extends StatelessWidget {
  const _NavigationBar({
    required this.title,
    required this.onStop,
  });

  final String title;
  final VoidCallback onStop;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 0, 16, 16),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.14),
            blurRadius: 24,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: const Color(0xFFE8F3E6),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(
              Icons.navigation_rounded,
              color: Color(0xFF1B4332),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  context.localized(ar: 'المسار مفعّل', en: 'Route active'),
                  style: GoogleFonts.cairo(
                    fontSize: 12,
                    fontWeight: FontWeight.w800,
                    color: const Color(0xFF64748B),
                  ),
                ),
                Text(
                  title,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.cairo(
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                    color: const Color(0xFF111827),
                  ),
                ),
              ],
            ),
          ),
          TextButton(
            onPressed: onStop,
            child: Text(
              context.localized(ar: 'إيقاف', en: 'Stop'),
              style: GoogleFonts.cairo(
                fontWeight: FontWeight.w900,
                color: const Color(0xFFDC2626),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
