import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/visit_info_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/visit_info.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/data/ticket_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';

class _VisitInfoBundle {
  const _VisitInfoBundle({
    required this.info,
    required this.ticketTypes,
  });

  final VisitInfo info;
  final List<TicketType> ticketTypes;
}

class VisitInfoScreen extends StatefulWidget {
  const VisitInfoScreen({
    super.key,
    this.repository,
    this.ticketRepository,
  });

  final VisitInfoRepository? repository;
  final TicketRepository? ticketRepository;

  @override
  State<VisitInfoScreen> createState() => _VisitInfoScreenState();
}

class _VisitInfoScreenState extends State<VisitInfoScreen> {
  late final VisitInfoRepository _repository =
      widget.repository ?? ApiVisitInfoRepository();
  late final TicketRepository _ticketRepository =
      widget.ticketRepository ?? ApiTicketRepository();
  late Future<_VisitInfoBundle> _bundleFuture = _loadBundle();
  bool _showLocalTickets = true;

  Future<_VisitInfoBundle> _loadBundle() async {
    final results = await Future.wait([
      _repository.fetch(),
      _ticketRepository.fetchTypes(),
    ]);

    return _VisitInfoBundle(
      info: results[0] as VisitInfo,
      ticketTypes: results[1] as List<TicketType>,
    );
  }

  void _reload() {
    setState(() {
      _bundleFuture = _loadBundle();
    });
  }

  Future<void> _openGoogleMaps(String url) async {
    final uri = Uri.parse(url);
    final launched = await launchUrl(uri, mode: LaunchMode.externalApplication);
    if (!launched && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            context.localized(
              ar: 'تعذر فتح خرائط Google',
              en: 'Unable to open Google Maps',
            ),
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isArabic = Localizations.localeOf(context).languageCode == 'ar';

    return Scaffold(
      backgroundColor: AppColors.background,
      body: Directionality(
        textDirection: isArabic ? TextDirection.rtl : TextDirection.ltr,
        child: FutureBuilder<_VisitInfoBundle>(
          future: _bundleFuture,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }

            if (snapshot.hasError) {
              return Center(
                child: Padding(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        context.localized(
                          ar: 'تعذر تحميل معلومات الزيارة',
                          en: 'Unable to load visit information',
                        ),
                        textAlign: TextAlign.center,
                        style: const TextStyle(
                          fontWeight: FontWeight.w700,
                          color: AppColors.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 16),
                      FilledButton(
                        onPressed: _reload,
                        child: Text(
                          context.localized(ar: 'إعادة المحاولة', en: 'Retry'),
                        ),
                      ),
                    ],
                  ),
                ),
              );
            }

            final bundle = snapshot.data!;
            final info = bundle.info;
            final visibleTickets = bundle.ticketTypes
                .where((ticket) => ticket.isLocal == _showLocalTickets)
                .toList();

            return CustomScrollView(
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
                      if (info.statusVisible &&
                          (info.statusText?.isNotEmpty ?? false)) ...[
                        _AlertBanner(
                          title: context.localized(
                            ar: 'حالة التشغيل',
                            en: 'Operating Status',
                          ),
                          message: info.statusText!,
                        ),
                        const SizedBox(height: 16),
                      ],
                      if (info.urgentAlert?.isNotEmpty ?? false) ...[
                        _AlertBanner(
                          title: context.localized(
                            ar: 'تنبيه للزوار',
                            en: 'Visitor Alert',
                          ),
                          message: info.urgentAlert!,
                          tone: _AlertTone.warning,
                        ),
                        const SizedBox(height: 16),
                      ],
                      Row(
                        children: [
                          Expanded(
                            child: _StatChip(
                              icon: Icons.access_time,
                              label: context.localized(
                                ar: 'ساعات العمل',
                                en: 'Opening Hours',
                              ),
                              value: info.workingHours,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: _StatChip(
                              icon: Icons.calendar_today_outlined,
                              label: context.localized(
                                ar: 'أيام العمل',
                                en: 'Working Days',
                              ),
                              value: info.workingDays,
                            ),
                          ),
                        ],
                      ),
                      if (info.location?.hasAddress ?? false) ...[
                        const SizedBox(height: 20),
                        _LocationCard(
                          address: info.location!.address!,
                          onOpenMaps: info.location!.mapsUrl == null
                              ? null
                              : () => _openGoogleMaps(info.location!.mapsUrl!),
                        ),
                      ],
                      if (visibleTickets.isNotEmpty ||
                          bundle.ticketTypes.isNotEmpty) ...[
                        const SizedBox(height: 20),
                        _TicketPricesCard(
                          showLocal: _showLocalTickets,
                          tickets: visibleTickets,
                          onAudienceChanged: (isLocal) {
                            setState(() => _showLocalTickets = isLocal);
                          },
                        ),
                      ],
                      if (info.guidelinesWithNotes().isNotEmpty) ...[
                        const SizedBox(height: 20),
                        _SectionCard(
                          title: context.localized(
                            ar: 'تعليمات وإرشادات الزيارة',
                            en: 'Visit Guidelines',
                          ),
                          icon: Icons.info_outline_rounded,
                          child: _GuidanceList(
                            items: info.guidelinesWithNotes(),
                            emptyMessage: context.localized(
                              ar: 'لا توجد تعليمات منشورة حالياً.',
                              en: 'No published guidelines at the moment.',
                            ),
                          ),
                        ),
                      ],
                      const SizedBox(height: 40),
                    ]),
                  ),
                ),
              ],
            );
          },
        ),
      ),
    );
  }
}

