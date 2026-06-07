import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';

class DoctorPlaceholderScreen extends StatelessWidget {
  const DoctorPlaceholderScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('قطاع الطبيب'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () {
              context.read<AuthProvider>().logout();
            },
          ),
        ],
      ),
      body: const Center(
        child: Text(
          'قريباً: واجهة الطبيب',
          style: TextStyle(fontSize: 24),
        ),
      ),
    );
  }
}
