import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/domain/doctor_notification.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorNotificationCard extends StatelessWidget {
  const DoctorNotificationCard({
    super.key,
    required this.notification,
    required this.onTap,
  });

  final DoctorNotification notification;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: notification.isRead ? Colors.white : const Color(0xFFF0FAF2),
        borderRadius: BorderRadius.circular(DoctorUi.cardRadiusSm),
        border: Border.all(
          color: notification.isRead
              ? DoctorUi.border
              : AppColors.primary.withValues(alpha: 0.4),
          width: notification.isRead ? 1.2 : 1.6,
        ),
        boxShadow: DoctorUi.softShadow,
      ),
      child: Material(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(DoctorUi.cardRadiusSm),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(DoctorUi.cardRadiusSm),
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: AppColors.primary.withValues(alpha: 0.08),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(
                    Icons.pets_outlined,
                    color: AppColors.primary,
                    size: 22,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              notification.title,
                              style: GoogleFonts.cairo(
                                fontSize: 13.5,
                                fontWeight: FontWeight.w800,
                                color: DoctorUi.textPrimary,
                              ),
                            ),
                          ),
                          if (!notification.isRead)
                            Container(
                              width: 8,
                              height: 8,
                              decoration: const BoxDecoration(
                                color: AppColors.accent,
                                shape: BoxShape.circle,
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(height: 6),
                      Text(
                        notification.message,
                        style: GoogleFonts.cairo(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: DoctorUi.textSecondary,
                          height: 1.45,
                        ),
                      ),
                      if (notification.caseNumber != null) ...[
                        const SizedBox(height: 8),
                        Text(
                          notification.caseNumber!,
                          style: GoogleFonts.cairo(
                            fontSize: 11,
                            fontWeight: FontWeight.w800,
                            color: AppColors.primaryDark,
                          ),
                        ),
                      ],
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
}
