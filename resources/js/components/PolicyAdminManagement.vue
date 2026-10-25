<template>
  <div class="policy-admin-management">
    <!-- Top Selector Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
      <div class="d-flex align-items-center gap-2">
        <button 
          class="btn font-weight-bold d-flex align-items-center gap-2 px-3 py-2 rounded-lg"
          :class="activeKey === 'privacy_policy' ? 'btn-primary shadow-sm' : 'btn-outline-secondary'"
          @click="selectPolicy('privacy_policy')"
        >
          <i class="fa-solid fa-shield-halved"></i>
          <span>প্রাইভেসি পলিসি (Privacy Policy)</span>
        </button>

        <button 
          class="btn font-weight-bold d-flex align-items-center gap-2 px-3 py-2 rounded-lg"
          :class="activeKey === 'terms_of_service' ? 'btn-danger shadow-sm' : 'btn-outline-secondary'"
          @click="selectPolicy('terms_of_service')"
        >
          <i class="fa-solid fa-file-contract"></i>
          <span>টার্মস অব সার্ভিস (Terms of Service)</span>
        </button>
      </div>

      <!-- Action Buttons -->
      <div class="d-flex align-items-center gap-2">
        <a 
          :href="activeKey === 'privacy_policy' ? '/privacy-policy' : '/terms-of-service'" 
          target="_blank" 
          class="btn btn-sm btn-outline-info font-weight-bold"
        >
          <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> লাইভ পেজ দেখুন
        </a>

        <button 
          class="btn btn-sm btn-outline-warning font-weight-bold" 
          @click="confirmResetDefault"
          :disabled="loading || saving"
        >
          <i class="fa-solid fa-rotate-left mr-1"></i> সরকারি ডিফল্ট ড্রাফট রিস্টোর
        </button>

        <button 
          class="btn btn-sm btn-success px-3 font-weight-bold shadow-sm" 
          @click="saveCurrentPolicy"
          :disabled="loading || saving"
        >
          <span v-if="saving" class="spinner-border spinner-border-sm mr-1"></span>
          <i v-else class="fa-solid fa-floppy-disk mr-1"></i>
          {{ saving ? 'সংরক্ষণ হচ্ছে...' : 'পরিবর্তন সংরক্ষণ করুন' }}
        </button>
      </div>
    </div>

    <!-- Alert / Feedback Notification -->
    <div v-if="alertMessage" class="alert alert-dismissible fade show" :class="'alert-' + alertType" role="alert">
      <div class="d-flex align-items-center">
        <i :class="alertType === 'success' ? 'fa-solid fa-circle-check text-success mr-2' : 'fa-solid fa-triangle-exclamation mr-2'"></i>
        <strong>{{ alertMessage }}</strong>
      </div>
      <button type="button" class="close" @click="alertMessage = ''">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2 text-muted font-weight-bold">পলিসি ডাটা লোড হচ্ছে...</p>
    </div>

    <!-- Edit Form -->
    <div v-else class="bg-white p-4 rounded-xl shadow-sm border">
      <!-- Status & Compliance Banner -->
      <div class="alert alert-light border d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
          <span class="badge" :class="currentPolicy.is_active ? 'badge-success' : 'badge-secondary'">
            {{ currentPolicy.is_active ? '● লাইভ ও সক্রিয় (Active)' : '○ খসড়া (Draft)' }}
          </span>
          <span class="text-muted small">
            সর্বশেষ আপডেট: <strong>{{ currentPolicy.last_reviewed_at ? new Date(currentPolicy.last_reviewed_at).toLocaleString() : 'এখনই' }}</strong>
          </span>
        </div>

        <div class="form-check form-switch d-flex align-items-center gap-2">
          <input 
            class="form-check-input cursor-pointer" 
            type="checkbox" 
            id="policyActiveSwitch" 
            v-model="currentPolicy.is_active"
          >
          <label class="form-check-label small font-weight-bold cursor-pointer mb-0" for="policyActiveSwitch">
            জনসাধারণের জন্য উন্মুক্ত রাখুন
          </label>
        </div>
      </div>

      <!-- Basic Info Grid -->
      <div class="row">
        <!-- Title (Bangla) -->
        <div class="col-md-6 form-group mb-3">
          <label class="font-weight-bold text-dark">
            দলিলের শিরোনাম (Title in Bangla) <span class="text-danger">*</span>
          </label>
          <input 
            type="text" 
            v-model="currentPolicy.title" 
            class="form-control" 
            required
            placeholder="প্রাইভেসি পলিসি..."
          >
        </div>

        <!-- Title (English) -->
        <div class="col-md-6 form-group mb-3">
          <label class="font-weight-bold text-dark">
            ইংরেজি শিরোনাম (Title in English)
          </label>
          <input 
            type="text" 
            v-model="currentPolicy.title_en" 
            class="form-control" 
            placeholder="Privacy Policy & Health Data Protection..."
          >
        </div>

        <!-- Subtitle / Statutory Legal Authority Line -->
        <div class="col-md-8 form-group mb-3">
          <label class="font-weight-bold text-dark">
            সরকারি প্রবিধান ও উপশিরোনাম (Statutory Subtitle)
          </label>
          <input 
            type="text" 
            v-model="currentPolicy.subtitle" 
            class="form-control" 
            placeholder="বাংলাদেশ স্বাস্থ্য অধিদপ্তর (DGHS), বিএমডিসি (BMDC)..."
          >
        </div>

        <!-- Effective Date -->
        <div class="col-md-4 form-group mb-3">
          <label class="font-weight-bold text-dark">
            কার্যকর তারিখ (Effective Date)
          </label>
          <input 
            type="text" 
            v-model="currentPolicy.effective_date" 
            class="form-control" 
            placeholder="০১ জানুয়ারি ২০২৬"
          >
        </div>

        <!-- Executive Summary -->
        <div class="col-12 form-group mb-4">
          <label class="font-weight-bold text-dark">
            সংক্ষিপ্ত সারসংক্ষেপ (Executive Summary / Hero Card Text)
          </label>
          <textarea 
            v-model="currentPolicy.summary" 
            class="form-control" 
            rows="2"
            placeholder="সংক্ষেপে মূল আইনি নিশ্চয়তা..."
          ></textarea>
        </div>
      </div>

      <!-- Sections Management (Structured Legal Sections) -->
      <div class="border-top pt-4 mt-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="font-weight-bold text-dark mb-0">
              <i class="fa-solid fa-list-ol text-primary mr-1"></i>
              আইনি ধারা ও সেকশনসমূহ ({{ (currentPolicy.sections || []).length }} টি ধারা)
            </h5>
            <small class="text-muted">রোগীদের সহজে পড়ার জন্য সূচিপত্র ও ধারা অনুযায়ী সাজানো।</small>
          </div>

          <button class="btn btn-sm btn-outline-primary font-weight-bold" @click="addNewSection">
            <i class="fa-solid fa-plus mr-1"></i> নতুন ধারা যুক্ত করুন
          </button>
        </div>

        <!-- Section Accordion/List -->
        <div class="space-y-3">
          <div 
            v-for="(section, sIndex) in (currentPolicy.sections || [])" 
            :key="sIndex"
            class="card border rounded-lg p-3 bg-light mb-3"
          >
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge badge-dark px-2.5 py-1">ধারা #{{ sIndex + 1 }}</span>
              <div class="d-flex align-items-center gap-1">
                <button 
                  class="btn btn-xs btn-outline-secondary py-0 px-2" 
                  :disabled="sIndex === 0" 
                  @click="moveSection(sIndex, -1)"
                  title="উপরে নিন"
                >
                  <i class="fa-solid fa-arrow-up text-xs"></i>
                </button>
                <button 
                  class="btn btn-xs btn-outline-secondary py-0 px-2" 
                  :disabled="sIndex === currentPolicy.sections.length - 1" 
                  @click="moveSection(sIndex, 1)"
                  title="নিচে নিন"
                >
                  <i class="fa-solid fa-arrow-down text-xs"></i>
                </button>
                <button 
                  class="btn btn-xs btn-outline-danger py-0 px-2 ml-2" 
                  @click="removeSection(sIndex)"
                  title="মুছে ফেলুন"
                >
                  <i class="fa-solid fa-trash text-xs"></i>
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-8 form-group mb-2">
                <label class="small font-weight-bold text-dark">ধারার শিরোনাম (Section Title):</label>
                <input type="text" v-model="section.title" class="form-control form-control-sm font-weight-bold" required>
              </div>

              <div class="col-md-4 form-group mb-2">
                <label class="small font-weight-bold text-dark">আইকন ক্লাস (FontAwesome):</label>
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i :class="['fa-solid', section.icon || 'fa-shield-halved']"></i></span>
                  </div>
                  <input type="text" v-model="section.icon" class="form-control" placeholder="fa-shield-halved">
                </div>
              </div>

              <div class="col-12 form-group mb-2">
                <label class="small font-weight-bold text-dark">ধারার সংক্ষিপ্ত বিষয় (Sub-headline / Summary):</label>
                <input type="text" v-model="section.summary" class="form-control form-control-sm" placeholder="এক লাইনে মূল বিষয়...">
              </div>

              <div class="col-12 form-group mb-0">
                <label class="small font-weight-bold text-dark">পূর্ণাঙ্গ আইনি বক্তব্য ও বিবরণ (Full Content):</label>
                <textarea v-model="section.content" class="form-control form-control-sm" rows="4" required></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-3">
          <button class="btn btn-sm btn-outline-primary px-4 font-weight-bold" @click="addNewSection">
            <i class="fa-solid fa-plus mr-1"></i> আরো একটি ধারা যোগ করুন
          </button>
        </div>
      </div>

      <!-- Metadata & Compliance Contacts -->
      <div class="border-top pt-4 mt-4" v-if="currentPolicy.meta">
        <h6 class="font-weight-bold text-dark mb-3">
          <i class="fa-solid fa-gears text-secondary mr-1"></i>
          আইনি কমপ্লায়েন্স ও যোগাযোগের তথ্য (Metadata)
        </h6>
        <div class="row">
          <div class="col-md-6 form-group mb-2">
            <label class="small font-weight-bold text-dark">রেগুলেটরি রেফারেন্স (Regulatory Reference):</label>
            <input type="text" v-model="currentPolicy.meta.compliance_reference" class="form-control form-control-sm">
          </div>
          <div class="col-md-6 form-group mb-2">
            <label class="small font-weight-bold text-dark">হেল্পলাইন / কন্টাক্ট নম্বর:</label>
            <input type="text" v-model="currentPolicy.meta.helpline" class="form-control form-control-sm">
          </div>
          <div class="col-md-6 form-group mb-2" v-if="currentPolicy.meta.contact_dpo">
            <label class="small font-weight-bold text-dark">DPO ইমেইল অ্যাড্রেস:</label>
            <input type="email" v-model="currentPolicy.meta.contact_dpo" class="form-control form-control-sm">
          </div>
          <div class="col-md-6 form-group mb-2" v-if="currentPolicy.meta.contact_legal">
            <label class="small font-weight-bold text-dark">আইনি সাপোর্ট ইমেইল:</label>
            <input type="email" v-model="currentPolicy.meta.contact_legal" class="form-control form-control-sm">
          </div>
        </div>
      </div>

      <!-- Bottom Save Action -->
      <div class="border-top pt-4 mt-4 d-flex justify-content-between align-items-center">
        <div class="text-muted small">
          <i class="fa-solid fa-circle-info mr-1"></i>
          সংরক্ষণ করার সাথে সাথে পাবলিক ওয়েবসাইটে নতুন নীতিমালা কার্যকর হবে।
        </div>
        <button 
          class="btn btn-success px-4 py-2 font-weight-bold shadow" 
          @click="saveCurrentPolicy"
          :disabled="saving"
        >
          <span v-if="saving" class="spinner-border spinner-border-sm mr-1"></span>
          <i v-else class="fa-solid fa-floppy-disk mr-1"></i>
          {{ saving ? 'সংরক্ষণ হচ্ছে...' : 'নীতিমালা সংরক্ষণ করুন (Save Policy)' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PolicyAdminManagement',
  data() {
    return {
      activeKey: 'privacy_policy',
      policies: {},
      currentPolicy: {},
      loading: false,
      saving: false,
      alertMessage: '',
      alertType: 'success',
    };
  },
  mounted() {
    this.fetchPolicies();
  },
  methods: {
    getAuthHeaders() {
      const token = localStorage.getItem('admin_token') || localStorage.getItem('saas_token');
      return {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json',
        },
      };
    },
    async fetchPolicies() {
      this.loading = true;
      try {
        const res = await axios.get('/api/admin/policies', this.getAuthHeaders());
        if (res.data && res.data.success) {
          const list = res.data.data;
          const mapped = {};
          list.forEach(p => {
            mapped[p.key] = p;
          });
          this.policies = mapped;
          this.selectPolicy(this.activeKey);
        }
      } catch (err) {
        console.error('Failed to load admin policies:', err);
        this.showAlert('পলিসি ডাটা লোড করতে সমস্যা হয়েছে।', 'danger');
      } finally {
        this.loading = false;
      }
    },
    selectPolicy(key) {
      this.activeKey = key;
      if (this.policies[key]) {
        // Deep copy to allow editing without immediately mutating state
        this.currentPolicy = JSON.parse(JSON.stringify(this.policies[key]));
        if (!this.currentPolicy.sections) {
          this.currentPolicy.sections = [];
        }
        if (!this.currentPolicy.meta) {
          this.currentPolicy.meta = {};
        }
      }
    },
    addNewSection() {
      if (!this.currentPolicy.sections) {
        this.currentPolicy.sections = [];
      }
      this.currentPolicy.sections.push({
        id: 'section-' + Date.now(),
        title: 'নতুন আইনি ধারা',
        icon: 'fa-file-lines',
        summary: 'সংক্ষিপ্ত বিবরণ',
        content: 'ধারার বিস্তারিত বিবরণ এখানে লিখুন...',
      });
    },
    removeSection(index) {
      if (confirm('আপনি কি নিশ্চিত এই ধারাটি মুছে ফেলতে চান?')) {
        this.currentPolicy.sections.splice(index, 1);
      }
    },
    moveSection(index, direction) {
      const target = index + direction;
      if (target >= 0 && target < this.currentPolicy.sections.length) {
        const temp = this.currentPolicy.sections[index];
        this.currentPolicy.sections[index] = this.currentPolicy.sections[target];
        this.currentPolicy.sections[target] = temp;
      }
    },
    async saveCurrentPolicy() {
      this.saving = true;
      this.alertMessage = '';
      try {
        const payload = {
          title: this.currentPolicy.title,
          title_en: this.currentPolicy.title_en,
          subtitle: this.currentPolicy.subtitle,
          summary: this.currentPolicy.summary,
          content: this.currentPolicy.content || this.currentPolicy.title,
          sections: this.currentPolicy.sections,
          effective_date: this.currentPolicy.effective_date,
          is_active: this.currentPolicy.is_active,
          meta: this.currentPolicy.meta,
        };

        const res = await axios.put(`/api/admin/policies/${this.activeKey}`, payload, this.getAuthHeaders());
        if (res.data && res.data.success) {
          this.policies[this.activeKey] = res.data.data;
          this.currentPolicy = JSON.parse(JSON.stringify(res.data.data));
          this.showAlert(res.data.message || 'পলিসি সফলভাবে সংরক্ষণ করা হয়েছে!', 'success');
        }
      } catch (err) {
        console.error('Failed to save policy:', err);
        const errorMsg = err.response?.data?.message || 'পলিসি সংরক্ষণ ব্যর্থ হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।';
        this.showAlert(errorMsg, 'danger');
      } finally {
        this.saving = false;
      }
    },
    async confirmResetDefault() {
      if (confirm('আপনি কি নিশ্চিত এই পলিসিটি বাংলাদেশ স্বাস্থ্য অধিদপ্তর ও বিএমডিসি অনুমোদিত মূল ডিফল্ট ড্রাফটে ফিরিয়ে নিতে চান? এতে আপনার কাস্টম পরিবর্তনগুলো প্রতিস্থাপিত হবে।')) {
        this.loading = true;
        try {
          const res = await axios.post(`/api/admin/policies/${this.activeKey}/reset-default`, {}, this.getAuthHeaders());
          if (res.data && res.data.success) {
            this.policies[this.activeKey] = res.data.data;
            this.currentPolicy = JSON.parse(JSON.stringify(res.data.data));
            this.showAlert(res.data.message || 'ডিফল্ট সরকারি কমপ্লায়েন্স ড্রাফটে রিস্টোর সম্পন্ন হয়েছে!', 'success');
          }
        } catch (err) {
          console.error('Failed to reset policy:', err);
          this.showAlert('রিস্টোর করতে সমস্যা হয়েছে।', 'danger');
        } finally {
          this.loading = false;
        }
      }
    },
    showAlert(msg, type = 'success') {
      this.alertMessage = msg;
      this.alertType = type;
      setTimeout(() => {
        if (this.alertMessage === msg) {
          this.alertMessage = '';
        }
      }, 5000);
    },
  },
};
</script>

<style scoped>
.btn-xs {
  font-size: 11px;
}
.space-y-3 > * + * {
  margin-top: 0.75rem;
}
</style>
