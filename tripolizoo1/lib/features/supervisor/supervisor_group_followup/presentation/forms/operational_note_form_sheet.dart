import 'dart:io' show File;

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/data/operational_notes_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';
import 'package:tripolizoo/shared/media/image_attachment_picker.dart';

class OperationalNoteFormSheet extends StatefulWidget {
  const OperationalNoteFormSheet({super.key});

  static Future<bool?> show(BuildContext context) {
    return SupervisorFormSheet.show(context, const OperationalNoteFormSheet());
  }

  @override
  State<OperationalNoteFormSheet> createState() =>
      _OperationalNoteFormSheetState();
}

class _OperationalNoteFormSheetState extends State<OperationalNoteFormSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _summary = TextEditingController();
  final _repository = OperationalNotesApiRepository();
  final _attachmentPicker = ImageAttachmentPicker();

  OperationalNoteKind _noteKind = OperationalNoteKind.feeding;
  XFile? _attachment;
  bool _loading = false;

  @override
  void dispose() {
    _scrollController.dispose();
    _summary.dispose();
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
      await _repository.createNote(
        noteKind: _noteKind == OperationalNoteKind.feeding
            ? 'feeding'
            : 'general',
        summary: _summary.text,
        attachment: _attachment,
      );

      if (!mounted) return;
      await context.read<FollowUpProvider>().refresh();

      if (!mounted) return;
      Navigator.of(context).pop(true);
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('تعذّر تسجيل الملاحظة التشغيلية. حاول مرة أخرى.'),
        ),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: 'تسجيل ملاحظة تشغيلية',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'حفظ الملاحظة',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        const SupervisorFormLabel('نوع الملاحظة *'),
        SupervisorFormRadioGroup<OperationalNoteKind>(
          value: _noteKind,
          onChanged: (v) => setState(() => _noteKind = v),
          options: const [
            SupervisorRadioOption(
              value: OperationalNoteKind.feeding,
              label: 'تغذية',
            ),
            SupervisorRadioOption(
              value: OperationalNoteKind.general,
              label: 'ملاحظة عامة',
            ),
          ],
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('نص الملاحظة *'),
        SupervisorFormMultilineField(
          controller: _summary,
          hint: 'تأخر وصول الغذاء للمجموعة',
          validator: (v) =>
              v == null || v.trim().isEmpty ? 'أدخل نص الملاحظة' : null,
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
