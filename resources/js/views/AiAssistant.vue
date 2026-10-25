<template>
  <div class="ai-assistant-page py-4 py-lg-5" style="background: #f8fafc; min-height: 85vh;">
    <div class="container">
      <!-- Top Title & Badge -->
      <div class="row justify-content-center mb-4 text-center">
        <div class="col-lg-8">
          <div class="d-inline-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm mb-3">
            <span class="badge badge-danger rounded-pill mr-2 px-2 py-1" style="font-size: 11px; background: linear-gradient(45deg, #e12454, #ff5722);">
              <i class="icofont-brain mr-1"></i> AI Powered
            </span>
            <span class="small font-weight-bold text-dark">সুখী মানুষ স্মার্ট লক্ষণ পরামর্শক ও স্পেশালিস্ট রেকমেন্ডার</span>
            <span class="status-dot ml-2"></span>
          </div>
          <h2 class="font-weight-bold text-dark mb-2">
            আপনার শারীরিক সমস্যা জানান, <span style="color: #e12454;">সঠিক বিশেষজ্ঞ খুঁজুন</span>
          </h2>
          <p class="text-muted mb-0" style="font-size: 15px;">
            বাংলা বা ইংরেজিতে আপনার রোগ বা লক্ষণের কথা লিখুন। আমাদের এআই ইঞ্জিন তাৎক্ষণিকভাবে লক্ষণ বিশ্লেষণ করে সঠিক বিশেষজ্ঞ ডাক্তার নির্বাচন এবং সরাসরি সিরিয়াল বুকিংয়ে সহায়তা করবে।
          </p>
        </div>
      </div>

      <!-- Main Assistant Box -->
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
          <div class="card border-0 shadow-sm rounded-lg overflow-hidden" style="border-radius: 16px;">
            
            <!-- Chat Card Header -->
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div class="d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-3 shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #223a66, #e12454);">
                  <i class="icofont-stethoscope-alt" style="font-size: 20px;"></i>
                </div>
                <div>
                  <h6 class="font-weight-bold text-dark mb-0">সুখী মানুষ (Sukhi Manush) AI Assistant</h6>
                  <small class="text-success font-weight-bold">
                    <i class="icofont-check-circled mr-1"></i>অনলাইন সক্রিয় &bull; বাংলায় পরামর্শ প্রস্তুত
                  </small>
                </div>
              </div>

              <!-- Patient Location Priority Controls -->
              <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                <div class="d-flex align-items-center">
                  <label class="small text-muted font-weight-bold mr-1 mb-0 d-none d-md-inline">📍 আপনার এলাকা:</label>
                  <select v-model="selectedDistrict" class="form-control form-control-sm" style="width: 125px; border-radius: 8px; font-size: 12px;">
                    <option value="">সকল জেলা</option>
                    <option value="Dhaka">ঢাকা (Dhaka)</option>
                    <option value="Chattogram">চট্টগ্রাম (Chattogram)</option>
                    <option value="Sylhet">সিলেট (Sylhet)</option>
                    <option value="Rajshahi">রাজশাহী (Rajshahi)</option>
                    <option value="Rangpur">রংপুর (Rangpur)</option>
                    <option value="Khulna">খুলনা (Khulna)</option>
                    <option value="Barishal">বরিশাল (Barishal)</option>
                    <option value="Mymensingh">ময়মনসিংহ (Mymensingh)</option>
                    <option value="Dinajpur">দিনাজপুর (Dinajpur)</option>
                    <option value="Bogura">বগুড়া (Bogura)</option>
                    <option value="Cumilla">কুমিল্লা (Cumilla)</option>
                  </select>
                </div>
                <input 
                  type="text" 
                  v-model="selectedUpazila" 
                  class="form-control form-control-sm d-none d-sm-inline-block" 
                  placeholder="উপজেলা / থানা (যেমনঃ মিরপুর)" 
                  style="width: 155px; border-radius: 8px; font-size: 12px;"
                  title="কাছের ডাক্তার পেতে আপনার থানা বা এলাকা লিখুন"
                >
                <button v-if="messages.length > 1" class="btn btn-sm btn-outline-secondary ml-1" @click="clearChat" title="নতুন করে শুরু করুন">
                  <i class="icofont-refresh"></i>
                </button>
              </div>
            </div>

            <!-- Quick Suggestion Symptom Chips -->
            <div class="bg-light px-3 py-2 border-bottom">
              <div class="small font-weight-bold text-muted mb-1 d-flex align-items-center justify-content-between">
                <span><i class="icofont-flash text-warning mr-1"></i> দ্রুত পরামর্শের জন্য লক্ষণে ক্লিক করুন:</span>
                <span class="d-md-none text-[10px] text-muted font-normal">ডানে সোয়াইপ করুন ›</span>
              </div>
              <div class="symptom-chips-scroll">
                <button 
                  v-for="chip in quickChips" 
                  :key="chip.id" 
                  type="button" 
                  class="btn btn-sm btn-white border shadow-none text-dark py-1 px-2.5 d-inline-flex align-items-center flex-shrink-0"
                  style="border-radius: 20px; font-size: 12px; background: #fff;"
                  @click="useChip(chip)"
                  :disabled="isLoading"
                >
                  <span class="mr-1">{{ chip.icon }}</span>
                  <span class="whitespace-nowrap">{{ chip.label }}</span>
                </button>
              </div>
            </div>

            <!-- Chat Message Stream -->
            <div ref="chatContainer" class="card-body p-3 p-md-4" style="height: 480px; overflow-y: auto; background: #fafbfc;">
              
              <!-- Message Bubble Iteration -->
              <div v-for="(msg, idx) in messages" :key="idx" class="mb-4">
                
                <!-- USER MESSAGE -->
                <div v-if="msg.sender === 'user'" class="d-flex justify-content-end">
                  <div class="user-bubble p-3 rounded-lg shadow-sm text-white" style="max-width: 80%; background: #223a66; border-radius: 16px 16px 4px 16px;">
                    <div class="font-weight-normal" style="font-size: 14.5px; line-height: 1.5;">{{ msg.text }}</div>
                    <small class="d-block text-right text-light mt-1" style="font-size: 10px; opacity: 0.8;">{{ msg.time }}</small>
                  </div>
                </div>

                <!-- AI ASSISTANT MESSAGE -->
                <div v-else class="d-flex align-items-start">
                  <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-2 flex-shrink-0 shadow-sm" style="width: 36px; height: 36px; background: #e12454;">
                    <i class="icofont-robot" style="font-size: 18px;"></i>
                  </div>
                  
                  <div class="ai-bubble p-3 p-md-4 bg-white border rounded-lg shadow-sm w-100" style="max-width: 90%; border-radius: 4px 16px 16px 16px;">
                    
                    <!-- Emergency Red Flag Alert (If triggered) -->
                    <div v-if="msg.is_emergency" class="alert alert-danger border-danger mb-3 p-3 rounded">
                      <div class="d-flex align-items-center font-weight-bold text-danger mb-1" style="font-size: 15px;">
                        <span class="spinner-grow spinner-grow-sm mr-2" role="status"></span>
                        {{ msg.emergency_alert?.title || '🚨 জরুরি সতর্কবার্তা (Red Flag Alert)' }}
                      </div>
                      <p class="mb-2 small text-dark">{{ msg.emergency_alert?.message }}</p>
                      <div class="d-flex flex-wrap gap-2">
                        <a href="tel:999" class="btn btn-sm btn-danger font-weight-bold mr-2">
                          <i class="icofont-phone mr-1"></i> জরুরি সেবা ৯৯৯ কল করুন
                        </a>
                        <router-link to="/emergency" class="btn btn-sm btn-outline-danger font-weight-bold">
                          <i class="icofont-ambulance mr-1"></i> জরুরি স্বাস্থ্য হাব
                        </router-link>
                      </div>
                    </div>

                    <!-- Recommended Specialty Badge & Engine Indicator -->
                    <div v-if="msg.specialty_bn || msg.ai_source" class="mb-2 d-flex align-items-center flex-wrap" style="gap: 6px;">
                      <span v-if="msg.specialty_bn" class="badge badge-light border text-primary px-2 py-1 font-weight-bold" style="font-size: 13px;">
                        <i class="icofont-doctor mr-1"></i>প্রস্তাবিত বিশেষজ্ঞ: {{ msg.specialty_bn }}
                      </span>
                      <span v-if="msg.ai_source" class="badge badge-light border text-secondary px-2 py-1" style="font-size: 11px;">
                        <i class="icofont-brain mr-1 text-info"></i>{{ msg.ai_source }}
                      </span>
                    </div>

                    <!-- AI Advice Text -->
                    <div class="text-dark mb-3" style="font-size: 14.5px; line-height: 1.6; white-space: pre-line;">
                      {{ msg.text }}
                    </div>

                    <!-- Recommended Doctors Section -->
                    <div v-if="msg.doctors && msg.doctors.length > 0" class="mt-3 pt-3 border-top">
                      <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold text-dark" style="font-size: 13.5px;">
                          <i class="icofont-ui-user-group text-primary mr-1"></i>সরাসরি সিরিয়াল বুকিংয়ের জন্য বিশেষজ্ঞ ডাক্তারগণ:
                        </span>
                        <router-link to="/doctors" class="small font-weight-bold text-primary">সকল ডাক্তার দেখুন ›</router-link>
                      </div>

                      <!-- Proximity Feedback Banner -->
                      <div v-if="msg.patient_location?.has_location" class="alert alert-success py-1 px-2 mb-2 d-flex align-items-center rounded" style="font-size: 11.5px; background: #e8f5e9; border: 1px solid #c8e6c9;">
                        <i class="icofont-google-map text-success mr-2 font-16"></i>
                        <span>
                          আপনার অবস্থান <strong>({{ msg.patient_location.upazila || msg.patient_location.district }})</strong> এর নিকটবর্তী চেম্বারগুলোকে অগ্রাধিকারে সাজানো হয়েছে।
                        </span>
                      </div>

                      <div class="row">
                        <div v-for="doc in msg.doctors" :key="doc.id" class="col-md-6 mb-2">
                          <div class="card h-100 border p-2 rounded shadow-none bg-light hover-shadow transition">
                            <div class="d-flex align-items-start">
                              <img 
                                :src="doc.avatar_url || '/images/team/1.jpg'" 
                                :alt="doc.name" 
                                class="rounded-circle mr-2 flex-shrink-0"
                                style="width: 44px; height: 44px; object-fit: cover;"
                                @error="$event.target.src = '/images/team/1.jpg'"
                              >
                              <div class="flex-grow-1 overflow-hidden">
                                <div class="font-weight-bold text-dark text-truncate mb-0" style="font-size: 13px;">
                                  {{ doc.name }}
                                  <i v-if="doc.is_verified" class="icofont-badge text-primary" title="BMDC Verified"></i>
                                </div>
                                <small class="text-muted d-block text-truncate" style="font-size: 11px;">{{ doc.specialization }}</small>
                                <small class="text-muted d-block text-truncate" style="font-size: 11px;">
                                  <i class="icofont-location-pin text-danger"></i> {{ doc.chamber_address || doc.district }}
                                </small>
                                <!-- Proximity Badge -->
                                <div v-if="doc.proximity_label" class="mt-1">
                                  <span 
                                    class="badge font-weight-bold px-2 py-1 text-truncate d-inline-block" 
                                    :class="doc.is_nearby ? 'badge-success text-white' : 'badge-light border text-muted'"
                                    style="font-size: 9.5px; max-width: 100%; border-radius: 6px;"
                                  >
                                    {{ doc.proximity_label }}
                                  </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                                  <span class="font-weight-bold text-success" style="font-size: 12px;">৳ {{ Number(doc.consultation_fee || 800) }}</span>
                                  <router-link 
                                    :to="`/appointment?doctor_id=${doc.id}`" 
                                    class="btn btn-xs btn-primary font-weight-bold py-0 px-2"
                                    style="font-size: 11px; border-radius: 12px;"
                                  >
                                    বুকিং করুন
                                  </router-link>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Follow-up Questions -->
                    <div v-if="msg.suggested_questions && msg.suggested_questions.length > 0" class="mt-3 pt-2 border-top">
                      <small class="text-muted font-weight-bold d-block mb-1">সম্পর্কিত প্রশ্নসমূহ:</small>
                      <div class="d-flex flex-wrap gap-1" style="gap: 4px;">
                        <button 
                          v-for="(q, qIdx) in msg.suggested_questions" 
                          :key="qIdx" 
                          type="button" 
                          class="btn btn-sm btn-outline-secondary py-0 px-2 text-left"
                          style="font-size: 11px; border-radius: 12px;"
                          @click="sendMessage(q)"
                        >
                          <i class="icofont-question-circle mr-1"></i>{{ q }}
                        </button>
                      </div>
                    </div>

                    <div class="msg-footer-bar d-flex flex-wrap align-items-center justify-content-between mt-2 pt-2 border-top">
                      <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <span class="msg-time-badge text-muted">
                          {{ msg.time }} &bull; সুখী মানুষ এআই
                        </span>
                        <!-- Listen / Read Aloud Button -->
                        <button 
                          type="button" 
                          class="btn-tts-listen d-inline-flex align-items-center"
                          :class="{ 'is-speaking': isMessageSpeaking(msg) }"
                          @click="toggleSpeakMessage(msg)"
                          :title="isMessageSpeaking(msg) ? 'ভয়েস বন্ধ করুন' : 'বাংলায় পড়ে শুনুন'"
                        >
                          <i :class="isMessageSpeaking(msg) ? 'icofont-volume-mute' : 'icofont-volume-up'" class="mr-1"></i>
                          <span>{{ isMessageSpeaking(msg) ? 'থামান' : 'পড়ে শুনুন' }}</span>
                        </button>
                      </div>
                      <span class="msg-security-badge text-muted d-none d-sm-inline">
                        <i class="icofont-shield-alt mr-1 text-success"></i>নিরাপদ ও সুরক্ষিত
                      </span>
                    </div>

                  </div>
                </div>

              </div>

              <!-- Loading Typing State -->
              <div v-if="isLoading" class="d-flex align-items-start mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-2 flex-shrink-0 shadow-sm" style="width: 36px; height: 36px; background: #e12454;">
                  <i class="icofont-robot" style="font-size: 18px;"></i>
                </div>
                <div class="p-3 bg-white border rounded shadow-sm">
                  <div class="d-flex align-items-center text-muted font-weight-bold" style="font-size: 13px;">
                    <div class="spinner-border spinner-border-sm text-danger mr-2" role="status"></div>
                    এআই আপনার লক্ষণ বিশ্লেষণ করে সঠিক ডাক্তার নির্বাচন করছে...
                  </div>
                </div>
              </div>

            </div>

            <!-- Input Bar with Prominent Voice Support -->
            <div class="card-footer bg-white border-top p-3">
              <!-- Voice Dictation Live Banner -->
              <div v-if="isListeningVoice" class="alert alert-danger py-2 px-3 mb-2 rounded-lg d-flex align-items-center justify-content-between shadow-sm animate-pulse-voice">
                <div class="d-flex align-items-center">
                  <span class="spinner-grow spinner-grow-sm mr-2 text-danger" role="status"></span>
                  <span class="font-weight-bold text-danger">🎙️ শুনছি... আপনার লক্ষণ বা শারীরিক সমস্যার কথা স্পষ্ট করে বাংলায় বলুন...</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 font-weight-bold" @click="toggleVoiceInput">থামান</button>
              </div>

              <form @submit.prevent="sendMessage(userInput)">
                <div class="ai-input-capsule d-flex align-items-center bg-white">
                  <input 
                    type="text" 
                    v-model="userInput" 
                    data-no-voice
                    class="ai-chat-input flex-grow-1" 
                    placeholder="শারীরিক সমস্যা লিখুন বা মুখে বলুন..." 
                    :disabled="isLoading"
                  >
                  
                  <div class="ai-input-actions d-flex align-items-center">
                    <!-- Voice Mic Button -->
                    <button 
                      type="button" 
                      class="ai-voice-mic-btn d-flex align-items-center justify-content-center"
                      :class="{ 'listening': isListeningVoice }"
                      @click="toggleVoiceInput"
                      :title="isListeningVoice ? 'ভয়েস ইনপুট বন্ধ করুন' : 'মাইক্রোফোনে বাংলায় মুখে বলুন'"
                    >
                      <i :class="isListeningVoice ? 'icofont-stop' : 'icofont-mic'" class="mic-icon"></i>
                      <span class="mic-label d-none d-md-inline ml-1">
                        {{ isListeningVoice ? 'শুনছি...' : 'মুখে বলুন' }}
                      </span>
                    </button>

                    <!-- Send / Consult Button -->
                    <button 
                      type="submit" 
                      class="ai-send-btn d-flex align-items-center justify-content-center font-weight-bold" 
                      :disabled="isLoading || !userInput.trim()"
                      :title="'পরামর্শ নিন'"
                    >
                      <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></span>
                      <template v-else>
                        <i class="icofont-paper-plane send-icon"></i>
                        <span class="send-label d-none d-md-inline ml-1">পরামর্শ নিন</span>
                      </template>
                    </button>
                  </div>
                </div>
              </form>

              <!-- Safety Disclaimer -->
              <div class="text-center mt-2">
                <small class="text-muted" style="font-size: 11px;">
                  <i class="icofont-info-circle mr-1 text-primary"></i>
                  সতর্কতা: এই এআই শুধুমাত্র প্রাথমিক স্বাস্থ্য বিশ্লেষণ ও সঠিক বিশেষজ্ঞ নির্বাচনের পরামর্শক। চূড়ান্ত চিকিৎসার জন্য অবশ্যই নিবন্ধিত চিকিৎসকের সাথে পরামর্শ করুন।
                </small>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import speechService from '../utils/voiceInput';

