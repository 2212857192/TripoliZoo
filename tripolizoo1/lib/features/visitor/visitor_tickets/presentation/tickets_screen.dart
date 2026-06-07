import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
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

  void _goToConfirm() {
    setState(() => _step = 1);
  }

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
      value: SystemUiOverlayStyle.light,
      child: Scaffold(
        backgroundColor: const Color(0xFFF4F7F5), // Very light pleasant background
        appBar: AppBar(
          backgroundColor: const Color(0xFF0F3D24), // Rich dark green to avoid dullness
          elevation: 0,
          leading: IconButton(
            icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 20),
            onPressed: _goBack,
          ),
          title: Text(
            _step == 0 ? 'حجز التذاكر' : 'التذكرة الإلكترونية',
            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18),
          ),
          centerTitle: true,
        ),
        body: AnimatedSwitcher(
          duration: const Duration(milliseconds: 300),
          child: _step == 0 ? _SelectionView(onPurchase: _goToConfirm) : const _TicketView(),
        ),
      ),
    );
  }
}

// ══════════════════════════════
// STEP 1 — Clean Colored Selection
// ══════════════════════════════
class _SelectionView extends StatelessWidget {
  final VoidCallback onPurchase;
  const _SelectionView({required this.onPurchase});

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<TicketCartProvider>();

