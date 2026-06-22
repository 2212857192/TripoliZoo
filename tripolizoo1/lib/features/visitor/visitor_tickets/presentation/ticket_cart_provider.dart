import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/data/ticket_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';

class TicketCartProvider extends ChangeNotifier {
  TicketCartProvider({TicketRepository? repository})
      : _repository = repository ?? ApiTicketRepository();

  final TicketRepository _repository;

  DateTime selectedDate = DateTime.now();
  final Map<String, int> _cart = {};
  List<TicketType> availableTypes = [];
  bool isLoadingTypes = false;
  bool isPurchasing = false;
  String? loadTypesError;

  final List<PurchasedTicket> _purchased = [];
  List<PurchasedTicket> _lastPurchaseTickets = [];
  VoidCallback? onTicketsTabOpened;

  Map<String, int> get cart => Map.unmodifiable(_cart);
  List<PurchasedTicket> get purchasedTickets => List.unmodifiable(_purchased);
  List<PurchasedTicket> get lastPurchaseTickets =>
      List.unmodifiable(_lastPurchaseTickets);
  List<TicketType> get localTypes =>
      availableTypes.where((type) => type.isLocal).toList();
  List<TicketType> get foreignTypes =>
      availableTypes.where((type) => !type.isLocal).toList();

  int get totalVisitors => _cart.values.fold(0, (a, b) => a + b);

  double get totalPrice {
    var sum = 0.0;
    for (final entry in _cart.entries) {
      final type = typeById(entry.key);
      if (type != null) sum += type.price * entry.value;
    }
    return sum;
  }

  TicketType? typeById(String id) {
    for (final type in availableTypes) {
      if (type.id == id) return type;
    }
    return null;
  }

  Future<void> loadTypes() async {
    isLoadingTypes = true;
    loadTypesError = null;
    notifyListeners();

    try {
      final types = await _repository.fetchTypes();
      availableTypes = types;
      for (final type in types) {
        _cart.putIfAbsent(type.id, () => 0);
      }
    } catch (error) {
      loadTypesError = error.toString();
    } finally {
      isLoadingTypes = false;
      notifyListeners();
    }
  }

  Future<void> loadPurchasedTickets() async {
    try {
      final tickets = await _repository.fetchMine();
      _purchased
        ..clear()
        ..addAll(tickets);
      notifyListeners();
    } catch (_) {
      // Keep local purchases when offline.
    }
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

  Future<List<PurchasedTicket>> purchaseCash() async {
    return _finalizePurchase(
      () => _repository.purchaseCash(cart: _cart),
    );
  }

  Future<ElectronicPaymentSession> verifyElectronicPayment({
    required String mobile,
  }) async {
    if (totalVisitors == 0 || isPurchasing) {
      throw StateError('لا توجد تذاكر في السلة');
    }

    isPurchasing = true;
    notifyListeners();

    try {
      return await _repository.verifyElectronic(cart: _cart, mobile: mobile);
    } finally {
      isPurchasing = false;
      notifyListeners();
    }
  }

  Future<List<PurchasedTicket>> confirmElectronicPayment({
    required String processId,
    required String otp,
  }) async {
    return _finalizePurchase(
      () => _repository.confirmElectronic(processId: processId, otp: otp),
    );
  }

  Future<List<PurchasedTicket>> _finalizePurchase(
    Future<List<PurchasedTicket>> Function() purchase,
  ) async {
    if (totalVisitors == 0 || isPurchasing) return const [];

    isPurchasing = true;
    notifyListeners();

    try {
      final created = await purchase();

      if (created.isEmpty) return const [];

      _lastPurchaseTickets = created;
      _purchased.addAll(created);
      for (final id in _cart.keys) {
        _cart[id] = 0;
      }
      notifyListeners();
      return List.unmodifiable(created);
    } finally {
      isPurchasing = false;
      notifyListeners();
    }
  }
}
