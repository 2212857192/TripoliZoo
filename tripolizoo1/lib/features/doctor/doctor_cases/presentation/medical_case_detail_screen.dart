import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/forms/close_field_case_sheet.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/forms/register_medical_procedure_sheet.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/widgets/medical_case_status_badge.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/widgets/medical_case_type_badge.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_form_launcher.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class MedicalCaseDetailScreen extends StatefulWidget {
  const MedicalCaseDetailScreen({super.key, required this.caseId});

  final String caseId;

  @override
  State<MedicalCaseDetailScreen> createState() =>
      _MedicalCaseDetailScreenState();
}

class _MedicalCaseDetailScreenState extends State<MedicalCaseDetailScreen> {
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadCase());
  }

  Future<void> _loadCase() async {
    await context.read<MedicalCasesProvider>().fetchCase(widget.caseId);
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _openProcedureForm(MedicalCase medicalCase) async {
    final saved = await RegisterMedicalProcedureSheet.show(
      context,
      caseId: medicalCase.id,
      isHospitalCase: medicalCase.type == MedicalCaseType.hospital,
    );
    if (!mounted || saved != true) return;
    showDoctorSuccessSnackBar(context, message: 'تم تسجيل الإجراء الطبي');
    await _loadCase();
  }

  Future<void> _openCloseForm(MedicalCase medicalCase) async {
    final saved = await CloseFieldCaseSheet.show(context, caseId: medicalCase.id);
    if (!mounted || saved != true) return;
    showDoctorSuccessSnackBar(context, message: 'تم إغلاق الحالة الميدانية');
    await _loadCase();
  }

  @override
  Widget build(BuildContext context) {
    final medicalCase = context.watch<MedicalCasesProvider>().findById(widget.caseId);
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    if (_loading) {
      return const Scaffold(
        body: Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
      );
    }

    if (medicalCase == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('تفاصيل الحالة')),
        body: const Center(child: Text('الحالة غير موجودة')),
      );
    }

    final showActions =
        medicalCase.canRegisterProcedure || medicalCase.canClose;

    return Scaffold(
      backgroundColor: DoctorUi.background,
      body: SafeArea(
        top: false,
        bottom: false,
        child: Column(
          children: [
            Expanded(
              child: CustomScrollView(
                physics: const BouncingScrollPhysics(),
                slivers: [
                  SliverToBoxAdapter(
                    child: AnnotatedRegion<SystemUiOverlayStyle>(
                      value: SystemUiOverlayStyle.dark,
                      child: Container(
                        decoration: const BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.vertical(
                            bottom: Radius.circular(24),
                          ),
                          border: Border(
                            bottom: BorderSide(color: DoctorUi.border, width: 1.5),
                          ),
                        ),
                        padding: EdgeInsets.fromLTRB(8, topPad + 4, 16, 16),
                        child: Row(
                          children: [
                            IconButton(
                              onPressed: () => context.pop(),
                              icon: const Icon(
                                  Icons.arrow_forward_ios_rounded,
                                  color: DoctorUi.textPrimary,
                                  size: 18,
                              ),
                            ),
                            Expanded(
                              child: Text(
                                'تفاصيل الحالة',
                                style: GoogleFonts.cairo(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w900,
                                  color: DoctorUi.textPrimary,
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
                      margin: const EdgeInsets.only(top: 8),
                      color: Colors.transparent,
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
                            if (medicalCase.hospitalStatusLabel != null)
                              _DetailRow(
                                label: 'الحالة داخل المستشفى',
                                value: medicalCase.hospitalStatusLabel!,
                              ),
                            _DetailRow(
                              label: 'تاريخ فتح الحالة',
                              value: formatMedicalCaseDateTime(
                                medicalCase.openedAt,
                              ),
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
                                _ProcedureBlock(
                                  procedure: procedure,
                                  showCaseResult:
                                      medicalCase.type == MedicalCaseType.hospital,
                                ),
                            ],
                          ),
                        ],
                        const SizedBox(height: 12),
                        _SectionCard(
                          title: 'التوصيات الغذائية العلاجية',
                          children: [
                            if (medicalCase.nutritionRecommendations.isEmpty)
                              Text(
                                'لا توجد توصيات غذائية علاجية لهذه الحالة.',
                                style: GoogleFonts.cairo(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: DoctorUi.muted,
                                  height: 1.45,
                                ),
                              )
                            else
                              for (final nutrition
                                  in medicalCase.nutritionRecommendations)
                                _NutritionBlock(nutrition: nutrition),
                          ],
                        ),
                        SizedBox(height: showActions ? 100 : 24),
                      ]),
                    ),
                  ),
                ],
              ),
            ),
            if (showActions)
              Container(
                padding: EdgeInsets.fromLTRB(16, 12, 16, bottomPad + 12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border(top: BorderSide(color: DoctorUi.border)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.05),
                      blurRadius: 12,
                      offset: const Offset(0, -4),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    if (medicalCase.canRegisterProcedure)
                      FilledButton.icon(
                        onPressed: () => _openProcedureForm(medicalCase),
                        style: FilledButton.styleFrom(
                          backgroundColor: AppColors.primary,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                        icon: const Icon(Icons.add_rounded, color: Colors.white),
                        label: Text(
                          'تسجيل إجراء طبي',
                          style: GoogleFonts.cairo(
                            fontSize: 14.5,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    if (medicalCase.canClose) ...[
                      if (medicalCase.canRegisterProcedure)
                        const SizedBox(height: 10),
                      OutlinedButton(
                        onPressed: () => _openCloseForm(medicalCase),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: AppColors.primaryDark,
                          side: const BorderSide(color: AppColors.primaryDark),
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                        child: Text(
                          'إغلاق الحالة',
                          style: GoogleFonts.cairo(
                            fontSize: 14.5,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class _ProcedureBlock extends StatelessWidget {
  const _ProcedureBlock({
    required this.procedure,
    this.showCaseResult = true,
  });

  final MedicalProcedure procedure;
  final bool showCaseResult;

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
          if (showCaseResult)
            Padding(
              padding: const EdgeInsets.only(top: 4),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Expanded(
                    child: Align(
                      alignment: AlignmentDirectional.centerStart,
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 5,
                        ),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEFF6FF),
                          borderRadius: BorderRadius.circular(999),
                          border: Border.all(color: const Color(0xFFBFDBFE)),
                        ),
                        child: Text(
                          procedure.caseResultLabel ?? procedure.caseResult.label,
                          style: GoogleFonts.cairo(
                            fontSize: 12.5,
                            fontWeight: FontWeight.w800,
                            color: const Color(0xFF1D4ED8),
                          ),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Text(
                    'نتيجة الحالة',
                    style: GoogleFonts.cairo(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: DoctorUi.muted,
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}

class _NutritionBlock extends StatelessWidget {
  const _NutritionBlock({required this.nutrition});

  final MedicalNutritionRecommendation nutrition;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAF8),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: DoctorUi.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _DetailRow(label: 'التوصية', value: nutrition.recommendationText),
          _DetailRow(
            label: 'من',
            value: formatMedicalCaseDate(nutrition.startDate),
          ),
          _DetailRow(
            label: 'إلى',
            value: formatMedicalCaseDate(nutrition.endDate),
          ),
          if (nutrition.note != null)
            _DetailRow(label: 'ملاحظة', value: nutrition.note!),
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
