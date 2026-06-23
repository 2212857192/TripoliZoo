import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/app.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/visitor_gps_platform.dart';
import 'package:tripolizoo/shared/api/api_runtime_config.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';
import 'package:tripolizoo/shared/push/push_notification_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  GoogleFonts.config.allowRuntimeFetching = false;
  await ApiRuntimeConfig.load();
  await configureVisitorGpsReader();
  await PushNotificationService.initialize();
  final localeProvider = LocaleProvider();
  await localeProvider.loadSaved();
  runApp(TripoliZooApp(localeProvider: localeProvider));
}
