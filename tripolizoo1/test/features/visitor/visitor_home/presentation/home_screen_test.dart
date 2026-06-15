import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/visitor/visitor_home/presentation/home_screen.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';

void main() {
  Widget buildHome(GoRouter router) {
    return ChangeNotifierProvider(
      create: (_) => LocaleProvider(),
      child: MaterialApp.router(routerConfig: router),
    );
  }

  GoRouter createTestRouter() {
    Widget page(String title) => Scaffold(body: Center(child: Text(title)));

    return GoRouter(
      initialLocation: '/home',
      routes: [
        GoRoute(path: '/home', builder: (_, __) => const HomeScreen()),
        GoRoute(
          path: '/qr-scanner',
          builder: (_, __) => page('QR destination'),
        ),
        GoRoute(
          path: '/virtual-tour',
          builder: (_, __) => page('Tour destination'),
        ),
        GoRoute(path: '/map', builder: (_, __) => page('Map destination')),
        GoRoute(
          path: '/visit-info',
          builder: (_, __) => page('Visit destination'),
        ),
        GoRoute(
          path: '/animals',
          builder: (_, __) => page('Animals destination'),
        ),
      ],
    );
  }

  final cardCases = {
    'ماسح QR': 'QR destination',
    'جولة افتراضية': 'Tour destination',
    'خريطة تفاعلية': 'Map destination',
    'معلومات الزوار': 'Visit destination',
  };

  testWidgets('does not show the open today badge', (tester) async {
    final router = createTestRouter();
    await tester.pumpWidget(buildHome(router));
    await tester.pumpAndSettle();

    expect(find.text('مفتوح اليوم'), findsNothing);
    router.dispose();
  });

  testWidgets('hero image and cards scroll as page content', (tester) async {
    final router = createTestRouter();
    await tester.pumpWidget(buildHome(router));
    await tester.pumpAndSettle();

    expect(find.byType(SliverAppBar), findsNothing);
    final card = find.text('معلومات الزوار');
    final initialTop = tester.getTopLeft(card).dy;

    await tester.drag(find.byType(CustomScrollView), const Offset(0, -300));
    await tester.pumpAndSettle();

    expect(tester.getTopLeft(card).dy, lessThan(initialTop));
    router.dispose();
  });

  for (final entry in cardCases.entries) {
    testWidgets('${entry.key} card opens its destination', (tester) async {
      final router = createTestRouter();
      await tester.pumpWidget(buildHome(router));
      await tester.pumpAndSettle();

      final card = find.text(entry.key);
      expect(card, findsOneWidget);
      await tester.tap(card);
      await tester.pumpAndSettle();

      expect(find.text(entry.value), findsOneWidget);
      router.dispose();
    });
  }

  testWidgets('animal card opens the selected animal details', (tester) async {
    final router = createTestRouter();
    await tester.pumpWidget(buildHome(router));
    await tester.pumpAndSettle();

    final lionCard = find.text('الأسد الأفريقي');
    await tester.drag(
      find.byType(CustomScrollView),
      const Offset(0, -650),
    );
    await tester.pumpAndSettle();
    await tester.tap(lionCard);
    await tester.pumpAndSettle();

    expect(find.text('Panthera leo'), findsOneWidget);
    router.dispose();
  });
}
