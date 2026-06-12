import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:provider/provider.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/shared/router/app_router.dart';
import 'package:tripolizoo/shared/theme/app_theme.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/follow_up_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_reports_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/presentation/supervisor_notifications_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/quarantine_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/receiving_tasks_provider.dart';

class TripoliZooApp extends StatefulWidget {
  const TripoliZooApp({super.key});

  @override
  State<TripoliZooApp> createState() => _TripoliZooAppState();
}

class _TripoliZooAppState extends State<TripoliZooApp> {
  late final AuthProvider _authProvider;
  late final GoRouter _router;

  @override
  void initState() {
    super.initState();
    _authProvider = AuthProvider();
    _router = createRouter(_authProvider);
  }

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider.value(value: _authProvider),
        ChangeNotifierProvider(create: (_) => LocaleProvider()),
        ChangeNotifierProvider(create: (_) => TicketCartProvider()),
        ChangeNotifierProvider(create: (_) => FollowUpProvider()),
        ChangeNotifierProvider(create: (_) => HealthReportsProvider()),
        ChangeNotifierProvider(create: (_) => ReceivingTasksProvider()),
        ChangeNotifierProvider(create: (_) => SupervisorNotificationsProvider()),
        ChangeNotifierProvider(create: (_) => MedicalCasesProvider()),
        ChangeNotifierProvider(create: (_) => QuarantineProvider()),
      ],
      child: Consumer<LocaleProvider>(
        builder: (context, localeProvider, child) {
          Locale appLocale;
          switch (localeProvider.locale) {
            case AppLocale.ar:
              appLocale = const Locale('ar');
              break;
            case AppLocale.en:
              appLocale = const Locale('en');
              break;
            case AppLocale.it:
              appLocale = const Locale('it');
              break;
          }

          return MaterialApp.router(
            debugShowCheckedModeBanner: false,
            title: 'Tripoli Zoo',
            theme: AppTheme.light,
            routerConfig: _router,
            locale: appLocale,
            supportedLocales: const [
              Locale('ar'),
              Locale('en'),
              Locale('it'),
            ],
            localizationsDelegates: const [
              GlobalMaterialLocalizations.delegate,
              GlobalWidgetsLocalizations.delegate,
              GlobalCupertinoLocalizations.delegate,
            ],
            builder: (context, child) {
              final isRtl = localeProvider.locale == AppLocale.ar;
              return Directionality(
                textDirection: isRtl ? TextDirection.rtl : TextDirection.ltr,
                child: child ?? const SizedBox.shrink(),
              );
            },
          );
        },
      ),
    );
  }
}
