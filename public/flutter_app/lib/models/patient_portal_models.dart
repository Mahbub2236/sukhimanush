class HealthMetricModel {
  final int id;
  final String metricType; // blood_sugar, blood_pressure, weight, pulse, temperature
  final double valuePrimary;
  final double? valueSecondary;
  final String? measuredTime;
  final String recordedDate;
  final String? notes;
  final int? familyMemberId;

  HealthMetricModel({
    required this.id,
    required this.metricType,
    required this.valuePrimary,
    this.valueSecondary,
    this.measuredTime,
    required this.recordedDate,
    this.notes,
    this.familyMemberId,
  });

  factory HealthMetricModel.fromJson(Map<String, dynamic> json) {
    return HealthMetricModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      metricType: json['metric_type']?.toString() ?? 'blood_sugar',
      valuePrimary: double.tryParse(json['value_primary'].toString()) ?? 0.0,
      valueSecondary: json['value_secondary'] != null
          ? double.tryParse(json['value_secondary'].toString())
          : null,
      measuredTime: json['measured_time']?.toString(),
      recordedDate: json['recorded_date']?.toString() ?? '',
      notes: json['notes']?.toString(),
      familyMemberId: json['family_member_id'] != null
          ? int.tryParse(json['family_member_id'].toString())
          : null,
    );
  }
}

class PrescriptionModel {
  final int id;
  final String? doctorName;
  final String? doctorSpecialty;
  final String? appointmentNumber;
  final String? diagnosis;
  final List<dynamic> medicines;
  final String? instructions;
  final String? audioUrl;
  final String createdAt;

  PrescriptionModel({
    required this.id,
    this.doctorName,
    this.doctorSpecialty,
    this.appointmentNumber,
    this.diagnosis,
    required this.medicines,
    this.instructions,
    this.audioUrl,
    required this.createdAt,
  });

  factory PrescriptionModel.fromJson(Map<String, dynamic> json) {
    String? dName;
    String? dSpec;
    if (json['doctor'] != null && json['doctor'] is Map) {
      dName = json['doctor']['name']?.toString();
      dSpec = json['doctor']['specialization']?.toString();
    } else if (json['appointment'] != null &&
        json['appointment'] is Map &&
        json['appointment']['doctor'] != null) {
      dName = json['appointment']['doctor']['name']?.toString();
      dSpec = json['appointment']['doctor']['specialization']?.toString();
    }

    String? apptNo;
    if (json['appointment'] != null && json['appointment'] is Map) {
      apptNo = json['appointment']['appointment_number']?.toString();
    }

    var meds = json['medicines'];
    List<dynamic> medList = [];
    if (meds is List) {
      medList = meds;
    } else if (json['medicines_json'] is List) {
      medList = json['medicines_json'];
    }

    return PrescriptionModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      doctorName: dName ?? 'Dr. Specialist',
      doctorSpecialty: dSpec ?? 'Medicine',
      appointmentNumber: apptNo,
      diagnosis: json['diagnosis']?.toString(),
      medicines: medList,
      instructions: json['instructions']?.toString() ?? json['advice']?.toString(),
      audioUrl: json['audio_path']?.toString() ?? json['audio_url']?.toString(),
      createdAt: json['created_at']?.toString() ?? '',
    );
  }
}

class PatientReportModel {
  final int id;
  final String reportNumber;
  final String reportTitle;
  final String testType;
  final String? diagnosticCenter;
  final String reportDate;
  final String filePath;
  final String fileType; // image or pdf
  final String? doctorRemarks;
  final String status;

  PatientReportModel({
    required this.id,
    required this.reportNumber,
    required this.reportTitle,
    required this.testType,
    this.diagnosticCenter,
    required this.reportDate,
    required this.filePath,
    required this.fileType,
    this.doctorRemarks,
    required this.status,
  });

  factory PatientReportModel.fromJson(Map<String, dynamic> json) {
    return PatientReportModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      reportNumber: json['report_number']?.toString() ?? 'RPT-${json['id']}',
      reportTitle: json['report_title']?.toString() ?? 'Medical Report',
      testType: json['test_type']?.toString() ?? 'General Test',
      diagnosticCenter: json['diagnostic_center']?.toString(),
      reportDate: json['report_date']?.toString() ?? '',
      filePath: json['file_path']?.toString() ?? '',
      fileType: json['file_type']?.toString() ?? 'image',
      doctorRemarks: json['doctor_remarks']?.toString(),
      status: json['status']?.toString() ?? 'uploaded',
    );
  }
}

class FamilyMemberModel {
  final int id;
  final String name;
  final String relationship;
  final String gender;
  final String? ageOrDob;
  final String? bloodGroup;
  final int appointmentsCount;
  final int healthMetricsCount;

  FamilyMemberModel({
    required this.id,
    required this.name,
    required this.relationship,
    required this.gender,
    this.ageOrDob,
    this.bloodGroup,
    required this.appointmentsCount,
    required this.healthMetricsCount,
  });

  factory FamilyMemberModel.fromJson(Map<String, dynamic> json) {
    return FamilyMemberModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      name: json['name']?.toString() ?? '',
      relationship: json['relationship']?.toString() ?? 'Other',
      gender: json['gender']?.toString() ?? 'Male',
      ageOrDob: json['age_or_dob']?.toString() ?? json['age']?.toString(),
      bloodGroup: json['blood_group']?.toString(),
      appointmentsCount: int.tryParse(json['appointments_count']?.toString() ?? '0') ?? 0,
      healthMetricsCount: int.tryParse(json['health_metrics_count']?.toString() ?? '0') ?? 0,
    );
  }
}

class HealthPassportModel {
  final String token;
  final String emergencyUrl;
  final int viewCount;
  final String? lastAccessedAt;

  HealthPassportModel({
    required this.token,
    required this.emergencyUrl,
    required this.viewCount,
    this.lastAccessedAt,
  });

  factory HealthPassportModel.fromJson(Map<String, dynamic> json) {
    return HealthPassportModel(
      token: json['token']?.toString() ?? '',
      emergencyUrl: json['emergency_url']?.toString() ?? '',
      viewCount: int.tryParse(json['view_count']?.toString() ?? '0') ?? 0,
      lastAccessedAt: json['last_accessed_at']?.toString(),
    );
  }
}
