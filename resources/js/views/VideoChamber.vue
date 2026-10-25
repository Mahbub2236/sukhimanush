<template>
  <div class="video-chamber-page bg-light min-vh-100 py-3 py-md-4">
    <div class="container-fluid px-3 px-md-4">
      
      <!-- Top Navigation & Return Bar -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center mb-2 mb-md-0">
          <button @click="goBack" class="btn btn-sm btn-outline-secondary rounded-pill mr-2 font-weight-bold">
            <i class="icofont-arrow-left mr-1"></i> ফিরে যান
          </button>
          <div>
            <h5 class="mb-0 font-weight-bold text-dark d-flex align-items-center">
              <i class="icofont-video-cam text-success mr-2 font-22"></i>
              সুখী মানুষ লাইভ ডিজিটাল ভিডিও চেম্বার
            </h5>
            <small class="text-muted">১-ক্লিক ইমেইল-মুক্ত সরাসরি ডাক্তার-রোগী ভিডিও কনসালটেশন</small>
          </div>
        </div>

        <!-- Room Live Status & Fallback Buttons -->
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
          <span v-if="isConnected" class="badge badge-success px-3 py-2 rounded-pill font-13 d-inline-flex align-items-center shadow-sm">
            <span class="blinking-dot mr-2"></span> লাইভ কল চলছে
          </span>
          <span v-else-if="isLoading" class="badge badge-warning px-3 py-2 rounded-pill font-13 text-dark shadow-sm">
            <i class="icofont-spinner-alt-3 icofont-spin mr-1"></i> চেম্বার প্রস্তুত হচ্ছে...
          </span>

          <!-- Direct Google Meet Fallback (If doctor has configured a permanent link) -->
          <a 
            v-if="doctor?.telemedicine_link && doctor.telemedicine_link.includes('http')" 
            :href="doctor.telemedicine_link" 
            target="_blank" 
            class="btn btn-sm btn-outline-primary rounded-pill font-weight-bold shadow-sm"
            title="ইন-অ্যাপে কোনো ডিভাইস সমস্যা হলে সরাসরি গুগল মিটে কথা বলুন"
          >
            <i class="icofont-brand-google mr-1 text-danger"></i> বিকল্প: Google Meet-এ কল
          </a>

          <!-- Doctor Direct Call via Phone (Emergency) -->
          <a 
            v-if="doctor?.phone" 
            :href="'tel:' + doctor.phone" 
            class="btn btn-sm btn-outline-dark rounded-pill font-weight-bold"
            title="জরুরি টেলিফোন যোগাযোগ"
          >
            <i class="icofont-phone mr-1 text-success"></i> হটলাইন
          </a>
        </div>
      </div>

      <!-- Rural-Friendly Banglish/Bangla Instruction Guide Alert -->
      <div class="alert alert-info border-0 shadow-sm rounded-lg py-2 px-3 mb-3 d-flex align-items-center flex-wrap justify-content-between" style="background-color: #e8f4fd;">
        <div class="d-flex align-items-center py-1">
          <i class="icofont-info-circle font-22 text-primary mr-2 flex-shrink-0"></i>
          <span class="small font-weight-bold text-dark">
            📢 <strong>সহজ নিয়ম:</strong> আপনার মোবাইলের স্ক্রিনে ক্যামেরা ও মাইকের অনুমতি চাইলে 
            <span class="badge badge-primary px-2 py-1 mx-1" style="font-size: 11px;">Allow / অনুমতি দিন</span> বাটনে চাপ দিন। 
            কোনো ইমেইল বা পাসওয়ার্ড লাগবে না।
          </span>
        </div>
        <div class="d-flex align-items-center py-1" style="gap: 6px;">
          <button 
            class="btn btn-sm btn-light border py-1 px-2 font-weight-bold text-muted" 
            style="font-size: 11px;"
            @click="toggleLowBandwidth"
            :class="{ 'btn-warning text-dark font-weight-bold': lowBandwidthMode }"
          >
            <i class="icofont-signal mr-1"></i>
            {{ lowBandwidthMode ? 'লো-ডাটা মোড সক্রিয় (2G/3G)' : 'দুর্বল নেটে অডিও মোড' }}
          </button>
          <button 
            class="btn btn-sm btn-light border py-1 px-2 text-muted" 
            style="font-size: 11px;" 
            @click="reloadChamber"
            title="রুম পুনরায় কানেক্ট করুন"
          >
            <i class="icofont-refresh mr-1"></i> রিলোড
          </button>
        </div>
      </div>

      <!-- Main Video Chamber Grid (Video Frame + Patient/Doctor Info Sidebar) -->
      <div class="row">
        
        <!-- Video Screen Column -->
        <div class="col-lg-9 col-md-8 mb-3">
          <div class="card border-0 shadow-sm rounded-lg overflow-hidden position-relative" style="background-color: #141824; min-height: 580px; height: calc(100vh - 220px);">
            
            <!-- Loading Indicator Inside Video Card -->
            <div v-if="isLoading" class="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white" style="z-index: 10; background: rgba(20, 24, 36, 0.95);">
              <div class="spinner-grow text-success mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">লোড হচ্ছে...</span>
              </div>
              <h5 class="font-weight-bold mb-1">নিরাপদ ভিডিও চেম্বার সংযুক্ত হচ্ছে...</h5>
              <p class="small text-muted mb-0">দয়া করে ১-২ সেকেন্ড অপেক্ষা করুন, কোনো লগইন প্রয়োজন নেই</p>
            </div>

            <!-- Permission Denied / Error Banner -->
            <div v-if="hasError" class="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-4 text-center" style="z-index: 10; background: rgba(20, 24, 36, 0.97);">
              <i class="icofont-warning-alt text-warning font-45 mb-2"></i>
              <h5 class="font-weight-bold mb-2">{{ errorMessage }}</h5>
              <p class="small text-muted max-w-500 mb-3">
                যদি ব্রাউজার থেকে ক্যামেরা ব্লক করা থাকে, তবে ব্রাউজারের উপরে তালার চিহ্নে (Lock icon) ক্লিক করে 'Camera' ও 'Microphone' চালু করুন।
              </p>
              <div class="d-flex flex-wrap justify-content-center" style="gap: 10px;">
                <button class="btn btn-success px-4 py-2 font-weight-bold rounded-pill" @click="reloadChamber">
                  <i class="icofont-refresh mr-1"></i> আবার চেষ্টা করুন
                </button>
                <a v-if="doctor?.telemedicine_link" :href="doctor.telemedicine_link" target="_blank" class="btn btn-outline-light px-4 py-2 font-weight-bold rounded-pill">
                  <i class="icofont-video-cam mr-1"></i> বিকল্প Google Meet খুলুন
                </a>
              </div>
            </div>

            <!-- In-App Jitsi IFrame Mount Point -->
            <div id="sukhi-manush-video-chamber-container" class="w-100 h-100" style="min-height: 560px;"></div>

          </div>
        </div>

        <!-- Sidebar Column: Consultation & Patient/Doctor Context Details -->
        <div class="col-lg-3 col-md-4 mb-3">
          
          <!-- Participant Card -->
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white mb-3">
            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2 d-flex align-items-center">
              <i class="icofont-stethoscope text-primary mr-2"></i>
              ডাক্তারের বিবরণ
            </h6>
            <div class="d-flex align-items-center mb-2">
              <img 
                :src="doctor?.avatar ? ('/' + doctor.avatar) : '/images/team/1.jpg'" 
                class="rounded-circle shadow-sm mr-2 flex-shrink-0" 
                style="width: 48px; height: 48px; object-fit: cover; border: 2px solid #223a66;"
                alt="Doctor Photo"
              >
              <div class="overflow-hidden">
                <h6 class="font-weight-bold mb-0 text-dark text-truncate">{{ doctor?.name || 'বিশেষজ্ঞ ডাক্তার' }}</h6>
                <small class="text-primary font-weight-bold d-block text-truncate">{{ doctor?.specialization || 'মেডিকেল স্পেশালিস্ট' }}</small>
                <small class="text-muted text-truncate d-block" style="font-size: 11px;">{{ doctor?.designation || doctor?.department_name }}</small>
              </div>
            </div>
          </div>

          <!-- Patient Information Card (For Doctor & Patient confirmation) -->
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white mb-3">
            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2 d-flex align-items-center justify-content-between">
              <span><i class="icofont-user-alt-4 text-success mr-2"></i> রোগীর বিবরণ</span>
              <span class="badge badge-success px-2 py-1 font-12">সিরিয়াল #{{ appointment?.serial_number }}</span>
            </h6>
            
            <div class="mb-2">
              <span class="text-muted small">রোগীর নাম:</span>
              <div class="font-weight-bold text-dark">{{ appointment?.patient_name || 'নাম নেই' }}</div>
            </div>

            <div class="mb-2">
              <span class="text-muted small">মোবাইল নম্বর:</span>
              <div class="font-weight-bold text-dark font-13">{{ appointment?.patient_phone || 'তথ্য নেই' }}</div>
            </div>

            <div class="mb-2">
              <span class="text-muted small">বুকিং তারিখ ও সময়:</span>
              <div class="font-weight-bold text-dark font-12">
                {{ formatDate(appointment?.appointment_date) }} ({{ appointment?.appointment_time }})
              </div>
            </div>

            <div v-if="appointment?.reason_for_visit" class="p-2 rounded bg-light border mt-2">
              <span class="text-muted small d-block font-weight-bold">সমস্যা / প্রধান উপসর্গ:</span>
              <small class="text-dark font-weight-bold">{{ appointment.reason_for_visit }}</small>
            </div>
          </div>

          <!-- Doctor Quick Action: Prescription or Patient Vitals Shortcut -->
          <div v-if="isDoctor" class="card border-0 shadow-sm rounded-lg p-3 bg-white mb-3 border-left border-primary" style="border-left-width: 4px !important;">
            <h6 class="font-weight-bold text-primary mb-2">
              <i class="icofont-prescription mr-1"></i> ডাক্তারের জন্য টুলস
            </h6>
            <p class="small text-muted mb-2">কল চলাকালীন রোগীকে সরাসরি ই-প্রেসক্রিপশন লিখে দিন।</p>
            <router-link 
              :to="'/doctor/dashboard'" 
              class="btn btn-sm btn-primary btn-block font-weight-bold py-2 shadow-sm"
              target="_blank"
            >
              <i class="icofont-edit mr-1"></i> প্রেসক্রিপশন ও হিস্ট্রি প্যানেল
            </router-link>
          </div>

          <!-- Instructions Card for Patient -->
          <div v-else class="card border-0 shadow-sm rounded-lg p-3 bg-white mb-3">
            <h6 class="font-weight-bold text-dark mb-2">
              <i class="icofont-shield-alt text-success mr-1"></i> পরামর্শ ও গোপনীয়তা
            </h6>
            <ul class="pl-3 mb-0 text-muted small" style="line-height: 1.6;">
              <li>কল শেষে ডাক্তার আপনার জন্য প্রেসক্রিপশন তৈরি করবেন।</li>
              <li>প্রেসক্রিপশনটি আপনার ড্যাশবোর্ডে ও ট্র্যাকিং পেজে স্বয়ংক্রিয়ভাবে সংরক্ষিত থাকবে।</li>
              <li>ভিডিও সম্পূর্ণ এনক্রিপ্টেড এবং নিরাপদ।</li>
            </ul>
          </div>

          <!-- Exit Call Button -->
          <button 
            @click="leaveChamber" 
            class="btn btn-outline-danger btn-block py-2 font-weight-bold shadow-sm rounded-pill"
          >
            <i class="icofont-ui-power mr-1"></i> চেম্বার সমাপ্ত করুন
          </button>

        </div>

      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'VideoChamber',
  props: {
    appointment_number: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      appointmentNumber: '',
      appointment: null,
      doctor: null,
      roomName: '',
      isDoctor: false,
      isLoading: true,
      isConnected: false,
      hasError: false,
      errorMessage: '',
      jitsiApi: null,
      lowBandwidthMode: false,
    };
  },
  async created() {
    this.appointmentNumber = this.appointment_number || this.$route.params.appointment_number;
    if (!this.appointmentNumber) {
      this.hasError = true;
      this.errorMessage = 'কোনো বৈধ অ্যাপয়েন্টমেন্ট নম্বর প্রদান করা হয়নি।';
      this.isLoading = false;
      return;
    }
    await this.fetchChamberData();
  },
  beforeUnmount() {
    this.destroyJitsi();
  },
  methods: {
    async fetchChamberData() {
      this.isLoading = true;
      this.hasError = false;
      try {
        const res = await axios.get(`/appointments/video-chamber/${this.appointmentNumber}`);
        if (res.data.success) {
          this.appointment = res.data.appointment;
          this.doctor = res.data.doctor;
          this.roomName = res.data.room_name;
          this.isDoctor = res.data.is_doctor;

          // Determine user role and load Jitsi Meet
          this.$nextTick(() => {
            this.initJitsiVideoRoom();
          });
        } else {
          this.hasError = true;
          this.errorMessage = res.data.message || 'ভিডিও চেম্বার লোড করা সম্ভব হয়নি।';
          this.isLoading = false;
        }
      } catch (err) {
        console.error('Error fetching chamber details:', err);
        this.hasError = true;
        this.errorMessage = err.response?.data?.message || 'ভিডিও চেম্বার সার্ভারে সংযোগ স্থাপন করা সম্ভব হয়নি। অনুগ্রহ করে ইন্টারনেট সংযোগ পরীক্ষা করুন।';
        this.isLoading = false;
      }
    },

    loadJitsiScript() {
      return new Promise((resolve, reject) => {
        if (window.JitsiMeetExternalAPI) {
          resolve();
          return;
        }
        const existingScript = document.getElementById('jitsi-external-api-script');
        if (existingScript) {
          existingScript.onload = () => resolve();
          return;
        }
        const script = document.createElement('script');
        script.id = 'jitsi-external-api-script';
        script.src = 'https://meet.jit.si/external_api.js';
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load Jitsi API'));
        document.body.appendChild(script);
      });
    },

    async initJitsiVideoRoom() {
      try {
        await this.loadJitsiScript();

        const container = document.getElementById('sukhi-manush-video-chamber-container');
        if (!container) {
          setTimeout(() => this.initJitsiVideoRoom(), 300);
          return;
        }

        // Clean any existing instance
        this.destroyJitsi();

        // 100% Zero-Email: Display Name only
        const displayName = this.isDoctor 
          ? (this.doctor?.name ? 'ডাঃ ' + this.doctor.name : 'ডাক্তার')
          : (this.appointment?.patient_name || 'রোগী');

        const domain = 'meet.jit.si';
        const options = {
          roomName: this.roomName,
          width: '100%',
          height: '100%',
          parentNode: container,
          userInfo: {
            displayName: displayName
          },
          configOverwrite: {
            prejoinPageEnabled: false,
            prejoinConfig: {
              enabled: false
            },
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            disableDeepLinking: true, // CRITICAL FOR RURAL PHONES: Disables "Download Mobile App" banner
            enableWelcomePage: false,
            enableClosePage: false,
            requireDisplayName: false,
            readOnlyName: true,
            p2p: {
              enabled: true
            },
            toolbarButtons: [
              'microphone',
              'camera',
              'closedcaptions',
              'desktop',
              'fullscreen',
              'fodeviceselection',
              'hangup',
              'chat',
              'tileview',
              'videoquality'
            ]
          },
          interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            SHOW_BRAND_WATERMARK: false,
            DEFAULT_BACKGROUND: '#141824',
            TOOLBAR_ALWAYS_VISIBLE: true,
            DISABLE_JOIN_LEAVE_NOTIFICATIONS: false,
            MOBILE_APP_PROMO: false
          }
        };

        this.jitsiApi = new window.JitsiMeetExternalAPI(domain, options);

        // Event Listeners
        this.jitsiApi.addEventListener('videoConferenceJoined', () => {
          this.isLoading = false;
          this.isConnected = true;
          this.hasError = false;
        });

        this.jitsiApi.addEventListener('readyToClose', () => {
          this.isConnected = false;
          this.leaveChamber();
        });

        this.jitsiApi.addEventListener('cameraError', (err) => {
          console.warn('Camera error:', err);
          this.hasError = true;
          this.errorMessage = 'ক্যামেরা সংযোগ পাওয়া যায়নি। অনুগ্রহ করে ক্যামেরা অ্যাক্সেস Allow করুন।';
        });

        this.jitsiApi.addEventListener('micError', (err) => {
          console.warn('Microphone error:', err);
        });

        // Set safety timeout in case connection takes time
        setTimeout(() => {
          if (this.isLoading) {
            this.isLoading = false;
            this.isConnected = true;
          }
        }, 5000);

      } catch (err) {
        console.error('Failed to initialize in-app video room:', err);
        this.isLoading = false;
        this.hasError = true;
        this.errorMessage = 'ভিডিও স্ক্রিন লোড করা সম্ভব হয়নি। সরাসরি বিকল্প Google Meet বা টেলিফোনে যোগাযোগ করতে পারেন।';
      }
    },

    toggleLowBandwidth() {
      this.lowBandwidthMode = !this.lowBandwidthMode;
      if (this.jitsiApi) {
        try {
          if (this.lowBandwidthMode) {
            // Mute video to preserve audio clarity for weak 2G/3G in rural areas
            this.jitsiApi.executeCommand('toggleVideo');
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'info',
              title: 'লো-ডাটা মোড সক্রিয়: দুর্বল নেটওয়ার্কে কথা স্পষ্ট থাকবে',
              showConfirmButton: false,
              timer: 3000
            });
          } else {
            this.jitsiApi.executeCommand('toggleVideo');
          }
        } catch (e) {
          console.warn('Command execute error:', e);
        }
      }
    },

    reloadChamber() {
      this.isLoading = true;
      this.hasError = false;
      this.initJitsiVideoRoom();
    },

    destroyJitsi() {
      if (this.jitsiApi) {
        try {
          this.jitsiApi.dispose();
        } catch (e) {
          console.warn('Jitsi dispose error:', e);
        }
        this.jitsiApi = null;
      }
    },

    leaveChamber() {
      this.destroyJitsi();
      Swal.fire({
        title: 'ভিডিও কনসালটেশন সমাপ্ত',
        text: 'আপনার ডিজিটাল চেম্বার সেশন শেষ হয়েছে। প্রেসক্রিপশন ও তথ্যের জন্য ড্যাশবোর্ডে ফিরতে চান?',
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ড্যাশবোর্ডে যান',
        cancelButtonText: 'হোম পেজে যান'
      }).then((result) => {
        if (result.isConfirmed) {
          if (this.isDoctor) {
            this.$router.push('/doctor/dashboard');
          } else {
            this.$router.push('/patient/dashboard');
          }
        } else {
          this.$router.push('/');
        }
      });
    },

    goBack() {
      if (window.history.length > 1) {
        this.$router.back();
      } else {
        this.$router.push('/');
      }
    },

    formatDate(dateStr) {
      if (!dateStr) return '';
      try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('bn-BD', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      } catch (e) {
        return dateStr;
      }
    }
  }
};
</script>

<style scoped>
.video-chamber-page {
  font-family: inherit;
}

.blinking-dot {
  width: 9px;
  height: 9px;
  background-color: #ffffff;
  border-radius: 50%;
  display: inline-block;
  animation: pulse-ring 1.5s infinite;
}

@keyframes pulse-ring {
  0% {
    transform: scale(0.9);
    opacity: 1;
  }
  50% {
    transform: scale(1.3);
    opacity: 0.6;
  }
  100% {
    transform: scale(0.9);
    opacity: 1;
  }
}

.max-w-500 {
  max-width: 500px;
}
</style>
