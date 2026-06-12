import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/widgets/medical_case_status_badge.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/widgets/medical_case_type_badge.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class MedicalCaseCard extends StatelessWidget {
  const MedicalCaseCard({
    super.key,
    required this.medicalCase,
    required this.onViewDetails,
  });

  final MedicalCase medicalCase;
  final VoidCallback onViewDetails;

  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: _border),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            children: [
              Text(
                '#${medicalCase.caseNumber}',
                style: GoogleFonts.cairo(
                  fontSize: 13,
                  fontWeight: FontWeight.w800,
                  color: _muted,
                ),
              ),
              const Spacer(),
              MedicalCaseTypeBadge(type: medicalCase.type),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            medicalCase.animalTitle,
            style: GoogleFonts.cairo(
              fontSize: 17,
              fontWeight: FontWeight.w900,
              color: const Color(0xFF1A1A1A),
              height: 1.2,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            medicalCase.animalGroup,
            style: GoogleFonts.cairo(
              fontSize: 12.5,
              fontWeight: FontWeight.w600,
              color: _muted,
            ),
          ),
          const SizedBox(height: 10),
          Text(
            medicalCase.reasonLine,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: const Color(0xFF374151),
              height: 1.45,
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: Text(
                  'آخر تحديث: ${formatMedicalCaseDateTime(medicalCase.updatedAt)}',
                  style: GoogleFonts.cairo(
                    fontSize: 11.5,
                    fontWeight: FontWeight.w600,
                    color: _muted,
                  ),
                ),
              ),
              MedicalCaseStatusBadge(status: medicalCase.status),
            ],
          ),
          const SizedBox(height: 14),
          Material(
            color: const Color(0xFFF0F4F0),
            borderRadius: BorderRadius.circular(12),
            child: InkWell(
              onTap: onViewDetails,
              borderRadius: BorderRadius.circular(12),
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.symmetric(vertical: 12),
                alignment: Alignment.center,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: _border),
                ),
                child: Text(
                  'عرض التفاصيل',
                  style: GoogleFonts.cairo(
                    fontSize: 13.5,
                    fontWeight: FontWeight.w800,
                    color: AppColors.primaryDark,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
