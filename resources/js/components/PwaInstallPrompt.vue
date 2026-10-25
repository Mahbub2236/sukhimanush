<template>
  <div>
    <!-- Slim Bottom-Left Capsule Pill -->
    <transition name="slide-fade">
      <div 
        v-if="showInstallPrompt && !isDismissed" 
        class="hidden lg:inline-flex fixed lg:bottom-5 lg:left-6 z-40 pwa-slim-badge items-center gap-1.5 p-1 px-2 rounded-full bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-lg shadow-slate-900/10 hover:border-sky-400 transition-all duration-200"
      >
        <!-- 1-Click Install Button -->
        <button 
          type="button"
          @click="installPwa"
          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-slate-800 hover:text-sky-600 hover:bg-sky-50 transition-all text-xs font-bold cursor-pointer"
          title="১ ক্লিকে অ্যাপ ইনস্টল করুন"
        >
          <span class="w-5 h-5 rounded-full bg-sky-600 text-white flex items-center justify-center text-[10px] shadow-xs">
            <i class="fa-solid fa-download"></i>
          </span>
          <span class="tracking-tight">অ্যাপ ইনস্টল</span>
        </button>

        <span class="w-px h-3.5 bg-slate-200"></span>

        <!-- Open QR Code Share Modal Button -->
        <button 
          type="button"
          @click="openQrModal"
          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-slate-700 hover:text-emerald-600 hover:bg-emerald-50 transition-all text-xs font-bold cursor-pointer"
          title="QR কোড দেখুন বা শেয়ার করুন"
        >
          <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] shadow-xs">
            <i class="fa-solid fa-qrcode"></i>
          </span>
          <span class="tracking-tight hidden sm:inline">QR কোড</span>
        </button>

        <span class="w-px h-3.5 bg-slate-200"></span>

        <!-- Close Pill Button -->
        <button 
          type="button"
          @click="dismissPrompt"
          class="w-6 h-6 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors text-xs cursor-pointer"
          title="বন্ধ করুন"
          aria-label="Close"
        >
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>
      </div>
    </transition>

    <!-- PWA QR Code & Share Modal -->
    <teleport to="body">
      <transition name="modal-fade">
        <div 
          v-if="showQrModal" 
          class="fixed inset-0 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
          style="z-index: 99999 !important;"
          @click.self="closeQrModal"
        >
          <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all animate-scale-up">
            <!-- Modal Header -->
            <div class="p-5 pb-4 bg-gradient-to-r from-sky-600 to-teal-600 text-white flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-lg shadow-inner">
                  <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                  <h3 class="text-base font-extrabold text-white leading-tight">অ্যাপ ইনস্টল QR কোড</h3>
                  <p class="text-xs text-sky-100 mt-0.5">ক্যামেরা দিয়ে স্ক্যান করে যেকেউ ইনস্টল করতে পারবে</p>
                </div>
              </div>

              <button 
                type="button" 
                @click="closeQrModal" 
                class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer"
                title="বন্ধ করুন"
              >
                <i class="fa-solid fa-xmark text-sm"></i>
              </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 text-center space-y-5">
              <!-- QR Code Card Box -->
              <div class="inline-block p-4 rounded-2xl bg-white border-2 border-dashed border-sky-200 shadow-md relative group">
                <img 
                  :src="qrCodeUrl" 
                  alt="সুখী মানুষ (Sukhi Manush) Health PWA Install QR Code" 
                  class="w-48 h-48 mx-auto rounded-xl object-contain"
                />
                <div class="mt-2 text-[11px] font-bold text-sky-700 bg-sky-50 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5 border border-sky-100">
                  <i class="fa-solid fa-mobile-screen-button text-xs"></i>
                  <span>ক্যামেরা দিয়ে স্ক্যান করুন</span>
                </div>
              </div>

              <!-- Shareable Link Copy Row -->
              <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200 text-left">
                <i class="fa-solid fa-link text-slate-400 text-xs ml-2"></i>
                <input 
                  type="text" 
                  readonly 
                  :value="appInstallUrl" 
                  class="flex-1 bg-transparent text-xs text-slate-700 font-mono truncate focus:outline-none"
                />
                <button 
                  type="button"
                  @click="copyLink"
                  class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer shrink-0"
                  :class="copied ? 'bg-emerald-600 text-white' : 'bg-slate-200 hover:bg-slate-300 text-slate-700'"
                >
                  <i :class="copied ? 'fa-solid fa-check' : 'fa-regular fa-copy'"></i>
                  <span>{{ copied ? 'কপি হয়েছে!' : 'কপি' }}</span>
                </button>
              </div>

              <!-- Quick Action Share & Download Buttons -->
              <div class="grid grid-cols-2 gap-2.5">
                <!-- WhatsApp Share -->
                <button 
                  type="button" 
                  @click="shareOnWhatsApp"
                  class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition active:scale-95 cursor-pointer"
                >
                  <i class="fa-brands fa-whatsapp text-sm"></i>
                  <span>WhatsApp-এ পাঠান</span>
                </button>

                <!-- Download QR Image -->
                <button 
                  type="button" 
                  @click="downloadQrCode"
                  class="py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition active:scale-95 cursor-pointer"
                >
                  <i class="fa-solid fa-cloud-arrow-down text-sm"></i>
                  <span>QR কোড ডাউনলোড</span>
                </button>
              </div>

              <!-- Guide Steps for Android and iPhone Users -->
              <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-left text-xs text-slate-600 space-y-1.5">
                <div class="font-bold text-slate-800 text-[11px] flex items-center gap-1.5">
                  <i class="fa-solid fa-circle-info text-sky-600"></i>
                  <span>স্ক্যান করার সহজ নিয়ম:</span>
                </div>
                <div class="flex items-start gap-1.5 text-[11px] leading-tight">
                  <i class="fa-brands fa-android text-emerald-600 mt-0.5"></i>
                  <span><strong>Android:</strong> ক্যামেরা অন করে স্ক্যান করুন &rarr; স্ক্রিনের <strong>"Install"</strong> বাটনে ট্যাপ করুন।</span>
                </div>
                <div class="flex items-start gap-1.5 text-[11px] leading-tight">
                  <i class="fa-brands fa-apple text-slate-700 mt-0.5"></i>
                  <span><strong>iPhone:</strong> ক্যামেরা দিয়ে স্ক্যান করুন &rarr; Safari ব্রাউজারে Share বাটন চেপে <strong>"Add to Home Screen"</strong> চাপুন।</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script>
