import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/animal.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/animals_explore_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/qr_scanner_screen.dart';

void main() {
  const animal = Animal(
    id: 1,
    name: 'الأسد الأفريقي',
    sciName: 'Panthera leo',
    category: 'predators',
    image: 'assets/images/lion.jpg',
    desc: 'وصف الحيوان',
    stats: {'العمر': '12-16 سنة', 'الوزن': '190 كجم'},
    facts: ['حقيقة أولى', 'حقيقة ثانية'],
    location: 'مملكة الأسود',
    habitat: 'السافانا المفتوحة',
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

  testWidgets('animal details use one card with one text for all information',
      (tester) async {
    await tester.pumpWidget(
      buildScreen(const AnimalDetailScreen(animal: animal)),
    );
    await tester.pumpAndSettle();

    expect(find.text(animal.name), findsWidgets);
    expect(
      find.byKey(const ValueKey('animal-information-card')),
      findsOneWidget,
    );

    final informationFinder =
        find.byKey(const ValueKey('animal-information-text'));
    expect(informationFinder, findsOneWidget);
    final information = tester.widget<Text>(informationFinder).data!;

    expect(information, contains('وصف الحيوان'));
    expect(information, contains('السافانا المفتوحة'));
    expect(information, contains('مملكة الأسود'));
    expect(information, contains('12-16 سنة'));
    expect(information, isNot(contains('حقيقة أولى')));
    expect(find.text('عرض موقعه على الخريطة'), findsNothing);
  });

  testWidgets('animal information card localizes its labels in English',
      (tester) async {
    await tester.pumpWidget(
      buildScreen(
        const AnimalDetailScreen(animal: animal),
        languageCode: 'en',
      ),
    );
    await tester.pumpAndSettle();

    final information = tester
        .widget<Text>(find.byKey(const ValueKey('animal-information-text')))
        .data!;

    expect(information, contains('Habitat'));
    expect(information, contains('Location'));
    expect(information, contains('Quick facts'));
    expect(information, isNot(contains('Amazing facts')));
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

  testWidgets('QR scanner explanation is available in English', (tester) async {
    await tester.pumpWidget(
      buildScreen(
        QrScannerScreen(
          requestCameraPermission: () async => false,
        ),
        languageCode: 'en',
      ),
    );
    await tester.pumpAndSettle();

    expect(
      find.text(
        'Scan an animal code to explore its information and learn more about it.',
      ),
      findsOneWidget,
    );
  });
}