enum _AlertTone { info, warning }

class _AlertBanner extends StatelessWidget {
  const _AlertBanner({
    required this.title,
    required this.message,
    this.tone = _AlertTone.info,
  });

  final String title;
  final String message;
  final _AlertTone tone;

  @override
  Widget build(BuildContext context) {
    final colors = tone == _AlertTone.warning
        ? (
            background: const Color(0xFFFFF7ED),
            border: const Color(0xFFFED7AA),
            title: const Color(0xFF9A3412),
            body: const Color(0xFF7C2D12),
          )
        : (
            background: const Color(0xFFFEF9C3),
            border: const Color(0xFFFDE68A),
            title: const Color(0xFF854D0E),
            body: const Color(0xFF451A03),
          );

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: colors.background,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: colors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: TextStyle(
              color: colors.title,
              fontWeight: FontWeight.w800,
              fontSize: 13,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            message,
            style: TextStyle(
              color: colors.body,
              height: 1.6,
              fontSize: 13,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
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
  const _StatChip({
    required this.icon,
    required this.label,
    required this.value,
  });

  final IconData icon;
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 12,
          ),
        ],
      ),
      child: Column(
        children: [
          Icon(icon, color: AppColors.primary, size: 26),
          const SizedBox(height: 8),
          Text(
            label,
            style: const TextStyle(
              fontSize: 11,
              color: AppColors.textSecondary,
            ),
          ),
          Text(
            value,
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 13,
            ),
          ),
        ],
      ),
    );
  }
}

class _LocationCard extends StatelessWidget {
  const _LocationCard({
    required this.address,
    this.onOpenMaps,
  });

  final String address;
  final VoidCallback? onOpenMaps;

