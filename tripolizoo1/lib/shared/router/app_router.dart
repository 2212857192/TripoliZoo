import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/animal_detail_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/animals_explore_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/animal_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/forgot_password_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/login_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/otp_verification_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/register_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/reset_password_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_home/presentation/home_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/interactive_map_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_profile/presentation/profile_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/qr_scanner_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_splash/presentation/splash_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/tickets_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/virtual_tour_screen.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/visit_info_screen.dart';
import 'package:tripolizoo/shared/widgets/main_shell.dart';
import 'package:tripolizoo/features/doctor/doctor_account/presentation/doctor_account_screen.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/doctor_cases_screen.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_case_detail_screen.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/doctor_quarantine_screen.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/quarantine_detail_screen.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/presentation/doctor_notifications_screen.dart';
import 'package:tripolizoo/features/doctor/presentation/doctor_shell.dart';
import 'package:tripolizoo/features/doctor/presentation/doctor_home_screen.dart';
import 'package:tripolizoo/features/doctor/presentation/doctor_reports_screen.dart';
import 'package:tripolizoo/features/supervisor/supervisor_account/presentation/account_screen.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/group_followup_screen.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_reports_screen.dart';
import 'package:tripolizoo/features/supervisor/supervisor_home/presentation/supervisor_home_screen.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/presentation/supervisor_notifications_screen.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/receiving_tasks_screen.dart';
import 'package:tripolizoo/features/supervisor/supervisor_shell/presentation/supervisor_shell.dart';

final rootNavigatorKey = GlobalKey<NavigatorState>();

