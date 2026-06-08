import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/shared/constants/ticket_data.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';

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
        setState(() => _step = 1);
      }
    });
  }

  void _goToConfirm() => setState(() => _step = 1);
  void _goBack() {
    if (_step == 1) {
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
          child: _step == 0
              ? _SelectionView(onPurchase: _goToConfirm, onBack: _goBack)
              : _TicketView(onBack: _goBack),
        ),
      ),
    );
  }
}

// ══════════════════════════════
// STEP 1 — Selection View
// ══════════════════════════════
class _SelectionView extends StatefulWidget {
  final VoidCallback onPurchase;
  final VoidCallback onBack;
  const _SelectionView({required this.onPurchase, required this.onBack});

  @override
  State<_SelectionView> createState() => _SelectionViewState();
}

class _SelectionViewState extends State<_SelectionView> {
  int _tab = 0; // 0 = Citizens, 1 = Foreigners

  static const _green = Color(0xFF2E7D32);
  static const _lightGreen = Color(0xFFE8F5E9);
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
        // ── Header ──
        Container(
          color: _bg,
          padding: EdgeInsets.fromLTRB(20, topPad + 16, 20, 0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'زيارة',
                        style: GoogleFonts.cairo(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          color: Colors.grey.shade500,
                        ),
                      ),
                      Text(
                        'التذاكر',
                        style: GoogleFonts.cairo(
                          fontSize: 32,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF1A1A1A),
                          height: 1.1,
                        ),
                      ),
                    ],
                  ),
                  GestureDetector(
                    onTap: widget.onBack,
                    child: Container(
                      width: 38,
                      height: 38,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade100,
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.black87),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),

        // ── Body ──
        Expanded(
          child: ListView(
            padding: EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 100),
            children: [

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
                        label: 'المواطنون',
                        active: _tab == 0,
                        onTap: () => setState(() => _tab = 0),
                      ),
                    ),
                    Expanded(
                      child: _TabBtn(
                        label: 'الأجانب',
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

              const SizedBox(height: 24),

              // ── Free admission ──
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'دخول مجاني',
                    style: GoogleFonts.inter(
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                      color: Colors.grey.shade500,
                      letterSpacing: 1.5,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                    decoration: BoxDecoration(
                      color: const Color(0xFFF0F7E8),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Row(
                      children: [
                        Icon(Icons.favorite_outline_rounded, color: _green, size: 16),
                        const SizedBox(width: 8),
                        Text(
                          'مجاني دائماً',
                          style: GoogleFonts.inter(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: _green,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        child: _FreePill(icon: Icons.child_care_rounded, label: 'أطفال أقل من 3'),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: _FreePill(icon: Icons.accessible_rounded, label: 'ذوو الإعاقة'),
                      ),
                    ],
                  ),
                ],
              ),
            ],
          ),
        ),

        // ── Bottom Bar ──
        Container(
          padding: EdgeInsets.fromLTRB(24, 16, 24, bottomPad + 16),
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
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'الإجمالي',
                    style: GoogleFonts.cairo(
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                      color: Colors.grey.shade500,
                    ),
                  ),
                  Text(
                    '${cart.totalVisitors} تذكرة',
                    style: GoogleFonts.cairo(
                      fontSize: 14,
                      color: Colors.grey.shade500,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 4),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '${cart.totalPrice.toStringAsFixed(0)} د.ل',
                    style: GoogleFonts.cairo(
                      fontSize: 28,
                      fontWeight: FontWeight.bold,
                      color: const Color(0xFF1A1A1A),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 14),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: cart.totalPrice > 0
                      ? () {
                          context.read<TicketCartProvider>().purchase();
                          widget.onPurchase();
                        }
                      : null,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2E7D32),
                    foregroundColor: Colors.white,
                    disabledBackgroundColor: Colors.grey.shade200,
                    disabledForegroundColor: Colors.grey.shade400,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    elevation: 0,
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.confirmation_number_rounded, size: 20),
                      const SizedBox(width: 8),
                      Text(
                        'احجز تذكرتك الآن',
                        style: GoogleFonts.cairo(fontSize: 16, fontWeight: FontWeight.bold),
                      ),
                    ],
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

  const _TabBtn({required this.label, required this.active, this.isRight = false, required this.onTap});

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
            color: active ? Colors.white : const Color(0xFFC8A84B),
          ),
        ),
      ),
    );
  }
}

