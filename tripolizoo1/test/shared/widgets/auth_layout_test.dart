import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/shared/widgets/auth_layout.dart';

void main() {
  testWidgets('back button returns to login when no page can be popped',
      (tester) async {
    final router = GoRouter(
      initialLocation: '/register',
      routes: [
        GoRoute(
          path: '/login',
          builder: (_, __) => const Scaffold(body: Text('Login screen')),
        ),
        GoRoute(
          path: '/register',
          builder: (_, __) => const AuthLayout(
            title: 'إنشاء حساب',
            heroTag: 'REGISTER',
            showBackButton: true,
            child: SizedBox.shrink(),
          ),
        ),
      ],
    );
    addTearDown(router.dispose);

    await tester.pumpWidget(MaterialApp.router(routerConfig: router));
    await tester.pumpAndSettle();

    await tester.tap(find.byIcon(Icons.arrow_forward_ios_rounded));
    await tester.pumpAndSettle();

    expect(find.text('Login screen'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });
}
