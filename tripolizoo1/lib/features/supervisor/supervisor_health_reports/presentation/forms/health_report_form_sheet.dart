import 'dart:io' show File;

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/shared/widgets/supervisor_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_reports_provider.dart';
import 'package:tripolizoo/shared/media/image_attachment_picker.dart';

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
  final _attachmentPicker = ImageAttachmentPicker();

  SupervisorAnimal? _selectedAnimal;
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

    if (!mounted) return;
    final animal = _selectedAnimal!;
    final provider = context.read<HealthReportsProvider>();
    final report = await provider.addReport(
      animalId: animal.id,
      animalType: animal.type ?? animal.name,
      description: _description.text,
      attachment: _attachment,
      isUrgent: widget.urgent,
    );

    if (!mounted) return;
    setState(() => _loading = false);

    if (report == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تعذّر إرسال البلاغ. حاول مرة أخرى.')),
      );
      return;
    }

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
          items: context.watch<HealthReportsProvider>().groupAnimals,
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
          _buildImage(),
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
                  child: Icon(
                    Icons.close_rounded,
                    color: Colors.white,
                    size: 18,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildImage() {
    if (!kIsWeb && attachment.path.isNotEmpty) {
      return Image.file(
        File(attachment.path),
        height: 180,
        width: double.infinity,
        fit: BoxFit.cover,
      );
    }

    return FutureBuilder<List<int>>(
      future: attachment.readAsBytes(),
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const SizedBox(
            height: 180,
            child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
          );
        }

        if (!snapshot.hasData || snapshot.data!.isEmpty) {
          return const SizedBox(
            height: 120,
            child: Center(child: Text('تعذّر عرض الصورة')),
          );
        }

        return Image.memory(
          Uint8List.fromList(snapshot.data!),
          height: 180,
          width: double.infinity,
          fit: BoxFit.cover,
        );
      },
    );
  }
}
