import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';

class CloseFieldCaseSheet extends StatefulWidget {
  const CloseFieldCaseSheet({super.key, required this.caseId});

  final String caseId;

  static Future<bool?> show(BuildContext context, {required String caseId}) {
    return SupervisorFormSheet.show<bool?>(
      context,
      CloseFieldCaseSheet(caseId: caseId),
    );
  }

  @override
  State<CloseFieldCaseSheet> createState() => _CloseFieldCaseSheetState();
}

class _CloseFieldCaseSheetState extends State<CloseFieldCaseSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  bool _loading = false;

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    setState(() => _loading = true);

    try {
      await context.read<MedicalCasesProvider>().closeFieldCase(
            caseId: widget.caseId,
          );

      if (!mounted) return;
      Navigator.of(context).pop(true);
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تعذّر إغلاق الحالة. حاول مرة أخرى.')),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: 'إغلاق الحالة الميدانية',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'تأكيد الإغلاق',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        Text(
          'هل أنت متأكد من إغلاق هذه الحالة؟ ستصبح للعرض فقط بعد الإغلاق.',
          style: GoogleFonts.cairo(
            fontSize: 13.5,
            fontWeight: FontWeight.w600,
            color: const Color(0xFF6B7280),
            height: 1.45,
          ),
        ),
      ],
    );
  }
}
