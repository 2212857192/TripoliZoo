import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter/services.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_profile/presentation/profile_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/data/ticket_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';

void main() {
  const urlLauncherChannel = MethodChannel('plugins.flutter.io/url_launcher');

  Widget buildProfile({
    TicketCartProvider? ticketCart,
    AuthProvider? authProvider,
  }) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider.value(
          value: authProvider ?? AuthProvider(),
        ),
        ChangeNotifierProvider(create: (_) => LocaleProvider()),
        ChangeNotifierProvider.value(
          value: ticketCart ?? TicketCartProvider(),
        ),
      ],
      child: const MaterialApp(
        locale: Locale('ar'),
        supportedLocales: [Locale('ar'), Locale('en')],
        localizationsDelegates: [
          GlobalMaterialLocalizations.delegate,
          GlobalWidgetsLocalizations.delegate,
          GlobalCupertinoLocalizations.delegate,
        ],
        home: ProfileScreen(),
      ),
    );
  }

  testWidgets('opens the emergency numbers section from the account settings',
      (tester) async {
    await tester.pumpWidget(buildProfile());

    final emergencyTab = find.text('أرقام الطوارئ');
    await tester.ensureVisible(emergencyTab);
    await tester.tap(emergencyTab);
    await tester.pumpAndSettle();

    expect(find.text('191'), findsOneWidget);
    expect(find.text('192'), findsOneWidget);
    expect(find.text('الإسعاف'), findsOneWidget);
    expect(find.text('الأمن'), findsOneWidget);

    await tester.tap(find.byIcon(Icons.arrow_back_ios_new));
    await tester.pumpAndSettle();

    expect(find.text('191'), findsNothing);
    expect(find.text('أرقام الطوارئ'), findsOneWidget);
  });

  testWidgets('opens the phone dialer with the emergency number',
      (tester) async {
    String? launchedUrl;
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(urlLauncherChannel, (call) async {
      launchedUrl = (call.arguments as Map<Object?, Object?>)['url'] as String?;
      return true;
    });

    await tester.pumpWidget(buildProfile());
    await tester.ensureVisible(find.text('أرقام الطوارئ'));
    await tester.tap(find.text('أرقام الطوارئ'));
    await tester.pumpAndSettle();

    await tester.tap(find.text('اتصال').first);
    await tester.pumpAndSettle();

    expect(launchedUrl, 'tel:191');

    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(urlLauncherChannel, null);
  });

  testWidgets('digital tickets row has no barcode icon', (tester) async {
    await tester.pumpWidget(buildProfile());

    expect(find.byIcon(Icons.line_weight_rounded), findsNothing);
  });

  testWidgets('guest account hides personal information and digital tickets',
      (tester) async {
    final authProvider = AuthProvider();
    await tester.runAsync(authProvider.guestLogin);
    await tester.pumpWidget(buildProfile(authProvider: authProvider));

    expect(find.text('تعديل'), findsNothing);
    expect(find.text('المعلومات الشخصية'), findsNothing);
    expect(find.text('تذاكري الرقمية'), findsNothing);
    expect(find.text('إعدادات اللغة'), findsOneWidget);
    expect(find.text('أرقام الطوارئ'), findsOneWidget);
  });

  testWidgets('edits personal information and displays the saved values',
      (tester) async {
    final authProvider = AuthProvider();
    await tester.runAsync(
      () => authProvider.login('visitor@example.com', '123456'),
    );
    await tester.pumpWidget(buildProfile(authProvider: authProvider));

    await tester.tap(find.text('المعلومات الشخصية'));
    await tester.pumpAndSettle();

    await tester.enterText(
      find.byKey(const ValueKey('profile-name-field')),
      'آمنة العقاب',
    );
    await tester.enterText(
      find.byKey(const ValueKey('profile-email-field')),
      'amna@example.com',
    );
    await tester.enterText(
      find.byKey(const ValueKey('profile-phone-field')),
      '+218 91 123 4567',
    );
    final saveButton = find.byKey(const ValueKey('save-profile-button'));
    await tester.ensureVisible(saveButton);
    await tester.tap(saveButton);
    await tester.pumpAndSettle();

    expect(find.text('تم تحديث المعلومات الشخصية'), findsOneWidget);
    expect(find.text('آمنة العقاب'), findsWidgets);
    expect(find.text('amna@example.com'), findsOneWidget);
    expect(find.text('+218 91 123 4567'), findsOneWidget);
  });

  testWidgets('changes the password inline for a registered account',
      (tester) async {
    final authProvider = AuthProvider();
    await tester.runAsync(
      () => authProvider.login('visitor@example.com', 'oldpass1'),
    );
    await tester.pumpWidget(buildProfile(authProvider: authProvider));

    await tester.tap(find.text('المعلومات الشخصية'));
    await tester.pumpAndSettle();

    final passwordTile = find.byKey(
      const ValueKey('change-password-tile'),
    );
    await tester.ensureVisible(passwordTile);
    await tester.tap(passwordTile);
    await tester.pumpAndSettle();

    await tester.enterText(
      find.byKey(const ValueKey('current-password-field')),
      'oldpass1',
    );
    await tester.enterText(
      find.byKey(const ValueKey('new-password-field')),
      'newpass123',
    );
    await tester.enterText(
      find.byKey(const ValueKey('confirm-password-field')),
      'newpass123',
    );

    final changeButton = find.byKey(const ValueKey('change-password-button'));
    tester.testTextInput.hide();
    await tester.pumpAndSettle();
    await tester.ensureVisible(changeButton);
    await tester.pumpAndSettle();
    await tester.tap(changeButton);
    await tester.pumpAndSettle();

    expect(find.text('تم تغيير كلمة المرور بنجاح'), findsOneWidget);
  });

  testWidgets('language settings contains Arabic and English only',
      (tester) async {
    await tester.pumpWidget(buildProfile());

    await tester.ensureVisible(find.text('إعدادات اللغة'));
    await tester.tap(find.text('إعدادات اللغة'));
    await tester.pumpAndSettle();

    expect(find.text('العربية'), findsOneWidget);
    expect(find.text('English'), findsOneWidget);
    expect(find.text('Italiano'), findsNothing);
  });

  testWidgets('opens and closes every account section without layout errors',
      (tester) async {
    final authProvider = AuthProvider();
    await tester.runAsync(
      () => authProvider.login('visitor@example.com', '123456'),
    );
    await tester.pumpWidget(buildProfile(authProvider: authProvider));

    for (final section in [
      'المعلومات الشخصية',
      'تذاكري الرقمية',
      'إعدادات اللغة',
      'أرقام الطوارئ',
    ]) {
      final sectionButton = find.text(section);
      await tester.ensureVisible(sectionButton);
      await tester.tap(sectionButton);
      await tester.pumpAndSettle();

      expect(tester.takeException(), isNull);

      await tester.tap(find.byIcon(Icons.arrow_back_ios_new));
      await tester.pumpAndSettle();
      expect(tester.takeException(), isNull);
    }
  });

  testWidgets('opens the selected ticket image from ticket history',
      (tester) async {
    final authProvider = AuthProvider();
    await tester.runAsync(
      () => authProvider.login('visitor@example.com', '123456'),
    );
    final ticketCart = TicketCartProvider(repository: MockTicketRepository());
    await ticketCart.loadTypes();
    ticketCart.increment('adult_ly');
    final ticket = (await ticketCart.purchaseCash()).single;
    await tester.pumpWidget(
      buildProfile(
        ticketCart: ticketCart,
        authProvider: authProvider,
      ),
    );

    await tester.ensureVisible(find.text('تذاكري الرقمية'));
    await tester.tap(find.text('تذاكري الرقمية'));
    await tester.pumpAndSettle();

    await tester.tap(find.text(ticket.typeTitle));
    await tester.pumpAndSettle();

    expect(find.text('صورة التذكرة'), findsOneWidget);
    expect(find.text(ticket.id), findsOneWidget);
    expect(find.byType(QrImageView), findsOneWidget);
    expect(
      find.byKey(ValueKey('ticket-preview-${ticket.id}')),
      findsOneWidget,
    );
  });
}
