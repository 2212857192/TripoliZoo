import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';

class SupervisorPlaceholderScreen extends StatelessWidget {
  const SupervisorPlaceholderScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('قطاع المشرف'),
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
          'قريباً: واجهة المشرف',
          style: TextStyle(fontSize: 24),
        ),
      ),
    );
  }
}
