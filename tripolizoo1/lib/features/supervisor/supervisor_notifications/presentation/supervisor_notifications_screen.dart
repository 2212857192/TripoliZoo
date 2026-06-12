import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_ui.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/domain/supervisor_notification.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/presentation/supervisor_notifications_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/presentation/widgets/supervisor_notification_card.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class SupervisorNotificationsScreen extends StatefulWidget {
  const SupervisorNotificationsScreen({super.key});

  @override
  State<SupervisorNotificationsScreen> createState() =>
      _SupervisorNotificationsScreenState();
}

class _SupervisorNotificationsScreenState
    extends State<SupervisorNotificationsScreen> {
  SupervisorNotificationReadFilter _filter =
      SupervisorNotificationReadFilter.all;

  void _openNotification(SupervisorNotification notification) {
    context
        .read<SupervisorNotificationsProvider>()
        .markAsRead(notification.id);

    final route = notification.targetRoute;
    if (route != null) {
      context.pop();
      context.go(route);
    }
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final provider = context.watch<SupervisorNotificationsProvider>();
    final items = provider.filtered(_filter);
    final unreadCount = provider.unreadCount;

    return Scaffold(
      backgroundColor: SupervisorUi.background,
      body: SafeArea(
        top: false,
        bottom: false,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(
              child: AnnotatedRegion<SystemUiOverlayStyle>(
                value: SystemUiOverlayStyle.dark,
                child: Container(
                  margin: const EdgeInsets.fromLTRB(16, 0, 16, 0),
                  padding: EdgeInsets.fromLTRB(4, topPad + 12, 4, 18),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: const BorderRadius.vertical(
                      bottom: Radius.circular(28),
                    ),
                    border: Border.all(
                      color: SupervisorUi.border,
                      width: 1.5,
                    ),
                    boxShadow: SupervisorUi.softShadow,
                  ),
                  child: Row(
                    children: [
                      Material(
                        color: const Color(0xFFF4F7F4),
                        shape: const CircleBorder(
                          side: BorderSide(color: SupervisorUi.border),
                        ),
                        child: InkWell(
                          onTap: () => context.pop(),
                          customBorder: const CircleBorder(),
                          child: const Padding(
                            padding: EdgeInsets.all(10),
                            child: Icon(
                              Icons.arrow_forward_ios_rounded,
                              size: 18,
                              color: SupervisorUi.textPrimary,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'الإشعارات',
                              style: GoogleFonts.cairo(
                                fontSize: 18,
                                fontWeight: FontWeight.w900,
                                color: SupervisorUi.textPrimary,
                                height: 1.15,
                              ),
                            ),
                            const SizedBox(height: 3),
                            Text(
                              unreadCount > 0
                                  ? '$unreadCount إشعار غير مقروء'
                                  : 'لا توجد إشعارات جديدة',
                              style: GoogleFonts.cairo(
                                fontSize: 11.5,
                                fontWeight: FontWeight.w700,
                                color: SupervisorUi.muted,
                              ),
                            ),
                          ],
                        ),
                      ),
                      if (unreadCount > 0)
                        Container(
                          width: 42,
                          height: 42,
                          decoration: BoxDecoration(
                            color: const Color(0xFFF4F7F4),
                            shape: BoxShape.circle,
                            border: Border.all(color: SupervisorUi.border),
                          ),
                          child: Stack(
                            alignment: Alignment.center,
                            children: [
                              const Icon(
                                Icons.notifications_outlined,
                                color: SupervisorUi.textPrimary,
                                size: 20,
                              ),
                              Positioned(
                                top: 8,
                                left: 8,
                                child: Container(
                                  width: 8,
                                  height: 8,
                                  decoration: const BoxDecoration(
                                    color: AppColors.primary,
                                    shape: BoxShape.circle,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                    ],
                  ),
                ),
              ),
            ),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: SupervisorUi.cardDecoration(),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          const SupervisorSectionTitle(
                            eyebrow: 'Filter',
                            title: 'تصفية الإشعارات',
                          ),
                          const SizedBox(height: 14),
                          _ReadFilterRow(
                            selected: _filter,
                            onChanged: (v) => setState(() => _filter = v),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),
                    const SupervisorSectionTitle(
                      eyebrow: 'Inbox',
                      title: 'آخر الإشعارات',
                    ),
                    const SizedBox(height: 14),
                  ],
                ),
              ),
            ),
            if (items.isEmpty)
              SliverFillRemaining(
                hasScrollBody: false,
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 32),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        width: 72,
                        height: 72,
                        decoration: BoxDecoration(
                          color: const Color(0xFFE8F5E9),
                          borderRadius: BorderRadius.circular(22),
                        ),
                        child: const Icon(
                          Icons.notifications_none_rounded,
                          color: AppColors.primary,
                          size: 34,
                        ),
                      ),
                      const SizedBox(height: 16),
                      Text(
                        'لا توجد إشعارات',
                        style: GoogleFonts.cairo(
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                          color: SupervisorUi.muted,
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
                  separatorBuilder: (_, __) => const SizedBox(height: 10),
                  itemBuilder: (context, index) {
                    final item = items[index];
                    return SupervisorNotificationCard(
                      notification: item,
                      onTap: () => _openNotification(item),
                    );
                  },
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class _ReadFilterRow extends StatelessWidget {
  const _ReadFilterRow({
    required this.selected,
    required this.onChanged,
  });

  final SupervisorNotificationReadFilter selected;
  final ValueChanged<SupervisorNotificationReadFilter> onChanged;

  static const _labels = {
    SupervisorNotificationReadFilter.all: 'الكل',
    SupervisorNotificationReadFilter.unread: 'غير مقروء',
    SupervisorNotificationReadFilter.read: 'مقروء',
  };

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: const BouncingScrollPhysics(),
      child: Row(
        children: [
          for (final entry in _labels.entries) ...[
            _Chip(
              label: entry.value,
              selected: selected == entry.key,
              onTap: () => onChanged(entry.key),
            ),
            if (entry.key != SupervisorNotificationReadFilter.read)
              const SizedBox(width: 8),
          ],
        ],
      ),
    );
  }
}

class _Chip extends StatelessWidget {
  const _Chip({
    required this.label,
    required this.selected,
    required this.onTap,
  });

  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: selected ? AppColors.primaryDark : const Color(0xFFF0F4F0),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: selected ? AppColors.primaryDark : SupervisorUi.border,
            ),
          ),
          child: Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 12.5,
              fontWeight: FontWeight.w700,
              color: selected ? Colors.white : SupervisorUi.textSecondary,
            ),
          ),
        ),
      ),
    );
  }
}
