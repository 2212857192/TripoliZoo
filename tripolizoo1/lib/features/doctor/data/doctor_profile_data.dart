class DoctorProfileData {
  const DoctorProfileData({
    required this.fullName,
    required this.displayTitle,
    required this.workplace,
    required this.employeeNumber,
    required this.department,
    required this.area,
    required this.phone,
    required this.email,
    required this.appointmentDate,
    required this.role,
  });

  final String fullName;
  final String displayTitle;
  final String workplace;
  final String employeeNumber;
  final String department;
  final String area;
  final String phone;
  final String email;
  final String appointmentDate;
  final String role;

  static const mock = DoctorProfileData(
    fullName: 'أحمد علي محمد الكبتي',
    displayTitle: 'طبيب بيطري - حديقة الحيوان طرابلس',
    workplace: 'حديقة الحيوان طرابلس',
    employeeNumber: 'VET-2041',
    department: 'الطب البيطري',
    area: 'قسم الحيوانات البرية',
    phone: '092-1234567',
    email: 'a.kabti@tripolizoo.ly',
    appointmentDate: '2021-03-15',
    role: 'طبيب بيطري تنفيذي',
  );
}
