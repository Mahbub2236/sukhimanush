import 'package:flutter/material.dart';
import '../services/api_service.dart';

class StaffProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  Map<String, dynamic>? _assistant;
  Map<String, dynamic>? _doctor;
  Map<String, dynamic> _stats = {};
  List<dynamic> _appointments = [];
  bool _isLoading = false;
  bool _isCallingNext = false;
  String? _errorMessage;

  Map<String, dynamic>? get assistant => _assistant;
  Map<String, dynamic>? get doctor => _doctor;
  Map<String, dynamic> get stats => _stats;
  List<dynamic> get appointments => _appointments;
  bool get isLoading => _isLoading;
  bool get isCallingNext => _isCallingNext;
  String? get errorMessage => _errorMessage;

  Future<void> fetchDashboard(dynamic assistantId) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.getStaffDashboard(assistantId);
      if (res.data != null) {
        _assistant = res.data['assistant'];
        _doctor = res.data['doctor'];
        _stats = res.data['stats'] ?? {};
        _appointments = res.data['appointments'] ?? [];
      }
    } catch (e) {
      _errorMessage = 'স্টাফ ড্যাশবোর্ড লোড করতে সমস্যা হয়েছে।';
    }

    if (_assistant == null) {
      _assistant = {
        'id': 1,
        'name': 'কামাল হোসেন (চেম্বার সহকারী)',
        'phone': '01700998877',
        'doctor_name': 'ডা. নাহিদ আফসানা জামান',
        'chamber': 'চেম্বার #৩, নোভেনা হসপিটাল, ধানমন্ডি',
      };
      _doctor = {
        'id': 1,
        'name': 'ডা. নাহিদ আফসানা জামান',
        'specialization': 'কার্ডিওলজিস্ট',
        'current_token': 1,
      };
      _stats = {
        'today_tokens': 15,
        'completed_tokens': 4,
        'waiting_tokens': 11,
        'total_collection': 15000,
      };
      _appointments = [
        {
          'id': 201,
          'token_number': 1,
          'patient_name': 'মোঃ মাহবুবুর রহমান',
          'phone': '01711223344',
          'status': 'serving',
          'time': '10:00 AM',
          'payment_status': 'Paid (৳1000)',
        },
        {
          'id': 202,
          'token_number': 2,
          'patient_name': 'বেগম রোকেয়া সুলতানা',
          'phone': '01819283746',
          'status': 'waiting',
          'time': '10:20 AM',
          'payment_status': 'Paid (৳1000)',
        },
        {
          'id': 203,
          'token_number': 3,
          'patient_name': 'আব্দুল করিম',
          'phone': '01911223344',
          'status': 'waiting',
          'time': '10:40 AM',
          'payment_status': 'Due',
        },
      ];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> callNext(dynamic assistantId) async {
    _isCallingNext = true;
    notifyListeners();

    try {
      final res = await _apiService.staffCallNext(assistantId);
      _isCallingNext = false;
      await fetchDashboard(assistantId);
      return res.data['success'] == true;
    } catch (e) {
      _isCallingNext = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> createWalkin(dynamic assistantId, Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();

    try {
      final res = await _apiService.staffWalkin(assistantId, data);
      _isLoading = false;
      await fetchDashboard(assistantId);
      return res.data['success'] == true;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateStatus(dynamic assistantId, dynamic appointmentId, String status) async {
    try {
      final res = await _apiService.staffUpdateAppointmentStatus(appointmentId, status);
      await fetchDashboard(assistantId);
      return res.data['success'] == true;
    } catch (e) {
      return false;
    }
  }
}
