import 'package:flutter/material.dart';
import '../models/appointment_model.dart';
import '../services/api_service.dart';

class AppointmentProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  bool _isSubmitting = false;
  String? _errorMessage;
  AppointmentModel? _createdAppointment;

  // Tracking & Queue
  bool _isTracking = false;
  List<AppointmentModel> _trackedAppointments = [];
  Map<int, Map<String, dynamic>> _doctorQueueStatuses = {};

  bool get isSubmitting => _isSubmitting;
  bool get isTracking => _isTracking;
  String? get errorMessage => _errorMessage;
  AppointmentModel? get createdAppointment => _createdAppointment;
  List<AppointmentModel> get trackedAppointments => _trackedAppointments;
  Map<int, Map<String, dynamic>> get doctorQueueStatuses => _doctorQueueStatuses;

  Future<bool> bookAppointment(Map<String, dynamic> data) async {
    _isSubmitting = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.createAppointment(data);
      if (res.data['success'] == true) {
        _createdAppointment = AppointmentModel.fromJson(Map<String, dynamic>.from(res.data['appointment']));
        _isSubmitting = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = res.data['message'] ?? 'Booking failed';
      }
    } catch (e) {
      _errorMessage = 'Booking submission error: $e';
    }

    _isSubmitting = false;
    notifyListeners();
    return false;
  }

  Future<bool> trackAppointmentQuery(String query) async {
    if (query.trim().isEmpty) return false;

    _isTracking = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.trackAppointment(query: query.trim());
      if (res.data['appointments'] is List) {
        final list = res.data['appointments'] as List;
        _trackedAppointments = list
            .map((item) => AppointmentModel.fromJson(Map<String, dynamic>.from(item)))
            .toList();

        // Also fetch live queue status for each doctor in the results
        for (var apt in _trackedAppointments) {
          fetchDoctorQueue(apt.doctorId);
        }

        _isTracking = false;
        notifyListeners();
        return _trackedAppointments.isNotEmpty;
      }
    } catch (e) {
      _errorMessage = 'কোনো অ্যাপয়েন্টমেন্ট পাওয়া যায়নি। নম্বর যাচাই করুন।';
    }

    _isTracking = false;
    notifyListeners();
    return false;
  }

  Future<void> fetchDoctorQueue(int doctorId) async {
    try {
      final res = await _apiService.getDoctorQueueStatus(doctorId);
      if (res.data['queue'] is Map) {
        _doctorQueueStatuses[doctorId] = Map<String, dynamic>.from(res.data['queue']);
        notifyListeners();
      }
    } catch (_) {}
  }
}