export default {
  name: 'AiAssistant',
  data() {
    return {
      userInput: '',
      selectedDistrict: '',
      selectedUpazila: '',
      selectedAddress: '',
      currentUser: null,
      isLoading: false,
      isListeningVoice: false,
      speakingMessageText: null,
      quickChips: [
        { id: 'stomach', icon: '🩺', label: 'পেট ব্যথা ও গ্যাস', query: 'আমার ৩ দিন ধরে পেটে তীব্র ব্যথা, বদহজম এবং গ্যাস হচ্ছে।' },
        { id: 'chest', icon: '🫀', label: 'বুকে চাপ ও হৃদস্পন্দন', query: 'আমার বুকে ভারি চাপ অনুভব হচ্ছে এবং বুক ধড়ফড় করছে।' },
        { id: 'baby', icon: '👶', label: 'শিশুর জ্বর ও সর্দি', query: 'আমার ছোট বাচ্চার দুই দিন ধরে জ্বর সাথে সর্দি ও কাশি।' },
        { id: 'head', icon: '🧠', label: 'মাথাব্যথা ও মাইগ্রেন', query: 'মাথার একপাশে প্রচণ্ড চিনচিনে ব্যথা, আলো সহ্য হচ্ছে না ও মাথা ঘোরাচ্ছে।' },
        { id: 'skin', icon: '🌿', label: 'চুলকানি ও এলার্জি', query: 'হাত-পা ও শরীরে লাল চাকা চাকা হয়ে প্রচণ্ড চুলকাচ্ছে, কোন ডাক্তার দেখাব?' },
        { id: 'bone', icon: '🦴', label: 'মাজা ও জয়েন্টে ব্যথা', query: 'হাঁটুর জোড়ায় প্রচণ্ড ব্যথা এবং দীর্ঘক্ষণ বসে থাকার পর কোমর সোজা করতে পারি না।' },
      ],
      messages: [
        {
          sender: 'ai',
          text: 'স্বাগতম! আমি সুখী মানুষ এআই স্বাস্থ্য সহকারী। আপনার শারীরিক কোনো অসুস্থতা, অস্বস্তি বা লক্ষণ থাকলে নিচে বিস্তারিত লিখুন। আমি আপনার লক্ষণ বিশ্লেষণ করে কোন বিশেষজ্ঞ ডাক্তার দেখানো উচিত তা জানিয়ে দেব এবং অবিলম্বে অ্যাপয়েন্টমেন্ট বুকিংয়ের ব্যবস্থা করব।',
          specialty_bn: '',
          doctors: [],
          is_emergency: false,
          suggested_questions: [
            'আমার পেটে গ্যাস ও বুক জ্বালাপোড়া করে, কী করব?',
            'বুকে ব্যথা হলে কোন বিশেষজ্ঞ ডাক্তার দেখানো উচিত?'
          ],
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }
      ]
    };
  },
  mounted() {
    this.fetchQuickChips();
  },
  methods: {
    async fetchQuickChips() {
      try {
        const res = await axios.get('/ai/quick-symptoms');
        if (res.data && res.data.chips) {
          this.quickChips = res.data.chips;
        }
      } catch (err) {
        console.warn('Could not load dynamic chips, using defaults.');
      }
    },
    useChip(chip) {
      this.userInput = chip.query;
      this.sendMessage(chip.query);
    },
    async sendMessage(queryText) {
      const text = (queryText || this.userInput || '').trim();
      if (!text || this.isLoading) return;

      const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

      // Append user message
      this.messages.push({
        sender: 'user',
        text: text,
        time: timeStr
      });

      this.userInput = '';
      this.isLoading = true;
      this.scrollToBottom();

      try {
        const res = await axios.post('/ai/analyze-symptoms', {
          message: text,
          district: this.selectedDistrict,
          upazila: this.selectedUpazila,
          address: this.selectedAddress || (this.currentUser?.address || '')
        });

        const data = res.data;
        if (data.success) {
          this.messages.push({
            sender: 'ai',
            text: data.ai_advice,
            specialty_bn: data.specialty_bn,
            specialty_en: data.specialty_en,
            is_emergency: data.is_emergency,
            emergency_alert: data.emergency_alert,
            doctors: data.recommended_doctors || [],
            patient_location: data.patient_location || null,
            suggested_questions: data.suggested_questions || [],
            ai_source: data.ai_source || null,
            ai_model: data.ai_model || null,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
          });
        } else {
          this.messages.push({
            sender: 'ai',
            text: 'দুঃখিত, এই মুহূর্তে বিশ্লেষণ সম্পন্ন করা সম্ভব হয়নি। অনুগ্রহ করে পুনরায় চেষ্টা করুন।',
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
          });
        }
      } catch (err) {
        console.error('Error in AI Symptom Analyzer:', err);
        this.messages.push({
          sender: 'ai',
          text: 'একটি প্রযুক্তিগত সমস্যা ঘটেছে। তবে জরুরি স্বাস্থ্য সমস্যায় দেরি না করে অবিলম্বে নিকটস্থ হাসপাতালের জরুরি বিভাগে যোগাযোগ করুন।',
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });
      } finally {
        this.isLoading = false;
        this.scrollToBottom();
      }
    },
    scrollToBottom() {
      this.$nextTick(() => {
        if (this.$refs.chatContainer) {
          this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
        }
      });
    },
    clearChat() {
      this.messages = [
        {
          sender: 'ai',
          text: 'নতুন আলোচনা শুরু করা হয়েছে। আপনার নতুন শারীরিক সমস্যা বা লক্ষণ জানান।',
          specialty_bn: '',
          doctors: [],
          is_emergency: false,
          suggested_questions: [],
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }
      ];
    },
    toggleVoiceInput() {
      if (this.isListeningVoice) {
        speechService.stopListening();
        this.isListeningVoice = false;
        return;
      }

      speechService.startListening({
        lang: 'bn-BD',
        onStart: () => {
          this.isListeningVoice = true;
        },
        onInterim: (text) => {
          this.userInput = text;
        },
        onResult: (text) => {
          this.userInput = text;
          this.isListeningVoice = false;
        },
        onEnd: () => {
          this.isListeningVoice = false;
        },
        onError: () => {
          this.isListeningVoice = false;
        }
      });
    },
    isMessageSpeaking(msg) {
      if (!this.speakingMessageText) return false;
      return this.speakingMessageText.includes(msg.text || '');
    },
    toggleSpeakMessage(msg) {
      const fullText = (msg.specialty_bn ? `প্রস্তাবিত বিশেষজ্ঞ: ${msg.specialty_bn}। ` : '') + (msg.text || '');
      if (this.speakingMessageText === fullText) {
        speechService.stopSpeaking();
        this.speakingMessageText = null;
        return;
      }

      this.speakingMessageText = fullText;
      speechService.speak(fullText, {
        lang: 'bn-BD',
        onStart: () => {
          this.speakingMessageText = fullText;
        },
        onEnd: () => {
          this.speakingMessageText = null;
        }
      });
    }
  },
  beforeUnmount() {
    if (this.isListeningVoice) {
      speechService.stopListening();
    }
    speechService.stopSpeaking();
  }
};
</script>

