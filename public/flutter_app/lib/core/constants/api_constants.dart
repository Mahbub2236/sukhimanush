import 'package:flutter/foundation.dart';

/// Comprehensive API Constants mapping 100% of Novena / Mahriva Laravel API Endpoints
class ApiConstants {
  // Auto-detect environment: Web/Browser/Desktop uses 127.0.0.1:8000, Android Emulator uses 10.0.2.2:8000
  static String get baseUrl {
    if (kIsWeb ||
        defaultTargetPlatform == TargetPlatform.windows ||
        defaultTargetPlatform == TargetPlatform.linux ||
        defaultTargetPlatform == TargetPlatform.macOS) {
      return 'http://127.0.0.1:8000/api';
    }
    return 'http://10.0.2.2:8000/api';
  }

  static const String fallbackBaseUrl = 'http://127.0.0.1:8000/api';
  static const String productionBaseUrl = 'https://yourdomain.com/api';

  // Timeout Configurations
  static const int connectTimeout = 15000; // 15 seconds
  static const int receiveTimeout = 15000;

  // ==========================================
  // 1. PUBLIC ENDPOINTS
  // ==========================================
  // Landing Page & Home
  static const String homeData = '/home-data';

  // Doctors & Search
  static const String doctors = '/doctors';
  static const String doctorLocations = '/doctors/locations';
  static String doctorDetail(String slug) => '/doctors/$slug';
  static String doctorSlots(dynamic id) => '/doctors/$id/slots';

  // AI Health Assistant & Symptom Checker
  static const String aiAnalyzeSymptoms = '/ai/analyze-symptoms';
  static const String aiQuickSymptoms = '/ai/quick-symptoms';
  static const String aiTts = '/ai/tts';

  // Medicines & Autocomplete
  static const String medicinesSearch = '/medicines/search';
  static const String medicinesPresets = '/medicines/presets';

  // Appointments & Booking
  static const String createAppointment = '/appointments';
  static const String trackAppointment = '/appointments/track';
  static String videoChamber(String appointmentNumber) => '/appointments/video-chamber/$appointmentNumber';

  // Live Chamber Queue
  static String patientTokenStatus(dynamic appointmentId) => '/queue/token/$appointmentId';
  static String doctorQueueStatus(dynamic doctorId) => '/queue/status/$doctorId';

  // Contact, Blog & Policies
  static const String contact = '/contact';
  static const String blogs = '/blog';
  static String blogDetail(String slug) => '/blog/$slug';
  static const String policies = '/policies';
  static String policyDetail(String key) => '/policies/$key';

  // Diagnostic Partner Centers & Referral
  static const String diagnosticCenters = '/diagnostic-centers';
  static String diagnosticCenterDetail(dynamic id) => '/diagnostic-centers/$id';
  static const String diagnosticCenterRegister = '/diagnostic-centers/register';
  static const String diagnosticOrderBook = '/diagnostic-orders/book';

  // Prescription Audio Stream
  static String prescriptionAudio(dynamic id) => '/prescriptions/$id/audio';

  // Public Authentication
  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String staffLogin = '/auth/staff-login';
  static const String adminLogin = '/admin/login';

  // Emergency Blood SOS
  static const String bloodRequests = '/blood-sos/requests';
  static const String bloodDonors = '/blood-sos/donors';
  static const String bloodDonorRegister = '/blood-sos/donors/register';
  static String bloodRespond(dynamic id) => '/blood-sos/requests/$id/respond';

  // 1-Tap Emergency Hub (Ambulances & ICU Beds)
  static const String emergencyOverview = '/emergency/overview';
  static const String emergencyAmbulances = '/emergency/ambulances';
  static const String emergencyHospitalBeds = '/emergency/hospital-beds';

  // Procedure Cost Estimator
  static const String costEstimatorProcedures = '/cost-estimator/procedures';
  static String costEstimatorDetail(dynamic id) => '/cost-estimator/procedures/$id';

  // Digital Health Passport Public QR Pass
  static String healthPassportEmergencyPass(String token) => '/health-passport/emergency/$token';

  // Payment Webhooks / Verification
  static const String bkashWebhook = '/payments/webhook/bkash';
  static const String nagadWebhook = '/payments/webhook/nagad';
  static const String sslcommerzIpn = '/payments/webhook/sslcommerz';
  static const String manualPaymentVerify = '/payments/manual-verify';

  // ==========================================
  // 2. AUTHENTICATED PROTECTED ROUTES (Sanctum)
  // ==========================================
  // User Profile
  static const String me = '/auth/me';
  static const String logout = '/auth/logout';
  static const String uploadAvatar = '/profile/upload-avatar';
  static const String updateProfile = '/profile/update';

  // Patient Portal
  static const String patientDashboard = '/patient-portal/dashboard';
  static const String patientDoctors = '/patient-portal/my-doctors';
  static const String patientAppointments = '/patient-portal/my-appointments';
  static const String patientPrescriptions = '/patient-portal/my-prescriptions';
  static const String patientReports = '/patient-portal/my-reports';
  static const String patientUploadReport = '/patient-portal/reports/upload';
  static String patientDeleteReport(dynamic id) => '/patient-portal/reports/$id';
  static const String familyMembers = '/patient-portal/family-members';
  static String familyMemberDetail(dynamic id) => '/patient-portal/family-members/$id';
  static const String healthMetrics = '/patient-portal/health-metrics';
  static String healthMetricDetail(dynamic id) => '/patient-portal/health-metrics/$id';
  static const String patientDiagnosticOrders = '/patient-portal/diagnostic-orders';

