import 'package:flutter/foundation.dart';

enum AppLocale { ar, en }

class LocaleProvider extends ChangeNotifier {
  AppLocale _locale = AppLocale.ar;

  AppLocale get locale => _locale;

  String get code {
    switch (_locale) {
      case AppLocale.ar:
        return 'AR';
      case AppLocale.en:
        return 'EN';
    }
  }

  void cycleLocale() {
    _locale = switch (_locale) {
      AppLocale.ar => AppLocale.en,
      AppLocale.en => AppLocale.ar,
    };
    notifyListeners();
  }

  void setLocale(AppLocale locale) {
    _locale = locale;
    notifyListeners();
  }
}
