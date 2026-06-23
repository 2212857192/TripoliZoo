import 'package:firebase_core/firebase_core.dart' show FirebaseOptions;
import 'package:flutter/foundation.dart'
    show defaultTargetPlatform, kIsWeb, TargetPlatform;

/// Firebase options generated from google-services.json / GoogleService-Info.plist.
class DefaultFirebaseOptions {
  static FirebaseOptions get currentPlatform {
    if (kIsWeb) {
      throw UnsupportedError('Firebase is not configured for web.');
    }

    return switch (defaultTargetPlatform) {
      TargetPlatform.android => android,
      TargetPlatform.iOS => ios,
      _ => throw UnsupportedError(
          'Firebase is not supported on $defaultTargetPlatform.',
        ),
    };
  }

  static const FirebaseOptions android = FirebaseOptions(
    apiKey: 'AIzaSyC_FSXRfZgftqEGDqrwYXfa8DjJRfGagMU',
    appId: '1:462792145653:android:e4eb6ea13fb9678a4de3bc',
    messagingSenderId: '462792145653',
    projectId: 'tripolizoo',
    storageBucket: 'tripolizoo.firebasestorage.app',
  );

  static const FirebaseOptions ios = FirebaseOptions(
    apiKey: 'AIzaSyD661jR6Ug0OSZE4iMYvgHbjnmt4LXvFkw',
    appId: '1:462792145653:ios:1a8485fcbe76bc7e4de3bc',
    messagingSenderId: '462792145653',
    projectId: 'tripolizoo',
    storageBucket: 'tripolizoo.firebasestorage.app',
    iosBundleId: 'com.amnaalogab.tripolizoo',
  );
}
