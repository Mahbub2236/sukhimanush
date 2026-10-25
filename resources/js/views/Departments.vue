<template>
  <div class="departments-page">
    <section class="page-title bg-1 py-5" style="background: url('/images/bg/22.jpg') 50% 50% / cover no-repeat;">
      <div class="overlay"></div>
      <div class="container position-relative">
        <div class="row">
          <div class="col-md-12 text-center text-white py-3">
            <span class="text-uppercase letter-spacing font-weight-bold text-white-50">Care Centers</span>
            <h1 class="text-capitalize mb-2 text-white">Hospital Departments</h1>
            <p class="mb-0 text-white-50">Our specialized medical divisions dedicated to comprehensive health care.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section department py-5 gray-bg">
      <div class="container">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
        </div>

        <div v-else class="row">
          <div v-for="dept in departments" :key="dept.id" class="col-lg-4 col-md-6 mb-4">
            <div class="dept-box bg-white p-4 rounded shadow-sm h-100 d-flex flex-column justify-content-between">
              <div>
                <div class="d-flex align-items-center mb-3">
                  <i :class="[dept.icon, 'text-color mr-3']" style="font-size: 36px;"></i>
                  <div>
                    <h4 class="font-weight-bold mb-0 text-dark">{{ dept.name }}</h4>
                    <span class="text-muted small">{{ dept.doctors_count || 0 }} Specialist Doctors</span>
                  </div>
                </div>
                <img :src="'/' + dept.image" :alt="dept.name" class="img-fluid rounded mb-3" style="height: 170px; width: 100%; object-fit: cover;">
                <p class="text-muted small mb-4">{{ dept.short_description }}</p>
              </div>

              <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <router-link :to="'/appoinment?department_id=' + dept.id" class="btn btn-sm btn-main-2 btn-round">
                  <i class="icofont-calendar mr-1"></i> Book Dept
                </router-link>
                <router-link :to="'/departments/' + dept.slug" class="btn btn-sm btn-outline-dark btn-round">
                  Learn More &rarr;
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
  name: 'Departments',
  data() {
    return {
      departments: [],
      loading: true,
    };
  },
  async mounted() {
    try {
      const res = await axios.get('/departments');
      this.departments = res.data || [];
    } catch (err) {
      console.error('Error fetching departments:', err);
    } finally {
      this.loading = false;
    }
  },
};
</script>
