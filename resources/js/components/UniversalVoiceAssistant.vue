<template>
  <div class="universal-voice-container">
    <!-- Active Recording Floating Overlay Card (Follows the active input) -->
    <teleport to="body">
      <transition name="voice-pop">
        <div 
          v-if="isListening && activeElement" 
          class="universal-voice-recording-card shadow-lg"
          :style="recordingCardStyle"
          @mousedown.stop
        >
          <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
            <div class="d-flex align-items-center">
              <span class="voice-pulse-ring mr-2"></span>
              <strong class="font-12 text-danger font-weight-bold">
                শুনছি... (বাংলায় বলুন)
              </strong>
            </div>
            <div class="d-flex align-items-center" style="gap: 4px;">
              <span class="badge badge-light border text-muted" style="font-size: 10px;">
                {{ isNumericField ? 'সংখ্যা/নাম্বার মোড' : 'বাংলা টেক্সট' }}
              </span>
              <button 
                type="button" 
                class="btn btn-sm btn-light py-0 px-1 text-muted" 
                style="font-size: 11px; line-height: 1;"
                @click="stopRecording(false)"
                title="বাতিল করুন"
              >
                ✕
              </button>
            </div>
          </div>

          <!-- Interim Live Transcript Display -->
          <div class="voice-transcript-box">
            <span v-if="interimTranscript" class="text-dark font-weight-bold">
              {{ interimTranscript }}
            </span>
            <span v-else class="text-muted font-italic">
              মাইক্রোফোনে কথা বলুন, লেখা স্বয়ংক্রিয়ভাবে টাইপ হবে...
            </span>
          </div>

          <!-- Audio Equalizer Bars Animation -->
          <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
            <div class="voice-wave-eq">
              <span class="wave-bar bar-1"></span>
              <span class="wave-bar bar-2"></span>
              <span class="wave-bar bar-3"></span>
              <span class="wave-bar bar-4"></span>
              <span class="wave-bar bar-5"></span>
            </div>
            <button 
              type="button" 
              class="btn btn-sm btn-success py-1 px-3 rounded-pill font-weight-bold shadow-sm"
              style="font-size: 11px;"
              @click="stopRecording(true)"
            >
              ✓ সম্পন্ন করুন
            </button>
          </div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script>
import speechService from '../utils/voiceInput';

