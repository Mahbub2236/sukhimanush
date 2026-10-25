<template>
  <div class="emergency-resources-page pb-5">
    <!-- Header Banner -->
    <section class="page-title py-4 py-md-5 text-white position-relative" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #0284c7 100%);">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-12 text-center">
            <span class="badge badge-danger px-3 py-1 font-weight-bold mb-2">
              🚨 জরুরি পরিবহন ও আইসিইউ সেবা
            </span>
            <h1 class="text-white font-weight-bold mb-2">এম্বুলেন্স ও আইসিইউ বেড রেডার</h1>
            <p class="text-white-50 small max-w-600 mx-auto mb-0">
              নিকটস্থ ২৪/৭ ভেরিফাইড এম্বুলেন্স চালকদের ফোন নম্বর এবং বিভিন্ন হাসপাতালের আইসিইউ ও সিসিইউ সিটের লাইভ স্ট্যাটাস।
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Navigation Tabs & Filter -->
    <div class="container mt-4">
      <div class="bg-white p-3 rounded-lg shadow-sm mb-4 border d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
        <ul class="nav nav-pills" style="gap: 8px;">
          <li class="nav-item">
            <button 
              class="nav-link font-weight-bold rounded-pill px-4 py-2" 
              :class="{ 'active bg-primary text-white': activeTab === 'ambulance', 'btn-light border text-dark': activeTab !== 'ambulance' }"
              @click="activeTab = 'ambulance'"
            >
              <i class="icofont-ambulance-cross mr-1"></i> ভেরিফাইড এম্বুলেন্স ({{ ambulances.length }})
            </button>
          </li>
          <li class="nav-item">
            <button 
              class="nav-link font-weight-bold rounded-pill px-4 py-2" 
              :class="{ 'active bg-success text-white': activeTab === 'icu', 'btn-light border text-dark': activeTab !== 'icu' }"
              @click="activeTab = 'icu'"
            >
              <i class="icofont-hospital mr-1"></i> লাইভ আইসিইউ / সিসিইউ বেড ({{ hospitals.length }})
            </button>
          </li>
        </ul>

        <!-- District Filter & GPS Locator -->
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
          <button 
            class="btn btn-sm btn-outline-danger font-weight-bold rounded-pill shadow-sm" 
            @click="detectUserLocation" 
            :disabled="locatingUser"
            title="আপনার বর্তমান GPS লোকেশন দিয়ে নিকটতম এম্বুলেন্স খুঁজুন"
          >
            <i class="icofont-location-arrow mr-1" :class="{ 'icofont-spin': locatingUser }"></i>
            {{ locatingUser ? 'লোকেশন ট্র্যাকিং...' : (userCoords ? 'GPS লোকেশন সক্রিয়' : 'আমার লোকেশন নিন') }}
          </button>

          <input 
            type="text" 
            v-model="filterDistrict" 
            class="form-control form-control-sm" 
            placeholder="জেলা দিয়ে খুঁজুন (যেমন: ঢাকা)" 
            @input="loadData"
            style="max-width: 180px;"
          >
          <button class="btn btn-sm btn-outline-secondary rounded-pill" @click="loadData">
            <i class="icofont-refresh"></i>
          </button>
        </div>
      </div>

      <!-- TAB 1: AMBULANCES -->
      <div v-if="activeTab === 'ambulance'">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="text-muted small mt-2">নিকটতম এম্বুলেন্স তালিকা লোড হচ্ছে...</p>
        </div>

        <div v-else-if="ambulances.length === 0" class="text-center py-5 bg-white rounded border">
          <i class="icofont-ambulance display-4 text-muted"></i>
          <h5 class="mt-3 font-weight-bold">কোনো এম্বুলেন্স পাওয়া যায়নি</h5>
          <p class="text-muted small">অন্য জেলা নির্বাচন করে চেষ্টা করুন অথবা ৯৯৯-এ কল দিন।</p>
        </div>

        <div v-else class="row">
          <div v-for="(amb, idx) in ambulances" :key="amb.id" class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-lg p-4 h-100 bg-white d-flex flex-column justify-content-between position-relative">
              <!-- Nearest badge for top ambulance -->
              <div v-if="idx === 0 && amb.distance_km" class="position-absolute" style="top: -10px; right: 20px;">
                <span class="badge badge-danger shadow-sm px-3 py-1 font-weight-bold">
                  🔥 আপনার সবচেয়ে নিকটতম
                </span>
              </div>

              <div>
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">{{ amb.service_name }}</h5>
                    <small class="text-muted"><i class="icofont-user text-primary"></i> চালক / পরিচালক: {{ amb.driver_or_contact_name }}</small>
                  </div>
                  <span class="badge px-3 py-1 font-weight-bold text-capitalize" :class="getVehicleBadge(amb.vehicle_type)">
                    {{ getVehicleText(amb.vehicle_type) }}
                  </span>
                </div>

                <!-- Distance Banner -->
                <div v-if="amb.distance_km" class="p-2 mb-2 rounded bg-light border border-danger d-flex justify-content-between align-items-center">
                  <span class="text-danger font-weight-bold small">
                    <i class="icofont-google-map mr-1"></i> আপনার থেকে দূরত্ব: {{ amb.distance_text }}
                  </span>
                  <span class="badge badge-warning text-dark font-weight-bold">
                    <i class="icofont-clock-time mr-1"></i> পৌঁছাতে ~{{ amb.time_text }}
                  </span>
                </div>

                <div class="bg-light p-3 rounded mb-3 small">
                  <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">লোকেশন / এলাকা:</span>
                    <strong class="text-dark"><i class="icofont-location-pin text-danger"></i> {{ amb.district }} <span v-if="amb.upazila">, {{ amb.upazila }}</span></strong>
                  </div>
                  <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">আনুমানিক বেস ভাড়া:</span>
                    <strong class="text-success font-15">৳{{ amb.base_fare }}</strong>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">প্রতি কি.মি. আনুমানিক:</span>
                    <strong class="text-dark">৳{{ amb.per_km_rate }}/কিমি</strong>
                  </div>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <span class="badge badge-success px-2 py-1 font-weight-bold">
                  <i class="icofont-check-circled"></i> ২৪/৭ সচল
                </span>
                <a :href="'tel:' + amb.phone" class="btn btn-primary font-weight-bold rounded-pill px-4 shadow-sm">
                  <i class="icofont-phone mr-1"></i> সরাসরি কল করুন ({{ amb.phone }})
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 2: HOSPITAL ICU / CCU BEDS -->
      <div v-if="activeTab === 'icu'">
        <div class="alert alert-warning p-3 rounded-lg mb-4 small d-flex align-items-center shadow-sm">
          <i class="icofont-info-circle font-22 mr-2 text-warning"></i>
          <div>
            <strong>সতর্কবার্তা:</strong> আইসিইউ বেডের সংখ্যা যেকোনো সময় পরিবর্তিত হতে পারে। হাসপাতালে পৌঁছানোর পূর্বে অনুগ্রহ করে সরাসরি হটলাইনে কল করে বেড বুকিং নিশ্চিত করুন।
          </div>
        </div>

        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-success" role="status"></div>
          <p class="text-muted small mt-2">হাসপাতাল বেড ডাটাবেজ রিফ্রেশ হচ্ছে...</p>
        </div>

        <div v-else class="row">
          <div v-for="hosp in hospitals" :key="hosp.id" class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-lg p-4 h-100 bg-white d-flex flex-column justify-content-between">
              <div>
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">{{ hosp.hospital_name }}</h5>
                    <small class="text-muted"><i class="icofont-location-pin text-danger"></i> {{ hosp.district }} <span v-if="hosp.upazila">, {{ hosp.upazila }}</span></small>
                  </div>
                  <span class="badge badge-secondary px-2 py-1">{{ hosp.hospital_type }}</span>
                </div>

                <!-- Bed Metrics Grid -->
                <div class="row g-2 text-center my-3">
                  <div class="col-4">
                    <div class="p-2 rounded border" :class="hosp.icu_available > 0 ? 'bg-success-light border-success' : 'bg-light'">
                      <small class="d-block text-muted" style="font-size: 11px;">আইসিইউ (ICU)</small>
                      <strong class="font-20" :class="hosp.icu_available > 0 ? 'text-success' : 'text-danger'">
                        {{ hosp.icu_available }} টি খালি
                      </strong>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="p-2 rounded border bg-light">
                      <small class="d-block text-muted" style="font-size: 11px;">সিসিইউ (CCU)</small>
                      <strong class="font-20 text-primary">{{ hosp.ccu_available }} টি খালি</strong>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="p-2 rounded border bg-light">
                      <small class="d-block text-muted" style="font-size: 11px;">এনআইসিইউ (NICU)</small>
                      <strong class="font-20 text-dark">{{ hosp.nicu_available }} টি খালি</strong>
                    </div>
                  </div>
                </div>

                <div class="small text-muted mb-2">
                  <i class="icofont-clock-time text-primary"></i> তথ্য যাচাই: 
                  <strong>{{ formatTimeAgo(hosp.last_verified_at) }}</strong>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                <span class="small font-weight-bold text-dark">হটলাইন: {{ hosp.hotline }}</span>
                <a :href="'tel:' + (hosp.emergency_phone || hosp.hotline)" class="btn btn-success font-weight-bold rounded-pill px-4 shadow-sm">
                  <i class="icofont-phone mr-1"></i> জরুরি বেড বুকিং কল
                </a>
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

