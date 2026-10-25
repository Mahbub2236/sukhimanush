import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import '../core/network/api_client.dart';

class AuthProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  final ApiClient _client = ApiClient();

  UserModel? _currentUser;
  bool _isLoading = false;
  String? _errorMessage;

  UserModel? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _currentUser != null;
  String? get errorMessage => _errorMessage;

  Future<void> loadUserFromStorage() async {
    final prefs = await SharedPreferences.getInstance();
    final rawUser = prefs.getString('user_data');
    final token = prefs.getString('auth_token');
    if (token != null && token.isNotEmpty) {
      _client.setAuthToken(token);
    }
    if (rawUser != null) {
      try {
        _currentUser = UserModel.fromJson(jsonDecode(rawUser));
        notifyListeners();
      } catch (_) {}
    }
  }

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiService.login(email, password);
      if (response.data['success'] == true) {
        final token = response.data['token'];
        final userData = response.data['user'] as Map<String, dynamic>;

        if (response.data['doctor'] != null) {
          userData['doctor_id'] = response.data['doctor']['id'];
        }

        _client.setAuthToken(token);
        _currentUser = UserModel.fromJson(userData);

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('user_data', jsonEncode(userData));
        await prefs.setString('auth_token', token.toString());

        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = response.data['message'] ?? 'Login failed';
      }
    } catch (e) {
      if (e is DioException && e.response?.data != null) {
        final data = e.response!.data;
        if (data is Map && data.containsKey('message')) {
          _errorMessage = data['message'].toString();
        } else {
          _errorMessage = 'Invalid email or password credentials.';
        }
      } else {
        _errorMessage = 'Could not connect to backend server. Ensure Laravel is running on port 8000.';
      }
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<bool> staffLogin(String phone, String accessPin) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiService.staffLogin(phone, accessPin);
      if (response.data['success'] == true) {
        final token = response.data['token'];
        final userData = response.data['user'] as Map<String, dynamic>;
        
        // Ensure assistant details are kept
        if (response.data['assistant'] != null) {
          userData['assistant_id'] = response.data['assistant']['id'];
          userData['doctor_id'] = response.data['assistant']['doctor_id'];
        }

        _client.setAuthToken(token);
        _currentUser = UserModel.fromJson(userData);

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('user_data', jsonEncode(userData));
        await prefs.setString('auth_token', token.toString());

        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = response.data['message'] ?? 'স্টাফ লগইন ব্যর্থ হয়েছে।';
      }
    } catch (e) {
      if (e is DioException && e.response?.data != null) {
        final data = e.response!.data;
        if (data is Map && data.containsKey('message')) {
          _errorMessage = data['message'].toString();
        } else {
          _errorMessage = 'ভুল মোবাইল নম্বর অথবা অ্যাক্সেস পিন!';
        }
      } else {
        _errorMessage = 'সার্ভারের সাথে সংযোগ স্থাপন করা যায়নি।';
      }
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<bool> adminLogin(String email, String password) async {
    return await login(email, password);
  }

  Future<bool> quickDemoLogin(String role) async {
    switch (role.toLowerCase()) {
      case 'doctor':
        return await login('dr.thomas@novena.com', 'password');
      case 'staff':
        return await staffLogin('01700000001', '1234');
      case 'agent':
        return await login('agent@novena.com', 'password');
      case 'admin':
        return await login('admin@novena.com', 'password');
      case 'patient':
      default:
        return await login('patient@novena.com', 'password');
    }
  }

  Future<bool> register(Map<String, dynamic> data) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiService.register(data);
      if (response.data['success'] == true) {
        final token = response.data['token'];
        final userData = response.data['user'];

        _client.setAuthToken(token);
        _currentUser = UserModel.fromJson(userData);

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('user_data', jsonEncode(userData));
        await prefs.setString('auth_token', token.toString());

        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = response.data['message'] ?? 'Registration failed';
      }
    } catch (e) {
      _errorMessage = 'Registration error. Check your email or input fields.';
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<void> logout() async {
    try {
      await _apiService.logout();
    } catch (_) {}
    await _client.clearAuthToken();
    _currentUser = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user_data');
    await prefs.remove('auth_token');
    notifyListeners();
  }
}
