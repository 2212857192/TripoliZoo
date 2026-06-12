import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/widgets/follow_up_time_label.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/domain/health_report.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/widgets/health_report_status_badge.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

/// تفاصيل البلاغ — bottom sheet للعرض فقط.
class HealthReportDetailSheet extends StatelessWidget {
  const HealthReportDetailSheet({super.key, required this.report});

  final HealthReport report;

  static Future<void> show(BuildContext context, HealthReport report) {
    return showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      barrierColor: Colors.black.withValues(alpha: 0.45),
      useSafeArea: true,
      builder: (ctx) => HealthReportDetailSheet(report: report),
    );
  }

  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  @override
  Widget build(BuildContext context) {
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final sheetHeight = MediaQuery.sizeOf(context).height * 0.9;

    return Padding(
      padding:
          EdgeInsets.only(bottom: MediaQuery.viewInsetsOf(context).bottom),
      child: Align(
        alignment: Alignment.bottomCenter,
        child: Container(
          height: sheetHeight,
          width: double.infinity,
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Column(
            children: [
              const SizedBox(height: 10),
              Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: const Color(0xFFE2E8E2),
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 12, 12, 0),
                child: Row(
                  children: [
                    Expanded(
                      child: Text(
                        'تفاصيل البلاغ ${report.reportNumber}',
                        style: GoogleFonts.cairo(
                          fontSize: 17,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF1A1A1A),
                        ),
                      ),
                    ),
                    Material(
                      color: AppColors.primaryDark,
                      shape: const CircleBorder(),
                      child: InkWell(
                        onTap: () => Navigator.of(context).pop(),
                        customBorder: const CircleBorder(),
                        child: const Padding(
                          padding: EdgeInsets.all(8),
                          child: Icon(
                            Icons.close_rounded,
                            color: Colors.white,
                            size: 20,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 8),
              Expanded(
                child: ListView(
                  physics: const BouncingScrollPhysics(),
                  padding: EdgeInsets.fromLTRB(20, 8, 20, bottomPad + 20),
                  children: [
                    _SectionCard(
                      title: 'بيانات البلاغ',
                      children: [
                        _DetailRow(
                          label: 'رقم البلاغ',
                          value: report.reportNumber,
                        ),
                        _DetailRow(
                          label: 'حالة البلاغ',
                          valueWidget:
                              HealthReportStatusBadge(status: report.status),
                        ),
                        _DetailRow(
                          label: 'تاريخ الإرسال',
                          value: formatFollowUpTime(report.sentAt),
                        ),
                        if (report.assignedDoctorName != null)
                          _DetailRow(
                            label: 'الطبيب المسؤول',
                            value: report.assignedDoctorName!,
                          ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    _SectionCard(
                      title: 'بيانات الحيوان',
                      children: [
                        _DetailRow(
                          label: 'رقم الحيوان',
                          value: report.animalId,
                        ),
                        _DetailRow(
                          label: 'نوع الحيوان',
                          value: report.animalType,
                        ),
                        _DetailRow(
                          label: 'المجموعة',
                          value: report.groupName,
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    _SectionCard(
                      title: 'وصف البلاغ',
                      children: [
                        Text(
                          report.description,
                          style: GoogleFonts.cairo(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: const Color(0xFF374151),
                            height: 1.55,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    _SectionCard(
                      title: 'المرفقات',
                      children: [
                        if (report.hasAttachment)
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: const Color(0xFFE8F5E9),
                              borderRadius: BorderRadius.circular(12),
                              border:
                                  Border.all(color: const Color(0xFFC8E6C9)),
                            ),
                            child: Row(
                              children: [
                                const Icon(
                                  Icons.image_outlined,
                                  color: AppColors.primary,
                                  size: 22,
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Text(
                                    'مرفق مرفوع مع البلاغ',
                                    style: GoogleFonts.cairo(
                                      fontSize: 13,
                                      fontWeight: FontWeight.w700,
                                      color: AppColors.primaryDark,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          )
                        else
                          Text(
                            'لا توجد مرفقات.',
                            style: GoogleFonts.cairo(
                              fontSize: 13.5,
                              fontWeight: FontWeight.w600,
                              color: _muted,
                            ),
                          ),
                      ],
                    ),
                    if (report.hasDoctorFollowUp) ...[
                      const SizedBox(height: 12),
                      _DoctorFollowUpCard(report: report),
                    ],
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _DoctorFollowUpCard extends StatelessWidget {
  const _DoctorFollowUpCard({required this.report});

  final HealthReport report;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: HealthReportDetailSheet._border),
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Text(
                  'متابعة الطبيب',
                  style: GoogleFonts.cairo(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                    color: AppColors.primaryDark,
                  ),
                ),
                if (report.doctorUpdatedAt != null) ...[
                  const SizedBox(height: 12),
                  _DetailRow(
                    label: 'آخر تحديث',
                    value: formatFollowUpTime(report.doctorUpdatedAt!),
                  ),
                ],
                if (report.doctorNote != null &&
                    report.doctorNote!.trim().isNotEmpty) ...[
                  const SizedBox(height: 4),
                  Text(
                    report.doctorNote!,
                    style: GoogleFonts.cairo(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: const Color(0xFF374151),
                      height: 1.55,
                    ),
                  ),
                ],
              ],
            ),
          ),
          if (report.fieldCaseOpened) ...[
            const SizedBox(height: 14),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              color: const Color(0xFFE8F5E9),
              child: Text(
                'تم فتح حالة طبية ميدانية مرتبطة بهذا البلاغ.',
                style: GoogleFonts.cairo(
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                  color: AppColors.primaryDark,
                  height: 1.4,
                ),
              ),
            ),
          ] else
            const SizedBox(height: 16),
        ],
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
        border: Border.all(color: HealthReportDetailSheet._border),
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
        crossAxisAlignment: CrossAxisAlignment.center,
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
                      color: const Color(0xFF1A1A1A),
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
              color: HealthReportDetailSheet._muted,
            ),
          ),
        ],
      ),
    );
  }
}
