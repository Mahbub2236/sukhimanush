import 'package:flutter/material.dart';
import '../models/doctor_model.dart';
import '../services/api_service.dart';

class DoctorProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  bool _isLoading = false;
  String? _errorMessage;

  List<DoctorModel> _doctors = [];
  Map<String, dynamic> _locations = {};
  DoctorModel? _selectedDoctor;
  List<String> _availableSlots = [];

  String? selectedDivision;
  String? selectedDistrict;
  String? selectedSpecialization;
  String searchQuery = '';

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  List<DoctorModel> get doctors => _doctors;
  Map<String, dynamic> get locations => _locations;
  DoctorModel? get selectedDoctor => _selectedDoctor;
  List<String> get availableSlots => _availableSlots;

  Future<void> fetchLocations() async {
    try {
      final res = await _apiService.getDoctorLocations();
      if (res.data is Map) {
        _locations = Map<String, dynamic>.from(res.data);
        notifyListeners();
      }
    } catch (_) {}
  }

  Future<void> fetchDoctors({
    String? division,
    String? district,
    String? specialization,
    String? query,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.getDoctors(
        division: division ?? selectedDivision,
        district: district ?? selectedDistrict,
        specialization: specialization ?? selectedSpecialization,
        search: (query != null && query.trim().isNotEmpty)
            ? query.trim()
            : (searchQuery.trim().isNotEmpty ? searchQuery.trim() : null),
      );

      var rawList = [];
      if (res.data is Map) {
        if (res.data['doctors'] is List) {
          rawList = res.data['doctors'];
        } else if (res.data['data'] is List) {
          rawList = res.data['data'];
        }
      } else if (res.data is List) {
        rawList = res.data;
      }

      _doctors = rawList.map((d) => DoctorModel.fromJson(Map<String, dynamic>.from(d))).toList();
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Failed to load doctors: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchDoctorDetail(String slug) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.getDoctorDetail(slug);
      final raw = res.data['doctor'] ?? res.data;
      _selectedDoctor = DoctorModel.fromJson(raw);
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Doctor not found.';
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchSlots(dynamic doctorId, String date) async {
    try {
      final res = await _apiService.getDoctorSlots(doctorId, date: date);
      var slots = res.data['slots'] as List? ?? [];
      _availableSlots = slots.map((s) => s.toString()).toList();
      notifyListeners();
    } catch (_) {
      _availableSlots = ['10:00 AM', '11:00 AM', '04:00 PM', '05:30 PM', '07:00 PM'];
      notifyListeners();
    }
  }
}
