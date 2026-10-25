import 'dart:io';
import 'package:dio/dio.dart';
import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';

class ApiService {
  final ApiClient _client = ApiClient();

  // ==========================================
  // 1. PUBLIC ENDPOINTS
  // ==========================================
  Future<Response> getHomeData() async {
    return await _client.get(ApiConstants.homeData);
  }

  Future<Response> getDoctors({
    String? division,
    String? district,
    String? upazila,
    String? specialization,
    String? search,
  }) async {
    return await _client.get(
      ApiConstants.doctors,
      queryParameters: {
        if (division != null) 'division': division,
        if (district != null) 'district': district,
        if (upazila != null) 'upazila': upazila,
        if (specialization != null) 'specialization': specialization,
        if (search != null) 'search': search,
      },
    );
  }

  Future<Response> getDoctorLocations() async {
    return await _client.get(ApiConstants.doctorLocations);
  }

  Future<Response> getDoctorDetail(String slug) async {
    return await _client.get(ApiConstants.doctorDetail(slug));
  }

  Future<Response> getDoctorSlots(dynamic doctorId, {String? date}) async {
    return await _client.get(
      ApiConstants.doctorSlots(doctorId),
      queryParameters: {if (date != null) 'date': date},
    );
  }

  // AI Symptom Checker & Audio
  Future<Response> analyzeSymptoms(String symptomsText, {String language = 'bn', String? district, String? upazila}) async {
    return await _client.post(
      ApiConstants.aiAnalyzeSymptoms,
      data: {
        'message': symptomsText,
        'symptoms': symptomsText,
        'lang': language,
        if (district != null) 'district': district,
        if (upazila != null) 'upazila': upazila,
      },
    );
  }

  Future<Response> getQuickSymptoms() async {
    return await _client.get(ApiConstants.aiQuickSymptoms);
  }

  Future<Response> getTtsAudio(String text) async {
    return await _client.get(
      ApiConstants.aiTts,
      queryParameters: {'text': text},
    );
  }

  // Medicines
  Future<Response> searchMedicines(String query) async {
    return await _client.get(
      ApiConstants.medicinesSearch,
      queryParameters: {'q': query},
    );
  }

  Future<Response> getMedicinePresets() async {
    return await _client.get(ApiConstants.medicinesPresets);
  }

  // Appointments
  Future<Response> createAppointment(Map<String, dynamic> appointmentData) async {
    return await _client.post(ApiConstants.createAppointment, data: appointmentData);
  }

  Future<Response> trackAppointment({String? query, String? appointmentNumber, String? phone}) async {
    final search = query ?? appointmentNumber ?? phone ?? '';
    return await _client.get(
      ApiConstants.trackAppointment,
      queryParameters: {'query': search},
    );
  }

  Future<Response> getVideoChamber(String appointmentNumber) async {
    return await _client.get(ApiConstants.videoChamber(appointmentNumber));
  }

  // Live Queue Status
  Future<Response> getPatientTokenStatus(dynamic appointmentId) async {
    return await _client.get(ApiConstants.patientTokenStatus(appointmentId));
  }

  Future<Response> getDoctorQueueStatus(dynamic doctorId) async {
    return await _client.get(ApiConstants.doctorQueueStatus(doctorId));
  }

  // Blogs, Policies & Contact
  Future<Response> getBlogs() async => await _client.get(ApiConstants.blogs);
  Future<Response> getBlogDetail(String slug) async => await _client.get(ApiConstants.blogDetail(slug));
  Future<Response> getPolicies() async => await _client.get(ApiConstants.policies);
  Future<Response> submitContact(Map<String, dynamic> data) async => await _client.post(ApiConstants.contact, data: data);

  // Diagnostic Centers
  Future<Response> getDiagnosticCenters() async => await _client.get(ApiConstants.diagnosticCenters);
  Future<Response> bookDiagnosticOrder(Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.diagnosticOrderBook, data: data);

