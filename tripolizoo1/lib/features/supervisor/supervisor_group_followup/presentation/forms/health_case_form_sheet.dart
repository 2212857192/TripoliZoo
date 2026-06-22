import 'dart:io' show File;

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/health_cases_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_reports_provider.dart';
import 'package:tripolizoo/shared/media/image_attachment_picker.dart';

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
  final _animalNotes = TextEditingController();
  final _repository = HealthCasesApiRepository();
  final _attachmentPicker = ImageAttachmentPicker();

  SupervisorAnimal? _selectedAnimal;
  HealthFollowUpKind _followUpKind = HealthFollowUpKind.noReferral;
  XFile? _attachment;
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (!mounted) return;
      await context.read<HealthReportsProvider>().reloadAnimals();
      if (!mounted) return;
      setState(() => _selectedAnimal = null);
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _description.dispose();
    _animalNotes.dispose();
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

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);

    try {
      final animal = _selectedAnimal!;
      final followUpKind = _followUpKind == HealthFollowUpKind.needsReferral
          ? 'needs_referral'
          : 'no_referral';

      await _repository.createCase(
        animalCode: animal.id,
        description: _description.text,
        followUpKind: followUpKind,
        animalNotes: _followUpKind == HealthFollowUpKind.needsReferral
            ? _animalNotes.text
            : null,
        attachment: _attachment,
      );

      if (!mounted) return;

      await context.read<FollowUpProvider>().refresh();

      if (!mounted) return;
      Navigator.of(context).pop(true);
    } catch (e) {
      if (!mounted) return;
      final message = e is AuthException
          ? e.message
          : 'تعذّر تسجيل الحالة. حاول مرة أخرى.';
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(message)),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final animals = context.watch<HealthReportsProvider>().groupAnimals;

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
          items: animals,
          itemLabel: (a) => a.label,
          onChanged: (v) => setState(() => _selectedAnimal = v),
          validator: (v) => v == null ? 'اختر رقم الحيوان' : null,
        ),
        const SizedBox(height: 18),
        const SupervisorFormLabel('وصف الحالة *'),
        SupervisorFormMultilineField(
          controller: _description,
          hint: 'صف الأعراض أو الإصابة المشاهدة...',
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
        if (_followUpKind == HealthFollowUpKind.needsReferral) ...[
          const SizedBox(height: 18),
          const SupervisorFormLabel('ملاحظات مسجلة عن الحيوان *'),
          SupervisorFormMultilineField(
            controller: _animalNotes,
            hint: 'سلوك الحيوان، شهيته، حركته، أو أي ملاحظات تهم المستشفى البيطري...',
            validator: (v) => _followUpKind == HealthFollowUpKind.needsReferral &&
                    (v == null || v.trim().isEmpty)
                ? 'أدخل ملاحظات عن الحيوان لإرسالها للمستشفى'
                : null,
          ),
        ],
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
