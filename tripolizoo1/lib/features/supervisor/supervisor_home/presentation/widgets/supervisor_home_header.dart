import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class SupervisorHomeHeader extends StatelessWidget {
  const SupervisorHomeHeader({
    super.key,
    required this.supervisorName,
    required this.groupName,
    required this.unreadNotifications,
    this.onNotificationsTap,
  });

  final String supervisorName;
  final String groupName;
  final int unreadNotifications;
  final VoidCallback? onNotificationsTap;

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark, // استخدام نص داكن لشريط الحالة لأن الخلفية بيضاء
      child: Container(
        padding: EdgeInsets.fromLTRB(16, topPad + 16, 16, 18),
        decoration: BoxDecoration(
          color: Colors.white, // خلفية بيضاء عصرية ونظيفة
          borderRadius: const BorderRadius.vertical(
            bottom: Radius.circular(28),
          ),
          border: Border.all(
            color: SupervisorUi.border,
            width: 1.5,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF1A3521).withValues(alpha: 0.03),
              blurRadius: 18,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // ─── Avatar initials with beautiful green gradient ───
            Container(
              width: 46,
              height: 46,
              decoration: BoxDecoration(
                gradient: AppColors.primaryGradient, // تدرج أخضر احترافي
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withValues(alpha: 0.25),
                    blurRadius: 8,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              child: Center(
                child: Text(
                  supervisorName.isNotEmpty ? supervisorName[0] : 'م',
                  style: GoogleFonts.cairo(
                    color: Colors.white,
                    fontSize: 19,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 14),
            // ─── Name & group ───
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    'مرحباً، $supervisorName',
                    style: GoogleFonts.cairo(
                      fontSize: 16.5,
                      fontWeight: FontWeight.w900,
                      color: SupervisorUi.textPrimary, // أخضر غاباتي داكن وراقي
                      height: 1.2,
                    ),
                  ),
                  const SizedBox(height: 4),
                  // Group tag/badge
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 3.5,
                    ),
                    decoration: BoxDecoration(
                      color: AppColors.primary.withValues(alpha: 0.08), // خلفية ناعمة جداً
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: AppColors.primary.withValues(alpha: 0.15),
                        width: 1,
                      ),
                    ),
                    child: Text(
                      'مجموعة: $groupName',
                      style: GoogleFonts.cairo(
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: AppColors.primaryDark,
                        height: 1,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 12),
            // ─── Notification bell ───
            _NotificationBell(
              unreadCount: unreadNotifications,
              onTap: onNotificationsTap,
            ),
          ],
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────
class _NotificationBell extends StatelessWidget {
  const _NotificationBell({required this.unreadCount, this.onTap});

  final int unreadCount;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: const Color(0xFFF4F7F4), // خلفية رمادية مخضرة خفيفة
              shape: BoxShape.circle,
              border: Border.all(
                color: SupervisorUi.border,
                width: 1.2,
              ),
            ),
            child: const Icon(
              Icons.notifications_outlined,
              color: SupervisorUi.textPrimary, // أيقونة داكنة هادئة
              size: 20,
            ),
          ),
          if (unreadCount > 0)
            Positioned(
              top: -1,
              left: -1,
              child: Container(
                width: 15,
                height: 15,
                decoration: BoxDecoration(
                  color: AppColors.primary,
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: Colors.white,
                    width: 2,
                  ),
                ),
                child: Center(
                  child: Container(
                    width: 5,
                    height: 5,
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      shape: BoxShape.circle,
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }
}
