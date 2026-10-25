<template>
  <div class="privacy-policy-page bg-slate-50/60 min-h-screen pb-5">
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
              <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-success-subtle border border-success text-success-emphasis font-weight-bold mb-3 shadow-sm" style="font-size: 13px; background: rgba(16, 185, 129, 0.15); color: #34d399 !important; border-color: rgba(52, 211, 153, 0.4) !important;">
                <i class="fa-solid fa-shield-halved mr-1"></i>
                <span>সরকারি স্বাস্থ্য ও আইসিটি আইন অনুযায়ী প্রস্তুতকৃত</span>
              </div>
              <h1 class="text-capitalize mb-2 text-2xl md:text-4xl font-weight-bold text-white" style="color: #ffffff !important;">
                {{ policy.title || 'প্রাইভেসি পলিসি ও স্বাস্থ্য তথ্য সুরক্ষা' }}
              </h1>
              <p class="text-light small max-w-700 mx-auto mb-3" style="color: #e2e8f0 !important;">
                {{ policy.subtitle || 'বাংলাদেশ স্বাস্থ্য অধিদপ্তর (DGHS), বিএমডিসি (BMDC) আচরণবিধি ও আইসিটি আইন ২০০৬ অনুযায়ী প্রণীত' }}
              </p>
              <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 text-xs text-slate-300">
                <span class="d-inline-flex align-items-center gap-1">
                  <i class="fa-regular fa-calendar-check text-sky-400"></i>
                  <span>কার্যকর তারিখ: <strong>{{ policy.effective_date || '০১ জানুয়ারি ২০২৬' }}</strong></span>
                </span>
                <span class="d-none d-md-inline">&bull;</span>
                <span class="d-inline-flex align-items-center gap-1">
                  <i class="fa-solid fa-file-shield text-emerald-400"></i>
                  <span>সংস্করণ: <strong>v2.4 (বাংলাদেশ স্বাস্থ্য কোড)</strong></span>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Trust Badges Strip (Govt Compliance Pillars) -->
    <section class="bg-white border-bottom py-3 shadow-sm">
      <div class="container">
        <div class="row g-3 align-items-center text-center text-md-left">
          <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <div class="p-2 rounded-circle bg-sky-50 text-sky-600 font-20">
                <i class="fa-solid fa-scale-balanced text-primary"></i>
              </div>
              <div class="text-left">
                <div class="font-weight-bold small text-dark leading-tight">ICT Act ২০০৬</div>
                <small class="text-muted" style="font-size: 11px;">সাইবার নিরাপত্তা আইন ২০২৩</small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <div class="p-2 rounded-circle bg-success-50 text-success font-20">
                <i class="fa-solid fa-user-doctor text-success"></i>
              </div>
              <div class="text-left">
                <div class="font-weight-bold small text-dark leading-tight">BMDC স্বীকৃত</div>
                <small class="text-muted" style="font-size: 11px;">ডক্টর-পেশেন্ট প্রিভিলেজ</small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <div class="p-2 rounded-circle bg-info-50 text-info font-20">
                <i class="fa-solid fa-lock text-info"></i>
              </div>
              <div class="text-left">
                <div class="font-weight-bold small text-dark leading-tight">AES-256 বিট</div>
                <small class="text-muted" style="font-size: 11px;">এন্ড-টু-এন্ড এনক্রিপশন</small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <div class="p-2 rounded-circle bg-danger-50 text-danger font-20">
                <i class="fa-solid fa-ban text-danger"></i>
              </div>
              <div class="text-left">
                <div class="font-weight-bold small text-dark leading-tight">জিরো ডেটা সেল</div>
                <small class="text-muted" style="font-size: 11px;">ডেটা বিক্রি সম্পূর্ণ নিষিদ্ধ</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content Area -->
    <div class="container py-5">
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-3 text-muted font-weight-bold">প্রাইভেসি পলিসি লোড হচ্ছে...</div>
      </div>

      <div v-else class="row">
        <!-- Sidebar Navigation (Table of Contents) -->
        <div class="col-lg-4 mb-4">
          <div class="sticky-top" style="top: 90px; z-index: 10;">
            <div class="card border-0 shadow-sm rounded-xl p-4 bg-white mb-3">
              <h5 class="font-weight-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-primary"></i>
                <span>সূচিপত্র (Table of Contents)</span>
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

              <!-- Quick Switch to Terms of Service -->
              <div class="p-3 rounded-lg bg-light border text-center">
                <div class="small text-muted mb-2">সেবা ব্যবহারের শর্তাবলী দেখতে চান?</div>
                <router-link to="/terms-of-service" class="btn btn-sm btn-outline-primary btn-block font-weight-bold">
                  <i class="fa-solid fa-file-contract mr-1"></i> টার্মস অব সার্ভিস পড়ুন
                </router-link>
              </div>
            </div>

            <!-- Print & DPO Contact Card -->
            <div class="card border-0 shadow-sm rounded-xl p-4 bg-white">
              <h6 class="font-weight-bold text-dark mb-2 d-flex align-items-center gap-2">
                <i class="fa-solid fa-user-shield text-success"></i>
                <span>ডেটা প্রটেকশন অফিসার (DPO)</span>
              </h6>
              <p class="text-muted small mb-3">
                আপনার স্বাস্থ্য তথ্য মুছে ফেলা, সংশোধন বা নিরাপত্তা সম্পর্কিত প্রশ্নের জন্য আমাদের সাথে যোগাযোগ করুন।
              </p>
              <div class="small mb-2">
                <i class="fa-solid fa-envelope text-primary mr-1"></i>
                <a href="mailto:privacy@sukhimanush.com" class="text-primary font-weight-bold">privacy@sukhimanush.com</a>
              </div>
              <div class="small mb-3">
                <i class="fa-solid fa-phone text-success mr-1"></i>
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
          <!-- Executive Summary Callout -->
          <div class="card border-0 shadow-sm rounded-xl p-4 bg-white mb-4 border-left-primary" style="border-left: 5px solid #223a66 !important;">
            <div class="d-flex align-items-start gap-3">
              <div class="p-3 rounded-xl bg-blue-50 text-primary font-24 flex-shrink-0">
                <i class="fa-solid fa-user-lock"></i>
              </div>
              <div>
                <h5 class="font-weight-bold text-dark mb-2">রোগীর স্বাস্থ্য তথ্যের সার্বভৌমিকতা</h5>
                <p class="text-secondary small mb-0 leading-relaxed">
                  {{ policy.summary || 'সুখী মানুষ প্ল্যাটফর্মে আপনার সংরক্ষিত স্বাস্থ্য তথ্য, ডিজিটাল প্রেসক্রিপশন ও মেডিকেল হিস্ট্রি শতভাগ সুরক্ষিত।' }}
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
                <div class="w-10 h-10 rounded-circle bg-sky-50 text-primary d-inline-flex align-items-center justify-content-center font-18 flex-shrink-0">
                  <i :class="['fa-solid', section.icon || 'fa-shield-halved']"></i>
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
              <span class="badge badge-success px-3 py-1 font-weight-bold">DGHS ও BMDC নির্দেশিকা সংগতিপূর্ণ</span>
            </div>
            <p class="small text-slate-300 mb-0 leading-relaxed" style="color: #cbd5e1 !important;">
              এই প্রাইভেসি পলিসি নিয়মিতভাবে বাংলাদেশ সরকারের স্বাস্থ্য ও পরিবার কল্যাণ মন্ত্রণালয়, আইসিটি বিভাগ এবং বাংলাদেশ মেডিকেল অ্যান্ড ডেন্টাল কাউন্সিল (BMDC)-এর জারি করা সর্বশেষ প্রজ্ঞাপন অনুযায়ী পর্যালোচনা ও হালনাগাদ করা হয়।
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
  name: 'PrivacyPolicy',
  data() {
    return {
      policy: {},
      loading: true,
      activeSection: 'legal-scope',
    };
  },
  mounted() {
    this.fetchPolicy();
  },
  methods: {
    async fetchPolicy() {
      this.loading = true;
      try {
        const res = await axios.get('/api/policies/privacy_policy');
        if (res.data && res.data.success) {
          this.policy = res.data.data;
          if (this.policy.sections && this.policy.sections.length > 0) {
            this.activeSection = this.policy.sections[0].id || 'sec-0';
          }
        }
      } catch (err) {
        console.error('Failed to load privacy policy:', err);
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
  color: #0284c7;
  border-left-color: #0284c7;
}
.policy-section-card {
  scroll-margin-top: 100px;
}
.policy-section-card:hover {
  box-shadow: 0 8px 24px rgba(34, 58, 102, 0.08) !important;
}
</style>
