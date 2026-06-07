import 'package:flutter/foundation.dart';
import 'package:tripolizoo/shared/constants/ticket_data.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';

class TicketCartProvider extends ChangeNotifier {
  DateTime selectedDate = DateTime.now();
  final Map<String, int> _cart = {
    'adult_ly': 1,
    'child_ly': 0,
    'adult_intl': 0,
    'child_intl': 0,
    'student': 0,
  };

  final List<PurchasedTicket> _purchased = [];

  Map<String, int> get cart => Map.unmodifiable(_cart);
  List<PurchasedTicket> get purchasedTickets => List.unmodifiable(_purchased);

  int get totalVisitors => _cart.values.fold(0, (a, b) => a + b);

  double get totalPrice {
    var sum = 0.0;
    for (final entry in _cart.entries) {
      final type = TicketData.byId(entry.key);
      if (type != null) sum += type.price * entry.value;
    }
    return sum;
  }

  void setDate(DateTime date) {
    selectedDate = date;
    notifyListeners();
  }

  void increment(String id) {
    _cart[id] = (_cart[id] ?? 0) + 1;
    notifyListeners();
  }

  void decrement(String id) {
    final current = _cart[id] ?? 0;
    if (current > 0) {
      _cart[id] = current - 1;
      notifyListeners();
    }
  }

  PurchasedTicket? purchase() {
    if (totalPrice <= 0) return null;
    final ticket = PurchasedTicket(
      id: 'ZL-${DateTime.now().millisecondsSinceEpoch}',
      qrData: 'TRIPOLI-ZOO-${DateTime.now().millisecondsSinceEpoch}',
      visitDate: selectedDate,
      items: Map.from(_cart),
      totalPrice: totalPrice,
      purchasedAt: DateTime.now(),
    );
    _purchased.add(ticket);
    notifyListeners();
    return ticket;
  }
}