  @override
  Widget build(BuildContext context) {
    return _SectionCard(
      title: context.localized(
        ar: 'موقع الحديقة',
        en: 'Park Location',
      ),
      icon: Icons.location_on_outlined,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            address,
            style: const TextStyle(
              fontWeight: FontWeight.w700,
              fontSize: 14,
              color: AppColors.textPrimary,
              height: 1.5,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            context.localized(
              ar: 'يمكنك فتح الموقع على Google Maps للحصول على الاتجاهات.',
              en: 'Open the location in Google Maps for directions.',
            ),
            style: const TextStyle(
              fontSize: 12,
              color: AppColors.textSecondary,
              height: 1.5,
            ),
          ),
          if (onOpenMaps != null) ...[
            const SizedBox(height: 16),
            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: onOpenMaps,
                style: OutlinedButton.styleFrom(
                  foregroundColor: AppColors.primary,
                  side: const BorderSide(color: AppColors.primary, width: 1.5),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
                icon: const Icon(Icons.map_outlined, size: 20),
                label: Text(
                  context.localized(
                    ar: 'فتح في خرائط Google',
                    en: 'Open in Google Maps',
                  ),
                  style: const TextStyle(fontWeight: FontWeight.w700),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}

class _TicketPricesCard extends StatelessWidget {
  const _TicketPricesCard({
    required this.showLocal,
    required this.tickets,
    required this.onAudienceChanged,
  });

  final bool showLocal;
  final List<TicketType> tickets;
  final ValueChanged<bool> onAudienceChanged;

  @override
  Widget build(BuildContext context) {
    return _SectionCard(
      title: context.localized(
        ar: 'أسعار تذاكر الدخول',
        en: 'Entrance Ticket Prices',
      ),
      icon: Icons.confirmation_number_outlined,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _AudienceToggle(
            showLocal: showLocal,
            onChanged: onAudienceChanged,
          ),
          const SizedBox(height: 16),
          if (tickets.isEmpty)
            Text(
              context.localized(
                ar: 'لا توجد أسعار منشورة لهذه الفئة حالياً.',
                en: 'No prices published for this category yet.',
              ),
              style: const TextStyle(
                fontSize: 13,
                color: AppColors.textSecondary,
              ),
            )
          else
            ...tickets.map(
              (ticket) => _TicketPriceRow(
                ticket: ticket,
                isArabic:
                    Localizations.localeOf(context).languageCode == 'ar',
              ),
            ),
        ],
      ),
    );
  }
}

class _AudienceToggle extends StatelessWidget {
  const _AudienceToggle({
    required this.showLocal,
    required this.onChanged,
  });

  final bool showLocal;
  final ValueChanged<bool> onChanged;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(4),
      decoration: BoxDecoration(
        color: const Color(0xFFF3F4F6),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        children: [
          Expanded(
            child: _ToggleOption(
              label: context.localized(ar: 'مواطنون', en: 'Citizens'),
              selected: showLocal,
              onTap: () => onChanged(true),
            ),
          ),
          Expanded(
            child: _ToggleOption(
              label: context.localized(ar: 'أجانب', en: 'Foreigners'),
              selected: !showLocal,
              onTap: () => onChanged(false),
            ),
          ),
        ],
      ),
    );
  }
}

class _ToggleOption extends StatelessWidget {
  const _ToggleOption({
    required this.label,
    required this.selected,
    required this.onTap,
  });

  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: selected ? Colors.white : Colors.transparent,
          borderRadius: BorderRadius.circular(10),
          boxShadow: selected
              ? [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.06),
                    blurRadius: 8,
                  ),
                ]
              : null,
        ),
        alignment: Alignment.center,
        child: Text(
          label,
          style: TextStyle(
            fontWeight: FontWeight.w700,
            fontSize: 13,
            color: selected ? AppColors.textPrimary : AppColors.textSecondary,
          ),
        ),
      ),
    );
  }
}

class _TicketPriceRow extends StatelessWidget {
  const _TicketPriceRow({
    required this.ticket,
    required this.isArabic,
  });

  final TicketType ticket;
  final bool isArabic;

  @override
  Widget build(BuildContext context) {
    final category = ticket.name?.isNotEmpty == true ? ticket.name! : ticket.title;
    final ageLabel = ticket.subtitle;

    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            isArabic ? '${ticket.price} د.ل' : '${ticket.price} LYD',
            style: const TextStyle(
              fontWeight: FontWeight.w800,
              fontSize: 15,
              color: AppColors.primaryDark,
            ),
          ),
          const Spacer(),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(
                  category,
                  textAlign: TextAlign.end,
                  style: const TextStyle(
                    fontWeight: FontWeight.w700,
                    fontSize: 14,
                    color: AppColors.textPrimary,
                  ),
                ),
                if (ageLabel.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(
                    ageLabel,
                    textAlign: TextAlign.end,
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textSecondary,
                    ),
                  ),
                ],
              ],
            ),
          ),
          const SizedBox(width: 10),
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: const Color(0xFFF2F7F2),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(ticket.icon, color: AppColors.primary, size: 20),
          ),
        ],
      ),
    );
  }
}

class _SectionCard extends StatelessWidget {
  const _SectionCard({
    required this.title,
    required this.icon,
    required this.child,
  });

  final String title;
  final IconData icon;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 16,
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
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
          const SizedBox(height: 16),
          child,
        ],
      ),
    );
  }
}

class _GuidanceList extends StatelessWidget {
  const _GuidanceList({
    required this.items,
    required this.emptyMessage,
  });

  final List<String> items;
  final String emptyMessage;

  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) {
      return Text(
        emptyMessage,
        style: const TextStyle(
          height: 1.6,
          fontSize: 13,
          color: AppColors.textSecondary,
        ),
      );
    }

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
