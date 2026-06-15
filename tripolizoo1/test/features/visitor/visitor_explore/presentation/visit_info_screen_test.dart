import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter/services.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/visit_info_screen.dart';

void main() {
  const urlLauncherChannel = MethodChannel('plugins.flutter.io/url_launcher');

  Widget buildVisitInfo() {
    final router = GoRouter(
      initialLocation: '/visit-info',
      routes: [
        GoRoute(
          path: '/visit-info',
          builder: (_, __) => const VisitInfoScreen(),
        ),
        GoRoute(
          path: '/tickets',
          builder: (_, __) => const Scaffold(
            body: Center(child: Text('Tickets destination')),
          ),
        ),
      ],
    );

    return MaterialApp.router(
      routerConfig: router,
      locale: const Locale('ar'),
      supportedLocales: const [Locale('ar'), Locale('en')],
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
    );
  }

  testWidgets('organizes visit information into the requested cards',
      (tester) async {
    await tester.pumpWidget(buildVisitInfo());
    await tester.pumpAndSettle();

    expect(find.text('موقع الحديقة'), findsOneWidget);
    expect(find.text('أسعار تذاكر الدخول'), findsOneWidget);
    expect(find.text('بالغ'), findsOneWidget);
    expect(find.text('طفل'), findsOneWidget);
    expect(find.text('طالب'), findsOneWidget);
    expect(find.text('مجاني'), findsNWidgets(2));

    await tester.drag(
      find.byType(CustomScrollView),
      const Offset(0, -700),
    );
    await tester.pumpAndSettle();

    expect(find.text('التذكرة الإلكترونية'), findsOneWidget);

    await tester.drag(
      find.byType(CustomScrollView),
      const Offset(0, -500),
    );
    await tester.pumpAndSettle();

    expect(find.text('تعليمات وإرشادات الزيارة'), findsOneWidget);

    expect(find.text('دخول مجاني'), findsNothing);
    expect(find.text('تعليمات هامة'), findsNothing);
    expect(find.text('يُمنع إدخال'), findsNothing);
  });

  testWidgets('opens the Tripoli Zoo location in Google Maps', (tester) async {
    String? launchedUrl;
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(urlLauncherChannel, (call) async {
      launchedUrl = (call.arguments as Map<Object?, Object?>)['url'] as String?;
      return true;
    });

    await tester.pumpWidget(buildVisitInfo());
    await tester.pumpAndSettle();

    final mapButton = find.byKey(const ValueKey('open-zoo-google-maps'));
    await tester.ensureVisible(mapButton);
    await tester.tap(mapButton);
    await tester.pumpAndSettle();

    expect(launchedUrl, contains('google.com/maps/search'));
    expect(launchedUrl, contains('32.859841'));
    expect(launchedUrl, contains('13.175367'));

    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(urlLauncherChannel, null);
  });
}
