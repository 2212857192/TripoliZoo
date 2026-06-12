import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/data/doctor_profile_data.dart';
import 'package:tripolizoo/features/supervisor/supervisor_account/presentation/change_password_dialog.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorAccountScreen extends StatelessWidget {
  const DoctorAccountScreen({super.key});

  static const _bg = Color(0xFFF5F5F5);
  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  @override
  Widget build(BuildContext context) {
    const profile = DoctorProfileData.mock;
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
                'حسابي',
                style: GoogleFonts.cairo(
                  fontSize: 22,
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFF1A1A1A),
                ),
              ),
              const SizedBox(height: 6),
              Text(
                'الملف الشخصي للطبيب البيطري',
                style: GoogleFonts.cairo(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: _muted,
                ),
              ),
              const SizedBox(height: 20),
              _ProfileCard(
                name: profile.fullName,
                subtitle: profile.displayTitle,
              ),
              const SizedBox(height: 12),
              _SectionCard(
                title: 'المعلومات الشخصية',
                rows: [
                  _InfoRow(label: 'الاسم الكامل', value: profile.fullName),
                  _InfoRow(
                    label: 'الرقم الوظيفي',
                    value: profile.employeeNumber,
                  ),
                  _InfoRow(label: 'القسم', value: profile.department),
                  _InfoRow(label: 'المنطقة', value: profile.area),
                ],
              ),
              const SizedBox(height: 12),
              _SectionCard(
                title: 'وسائل التواصل',
                rows: [
                  _InfoRow(
                    label: 'الهاتف',
                    value: profile.phone,
                    icon: Icons.phone_outlined,
                  ),
                  _InfoRow(
                    label: 'البريد الإلكتروني',
                    value: profile.email,
                    icon: Icons.email_outlined,
                  ),
                ],
              ),
              const SizedBox(height: 12),
              _SectionCard(
                title: 'معلومات العمل',
                rows: [
                  _InfoRow(
                    label: 'مكان العمل',
                    value: profile.workplace,
                    icon: Icons.business_outlined,
                  ),
                  _InfoRow(
                    label: 'تاريخ التعيين',
                    value: profile.appointmentDate,
                    icon: Icons.calendar_today_outlined,
                  ),
                  _InfoRow(
                    label: 'الدور',
                    value: profile.role,
                    icon: Icons.verified_user_outlined,
                  ),
                ],
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
  const _ProfileCard({required this.name, required this.subtitle});

  final String name;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: DoctorAccountScreen._border),
      ),
      child: Row(
        children: [
          Container(
            width: 64,
            height: 64,
            decoration: BoxDecoration(
              color: const Color(0xFFE8F5E9),
              shape: BoxShape.circle,
              border: Border.all(color: const Color(0xFFC8E6C9)),
            ),
            child: const Icon(
              Icons.person_rounded,
              size: 32,
              color: AppColors.primaryDark,
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  style: GoogleFonts.cairo(
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                    color: const Color(0xFF1A1A1A),
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  subtitle,
                  style: GoogleFonts.cairo(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: DoctorAccountScreen._muted,
                    height: 1.35,
                  ),
                ),
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: AppColors.primaryDark,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(
                        Icons.check_circle_rounded,
                        color: Colors.white,
                        size: 14,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        'نشط',
                        style: GoogleFonts.cairo(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _SectionCard extends StatelessWidget {
  const _SectionCard({required this.title, required this.rows});

  final String title;
  final List<Widget> rows;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: DoctorAccountScreen._border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            title,
            style: GoogleFonts.cairo(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: AppColors.primaryDark,
            ),
          ),
          const SizedBox(height: 12),
          ...rows,
        ],
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  const _InfoRow({
    required this.label,
    required this.value,
    this.icon,
  });

  final String label;
  final String value;
  final IconData? icon;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Row(
              children: [
                if (icon != null) ...[
                  Icon(icon, size: 16, color: DoctorAccountScreen._muted),
                  const SizedBox(width: 6),
                ],
                Expanded(
                  child: Text(
                    value,
                    style: GoogleFonts.cairo(
                      fontSize: 13.5,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF1A1A1A),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 12),
          Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: DoctorAccountScreen._muted,
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
            border: Border.all(color: DoctorAccountScreen._border),
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
                color: DoctorAccountScreen._muted,
                size: 22,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
