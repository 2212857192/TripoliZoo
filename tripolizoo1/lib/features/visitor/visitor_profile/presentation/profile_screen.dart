import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  String _section = 'main';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9F8F5),
      body: AnimatedSwitcher(
        duration: const Duration(milliseconds: 350),
        child: switch (_section) {
          'info' => _InfoSection(onBack: () => setState(() => _section = 'main')),
          'tickets' => _TicketsSection(onBack: () => setState(() => _section = 'main')),
          'language' => _LanguageSection(onBack: () => setState(() => _section = 'main')),
          _ => _MainSection(onSelect: (s) => setState(() => _section = s)),
        },
      ),
    );
  }
}

class _MainSection extends StatelessWidget {
  final ValueChanged<String> onSelect;

  const _MainSection({required this.onSelect});

  String _getInitials(String name) {
    if (name.isEmpty) return 'ز';
    final parts = name.trim().split(' ');
    if (parts.length > 1) {
      // Get first letters of first and last name
      try {
        return (parts.first[0] + parts.last[0]).toUpperCase();
      } catch (_) {
        return name[0].toUpperCase();
      }
    }
    return name[0].toUpperCase();
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final locale = context.watch<LocaleProvider>();
    final name = user?.name ?? 'إلينا مارش';
    final initials = _getInitials(name);

    final isGuest = user?.isGuest ?? true;
    final membershipText = isGuest ? 'عضو زائر' : 'العضوية السنوية · عضو منذ 2026';

    final localeLabels = {
      AppLocale.ar: 'العربية',
      AppLocale.en: 'English',
      AppLocale.it: 'Italiano',
    };
    final currentLanguage = localeLabels[locale.locale] ?? 'العربية';

    return Directionality(
      textDirection: TextDirection.rtl,
      child: ListView(
        padding: EdgeInsets.fromLTRB(24, MediaQuery.of(context).padding.top + 24, 24, 100),
        children: [
          // ── Header ──
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'ACCOUNT',
                style: GoogleFonts.cairo(
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey.shade500,
                  letterSpacing: 1.5,
                ),
              ),
              Text(
                'حسابي',
                style: GoogleFonts.amiri(
                  fontSize: 38,
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF1A1A1A),
                  height: 1.1,
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),

          // ── Profile Card ──
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(28),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.03),
                  blurRadius: 16,
                  offset: const Offset(0, 8),
                ),
              ],
            ),
            child: Row(
              children: [
                // Avatar with Badge
                Stack(
                  children: [
                    Container(
                      width: 64,
                      height: 64,
                      decoration: const BoxDecoration(
                        shape: BoxShape.circle,
                        gradient: LinearGradient(
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                          colors: [Color(0xFF2E7D32), Color(0xFF1B5E20)],
                        ),
                      ),
                      child: Center(
                        child: Text(
                          initials,
                          style: GoogleFonts.cairo(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),
                    Positioned(
                      bottom: 0,
                      left: 0,
                      child: Container(
                        width: 16,
                        height: 16,
                        decoration: BoxDecoration(
                          color: const Color(0xFFC5D639),
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 2),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(width: 16),
                // Name & Subtitle
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        name,
                        style: GoogleFonts.cairo(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF1A1A1A),
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        membershipText,
                        style: GoogleFonts.cairo(
                          fontSize: 12,
                          color: Colors.grey.shade500,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
                // Edit button
                GestureDetector(
                  onTap: () => onSelect('info'),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    decoration: BoxDecoration(
                      color: const Color(0xFFE8F5E9),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      'تعديل',
                      style: GoogleFonts.cairo(
                        fontSize: 13,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF2E7D32),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),

          // ── Settings Label ──
          Padding(
            padding: const EdgeInsets.only(right: 8, bottom: 8),
            child: Text(
              'SETTINGS',
              style: GoogleFonts.cairo(
                fontSize: 11,
                fontWeight: FontWeight.bold,
                color: Colors.grey.shade500,
                letterSpacing: 1.5,
              ),
            ),
          ),

          // ── Settings Card Group ──
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(28),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.03),
                  blurRadius: 16,
                  offset: const Offset(0, 8),
                ),
              ],
            ),
            child: Column(
              children: [
                _SettingsItem(
                  icon: Icons.person_outline_rounded,
                  title: 'المعلومات الشخصية',
                  subtitle: name,
                  onTap: () => onSelect('info'),
                ),
                const Divider(height: 1, indent: 64, endIndent: 20),
                _SettingsItem(
                  icon: Icons.confirmation_number_outlined,
                  title: 'تذاكري الرقمية',
                  trailingWidget: const Icon(Icons.line_weight_rounded, size: 24, color: Colors.black54),
                  onTap: () => onSelect('tickets'),
                ),
                const Divider(height: 1, indent: 64, endIndent: 20),
                _SettingsItem(
                  icon: Icons.translate_rounded,
                  title: 'إعدادات اللغة',
                  subtitle: currentLanguage,
                  onTap: () => onSelect('language'),
                  isLast: true,
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),

          // ── Logout Card ──
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(28),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.03),
                  blurRadius: 16,
                  offset: const Offset(0, 8),
                ),
              ],
            ),
            child: _SettingsItem(
              icon: Icons.logout_rounded,
              iconColor: const Color(0xFFE65100),
              iconBgColor: const Color(0xFFFFF3E0),
              title: 'تسجيل الخروج',
              titleColor: const Color(0xFFE65100),
              isLast: true,
              onTap: () {
                context.read<AuthProvider>().logout();
                context.go('/login');
              },
            ),
          ),
          const SizedBox(height: 32),

          // ── Footer ──
          Center(
            child: Text(
              'Tripoli Zoo - v1.0.0',
              style: GoogleFonts.cairo(
                fontSize: 12,
                color: Colors.grey.shade400,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _InfoSection extends StatelessWidget {
  final VoidCallback onBack;

  const _InfoSection({required this.onBack});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return ListView(
      padding: const EdgeInsets.fromLTRB(24, 56, 24, 40),
      children: [
        _SubHeader(title: 'معلوماتي الشخصية', onBack: onBack),
        const SizedBox(height: 32),
        Center(
          child: Container(
            width: 110,
            height: 110,
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              borderRadius: BorderRadius.circular(32),
              boxShadow: [
                BoxShadow(
                  color: AppColors.primary.withValues(alpha: 0.25),
                  blurRadius: 20,
                ),
              ],
            ),
            child: const Icon(Icons.person, color: Colors.white, size: 52),
          ),
        ),
        const SizedBox(height: 20),
        Center(
          child: Column(
            children: [
              Text(user?.name ?? 'زائر',
                  style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900)),
              if (user?.isGuest ?? true)
                const Text('زائر',
                    style: TextStyle(color: AppColors.emerald, fontSize: 12))
              else
                const Text('عضو مسجل',
                    style: TextStyle(color: AppColors.emerald, fontSize: 12)),
            ],
          ),
        ),
        const SizedBox(height: 32),
        _InfoRow(label: 'الاسم', value: user?.name ?? '-', icon: Icons.person),
        _InfoRow(label: 'البريد', value: user?.email ?? '-', icon: Icons.email),
        _InfoRow(label: 'الهاتف', value: user?.phone ?? '-', icon: Icons.phone),
      ],
    );
  }
}

class _TicketsSection extends StatelessWidget {
  final VoidCallback onBack;

  const _TicketsSection({required this.onBack});

  @override
  Widget build(BuildContext context) {
    final tickets = context.watch<TicketCartProvider>().purchasedTickets;

    return ListView(
      padding: const EdgeInsets.fromLTRB(24, 56, 24, 40),
      children: [
        _SubHeader(title: 'تذاكري الرقمية', onBack: onBack),
        const SizedBox(height: 24),
        if (tickets.isEmpty)
          const Center(
            child: Padding(
              padding: EdgeInsets.all(40),
              child: Column(
                children: [
                  Icon(Icons.confirmation_number_outlined,
                      size: 64, color: Colors.grey),
                  SizedBox(height: 16),
                  Text('لا توجد تذاكر حالياً',
                      style: TextStyle(color: AppColors.textSecondary)),
                ],
              ),
            ),
          )
        else
          ...tickets.reversed.map((t) => Container(
                margin: const EdgeInsets.only(bottom: 12),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: AppColors.background,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.grey.shade100),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('${t.totalPrice.toInt()} د.ل',
                        style: const TextStyle(
                            fontWeight: FontWeight.bold, color: AppColors.primary)),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(t.id, style: const TextStyle(fontWeight: FontWeight.bold)),
                        Text(
                          '${t.visitDate.day}/${t.visitDate.month}/${t.visitDate.year}',
                          style: const TextStyle(
                              fontSize: 12, color: AppColors.textSecondary),
                        ),
                      ],
                    ),
                  ],
                ),
              )),
      ],
    );
  }
}

