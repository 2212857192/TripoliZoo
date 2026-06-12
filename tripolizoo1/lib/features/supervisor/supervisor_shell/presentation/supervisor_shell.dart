import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

/// شريط تنقل المشرف — تصميم مسطّح كما في الواجهة المرجعية.
class SupervisorShell extends StatelessWidget {
  const SupervisorShell({super.key, required this.navigationShell});

  final StatefulNavigationShell navigationShell;

  static const _green = AppColors.primary;
  static const _inactive = Color(0xFF9CA3AF);
  static const _border = Color(0xFFE5E7EB);

  @override
  Widget build(BuildContext context) {
    final idx = navigationShell.currentIndex;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      body: navigationShell,
      bottomNavigationBar: Container(
        decoration: const BoxDecoration(
          color: Colors.white,
          border: Border(top: BorderSide(color: _border)),
        ),
        padding: EdgeInsets.only(top: 6, bottom: bottomPad + 6),
        child: Directionality(
          textDirection: TextDirection.rtl,
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
    _NavMeta(
      Icons.assignment_outlined,
      Icons.assignment_rounded,
      'البلاغات الصحية',
    ),
    _NavMeta(
      Icons.fact_check_outlined,
      Icons.fact_check_rounded,
      'متابعة المجموعة',
    ),
    _NavMeta(
      Icons.inventory_2_outlined,
      Icons.inventory_2_rounded,
      'مهام الاستلام',
    ),
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
    final color =
        selected ? SupervisorShell._green : SupervisorShell._inactive;

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 2),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(selected ? activeIcon : icon, color: color, size: 22),
              const SizedBox(height: 3),
              Text(
                label,
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.cairo(
                  color: color,
                  fontSize: 9,
                  fontWeight: selected ? FontWeight.w800 : FontWeight.w600,
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
