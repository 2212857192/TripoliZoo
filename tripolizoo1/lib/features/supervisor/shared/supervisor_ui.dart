import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

/// نظام تصميم احترافي لواجهة المشرف — مستوى enterprise.
abstract final class SupervisorUi {
  // Palette
  static const Color background   = Color(0xFFF4F7F4); // رمادي مخضر هادئ وراقي جداً
  static const Color card         = Color(0xFFFFFFFF);
  static const Color border       = Color(0xFFE2EBE3); // حدود بلون أخضر المريمية الخفيف
  static const Color borderStrong = Color(0xFFC8D5CB);
  static const Color muted        = Color(0xFF6E8272); // نصوص ثانوية خضراء هادئة
  static const Color mutedDark    = Color(0xFF4A5C50);

  // Typography
  static const Color textPrimary   = Color(0xFF142E1B); // أخضر غاباتي داكن جداً (بديل للأسود القاسي)
  static const Color textSecondary = Color(0xFF354B3B);

  static const double cardRadius   = 20;
  static const double cardRadiusSm = 12;

  // Shadows — خفية جداً وبدون ضجيج بصري
  static List<BoxShadow> get cardShadow => [
        BoxShadow(
          color: const Color(0xFF1A3521).withValues(alpha: 0.04),
          blurRadius: 16,
          offset: const Offset(0, 6),
        ),
      ];

  static List<BoxShadow> get softShadow => [
        BoxShadow(
          color: const Color(0xFF1A3521).withValues(alpha: 0.03),
          blurRadius: 10,
          offset: const Offset(0, 4),
        ),
      ];

  // Card decoration — نظيف ودقيق
  static BoxDecoration cardDecoration({Color? color, double? radius}) =>
      BoxDecoration(
        color: color ?? card,
        borderRadius: BorderRadius.circular(radius ?? cardRadius),
        border: Border.all(color: border, width: 1.5),
        boxShadow: cardShadow,
      );

  // Outlined variant — بدون ظل
  static BoxDecoration outlinedDecoration({double? radius}) => BoxDecoration(
        color: const Color(0xFFF8FAF8),
        borderRadius: BorderRadius.circular(radius ?? cardRadiusSm),
        border: Border.all(color: border, width: 1.2),
      );
}

// ─────────────────────────────────────────────
// Section header — بسيط ومهيمن
// ─────────────────────────────────────────────
class SupervisorSectionTitle extends StatelessWidget {
  const SupervisorSectionTitle({
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
              color: SupervisorUi.textPrimary,
              height: 1.1,
            ),
          ),
        ),
        if (trailing != null) trailing!,
      ],
    );
  }
}

