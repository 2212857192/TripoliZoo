import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

/// ترتيب RTL — أقصى اليمين → اليسار:
/// الرئيسة → الخريطة → [مسح QR] → تذاكر → الحساب
class MainShell extends StatelessWidget {
  final StatefulNavigationShell navigationShell;

  const MainShell({super.key, required this.navigationShell});

  static const _green = AppColors.primary;

  @override
  Widget build(BuildContext context) {
    final idx = navigationShell.currentIndex;
    final bottomInset = MediaQuery.paddingOf(context).bottom;

    return Scaffold(
      backgroundColor: AppColors.background,
      body: navigationShell,
      extendBody: false,
      bottomNavigationBar: ClipRect(
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 16, sigmaY: 16),
          child: Container(
            width: double.infinity,
            height: 68 + bottomInset,
            padding: EdgeInsets.only(bottom: bottomInset),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.96),
              border: Border(
                top: BorderSide(
                  color: Colors.black.withValues(alpha: 0.06),
                  width: 1.2,
                ),
              ),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.06),
                  blurRadius: 14,
                  offset: const Offset(0, -3),
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
                    Expanded(
                      child: _NavItem(
                        icon: Icons.map_outlined,
                        activeIcon: Icons.map_rounded,
                        label: 'الخريطة',
                        selected: idx == 1,
                        onTap: () => _goBranch(context, 1),
                      ),
                    ),
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
                        label: 'الحساب',
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
    );
  }

  void _goBranch(BuildContext context, int index) {
    navigationShell.goBranch(
      index,
      initialLocation: index == navigationShell.currentIndex,
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
    this.imageAsset,
  });

  final IconData icon;
  final IconData activeIcon;
  final String? imageAsset;
  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final iconColor = selected ? MainShell._green : const Color(0xFF4B5563);
    final textColor = selected ? MainShell._green : const Color(0xFF1A1A1A);

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        splashColor: MainShell._green.withValues(alpha: 0.08),
        child: SizedBox(
          height: 60,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              AnimatedContainer(
                duration: const Duration(milliseconds: 180),
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color:
                      selected ? const Color(0xFFE8F5E9) : Colors.transparent,
                  shape: BoxShape.circle,
                ),
                alignment: Alignment.center,
                child: imageAsset != null
                    ? ColorFiltered(
                        colorFilter: ColorFilter.mode(
                          iconColor,
                          BlendMode.srcIn,
                        ),
                        child: Image.asset(
                          imageAsset!,
                          width: 24,
                          height: 24,
                          fit: BoxFit.contain,
                        ),
                      )
                    : Icon(
                        selected ? activeIcon : icon,
                        color: iconColor,
                        size: 24,
                      ),
              ),
              const SizedBox(height: 2),
              Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.cairo(
                  color: textColor,
                  fontSize: 11.5,
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