  Future<Response> getBloodRequests({String? bloodGroup, String? district}) async =>
      await _client.get(
        ApiConstants.bloodRequests,
        queryParameters: {
          if (bloodGroup != null && bloodGroup != 'All') 'blood_group': bloodGroup,
          if (district != null && district.isNotEmpty) 'district': district,
        },
      );
  Future<Response> storeBloodRequest(Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.bloodRequests, data: data);
  Future<Response> getBloodDonors({String? bloodGroup, String? district}) async =>
      await _client.get(
        ApiConstants.bloodDonors,
        queryParameters: {
          if (bloodGroup != null) 'blood_group': bloodGroup,
          if (district != null) 'district': district,
        },
      );
  Future<Response> registerBloodDonor(Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.bloodDonorRegister, data: data);
  Future<Response> respondToBloodSos(dynamic id, Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.bloodRespond(id), data: data);

  // Emergency Hub
  Future<Response> getEmergencyOverview() async => await _client.get(ApiConstants.emergencyOverview);
  Future<Response> getAmbulances({String? division, String? district}) async =>
      await _client.get(
        ApiConstants.emergencyAmbulances,
        queryParameters: {
          if (division != null) 'division': division,
          if (district != null) 'district': district,
        },
      );
  Future<Response> getHospitalBeds({String? district, String? type}) async =>
      await _client.get(
        ApiConstants.emergencyHospitalBeds,
        queryParameters: {
          if (district != null) 'district': district,
          if (type != null) 'type': type,
        },
      );

  // Procedure Cost Estimator
  Future<Response> getCostEstimates() async => await _client.get(ApiConstants.costEstimatorProcedures);

  // Digital Health Passport Public QR View
  Future<Response> getHealthPassportPublic(String token) async =>
      await _client.get(ApiConstants.healthPassportEmergencyPass(token));

  // ==========================================
  // 2. AUTHENTICATION & PROFILE
  // ==========================================
  Future<Response> login(String email, String password) async {
    return await _client.post(ApiConstants.login, data: {'email': email, 'password': password});
  }

  Future<Response> register(Map<String, dynamic> registerData) async {
    return await _client.post(ApiConstants.register, data: registerData);
  }

  Future<Response> staffLogin(String phone, String pin) async {
    return await _client.post(ApiConstants.staffLogin, data: {'phone': phone, 'access_pin': pin});
  }

  Future<Response> adminLogin(String email, String password) async {
    return await _client.post(ApiConstants.adminLogin, data: {'email': email, 'password': password});
  }

  Future<Response> getMe() async => await _client.get(ApiConstants.me);
  Future<Response> logout() async => await _client.post(ApiConstants.logout);

  Future<Response> updateProfile(Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.updateProfile, data: data);

  Future<Response> uploadAvatar(File avatarFile) async =>
      await _client.uploadFile(ApiConstants.uploadAvatar, file: avatarFile, fileKey: 'avatar');

  // ==========================================
  // 3. PATIENT PORTAL
  // ==========================================
  Future<Response> getPatientDashboard() async => await _client.get(ApiConstants.patientDashboard);
  Future<Response> getPatientDoctors() async => await _client.get(ApiConstants.patientDoctors);
  Future<Response> getPatientAppointments() async => await _client.get(ApiConstants.patientAppointments);
  Future<Response> getPatientPrescriptions() async => await _client.get(ApiConstants.patientPrescriptions);
  Future<Response> getPatientReports({String? testType, String? search}) async =>
      await _client.get(
        ApiConstants.patientReports,
        queryParameters: {
          if (testType != null) 'test_type': testType,
          if (search != null) 'search': search,
        },
      );
  Future<Response> uploadPatientReport(File file, Map<String, dynamic> meta) async =>
      await _client.uploadFile(ApiConstants.patientUploadReport, file: file, fileKey: 'report_file', additionalData: meta);
  Future<Response> deletePatientReport(dynamic id) async =>
      await _client.delete(ApiConstants.patientDeleteReport(id));
  Future<Response> getFamilyMembers() async => await _client.get(ApiConstants.familyMembers);
  Future<Response> storeFamilyMember(Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.familyMembers, data: data);
  Future<Response> deleteFamilyMember(dynamic id) async =>
      await _client.delete(ApiConstants.familyMemberDetail(id));
  Future<Response> getHealthMetrics({String? metricType, dynamic familyMemberId}) async =>
      await _client.get(
        ApiConstants.healthMetrics,
        queryParameters: {
          if (metricType != null) 'metric_type': metricType,
          if (familyMemberId != null) 'family_member_id': familyMemberId,
        },
      );
  Future<Response> storeHealthMetric(Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.healthMetrics, data: data);
  Future<Response> deleteHealthMetric(dynamic id) async =>
      await _client.delete(ApiConstants.healthMetricDetail(id));