<style scoped>
.symptom-chips-scroll {
  display: flex;
  flex-wrap: nowrap;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  gap: 6px;
  padding-bottom: 3px;
}
.symptom-chips-scroll::-webkit-scrollbar {
  display: none;
}
@media (min-width: 992px) {
  .symptom-chips-scroll {
    flex-wrap: wrap;
    overflow-x: visible;
  }
}

.ai-input-capsule {
  border: 1.5px solid #e2e8f0;
  border-radius: 9999px;
  padding: 4px 6px 4px 16px;
  background-color: #ffffff;
  box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  gap: 6px;
}

.ai-input-capsule:focus-within {
  border-color: #e12454;
  box-shadow: 0 4px 20px rgba(225, 36, 84, 0.15);
}

.ai-chat-input {
  border: none !important;
  outline: none !important;
  background: transparent !important;
  font-size: 14px;
  color: #1e293b;
  min-width: 0;
  padding: 8px 6px 8px 0;
  box-shadow: none !important;
}

.ai-chat-input::placeholder {
  color: #94a3b8;
  font-size: 13px;
}

.ai-input-actions {
  gap: 6px;
  flex-shrink: 0;
}

/* Modern Mic Button */
.ai-voice-mic-btn {
  background: #f8fafc;
  color: #e11d48;
  border: 1px solid #fed7aa;
  border-radius: 9999px;
  width: 36px;
  height: 36px;
  padding: 0;
  font-size: 13px;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
  outline: none;
  flex-shrink: 0;
}

