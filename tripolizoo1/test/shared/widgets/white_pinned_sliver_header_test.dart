import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/shared/widgets/white_pinned_sliver_header.dart';

void main() {
  testWidgets('keeps the original white header content pinned after scrolling',
      (tester) async {
    await tester.pumpWidget(
      MaterialApp(
        home: Scaffold(
          body: CustomScrollView(
            slivers: [
              WhitePinnedSliverHeader(
                toolbarHeight: 84,
                child: Column(
                  children: const [
                    Text('ACCOUNT'),
                    Text('حسابي'),
                  ],
                ),
              ),
              SliverList.builder(
                itemCount: 30,
                itemBuilder: (_, __) => const SizedBox(height: 80),
              ),
            ],
          ),
        ),
      ),
    );

    expect(find.text('ACCOUNT'), findsOneWidget);
    expect(find.text('حسابي'), findsOneWidget);

    await tester.drag(find.byType(CustomScrollView), const Offset(0, -300));
    await tester.pumpAndSettle();

    expect(find.text('ACCOUNT'), findsOneWidget);
    expect(find.text('حسابي'), findsOneWidget);

    final appBar = tester.widget<SliverAppBar>(find.byType(SliverAppBar));
    expect(appBar.pinned, isTrue);
    expect(appBar.backgroundColor, Colors.white);
  });
}
