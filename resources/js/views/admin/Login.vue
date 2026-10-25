<template>
  <div class="admin-login-page py-5 gray-bg min-vh-100 d-flex align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="card-header bg-primary text-white text-center py-4">
              <div class="mb-2">
                <img src="/images/logo/Logo2.png" alt="সুখী মানুষ (Sukhi Manush)" style="height: 52px; background: rgba(255,255,255,0.92); padding: 4px 12px; border-radius: 12px;">
              </div>
              <h4 class="mb-0 text-white font-weight-bold">সুখী মানুষ Admin Portal</h4>
              <small class="text-white-50">Clinic & Appointments Management</small>
            </div>

            <div class="card-body p-4 p-md-5 bg-white">
              <!-- Error Alert -->
              <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="icofont-warning mr-1"></i> {{ errorMessage }}
                <button type="button" class="close" @click="errorMessage = ''">&times;</button>
              </div>

              <!-- Demo Credentials Box (Development Only) -->
              <div v-if="showDemoCredentials" class="alert alert-info py-2 px-3 small mb-4">
                <strong>Default Credentials:</strong><br>
                Email: <code>admin@sukhimanush.com</code><br>
                Password: <code>password</code>
              </div>

              <form @submit.prevent="handleLogin">
                <div class="form-group mb-3">
                  <label class="font-weight-bold text-dark small">Admin Email</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-light"><i class="icofont-ui-user"></i></span>
                    </div>
                    <input 
                      type="email" 
                      v-model="form.email" 
                      class="form-control" 
                      placeholder="admin@sukhimanush.com"
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
                    <span class="spinner-border spinner-border-sm mr-2"></span> Authenticating...
                  </span>
                  <span v-else>
                    <i class="icofont-login mr-1"></i> Login to Dashboard
                  </span>
                </button>
              </form>

              <div class="text-center mt-4">
                <router-link to="/" class="text-muted small">
                  &larr; Back to Public Website
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
  name: 'AdminLogin',
  data() {
    const isDev = Boolean(import.meta.env.DEV || import.meta.env.VITE_ENABLE_DEMO_LOGIN === 'true');
    return {
      loading: false,
      errorMessage: '',
      form: {
        email: isDev ? 'admin@sukhimanush.com' : '',
        password: isDev ? 'password' : '',
      },
    };
  },
  computed: {
    showDemoCredentials() {
      return Boolean(import.meta.env.DEV || import.meta.env.VITE_ENABLE_DEMO_LOGIN === 'true');
    },
  },
  mounted() {
    if (localStorage.getItem('admin_token')) {
      this.$router.push('/admin/dashboard');
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.errorMessage = '';

      try {
        const res = await axios.post('/admin/login', this.form);
        if (res.data.success) {
          localStorage.setItem('admin_token', res.data.token);
          localStorage.setItem('admin_user', JSON.stringify(res.data.user));
          this.$router.push('/admin/dashboard');
        }
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'Invalid email or password.';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
