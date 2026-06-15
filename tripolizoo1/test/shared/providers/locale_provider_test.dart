import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';

void main() {
  test('supports Arabic and English only', () {
    expect(AppLocale.values, [AppLocale.ar, AppLocale.en]);
  });

  test('language cycle alternates between Arabic and English', () {
    final provider = LocaleProvider();

    expect(provider.locale, AppLocale.ar);
    expect(provider.code, 'AR');

    provider.cycleLocale();
    expect(provider.locale, AppLocale.en);
    expect(provider.code, 'EN');

    provider.cycleLocale();
    expect(provider.locale, AppLocale.ar);
    expect(provider.code, 'AR');
  });
}
