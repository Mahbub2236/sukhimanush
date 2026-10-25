import 'package:flutter/material.dart';
import '../models/ai_symptom_model.dart';
import '../services/api_service.dart';

class AiAssistantProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  bool _isAnalyzing = false;
  String? _errorMessage;
  AiSymptomResultModel? _analysisResult;
  List<String> _quickSymptoms = [];

  bool get isAnalyzing => _isAnalyzing;
  String? get errorMessage => _errorMessage;
  AiSymptomResultModel? get analysisResult => _analysisResult;
  List<String> get quickSymptoms => _quickSymptoms;

  Future<void> fetchQuickSymptoms() async {
    try {
      final res = await _apiService.getQuickSymptoms();
      var list = res.data['quick_symptoms'] as List? ?? [];
      _quickSymptoms = list.map((e) => e.toString()).toList();
      notifyListeners();
    } catch (_) {
      _quickSymptoms = [
        'মাথা ব্যথা',
        'বুকে তীব্র চাপ',
        'জ্বর ও সর্দি',
        'পেট খারাপ ও গ্যাস্ট্রিক',
        'শ্বাসকষ্ট',
        'কোমর ব্যথা'
      ];
      notifyListeners();
    }
  }

  Future<bool> analyzeSymptoms(String symptomText) async {
    if (symptomText.trim().isEmpty) return false;

    _isAnalyzing = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _apiService.analyzeSymptoms(symptomText);
      _analysisResult = AiSymptomResultModel.fromJson(res.data);
      _isAnalyzing = false;
      notifyListeners();
      return true;
    } catch (e) {
      debugPrint('AI analyze error: $e');
      _errorMessage = 'লক্ষণ বিশ্লেষণ করতে সমস্যা হয়েছে: $e';
      _isAnalyzing = false;
      notifyListeners();
      return false;
    }
  }

  void resetAnalysis() {
    _analysisResult = null;
    notifyListeners();
  }
}
