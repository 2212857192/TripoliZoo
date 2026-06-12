import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class MedicalCaseTypeBadge extends StatelessWidget {
  const MedicalCaseTypeBadge({super.key, required this.type});

  final MedicalCaseType type;

  @override
  Widget build(BuildContext context) {
    final icon = switch (type) {
      MedicalCaseType.field => Icons.location_on_outlined,
      MedicalCaseType.hospital => Icons.local_hospital_outlined,
    };

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: const Color(0xFFE8F5E9),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFC8E6C9)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: AppColors.primaryDark),
          const SizedBox(width: 4),
          Text(
            type.label,
            style: GoogleFonts.cairo(
              fontSize: 11,
              fontWeight: FontWeight.w800,
              color: AppColors.primaryDark,
            ),
          ),
        ],
      ),
    );
  }
}
