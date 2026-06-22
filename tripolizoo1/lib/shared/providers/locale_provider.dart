import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

enum AppLocale { ar, en }

class LocaleProvider extends ChangeNotifier {
  static const _storageKey = 'app_locale';

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

  Future<void> loadSaved() async {
    final prefs = await SharedPreferences.getInstance();
    final saved = prefs.getString(_storageKey);
    final next = switch (saved) {
      'en' => AppLocale.en,
      'ar' => AppLocale.ar,
      _ => AppLocale.ar,
    };
    if (next != _locale) {
      _locale = next;
      notifyListeners();
    }
  }

  Future<void> _persist(AppLocale locale) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_storageKey, locale.name);
  }

  void cycleLocale() {
    _locale = switch (_locale) {
      AppLocale.ar => AppLocale.en,
      AppLocale.en => AppLocale.ar,
    };
    _persist(_locale);
    notifyListeners();
  }

  void setLocale(AppLocale locale) {
    if (_locale == locale) return;
    _locale = locale;
    _persist(_locale);
    notifyListeners();
  }
}
