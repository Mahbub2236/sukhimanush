class UserModel {
  final int id;
  final String name;
  final String email;
  final String role;
  final bool isApproved;
  final String? phone;
  final String? address;
  final String? avatar;
  final String? pharmacyName;
  final double? agentCommissionBalance;
  final int? assistantId;
  final int? doctorId;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    required this.isApproved,
    this.phone,
    this.address,
    this.avatar,
    this.pharmacyName,
    this.agentCommissionBalance,
    this.assistantId,
    this.doctorId,
  });

  bool get isDoctor => role == 'doctor';
  bool get isPatient => role == 'patient';
  bool get isStaff => role == 'staff';
  bool get isAgent => role == 'agent';
  bool get isAdmin => role == 'admin' || role == 'super_admin';

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'patient',
      isApproved: json['is_approved'] == true || json['is_approved'] == 1,
      phone: json['phone'],
      address: json['address'],
      avatar: json['avatar'],
      pharmacyName: json['pharmacy_name'],
      agentCommissionBalance: json['agent_commission_balance'] != null
          ? double.tryParse(json['agent_commission_balance'].toString())
          : null,
      assistantId: json['assistant_id'] ?? json['assistant']?['id'],
      doctorId: json['doctor_id'] ?? json['assistant']?['doctor_id'] ?? json['doctor']?['id'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'is_approved': isApproved,
      'phone': phone,
      'address': address,
      'avatar': avatar,
      'pharmacy_name': pharmacyName,
      'agent_commission_balance': agentCommissionBalance,
      'assistant_id': assistantId,
      'doctor_id': doctorId,
    };
  }
}
