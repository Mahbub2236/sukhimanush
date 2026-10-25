<template>
  <button 
    type="button" 
    :class="computedClasses"
    :title="isListening ? 'রেকর্ডিং বন্ধ করতে ক্লিক করুন' : (title || 'মুখে বলুন (Voice Input)')"
    @click="toggleListen"
    style="user-select: none; transition: all 0.25s ease;"
  >
    <!-- Non-embedded mode listening spinner -->
    <span v-if="isListening && !embedded" class="spinner-grow spinner-grow-sm mr-1 text-danger" role="status" style="width: 8px; height: 8px;"></span>
    
    <!-- Icons -->
    <i 
      v-if="embedded" 
      :class="isListening ? 'fa-solid fa-volume-high text-xs animate-bounce' : 'fa-solid fa-microphone text-sm'"
    ></i>
    <i 
      v-else 
      :class="isListening ? 'fa-solid fa-volume-high text-danger' : 'fa-solid fa-microphone'"
    ></i>

    <!-- Label (only shown if not embedded) -->
    <span v-if="showLabel && !embedded" class="ml-1 font-weight-bold" style="font-size: 11.5px;">
      {{ isListening ? 'শুনছি...' : label }}
    </span>
  </button>
</template>

<script>
import speechService from '../utils/voiceInput';

export default {
  name: 'VoiceInputBtn',
  props: {
    embedded: {
      type: Boolean,
      default: false
    },
    topRight: {
      type: Boolean,
      default: false
    },
    lang: {
      type: String,
      default: 'bn-BD'
    },
    asDigits: {
      type: Boolean,
      default: false
    },
    asEmail: {
      type: Boolean,
      default: false
    },
    label: {
      type: String,
      default: 'মুখে বলুন'
    },
    showLabel: {
      type: Boolean,
      default: true
    },
    title: {
      type: String,
      default: 'মাইক্রোফোনে বাংলায় মুখে বলুন'
    },
    customClass: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      isListening: false,
    };
  },
  computed: {
    computedClasses() {
      if (this.customClass) {
        return this.customClass + (this.isListening ? ' pulse-voice-active' : '');
      }
      if (this.embedded) {
        const base = this.topRight ? 'btn-voice-embedded-topright' : 'btn-voice-embedded';
        return `${base} ${this.isListening ? 'voice-embedded-active' : 'voice-embedded-idle'}`;
      }
      return this.isListening 
        ? 'btn btn-sm btn-danger text-white pulse-voice-active py-0 px-2 rounded-pill shadow-sm d-inline-flex align-items-center font-weight-bold'
        : 'btn btn-sm btn-outline-danger py-0 px-2 rounded-pill small font-weight-bold d-inline-flex align-items-center';
    }
  },
  beforeUnmount() {
    if (this.isListening) {
      speechService.stopListening();
    }
  },
  methods: {
    processText(text) {
      if (this.asEmail) {
        return speechService.convertToEmail(text);
      }
      if (this.asDigits) {
        return speechService.convertToDigits(text);
      }
      return text;
    },
    toggleListen() {
      if (this.isListening) {
        speechService.stopListening();
        this.isListening = false;
        this.$emit('listening', false);
        return;
      }

      speechService.startListening({
        lang: this.lang,
        onStart: () => {
          this.isListening = true;
          this.$emit('listening', true);
        },
        onInterim: (text) => {
          this.$emit('interim', this.processText(text));
        },
        onResult: (text) => {
          this.$emit('result', this.processText(text));
        },
        onEnd: () => {
          this.isListening = false;
          this.$emit('listening', false);
        },
        onError: (err) => {
          this.isListening = false;
          this.$emit('listening', false);
          this.$emit('error', err);
        }
      });
    }
  }
};
</script>

<style scoped>
/* Embedded inside single-line input field */
.btn-voice-embedded {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  z-index: 10;
  outline: none !important;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Embedded inside multiline textarea */
.btn-voice-embedded-topright {
  position: absolute;
  right: 8px;
  top: 8px;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  z-index: 10;
  outline: none !important;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.voice-embedded-idle {
  color: #94a3b8;
}
.voice-embedded-idle:hover {
  color: #e12454;
  background: #ffe4e6;
  transform: translateY(-50%) scale(1.08);
}
.btn-voice-embedded-topright.voice-embedded-idle:hover {
  transform: scale(1.08);
}

.voice-embedded-active {
  color: #ffffff !important;
  background: #e12454 !important;
  box-shadow: 0 4px 12px rgba(225, 36, 84, 0.45);
  animation: pulse-mic-ring 1.5s infinite;
}

.pulse-voice-active {
  animation: pulse-mic-ring 1.5s infinite;
  background-color: #e12454 !important;
  border-color: #e12454 !important;
  color: #fff !important;
}

@keyframes pulse-mic-ring {
  0% {
    box-shadow: 0 0 0 0 rgba(225, 36, 84, 0.7);
  }
  70% {
    box-shadow: 0 0 0 8px rgba(225, 36, 84, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(225, 36, 84, 0);
  }
}
</style>
