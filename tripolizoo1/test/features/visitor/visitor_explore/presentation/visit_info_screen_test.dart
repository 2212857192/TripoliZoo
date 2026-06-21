import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/visit_info_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/visit_info_screen.dart';

void main() {
  Widget buildVisitInfo() {
    final router = GoRouter(
      initialLocation: '/visit-info',
      routes: [
        GoRoute(
          path: '/visit-info',
          builder: (_, __) => VisitInfoScreen(
            repository: MockVisitInfoRepository(),
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

  testWidgets('shows visit information from backend without tickets or map',
      (tester) async {
    await tester.pumpWidget(buildVisitInfo());
    await tester.pumpAndSettle();

    expect(find.text('حالة التشغيل'), findsOneWidget);
    expect(find.text('مفتوحة — أهلاً بزوارنا'), findsOneWidget);
    expect(find.text('ساعات العمل'), findsOneWidget);
    expect(find.text('آخر موعد للدخول'), findsOneWidget);
    expect(find.text('مفتوحة يومياً'), findsOneWidget);
    expect(find.text('أرقام الطوارئ'), findsOneWidget);

    await tester.drag(
      find.byType(CustomScrollView),
      const Offset(0, -300),
    );
    await tester.pumpAndSettle();

    expect(find.text('تعليمات وإرشادات الزيارة'), findsOneWidget);

    expect(find.text('موقع الحديقة'), findsNothing);
    expect(find.text('أسعار تذاكر الدخول'), findsNothing);
    expect(find.text('التذكرة الإلكترونية'), findsNothing);
    expect(find.text('شراء تذكرة'), findsNothing);
    expect(find.text('فتح في خرائط Google'), findsNothing);
  });
}