.ai-voice-mic-btn:hover {
  background: #fff1f2;
  border-color: #fda4af;
  color: #be123c;
  transform: scale(1.05);
}

.ai-voice-mic-btn.listening {
  background: #e11d48;
  color: #ffffff;
  border-color: #be123c;
  animation: pulseVoice 1.4s infinite;
  box-shadow: 0 0 12px rgba(225, 29, 72, 0.45);
}

.mic-icon {
  font-size: 15px;
}

/* Modern Send Button */
.ai-send-btn {
  background: linear-gradient(135deg, #e12454 0%, #be123c 100%);
  color: #ffffff;
  border: none;
  border-radius: 9999px;
  width: 36px;
  height: 36px;
  padding: 0;
  font-size: 13px;
  box-shadow: 0 3px 10px rgba(225, 36, 84, 0.3);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
  outline: none;
  flex-shrink: 0;
}

.ai-send-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
  box-shadow: 0 4px 14px rgba(225, 36, 84, 0.45);
  transform: scale(1.05);
}

.ai-send-btn:disabled {
  opacity: 0.45;
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}

.send-icon {
  font-size: 14px;
}

/* On Desktop (>= 768px): Expand buttons gracefully with text */
@media (min-width: 768px) {
  .ai-voice-mic-btn {
    width: auto;
    height: auto;
    padding: 6px 14px;
    font-size: 12.5px;
  }
  .ai-send-btn {
    width: auto;
    height: auto;
    padding: 7px 18px;
    font-size: 13px;
  }
}

