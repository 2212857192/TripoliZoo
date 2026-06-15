import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/shared/constants/ticket_data.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';

class VisitInfoScreen extends StatefulWidget {
  const VisitInfoScreen({super.key});

  @override
  State<VisitInfoScreen> createState() => _VisitInfoScreenState();
}

class _VisitInfoScreenState extends State<VisitInfoScreen> {
  bool isLocal = true;

  String _ticketTitle(BuildContext context, String id, String fallback) {
    if (Localizations.localeOf(context).languageCode == 'ar') return fallback;
    return switch (id) {
      'adult_ly' || 'adult_intl' => 'Adult',
      'child_ly' || 'child_intl' => 'Child',
      'student' => 'Student',
      _ => fallback,
    };
  }

  Future<void> _openGoogleMaps() async {
    final uri = Uri.https(
      'www.google.com',
      '/maps/search/',
      {
        'api': '1',
        'query': '${AppConstants.zooLatitude},${AppConstants.zooLongitude}',
      },
    );
    final launched = await launchUrl(
      uri,
      mode: LaunchMode.externalApplication,
    );
    if (!launched && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            context.localized(
              ar: 'تعذر فتح موقع الحديقة',
              en: 'Unable to open the zoo location',
            ),
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final tickets = isLocal ? TicketData.local : TicketData.foreign;
    final isArabic = Localizations.localeOf(context).languageCode == 'ar';

    return Scaffold(
      backgroundColor: AppColors.background,
      body: Directionality(
        textDirection: isArabic ? TextDirection.rtl : TextDirection.ltr,
        child: CustomScrollView(
          slivers: [
            SliverAppBar(
              expandedHeight: 220,
              pinned: true,
              backgroundColor: Colors.white,
              foregroundColor: AppColors.textPrimary,
              surfaceTintColor: Colors.white,
              leading: Padding(
                padding: const EdgeInsets.all(8),
                child: CircleAvatar(
                  backgroundColor: Colors.white.withValues(alpha: 0.9),
                  child: IconButton(
                    icon: const Icon(
                      Icons.arrow_forward_ios_rounded,
                      color: AppColors.textPrimary,
                      size: 18,
                    ),
                    onPressed: () => context.pop(),
                  ),
                ),
              ),
              flexibleSpace: const _VisitInfoFlexibleHeader(),
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
                          label: context.localized(
                            ar: 'ساعات العمل',
                            en: 'Opening Hours',
                          ),
                          value: AppConstants.workingHours,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _StatChip(
                          icon: Icons.calendar_month,
                          label: context.localized(
                            ar: 'أيام العمل',
                            en: 'Working Days',
                          ),
                          value: context.localized(
                            ar: AppConstants.workingDays,
                            en: 'Every day',
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  _SectionCard(
                    title: context.localized(
                      ar: 'موقع الحديقة',
                      en: 'Zoo Location',
                    ),
                    icon: Icons.location_on_outlined,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Text(
                          context.localized(
                            ar: 'حديقة حيوان طرابلس، طرابلس، ليبيا',
                            en: 'Tripoli Zoo, Tripoli, Libya',
                          ),
                          style: const TextStyle(
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          context.localized(
                            ar: 'افتح الموقع في خرائط Google للحصول على الاتجاهات والوصول بسهولة.',
                            en: 'Open the location in Google Maps for directions and easy navigation.',
                          ),
                          style: const TextStyle(
                            height: 1.6,
                            fontSize: 12,
                            color: AppColors.textSecondary,
                          ),
                        ),
                        const SizedBox(height: 14),
                        OutlinedButton.icon(
                          key: const ValueKey('open-zoo-google-maps'),
                          onPressed: _openGoogleMaps,
                          icon: const Icon(Icons.map_outlined),
                          label: Text(
                            context.localized(
                              ar: 'فتح في خرائط Google',
                              en: 'Open in Google Maps',
                            ),
                          ),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: AppColors.primary,
                            side: const BorderSide(color: AppColors.primary),
                            padding: const EdgeInsets.symmetric(vertical: 13),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  _SectionCard(
                    title: context.localized(
                      ar: 'أسعار تذاكر الدخول',
                      en: 'Admission Prices',
                    ),
                    icon: Icons.confirmation_number_outlined,
                    trailing: _Toggle(
                      isLocal: isLocal,
                      onChanged: (value) => setState(() => isLocal = value),
                    ),
                    child: Column(
                      children: [
                        ...tickets.map(
                          (ticket) => _PriceRow(
                            icon: ticket.icon,
                            label: _ticketTitle(
                              context,
                              ticket.id,
                              ticket.title,
                            ),
                            price:
                                '${ticket.price} ${context.localized(ar: 'د.ل', en: 'LYD')}',
                          ),
                        ),
                        const Divider(height: 24),
                        _PriceRow(
                          icon: Icons.child_friendly_outlined,
                          label: context.localized(
                            ar: 'الأطفال دون 3 سنوات',
                            en: 'Children under 3',
                          ),
                          price: context.localized(ar: 'مجاني', en: 'Free'),
                          isFree: true,
                        ),
                        _PriceRow(
                          icon: Icons.accessible_rounded,
                          label: context.localized(
                            ar: 'ذوو الاحتياجات الخاصة',
                            en: 'Visitors with disabilities',
                          ),
                          price: context.localized(ar: 'مجاني', en: 'Free'),
                          isFree: true,
                          addBottomSpacing: false,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  _SectionCard(
                    title: context.localized(
                      ar: 'التذكرة الإلكترونية',
                      en: 'Electronic Ticket',
                    ),
                    icon: Icons.qr_code_2_rounded,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Text(
                          context.localized(
                            ar: 'اختر الفئة والعدد، أكمل الدفع، ثم استخدم رمز QR للدخول.',
                            en: 'Choose ticket types and quantities, complete payment, then use the QR code for entry.',
                          ),
                          style: const TextStyle(
                            height: 1.6,
                            fontSize: 13,
                            color: AppColors.textSecondary,
                          ),
                        ),
                        const SizedBox(height: 14),
                        FilledButton.icon(
                          key: const ValueKey('visit-info-buy-ticket'),
                          onPressed: () => context.go('/tickets'),
                          icon: const Icon(
                            Icons.arrow_forward_rounded,
                            size: 18,
                          ),
                          label: Text(
                            context.localized(
                              ar: 'شراء تذكرة',
                              en: 'Buy a Ticket',
                            ),
                          ),
                          style: FilledButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  _SectionCard(
                    title: context.localized(
                      ar: 'تعليمات وإرشادات الزيارة',
                      en: 'Visit Guidelines',
                    ),
                    icon: Icons.info_outline_rounded,
                    child: _GuidanceList(
                      items: [
                        context.localized(
                          ar: 'يجب الإشراف على الأطفال طوال وقت الزيارة.',
                          en: 'Children must be supervised throughout the visit.',
                        ),
                        context.localized(
                          ar: 'الالتزام بالمسارات واللوحات الإرشادية وتعليمات الموظفين.',
                          en: 'Follow marked paths, signs, and staff instructions.',
                        ),
                        context.localized(
                          ar: 'يمنع إطعام الحيوانات أو الاقتراب من الحواجز.',
                          en: 'Do not feed animals or approach enclosure barriers.',
                        ),
                        context.localized(
                          ar: 'يمنع إدخال الأدوات الحادة والدراجات ومكبرات الصوت.',
                          en: 'Sharp tools, bicycles, and loudspeakers are prohibited.',
                        ),
                        context.localized(
                          ar: 'المحافظة على النظافة ووضع المخلفات في الأماكن المخصصة.',
                          en: 'Keep the zoo clean and use designated waste bins.',
                        ),
                        context.localized(
                          ar: 'احتفظ بالتذكرة ورمز QR جاهزين عند بوابة الدخول.',
                          en: 'Keep your ticket and QR code ready at the entrance.',
                        ),
                      ],
                    ),
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

class _VisitInfoFlexibleHeader extends StatelessWidget {
  const _VisitInfoFlexibleHeader();

  @override
  Widget build(BuildContext context) {
    final topPadding = MediaQuery.paddingOf(context).top;

    return LayoutBuilder(
      builder: (context, constraints) {
        final minHeight = topPadding + kToolbarHeight;
        final progress =
            ((constraints.maxHeight - minHeight) / (220 - minHeight))
                .clamp(0.0, 1.0);

        return Stack(
          fit: StackFit.expand,
          children: [
            const ColoredBox(color: Colors.white),
            Opacity(
              opacity: progress,
              child: Stack(
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
            Positioned(
              left: 72,
              right: 72,
              bottom: 16,
              child: Text(
                context.localized(
                  ar: 'معلومات الزيارة',
                  en: 'Visit Information',
                ),
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 20,
                  color: Color.lerp(
                    AppColors.textPrimary,
                    Colors.white,
                    progress,
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}

class _StatChip extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _StatChip(
      {required this.icon, required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [
          BoxShadow(
              color: Colors.black.withValues(alpha: 0.04), blurRadius: 12),
        ],
      ),
      child: Column(
        children: [
          Icon(icon, color: AppColors.primary, size: 26),
          const SizedBox(height: 8),
          Text(label,
              style: const TextStyle(
                  fontSize: 11, color: AppColors.textSecondary)),
          Text(value,
              style:
                  const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
        ],
      ),
    );
  }
}

class _SectionCard extends StatelessWidget {
  final String title;
  final IconData icon;
  final Widget? trailing;
  final Widget child;

  const _SectionCard({
    required this.title,
    required this.icon,
    this.trailing,
    required this.child,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [
          BoxShadow(
              color: Colors.black.withValues(alpha: 0.04), blurRadius: 16),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Row(
                  children: [
                    Icon(icon, color: AppColors.primary, size: 21),
                    const SizedBox(width: 9),
                    Flexible(
                      child: Text(
                        title,
                        style: const TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primaryDark,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              if (trailing != null) const SizedBox(width: 10),
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
          _btn(
            context.localized(ar: 'أجانب', en: 'Foreign'),
            !isLocal,
            () => onChanged(false),
          ),
          _btn(
            context.localized(ar: 'مواطنون', en: 'Citizens'),
            isLocal,
            () => onChanged(true),
          ),
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
              ? [
                  BoxShadow(
                      color: Colors.black.withValues(alpha: 0.06),
                      blurRadius: 8)
                ]
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

class _PriceRow extends StatelessWidget {
  final IconData icon;
  final String label;
  final String price;
  final bool isFree;
  final bool addBottomSpacing;

  const _PriceRow({
    required this.icon,
    required this.label,
    required this.price,
    this.isFree = false,
    this.addBottomSpacing = true,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(bottom: addBottomSpacing ? 12 : 0),
      child: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: const Color(0xFFF2F7F2),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: AppColors.primary, size: 19),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              label,
              style: const TextStyle(
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary,
              ),
            ),
          ),
          Text(
            price,
            style: TextStyle(
              fontWeight: FontWeight.w900,
              color: isFree ? AppColors.primary : AppColors.textPrimary,
              fontSize: 15,
            ),
          ),
        ],
      ),
    );
  }
}

class _GuidanceList extends StatelessWidget {
  final List<String> items;

  const _GuidanceList({required this.items});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: items
          .map(
            (item) => Padding(
              padding: const EdgeInsets.only(bottom: 11),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 6,
                    height: 6,
                    margin: const EdgeInsets.only(top: 8),
                    decoration: const BoxDecoration(
                      color: AppColors.primary,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      item,
                      style: const TextStyle(
                        height: 1.6,
                        fontSize: 13,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          )
          .toList(),
    );
  }
}
