<template>
  <div class="service-detail-page">
    <section class="page-title bg-1 py-5" style="background: url('/images/bg/22.jpg') 50% 50% / cover no-repeat;">
      <div class="overlay"></div>
      <div class="container position-relative">
        <div class="row">
          <div class="col-md-12 text-center text-white py-3">
            <span class="text-uppercase letter-spacing font-weight-bold text-white-50">Service Overview</span>
            <h1 class="text-capitalize mb-2 text-white">{{ service?.title || 'Service Details' }}</h1>
            <p class="mb-0 text-white-50">{{ service?.department?.name }} Department</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section service-single py-5">
      <div class="container">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
        </div>

        <div v-else-if="service" class="row">
          <div class="col-lg-8">
            <div class="service-single-item mb-4">
              <img :src="'/' + service.image" :alt="service.title" class="img-fluid rounded shadow-sm w-100 mb-4" style="max-height: 400px; object-fit: cover;">
              <h2 class="font-weight-bold mb-3">{{ service.title }}</h2>
              <p class="lead text-muted">{{ service.short_description }}</p>
              <div class="divider my-4"></div>
              <p class="text-dark">{{ service.full_description }}</p>

              <h4 class="font-weight-bold mt-4 mb-3">Key Features & Inclusions</h4>
              <ul class="list-unstyled">
                <li v-for="(feat, idx) in service.features" :key="idx" class="d-flex align-items-center mb-2">
                  <i class="icofont-check-circled text-color mr-2 font-weight-bold"></i>
                  <span>{{ feat }}</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="sidebar-widget bg-light p-4 rounded shadow-sm mb-4">
              <h5 class="font-weight-bold mb-3">Service Package</h5>
              <div class="h3 font-weight-bold text-success mb-3">${{ service.price }}</div>
              <p class="text-muted small">Inclusive of initial clinical evaluation, procedural diagnostic tests, and follow-up support.</p>
              <router-link :to="'/appoinment?department_id=' + service.department_id" class="btn btn-main-2 btn-round-full w-100 mb-3">
                <i class="icofont-calendar mr-1"></i> Book This Service
              </router-link>
              <router-link to="/services" class="btn btn-outline-dark btn-round-full w-100">
                &larr; Back to All Services
              </router-link>
            </div>

            <div class="sidebar-widget bg-white p-4 rounded border">
              <h5 class="font-weight-bold mb-3">24/7 Emergency Care</h5>
              <p class="text-muted small">For urgent medical consultation or ambulance requests:</p>
              <h5 class="text-color font-weight-bold"><i class="icofont-support mr-2"></i> +1 (800) 234-5678</h5>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ServiceDetail',
  props: ['slug'],
  data() {
    return {
      service: null,
      loading: true,
    };
  },
  async mounted() {
    try {
      const res = await axios.get(`/services/${this.slug}`);
      this.service = res.data;
    } catch (err) {
      console.error('Error fetching service:', err);
    } finally {
      this.loading = false;
    }
  },
};
</script>
