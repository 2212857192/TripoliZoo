import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
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
      backgroundColor: AppColors.background,
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

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return ListView(
      padding: const EdgeInsets.fromLTRB(24, 60, 24, 100),
      children: [
        Row(
          children: [
            CircleAvatar(
              radius: 32,
              backgroundColor: AppColors.primary.withValues(alpha: 0.12),
              child: const Icon(Icons.person, color: AppColors.primary, size: 32),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('حسابي',
                      style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                            fontWeight: FontWeight.w900,
                            color: AppColors.primaryDark,
                          )),
                  Text(
                    user?.name ?? 'زائر',
                    style: const TextStyle(color: AppColors.textSecondary),
                  ),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 32),
        _MenuTile(
          icon: Icons.person_outline,
          color: Colors.blue,
          title: 'معلوماتي الشخصية',
          subtitle: 'الاسم، البريد، وبيانات التواصل',
          onTap: () => onSelect('info'),
        ),
        _MenuTile(
          icon: Icons.confirmation_number_outlined,
          color: AppColors.accent,
          title: 'تذاكري الرقمية',
          subtitle: 'عرض تذاكر الحجز والباركود',
          onTap: () => onSelect('tickets'),
        ),
        _MenuTile(
          icon: Icons.language,
          color: AppColors.emerald,
          title: 'إعدادات اللغة',
          subtitle: 'العربية، English، Italiano',
          onTap: () => onSelect('language'),
        ),
        const SizedBox(height: 20),
        const Divider(),
        _MenuTile(
          icon: Icons.logout,
          color: AppColors.error,
          title: 'تسجيل الخروج',
          subtitle: '',
          onTap: () {
            context.read<AuthProvider>().logout();
            context.go('/login');
          },
        ),
      ],
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
