import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/visit_info_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/visit_info_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/data/ticket_repository.dart';

void main() {
  Widget buildVisitInfo() {
    final router = GoRouter(
      initialLocation: '/visit-info',
      routes: [
        GoRoute(
          path: '/visit-info',
          builder: (_, __) => VisitInfoScreen(
            repository: MockVisitInfoRepository(),
            ticketRepository: MockTicketRepository(),
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

  Future<void> scrollVisitInfo(WidgetTester tester, double offset) async {
    await tester.drag(find.byType(CustomScrollView), Offset(0, offset));
    await tester.pumpAndSettle();
  }

  testWidgets('shows dynamic visit information with location and ticket prices',
      (tester) async {
    await tester.pumpWidget(buildVisitInfo());
    await tester.pumpAndSettle();

    expect(find.text('حالة التشغيل'), findsOneWidget);
    expect(find.text('مفتوحة — أهلاً بزوارنا'), findsOneWidget);
    expect(find.text('ساعات العمل'), findsOneWidget);
    expect(find.text('أيام العمل'), findsOneWidget);
    expect(find.text('مفتوحة يومياً'), findsOneWidget);
    expect(find.text('موقع الحديقة'), findsOneWidget);
    expect(find.text('حديقة حيوان طرابلس، طرابلس، ليبيا'), findsOneWidget);
    expect(find.text('فتح في خرائط Google'), findsOneWidget);

    await scrollVisitInfo(tester, -250);

    expect(find.text('أسعار تذاكر الدخول'), findsOneWidget);
    expect(find.text('مواطنون'), findsOneWidget);
    expect(find.text('أجانب'), findsOneWidget);
    expect(find.text('10 د.ل'), findsOneWidget);
    expect(find.text('5 د.ل'), findsAtLeastNWidgets(2));
    expect(find.text('12 سنة فأكثر'), findsOneWidget);
    expect(find.text('أرقام الطوارئ'), findsNothing);

    await scrollVisitInfo(tester, -200);

    expect(find.text('تعليمات وإرشادات الزيارة'), findsOneWidget);
  });
}
