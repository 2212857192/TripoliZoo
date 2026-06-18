import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/data/ticket_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';
import 'package:tripolizoo/shared/constants/ticket_data.dart';

class _FakeTicketRepository implements TicketRepository {
  @override
  Future<List<TicketType>> fetchTypes() async => TicketData.all;

  @override
  Future<List<PurchasedTicket>> fetchMine() async => const [];

  @override
  Future<List<PurchasedTicket>> purchaseCash({
    required Map<String, int> cart,
  }) async {
    final purchasedAt = DateTime(2026, 6, 18);
    final created = <PurchasedTicket>[];
    var sequence = 1;

    for (final entry in cart.entries) {
      final type = TicketData.byId(entry.key);
      if (type == null) continue;

      for (var index = 0; index < entry.value; index++) {
        created.add(
          PurchasedTicket(
            id: 'TK-00000$sequence',
            qrData: 'QR-$sequence',
            visitDate: purchasedAt,
            typeId: type.id,
            typeTitle: type.title,
            price: type.price,
            purchasedAt: purchasedAt,
          ),
        );
        sequence++;
      }
    }

    return created;
  }

  @override
  Future<ElectronicPaymentSession> verifyElectronic({
    required Map<String, int> cart,
    required String mobile,
  }) async {
    return const ElectronicPaymentSession(
      processId: 'mock',
      amount: 10,
      invoiceNo: 'TZ-MOCK',
    );
  }

  @override
  Future<List<PurchasedTicket>> confirmElectronic({
    required String processId,
    required String otp,
  }) async {
    return purchaseCash(cart: const {'adult_ly': 1});
  }
}

void main() {
  test('purchaseCash creates one independent ticket for every visitor', () async {
    final cart = TicketCartProvider(repository: _FakeTicketRepository());
    await cart.loadTypes();

    cart
      ..increment('child_ly')
      ..increment('child_ly')
      ..increment('student')
      ..increment('adult_intl');

    final tickets = await cart.purchaseCash();

    expect(tickets, hasLength(4));
    expect(tickets.map((ticket) => ticket.id).toSet(), hasLength(4));
    expect(cart.purchasedTickets, hasLength(4));
    expect(cart.totalVisitors, 0);
    expect(cart.totalPrice, 0);
  });
}
