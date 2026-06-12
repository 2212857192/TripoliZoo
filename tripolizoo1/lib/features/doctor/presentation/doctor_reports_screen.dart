import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/domain/health_report.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_reports_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/widgets/follow_up_time_label.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorReportsScreen extends StatefulWidget {
  const DoctorReportsScreen({super.key});

  @override
  State<DoctorReportsScreen> createState() => _DoctorReportsScreenState();
}

class _DoctorReportsScreenState extends State<DoctorReportsScreen>
    with SingleTickerProviderStateMixin {
  late final TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  void _openReportDetail(BuildContext context, HealthReport report) {
    // 1. Mark report as received automatically upon opening
    final provider = context.read<HealthReportsProvider>();
    provider.markAsReceived(report.id, 'د. أحمد');

    // 2. Open details sheet
    showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      barrierColor: Colors.black.withValues(alpha: 0.45),
      useSafeArea: true,
      builder: (ctx) => MultiProvider(
        providers: [
          ChangeNotifierProvider.value(value: provider),
        ],
        child: _DoctorReportDetailSheet(reportId: report.id),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: DoctorUi.background,
      body: SafeArea(
        top: false,
        bottom: false,
        child: Column(
          children: [
            // Top App Bar Area
            Container(
              padding: EdgeInsets.fromLTRB(16, topPad + 16, 16, 10),
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(
                  bottom: Radius.circular(24),
                ),
                border: Border(
                  bottom: BorderSide(color: DoctorUi.border, width: 1.5),
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Text(
                    'البلاغات الصحية',
                    style: GoogleFonts.cairo(
                      fontSize: 20,
                      fontWeight: FontWeight.w900,
                      color: DoctorUi.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'البلاغات والتنبيهات الصحية الواردة من مشرفي المجموعات',
                    style: GoogleFonts.cairo(
                      fontSize: 12.5,
                      fontWeight: FontWeight.w600,
                      color: DoctorUi.muted,
                    ),
                  ),
                  const SizedBox(height: 16),
                  TabBar(
                    controller: _tabController,
                    indicatorColor: AppColors.primary,
                    indicatorSize: TabBarIndicatorSize.tab,
                    labelColor: AppColors.primary,
                    unselectedLabelColor: DoctorUi.muted,
                    labelStyle: GoogleFonts.cairo(
                      fontWeight: FontWeight.w800,
                      fontSize: 13,
                    ),
                    unselectedLabelStyle: GoogleFonts.cairo(
                      fontWeight: FontWeight.w700,
                      fontSize: 13,
                    ),
                    dividerColor: Colors.transparent,
                    tabs: const [
                      Tab(text: 'الكل'),
                      Tab(text: 'جديد'),
                      Tab(text: 'تم الاطلاع'),
                      Tab(text: 'مغلق'),
                    ],
                  ),
                ],
              ),
            ),

            // Tab Views List
            Expanded(
              child: Consumer<HealthReportsProvider>(
                builder: (context, reportsProvider, child) {
                  final allReports = reportsProvider.allReports;

                  return TabBarView(
                    controller: _tabController,
                    children: [
                      _buildReportsList(allReports),
                      _buildReportsList(allReports
                          .where((r) => r.status == HealthReportStatus.sent)
                          .toList()),
                      _buildReportsList(allReports
                          .where((r) => r.status == HealthReportStatus.received)
                          .toList()),
                      _buildReportsList(allReports
                          .where((r) => r.status == HealthReportStatus.closed)
                          .toList()),
                    ],
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildReportsList(List<HealthReport> reports) {
    final bottomPad = MediaQuery.of(context).padding.bottom;

    if (reports.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.assignment_outlined,
              size: 44,
              color: DoctorUi.muted.withValues(alpha: 0.4),
            ),
            const SizedBox(height: 12),
            Text(
              'لا توجد بلاغات صحية في هذا التبويب',
              style: GoogleFonts.cairo(
                fontSize: 13.5,
                fontWeight: FontWeight.w700,
                color: DoctorUi.muted,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.separated(
      physics: const BouncingScrollPhysics(),
      padding: EdgeInsets.fromLTRB(16, 16, 16, bottomPad + 96),
      itemCount: reports.length,
      separatorBuilder: (_, __) => const SizedBox(height: 12),
      itemBuilder: (context, index) {
        final report = reports[index];
        return _DoctorReportCard(
          report: report,
          onTap: () => _openReportDetail(context, report),
        );
      },
    );
  }
}

class _DoctorReportCard extends StatelessWidget {
  const _DoctorReportCard({required this.report, required this.onTap});

  final HealthReport report;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    Color statusColor = AppColors.primary;
    Color statusBg = AppColors.primary.withValues(alpha: 0.08);
    String statusText = 'جديد';

    if (report.status == HealthReportStatus.received) {
      statusColor = const Color(0xFFE2A014);
      statusBg = const Color(0xFFFEF9EC);
      statusText = 'تم الاطلاع';
    } else if (report.status == HealthReportStatus.closed) {
      statusColor = DoctorUi.muted;
      statusBg = DoctorUi.border;
      statusText = 'مغلق';
    }

    return Container(
      decoration: DoctorUi.cardDecoration(),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(DoctorUi.cardRadius),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: statusBg,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        statusText,
                        style: GoogleFonts.cairo(
                          fontSize: 11,
                          fontWeight: FontWeight.w800,
                          color: statusColor,
                        ),
                      ),
                    ),
                    Text(
                      report.reportNumber,
                      style: GoogleFonts.cairo(
                        fontSize: 12.5,
                        fontWeight: FontWeight.w700,
                        color: DoctorUi.muted,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Icon(
                      Icons.pets_rounded,
                      color: AppColors.primary,
                      size: 18,
                    ),
                    const SizedBox(width: 8),
                    Text(
                      report.animalLabel,
                      style: GoogleFonts.cairo(
                        fontSize: 14.5,
                        fontWeight: FontWeight.w800,
                        color: DoctorUi.textPrimary,
                      ),
                    ),
                    const Spacer(),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 3,
                      ),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF4F7F4),
                        borderRadius: BorderRadius.circular(6),
                        border: Border.all(color: DoctorUi.border, width: 1),
                      ),
                      child: Text(
                        report.groupName,
                        style: GoogleFonts.cairo(
                          fontSize: 10.5,
                          fontWeight: FontWeight.w700,
                          color: DoctorUi.textSecondary,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Text(
                  report.description,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.cairo(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: DoctorUi.textSecondary,
                    height: 1.45,
                  ),
                ),
                const SizedBox(height: 14),
                const Divider(color: DoctorUi.border, height: 1),
                const SizedBox(height: 10),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(
                          Icons.access_time_rounded,
                          size: 14,
                          color: DoctorUi.muted,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          formatFollowUpTime(report.sentAt),
                          style: GoogleFonts.cairo(
                            fontSize: 11.5,
                            fontWeight: FontWeight.w600,
                            color: DoctorUi.muted,
                          ),
                        ),
                      ],
                    ),
                    Row(
                      children: [
                        Text(
                          'عرض التفاصيل',
                          style: GoogleFonts.cairo(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: AppColors.primary,
                          ),
                        ),
                        const SizedBox(width: 2),
                        Icon(
                          Icons.chevron_left_rounded,
                          color: AppColors.primary,
                          size: 16,
                        ),
                      ],
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _DoctorReportDetailSheet extends StatefulWidget {
  const _DoctorReportDetailSheet({required this.reportId});

  final String reportId;

  @override
  State<_DoctorReportDetailSheet> createState() =>
      _DoctorReportDetailSheetState();
}

class _DoctorReportDetailSheetState extends State<_DoctorReportDetailSheet> {
  final _noteController = TextEditingController();
  bool _isClosingFormVisible = false;

  @override
  void dispose() {
    _noteController.dispose();
    super.dispose();
  }

  void _submitClose(BuildContext context, HealthReport report) {
    final note = _noteController.text.trim();
    if (note.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('الرجاء إدخال ملاحظات أو توصية الإغلاق')),
      );
      return;
    }

    context.read<HealthReportsProvider>().closeReport(
          report.id,
          note: note,
          doctorName: 'د. أحمد',
        );

    Navigator.of(context).pop();
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('تم إغلاق البلاغ بنجاح')),
    );
  }

  @override
  Widget build(BuildContext context) {
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final reportsProvider = context.watch<HealthReportsProvider>();
    final report = reportsProvider.findById(widget.reportId);

    if (report == null) {
      return Container(
        height: 200,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: const Center(child: CircularProgressIndicator()),
      );
    }

    final sheetHeight = MediaQuery.sizeOf(context).height * 0.85;

    return Directionality(
      textDirection: TextDirection.rtl,
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
                        fontWeight: FontWeight.w900,
                        color: DoctorUi.textPrimary,
                      ),
                    ),
                  ),
                  Material(
                    color: AppColors.primary,
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
                padding: EdgeInsets.fromLTRB(20, 8, 20, bottomPad + 30),
                children: [
                  // Info Card
                  _buildSectionCard(
                    title: 'معلومات البلاغ والحيوان',
                    children: [
                      _buildDetailRow('رقم البلاغ', report.reportNumber),
                      _buildDetailRow('نوع الحيوان ID', report.animalLabel),
                      _buildDetailRow('المجموعة المشرفة', report.groupName),
                      _buildDetailRow(
                          'تاريخ الإرسال', formatFollowUpTime(report.sentAt)),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Description Card
                  _buildSectionCard(
                    title: 'وصف البلاغ الصحي',
                    children: [
                      Text(
                        report.description,
                        style: GoogleFonts.cairo(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: DoctorUi.textSecondary,
                          height: 1.55,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Attachments Card
                  _buildSectionCard(
                    title: 'المرفقات المرفوعة',
                    children: [
                      if (report.hasAttachment)
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: const Color(0xFFE8F5E9),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: const Color(0xFFC8E6C9)),
                          ),
                          child: Row(
                            children: [
                              Icon(
                                Icons.image_outlined,
                                color: AppColors.primary,
                                size: 22,
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  'صورة مرفقة مع البلاغ من المشرف',
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
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: DoctorUi.muted,
                          ),
                        ),
                    ],
                  ),

                  // If closed, show doctor note
                  if (report.status == HealthReportStatus.closed) ...[
                    const SizedBox(height: 12),
                    _buildSectionCard(
                      title: 'توصية الطبيب عند الإغلاق',
                      children: [
                        Text(
                          report.doctorNote ?? 'لا توجد ملاحظات.',
                          style: GoogleFonts.cairo(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: DoctorUi.textSecondary,
                            height: 1.5,
                          ),
                        ),
                        const SizedBox(height: 8),
                        if (report.doctorUpdatedAt != null)
                          Text(
                            'أُغلق في: ${formatFollowUpTime(report.doctorUpdatedAt!)}',
                            style: GoogleFonts.cairo(
                              fontSize: 11.5,
                              fontWeight: FontWeight.w600,
                              color: DoctorUi.muted,
                            ),
                          ),
                      ],
                    ),
                  ],

                  // If not closed, show closing action
                  if (report.status != HealthReportStatus.closed) ...[
                    const SizedBox(height: 20),
                    if (!_isClosingFormVisible)
                      Material(
                        color: AppColors.primary,
                        borderRadius: BorderRadius.circular(14),
                        child: InkWell(
                          onTap: () {
                            setState(() {
                              _isClosingFormVisible = true;
                            });
                          },
                          borderRadius: BorderRadius.circular(14),
                          child: Container(
                            height: 48,
                            alignment: Alignment.center,
                            child: Text(
                              'إغلاق البلاغ',
                              style: GoogleFonts.cairo(
                                fontSize: 14,
                                fontWeight: FontWeight.w800,
                                color: Colors.white,
                              ),
                            ),
                          ),
                        ),
                      )
                    else ...[
                      Text(
                        'ملاحظات وتوصية الإغلاق:',
                        style: GoogleFonts.cairo(
                          fontSize: 13.5,
                          fontWeight: FontWeight.w800,
                          color: DoctorUi.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 8),
                      TextField(
                        controller: _noteController,
                        maxLines: 3,
                        style: GoogleFonts.cairo(
                          fontSize: 13.5,
                          color: DoctorUi.textPrimary,
                          fontWeight: FontWeight.w600,
                        ),
                        decoration: InputDecoration(
                          hintText: 'اكتب هنا توصيتك الطبية أو تفاصيل المتابعة لإغلاق البلاغ...',
                          hintStyle: GoogleFonts.cairo(
                            fontSize: 12.5,
                            color: DoctorUi.muted,
                          ),
                          filled: true,
                          fillColor: const Color(0xFFF4F7F4),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: const BorderSide(color: DoctorUi.border),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: const BorderSide(color: DoctorUi.border),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: AppColors.primary, width: 1.5),
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          Expanded(
                            child: Material(
                              color: AppColors.primary,
                              borderRadius: BorderRadius.circular(10),
                              child: InkWell(
                                onTap: () => _submitClose(context, report),
                                borderRadius: BorderRadius.circular(10),
                                child: Container(
                                  height: 40,
                                  alignment: Alignment.center,
                                  child: Text(
                                    'تأكيد الإغلاق',
                                    style: GoogleFonts.cairo(
                                      fontSize: 13,
                                      fontWeight: FontWeight.w800,
                                      color: Colors.white,
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: OutlinedButton(
                              onPressed: () {
                                setState(() {
                                  _isClosingFormVisible = false;
                                });
                              },
                              style: OutlinedButton.styleFrom(
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                side: const BorderSide(color: DoctorUi.border),
                              ),
                              child: Text(
                                'إلغاء',
                                style: GoogleFonts.cairo(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w800,
                                  color: DoctorUi.textSecondary,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionCard({
    required String title,
    required List<Widget> children,
  }) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: DoctorUi.cardDecoration(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            title,
            style: GoogleFonts.cairo(
              fontSize: 13.5,
              fontWeight: FontWeight.w800,
              color: AppColors.primaryDark,
            ),
          ),
          const SizedBox(height: 10),
          ...children,
        ],
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 12.5,
              fontWeight: FontWeight.w600,
              color: DoctorUi.muted,
            ),
          ),
          Text(
            value,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: FontWeight.w700,
              color: DoctorUi.textPrimary,
            ),
          ),
        ],
      ),
    );
  }
}
