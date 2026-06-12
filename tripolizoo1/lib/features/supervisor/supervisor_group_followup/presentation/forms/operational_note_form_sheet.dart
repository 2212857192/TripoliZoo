import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';

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
  final _fullText = TextEditingController();

  OperationalNoteKind _noteKind = OperationalNoteKind.feeding;
  bool _hasAttachment = false;
  bool _loading = false;

  @override
  void dispose() {
    _scrollController.dispose();
    _summary.dispose();
    _fullText.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    await Future.delayed(const Duration(milliseconds: 400));

    if (!mounted) return;
    context.read<FollowUpProvider>().addOperationalNote(
          noteKind: _noteKind,
          summary: _summary.text.trim(),
          fullText: _fullText.text.trim(),
          hasAttachment: _hasAttachment,
        );

    if (!mounted) return;
    Navigator.of(context).pop(true);
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
        const SizedBox(height: 16),
        const SupervisorFormLabel('تفاصيل إضافية'),
        SupervisorFormMultilineField(
          controller: _fullText,
          hint: 'اختياري — يظهر عند عرض المزيد',
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
