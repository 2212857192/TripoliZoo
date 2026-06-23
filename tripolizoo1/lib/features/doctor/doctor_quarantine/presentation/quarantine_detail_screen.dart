import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/domain/quarantine_record.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/quarantine_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/widgets/quarantine_health_note_sheet.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/widgets/quarantine_vaccine_sheet.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class QuarantineDetailScreen extends StatefulWidget {
  const QuarantineDetailScreen({super.key, required this.recordId});

  final String recordId;

  @override
  State<QuarantineDetailScreen> createState() => _QuarantineDetailScreenState();
}

class _QuarantineDetailScreenState extends State<QuarantineDetailScreen> {
  bool _loading = true;
  bool _submitting = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  @override
  void dispose() {
    super.dispose();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    await context.read<QuarantineProvider>().loadDetail(widget.recordId);
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _openNoteSheet(QuarantineRecord record) async {
    final text = await QuarantineHealthNoteSheet.show(context);
    if (text == null || text.isEmpty || !mounted) return;

    setState(() => _submitting = true);
    try {
      await context.read<QuarantineProvider>().addNote(record.id, text);
      if (!mounted) return;
      _showSnack('تم تسجيل الملاحظة الصحية', success: true);
    } catch (e) {
      if (!mounted) return;
      _showSnack(e.toString());
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  Future<void> _openVaccineSheet(QuarantineRecord record) async {
    final input = await QuarantineVaccineSheet.show(context);
    if (input == null || !mounted) return;

    setState(() => _submitting = true);
    try {
      await context.read<QuarantineProvider>().addVaccine(
            caseNumber: record.id,
            name: input.name,
            administeredAt: input.date,
            note: input.note,
          );
      if (!mounted) return;
      _showSnack('تم تسجيل الجرعة الوقائية', success: true);
    } catch (e) {
      if (!mounted) return;
      _showSnack(e.toString());
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  String _doctorLabel(String? author) {
    if (author == null || author.isEmpty) return '';
    if (author.contains('د.')) return 'الطبيب: $author';
    return 'الطبيب: د. $author';
  }

  void _showSnack(String message, {bool success = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          message.replaceFirst('Exception: ', ''),
          style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
        ),
        backgroundColor: success ? AppColors.primary : null,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final record = context.watch<QuarantineProvider>().findById(widget.recordId);
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    if (_loading && record == null) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    if (record == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('تفاصيل الحيوان')),
        body: const Center(child: Text('السجل غير موجود')),
      );
    }

    return Scaffold(
      backgroundColor: DoctorUi.background,
      body: SafeArea(
        top: false,
        bottom: false,
        child: Stack(
          children: [
            RefreshIndicator(
              onRefresh: _load,
              color: AppColors.primary,
              child: CustomScrollView(
                physics: const AlwaysScrollableScrollPhysics(
                  parent: BouncingScrollPhysics(),
                ),
                slivers: [
                SliverToBoxAdapter(
                  child: AnnotatedRegion<SystemUiOverlayStyle>(
                    value: SystemUiOverlayStyle.dark,
                    child: Container(
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.vertical(
                          bottom: Radius.circular(24),
                        ),
                        border: Border(
                          bottom: BorderSide(color: DoctorUi.border, width: 1.5),
                        ),
                      ),
                      padding: EdgeInsets.fromLTRB(8, topPad + 4, 16, 16),
                      child: Row(
                        children: [
                          IconButton(
                            onPressed: () => context.pop(),
                            icon: const Icon(
                              Icons.arrow_forward_ios_rounded,
                              color: DoctorUi.textPrimary,
                              size: 18,
                            ),
                          ),
                          Expanded(
                            child: Text(
                              'تفاصيل الحيوان',
                              style: GoogleFonts.cairo(
                                fontSize: 18,
                                fontWeight: FontWeight.w900,
                                color: DoctorUi.textPrimary,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                SliverToBoxAdapter(
                  child: Container(
                    margin: const EdgeInsets.only(top: 8),
                    color: Colors.transparent,
                    padding: const EdgeInsets.fromLTRB(20, 14, 20, 14),
                    child: Row(
                      children: [
                        Expanded(
                          child: Text(
                            record.animalName,
                            style: GoogleFonts.cairo(
                              fontSize: 20,
                              fontWeight: FontWeight.w900,
                              color: DoctorUi.textPrimary,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                SliverPadding(
                  padding: EdgeInsets.fromLTRB(16, 12, 16, bottomPad + 24),
                  sliver: SliverList(
                    delegate: SliverChildListDelegate([
                      _SectionCard(
                        title: 'بيانات الحيوان داخل الحجر',
                        children: [
                          _DetailRow(
                            label: 'الرقم المؤقت',
                            value: record.tempNumber,
                          ),
                          _DetailRow(
                            label: 'نوع الحيوان',
                            value: record.species ?? record.animalName,
                          ),
                          _DetailRow(label: 'الجنس', value: record.gender),
                          if (record.approximateAge != null)
                            _DetailRow(
                              label: 'العمر التقريبي',
                              value: record.approximateAge!,
                            ),
                          _DetailRow(
                            label: 'المجموعة المتوقعة',
                            value: record.expectedGroup,
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      _SectionCard(
                        title: 'بيانات الحجر',
                        children: [
                          _DetailRow(
                            label: 'تاريخ دخول الحجر',
                            value: formatQuarantineDate(record.entryDate),
                          ),
                          _DetailRow(
                            label: 'حالة الحجر',
                            valueWidget: Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 4,
                              ),
                              decoration: BoxDecoration(
                                color: const Color(0xFFFFF7ED),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Text(
                                record.status.label,
                                style: GoogleFonts.cairo(
                                  fontSize: 11.5,
                                  fontWeight: FontWeight.w800,
                                  color: const Color(0xFFB45309),
                                ),
                              ),
                            ),
                          ),
                          _DetailRow(
                            label: 'مدة الحجر',
                            value: '${record.durationDays} يوماً',
                          ),
                          _DetailRow(
                            label: 'الطبيب المسؤول',
                            value: record.responsibleDoctor,
                          ),
                          if (record.initialHealthStatus != null)
                            _DetailRow(
                              label: 'الحالة الصحية الأولية',
                              value: record.initialHealthStatus!,
                            ),
                          if (record.generalNotes != null)
                            _DetailRow(
                              label: 'ملاحظات عامة',
                              value: record.generalNotes!,
                            ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      if (record.preventiveVaccines.isNotEmpty)
                        _SectionCard(
                          title: 'الجرعات الوقائية',
                          children: record.preventiveVaccines
                              .map(
                                (vaccine) => Container(
                                  width: double.infinity,
                                  margin: const EdgeInsets.only(bottom: 8),
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFEFF6FF),
                                    borderRadius: BorderRadius.circular(10),
                                    border:
                                        Border.all(color: DoctorUi.border),
                                  ),
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        formatQuarantineDate(vaccine.date),
                                        style: GoogleFonts.cairo(
                                          fontSize: 11.5,
                                          fontWeight: FontWeight.w700,
                                          color: DoctorUi.muted,
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        vaccine.name,
                                        style: GoogleFonts.cairo(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w800,
                                          color: AppColors.primaryDark,
                                        ),
                                      ),
                                      if (vaccine.note != null) ...[
                                        const SizedBox(height: 4),
                                        Text(
                                          vaccine.note!,
                                          style: GoogleFonts.cairo(
                                            fontSize: 13,
                                            fontWeight: FontWeight.w600,
                                            color: DoctorUi.textPrimary,
                                          ),
                                        ),
                                      ],
                                    ],
                                  ),
                                ),
                              )
                              .toList(),
                        ),
                      if (record.preventiveVaccines.isNotEmpty)
                        const SizedBox(height: 12),
                      _SectionCard(
                        title: 'الملاحظات الصحية',
                        children: [
                          if (record.healthNotes.isEmpty)
                            Text(
                              'لا توجد ملاحظات صحية مسجلة',
                              style: GoogleFonts.cairo(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: DoctorUi.muted,
                              ),
                            )
                          else
                            ...record.healthNotes.map(
                              (note) => Container(
                                width: double.infinity,
                                margin: const EdgeInsets.only(bottom: 8),
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF8FAFC),
                                  borderRadius: BorderRadius.circular(10),
                                  border: Border.all(color: DoctorUi.border),
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Align(
                                      alignment: AlignmentDirectional.centerEnd,
                                      child: Text(
                                        formatQuarantineDate(note.date),
                                        style: GoogleFonts.cairo(
                                          fontSize: 11.5,
                                          fontWeight: FontWeight.w700,
                                          color: DoctorUi.muted,
                                        ),
                                      ),
                                    ),
                                    const SizedBox(height: 6),
                                    Text(
                                      note.text,
                                      style: GoogleFonts.cairo(
                                        fontSize: 13,
                                        fontWeight: FontWeight.w700,
                                        color: DoctorUi.textPrimary,
                                        height: 1.4,
                                      ),
                                    ),
                                    if (note.author != null) ...[
                                      const SizedBox(height: 6),
                                      Align(
                                        alignment:
                                            AlignmentDirectional.centerEnd,
                                        child: Text(
                                          _doctorLabel(note.author),
                                          style: GoogleFonts.cairo(
                                            fontSize: 11.5,
                                            fontWeight: FontWeight.w600,
                                            color: DoctorUi.muted,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ],
                                ),
                              ),
                            ),
                          if (record.canManage) ...[
                            const SizedBox(height: 14),
                            SizedBox(
                              width: double.infinity,
                              child: FilledButton.icon(
                                onPressed: _submitting
                                    ? null
                                    : () => _openVaccineSheet(record),
                                style: FilledButton.styleFrom(
                                  backgroundColor: AppColors.primaryDark,
                                  padding: const EdgeInsets.symmetric(
                                    vertical: 14,
                                  ),
                                ),
                                icon: const Icon(Icons.vaccines_outlined),
                                label: Text(
                                  'إضافة جرعة وقائية',
                                  style: GoogleFonts.cairo(
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(height: 10),
                            SizedBox(
                              width: double.infinity,
                              child: OutlinedButton.icon(
                                onPressed: _submitting
                                    ? null
                                    : () => _openNoteSheet(record),
                                style: OutlinedButton.styleFrom(
                                  foregroundColor: AppColors.primaryDark,
                                  side: BorderSide(
                                    color: AppColors.primary.withValues(
                                      alpha: 0.35,
                                    ),
                                  ),
                                  backgroundColor: const Color(0xFFF0F4F0),
                                  padding: const EdgeInsets.symmetric(
                                    vertical: 14,
                                  ),
                                ),
                                icon: const Icon(Icons.edit_note_outlined),
                                label: Text(
                                  'تسجيل ملاحظة صحية',
                                  style: GoogleFonts.cairo(
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ],
                      ),
                      SizedBox(height: bottomPad + 24),
                    ]),
                  ),
                ),
              ],
            ),
            ),
            if (_submitting)
              const ColoredBox(
                color: Color(0x33000000),
                child: Center(child: CircularProgressIndicator()),
              ),
          ],
        ),
      ),
    );
  }
}

class _SectionCard extends StatelessWidget {
  const _SectionCard({required this.title, required this.children});

  final String title;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE5E7EB)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            title,
            style: GoogleFonts.cairo(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: AppColors.primaryDark,
            ),
          ),
          const SizedBox(height: 12),
          ...children,
        ],
      ),
    );
  }
}

class _DetailRow extends StatelessWidget {
  const _DetailRow({
    required this.label,
    this.value,
    this.valueWidget,
  });

  final String label;
  final String? value;
  final Widget? valueWidget;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Align(
              alignment: AlignmentDirectional.centerStart,
              child: valueWidget ??
                  Text(
                    value ?? '',
                    textAlign: TextAlign.start,
                    style: GoogleFonts.cairo(
                      fontSize: 13.5,
                      fontWeight: FontWeight.w700,
                      color: DoctorUi.textPrimary,
                      height: 1.35,
                    ),
                  ),
            ),
          ),
          const SizedBox(width: 12),
          Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: DoctorUi.muted,
            ),
          ),
        ],
      ),
    );
  }
}