@keyframes pulseVoice {
  0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.6); }
  70% { transform: scale(1.03); box-shadow: 0 0 0 10px rgba(225, 29, 72, 0); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
}

.pulse-voice-active {
  animation: pulse-mic-ring 1.5s infinite;
  background-color: #e12454 !important;
  border-color: #e12454 !important;
  color: #fff !important;
}

@keyframes pulse-mic-ring {
  0% { box-shadow: 0 0 0 0 rgba(225, 36, 84, 0.7); }
  70% { box-shadow: 0 0 0 10px rgba(225, 36, 84, 0); }
  100% { box-shadow: 0 0 0 0 rgba(225, 36, 84, 0); }
}
.status-dot {
  width: 8px;
  height: 8px;
  background-color: #28a745;
  border-radius: 50%;
  display: inline-block;
  animation: pulse 1.8s infinite;
}
@keyframes pulse {
  0% { transform: scale(0.9); opacity: 0.8; }
  50% { transform: scale(1.3); opacity: 1; }
  100% { transform: scale(0.9); opacity: 0.8; }
}
.hover-shadow:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border-color: #e12454 !important;
}
.transition {
  transition: all 0.2s ease-in-out;
}

/* Responsive Audio Speak Button in Chat */
.msg-footer-bar {
  font-size: 11px;
}

.msg-time-badge {
  font-size: 10.5px;
  white-space: nowrap;
}

.btn-tts-listen {
  background: #f0f9ff;
  color: #0369a1;
  border: 1px solid #bae6fd;
  border-radius: 20px;
  padding: 3px 10px;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
  transition: all 0.2s ease;
  cursor: pointer;
  line-height: 1.4;
  outline: none;
}

.btn-tts-listen:hover {
  background: #e0f2fe;
  color: #0284c7;
  border-color: #7dd3fc;
  transform: translateY(-1px);
}

.btn-tts-listen.is-speaking {
  background: #fee2e2;
  color: #be123c;
  border-color: #fecdd3;
  animation: pulse-mic-ring 1.5s infinite;
}

.btn-tts-listen.is-speaking i {
  color: #be123c !important;
}

@media (max-width: 576px) {
  .msg-footer-bar {
    font-size: 10px;
  }
  .msg-time-badge {
    font-size: 10px;
  }
  .btn-tts-listen {
    padding: 2px 8px;
    font-size: 10.5px;
  }
}
</style>
