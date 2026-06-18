import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/services/ticket_image_service.dart';
import 'package:tripolizoo/shared/utils/date_formatters.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';
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
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final cart = context.read<TicketCartProvider>();
      await cart.loadTypes();
      await cart.loadPurchasedTickets();
      if (!mounted) return;
      if (cart.purchasedTickets.isNotEmpty) {
        setState(() => _step = 2);
      }
    });
  }

  void _goToPayment() => setState(() => _step = 1);

  void _onPaymentSuccess() => setState(() => _step = 2);

  void _goBack() {
    if (_step == 2 || _step == 1) {
      setState(() => _step = 0);
    } else {
      context.go('/home');
    }
  }

  @override
  Widget build(BuildContext context) {
    final hasAccount = context.watch<AuthProvider>().hasAccount;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Scaffold(
        backgroundColor: Colors.white,
        body: hasAccount
            ? AnimatedSwitcher(
                duration: const Duration(milliseconds: 300),
                child: switch (_step) {
                  0 => _SelectionView(
                      onContinue: _goToPayment,
                      onBack: _goBack,
                    ),
                  1 => _PaymentView(
                      onBack: _goBack,
                      onSuccess: _onPaymentSuccess,
                    ),
                  _ => _TicketView(onBack: _goBack),
                },
              )
            : _TicketsAccountRequiredView(onBack: _goBack),
      ),
    );
  }
}

class _TicketsAccountRequiredView extends StatelessWidget {
  final VoidCallback onBack;

  const _TicketsAccountRequiredView({required this.onBack});