export default {
  name: 'UniversalVoiceAssistant',
  data() {
    return {
      isListening: false,
      activeElement: null,
      interimTranscript: '',
      finalTranscript: '',
      initialValue: '',
      badgeMinimized: true,
      activeInputRect: null,
      resizeObserver: null,
    };
  },
  computed: {
    isNumericField() {
      if (!this.activeElement) return false;
      const type = (this.activeElement.getAttribute('type') || '').toLowerCase();
      const name = (this.activeElement.getAttribute('name') || '').toLowerCase();
      const placeholder = (this.activeElement.getAttribute('placeholder') || '').toLowerCase();
      return type === 'tel' || type === 'number' || name.includes('phone') || name.includes('mobile') || placeholder.includes('01') || placeholder.includes('ফোন');
    },
    recordingCardStyle() {
      if (!this.activeInputRect) {
        return { bottom: '80px', right: '20px', position: 'fixed' };
      }

      const rect = this.activeInputRect;
      const cardWidth = 320;
      let left = rect.left;
      
      // Prevent overflow on right side
      if (left + cardWidth > window.innerWidth - 15) {
        left = window.innerWidth - cardWidth - 15;
      }
      if (left < 15) left = 15;

      let top = rect.bottom + window.scrollY + 6;
      // If bottom goes below viewport, show above the input
      if (rect.bottom + 140 > window.innerHeight && rect.top > 140) {
        top = rect.top + window.scrollY - 130;
      }

      return {
        position: 'absolute',
        top: `${top}px`,
        left: `${left}px`,
        width: `${cardWidth}px`,
        zIndex: 99999,
      };
    }
  },
  mounted() {
    this.injectUniversalVoiceStyles();
    this.attachGlobalListeners();
  },
  beforeUnmount() {
    this.removeGlobalListeners();
    if (this.isListening) {
      speechService.stopListening();
    }
  },
  methods: {
    injectUniversalVoiceStyles() {
      if (document.getElementById('universal-voice-styles')) return;

      const style = document.createElement('style');
      style.id = 'universal-voice-styles';
      style.textContent = `
        /* Universal Mic Icon Inside Text Inputs & Textareas WITHOUT existing voice buttons */
        input[type="text"]:not([data-no-voice]):not(.no-voice):not([readonly]):not([disabled]),
        input[type="search"]:not([data-no-voice]):not(.no-voice):not([readonly]):not([disabled]),
        input[type="tel"]:not([data-no-voice]):not(.no-voice):not([readonly]):not([disabled]),
        input[type="email"]:not([data-no-voice]):not(.no-voice):not([readonly]):not([disabled]),
        input[type="number"]:not([data-no-voice]):not(.no-voice):not([readonly]):not([disabled]),
        input:not([type]):not([data-no-voice]):not(.no-voice):not([readonly]):not([disabled]),
        textarea:not([data-no-voice]):not(.no-voice):not([readonly]):not([disabled]) {
          padding-right: 36px !important;
          background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238896a6' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z'/%3E%3Cpath d='M19 10v2a7 7 0 0 1-14 0v-2'/%3E%3Cline x1='12' y1='19' x2='12' y2='23'/%3E%3Cline x1='8' y1='23' x2='16' y2='23'/%3E%3C/svg%3E");
          background-repeat: no-repeat !important;
          background-position: right 10px center !important;
          background-size: 16px 16px !important;
          transition: border-color 0.2s ease, box-shadow 0.2s ease, background-image 0.2s ease !important;
        }

        /* Prevent double icons anywhere custom voice buttons, input groups or embedded buttons are present */
        input[data-no-voice],
        textarea[data-no-voice],
        .no-voice,
        .input-group > input,
        .input-group > textarea,
        .has-voice-btn input,
        .has-voice-btn textarea,
        *:has(> .btn-voice-embedded) > input,
        *:has(> [class*="fa-microphone"]) > input,
        *:has(> .btn-voice-embedded) > textarea,
        *:has(> .btn-voice-embedded-topright) > textarea,
        *:has(> [class*="fa-microphone"]) > textarea,
        *:has(> [class*="icofont-mic"]) > textarea {
          background-image: none !important;
          padding-right: inherit !important;
        }

        textarea:not([data-no-voice]):not(.no-voice):not([readonly]):not([disabled]) {
          background-position: right 10px top 10px !important;
        }

        /* Hover over the mic zone */
        .voice-mic-hover-active {
          background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23e12454' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z'/%3E%3Cpath d='M19 10v2a7 7 0 0 1-14 0v-2'/%3E%3Cline x1='12' y1='19' x2='12' y2='23'/%3E%3Cline x1='8' y1='23' x2='16' y2='23'/%3E%3C/svg%3E") !important;
        }

        /* Currently Recording on this field */
        .voice-field-active-recording {
          border-color: #e12454 !important;
          box-shadow: 0 0 0 3px rgba(225, 36, 84, 0.25) !important;
          background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%23e12454' stroke='%23e12454' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z'/%3E%3Cpath d='M19 10v2a7 7 0 0 1-14 0v-2'/%3E%3Cline x1='12' y1='19' x2='12' y2='23'/%3E%3Cline x1='8' y1='23' x2='16' y2='23'/%3E%3C/svg%3E") !important;
          animation: pulse-border-field 1.5s infinite;
        }

        @keyframes pulse-border-field {
          0% { box-shadow: 0 0 0 0 rgba(225, 36, 84, 0.4); }
          70% { box-shadow: 0 0 0 6px rgba(225, 36, 84, 0); }
          100% { box-shadow: 0 0 0 0 rgba(225, 36, 84, 0); }
        }

        /* Floating Recording Card */
        .universal-voice-recording-card {
          background: #ffffff;
          border-radius: 12px;
          border: 1.5px solid #e12454;
          padding: 10px 14px;
          font-family: inherit;
        }

        .voice-pulse-ring {
          display: inline-block;
          width: 10px;
          height: 10px;
          border-radius: 50%;
          background: #e12454;
          box-shadow: 0 0 0 0 rgba(225, 36, 84, 0.7);
          animation: pulse-ring-dot 1.2s infinite;
        }

        @keyframes pulse-ring-dot {
          0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(225, 36, 84, 0.7); }
          70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(225, 36, 84, 0); }
          100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(225, 36, 84, 0); }
        }

        .voice-transcript-box {
          min-height: 38px;
          max-height: 80px;
          overflow-y: auto;
          font-size: 13px;
          padding: 4px 6px;
          background: #fbfcfe;
          border-radius: 6px;
          border: 1px dashed #d1d5db;
          line-height: 1.4;
        }

        .voice-wave-eq {
          display: flex;
          align-items: center;
          gap: 3px;
          height: 16px;
        }
        .wave-bar {
          width: 3px;
          background-color: #e12454;
          border-radius: 2px;
          animation: wave-bar-anim 1s infinite ease-in-out;
        }
        .bar-1 { height: 6px; animation-delay: 0.1s; }
        .bar-2 { height: 14px; animation-delay: 0.3s; }
        .bar-3 { height: 18px; animation-delay: 0.2s; }
        .bar-4 { height: 10px; animation-delay: 0.4s; }
        .bar-5 { height: 8px; animation-delay: 0.15s; }

        @keyframes wave-bar-anim {
          0%, 100% { transform: scaleY(0.4); }
          50% { transform: scaleY(1.3); }
        }

        /* Bottom Floating Indicator Badge */
        .universal-voice-indicator-badge {
          position: fixed;
          bottom: 20px;
          left: 20px;
          background: #ffffff;
          border: 1px solid #e2e8f0;
          border-radius: 30px;
          z-index: 9998;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @media (max-width: 1023.98px) {
          .universal-voice-indicator-badge {
            bottom: 75px !important;
            left: 16px !important;
          }
        }
        .universal-voice-indicator-badge.badge-recording {
          background: #e12454;
          color: #ffffff !important;
          border-color: #e12454;
          box-shadow: 0 4px 15px rgba(225, 36, 84, 0.4);
        }
        .btn-badge-close {
          background: transparent;
          border: none;
          color: #94a3b8;
          font-size: 14px;
          cursor: pointer;
          padding: 0 4px;
          line-height: 1;
        }
        .btn-badge-close:hover {
          color: #e12454;
        }
        .btn-badge-mini {
          width: 36px;
          height: 36px;
          border-radius: 50%;
          background: #ffffff;
          border: 1px solid #cbd5e1;
          color: #e12454;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 18px;
          cursor: pointer;
          box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .btn-badge-mini:hover {
          background: #fdf2f4;
          transform: scale(1.08);
        }

        /* Transitions */
        .voice-pop-enter-active, .voice-pop-leave-active {
          transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .voice-pop-enter-from, .voice-pop-leave-to {
          opacity: 0;
          transform: scale(0.92) translateY(-6px);
        }
      `;
      document.head.appendChild(style);
    },
    attachGlobalListeners() {
      this.handleGlobalClick = this.onGlobalClick.bind(this);
      this.handleGlobalMouseMove = this.onGlobalMouseMove.bind(this);
      this.handleGlobalKeyDown = this.onGlobalKeyDown.bind(this);

      document.addEventListener('click', this.handleGlobalClick, true);
      document.addEventListener('mousemove', this.handleGlobalMouseMove, true);
      document.addEventListener('keydown', this.handleGlobalKeyDown, true);
    },
    removeGlobalListeners() {
      document.removeEventListener('click', this.handleGlobalClick, true);
      document.removeEventListener('mousemove', this.handleGlobalMouseMove, true);
      document.removeEventListener('keydown', this.handleGlobalKeyDown, true);
    },
    isEligibleInput(target) {
      if (!target || !(target instanceof HTMLElement)) return false;
      const tag = target.tagName.toLowerCase();
      if (tag !== 'input' && tag !== 'textarea') return false;

      // Check if explicitly disabled or marked as no-voice
      if (
        target.hasAttribute('data-no-voice') || 
        target.classList.contains('no-voice') || 
        target.closest('[data-no-voice]') || 
        target.closest('.no-voice')
      ) {
        return false;
      }

      // If inside an input group with attached buttons, skip
      if (target.closest('.input-group')) {
        return false;
      }

      // If container already contains an embedded or dedicated voice button, skip
      const parent = target.parentElement;
      if (parent && (
        parent.querySelector('.btn-voice-embedded') ||
        parent.querySelector('[class*="fa-microphone"]') ||
        parent.querySelector('[class*="icofont-mic"]') ||
        parent.querySelector('button[title*="মুখে"]') ||
        parent.querySelector('button[title*="ভয়েস"]') ||
        parent.querySelector('button[title*="voice"]')
      )) {
        return false;
      }

      if (target.hasAttribute('readonly') || target.hasAttribute('disabled')) {
        return false;
      }

      if (tag === 'textarea') return true;

      const type = (target.getAttribute('type') || 'text').toLowerCase();
      const disallowed = ['password', 'hidden', 'checkbox', 'radio', 'file', 'date', 'time', 'color', 'range', 'submit', 'button'];
      return !disallowed.includes(type);
    },
    isClickInMicZone(e, target) {
      const rect = target.getBoundingClientRect();
      const clickX = e.clientX;
      const clickY = e.clientY;

      // The rightmost 38px of the element
      const isInX = clickX >= (rect.right - 38) && clickX <= rect.right;
      let isInY = false;

      if (target.tagName.toLowerCase() === 'textarea') {
        // For textarea, mic icon is at top-right
        isInY = clickY >= rect.top && clickY <= (rect.top + 38);
      } else {
        // For standard input, entire vertical height
        isInY = clickY >= rect.top && clickY <= rect.bottom;
      }

      return isInX && isInY;
    },
    onGlobalMouseMove(e) {
      const target = e.target;
      if (!this.isEligibleInput(target)) return;

      if (this.isClickInMicZone(e, target)) {
        target.classList.add('voice-mic-hover-active');
        target.style.cursor = 'pointer';
        target.setAttribute('title', 'মাইক্রোফোনে বাংলায় মুখে বলুন (Click to Voice Type)');
      } else {
        target.classList.remove('voice-mic-hover-active');
        target.style.cursor = '';
        if (target.getAttribute('title') === 'মাইক্রোফোনে বাংলায় মুখে বলুন (Click to Voice Type)') {
          target.removeAttribute('title');
        }
      }
    },
    onGlobalClick(e) {
      const target = e.target;
      if (!this.isEligibleInput(target)) return;

      if (this.isClickInMicZone(e, target)) {
        e.preventDefault();
        e.stopPropagation();

        if (this.isListening && this.activeElement === target) {
          this.stopRecording(true);
        } else {
          this.startRecording(target);
        }
      }
    },
    onGlobalKeyDown(e) {
      // Shortcut: Alt + V triggers voice on active focused input
      if (e.altKey && e.key.toLowerCase() === 'v') {
        const active = document.activeElement;
        if (this.isEligibleInput(active)) {
          e.preventDefault();
          if (this.isListening && this.activeElement === active) {
            this.stopRecording(true);
          } else {
            this.startRecording(active);
          }
        }
      }
    },
    startRecording(element) {
      if (this.isListening) {
        this.stopRecording(true);
      }

      this.activeElement = element;
      this.initialValue = element.value || '';
      this.interimTranscript = '';
      this.finalTranscript = '';
      this.activeInputRect = element.getBoundingClientRect();

      element.classList.add('voice-field-active-recording');
      element.focus();

      speechService.startListening({
        lang: 'bn-BD',
        onStart: () => {
          this.isListening = true;
        },
        onInterim: (text) => {
          this.interimTranscript = text;
          this.applyToField(text, false);
        },
        onResult: (text) => {
          this.finalTranscript = text;
          this.applyToField(text, true);
        },
        onEnd: () => {
          this.finishRecording();
        },
        onError: (err) => {
          console.warn('Universal Voice Assistant Error:', err);
          this.finishRecording();
        }
      });
    },
    applyToField(text, isFinal) {
      if (!this.activeElement) return;

      let processed = text;
      if (this.isNumericField) {
        processed = speechService.convertToDigits(text);
      }

      let newValue = '';
      if (this.initialValue) {
        newValue = this.initialValue + ' ' + processed;
      } else {
        newValue = processed;
      }

      this.activeElement.value = newValue;

      // Fire HTML5 input and change events with bubbles so Vue 3 v-model updates instantly
      this.activeElement.dispatchEvent(new Event('input', { bubbles: true }));
      if (isFinal) {
        this.activeElement.dispatchEvent(new Event('change', { bubbles: true }));
      }
    },
    stopRecording(save = true) {
      speechService.stopListening();
      if (!save && this.activeElement) {
        this.activeElement.value = this.initialValue;
        this.activeElement.dispatchEvent(new Event('input', { bubbles: true }));
      }
      this.finishRecording();
    },
    finishRecording() {
      this.isListening = false;
      if (this.activeElement) {
        this.activeElement.classList.remove('voice-field-active-recording');
        this.activeElement = null;
      }
      this.interimTranscript = '';
      this.activeInputRect = null;
    }
  }
};
</script>
