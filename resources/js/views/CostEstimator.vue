<template>
  <div class="cost-estimator-page pb-5">
    <!-- Header Banner -->
    <section class="page-title py-4 py-md-5 text-white position-relative" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #0284c7 100%);">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-12 text-center">
            <span class="badge badge-warning text-dark px-3 py-1 font-weight-bold mb-2">
              💰 স্বচ্ছ ও ফেয়ার মেডিকেল খরচ
            </span>
            <h1 class="text-white font-weight-bold mb-2">হাসপাতাল অপারেশন ও টেস্ট খরচ ক্যালকুলেটর</h1>
            <p class="text-white-50 small max-w-600 mx-auto mb-0">
              কোন অপারেশনে বা টেস্টে কত টাকা খরচ হয় তা হাসপাতালে যাওয়ার আগেই জানুন। কোনো হিডেন চার্জ ছাড়া সঠিক বাজেট করুন।
            </p>
          </div>
        </div>
      </div>
    </section>

    <div class="container mt-4">
      <!-- Search & Category Filters -->
      <div class="bg-white p-3 p-md-4 rounded-lg shadow-sm mb-4 border">
        <div class="row align-items-center">
          <div class="col-md-7 mb-2 mb-md-0">
            <label class="font-weight-bold small text-dark mb-1">অপারেশন বা টেস্টের নাম লিখে খুঁজুন:</label>
            <div class="input-group">
              <input 
                type="text" 
                v-model="searchQuery" 
                class="form-control font-weight-bold" 
                placeholder="যেমন: সিজার, অ্যাপেন্ডিক্স, পিত্তথলির পাথর, এমআরআই, ইকো..."
                @input="loadProcedures"
              >
              <div class="input-group-append">
                <span class="input-group-text bg-primary text-white"><i class="icofont-search"></i></span>
              </div>
            </div>
          </div>

          <div class="col-md-5">
            <label class="font-weight-bold small text-dark mb-1">ক্যাটাগরি:</label>
            <select v-model="selectedCategory" class="form-control" @change="loadProcedures">
              <option value="all">সকল ক্যাটাগরি</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Procedures List -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted small mt-2">খরচ তালিকা লোড হচ্ছে...</p>
      </div>

      <div v-else class="row">
        <div v-for="proc in procedures" :key="proc.id" class="col-lg-6 mb-4">
          <div class="card border-0 shadow-sm rounded-lg p-4 h-100 bg-white d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <span class="badge badge-primary mb-1">{{ proc.category }}</span>
                  <h5 class="font-weight-bold text-dark mb-1">{{ proc.name_bn }}</h5>
                  <small class="text-muted font-italic">{{ proc.name_en }}</small>
                </div>
                <span class="badge badge-success px-2 py-1 small">
                  <i class="icofont-check-circled"></i> মার্কেট রেট
                </span>
              </div>

              <!-- Estimated Price Range Badge -->
              <div class="p-3 rounded-lg mb-3 my-2" style="background: #f0fdf4; border: 1.5px dashed #22c55e;">
                <small class="text-muted d-block font-weight-bold">বাংলাদেশে আনুমানিক খরচ সীমা (Estimated Range):</small>
                <div class="font-weight-bold text-success font-24">
                  ৳{{ Number(proc.min_cost).toLocaleString() }} - ৳{{ Number(proc.max_cost).toLocaleString() }}
                </div>
                <small class="text-muted">হাসপাতালে থাকার আনুমানিক সময়: <strong>{{ proc.typical_stay_days > 0 ? (proc.typical_stay_days + ' দিন') : 'একই দিনে বাড়ি ফেরা সম্ভব' }}</strong></small>
              </div>

              <!-- Cost Breakdown Grid -->
              <div v-if="proc.cost_breakdown" class="mb-3 bg-light p-3 rounded small">
                <div class="font-weight-bold text-dark mb-2 border-bottom pb-1">
                  <i class="icofont-pie-chart text-primary mr-1"></i> আনুমানিক ব্যয়ের বিভাজন:
                </div>
                <div v-for="(amount, item) in proc.cost_breakdown" :key="item" class="d-flex justify-content-between py-1 border-bottom border-white">
                  <span class="text-muted">{{ item }}</span>
                  <strong class="text-dark">৳{{ Number(amount).toLocaleString() }}</strong>
                </div>
              </div>

              <!-- Inclusions / Exclusions -->
              <div class="small mb-3">
                <div class="text-success mb-1">
                  <i class="icofont-check-circled mr-1"></i> <strong>প্যাকেজে অন্তর্ভুক্ত:</strong> {{ proc.included_services_bn }}
                </div>
                <div class="text-muted">
                  <i class="icofont-close-circled text-danger mr-1"></i> <strong>অতিরিক্ত লাগতে পারে:</strong> {{ proc.excluded_services_bn }}
                </div>
              </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
              <span class="text-muted small">হাসপাতাল ভেদে রেট ভিন্ন হতে পারে</span>
              <router-link to="/doctors" class="btn btn-sm btn-outline-primary font-weight-bold rounded-pill px-3">
                <i class="icofont-stethoscope mr-1"></i> বিশেষজ্ঞ সার্জন খুঁজুন
              </router-link>
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
  name: 'CostEstimator',
  data() {
    return {
      procedures: [],
      categories: [],
      searchQuery: '',
      selectedCategory: 'all',
      loading: true,
    };
  },
  mounted() {
    this.loadProcedures();
  },
  methods: {
    async loadProcedures() {
      this.loading = true;
      try {
        const res = await axios.get('/cost-estimator/procedures', {
          params: { q: this.searchQuery, category: this.selectedCategory }
        });
        this.procedures = res.data.procedures || [];
        this.categories = res.data.categories || [];
      } catch (err) {
        console.error('Error loading procedures:', err);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
