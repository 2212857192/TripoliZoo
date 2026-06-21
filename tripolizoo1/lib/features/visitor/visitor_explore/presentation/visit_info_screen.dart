import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/visit_info_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/visit_info.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';

class VisitInfoScreen extends StatefulWidget {
  const VisitInfoScreen({super.key, this.repository});

  final VisitInfoRepository? repository;

  @override
  State<VisitInfoScreen> createState() => _VisitInfoScreenState();
}

class _VisitInfoScreenState extends State<VisitInfoScreen> {
  late final VisitInfoRepository _repository =
      widget.repository ?? ApiVisitInfoRepository();
  late Future<VisitInfo> _visitInfoFuture = _repository.fetch();

  void _reload() {
    setState(() {
      _visitInfoFuture = _repository.fetch();
    });
  }

  Future<void> _callPhone(String phone) async {
    final uri = Uri(scheme: 'tel', path: phone.replaceAll(' ', ''));
    final launched = await launchUrl(uri);
    if (!launched && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            context.localized(
              ar: 'تعذر فتح تطبيق الاتصال',
              en: 'Unable to open the phone app',
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
        child: FutureBuilder<VisitInfo>(
          future: _visitInfoFuture,
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

            final info = snapshot.data!;

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
                              icon: Icons.schedule_outlined,
                              label: context.localized(
                                ar: 'آخر موعد للدخول',
                                en: 'Last Entry',
                              ),
                              value: info.lastTicketTimeNote?.isNotEmpty == true
                                  ? info.lastTicketTimeNote!
                                  : context.localized(ar: '—', en: '—'),
                            ),
                          ),
                        ],
                      ),
                      if (info.workingDays.isNotEmpty) ...[
                        const SizedBox(height: 12),
                        _StatChip(
                          icon: Icons.event_available_outlined,
                          label: context.localized(
                            ar: 'نمط العمل',
                            en: 'Schedule',
                          ),
                          value: isArabic
                              ? info.workingDays
                              : context.localized(
                                  ar: info.workingDays,
                                  en: 'Open daily',
                                ),
                          fullWidth: true,
                        ),
                      ],
                      if (_hasEmergencyContacts(info)) ...[
                        const SizedBox(height: 20),
                        _SectionCard(
                          title: context.localized(
                            ar: 'أرقام الطوارئ',
                            en: 'Emergency Contacts',
                          ),
                          icon: Icons.phone_in_talk_outlined,
                          child: Column(
                            children: [
                              if (info.ambulancePhone?.isNotEmpty ?? false)
                                _ContactRow(
                                  icon: Icons.local_hospital_outlined,
                                  label: context.localized(
                                    ar: 'الإسعاف',
                                    en: 'Ambulance',
                                  ),
                                  phone: info.ambulancePhone!,
                                  onTap: () => _callPhone(info.ambulancePhone!),
                                ),
                              if (info.securityPhone?.isNotEmpty ?? false)
                                _ContactRow(
                                  icon: Icons.security_outlined,
                                  label: context.localized(
                                    ar: 'الأمن',
                                    en: 'Security',
                                  ),
                                  phone: info.securityPhone!,
                                  onTap: () => _callPhone(info.securityPhone!),
                                ),
                            ],
                          ),
                        ),
                      ],
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

  bool _hasEmergencyContacts(VisitInfo info) {
    return (info.ambulancePhone?.isNotEmpty ?? false) ||
        (info.securityPhone?.isNotEmpty ?? false);
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
  final IconData icon;
  final String label;
  final String value;
  final bool fullWidth;

  const _StatChip({
    required this.icon,
    required this.label,
    required this.value,
    this.fullWidth = false,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: fullWidth ? double.infinity : null,
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
              textAlign: TextAlign.center,
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
  final Widget child;

  const _SectionCard({
    required this.title,
    required this.icon,
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

class _ContactRow extends StatelessWidget {
  const _ContactRow({
    required this.icon,
    required this.label,
    required this.phone,
    required this.onTap,
  });

  final IconData icon;
  final String label;
  final String phone;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 4),
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
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      label,
                      style: const TextStyle(
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    Text(
                      phone,
                      style: const TextStyle(
                        fontSize: 13,
                        color: AppColors.primary,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
              const Icon(Icons.call_outlined, color: AppColors.primary, size: 18),
            ],
          ),
        ),
      ),
    );
  }
}

class _GuidanceList extends StatelessWidget {
  final List<String> items;
  final String emptyMessage;

  const _GuidanceList({
    required this.items,
    required this.emptyMessage,
  });

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
