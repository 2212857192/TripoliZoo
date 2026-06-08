import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

/// ترتيب RTL — أقصى اليمين → اليسار:
/// الرئيسة → الخريطة → [مسح QR] → تذاكر → حسابي
class MainShell extends StatelessWidget {
  final StatefulNavigationShell navigationShell;

  const MainShell({super.key, required this.navigationShell});

  static const _green = AppColors.primary;
  static const _greenLight = AppColors.primaryLight;
  static const _inactive = Color(0xFF64748B);

  @override
  Widget build(BuildContext context) {
    final idx = navigationShell.currentIndex;

    return Scaffold(
      backgroundColor: AppColors.background,
      body: navigationShell,
      extendBody: true,
      bottomNavigationBar: Padding(
        padding: EdgeInsets.fromLTRB(
          20,
          0,
          20,
          MediaQuery.of(context).padding.bottom + 12,
        ),
        child: SizedBox(
          height: 80,
          child: Stack(
            clipBehavior: Clip.none,
            alignment: Alignment.bottomCenter,
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(40),
                child: BackdropFilter(
                  filter: ImageFilter.blur(sigmaX: 16, sigmaY: 16),
                  child: Container(
                    height: 64,
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.78),
                      borderRadius: BorderRadius.circular(40),
                      border: Border.all(
                        color: Colors.white.withValues(alpha: 0.65),
                        width: 1.2,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: _green.withValues(alpha: 0.12),
                          blurRadius: 24,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: Directionality(
                      textDirection: TextDirection.rtl,
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 6),
                        child: Row(
                          children: [
                            Expanded(
                              child: _NavItem(
                                icon: Icons.home_outlined,
                                activeIcon: Icons.home_rounded,
                                label: 'الرئيسة',
                                selected: idx == 0,
                                onTap: () => _goBranch(context, 0),
                              ),
                            ),
                            Expanded(
                              child: _NavItem(
                                icon: Icons.confirmation_number_outlined,
                                activeIcon: Icons.confirmation_number_rounded,
                                label: 'تذاكر',
                                selected: idx == 2,
                                onTap: () => _goBranch(context, 2),
                              ),
                            ),
                            const SizedBox(width: 68),
                            Expanded(
                              child: _NavItem(
                                icon: Icons.qr_code_scanner_rounded,
                                activeIcon: Icons.qr_code_scanner_rounded,
                                label: 'ماسح',
                                selected: false,
                                onTap: () => context.push('/qr-scanner'),
                              ),
                            ),
                            Expanded(
                              child: _NavItem(
                                icon: Icons.person_outline_rounded,
                                activeIcon: Icons.person_rounded,
                                label: 'حسابي',
                                selected: idx == 3,
                                onTap: () => _goBranch(context, 3),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ),
              Positioned(
                top: 0,
                child: _CenterMapButton(
                  selected: idx == 1,
                  onTap: () => _goBranch(context, 1),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _goBranch(BuildContext context, int index) {
    navigationShell.goBranch(
      index,
      initialLocation: index == navigationShell.currentIndex,
    );
  }
}

class _CenterMapButton extends StatelessWidget {
  const _CenterMapButton({required this.onTap, required this.selected});

  final VoidCallback onTap;
  final bool selected;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        customBorder: const CircleBorder(),
        child: Container(
          width: 62,
          height: 62,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: Colors.white,
            border: Border.all(
              color: selected ? MainShell._green : Colors.transparent,
              width: 2.0,
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.15),
                blurRadius: 12,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: ClipOval(
            child: Image.asset(
              'assets/images/map_nav_icon.png',
              fit: BoxFit.cover,
            ),
          ),
        ),
      ),
    );
  }
}

class _NavItem extends StatelessWidget {
  const _NavItem({
    required this.icon,
    required this.activeIcon,
    required this.label,
    required this.selected,
    required this.onTap,
  });

  final IconData icon;
  final IconData activeIcon;
  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final color = selected ? MainShell._green : MainShell._inactive;

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        splashColor: MainShell._green.withValues(alpha: 0.08),
        child: SizedBox(
          height: 56,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                selected ? activeIcon : icon,
                color: color,
                size: 24,
              ),
              const SizedBox(height: 3),
              Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.cairo(
                  color: color,
                  fontSize: 11,
                  fontWeight: selected ? FontWeight.w800 : FontWeight.w700,
                  height: 1.1,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
