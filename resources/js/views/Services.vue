<template>
  <div class="services-page">
    <section class="page-title bg-1 py-5" style="background: url('/images/bg/22.jpg') 50% 50% / cover no-repeat;">
      <div class="overlay"></div>
      <div class="container position-relative">
        <div class="row">
          <div class="col-md-12 text-center text-white py-3">
            <span class="text-uppercase letter-spacing font-weight-bold text-white-50">Our Healthcare Capabilities</span>
            <h1 class="text-capitalize mb-2 text-white">Medical Services & Treatments</h1>
            <p class="mb-0 text-white-50">Comprehensive, specialized medical services tailored to each patient's individual care plan.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section service-2 py-5 gray-bg">
      <div class="container">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
        </div>

        <div v-else class="row">
          <div v-for="service in services" :key="service.id" class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <div class="service-block bg-white p-4 rounded shadow-sm h-100 d-flex flex-column justify-content-between">
              <div>
                <img :src="'/' + service.image" :alt="service.title" class="img-fluid rounded mb-3" style="height: 180px; width: 100%; object-fit: cover;">
                <div class="d-flex align-items-center mb-2">
                  <i :class="[service.icon, 'text-color mr-2']" style="font-size: 24px;"></i>
                  <span class="badge badge-light border">{{ service.department?.name }}</span>
                </div>
                <h4 class="font-weight-bold mb-2">{{ service.title }}</h4>
                <p class="text-muted small mb-3">{{ service.short_description }}</p>

                <ul v-if="service.features" class="list-unstyled small mb-4">
                  <li v-for="(feat, idx) in service.features.slice(0, 3)" :key="idx" class="text-muted mb-1">
                    <i class="icofont-check text-success mr-1"></i> {{ feat }}
                  </li>
                </ul>
              </div>

              <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                <span v-if="service.price" class="h6 font-weight-bold text-success mb-0">${{ service.price }}</span>
                <router-link :to="'/services/' + service.slug" class="btn btn-sm btn-outline-primary btn-round">
                  Full Details &rarr;
                </router-link>
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

export default {
  name: 'Services',
  data() {
    return {
      services: [],
      loading: true,
    };
  },
  async mounted() {
    try {
      const res = await axios.get('/services');
      this.services = res.data || [];
    } catch (err) {
      console.error('Error fetching services:', err);
    } finally {
      this.loading = false;
    }
  },
};
</script>
