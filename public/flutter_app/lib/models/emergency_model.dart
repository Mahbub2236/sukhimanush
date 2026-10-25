import 'doctor_model.dart';

class AiSymptomResultModel {
  final bool isEmergency;
  final String specialtyBn;
  final String specialtyEn;
  final String advice;
  final List<String> matchedSymptoms;
  final List<String> emergencyWarnings;
  final List<String> suggestedQuestions;
  final List<DoctorModel> recommendedDoctors;

  AiSymptomResultModel({
    required this.isEmergency,
    required this.specialtyBn,
    required this.specialtyEn,
    required this.advice,
    required this.matchedSymptoms,
    required this.emergencyWarnings,
    required this.suggestedQuestions,
    required this.recommendedDoctors,
  });

  factory AiSymptomResultModel.fromJson(Map<String, dynamic> json) {
    var rawDocs = json['recommended_doctors'] as List? ?? [];
    List<DoctorModel> docs =
        rawDocs.map((d) => DoctorModel.fromJson(Map<String, dynamic>.from(d))).toList();

    return AiSymptomResultModel(
      isEmergency: json['is_emergency'] == true || json['is_emergency'] == 1,
      specialtyBn: json['specialty_bn'] ?? json['department_bn'] ?? '',
      specialtyEn: json['specialty_en'] ?? json['department_en'] ?? '',
      advice: json['advice'] ?? '',
      matchedSymptoms: (json['matched_symptoms'] as List?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      emergencyWarnings: (json['emergency_warnings'] as List?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      suggestedQuestions: (json['suggested_questions'] as List?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      recommendedDoctors: docs,
    );
  }
}

class AmbulanceModel {
  final int id;
  final String serviceName;
  final String driverName;
  final String phone;
  final String vehicleType; // ac, non_ac, icu, freezer, cardiac
  final String district;
  final String? division;
  final String? upazila;
  final String? hospitalBase;
  final bool isAvailable;
  final String? distanceText;
  final String? timeText;

  AmbulanceModel({
    required this.id,
    required this.serviceName,
    required this.driverName,
    required this.phone,
    required this.vehicleType,
    required this.district,
    this.division,
    this.upazila,
    this.hospitalBase,
    required this.isAvailable,
    this.distanceText,
    this.timeText,
  });

  factory AmbulanceModel.fromJson(Map<String, dynamic> json) {
    return AmbulanceModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      serviceName: json['service_name']?.toString() ?? json['name']?.toString() ?? 'Ambulance Service',
      driverName: json['driver_or_contact_name']?.toString() ?? json['driver_name']?.toString() ?? 'Driver',
      phone: json['phone_primary']?.toString() ?? json['phone']?.toString() ?? '',
      vehicleType: json['vehicle_type']?.toString() ?? 'ac',
      district: json['district']?.toString() ?? '',
      division: json['division']?.toString(),
      upazila: json['upazila']?.toString(),
      hospitalBase: json['hospital_base']?.toString(),
      isAvailable: json['is_available_24_7'] == true ||
          json['is_available_24_7'] == 1 ||
          json['is_available'] == true ||
          json['is_available'] == 1,
      distanceText: json['distance_text']?.toString(),
      timeText: json['time_text']?.toString(),
    );
  }
}

class HospitalBedModel {
  final int id;
  final String hospitalName;
  final String district;
  final String? address;
  final String? emergencyPhone;
  final int icuAvailable;
  final int icuTotal;
  final int nicuAvailable;
  final int ccuAvailable;
  final String? lastVerifiedAt;

  HospitalBedModel({
    required this.id,
    required this.hospitalName,
    required this.district,
    this.address,
    this.emergencyPhone,
    required this.icuAvailable,
    required this.icuTotal,
    required this.nicuAvailable,
    required this.ccuAvailable,
    this.lastVerifiedAt,
  });

  factory HospitalBedModel.fromJson(Map<String, dynamic> json) {
    return HospitalBedModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      hospitalName: json['hospital_name']?.toString() ?? json['name']?.toString() ?? 'Hospital',
      district: json['district']?.toString() ?? '',
      address: json['address']?.toString(),
      emergencyPhone: json['emergency_phone']?.toString() ?? json['phone']?.toString(),
      icuAvailable: int.tryParse(json['icu_available'].toString()) ?? 0,
      icuTotal: int.tryParse(json['icu_total'].toString()) ?? 0,
      nicuAvailable: int.tryParse(json['nicu_available'].toString()) ?? 0,
      ccuAvailable: int.tryParse(json['ccu_available'].toString()) ?? 0,
      lastVerifiedAt: json['last_verified_at']?.toString() ?? json['updated_at']?.toString(),
    );
  }
}

class BloodRequestModel {
  final int id;
  final String requestCode;
  final String bloodGroup; // A+, B+, O+, AB+, etc.
  final String component; // whole_blood, platelets, plasma, rbc
  final String patientName;
  final String hospitalName;
  final String district;
  final String? upazila;
  final int unitsNeeded;
  final String contactName;
  final String contactPhone;
  final String urgency; // critical_immediate, within_12h, within_24h, scheduled
  final String? reason;
  final String status; // open, fulfilled, closed
  final String? createdAt;

