import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorShell extends StatelessWidget {
  const DoctorShell({super.key, required this.navigationShell});

  final StatefulNavigationShell navigationShell;

  static const _green = AppColors.primary;
  static const _inactive = Color(0xFF64748B);

  @override
  Widget build(BuildContext context) {
    final idx = navigationShell.currentIndex;

    return Scaffold(
      backgroundColor: DoctorUi.background,
      body: navigationShell,
      extendBody: true,
      bottomNavigationBar: Theme(
        data: Theme.of(context).copyWith(
          canvasColor: Colors.transparent, // يضمن أن شريط التنقل يطفو بشكل شفاف تماماً
        ),
        child: Padding(
          padding: EdgeInsets.fromLTRB(
            20,
            0,
            20,
            MediaQuery.of(context).padding.bottom + 12,
          ),
          child: SizedBox(
            height: 72,
            child: ClipRRect(
              borderRadius: BorderRadius.circular(40),
              child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 18, sigmaY: 18),
                child: Container(
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.82),
                    borderRadius: BorderRadius.circular(40),
                    border: Border.all(
                      color: Colors.white.withValues(alpha: 0.7),
                      width: 1.2,
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: _green.withValues(alpha: 0.14),
                        blurRadius: 28,
                        offset: const Offset(0, 10),
                      ),
                    ],
                  ),
                  child: Directionality(
                    textDirection: TextDirection.rtl,
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 6),
                      child: Row(
                        children: [
                          for (var i = 0; i < _items.length; i++)
                            Expanded(
                              child: _NavItem(
                                icon: _items[i].icon,
                                activeIcon: _items[i].activeIcon,
                                label: _items[i].label,
                                selected: idx == i,
                                onTap: () => _goBranch(context, i),
                              ),
                            ),
                        ],
                      ),
                    ),
                  ),
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

  static const _items = [
    _NavMeta(Icons.home_outlined, Icons.home_rounded, 'الرئيسية'),
    _NavMeta(Icons.assignment_outlined, Icons.assignment_rounded, 'البلاغات'),
    _NavMeta(Icons.medical_services_outlined, Icons.medical_services_rounded, 'الحالات'),
    _NavMeta(Icons.security_outlined, Icons.security_rounded, 'الحجر الصحي'),
    _NavMeta(Icons.person_outline_rounded, Icons.person_rounded, 'الحساب'),
  ];
}

class _NavMeta {
  const _NavMeta(this.icon, this.activeIcon, this.label);
  final IconData icon;
  final IconData activeIcon;
  final String label;
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
    final color = selected ? DoctorShell._green : DoctorShell._inactive;

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(22),
        splashColor: DoctorShell._green.withValues(alpha: 0.1),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 220),
          curve: Curves.easeOut,
          margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 2),
          decoration: BoxDecoration(
            color: selected
                ? DoctorShell._green.withValues(alpha: 0.1)
                : Colors.transparent,
            borderRadius: BorderRadius.circular(22),
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(selected ? activeIcon : icon, color: color, size: 23),
              const SizedBox(height: 2),
              Text(
                label,
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.cairo(
                  color: color,
                  fontSize: 8.5,
                  fontWeight: selected ? FontWeight.w800 : FontWeight.w700,
                  height: 1.05,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
