import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_dashboard_data.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DietRecommendationsSection extends StatelessWidget {
  const DietRecommendationsSection({
    super.key,
    required this.recommendations,
  });

  final List<DietRecommendation> recommendations;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        const SupervisorSectionTitle(
          eyebrow: 'Nutrition',
          title: 'التوصيات الغذائية العلاجية',
        ),
        const SizedBox(height: 14),
        ...recommendations.map(
          (item) => Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: _DietCard(item: item),
          ),
        ),
      ],
    );
  }
}

// ─────────────────────────────────────────────
class _DietCard extends StatelessWidget {
  const _DietCard({required this.item});

  final DietRecommendation item;

  Color _urgencyColor() {
    if (item.daysRemaining <= 1) return AppColors.primary;
    if (item.daysRemaining <= 3) return const Color(0xFF388E3C);
    return const Color(0xFF4CAF50);
  }

  @override
  Widget build(BuildContext context) {
    final urgColor = _urgencyColor();

    return Container(
      decoration: SupervisorUi.cardDecoration(),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(SupervisorUi.cardRadius),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // ─── Top section ───
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
              child: Row(
                children: [
                  // Icon
                  Container(
                    width: 38,
                    height: 38,
                    decoration: BoxDecoration(
                      color: const Color(0xFFF0F4F0),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: SupervisorUi.border,
                        width: 1,
                      ),
                    ),
                    child: const Icon(
                      Icons.restaurant_outlined,
                      color: AppColors.primary,
                      size: 18,
                    ),
                  ),
                  const SizedBox(width: 12),
                  // Name & ID
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          item.animalName,
                          style: GoogleFonts.cairo(
                            fontSize: 15,
                            fontWeight: FontWeight.w800,
                            color: SupervisorUi.textPrimary,
                            height: 1.1,
                          ),
                        ),
                        Text(
                          item.animalId,
                          style: GoogleFonts.cairo(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: SupervisorUi.muted,
                          ),
                        ),
                      ],
                    ),
                  ),
                  // Days badge
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: urgColor.withValues(alpha: 0.08),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: urgColor.withValues(alpha: 0.25),
                        width: 1,
                      ),
                    ),
                    child: Text(
                      '${item.daysRemaining}ي',
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        fontWeight: FontWeight.w900,
                        color: urgColor,
                        height: 1,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            // ─── Divider ───
            const SizedBox(height: 12),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text(
                item.instruction,
                style: GoogleFonts.cairo(
                  fontSize: 13.5,
                  fontWeight: FontWeight.w700,
                  color: SupervisorUi.textPrimary,
                  height: 1.4,
                ),
              ),
            ),
            const SizedBox(height: 10),
            // ─── Doctor note block ───
            Container(
              margin: const EdgeInsets.fromLTRB(16, 0, 16, 14),
              padding: const EdgeInsets.all(11),
              decoration: BoxDecoration(
                color: const Color(0xFFF7FAF7),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(
                  color: SupervisorUi.border,
                  width: 1,
                ),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(
                    Icons.person_outlined,
                    color: AppColors.primary,
                    size: 15,
                  ),
                  const SizedBox(width: 7),
                  Expanded(
                    child: Text.rich(
                      TextSpan(
                        style: GoogleFonts.cairo(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: SupervisorUi.muted,
                          height: 1.5,
                        ),
                        children: [
                          TextSpan(
                            text: 'ملاحظة الطبيب: ',
                            style: GoogleFonts.cairo(
                              fontSize: 12,
                              fontWeight: FontWeight.w800,
                              color: AppColors.primary,
                            ),
                          ),
                          TextSpan(text: item.doctorNote),
                        ],
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
}
