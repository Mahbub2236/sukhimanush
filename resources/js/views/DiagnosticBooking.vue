<template>
  <div class="diagnostic-booking-page bg-slate-50 min-vh-100 py-4 py-md-5">
    <div class="container" style="max-width: 1140px;">
      <!-- Hero Header Section -->
      <div class="text-center mb-5">
        <span class="badge badge-danger text-uppercase px-3 py-1 font-weight-bold rounded-pill mb-2" style="font-size: 11px; letter-spacing: 0.5px;">
          🧪 Diagnostic Network & Home Sample Collection
        </span>
        <h2 class="font-weight-bold text-dark display-5 mb-2" style="font-size: 28px;">
          স্মার্ট ডায়াগনস্টিক টেস্ট ও হোম স্যাম্পল কালেকশন
        </h2>
        <p class="text-muted mx-auto mb-3" style="max-width: 680px; font-size: 14px;">
          পপুলার, ইবনে সিনা, ল্যাবএইড সহ অনুমোদিত শীর্ষস্থানীয় ডায়াগনস্টিক সেন্টারে <strong class="text-danger font-weight-bold">১০% - ১৫% বিশেষ ছাড়ে</strong> টেস্ট বুক করুন অথবা বাড়ি বসেই রক্তের স্যাম্পল দিন।
        </p>
        
        <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 10px;">
          <button 
            type="button" 
            class="btn btn-sm rounded-pill px-4 py-2 font-weight-bold shadow-sm"
            :class="activeView === 'book' ? 'btn-danger text-white' : 'btn-white bg-white text-dark border'"
            @click="activeView = 'book'"
          >
            <i class="icofont-test-tube-alt mr-1"></i> টেস্ট বুকিং ও হোম স্যাম্পল
          </button>
          <button 
            type="button" 
            class="btn btn-sm rounded-pill px-4 py-2 font-weight-bold shadow-sm"
            :class="activeView === 'register' ? 'btn-primary text-white' : 'btn-white bg-white text-dark border'"
            @click="activeView = 'register'"
          >
            <i class="icofont-hospital mr-1"></i> ডায়াগনস্টিক পার্টনার রেজিস্ট্রেশন
          </button>
        </div>
      </div>

      <!-- VIEW 1: TEST BOOKING FLOW -->
      <div v-if="activeView === 'book'">
        <div class="row">
          <!-- Left Column: Center & Test Selector -->
          <div class="col-lg-7 mb-4">
            <!-- 1. Select Diagnostic Center -->
            <div class="card border-0 shadow-sm rounded-20 p-3 p-md-4 bg-white mb-4">
              <h5 class="font-weight-bold text-dark mb-1 d-flex align-items-center">
                <span class="badge badge-danger rounded-circle mr-2 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 12px;">১</span>
                ডায়াগনস্টিক সেন্টার নির্বাচন করুন
              </h5>
              <p class="text-muted small mb-3">যে ল্যাব থেকে টেস্ট করাতে বা স্যাম্পল সংগ্রহ করাতে চান:</p>

              <div v-if="loadingCenters" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
                <span class="ml-2 small text-muted">ডায়াগনস্টিক সেন্টারের তালিকা লোড হচ্ছে...</span>
              </div>

              <div v-else class="row g-2">
                <div 
                  v-for="center in centers" 
                  :key="center.id" 
                  class="col-12 col-sm-6 mb-2"
                >
                  <div 
                    class="p-3 rounded-16 border cursor-pointer h-100 transition-all position-relative"
                    :style="selectedCenter?.id === center.id ? 'border: 2px solid #e12454 !important; background-color: #fff5f7 !important;' : 'background-color: #f8fafc;'"
                    @click="selectCenter(center)"
                  >
                    <div class="d-flex justify-content-between align-items-start mb-1">
                      <h6 class="font-weight-bold text-dark mb-0" style="font-size: 13px;">{{ center.name }}</h6>
                      <span class="badge badge-success text-white font-weight-bold" style="font-size: 10px;">
                        {{ center.discount_rate }}% ছাড়
                      </span>
                    </div>
                    <p class="text-muted small mb-1" style="font-size: 11px;">
                      <i class="icofont-location-pin text-danger"></i> {{ center.district }} ({{ center.address }})
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top small" style="font-size: 11px;">
                      <span class="text-primary font-weight-bold">
                        <i class="icofont-home mr-1"></i> হোম স্যাম্পল: ৳{{ center.home_collection_fee }}
                      </span>
                      <span v-if="selectedCenter?.id === center.id" class="badge badge-danger text-white">
                        ✓ নির্বাচিত
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Select Medical Tests -->
            <div class="card border-0 shadow-sm rounded-20 p-3 p-md-4 bg-white mb-4">
              <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap">
                <h5 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                  <span class="badge badge-danger rounded-circle mr-2 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 12px;">২</span>
                  প্রয়োজনীয় টেস্ট নির্বাচন করুন
                </h5>
                <span class="badge badge-light border text-muted small">
                  {{ selectedTests.length }}টি টেস্ট নির্বাচিত
                </span>
              </div>
              <p class="text-muted small mb-3">ডাক্তারের প্রেসক্রিপশন অনুযায়ী অথবা নিজে প্রয়োজনীয় টেস্টে টিক দিন:</p>

              <!-- Quick Search Filter for Tests -->
              <div class="input-group input-group-sm mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0"><i class="icofont-search-2 text-muted"></i></span>
                </div>
                <input 
                  type="text" 
                  v-model="testSearchQuery" 
                  class="form-control border-left-0" 
                  placeholder="টেস্টের নাম দিয়ে খুঁজুন (যেমন: CBC, Sugar, USG, Lipid...)"
                >
              </div>

              <div class="test-selection-list" style="max-height: 280px; overflow-y: auto;">
                <div 
                  v-for="(test, tIdx) in filteredAvailableTests" 
                  :key="tIdx"
                  class="d-flex justify-content-between align-items-center p-2 mb-1 rounded hover-bg-light border"
                  style="cursor: pointer;"
                  @click="toggleTest(test)"
                >
                  <div class="d-flex align-items-center" style="gap: 10px;">
                    <input 
                      type="checkbox" 
                      :checked="isTestSelected(test)" 
                      class="cursor-pointer"
                      @click.stop="toggleTest(test)"
                    >
                    <div>
                      <strong class="text-dark small d-block">{{ test.name }}</strong>
                      <span class="badge badge-light border text-muted" style="font-size: 10px;" v-if="test.category">{{ test.category }}</span>
                    </div>
                  </div>
                  <div class="text-right">
                    <span class="font-weight-bold text-dark small">৳{{ test.price }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Booking Summary & Patient Details -->
          <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-20 p-3 p-md-4 bg-white sticky-top" style="top: 85px;">
              <h5 class="font-weight-bold text-dark mb-3 d-flex align-items-center border-bottom pb-2">
                <i class="icofont-bill-alt mr-2 text-danger"></i> বুকিং সামারি ও খরচ
              </h5>

              <!-- Center Info -->
              <div class="alert alert-light border p-2 mb-3 rounded-12 small">
                <span class="text-muted d-block" style="font-size: 11px;">নির্বাচিত ডায়াগনস্টিক সেন্টার:</span>
                <strong class="text-primary font-14">{{ selectedCenter?.name || 'দয়া করে সেন্টার নির্বাচন করুন' }}</strong>
              </div>

              <!-- Collection Mode Toggle -->
              <label class="font-weight-bold text-dark small mb-1">স্যাম্পল সংগ্রহের ধরণ:</label>
              <div class="btn-group btn-group-sm w-100 mb-3" role="group">
                <button 
                  type="button" 
                  class="btn font-weight-bold py-2"
                  :class="bookingForm.sample_collection_type === 'home_collection' ? 'btn-danger text-white' : 'btn-light border'"
                  @click="bookingForm.sample_collection_type = 'home_collection'"
                >
                  🏠 হোম স্যাম্পল কালেকশন
                </button>
                <button 
                  type="button" 
                  class="btn font-weight-bold py-2"
                  :class="bookingForm.sample_collection_type === 'lab_visit' ? 'btn-danger text-white' : 'btn-light border'"
                  @click="bookingForm.sample_collection_type = 'lab_visit'"
                >
                  🏥 সরাসরি ল্যাবে ভিজিট
                </button>
              </div>

              <!-- Price Breakdown -->
              <div class="bg-light p-3 rounded-16 mb-3 small">
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-muted">টেস্টের মূল খরচ ({{ selectedTests.length }}টি):</span>
                  <span class="font-weight-bold text-dark">৳{{ subtotalTests }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-success font-weight-bold" v-if="discountAmount > 0">
                  <span>পার্টনার ডিসকাউন্ট ({{ selectedCenter?.discount_rate || 0 }}%):</span>
                  <span>- ৳{{ discountAmount }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-primary" v-if="bookingForm.sample_collection_type === 'home_collection'">
                  <span>হোম কালেকশন ফি:</span>
                  <span>+ ৳{{ homeFee }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top mt-2">
                  <strong class="text-dark font-15">সর্বমোট প্রদেয়:</strong>
                  <strong class="text-danger font-16">৳{{ finalTotalAmount }}</strong>
                </div>
              </div>

              <!-- Patient Contact Form -->
              <form @submit.prevent="submitBooking">
                <div class="form-group mb-2">
                  <label class="font-weight-bold small text-dark mb-1">রোগীর নাম *</label>
                  <input type="text" v-model="bookingForm.patient_name" class="form-control form-control-sm" required placeholder="রোগীর পূর্ণ নাম">
                </div>

                <div class="form-group mb-2">
                  <label class="font-weight-bold small text-dark mb-1">মোবাইল নম্বর *</label>
                  <input type="tel" v-model="bookingForm.contact_phone" class="form-control form-control-sm" required placeholder="01XXXXXXXXX">
                </div>

                <div class="form-group mb-2" v-if="bookingForm.sample_collection_type === 'home_collection'">
                  <label class="font-weight-bold small text-dark mb-1">স্যাম্পল কালেকশন ঠিকানা *</label>
                  <textarea v-model="bookingForm.collection_address" rows="2" class="form-control form-control-sm" required placeholder="বাসা নং, রোড নং, এলাকা ও জেলা"></textarea>
                </div>

                <div class="row g-2 mb-3">
                  <div class="col-6">
                    <label class="font-weight-bold small text-dark mb-1">তারিখ *</label>
                    <input type="date" v-model="bookingForm.preferred_date" :min="todayDate" class="form-control form-control-sm" required>
                  </div>
                  <div class="col-6">
                    <label class="font-weight-bold small text-dark mb-1">সময়সূচি *</label>
                    <select v-model="bookingForm.preferred_time" class="custom-select custom-select-sm">
                      <option value="Morning (7:30 AM - 10:30 AM)">সকাল (৭:৩০ - ১০:৩০)</option>
                      <option value="Midday (11:00 AM - 2:00 PM)">দুপুর (১১:০০ - ২:০০)</option>
                      <option value="Afternoon (3:00 PM - 6:00 PM)">বিকাল (৩:০০ - ৬:০০)</option>
                    </select>
                  </div>
                </div>

                <button 
                  type="submit" 
                  class="btn btn-danger btn-block font-weight-bold rounded-pill py-2 shadow-sm text-white"
                  :disabled="submittingBooking || selectedTests.length === 0"
                  style="background: linear-gradient(135deg, #e12454, #b91c1c); border: none;"
                >
                  <span v-if="submittingBooking" class="spinner-border spinner-border-sm mr-1"></span>
                  {{ submittingBooking ? 'বুকিং জমা হচ্ছে...' : '🧪 কনফার্ম বুকিং ও অর্ডার করুন' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- VIEW 2: DIAGNOSTIC CENTER PARTNER REGISTRATION FORM -->
      <div v-else-if="activeView === 'register'" class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-20 p-4 bg-white">
            <div class="border-bottom pb-3 mb-4">
              <span class="badge badge-primary px-3 py-1 font-weight-bold rounded-pill mb-1">Partner Onboarding</span>
              <h4 class="font-weight-bold text-dark mb-1">ডায়াগনস্টিক সেন্টার পার্টনারশিপ রেজিস্ট্রেশন</h4>
              <p class="text-muted small mb-0">আপনার ডায়াগনস্টিক সেন্টারকে আমাদের ডিজিটাল হেলথ নেটওয়ার্কে যুক্ত করুন এবং শত শত রোগীর টেস্ট রেফারেল গ্রহণ করুন।</p>
            </div>

            <form @submit.prevent="submitCenterRegistration">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">ডায়াগনস্টিক সেন্টারের নাম *</label>
                  <input type="text" v-model="regForm.name" class="form-control" required placeholder="যেমন: পপুলার ডায়াগনস্টিক সেন্টার">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">DGHS / স্বাস্থ্য অধিদপ্তর লাইসেন্স নং</label>
                  <input type="text" v-model="regForm.license_number" class="form-control" placeholder="DGHS-DIAG-XXXXX">
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">বিভাগ *</label>
                  <select v-model="regForm.division" class="custom-select" required>
                    <option value="Dhaka">ঢাকা বিভাগ</option>
                    <option value="Chittagong">চট্টগ্রাম বিভাগ</option>
                    <option value="Rajshahi">রাজশাহী বিভাগ</option>
                    <option value="Khulna">খুলনা বিভাগ</option>
                    <option value="Barisal">বরিশাল বিভাগ</option>
                    <option value="Sylhet">সিলেট বিভাগ</option>
                    <option value="Rangpur">রংপুর বিভাগ</option>
                    <option value="Mymensingh">ময়মনসিংহ বিভাগ</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">জেলা *</label>
                  <input type="text" v-model="regForm.district" class="form-control" required placeholder="যেমন: ঢাকা / রাজশাহী">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">উপজেলা / থানা</label>
                  <input type="text" v-model="regForm.upazila" class="form-control" placeholder="যেমন: ধানমন্ডি">
                </div>
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark mb-1">সেন্টারের পূর্ণাঙ্গ ঠিকানা *</label>
                <textarea v-model="regForm.address" rows="2" class="form-control" required placeholder="বাড়ি নং, রোড নং, এলাকা"></textarea>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">অফিসিয়াল যোগাযোগ মোবাইল *</label>
                  <input type="tel" v-model="regForm.contact_phone" class="form-control" required placeholder="01XXXXXXXXX">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">ইমেইল ঠিকানা</label>
                  <input type="email" v-model="regForm.contact_email" class="form-control" placeholder="lab@example.com">
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">প্ল্যাটফর্ম রেফারেল কমিশন (%) *</label>
                  <input type="number" step="0.5" min="5" max="50" v-model="regForm.commission_rate" class="form-control" required placeholder="15">
                  <small class="text-muted">স্ট্যান্ডার্ড: ১৫% - ২০%</small>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">রোগীদের জন্য বিশেষ ছাড় (%) *</label>
                  <input type="number" step="0.5" min="0" max="40" v-model="regForm.discount_rate" class="form-control" required placeholder="10">
                  <small class="text-muted">রোগীর ডিসকাউন্ট (যেমন: ১০%)</small>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="font-weight-bold small text-dark mb-1">হোম কালেকশন ফি (টাকা)</label>
                  <input type="number" min="0" v-model="regForm.home_collection_fee" class="form-control" placeholder="100">
                  <small class="text-muted">হোম স্যাম্পল ডেলিভারি ফি</small>
                </div>
              </div>

              <div class="d-flex justify-content-end mt-4">
                <button 
                  type="submit" 
                  class="btn btn-primary font-weight-bold px-4 py-2 rounded-pill shadow-sm"
                  :disabled="submittingReg"
                  style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none;"
                >
                  <span v-if="submittingReg" class="spinner-border spinner-border-sm mr-1"></span>
                  {{ submittingReg ? 'নিবন্ধন হচ্ছে...' : 'অনলাইন পার্টনার হিসেবে যোগ দিন ➜' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'DiagnosticBooking',
  data() {
    return {
      activeView: 'book', // 'book' or 'register'
      loadingCenters: true,
      submittingBooking: false,
      submittingReg: false,
      centers: [],
      selectedCenter: null,
      selectedTests: [],
      testSearchQuery: '',
      todayDate: new Date().toISOString().split('T')[0],
      bookingForm: {
        patient_name: '',
        contact_phone: '',
        collection_address: '',
        preferred_date: new Date().toISOString().split('T')[0],
        preferred_time: 'Morning (7:30 AM - 10:30 AM)',
        sample_collection_type: 'home_collection',
      },
      regForm: {
        name: '',
        license_number: '',
        division: 'Dhaka',
        district: 'Dhaka',
        upazila: '',
        address: '',
        contact_phone: '',
        contact_email: '',
        commission_rate: 15.00,
        discount_rate: 10.00,
        home_collection_available: true,
        home_collection_fee: 100.00,
      },
    };
  },
  computed: {
    availableTests() {
      if (this.selectedCenter && Array.isArray(this.selectedCenter.available_tests) && this.selectedCenter.available_tests.length > 0) {
        return this.selectedCenter.available_tests;
      }
      return [
        { name: 'Complete Blood Count (CBC)', price: 400.00, category: 'Hematology' },
        { name: 'Fasting Blood Sugar (FBS)', price: 150.00, category: 'Biochemistry' },
        { name: 'HbA1c (Glycated Hemoglobin)', price: 950.00, category: 'Biochemistry' },
        { name: 'Serum Lipid Profile', price: 1050.00, category: 'Biochemistry' },
        { name: 'Serum Creatinine', price: 400.00, category: 'Biochemistry' },
        { name: 'Serum Electrolytes', price: 900.00, category: 'Biochemistry' },
        { name: 'Thyroid Stimulating Hormone (TSH)', price: 650.00, category: 'Immunology' },
        { name: 'Urine Routine Examination (R/E)', price: 250.00, category: 'Clinical Pathology' },
        { name: 'Ultrasonography of Whole Abdomen (USG)', price: 1400.00, category: 'Radiology' },
        { name: 'Chest X-Ray P/A View', price: 650.00, category: 'Radiology' },
        { name: '12-Lead Digital ECG', price: 450.00, category: 'Cardiology' },
      ];
    },
    filteredAvailableTests() {
      if (!this.testSearchQuery) return this.availableTests;
      const q = this.testSearchQuery.toLowerCase();
      return this.availableTests.filter(t => t.name.toLowerCase().includes(q) || (t.category && t.category.toLowerCase().includes(q)));
    },
    subtotalTests() {
      return this.selectedTests.reduce((sum, t) => sum + (parseFloat(t.price) || 0), 0);
    },
    discountAmount() {
      const rate = this.selectedCenter?.discount_rate || 0;
      return Math.round(this.subtotalTests * (rate / 100));
    },
    homeFee() {
      if (this.bookingForm.sample_collection_type === 'home_collection') {
        return parseFloat(this.selectedCenter?.home_collection_fee || 100);
      }
      return 0;
    },
    finalTotalAmount() {
      return Math.max(0, this.subtotalTests - this.discountAmount + this.homeFee);
    },
  },
  created() {
    this.fetchCenters();
    // Prefill user details if logged in
    const saasUser = localStorage.getItem('saas_user');
    if (saasUser) {
      try {
        const u = JSON.parse(saasUser);
        if (u.name) this.bookingForm.patient_name = u.name;
        if (u.phone) this.bookingForm.contact_phone = u.phone;
        if (u.address) this.bookingForm.collection_address = u.address;
      } catch (e) {}
    }
  },
  methods: {
    async fetchCenters() {
      this.loadingCenters = true;
      try {
        const res = await axios.get('/diagnostic-centers');
        if (res.data.success && res.data.centers) {
          this.centers = res.data.centers;
          if (this.centers.length > 0) {
            this.selectCenter(this.centers[0]);
          }
        }
      } catch (err) {
        console.error('Failed to load diagnostic centers:', err);
      } finally {
        this.loadingCenters = false;
      }
    },
    selectCenter(center) {
      this.selectedCenter = center;
      // Default select CBC if none selected
      if (this.selectedTests.length === 0 && this.availableTests.length > 0) {
        this.selectedTests = [this.availableTests[0]];
      }
    },
    isTestSelected(test) {
      return this.selectedTests.some(t => t.name === test.name);
    },
    toggleTest(test) {
      const idx = this.selectedTests.findIndex(t => t.name === test.name);
      if (idx >= 0) {
        this.selectedTests.splice(idx, 1);
      } else {
        this.selectedTests.push(test);
      }
    },
    async submitBooking() {
      if (!this.selectedCenter) {
        Swal.fire('সতর্কতা', 'অনুগ্রহ করে একটি ডায়াগনস্টিক সেন্টার নির্বাচন করুন।', 'warning');
        return;
      }
      if (this.selectedTests.length === 0) {
        Swal.fire('সতর্কতা', 'কমপক্ষে একটি টেস্ট নির্বাচন করুন।', 'warning');
        return;
      }

      this.submittingBooking = true;
      try {
        const payload = {
          ...this.bookingForm,
          diagnostic_center_id: this.selectedCenter.id,
          diagnostic_center: this.selectedCenter.name,
          test_names: this.selectedTests.map(t => `${t.name} (৳${t.price})`),
          total_amount: this.finalTotalAmount,
        };

        const res = await axios.post('/diagnostic-orders/book', payload);
        if (res.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'বুকিং সফল হয়েছে!',
            html: `আপনার ডায়াগনস্টিক টেস্টের অর্ডার সফলভাবে গ্রহণ করা হয়েছে।<br><strong>অর্ডার নম্বর: ${res.data.order?.order_number}</strong><br>আমাদের ল্যাব প্রতিনিধি শীঘ্রই আপনার মোবাইল নম্বরে কল করবেন।`,
          });
          // Reset
          this.selectedTests = [this.availableTests[0]];
        }
      } catch (err) {
        Swal.fire('ত্রুটি', err.response?.data?.message || 'বুকিং সম্পন্ন করতে সমস্যা হয়েছে।', 'error');
      } finally {
        this.submittingBooking = false;
      }
    },
    async submitCenterRegistration() {
      this.submittingReg = true;
      try {
        const res = await axios.post('/diagnostic-centers/register', this.regForm);
        if (res.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'নিবন্ধন সম্পন্ন হয়েছে!',
            text: res.data.message || 'আপনার ডায়াগনস্টিক সেন্টার সফলভাবে পার্টনার তালিকায় যুক্ত হয়েছে।',
          });
          this.activeView = 'book';
          this.fetchCenters();
        }
      } catch (err) {
        Swal.fire('ত্রুটি', err.response?.data?.message || 'নিবন্ধন সম্পন্ন করতে সমস্যা হয়েছে।', 'error');
      } finally {
        this.submittingReg = false;
      }
    },
  },
};
</script>

<style scoped>
.rounded-20 {
  border-radius: 20px;
}
.rounded-16 {
  border-radius: 16px;
}
.rounded-12 {
  border-radius: 12px;
}
.hover-bg-light:hover {
  background-color: #f1f5f9;
}
.transition-all {
  transition: all 0.2s ease;
}
</style>
