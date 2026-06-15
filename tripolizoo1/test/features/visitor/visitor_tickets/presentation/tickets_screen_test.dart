import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter/services.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/tickets_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/services/ticket_image_service.dart';

void main() {
  const imageChannel = MethodChannel('tripolizoo/ticket_images');

  Future<AuthProvider> registeredAuth(WidgetTester tester) async {
    final auth = AuthProvider();
    await tester.runAsync(
      () => auth.login('visitor@example.com', '123456'),
    );
    return auth;
  }

  Widget buildTickets(
    TicketCartProvider cart,
    AuthProvider auth, {
    Locale locale = const Locale('ar'),
  }) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider.value(value: cart),
        ChangeNotifierProvider.value(value: auth),
      ],
      child: MaterialApp(
        locale: locale,
        supportedLocales: const [Locale('ar'), Locale('en')],
        localizationsDelegates: const [
          GlobalMaterialLocalizations.delegate,
          GlobalWidgetsLocalizations.delegate,
          GlobalCupertinoLocalizations.delegate,
        ],
        home: const TicketsScreen(),
      ),
    );
  }

  testWidgets('uses a compact summary and neutral inactive filter',
      (tester) async {
    final auth = await registeredAuth(tester);

    await tester.pumpWidget(buildTickets(TicketCartProvider(), auth));

    final nextButton = tester.widget<SizedBox>(
      find.byKey(const ValueKey('compact-next-button')),
    );
    expect(nextButton.width, 120);
    expect(nextButton.height, 44);
    expect(
      find.ancestor(
        of: find.text('10 د.ل'),
        matching: find.byKey(const ValueKey('ticket-total-and-count-row')),
      ),
      findsOneWidget,
    );
    expect(
      find.ancestor(
        of: find.text('(تذكرة واحدة)'),
        matching: find.byKey(const ValueKey('ticket-total-and-count-row')),
      ),
      findsOneWidget,
    );

    final foreignTab = tester.widget<Text>(find.text('الأجانب'));
    expect(foreignTab.style?.color, Colors.grey.shade600);
  });

  testWidgets('shows every purchased ticket as a separate QR card',
      (tester) async {
    final auth = await registeredAuth(tester);
    final cart = TicketCartProvider()
      ..increment('child_ly')
      ..increment('child_ly')
      ..increment('student')
      ..increment('adult_intl');

    await tester.pumpWidget(buildTickets(cart, auth));

    await tester.tap(
      find.byKey(const ValueKey('continue-to-payment-button')),
    );
    await tester.pumpAndSettle();

    expect(find.text('إتمام الدفع'), findsOneWidget);
    expect(find.byType(QrImageView), findsNothing);
    expect(cart.purchasedTickets, isEmpty);
    expect(find.byKey(const ValueKey('payment-invoice')), findsOneWidget);
    expect(find.text('بالغ × 1'), findsNWidgets(2));
    expect(find.text('طفل × 2'), findsOneWidget);
    expect(find.text('طالب × 1'), findsOneWidget);

    final totalCard = tester.widget<Container>(
      find.byKey(const ValueKey('payment-total-card')),
    );
    expect((totalCard.decoration! as BoxDecoration).color, Colors.white);

    await tester.enterText(
      find.byKey(const ValueKey('payment-phone-field')),
      '0912345678',
    );
    await tester.tap(find.byKey(const ValueKey('confirm-payment-button')));
    await tester.pumpAndSettle();

    expect(find.byType(QrImageView), findsNWidgets(5));
    expect(find.text('تم إصدار 5 تذاكر منفصلة'), findsOneWidget);
    expect(find.text('تحميل التذاكر'), findsOneWidget);
    expect(find.text('تذكرة دخول'), findsNWidgets(5));
    expect(find.text('الفئة'), findsNWidgets(5));
    expect(find.text('مواطن (بالغ)'), findsOneWidget);
    expect(find.text('أجنبي (بالغ)'), findsOneWidget);
  });

  testWidgets('shows the purchased ticket category in English', (tester) async {
    final auth = await registeredAuth(tester);
    final cart = TicketCartProvider();

    await tester.pumpWidget(
      buildTickets(cart, auth, locale: const Locale('en')),
    );

    await tester.tap(
      find.byKey(const ValueKey('continue-to-payment-button')),
    );
    await tester.pumpAndSettle();
    await tester.enterText(
      find.byKey(const ValueKey('payment-phone-field')),
      '0912345678',
    );
    await tester.tap(find.byKey(const ValueKey('confirm-payment-button')));
    await tester.pumpAndSettle();

    expect(find.text('Entry Ticket'), findsOneWidget);
    expect(find.text('Category'), findsOneWidget);
    expect(find.text('Citizen (Adult)'), findsOneWidget);
  });

  testWidgets('payment requires a valid linked phone number', (tester) async {
    final auth = await registeredAuth(tester);
    final cart = TicketCartProvider();

    await tester.pumpWidget(buildTickets(cart, auth));

    await tester.tap(
      find.byKey(const ValueKey('continue-to-payment-button')),
    );
    await tester.pumpAndSettle();
    await tester.tap(find.byKey(const ValueKey('confirm-payment-button')));
    await tester.pumpAndSettle();

    expect(
      find.text('أدخل رقم الهاتف المرتبط بخدمة الدفع'),
      findsOneWidget,
    );
    expect(cart.purchasedTickets, isEmpty);
    expect(find.byType(QrImageView), findsNothing);
  });

  testWidgets('guest users see sign in and registration options',
      (tester) async {
    final auth = AuthProvider();
    await tester.runAsync(auth.guestLogin);

    await tester.pumpWidget(buildTickets(TicketCartProvider(), auth));
    await tester.pumpAndSettle();

    expect(
      find.byKey(const ValueKey('tickets-account-required')),
      findsOneWidget,
    );
    expect(find.text('تسجيل الدخول مطلوب'), findsOneWidget);
    expect(
      find.byKey(const ValueKey('tickets-login-button')),
      findsOneWidget,
    );
    expect(
      find.byKey(const ValueKey('tickets-register-button')),
      findsOneWidget,
    );
    expect(
      find.byKey(const ValueKey('continue-to-payment-button')),
      findsNothing,
    );
  });

  testWidgets('exports one PNG image for every purchased ticket',
      (tester) async {
    final cart = TicketCartProvider()
      ..increment('child_ly')
      ..increment('child_ly')
      ..increment('student')
      ..increment('adult_intl');
    final tickets = cart.purchase();
    final savedNames = <String>[];

    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(imageChannel, (call) async {
      expect(call.method, 'saveImage');
      final arguments = call.arguments as Map<Object?, Object?>;
      final bytes = arguments['bytes']! as Uint8List;
      final name = arguments['name']! as String;

      expect(bytes.take(8).toList(), [137, 80, 78, 71, 13, 10, 26, 10]);
      savedNames.add(name);
      return true;
    });

    final savedCount = await tester.runAsync(
      () => TicketImageService.saveTickets(tickets),
    );

    expect(savedCount, 5);
    expect(savedNames, hasLength(5));
    expect(savedNames.toSet(), hasLength(5));

    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(imageChannel, null);
  });
}
