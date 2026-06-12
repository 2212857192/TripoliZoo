import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class OpenFieldCaseFormSheet extends StatefulWidget {
  const OpenFieldCaseFormSheet({super.key});

  static Future<String?> show(BuildContext context) {
    return SupervisorFormSheet.show<String?>(
      context,
      const OpenFieldCaseFormSheet(),
    );
  }

  @override
  State<OpenFieldCaseFormSheet> createState() => _OpenFieldCaseFormSheetState();
}

class _OpenFieldCaseFormSheetState extends State<OpenFieldCaseFormSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _animalId = TextEditingController();
  final _reason = TextEditingController();
  final _initialNote = TextEditingController();
  bool _loading = false;

  @override
  void dispose() {
    _scrollController.dispose();
    _animalId.dispose();
    _reason.dispose();
    _initialNote.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    await Future.delayed(const Duration(milliseconds: 400));

    if (!mounted) return;
    final created = context.read<MedicalCasesProvider>().openFieldCase(
          animalId: _animalId.text.trim(),
          openReason: _reason.text.trim(),
          initialNote: _initialNote.text,
        );

    if (!mounted) return;
    Navigator.of(context).pop(created.id);
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: 'فتح حالة طبية ميدانية',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'حفظ وفتح الحالة',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        Container(
          padding: const EdgeInsets.all(16),
          decoration: DoctorUi.cardDecoration(radius: 16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text(
                'بيانات الحالة',
                style: GoogleFonts.cairo(
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                  color: AppColors.primaryDark,
                ),
              ),
              const SizedBox(height: 16),
              const SupervisorFormLabel('رقم الحيوان', required: true),
              SupervisorFormTextField(
                controller: _animalId,
                hint: 'مثال: A-102',
                validator: (v) {
                  if (v == null || v.trim().isEmpty) {
                    return 'أدخل رقم الحيوان';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              const SupervisorFormLabel('سبب فتح الحالة', required: true),
              SupervisorFormMultilineField(
                controller: _reason,
                hint: 'اكتب سبب فتح الحالة الطبية',
                minLines: 3,
                validator: (v) =>
                    v == null || v.trim().isEmpty ? 'أدخل سبب فتح الحالة' : null,
              ),
              const SizedBox(height: 16),
              const SupervisorFormLabel('ملاحظة أولية (اختيارية)'),
              SupervisorFormMultilineField(
                controller: _initialNote,
                hint: 'ملاحظة أولية',
                minLines: 2,
              ),
            ],
          ),
        ),
      ],
    );
  }
}
