<template>
  <div class="department-detail-page">
    <section class="page-title bg-1 py-5" style="background: url('/images/bg/22.jpg') 50% 50% / cover no-repeat;">
      <div class="overlay"></div>
      <div class="container position-relative">
        <div class="row">
          <div class="col-md-12 text-center text-white py-3">
            <span class="text-uppercase letter-spacing font-weight-bold text-white-50">Department Overview</span>
            <h1 class="text-capitalize mb-2 text-white">{{ department?.name || 'Department' }}</h1>
            <p class="mb-0 text-white-50">Center of Clinical Excellence</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section department-single py-5">
      <div class="container">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
        </div>

        <div v-else-if="department" class="row">
          <div class="col-lg-8">
            <div class="department-content">
              <img :src="'/' + department.image" :alt="department.name" class="img-fluid rounded shadow-sm w-100 mb-4" style="max-height: 380px; object-fit: cover;">
              <h2 class="font-weight-bold mb-3">{{ department.name }} Services</h2>
              <p class="lead text-muted">{{ department.short_description }}</p>
              <div class="divider my-4"></div>
              <p class="text-dark">{{ department.full_description }}</p>

              <!-- Doctors in this Department -->
              <h3 class="font-weight-bold mt-5 mb-4">Department Specialists</h3>
              <div class="row">
                <div v-for="doc in department.doctors" :key="doc.id" class="col-md-6 mb-3">
                  <div class="bg-light p-3 rounded d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <img :src="'/' + doc.image" class="rounded-circle mr-3" style="width: 55px; height: 55px; object-fit: cover;">
                      <div>
                        <h6 class="font-weight-bold mb-0">{{ doc.name }}</h6>
                        <small class="text-muted">{{ doc.designation }}</small>
                      </div>
                    </div>
                    <router-link :to="'/appoinment?doctor_id=' + doc.id" class="btn btn-sm btn-main-2 btn-round">
                      Book
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="sidebar-widget bg-light p-4 rounded shadow-sm mb-4">
              <h5 class="font-weight-bold mb-3">Department Hotline</h5>
              <h4 class="text-color font-weight-bold mb-2">{{ department.emergency_contact || '+1 (800) 234-5678' }}</h4>
              <p class="text-muted small">Direct triage contact for urgent patient admissions.</p>
              <router-link :to="'/appoinment?department_id=' + department.id" class="btn btn-main-2 btn-round-full w-100">
                <i class="icofont-calendar mr-1"></i> Make An Appointment
              </router-link>
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
  name: 'DepartmentDetail',
  props: ['slug'],
  data() {
    return {
      department: null,
      loading: true,
    };
  },
  async mounted() {
    try {
      const res = await axios.get(`/departments/${this.slug}`);
      this.department = res.data;
    } catch (err) {
      console.error('Error fetching department:', err);
    } finally {
      this.loading = false;
    }
  },
};
</script>
