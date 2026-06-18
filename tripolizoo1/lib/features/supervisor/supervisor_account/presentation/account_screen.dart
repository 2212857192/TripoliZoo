import 'package:flutter/material.dart';
import 'package:tripolizoo/shared/widgets/portal_account_screen.dart';

class SupervisorAccountScreen extends StatelessWidget {
  const SupervisorAccountScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return const PortalAccountScreen(roleLabel: 'مشرف المجموعة');
  }
}
