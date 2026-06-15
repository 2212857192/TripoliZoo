import 'package:flutter/material.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';

/// Single source of truth for ticket pricing — ready for backend sync.
abstract final class TicketData {
  static const List<TicketType> all = [
    TicketType(
      id: 'adult_ly',
      title: 'بالغ',
      categoryLabel: 'بالغ',
      price: 10,
      subtitle: 'فوق 12 سنة',
      icon: Icons.person,
      isLocal: true,
    ),
    TicketType(
      id: 'child_ly',
      title: 'طفل',
      categoryLabel: 'طفل',
      price: 5,
      subtitle: 'من 3 إلى 12 سنة',
      icon: Icons.child_care,
      isLocal: true,
    ),
    TicketType(
      id: 'student',
      title: 'طالب',
      categoryLabel: 'طالب',
      price: 5,
      subtitle: 'مدارس وجامعات',
      icon: Icons.school,
      isLocal: true,
    ),
    TicketType(
      id: 'adult_intl',
      title: 'بالغ',
      categoryLabel: 'بالغ',
      price: 50,
      subtitle: 'Foreign Adult',
      icon: Icons.public,
      isLocal: false,
    ),
    TicketType(
      id: 'child_intl',
      title: 'طفل',
      categoryLabel: 'طفل',
      price: 25,
      subtitle: 'Foreign Child',
      icon: Icons.child_friendly,
      isLocal: false,
    ),
  ];

  static List<TicketType> get local => all.where((t) => t.isLocal).toList();

  static List<TicketType> get foreign => all.where((t) => !t.isLocal).toList();

  static TicketType? byId(String id) {
    try {
      return all.firstWhere((t) => t.id == id);
    } catch (_) {
      return null;
    }
  }
}
