<template>
  <div class="blood-sos-page pb-5">
    <!-- Header Banner -->
    <section class="page-title py-4 py-md-5 text-white position-relative" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 50%, #dc2626 100%);">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-12 text-center">
            <span class="badge badge-light text-danger px-3 py-1 font-weight-bold mb-2">
              🩸 জীবন বাঁচাতে রক্তদান
            </span>
            <h1 class="text-white font-weight-bold mb-2">ব্লাড ও প্লাটিলেট SOS নেটওয়ার্ক</h1>
            <p class="text-white-50 small max-w-600 mx-auto mb-3">
              জরুরি রক্তের প্রয়োজনে তাত্ক্ষণিক পোস্ট করুন অথবা স্বেচ্ছাসেবী রক্তদাতা হিসেবে যুক্ত হয়ে জীবন বাঁচান।
            </p>
            <div class="d-flex justify-content-center" style="gap: 10px;">
              <button class="btn btn-warning text-dark font-weight-bold rounded-pill px-4 shadow" @click="showRequestModal = true">
                <i class="icofont-megaphone mr-1"></i> জরুরি রক্তের আবেদন করুন
              </button>
              <button class="btn btn-outline-light font-weight-bold rounded-pill px-4" @click="showDonorModal = true">
                <i class="icofont-user mr-1"></i> রক্তদাতা হিসেবে নাম লেখান
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Content Section -->
    <div class="container mt-4">
      <!-- Quick Stats & Filter Bar -->
      <div class="bg-white p-3 rounded-lg shadow-sm mb-4 border d-flex flex-wrap justify-content-between align-items-center" style="gap: 10px;">
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
          <span class="font-weight-bold small text-dark"><i class="icofont-filter text-danger"></i> গ্রুপ ফিল্টার:</span>
          <button 
            v-for="grp in bloodGroups" 
            :key="grp"
            class="btn btn-sm rounded-pill font-weight-bold px-3 py-1"
            :class="selectedGroup === grp ? 'btn-danger text-white' : 'btn-light border text-dark'"
            @click="selectedGroup = (selectedGroup === grp ? '' : grp); loadData()"
          >
            {{ grp }}
          </button>
        </div>

        <div class="d-flex align-items-center" style="gap: 6px;">
          <input 
            type="text" 
            v-model="filterDistrict" 
            data-no-voice
            class="form-control form-control-sm" 
            placeholder="জেলা লিখুন (যেমন: ঢাকা, রংপুর)" 
            @input="loadData"
            style="max-width: 200px;"
          >
          <VoiceInputBtn @result="val => { filterDistrict = val; loadData(); }" />
          <button class="btn btn-sm btn-outline-secondary rounded-pill" @click="loadData">
            <i class="icofont-refresh"></i>
          </button>
        </div>
      </div>

      <!-- Tabs (Active SOS Requests vs Voluntary Donors) -->
      <ul class="nav nav-pills mb-4" style="gap: 10px;">
        <li class="nav-item">
          <button 
            class="nav-link font-weight-bold rounded-pill px-4 py-2" 
            :class="{ 'active bg-danger text-white': activeTab === 'requests', 'btn-light border': activeTab !== 'requests' }"
            @click="activeTab = 'requests'"
          >
            <i class="icofont-megaphone mr-1"></i> জরুরি রক্তের পোস্ট ({{ requests.length }})
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link font-weight-bold rounded-pill px-4 py-2" 
            :class="{ 'active bg-danger text-white': activeTab === 'donors', 'btn-light border': activeTab !== 'donors' }"
            @click="activeTab = 'donors'"
          >
            <i class="icofont-users-alt-5 mr-1"></i> স্বেচ্ছাসেবী রক্তদাতা তালিকা ({{ donors.length }})
          </button>
        </li>
      </ul>

      <!-- Tab 1: Live SOS Requests -->
      <div v-if="activeTab === 'requests'">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
          <p class="text-muted small mt-2">জরুরি আবেদন লোড হচ্ছে...</p>
        </div>

        <div v-else-if="requests.length === 0" class="text-center py-5 bg-white rounded border">
          <i class="icofont-blood-drop display-4 text-muted"></i>
          <h5 class="mt-3 font-weight-bold">কোনো সক্রিয় জরুরি রক্তের আবেদন নেই</h5>
          <p class="text-muted small">আপনার রক্তের প্রয়োজন থাকলে উপরের বোতামে ক্লিক করে পোস্ট করুন।</p>
        </div>

        <div v-else class="row">
          <div v-for="req in requests" :key="req.id" class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-lg p-3 p-md-4 h-100 bg-white border-left-danger">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center">
                  <div class="blood-badge-lg mr-3">
                    {{ req.blood_group }}
                  </div>
                  <div>
                    <h5 class="font-weight-bold text-dark mb-0">{{ req.patient_name }}</h5>
                    <span class="badge badge-secondary" style="font-size: 10px;">{{ getComponentText(req.component) }}</span>
                    <span class="badge badge-warning text-dark ml-1 font-weight-bold" style="font-size: 10px;">
                      {{ req.units_needed }} ব্যাগ প্রয়োজন
                    </span>
                  </div>
                </div>
                <span class="badge px-2 py-1 font-weight-bold" :class="req.urgency === 'critical_immediate' ? 'badge-danger' : 'badge-warning text-dark'">
                  {{ req.urgency === 'critical_immediate' ? '🚨 অতি জরুরি' : 'জরুরি (২৪ ঘণ্টা)' }}
                </span>
              </div>

              <div class="bg-light p-3 rounded mb-3 small">
                <div class="mb-1 text-dark">
                  <i class="icofont-hospital text-danger mr-1"></i> <strong>হাসপাতাল:</strong> {{ req.hospital_name }}
                </div>
                <div class="text-muted">
                  <i class="icofont-location-pin text-primary mr-1"></i> {{ req.district }} <span v-if="req.upazila">, {{ req.upazila }}</span>
                </div>
                <div v-if="req.reason" class="mt-2 text-dark font-italic">
                  "{{ req.reason }}"
                </div>
              </div>

              <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 border-top mt-auto" style="gap: 8px;">
                <small class="text-muted">কোড: <strong>{{ req.request_code }}</strong></small>
                <div class="d-flex" style="gap: 6px;">
                  <button class="btn btn-sm btn-outline-success font-weight-bold rounded-pill px-3" @click="respondToSos(req)">
                    <i class="icofont-hand mr-1"></i> আমি রক্ত দিতে ইচ্ছুক
                  </button>
                  <a :href="'tel:' + req.contact_phone" class="btn btn-sm btn-danger font-weight-bold rounded-pill px-3">
                    <i class="icofont-phone mr-1"></i> কল দিন
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 2: Voluntary Donors -->
      <div v-if="activeTab === 'donors'">
        <div class="row">
          <div v-for="donor in donors" :key="donor.id" class="col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg p-3 bg-white d-flex flex-row align-items-center">
              <div class="blood-badge-sm mr-3">
                {{ donor.blood_group }}
              </div>
              <div class="flex-grow-1 overflow-hidden">
                <h6 class="font-weight-bold text-dark mb-0 text-truncate">{{ donor.name }}</h6>
                <small class="text-muted d-block text-truncate">
                  <i class="icofont-location-pin text-danger"></i> {{ donor.district }}
                </small>
                <small class="text-success font-weight-bold d-block">
                  <i class="icofont-badge text-warning"></i> {{ donor.total_donations }}+ বার রক্ত দিয়েছেন
                </small>
              </div>
              <a :href="'tel:' + donor.phone" class="btn btn-sm btn-outline-danger rounded-circle ml-2" title="কল দিন" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="icofont-phone"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Request Modal -->
    <div v-if="showRequestModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3">
      <div class="modal-card bg-white rounded-lg shadow-lg p-4 w-100" style="max-width: 550px; max-height: 90vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="font-weight-bold text-danger mb-0"><i class="icofont-megaphone mr-1"></i> রক্তের জরুরি আবেদন ফরম</h5>
          <button type="button" class="close font-24" @click="showRequestModal = false">&times;</button>
        </div>

        <form @submit.prevent="submitBloodRequest">
          <div class="form-group mb-2">
            <label class="font-weight-bold small">রোগীর নাম *</label>
            <input type="text" v-model="newReq.patient_name" class="form-control form-control-sm" required placeholder="রোগীর পুরো নাম লিখুন">
          </div>

          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">রক্তের গ্রুপ *</label>
              <select v-model="newReq.blood_group" class="form-control form-control-sm" required>
                <option value="">গ্রুপ নির্বাচন করুন</option>
                <option v-for="g in bloodGroups" :key="g" :value="g">{{ g }}</option>
              </select>
            </div>
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">উপাদান (Component) *</label>
              <select v-model="newReq.component" class="form-control form-control-sm" required>
                <option value="whole_blood">হোল ব্লাড (সাধারণ রক্ত)</option>
                <option value="platelets">প্লাটিলেট (ডেঙ্গু রোগী)</option>
                <option value="plasma">প্লাজমা</option>
                <option value="rbc">পিআরবিসি</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">কয় ব্যাগ প্রয়োজন? *</label>
              <input type="number" v-model="newReq.units_needed" min="1" max="10" class="form-control form-control-sm" required>
            </div>
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">জরুরিতার মাত্রা *</label>
              <select v-model="newReq.urgency" class="form-control form-control-sm" required>
                <option value="critical_immediate">🚨 অতি জরুরি (এখনই)</option>
                <option value="within_12h">১২ ঘণ্টার মধ্যে</option>
                <option value="within_24h">২৪ ঘণ্টার মধ্যে</option>
                <option value="scheduled">নির্ধারিত সময়ে (অপারেশন)</option>
              </select>
            </div>
          </div>

          <div class="form-group mb-2">
            <label class="font-weight-bold small">হাসপাতালের নাম ও ঠিকানা *</label>
            <input type="text" v-model="newReq.hospital_name" class="form-control form-control-sm" required placeholder="যেমন: ঢাকা মেডিকেল, বেড ৩, ওয়ার্ড ৪">
          </div>

          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">জেলা *</label>
              <input type="text" v-model="newReq.district" class="form-control form-control-sm" required placeholder="যেমন: ঢাকা">
            </div>
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">উপজেলা / থানা</label>
              <input type="text" v-model="newReq.upazila" class="form-control form-control-sm" placeholder="যেমন: ধানমন্ডি">
            </div>
          </div>

          <div class="row">
            <div class="col-6 form-group mb-3">
              <label class="font-weight-bold small">যোগাযোগকারীর নাম *</label>
              <input type="text" v-model="newReq.contact_name" class="form-control form-control-sm" required placeholder="আপনার নাম">
            </div>
            <div class="col-6 form-group mb-3">
              <label class="font-weight-bold small">জরুরি মোবাইল নম্বর *</label>
              <input type="text" v-model="newReq.contact_phone" class="form-control form-control-sm" required placeholder="017XXXXXXXX">
            </div>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold small">রোগীর অবস্থা বা কারণ (সংক্ষেপে)</label>
            <textarea v-model="newReq.reason" rows="2" class="form-control form-control-sm" placeholder="যেমন: ডেঙ্গু প্লাটিলেট নেমে গেছে, সিজারিয়ান অপারেশন..."></textarea>
          </div>

          <button type="submit" class="btn btn-danger btn-block font-weight-bold rounded-pill py-2 shadow-sm" :disabled="submitting">
            {{ submitting ? 'পোস্ট হচ্ছে...' : 'জরুরি ব্লাড SOS পোস্ট করুন' }}
          </button>
        </form>
      </div>
    </div>

    <!-- Donor Registration Modal -->
    <div v-if="showDonorModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3">
      <div class="modal-card bg-white rounded-lg shadow-lg p-4 w-100" style="max-width: 480px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="font-weight-bold text-dark mb-0"><i class="icofont-heart-alt text-danger mr-1"></i> রক্তদাতা হিসেবে নিবন্ধন</h5>
          <button type="button" class="close font-24" @click="showDonorModal = false">&times;</button>
        </div>

        <form @submit.prevent="submitDonorRegistration">
          <div class="form-group mb-2">
            <label class="font-weight-bold small">আপনার নাম *</label>
            <input type="text" v-model="newDonor.name" class="form-control form-control-sm" required placeholder="আপনার পুরো নাম">
          </div>

          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">রক্তের গ্রুপ *</label>
              <select v-model="newDonor.blood_group" class="form-control form-control-sm" required>
                <option value="">গ্রুপ বাছুন</option>
                <option v-for="g in bloodGroups" :key="g" :value="g">{{ g }}</option>
              </select>
            </div>
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">মোবাইল নম্বর *</label>
              <input type="text" v-model="newDonor.phone" class="form-control form-control-sm" required placeholder="017XXXXXXXX">
            </div>
          </div>

          <div class="row">
            <div class="col-6 form-group mb-3">
              <label class="font-weight-bold small">জেলা *</label>
              <input type="text" v-model="newDonor.district" class="form-control form-control-sm" required placeholder="যেমন: রংপুর">
            </div>
            <div class="col-6 form-group mb-3">
              <label class="font-weight-bold small">উপজেলা / থানা</label>
              <input type="text" v-model="newDonor.upazila" class="form-control form-control-sm" placeholder="যেমন: সদর">
            </div>
          </div>

          <button type="submit" class="btn btn-success btn-block font-weight-bold rounded-pill py-2 shadow-sm" :disabled="submitting">
            {{ submitting ? 'সংরক্ষণ হচ্ছে...' : 'স্বেচ্ছাসেবী রক্তদাতা নিবন্ধন সম্পন্ন করুন' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'BloodSos',
  data() {
    return {
      activeTab: 'requests',
      requests: [],
      donors: [],
      loading: true,
      submitting: false,
      selectedGroup: '',
      filterDistrict: '',
      bloodGroups: ['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-'],
      showRequestModal: false,
      showDonorModal: false,
      newReq: {
        patient_name: '',
        blood_group: '',
        component: 'whole_blood',
        units_needed: 1,
        hospital_name: '',
        district: 'Dhaka',
        upazila: '',
        contact_name: '',
        contact_phone: '',
        urgency: 'within_24h',
        reason: '',
      },
      newDonor: {
        name: '',
        blood_group: '',
        phone: '',
        district: 'Dhaka',
        upazila: '',
      },
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    async loadData() {
      this.loading = true;
      try {
        const [reqRes, donorRes] = await Promise.all([
          axios.get('/blood-sos/requests', {
            params: { blood_group: this.selectedGroup, district: this.filterDistrict }
          }),
          axios.get('/blood-sos/donors', {
            params: { blood_group: this.selectedGroup, district: this.filterDistrict }
          }),
        ]);
        this.requests = reqRes.data.requests || [];
        this.donors = donorRes.data.donors || [];
      } catch (err) {
        console.error('Error loading blood SOS data:', err);
      } finally {
        this.loading = false;
      }
    },
    getComponentText(comp) {
      switch (comp) {
        case 'platelets': return 'প্লাটিলেট (Platelets)';
        case 'plasma': return 'প্লাজমা (Plasma)';
        case 'rbc': return 'পিআরবিসি (PRBC)';
        case 'whole_blood':
        default:
          return 'হোল ব্লাড';
      }
    },
    async submitBloodRequest() {
      this.submitting = true;
      try {
        const res = await axios.post('/blood-sos/requests', this.newReq);
        if (res.data.success) {
          Swal.fire('সফল হয়েছে!', res.data.message, 'success');
          this.showRequestModal = false;
          this.loadData();
        }
      } catch (err) {
        Swal.fire('ত্রুটি', err.response?.data?.message || 'আবেদন পোস্ট করা সম্ভব হয়নি।', 'error');
      } finally {
        this.submitting = false;
      }
    },
    async submitDonorRegistration() {
      this.submitting = true;
      try {
        const res = await axios.post('/blood-sos/donors/register', this.newDonor);
        if (res.data.success) {
          Swal.fire('অভিনন্দন!', res.data.message, 'success');
          this.showDonorModal = false;
          this.loadData();
        }
      } catch (err) {
        Swal.fire('ত্রুটি', err.response?.data?.message || 'নিবন্ধন ব্যর্থ হয়েছে।', 'error');
      } finally {
        this.submitting = false;
      }
    },
    async respondToSos(req) {
      Swal.fire({
        title: 'রক্তদান সহায়তা',
        html: `
          <p class="text-muted">রোগীর স্বজন: <strong>${req.contact_name}</strong></p>
          <p class="font-20 font-weight-bold text-danger">📞 <a href="tel:${req.contact_phone}">${req.contact_phone}</a></p>
          <p class="small text-muted">হাসপাতাল: ${req.hospital_name}</p>
        `,
        confirmButtonText: 'ফোন করুন',
        confirmButtonColor: '#b91c1c',
      });
    },
  },
};
</script>

<style scoped>
.blood-badge-lg {
  width: 55px;
  height: 55px;
  background: #fee2e2;
  color: #b91c1c;
  border: 2px solid #f87171;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  font-size: 20px;
}
.blood-badge-sm {
  width: 44px;
  height: 44px;
  background: #fee2e2;
  color: #b91c1c;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 15px;
}
.border-left-danger {
  border-left: 5px solid #dc2626 !important;
}
.modal-backdrop-custom {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  z-index: 9999;
}
</style>
