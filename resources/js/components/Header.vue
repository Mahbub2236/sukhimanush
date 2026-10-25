<template>
  <header class="w-full relative z-40">
    <!-- Top Micro Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 border-b border-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
          <!-- Left: Contact / Brand -->
          <div class="flex items-center gap-4">
            <a href="tel:+8801712345678" class="hidden sm:inline-flex items-center gap-1.5 hover:text-sky-400 transition-colors">
              <i class="fa-solid fa-phone text-sky-400 text-xs"></i>
              <span>+৮৮০ ১৭১২-৩৪৫৬৭৮</span>
            </a>
            <a href="mailto:support@sukhimanush.com" class="hidden md:inline-flex items-center gap-1.5 hover:text-sky-400 transition-colors">
              <i class="fa-solid fa-envelope text-sky-400 text-xs"></i>
              <span>support@sukhimanush.com</span>
            </a>
            <span class="inline-flex items-center gap-1.5 text-slate-400 shrink-0">
              <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
              <span class="text-[11px] sm:text-xs whitespace-nowrap">২৪/৭ স্মার্ট স্বাস্থ্যসেবা নেটওয়ার্ক</span>
            </span>
          </div>

          <!-- Right: Auth / Quick Links -->
          <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <!-- Logged In State -->
            <template v-if="currentUser">
              <router-link 
                :to="portalPath" 
                class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm transition-all shrink-0"
                :class="portalBadgeClass"
                style="color: #ffffff !important;"
              >
                <img 
                  v-if="currentUser.avatar" 
                  :src="'/' + currentUser.avatar.replace(/^\/+/, '')" 
                  class="w-4 h-4 rounded-full object-cover border border-white/80 shrink-0" 
                  alt=""
                />
                <i v-else class="fa-solid fa-user-circle text-xs shrink-0"></i>
                <span class="max-w-[90px] sm:max-w-[120px] truncate text-white" style="color: #ffffff !important;">{{ currentUser.name }}</span>
                <span class="text-[9px] sm:text-[10px] px-1.5 py-0.5 rounded-full bg-white/20 text-white font-black uppercase">{{ portalLabel }}</span>
              </router-link>

              <router-link 
                to="/track-appointment" 
                class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-800 hover:bg-slate-700 border border-slate-700 transition"
                style="color: #fcd34d !important;"
              >
                <i class="fa-solid fa-ticket-simple text-amber-400"></i>
                <span style="color: #fcd34d !important;">ট্র্যাক পাস</span>
              </router-link>

              <button 
                type="button" 
                @click="handleLogout" 
                class="p-1 px-2 rounded-full text-slate-300 hover:text-rose-400 hover:bg-slate-800 text-xs transition shrink-0"
                title="লগআউট"
              >
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
              </button>
            </template>

            <!-- Guest State (Hidden on mobile to prevent duplicate buttons with bottom nav) -->
            <template v-else>
              <router-link 
                to="/track-appointment" 
                class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs text-slate-300 hover:text-white bg-slate-800/90 hover:bg-slate-800 transition shrink-0"
              >
                <i class="fa-solid fa-magnifying-glass text-sky-400 text-[11px]"></i>
                <span class="hidden xs:inline">সিরিয়াল</span> ট্র্যাক
              </router-link>
              <router-link 
                to="/login" 
                class="hidden sm:inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white bg-sky-600 hover:bg-sky-500 shadow-xs transition shrink-0"
              >
                <i class="fa-solid fa-right-to-bracket text-[11px]"></i>
                <span>লগইন</span>
              </router-link>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-xs">
      <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-18">
          <!-- Logo Brand -->
          <router-link to="/" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-none shrink-0 py-1">
            <img src="/images/logo/Logo2.png" alt="সুখী মানুষ - Sukhi Manush" class="h-10 sm:h-12 w-auto object-contain group-hover:scale-105 transition-transform" />
          </router-link>

          <!-- Desktop Navigation Links -->
          <div class="hidden lg:flex items-center gap-1 xl:gap-2">
            <router-link 
              to="/" 
              class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
              :class="$route.path === '/' ? 'text-sky-600 bg-sky-50/80 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50'"
            >
              হোম
            </router-link>

            <router-link 
              to="/doctors" 
              class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
              :class="$route.path === '/doctors' ? 'text-sky-600 bg-sky-50/80 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50'"
            >
              ডাক্তার খুঁজুন
            </router-link>

            <!-- AI Symptom Checker Link with Pulse Tag -->
            <router-link 
              to="/ai-assistant" 
              class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors inline-flex items-center gap-1.5"
              :class="$route.path === '/ai-assistant' ? 'text-sky-600 bg-sky-50/80 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50'"
            >
              <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-gradient-to-r from-rose-500 to-amber-500 text-white leading-none shadow-xs">AI</span>
              <span>লক্ষণ পরামর্শক</span>
            </router-link>

            <!-- Health Services Dropdown -->
            <div 
              class="relative" 
              @mouseenter="isServicesDropdownOpen = true" 
              @mouseleave="isServicesDropdownOpen = false"
            >
              <button 
                type="button" 
                class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors inline-flex items-center gap-1.5 text-slate-600 hover:text-sky-600 hover:bg-slate-50"
                @click="isServicesDropdownOpen = !isServicesDropdownOpen"
              >
                <span>স্বাস্থ্য সেবাসমূহ</span>
                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="{ 'rotate-180': isServicesDropdownOpen }"></i>
              </button>

              <!-- Dropdown Menu -->
              <transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 translate-y-1 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 translate-y-1 scale-95"
              >
                <div 
                  v-show="isServicesDropdownOpen" 
                  class="absolute left-0 mt-1 w-72 rounded-2xl bg-white shadow-xl shadow-slate-900/10 border border-slate-100 p-2 z-50"
                >
                  <router-link 
                    to="/prescription-explainer" 
                    class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-emerald-50/70 transition group"
                    @click="isServicesDropdownOpen = false"
                  >
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                      <i class="fa-solid fa-file-prescription text-sm"></i>
                    </div>
                    <div>
                      <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        AI প্রেসক্রিপশন অনুবাদক
                        <span class="text-[9px] bg-emerald-100 text-emerald-700 px-1 rounded-sm font-semibold">নতুন</span>
                      </div>
                      <p class="text-[11px] text-slate-500 mt-0.5">ছবি দিন, সহজ বাংলায় জানুন ঔষধের নিয়ম</p>
                    </div>
                  </router-link>

                  <router-link 
                    to="/ai-assistant" 
                    class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition group"
                    @click="isServicesDropdownOpen = false"
                  >
                    <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                      <i class="fa-solid fa-brain text-sm"></i>
                    </div>
                    <div>
                      <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        AI লক্ষণ পরামর্শক
                        <span class="text-[9px] bg-rose-100 text-rose-700 px-1 rounded-sm font-semibold">স্মার্ট</span>
                      </div>
                      <p class="text-[11px] text-slate-500 mt-0.5">লক্ষণ বিশ্লেষণ ও সঠিক ডাক্তার নির্বাচন</p>
                    </div>
                  </router-link>

                  <router-link 
                    to="/blood-sos" 
                    class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition group"
                    @click="isServicesDropdownOpen = false"
                  >
                    <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                      <i class="fa-solid fa-droplet text-sm"></i>
                    </div>
                    <div>
                      <div class="text-xs font-bold text-slate-800">রক্ত ও প্লাটিলেট SOS</div>
                      <p class="text-[11px] text-slate-500 mt-0.5">জরুরি রক্তের সন্ধান ও ডোনার নেটওয়ার্ক</p>
                    </div>
                  </router-link>

                  <router-link 
                    to="/diagnostic-centers" 
                    class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition group"
                    @click="isServicesDropdownOpen = false"
                  >
                    <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                      <i class="fa-solid fa-microscope text-sm"></i>
                    </div>
                    <div>
                      <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        ডায়াগনস্টিক টেস্ট বুকিং
                        <span class="text-[9px] bg-teal-100 text-teal-700 px-1 rounded-sm font-semibold">১০-১৫% ছাড়</span>
                      </div>
                      <p class="text-[11px] text-slate-500 mt-0.5">ল্যাব টেস্ট, হোম স্যাম্পল ও পার্টনার ল্যাব</p>
                    </div>
                  </router-link>

                  <router-link 
                    to="/cost-estimator" 
                    class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition group"
                    @click="isServicesDropdownOpen = false"
                  >
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                      <i class="fa-solid fa-calculator text-sm"></i>
                    </div>
                    <div>
                      <div class="text-xs font-bold text-slate-800">চিকিৎসা খরচ ক্যালকুলেটর</div>
                      <p class="text-[11px] text-slate-500 mt-0.5">সার্জারি ও ডায়াগনস্টিক পরীক্ষার সম্ভাব্য খরচ</p>
                    </div>
                  </router-link>

                  <router-link 
                    to="/emergency-resources" 
                    class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition group"
                    @click="isServicesDropdownOpen = false"
                  >
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                      <i class="fa-solid fa-truck-medical text-sm"></i>
                    </div>
                    <div>
                      <div class="text-xs font-bold text-slate-800">এম্বুলেন্স ও আইসিইউ বেড</div>
                      <p class="text-[11px] text-slate-500 mt-0.5">২৪/৭ লাইভ হাসপাতাল বেড রাডার</p>
                    </div>
                  </router-link>

                  <router-link 
                    to="/track-appointment" 
                    class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition group"
                    @click="isServicesDropdownOpen = false"
                  >
                    <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                      <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    </div>
                    <div>
                      <div class="text-xs font-bold text-slate-800">লাইভ চেম্বার সিরিয়াল ট্র্যাকার</div>
                      <p class="text-[11px] text-slate-500 mt-0.5">এখন কোন সিরিয়াল চলছে দেখুন</p>
                    </div>
                  </router-link>
                </div>
              </transition>
            </div>

            <router-link 
              to="/about" 
              class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
              :class="$route.path === '/about' ? 'text-sky-600 bg-sky-50/80 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50'"
            >
              আমাদের সম্পর্কে
            </router-link>

            <router-link 
              to="/contact" 
              class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
              :class="$route.path === '/contact' ? 'text-sky-600 bg-sky-50/80 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50'"
            >
              যোগাযোগ
            </router-link>
          </div>

          <!-- Right Action CTAs -->
          <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
            <!-- Emergency SOS Button with Pulse Effect -->
            <router-link 
              to="/emergency" 
              class="relative inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 shadow-md shadow-rose-600/30 transition-all active:scale-95 shrink-0"
            >
              <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
              </span>
              <span class="whitespace-nowrap">জরুরি SOS</span>
            </router-link>

            <!-- Appointment Booking Primary Button -->
            <router-link 
              to="/appoinment" 
              class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-sky-600 hover:bg-sky-500 shadow-md shadow-sky-600/30 transition-all active:scale-95"
              style="color: #ffffff !important; background-color: #0284c7 !important;"
            >
              <i class="fa-regular fa-calendar-check text-white"></i>
              <span class="text-white font-bold" style="color: #ffffff !important;">সিরিয়াল বুকিং</span>
            </router-link>

            <!-- Mobile Hamburger Button -->
            <button 
              type="button" 
              class="lg:hidden p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition focus:outline-none"
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              aria-label="Toggle Navigation"
            >
              <i class="fa-solid fa-bars-staggered text-lg" v-if="!isMobileMenuOpen"></i>
              <i class="fa-solid fa-xmark text-lg" v-else></i>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Slide-Over Drawer Navigation -->
    <transition
      enter-active-class="transition-opacity ease-linear duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="isMobileMenuOpen" 
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs lg:hidden"
        @click="closeMenu"
      ></div>
    </transition>

    <div 
      class="fixed inset-y-0 right-0 z-50 w-full max-w-xs bg-white shadow-2xl flex flex-col justify-between transform transition-transform duration-300 ease-in-out lg:hidden"
      :class="isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full'"
    >
      <!-- Drawer Header -->
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <router-link to="/" class="flex items-center gap-2" @click="closeMenu">
          <img src="/images/logo/Logo2.png" alt="সুখী মানুষ - Sukhi Manush" class="h-9 w-auto object-contain" />
        </router-link>

        <button 
          type="button" 
          @click="closeMenu" 
          class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition"
        >
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <!-- Drawer Scrollable Links -->
      <div class="p-5 space-y-4 overflow-y-auto flex-1">
        <!-- User Status Card if Logged In -->
        <div v-if="currentUser" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70">
          <div class="flex items-center gap-3">
            <img 
              v-if="currentUser.avatar" 
              :src="'/' + currentUser.avatar.replace(/^\/+/, '')" 
              class="w-10 h-10 rounded-full object-cover border-2 border-sky-500" 
              alt=""
            />
            <div v-else class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold">
              {{ currentUser.name ? currentUser.name.charAt(0) : 'U' }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-bold text-slate-800 text-sm truncate">{{ currentUser.name }}</div>
              <div class="text-[11px] text-slate-500 capitalize">{{ currentUser.role || 'Patient' }}</div>
            </div>
          </div>
          <div class="mt-3 flex gap-2">
            <router-link 
              :to="portalPath" 
              class="flex-1 py-1.5 px-3 rounded-lg bg-sky-600 text-white text-xs font-bold text-center" 
              @click="closeMenu"
            >
              ড্যাশবোর্ড
            </router-link>
            <button 
              type="button" 
              @click="handleLogout" 
              class="py-1.5 px-3 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition"
            >
              লগআউট
            </button>
          </div>
        </div>

        <!-- Main Nav Links -->
        <div class="space-y-1">
          <router-link 
            to="/" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-solid fa-house w-5 text-slate-400"></i>
            <span>হোম</span>
          </router-link>

          <router-link 
            to="/doctors" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/doctors' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-solid fa-user-doctor w-5 text-slate-400"></i>
            <span>ডাক্তার খুঁজুন</span>
          </router-link>

          <router-link 
            to="/appoinment" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/appoinment' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-regular fa-calendar-check w-5 text-slate-400"></i>
            <span>চেম্বার সিরিয়াল বুকিং</span>
          </router-link>

          <router-link 
            to="/prescription-explainer" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/prescription-explainer' ? 'bg-emerald-50 text-emerald-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-solid fa-file-prescription w-5 text-emerald-500"></i>
            <span>AI প্রেসক্রিপশন অনুবাদক</span>
            <span class="ml-auto text-[10px] font-bold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">নতুন</span>
          </router-link>

          <router-link 
            to="/ai-assistant" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/ai-assistant' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-solid fa-brain w-5 text-rose-500"></i>
            <span>AI লক্ষণ পরামর্শক</span>
            <span class="ml-auto text-[10px] font-bold bg-rose-100 text-rose-600 px-1.5 py-0.5 rounded-full">স্মার্ট</span>
          </router-link>

          <router-link 
            to="/track-appointment" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/track-appointment' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-solid fa-clock-rotate-left w-5 text-slate-400"></i>
            <span>লাইভ সিরিয়াল ট্র্যাকার</span>
          </router-link>

          <router-link 
            to="/blood-sos" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/blood-sos' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-solid fa-droplet w-5 text-red-500"></i>
            <span>রক্ত ও প্লাটিলেট SOS</span>
          </router-link>

          <router-link 
            to="/cost-estimator" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/cost-estimator' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-solid fa-calculator w-5 text-slate-400"></i>
            <span>চিকিৎসা খরচ হিসাব</span>
          </router-link>

          <router-link 
            to="/diagnostic-centers" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/diagnostic-centers' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-solid fa-microscope w-5 text-teal-600"></i>
            <span>ডায়াগনস্টিক টেস্ট ও ল্যাব</span>
            <span class="ml-auto text-[10px] font-bold bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded-full">১০-১৫% ছাড়</span>
          </router-link>

          <router-link 
            to="/about" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/about' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-regular fa-hospital w-5 text-slate-400"></i>
            <span>আমাদের সম্পর্কে</span>
          </router-link>

          <router-link 
            to="/contact" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
            :class="$route.path === '/contact' ? 'bg-sky-50 text-sky-600 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            @click="closeMenu"
          >
            <i class="fa-regular fa-paper-plane w-5 text-slate-400"></i>
            <span>যোগাযোগ</span>
          </router-link>
        </div>
      </div>

      <!-- Drawer Footer -->
      <div class="p-5 border-t border-slate-100 bg-slate-50 space-y-2">
        <a 
          href="tel:+8801712345678" 
          class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-xs"
        >
          <i class="fa-solid fa-phone"></i>
          <span>হটলাইন: +৮৮০ ১৭১২-৩৪৫৬৭৮</span>
        </a>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'AppHeader',
  data() {
    return {
      isMobileMenuOpen: false,
      isServicesDropdownOpen: false,
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
    portalLabel() {
      if (!this.currentUser) return '';
      if (this.currentUser.role === 'super_admin') return 'Admin';
      if (this.currentUser.role === 'doctor') return 'Doctor';
      if (this.currentUser.role === 'staff') return 'Staff';
      if (this.currentUser.role === 'agent') return 'Agent';
      return 'Portal';
    },
    portalBadgeClass() {
      if (!this.currentUser) return '';
      if (this.currentUser.role === 'super_admin') return 'bg-purple-600 text-white hover:bg-purple-500';
      if (this.currentUser.role === 'doctor') return 'bg-rose-600 text-white hover:bg-rose-500';
      if (this.currentUser.role === 'staff') return 'bg-amber-600 text-white hover:bg-amber-500';
      if (this.currentUser.role === 'agent') return 'bg-emerald-600 text-white hover:bg-emerald-500';
      return 'bg-slate-800 text-white hover:bg-slate-700';
    },
  },
  watch: {
    $route() {
      this.closeMenu();
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
    closeMenu() {
      this.isMobileMenuOpen = false;
      this.isServicesDropdownOpen = false;
    },
    handleLogout() {
      localStorage.removeItem('saas_token');
      localStorage.removeItem('saas_user');
      localStorage.removeItem('saas_doctor');
      this.currentUser = null;
      window.dispatchEvent(new Event('auth-change'));
      this.$router.push('/login');
    },
  },
};
</script>
