<template>
  <div v-if="shouldShowWidget" class="fixed bottom-20 lg:bottom-6 right-5 z-50 font-sans">
    <!-- Floating Trigger Button -->
    <button 
      v-if="!isOpen" 
      type="button"
      class="flex items-center gap-3 px-3 py-2 sm:px-4 sm:py-2.5 rounded-full text-white shadow-2xl transition-all duration-300 hover:scale-105 active:scale-95 group focus:outline-none border border-white/20"
      @click="toggleChat"
      title="সুখী মানুষ এআই স্বাস্থ্য সহকারী"
      style="background: linear-gradient(135deg, #0f172a 0%, #0284c7 50%, #e11d48 100%) !important; border-radius: 50px !important;"
    >
      <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white shrink-0 shadow-inner">
        <i class="fa-solid fa-robot text-base text-white"></i>
      </div>
      <div class="text-left hidden sm:block">
        <div class="text-xs font-extrabold text-white leading-tight" style="color: #ffffff !important;">
          ✨ এআই ডাক্তার পরামর্শক
        </div>
        <div class="text-[10px] text-sky-200 leading-tight font-medium mt-0.5" style="color: #bae6fd !important;">
          লক্ষণ লিখে সমাধান নিন
        </div>
      </div>
      <span class="relative flex h-2.5 w-2.5 ml-1 shrink-0">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400"></span>
      </span>
    </button>

    <!-- Floating Chat Window -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform opacity-0 scale-95 translate-y-4"
      enter-to-class="transform opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform opacity-100 scale-100 translate-y-0"
      leave-to-class="transform opacity-0 scale-95 translate-y-4"
    >
      <div 
        v-if="isOpen" 
        class="w-[360px] max-w-[92vw] h-[520px] max-h-[85vh] rounded-3xl shadow-2xl border border-slate-200 bg-white flex flex-col overflow-hidden"
      >
        <!-- Header -->
        <div class="p-4 bg-gradient-to-r from-slate-900 via-slate-800 to-sky-900 text-white flex items-center justify-between shrink-0 shadow-md">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white text-base">
              <i class="fa-solid fa-stethoscope text-sky-300"></i>
            </div>
            <div>
              <div class="font-extrabold text-sm text-white leading-none">সুখী মানুষ AI Assistant</div>
              <div class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>অনলাইন &bull; বাংলায় স্বাস্থ্য তথ্য</span>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-1">
            <router-link 
              to="/ai-assistant" 
              class="w-7 h-7 rounded-lg hover:bg-white/10 flex items-center justify-center text-white/80 hover:text-white transition"
              title="পূর্ণ স্ক্রিনে খুলুন" 
              @click="isOpen = false"
            >
              <i class="fa-solid fa-up-right-from-square text-xs"></i>
            </router-link>
            <button 
              type="button" 
              class="w-7 h-7 rounded-lg hover:bg-white/10 flex items-center justify-center text-white/80 hover:text-white transition"
              @click="toggleChat" 
              title="মিনিমাইজ করুন"
            >
              <i class="fa-solid fa-xmark text-sm"></i>
            </button>
          </div>
        </div>

        <!-- Quick Question Chips -->
        <div class="px-3 py-2 bg-slate-50 border-b border-slate-100 flex gap-1.5 overflow-x-auto shrink-0 scrollbar-none">
          <button 
            v-for="chip in miniChips" 
            :key="chip.id" 
            type="button"
            class="px-2.5 py-1 rounded-full bg-white border border-slate-200/80 hover:border-sky-500 text-[11px] font-medium text-slate-700 hover:text-sky-600 whitespace-nowrap shadow-2xs transition shrink-0"
            @click="sendQuick(chip.query)"
          >
            {{ chip.icon }} {{ chip.label }}
          </button>
        </div>

        <!-- Chat Stream Body -->
        <div ref="chatBody" class="flex-1 p-4 overflow-y-auto space-y-4 bg-slate-50/50 text-xs">
          <div v-for="(msg, i) in messages" :key="i">
            <!-- User Bubble -->
            <div v-if="msg.sender === 'user'" class="flex justify-end">
              <div class="max-w-[85%] rounded-2xl rounded-tr-xs bg-slate-900 text-white p-3 shadow-xs">
                <p class="leading-relaxed" style="color: #ffffff !important;">{{ msg.text }}</p>
                <span class="block text-[9px] text-slate-400 text-right mt-1">{{ msg.time }}</span>
              </div>
            </div>

            <!-- AI Bubble -->
            <div v-else class="flex items-start gap-2.5">
              <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                <i class="fa-solid fa-robot text-xs"></i>
              </div>
              <div class="max-w-[88%] rounded-2xl rounded-tl-xs bg-white border border-slate-200/80 p-3.5 shadow-xs text-slate-800 space-y-2">
                <!-- Emergency Alert -->
                <div v-if="msg.is_emergency" class="p-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-bold">
                  🚨 <strong>জরুরি:</strong> নিকটস্থ হাসপাতালে যান অথবা <a href="tel:999" class="underline font-black text-rose-800">৯৯৯</a>-এ কল করুন।
                </div>

                <!-- Specialty Badge -->
                <div v-if="msg.specialty_bn">
                  <span class="inline-block px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 font-bold text-[10px] border border-sky-100">
                    {{ msg.specialty_bn }}
                  </span>
                </div>

                <div class="leading-relaxed whitespace-pre-line text-slate-700">{{ msg.text }}</div>

                <!-- Doctors mini list -->
                <div v-if="msg.doctors && msg.doctors.length > 0" class="pt-2 border-t border-slate-100 space-y-1.5">
                  <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">রেকমেন্ডেড ডাক্তার:</span>
                    <router-link to="/doctors" class="text-[10px] text-sky-600 hover:underline font-bold" @click="isOpen = false">সকল ডাক্তার</router-link>
                  </div>
                  <div 
                    v-for="doc in msg.doctors.slice(0, 2)" 
                    :key="doc.id" 
                    class="p-2 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-2 shadow-2xs hover:bg-white transition"
                  >
                    <div class="min-w-0 flex-1">
                      <div class="font-bold text-slate-800 truncate text-[11px]">{{ doc.name }}</div>
                      <div class="text-[10px] text-slate-500 truncate">{{ doc.specialization }}</div>
                      <div v-if="doc.consultation_fee" class="text-[9.5px] font-semibold text-emerald-600">
                        ভিজিট: ৳ {{ Number(doc.consultation_fee) }}
                      </div>
                    </div>
                    <router-link 
                      :to="`/appoinment?doctor_id=${doc.id}`" 
                      class="px-2.5 py-1 rounded-md text-white font-bold text-[11px] shrink-0 transition"
                      style="background-color: #0284c7 !important; color: #ffffff !important; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 3px rgba(2, 132, 199, 0.35);"
                      @click="isOpen = false"
                      title="সিরিয়াল বুকিং করুন"
                    >
                      <i class="fa-regular fa-calendar-check text-[10px]" style="color: #ffffff !important;"></i>
                      <span style="color: #ffffff !important;">বুকিং</span>
                    </router-link>
                  </div>
                </div>

                <!-- Audio Read Aloud & Time -->
                <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-[10px] text-slate-400">
                  <button 
                    type="button" 
                    class="hover:text-sky-600 font-semibold flex items-center gap-1 transition"
                    @click="speakMessage(msg.text)"
                  >
                    <i :class="speakingText === msg.text ? 'fa-solid fa-volume-xmark text-rose-500' : 'fa-solid fa-volume-high text-sky-600'"></i>
                    <span>{{ speakingText === msg.text ? 'থামান' : 'শুনুন' }}</span>
                  </button>
                  <span>{{ msg.time }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading Spinner -->
          <div v-if="loading" class="flex items-center gap-2 text-slate-400 text-xs py-2">
            <span class="w-3.5 h-3.5 border-2 border-sky-600 border-t-transparent rounded-full animate-spin"></span>
            <span>এআই উত্তর প্রস্তুত করছে...</span>
          </div>
        </div>

        <!-- Live Voice Listening Alert Bar -->
        <div v-if="isListening" class="px-3 py-2 bg-rose-600 text-white text-xs flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
            <span class="font-medium text-[11px]"><strong>শুনছি...</strong> বাংলায় আপনার সমস্যা বলুন</span>
          </div>
          <button type="button" class="text-[10px] px-2 py-0.5 rounded bg-white/20 font-bold" @click="toggleVoiceChat">থামান</button>
        </div>

        <!-- Footer Input -->
        <div class="p-3 bg-white border-t border-slate-100 shrink-0">
          <form @submit.prevent="send" class="flex items-center gap-2">
            <div class="relative flex-1">
              <input 
                type="text" 
                v-model="input" 
                data-no-voice
                class="w-full pl-3 pr-9 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                placeholder="লক্ষণ লিখুন বা মুখে বলুন..." 
                :disabled="loading"
              />
              <button 
                type="button" 
                class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500 transition"
                :class="{ 'text-rose-600 animate-pulse': isListening }"
                @click="toggleVoiceChat"
                title="মাইক্রোফোনে কথা বলুন"
              >
                <i class="fa-solid fa-microphone text-xs"></i>
              </button>
            </div>
            <button 
              type="submit" 
              class="w-8 h-8 rounded-xl bg-sky-600 hover:bg-sky-500 text-white flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed shrink-0 shadow-xs"
              :disabled="loading || !input.trim()"
            >
              <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';
import speechService from '../utils/voiceInput';

export default {
  name: 'AiChatWidget',
  computed: {
    shouldShowWidget() {
      const path = (this.$route && this.$route.path) ? this.$route.path : '';
      const hiddenRoutes = ['/ai-assistant', '/login', '/register', '/admin/login'];
      if (hiddenRoutes.includes(path)) return false;
      if (path.startsWith('/video-chamber/')) return false;
      return true;
    },
  },
  data() {
    return {
      isOpen: false,
      input: '',
      loading: false,
      isListening: false,
      speakingText: null,
      miniChips: [
        { id: 1, icon: '🩺', label: 'পেটে গ্যাস ও ব্যথা', query: 'পেটে তীব্র গ্যাস ও ব্যথা হচ্ছে, কী করব?' },
        { id: 2, icon: '🫀', label: 'বুক ধড়ফড়', query: 'বুকে ভারি চাপ ও বুক ধড়ফড় করছে।' },
        { id: 3, icon: '👶', label: 'শিশুর জ্বর', query: 'বাচ্চার দুই দিন ধরে জ্বর ও সর্দি।' },
        { id: 4, icon: '🧠', label: 'মাথাব্যথা', query: 'মাথায় তীব্র যন্ত্রণা ও চোখ দিয়ে পানি পড়ছে।' },
      ],
      messages: [
        {
          sender: 'ai',
          text: 'স্বাগতম! আমি সুখী মানুষ এআই স্বাস্থ্য সহকারী। আপনার কি কোনো শারীরিক সমস্যা বা লক্ষণ রয়েছে? এখানে লিখে জানান।',
          specialty_bn: '',
          doctors: [],
          is_emergency: false,
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }
      ]
    };
  },
  methods: {
    toggleChat() {
      this.isOpen = !this.isOpen;
      if (this.isOpen) {
        this.scrollBottom();
      }
    },
    sendQuick(query) {
      this.input = query;
      this.send();
    },
    async send() {
      if (!this.input.trim() || this.loading) return;

      const userText = this.input.trim();
      this.messages.push({
        sender: 'user',
        text: userText,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      });
      this.input = '';
      this.loading = true;
      this.scrollBottom();

      try {
        let userAddress = '';
        try {
          const userObj = JSON.parse(localStorage.getItem('user') || '{}');
          userAddress = userObj.address || userObj.district || '';
        } catch (e) {}

        const res = await axios.post('/ai/analyze-symptoms', { 
          message: userText,
          address: userAddress
        });
        const data = res.data;

        this.messages.push({
          sender: 'ai',
          text: data.ai_advice || data.message || 'দুঃখিত, কোনো তথ্য পাওয়া যায়নি। অনুগ্রহ করে বিশেষজ্ঞ ডাক্তারের পরামর্শ নিন।',
          specialty_bn: data.specialty_bn || '',
          doctors: data.recommended_doctors || [],
          is_emergency: data.is_emergency || false,
          ai_source: data.ai_source || '',
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });
      } catch (err) {
        console.error('AI chat error:', err);
        this.messages.push({
          sender: 'ai',
          text: 'সার্ভারে সংযোগ করতে সমস্যা হয়েছে। দয়া করে কিছুক্ষণ পর আবার চেষ্টা করুন।',
          specialty_bn: '',
          doctors: [],
          is_emergency: false,
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });
      } finally {
        this.loading = false;
        this.scrollBottom();
      }
    },
    scrollBottom() {
      this.$nextTick(() => {
        if (this.$refs.chatBody) {
          this.$refs.chatBody.scrollTop = this.$refs.chatBody.scrollHeight;
        }
      });
    },
    toggleVoiceChat() {
      if (this.isListening) {
        speechService.stopListening();
        this.isListening = false;
        return;
      }

      speechService.startListening({
        lang: 'bn-BD',
        onStart: () => {
          this.isListening = true;
        },
        onInterim: (text) => {
          this.input = text;
        },
        onResult: (text) => {
          this.input = text;
          this.isListening = false;
        },
        onEnd: () => {
          this.isListening = false;
        },
        onError: () => {
          this.isListening = false;
        }
      });
    },
    speakMessage(text) {
      if (this.speakingText === text) {
        speechService.stopSpeaking();
        this.speakingText = null;
        return;
      }

      this.speakingText = text;
      speechService.speak(text, {
        lang: 'bn-BD',
        onEnd: () => {
          this.speakingText = null;
        }
      });
    }
  },
  beforeUnmount() {
    if (this.isListening) {
      speechService.stopListening();
    }
    speechService.stopSpeaking();
  }
};
</script>