  // Doctor Portal
  static const String doctorDashboard = '/doctor-portal/dashboard';
  static const String doctorAppointments = '/doctor-portal/appointments';
  static String doctorUpdateAppointmentStatus(dynamic id) => '/doctor-portal/appointments/$id/status';
  static String doctorUpdateMeetingLink(dynamic id) => '/doctor-portal/appointments/$id/meeting-link';
  static const String doctorUpdateSchedule = '/doctor-portal/schedule/update';
  static const String doctorPrescriptions = '/doctor-portal/prescriptions';
  static String doctorPatientReports(dynamic patientId) => '/doctor-portal/patient-reports/$patientId';
  static String doctorReportRemarks(dynamic id) => '/doctor-portal/reports/$id/remarks';
  static String doctorPatientMetrics(dynamic patientId) => '/doctor-portal/patient-metrics/$patientId';
  static const String doctorChambers = '/doctor-portal/chambers';
  static String doctorChamberDetail(dynamic id) => '/doctor-portal/chambers/$id';
  static String doctorChamberSetPrimary(dynamic id) => '/doctor-portal/chambers/$id/set-primary';
  static String doctorAssistants(dynamic doctorId) => '/doctor-portal/assistants/$doctorId';
  static String doctorAssistantDelete(dynamic id) => '/doctor-portal/assistants/$id';
  static String doctorQueueCallNext(dynamic doctorId) => '/doctor-portal/queue/call-next/$doctorId';
  static String doctorQueueBypass(dynamic doctorId) => '/doctor-portal/queue/emergency-bypass/$doctorId';
  static const String doctorPortfolio = '/doctor-portal/portfolio';
  static const String doctorUploadPortfolioMedia = '/doctor-portal/portfolio/upload-media';
  static const String doctorUploadPortfolioVideo = '/doctor-portal/portfolio/upload-video';
  static const String doctorMembershipUpgrade = '/doctor-portal/membership/upgrade';

  // Staff / Compounder Chamber Portal
  static String staffDashboard(dynamic assistantId) => '/staff-portal/dashboard/$assistantId';
  static String staffCallNext(dynamic assistantId) => '/staff-portal/queue/call-next/$assistantId';
  static String staffUpdateAppointmentStatus(dynamic id) => '/staff-portal/appointments/$id/status';
  static String staffWalkin(dynamic assistantId) => '/staff-portal/walkin/$assistantId';

  // Live Chamber Queue Controls
  static String queueCallNext(dynamic doctorId) => '/queue/call-next/$doctorId';
  static String queueSkip(dynamic doctorId, dynamic appointmentId) => '/queue/skip/$doctorId/$appointmentId';
  static String queueDoctorStatus(dynamic doctorId) => '/queue/doctor-status/$doctorId';

  // Rural Pharmacy Agent Portal
  static String agentDashboard(dynamic agentId) => '/agent-portal/dashboard/$agentId';
  static const String agentSearchPatients = '/agent-portal/patients/search';
  static String agentCreateBooking(dynamic agentId) => '/agent-portal/bookings/$agentId';

  // Medicine Reminders & AI Schedule Extraction
  static const String medicineSchedules = '/medicines/schedules';
  static String medicineAdherence(dynamic id) => '/medicines/schedules/$id/adherence';
  static String medicineScheduleDelete(dynamic id) => '/medicines/schedules/$id';
  static const String medicineAiExtract = '/medicines/ai-extract';

  // Digital Health Passport (User Private)
  static String passportToken(dynamic userId) => '/health-passport/token/$userId';
  static String passportEmergencyInfo(dynamic userId) => '/health-passport/emergency-info/$userId';

  // Super Admin Portal Endpoints
  static const String adminStats = '/admin/stats';
  static const String adminAppointments = '/admin/appointments';
  static String adminAppointmentStatus(dynamic id) => '/admin/appointments/$id/status';
  static const String adminDoctors = '/admin/doctors';
  static String adminDoctorDetail(dynamic id) => '/admin/doctors/$id';
  static String adminDoctorToggleStatus(dynamic id) => '/admin/doctors/$id/status';
  static const String adminPayments = '/admin/payments';
  static const String adminMessages = '/admin/messages';
  static const String adminAgents = '/admin/agents';
  static const String adminStaff = '/admin/staff';
  static const String adminPatients = '/admin/patients';
  static const String adminAmbulances = '/admin/ambulances';
  static const String adminHospitalBeds = '/admin/hospital-beds';
  static const String adminBloodRequests = '/admin/blood-requests';
  static const String adminBloodDonors = '/admin/blood-donors';
  static const String adminHealthPassports = '/admin/health-passports';
  static const String adminDiagnosticOrders = '/admin/diagnostic-orders';
  static const String adminPolicies = '/admin/policies';
}
