import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/app.dart';

void main() {
  testWidgets('App launches with splash screen', (WidgetTester tester) async {
    await tester.pumpWidget(TripoliZooApp());
    await tester.pump();

    expect(find.text('حديقة طرابلس'), findsOneWidget);
    expect(find.text('ابدأ رحلتك'), findsOneWidget);
  });
}
