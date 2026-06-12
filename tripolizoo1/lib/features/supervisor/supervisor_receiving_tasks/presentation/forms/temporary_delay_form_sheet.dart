import 'package:flutter/material.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';

class TemporaryDelayFormSheet extends StatefulWidget {
  const TemporaryDelayFormSheet({super.key});

  static Future<({String reason, String? extraNote})?> show(
    BuildContext context,
  ) {
    return SupervisorFormSheet.show(
      context,
      const TemporaryDelayFormSheet(),
    );
  }

  @override
  State<TemporaryDelayFormSheet> createState() =>
      _TemporaryDelayFormSheetState();
}

class _TemporaryDelayFormSheetState extends State<TemporaryDelayFormSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _reason = TextEditingController();
  final _extraNote = TextEditingController();
  bool _loading = false;

  @override
  void dispose() {
    _scrollController.dispose();
    _reason.dispose();
    _extraNote.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    await Future.delayed(const Duration(milliseconds: 300));
    if (!mounted) return;
    Navigator.of(context).pop((
      reason: _reason.text.trim(),
      extraNote: _extraNote.text.trim().isEmpty
          ? null
          : _extraNote.text.trim(),
    ));
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: 'تسجيل تعذر الاستلام مؤقتًا',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'حفظ التعذر',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        const SupervisorFormLabel('سبب التعذر', required: true),
        SupervisorFormMultilineField(
          controller: _reason,
          hint: 'مثال: القفص غير جاهز حاليًا',
          minLines: 3,
          validator: (v) =>
              v == null || v.trim().isEmpty ? 'أدخل سبب التعذر' : null,
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('ملاحظة إضافية'),
        SupervisorFormMultilineField(
          controller: _extraNote,
          hint: 'تفاصيل إضافية (اختياري)',
          minLines: 2,
        ),
      ],
    );
  }
}
