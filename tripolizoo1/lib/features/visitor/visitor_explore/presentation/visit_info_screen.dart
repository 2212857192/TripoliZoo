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
              ar: '╪ز╪╣╪░╪▒ ┘╪ز╪ص ┘à┘ê┘é╪╣ ╪د┘╪ص╪»┘è┘é╪ر',
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
                            ar: '╪│╪د╪╣╪د╪ز ╪د┘╪╣┘à┘',
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
                            ar: '╪ث┘è╪د┘à ╪د┘╪╣┘à┘',
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
                      ar: '┘à┘ê┘é╪╣ ╪د┘╪ص╪»┘è┘é╪ر',
                      en: 'Zoo Location',
                    ),
                    icon: Icons.location_on_outlined,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Text(
                          context.localized(
                            ar: '╪ص╪»┘è┘é╪ر ╪ص┘è┘ê╪د┘ ╪╖╪▒╪د╪ذ┘╪│╪î ╪╖╪▒╪د╪ذ┘╪│╪î ┘┘è╪ذ┘è╪د',
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
                            ar: '╪د┘╪ز╪ص ╪د┘┘à┘ê┘é╪╣ ┘┘è ╪«╪▒╪د╪خ╪╖ Google ┘┘╪ص╪╡┘ê┘ ╪╣┘┘ë ╪د┘╪د╪ز╪ش╪د┘ç╪د╪ز ┘ê╪د┘┘ê╪╡┘ê┘ ╪ذ╪│┘ç┘ê┘╪ر.',
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
                              ar: '┘╪ز╪ص ┘┘è ╪«╪▒╪د╪خ╪╖ Google',
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
                      ar: '╪ث╪│╪╣╪د╪▒ ╪ز╪░╪د┘â╪▒ ╪د┘╪»╪«┘ê┘',
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
                                '${ticket.price} ${context.localized(ar: '╪».┘', en: 'LYD')}',
                          ),
                        ),
                        const Divider(height: 24),
                        _PriceRow(
                          icon: Icons.child_friendly_outlined,
                          label: context.localized(
                            ar: '╪د┘╪ث╪╖┘╪د┘ ╪»┘ê┘ 3 ╪│┘┘ê╪د╪ز',
                            en: 'Children under 3',
                          ),
                          price: context.localized(ar: '┘à╪ش╪د┘┘è', en: 'Free'),
                          isFree: true,
                        ),
                        _PriceRow(
                          icon: Icons.accessible_rounded,
                          label: context.localized(
                            ar: '╪░┘ê┘ê ╪د┘╪د╪ص╪ز┘è╪د╪ش╪د╪ز ╪د┘╪«╪د╪╡╪ر',
                            en: 'Visitors with disabilities',
                          ),
                          price: context.localized(ar: '┘à╪ش╪د┘┘è', en: 'Free'),
                          isFree: true,
                          addBottomSpacing: false,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  _SectionCard(
                    title: context.localized(
                      ar: '╪د┘╪ز╪░┘â╪▒╪ر ╪د┘╪ح┘┘â╪ز╪▒┘ê┘┘è╪ر',
                      en: 'Electronic Ticket',
                    ),
                    icon: Icons.qr_code_2_rounded,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Text(
                          context.localized(
                            ar: '╪د╪«╪ز╪▒ ╪د┘┘╪خ╪ر ┘ê╪د┘╪╣╪»╪»╪î ╪ث┘â┘à┘ ╪د┘╪»┘╪╣╪î ╪س┘à ╪د╪│╪ز╪«╪»┘à ╪▒┘à╪▓ QR ┘┘╪»╪«┘ê┘.',
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
                              ar: '╪┤╪▒╪د╪ة ╪ز╪░┘â╪▒╪ر',
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
                      ar: '╪ز╪╣┘┘è┘à╪د╪ز ┘ê╪ح╪▒╪┤╪د╪»╪د╪ز ╪د┘╪▓┘è╪د╪▒╪ر',
                      en: 'Visit Guidelines',
                    ),
                    icon: Icons.info_outline_rounded,
                    child: _GuidanceList(
                      items: [
                        context.localized(
                          ar: '┘è╪ش╪ذ ╪د┘╪ح╪┤╪▒╪د┘ ╪╣┘┘ë ╪د┘╪ث╪╖┘╪د┘ ╪╖┘ê╪د┘ ┘ê┘é╪ز ╪د┘╪▓┘è╪د╪▒╪ر.',
                          en: 'Children must be supervised throughout the visit.',
                        ),
                        context.localized(
                          ar: '╪د┘╪د┘╪ز╪▓╪د┘à ╪ذ╪د┘┘à╪│╪د╪▒╪د╪ز ┘ê╪د┘┘┘ê╪ص╪د╪ز ╪د┘╪ح╪▒╪┤╪د╪»┘è╪ر ┘ê╪ز╪╣┘┘è┘à╪د╪ز ╪د┘┘à┘ê╪╕┘┘è┘.',
                          en: 'Follow marked paths, signs, and staff instructions.',
                        ),
                        context.localized(
                          ar: '┘è┘à┘╪╣ ╪ح╪╖╪╣╪د┘à ╪د┘╪ص┘è┘ê╪د┘╪د╪ز ╪ث┘ê ╪د┘╪د┘é╪ز╪▒╪د╪ذ ┘à┘ ╪د┘╪ص┘ê╪د╪ش╪▓.',
                          en: 'Do not feed animals or approach enclosure barriers.',
                        ),
                        context.localized(
                          ar: '┘è┘à┘╪╣ ╪ح╪»╪«╪د┘ ╪د┘╪ث╪»┘ê╪د╪ز ╪د┘╪ص╪د╪»╪ر ┘ê╪د┘╪»╪▒╪د╪ش╪د╪ز ┘ê┘à┘â╪ذ╪▒╪د╪ز ╪د┘╪╡┘ê╪ز.',
                          en: 'Sharp tools, bicycles, and loudspeakers are prohibited.',
                        ),
                        context.localized(
                          ar: '╪د┘┘à╪ص╪د┘╪╕╪ر ╪╣┘┘ë ╪د┘┘╪╕╪د┘╪ر ┘ê┘ê╪╢╪╣ ╪د┘┘à╪«┘┘╪د╪ز ┘┘è ╪د┘╪ث┘à╪د┘â┘ ╪د┘┘à╪«╪╡╪╡╪ر.',
                          en: 'Keep the zoo clean and use designated waste bins.',
                        ),
                        context.localized(
                          ar: '╪د╪ص╪ز┘╪╕ ╪ذ╪د┘╪ز╪░┘â╪▒╪ر ┘ê╪▒┘à╪▓ QR ╪ش╪د┘ç╪▓┘è┘ ╪╣┘╪» ╪ذ┘ê╪د╪ذ╪ر ╪د┘╪»╪«┘ê┘.',
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
                  ar: '┘à╪╣┘┘ê┘à╪د╪ز ╪د┘╪▓┘è╪د╪▒╪ر',
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
            context.localized(ar: '╪ث╪ش╪د┘╪ذ', en: 'Foreign'),
            !isLocal,
            () => onChanged(false),
          ),
          _btn(
            context.localized(ar: '┘à┘ê╪د╪╖┘┘ê┘', en: 'Citizens'),
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
