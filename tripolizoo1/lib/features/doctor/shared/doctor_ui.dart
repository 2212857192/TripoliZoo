import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

/// نظام تصميم احترافي لواجهة الطبيب — متطابق مع هوية المشرف الجديدة.
abstract final class DoctorUi {
  // Palette
  static const Color background   = Colors.white;
  static const Color card         = Color(0xFFFFFFFF);
  static const Color border       = Color(0xFFE4EDE5); // حدود بلون أخضر المريمية الخفيف والراقي
  static const Color borderStrong = Color(0xFFC2D3C5);
  static const Color muted        = Color(0xFF6B7E6F); // نصوص ثانوية هادئة
  static const Color mutedDark    = Color(0xFF455749);

  // Typography
  static const Color textPrimary   = Color(0xFF152A1A); // أخضر غاباتي داكن جداً وراقٍ
  static const Color textSecondary = Color(0xFF3A4F40);

  static const double cardRadius   = 24;
  static const double cardRadiusSm = 16;

  // Shadows
  static List<BoxShadow> get cardShadow => [
        BoxShadow(
          color: const Color(0xFF142E1B).withValues(alpha: 0.05),
          blurRadius: 20,
          offset: const Offset(0, 8),
        ),
      ];

  static List<BoxShadow> get softShadow => [
        BoxShadow(
          color: const Color(0xFF142E1B).withValues(alpha: 0.03),
          blurRadius: 12,
          offset: const Offset(0, 4),
        ),
      ];

  // Card decoration
  static BoxDecoration cardDecoration({Color? color, double? radius}) =>
      BoxDecoration(
        color: color ?? card,
        borderRadius: BorderRadius.circular(radius ?? cardRadius),
        border: Border.all(color: border, width: 1.5),
        boxShadow: cardShadow,
      );

  // Outlined variant
  static BoxDecoration outlinedDecoration({double? radius}) => BoxDecoration(
        color: const Color(0xFFF8FAF8),
        borderRadius: BorderRadius.circular(radius ?? cardRadiusSm),
        border: Border.all(color: border, width: 1.2),
      );
}

// ─────────────────────────────────────────────
// Section header
// ─────────────────────────────────────────────
class DoctorSectionTitle extends StatelessWidget {
  const DoctorSectionTitle({
    super.key,
    required this.eyebrow,
    required this.title,
    this.trailing,
  });

  final String eyebrow;
  final String title;
  final Widget? trailing;

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Container(
          width: 3.5,
          height: 18,
          decoration: BoxDecoration(
            color: AppColors.primary,
            borderRadius: BorderRadius.circular(2),
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            title,
            style: GoogleFonts.cairo(
              fontSize: 15.5,
              fontWeight: FontWeight.w800,
              color: DoctorUi.textPrimary,
              height: 1.1,
            ),
          ),
        ),
        if (trailing != null) trailing!,
      ],
    );
  }
}
