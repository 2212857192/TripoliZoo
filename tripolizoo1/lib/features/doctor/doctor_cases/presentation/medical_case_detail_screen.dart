import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/widgets/medical_case_status_badge.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/widgets/medical_case_type_badge.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class MedicalCaseDetailScreen extends StatelessWidget {
  const MedicalCaseDetailScreen({super.key, required this.caseId});

  final String caseId;

  @override
  Widget build(BuildContext context) {
    final medicalCase = context.watch<MedicalCasesProvider>().findById(caseId);
    final topPad = MediaQuery.of(context).padding.top;

    if (medicalCase == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('تفاصيل الحالة')),
        body: const Center(child: Text('الحالة غير موجودة')),
      );
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      body: SafeArea(
        top: false,
        bottom: true,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(
              child: AnnotatedRegion<SystemUiOverlayStyle>(
                value: SystemUiOverlayStyle.light,
                child: Container(
                  color: AppColors.primaryDark,
                  padding: EdgeInsets.fromLTRB(8, topPad + 4, 16, 16),
                  child: Row(
                    children: [
                      IconButton(
                        onPressed: () => context.pop(),
                        icon: const Icon(
                          Icons.arrow_forward_ios_rounded,
                          color: Colors.white,
                          size: 18,
                        ),
                      ),
                      Expanded(
                        child: Text(
                          'تفاصيل الحالة',
                          style: GoogleFonts.cairo(
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            SliverToBoxAdapter(
              child: Container(
                color: Colors.white,
                padding: const EdgeInsets.fromLTRB(20, 14, 20, 14),
                child: Row(
                  children: [
                    Text(
                      '#${medicalCase.caseNumber}',
                      style: GoogleFonts.cairo(
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                        color: DoctorUi.muted,
                      ),
                    ),
                    const Spacer(),
                    MedicalCaseTypeBadge(type: medicalCase.type),
                  ],
                ),
              ),
            ),
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
              sliver: SliverList(
                delegate: SliverChildListDelegate([
                  _SectionCard(
                    title: 'بيانات الحالة',
                    children: [
                      _DetailRow(
                        label: 'رقم الحالة',
                        value: medicalCase.caseNumber,
                      ),
                      _DetailRow(
                        label: 'نوع الحالة',
                        value: medicalCase.type.detailLabel,
                      ),
                      _DetailRow(
                        label: 'حالة الحالة',
                        valueWidget: MedicalCaseStatusBadge(
                          status: medicalCase.status,
                        ),
                      ),
                      _DetailRow(
                        label: 'تاريخ فتح الحالة',
                        value: formatMedicalCaseDateTime(medicalCase.openedAt),
                      ),
                      _DetailRow(
                        label: 'سبب فتح الحالة',
                        value: medicalCase.openReason,
                      ),
                      _DetailRow(
                        label: 'مصدر الحالة',
                        value: medicalCase.sourceLabel,
                      ),
                      if (medicalCase.initialNote != null)
                        _DetailRow(
                          label: 'ملاحظة أولية',
                          value: medicalCase.initialNote!,
                        ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  _SectionCard(
                    title: 'بيانات الحيوان',
                    children: [
                      _DetailRow(
                        label: 'رقم الحيوان',
                        value: medicalCase.animalId,
                      ),
                      _DetailRow(
                        label: 'نوع الحيوان',
                        value: medicalCase.animalType,
                      ),
                      _DetailRow(
                        label: 'المجموعة',
                        value: medicalCase.animalGroup,
                      ),
                      if (medicalCase.gender != null)
                        _DetailRow(
                          label: 'الجنس',
                          value: medicalCase.gender!,
                        ),
                      if (medicalCase.age != null)
                        _DetailRow(
                          label: 'العمر',
                          value: medicalCase.age!,
                        ),
                    ],
                  ),
                  if (medicalCase.procedures.isNotEmpty) ...[
                    const SizedBox(height: 12),
                    _SectionCard(
                      title: 'الإجراءات الطبية المسجلة',
                      children: [
                        for (final procedure in medicalCase.procedures)
                          _ProcedureBlock(procedure: procedure),
                      ],
                    ),
                  ],
                ]),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ProcedureBlock extends StatelessWidget {
  const _ProcedureBlock({required this.procedure});

  final MedicalProcedure procedure;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFF4F7F4),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: DoctorUi.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Align(
            alignment: AlignmentDirectional.centerStart,
            child: Text(
              formatMedicalCaseDateTime(procedure.recordedAt),
              style: GoogleFonts.cairo(
                fontSize: 11.5,
                fontWeight: FontWeight.w700,
                color: DoctorUi.muted,
              ),
            ),
          ),
          const SizedBox(height: 8),
          _DetailRow(label: 'التشخيص', value: procedure.diagnosis),
          _DetailRow(label: 'العلاج', value: procedure.treatment),
          if (procedure.note != null)
            _DetailRow(label: 'ملاحظة', value: procedure.note!),
        ],
      ),
    );
  }
}

class _SectionCard extends StatelessWidget {
  const _SectionCard({required this.title, required this.children});

  final String title;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE5E7EB)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            title,
            style: GoogleFonts.cairo(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: AppColors.primaryDark,
            ),
          ),
          const SizedBox(height: 12),
          ...children,
        ],
      ),
    );
  }
}

class _DetailRow extends StatelessWidget {
  const _DetailRow({
    required this.label,
    this.value,
    this.valueWidget,
  });

  final String label;
  final String? value;
  final Widget? valueWidget;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Align(
              alignment: AlignmentDirectional.centerStart,
              child: valueWidget ??
                  Text(
                    value ?? '',
                    textAlign: TextAlign.start,
                    style: GoogleFonts.cairo(
                      fontSize: 13.5,
                      fontWeight: FontWeight.w700,
                      color: DoctorUi.textPrimary,
                      height: 1.35,
                    ),
                  ),
            ),
          ),
          const SizedBox(width: 12),
          Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: DoctorUi.muted,
            ),
          ),
        ],
      ),
    );
  }
}
