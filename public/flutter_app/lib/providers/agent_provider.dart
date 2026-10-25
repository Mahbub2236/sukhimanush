import 'package:flutter/material.dart';
import '../services/api_service.dart';

class AgentProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  Map<String, dynamic>? _agent;
  Map<String, dynamic> _stats = {};
  List<dynamic> _recentBookings = [];
  List<dynamic> _searchResults = [];
  bool _isLoading = false;
  String? _errorMessage;

  Map<String, dynamic>? get agent => _agent;
  Map<String, dynamic> get stats => _stats;
  List<dynamic> get recentBookings => _recentBookings;
  List<dynamic> get searchResults => _searchResults;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> fetchDashboard(dynamic agentId) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.getAgentDashboard(agentId);
      if (res.data != null) {
        _agent = res.data['agent'];
        _stats = res.data['stats'] ?? {};
        _recentBookings = res.data['recent_bookings'] ?? [];
      }
    } catch (e) {
      _errorMessage = 'এজেন্ট পোর্টাল ডেটা লোড করা যায়নি।';
    }

    if (_agent == null) {
      _agent = {
        'id': 1,
        'name': 'সাকিব আল হাসান (গ্রামীণ হেলথ এজেন্ট)',
        'shop_name': 'সাকিব ফার্মেসি ও ডিজিটাল সেবা কেন্দ্র',
        'location': 'শ্রীপুর বাজার, গাজীপুর',
        'phone': '01712345678',
        'wallet_balance': 3450,
        'commission_rate': '10%',
      };
      _stats = {
        'total_bookings': 56,
        'this_month_bookings': 24,
        'total_commission': 5600,
        'wallet_balance': 3450,
      };
      _recentBookings = [
        {
          'id': 301,
          'patient_name': 'মোঃ রফিকুল ইসলাম',
          'doctor_name': 'ডা. নাহিদ আফসানা জামান',
          'fee': 1000,
          'commission': 100,
          'status': 'confirmed',
          'created_at': 'আজকে, ০৯:৩০ AM',
        },
        {
          'id': 302,
          'patient_name': 'মোছাঃ ফাতেমা বেগম',
          'doctor_name': 'ডা. থমাস হেনরি',
          'fee': 800,
          'commission': 80,
          'status': 'completed',
          'created_at': 'গতকাল, ০৪:১৫ PM',
        },
        {
          'id': 303,
          'patient_name': 'আব্দুল করিম',
          'doctor_name': 'ডা. সেলিনা পারভীন',
          'fee': 1200,
          'commission': 120,
          'status': 'confirmed',
          'created_at': 'আজকে, ১১:০০ AM',
        },
      ];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> searchPatients(String query) async {
    try {
      final res = await _apiService.agentSearchPatients(query);
      if (res.data != null && res.data['patients'] != null) {
        _searchResults = res.data['patients'];
        notifyListeners();
      }
    } catch (_) {}
  }

  Future<bool> createBooking(dynamic agentId, Map<String, dynamic> data) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.agentCreateBooking(agentId, data);
      _isLoading = false;
      await fetchDashboard(agentId);
      return res.data['success'] == true;
    } catch (e) {
      _isLoading = false;
      _errorMessage = 'রোগী বুকিং সম্পন্ন করা যায়নি।';
      notifyListeners();
      return false;
    }
  }
}
