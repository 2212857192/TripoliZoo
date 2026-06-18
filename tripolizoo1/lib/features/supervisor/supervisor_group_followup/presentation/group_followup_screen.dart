import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_launcher.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_type.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_ui.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/widgets/follow_up_date_filter.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/widgets/follow_up_entry_card.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/widgets/group_registration_grid.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class GroupFollowupScreen extends StatefulWidget {
  const GroupFollowupScreen({super.key});

  @override
  State<GroupFollowupScreen> createState() => _GroupFollowupScreenState();
}

class _GroupFollowupScreenState extends State<GroupFollowupScreen> {

  FollowUpDatePreset _preset = FollowUpDatePreset.today;
  DateTime? _customDate;

  DateTime get _selectedDate {
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    return switch (_preset) {
      FollowUpDatePreset.today => today,
      FollowUpDatePreset.yesterday =>
        today.subtract(const Duration(days: 1)),
      FollowUpDatePreset.custom =>
        _customDate != null
            ? DateTime(
                _customDate!.year,
                _customDate!.month,
                _customDate!.day,
              )
            : today,
    };
  }

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _reloadEntries());
  }

  Future<void> _reloadEntries() async {
    if (!mounted) return;
    await context.read<FollowUpProvider>().loadForDate(_selectedDate);
  }

  Future<void> _openForm(BuildContext context, SupervisorFormType type) async {
    final saved = await openSupervisorForm(context, type);
    if (!context.mounted || saved != true) return;
    setState(() => _preset = FollowUpDatePreset.today);
    await _reloadEntries();
    if (!context.mounted) return;
    showSupervisorSuccessSnackBar(
      context,
      message: 'تم تسجيل الحالة وإرسالها لقسم الرعاية',
    );
  }

  void _onDateFilterChanged() {
    _reloadEntries();
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final provider = context.watch<FollowUpProvider>();
    final entries = provider.entries;

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: SupervisorUi.background,
        body: SafeArea(
          top: false,
          bottom: false,
          child: RefreshIndicator(
            color: AppColors.primary,
            onRefresh: _reloadEntries,
            child: CustomScrollView(
              physics: const AlwaysScrollableScrollPhysics(
                parent: BouncingScrollPhysics(),
              ),
              slivers: [
                // ── Premium Header ──
                SliverToBoxAdapter(
                  child: AnnotatedRegion<SystemUiOverlayStyle>(
                    value: SystemUiOverlayStyle.dark,
                    child: Container(
                      padding: EdgeInsets.fromLTRB(20, topPad + 18, 20, 20),
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.vertical(
                          bottom: Radius.circular(28),
                        ),
                        border: Border(
                          bottom: BorderSide(color: SupervisorUi.border, width: 1.5),
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: Color(0x0D142E1B),
                            blurRadius: 16,
                            offset: Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          Text(
                            'متابعة المجموعة',
                            style: GoogleFonts.cairo(
                              fontSize: 22,
                              fontWeight: FontWeight.w900,
                              color: SupervisorUi.textPrimary,
                              height: 1.2,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'سجّل الأحداث والملاحظات الخاصة بمجموعتك وطاقم العمل',
                            style: GoogleFonts.cairo(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: SupervisorUi.muted,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 18, 20, 0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        GroupRegistrationGrid(
                          actions: [
                            GroupRegistrationAction(
                              icon: Icons.medical_services_outlined,
                              label: 'تسجيل حالة صحية',
                              onTap: () => _openForm(
                                context,
                                SupervisorFormType.health,
                              ),
                            ),
                            GroupRegistrationAction(
                              icon: Icons.child_care_outlined,
                              label: 'تسجيل ولادة جديدة',
                              onTap: () => _openForm(
                                context,
                                SupervisorFormType.birth,
                              ),
                            ),
                            GroupRegistrationAction(
                              icon: Icons.edit_note_outlined,
                              label: 'تسجيل ملاحظة تشغيلية',
                              onTap: () => _openForm(
                                context,
                                SupervisorFormType.operationalNote,
                              ),
                            ),
                            GroupRegistrationAction(
                              icon: Icons.heart_broken_outlined,
                              label: 'تسجيل حالة نفوق',
                              onTap: () => _openForm(
                                context,
                                SupervisorFormType.mortality,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 24),
                        Text(
                          'سجل المتابعة',
                          style: GoogleFonts.cairo(
                            fontSize: 17,
                            fontWeight: FontWeight.w800,
                            color: SupervisorUi.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 12),
                        FollowUpDateFilter(
                          preset: _preset,
                          customDate: _customDate,
                          onPresetChanged: (value) {
                            setState(() => _preset = value);
                            _onDateFilterChanged();
                          },
                          onCustomDatePicked: (date) {
                            setState(() {
                              _preset = FollowUpDatePreset.custom;
                              _customDate = date;
                            });
                            _onDateFilterChanged();
                          },
                        ),
                        const SizedBox(height: 14),
                      ],
                    ),
                  ),
                ),
                if (provider.isLoading && entries.isEmpty)
                  const SliverFillRemaining(
                    hasScrollBody: false,
                    child: Center(
                      child: CircularProgressIndicator(color: AppColors.primary),
                    ),
                  )
                else if (provider.error != null && entries.isEmpty)
                  SliverFillRemaining(
                    hasScrollBody: false,
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 20),
                      child: _ErrorLogState(
                        message: provider.error!,
                        onRetry: _reloadEntries,
                      ),
                    ),
                  )
                else if (entries.isEmpty)
                  SliverFillRemaining(
                    hasScrollBody: false,
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 20),
                      child: _EmptyLogState(),
                    ),
                  )
                else
                  SliverPadding(
                    padding: EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 88),
                    sliver: SliverList.separated(
                      itemCount: entries.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 10),
                      itemBuilder: (context, index) =>
                          FollowUpEntryCard(entry: entries[index]),
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _EmptyLogState extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: const BoxDecoration(
              color: Color(0xFFE8F5E9),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.fact_check_outlined,
              color: AppColors.primary,
              size: 26,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            'لا توجد تسجيلات في هذا اليوم',
            style: GoogleFonts.cairo(
              fontSize: 14,
              fontWeight: FontWeight.w700,
              color: const Color(0xFF6B7280),
            ),
          ),
        ],
      ),
    );
  }
}

class _ErrorLogState extends StatelessWidget {
  const _ErrorLogState({
    required this.message,
    required this.onRetry,
  });

  final String message;
  final Future<void> Function() onRetry;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(
            Icons.cloud_off_outlined,
            color: Color(0xFF9CA3AF),
            size: 40,
          ),
          const SizedBox(height: 12),
          Text(
            message,
            textAlign: TextAlign.center,
            style: GoogleFonts.cairo(
              fontSize: 14,
              fontWeight: FontWeight.w700,
              color: const Color(0xFF6B7280),
            ),
          ),
          const SizedBox(height: 12),
          TextButton(
            onPressed: onRetry,
            child: Text(
              'إعادة المحاولة',
              style: GoogleFonts.cairo(
                fontSize: 14,
                fontWeight: FontWeight.w800,
                color: AppColors.primary,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
