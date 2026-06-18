import 'dart:io' show File;

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/birth_registrations_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/media/image_attachment_picker.dart';

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
  final _repository = BirthRegistrationsApiRepository();
  final _attachmentPicker = ImageAttachmentPicker();

  SupervisorAnimal? _selectedMother;
  DateTime? _birthDate;
  final List<_NewbornFields> _newborns = [_NewbornFields()];
  List<SupervisorAnimal> _mothers = [];
  bool _loadingMothers = true;
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    _birthCount.addListener(_onBirthCountChanged);
    _loadMothers();
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

  Future<void> _loadMothers() async {
    try {
      final mothers = await _repository.fetchMothers();
      if (!mounted) return;
      setState(() {
        _mothers = mothers;
        _loadingMothers = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() => _loadingMothers = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تعذّر تحميل قائمة الأمهات.')),
      );
    }
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

  Future<void> _pickNewbornPhoto(int index) async {
    try {
      final image = await _attachmentPicker.pickFromGallery();
      if (!mounted || image == null) return;
      setState(() => _newborns[index].photo = image);
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تعذّر اختيار الصورة. حاول مرة أخرى.')),
      );
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    for (var i = 0; i < _newborns.length; i++) {
      if (_newborns[i].photo == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('أضف صورة للمولود ${i + 1}')),
        );
        return;
      }
    }

    setState(() => _loading = true);

    try {
      final mother = _selectedMother!;
      final newborns = _newborns
          .map(
            (n) => NewbornRecord(
              gender: n.gender!,
              photo: n.photo,
              distinguishingMark: n.markController.text.trim(),
              note: n.noteController.text.trim(),
            ),
          )
          .toList();

      await _repository.createRegistration(
        motherCode: mother.id,
        birthDate: _birthDate!,
        newborns: newborns,
      );

      if (!mounted) return;
      await context.read<FollowUpProvider>().refresh();

      if (!mounted) return;
      Navigator.of(context).pop(true);
    } catch (error) {
      if (!mounted) return;
      final message = error is AuthException
          ? error.message
          : 'تعذّر تسجيل الولادة. حاول مرة أخرى.';
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(message)),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
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
        if (_loadingMothers)
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 12),
            child: Center(child: CircularProgressIndicator()),
          )
        else
          SupervisorFormDropdown<SupervisorAnimal>(
            value: _selectedMother,
            hint: _mothers.isEmpty ? 'لا توجد إناث في مجموعتك' : 'اختر الأم',
            items: _mothers,
            itemLabel: (a) => a.label,
            onChanged: _mothers.isEmpty ? (_) {} : (v) => setState(() => _selectedMother = v),
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
              onPickPhoto: () => _pickNewbornPhoto(index),
              onRemovePhoto: () => setState(() => newborn.photo = null),
            ),
          );
        }),
      ],
    );
  }
}

class _NewbornFields {
  NewbornGender? gender;
  XFile? photo;
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
    required this.onPickPhoto,
    required this.onRemovePhoto,
  });

  final int index;
  final _NewbornFields fields;
  final VoidCallback onChanged;
  final VoidCallback onPickPhoto;
  final VoidCallback onRemovePhoto;

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
          const SupervisorFormLabel('صورة المولود', required: true),
          SupervisorAttachmentButton(
            attached: fields.photo != null,
            onTap: onPickPhoto,
          ),
          if (fields.photo != null) ...[
            const SizedBox(height: 12),
            _PhotoPreview(
              photo: fields.photo!,
              onRemove: onRemovePhoto,
            ),
          ],
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

class _PhotoPreview extends StatelessWidget {
  const _PhotoPreview({
    required this.photo,
    required this.onRemove,
  });

  final XFile photo;
  final VoidCallback onRemove;

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(14),
      child: Stack(
        children: [
          if (!kIsWeb && photo.path.isNotEmpty)
            Image.file(
              File(photo.path),
              height: 140,
              width: double.infinity,
              fit: BoxFit.cover,
            )
          else
            Container(
              height: 140,
              width: double.infinity,
              color: const Color(0xFFF3F4F6),
              alignment: Alignment.center,
              child: const Icon(Icons.image_outlined, size: 40),
            ),
          Positioned(
            top: 8,
            left: 8,
            child: Material(
              color: Colors.black54,
              shape: const CircleBorder(),
              child: InkWell(
                onTap: onRemove,
                customBorder: const CircleBorder(),
                child: const Padding(
                  padding: EdgeInsets.all(6),
                  child: Icon(Icons.close_rounded, color: Colors.white, size: 18),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
