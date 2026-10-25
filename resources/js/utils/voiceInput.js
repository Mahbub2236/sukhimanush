// Web Speech API Voice Dictation & Text-to-Speech Engine for Sukhi Manush (সুখী মানুষ) Healthcare
class SpeechService {
  constructor() {
    this.recognition = null;
    this.isListening = false;
    this.currentLanguage = 'bn-BD'; // Default to Bengali
    this.currentAudio = null;
  }

  isSupported() {
    return typeof window !== 'undefined' && !!(window.SpeechRecognition || window.webkitSpeechRecognition);
  }

  isTtsSupported() {
    return typeof window !== 'undefined' && !!window.speechSynthesis;
  }

  startListening({
    lang = 'bn-BD',
    continuous = false,
    interimResults = true,
    onStart = () => {},
    onResult = () => {},
    onInterim = () => {},
    onEnd = () => {},
    onError = () => {},
  } = {}) {
    if (!this.isSupported()) {
      onError({ 
        error: 'not-supported', 
        message: 'আপনার ব্রাউজারে ভয়েস রিকগনিশন সাপোর্ট করে না। অনুগ্রহ করে Google Chrome বা Microsoft Edge ব্রাউজার ব্যবহার করুন।' 
      });
      return null;
    }

    if (this.isListening) {
      this.stopListening();
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    this.recognition = new SpeechRecognition();
    this.recognition.lang = lang;
    this.recognition.continuous = continuous;
    this.recognition.interimResults = interimResults;
    this.recognition.maxAlternatives = 1;

    this.recognition.onstart = () => {
      this.isListening = true;
      onStart();
    };

    this.recognition.onresult = (event) => {
      let interimTranscript = '';
      let finalTranscript = '';

      for (let i = event.resultIndex; i < event.results.length; ++i) {
        if (event.results[i].isFinal) {
          finalTranscript += event.results[i][0].transcript;
        } else {
          interimTranscript += event.results[i][0].transcript;
        }
      }

      if (interimTranscript) {
        onInterim(interimTranscript);
      }
      if (finalTranscript) {
        onResult(finalTranscript.trim());
      }
    };

    this.recognition.onerror = (event) => {
      console.warn('Speech recognition error:', event.error);
      this.isListening = false;
      let msg = 'ভয়েস সনাক্ত করা যায়নি, আবার চেষ্টা করুন।';
      if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
        msg = 'মাইক্রোফোন পারমিশন প্রয়োজন। ব্রাউজারের ঠিকানা বারে গিয়ে মাইক্রোফোন পারমিশন Allow করুন।';
      } else if (event.error === 'no-speech') {
        msg = 'কোনো কথা শোনা যায়নি। অনুগ্রহ করে মাইক্রোফোনের কাছে কথা বলুন।';
      } else if (event.error === 'audio-capture') {
        msg = 'মাইক্রোফোন পাওয়া যায়নি। মাইক্রোফোন সংযোগ পরীক্ষা করুন।';
      }
      onError({ error: event.error, message: msg });
    };

    this.recognition.onend = () => {
      this.isListening = false;
      onEnd();
    };

    try {
      this.recognition.start();
    } catch (e) {
      console.error('Failed to start speech recognition:', e);
      this.isListening = false;
      onError({ error: 'start-failed', message: e.message });
    }

    return this.recognition;
  }

  stopListening() {
    if (this.recognition && this.isListening) {
      try {
        this.recognition.stop();
      } catch (e) {}
    }
    this.isListening = false;
  }

  // Convert Bengali numerals or words to standard digits if input is phone or number
  convertToDigits(text) {
    if (!text) return '';
    const wordMap = {
      'জিরো': '0', 'শূন্য': '0', 'শুন্য': '0', 'zero': '0',
      'এক': '1', 'ওয়ান': '1', 'one': '1',
      'দুই': '2', 'টু': '2', 'two': '2',
      'তিন': '3', 'থ্রি': '3', 'three': '3',
      'চার': '4', 'ফোর': '4', 'four': '4',
      'পাঁচ': '5', 'পাচ': '5', 'ফাইভ': '5', 'five': '5',
      'ছয়': '6', 'ছয়': '6', 'সিক্স': '6', 'six': '6',
      'সাত': '7', 'সেভেন': '7', 'seven': '7',
      'আট': '8', 'এইট': '8', 'eight': '8',
      'নয়': '9', 'নয়': '9', 'নাইন': '9', 'nine': '9',
      '০': '0', '১': '1', '২': '2', '৩': '3', '৪': '4',
      '৫': '5', '৬': '6', '৭': '7', '৮': '8', '৯': '9'
    };

    let result = text.toLowerCase();
    for (const [word, digit] of Object.entries(wordMap)) {
      result = result.split(word).join(digit);
    }
    // Remove non-digit characters except + for phone numbers
    return result.replace(/[^\d+]/g, '');
  }

