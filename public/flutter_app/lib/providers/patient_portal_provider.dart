import 'dart:io';
import 'package:flutter/material.dart';
import 'package:audioplayers/audioplayers.dart';
import '../models/patient_portal_models.dart';
import '../models/appointment_model.dart';
import '../services/api_service.dart';
import '../core/constants/api_constants.dart';

class PatientPortalProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  final AudioPlayer _audioPlayer = AudioPlayer();

  bool _isLoading = false;
  String? _errorMessage;

  // Dashboard Stats
  Map<String, dynamic> _stats = {};

  // Lists
  List<AppointmentModel> _appointments = [];
  List<PrescriptionModel> _prescriptions = [];
  List<PatientReportModel> _reports = [];
  List<HealthMetricModel> _healthMetrics = [];
  List<FamilyMemberModel> _familyMembers = [];
  HealthPassportModel? _healthPassport;

  // Active Family Member filter
  FamilyMemberModel? _activeFamilyMember;

  // Audio Playback
  int? _playingPrescriptionId;
  bool _isPlayingAudio = false;
  bool _isAudioLoading = false;

  // Getters
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  Map<String, dynamic> get stats => _stats;
  List<AppointmentModel> get appointments => _appointments;
  List<PrescriptionModel> get prescriptions => _prescriptions;
  List<PatientReportModel> get reports => _reports;
  List<HealthMetricModel> get healthMetrics => _healthMetrics;
  List<FamilyMemberModel> get familyMembers => _familyMembers;
  HealthPassportModel? get healthPassport => _healthPassport;
  FamilyMemberModel? get activeFamilyMember => _activeFamilyMember;
  int? get playingPrescriptionId => _playingPrescriptionId;
  bool get isPlayingAudio => _isPlayingAudio;
  bool get isAudioLoading => _isAudioLoading;

  PatientPortalProvider() {
    _audioPlayer.onPlayerStateChanged.listen((state) {
      _isPlayingAudio = state == PlayerState.playing;
      if (state == PlayerState.completed || state == PlayerState.stopped) {
        _playingPrescriptionId = null;
        _isPlayingAudio = false;
      }
      notifyListeners();
    });
  }

  void setActiveFamilyMember(FamilyMemberModel? member) {
    _activeFamilyMember = member;
    notifyListeners();
    fetchHealthMetrics(familyMemberId: member?.id);
  }

  // 1. Fetch Dashboard & All Data
  Future<void> fetchAllPortalData(dynamic userId) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await Future.wait([
        fetchDashboard(),
        fetchAppointments(),
        fetchPrescriptions(),
        fetchReports(),
        fetchHealthMetrics(),
        fetchFamilyMembers(),
        if (userId != null) fetchHealthPassport(userId),
      ]);
    } catch (e) {
      _errorMessage = 'Failed to load some medical records: $e';
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchDashboard() async {
    try {
      final res = await _apiService.getPatientDashboard();
      if (res.data['stats'] is Map) {
        _stats = Map<String, dynamic>.from(res.data['stats']);
        notifyListeners();
      }
    } catch (_) {}
  }

  // 2. Appointments
  Future<void> fetchAppointments() async {
    try {
      final res = await _apiService.getPatientAppointments();
      var raw = res.data;
      List list = [];
      if (raw is Map && raw['data'] is List) {
        list = raw['data'];
      } else if (raw is List) {
        list = raw;
      }
      _appointments = list
          .map((item) => AppointmentModel.fromJson(Map<String, dynamic>.from(item)))
          .toList();
      notifyListeners();
    } catch (_) {}
  }

  // 3. Prescriptions
  Future<void> fetchPrescriptions() async {
    try {
      final res = await _apiService.getPatientPrescriptions();
      var raw = res.data;
      List list = [];
      if (raw is Map && raw['data'] is List) {
        list = raw['data'];
      } else if (raw is List) {
        list = raw;
      }
      _prescriptions = list
          .map((item) => PrescriptionModel.fromJson(Map<String, dynamic>.from(item)))
          .toList();
      notifyListeners();
    } catch (_) {}
  }

  // 4. Reports
  Future<void> fetchReports({String? testType, String? search}) async {
    try {
      final res = await _apiService.getPatientReports(testType: testType, search: search);
      if (res.data['reports'] is List) {
        final list = res.data['reports'] as List;
        _reports = list
            .map((item) => PatientReportModel.fromJson(Map<String, dynamic>.from(item)))
            .toList();
        notifyListeners();
      }
    } catch (_) {}
  }

  Future<bool> uploadReport(File file, Map<String, dynamic> meta) async {
    try {
      final res = await _apiService.uploadPatientReport(file, meta);
      if (res.data['success'] == true) {
        await fetchReports();
        return true;
      }
    } catch (_) {}
    return false;
  }

  Future<bool> deleteReport(int id) async {
    try {
      final res = await _apiService.deletePatientReport(id);
      if (res.data['success'] == true) {
        _reports.removeWhere((r) => r.id == id);
        notifyListeners();
        return true;
      }
    } catch (_) {}
    return false;
  }

  // 5. Health Metrics / Chronic Vitals
  Future<void> fetchHealthMetrics({String? metricType, dynamic familyMemberId}) async {
    try {
      final res = await _apiService.getHealthMetrics(
        metricType: metricType,
        familyMemberId: familyMemberId ?? _activeFamilyMember?.id,
      );
      if (res.data['metrics'] is List) {
        final list = res.data['metrics'] as List;
        _healthMetrics = list
            .map((item) => HealthMetricModel.fromJson(Map<String, dynamic>.from(item)))
            .toList();
        notifyListeners();
      }
    } catch (_) {}
  }

  Future<bool> storeHealthMetric(Map<String, dynamic> data) async {
    try {
      if (_activeFamilyMember != null && !data.containsKey('family_member_id')) {
        data['family_member_id'] = _activeFamilyMember!.id;
      }
      final res = await _apiService.storeHealthMetric(data);
      if (res.data['success'] == true) {
        await fetchHealthMetrics();
        return true;
      }
    } catch (_) {}
    return false;
  }

  Future<bool> deleteHealthMetric(int id) async {
    try {
      final res = await _apiService.deleteHealthMetric(id);
      if (res.data['success'] == true) {
        _healthMetrics.removeWhere((m) => m.id == id);
        notifyListeners();
        return true;
      }
    } catch (_) {}
    return false;
  }

  // 6. Family Members
  Future<void> fetchFamilyMembers() async {
    try {
      final res = await _apiService.getFamilyMembers();
      if (res.data['family_members'] is List) {
        final list = res.data['family_members'] as List;
        _familyMembers = list
            .map((item) => FamilyMemberModel.fromJson(Map<String, dynamic>.from(item)))
            .toList();
        notifyListeners();
      }
    } catch (_) {}
  }

  Future<bool> storeFamilyMember(Map<String, dynamic> data) async {
    try {
      final res = await _apiService.storeFamilyMember(data);
      if (res.data['success'] == true) {
        await fetchFamilyMembers();
        return true;
      }
    } catch (_) {}
    return false;
  }

  Future<bool> deleteFamilyMember(int id) async {
    try {
      final res = await _apiService.deleteFamilyMember(id);
      if (res.data['success'] == true) {
        _familyMembers.removeWhere((m) => m.id == id);
        if (_activeFamilyMember?.id == id) {
          _activeFamilyMember = null;
        }
        notifyListeners();
        return true;
      }
    } catch (_) {}
    return false;
  }

  // 7. Health Passport QR
  Future<void> fetchHealthPassport(dynamic userId) async {
    try {
      final res = await _apiService.getHealthPassportToken(userId);
      if (res.data['success'] == true) {
        _healthPassport = HealthPassportModel.fromJson(Map<String, dynamic>.from(res.data));
        notifyListeners();
      }
    } catch (_) {}
  }

  // 8. Audio Dosage Instruction Player (Supports both audioUrl and dynamic Bengali TTS Speech)
  Future<void> togglePrescriptionSpeech({
    required int prescriptionId,
    required String text,
    String? audioUrl,
  }) async {
    // If already playing this prescription, stop it
    if (_playingPrescriptionId == prescriptionId && _isPlayingAudio) {
      await _audioPlayer.stop();
      _isPlayingAudio = false;
      _playingPrescriptionId = null;
      notifyListeners();
      return;
    }

    try {
      _isAudioLoading = true;
      _playingPrescriptionId = prescriptionId;
      notifyListeners();

      String fullUrl;
      if (audioUrl != null && audioUrl.isNotEmpty) {
        fullUrl = audioUrl;
        if (!fullUrl.startsWith('http://') && !fullUrl.startsWith('https://')) {
          fullUrl = '${ApiConstants.baseUrl.replaceAll('/api', '')}/$audioUrl';
        }
      } else {
        // Synthesize crystal-clear Bengali audio via backend /api/ai/tts
        final encoded = Uri.encodeComponent(text.trim());
        fullUrl = '${ApiConstants.baseUrl}${ApiConstants.aiTts}?text=$encoded';
      }

      await _audioPlayer.stop();
      await _audioPlayer.play(UrlSource(fullUrl));
      _isPlayingAudio = true;
    } catch (e) {
      debugPrint('Error playing prescription speech: $e');
      _isPlayingAudio = false;
      _playingPrescriptionId = null;
    } finally {
      _isAudioLoading = false;
      notifyListeners();
    }
  }

  Future<void> toggleAudioPlayback(int prescriptionId, String? audioUrl) async {
    await togglePrescriptionSpeech(
      prescriptionId: prescriptionId,
      text: 'প্রেসক্রিপশন অনুযায়ী নিয়মিত ওষুধ সেবন করুন।',
      audioUrl: audioUrl,
    );
  }

  @override
  void dispose() {
    _audioPlayer.dispose();
    super.dispose();
  }
}
