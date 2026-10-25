<template>
  <div 
    id="sukhi-manush-spa-root" 
    :class="isPortfolioRoute ? 'portfolio-mode' : 'min-h-screen flex flex-col bg-slate-50/50 pb-16 lg:pb-0 font-sans text-slate-800 antialiased selection:bg-sky-500 selection:text-white'"
  >
    <!-- Main Header (hidden on standalone portfolio route) -->
    <AppHeader v-if="!isPortfolioRoute" />

    <!-- SPA Router View with transition -->
    <main :class="isPortfolioRoute ? '' : 'flex-grow'">
      <router-view v-slot="{ Component }">
        <transition name="page-fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Main Footer (hidden on standalone portfolio route) -->
    <AppFooter v-if="!isPortfolioRoute" />

    <!-- Mobile Bottom Navigation (Visible only on mobile devices) -->
    <MobileBottomNav v-if="!isPortfolioRoute" />

    <!-- Sukhi Manush AI Health Floating Assistant Widget -->
    <AiChatWidget v-if="!isPortfolioRoute" />

    <!-- 100% Universal Voice Assistant for all Input fields & Textareas -->
    <UniversalVoiceAssistant v-if="!isPortfolioRoute" />

    <!-- PWA Install Prompt Banner -->
    <PwaInstallPrompt />
  </div>
</template>

<script>
import AppHeader from './components/Header.vue';
import AppFooter from './components/Footer.vue';
import MobileBottomNav from './components/MobileBottomNav.vue';
import AiChatWidget from './components/AiChatWidget.vue';
import UniversalVoiceAssistant from './components/UniversalVoiceAssistant.vue';
import PwaInstallPrompt from './components/PwaInstallPrompt.vue';

export default {
  name: 'App',
  components: {
    AppHeader,
    AppFooter,
    MobileBottomNav,
    AiChatWidget,
    UniversalVoiceAssistant,
    PwaInstallPrompt,
  },
  computed: {
    isPortfolioRoute() {
      const name = this.$route.name;
      const path = this.$route.path || '';
      return name === 'DoctorPortfolio' || path.startsWith('/dr/') || path.startsWith('/portfolio/') || path.startsWith('/p/');
    },
  },
};
</script>
