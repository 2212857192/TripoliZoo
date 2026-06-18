import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/data/medical_cases_api_repository.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
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
  final _repository = MedicalCasesApiRepository();
  final _reason = TextEditingController();
  final _initialNote = TextEditingController();

  SupervisorAnimal? _selectedAnimal;
  List<SupervisorAnimal> _animals = [];
  bool _loadingAnimals = true;
  bool _loading = false;
  String? _animalsError;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadAnimals());
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _reason.dispose();
    _initialNote.dispose();
    super.dispose();
  }

  Future<void> _loadAnimals() async {
    setState(() {
      _loadingAnimals = true;
      _animalsError = null;
    });

    try {
      final animals = await _repository.fetchGroupAnimals();
      if (!mounted) return;
      setState(() {
        _animals = animals;
        _loadingAnimals = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _animals = [];
        _loadingAnimals = false;
        _animalsError = 'تعذّر تحميل حيوانات المجموعة';
      });
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);

    try {
      final created = await context.read<MedicalCasesProvider>().openFieldCase(
            animalId: _selectedAnimal!.id,
            openReason: _reason.text.trim(),
            initialNote: _initialNote.text,
          );

      if (!mounted || created == null) return;
      Navigator.of(context).pop(created.id);
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('تعذّر فتح الحالة. حاول مرة أخرى.'),
        ),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
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
              const SupervisorFormLabel('الحيوان', required: true),
              if (_loadingAnimals)
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 12),
                  child: Center(
                    child: SizedBox(
                      width: 24,
                      height: 24,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        color: AppColors.primary,
                      ),
                    ),
                  ),
                )
              else if (_animalsError != null)
                Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      _animalsError!,
                      style: GoogleFonts.cairo(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF6B7280),
                      ),
                    ),
                    const SizedBox(height: 8),
                    TextButton(
                      onPressed: _loadAnimals,
                      child: Text(
                        'إعادة المحاولة',
                        style: GoogleFonts.cairo(
                          fontWeight: FontWeight.w800,
                          color: AppColors.primary,
                        ),
                      ),
                    ),
                  ],
                )
              else if (_animals.isEmpty)
                Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      'لا توجد حيوانات مسجّلة نشطة في مجموعتك حالياً.',
                      style: GoogleFonts.cairo(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF6B7280),
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'يظهر هنا فقط الحيوانات المستلمة والنشطة في المجموعة.',
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF9CA3AF),
                      ),
                    ),
                    const SizedBox(height: 8),
                    TextButton(
                      onPressed: _loadAnimals,
                      child: Text(
                        'تحديث القائمة',
                        style: GoogleFonts.cairo(
                          fontWeight: FontWeight.w800,
                          color: AppColors.primary,
                        ),
                      ),
                    ),
                  ],
                )
              else
                SupervisorFormDropdown<SupervisorAnimal>(
                  key: ValueKey('field-case-animals-${_animals.length}'),
                  value: _selectedAnimal,
                  hint: 'اختر الحيوان (${_animals.length})',
                  items: _animals,
                  itemLabel: (animal) => animal.label,
                  onChanged: (value) => setState(() => _selectedAnimal = value),
                  validator: (value) =>
                      value == null ? 'اختر الحيوان من قائمة مجموعتك' : null,
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