// ── Ticket Row ──
class _TicketRow extends StatelessWidget {
  final dynamic type;
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
                   type.title,
                   style: GoogleFonts.cairo(
                     fontSize: 15,
                     fontWeight: FontWeight.bold,
                     color: const Color(0xFF1A1A1A),
                   ),
                 ),
                 const SizedBox(height: 2),
                 Text(
                   '${type.subtitle} · ${type.price} د.ل',
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
                onTap: () => context.read<TicketCartProvider>().decrement(type.id),
              ),
              SizedBox(
                width: 36,
                child: Text(
                  '$qty',
                  textAlign: TextAlign.center,
                  style: GoogleFonts.cairo(fontSize: 16, fontWeight: FontWeight.bold),
                ),
              ),
              _CounterBtn(
                icon: Icons.add,
                enabled: true,
                filled: true,
                onTap: () => context.read<TicketCartProvider>().increment(type.id),
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

  const _CounterBtn({required this.icon, required this.enabled, required this.filled, required this.onTap});

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
              ? Border.all(color: enabled ? Colors.grey.shade400 : Colors.grey.shade200, width: 1.5)
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

// ── Free Pill ──
class _FreePill extends StatelessWidget {
  final IconData icon;
  final String label;
  const _FreePill({required this.icon, required this.label});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200, width: 1),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 15, color: Colors.grey.shade600),
          const SizedBox(width: 7),
          Expanded(
            child: Text(
              label,
              style: GoogleFonts.cairo(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: Colors.grey.shade700,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ══════════════════════════════
// STEP 2 — Ticket View
// ══════════════════════════════
class _TicketView extends StatelessWidget {
  final VoidCallback onBack;
  const _TicketView({required this.onBack});

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<TicketCartProvider>();
    final ticket = cart.purchasedTickets.isNotEmpty ? cart.purchasedTickets.last : null;
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    if (ticket == null) return const SizedBox.shrink();

    return SingleChildScrollView(
      padding: EdgeInsets.fromLTRB(24, topPad + 24, 24, bottomPad + 120),
      child: Column(
        children: [
          Row(
            children: [
              GestureDetector(
                onTap: onBack,
                child: Container(
                  width: 38,
                  height: 38,
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.7),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.arrow_back_ios_new_rounded, size: 16, color: Colors.black87),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          const Icon(Icons.check_circle_rounded, color: Color(0xFF2E7D32), size: 64),
          const SizedBox(height: 12),
          Text('تم الحجز بنجاح!', style: GoogleFonts.cairo(fontSize: 22, fontWeight: FontWeight.bold, color: const Color(0xFF1A1A1A))),
          const SizedBox(height: 32),
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              boxShadow: [
                BoxShadow(color: Colors.black.withValues(alpha: 0.06), blurRadius: 16, offset: const Offset(0, 8)),
              ],
            ),
            child: Column(
              children: [
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(20),
                  decoration: const BoxDecoration(
                    color: Color(0xFF2E7D32),
                    borderRadius: BorderRadius.only(topLeft: Radius.circular(24), topRight: Radius.circular(24)),
                  ),
                  child: Column(
                    children: [
                      Text('Tripoli Zoo', style: GoogleFonts.inter(color: Colors.white70, fontSize: 12, letterSpacing: 1)),
                      const SizedBox(height: 4),
                      Text('تذكرة دخول', style: GoogleFonts.cairo(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
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
                        eyeStyle: const QrEyeStyle(eyeShape: QrEyeShape.square, color: Color(0xFF2E7D32)),
                        dataModuleStyle: const QrDataModuleStyle(dataModuleShape: QrDataModuleShape.square, color: Color(0xFF2E7D32)),
                      ),
                      const SizedBox(height: 8),
                      Text('رقم التذكرة: ${ticket.id}', style: GoogleFonts.inter(color: Colors.grey, fontSize: 12)),
                    ],
                  ),
                ),
                const Divider(height: 1),
                Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    children: [
                      _DetailRow('التاريخ', DateFormat('d MMMM yyyy', 'ar').format(ticket.visitDate)),
                      const SizedBox(height: 10),
                      const _DetailRow('الوقت', AppConstants.workingHours),
                      const SizedBox(height: 10),
                      _DetailRow('الزوار', '${cart.totalVisitors} أشخاص'),
                      const SizedBox(height: 10),
                      _DetailRow('المبلغ المدفوع', '${ticket.totalPrice.toStringAsFixed(0)} د.ل', isTotal: true),
                    ],
                  ),
                ),
              ],
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
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(50)),
                elevation: 0,
              ),
              child: Text('العودة للرئيسية', style: GoogleFonts.cairo(fontSize: 16, fontWeight: FontWeight.bold)),
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
        Text(label, style: GoogleFonts.cairo(color: Colors.grey.shade600, fontSize: 14)),
        Text(value, style: GoogleFonts.cairo(
          color: isTotal ? const Color(0xFF2E7D32) : Colors.black87,
          fontSize: 14,
          fontWeight: FontWeight.bold,
        )),
      ],
    );
  }
}
