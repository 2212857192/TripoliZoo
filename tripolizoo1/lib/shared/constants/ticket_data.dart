import 'package:flutter/material.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';

/// Single source of truth for ticket pricing — ready for backend sync.
abstract final class TicketData {
  static const List<TicketType> all = [
    TicketType(
      id: 'adult_ly',
      title: 'بالغ ليبي',
      categoryLabel: 'بالغين (ليبيون)',
      price: 10,
      subtitle: 'فوق 12 سنة',
      icon: Icons.person,
      isLocal: true,
    ),
    TicketType(
      id: 'child_ly',
      title: 'طفل ليبي',
      categoryLabel: 'أطفال (3-12 سنة)',
      price: 5,
      subtitle: 'من 3 إلى 12 سنة',
      icon: Icons.child_care,
      isLocal: true,
    ),
    TicketType(
      id: 'student',
      title: 'طلبة',
      categoryLabel: 'طلبة (مدارس وجامعات)',
      price: 5,
      subtitle: 'مدارس وجامعات',
      icon: Icons.school,
      isLocal: true,
    ),
    TicketType(
      id: 'adult_intl',
      title: 'بالغ أجنبي',
      categoryLabel: 'بالغين (أجانب)',
      price: 50,
      subtitle: 'Foreign Adult',
      icon: Icons.public,
      isLocal: false,
    ),
    TicketType(
      id: 'child_intl',
      title: 'طفل أجنبي',
      categoryLabel: 'أطفال (أجانب)',
      price: 25,
      subtitle: 'Foreign Child',
      icon: Icons.child_friendly,
      isLocal: false,
    ),
  ];

  static List<TicketType> get local =>
      all.where((t) => t.isLocal).toList();

  static List<TicketType> get foreign =>
      all.where((t) => !t.isLocal).toList();

  static TicketType? byId(String id) {
    try {
      return all.firstWhere((t) => t.id == id);
    } catch (_) {
      return null;
    }
  }
}
