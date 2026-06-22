import 'package:flutter/material.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';
import 'package:tripolizoo/shared/constants/ticket_data.dart';

class ElectronicPaymentSession {
  const ElectronicPaymentSession({
    required this.processId,
    required this.amount,
    required this.invoiceNo,
  });

  final String processId;
  final double amount;
  final String invoiceNo;

  factory ElectronicPaymentSession.fromJson(Map<String, dynamic> json) {
    return ElectronicPaymentSession(
      processId: json['process_id']?.toString() ?? '',
      amount: (json['amount'] as num?)?.toDouble() ?? 0,
      invoiceNo: json['invoice_no']?.toString() ?? '',
    );
  }
}

abstract class TicketRepository {
  Future<List<TicketType>> fetchTypes();
  Future<List<PurchasedTicket>> purchaseCash({required Map<String, int> cart});
  Future<ElectronicPaymentSession> verifyElectronic({
    required Map<String, int> cart,
    required String mobile,
  });
  Future<List<PurchasedTicket>> confirmElectronic({
    required String processId,
    required String otp,
  });
  Future<List<PurchasedTicket>> fetchMine();
}

class ApiTicketRepository implements TicketRepository {
  ApiTicketRepository({ApiClient? apiClient, TicketRepository? fallback})
      : _apiClient = apiClient ?? ApiClient(),
        _fallback = fallback ?? MockTicketRepository();

  final ApiClient _apiClient;
  final TicketRepository _fallback;

  @override
  Future<List<TicketType>> fetchTypes() async {
    try {
      final response = await _apiClient.get(ApiConfig.ticketTypes, auth: false);
      final data = response['data'];
      if (data is List) {
        return data
            .whereType<Map<String, dynamic>>()
            .map(_typeFromJson)
            .toList();
      }
    } catch (_) {
      return _fallback.fetchTypes();
    }

    return _fallback.fetchTypes();
  }

  List<Map<String, dynamic>> _itemsPayload(Map<String, int> cart) {
    return cart.entries
        .where((entry) => entry.value > 0)
        .map(
          (entry) => {
            'ticket_type_id': int.parse(entry.key),
            'quantity': entry.value,
          },
        )
        .toList();
  }

  @override
  Future<List<PurchasedTicket>> purchaseCash({
    required Map<String, int> cart,
  }) async {
    final response = await _apiClient.post(
      ApiConfig.ticketPurchaseCash,
      body: {'items': _itemsPayload(cart)},
    );

    return _ticketsFromResponse(response);
  }

  @override
  Future<ElectronicPaymentSession> verifyElectronic({
    required Map<String, int> cart,
    required String mobile,
  }) async {
    final response = await _apiClient.post(
      ApiConfig.ticketPurchaseElectronicVerify,
      body: {
        'items': _itemsPayload(cart),
        'mobile': mobile,
      },
    );

    final data = response['data'];
    if (data is! Map<String, dynamic>) {
      throw const AuthException('استجابة غير متوقعة من الخادم');
    }

    return ElectronicPaymentSession.fromJson(data);
  }

  @override
  Future<List<PurchasedTicket>> confirmElectronic({
    required String processId,
    required String otp,
  }) async {
    final response = await _apiClient.post(
      ApiConfig.ticketPurchaseElectronicConfirm,
      body: {
        'process_id': processId,
        'otp': otp,
      },
    );

    return _ticketsFromResponse(response);
  }

  @override
  Future<List<PurchasedTicket>> fetchMine() async {
    try {
      final response = await _apiClient.get(ApiConfig.tickets);
      final data = response['data'];
      if (data is List) {
        return data
            .whereType<Map<String, dynamic>>()
            .map(PurchasedTicket.fromJson)
            .toList();
      }
    } catch (_) {
      return _fallback.fetchMine();
    }

    return _fallback.fetchMine();
  }

  List<PurchasedTicket> _ticketsFromResponse(Map<String, dynamic> response) {
    final data = response['data'];
    if (data is! List) {
      throw const AuthException('استجابة غير متوقعة من الخادم');
    }

    return data
        .whereType<Map<String, dynamic>>()
        .map(PurchasedTicket.fromJson)
        .toList();
  }

  TicketType _typeFromJson(Map<String, dynamic> json) {
    final isLocal = json['is_local'] as bool? ?? true;
    final ageGroup =
        json['visitor_age_group']?.toString() ?? json['subtitle']?.toString() ?? '';
    final category =
        json['title']?.toString() ?? json['name']?.toString() ?? '';

    return TicketType(
      id: json['id'].toString(),
      title: category,
      categoryLabel: json['category_label']?.toString() ?? category,
      price: (json['price'] as num?)?.round() ?? 0,
      subtitle: ageGroup,
      icon: _iconForAgeGroup(ageGroup, isLocal),
      isLocal: isLocal,
      name: json['name']?.toString(),
    );
  }

  IconData _iconForAgeGroup(String ageGroup, bool isLocal) {
    final normalized = ageGroup.toLowerCase();
    if (normalized.contains('طفل') || normalized.contains('child')) {
      return isLocal ? Icons.child_care : Icons.child_friendly;
    }
    if (normalized.contains('طالب') || normalized.contains('student')) {
      return Icons.school;
    }
    return isLocal ? Icons.person : Icons.public;
  }
}

class MockTicketRepository implements TicketRepository {
  @override
  Future<List<TicketType>> fetchTypes() async {
    await Future<void>.delayed(const Duration(milliseconds: 100));
    return TicketData.all;
  }

  @override
  Future<List<PurchasedTicket>> purchaseCash({
    required Map<String, int> cart,
  }) async {
    return _mockPurchase(cart);
  }

  @override
  Future<ElectronicPaymentSession> verifyElectronic({
    required Map<String, int> cart,
    required String mobile,
  }) async {
    await Future<void>.delayed(const Duration(milliseconds: 100));
    return ElectronicPaymentSession(
      processId: 'mock-process',
      amount: _mockTotal(cart),
      invoiceNo: 'TZ-MOCK',
    );
  }

  @override
  Future<List<PurchasedTicket>> confirmElectronic({
    required String processId,
    required String otp,
  }) async {
    await Future<void>.delayed(const Duration(milliseconds: 100));
    return const [];
  }

  @override
  Future<List<PurchasedTicket>> fetchMine() async => const [];

  Future<List<PurchasedTicket>> _mockPurchase(Map<String, int> cart) async {
    await Future<void>.delayed(const Duration(milliseconds: 150));
    final purchasedAt = DateTime.now();
    final batchId = purchasedAt.microsecondsSinceEpoch;
    final created = <PurchasedTicket>[];
    var sequence = 1;

    for (final entry in cart.entries) {
      final type = TicketData.byId(entry.key);
      if (type == null) continue;

      for (var index = 0; index < entry.value; index++) {
        final serial = sequence.toString().padLeft(2, '0');
        created.add(
          PurchasedTicket(
            id: 'TK-${batchId.toString().padLeft(6, '0')}-$serial',
            qrData: 'TRIPOLI-ZOO-$batchId-$serial-${type.id}',
            visitDate: purchasedAt,
            typeId: type.id,
            typeTitle: type.name ?? type.title,
            price: type.price,
            purchasedAt: purchasedAt,
          ),
        );
        sequence++;
      }
    }

    return created;
  }

  double _mockTotal(Map<String, int> cart) {
    var total = 0.0;
    for (final entry in cart.entries) {
      final type = TicketData.byId(entry.key);
      if (type != null) total += type.price * entry.value;
    }
    return total;
  }
}