  // ==========================================
  // 4. DOCTOR PORTAL
  // ==========================================
  Future<Response> getDoctorDashboard() async => await _client.get(ApiConstants.doctorDashboard);
  Future<Response> getDoctorAppointments() async => await _client.get(ApiConstants.doctorAppointments);
  Future<Response> updateAppointmentStatus(dynamic id, String status) async =>
      await _client.patch(ApiConstants.doctorUpdateAppointmentStatus(id), data: {'status': status});
  Future<Response> updateMeetingLink(dynamic id, String meetingLink) async =>
      await _client.patch(ApiConstants.doctorUpdateMeetingLink(id), data: {'meeting_link': meetingLink});
  Future<Response> callNextDoctorPatient(dynamic doctorId) async =>
      await _client.post(ApiConstants.doctorQueueCallNext(doctorId));
  Future<Response> emergencyBypassDoctorPatient(dynamic doctorId, dynamic appointmentId) async =>
      await _client.post(ApiConstants.doctorQueueBypass(doctorId), data: {'appointment_id': appointmentId});
  Future<Response> getDoctorChambers() async => await _client.get(ApiConstants.doctorChambers);
  Future<Response> createPrescription(Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.doctorPrescriptions, data: data);

  // ==========================================
  // 5. STAFF & AGENT PORTALS
  // ==========================================
  Future<Response> getStaffDashboard(dynamic assistantId) async =>
      await _client.get(ApiConstants.staffDashboard(assistantId));
  Future<Response> staffCallNext(dynamic assistantId) async =>
      await _client.post(ApiConstants.staffCallNext(assistantId));
  Future<Response> staffWalkin(dynamic assistantId, Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.staffWalkin(assistantId), data: data);
  Future<Response> staffUpdateAppointmentStatus(dynamic id, String status) async =>
      await _client.patch(ApiConstants.staffUpdateAppointmentStatus(id), data: {'status': status});
  Future<Response> getAgentDashboard(dynamic agentId) async =>
      await _client.get(ApiConstants.agentDashboard(agentId));
  Future<Response> agentSearchPatients(String query) async =>
      await _client.get(ApiConstants.agentSearchPatients, queryParameters: {'q': query});
  Future<Response> agentCreateBooking(dynamic agentId, Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.agentCreateBooking(agentId), data: data);

  // ==========================================
  // 6. SUPER ADMIN PORTAL
  // ==========================================
  Future<Response> getAdminStats() async => await _client.get(ApiConstants.adminStats);
  Future<Response> getAdminAppointments({String? status}) async =>
      await _client.get(ApiConstants.adminAppointments, queryParameters: {
        if (status != null && status != 'all') 'status': status,
      });
  Future<Response> updateAdminAppointmentStatus(dynamic id, String status) async =>
      await _client.patch(ApiConstants.adminAppointmentStatus(id), data: {'status': status});
  Future<Response> getAdminDoctors() async => await _client.get(ApiConstants.adminDoctors);
  Future<Response> toggleAdminDoctorStatus(dynamic id) async =>
      await _client.patch(ApiConstants.adminDoctorToggleStatus(id));

  // ==========================================
  // 6. MEDICINES REMINDERS & HEALTH PASSPORT
  // ==========================================
  Future<Response> getMedicineSchedules() async => await _client.get(ApiConstants.medicineSchedules);
  Future<Response> storeMedicineSchedule(Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.medicineSchedules, data: data);
  Future<Response> logMedicineAdherence(dynamic id, bool taken) async =>
      await _client.patch(ApiConstants.medicineAdherence(id), data: {'is_taken': taken});
  Future<Response> getHealthPassportToken(dynamic userId) async =>
      await _client.get(ApiConstants.passportToken(userId));
  Future<Response> updateHealthPassportEmergency(dynamic userId, Map<String, dynamic> data) async =>
      await _client.post(ApiConstants.passportEmergencyInfo(userId), data: data);
}
