import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';

class MedicalCaseStatusBadge extends StatelessWidget {
  const MedicalCaseStatusBadge({super.key, required this.status});

  final MedicalCaseStatus status;

  @override
  Widget build(BuildContext context) {
    final (bg, fg) = switch (status) {
      MedicalCaseStatus.active => (
          const Color(0xFFE3F2FD),
          const Color(0xFF1565C0),
        ),
      MedicalCaseStatus.closed => (
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
        ),
      ),
    );
  }
}
