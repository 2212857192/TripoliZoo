// ignore_for_file: avoid_web_libraries_in_flutter

import 'dart:html' as html;

Future<void> requestBrowserNotificationPermission() async {
  if (!html.Notification.supported) return;

  final permission = html.Notification.permission;
  if (permission == 'default') {
    await html.Notification.requestPermission();
  }
}

void showBrowserNotification({required String title, String? body}) {
  if (!html.Notification.supported) return;
  if (html.Notification.permission != 'granted') return;

  html.Notification(title, body: body);
}
