class AppointmentModel {
  final int id;
  final String appointmentNumber;
  final int doctorId;
  final String? doctorName;
  final String? doctorSpecialization;
  final int? chamberId;
  final String? chamberName;
  final String patientName;
  final String patientPhone;
  final String? patientEmail;
  final String? patientGender;
  final int? patientAge;
  final String appointmentDate;
  final String timeSlot;
  final String status;
  final String paymentStatus;
  final String paymentMethod;
  final double fee;
  final int? serialNumber;
  final String? meetingLink;
  final String? diseaseDescription;

  AppointmentModel({
    required this.id,
    required this.appointmentNumber,
    required this.doctorId,
    this.doctorName,
    this.doctorSpecialization,
    this.chamberId,
    this.chamberName,
    required this.patientName,
    required this.patientPhone,
    this.patientEmail,
    this.patientGender,
    this.patientAge,
    required this.appointmentDate,
    required this.timeSlot,
    required this.status,
    required this.paymentStatus,
    required this.paymentMethod,
    required this.fee,
    this.serialNumber,
    this.meetingLink,
    this.diseaseDescription,
  });

  factory AppointmentModel.fromJson(Map<String, dynamic> json) {
    return AppointmentModel(
      id: json['id'] ?? 0,
      appointmentNumber: json['appointment_number'] ?? '',
      doctorId: json['doctor_id'] ?? 0,
      doctorName: json['doctor']?['name'] ?? json['doctor_name'],
      doctorSpecialization: json['doctor']?['specialization'] ?? json['specialization'],
      chamberId: json['doctor_chamber_id'],
      chamberName: json['chamber']?['chamber_name'] ?? json['chamber_name'],
      patientName: json['patient_name'] ?? '',
      patientPhone: json['patient_phone'] ?? '',
      patientEmail: json['patient_email'],
      patientGender: json['patient_gender'],
      patientAge: json['patient_age'],
      appointmentDate: json['appointment_date'] ?? '',
      timeSlot: json['time_slot'] ?? '',
      status: json['status'] ?? 'pending',
      paymentStatus: json['payment_status'] ?? 'unpaid',
      paymentMethod: json['payment_method'] ?? 'cash',
      fee: (json['fee'] != null)
          ? double.tryParse(json['fee'].toString()) ?? 0.0
          : 0.0,
      serialNumber: json['serial_number'],
      meetingLink: json['meeting_link'],
      diseaseDescription: json['disease_description'],
    );
  }
}

class QueueStatusModel {
  final int currentServingToken;
  final int yourToken;
  final int peopleAhead;
  final int estimatedWaitMinutes;
  final String doctorStatus; // in_chamber, on_break, completed
  final String? currentPatientName;

  QueueStatusModel({
    required this.currentServingToken,
    required this.yourToken,
    required this.peopleAhead,
    required this.estimatedWaitMinutes,
    required this.doctorStatus,
    this.currentPatientName,
  });

  factory QueueStatusModel.fromJson(Map<String, dynamic> json) {
    return QueueStatusModel(
      currentServingToken: json['current_token'] ?? json['serving_token'] ?? 0,
      yourToken: json['your_token'] ?? 0,
      peopleAhead: json['people_ahead'] ?? json['waiting_count'] ?? 0,
      estimatedWaitMinutes: json['estimated_wait_minutes'] ?? 0,
      doctorStatus: json['doctor_status'] ?? 'in_chamber',
      currentPatientName: json['current_patient_name'],
    );
  }
}
