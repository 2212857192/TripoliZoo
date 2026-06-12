import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/domain/health_report.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class HealthReportStatusBadge extends StatelessWidget {
  const HealthReportStatusBadge({super.key, required this.status});

  final HealthReportStatus status;

  @override
  Widget build(BuildContext context) {
    final (bg, fg) = switch (status) {
      HealthReportStatus.sent => (
          const Color(0xFFFFF7ED),
          const Color(0xFFEA580C),
        ),
      HealthReportStatus.received => (
          const Color(0xFFE8F5E9),
          AppColors.primaryDark,
        ),
      HealthReportStatus.closed => (
          const Color(0xFFF3F4F6),
          const Color(0xFF6B7280),
        ),
    };

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        status.label,
        style: GoogleFonts.cairo(
          fontSize: 11.5,
          fontWeight: FontWeight.w800,
          color: fg,
          height: 1.1,
        ),
      ),
    );
  }
}
