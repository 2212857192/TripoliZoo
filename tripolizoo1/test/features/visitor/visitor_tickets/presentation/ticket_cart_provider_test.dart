import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';

void main() {
  test('purchase creates one independent ticket for every visitor', () {
    final cart = TicketCartProvider()
      ..increment('child_ly')
      ..increment('child_ly')
      ..increment('student')
      ..increment('adult_intl');

    final tickets = cart.purchase();

    expect(tickets, hasLength(5));
    expect(tickets.map((ticket) => ticket.id).toSet(), hasLength(5));
    expect(tickets.map((ticket) => ticket.qrData).toSet(), hasLength(5));
    expect(
      tickets.map((ticket) => ticket.typeTitle),
      containsAll([
        'بالغ',
        'طفل',
        'طفل',
        'طالب',
        'بالغ',
      ]),
    );
    expect(cart.purchasedTickets, hasLength(5));
    expect(cart.lastPurchaseTickets, hasLength(5));
    expect(cart.totalVisitors, 0);
    expect(cart.totalPrice, 0);
  });
}
