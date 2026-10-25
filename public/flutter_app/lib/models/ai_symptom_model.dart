import 'doctor_model.dart';

class AiSymptomResultModel {
  final bool isEmergency;
  final String specialtyBn;
  final String specialtyEn;
  final String advice;
  final List<String> matchedSymptoms;
  final List<String> emergencyWarnings;
  final List<String> suggestedQuestions;
  final List<DoctorModel> recommendedDoctors;

  AiSymptomResultModel({
    required this.isEmergency,
    required this.specialtyBn,
    required this.specialtyEn,
    required this.advice,
    required this.matchedSymptoms,
    required this.emergencyWarnings,
    required this.suggestedQuestions,
    required this.recommendedDoctors,
  });

  factory AiSymptomResultModel.fromJson(Map<String, dynamic> json) {
    var rawDocs = json['recommended_doctors'] as List? ?? [];
    List<DoctorModel> docs =
        rawDocs.map((d) => DoctorModel.fromJson(d)).toList();

    List<String> warnings = [];
    if (json['emergency_warnings'] is List) {
      warnings = (json['emergency_warnings'] as List).map((e) => e.toString()).toList();
    } else if (json['emergency_alert'] is Map && json['emergency_alert']['message'] != null) {
      warnings = [json['emergency_alert']['message'].toString()];
    }

    List<String> symptoms = [];
    if (json['matched_symptoms'] is List) {
      symptoms = (json['matched_symptoms'] as List).map((e) => e.toString()).toList();
    } else if (json['matched_keywords'] is List) {
      symptoms = (json['matched_keywords'] as List).map((e) => e.toString()).toList();
    }

    return AiSymptomResultModel(
      isEmergency: json['is_emergency'] == true || json['is_emergency'] == 1,
      specialtyBn: json['specialty_bn'] ?? json['department_bn'] ?? '',
      specialtyEn: json['specialty_en'] ?? json['department_en'] ?? '',
      advice: json['ai_advice'] ?? json['advice'] ?? '',
      matchedSymptoms: symptoms,
      emergencyWarnings: warnings,
      suggestedQuestions: (json['suggested_questions'] as List?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      recommendedDoctors: docs,
    );
  }
}