export default {
  name: 'EmergencyResources',
  data() {
    return {
      activeTab: 'ambulance',
      ambulances: [],
      hospitals: [],
      loading: true,
      filterDistrict: '',
      userCoords: null,
      locatingUser: false,
    };
  },
  mounted() {
    if (this.$route.query.tab) {
      this.activeTab = this.$route.query.tab;
    }
    this.detectUserLocation();
  },
  methods: {
    detectUserLocation() {
      if ('geolocation' in navigator) {
        this.locatingUser = true;
        navigator.geolocation.getCurrentPosition(
          (pos) => {
            this.userCoords = {
              lat: pos.coords.latitude,
              lng: pos.coords.longitude,
            };
            this.locatingUser = false;
            this.loadData();
          },
          (err) => {
            console.warn('Geolocation access denied or unavailable:', err.message);
            this.locatingUser = false;
            this.loadData();
          },
          { timeout: 7000 }
        );
      } else {
        this.loadData();
      }
    },
    async loadData() {
      this.loading = true;
      try {
        const params = { district: this.filterDistrict };
        if (this.userCoords) {
          params.lat = this.userCoords.lat;
          params.lng = this.userCoords.lng;
        }

        const [ambRes, bedRes] = await Promise.all([
          axios.get('/emergency/ambulances', { params }),
          axios.get('/emergency/hospital-beds', { params: { district: this.filterDistrict } }),
        ]);
        this.ambulances = ambRes.data.ambulances || [];
        this.hospitals = bedRes.data.hospitals || [];
      } catch (err) {
        console.error('Error loading emergency resources:', err);
      } finally {
        this.loading = false;
      }
    },
    getVehicleText(type) {
      switch (type) {
        case 'icu_support': return 'আইসিইউ এম্বুলেন্স';
        case 'freezer': return 'ফ্রিজার এম্বুলেন্স';
        case 'non_ac': return 'নন-এসি সাধারণ';
        case 'ac':
        default:
          return 'এসি এম্বুলেন্স';
      }
    },
    getVehicleBadge(type) {
      switch (type) {
        case 'icu_support': return 'badge-danger text-white';
        case 'freezer': return 'badge-dark text-white';
        case 'non_ac': return 'badge-secondary text-white';
        case 'ac':
        default:
          return 'badge-primary text-white';
      }
    },
    formatTimeAgo(dateStr) {
      if (!dateStr) return 'সম্প্রতি যাচাইকৃত';
      return 'কয়েক মিনিট পূর্বে যাচাইকৃত';
    },
  },
};
</script>

<style scoped>
.bg-success-light {
  background: #f0fdf4;
}
</style>
