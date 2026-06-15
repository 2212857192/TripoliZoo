import 'package:flutter/material.dart';

class TicketType {
  final String id;
  final String title;
  final String categoryLabel;
  final int price;
  final String subtitle;
  final IconData icon;
  final bool isLocal;

  const TicketType({
    required this.id,
    required this.title,
    required this.categoryLabel,
    required this.price,
    required this.subtitle,
    required this.icon,
    required this.isLocal,
  });

  factory TicketType.fromJson(Map<String, dynamic> json) => TicketType(
        id: json['id'] as String,
        title: json['title'] as String,
        categoryLabel: json['category_label'] as String,
        price: json['price'] as int,
        subtitle: json['subtitle'] as String? ?? '',
        icon: Icons.confirmation_number,
        isLocal: json['is_local'] as bool? ?? true,
      );
}

class PurchasedTicket {
  final String id;
  final String qrData;
  final DateTime visitDate;
  final String typeId;
  final String typeTitle;
  final int price;
  final DateTime purchasedAt;

  const PurchasedTicket({
    required this.id,
    required this.qrData,
    required this.visitDate,
    required this.typeId,
    required this.typeTitle,
    required this.price,
    required this.purchasedAt,
  });

  String localizedTypeTitle(String languageCode) {
    final isArabic = languageCode == 'ar';
    return switch (typeId) {
      'adult_ly' || 'adult_intl' => isArabic ? 'بالغ' : 'Adult',
      'child_ly' || 'child_intl' => isArabic ? 'طفل' : 'Child',
      'student' => isArabic ? 'طالب' : 'Student',
      _ => typeTitle,
    };
  }

  String localizedCategoryLabel(String languageCode) {
    final isArabic = languageCode == 'ar';
    final audience = typeId.endsWith('_intl')
        ? (isArabic ? 'أجنبي' : 'Foreigner')
        : (isArabic ? 'مواطن' : 'Citizen');
    return '$audience (${localizedTypeTitle(languageCode)})';
  }
}
