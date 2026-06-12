import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_launcher.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_type.dart';
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
  static const _bg = Color(0xFFF5F5F5);
  static const _muted = Color(0xFF6B7280);

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

  Future<void> _openForm(BuildContext context, SupervisorFormType type) async {
    final saved = await openSupervisorForm(context, type);
    if (!context.mounted || saved != true) return;
    setState(() => _preset = FollowUpDatePreset.today);
    showSupervisorSuccessSnackBar(
      context,
      message: 'تم التسجيل بنجاح في سجل المتابعة',
    );
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final entries = context.watch<FollowUpProvider>().forDate(_selectedDate);

    return Scaffold(
      backgroundColor: _bg,
      body: SafeArea(
        top: false,
        bottom: false,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(
              child: Padding(
                padding: EdgeInsets.fromLTRB(20, topPad + 20, 20, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      'متابعة المجموعة',
                      style: GoogleFonts.cairo(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF1A1A1A),
                        height: 1.2,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'سجّل الأحداث والملاحظات الخاصة بمجموعتك.',
                      style: GoogleFonts.cairo(
                        fontSize: 13.5,
                        fontWeight: FontWeight.w500,
                        color: _muted,
                        height: 1.45,
                      ),
                    ),
                    const SizedBox(height: 20),
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
                        color: const Color(0xFF1A1A1A),
                      ),
                    ),
                    const SizedBox(height: 12),
                    FollowUpDateFilter(
                      preset: _preset,
                      customDate: _customDate,
                      onPresetChanged: (value) =>
                          setState(() => _preset = value),
                      onCustomDatePicked: (date) => setState(() {
                        _preset = FollowUpDatePreset.custom;
                        _customDate = date;
                      }),
                    ),
                    const SizedBox(height: 14),
                  ],
                ),
              ),
            ),
            if (entries.isEmpty)
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
            decoration: BoxDecoration(
              color: const Color(0xFFE8F5E9),
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
