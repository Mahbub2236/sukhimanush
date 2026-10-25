<template>
  <nav class="mobile-bottom-nav d-lg-none lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t border-slate-200/80 px-2 py-1 shadow-lg shadow-slate-900/10">
    <div class="max-w-md mx-auto flex items-center justify-around">
      <!-- Home -->
      <router-link 
        to="/" 
        class="flex flex-col items-center justify-center py-1.5 px-2 rounded-xl transition-all"
        :class="$route.path === '/' ? 'text-sky-600 font-bold scale-105' : 'text-slate-500 hover:text-slate-800'"
      >
        <i class="fa-solid fa-house text-base"></i>
        <span class="text-[10px] mt-0.5">হোম</span>
      </router-link>

      <!-- Doctors -->
      <router-link 
        to="/doctors" 
        class="flex flex-col items-center justify-center py-1.5 px-2 rounded-xl transition-all"
        :class="$route.path === '/doctors' ? 'text-sky-600 font-bold scale-105' : 'text-slate-500 hover:text-slate-800'"
      >
        <i class="fa-solid fa-user-doctor text-base"></i>
        <span class="text-[10px] mt-0.5">ডাক্তার</span>
      </router-link>

      <!-- Center Floating Booking Button -->
      <router-link 
        to="/appoinment" 
        class="-mt-5 flex flex-col items-center justify-center w-12 h-12 rounded-full bg-gradient-to-tr from-sky-600 to-teal-500 text-white shadow-lg shadow-sky-600/30 active:scale-95 transition-transform"
      >
        <i class="fa-regular fa-calendar-plus text-lg"></i>
      </router-link>

      <!-- Track Pass -->
      <router-link 
        to="/track-appointment" 
        class="flex flex-col items-center justify-center py-1.5 px-2 rounded-xl transition-all"
        :class="$route.path === '/track-appointment' ? 'text-sky-600 font-bold scale-105' : 'text-slate-500 hover:text-slate-800'"
      >
        <i class="fa-solid fa-ticket-simple text-base"></i>
        <span class="text-[10px] mt-0.5">ট্র্যাক</span>
      </router-link>

      <!-- Profile / Login -->
      <router-link 
        :to="portalPath" 
        class="flex flex-col items-center justify-center py-1.5 px-2 rounded-xl transition-all"
        :class="isProfileActive ? 'text-sky-600 font-bold scale-105' : 'text-slate-500 hover:text-slate-800'"
      >
        <i class="fa-regular fa-user text-base"></i>
        <span class="text-[10px] mt-0.5">{{ userLabel }}</span>
      </router-link>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'MobileBottomNav',
  data() {
    return {
      currentUser: null,
    };
  },
  computed: {
    portalPath() {
      if (!this.currentUser) return '/login';
      if (this.currentUser.role === 'super_admin') return '/admin/dashboard';
      if (this.currentUser.role === 'doctor') return '/doctor/dashboard';
      if (this.currentUser.role === 'staff') return '/staff/dashboard';
      if (this.currentUser.role === 'agent') return '/agent/dashboard';
      return '/patient/dashboard';
    },
    userLabel() {
      if (!this.currentUser) return 'লগইন';
      return 'প্রোফাইল';
    },
    isProfileActive() {
      const p = this.$route.path;
      return p.includes('dashboard') || p === '/login' || p === '/register';
    },
  },
  mounted() {
    this.checkAuth();
    window.addEventListener('auth-change', this.checkAuth);
  },
  beforeUnmount() {
    window.removeEventListener('auth-change', this.checkAuth);
  },
  methods: {
    checkAuth() {
      const userStr = localStorage.getItem('saas_user');
      this.currentUser = userStr ? JSON.parse(userStr) : null;
    },
  },
};
</script>

<style scoped>
@media (min-width: 992px) {
  .mobile-bottom-nav {
    display: none !important;
  }
}
</style>
