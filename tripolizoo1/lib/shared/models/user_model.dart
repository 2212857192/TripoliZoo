class UserModel {
  final String id;
  final String name;
  final String email;
  final String phone;
  final bool isGuest;
  final String role; // visitor, doctor, supervisor
  final String? assignedGroup;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    this.isGuest = false,
    this.role = 'visitor',
    this.assignedGroup,
  });

  factory UserModel.guest() => const UserModel(
        id: 'guest',
        name: 'زائر الحديقة',
        email: 'guest@tripolizoo.ly',
        phone: '+218 91 000 0000',
        isGuest: true,
        role: 'visitor',
      );

  factory UserModel.fromJson(Map<String, dynamic> json) => UserModel(
        id: json['id'] as String,
        name: json['name'] as String,
        email: json['email'] as String,
        phone: json['phone'] as String? ?? '',
        isGuest: json['is_guest'] as bool? ?? false,
        role: json['role'] as String? ?? 'visitor',
        assignedGroup: json['assigned_group'] as String?,
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'email': email,
        'phone': phone,
        'is_guest': isGuest,
        'role': role,
        'assigned_group': assignedGroup,
      };

  UserModel copyWith({
    String? name,
    String? email,
    String? phone,
    String? role,
    String? assignedGroup,
  }) =>
      UserModel(
        id: id,
        name: name ?? this.name,
        email: email ?? this.email,
        phone: phone ?? this.phone,
        isGuest: isGuest,
        role: role ?? this.role,
        assignedGroup: assignedGroup ?? this.assignedGroup,
      );
}
