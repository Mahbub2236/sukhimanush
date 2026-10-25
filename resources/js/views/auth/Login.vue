<template>
  <div class="auth-page py-4 py-md-5 gray-bg min-vh-100 d-flex flex-column justify-content-center" style="padding-bottom: 110px !important;">
    <div class="container my-auto pb-4">
      <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
          <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="card-header bg-primary text-white text-center py-4">
              <router-link to="/">
                <img src="/images/logo/Logo2.png" alt="সুখী মানুষ (Sukhi Manush)" style="height: 52px; background: rgba(255,255,255,0.92); padding: 4px 12px; border-radius: 12px;" class="mb-2">
              </router-link>
              <h4 class="mb-0 text-white font-weight-bold">সুখী মানুষ (Sukhi Manush)</h4>
              <small class="text-white-50">Patient, Doctor & Admin Portal</small>
            </div>

            <div class="card-body p-4 p-md-5 bg-white">
              <!-- Portal Mode Switcher Tabs -->
              <div class="d-flex mb-3 border rounded overflow-hidden p-1 bg-light">
                <button 
                  type="button" 
                  class="btn btn-sm flex-fill font-weight-bold" 
                  :class="activeMode === 'standard' ? 'btn-primary shadow-sm' : 'text-muted'"
                  @click="activeMode = 'standard'"
                >
                  <i class="icofont-doctor mr-1"></i> Patient / Doctor / Admin
                </button>
                <button 
                  type="button" 
                  class="btn btn-sm flex-fill font-weight-bold" 
                  :class="activeMode === 'staff' ? 'btn-danger shadow-sm' : 'text-muted'"
                  @click="activeMode = 'staff'"
                >
                  <i class="icofont-users-alt-5 mr-1"></i> চেম্বার সহকারী / Staff
                </button>
              </div>

              <!-- Quick 1-Click Demo Login Bar (Enabled in development only) -->
              <div v-if="showDemoLogins" class="bg-light p-2 rounded mb-3 border">
                <span class="small font-weight-bold text-dark d-block mb-1 text-center" style="font-size: 11px;">
                  <i class="icofont-flash text-warning mr-1"></i> Quick 1-Click Demo Logins:
                </span>
                <div class="d-flex justify-content-between gap-1 flex-wrap">
                  <button type="button" class="btn btn-xs btn-outline-primary flex-fill mr-1 py-1" style="font-size: 11px;" @click="setDemo('patient')">
                    Patient
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-success flex-fill mr-1 py-1" style="font-size: 11px;" @click="setDemo('doctor')">
                    Doctor
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-info flex-fill mr-1 py-1" style="font-size: 11px;" @click="setDemo('agent')">
                    Agent (Rafiq)
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-danger flex-fill mr-1 py-1" style="font-size: 11px;" @click="setDemo('staff')">
                    Staff (Kamal)
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-dark flex-fill py-1" style="font-size: 11px;" @click="setDemo('admin')">
                    Admin
                  </button>
                </div>
              </div>

              <!-- Error Alert -->
              <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="icofont-warning mr-1"></i> {{ errorMessage }}
                <button type="button" class="close" @click="errorMessage = ''">&times;</button>
              </div>

              <!-- STANDARD USER / DOCTOR LOGIN FORM -->
              <form v-if="activeMode === 'standard'" @submit.prevent="handleLogin">
                <div class="form-group mb-3">
                  <label class="font-weight-bold text-dark small">Email Address</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-light"><i class="icofont-ui-user"></i></span>
                    </div>
                    <input 
                      type="email" 
                      v-model="form.email" 
                      class="form-control" 
                      placeholder="your.email@example.com"
                      required
                    >
                  </div>
                </div>

                <div class="form-group mb-4">
                  <label class="font-weight-bold text-dark small">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-light"><i class="icofont-ui-password"></i></span>
                    </div>
                    <input 
                      type="password" 
                      v-model="form.password" 
                      class="form-control" 
                      placeholder="••••••••"
                      required
                    >
                  </div>
                </div>

                <button 
                  type="submit" 
                  class="btn btn-main-2 btn-round-full w-100 py-2 shadow-sm font-weight-bold"
                  :disabled="loading"
                >
                  <span v-if="loading">
                    <span class="spinner-border spinner-border-sm mr-2"></span> Signing In...
                  </span>
                  <span v-else>
                    <i class="icofont-login mr-1"></i> Sign In to Portal
                  </span>
                </button>
              </form>

              <!-- STAFF / ASSISTANT LOGIN FORM -->
              <form v-else @submit.prevent="handleStaffLogin">
                <div class="form-group mb-3">
                  <label class="font-weight-bold text-dark small">সহকারীর মোবাইল নম্বর (Phone)</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-light"><i class="icofont-phone"></i></span>
                    </div>
                    <input 
                      type="tel" 
                      v-model="staffForm.phone" 
                      class="form-control font-weight-bold" 
                      placeholder="যেমন: 01838046508"
                      required
                    >
                  </div>
                </div>

                <div class="form-group mb-4">
                  <label class="font-weight-bold text-dark small">অ্যাক্সেস পিন (4-6 Digit Access PIN)</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-light"><i class="icofont-key"></i></span>
                    </div>
                    <input 
                      type="password" 
                      v-model="staffForm.access_pin" 
                      class="form-control font-weight-bold" 
                      placeholder="যেমন: 123456"
                      maxlength="6"
                      required
                    >
                  </div>
                  <small class="text-muted">ডাক্তার তাঁর ড্যাশবোর্ড থেকে যে পিন দিয়েছেন তা লিখুন</small>
                </div>

                <button 
                  type="submit" 
                  class="btn btn-danger btn-round-full w-100 py-2 shadow-sm font-weight-bold"
                  :disabled="loading"
                >
                  <span v-if="loading">
                    <span class="spinner-border spinner-border-sm mr-2"></span> যাচাই হচ্ছে...
                  </span>
                  <span v-else>
                    <i class="icofont-login mr-1"></i> সহকারী পোর্টালে প্রবেশ করুন
                  </span>
                </button>
              </form>

              <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted small mb-2">Don't have an account?</p>
                <router-link to="/register" class="btn btn-sm btn-outline-dark btn-round-full px-4">
                  Register as Patient / Doctor
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
  name: 'Login',
  data() {
    const isDev = Boolean(import.meta.env.DEV || import.meta.env.VITE_ENABLE_DEMO_LOGIN === 'true');
    return {
      activeMode: 'standard', // 'standard' or 'staff'
      loading: false,
      errorMessage: '',
      form: {
        email: isDev ? 'patient@sukhimanush.com' : '',
        password: isDev ? 'password' : '',
      },
      staffForm: {
        phone: isDev ? '01838046508' : '',
        access_pin: isDev ? '123456' : '',
      },
    };
  },
  computed: {
    showDemoLogins() {
      return Boolean(import.meta.env.DEV || import.meta.env.VITE_ENABLE_DEMO_LOGIN === 'true');
    },
  },
  mounted() {
    const user = localStorage.getItem('saas_user');
    if (user) {
      this.redirectUser(JSON.parse(user));
    }
  },
  methods: {
    setDemo(type) {
      if (type === 'patient') {
        this.activeMode = 'standard';
        this.form.email = 'patient@sukhimanush.com';
        this.form.password = 'password';
      } else if (type === 'doctor') {
        this.activeMode = 'standard';
        this.form.email = 'dr.thomas@sukhimanush.com';
        this.form.password = 'password';
      } else if (type === 'agent') {
        this.activeMode = 'standard';
        this.form.email = 'agent@sukhimanush.local';
        this.form.password = 'password';
      } else if (type === 'admin') {
        this.activeMode = 'standard';
        this.form.email = 'admin@sukhimanush.com';
        this.form.password = 'password';
      } else if (type === 'staff') {
        this.activeMode = 'staff';
        this.staffForm.phone = '01838046508';
        this.staffForm.access_pin = '123456';
      }
    },
    async handleLogin() {
      this.loading = true;
      this.errorMessage = '';

      try {
        const res = await axios.post('/auth/login', this.form);
        if (res.data.success) {
          localStorage.setItem('saas_token', res.data.token);
          localStorage.setItem('saas_user', JSON.stringify(res.data.user));
          if (res.data.doctor) {
            localStorage.setItem('saas_doctor', JSON.stringify(res.data.doctor));
          } else {
            localStorage.removeItem('saas_doctor');
          }
          localStorage.removeItem('saas_assistant');

          // Trigger custom storage event for header update
          window.dispatchEvent(new Event('auth-change'));

          this.redirectUser(res.data.user);
        }
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'Invalid email or password.';
      } finally {
        this.loading = false;
      }
    },
    async handleStaffLogin() {
      this.loading = true;
      this.errorMessage = '';

      try {
        const res = await axios.post('/auth/staff-login', this.staffForm);
        if (res.data.success) {
          localStorage.setItem('saas_token', res.data.token);
          localStorage.setItem('saas_user', JSON.stringify(res.data.user));
          localStorage.setItem('saas_assistant', JSON.stringify(res.data.assistant));
          if (res.data.doctor) {
            localStorage.setItem('saas_doctor', JSON.stringify(res.data.doctor));
          }

          window.dispatchEvent(new Event('auth-change'));

          this.$router.push('/staff/dashboard');
        }
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'ভুল মোবাইল নম্বর অথবা অ্যাক্সেস পিন!';
      } finally {
        this.loading = false;
      }
    },
    redirectUser(user) {
      if (user.role === 'super_admin') {
        this.$router.push('/admin/dashboard');
      } else if (user.role === 'doctor') {
        this.$router.push('/doctor/dashboard');
      } else if (user.role === 'staff') {
        this.$router.push('/staff/dashboard');
      } else if (user.role === 'agent') {
        this.$router.push('/agent/dashboard');
      } else {
        this.$router.push('/patient/dashboard');
      }
    },
  },
};
</script>