GoRouter createRouter(AuthProvider authProvider) {
  return GoRouter(
    navigatorKey: rootNavigatorKey,
    initialLocation: '/splash',
    refreshListenable: authProvider,
    redirect: (context, state) {
      final hasSession = authProvider.hasSession;
      final isLoggedIn = authProvider.isAuthenticated;
      final isSplash = state.matchedLocation == '/splash';
      final isAuthRoute = state.matchedLocation.startsWith('/login') ||
          state.matchedLocation.startsWith('/register') ||
          state.matchedLocation.startsWith('/forgot-password') ||
          state.matchedLocation.startsWith('/otp') ||
          state.matchedLocation.startsWith('/reset-password');

      if (isSplash) {
        return null;
      }

      if (!authProvider.bootstrapped) {
        return null;
      }

      if (!hasSession && !isAuthRoute) {
        return '/login';
      }

      if (hasSession && state.matchedLocation.startsWith('/tickets')) {
        final role = authProvider.user?.role;
        if (role == 'supervisor') return '/supervisor/home';
        if (role == 'doctor') return '/doctor/home';
      }

      final role = authProvider.user?.role;
      final path = state.matchedLocation;

      if (hasSession && path.startsWith('/doctor') && role != 'doctor') {
        if (role == 'supervisor') return '/supervisor/home';
        return '/home';
      }

      if (hasSession && path.startsWith('/supervisor') && role != 'supervisor') {
        if (role == 'doctor') return '/doctor/home';
        return '/home';
      }

      if (isLoggedIn && isAuthRoute) {
        final loggedInRole = authProvider.user?.role ?? 'visitor';
        if (loggedInRole == 'doctor') return '/doctor';
        if (loggedInRole == 'supervisor') return '/supervisor/home';
        return '/home'; // Default visitor
      }

      return null;
    },
    routes: [
      GoRoute(
        path: '/splash',
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(
        path: '/login',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: '/register',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const RegisterScreen(),
      ),
      GoRoute(
        path: '/forgot-password',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const ForgotPasswordScreen(),
      ),
      GoRoute(
        path: '/otp',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const OtpVerificationScreen(),
      ),
      GoRoute(
        path: '/reset-password',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const ResetPasswordScreen(),
      ),
      GoRoute(
        path: '/doctor',
        redirect: (context, state) {
          final path = state.uri.path;
          if (path == '/doctor' || path == '/doctor/') {
            return '/doctor/home';
          }
          return null;
        },
        routes: [
          GoRoute(
            path: 'notifications',
            parentNavigatorKey: rootNavigatorKey,
            builder: (context, state) => const DoctorNotificationsScreen(),
          ),
        ],
      ),
      StatefulShellRoute.indexedStack(
        builder: (context, state, navigationShell) =>
            DoctorShell(navigationShell: navigationShell),
        branches: [
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/doctor/home',
                builder: (context, state) => const DoctorHomeScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/doctor/reports',
                builder: (context, state) => const DoctorReportsScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/doctor/cases',
                builder: (context, state) => const DoctorCasesScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/doctor/quarantine',
                builder: (context, state) => const DoctorQuarantineScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/doctor/account',
                builder: (context, state) => const DoctorAccountScreen(),
              ),
            ],
          ),
        ],
      ),
      GoRoute(
        path: '/supervisor',
        redirect: (context, state) => '/supervisor/home',
      ),
      StatefulShellRoute.indexedStack(
        builder: (context, state, navigationShell) =>
            SupervisorShell(navigationShell: navigationShell),
        branches: [
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/supervisor/home',
                builder: (context, state) => const SupervisorHomeScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/supervisor/health-reports',
                builder: (context, state) => const HealthReportsScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/supervisor/group-followup',
                builder: (context, state) => const GroupFollowupScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/supervisor/receiving-tasks',
                builder: (context, state) => ReceivingTasksScreen(
                  initialFilterQuery: state.uri.queryParameters['filter'],
                ),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/supervisor/account',
                builder: (context, state) => const SupervisorAccountScreen(),
              ),
            ],
          ),
        ],
      ),
      StatefulShellRoute.indexedStack(
        builder: (context, state, navigationShell) =>
            MainShell(navigationShell: navigationShell),
        branches: [
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/home',
                builder: (context, state) => const HomeScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/map',
                builder: (context, state) => InteractiveMapScreen(
                  focusLocationId:
                      int.tryParse(state.uri.queryParameters['focus'] ?? ''),
                  autoNavigate: state.uri.queryParameters['navigate'] == '1',
                ),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/tickets',
                builder: (context, state) => const TicketsScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/profile',
                builder: (context, state) => const ProfileScreen(),
              ),
            ],
          ),
        ],
      ),
      GoRoute(
        path: '/visit-info',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const VisitInfoScreen(),
      ),
      GoRoute(
        path: '/animals',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const AnimalsExploreScreen(),
        routes: [
          GoRoute(
            path: ':id',
            parentNavigatorKey: rootNavigatorKey,
            pageBuilder: (context, state) => MaterialPage<void>(
              key: state.pageKey,
              child: _AnimalProfileLoader(
                identifier: state.pathParameters['id'] ?? '',
              ),
            ),
          ),
        ],
      ),
      GoRoute(
        path: '/qr-scanner',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const QrScannerScreen(),
      ),
      GoRoute(
        path: '/virtual-tour',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const VirtualTourScreen(),
      ),
      GoRoute(
        path: '/supervisor/notifications',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => const SupervisorNotificationsScreen(),
      ),
      GoRoute(
        path: '/doctor/cases/:id',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => MedicalCaseDetailScreen(
          caseId: state.pathParameters['id']!,
        ),
      ),
      GoRoute(
        path: '/doctor/quarantine/:id',
        parentNavigatorKey: rootNavigatorKey,
        builder: (context, state) => QuarantineDetailScreen(
          recordId: state.pathParameters['id']!,
        ),
      ),
    ],
  );
}

/// Loads an animal by profile ID/code and shows [AnimalDetailScreen].
class _AnimalProfileLoader extends StatefulWidget {
  const _AnimalProfileLoader({required this.identifier});
  final String identifier;

  @override
  State<_AnimalProfileLoader> createState() => _AnimalProfileLoaderState();
}

class _AnimalProfileLoaderState extends State<_AnimalProfileLoader> {
  final _repo = ApiAnimalRepository();
  late Future<dynamic> _future;

  @override
  void initState() {
    super.initState();
    _future = _repo.getByQrCode(widget.identifier);
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder(
      future: _future,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Scaffold(
            body: Center(
              child: CircularProgressIndicator(color: Color(0xFF1B4332)),
            ),
          );
        }

        if (snapshot.data != null) {
          return AnimalDetailScreen(animal: snapshot.data!);
        }

        return Scaffold(
          appBar: AppBar(title: const Text('تفاصيل الحيوان')),
          body: Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.pets_rounded, size: 48, color: Colors.grey),
                const SizedBox(height: 12),
                const Text('لم يتم العثور على الحيوان'),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: () =>
                      Navigator.of(context).canPop()
                          ? Navigator.of(context).pop()
                          : null,
                  child: const Text('رجوع'),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
