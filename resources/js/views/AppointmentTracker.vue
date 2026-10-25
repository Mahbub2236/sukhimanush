<template>
  <div class="appointment-tracker-page pb-5">
    <!-- Page Header Banner -->
    <section class="page-title bg-1 py-4 py-md-5 text-white position-relative" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(34, 58, 102, 0.82) 100%), url('/images/bg/22.jpg') center/cover no-repeat;">
      <div class="overlay"></div>
      <div class="container position-relative" style="z-index: 2;">
        <div class="row">
          <div class="col-md-12 text-center text-white">
            <div class="block">
              <span class="badge badge-danger px-3 py-1 font-weight-bold mb-2">রিয়েল-টাইম স্বাস্থ্য সেবা</span>
              <h1 class="text-capitalize mb-2 text-2xl md:text-3xl font-weight-bold text-white" style="color: #ffffff !important;">লাইভ চেম্বার সিরিয়াল ট্র্যাকার</h1>
              <p class="text-light small max-w-600 mx-auto mb-0" style="color: #e2e8f0 !important;">
                চেম্বারে দীর্ঘক্ষণ বসে না থেকে মোবাইলেই লাইভ দেখুন কত নম্বর সিরিয়াল চলছে এবং আপনার ডাকের সম্ভাব্য সময়।
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Search / Lookup Section -->
    <section class="section gray-bg pt-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-9">
            <!-- Search Box Card -->
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border">
              <h5 class="font-weight-bold mb-2 text-dark d-flex align-items-center">
                <i class="icofont-search-document text-danger mr-2 font-22"></i> 
                <span>সিরিয়াল ট্র্যাকিং কোড অথবা মোবাইল নম্বর দিন:</span>
              </h5>
              <p class="text-muted small mb-3">
                বুকিং কনফার্মেশন রেফারেন্স (যেমনঃ <code>NOV-2026-8942</code>) অথবা বুকিংয়ে ব্যবহৃত ফোন নম্বর দিয়ে খুঁজুন।
              </p>

              <form @submit.prevent="performSearch" class="d-flex flex-column flex-sm-row" style="gap: 8px;">
                <div class="input-group flex-grow-1">
                  <input 
                    type="text" 
                    v-model="searchQuery" 
                    data-no-voice
                    class="form-control form-control-lg font-weight-bold text-dark" 
                    placeholder="যেমনঃ 01711223344 অথবা NOV-2026-8942"
                    required
                    style="font-size: 15px;"
                  >
                  <div class="input-group-append">
                    <VoiceInputBtn 
                      asDigits 
                      label="মুখে বলুন"
                      customClass="btn btn-outline-danger px-3 font-weight-bold d-flex align-items-center"
                      @result="val => { searchQuery = val; performSearch(); }" 
                    />
                  </div>
                </div>
                <button type="submit" class="btn btn-danger btn-round-full px-4 font-weight-bold d-flex align-items-center justify-content-center shadow-sm" :disabled="loading" style="min-height: 48px; white-space: nowrap;">
                  <span v-if="loading">
                    <span class="spinner-border spinner-border-sm mr-2"></span> অনুসন্ধান...
                  </span>
                  <span v-else>
                    <i class="icofont-search mr-1"></i> লাইভ ট্র্যাকিং দেখুন
                  </span>
                </button>
              </form>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
              <p class="text-muted mt-2 font-weight-bold">চেম্বার ডাটাবেজ থেকে তথ্য লোড হচ্ছে...</p>
            </div>

            <!-- Results Section -->
            <div v-else-if="searched">
              <div v-if="appointments.length === 0" class="alert alert-warning text-center p-4 bg-white shadow-sm rounded-lg border">
                <i class="icofont-exclamation-circle display-4 text-warning mb-2 d-block"></i>
                <h5 class="font-weight-bold text-dark">কোনো সিরিয়াল পাওয়া যায়নি</h5>
                <p class="text-muted mb-0">"<strong>{{ searchQuery }}</strong>" নম্বরের সাথে মিল রয়েছে এমন কোনো সক্রিয় বুকিং নেই। অনুগ্রহ করে নম্বরটি যাচাই করে পুনরায় চেষ্টা করুন।</p>
              </div>

              <div v-else>
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="font-weight-bold text-dark mb-0">
                    মোট <span class="badge badge-primary px-2 py-1">{{ appointments.length }}</span> টি অ্যাপয়েন্টমেন্ট পাওয়া গেছে
                  </h6>
                  <button class="btn btn-sm btn-outline-secondary font-weight-bold rounded-pill" @click="refreshAllQueues">
                    <i class="icofont-refresh mr-1"></i> লাইভ রিফ্রেশ
                  </button>
                </div>

                <div v-for="apt in appointments" :key="apt.id" :id="'ticket-' + apt.id" class="appointment-pass bg-white rounded-lg shadow-sm p-4 mb-4 border position-relative overflow-hidden">
                  <!-- EMERGENCY BYPASS LIVE PATIENT ALERT BANNER -->
                  <div v-if="apt.queue?.is_emergency_active" class="p-3 mb-3 rounded-lg text-white font-weight-bold d-flex align-items-center shadow" style="background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%); border-left: 6px solid #fef08a;">
                    <div class="mr-3" style="font-size: 32px; line-height: 1;">🚨</div>
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <span class="text-warning font-weight-bold text-uppercase font-14">
                          চেম্বারে জরুরি রোগী প্রবেশ করেছে (Emergency Bypass Active)
                        </span>
                        <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold small mt-1 mt-md-0">
                          +১৫ মিনিট সাময়িক বিলম্ব
                        </span>
                      </div>
                      <div class="small font-weight-normal text-white mt-1" style="font-size: 13px; line-height: 1.5;">
                        জরুরি মানবিক ও চিকিৎসার প্রয়োজনে ডাক্তার বর্তমানে একজন আশঙ্কাজনক জরুরি রোগী
                        <strong v-if="apt.queue?.emergency_patient_name">({{ apt.queue.emergency_patient_name }})</strong>
                        <span v-if="apt.queue?.emergency_reason"> - {{ apt.queue.emergency_reason }}</span>
                        দেখছেন। আপনার সিরিয়াল নম্বর অপরিবর্তিত থাকবে। জরুরি চিকিৎসা শেষ হওয়া মাত্রই নিয়মিত সিরিয়াল চালু হবে।
                      </div>
                    </div>
                  </div>

                  <!-- 1. LIVE QUEUE HERO CARD (Uber-style Where is My Turn?) -->
                  <div class="live-queue-banner p-3 p-md-4 rounded-lg mb-4 text-white position-relative" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 60%, #0284c7 100%);">
                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom border-white-50 pb-2 mb-3">
                      <div class="d-flex align-items-center">
                        <span class="spinner-grow spinner-grow-sm text-warning mr-2" role="status"></span>
                        <strong class="font-15">লাইভ চেম্বার কিউ (Live Queue)</strong>
                      </div>
                      <div class="badge px-3 py-1 font-weight-bold" :class="apt.queue?.is_emergency_active ? 'badge-danger' : getDoctorStatusBadge(apt.queue?.doctor_status)">
                        {{ apt.queue?.is_emergency_active ? '🚨 জরুরি রোগী চলমান' : getDoctorStatusText(apt.queue?.doctor_status) }}
                      </div>
                    </div>

                    <!-- Tokens Display Row -->
                    <div class="row align-items-center text-center my-2">
                      <div class="col-6 border-right border-white-50">
                        <div class="small text-light">এখন চেম্বারে দেখছেন</div>
                        <template v-if="apt.queue?.is_emergency_active">
                          <div class="display-4 font-weight-bold text-danger my-1" style="font-size: 26px; line-height: 1.2;">
                            🚨 EMERGENCY
                          </div>
                          <div class="small text-warning font-weight-bold text-truncate">
                            {{ apt.queue?.emergency_reason || 'আশঙ্কাজনক জরুরি রোগী' }}
                          </div>
                        </template>
                        <template v-else>
                          <div class="display-4 font-weight-bold text-warning my-1">
                            #{{ apt.queue?.current_serving_serial || apt.current_serving_serial || 1 }}
                          </div>
                          <div class="small text-light text-truncate">
                            {{ apt.queue?.called_patient_name ? ('রোগী: ' + apt.queue.called_patient_name) : 'চলমান সিরিয়াল' }}
                          </div>
                        </template>
                      </div>

                      <div class="col-6">
                        <div class="small text-light">আপনার টোকেন নম্বর</div>
                        <div class="display-4 font-weight-bold text-white my-1">
                          #{{ apt.serial_number }}
                        </div>
                        <div class="small text-light">
                          মোট সিরিয়াল: {{ apt.queue?.total_serials || apt.serial_number }} জন
                        </div>
                      </div>
                    </div>

                    <!-- Progress Bar & Patients Ahead -->
                    <div class="mt-3 bg-white-20 p-3 rounded text-white small">
                      <div class="d-flex justify-content-between font-weight-bold mb-1">
                        <span>
                          <i class="icofont-users mr-1"></i> আপনার সামনে আছেন: 
                          <strong class="text-warning font-16">{{ apt.queue ? apt.queue.patients_ahead : Math.max(0, apt.serial_number - 1) }} জন</strong>
                        </span>
                        <span>
                          <i class="icofont-clock-time mr-1"></i> আনুমানিক অপেক্ষা: 
                          <strong class="text-warning font-16">{{ apt.queue ? apt.queue.estimated_wait_minutes : (Math.max(0, apt.serial_number - 1) * 12) }} মিনিট</strong>
                        </span>
                      </div>
                      <div class="progress" style="height: 8px; border-radius: 4px; background: rgba(255,255,255,0.25);">
                        <div 
                          class="progress-bar bg-warning" 
                          role="progressbar" 
                          :style="{ width: calculateProgress(apt) + '%' }" 
                          aria-valuemin="0" 
                          aria-valuemax="100"
                        ></div>
                      </div>
                    </div>

                    <!-- Milestone Alert Notification Box -->
                    <div v-if="apt.queue && apt.queue.milestone_alert" class="alert alert-light mt-3 mb-0 p-2 text-dark font-weight-bold small d-flex align-items-center shadow-sm">
                      <i class="icofont-megaphone font-20 text-danger mr-2"></i>
                      <span>{{ apt.queue.milestone_alert }}</span>
                    </div>
                  </div>

                  <!-- 2. Appointment Details Grid -->
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <span class="text-muted small">ডাক্তারের নাম ও চেম্বার:</span>
                      <h6 class="font-weight-bold mb-1 text-dark">{{ apt.doctor?.name }}</h6>
                      <small class="text-primary font-weight-bold d-block">{{ apt.doctor?.specialization }}</small>
                      <small class="text-muted"><i class="icofont-google-map text-danger"></i> {{ apt.doctor?.chamber_address }}</small>
                    </div>

                    <div class="col-md-6 mb-3 text-md-right">
                      <span class="text-muted small">বুকিং তারিখ ও সময়:</span>
                      <h6 class="font-weight-bold text-dark mb-0">{{ formatDate(apt.appointment_date) }}</h6>
                      <span class="badge badge-secondary mt-1">সময়: {{ apt.appointment_time }}</span>
                      <div class="small text-success font-weight-bold mt-1">
                        কনসালটেশন ফি: ৳{{ apt.consultation_fee }} ({{ apt.payment_status === 'paid' ? 'পরিশোধিত' : 'চেম্বারে প্রদেয়' }})
                      </div>
                    </div>

                    <div class="col-md-6 mb-2">
                      <span class="text-muted small">রোগীর নাম ও ফোন:</span>
                      <h6 class="font-weight-bold mb-0 text-dark">{{ apt.patient_name }}</h6>
                      <small class="text-muted">{{ apt.patient_phone }}</small>
                    </div>

                    <div class="col-md-6 mb-2 text-md-right">
                      <span class="text-muted small">ট্র্যাকিং রেফারেন্স:</span>
                      <div class="font-weight-bold text-primary font-15">{{ apt.appointment_number }}</div>
                    </div>
                  </div>

                  <!-- 3. Bottom Action Buttons -->
                  <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 border-top mt-2" style="gap: 8px;">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                      <!-- 1-Click Video Chamber Button for Online Consultations -->
                      <a 
                        v-if="apt.consultation_type === 'online_video' && apt.status !== 'cancelled'"
                        :href="'/video-chamber/' + apt.appointment_number"
                        target="_blank"
                        class="btn btn-sm btn-success font-weight-bold rounded-pill px-3 shadow-sm"
                      >
                        <i class="icofont-video-cam mr-1"></i> ভিডিও কলে যোগ দিন
                      </a>

                      <!-- 1-Click WhatsApp Share Link -->
                      <a 
                        :href="getWhatsAppShareUrl(apt)" 
                        target="_blank" 
                        class="btn btn-sm font-weight-bold rounded-pill px-3 shadow-sm d-inline-flex align-items-center"
                        style="background: #25D366 !important; border-color: #25D366 !important; color: #ffffff !important;"
                        title="হোয়াটসঅ্যাপে বুকিং স্লিপ ও লাইভ ট্র্যাকিং লিংক পাঠান"
                      >
                        <i class="fa-brands fa-whatsapp mr-1.5 font-16"></i> WhatsApp রিসিট
                      </a>

                      <button class="btn btn-sm btn-outline-dark font-weight-bold rounded-pill px-3" @click="printPass(apt.id)">
                        <i class="icofont-print mr-1"></i> স্লিপ প্রিন্ট করুন
                      </button>
                      <a v-if="apt.doctor?.phone" :href="'tel:' + apt.doctor.phone" class="btn btn-sm btn-outline-primary font-weight-bold rounded-pill px-3">
                        <i class="icofont-phone mr-1"></i> চেম্বারে কল
                      </a>
                    </div>

                    <button 
                      v-if="apt.status !== 'cancelled' && apt.status !== 'completed'"
                      class="btn btn-sm btn-outline-danger font-weight-bold rounded-pill px-3" 
                      @click="cancelAppointment(apt)"
                    >
                      <i class="icofont-ui-close mr-1"></i> সিরিয়াল বাতিল করুন
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'AppointmentTracker',
  data() {
    return {
      searchQuery: '',
      appointments: [],
      loading: false,
      searched: false,
      pollTimer: null,
    };
  },
  mounted() {
    if (this.$route.query.query) {
      this.searchQuery = this.$route.query.query;
      this.performSearch();
    }
  },
  beforeUnmount() {
    if (this.pollTimer) {
      clearInterval(this.pollTimer);
    }
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '';
      const cleanDate = dateStr.includes('T') ? dateStr.split('T')[0] : dateStr;
      const parts = cleanDate.split('-');
      if (parts.length === 3) {
        const year = parts[0];
        const month = parts[1] - 1;
        const day = parts[2];
        const d = new Date(year, month, day);
        return d.toLocaleDateString('bn-BD', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        });
      }
      return cleanDate;
    },
    async performSearch() {
      if (!this.searchQuery) return;
      this.loading = true;
      this.searched = true;

      try {
        const res = await axios.get('/appointments/track', {
          params: { query: this.searchQuery }
        });
        this.appointments = res.data.appointments || [];

        // Load live queue status for each appointment
        await this.loadQueueStatusForAll();

        // Start auto-poll every 15 seconds to keep queue alive
        if (this.pollTimer) clearInterval(this.pollTimer);
        this.pollTimer = setInterval(() => {
          this.loadQueueStatusForAll();
        }, 15000);

      } catch (err) {
        console.error('Error tracking appointment:', err);
        this.appointments = [];
      } finally {
        this.loading = false;
      }
    },
    async loadQueueStatusForAll() {
      for (const apt of this.appointments) {
        try {
          const qRes = await axios.get(`/queue/token/${apt.id}`);
          if (qRes.data.success) {
            apt.queue = qRes.data;
            if (apt.queue.is_emergency_active && !apt._emergencyAnnounced) {
              apt._emergencyAnnounced = true;
              this.speakEmergencyAnnouncement(apt);
            }
            if (apt.queue.is_my_turn && !apt._announced) {
              apt._announced = true;
              this.speakTurnAnnouncement(apt);
            }
          }
        } catch (e) {
          console.warn('Queue status poll error:', e);
        }
      }
      this.$forceUpdate();
    },
    refreshAllQueues() {
      this.loadQueueStatusForAll();
    },
    speakEmergencyAnnouncement(apt) {
      if ('speechSynthesis' in window) {
        const text = 'জরুরি বিজ্ঞপ্তি: চেম্বারে আশঙ্কাজনক জরুরি রোগী দেখা হচ্ছে। আপনার সিরিয়াল সাময়িকভাবে স্থগিত। অনুগ্রহ করে অপেক্ষা করুন।';
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'bn-BD';
        window.speechSynthesis.speak(utterance);
      }
    },
    speakTurnAnnouncement(apt) {
      if ('speechSynthesis' in window) {
        const text = `সিরিয়াল নম্বর ${apt.serial_number}, আপনার সিরিয়াল ডাকা হয়েছে। অনুগ্রহ করে ডাক্তারের চেম্বারে প্রবেশ করুন।`;
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'bn-BD';
        window.speechSynthesis.speak(utterance);
      }
    },
    calculateProgress(apt) {
      if (!apt.queue) return 50;
      const current = apt.queue.current_serving_serial || 1;
      const my = apt.serial_number || 1;
      if (current >= my) return 100;
      const total = apt.queue.total_serials || my;
      return Math.min(100, Math.round((current / my) * 100));
    },
    getDoctorStatusText(status) {
      switch (status) {
        case 'on_the_way': return 'ডাক্তার পথে আছেন';
        case 'paused': return 'সিরিয়াল সাময়িক বিরতিতে';
        case 'completed': return 'আজকের চেম্বার সম্পন্ন';
        case 'not_arrived': return 'ডাক্তার পৌঁছাননি';
        case 'consultation_started':
        default:
          return 'ডাক্তার চেম্বারে রোগী দেখছেন';
      }
    },
    getDoctorStatusBadge(status) {
      switch (status) {
        case 'on_the_way': return 'badge-warning text-dark';
        case 'paused': return 'badge-secondary text-white';
        case 'completed': return 'badge-dark text-white';
        case 'not_arrived': return 'badge-danger text-white';
        case 'consultation_started':
        default:
          return 'badge-success text-white';
      }
    },
    printPass(id) {
      const el = document.getElementById('ticket-' + id);
      if (!el) {
        window.print();
        return;
      }
      const printWindow = window.open('', '_blank', 'width=750,height=600');
      if (!printWindow) {
        window.print();
        return;
      }
      printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
          <title>Appointment Serial Pass - সুখী মানুষ (Sukhi Manush)</title>
          <link rel="stylesheet" href="/plugins/bootstrap/css/bootstrap.min.css">
          <style>
            body { font-family: 'Roboto', sans-serif; background: #fff; padding: 20px; }
            .appointment-pass { border: 2px solid #1e3c72; border-radius: 12px; padding: 25px; }
          </style>
        </head>
        <body>
          <div class="appointment-pass">
            ${el.innerHTML}
          </div>
          <script>
            window.onload = function() {
              window.print();
              window.onafterprint = function() { window.close(); };
            };
          <\/script>
        </body>
        </html>
      `);
      printWindow.document.close();
    },
    async cancelAppointment(apt) {
      const result = await Swal.fire({
        title: 'সিরিয়াল বাতিল করতে চান?',
        text: `আপনি কি নিশ্চিত যে সিরিয়াল #${apt.serial_number} বাতিল করতে চান?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e12454',
        cancelButtonColor: '#223a66',
        confirmButtonText: 'হ্যাঁ, বাতিল করুন',
        cancelButtonText: 'না',
      });

      if (result.isConfirmed) {
        try {
          const res = await axios.patch(`/staff-portal/appointments/${apt.id}/status`, {
            status: 'cancelled'
          });
          if (res.data.success) {
            apt.status = 'cancelled';
            Swal.fire('বাতিল হয়েছে', 'আপনার সিরিয়ালটি বাতিল করা হয়েছে।', 'success');
          }
        } catch (err) {
          Swal.fire('ত্রুটি', err.response?.data?.message || 'সিরিয়াল বাতিল করা সম্ভব হয়নি।', 'error');
        }
      }
    },
    getWhatsAppShareUrl(apt) {
      if (!apt) return '#';
      const origin = window.location.origin;
      const docName = apt.doctor?.name || 'বিশেষজ্ঞ ডাক্তার';
      const docSpecialty = apt.doctor?.specialization || '';
      const isVideo = apt.consultation_type === 'online_video';
      const trackingLink = `${origin}/track-appointment?serial=${apt.appointment_number}`;
      const videoLink = `${origin}/video-chamber/${apt.appointment_number}`;

      let msg = `🏥 *সুখী মানুষ (Sukhi Manush) ডিজিটাল স্বাস্থ্যসেবা*\n` +
        `👤 রোগী: ${apt.patient_name || ''}\n` +
        `🔢 সিরিয়াল: #${apt.serial_number || ''}\n` +
        `🩺 ডাক্তার: ডাঃ ${docName} (${docSpecialty})\n` +
        `📅 তারিখ ও সময়: ${this.formatDate(apt.appointment_date)} (${apt.appointment_time || ''})\n` +
        `📍 চেম্বার/মাধ্যম: ${isVideo ? '🎥 অনলাইন ভিডিও চেম্বার' : (apt.doctor?.chamber_address || 'চেম্বার')}\n\n`;

      if (isVideo) {
        msg += `🎥 *১-ক্লিক ভিডিও কলে যুক্ত হন:*\n${videoLink}\n\n`;
      }
      msg += `🔍 *লাইভ কিউ ট্র্যাকিং লিংক:*\n${trackingLink}`;

      let phone = (apt.patient_phone || '').replace(/[^\d]/g, '');
      if (phone.startsWith('01')) phone = '88' + phone;
      return phone ? `https://wa.me/${phone}?text=${encodeURIComponent(msg)}` : `https://wa.me/?text=${encodeURIComponent(msg)}`;
    },
  },
};
</script>

<style scoped>
.bg-white-20 {
  background: rgba(255, 255, 255, 0.15);
}
</style>
