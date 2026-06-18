import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class RegisterMedicalProcedureSheet extends StatefulWidget {
  const RegisterMedicalProcedureSheet({
    super.key,
    required this.caseId,
    required this.isHospitalCase,
  });

  final String caseId;
  final bool isHospitalCase;

  static Future<bool?> show(
    BuildContext context, {
    required String caseId,
    required bool isHospitalCase,
  }) {
    return SupervisorFormSheet.show<bool?>(
      context,
      RegisterMedicalProcedureSheet(
        caseId: caseId,
        isHospitalCase: isHospitalCase,
      ),
    );
  }

  @override
  State<RegisterMedicalProcedureSheet> createState() =>
      _RegisterMedicalProcedureSheetState();
}

class _RegisterMedicalProcedureSheetState
    extends State<RegisterMedicalProcedureSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _diagnosis = TextEditingController();
  final _treatment = TextEditingController();
  final _note = TextEditingController();
  final _nutritionText = TextEditingController();
  final _nutritionNote = TextEditingController();

  bool _includeNutrition = false;
  bool _loading = false;
  DateTime? _nutritionStart;
  DateTime? _nutritionEnd;
  MedicalCaseResult _caseResult = MedicalCaseResult.continueTreatment;

  @override
  void initState() {
    super.initState();
    final today = DateTime.now();
    _nutritionStart = DateTime(today.year, today.month, today.day);
    _nutritionEnd = DateTime(today.year, today.month, today.day);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _diagnosis.dispose();
    _treatment.dispose();
    _note.dispose();
    _nutritionText.dispose();
    _nutritionNote.dispose();
    super.dispose();
  }

  Future<void> _pickDate({required bool isStart}) async {
    final initial = isStart
        ? (_nutritionStart ?? DateTime.now())
        : (_nutritionEnd ?? _nutritionStart ?? DateTime.now());
    final picked = await showDatePicker(
      context: context,
      initialDate: initial,
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
    );
    if (!mounted || picked == null) return;
    setState(() {
      if (isStart) {
        _nutritionStart = picked;
      } else {
        _nutritionEnd = picked;
      }
    });
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    if (_includeNutrition) {
      final today = DateTime.now();
      _nutritionStart ??= DateTime(today.year, today.month, today.day);
      _nutritionEnd ??= DateTime(today.year, today.month, today.day);
      if (_nutritionEnd!.isBefore(_nutritionStart!)) {
        _nutritionEnd = _nutritionStart;
      }
    }

    setState(() => _loading = true);

    try {
      await context.read<MedicalCasesProvider>().registerProcedure(
            caseId: widget.caseId,
            diagnosis: _diagnosis.text,
            treatment: _treatment.text,
            caseResult: _caseResult,
            note: _note.text,
            nutritionText: _includeNutrition ? _nutritionText.text : null,
            nutritionStart: _nutritionStart,
            nutritionEnd: _nutritionEnd,
            nutritionNote: _nutritionNote.text,
          );

      if (!mounted) return;
      Navigator.of(context).pop(true);
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تعذّر تسجيل الإجراء. حاول مرة أخرى.')),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: widget.isHospitalCase ? 'تسجيل إجراء طبي' : 'تسجيل قرار طبي',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'حفظ',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        const SupervisorFormLabel('التشخيص', required: true),
        SupervisorFormMultilineField(
          controller: _diagnosis,
          hint: 'اكتب التشخيص...',
          minLines: 2,
          validator: (v) =>
              v == null || v.trim().isEmpty ? 'أدخل التشخيص' : null,
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('العلاج', required: true),
        SupervisorFormMultilineField(
          controller: _treatment,
          hint: 'اكتب العلاج أو الإجراء المتخذ...',
          minLines: 2,
          validator: (v) =>
              v == null || v.trim().isEmpty ? 'أدخل العلاج' : null,
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('ملاحظة (اختيارية)'),
        SupervisorFormMultilineField(
          controller: _note,
          hint: 'ملاحظات إضافية',
          minLines: 2,
        ),
        const SizedBox(height: 18),
        if (widget.isHospitalCase) ...[
          const SupervisorFormLabel('نتيجة الحالة', required: true),
          Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: Text(
              'خيارا «جاهز للخروج» و«لا يستجيب للعلاج» يُرسلان لرئيس القسم للاعتماد.',
              style: GoogleFonts.cairo(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: DoctorUi.muted,
                height: 1.4,
              ),
            ),
          ),
          SupervisorFormRadioGroup<MedicalCaseResult>(
            value: _caseResult,
            onChanged: (value) => setState(() => _caseResult = value),
            options: const [
              SupervisorRadioOption(
                value: MedicalCaseResult.continueTreatment,
                label: 'استمرار العلاج',
              ),
              SupervisorRadioOption(
                value: MedicalCaseResult.noResponse,
                label: 'لا يستجيب للعلاج',
              ),
              SupervisorRadioOption(
                value: MedicalCaseResult.readyForDischarge,
                label: 'جاهز للخروج',
              ),
            ],
          ),
          const SizedBox(height: 18),
        ],
        _NutritionToggleCard(
          enabled: _includeNutrition,
          onChanged: (value) {
            final today = DateTime.now();
            setState(() {
              _includeNutrition = value;
              if (value) {
                _nutritionStart = DateTime(today.year, today.month, today.day);
                _nutritionEnd = DateTime(today.year, today.month, today.day);
              }
            });
          },
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const SupervisorFormLabel('نص التوصية', required: true),
              SupervisorFormMultilineField(
                controller: _nutritionText,
                hint: 'اكتب التوصية الغذائية العلاجية',
                minLines: 2,
                validator: (v) {
                  if (!_includeNutrition) return null;
                  if (v == null || v.trim().isEmpty) {
                    return 'أدخل نص التوصية';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: _DateField(
                      label: 'تاريخ البداية',
                      value: _nutritionStart,
                      onTap: () => _pickDate(isStart: true),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: _DateField(
                      label: 'تاريخ الانتهاء',
                      value: _nutritionEnd,
                      onTap: () => _pickDate(isStart: false),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              const SupervisorFormLabel('ملاحظة للتوصية (اختيارية)'),
              SupervisorFormMultilineField(
                controller: _nutritionNote,
                hint: 'ملاحظة',
                minLines: 2,
              ),
            ],
          ),
        ),
      ],
    );
  }
}

class _NutritionToggleCard extends StatelessWidget {
  const _NutritionToggleCard({
    required this.enabled,
    required this.onChanged,
    required this.child,
  });

  final bool enabled;
  final ValueChanged<bool> onChanged;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: DoctorUi.cardDecoration(radius: 14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          InkWell(
            onTap: () => onChanged(!enabled),
            borderRadius: BorderRadius.circular(10),
            child: Row(
              children: [
                Icon(
                  enabled
                      ? Icons.check_circle_rounded
                      : Icons.radio_button_unchecked_rounded,
                  color: enabled ? AppColors.primary : DoctorUi.muted,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    'إضافة توصية غذائية علاجية',
                    style: GoogleFonts.cairo(
                      fontSize: 14,
                      fontWeight: FontWeight.w800,
                      color: AppColors.primaryDark,
                    ),
                  ),
                ),
              ],
            ),
          ),
          if (enabled) ...[
            const SizedBox(height: 14),
            child,
          ],
        ],
      ),
    );
  }
}

class _DateField extends StatelessWidget {
  const _DateField({
    required this.label,
    required this.value,
    required this.onTap,
  });

  final String label;
  final DateTime? value;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Text(
          label,
          style: GoogleFonts.cairo(
            fontSize: 12,
            fontWeight: FontWeight.w700,
            color: DoctorUi.muted,
          ),
        ),
        const SizedBox(height: 6),
        InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(12),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            decoration: BoxDecoration(
              border: Border.all(color: DoctorUi.border),
              borderRadius: BorderRadius.circular(12),
              color: Colors.white,
            ),
            child: Text(
              value == null
                  ? 'dd/mm/yyyy'
                  : formatMedicalCaseDate(value!),
              style: GoogleFonts.cairo(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: value == null ? DoctorUi.muted : DoctorUi.textPrimary,
              ),
            ),
          ),
        ),
      ],
    );
  }
}
