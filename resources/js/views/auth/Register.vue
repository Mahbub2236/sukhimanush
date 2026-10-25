<template>
  <div class="auth-page py-4 py-md-5 gray-bg min-vh-100 d-flex flex-column justify-content-center" style="padding-bottom: 110px !important;">
    <div class="container my-auto pb-4">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="card-header bg-primary text-white text-center py-4">
              <router-link to="/">
                <img src="/images/logo/Logo2.png" alt="সুখী মানুষ (Sukhi Manush)" style="height: 52px; background: rgba(255,255,255,0.92); padding: 4px 12px; border-radius: 12px;" class="mb-2">
              </router-link>
              <h4 class="mb-0 text-white font-weight-bold">সুখী মানুষ (Sukhi Manush)</h4>
              <small class="text-white-50">Create your Patient, Doctor, or Rural Health Agent Account</small>
            </div>

            <div class="card-body p-4 p-md-5 bg-white">
              <!-- Role Selector Tabs -->
              <div class="d-flex mb-4 bg-light p-1 rounded">
                <button 
                  type="button" 
                  class="btn flex-fill py-2 font-weight-bold"
                  :class="form.role === 'patient' ? 'btn-primary shadow-sm' : 'btn-light text-muted'"
                  @click="form.role = 'patient'"
                >
                  <i class="icofont-patient-file mr-1"></i> Patient
                </button>
                <button 
                  type="button" 
                  class="btn flex-fill py-2 font-weight-bold"
                  :class="form.role === 'doctor' ? 'btn-danger shadow-sm' : 'btn-light text-muted'"
                  @click="form.role = 'doctor'"
                >
                  <i class="icofont-doctor-alt mr-1"></i> Doctor
                </button>
                <button 
                  type="button" 
                  class="btn flex-fill py-2 font-weight-bold"
                  :class="form.role === 'agent' ? 'btn-success shadow-sm' : 'btn-light text-muted'"
                  @click="form.role = 'agent'"
                >
                  <i class="icofont-hospital mr-1"></i> Agent
                </button>
              </div>

              <!-- Agent Benefit Notice Banner -->
              <div v-if="form.role === 'agent'" class="alert alert-success d-flex align-items-center mb-4 py-2 px-3 small border-0 shadow-sm" style="background: #e8f8f5; color: #117864; border-radius: 8px;">
                <i class="icofont-badge text-success mr-2 font-weight-bold" style="font-size: 24px;"></i>
                <div>
                  <strong>পল্লী ডিজিটাল হেলথ পয়েন্ট পার্টনারশিপ:</strong> গ্রামের সাধারণ রোগীদের সিরিয়াল বুক করে দিন এবং প্রতি বুকিংয়ে <strong>৳৪০ কমিশন</strong> আয় করুন।
                </div>
              </div>

              <!-- Error Alert -->
              <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="icofont-warning mr-1"></i> {{ errorMessage }}
                <button type="button" class="close" @click="errorMessage = ''">&times;</button>
              </div>

              <form @submit.prevent="handleRegister">
                <div class="row">
                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Full Name <span class="text-danger">*</span></label>
                    <input 
                      type="text" 
                      v-model="form.name" 
                      class="form-control" 
                      :placeholder="form.role === 'doctor' ? 'Dr. John Doe' : 'John Doe'"
                      required
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Email Address <span class="text-danger">*</span></label>
                    <input 
                      type="email" 
                      v-model="form.email" 
                      class="form-control" 
                      placeholder="john@example.com"
                      required
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Password <span class="text-danger">*</span></label>
                    <input 
                      type="password" 
                      v-model="form.password" 
                      class="form-control" 
                      placeholder="Minimum 6 characters"
                      required
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Phone Number</label>
                    <input 
                      type="tel" 
                      v-model="form.phone" 
                      class="form-control" 
                      placeholder="+8801xxxxxxxxx"
                    >
                  </div>

                  <!-- DOCTOR SPECIFIC FIELDS -->
                  <template v-if="form.role === 'doctor'">
                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark small">Specialization <span class="text-danger">*</span></label>
                      <select v-model="form.specialization" class="form-control" required>
                        <option value="Cardiology">Cardiology (Heart Specialist)</option>
                        <option value="Pediatrics">Pediatrics (Child Specialist)</option>
                        <option value="Neurology">Neurology (Brain & Spine)</option>
                        <option value="Dental Care">Dental Care (Dentist)</option>
                        <option value="Ophthalmology">Ophthalmology (Eye Specialist)</option>
                        <option value="Orthopedics">Orthopedics (Bone & Joint)</option>
                        <option value="General Medicine">General Medicine</option>
                      </select>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark small">Designation <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        v-model="form.designation" 
                        class="form-control" 
                        placeholder="e.g. Senior Consultant Cardiologist"
                        required
                      >
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark small">Qualifications</label>
                      <input 
                        type="text" 
                        v-model="form.qualifications" 
                        class="form-control" 
                        placeholder="e.g. MBBS, FCPS, MD"
                      >
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark small">Consultation Fee ($)</label>
                      <input 
                        type="number" 
                        v-model="form.consultation_fee" 
                        class="form-control" 
                        placeholder="50"
                        min="10"
                      >
                    </div>

                    <div class="col-12 form-group mb-3">
                      <label class="font-weight-bold text-dark small">Clinic / Chamber Address <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        v-model="form.chamber_address" 
                        class="form-control" 
                        placeholder="e.g. Heart Care Chamber, Suite 201, 45 Central Road"
                        required
                      >
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark small">City</label>
                      <input 
                        type="text" 
                        v-model="form.city" 
                        class="form-control" 
                        placeholder="e.g. New York / Dhaka"
                      >
                    </div>
                  </template>

                  <!-- RURAL AGENT SPECIFIC FIELDS -->
                  <template v-else-if="form.role === 'agent'">
                    <div class="col-12 form-group mb-3">
                      <label class="font-weight-bold text-dark small">Pharmacy / Health Point Name (ফার্মেসি বা দোকানের নাম) <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        v-model="form.pharmacy_name" 
                        class="form-control font-weight-bold text-primary" 
                        placeholder="যেমন: মেসার্স সততা ডিজিটাল হেলথ পয়েন্ট"
                        required
                      >
                    </div>

                    <div class="col-12 form-group mb-3">
                      <label class="font-weight-bold text-dark small">Market & Village Location (বাজার, উপজেলা ও জেলা) <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        v-model="form.address" 
                        class="form-control" 
                        placeholder="যেমন: শিবগঞ্জ বাজার, শিবগঞ্জ, বগুড়া"
                        required
                      >
                    </div>
                  </template>

                  <!-- PATIENT SPECIFIC FIELDS -->
                  <template v-else>
                    <div class="col-12 form-group mb-3">
                      <label class="font-weight-bold text-dark small">Home Address / Location</label>
                      <input 
                        type="text" 
                        v-model="form.address" 
                        class="form-control" 
                        placeholder="Your residential address"
                      >
                    </div>
                  </template>
                </div>

                <button 
                  type="submit" 
                  class="btn btn-main-2 btn-round-full w-100 py-2 shadow-sm font-weight-bold mt-2"
                  :disabled="loading"
                >
                  <span v-if="loading">
                    <span class="spinner-border spinner-border-sm mr-2"></span> Creating Account...
                  </span>
                  <span v-else>
                    <i class="icofont-check-circled mr-1"></i> 
                    <span v-if="form.role === 'agent'">Register as Rural Agent</span>
                    <span v-else-if="form.role === 'doctor'">Join as Doctor</span>
                    <span v-else>Register Account</span>
                  </span>
                </button>
              </form>

              <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted small">Already have an account? </span>
                <router-link to="/login" class="font-weight-bold text-color">
                  Login Here
                </router-link>
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
  name: 'Register',
  data() {
    return {
      loading: false,
      errorMessage: '',
      form: {
        role: 'patient',
        name: '',
        pharmacy_name: '',
        email: '',
        password: '',
        phone: '',
        address: '',
        specialization: 'General Medicine',
        designation: '',
        qualifications: 'MBBS',
        chamber_address: '',
        city: 'Dhaka',
        consultation_fee: 500,
      },
    };
  },
  methods: {
    async handleRegister() {
      this.loading = true;
      this.errorMessage = '';

      try {
        const res = await axios.post('/auth/register', this.form);
        if (res.data.success) {
          localStorage.setItem('saas_token', res.data.token);
          localStorage.setItem('saas_user', JSON.stringify(res.data.user));
          if (res.data.doctor) {
            localStorage.setItem('saas_doctor', JSON.stringify(res.data.doctor));
          }

          window.dispatchEvent(new Event('auth-change'));

          if (res.data.user.role === 'doctor') {
            this.$router.push('/doctor/dashboard');
          } else if (res.data.user.role === 'agent') {
            this.$router.push('/agent/dashboard');
          } else {
            this.$router.push('/patient/dashboard');
          }
        }
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'Registration failed. Please check form details.';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
