import 'package:flutter/foundation.dart';
import 'package:speech_to_text/speech_to_text.dart' as stt;

class VoiceService {
  static final VoiceService _instance = VoiceService._internal();
  factory VoiceService() => _instance;

  final stt.SpeechToText _speech = stt.SpeechToText();
  bool _isInitialized = false;

  VoiceService._internal();

  bool get isListening => _speech.isListening;
  bool get isAvailable => _isInitialized;

  Future<bool> init() async {
    if (_isInitialized) return true;
    try {
      _isInitialized = await _speech.initialize(
        onError: (val) => debugPrint('Speech error: $val'),
        onStatus: (val) => debugPrint('Speech status: $val'),
      );
      return _isInitialized;
    } catch (e) {
      debugPrint('Speech init failed: $e');
      _isInitialized = false;
      return false;
    }
  }

  Future<void> startListening({
    required Function(String recognizedWords) onResult,
    String? localeId,
  }) async {
    bool available = await init();
    if (!available) {
      onResult('');
      return;
    }

    try {
      await _speech.listen(
        onResult: (result) {
          if (result.recognizedWords.isNotEmpty) {
            onResult(result.recognizedWords);
          }
        },
        localeId: localeId ?? 'bn_BD', // Default Bengali (BD), fallback to browser/system
        listenMode: stt.ListenMode.confirmation,
        cancelOnError: true,
      );
    } catch (e) {
      debugPrint('Error starting speech listening: $e');
    }
  }

  Future<void> stopListening() async {
    if (_speech.isListening) {
      await _speech.stop();
    }
  }
}
