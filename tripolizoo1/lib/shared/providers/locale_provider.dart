import 'package:flutter/foundation.dart';

enum AppLocale { ar, en, it }

class LocaleProvider extends ChangeNotifier {
  AppLocale _locale = AppLocale.ar;

  AppLocale get locale => _locale;

  String get code {
    switch (_locale) {
      case AppLocale.ar:
        return 'AR';
      case AppLocale.en:
        return 'EN';
      case AppLocale.it:
        return 'IT';
    }
  }

  void cycleLocale() {
    _locale = switch (_locale) {
      AppLocale.ar => AppLocale.en,
      AppLocale.en => AppLocale.it,
      AppLocale.it => AppLocale.ar,
    };
    notifyListeners();
  }

  void setLocale(AppLocale locale) {
    _locale = locale;
    notifyListeners();
  }
}
