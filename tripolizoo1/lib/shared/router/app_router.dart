import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/animals_explore_screen.dart';
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
      final isLoggedIn = authProvider.isAuthenticated;
      final isSplash = state.matchedLocation == '/splash';
      final isAuthRoute = state.matchedLocation.startsWith('/login') ||
          state.matchedLocation.startsWith('/register') ||
          state.matchedLocation.startsWith('/forgot-password') ||
          state.matchedLocation.startsWith('/otp') ||
          state.matchedLocation.startsWith('/reset-password');

      if (isSplash) {
        return null; // Let splash screen handle its logic
      }

      if (!isLoggedIn && !isAuthRoute) {
        return '/login';
      }

      if (isLoggedIn && isAuthRoute) {
        final role = authProvider.user?.role ?? 'visitor';
        if (role == 'doctor') return '/doctor';
        if (role == 'supervisor') return '/supervisor/home';
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
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: '/register',
        builder: (context, state) => const RegisterScreen(),
      ),
      GoRoute(
        path: '/forgot-password',
        builder: (context, state) => const ForgotPasswordScreen(),
      ),
      GoRoute(
        path: '/otp',
        builder: (context, state) => const OtpVerificationScreen(),
      ),
      GoRoute(
        path: '/reset-password',
        builder: (context, state) => const ResetPasswordScreen(),
      ),
      GoRoute(
        path: '/doctor',
        redirect: (context, state) => '/doctor/home',
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
                builder: (context, state) => const InteractiveMapScreen(),
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
