import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/shared/constants/ticket_data.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/services/ticket_image_service.dart';
import 'package:tripolizoo/shared/utils/date_formatters.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';
import 'package:tripolizoo/shared/router/ticket_access.dart';
import 'package:tripolizoo/shared/widgets/white_pinned_sliver_header.dart';

String _localizedTicketTitle(BuildContext context, TicketType type) {
  if (Localizations.localeOf(context).languageCode == 'ar') {
    return type.title;
  }
  return switch (type.id) {
    'adult_ly' || 'adult_intl' => 'Adult',
    'child_ly' || 'child_intl' => 'Child',
    'student' => 'Student',
    _ => type.title,
  };
}

String _localizedTicketSubtitle(BuildContext context, TicketType type) {
  if (Localizations.localeOf(context).languageCode == 'ar') {
    return type.subtitle;
  }
  return switch (type.id) {
    'adult_ly' || 'adult_intl' => 'Over 12 years',
    'child_ly' || 'child_intl' => 'From 3 to 12 years',
    'student' => 'Schools and universities',
    _ => type.subtitle,
  };
}

class TicketsScreen extends StatefulWidget {
  const TicketsScreen({super.key});

  @override
  State<TicketsScreen> createState() => _TicketsScreenState();
}

class _TicketsScreenState extends State<TicketsScreen> {
  int _step = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final cart = context.read<TicketCartProvider>();
      if (cart.purchasedTickets.isNotEmpty) {
        setState(() => _step = 2);
      }
    });
  }

  void _goToPayment(BuildContext context) {
    if (!context.read<AuthProvider>().hasAccount) {
      promptLoginForPurchase(context);
      return;
    }
    setState(() => _step = 1);
  }

  void _completePayment() {
    final tickets = context.read<TicketCartProvider>().purchase();
    if (tickets.isNotEmpty) {
      setState(() => _step = 2);
    }
  }

  void _goBack() {
    if (_step == 2 || _step == 1) {
      setState(() => _step = 0);
    } else {
      context.go('/home');
    }
  }

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Scaffold(
        backgroundColor: Colors.white,
        body: AnimatedSwitcher(
          duration: const Duration(milliseconds: 300),
          child: switch (_step) {
            0 => _SelectionView(
                onContinue: () => _goToPayment(context),
                onBack: _goBack,
              ),
            1 => _PaymentView(
                onBack: _goBack,
                onPaymentConfirmed: _completePayment,
              ),
            _ => _TicketView(onBack: _goBack),
          },
        ),
      ),
    );
  }
}

class _TicketsAccountRequiredView extends StatelessWidget {
  final VoidCallback onBack;

  const _TicketsAccountRequiredView({required this.onBack});

