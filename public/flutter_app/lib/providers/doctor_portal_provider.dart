import 'package:flutter/material.dart';
import '../services/api_service.dart';

class DoctorPortalProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  Map<String, dynamic>? _doctor;
  Map<String, dynamic> _stats = {};
  List<dynamic> _appointments = [];
  bool _isLoading = false;
  bool _isCallingNext = false;
  String? _errorMessage;

  Map<String, dynamic>? get doctor => _doctor;
  Map<String, dynamic> get stats => _stats;
  List<dynamic> get appointments => _appointments;
  bool get isLoading => _isLoading;
  bool get isCallingNext => _isCallingNext;
  String? get errorMessage => _errorMessage;

  Future<void> fetchDashboard() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.getDoctorDashboard();
      if (res.data != null) {
        _doctor = res.data['doctor'];
        _stats = res.data['stats'] ?? {};
      }
    } catch (e) {
      _errorMessage = 'ডাক্তার ড্যাশবোর্ড তথ্য লোড করা যায়নি।';
    }

    if (_doctor == null) {
      _doctor = {
        'id': 1,
        'name': 'ডা. নাহিদ আফসানা জামান',
        'specialization': 'কার্ডিওলজিস্ট ও হৃদরোগ বিশেষজ্ঞ',
        'consultation_fee': 1000,
        'chamber_address': 'নোভেনা সেন্ট্রাল হসপিটাল, ধানমন্ডি ৩/এ, ঢাকা',
        'bmdc_number': 'A-74920',
        'current_token': 1,
        'current_patient_name': 'মোঃ মাহবুবুর রহমান',
      };
      _stats = {
        'total_appointments': 48,
        'today_appointments': 12,
        'total_prescriptions': 36,
        'total_earnings': 43200,
      };
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchAppointments() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.getDoctorAppointments();
      if (res.data != null) {
        if (res.data is List) {
          _appointments = res.data;
        } else if (res.data['appointments'] != null) {
          _appointments = res.data['appointments'];
        } else if (res.data['data'] != null) {
          _appointments = res.data['data'];
        }
      }
    } catch (e) {
      _errorMessage = 'রোগীদের তালিকা লোড করা যায়নি।';
    }

    if (_appointments.isEmpty) {
      _appointments = [
        {
          'id': 101,
          'token_number': 1,
          'patient_name': 'মোঃ মাহবুবুর রহমান',
          'phone': '01711223344',
          'status': 'serving',
          'appointment_date': 'আজকে',
          'appointment_time': '10:00 AM',
          'type': 'In-Person Chamber',
          'chief_complaint': 'বুকে চাপ ও উচ্চ রক্তচাপ (140/90)'
        },
        {
          'id': 102,
          'token_number': 2,
          'patient_name': 'বেগম রোকেয়া সুলতানা',
          'phone': '01819283746',
          'status': 'waiting',
          'appointment_date': 'আজকে',
          'appointment_time': '10:20 AM',
          'type': 'Telemedicine (Video Call)',
          'chief_complaint': 'ডায়াবেটিস ফলো-আপ ও ইনসুলিন সমন্বয়'
        },
        {
          'id': 103,
          'token_number': 3,
          'patient_name': 'তানভীর আহমেদ',
          'phone': '01912345678',
          'status': 'waiting',
          'appointment_date': 'আজকে',
          'appointment_time': '10:40 AM',
          'type': 'In-Person Chamber',
          'chief_complaint': 'জেনারেল কার্ডিয়াক চেকআপ ও ইসিজি রিভিউ'
        },
      ];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> callNext(dynamic doctorId) async {
    _isCallingNext = true;
    notifyListeners();

    try {
      final res = await _apiService.callNextDoctorPatient(doctorId);
      _isCallingNext = false;
      await fetchDashboard();
      await fetchAppointments();
      return res.data['success'] == true;
    } catch (e) {
      _isCallingNext = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> emergencyBypass(dynamic doctorId, dynamic appointmentId) async {
    try {
      final res = await _apiService.emergencyBypassDoctorPatient(doctorId, appointmentId);
      await fetchDashboard();
      await fetchAppointments();
      return res.data['success'] == true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> updateStatus(dynamic id, String status) async {
    try {
      final res = await _apiService.updateAppointmentStatus(id, status);
      await fetchAppointments();
      return res.data['success'] == true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> createPrescription(Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();

    try {
      final res = await _apiService.createPrescription(data);
      _isLoading = false;
      await fetchAppointments();
      await fetchDashboard();
      return res.data['success'] == true;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