class _LanguageSection extends StatelessWidget {
  final VoidCallback onBack;

  const _LanguageSection({required this.onBack});

  @override
  Widget build(BuildContext context) {
    final locale = context.watch<LocaleProvider>();

    return ListView(
      padding: const EdgeInsets.fromLTRB(24, 56, 24, 40),
      children: [
        _SubHeader(title: 'إعدادات اللغة', onBack: onBack),
        const SizedBox(height: 24),
        ...AppLocale.values.map((l) {
          final labels = {
            AppLocale.ar: 'العربية',
            AppLocale.en: 'English',
            AppLocale.it: 'Italiano',
          };
          final selected = locale.locale == l;
          return Container(
            margin: const EdgeInsets.only(bottom: 10),
            child: ListTile(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
                side: BorderSide(
                  color: selected ? AppColors.primary : Colors.grey.shade100,
                  width: selected ? 2 : 1,
                ),
              ),
              title: Text(labels[l]!,
                  textAlign: TextAlign.right,
                  style: TextStyle(
                      fontWeight: selected ? FontWeight.bold : FontWeight.normal)),
              trailing: selected
                  ? const Icon(Icons.check_circle, color: AppColors.primary)
                  : null,
              onTap: () => locale.setLocale(l),
            ),
          );
        }),
      ],
    );
  }
}

