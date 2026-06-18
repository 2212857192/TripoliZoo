import 'dart:io' show File;

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/birth_registrations_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/mortality_cases_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_reports_provider.dart';
import 'package:tripolizoo/shared/media/image_attachment_picker.dart';
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
  final _repository = MortalityCasesApiRepository();
  final _birthRepository = BirthRegistrationsApiRepository();
  final _attachmentPicker = ImageAttachmentPicker();

  MortalityVictimKind _victimKind = MortalityVictimKind.zooAnimal;
  SupervisorAnimal? _selectedAnimal;
  List<SupervisorAnimal> _newbornAnimals = [];
  bool _loadingNewborns = false;
  XFile? _attachment;
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;
      final provider = context.read<HealthReportsProvider>();
      if (provider.groupAnimals.isEmpty) {
        provider.load(audience: HealthReportsAudience.supervisor);
      }
      _loadNewborns();
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _deathCause.dispose();
    _extraNotes.dispose();
    super.dispose();
  }

  Future<void> _pickAttachment() async {
    try {
      final image = await _attachmentPicker.pickFromGallery();
      if (!mounted || image == null) return;
      setState(() => _attachment = image);
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تعذّر اختيار الصورة. حاول مرة أخرى.')),
      );
    }
  }

  void _removeAttachment() {
    setState(() => _attachment = null);
  }

  Future<void> _loadNewborns() async {
    setState(() => _loadingNewborns = true);
    try {
      final newborns = await _birthRepository.fetchNewborns();
      if (!mounted) return;
      setState(() {
        _newbornAnimals = newborns;
        _loadingNewborns = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() => _loadingNewborns = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تعذّر تحميل قائمة المواليد.')),
      );
    }
  }

  void _onVictimKindChanged(MortalityVictimKind kind) {
    setState(() {
      _victimKind = kind;
      _selectedAnimal = null;
    });
    if (kind == MortalityVictimKind.newbornUnderFollowUp &&
        _newbornAnimals.isEmpty &&
        !_loadingNewborns) {
      _loadNewborns();
    }
  }

  List<SupervisorAnimal> _zooAnimals(List<SupervisorAnimal> groupAnimals) {
    if (_newbornAnimals.isEmpty) {
      return groupAnimals;
    }

    final newbornIds = _newbornAnimals.map((animal) => animal.id).toSet();
    return groupAnimals
        .where((animal) => !newbornIds.contains(animal.id))
        .toList();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);

    try {
      final animal = _selectedAnimal!;
      final victimKind = _victimKind == MortalityVictimKind.zooAnimal
          ? 'zoo_animal'
          : 'newborn_under_follow_up';

      await _repository.createCase(
        animalCode: animal.id,
        victimKind: victimKind,
        animalType: animal.type,
        deathCause: _deathCause.text,
        notes: _extraNotes.text,
        attachment: _attachment,
      );

      if (!mounted) return;
      await context.read<FollowUpProvider>().refresh();

      if (!mounted) return;
      Navigator.of(context).pop(true);
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تعذّر تسجيل حالة النفوق. حاول مرة أخرى.')),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final groupAnimals = context.watch<HealthReportsProvider>().groupAnimals;
    final isNewborn = _victimKind == MortalityVictimKind.newbornUnderFollowUp;
    final animals = isNewborn ? _newbornAnimals : _zooAnimals(groupAnimals);
    final animalHint = isNewborn ? 'اختر رقم المولود' : 'اختر رقم الحيوان';

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
          onChanged: _onVictimKindChanged,
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
        SupervisorFormLabel(isNewborn ? 'رقم المولود *' : 'رقم الحيوان *'),
        if (isNewborn && _loadingNewborns)
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 12),
            child: Center(child: CircularProgressIndicator()),
          )
        else
          SupervisorFormDropdown<SupervisorAnimal>(
            key: ValueKey(_victimKind),
            value: _selectedAnimal,
            hint: animals.isEmpty
                ? (isNewborn
                    ? 'لا يوجد مواليد قيد المتابعة'
                    : 'لا يوجد حيوانات متاحة')
                : animalHint,
            items: animals,
            itemLabel: (a) => a.label,
            onChanged:
                animals.isEmpty ? (_) {} : (v) => setState(() => _selectedAnimal = v),
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
          attached: _attachment != null,
          onTap: _pickAttachment,
        ),
        if (_attachment != null) ...[
          const SizedBox(height: 12),
          _AttachmentPreview(
            attachment: _attachment!,
            onRemove: _removeAttachment,
          ),
        ],
      ],
    );
  }
}

class _AttachmentPreview extends StatelessWidget {
  const _AttachmentPreview({
    required this.attachment,
    required this.onRemove,
  });

  final XFile attachment;
  final VoidCallback onRemove;

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(14),
      child: Stack(
        children: [
          if (!kIsWeb && attachment.path.isNotEmpty)
            Image.file(
              File(attachment.path),
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