    return Column(
      children: [
        Expanded(
          child: ListView(
            padding: const EdgeInsets.all(20),
            children: [
              // Date Card
              _CompactDateCard(cart: cart),
              const SizedBox(height: 24),

              const Text(
                'تذاكر المواطنين',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF0F3D24)),
              ),
              const SizedBox(height: 12),
              ...TicketData.local.map((t) => _CleanTicketCard(type: t)),
              const SizedBox(height: 24),

              const Text(
                'تذاكر الأجانب',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF0F3D24)),
              ),
              const SizedBox(height: 12),
              ...TicketData.foreign.map((t) => _CleanTicketCard(type: t)),
              const SizedBox(height: 40),
            ],
          ),
        ),

        // Bottom Action Bar
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.05),
                blurRadius: 10,
                offset: const Offset(0, -2),
              ),
            ],
          ),
          child: SafeArea(
            child: Row(
              children: [
                Expanded(
                  flex: 1,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Text('الإجمالي', style: TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.bold)),
                      Text(
                        '${cart.totalPrice.toInt()} د.ل',
                        style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Color(0xFF0F3D24)),
                      ),
                    ],
                  ),
                ),
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: cart.totalPrice > 0
                        ? () {
                            context.read<TicketCartProvider>().purchase();
                            onPurchase();
                          }
                        : null,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF1B8A2A),
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.grey.shade300,
                      disabledForegroundColor: Colors.grey.shade500,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      elevation: 0,
                    ),
                    child: const Text('تأكيد الحجز', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
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

class _CompactDateCard extends StatelessWidget {
  final TicketCartProvider cart;
  const _CompactDateCard({required this.cart});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () async {
            final picked = await showDatePicker(
              context: context,
              initialDate: cart.selectedDate,
              firstDate: DateTime.now(),
              lastDate: DateTime.now().add(const Duration(days: 30)),
              builder: (context, child) => Theme(
                data: Theme.of(context).copyWith(
                  colorScheme: const ColorScheme.light(primary: Color(0xFF1B8A2A)),
                ),
                child: child!,
              ),
            );
            if (picked != null && context.mounted) {
              context.read<TicketCartProvider>().setDate(picked);
            }
          },
          borderRadius: BorderRadius.circular(16),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Row(
              children: [
                const Icon(Icons.calendar_month_rounded, color: Color(0xFF1B8A2A), size: 24),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('تاريخ الزيارة', style: TextStyle(fontSize: 12, color: Colors.grey)),
                      const SizedBox(height: 2),
                      Text(
                        DateFormat('EEEE, d MMMM yyyy', 'ar').format(cart.selectedDate),
                        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.black87),
                      ),
                    ],
                  ),
                ),
                const Text('تغيير', style: TextStyle(color: Color(0xFF1B8A2A), fontSize: 13, fontWeight: FontWeight.bold)),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _CleanTicketCard extends StatelessWidget {
  final dynamic type;
  const _CleanTicketCard({required this.type});

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<TicketCartProvider>();
    final qty = cart.cart[type.id] ?? 0;
    final isSelected = qty > 0;

    return AnimatedContainer(
      duration: const Duration(milliseconds: 200),
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isSelected ? const Color(0xFFF0FDF4) : Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isSelected ? const Color(0xFF1B8A2A) : Colors.grey.shade200,
          width: isSelected ? 1.5 : 1,
        ),
      ),
      child: Row(
        children: [
          Icon(type.icon, color: isSelected ? const Color(0xFF1B8A2A) : Colors.grey.shade500, size: 28),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  type.title,
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: isSelected ? const Color(0xFF0F3D24) : Colors.black87),
                ),
                if (type.subtitle.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(type.subtitle, style: const TextStyle(fontSize: 12, color: Colors.grey)),
                ],
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                '${type.price} د.ل',
                style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF1B8A2A)),
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  _CleanCounterBtn(
                    icon: Icons.remove,
                    enabled: qty > 0,
                    onTap: () => context.read<TicketCartProvider>().decrement(type.id),
                  ),
                  SizedBox(
                    width: 32,
                    child: Text(
                      '$qty',
                      textAlign: TextAlign.center,
                      style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                  ),
                  _CleanCounterBtn(
                    icon: Icons.add,
                    enabled: true,
                    onTap: () => context.read<TicketCartProvider>().increment(type.id),
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _CleanCounterBtn extends StatelessWidget {
  final IconData icon;
  final bool enabled;
  final VoidCallback onTap;

  const _CleanCounterBtn({required this.icon, required this.enabled, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: enabled ? onTap : null,
      borderRadius: BorderRadius.circular(8),
      child: Container(
        width: 30,
        height: 30,
        decoration: BoxDecoration(
          color: enabled ? const Color(0xFF1B8A2A) : Colors.grey.shade100,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Icon(icon, size: 18, color: enabled ? Colors.white : Colors.grey.shade400),
      ),
    );
  }
}

// ══════════════════════════════
// STEP 2 — Clean Ticket Preview
// ══════════════════════════════
class _TicketView extends StatelessWidget {
  const _TicketView();

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<TicketCartProvider>();
    final ticket = cart.purchasedTickets.isNotEmpty ? cart.purchasedTickets.last : null;

    if (ticket == null) return const SizedBox.shrink();

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        children: [
          // Clean Success Message
          const Icon(Icons.check_circle_rounded, color: Color(0xFF1B8A2A), size: 64),
          const SizedBox(height: 16),
          const Text('تم الحجز بنجاح!', style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Color(0xFF0F3D24))),
          const SizedBox(height: 32),

          // Clean Ticket Card
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.grey.shade200),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.05),
                  blurRadius: 10,
                  offset: const Offset(0, 5),
                ),
              ],
            ),
            child: Column(
              children: [
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(20),
                  decoration: const BoxDecoration(
                    color: Color(0xFF0F3D24),
                    borderRadius: BorderRadius.only(topLeft: Radius.circular(16), topRight: Radius.circular(16)),
                  ),
                  child: const Column(
                    children: [
                      Text('Tripoli Zoo', style: TextStyle(color: Colors.white70, fontSize: 12, letterSpacing: 1)),
                      SizedBox(height: 4),
                      Text('تذكرة دخول', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
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
                        eyeStyle: const QrEyeStyle(eyeShape: QrEyeShape.square, color: Color(0xFF0F3D24)),
                        dataModuleStyle: const QrDataModuleStyle(dataModuleShape: QrDataModuleShape.square, color: Color(0xFF0F3D24)),
                      ),
                      const SizedBox(height: 12),
                      Text('رقم التذكرة: ${ticket.id}', style: const TextStyle(color: Colors.grey, fontSize: 12)),
                    ],
                  ),
                ),
                const Divider(height: 1, color: Color(0xFFEEEEEE)),
                Padding(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    children: [
                      _DetailRow('التاريخ', DateFormat('d MMMM yyyy', 'ar').format(ticket.visitDate)),
                      const SizedBox(height: 12),
                      const _DetailRow('الوقت', AppConstants.workingHours),
                      const SizedBox(height: 12),
                      _DetailRow('الزوار', '${cart.totalVisitors} أشخاص'),
                      const SizedBox(height: 12),
                      _DetailRow('المبلغ المدفوع', '${ticket.totalPrice.toInt()} د.ل', isTotal: true),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 32),

          SizedBox(
            width: double.infinity,
            child: OutlinedButton(
              onPressed: () => context.go('/home'),
              style: OutlinedButton.styleFrom(
                padding: const EdgeInsets.symmetric(vertical: 16),
                side: const BorderSide(color: Color(0xFF0F3D24)),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('العودة للرئيسية', style: TextStyle(color: Color(0xFF0F3D24), fontSize: 16, fontWeight: FontWeight.bold)),
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
        Text(label, style: TextStyle(color: Colors.grey.shade600, fontSize: 14)),
        Text(value, style: TextStyle(color: isTotal ? const Color(0xFF1B8A2A) : Colors.black87, fontSize: 14, fontWeight: FontWeight.bold)),
      ],
    );
  }
}
