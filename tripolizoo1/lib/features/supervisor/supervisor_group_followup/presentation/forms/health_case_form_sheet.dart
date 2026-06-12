import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';

class HealthCaseFormSheet extends StatefulWidget {
  const HealthCaseFormSheet({super.key});

  static Future<bool?> show(BuildContext context) {
    return SupervisorFormSheet.show(context, const HealthCaseFormSheet());
  }

  @override
  State<HealthCaseFormSheet> createState() => _HealthCaseFormSheetState();
}

class _HealthCaseFormSheetState extends State<HealthCaseFormSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _description = TextEditingController();

  SupervisorAnimal? _selectedAnimal;
  HealthFollowUpKind _followUpKind = HealthFollowUpKind.noReferral;
  bool _hasAttachment = false;
  bool _loading = false;

  @override
  void dispose() {
    _scrollController.dispose();
    _description.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    await Future.delayed(const Duration(milliseconds: 400));

    if (!mounted) return;
    final animal = _selectedAnimal!;
    context.read<FollowUpProvider>().addHealth(
          animalId: animal.id,
          animalType: animal.type,
          description: _description.text.trim(),
          followUpKind: _followUpKind,
          hasAttachment: _hasAttachment,
        );

    if (!mounted) return;
    Navigator.of(context).pop(true);
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: 'تسجيل حالة صحية',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'حفظ الحالة',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        const SupervisorFormLabel('رقم الحيوان *'),
        SupervisorFormDropdown<SupervisorAnimal>(
          value: _selectedAnimal,
          hint: 'اختر رقم الحيوان',
          items: SupervisorAnimalsData.groupAnimals,
          itemLabel: (a) => a.label,
          onChanged: (v) => setState(() => _selectedAnimal = v),
          validator: (v) => v == null ? 'اختر رقم الحيوان' : null,
        ),
        const SizedBox(height: 18),
        const SupervisorFormLabel('وصف الحالة *'),
        SupervisorFormMultilineField(
          controller: _description,
          hint: 'صف الأعراض المشاهدة...',
          validator: (v) =>
              v == null || v.trim().isEmpty ? 'أدخل وصف الحالة' : null,
        ),
        const SizedBox(height: 18),
        const SupervisorFormLabel('نوع المتابعة *'),
        SupervisorFormRadioGroup<HealthFollowUpKind>(
          value: _followUpKind,
          onChanged: (v) => setState(() => _followUpKind = v),
          options: const [
            SupervisorRadioOption(
              value: HealthFollowUpKind.noReferral,
              label: 'لا تحتاج إحالة',
            ),
            SupervisorRadioOption(
              value: HealthFollowUpKind.needsReferral,
              label: 'تحتاج إحالة',
            ),
          ],
        ),
        const SizedBox(height: 12),
        SupervisorAttachmentButton(
          attached: _hasAttachment,
          onTap: () => setState(() => _hasAttachment = !_hasAttachment),
        ),
      ],
    );
  }
}
