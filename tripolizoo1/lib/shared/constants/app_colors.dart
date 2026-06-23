import 'package:flutter/material.dart';

/// Central color palette for Tripoli Zoo.
abstract final class AppColors {
  static const Color primary = Color(0xFF2E7D32);
  static const Color primaryDark = Color(0xFF1B5E20);
  static const Color primaryLight = Color(0xFF4CAF50);
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
    colors: [
      Color(0xFF1A3D1A),
      Color(0xFF2D6A30),
      Color(0xFF3A7D3E),
    ],
    stops: [0.0, 0.6, 1.0],
  );

  static const LinearGradient headerGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFFE8F5E9),
      Color(0xFFF0FDF4),
      Color(0xFFFFFFFF),
    ],
    stops: [0.0, 0.6, 1.0],
  );

  static const LinearGradient accentGradient = LinearGradient(
    colors: [Color(0xFFF57C00), Color(0xFFFF9800)],
  );
}
