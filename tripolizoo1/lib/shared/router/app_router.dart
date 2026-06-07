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
import 'package:tripolizoo/features/doctor/presentation/doctor_placeholder_screen.dart';
import 'package:tripolizoo/features/supervisor/presentation/supervisor_placeholder_screen.dart';

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
        if (role == 'supervisor') return '/supervisor';
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
        builder: (context, state) => const DoctorPlaceholderScreen(),
      ),
      GoRoute(
        path: '/supervisor',
        builder: (context, state) => const SupervisorPlaceholderScreen(),
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
    ],
  );
}
