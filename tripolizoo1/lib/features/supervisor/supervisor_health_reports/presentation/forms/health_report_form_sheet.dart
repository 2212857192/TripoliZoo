import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_reports_provider.dart';

class HealthReportFormSheet extends StatefulWidget {
  const HealthReportFormSheet({
    super.key,
    this.urgent = false,
  });

  final bool urgent;

  static Future<bool?> show(BuildContext context, {bool urgent = false}) {
    return SupervisorFormSheet.show(
      context,
      HealthReportFormSheet(urgent: urgent),
    );
  }

  @override
  State<HealthReportFormSheet> createState() => _HealthReportFormSheetState();
}

class _HealthReportFormSheetState extends State<HealthReportFormSheet> {
  final _formKey = GlobalKey<FormState>();
  final _scrollController = ScrollController();
  final _description = TextEditingController();

  SupervisorAnimal? _selectedAnimal;
  bool _hasAttachment = false;
  bool _loading = false;

  @override
  void dispose() {
    _scrollController.dispose();
    _description.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    await Future.delayed(const Duration(milliseconds: 450));

    if (!mounted) return;
    final animal = _selectedAnimal!;
    context.read<HealthReportsProvider>().addReport(
          animalId: animal.id,
          animalType: animal.type ?? animal.name,
          description: _description.text,
          hasAttachment: _hasAttachment,
        );

    if (!mounted) return;
    Navigator.of(context).pop(true);
  }

  @override
  Widget build(BuildContext context) {
    return SupervisorFormSheet(
      title: widget.urgent ? 'بلاغ صحي عاجل' : 'إرسال بلاغ صحي',
      formKey: _formKey,
      scrollController: _scrollController,
      submitLabel: 'إرسال البلاغ',
      isLoading: _loading,
      onSubmit: _submit,
      children: [
        if (widget.urgent) ...[
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: const Color(0xFFFFF7ED),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: const Color(0xFFFED7AA)),
            ),
            child: Row(
              children: [
                const Icon(
                  Icons.warning_amber_rounded,
                  color: Color(0xFFEA580C),
                  size: 20,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    'يُرسل مباشرة للطبيب المسؤول — للحالات التي تحتاج تدخلاً سريعاً',
                    style: Theme.of(context).textTheme.bodySmall?.copyWith(
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF9A3412),
                        ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
        ],
        const SupervisorFormLabel('رقم الحيوان', required: true),
        SupervisorFormDropdown<SupervisorAnimal>(
          value: _selectedAnimal,
          hint: 'اختر رقم الحيوان',
          items: SupervisorAnimalsData.groupAnimals,
          itemLabel: (a) => a.label,
          onChanged: (v) => setState(() => _selectedAnimal = v),
          validator: (v) => v == null ? 'اختر رقم الحيوان' : null,
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('وصف البلاغ', required: true),
        SupervisorFormMultilineField(
          controller: _description,
          hint: 'صف المشكلة الصحية بالتفصيل...',
          validator: (v) =>
              v == null || v.trim().isEmpty ? 'أدخل وصف البلاغ' : null,
        ),
        const SizedBox(height: 16),
        const SupervisorFormLabel('صورة / مرفق'),
        SupervisorAttachmentButton(
          attached: _hasAttachment,
          onTap: () => setState(() => _hasAttachment = !_hasAttachment),
        ),
      ],
    );
  }
}
