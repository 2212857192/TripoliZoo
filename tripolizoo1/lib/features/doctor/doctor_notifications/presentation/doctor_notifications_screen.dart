import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/domain/doctor_notification.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/presentation/doctor_notifications_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/presentation/widgets/doctor_notification_card.dart';
import 'package:tripolizoo/features/doctor/presentation/doctor_dashboard_provider.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorNotificationsScreen extends StatefulWidget {
  const DoctorNotificationsScreen({super.key});

  @override
  State<DoctorNotificationsScreen> createState() =>
      _DoctorNotificationsScreenState();
}

class _DoctorNotificationsScreenState
    extends State<DoctorNotificationsScreen> {
  DoctorNotificationReadFilter _filter = DoctorNotificationReadFilter.all;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DoctorNotificationsProvider>().load();
    });
  }

  Future<void> _openNotification(DoctorNotification notification) async {
    final provider = context.read<DoctorNotificationsProvider>();
    await provider.markAsRead(notification);
    if (!mounted) return;

    context.read<DoctorDashboardProvider>().load();

    final route = notification.targetRoute;
    if (route != null) {
      context.pop();
      context.push(route);
    }
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final provider = context.watch<DoctorNotificationsProvider>();
    final items = provider.filtered(_filter);

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: DoctorUi.background,
        body: SafeArea(
          top: false,
          bottom: false,
          child: RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () => provider.load(),
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
                      padding:
                          EdgeInsets.fromLTRB(20, topPad + 18, 20, 18),
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.vertical(
                          bottom: Radius.circular(28),
                        ),
                        border: Border(
                          bottom: BorderSide(
                              color: DoctorUi.border, width: 1.5),
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: Color(0x0D142E1B),
                            blurRadius: 16,
                            offset: Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          // Back button
                          Material(
                            color: const Color(0xFFF4F7F4),
                            shape: const CircleBorder(
                              side: BorderSide(color: DoctorUi.border),
                            ),
                            child: InkWell(
                              onTap: () => context.pop(),
                              customBorder: const CircleBorder(),
                              child: const Padding(
                                padding: EdgeInsets.all(10),
                                child: Icon(
                                  Icons.arrow_forward_ios_rounded,
                                  size: 18,
                                  color: DoctorUi.textPrimary,
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 14),
                          // Title block
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'إشعارات الحجر',
                                  style: GoogleFonts.cairo(
                                    fontSize: 20,
                                    fontWeight: FontWeight.w900,
                                    color: DoctorUi.textPrimary,
                                    height: 1.2,
                                  ),
                                ),
                                const SizedBox(height: 3),
                                Text(
                                  provider.unreadCount > 0
                                      ? '${provider.unreadCount} غير مقروء'
                                      : 'لا توجد إشعارات جديدة',
                                  style: GoogleFonts.cairo(
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                    color: DoctorUi.muted,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          // Mark all read
                          if (provider.unreadCount > 0)
                            TextButton(
                              onPressed: () async {
                                await provider.markAllAsRead();
                                if (!context.mounted) return;
                                await context
                                    .read<DoctorDashboardProvider>()
                                    .load();
                              },
                              child: Text(
                                'قراءة الكل',
                                style: GoogleFonts.cairo(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w800,
                                  color: AppColors.primary,
                                ),
                              ),
                            ),
                        ],
                      ),
                    ),
                  ),
                ),

                // ── Filter Chips ──
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                    child: _FilterRow(
                      selected: _filter,
                      onChanged: (value) =>
                          setState(() => _filter = value),
                    ),
                  ),
                ),

                // ── List / States ──
                if (provider.isLoading && items.isEmpty)
                  const SliverFillRemaining(
                    child: Center(
                      child: CircularProgressIndicator(
                          color: AppColors.primary),
                    ),
                  )
                else if (provider.errorMessage != null && items.isEmpty)
                  SliverFillRemaining(
                    child: Center(
                      child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Text(
                          provider.errorMessage!,
                          textAlign: TextAlign.center,
                          style: GoogleFonts.cairo(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                            color: DoctorUi.muted,
                          ),
                        ),
                      ),
                    ),
                  )
                else if (items.isEmpty)
                  SliverFillRemaining(
                    child: Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.notifications_none_rounded,
                            size: 56,
                            color: DoctorUi.muted.withValues(alpha: 0.5),
                          ),
                          const SizedBox(height: 12),
                          Text(
                            'لا توجد إشعارات',
                            style: GoogleFonts.cairo(
                              fontSize: 14,
                              fontWeight: FontWeight.w700,
                              color: DoctorUi.muted,
                            ),
                          ),
                        ],
                      ),
                    ),
                  )
                else
                  SliverPadding(
                    padding: EdgeInsets.fromLTRB(16, 0, 16, bottomPad + 24),
                    sliver: SliverList.separated(
                      itemCount: items.length,
                      separatorBuilder: (_, __) =>
                          const SizedBox(height: 10),
                      itemBuilder: (context, index) {
                        final notification = items[index];
                        return DoctorNotificationCard(
                          notification: notification,
                          onTap: () => _openNotification(notification),
                        );
                      },
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

class _FilterRow extends StatelessWidget {
  const _FilterRow({
    required this.selected,
    required this.onChanged,
  });

  final DoctorNotificationReadFilter selected;
  final ValueChanged<DoctorNotificationReadFilter> onChanged;

  static const _labels = {
    DoctorNotificationReadFilter.all: 'الكل',
    DoctorNotificationReadFilter.unread: 'غير مقروء',
    DoctorNotificationReadFilter.read: 'مقروء',
  };

  @override
  Widget build(BuildContext context) {
    return Wrap(
      spacing: 8,
      children: _labels.entries.map((entry) {
        final isSelected = entry.key == selected;
        return ChoiceChip(
          label: Text(
            entry.value,
            style: GoogleFonts.cairo(
              fontSize: 12,
              fontWeight: FontWeight.w700,
            ),
          ),
          selected: isSelected,
          onSelected: (_) => onChanged(entry.key),
          selectedColor: AppColors.primary.withValues(alpha: 0.15),
          labelStyle: TextStyle(
            color: isSelected ? AppColors.primary : DoctorUi.muted,
          ),
          side: BorderSide(
            color: isSelected ? AppColors.primary : DoctorUi.border,
          ),
        );
      }).toList(),
    );
  }
}
