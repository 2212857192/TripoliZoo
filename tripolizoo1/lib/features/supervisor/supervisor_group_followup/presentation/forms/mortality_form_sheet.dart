import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';
import 'package:tripolizoo/shared/widgets/app_text_field.dart';

class MortalityFormSheet extends StatefulWidget {
  const MortalityFormSheet({super.key});

  static Future<bool?> show(BuildContext context) {
    return SupervisorFormSheet.show(context, const MortalityFormSheet());
  }

  @override
  State<MortalityFormSheet> createState() => _MortalityFormSheetState();
}

class _MortalityFormSheetState extends State<MortalityFormSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _deathCause = TextEditingController();
  final _extraNotes = TextEditingController();

  MortalityVictimKind _victimKind = MortalityVictimKind.zooAnimal;
  SupervisorAnimal? _selectedAnimal;
  bool _hasAttachment = false;
  bool _loading = false;

  List<SupervisorAnimal> get _animals => _victimKind ==
          MortalityVictimKind.zooAnimal
      ? SupervisorAnimalsData.groupAnimals
      : SupervisorAnimalsData.newbornAnimals;

  @override
  void dispose() {
    _scrollController.dispose();
    _deathCause.dispose();
    _extraNotes.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    await Future.delayed(const Duration(milliseconds: 400));

    if (!mounted) return;
    final animal = _selectedAnimal!;
    context.read<FollowUpProvider>().addMortality(
          victimKind: _victimKind,
          animalId: animal.id,
          animalType: animal.type,
          deathCause: _deathCause.text.trim(),
          extraNotes: _extraNotes.text.trim(),
          hasAttachment: _hasAttachment,
        );

    if (!mounted) return;
    Navigator.of(context).pop(true);
  }

  @override
  Widget build(BuildContext context) {
    final animalHint = _victimKind == MortalityVictimKind.zooAnimal
        ? 'اختر رقم الحيوان'
        : 'اختر رقم المولود';

    return SupervisorFormSheet(
      title: 'تسجيل حالة نفوق',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'حفظ الحالة',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        const SupervisorFormLabel('نوع النافق *'),
        SupervisorFormRadioGroup<MortalityVictimKind>(
          value: _victimKind,
          onChanged: (v) => setState(() {
            _victimKind = v;
            _selectedAnimal = null;
          }),
          options: const [
            SupervisorRadioOption(
              value: MortalityVictimKind.zooAnimal,
              label: 'حيوان داخل الحديقة',
            ),
            SupervisorRadioOption(
              value: MortalityVictimKind.newbornUnderFollowUp,
              label: 'مولود قيد المتابعة',
            ),
          ],
        ),
        const SizedBox(height: 16),
        SupervisorFormLabel(
          _victimKind == MortalityVictimKind.zooAnimal
              ? 'رقم الحيوان *'
              : 'رقم المولود *',
        ),
        SupervisorFormDropdown<SupervisorAnimal>(
          key: ValueKey(_victimKind),
          value: _selectedAnimal,
          hint: animalHint,
          items: _animals,
          itemLabel: (a) => a.label,
          onChanged: (v) => setState(() => _selectedAnimal = v),
          validator: (v) => v == null ? 'اختر الرقم' : null,
        ),
        const SizedBox(height: 16),
        AppTextField(
          controller: _deathCause,
          label: 'سبب النفوق',
          hint: 'اختياري — إن تُرك فارغاً يظهر: غير ظاهر',
          icon: Icons.info_outline_rounded,
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('ملاحظات إضافية'),
        SupervisorFormMultilineField(
          controller: _extraNotes,
          hint: 'اختياري',
          minLines: 2,
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
