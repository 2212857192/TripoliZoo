import 'package:flutter/material.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';

/// Single source of truth for ticket pricing — ready for backend sync.
abstract final class TicketData {
  static const List<TicketType> all = [
    TicketType(
      id: 'adult_ly',
      title: 'تذكرة الكبار — مواطن',
      categoryLabel: 'تذكرة الكبار — مواطن',
      price: 10,
      subtitle: '12 سنة فأكثر',
      icon: Icons.person,
      isLocal: true,
      name: 'تذكرة الكبار — مواطن',
    ),
    TicketType(
      id: 'child_ly',
      title: 'تذكرة الأطفال — مواطن',
      categoryLabel: 'تذكرة الأطفال — مواطن',
      price: 5,
      subtitle: 'من 3 إلى 12 سنة',
      icon: Icons.child_care,
      isLocal: true,
      name: 'تذكرة الأطفال — مواطن',
    ),
    TicketType(
      id: 'student',
      title: 'تذكرة الطلاب — مواطن',
      categoryLabel: 'تذكرة الطلاب — مواطن',
      price: 5,
      subtitle: 'مدارس وجامعات',
      icon: Icons.school,
      isLocal: true,
      name: 'تذكرة الطلاب — مواطن',
    ),
    TicketType(
      id: 'adult_intl',
      title: 'تذكرة أجنبي — بالغ',
      categoryLabel: 'تذكرة أجنبي — بالغ',
      price: 50,
      subtitle: '12 سنة فأكثر',
      icon: Icons.public,
      isLocal: false,
      name: 'تذكرة أجنبي — بالغ',
    ),
    TicketType(
      id: 'child_intl',
      title: 'تذكرة أجنبي — طفل',
      categoryLabel: 'تذكرة أجنبي — طفل',
      price: 25,
      subtitle: 'من 3 إلى 12 سنة',
      icon: Icons.child_friendly,
      isLocal: false,
      name: 'تذكرة أجنبي — طفل',
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
