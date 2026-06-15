import 'package:flutter/widgets.dart';

extension LocalizedTextContext on BuildContext {
  String localized({
    required String ar,
    required String en,
  }) {
    return Localizations.localeOf(this).languageCode == 'ar' ? ar : en;
  }
}
