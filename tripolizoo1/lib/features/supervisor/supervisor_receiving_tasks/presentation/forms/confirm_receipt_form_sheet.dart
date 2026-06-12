import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class ConfirmReceiptFormSheet extends StatefulWidget {
  const ConfirmReceiptFormSheet({super.key});

  static Future<String?> show(BuildContext context) {
    return SupervisorFormSheet.show<String?>(
      context,
      const ConfirmReceiptFormSheet(),
    );
  }

  @override
  State<ConfirmReceiptFormSheet> createState() =>
      _ConfirmReceiptFormSheetState();
}

class _ConfirmReceiptFormSheetState extends State<ConfirmReceiptFormSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _note = TextEditingController();

  @override
  void dispose() {
    _scrollController.dispose();
    _note.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text(
          'تأكيد الاستلام',
          style: GoogleFonts.cairo(fontWeight: FontWeight.w800),
        ),
        content: Text(
          'هل أنت متأكد من تأكيد استلام هذا الحيوان؟',
          style: GoogleFonts.cairo(
            fontWeight: FontWeight.w600,
            height: 1.5,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: Text(
              'إلغاء',
              style: GoogleFonts.cairo(
                fontWeight: FontWeight.w700,
                color: AppColors.textSecondary,
              ),
            ),
          ),
          FilledButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            style: FilledButton.styleFrom(
              backgroundColor: AppColors.primaryDark,
            ),
            child: Text(
              'تأكيد الاستلام',
              style: GoogleFonts.cairo(fontWeight: FontWeight.w800),
            ),
          ),
        ],
      ),
    );

    if (!mounted || confirmed != true) return;
    Navigator.of(context).pop(_note.text.trim());
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: 'تأكيد استلام الحيوان',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'تأكيد الاستلام',
      onSubmit: _submit,
      children: [
        const SupervisorFormLabel('ملاحظة الاستلام'),
        SupervisorFormMultilineField(
          controller: _note,
          hint: 'ملاحظة اختيارية عن حالة الاستلام...',
          minLines: 3,
        ),
      ],
    );
  }
}
