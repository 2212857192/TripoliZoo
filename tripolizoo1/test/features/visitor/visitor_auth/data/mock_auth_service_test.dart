import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/mock_auth_service.dart';

void main() {
  group('MockAuthService.login', () {
    final service = MockAuthService();

    test('assigns the supervisor role to both demo supervisor emails',
        () async {
      for (final email in [
        'supervisor@tripolizoo.ly',
        'm.supervisor@tripoli-zoo.ly',
      ]) {
        final user = await service.login(email: email, password: '123456');

        expect(user.role, 'supervisor');
      }
    });

    test('assigns the doctor role to both demo doctor emails', () async {
      for (final email in [
        'doctor@tripolizoo.ly',
        'a.kabti@tripolizoo.ly',
      ]) {
        final user = await service.login(email: email, password: '123456');

        expect(user.role, 'doctor');
      }
    });

    test('normalizes letter case before assigning the role', () async {
      final user = await service.login(
        email: 'SUPERVISOR@TRIPOLIZOO.LY',
        password: '123456',
      );

      expect(user.role, 'supervisor');
    });
  });
}