export default {
  name: 'PwaInstallPrompt',
  data() {
    return {
      deferredPrompt: null,
      showInstallPrompt: false,
      isDismissed: false,
      showQrModal: false,
      copied: false,
    };
  },
  computed: {
    appInstallUrl() {
      if (typeof window !== 'undefined') {
        const origin = window.location.origin;
        return `${origin}/?source=qr_install`;
      }
      return 'https://sukhimanush.com/?source=qr_install';
    },
    qrCodeUrl() {
      return `https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=8&data=${encodeURIComponent(this.appInstallUrl)}`;
    },
  },
  mounted() {
    // Check if launched from QR code scan
    if (typeof window !== 'undefined') {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('source') === 'qr_install') {
        setTimeout(() => {
          if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
          }
        }, 800);
      }
    }

    // Check if dismissed in localStorage within last 7 days
    const dismissedAt = localStorage.getItem('pwa_prompt_dismissed_at');
    if (dismissedAt) {
      const elapsed = Date.now() - parseInt(dismissedAt, 10);
      if (elapsed < 7 * 24 * 60 * 60 * 1000) {
        this.isDismissed = true;
      }
    }

    // Capture the beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      this.deferredPrompt = e;
      this.showInstallPrompt = true;
    });

    if (!this.isDismissed && !window.matchMedia('(display-mode: standalone)').matches) {
      this.showInstallPrompt = true;
    }

    // Listen for successful installation
    window.addEventListener('appinstalled', () => {
      this.showInstallPrompt = false;
      this.deferredPrompt = null;
    });
  },
  methods: {
    async installPwa() {
      if (!this.deferredPrompt) {
        // Fallback open the QR code modal so user can scan or follow guide
        this.openQrModal();
        return;
      }
      this.deferredPrompt.prompt();
      const { outcome } = await this.deferredPrompt.userChoice;
      if (outcome === 'accepted') {
        this.showInstallPrompt = false;
      }
      this.deferredPrompt = null;
    },
    openQrModal() {
      this.showQrModal = true;
    },
    closeQrModal() {
      this.showQrModal = false;
    },
    copyLink() {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(this.appInstallUrl);
      } else {
        const input = document.createElement('input');
        input.value = this.appInstallUrl;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
      }
      this.copied = true;
      setTimeout(() => {
        this.copied = false;
      }, 2000);
    },
    shareOnWhatsApp() {
      const text = encodeURIComponent(`সুখী মানুষ (Sukhi Manush) ডিজিটাল স্বাস্থ্যসেবা অ্যাপ ইনস্টল করতে এই লিংকে ক্লিক করুন বা স্ক্যান করুন:\n${this.appInstallUrl}`);
      window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
    },
    async downloadQrCode() {
      try {
        const res = await fetch(this.qrCodeUrl);
        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'sukhi-manush-app-qr.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
      } catch (err) {
        window.open(this.qrCodeUrl, '_blank');
      }
    },
    dismissPrompt() {
      this.isDismissed = true;
      localStorage.setItem('pwa_prompt_dismissed_at', String(Date.now()));
    },
  },
};
</script>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(12px);
  opacity: 0;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: all 0.25s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

@keyframes scaleUp {
  0% { transform: scale(0.95); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-scale-up {
  animation: scaleUp 0.25s ease-out forwards;
}

.pwa-slim-badge {
  user-select: none;
}
</style>