  BloodRequestModel({
    required this.id,
    required this.requestCode,
    required this.bloodGroup,
    required this.component,
    required this.patientName,
    required this.hospitalName,
    required this.district,
    this.upazila,
    required this.unitsNeeded,
    required this.contactName,
    required this.contactPhone,
    required this.urgency,
    this.reason,
    required this.status,
    this.createdAt,
  });

  factory BloodRequestModel.fromJson(Map<String, dynamic> json) {
    return BloodRequestModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      requestCode: json['request_code']?.toString() ?? 'SOS-${json['id']}',
      bloodGroup: json['blood_group']?.toString() ?? 'O+',
      component: json['component']?.toString() ?? 'whole_blood',
      patientName: json['patient_name']?.toString() ?? 'Patient',
      hospitalName: json['hospital_name']?.toString() ?? '',
      district: json['district']?.toString() ?? '',
      upazila: json['upazila']?.toString(),
      unitsNeeded: int.tryParse(json['units_needed'].toString()) ?? 1,
      contactName: json['contact_name']?.toString() ?? 'Relative',
      contactPhone: json['contact_phone']?.toString() ?? json['phone']?.toString() ?? '',
      urgency: json['urgency']?.toString() ?? 'critical_immediate',
      reason: json['reason']?.toString(),
      status: json['status']?.toString() ?? 'open',
      createdAt: json['created_at']?.toString(),
    );
  }
}

class BloodDonorModel {
  final int id;
  final String name;
  final String bloodGroup;
  final String district;
  final String? upazila;
  final String phone;
  final String? lastDonatedDate;
  final bool isAvailable;
  final int totalDonations;
  final String? badge;

  BloodDonorModel({
    required this.id,
    required this.name,
    required this.bloodGroup,
    required this.district,
    this.upazila,
    required this.phone,
    this.lastDonatedDate,
    required this.isAvailable,
    required this.totalDonations,
    this.badge,
  });

  factory BloodDonorModel.fromJson(Map<String, dynamic> json) {
    return BloodDonorModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      name: json['name']?.toString() ?? 'Anonymous Donor',
      bloodGroup: json['blood_group']?.toString() ?? 'O+',
      district: json['district']?.toString() ?? '',
      upazila: json['upazila']?.toString(),
      phone: json['phone']?.toString() ?? '',
      lastDonatedDate: json['last_donated_date']?.toString(),
      isAvailable: json['is_available'] == true || json['is_available'] == 1,
      totalDonations: int.tryParse(json['total_donations'].toString()) ?? 0,
      badge: json['badge']?.toString() ?? 'life_saver',
    );
  }
}
