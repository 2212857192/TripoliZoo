import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/app.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';

void main() {
  testWidgets('App launches with splash screen', (WidgetTester tester) async {
    final localeProvider = LocaleProvider();
    await tester.pumpWidget(TripoliZooApp(localeProvider: localeProvider));
    await tester.pump();

    expect(find.text('حديقة طرابلس'), findsOneWidget);
    expect(find.text('ابدأ رحلتك'), findsOneWidget);
  });
}
