import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';

class BirthFormSheet extends StatefulWidget {
  const BirthFormSheet({super.key});

  static Future<bool?> show(BuildContext context) {
    return SupervisorFormSheet.show(context, const BirthFormSheet());
  }

  @override
  State<BirthFormSheet> createState() => _BirthFormSheetState();
}

class _BirthFormSheetState extends State<BirthFormSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _birthCount = TextEditingController(text: '1');

  SupervisorAnimal? _selectedMother;
  DateTime? _birthDate;
  final List<_NewbornFields> _newborns = [_NewbornFields()];
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    _birthCount.addListener(_onBirthCountChanged);
  }

  @override
  void dispose() {
    _birthCount.removeListener(_onBirthCountChanged);
    _scrollController.dispose();
    _birthCount.dispose();
    for (final n in _newborns) {
      n.dispose();
    }
    super.dispose();
  }

  void _onBirthCountChanged() {
    final count = int.tryParse(_birthCount.text.trim()) ?? 1;
    _syncNewbornFields(count.clamp(1, 10));
  }

  void _syncNewbornFields(int count) {
    while (_newborns.length < count) {
      _newborns.add(_NewbornFields());
    }
    while (_newborns.length > count) {
      _newborns.removeLast().dispose();
    }
    if (mounted) setState(() {});
  }

  Future<void> _pickBirthDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _birthDate ?? DateTime.now(),
      firstDate: DateTime.now().subtract(const Duration(days: 30)),
      lastDate: DateTime.now(),
      locale: const Locale('ar'),
    );
    if (picked != null) {
      setState(() => _birthDate = picked);
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    await Future.delayed(const Duration(milliseconds: 400));

    if (!mounted) return;
    final mother = _selectedMother!;
    final newborns = _newborns
        .map(
          (n) => NewbornRecord(
            gender: n.gender!,
            distinguishingMark: n.markController.text.trim(),
            note: n.noteController.text.trim(),
          ),
        )
        .toList();

    context.read<FollowUpProvider>().addBirth(
          motherId: mother.id,
          animalType: mother.type,
          birthDate: _birthDate!,
          birthCount: newborns.length,
          newborns: newborns,
        );

    if (!mounted) return;
    Navigator.of(context).pop(true);
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: 'تسجيل ولادة جديدة',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'حفظ الولادة',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        const SupervisorFormLabel('رقم الأم', required: true),
        SupervisorFormDropdown<SupervisorAnimal>(
          value: _selectedMother,
          hint: 'اختر الأم',
          items: SupervisorAnimalsData.mothers,
          itemLabel: (a) => a.label,
          onChanged: (v) => setState(() => _selectedMother = v),
          validator: (v) => v == null ? 'اختر الأم' : null,
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('تاريخ الولادة', required: true),
        SupervisorFormDateField(
          value: _birthDate,
          onPick: _pickBirthDate,
          validator: (v) => v == null ? 'اختر تاريخ الولادة' : null,
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('عدد المواليد', required: true),
        SupervisorFormTextField(
          controller: _birthCount,
          hint: '1',
          keyboardType: TextInputType.number,
          validator: (v) {
            if (v == null || v.trim().isEmpty) return 'أدخل عدد المواليد';
            final n = int.tryParse(v.trim());
            if (n == null || n < 1 || n > 10) {
              return 'أدخل رقماً بين 1 و 10';
            }
            return null;
          },
        ),
        const SizedBox(height: 20),
        const SupervisorFormSectionHeader('بيانات المواليد'),
        ...List.generate(_newborns.length, (index) {
          final newborn = _newborns[index];
          return Padding(
            padding: EdgeInsets.only(
              bottom: index == _newborns.length - 1 ? 0 : 16,
            ),
            child: _NewbornSection(
              index: index,
              fields: newborn,
              onChanged: () => setState(() {}),
            ),
          );
        }),
      ],
    );
  }
}

class _NewbornFields {
  NewbornGender? gender;
  final markController = TextEditingController();
  final noteController = TextEditingController();

  void dispose() {
    markController.dispose();
    noteController.dispose();
  }
}

class _NewbornSection extends StatelessWidget {
  const _NewbornSection({
    required this.index,
    required this.fields,
    required this.onChanged,
  });

  final int index;
  final _NewbornFields fields;
  final VoidCallback onChanged;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAF8),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE5E7EB)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            'المولود ${index + 1}',
            style: GoogleFonts.cairo(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: const Color(0xFF1A1A1A),
            ),
          ),
          const SizedBox(height: 12),
          const SupervisorFormLabel('الجنس', required: true),
          SupervisorFormDropdown<NewbornGender>(
            value: fields.gender,
            hint: 'الجنس',
            items: NewbornGender.values,
            itemLabel: (g) => switch (g) {
              NewbornGender.male => 'ذكر',
              NewbornGender.female => 'أنثى',
            },
            onChanged: (v) {
              fields.gender = v;
              onChanged();
            },
            validator: (v) => v == null ? 'اختر الجنس' : null,
          ),
          const SizedBox(height: 12),
          const SupervisorFormLabel('علامة تمييز (اختياري)'),
          SupervisorFormTextField(
            controller: fields.markController,
            hint: 'علامة تمييز (اختياري)',
          ),
          const SizedBox(height: 12),
          const SupervisorFormLabel('ملاحظة (اختياري)'),
          SupervisorFormMultilineField(
            controller: fields.noteController,
            hint: 'ملاحظة (اختياري)',
            minLines: 2,
          ),
        ],
      ),
    );
  }
}
