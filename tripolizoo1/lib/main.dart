import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/app.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/visitor_gps_platform.dart';
import 'package:tripolizoo/shared/push/push_notification_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  GoogleFonts.config.allowRuntimeFetching = false;
  await configureVisitorGpsReader();
  await PushNotificationService.initialize();
  runApp(const TripoliZooApp());
}
