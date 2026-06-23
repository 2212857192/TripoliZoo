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
    this.routeDistanceMeters,
    this.routeWalkMinutes,
  });

  final VisitorMapLocation location;
  final VoidCallback onClose;
  final VoidCallback onShowRoute;
  final bool isNavigating;
  final int? routeDistanceMeters;
  final int? routeWalkMinutes;

  String get _displayName =>
      location.animalName?.isNotEmpty == true ? location.animalName! : location.name;

  Color get _categoryColor => switch (location.category) {
        'dining' => const Color(0xFFB45309),
        'service' => const Color(0xFF1D4ED8),
        _ => const Color(0xFF1B4332),
      };

  IconData get _categoryIcon => switch (location.category) {
        'dining' => Icons.restaurant_rounded,
        'service' => Icons.wc_rounded,
        _ => Icons.pets_rounded,
      };

  @override
  Widget build(BuildContext context) {
    if (isNavigating) {
      return _NavigationBar(
        title: _displayName,
        distanceMeters: routeDistanceMeters,
        walkMinutes: routeWalkMinutes,
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
        borderRadius: BorderRadius.circular(20),
        clipBehavior: Clip.antiAlias,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 16),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              // ── Thumbnail / icon ──────────────────────────────────────
              ClipRRect(
                borderRadius: BorderRadius.circular(12),
                child: SizedBox(
                  width: 56,
                  height: 56,
                  child: hasPhoto
                      ? Image.network(
                          location.animalPhotoUrl!,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) =>
                              _iconBox(_categoryColor, _categoryIcon),
                        )
                      : _iconBox(_categoryColor, _categoryIcon),
                ),
              ),
              const SizedBox(width: 12),

              // ── Name + description ────────────────────────────────────
              Expanded(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _displayName,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: GoogleFonts.cairo(
                        fontSize: 16,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xFF111827),
                      ),
                    ),
                    if (location.description.isNotEmpty) ...[
                      const SizedBox(height: 2),
                      Text(
                        location.description,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: GoogleFonts.cairo(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: const Color(0xFF6B7280),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
              const SizedBox(width: 10),

              // ── Buttons ───────────────────────────────────────────────
              Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  FilledButton.icon(
                    key: const ValueKey('map-show-route-button'),
                    onPressed: onShowRoute,
                    style: FilledButton.styleFrom(
                      backgroundColor: _categoryColor,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 10),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      minimumSize: Size.zero,
                      tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                    ),
                    icon: const Icon(Icons.directions_rounded, size: 18),
                    label: Text(
                      context.localized(ar: 'ابدأ التنقل', en: 'Navigate'),
                      style: GoogleFonts.cairo(
                        fontSize: 13,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                  ),
                  const SizedBox(height: 6),
                  GestureDetector(
                    onTap: onClose,
                    child: Text(
                      context.localized(ar: 'إغلاق', en: 'Close'),
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        color: const Color(0xFF9CA3AF),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _iconBox(Color color, IconData icon) {
    return Container(
      color: color.withValues(alpha: 0.12),
      child: Center(
        child: Icon(icon, color: color, size: 28),
      ),
    );
  }
}

class _NavigationBar extends StatelessWidget {
  const _NavigationBar({
    required this.title,
    required this.onStop,
    this.distanceMeters,
    this.walkMinutes,
  });

  final String title;
  final VoidCallback onStop;
  final int? distanceMeters;
  final int? walkMinutes;

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
                if (distanceMeters != null && walkMinutes != null)
                  Text(
                    context.localized(
                      ar: 'حوالي $distanceMeters م · $walkMinutes د',
                      en: 'About $distanceMeters m · $walkMinutes min',
                    ),
                    style: GoogleFonts.cairo(
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF64748B),
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
