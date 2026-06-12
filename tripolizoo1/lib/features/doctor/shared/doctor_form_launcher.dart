import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/forms/open_field_case_form_sheet.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

/// يفتح نماذج الطبيب كـ bottom sheet فوق الصفحة الحالية.
Future<String?> openDoctorFieldCaseForm(BuildContext context) {
  return OpenFieldCaseFormSheet.show(context);
}

void showDoctorSuccessSnackBar(
  BuildContext context, {
  required String message,
}) {
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      content: Text(
        message,
        style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
      ),
      backgroundColor: AppColors.primary,
    ),
  );
}