  // Convert spoken English/Bengali speech to valid email format
  convertToEmail(text) {
    if (!text) return '';
    const wordMap = {
      'জিরো': '0', 'শূন্য': '0', 'শুন্য': '0', 'zero': '0',
      'এক': '1', 'ওয়ান': '1', 'one': '1',
      'দুই': '2', 'টু': '2', 'two': '2',
      'তিন': '3', 'থ্রি': '3', 'three': '3',
      'চার': '4', 'ফোর': '4', 'four': '4',
      'পাঁচ': '5', 'পাচ': '5', 'ফাইভ': '5', 'five': '5',
      'ছয়': '6', 'ছয়': '6', 'সিক্স': '6', 'six': '6',
      'সাত': '7', 'সেভেন': '7', 'seven': '7',
      'আট': '8', 'এইট': '8', 'eight': '8',
      'নয়': '9', 'নয়': '9', 'নাইন': '9', 'nine': '9',
      '০': '0', '১': '1', '২': '2', '৩': '3', '৪': '4',
      '৫': '5', '৬': '6', '৭': '7', '৮': '8', '৯': '9'
    };

    let result = text.toLowerCase().trim();
    for (const [word, digit] of Object.entries(wordMap)) {
      result = result.split(word).join(digit);
    }

    // Convert spoken email symbols (at, dot, dash, underscore)
    result = result
      .replace(/\s*(এট|অ্যাট|@|at the rate|at)\s*/gi, '@')
      .replace(/\s*(ডট|point|dot|\.)\s*/gi, '.')
      .replace(/\s*(আন্ডারস্কোর|underscore|_)\s*/gi, '_')
      .replace(/\s*(হাইফেন|hyphen|dash|-)\s*/gi, '-')
      .replace(/\s+/g, '');

    return result;
  }

  // Text-to-Speech (TTS) for illiterate/rural patients to hear medical guidance
  speak(text, { lang = 'bn-BD', rate = 0.95, onStart = () => {}, onEnd = () => {} } = {}) {
    this.stopSpeaking();

    // Clean formatting characters from text for natural speech
    const cleanText = text
      .replace(/[#*_`~>[\]()]/g, ' ')
      .replace(/https?:\/\/\S+/g, ' ')
      .replace(/[🚨🩺🏥💡✨⚠️📍\n\r]/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();

    if (!cleanText) {
      onEnd();
      return;
    }

    // Check if browser has a native Bengali voice
    let hasNativeBnVoice = false;
    if (this.isTtsSupported()) {
      const voices = window.speechSynthesis.getVoices();
      const bnVoice = voices.find(v => v.lang && (v.lang.toLowerCase().includes('bn') || v.lang.toLowerCase().includes('bangla')));
      if (bnVoice) {
        hasNativeBnVoice = true;
      }
    }

    // If native Bengali voice exists in system, use client SpeechSynthesis
    if (hasNativeBnVoice && this.isTtsSupported()) {
      const utterance = new SpeechSynthesisUtterance(cleanText);
      utterance.lang = lang;
      utterance.rate = rate;
      const voices = window.speechSynthesis.getVoices();
      const bnVoice = voices.find(v => v.lang && (v.lang.toLowerCase().includes('bn') || v.lang.toLowerCase().includes('bangla')));
      if (bnVoice) utterance.voice = bnVoice;

      utterance.onstart = onStart;
      utterance.onend = onEnd;
      utterance.onerror = () => onEnd();

      window.speechSynthesis.speak(utterance);
      return;
    }

    // High-fidelity Server-side Bengali Audio Streaming (100% audible on Windows/Mac/Mobile)
    try {
      const audioUrl = `/api/ai/tts?text=${encodeURIComponent(cleanText.slice(0, 500))}`;
      const audio = new Audio(audioUrl);
      this.currentAudio = audio;

      audio.onplay = () => {
        onStart();
      };
      audio.onended = () => {
        this.currentAudio = null;
        onEnd();
      };
      audio.onerror = (err) => {
        console.warn('Server TTS stream failed, attempting Web Speech fallback:', err);
        this.currentAudio = null;
        if (this.isTtsSupported()) {
          const utterance = new SpeechSynthesisUtterance(cleanText);
          utterance.lang = lang;
          utterance.onstart = onStart;
          utterance.onend = onEnd;
          utterance.onerror = () => onEnd();
          window.speechSynthesis.speak(utterance);
        } else {
          onEnd();
        }
      };

      const playPromise = audio.play();
      if (playPromise !== undefined) {
        playPromise.catch((e) => {
          console.warn('Audio play prevented or interrupted:', e);
          this.currentAudio = null;
          onEnd();
        });
      }
    } catch (e) {
      console.error('TTS playback error:', e);
      this.currentAudio = null;
      onEnd();
    }
  }

  stopSpeaking() {
    if (this.currentAudio) {
      try {
        this.currentAudio.pause();
        this.currentAudio.currentTime = 0;
      } catch (e) {}
      this.currentAudio = null;
    }
    if (this.isTtsSupported()) {
      try {
        window.speechSynthesis.cancel();
      } catch (e) {}
    }
  }

  isSpeaking() {
    if (this.currentAudio && !this.currentAudio.paused) return true;
    return this.isTtsSupported() && window.speechSynthesis.speaking;
  }
}

export const speechService = new SpeechService();
export const isSpeechSupported = () => speechService.isSupported();
export const startVoiceRecognition = (options) => speechService.startListening(options);
export const stopVoiceRecognition = () => speechService.stopListening();
export const speakText = (text, options) => speechService.speak(text, options);
export default speechService;
