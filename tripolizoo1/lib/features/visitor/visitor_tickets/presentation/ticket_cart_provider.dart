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
  List<PurchasedTicket> _lastPurchaseTickets = [];

  Map<String, int> get cart => Map.unmodifiable(_cart);
  List<PurchasedTicket> get purchasedTickets => List.unmodifiable(_purchased);
  List<PurchasedTicket> get lastPurchaseTickets =>
      List.unmodifiable(_lastPurchaseTickets);

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

  List<PurchasedTicket> purchase() {
    if (totalVisitors == 0) return const [];

    final purchasedAt = DateTime.now();
    final batchId = purchasedAt.microsecondsSinceEpoch;
    final created = <PurchasedTicket>[];
    var sequence = 1;

    for (final entry in _cart.entries) {
      final type = TicketData.byId(entry.key);
      if (type == null) continue;

      for (var index = 0; index < entry.value; index++) {
        final serial = sequence.toString().padLeft(2, '0');
        created.add(
          PurchasedTicket(
            id: 'ZL-$batchId-$serial',
            qrData: 'TRIPOLI-ZOO-$batchId-$serial-${type.id}',
            visitDate: selectedDate,
            typeId: type.id,
            typeTitle: type.title,
            price: type.price,
            purchasedAt: purchasedAt,
          ),
        );
        sequence++;
      }
    }

    _lastPurchaseTickets = created;
    _purchased.addAll(created);
    for (final id in _cart.keys) {
      _cart[id] = 0;
    }
    notifyListeners();
    return List.unmodifiable(created);
  }
}
