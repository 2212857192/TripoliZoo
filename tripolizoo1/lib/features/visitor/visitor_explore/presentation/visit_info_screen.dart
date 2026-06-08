import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/shared/constants/ticket_data.dart';

class VisitInfoScreen extends StatefulWidget {
  const VisitInfoScreen({super.key});

  @override
  State<VisitInfoScreen> createState() => _VisitInfoScreenState();
}

class _VisitInfoScreenState extends State<VisitInfoScreen> {
  bool isLocal = true;

  @override
  Widget build(BuildContext context) {
    final tickets = isLocal ? TicketData.local : TicketData.foreign;

    return Scaffold(
      backgroundColor: AppColors.background,
      body: Directionality(
        textDirection: TextDirection.rtl,
        child: CustomScrollView(
          slivers: [
            SliverAppBar(
              expandedHeight: 220,
              pinned: true,
              backgroundColor: AppColors.primaryDark,
              leading: IconButton(
                icon: const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white, size: 20),
                onPressed: () => context.pop(),
              ),
              flexibleSpace: FlexibleSpaceBar(
                title: const Text(
                  'معلومات الزيارة',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 18,
                    color: Colors.white,
                  ),
                ),
                background: Stack(
                  fit: StackFit.expand,
                  children: [
                    Image.asset('assets/images/Hello2.jpg', fit: BoxFit.cover),
                    Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [
                            Colors.transparent,
                            AppColors.primaryDark.withValues(alpha: 0.8),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          SliverPadding(
            padding: const EdgeInsets.all(20),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                Row(
                  children: [
                    Expanded(
                      child: _StatChip(
                        icon: Icons.access_time,
                        label: 'ساعات العمل',
                        value: AppConstants.workingHours,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _StatChip(
                        icon: Icons.calendar_month,
                        label: 'أيام العمل',
                        value: AppConstants.workingDays,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                _SectionCard(
                  title: 'تذاكر الدخول',
                  trailing: _Toggle(isLocal: isLocal, onChanged: (v) => setState(() => isLocal = v)),
                  child: Column(
                    children: tickets.map((t) => Padding(
                      padding: const EdgeInsets.only(bottom: 12),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Icon(t.icon, color: AppColors.primary, size: 20),
                              const SizedBox(width: 10),
                              Text(t.categoryLabel,
                                  style: const TextStyle(fontWeight: FontWeight.w600)),
                            ],
                          ),
                          Text('${t.price} د.ل',
                              style: const TextStyle(
                                  fontWeight: FontWeight.w900,
                                  color: AppColors.accent,
                                  fontSize: 16)),
                        ],
                      ),
                    )).toList(),
                  ),
                ),
                const SizedBox(height: 20),
                GestureDetector(
                  onTap: () => context.go('/tickets'),
                  child: Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF1B5E20), Color(0xFF2E7D32)],
                      ),
                      borderRadius: BorderRadius.circular(24),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        const Row(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            Text('شراء تذكرة إلكترونية',
                                style: TextStyle(
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 16)),
                            SizedBox(width: 10),
                            Icon(Icons.qr_code_2, color: Colors.white),
                          ],
                        ),
                        const SizedBox(height: 10),
                        Text(
                          'اصدر تذكرتك مباشرة وادخل عبر QR Code',
                          textAlign: TextAlign.right,
                          style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.7),
                              fontSize: 13),
                        ),
                        const SizedBox(height: 20),
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton(
                            onPressed: () => context.go('/tickets'),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.white,
                              foregroundColor: AppColors.primary,
                              padding: const EdgeInsets.symmetric(vertical: 14),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12),
                              ),
                            ),
                            child: const Text('شراء تذكرة الآن', style: TextStyle(fontWeight: FontWeight.bold)),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                _InfoBanner(
                  title: 'دخول مجاني',
                  items: const [
                    'الأطفال دون سن 3 سنوات',
                    'ذوي الاحتياجات الخاصة',
                  ],
                  color: AppColors.primary,
                ),
                const SizedBox(height: 16),
                _InfoBanner(
                  title: 'تعليمات هامة',
                  items: const [
                    'إشراف دائم على الأطفال',
                    'الالتزام بالمسارات المحددة',
                    'يمنع إطعام الحيوانات',
                  ],
                  color: AppColors.primaryDark,
                ),
                const SizedBox(height: 16),
                _InfoBanner(
                  title: 'يُمنع إدخال',
                  items: const [
                    'الأدوات الحادة',
                    'الدراجات',
                    'مكبرات الصوت',
                  ],
                  color: AppColors.error,
                ),
                const SizedBox(height: 40),
              ]),
            ),
            ),
          ],
        ),
      ),
    );
  }
}

class _StatChip extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _StatChip({required this.icon, required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 12),
        ],
      ),
      child: Column(
        children: [
          Icon(icon, color: AppColors.primary, size: 26),
          const SizedBox(height: 8),
          Text(label, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
        ],
      ),
    );
  }
}

class _SectionCard extends StatelessWidget {
  final String title;
  final Widget? trailing;
  final Widget child;

  const _SectionCard({required this.title, this.trailing, required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 16),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(title,
                  style: const TextStyle(
                      fontSize: 18, fontWeight: FontWeight.bold)),
              if (trailing != null) trailing!,
            ],
          ),
          const SizedBox(height: 16),
          child,
        ],
      ),
    );
  }
}

class _Toggle extends StatelessWidget {
  final bool isLocal;
  final ValueChanged<bool> onChanged;

  const _Toggle({required this.isLocal, required this.onChanged});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(4),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          _btn('أجانب', !isLocal, () => onChanged(false)),
          _btn('ليبيون', isLocal, () => onChanged(true)),
        ],
      ),
    );
  }

  Widget _btn(String label, bool active, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: active ? AppColors.background : Colors.transparent,
          borderRadius: BorderRadius.circular(10),
          boxShadow: active
              ? [BoxShadow(color: Colors.black.withValues(alpha: 0.06), blurRadius: 8)]
              : null,
        ),
        child: Text(label,
            style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 12,
                color: active ? AppColors.primary : AppColors.textSecondary)),
      ),
    );
  }
}

class _InfoBanner extends StatelessWidget {
  final String title;
  final List<String> items;
  final Color color;

  const _InfoBanner({
    required this.title,
    required this.items,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: color,
        borderRadius: BorderRadius.circular(24),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title,
              style: const TextStyle(
                  color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
          const Divider(color: Colors.white24, height: 24),
          ...items.map((item) => Padding(
                padding: const EdgeInsets.only(bottom: 6),
                child: Text('• $item',
                    textAlign: TextAlign.right,
                    style: const TextStyle(color: Colors.white, fontSize: 13)),
              )),
        ],
      ),
    );
  }
}
