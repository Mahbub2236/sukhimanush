import 'package:flutter/material.dart';
import '../../screens/landing/landing_screen.dart';
import '../../screens/doctors/doctor_list_screen.dart';
import '../../screens/doctors/doctor_detail_screen.dart';
import '../../screens/appointments/appointment_booking_screen.dart';
import '../../screens/appointments/live_queue_screen.dart';
import '../../screens/ai_assistant/ai_symptom_checker_screen.dart';
import '../../screens/emergency/emergency_hub_screen.dart';
import '../../screens/emergency/blood_sos_screen.dart';
import '../../screens/auth/login_screen.dart';
import '../../screens/patient/patient_dashboard_screen.dart';
import '../../screens/doctor/doctor_dashboard_screen.dart';
import '../../screens/staff/staff_dashboard_screen.dart';
import '../../screens/agent/agent_dashboard_screen.dart';
import '../../screens/admin/admin_dashboard_screen.dart';

class AppRoutes {
  static const String landing = '/';
  static const String login = '/login';
  static const String doctorList = '/doctors';
  static const String doctorDetail = '/doctor-detail';
  static const String bookAppointment = '/book-appointment';
  static const String liveQueue = '/live-queue';
  static const String aiSymptomChecker = '/ai-symptom-checker';
  static const String emergencyHub = '/emergency-hub';
  static const String bloodSos = '/blood-sos';
  static const String patientDashboard = '/patient-dashboard';
  static const String doctorDashboard = '/doctor-dashboard';
  static const String staffDashboard = '/staff-dashboard';
  static const String agentDashboard = '/agent-dashboard';
  static const String adminDashboard = '/admin-dashboard';

  static Map<String, WidgetBuilder> get routes {
    return {
      landing: (context) => const LandingScreen(),
      login: (context) => const LoginScreen(),
      doctorList: (context) => const DoctorListScreen(),
      doctorDetail: (context) => const DoctorDetailScreen(),
      bookAppointment: (context) => const AppointmentBookingScreen(),
      liveQueue: (context) => const LiveQueueScreen(),
      aiSymptomChecker: (context) => const AiSymptomCheckerScreen(),
      emergencyHub: (context) => const EmergencyHubScreen(),
      bloodSos: (context) => const BloodSosScreen(),
      patientDashboard: (context) => const PatientDashboardScreen(),
      doctorDashboard: (context) => const DoctorDashboardScreen(),
      staffDashboard: (context) => const StaffDashboardScreen(),
      agentDashboard: (context) => const AgentDashboardScreen(),
      adminDashboard: (context) => const AdminDashboardScreen(),
    };
  }
}
