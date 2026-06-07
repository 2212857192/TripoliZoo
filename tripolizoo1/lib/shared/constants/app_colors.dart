import 'package:flutter/material.dart';

/// Central color palette for Tripoli Zoo.
abstract final class AppColors {
  static const Color primary = Color(0xFF0B5913);
  static const Color primaryDark = Color(0xFF0F3D24);
  static const Color primaryLight = Color(0xFF14532D);
  static const Color accent = Color(0xFFF57C00);
  static const Color accentLight = Color(0xFFFF9800);
  static const Color emerald = Color(0xFF10B981);
  static const Color background = Color(0xFFFFFFFF);
  static const Color surface = Color(0xFFF5F7F5);
  static const Color textPrimary = Color(0xFF1A1A1A);
  static const Color textSecondary = Color(0xFF666666);
  static const Color error = Color(0xFFD32F2F);
  static const Color success = Color(0xFF2E7D32);

  static const LinearGradient primaryGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFF0B5913), Color(0xFF14532D), Color(0xFF0F3D24)],
  );

  static const LinearGradient accentGradient = LinearGradient(
    colors: [Color(0xFFF57C00), Color(0xFFFF9800)],
  );
}
