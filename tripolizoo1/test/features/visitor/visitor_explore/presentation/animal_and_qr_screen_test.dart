import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/animal.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/animal_detail_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/qr_scanner_screen.dart';

void main() {
  const animal = Animal(
    id: 1,
    name: 'الأسد الأفريقي',
    sciName: 'Panthera leo',
    category: 'predators',
    image: 'assets/images/lion.jpg',
    desc: 'وصف الحيوان',
    stats: {'الرمز': 'L-001', 'العمر': '12-16 سنة'},
    facts: ['حقيقة أولى'],
    location: 'مملكة الأسود',
    habitat: 'السافانا المفتوحة',
    mapLocationId: 7,
  );

  Widget buildScreen(Widget child, {String languageCode = 'ar'}) {
    return MaterialApp(
      locale: Locale(languageCode),
      supportedLocales: const [Locale('ar'), Locale('en')],
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      home: child,
    );
  }

  testWidgets('animal details show description and show-the-way button',
      (tester) async {
    await tester.pumpWidget(
      buildScreen(const AnimalDetailScreen(animal: animal)),
    );
    await tester.pumpAndSettle();

    expect(find.text(animal.name), findsWidgets);
    expect(find.text('وصف الحيوان'), findsOneWidget);
    expect(find.text('أظهر الطريق'), findsOneWidget);
  });

  testWidgets('animal show-the-way button localizes in English', (tester) async {
    await tester.pumpWidget(
      buildScreen(
        const AnimalDetailScreen(animal: animal),
        languageCode: 'en',
      ),
    );
    await tester.pumpAndSettle();

    expect(find.text('Show the way'), findsOneWidget);
  });

  testWidgets('QR scanner explains that scanning reveals animal information',
      (tester) async {
    await tester.pumpWidget(
      buildScreen(
        QrScannerScreen(
          requestCameraPermission: () async => false,
        ),
      ),
    );
    await tester.pumpAndSettle();

    expect(
      find.text('امسح رمز الحيوان لاستكشاف معلوماته والتعرّف عليه.'),
      findsOneWidget,
    );
  });
}