  void _openAuth(BuildContext context, String route) {
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
              title: context.localized(ar: 'التذاكر', en: 'Tickets'),
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
                      ar: 'تسجيل الدخول مطلوب',
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
                      ar: 'شراء التذاكر الإلكترونية وحفظها في حسابك متاحان للمستخدمين الذين يمتلكون حسابًا فقط.',
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
                        ar: 'تسجيل الدخول',
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
                        ar: 'إنشاء حساب جديد',
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

// ══════════════════════════════
// STEP 1 — Selection View
// ══════════════════════════════
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
    final tickets = _tab == 0 ? cart.localTypes : cart.foreignTypes;

    if (cart.isLoadingTypes) {
      return const Column(
        children: [
          Expanded(child: Center(child: CircularProgressIndicator())),
        ],
      );
    }

    if (tickets.isEmpty) {
      return Column(
        children: [
          Expanded(
            child: Center(
              child: Text(
                context.localized(
                  ar: 'لا توجد تذاكر متاحة حالياً',
                  en: 'No tickets are available right now',
                ),
                style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
              ),
            ),
          ),
        ],
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // ── Header ──
        Container(
          color: _bg,
          padding: EdgeInsets.fromLTRB(20, topPad + 8, 20, 8),
          child: SizedBox(
            height: 56,
            child: CenteredPageHeader(
              title: context.localized(ar: 'التذاكر', en: 'Tickets'),
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

        // ── Body ──
        Expanded(
          child: ListView(
            padding: EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 100),
            children: [
              const _VisitTicketInfoCard(),
              const SizedBox(height: 18),

              // ── Tabs ──
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
                            context.localized(ar: 'المواطنون', en: 'Citizens'),
                        active: _tab == 0,
                        onTap: () => setState(() => _tab = 0),
                      ),
                    ),
                    Expanded(
                      child: _TabBtn(
                        label:
                            context.localized(ar: 'الأجانب', en: 'Foreigners'),
                        active: _tab == 1,
                        isRight: true,
                        onTap: () => setState(() => _tab = 1),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // ── Ticket rows ──
              ...tickets.map((t) => _TicketRow(type: t)),
              const SizedBox(height: 16),
            ],
          ),
        ),

        // ── Bottom Bar ──
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
                      context.localized(ar: 'الإجمالي', en: 'TOTAL'),
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
                          '${cart.totalPrice.toStringAsFixed(0)} ${context.localized(ar: 'د.ل', en: 'LYD')}',
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
                                  ? '(تذكرة واحدة)'
                                  : '(${cart.totalVisitors} تذاكر)',
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
                          context.localized(ar: 'التالي', en: 'Next'),
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

// ── Tab Button ──
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

// ── Ticket Row ──
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
                  '${_localizedTicketSubtitle(context, type)} · ${type.price} ${context.localized(ar: 'د.ل', en: 'LYD')}',
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

// ── Counter Button ──
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
                        ar: 'مفتوح اليوم · ${AppConstants.workingHours}',
                        en: 'Open today · ${AppConstants.workingHours}',
                      ),
                      style: GoogleFonts.cairo(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    Text(
                      context.localized(
                        ar: 'آخر دخول قبل موعد الإغلاق بنصف ساعة',
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
              ar: 'الدخول مجاني للأطفال دون الثالثة ولذوي الاحتياجات الخاصة.',
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
  final VoidCallback onSuccess;

  const _PaymentView({
    required this.onBack,
    required this.onSuccess,
  });

  @override
  State<_PaymentView> createState() => _PaymentViewState();
}

class _PaymentViewState extends State<_PaymentView> {
  final _formKey = GlobalKey<FormState>();
  final _phoneController = TextEditingController();
  String _paymentMethod = 'cash';

  static const _green = Color(0xFF2E7D32);

  @override
  void dispose() {
    _phoneController.dispose();
    super.dispose();
  }

  Future<void> _confirmPayment() async {
    if (_paymentMethod == 'electronic' &&
        !(_formKey.currentState?.validate() ?? false)) {
      return;
    }

    FocusScope.of(context).unfocus();
    final cart = context.read<TicketCartProvider>();
    final messenger = ScaffoldMessenger.of(context);

    try {
      if (_paymentMethod == 'cash') {
        final tickets = await cart.purchaseCash();
        if (!mounted) return;
        if (tickets.isNotEmpty) widget.onSuccess();
        return;
      }

      final session = await cart.verifyElectronicPayment(
        mobile: _phoneController.text,
      );
      if (!mounted) return;

      final otp = await _promptForOtp();
      if (!mounted || otp == null || otp.isEmpty) return;

      final tickets = await cart.confirmElectronicPayment(
        processId: session.processId,
        otp: otp,
      );
      if (!mounted) return;
      if (tickets.isNotEmpty) widget.onSuccess();
    } catch (error) {
      if (!mounted) return;
      messenger.showSnackBar(
        SnackBar(content: Text(error.toString())),
      );
    }
  }

  Future<String?> _promptForOtp() async {
    final controller = TextEditingController();
    return showDialog<String>(
      context: context,
      barrierDismissible: false,
      builder: (dialogContext) {
        return AlertDialog(
          title: Text(
            context.localized(
              ar: 'رمز التحقق',
              en: 'Verification Code',
            ),
            style: GoogleFonts.cairo(fontWeight: FontWeight.w800),
          ),
          content: TextField(
            controller: controller,
            keyboardType: TextInputType.number,
            maxLength: 4,
            textAlign: TextAlign.center,
            decoration: InputDecoration(
              hintText: context.localized(ar: 'أدخل OTP', en: 'Enter OTP'),
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(dialogContext).pop(),
              child: Text(context.localized(ar: 'إلغاء', en: 'Cancel')),
            ),
            TextButton(
              onPressed: () => Navigator.of(dialogContext).pop(controller.text),
              child: Text(context.localized(ar: 'تأكيد', en: 'Confirm')),
            ),
          ],
        );
      },
    );
  }

  String? _validatePhone(String? value) {
    final digits = (value ?? '').replaceAll(RegExp(r'\D'), '');
    if (digits.isEmpty) {
      return context.localized(
        ar: 'أدخل رقم الهاتف المرتبط بخدمة الدفع',
        en: 'Enter the phone number linked to the payment service',
      );
    }
    if (digits.length < 9 || digits.length > 10) {
      return context.localized(
        ar: 'أدخل رقم هاتف صحيحًا',
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
            type: cart.typeById(entry.key)!,
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
                    ar: 'إتمام الدفع', en: 'Complete Payment'),
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
                              ar: 'المبلغ الإجمالي',
                              en: 'Total amount',
                            ),
                            style: GoogleFonts.cairo(
                              color: Colors.grey.shade600,
                              fontSize: 14,
                            ),
                          ),
                          Text(
                            '${cart.totalPrice.toStringAsFixed(0)} ${context.localized(ar: 'د.ل', en: 'LYD')}',
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
                                ar: 'عدد التذاكر: ${cart.totalVisitors}',
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
                          context.localized(ar: 'ملخص الفاتورة', en: 'Invoice'),
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
                                    '${_localizedTicketTitle(context, item.type)} × ${item.quantity}',
                                value:
                                    '${item.type.price * item.quantity} ${context.localized(ar: 'د.ل', en: 'LYD')}',
                              ),
                            ),
                          ),
                          const Divider(height: 20),
                          _InvoiceRow(
                            label:
                                context.localized(ar: 'الإجمالي', en: 'Total'),
                            value:
                                '${cart.totalPrice.toStringAsFixed(0)} ${context.localized(ar: 'د.ل', en: 'LYD')}',
                            emphasized: true,
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 26),
                    _PaymentSectionTitle(
                      title: context.localized(
                          ar: 'طريقة الدفع', en: 'Payment Method'),
                    ),
                    const SizedBox(height: 10),
                    _PaymentMethodCard(
                      id: 'cash',
                      title: context.localized(ar: 'نقدي', en: 'Cash'),
                      subtitle: context.localized(
                        ar: 'الدفع عند نقطة البيع أو الكشك',
                        en: 'Pay at the ticket counter',
                      ),
                      icon: Icons.payments_rounded,
                      selected: _paymentMethod == 'cash',
                      onTap: () => setState(() => _paymentMethod = 'cash'),
                    ),
                    const SizedBox(height: 10),
                    _PaymentMethodCard(
                      id: 'electronic',
                      title: context.localized(ar: 'إلكتروني', en: 'Electronic'),
                      subtitle: context.localized(
                        ar: 'الدفع عبر بوابة Plutu',
                        en: 'Pay through Plutu gateway',
                      ),
                      icon: Icons.account_balance_wallet_rounded,
                      selected: _paymentMethod == 'electronic',
                      onTap: () =>
                          setState(() => _paymentMethod = 'electronic'),
                    ),
                    if (_paymentMethod == 'electronic') ...[
                      const SizedBox(height: 26),
                      _PaymentSectionTitle(
                        title: context.localized(
                            ar: 'رقم الهاتف', en: 'Phone Number'),
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
                                ar: 'سيُرسل رمز OTP إلى هاتفك عبر Plutu لتأكيد الدفع الإلكتروني.',
                                en: 'An OTP will be sent to your phone via Plutu to confirm electronic payment.',
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
                    ar: 'تأكيد الدفع',
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

// ══════════════════════════════
// STEP 3 — Ticket View
// ══════════════════════════════
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
                ? 'تم حفظ $savedCount تذاكر كصور في المعرض'
                : 'تم حفظ $savedCount من أصل ${tickets.length} تذاكر',
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
            'تعذر حفظ صور التذاكر. تأكد من السماح بالوصول إلى الصور.',
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
              title: context.localized(ar: 'التذاكر', en: 'Tickets'),
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
                          ar: 'جاري الحفظ...',
                          en: 'Saving...',
                        )
                      : context.localized(
                          ar: 'تحميل التذاكر',
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
                Text('تم الحجز بنجاح!',
                    style: GoogleFonts.cairo(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF1A1A1A))),
                const SizedBox(height: 6),
                Text(
                  'تم إصدار ${tickets.length} ${tickets.length == 1 ? "تذكرة" : "تذاكر"} منفصلة',
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
                    child: Text('العودة للرئيسية',
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
                  'Tripoli Zoo · $number/$total',
                  style: GoogleFonts.cairo(
                    color: Colors.white70,
                    fontSize: 12,
                    letterSpacing: 1,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  context.localized(ar: 'تذكرة دخول', en: 'Entry Ticket'),
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
                  'رقم التذكرة: ${ticket.id}',
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
                  context.localized(ar: 'الفئة', en: 'Category'),
                  ticket.localizedCategoryLabel(languageCode),
                ),
                const SizedBox(height: 10),
                _DetailRow(
                  context.localized(ar: 'التاريخ', en: 'Date'),
                  formatArabicDate(ticket.visitDate),
                ),
                const SizedBox(height: 10),
                _DetailRow(
                  context.localized(ar: 'الوقت', en: 'Time'),
                  AppConstants.workingHours,
                ),
                const SizedBox(height: 10),
                _DetailRow(
                  context.localized(ar: 'السعر', en: 'Price'),
                  '${ticket.price} ${context.localized(ar: 'د.ل', en: 'LYD')}',
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
