import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_type.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/forms/birth_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/forms/health_case_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/forms/mortality_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/forms/operational_note_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/forms/health_report_form_sheet.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

/// يفتح نموذج التسجيل كـ bottom sheet فوق الصفحة الحالية.
Future<bool?> openSupervisorForm(
  BuildContext context,
  SupervisorFormType type,
) {
  return switch (type) {
    SupervisorFormType.health => HealthCaseFormSheet.show(context),
    SupervisorFormType.birth => BirthFormSheet.show(context),
    SupervisorFormType.mortality => MortalityFormSheet.show(context),
    SupervisorFormType.operationalNote =>
      OperationalNoteFormSheet.show(context),
    SupervisorFormType.urgentHealth =>
      HealthReportFormSheet.show(context, urgent: true),
  };
}

void showSupervisorSuccessSnackBar(
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
