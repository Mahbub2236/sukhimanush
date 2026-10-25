import 'package:flutter/material.dart';
import '../models/doctor_model.dart';
import '../services/api_service.dart';

class HomeProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  bool _isLoading = true;
  String? _errorMessage;

  DoctorModel? _heroDoctor;
  List<DoctorModel> _featuredDoctors = [];
  List<String> _specializations = [];
  List<Map<String, dynamic>> _testimonials = [];
  List<Map<String, dynamic>> _recentBlogs = [];
  Map<String, dynamic> _stats = {
    'total_doctors': '150+',
    'happy_patients': '15,000+',
    'cities_covered': '12+',
    'satisfaction_rate': '99.4%',
  };

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  DoctorModel? get heroDoctor => _heroDoctor;
  List<DoctorModel> get featuredDoctors => _featuredDoctors;
  List<String> get specializations => _specializations;
  List<Map<String, dynamic>> get testimonials => _testimonials;
  List<Map<String, dynamic>> get recentBlogs => _recentBlogs;
  Map<String, dynamic> get stats => _stats;

  Future<void> fetchHomeData() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiService.getHomeData();
      final data = response.data;

      if (data['hero_doctor'] != null && data['hero_doctor'] is Map) {
        _heroDoctor = DoctorModel.fromJson(Map<String, dynamic>.from(data['hero_doctor']));
      }

      if (data['featured_doctors'] != null && data['featured_doctors'] is List) {
        var docs = data['featured_doctors'] as List;
        _featuredDoctors = docs
            .whereType<Map>()
            .map((d) => DoctorModel.fromJson(Map<String, dynamic>.from(d)))
            .toList();
      }

      if (data['specializations'] != null) {
        _specializations = (data['specializations'] as List)
            .map((s) => s.toString())
            .toList();
      }

      if (data['testimonials'] != null) {
        _testimonials = List<Map<String, dynamic>>.from(data['testimonials']);
      }

      if (data['recent_blogs'] != null) {
        _recentBlogs = List<Map<String, dynamic>>.from(data['recent_blogs']);
      }

      if (data['stats'] != null) {
        _stats = Map<String, dynamic>.from(data['stats']);
      }

      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Could not load home data: $e';
      _isLoading = false;
      notifyListeners();
    }
  }
}
