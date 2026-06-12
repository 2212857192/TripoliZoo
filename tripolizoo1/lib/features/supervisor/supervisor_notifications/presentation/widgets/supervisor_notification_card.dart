import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_ui.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/widgets/follow_up_time_label.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/domain/supervisor_notification.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class SupervisorNotificationCard extends StatelessWidget {
  const SupervisorNotificationCard({
    super.key,
    required this.notification,
    required this.onTap,
  });

  final SupervisorNotification notification;
  final VoidCallback onTap;

  IconData get _icon => switch (notification.type) {
        SupervisorNotificationType.dietRecommendation =>
          Icons.restaurant_outlined,
        SupervisorNotificationType.receivingTask => Icons.inbox_outlined,
        SupervisorNotificationType.healthReportUpdate =>
          Icons.assignment_outlined,
        SupervisorNotificationType.newbornFollowUp => Icons.child_care_outlined,
        SupervisorNotificationType.mortalityApproval =>
          Icons.heart_broken_outlined,
      };

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(SupervisorUi.cardRadius),
        child: Ink(
          decoration: SupervisorUi.cardDecoration(),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(SupervisorUi.cardRadius),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        width: 38,
                        height: 38,
                        decoration: BoxDecoration(
                          color: const Color(0xFFF0F4F0),
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(
                            color: SupervisorUi.border,
                          ),
                        ),
                        child: Icon(
                          _icon,
                          color: AppColors.primary,
                          size: 18,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              notification.type.label,
                              style: GoogleFonts.cairo(
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                                color: SupervisorUi.muted,
                              ),
                            ),
                            Text(
                              formatFollowUpTime(notification.createdAt),
                              style: GoogleFonts.cairo(
                                fontSize: 12.5,
                                fontWeight: FontWeight.w800,
                                color: AppColors.primaryDark,
                                height: 1.2,
                              ),
                            ),
                          ],
                        ),
                      ),
                      if (!notification.isRead)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 10,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: AppColors.primary.withValues(alpha: 0.08),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(
                              color: AppColors.primary.withValues(alpha: 0.25),
                            ),
                          ),
                          child: Text(
                            'جديد',
                            style: GoogleFonts.cairo(
                              fontSize: 11.5,
                              fontWeight: FontWeight.w900,
                              color: AppColors.primary,
                              height: 1,
                            ),
                          ),
                        )
                      else
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 10,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF0F4F0),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: SupervisorUi.border),
                          ),
                          child: Text(
                            'مقروء',
                            style: GoogleFonts.cairo(
                              fontSize: 11.5,
                              fontWeight: FontWeight.w800,
                              color: SupervisorUi.muted,
                              height: 1,
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Text(
                    notification.title,
                    style: GoogleFonts.cairo(
                      fontSize: 14.5,
                      fontWeight: FontWeight.w800,
                      color: SupervisorUi.textPrimary,
                      height: 1.4,
                    ),
                  ),
                ),
                const SizedBox(height: 10),
                Container(
                  margin: const EdgeInsets.fromLTRB(16, 0, 16, 14),
                  padding: const EdgeInsets.all(11),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF7FAF7),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: SupervisorUi.border),
                  ),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Icon(
                        _metaIcon,
                        color: AppColors.primary,
                        size: 15,
                      ),
                      const SizedBox(width: 7),
                      Expanded(
                        child: Text(
                          notification.description,
                          style: GoogleFonts.cairo(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: SupervisorUi.muted,
                            height: 1.5,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 0, 16, 14),
                  child: Row(
                    children: [
                      Text(
                        'فتح',
                        style: GoogleFonts.cairo(
                          fontSize: 12.5,
                          fontWeight: FontWeight.w800,
                          color: AppColors.primaryDark,
                        ),
                      ),
                      const Icon(
                        Icons.chevron_left_rounded,
                        size: 18,
                        color: AppColors.primaryDark,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  IconData get _metaIcon => switch (notification.type) {
        SupervisorNotificationType.dietRecommendation =>
          Icons.info_outline_rounded,
        SupervisorNotificationType.receivingTask =>
          Icons.local_shipping_outlined,
        SupervisorNotificationType.healthReportUpdate =>
          Icons.medical_services_outlined,
        SupervisorNotificationType.newbornFollowUp =>
          Icons.child_care_outlined,
        SupervisorNotificationType.mortalityApproval =>
          Icons.report_outlined,
      };
}