class _SubHeader extends StatelessWidget {
  final String title;
  final VoidCallback onBack;

  const _SubHeader({required this.title, required this.onBack});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        IconButton(
          icon: const Icon(Icons.arrow_back_ios_new),
          onPressed: onBack,
        ),
        Text(title,
            style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: AppColors.primaryDark)),
        const SizedBox(width: 48),
      ],
    );
  }
}

class _MenuTile extends StatelessWidget {
  final IconData icon;
  final Color color;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  const _MenuTile({
    required this.icon,
    required this.color,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Material(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(24),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(24),
          child: Ink(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: Colors.grey.shade100),
              boxShadow: [
                BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 10),
              ],
            ),
            child: Row(
              children: [
                Icon(Icons.arrow_back_ios_new, size: 14, color: Colors.grey.shade400),
                const Spacer(),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
                    if (subtitle.isNotEmpty)
                      Text(subtitle,
                          style: const TextStyle(
                              fontSize: 11, color: AppColors.textSecondary)),
                  ],
                ),
                const SizedBox(width: 14),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(icon, color: color, size: 22),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;

  const _InfoRow({required this.label, required this.value, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Row(
        children: [
          Icon(icon, color: AppColors.textSecondary, size: 20),
          const Spacer(),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(label,
                  style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
              Text(value, style: const TextStyle(fontWeight: FontWeight.bold)),
            ],
          ),
        ],
      ),
    );
  }
}

class _SettingsItem extends StatelessWidget {
  final IconData icon;
  final String title;
  final String? subtitle;
  final Widget? trailingWidget;
  final VoidCallback onTap;
  final Color? iconColor;
  final Color? iconBgColor;
  final Color? titleColor;
  final bool isLast;

  const _SettingsItem({
    required this.icon,
    required this.title,
    this.subtitle,
    this.trailingWidget,
    required this.onTap,
    this.iconColor,
    this.iconBgColor,
    this.titleColor,
    this.isLast = false,
  });

  @override
  Widget build(BuildContext context) {
    final defaultIconColor = const Color(0xFF2E7D32);
    final defaultIconBgColor = const Color(0xFFE8F5E9);

    return InkWell(
      onTap: onTap,
      borderRadius: isLast
          ? const BorderRadius.only(
              bottomLeft: Radius.circular(28),
              bottomRight: Radius.circular(28),
            )
          : null,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        child: Row(
          children: [
            // Icon
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: iconBgColor ?? defaultIconBgColor,
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon,
                color: iconColor ?? defaultIconColor,
                size: 20,
              ),
            ),
            const SizedBox(width: 16),
            // Title & Subtitle
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: GoogleFonts.cairo(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: titleColor ?? const Color(0xFF1A1A1A),
                    ),
                  ),
                  if (subtitle != null) ...[
                    const SizedBox(height: 2),
                    Text(
                      subtitle!,
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        color: Colors.grey.shade400,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ],
              ),
            ),
            if (trailingWidget != null) ...[
              trailingWidget!,
              const SizedBox(width: 8),
            ],
            // Chevron arrow (RTL points left)
            Icon(
              Icons.arrow_back_ios_new_rounded,
              size: 14,
              color: titleColor ?? Colors.grey.shade300,
            ),
          ],
        ),
      ),
    );
  }
}

