import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/domain/quarantine_record.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/quarantine_provider.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class QuarantineDetailScreen extends StatelessWidget {
  const QuarantineDetailScreen({super.key, required this.recordId});

  final String recordId;

  @override
  Widget build(BuildContext context) {
    final record = context.watch<QuarantineProvider>().findById(recordId);
    final topPad = MediaQuery.of(context).padding.top;

    if (record == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('تفاصيل الحيوان')),
        body: const Center(child: Text('السجل غير موجود')),
      );
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      body: SafeArea(
        top: false,
        bottom: true,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(
              child: AnnotatedRegion<SystemUiOverlayStyle>(
                value: SystemUiOverlayStyle.light,
                child: Container(
                  color: AppColors.primaryDark,
                  padding: EdgeInsets.fromLTRB(8, topPad + 4, 16, 16),
                  child: Row(
                    children: [
                      IconButton(
                        onPressed: () => context.pop(),
                        icon: const Icon(
                          Icons.arrow_forward_ios_rounded,
                          color: Colors.white,
                          size: 18,
                        ),
                      ),
                      Expanded(
                        child: Text(
                          'تفاصيل الحيوان',
                          style: GoogleFonts.cairo(
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
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
                color: Colors.white,
                padding: const EdgeInsets.fromLTRB(20, 14, 20, 14),
                child: Row(
                  children: [
                    Expanded(
                      child: Text(
                        record.animalName,
                        style: GoogleFonts.cairo(
                          fontSize: 18,
                          fontWeight: FontWeight.w900,
                          color: DoctorUi.textPrimary,
                        ),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF3F4F6),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        record.tempNumber,
                        style: GoogleFonts.cairo(
                          fontSize: 12,
                          fontWeight: FontWeight.w800,
                          color: DoctorUi.muted,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
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
                        value: record.animalName,
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
                      if (record.animalSource != null)
                        _DetailRow(
                          label: 'مصدر الحيوان',
                          value: record.animalSource!,
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
                      if (record.generalNotes != null)
                        _DetailRow(
                          label: 'ملاحظات عامة',
                          value: record.generalNotes!,
                        ),
                    ],
                  ),
                  if (record.lastVaccine != null) ...[
                    const SizedBox(height: 12),
                    _SectionCard(
                      title: 'الجرعات الوقائية',
                      children: [
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF0F4F0),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: DoctorUi.border),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              Text(
                                record.lastVaccine!.name,
                                style: GoogleFonts.cairo(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w800,
                                  color: AppColors.primaryDark,
                                ),
                              ),
                              const SizedBox(height: 6),
                              Text(
                                'التاريخ: ${formatQuarantineDate(record.lastVaccine!.date)}',
                                style: GoogleFonts.cairo(
                                  fontSize: 12.5,
                                  fontWeight: FontWeight.w600,
                                  color: DoctorUi.muted,
                                ),
                              ),
                              if (record.lastVaccine!.note != null)
                                Text(
                                  'ملاحظة: ${record.lastVaccine!.note}',
                                  style: GoogleFonts.cairo(
                                    fontSize: 12.5,
                                    fontWeight: FontWeight.w600,
                                    color: DoctorUi.muted,
                                  ),
                                ),
                              if (record.lastVaccine!.doctorName != null)
                                Text(
                                  'الطبيب: ${record.lastVaccine!.doctorName}',
                                  style: GoogleFonts.cairo(
                                    fontSize: 12.5,
                                    fontWeight: FontWeight.w600,
                                    color: DoctorUi.muted,
                                  ),
                                ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ]),
              ),
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