  void _openAuth(BuildContext context, String route) {
    context.read<AuthProvider>().logout();
    context.go(route);
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.paddingOf(context).top;
    final bottomPad = MediaQuery.paddingOf(context).bottom;

    return Column(
      children: [
        Container(
          color: Colors.white,
          padding: EdgeInsets.fromLTRB(20, topPad + 8, 20, 8),
          child: SizedBox(
            height: 56,
            child: CenteredPageHeader(
              title: context.localized(ar: '╪د┘╪ز╪░╪د┘â╪▒', en: 'Tickets'),
              leading: IconButton(
                onPressed: onBack,
                icon: const Icon(Icons.arrow_forward_ios_rounded),
              ),
            ),
          ),
        ),
        Expanded(
          child: SingleChildScrollView(
            padding: EdgeInsets.fromLTRB(24, 44, 24, bottomPad + 32),
            child: Container(
              key: const ValueKey('tickets-account-required'),
              width: double.infinity,
              padding: const EdgeInsets.all(28),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(28),
                border: Border.all(color: Colors.grey.shade200),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.06),
                    blurRadius: 18,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Column(
                children: [
                  Container(
                    width: 72,
                    height: 72,
                    decoration: const BoxDecoration(
                      color: Color(0xFFE8F5E9),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.account_circle_outlined,
                      color: AppColors.primary,
                      size: 38,
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    context.localized(
                      ar: '╪ز╪│╪ش┘è┘ ╪د┘╪»╪«┘ê┘ ┘à╪╖┘┘ê╪ذ',
                      en: 'Sign In Required',
                    ),
                    textAlign: TextAlign.center,
                    style: GoogleFonts.cairo(
                      fontSize: 21,
                      fontWeight: FontWeight.w900,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    context.localized(
                      ar: '╪┤╪▒╪د╪ة ╪د┘╪ز╪░╪د┘â╪▒ ╪د┘╪ح┘┘â╪ز╪▒┘ê┘┘è╪ر ┘ê╪ص┘╪╕┘ç╪د ┘┘è ╪ص╪│╪د╪ذ┘â ┘à╪ز╪د╪ص╪د┘ ┘┘┘à╪│╪ز╪«╪»┘à┘è┘ ╪د┘╪░┘è┘ ┘è┘à╪ز┘┘â┘ê┘ ╪ص╪│╪د╪ذ┘ï╪د ┘┘é╪╖.',
                      en: 'Electronic ticket purchases and saving tickets to your account are available only to registered users.',
                    ),
                    textAlign: TextAlign.center,
                    style: GoogleFonts.cairo(
                      fontSize: 13,
                      height: 1.7,
                      color: AppColors.textSecondary,
                    ),
                  ),
                  const SizedBox(height: 26),
                  FilledButton(
                    key: const ValueKey('tickets-login-button'),
                    onPressed: () => _openAuth(context, '/login'),
                    style: FilledButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      minimumSize: const Size.fromHeight(52),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                    ),
                    child: Text(
                      context.localized(
                        ar: '╪ز╪│╪ش┘è┘ ╪د┘╪»╪«┘ê┘',
                        en: 'Sign In',
                      ),
                      style: GoogleFonts.cairo(fontWeight: FontWeight.w800),
                    ),
                  ),
                  const SizedBox(height: 12),
                  OutlinedButton(
                    key: const ValueKey('tickets-register-button'),
                    onPressed: () => _openAuth(context, '/register'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppColors.primary,
                      minimumSize: const Size.fromHeight(52),
                      side: const BorderSide(color: AppColors.primary),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                    ),
                    child: Text(
                      context.localized(
                        ar: '╪ح┘╪┤╪د╪ة ╪ص╪│╪د╪ذ ╪ش╪»┘è╪»',
                        en: 'Create New Account',
                      ),
                      style: GoogleFonts.cairo(fontWeight: FontWeight.w800),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }
}

// ظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـ
// STEP 1 ظ¤ Selection View
// ظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـ
class _SelectionView extends StatefulWidget {
  final VoidCallback onContinue;
  final VoidCallback onBack;
  const _SelectionView({required this.onContinue, required this.onBack});

  @override
  State<_SelectionView> createState() => _SelectionViewState();
}

class _SelectionViewState extends State<_SelectionView> {
  int _tab = 0; // 0 = Citizens, 1 = Foreigners

  static const _bg = Colors.white;

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<TicketCartProvider>();
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final tickets = _tab == 0 ? TicketData.local : TicketData.foreign;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // ظ¤ظ¤ Header ظ¤ظ¤
        Container(
          color: _bg,
          padding: EdgeInsets.fromLTRB(20, topPad + 8, 20, 8),
          child: SizedBox(
            height: 56,
            child: CenteredPageHeader(
              title: context.localized(ar: '╪د┘╪ز╪░╪د┘â╪▒', en: 'Tickets'),
              leading: GestureDetector(
                onTap: widget.onBack,
                child: Container(
                  width: 38,
                  height: 38,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade100,
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.arrow_forward_ios_rounded,
                      size: 16, color: Colors.black87),
                ),
              ),
            ),
          ),
        ),

        // ظ¤ظ¤ Body ظ¤ظ¤
        Expanded(
          child: ListView(
            padding: EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 100),
            children: [
              const _VisitTicketInfoCard(),
              const SizedBox(height: 18),

              // ظ¤ظ¤ Tabs ظ¤ظ¤
              Container(
                padding: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: _TabBtn(
                        label:
                            context.localized(ar: '╪د┘┘à┘ê╪د╪╖┘┘ê┘', en: 'Citizens'),
                        active: _tab == 0,
                        onTap: () => setState(() => _tab = 0),
                      ),
                    ),
                    Expanded(
                      child: _TabBtn(
                        label:
                            context.localized(ar: '╪د┘╪ث╪ش╪د┘╪ذ', en: 'Foreigners'),
                        active: _tab == 1,
                        isRight: true,
                        onTap: () => setState(() => _tab = 1),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // ظ¤ظ¤ Ticket rows ظ¤ظ¤
              ...tickets.map((t) => _TicketRow(type: t)),
              const SizedBox(height: 16),
            ],
          ),
        ),

        // ظ¤ظ¤ Bottom Bar ظ¤ظ¤
        Container(
          padding: EdgeInsets.fromLTRB(20, 10, 20, bottomPad + 10),
          decoration: BoxDecoration(
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.05),
                blurRadius: 16,
                offset: const Offset(0, -4),
              ),
            ],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      context.localized(ar: '╪د┘╪ح╪ش┘à╪د┘┘è', en: 'TOTAL'),
                      style: GoogleFonts.cairo(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: Colors.grey.shade500,
                        letterSpacing: 1,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Row(
                      key: const ValueKey('ticket-total-and-count-row'),
                      children: [
                        Text(
                          '${cart.totalPrice.toStringAsFixed(0)} ${context.localized(ar: '╪».┘', en: 'LYD')}',
                          style: GoogleFonts.cairo(
                            fontSize: 21,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF1A1A1A),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Flexible(
                          child: Text(
                            context.localized(
                              ar: cart.totalVisitors == 1
                                  ? '(╪ز╪░┘â╪▒╪ر ┘ê╪د╪ص╪»╪ر)'
                                  : '(${cart.totalVisitors} ╪ز╪░╪د┘â╪▒)',
                              en: cart.totalVisitors == 1
                                  ? '(1 ticket)'
                                  : '(${cart.totalVisitors} tickets)',
                            ),
                            overflow: TextOverflow.ellipsis,
                            style: GoogleFonts.cairo(
                              fontSize: 11,
                              color: Colors.grey.shade500,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              SizedBox(
                key: const ValueKey('compact-next-button'),
                width: 120,
                height: 44,
                child: ElevatedButton(
                  key: const ValueKey('continue-to-payment-button'),
                  onPressed: cart.totalPrice > 0 ? widget.onContinue : null,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2E7D32),
                    foregroundColor: Colors.white,
                    disabledBackgroundColor: Colors.grey.shade200,
                    disabledForegroundColor: Colors.grey.shade400,
                    padding: const EdgeInsets.symmetric(horizontal: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(22),
                    ),
                    elevation: 0,
                  ),
                  child: FittedBox(
                    fit: BoxFit.scaleDown,
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          context.localized(ar: '╪د┘╪ز╪د┘┘è', en: 'Next'),
                          style: GoogleFonts.cairo(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(width: 6),
                        const Icon(Icons.arrow_back_ios_new_rounded, size: 14),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

// ظ¤ظ¤ Tab Button ظ¤ظ¤
class _TabBtn extends StatelessWidget {
  final String label;
  final bool active;
  final bool isRight;
  final VoidCallback onTap;

  const _TabBtn(
      {required this.label,
      required this.active,
      this.isRight = false,
      required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: active ? const Color(0xFF2E7D32) : Colors.transparent,
          borderRadius: BorderRadius.circular(12),
        ),
        child: Text(
          label,
          textAlign: TextAlign.center,
          style: GoogleFonts.cairo(
            fontSize: 14,
            fontWeight: FontWeight.w700,
            color: active ? Colors.white : Colors.grey.shade600,
          ),
        ),
      ),
    );
  }
}

// ظ¤ظ¤ Ticket Row ظ¤ظ¤
class _TicketRow extends StatelessWidget {
  final TicketType type;
  const _TicketRow({required this.type});

  static const _green = Color(0xFF2E7D32);

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<TicketCartProvider>();
    final qty = cart.cart[type.id] ?? 0;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade100, width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.03),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 46,
            height: 46,
            decoration: BoxDecoration(
              color: const Color(0xFFE8F5E9),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(type.icon, color: _green, size: 22),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _localizedTicketTitle(context, type),
                  style: GoogleFonts.cairo(
                    fontSize: 15,
                    fontWeight: FontWeight.bold,
                    color: const Color(0xFF1A1A1A),
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  '${_localizedTicketSubtitle(context, type)} ┬╖ ${type.price} ${context.localized(ar: '╪».┘', en: 'LYD')}',
                  style: GoogleFonts.cairo(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: Colors.grey.shade500,
                  ),
                ),
              ],
            ),
          ),
          // Counter
          Row(
            children: [
              _CounterBtn(
                icon: Icons.remove,
                enabled: qty > 0,
                filled: false,
                onTap: () =>
                    context.read<TicketCartProvider>().decrement(type.id),
              ),
              SizedBox(
                width: 36,
                child: Text(
                  '$qty',
                  textAlign: TextAlign.center,
                  style: GoogleFonts.cairo(
                      fontSize: 16, fontWeight: FontWeight.bold),
                ),
              ),
              _CounterBtn(
                icon: Icons.add,
                enabled: true,
                filled: true,
                onTap: () =>
                    context.read<TicketCartProvider>().increment(type.id),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

// ظ¤ظ¤ Counter Button ظ¤ظ¤
class _CounterBtn extends StatelessWidget {
  final IconData icon;
  final bool enabled;
  final bool filled;
  final VoidCallback onTap;

  const _CounterBtn(
      {required this.icon,
      required this.enabled,
      required this.filled,
      required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: enabled ? onTap : null,
      child: Container(
        width: 30,
        height: 30,
        decoration: BoxDecoration(
          color: filled
              ? (enabled ? const Color(0xFF2E7D32) : Colors.grey.shade200)
              : Colors.transparent,
          shape: BoxShape.circle,
          border: !filled
              ? Border.all(
                  color: enabled ? Colors.grey.shade400 : Colors.grey.shade200,
                  width: 1.5)
              : null,
        ),
        child: Icon(
          icon,
          size: 16,
          color: filled
              ? (enabled ? Colors.white : Colors.grey.shade400)
              : (enabled ? Colors.grey.shade600 : Colors.grey.shade300),
        ),
      ),
    );
  }
}

class _VisitTicketInfoCard extends StatelessWidget {
  const _VisitTicketInfoCard();

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 14,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              Container(
                width: 46,
                height: 46,
                decoration: const BoxDecoration(
                  color: Color(0xFFF0F7E8),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.schedule_rounded,
                  color: Color(0xFF2E7D32),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      context.localized(
                        ar: '┘à┘╪ز┘ê╪ص ╪د┘┘è┘ê┘à ┬╖ ${AppConstants.workingHours}',
                        en: 'Open today ┬╖ ${AppConstants.workingHours}',
                      ),
                      style: GoogleFonts.cairo(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    Text(
                      context.localized(
                        ar: '╪ت╪«╪▒ ╪»╪«┘ê┘ ┘é╪ذ┘ ┘à┘ê╪╣╪» ╪د┘╪ح╪║┘╪د┘é ╪ذ┘╪╡┘ ╪│╪د╪╣╪ر',
                        en: 'Last entry is 30 minutes before closing',
                      ),
                      style: GoogleFonts.cairo(
                        fontSize: 11,
                        color: Colors.grey.shade500,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const Divider(height: 28),
          Text(
            context.localized(
              ar: '╪د┘╪»╪«┘ê┘ ┘à╪ش╪د┘┘è ┘┘╪ث╪╖┘╪د┘ ╪»┘ê┘ ╪د┘╪س╪د┘╪س╪ر ┘ê┘╪░┘ê┘è ╪د┘╪د╪ص╪ز┘è╪د╪ش╪د╪ز ╪د┘╪«╪د╪╡╪ر.',
              en: 'Admission is free for children under three and visitors with disabilities.',
            ),
            style: GoogleFonts.cairo(
              fontSize: 12,
              height: 1.7,
              color: Colors.grey.shade700,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }
}

class _PaymentView extends StatefulWidget {
  final VoidCallback onBack;
  final VoidCallback onPaymentConfirmed;

  const _PaymentView({
    required this.onBack,
    required this.onPaymentConfirmed,
  });

  @override
  State<_PaymentView> createState() => _PaymentViewState();
}

class _PaymentViewState extends State<_PaymentView> {
  final _formKey = GlobalKey<FormState>();
  final _phoneController = TextEditingController();
  String _paymentMethod = 'edfa3ly';

  static const _green = Color(0xFF2E7D32);

  @override
  void dispose() {
    _phoneController.dispose();
    super.dispose();
  }

  void _confirmPayment() {
    if (!(_formKey.currentState?.validate() ?? false)) return;
    FocusScope.of(context).unfocus();
    widget.onPaymentConfirmed();
  }

  String? _validatePhone(String? value) {
    final digits = (value ?? '').replaceAll(RegExp(r'\D'), '');
    if (digits.isEmpty) {
      return context.localized(
        ar: '╪ث╪»╪«┘ ╪▒┘é┘à ╪د┘┘ç╪د╪ز┘ ╪د┘┘à╪▒╪ز╪ذ╪╖ ╪ذ╪«╪»┘à╪ر ╪د┘╪»┘╪╣',
        en: 'Enter the phone number linked to the payment service',
      );
    }
    if (digits.length < 9 || digits.length > 10) {
      return context.localized(
        ar: '╪ث╪»╪«┘ ╪▒┘é┘à ┘ç╪د╪ز┘ ╪╡╪ص┘è╪ص┘ï╪د',
        en: 'Enter a valid phone number',
      );
    }
    return null;
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<TicketCartProvider>();
    final topPad = MediaQuery.paddingOf(context).top;
    final bottomPad = MediaQuery.paddingOf(context).bottom;
    final invoiceItems = cart.cart.entries
        .where((entry) => entry.value > 0)
        .map(
          (entry) => (
            type: TicketData.byId(entry.key)!,
            quantity: entry.value,
          ),
        )
        .toList();

    return Form(
      key: _formKey,
      child: Column(
        children: [
          Container(
            color: Colors.white,
            padding: EdgeInsets.fromLTRB(20, topPad + 8, 20, 8),
            child: SizedBox(
              height: 56,
              child: CenteredPageHeader(
                title: context.localized(
                    ar: '╪ح╪ز┘à╪د┘à ╪د┘╪»┘╪╣', en: 'Complete Payment'),
                leading: IconButton(
                  onPressed: widget.onBack,
                  icon: const Icon(Icons.arrow_forward_ios_rounded),
                ),
              ),
            ),
          ),
          Expanded(
            child: SingleChildScrollView(
              child: Padding(
                padding: EdgeInsets.fromLTRB(24, 10, 24, bottomPad + 110),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Container(
                      key: const ValueKey('payment-total-card'),
                      padding: const EdgeInsets.symmetric(vertical: 28),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(28),
                        border: Border.all(color: Colors.grey.shade200),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.06),
                            blurRadius: 16,
                            offset: const Offset(0, 8),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          Container(
                            width: 54,
                            height: 54,
                            decoration: BoxDecoration(
                              color: const Color(0xFFE8F5E9),
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(
                              Icons.receipt_long_rounded,
                              color: _green,
                              size: 30,
                            ),
                          ),
                          const SizedBox(height: 14),
                          Text(
                            context.localized(
                              ar: '╪د┘┘à╪ذ┘╪║ ╪د┘╪ح╪ش┘à╪د┘┘è',
                              en: 'Total amount',
                            ),
                            style: GoogleFonts.cairo(
                              color: Colors.grey.shade600,
                              fontSize: 14,
                            ),
                          ),
                          Text(
                            '${cart.totalPrice.toStringAsFixed(0)} ${context.localized(ar: '╪».┘', en: 'LYD')}',
                            style: GoogleFonts.cairo(
                              color: _green,
                              fontSize: 38,
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                          Container(
                            margin: const EdgeInsets.only(top: 8),
                            padding: const EdgeInsets.symmetric(
                              horizontal: 18,
                              vertical: 7,
                            ),
                            decoration: BoxDecoration(
                              color: const Color(0xFFF2F7F2),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Text(
                              context.localized(
                                ar: '╪╣╪»╪» ╪د┘╪ز╪░╪د┘â╪▒: ${cart.totalVisitors}',
                                en: 'Tickets: ${cart.totalVisitors}',
                              ),
                              style: GoogleFonts.cairo(
                                color: AppColors.textPrimary,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 28),
                    _PaymentSectionTitle(
                      title:
                          context.localized(ar: '┘à┘╪«╪╡ ╪د┘┘╪د╪ز┘ê╪▒╪ر', en: 'Invoice'),
                    ),
                    const SizedBox(height: 10),
                    Container(
                      key: const ValueKey('payment-invoice'),
                      padding: const EdgeInsets.all(18),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.grey.shade200),
                      ),
                      child: Column(
                        children: [
                          ...invoiceItems.map(
                            (item) => Padding(
                              padding: const EdgeInsets.only(bottom: 10),
                              child: _InvoiceRow(
                                label:
                                    '${_localizedTicketTitle(context, item.type)} ├ù ${item.quantity}',
                                value:
                                    '${item.type.price * item.quantity} ${context.localized(ar: '╪».┘', en: 'LYD')}',
                              ),
                            ),
                          ),
                          const Divider(height: 20),
                          _InvoiceRow(
                            label:
                                context.localized(ar: '╪د┘╪ح╪ش┘à╪د┘┘è', en: 'Total'),
                            value:
                                '${cart.totalPrice.toStringAsFixed(0)} ${context.localized(ar: '╪».┘', en: 'LYD')}',
                            emphasized: true,
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 26),
                    _PaymentSectionTitle(
                      title: context.localized(
                          ar: '╪╖╪▒┘è┘é╪ر ╪د┘╪»┘╪╣', en: 'Payment Method'),
                    ),
                    const SizedBox(height: 10),
                    _PaymentMethodCard(
                      id: 'edfa3ly',
                      title: context.localized(ar: '╪د╪»┘╪╣ ┘┘è', en: 'Edfa3ly'),
                      subtitle: context.localized(
                        ar: '┘à╪ص┘╪╕╪ر ╪ح┘┘â╪ز╪▒┘ê┘┘è╪ر ╪ت┘à┘╪ر',
                        en: 'Secure digital wallet',
                      ),
                      icon: Icons.account_balance_wallet_rounded,
                      selected: _paymentMethod == 'edfa3ly',
                      onTap: () => setState(() => _paymentMethod = 'edfa3ly'),
                    ),
                    const SizedBox(height: 10),
                    _PaymentMethodCard(
                      id: 'sadad',
                      title: context.localized(ar: '╪│╪»╪د╪»', en: 'Sadad'),
                      subtitle: context.localized(
                        ar: '╪»┘╪╣ ╪ح┘┘â╪ز╪▒┘ê┘┘è ╪╣╪ذ╪▒ ╪د┘┘ç╪د╪ز┘',
                        en: 'Mobile electronic payment',
                      ),
                      icon: Icons.phone_android_rounded,
                      selected: _paymentMethod == 'sadad',
                      onTap: () => setState(() => _paymentMethod = 'sadad'),
                    ),
                    const SizedBox(height: 26),
                    _PaymentSectionTitle(
                      title: context.localized(
                          ar: '╪▒┘é┘à ╪د┘┘ç╪د╪ز┘', en: 'Phone Number'),
                    ),
                    const SizedBox(height: 10),
                    TextFormField(
                      key: const ValueKey('payment-phone-field'),
                      controller: _phoneController,
                      keyboardType: TextInputType.phone,
                      textDirection: TextDirection.ltr,
                      inputFormatters: [
                        FilteringTextInputFormatter.digitsOnly,
                        LengthLimitingTextInputFormatter(10),
                      ],
                      decoration: InputDecoration(
                        hintText: '09X XXX XXXX',
                        prefixText: '+218  ',
                        prefixStyle: GoogleFonts.cairo(
                          color: Colors.black87,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      validator: _validatePhone,
                    ),
                    const SizedBox(height: 10),
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Icon(
                          Icons.info_outline_rounded,
                          size: 17,
                          color: Colors.grey,
                        ),
                        const SizedBox(width: 7),
                        Expanded(
                          child: Text(
                            context.localized(
                              ar: '╪│┘è┘╪│╪ز╪«╪»┘à ╪د┘╪▒┘é┘à ╪د┘┘à╪▒╪ز╪ذ╪╖ ╪ذ╪«╪»┘à╪ر ╪د┘╪»┘╪╣ ┘╪ح╪▒╪│╪د┘ ╪▒┘à╪▓ OTP ┘┘è ╪«╪╖┘ê╪ر ╪د┘╪ز╪ص┘é┘é ┘╪د╪ص┘é┘ï╪د.',
                              en: 'The number linked to the payment service will be used to send an OTP in a later verification step.',
                            ),
                            style: GoogleFonts.cairo(
                              fontSize: 11,
                              height: 1.5,
                              color: Colors.grey.shade600,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
          Container(
            padding: EdgeInsets.fromLTRB(24, 14, 24, bottomPad + 14),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.06),
                  blurRadius: 16,
                  offset: const Offset(0, -4),
                ),
              ],
            ),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                key: const ValueKey('confirm-payment-button'),
                onPressed: _confirmPayment,
                style: ElevatedButton.styleFrom(
                  backgroundColor: _green,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 17),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(30),
                  ),
                ),
                child: Text(
                  context.localized(
                    ar: '╪ز╪ث┘â┘è╪» ╪د┘╪»┘╪╣',
                    en: 'Confirm Payment',
                  ),
                  style: GoogleFonts.cairo(
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _PaymentSectionTitle extends StatelessWidget {
  final String title;

  const _PaymentSectionTitle({required this.title});

  @override
  Widget build(BuildContext context) {
    return Text(
      title,
      style: GoogleFonts.cairo(
        fontSize: 17,
        fontWeight: FontWeight.w800,
        color: const Color(0xFF1A1A1A),
      ),
    );
  }
}

class _PaymentMethodCard extends StatelessWidget {
  final String id;
  final String title;
  final String subtitle;
  final IconData icon;
  final bool selected;
  final VoidCallback onTap;

  const _PaymentMethodCard({
    required this.id,
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.selected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        key: ValueKey('payment-method-$id'),
        onTap: onTap,
        borderRadius: BorderRadius.circular(18),
        child: Ink(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(
              color: selected ? const Color(0xFF2E7D32) : Colors.grey.shade200,
              width: selected ? 2 : 1,
            ),
          ),
          child: Row(
            children: [
              Icon(
                selected
                    ? Icons.check_circle_rounded
                    : Icons.radio_button_unchecked_rounded,
                color:
                    selected ? const Color(0xFF2E7D32) : Colors.grey.shade400,
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.cairo(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    Text(
                      subtitle,
                      style: GoogleFonts.cairo(
                        fontSize: 11,
                        color: Colors.grey.shade500,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: const Color(0xFFF2F7F2),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: const Color(0xFF2E7D32)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _InvoiceRow extends StatelessWidget {
  final String label;
  final String value;
  final bool emphasized;

  const _InvoiceRow({
    required this.label,
    required this.value,
    this.emphasized = false,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Expanded(
          child: Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: emphasized ? FontWeight.w800 : FontWeight.w600,
            ),
          ),
        ),
        const SizedBox(width: 12),
        Text(
          value,
          style: GoogleFonts.cairo(
            fontSize: 13,
            fontWeight: FontWeight.w800,
            color: emphasized ? const Color(0xFF2E7D32) : Colors.black87,
          ),
        ),
      ],
    );
  }
}

// ظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـ
// STEP 3 ظ¤ Ticket View
// ظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـظـ
class _TicketView extends StatefulWidget {
  final VoidCallback onBack;
  const _TicketView({required this.onBack});

  @override
  State<_TicketView> createState() => _TicketViewState();
}

class _TicketViewState extends State<_TicketView> {
  bool _saving = false;

  Future<void> _saveTickets(List<PurchasedTicket> tickets) async {
    if (_saving || tickets.isEmpty) return;
    setState(() => _saving = true);

    try {
      final savedCount = await TicketImageService.saveTickets(
        tickets,
        languageCode: Localizations.localeOf(context).languageCode,
      );
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            savedCount == tickets.length
                ? '╪ز┘à ╪ص┘╪╕ $savedCount ╪ز╪░╪د┘â╪▒ ┘â╪╡┘ê╪▒ ┘┘è ╪د┘┘à╪╣╪▒╪╢'
                : '╪ز┘à ╪ص┘╪╕ $savedCount ┘à┘ ╪ث╪╡┘ ${tickets.length} ╪ز╪░╪د┘â╪▒',
            style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
          ),
          backgroundColor: const Color(0xFF2E7D32),
        ),
      );
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            '╪ز╪╣╪░╪▒ ╪ص┘╪╕ ╪╡┘ê╪▒ ╪د┘╪ز╪░╪د┘â╪▒. ╪ز╪ث┘â╪» ┘à┘ ╪د┘╪│┘à╪د╪ص ╪ذ╪د┘┘ê╪╡┘ê┘ ╪ح┘┘ë ╪د┘╪╡┘ê╪▒.',
            style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
          ),
          backgroundColor: Colors.red.shade700,
        ),
      );
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<TicketCartProvider>();
    final tickets = cart.lastPurchaseTickets.isNotEmpty
        ? cart.lastPurchaseTickets
        : cart.purchasedTickets;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    if (tickets.isEmpty) return const SizedBox.shrink();

    return CustomScrollView(
      slivers: [
        WhitePinnedSliverHeader(
          toolbarHeight: 68,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: CenteredPageHeader(
              title: context.localized(ar: '╪د┘╪ز╪░╪د┘â╪▒', en: 'Tickets'),
              leading: GestureDetector(
                onTap: widget.onBack,
                child: Container(
                  width: 38,
                  height: 38,
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.7),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.arrow_back_ios_new_rounded,
                      size: 16, color: Colors.black87),
                ),
              ),
              trailing: FilledButton.icon(
                onPressed: _saving ? null : () => _saveTickets(tickets),
                style: FilledButton.styleFrom(
                  backgroundColor: const Color(0xFFE8F5E9),
                  foregroundColor: const Color(0xFF2E7D32),
                  disabledBackgroundColor: Colors.grey.shade100,
                  padding:
                      const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(18),
                  ),
                  elevation: 0,
                ),
                icon: _saving
                    ? const SizedBox(
                        width: 17,
                        height: 17,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Color(0xFF2E7D32),
                        ),
                      )
                    : const Icon(Icons.download_rounded, size: 20),
                label: Text(
                  _saving
                      ? context.localized(
                          ar: '╪ش╪د╪▒┘è ╪د┘╪ص┘╪╕...',
                          en: 'Saving...',
                        )
                      : context.localized(
                          ar: '╪ز╪ص┘à┘è┘ ╪د┘╪ز╪░╪د┘â╪▒',
                          en: 'Download tickets',
                        ),
                  style: GoogleFonts.cairo(
                    fontSize: 13,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ),
          ),
        ),
        SliverToBoxAdapter(
          child: Padding(
            padding: EdgeInsets.fromLTRB(24, 24, 24, bottomPad + 120),
            child: Column(
              children: [
                const Icon(Icons.check_circle_rounded,
                    color: Color(0xFF2E7D32), size: 64),
                const SizedBox(height: 12),
                Text('╪ز┘à ╪د┘╪ص╪ش╪▓ ╪ذ┘╪ش╪د╪ص!',
                    style: GoogleFonts.cairo(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF1A1A1A))),
                const SizedBox(height: 6),
                Text(
                  '╪ز┘à ╪ح╪╡╪»╪د╪▒ ${tickets.length} ${tickets.length == 1 ? "╪ز╪░┘â╪▒╪ر" : "╪ز╪░╪د┘â╪▒"} ┘à┘┘╪╡┘╪ر',
                  style: GoogleFonts.cairo(
                    fontSize: 14,
                    color: Colors.grey.shade500,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 28),
                ...tickets.asMap().entries.map(
                      (entry) => Padding(
                        padding: const EdgeInsets.only(bottom: 18),
                        child: _PurchasedTicketCard(
                          ticket: entry.value,
                          number: entry.key + 1,
                          total: tickets.length,
                        ),
                      ),
                    ),
                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () => context.go('/home'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF2E7D32),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 18),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(50)),
                      elevation: 0,
                    ),
                    child: Text('╪د┘╪╣┘ê╪»╪ر ┘┘╪▒╪خ┘è╪│┘è╪ر',
                        style: GoogleFonts.cairo(
                            fontSize: 16, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}

class _PurchasedTicketCard extends StatelessWidget {
  final PurchasedTicket ticket;
  final int number;
  final int total;

  const _PurchasedTicketCard({
    required this.ticket,
    required this.number,
    required this.total,
  });

  @override
  Widget build(BuildContext context) {
    final languageCode = Localizations.localeOf(context).languageCode;

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.06),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        children: [
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(20),
            decoration: const BoxDecoration(
              color: Color(0xFF2E7D32),
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(24),
                topRight: Radius.circular(24),
              ),
            ),
            child: Column(
              children: [
                Text(
                  'Tripoli Zoo ┬╖ $number/$total',
                  style: GoogleFonts.cairo(
                    color: Colors.white70,
                    fontSize: 12,
                    letterSpacing: 1,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  context.localized(ar: '╪ز╪░┘â╪▒╪ر ╪»╪«┘ê┘', en: 'Entry Ticket'),
                  style: GoogleFonts.cairo(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              children: [
                QrImageView(
                  data: ticket.qrData,
                  version: QrVersions.auto,
                  size: 180,
                  eyeStyle: const QrEyeStyle(
                    eyeShape: QrEyeShape.square,
                    color: Color(0xFF2E7D32),
                  ),
                  dataModuleStyle: const QrDataModuleStyle(
                    dataModuleShape: QrDataModuleShape.square,
                    color: Color(0xFF2E7D32),
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  '╪▒┘é┘à ╪د┘╪ز╪░┘â╪▒╪ر: ${ticket.id}',
                  textDirection: TextDirection.ltr,
                  style: GoogleFonts.cairo(
                    color: Colors.grey,
                    fontSize: 12,
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                _DetailRow(
                  context.localized(ar: '╪د┘┘╪خ╪ر', en: 'Category'),
                  ticket.localizedCategoryLabel(languageCode),
                ),
                const SizedBox(height: 10),
                _DetailRow(
                  context.localized(ar: '╪د┘╪ز╪د╪▒┘è╪«', en: 'Date'),
                  formatArabicDate(ticket.visitDate),
                ),
                const SizedBox(height: 10),
                _DetailRow(
                  context.localized(ar: '╪د┘┘ê┘é╪ز', en: 'Time'),
                  AppConstants.workingHours,
                ),
                const SizedBox(height: 10),
                _DetailRow(
                  context.localized(ar: '╪د┘╪│╪╣╪▒', en: 'Price'),
                  '${ticket.price} ${context.localized(ar: '╪».┘', en: 'LYD')}',
                  isTotal: true,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _DetailRow extends StatelessWidget {
  final String label;
  final String value;
  final bool isTotal;
  const _DetailRow(this.label, this.value, {this.isTotal = false});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label,
            style:
                GoogleFonts.cairo(color: Colors.grey.shade600, fontSize: 14)),
        Text(value,
            style: GoogleFonts.cairo(
              color: isTotal ? const Color(0xFF2E7D32) : Colors.black87,
              fontSize: 14,
              fontWeight: FontWeight.bold,
            )),
      ],
    );
  }
}
