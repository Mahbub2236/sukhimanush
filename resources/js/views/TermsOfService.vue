<template>
  <div class="terms-of-service-page bg-slate-50/60 min-h-screen pb-5">
    <!-- Page Header Banner with Dark Navy Gradient -->
    <section 
      class="page-title bg-1 py-4 py-md-5 text-white position-relative" 
      style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.90) 0%, rgba(30, 58, 102, 0.85) 100%), url('/images/bg/22.jpg') center/cover no-repeat;"
    >
      <div class="overlay"></div>
      <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
          <div class="col-lg-10 text-center text-white">
            <div class="block">
              <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-danger-subtle border border-danger text-danger-emphasis font-weight-bold mb-3 shadow-sm" style="font-size: 13px; background: rgba(239, 68, 68, 0.15); color: #f87171 !important; border-color: rgba(248, 113, 113, 0.4) !important;">
                <i class="fa-solid fa-file-contract mr-1"></i>
                <span>আইনি চুক্তি ও বিএমডিসি টেলিমেডিসিন কোড</span>
              </div>
              <h1 class="text-capitalize mb-2 text-2xl md:text-4xl font-weight-bold text-white" style="color: #ffffff !important;">
                {{ policy.title || 'টার্মস অব সার্ভিস ও ব্যবহারের শর্তাবলী' }}
              </h1>
              <p class="text-light small max-w-700 mx-auto mb-3" style="color: #e2e8f0 !important;">
                {{ policy.subtitle || 'বাংলাদেশ স্বাস্থ্য অধিদপ্তর (DGHS), বিএমডিসি অ্যাক্ট ২০১০ ও ভোক্তা অধিকার সংরক্ষণ আইন ২০০৯ অনুসারে প্রযোজ্য' }}
              </p>
              <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 text-xs text-slate-300">
                <span class="d-inline-flex align-items-center gap-1">
                  <i class="fa-regular fa-calendar-check text-sky-400"></i>
                  <span>কার্যকর তারিখ: <strong>{{ policy.effective_date || '০১ জানুয়ারি ২০২৬' }}</strong></span>
                </span>
                <span class="d-none d-md-inline">&bull;</span>
                <span class="d-inline-flex align-items-center gap-1">
                  <i class="fa-solid fa-scale-balanced text-amber-400"></i>
                  <span>আইনি এখতিয়ার: <strong>ঢাকা, বাংলাদেশ</strong></span>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CRITICAL EMERGENCY 999 ALERT BANNER (Legal Immunity & Patient Life-Safety) -->
    <section class="bg-rose-950 text-white py-3 border-b-2 border-rose-500 shadow-sm" style="background-color: #4c0519 !important;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-9 d-flex align-items-center gap-3 mb-2 mb-md-0">
            <div class="p-2 rounded-circle bg-rose-600 text-white font-22 flex-shrink-0 animate-pulse">
              <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
              <div class="font-weight-bold text-rose-200 small text-uppercase tracking-wider">জরুরি স্বাস্থ্য সতর্কবার্তা (Emergency Notice)</div>
              <p class="mb-0 text-white text-xs sm:text-sm font-medium">
                টেলিমেডিসিন কোনোভাবেই জরুরি আইসিইউ বা জীবনঘাতী পরিস্থিতির বিকল্প নয়। মারাত্মক বুকে ব্যথা, স্ট্রোক, দুর্ঘটনা বা শ্বাসকষ্টে তাৎক্ষণিক জাতীয় জরুরি সেবা <strong>৯৯৯ (999)</strong> এ কল করুন।
              </p>
            </div>
          </div>
          <div class="col-md-3 text-md-right text-center">
            <a href="tel:999" class="btn btn-danger btn-sm font-weight-bold px-3 py-1.5 rounded-pill shadow">
              <i class="fa-solid fa-phone-volume mr-1"></i> জরুরি ৯৯৯ কল করুন
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Compliance Highlights Strip -->
    <section class="bg-white border-bottom py-3 shadow-sm">
      <div class="container">
        <div class="row g-3 align-items-center text-center text-md-left">
          <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <div class="p-2 rounded-circle bg-sky-50 text-sky-600 font-20">
                <i class="fa-solid fa-user-check text-primary"></i>
              </div>
              <div class="text-left">
                <div class="font-weight-bold small text-dark leading-tight">BMDC ভেরিফাইড</div>
                <small class="text-muted" style="font-size: 11px;">লাইসেন্সধারী বিশেষজ্ঞ ডাক্তার</small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <div class="p-2 rounded-circle bg-danger-50 text-danger font-20">
                <i class="fa-solid fa-pills text-danger"></i>
              </div>
              <div class="text-left">
                <div class="font-weight-bold small text-dark leading-tight">নিষিদ্ধ ওষুধ বর্জন</div>
                <small class="text-muted" style="font-size: 11px;">মাদক বা ঘুমের ওষুধ নিষিদ্ধ</small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <div class="p-2 rounded-circle bg-success-50 text-success font-20">
                <i class="fa-solid fa-money-bill-transfer text-success"></i>
              </div>
              <div class="text-left">
                <div class="font-weight-bold small text-dark leading-tight">১০০% রিফান্ড পলিসি</div>
                <small class="text-muted" style="font-size: 11px;">স্বচ্ছ বাতিল ও অর্থ ফেরত</small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <div class="p-2 rounded-circle bg-amber-50 text-amber-600 font-20">
                <i class="fa-solid fa-building-columns text-amber-600"></i>
              </div>
              <div class="text-left">
                <div class="font-weight-bold small text-dark leading-tight">বাংলাদেশ আইন</div>
                <small class="text-muted" style="font-size: 11px;">ঢাকা আদালতের এখতিয়ার</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content Area -->
    <div class="container py-5">
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-danger" role="status"></div>
        <div class="mt-3 text-muted font-weight-bold">টার্মস অব সার্ভিস লোড হচ্ছে...</div>
      </div>

      <div v-else class="row">
        <!-- Sidebar Navigation (Table of Contents) -->
        <div class="col-lg-4 mb-4">
          <div class="sticky-top" style="top: 90px; z-index: 10;">
            <div class="card border-0 shadow-sm rounded-xl p-4 bg-white mb-3">
              <h5 class="font-weight-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-primary"></i>
                <span>শর্তাবলীর সূচিপত্র</span>
              </h5>
              <nav class="nav flex-column toc-nav">
                <a 
                  v-for="(section, idx) in policy.sections" 
                  :key="idx" 
                  :href="'#' + (section.id || 'sec-' + idx)"
                  class="nav-link py-2 px-3 rounded mb-1 text-slate-700 font-weight-medium transition-colors d-flex align-items-center justify-content-between"
                  :class="{ 'bg-primary-subtle text-primary font-weight-bold': activeSection === (section.id || 'sec-' + idx) }"
                  @click="activeSection = (section.id || 'sec-' + idx)"
                >
                  <span class="text-truncate mr-2">{{ section.title }}</span>
                  <i class="fa-solid fa-chevron-right text-xs opacity-50"></i>
                </a>
              </nav>

              <hr class="my-3">

              <!-- Quick Switch to Privacy Policy -->
              <div class="p-3 rounded-lg bg-light border text-center">
                <div class="small text-muted mb-2">স্বাস্থ্য তথ্য সুরক্ষা নীতিমালা দেখতে চান?</div>
                <router-link to="/privacy-policy" class="btn btn-sm btn-outline-success btn-block font-weight-bold">
                  <i class="fa-solid fa-shield-halved mr-1"></i> প্রাইভেসি পলিসি পড়ুন
                </router-link>
              </div>
            </div>

            <!-- Print & Legal Department Card -->
            <div class="card border-0 shadow-sm rounded-xl p-4 bg-white">
              <h6 class="font-weight-bold text-dark mb-2 d-flex align-items-center gap-2">
                <i class="fa-solid fa-gavel text-primary"></i>
                <span>আইনি ও কমপ্লায়েন্স বিভাগ</span>
              </h6>
              <p class="text-muted small mb-3">
                চিকিৎসক চুক্তি, রিফান্ড বিরোধ বা নীতিমালার যেকোনো ব্যাখ্যার জন্য আমাদের আইনি দলের সাথে যোগাযোগ করুন।
              </p>
              <div class="small mb-2">
                <i class="fa-solid fa-envelope text-primary mr-1"></i>
                <a href="mailto:legal@sukhimanush.com" class="text-primary font-weight-bold">legal@sukhimanush.com</a>
              </div>
              <div class="small mb-3">
                <i class="fa-solid fa-headset text-success mr-1"></i>
                <a href="tel:+8801712345678" class="text-dark font-weight-bold">+৮৮০ ১৭১২-৩৪৫৬৭৮</a>
              </div>

              <button class="btn btn-sm btn-light border btn-block text-muted" @click="printPage">
                <i class="fa-solid fa-print mr-1"></i> এই পাতাটি প্রিন্ট করুন
              </button>
            </div>
          </div>
        </div>

        <!-- Policy Body & Detailed Sections -->
        <div class="col-lg-8">
          <!-- Overview Card -->
          <div class="card border-0 shadow-sm rounded-xl p-4 bg-white mb-4 border-left-danger" style="border-left: 5px solid #dc2626 !important;">
            <div class="d-flex align-items-start gap-3">
              <div class="p-3 rounded-xl bg-rose-50 text-danger font-24 flex-shrink-0">
                <i class="fa-solid fa-handshake"></i>
              </div>
              <div>
                <h5 class="font-weight-bold text-dark mb-2">ব্যবহারকারীর আইনি অঙ্গীকার ও সম্মতি</h5>
                <p class="text-secondary small mb-0 leading-relaxed">
                  {{ policy.summary || 'সুখী মানুষ প্ল্যাটফর্ম ব্যবহারের পূর্বে নিয়মাবলী মনোযোগ সহকারে পড়ুন। টেলিমেডিসিন বা অনলাইন অ্যাপয়েন্টমেন্ট জরুরি জীবনঘাতী সংকটের বিকল্প নয়।' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Policy Sections Accordion/Cards -->
          <div class="space-y-4">
            <div 
              v-for="(section, idx) in policy.sections" 
              :key="idx" 
              :id="section.id || 'sec-' + idx"
              class="card border-0 shadow-sm rounded-xl p-4 p-md-4 bg-white policy-section-card transition-all"
            >
              <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3">
                <div class="w-10 h-10 rounded-circle bg-rose-50 text-danger d-inline-flex align-items-center justify-content-center font-18 flex-shrink-0">
                  <i :class="['fa-solid', section.icon || 'fa-file-lines']"></i>
                </div>
                <div>
                  <h4 class="font-weight-bold text-dark mb-0 font-18 md:font-20">
                    {{ section.title }}
                  </h4>
                  <small v-if="section.summary" class="text-muted d-block mt-0.5">
                    {{ section.summary }}
                  </small>
                </div>
              </div>

              <div class="text-secondary text-sm leading-relaxed" style="line-height: 1.8; color: #334155 !important;">
                <p class="mb-0 whitespace-pre-line">{{ section.content }}</p>
              </div>
            </div>
          </div>

          <!-- Footer Legal Assurance Note -->
          <div class="mt-4 p-4 rounded-xl bg-slate-900 text-white shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-2">
              <span class="badge badge-primary px-3 py-1 font-weight-bold">ভোক্তা অধিকার ও সরকারি আইন দ্বারা সংরক্ষিত</span>
            </div>
            <p class="small text-slate-300 mb-0 leading-relaxed" style="color: #cbd5e1 !important;">
              এই ব্যবহারের শর্তাবলী বাংলাদেশ সরকারের ভোক্তা অধিকার সংরক্ষণ আইন ২০০৯ এবং সাইবার নিরাপত্তা আইন ২০২৩-এর সাথে সম্পূর্ণ সংগতিপূর্ণ। কোনো প্রশ্ন বা ব্যাখ্যার প্রয়োজন হলে অনুগ্রহ করে আইনি বিভাগে যোগাযোগ করুন।
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'TermsOfService',
  data() {
    return {
      policy: {},
      loading: true,
      activeSection: 'emergency-disclaimer',
    };
  },
  mounted() {
    this.fetchPolicy();
  },
  methods: {
    async fetchPolicy() {
      this.loading = true;
      try {
        const res = await axios.get('/api/policies/terms_of_service');
        if (res.data && res.data.success) {
          this.policy = res.data.data;
          if (this.policy.sections && this.policy.sections.length > 0) {
            this.activeSection = this.policy.sections[0].id || 'sec-0';
          }
        }
      } catch (err) {
        console.error('Failed to load terms of service:', err);
      } finally {
        this.loading = false;
      }
    },
    printPage() {
      window.print();
    },
  },
};
</script>

<style scoped>
.toc-nav .nav-link {
  font-size: 13px;
  color: #475569;
  border-left: 3px solid transparent;
  transition: all 0.2s ease;
}
.toc-nav .nav-link:hover {
  background-color: #f1f5f9;
  color: #e11d48;
  border-left-color: #e11d48;
}
.policy-section-card {
  scroll-margin-top: 100px;
}
.policy-section-card:hover {
  box-shadow: 0 8px 24px rgba(225, 36, 84, 0.08) !important;
}
</style>
