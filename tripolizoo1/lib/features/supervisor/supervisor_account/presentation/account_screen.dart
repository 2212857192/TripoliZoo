import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_dashboard_data.dart';
import 'package:tripolizoo/features/supervisor/supervisor_account/presentation/change_password_dialog.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class SupervisorAccountScreen extends StatelessWidget {
  const SupervisorAccountScreen({super.key});

  static const _bg = Color(0xFFF5F5F5);
  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  String _displayEmail(String? email) {
    if (email == null || email.isEmpty) return '—';
    if (email.startsWith('supervisor@')) {
      return 'm.supervisor@tripoli-zoo.ly';
    }
    return email;
  }

  @override
  Widget build(BuildContext context) {
    const data = SupervisorDashboardData.mock;
    final auth = context.watch<AuthProvider>();
    final user = auth.user;
    final name = user?.name ?? data.supervisorName;
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    return Scaffold(
      backgroundColor: _bg,
      body: SafeArea(
        top: false,
        bottom: false,
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(),
          padding: EdgeInsets.fromLTRB(20, topPad + 20, 20, bottomPad + 100),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text(
                'الحساب',
                style: GoogleFonts.cairo(
                  fontSize: 22,
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFF1A1A1A),
                ),
              ),
              const SizedBox(height: 20),
              _ProfileCard(
                name: name,
                groupName: data.groupName,
              ),
              const SizedBox(height: 12),
              _InfoTile(
                label: 'البريد الإلكتروني',
                value: _displayEmail(user?.email),
              ),
              const SizedBox(height: 12),
              _ActionTile(
                icon: Icons.lock_outline_rounded,
                label: 'تغيير كلمة المرور',
                iconBg: const Color(0xFFE8F5E9),
                iconColor: AppColors.primaryDark,
                onTap: () async {
                  final saved = await ChangePasswordDialog.show(context);
                  if (!context.mounted || saved != true) return;
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(
                        'تم تغيير كلمة المرور بنجاح',
                        style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
                      ),
                      backgroundColor: AppColors.primary,
                    ),
                  );
                },
              ),
              const SizedBox(height: 12),
              _ActionTile(
                icon: Icons.logout_rounded,
                label: 'تسجيل الخروج',
                iconBg: const Color(0xFFFEE2E2),
                iconColor: const Color(0xFFDC2626),
                labelColor: const Color(0xFFDC2626),
                onTap: () => context.read<AuthProvider>().logout(),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _ProfileCard extends StatelessWidget {
  const _ProfileCard({
    required this.name,
    required this.groupName,
  });

  final String name;
  final String groupName;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: SupervisorAccountScreen._border),
      ),
      child: Column(
        children: [
          Container(
            width: 72,
            height: 72,
            decoration: BoxDecoration(
              color: const Color(0xFFE8F5E9),
              shape: BoxShape.circle,
              border: Border.all(color: const Color(0xFFC8E6C9)),
            ),
            child: const Icon(
              Icons.person_rounded,
              size: 36,
              color: AppColors.primaryDark,
            ),
          ),
          const SizedBox(height: 14),
          Text(
            name,
            style: GoogleFonts.cairo(
              fontSize: 18,
              fontWeight: FontWeight.w800,
              color: const Color(0xFF1A1A1A),
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'مشرف المجموعة',
            style: GoogleFonts.cairo(
              fontSize: 13.5,
              fontWeight: FontWeight.w600,
              color: SupervisorAccountScreen._muted,
            ),
          ),
          const SizedBox(height: 10),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
            decoration: BoxDecoration(
              color: AppColors.primaryDark,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              'المجموعة: $groupName',
              style: GoogleFonts.cairo(
                fontSize: 12,
                fontWeight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _InfoTile extends StatelessWidget {
  const _InfoTile({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: SupervisorAccountScreen._border),
      ),
      child: Row(
        children: [
          Expanded(
            child: Text(
              value,
              textAlign: TextAlign.start,
              style: GoogleFonts.cairo(
                fontSize: 13.5,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF1A1A1A),
              ),
            ),
          ),
          Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: SupervisorAccountScreen._muted,
            ),
          ),
        ],
      ),
    );
  }
}

class _ActionTile extends StatelessWidget {
  const _ActionTile({
    required this.icon,
    required this.label,
    required this.iconBg,
    required this.iconColor,
    required this.onTap,
    this.labelColor,
  });

  final IconData icon;
  final String label;
  final Color iconBg;
  final Color iconColor;
  final Color? labelColor;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Ink(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: SupervisorAccountScreen._border),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: iconBg,
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, color: iconColor, size: 18),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  label,
                  style: GoogleFonts.cairo(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                    color: labelColor ?? const Color(0xFF1A1A1A),
                  ),
                ),
              ),
              const Icon(
                Icons.chevron_left_rounded,
                color: SupervisorAccountScreen._muted,
                size: 22,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
