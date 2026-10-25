import 'package:flutter/material.dart';
import '../models/emergency_model.dart';
import '../services/api_service.dart';

class EmergencyProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  // Overview & Hotlines
  bool _isLoadingOverview = false;
  Map<String, dynamic> _overviewStats = {};
  List<Map<String, dynamic>> _hotlines = [];

  // Ambulances & ICU Beds
  bool _isLoadingAmbulances = false;
  List<AmbulanceModel> _ambulances = [];
  bool _isLoadingBeds = false;
  List<HospitalBedModel> _hospitalBeds = [];
  int _totalIcuFree = 0;
  int _totalNicuFree = 0;
  int _totalCcuFree = 0;

  // Blood SOS
  bool _isLoadingBlood = false;
  List<BloodRequestModel> _bloodRequests = [];
  List<BloodDonorModel> _bloodDonors = [];
  Map<String, dynamic> _bloodStats = {};
  String? _errorMessage;

  // Getters
  bool get isLoadingOverview => _isLoadingOverview;
  Map<String, dynamic> get overviewStats => _overviewStats;
  List<Map<String, dynamic>> get hotlines => _hotlines;

  bool get isLoadingAmbulances => _isLoadingAmbulances;
  List<AmbulanceModel> get ambulances => _ambulances;

  bool get isLoadingBeds => _isLoadingBeds;
  List<HospitalBedModel> get hospitalBeds => _hospitalBeds;
  int get totalIcuFree => _totalIcuFree;
  int get totalNicuFree => _totalNicuFree;
  int get totalCcuFree => _totalCcuFree;

  bool get isLoadingBlood => _isLoadingBlood;
  List<BloodRequestModel> get bloodRequests => _bloodRequests;
  List<BloodDonorModel> get bloodDonors => _bloodDonors;
  Map<String, dynamic> get bloodStats => _bloodStats;
  String? get errorMessage => _errorMessage;

  // 1. Fetch Emergency Overview
  Future<void> fetchEmergencyOverview() async {
    _isLoadingOverview = true;
    notifyListeners();

    try {
      final res = await _apiService.getEmergencyOverview();
      if (res.data['success'] == true) {
        _overviewStats = res.data;
        if (res.data['hotlines'] is List) {
          _hotlines = List<Map<String, dynamic>>.from(res.data['hotlines']);
        }
      }
    } catch (_) {}

    _isLoadingOverview = false;
    notifyListeners();
  }

  // 2. Fetch Ambulances
  Future<void> fetchAmbulances({String? district, String? type}) async {
    _isLoadingAmbulances = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.getAmbulances(district: district);
      if (res.data['ambulances'] is List) {
        final list = res.data['ambulances'] as List;
        _ambulances = list
            .map((item) => AmbulanceModel.fromJson(Map<String, dynamic>.from(item)))
            .toList();
      }
    } catch (e) {
      _errorMessage = 'Could not load ambulances: $e';
    }

    _isLoadingAmbulances = false;
    notifyListeners();
  }

  // 3. Fetch ICU Hospital Beds
  Future<void> fetchHospitalBeds({String? district}) async {
    _isLoadingBeds = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.getHospitalBeds(district: district);
      if (res.data['hospitals'] is List) {
        final list = res.data['hospitals'] as List;
        _hospitalBeds = list
            .map((item) => HospitalBedModel.fromJson(Map<String, dynamic>.from(item)))
            .toList();
      }
      _totalIcuFree = int.tryParse(res.data['total_icu_free']?.toString() ?? '0') ?? 0;
      _totalNicuFree = int.tryParse(res.data['total_nicu_free']?.toString() ?? '0') ?? 0;
      _totalCcuFree = int.tryParse(res.data['total_ccu_free']?.toString() ?? '0') ?? 0;
    } catch (e) {
      _errorMessage = 'Could not load hospital beds: $e';
    }

    _isLoadingBeds = false;
    notifyListeners();
  }

  // 4. Fetch Blood SOS Requests & Donors
  Future<void> fetchBloodData({String? bloodGroup, String? district}) async {
    _isLoadingBlood = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final reqRes = await _apiService.getBloodRequests(
        bloodGroup: bloodGroup,
        district: district,
      );
      if (reqRes.data['requests'] is List) {
        final list = reqRes.data['requests'] as List;
        _bloodRequests = list
            .map((item) => BloodRequestModel.fromJson(Map<String, dynamic>.from(item)))
            .toList();
      }
      if (reqRes.data['stats'] is Map) {
        _bloodStats = Map<String, dynamic>.from(reqRes.data['stats']);
      }

      final donorRes = await _apiService.getBloodDonors(
        bloodGroup: bloodGroup,
        district: district,
      );
      if (donorRes.data['donors'] is List) {
        final list = donorRes.data['donors'] as List;
        _bloodDonors = list
            .map((item) => BloodDonorModel.fromJson(Map<String, dynamic>.from(item)))
            .toList();
      }
    } catch (e) {
      _errorMessage = 'Could not load blood SOS data: $e';
    }

    _isLoadingBlood = false;
    notifyListeners();
  }

  // 5. Create Blood SOS Request
  Future<bool> createBloodRequest(Map<String, dynamic> data) async {
    try {
      final res = await _apiService.storeBloodRequest(data);
      if (res.data['success'] == true) {
        await fetchBloodData();
        return true;
      }
    } catch (_) {}
    return false;
  }

  // 6. Register as Blood Donor
  Future<bool> registerDonor(Map<String, dynamic> data) async {
    try {
      final res = await _apiService.registerBloodDonor(data);
      if (res.data['success'] == true) {
        await fetchBloodData();
        return true;
      }
    } catch (_) {}
    return false;
  }

  // 7. Respond to SOS
  Future<Map<String, dynamic>?> respondToSos(int requestId, String phone, String name) async {
    try {
      final res = await _apiService.respondToBloodSos(
        requestId,
        {'donor_phone': phone, 'donor_name': name},
      );
      if (res.data['success'] == true) {
        return Map<String, dynamic>.from(res.data);
      }
    } catch (_) {}
    return null;
  }
}
