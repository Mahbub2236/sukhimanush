<template>
  <div class="w-full bg-slate-50/50">
    <!-- Hero Banner with Live Search -->
    <section class="relative overflow-hidden bg-gradient-to-b from-sky-50 via-white to-slate-50/50 pt-10 sm:pt-16 pb-16 sm:pb-24 border-b border-slate-100">
      <!-- Background Ambient Glow Shapes -->
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-sky-400/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute top-1/2 -right-24 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
          <!-- Left Text & Search Bar -->
          <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sky-100 text-sky-700 text-xs sm:text-sm font-bold tracking-wide">
              <span class="w-2 h-2 rounded-full bg-sky-500 animate-ping"></span>
              <span>সুখী মানুষ ডিজিটাল স্বাস্থ্যসেবা ও স্মার্ট ডক্টর প্ল্যাটফর্ম</span>
            </div>

            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
              বিশেষজ্ঞ ডাক্তার খুঁজুন ও ঘরে বসেই <span class="bg-gradient-to-r from-sky-600 to-teal-500 bg-clip-text text-transparent">চেম্বার সিরিয়াল</span> নিন
            </h1>

            <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
              বাংলাদেশের শীর্ষ হাসপাতাল ও ডায়াগনস্টিক সেন্টারের ভেরিফায়েড বিশেষজ্ঞ চিকিৎসক খুঁজুন, সরাসরি চেম্বারে নিশ্চিত সিরিয়াল নিন কিংবা ঘরে বসে লাইভ কিউ ট্র্যাক করুন।
            </p>

            <!-- Hero Interactive Search Widget -->
            <div class="p-3 sm:p-4 rounded-2xl bg-white shadow-xl shadow-slate-900/5 border border-slate-200/80 max-w-2xl mx-auto lg:mx-0">
              <form @submit.prevent="executeSearch" class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-3">
                <!-- Search Input -->
                <div class="sm:col-span-7 relative">
                  <i class="fa-solid fa-user-doctor absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                  <input 
                    v-model="searchQuery" 
                    type="text" 
                    placeholder="ডাক্তার, রোগ বা হাসপাতালের নাম..." 
                    class="w-full pl-10 pr-3 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                  />
                </div>

                <!-- Specialization Dropdown -->
                <div class="sm:col-span-5 relative">
                  <i class="fa-solid fa-stethoscope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                  <select 
                    v-model="selectedSpecialization" 
                    class="w-full pl-10 pr-8 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition appearance-none cursor-pointer"
                  >
                    <option value="">সকল বিশেষজ্ঞ বিভাগ ({{ departments.length }}টি বিভাগ)</option>
                    <option 
                      v-for="dept in departments" 
                      :key="dept.key" 
                      :value="dept.specialization"
                    >
                      {{ dept.name }} ({{ dept.subtitle }})
                    </option>
                  </select>
                  <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                </div>

                <!-- Search Button -->
                <div class="sm:col-span-12">
                  <button 
                    type="submit" 
                    class="w-full py-3 px-6 rounded-xl bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-500 hover:to-sky-600 text-white font-bold text-sm shadow-md shadow-sky-600/20 flex items-center justify-center gap-2 active:scale-[0.99] transition-all"
                  >
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>ডাক্তার ও চেম্বার সার্চ করুন</span>
                  </button>
                </div>
              </form>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-3 gap-2 sm:gap-6 pt-4 max-w-lg mx-auto lg:mx-0">
              <div class="p-3 rounded-xl bg-white/60 border border-slate-200/60 text-center lg:text-left">
                <div class="text-xl sm:text-2xl font-black text-slate-900">৩,৩০০+</div>
                <div class="text-[11px] sm:text-xs text-slate-500 font-medium">ভেরিফায়েড ডাক্তার</div>
              </div>
              <div class="p-3 rounded-xl bg-white/60 border border-slate-200/60 text-center lg:text-left">
                <div class="text-xl sm:text-2xl font-black text-slate-900">৫০,০০০+</div>
                <div class="text-[11px] sm:text-xs text-slate-500 font-medium">সিরিয়াল বুকিং</div>
              </div>
              <div class="p-3 rounded-xl bg-white/60 border border-slate-200/60 text-center lg:text-left">
                <div class="text-xl sm:text-2xl font-black text-slate-900">৯৯%</div>
                <div class="text-[11px] sm:text-xs text-slate-500 font-medium">রোগী সন্তুষ্টি</div>
              </div>
            </div>
          </div>

          <!-- Right Hero Illustration / Card Showcase -->
          <div class="lg:col-span-5 relative">
            <div class="relative mx-auto max-w-sm sm:max-w-md">
              <!-- Doctor Presentation Card -->
              <div class="p-6 rounded-3xl bg-white shadow-2xl shadow-sky-900/10 border border-slate-100 relative z-10 space-y-5">
                <div class="flex items-center gap-4">
                  <router-link :to="'/doctor/' + (heroDoctor.slug || heroDoctor.id)" class="relative group shrink-0">
                    <img 
                      :src="getDoctorAvatar(heroDoctor)" 
                      class="w-16 h-16 rounded-2xl object-cover border-2 border-sky-500 shadow-md group-hover:scale-105 transition duration-200" 
                      :alt="heroDoctor.name"
                      @error="onAvatarError($event, heroDoctor)"
                    />
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white ring-2 ring-emerald-100"></span>
                  </router-link>
                  <div class="min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600 bg-sky-50 px-2 py-0.5 rounded-full inline-block mb-1">আজকের চেম্বার খোলা</span>
                    <router-link :to="'/doctor/' + (heroDoctor.slug || heroDoctor.id)" class="block group">
                      <h3 class="text-base font-bold text-slate-900 mt-0.5 group-hover:text-sky-600 transition truncate leading-snug">{{ heroDoctor.name }}</h3>
                    </router-link>
                    <p class="text-xs text-slate-500 truncate" :title="heroDoctor.qualifications || heroDoctor.specialization">
                      {{ heroDoctor.qualifications || heroDoctor.specialization }}
                    </p>
                  </div>
                </div>

                <!-- Live Queue Simulator Badge -->
                <div class="p-3.5 rounded-xl bg-sky-50/80 border border-sky-100 flex items-center justify-between">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center text-xs font-bold animate-pulse">
                      #১২
                    </div>
                    <div>
                      <div class="text-xs font-bold text-slate-800">এখন চলমান সিরিয়াল</div>
                      <div class="text-[11px] text-slate-500 truncate max-w-[180px]">{{ heroDoctorChamberName }}</div>
                    </div>
                  </div>
                  <router-link to="/track-appointment" class="text-xs font-bold text-sky-600 hover:text-sky-700 whitespace-nowrap">
                    ট্র্যাক করুন &rarr;
                  </router-link>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 text-xs text-slate-600">
                  <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock text-slate-400"></i> সময়:</span>
                    <span class="font-bold text-slate-800">{{ heroDoctor.time_slot || 'দুপুর ২:০০ - বিকাল ৪:০০' }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-money-bill-wave text-emerald-500"></i> ভিজিট ফি:</span>
                    <span class="font-bold text-emerald-600">৳ {{ formatFee(heroDoctor.consultation_fee) }}</span>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                  <router-link 
                    :to="'/doctor/' + (heroDoctor.slug || heroDoctor.id)" 
                    class="btn-doctor-card-profile py-2.5 px-3 rounded-xl text-center font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm"
                  >
                    <i class="fa-regular fa-user text-xs"></i>
                    <span>প্রোফাইল</span>
                  </router-link>
                  <router-link 
                    :to="'/appoinment?doctor_id=' + (heroDoctor.id || 2210)" 
                    class="btn-doctor-card-booking py-2.5 px-3 rounded-xl text-center font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm"
                  >
                    <i class="fa-regular fa-calendar-check text-xs"></i>
                    <span>সিরিয়াল নিন</span>
                  </router-link>
                </div>
              </div>

              <!-- Decorative Floating Badges (Positioned cleanly without overlapping buttons) -->
              <div class="absolute -top-3 -right-3 p-2.5 px-3 rounded-2xl bg-white shadow-lg border border-slate-100 flex items-center gap-2 z-20 hidden sm:flex">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                  <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="text-left">
                  <div class="text-[11px] font-bold text-slate-800 leading-tight">BMDC রেজিস্টার্ড</div>
                  <div class="text-[9px] text-slate-400">১০০% ভেরিফায়েড</div>
                </div>
              </div>

              <div class="absolute -bottom-3 -left-3 p-2.5 px-3 rounded-2xl bg-white shadow-lg border border-slate-100 flex items-center gap-2 z-20 hidden sm:flex">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs">
                  <i class="fa-solid fa-robot"></i>
                </div>
                <div class="text-left">
                  <div class="text-[11px] font-bold text-slate-800 leading-tight">AI লক্ষণ ট্রায়ার</div>
                  <div class="text-[9px] text-slate-400">২৪/৭ ফ্রি পরামর্শ</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 3 Core Feature Overlap Cards -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 sm:-mt-10 relative z-20">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <!-- Feature 1 -->
        <div class="p-6 rounded-2xl bg-white shadow-lg shadow-slate-900/5 border border-slate-100 hover:-translate-y-1 transition-all duration-200">
          <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl mb-4">
            <i class="fa-solid fa-user-doctor"></i>
          </div>
          <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">ভেরিফায়েড বিশেষজ্ঞ</span>
          <h3 class="text-lg font-bold text-slate-900 mt-1 mb-2">চেম্বার সিরিয়াল বুকিং</h3>
          <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed">
            ডাক্তারের নির্দিষ্ট চেম্বার ও হাসপাতাল নির্বাচন করুন এবং ঘরে বসেই নিশ্চিত সিরিয়াল টোকেন গ্রহণ করুন।
          </p>
          <router-link to="/doctors" class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 hover:text-sky-700">
            <span>সকল ডাক্তার দেখুন</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </router-link>
        </div>

        <!-- Feature 2: AI Prescription Explainer -->
        <div class="p-6 rounded-2xl bg-white shadow-lg shadow-slate-900/5 border border-slate-100 hover:-translate-y-1 transition-all duration-200 group">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-file-prescription"></i>
          </div>
          <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">এআই মেডিকেল টুল</span>
          <h3 class="text-lg font-bold text-slate-900 mt-1 mb-2">AI বাংলা প্রেসক্রিপশন (Rx)</h3>
          <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed">
            ডাক্তারের প্রেসক্রিপশনের ছবি আপলোড করুন; ঔষধের নাম, খাওয়ার সঠিক নিয়ম, সময় ও ল্যাব টেস্টের কারণ জানুন সহজ বাংলায়।
          </p>
          <router-link to="/prescription-explainer" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700">
            <span>প্রেসক্রিপশন বুঝুন</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </router-link>
        </div>

        <!-- Feature 3 -->
        <div class="p-6 rounded-2xl bg-white shadow-lg shadow-slate-900/5 border border-slate-100 hover:-translate-y-1 transition-all duration-200">
          <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mb-4">
            <i class="fa-solid fa-clock-rotate-left"></i>
          </div>
          <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">রিয়েল-টাইম শিডিউল</span>
          <h3 class="text-lg font-bold text-slate-900 mt-1 mb-2">লাইভ কিউ ট্র্যাকিং</h3>
          <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed">
            চেম্বারে গিয়ে দীর্ঘক্ষণ অপেক্ষা করতে হবে না; অ্যাপ থেকেই দেখুন এখন কোন সিরিয়াল নম্বরটি ডাকা হচ্ছে।
          </p>
          <router-link to="/track-appointment" class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-700">
            <span>লাইভ ট্র্যাক করুন</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Top Medical Specialties Section (Responsive Slider) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-bold mb-2">
            <i class="fa-solid fa-layer-group text-sky-600"></i>
            <span>ডিপার্টমেন্টসমূহ (মোট {{ departments.length }}টি বিভাগ)</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            বিশেষায়িত বিভাগ অনুযায়ী ডাক্তার খুঁজুন
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
            আপনার প্রয়োজনীয় স্বাস্থ্য বিভাগের ওপর ক্লিক করে অভিজ্ঞ চিকিৎসকদের তালিকা দেখুন। স্লাইডার ড্র্যাগ করে বা তীর চিহ্নে ক্লিক করে সকল ডিপার্টমেন্ট ঘুরে দেখুন।
          </p>
        </div>

        <!-- Slider Header Navigation Controls -->
        <div class="flex items-center gap-3 shrink-0">
          <router-link 
            to="/doctors" 
            class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-slate-200 bg-white hover:border-sky-500 hover:text-sky-600 text-xs font-bold text-slate-700 transition-all shadow-xs"
          >
            <span>সকল ডাক্তার দেখুন</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </router-link>

          <div class="flex items-center gap-2">
            <button 
              type="button" 
              class="slider-nav-circle"
              :class="canScrollLeft ? 'active-nav cursor-pointer' : 'disabled-nav cursor-not-allowed'"
              :disabled="!canScrollLeft"
              @click="scrollSlider(-1)"
              title="পূর্ববর্তী বিভাগ"
              aria-label="Previous departments"
            >
              <i class="fa-solid fa-arrow-left text-sm"></i>
            </button>
            <button 
              type="button" 
              class="slider-nav-circle"
              :class="canScrollRight ? 'active-nav cursor-pointer' : 'disabled-nav cursor-not-allowed'"
              :disabled="!canScrollRight"
              @click="scrollSlider(1)"
              title="পরবর্তী বিভাগ"
              aria-label="Next departments"
            >
              <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Slider Carousel Area -->
      <div 
        class="relative group/slider"
        @mouseenter="pauseAutoplay" 
        @mouseleave="resumeAutoplay"
        @touchstart="pauseAutoplay"
        @touchend="resumeAutoplay"
      >

        <!-- Scrollable Track -->
        <div 
          ref="deptSlider"
          class="dept-slider-track flex items-stretch gap-3 sm:gap-4 overflow-x-auto py-3 px-1 scroll-smooth select-none cursor-grab active:cursor-grabbing"
          @scroll="onSliderScroll"
          @mousedown="onMouseDown"
          @mousemove="onMouseMove"
          @mouseup="onMouseUp"
          @mouseleave="onMouseLeave"
        >
          <div 
            v-for="dept in departments" 
            :key="dept.key"
            @click="handleCardClick(dept, $event)"
            class="dept-slider-card flex-shrink-0 w-[165px] sm:w-[185px] md:w-[200px] p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-sky-500 hover:shadow-xl hover:shadow-sky-500/10 text-center flex flex-col items-center justify-between transition-all duration-300 group cursor-pointer"
          >
            <!-- Department Icon Container -->
            <div 
              class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center text-2xl mb-3 transition-transform duration-300 group-hover:scale-110 shadow-xs" 
              :class="dept.bgClass"
            >
              <i :class="dept.icon"></i>
            </div>

            <!-- Name and Subtitle -->
            <div class="w-full min-w-0 flex flex-col items-center">
              <span class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-sky-600 transition-colors text-center leading-snug line-clamp-2 min-h-[36px] sm:min-h-[40px] flex items-center justify-center" :title="dept.name">
                {{ dept.name }}
              </span>
              <span class="text-[10px] sm:text-[11px] text-slate-400 mt-1 block truncate w-full" :title="dept.subtitle">
                {{ dept.subtitle }}
              </span>
            </div>

            <!-- Card Bottom Hover Footer -->
            <div class="mt-3 pt-2 border-t border-slate-100 w-full flex items-center justify-center gap-1 text-[10px] font-semibold text-slate-400 group-hover:text-sky-600 transition-colors">
              <span>ডাক্তার তালিকা</span>
              <i class="fa-solid fa-arrow-right text-[8px] transform group-hover:translate-x-0.5 transition-transform"></i>
            </div>
          </div>
        </div>

        <!-- Slider Bottom Information & Progress Bar -->
        <div class="flex items-center justify-between mt-4 px-1">
          <div class="flex items-center gap-2 text-[11px] text-slate-400">
            <i class="fa-solid fa-arrows-left-right text-[10px]"></i>
            <span>মাউস দিয়ে টেনে অথবা ডানে-বামে তীর চিহ্নে ক্লিক করে দেখুন</span>
          </div>

          <div class="flex items-center gap-2">
            <span class="text-[11px] font-semibold text-slate-500 hidden sm:inline">
              {{ Math.round(scrollProgress * 100) }}%
            </span>
            <div class="w-28 sm:w-44 h-1.5 bg-slate-200/80 rounded-full overflow-hidden">
              <div 
                class="h-full bg-gradient-to-r from-sky-500 to-teal-500 rounded-full transition-all duration-150"
                :style="{ width: Math.max(10, scrollProgress * 100) + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- AI Prescription Explainer Feature Banner Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
      <div class="rounded-3xl bg-gradient-to-br from-emerald-900 via-teal-950 to-slate-900 text-white p-6 sm:p-10 lg:p-12 relative overflow-hidden shadow-2xl border border-emerald-800/40">
        <!-- Background Ambient Glow -->
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          
          <!-- Left Column: Copy & Value Proposition -->
          <div class="lg:col-span-7 space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-400/30">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>✨ বিশেষ ফিচার • এআই বাংলা প্রেসক্রিপশন অনুবাদক</span>
            </div>

            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">
              ডাক্তারের প্রেসক্রিপশন বুঝতে পারছেন না? <br class="hidden sm:inline" />
              <span class="text-emerald-400">ছবি দিন, সহজ বাংলায় জানুন</span> ঔষধের নিয়ম!
            </h2>

            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl">
              ডাক্তারের পেঁচানো হাতের লেখা ও কঠিন মেডিকেল ভাষা নিয়ে দুশ্চিন্তার দিন শেষ। আপনার প্রেসক্রিপশনের ছবি আপলোড করুন—আমাদের এআই তাৎক্ষণিকভাবে প্রতিটি ঔষধের নাম, খাওয়ার সঠিক নিয়ম (১+০+১, খালি পেটে না ভরা পেটে) এবং ল্যাব টেস্টের কারণ বাংলায় বুঝিয়ে দেবে।
            </p>

            <!-- 4 Pillar Feature Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-2 text-xs">
              <div class="p-2.5 rounded-xl bg-white/5 border border-white/10 flex items-center gap-2">
                <i class="fa-solid fa-camera text-emerald-400 text-sm"></i>
                <span class="font-bold text-white text-[11px]">ছবি স্ক্যানার</span>
              </div>
              <div class="p-2.5 rounded-xl bg-white/5 border border-white/10 flex items-center gap-2">
                <i class="fa-solid fa-clock text-amber-400 text-sm"></i>
                <span class="font-bold text-white text-[11px]">খাওয়ার সঠিক নিয়ম</span>
              </div>
              <div class="p-2.5 rounded-xl bg-white/5 border border-white/10 flex items-center gap-2">
                <i class="fa-solid fa-microscope text-teal-400 text-sm"></i>
                <span class="font-bold text-white text-[11px]">টেস্টের কারণ ব্যাখ্যা</span>
              </div>
              <div class="p-2.5 rounded-xl bg-white/5 border border-white/10 flex items-center gap-2">
                <i class="fa-solid fa-volume-high text-sky-400 text-sm"></i>
                <span class="font-bold text-white text-[11px]">মিষ্টি বাংলায় ভয়েস</span>
              </div>
            </div>

            <!-- Action CTAs -->
            <div class="flex flex-wrap items-center gap-3 pt-3">
              <router-link 
                to="/prescription-explainer"
                class="px-6 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/25 transition-all duration-200 active:scale-95 flex items-center gap-2 cursor-pointer"
                style="background-color: #10b981 !important; color: #022c22 !important;"
              >
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>প্রেসক্রিপশন অনুবাদ করুন</span>
                <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
              </router-link>

              <router-link 
                to="/prescription-explainer?sample_id=sample_gastric"
                class="px-5 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer"
              >
                <i class="fa-solid fa-eye text-emerald-400"></i>
                <span>নমুনা প্রেসক্রিপশন দেখুন</span>
              </router-link>
            </div>
          </div>

          <!-- Right Column: Interactive Mock Card Preview -->
          <div class="lg:col-span-5">
            <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 p-4 sm:p-5 shadow-xl text-white space-y-3.5">
              <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <div class="flex items-center gap-2.5">
                  <span class="w-8 h-8 rounded-lg bg-emerald-500 text-slate-950 flex items-center justify-center font-bold text-xs">
                    Rx
                  </span>
                  <div>
                    <div class="text-xs font-bold text-white">সহজ বাংলা প্রেসক্রিপশন কার্ড</div>
                    <div class="text-[10px] text-emerald-300">এআই ডেমো প্রিভিউ</div>
                  </div>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/30 text-emerald-200 border border-emerald-400/30 font-bold">
                  লাইভ প্রিভিউ
                </span>
              </div>

              <!-- Preview Item 1 -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-white">Cap. Seclo 20mg</span>
                  <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold">১ + ০ + ১</span>
                </div>
                <p class="text-[11px] text-slate-300">গ্যাস্ট্রিক ও বুক জ্বালাপোড়া কমানোর জন্য</p>
                <div class="text-[10px] text-amber-300 flex items-center gap-1 font-semibold">
                  <i class="fa-regular fa-clock text-[9px]"></i>
                  <span>খাওয়ার ৩০ মিনিট আগে (খালি পেটে)</span>
                </div>
              </div>

              <!-- Preview Item 2 -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-white">Tab. Napa Extend 665mg</span>
                  <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold">১ + ০ + ১</span>
                </div>
                <p class="text-[11px] text-slate-300">জ্বর ও শরীরের ব্যথা উপশম</p>
                <div class="text-[10px] text-amber-300 flex items-center gap-1 font-semibold">
                  <i class="fa-regular fa-clock text-[9px]"></i>
                  <span>ভরা পেটে খাওয়ার পর</span>
                </div>
              </div>

              <!-- Voice Audio Preview Mini Strip -->
              <div class="p-2.5 rounded-xl bg-emerald-950/60 border border-emerald-500/30 flex items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 text-emerald-200 text-[11px]">
                  <i class="fa-solid fa-volume-high text-emerald-400 animate-pulse"></i>
                  <span>"আপনার সেকলো ক্যাপসুলটি সকালে ও রাতে..."</span>
                </div>
                <router-link 
                  to="/prescription-explainer?sample_id=sample_gastric"
                  class="text-[10px] font-bold text-emerald-300 hover:text-white underline whitespace-nowrap"
                >
                  শুনুন &rarr;
                </router-link>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Featured Doctors Grid -->
    <section class="bg-white py-16 border-y border-slate-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between mb-10 gap-4">
          <div>
            <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">ভেরিফায়েড ডক্টর</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">বিশেষজ্ঞ চিকিৎসক ও চেম্বারসমূহ</h2>
          </div>
          <router-link 
            to="/doctors" 
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 hover:border-sky-600 text-xs sm:text-sm font-bold text-slate-700 hover:text-sky-600 transition"
          >
            <span>সকল ডাক্তার দেখুন</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
          </router-link>
        </div>

        <!-- Doctor Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="doc in featuredDoctors" 
            :key="doc.id"
            class="doctor-feature-card rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-slate-300 transition-all duration-300 flex flex-col justify-between overflow-hidden group"
          >
            <!-- Card Body -->
            <div class="p-4 sm:p-5 space-y-3.5 flex-1">
              <!-- Avatar & Top Header -->
              <div class="flex items-start gap-3.5">
                <router-link :to="'/doctor/' + (doc.slug || doc.id)" class="relative shrink-0 block">
                  <img 
                    :src="getDoctorAvatar(doc)" 
                    :alt="doc.name" 
                    @error="onAvatarError($event, doc)" 
                    class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-100 shadow-sm group-hover:scale-105 transition-transform duration-300"
                    style="width: 64px; height: 64px; object-fit: cover; background: #f8fafc;"
                  />
                  <!-- Online / Verified Badge Indicator -->
                  <span 
                    class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-[8px] text-white shadow-xs"
                    title="BMDC ভেরিফাইড চিকিৎসক"
                  >
                    <i class="fa-solid fa-check"></i>
                  </span>
                </router-link>

                <div class="min-w-0 flex-1">
                  <!-- Specialization Pill -->
                  <div class="mb-1">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-700 bg-sky-50 px-2.5 py-0.5 rounded-full border border-sky-100">
                      <i class="fa-solid fa-stethoscope text-[10px] text-sky-500"></i>
                      <span class="truncate">{{ doc.specialization }}</span>
                    </span>
                  </div>

                  <!-- Doctor Name -->
                  <h3 class="text-base font-extrabold text-slate-900 leading-snug truncate" :title="doc.name">
                    <router-link :to="'/doctor/' + (doc.slug || doc.id)" class="text-slate-900 hover:text-rose-600 transition-colors">
                      {{ doc.name }}
                    </router-link>
                  </h3>

                  <!-- Qualifications & BMDC -->
                  <p class="text-xs text-slate-500 font-medium truncate mt-0.5" :title="doc.qualifications">
                    {{ doc.qualifications || 'বিশেষজ্ঞ চিকিৎসক' }}
                  </p>
                  <p v-if="doc.bmdc_number" class="text-[10px] text-slate-400 font-semibold tracking-wide">
                    BMDC: {{ doc.bmdc_number }}
                  </p>
                </div>
              </div>

              <!-- Chamber & Visit Fee Info Card -->
              <div class="p-3 rounded-xl bg-slate-50/90 border border-slate-200/70 space-y-2 text-xs text-slate-700">
                <!-- Chamber Location -->
                <div class="flex items-start gap-2" :title="doc.chamber_address">
                  <i class="fa-solid fa-location-dot text-rose-500 mt-0.5 shrink-0 text-xs"></i>
                  <div class="min-w-0 flex-1">
                    <span class="font-bold text-slate-800">চেম্বার: </span>
                    <span class="text-slate-600 truncate block">{{ doc.chamber_address || 'ধানমন্ডি, ঢাকা' }}</span>
                  </div>
                </div>

                <!-- Fee & Experience Row -->
                <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60">
                  <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">
                      ৳
                    </span>
                    <span class="text-xs font-semibold text-slate-600">
                      ফি: <strong class="text-emerald-700 font-extrabold text-sm">৳{{ doc.consultation_fee }}</strong>
                    </span>
                  </div>

                  <span class="text-[11px] font-medium text-slate-600 bg-white px-2 py-0.5 rounded-md border border-slate-200/80 shadow-2xs">
                    {{ doc.experience_years || '5' }}+ বছর অভিজ্ঞতা
                  </span>
                </div>

                <!-- Telemedicine / Free Clinic Badges (If Available) -->
                <div v-if="doc.telemedicine_link || doc.free_consultation_enabled" class="flex flex-wrap gap-1.5 pt-1">
                  <span v-if="doc.telemedicine_link" class="inline-flex items-center gap-1 text-[10px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200">
                    <i class="fa-solid fa-video text-sky-600"></i> অনলাইন ভিডিও কনসাল্ট
                  </span>
                  <span v-if="doc.free_consultation_enabled" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    <i class="fa-solid fa-gift text-emerald-600"></i> ফ্রি কনসালটেশন
                  </span>
                </div>
              </div>

              <!-- Quick Connect Buttons (WhatsApp, Messenger, Phone) -->
              <div class="flex items-center justify-between px-1 pt-0.5">
                <span class="text-[11px] font-bold text-slate-400">সরাসরি যোগাযোগ:</span>
                <div class="flex items-center gap-2">
                  <button 
                    type="button" 
                    class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition flex items-center justify-center text-xs shadow-2xs"
                    title="হোয়াটসঅ্যাপে চ্যাট করুন" 
                    @click="openWhatsApp(doc)"
                  >
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                  </button>
                  <button 
                    type="button" 
                    class="w-7 h-7 rounded-full bg-sky-50 text-sky-600 hover:bg-sky-600 hover:text-white transition flex items-center justify-center text-xs shadow-2xs"
                    title="মেসেঞ্জারে শেয়ার করুন" 
                    @click="shareMessenger(doc)"
                  >
                    <i class="fa-brands fa-facebook-messenger text-xs"></i>
                  </button>
                  <a 
                    v-if="doc.phone" 
                    :href="'tel:' + doc.phone" 
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition flex items-center justify-center text-xs shadow-2xs"
                    :title="'সরাসরি কল: ' + doc.phone"
                  >
                    <i class="fa-solid fa-phone text-xs"></i>
                  </a>
                </div>
              </div>
            </div>

            <!-- Card Actions Footer (High-Contrast Buttons) -->
            <div class="p-3.5 bg-slate-50/90 border-t border-slate-100 grid grid-cols-2 gap-2">
              <router-link 
                :to="'/doctor/' + (doc.slug || doc.id)" 
                class="btn-doctor-card-profile py-2.5 px-3 rounded-xl text-center font-bold text-xs transition-all flex items-center justify-center gap-1.5"
              >
                <i class="fa-regular fa-user text-xs"></i>
                <span>প্রোফাইল</span>
              </router-link>
              <router-link 
                :to="'/appoinment?doctor_id=' + doc.id" 
                class="btn-doctor-card-booking py-2.5 px-3 rounded-xl text-center font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5"
              >
                <i class="fa-regular fa-calendar-check text-xs"></i>
                <span>সিরিয়াল নিন</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Doctor Registration CTA Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-sky-950 text-white p-8 sm:p-12 lg:p-16 relative overflow-hidden shadow-2xl border border-slate-700/60">
        <div class="relative z-10 max-w-2xl space-y-5">
          <span 
            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-rose-500/20 text-rose-300 text-xs font-bold border border-rose-400/30 uppercase tracking-wider"
            style="color: #fca5a5 !important;"
          >
            <i class="fa-solid fa-user-doctor"></i>
            <span>ডাক্তার ও চেম্বারদের জন্য</span>
          </span>

          <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight" style="color: #ffffff !important;">
            <span class="block text-white" style="color: #ffffff !important;">আপনি কি একজন চিকিৎসক?</span>
            <span class="block text-sky-400 mt-1" style="color: #38bdf8 !important;">আজই আপনার চেম্বারকে স্মার্ট ও ডিজিটাল করুন!</span>
          </h2>

          <p class="text-xs sm:text-sm text-slate-200 leading-relaxed max-w-xl" style="color: #e2e8f0 !important;">
            রোগীদের দীর্ঘ লাইনের ঝামেলা দূর করুন। সহকারী পিন লগইন, লাইভ সিরিয়াল ডিসপ্লে, ডিজিটাল প্রেসক্রিপশন এবং রোগীদের সঠিক ট্র্যাকিং নিশ্চিত করুন মাত্র ১ ক্লিকে।
          </p>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs">
            <div class="flex items-center gap-2 text-white font-medium" style="color: #ffffff !important;">
              <i class="fa-solid fa-circle-check text-emerald-400 shrink-0"></i>
              <span>স্মার্ট কিউ ট্র্যাকার</span>
            </div>
            <div class="flex items-center gap-2 text-white font-medium" style="color: #ffffff !important;">
              <i class="fa-solid fa-circle-check text-emerald-400 shrink-0"></i>
              <span>ডিজিটাল প্রেসক্রিপশন</span>
            </div>
            <div class="flex items-center gap-2 text-white font-medium" style="color: #ffffff !important;">
              <i class="fa-solid fa-circle-check text-emerald-400 shrink-0"></i>
              <span>চেম্বার সহকারী পিন</span>
            </div>
            <div class="flex items-center gap-2 text-white font-medium" style="color: #ffffff !important;">
              <i class="fa-solid fa-circle-check text-emerald-400 shrink-0"></i>
              <span>সরাসরি পেমেন্ট পেআউট</span>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-3.5 pt-4">
            <router-link 
              to="/register" 
              class="px-6 py-3.5 rounded-xl bg-sky-400 hover:bg-sky-300 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-sky-400/25 transition active:scale-95 flex items-center gap-2"
              style="color: #020617 !important; background-color: #38bdf8 !important;"
            >
              <span>ডাক্তার হিসেবে যোগ দিন</span>
              <i class="fa-solid fa-arrow-right text-xs"></i>
            </router-link>

            <router-link 
              to="/login" 
              class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 border-2 border-slate-600 hover:border-slate-400 text-white font-bold text-xs sm:text-sm transition flex items-center gap-2 shadow-sm"
              style="color: #ffffff !important;"
            >
              <i class="fa-solid fa-right-to-bracket text-sky-400"></i>
              <span style="color: #ffffff !important;">ডাক্তার লগইন</span>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Hospital Partners Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
      <div class="text-center max-w-xl mx-auto mb-8">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">পার্টনার নেটওয়ার্ক</span>
        <h3 class="text-xl sm:text-2xl font-bold text-slate-800 mt-1">সহযোগী হাসপাতাল ও ডায়াগনস্টিক সেন্টার</h3>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <router-link 
          v-for="(partner, idx) in hospitalPartners" 
          :key="idx"
          :to="{ path: '/doctors', query: partner.query ? { search: partner.query } : {} }" 
          class="p-4 rounded-xl bg-white border border-slate-200/80 hover:border-sky-500 hover:shadow-md transition text-center flex flex-col items-center justify-between"
        >
          <div class="h-10 flex items-center justify-center mb-2">
            <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-sm font-black">
              {{ partner.shortName.charAt(0) }}
            </span>
          </div>
          <div class="text-xs font-bold text-slate-800 truncate w-full">{{ partner.shortName }}</div>
          <span class="text-[10px] text-slate-400 mt-0.5">{{ partner.tag }}</span>
        </router-link>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Home',
  data() {
    return {
      searchQuery: '',
      selectedSpecialization: '',
      heroDoctor: {
        id: 2210,
        name: 'সহকারী অধ্যাপক ডাঃ মোঃ মোশারফ হোসেন',
        slug: 'asst-prof-dr-md-mosharof-hossain-ibnsina-3165',
        qualifications: 'MBBS (DU), BCS (Health) MD (Paediatrics) BMU',
        specialization: 'শিশু বিশেষজ্ঞ (General Medicine)',
        avatar: 'https://www.ibnsinatrust.com/upload/Mosharofpd1768627355.jpg',
        chamber_address: 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, কান্দিরপাড়, কুমিল্লা',
        consultation_fee: 1200,
        time_slot: 'দুপুর ২:০০ - বিকাল ৪:০০',
      },
      featuredDoctors: [],
      testimonials: [],
      stats: {},
      // Slider Reactive States
      canScrollLeft: false,
      canScrollRight: true,
      scrollProgress: 0,
      isMouseDown: false,
      startX: 0,
      scrollLeftStart: 0,
      hasDragged: false,
      autoplayInterval: null,
      departments: [
        { key: 'cardiology', specialization: 'cardiology', name: 'কার্ডিওলজি', subtitle: 'হৃদরোগ ও রক্তচাপ', icon: 'fa-solid fa-heart-pulse', bgClass: 'bg-rose-50 text-rose-600' },
        { key: 'medicine', specialization: 'medicine', name: 'মেডিসিন', subtitle: 'সাধারণ ও ইন্টারনাল', icon: 'fa-solid fa-capsules', bgClass: 'bg-sky-50 text-sky-600' },
        { key: 'pediatrics', specialization: 'pediatrics', name: 'পেডিয়াট্রিক্স', subtitle: 'শিশু ও নবজাতক রোগ', icon: 'fa-solid fa-baby', bgClass: 'bg-amber-50 text-amber-600' },
        { key: 'gynecology', specialization: 'gynecology', name: 'গাইনী ও প্রসূতি', subtitle: 'মহিলা ও প্রসূতিরোগ', icon: 'fa-solid fa-person-breastfeeding', bgClass: 'bg-purple-50 text-purple-600' },
        { key: 'orthopedics', specialization: 'orthopedics', name: 'অর্থোপেডিক্স', subtitle: 'হাড়, জোড়া ও বাতব্যথা', icon: 'fa-solid fa-bone', bgClass: 'bg-emerald-50 text-emerald-600' },
        { key: 'neurology', specialization: 'neurology', name: 'নিউরোলজি', subtitle: 'মস্তিষ্ক ও স্নায়ুরোগ', icon: 'fa-solid fa-brain', bgClass: 'bg-indigo-50 text-indigo-600' },
        { key: 'surgery', specialization: 'surgery', name: 'জেনারেল সার্জারি', subtitle: 'সাধারণ ও ল্যাপারোস্কপিক', icon: 'fa-solid fa-syringe', bgClass: 'bg-blue-50 text-blue-600' },
        { key: 'urology', specialization: 'urology', name: 'ইউরোলজি ও কিডনি', subtitle: 'মূত্রনালী ও কিডনি সার্জারি', icon: 'fa-solid fa-droplet', bgClass: 'bg-cyan-50 text-cyan-600' },
        { key: 'ent', specialization: 'ent', name: 'নাক কান গলা (ইএনটি)', subtitle: 'নাক, কান ও কণ্ঠনালী', icon: 'fa-solid fa-head-side-cough', bgClass: 'bg-teal-50 text-teal-600' },
        { key: 'dermatology', specialization: 'dermatology', name: 'চর্ম ও যৌন রোগ', subtitle: 'ত্বক, চুল ও অ্যালার্জি', icon: 'fa-solid fa-hand-dots', bgClass: 'bg-pink-50 text-pink-600' },
        { key: 'gastroenterology', specialization: 'gastroenterology', name: 'গ্যাস্ট্রোএন্টারোলজি', subtitle: 'পরিপাকতন্ত্র ও লিভার', icon: 'fa-solid fa-shield-virus', bgClass: 'bg-yellow-50 text-yellow-600' },
        { key: 'endocrinology', specialization: 'endocrinology', name: 'ডায়াবেটিস ও হরমোন', subtitle: 'ডায়াবেটিস ও থাইরয়েড', icon: 'fa-solid fa-cubes-stacked', bgClass: 'bg-red-50 text-red-600' },
        { key: 'pulmonology', specialization: 'pulmonology', name: 'বক্ষব্যাধি ও ফুসফুস', subtitle: 'হাঁপানি, টিবি ও শ্বাসকষ্ট', icon: 'fa-solid fa-lungs', bgClass: 'bg-sky-50 text-sky-700' },
        { key: 'ophthalmology', specialization: 'ophthalmology', name: 'চক্ষু বিশেষজ্ঞ', subtitle: 'চোখ ও দৃষ্টি শক্তি', icon: 'fa-solid fa-eye', bgClass: 'bg-blue-50 text-blue-700' },
        { key: 'dental', specialization: 'dental', name: 'ডেন্টাল ও মুখরোগ', subtitle: 'দাঁত ও মাড়ির চিকিৎসা', icon: 'fa-solid fa-tooth', bgClass: 'bg-teal-50 text-teal-700' },
        { key: 'psychiatry', specialization: 'psychiatry', name: 'মানসিক রোগ ও মনোরোগ', subtitle: 'মনোরোগ ও সাইকিয়াট্রি', icon: 'fa-solid fa-head-side-virus', bgClass: 'bg-violet-50 text-violet-600' },
        { key: 'nephrology', specialization: 'nephrology', name: 'কিডনি ও নেফ্রোলজি', subtitle: 'কিডনি রোগ ও ডায়ালাইসিস', icon: 'fa-solid fa-filter', bgClass: 'bg-emerald-50 text-emerald-700' },
        { key: 'oncology', specialization: 'oncology', name: 'ক্যান্সার বিশেষজ্ঞ', subtitle: 'টিউমার ও ক্যান্সার কেয়ার', icon: 'fa-solid fa-ribbon', bgClass: 'bg-rose-50 text-rose-700' },
        { key: 'neurosurgery', specialization: 'neurosurgery', name: 'নিউরোসার্জারি', subtitle: 'ব্রেন ও মেরুদণ্ড সার্জারি', icon: 'fa-solid fa-brain', bgClass: 'bg-slate-100 text-slate-700' },
        { key: 'cardiac_surgery', specialization: 'cardiac_surgery', name: 'কার্ডিয়াক সার্জারি', subtitle: 'হার্ট বাইপাস সার্জারি', icon: 'fa-solid fa-heart', bgClass: 'bg-red-50 text-red-700' },
        { key: 'rheumatology', specialization: 'rheumatology', name: 'বাত ও রিউম্যাটোলজি', subtitle: 'বাতব্যথা ও আর্থ্রাইটিস', icon: 'fa-solid fa-bone', bgClass: 'bg-amber-50 text-amber-700' },
        { key: 'hematology', specialization: 'hematology', name: 'রক্তরোগ (হেমাটোলজি)', subtitle: 'রক্তস্বল্পতা ও থ্যালাসেমিয়া', icon: 'fa-solid fa-vial', bgClass: 'bg-red-50 text-red-600' },
        { key: 'physiotherapy', specialization: 'physiotherapy', name: 'ফিজিওথেরাপি ও রিহ্যাব', subtitle: 'প্যারালাইসিস ও থেরাপি', icon: 'fa-solid fa-person-walking', bgClass: 'bg-green-50 text-green-700' },
        { key: 'pediatric_surgery', specialization: 'pediatric_surgery', name: 'শিশু সার্জারি', subtitle: 'শিশুদের অস্ত্রোপচার', icon: 'fa-solid fa-baby-carriage', bgClass: 'bg-orange-50 text-orange-600' },
        { key: 'plastic_surgery', specialization: 'plastic_surgery', name: 'প্লাস্টিক ও কসমেটিক', subtitle: 'পোড়া ক্ষত ও রিকনস্ট্রাকশন', icon: 'fa-solid fa-wand-magic-sparkles', bgClass: 'bg-fuchsia-50 text-fuchsia-600' },
        { key: 'nutrition', specialization: 'nutrition', name: 'পুষ্টিবিদ ও ডায়েট', subtitle: 'খাদ্যতালিকা ও ডায়েট কেয়ার', icon: 'fa-solid fa-apple-whole', bgClass: 'bg-lime-50 text-lime-700' },
        { key: 'radiology', specialization: 'radiology', name: 'রেডিওলজি ও ইমেজিং', subtitle: 'এক্স-রে, সিটি ও এমআরআই', icon: 'fa-solid fa-x-ray', bgClass: 'bg-cyan-50 text-cyan-700' },
        { key: 'infertility', specialization: 'gynecology', name: 'ইনফার্টিলিটি ও আইভিএফ', subtitle: 'বন্ধ্যাত্ব ও টেস্টটিউব কেয়ার', icon: 'fa-solid fa-seedling', bgClass: 'bg-rose-50 text-rose-500' },
      ],
      hospitalPartners: [
        { name: 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড ইমেজিং সেন্টার', shortName: 'ইবনে সিনা', query: 'ইবনে সিনা', tag: 'স্পেশালাইজড সেন্টার' },
        { name: 'পপুলার ডায়াগনস্টিক সেন্টার', shortName: 'পপুলার ডায়াগনস্টিক', query: 'পপুলার', tag: 'দেশব্যাপী শাখা' },
        { name: 'চেক আপ ডায়াগনস্টিক ও হাসপাতাল', shortName: 'চেক আপ হাসপাতাল', query: 'চেক আপ', tag: 'উত্তরবঙ্গ হাব' },
        { name: 'জিয়া হার্ট ফাউন্ডেশন হাসপাতাল', shortName: 'জিয়া হার্ট ফাউন্ডেশন', query: 'জিয়া হার্ট', tag: 'কার্ডিয়াক কেয়ার' },
        { name: 'শহীদ আসাদুল্লাহ্ আইডিয়াল হসপিটাল', shortName: 'শহীদ আসাদুল্লাহ্ হসপিটাল', query: 'শহীদ আসাদুল্লাহ্', tag: 'মা ও শিশু কেয়ার' },
        { name: 'সুখী মানুষ ডিজিটাল হেলথ পার্টনার্স', shortName: 'সুখী মানুষ হেলথকেয়ার', query: '', tag: 'টেলিমেডিসিন হাব' },
      ],
    };
  },
  computed: {
    heroDoctorChamberName() {
      if (!this.heroDoctor) return 'ইবনে সিনা ডায়াগনস্টিক, কুমিল্লা';
      if (this.heroDoctor.chambers && this.heroDoctor.chambers.length > 0) {
        const primary = this.heroDoctor.chambers.find(c => c.is_primary) || this.heroDoctor.chambers[0];
        return primary.chamber_name ? `${primary.chamber_name}, ${primary.branch_name || primary.district || ''}` : primary.address;
      }
      return this.heroDoctor.chamber_address || 'ইবনে সিনা ডায়াগনস্টিক, কুমিল্লা';
    },
  },
  async mounted() {
    try {
      const res = await axios.get('/home-data');
      if (res.data.hero_doctor) {
        this.heroDoctor = {
          ...res.data.hero_doctor,
          time_slot: this.formatTimeSlot(res.data.hero_doctor),
        };
      }
      this.featuredDoctors = res.data.featured_doctors || [];
      this.testimonials = res.data.testimonials || [];
      this.stats = res.data.stats || {};
    } catch (err) {
      console.error('Error fetching home data:', err);
    }

    this.$nextTick(() => {
      this.updateScrollButtons();
      this.startAutoplay();
    });
  },
  beforeUnmount() {
    this.stopAutoplay();
  },
  methods: {
    toBengaliNumber(val) {
      if (!val && val !== 0) return '';
      const bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
      return String(val).replace(/\d/g, (d) => bengaliDigits[Number(d)]);
    },
    formatFee(fee) {
      if (!fee) return '১,২০০';
      const num = parseInt(fee, 10);
      return this.toBengaliNumber(num.toLocaleString('en-US'));
    },
    formatTimeSlot(doc) {
      if (!doc) return 'দুপুর ২:০০ - বিকাল ৪:০০';
      if (doc.start_time && doc.end_time) {
        return 'দুপুর ২:০০ - বিকাল ৪:০০';
      }
      return 'দুপুর ২:০০ - বিকাল ৪:০০';
    },
    executeSearch() {
      const query = {};
      if (this.searchQuery && this.searchQuery.trim()) {
        query.search = this.searchQuery.trim();
      }
      if (this.selectedSpecialization) {
        query.specialization = this.selectedSpecialization;
      }
      this.$router.push({ path: '/doctors', query });
    },
    getDoctorAvatar(doctor) {
      if (!doctor) return '/images/team/default-doctor-male.svg';
      if (doctor.avatar_url) return doctor.avatar_url;
      if (doctor.avatar) {
        const av = doctor.avatar.trim();
        if (av.startsWith('http://') || av.startsWith('https://')) return av;
        return '/' + av.replace(/^\/+/, '');
      }
      return doctor.gender === 'female' 
        ? '/images/team/default-doctor-female.svg' 
        : '/images/team/default-doctor-male.svg';
    },
    onAvatarError(event, doctor) {
      event.target.onerror = null;
      event.target.src = (doctor && doctor.gender === 'female')
        ? '/images/team/default-doctor-female.svg'
        : '/images/team/default-doctor-male.svg';
    },
    openWhatsApp(doctor) {
      const baseUrl = window.location.origin;
      const shareUrl = `${baseUrl}/appoinment?doctor_id=${doctor.id}`;
      const text = encodeURIComponent(`ডাঃ ${doctor.name} - ${doctor.specialization} (${doctor.chamber_address})\nসিরিয়াল ও বুকিং নিন: ${shareUrl}`);
      if (doctor.phone) {
        let cleanPhone = doctor.phone.replace(/[^\d]/g, '');
        if (cleanPhone.startsWith('0')) cleanPhone = '88' + cleanPhone;
        window.open(`https://api.whatsapp.com/send?phone=${cleanPhone}&text=${text}`, '_blank');
      } else {
        window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
      }
    },
    shareMessenger(doctor) {
      const baseUrl = window.location.origin;
      const shareUrl = encodeURIComponent(`${baseUrl}/appoinment?doctor_id=${doctor.id}`);
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`, '_blank');
    },
    // Slider Navigation & Drag Interaction Methods
    scrollSlider(direction) {
      const slider = this.$refs.deptSlider;
      if (!slider) return;
      const scrollAmount = Math.max(slider.clientWidth * 0.75, 240);
      slider.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth',
      });
      setTimeout(() => this.updateScrollButtons(), 350);
    },
    onSliderScroll() {
      this.updateScrollButtons();
    },
    updateScrollButtons() {
      const slider = this.$refs.deptSlider;
      if (!slider) return;
      this.canScrollLeft = slider.scrollLeft > 10;
      const maxScroll = slider.scrollWidth - slider.clientWidth;
      this.canScrollRight = slider.scrollLeft < maxScroll - 10;
      this.scrollProgress = maxScroll > 0 ? (slider.scrollLeft / maxScroll) : 0;
    },
    onMouseDown(e) {
      const slider = this.$refs.deptSlider;
      if (!slider) return;
      this.isMouseDown = true;
      this.hasDragged = false;
      this.startX = e.pageX - slider.offsetLeft;
      this.scrollLeftStart = slider.scrollLeft;
      this.pauseAutoplay();
    },
    onMouseMove(e) {
      if (!this.isMouseDown) return;
      const slider = this.$refs.deptSlider;
      if (!slider) return;
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - this.startX) * 1.3;
      if (Math.abs(walk) > 6) {
        this.hasDragged = true;
      }
      slider.scrollLeft = this.scrollLeftStart - walk;
    },
    onMouseUp() {
      this.isMouseDown = false;
      setTimeout(() => {
        this.hasDragged = false;
      }, 50);
    },
    onMouseLeave() {
      this.isMouseDown = false;
      this.hasDragged = false;
      this.resumeAutoplay();
    },
    handleCardClick(dept, event) {
      if (this.hasDragged) {
        if (event) event.preventDefault();
        return;
      }
      this.$router.push({
        path: '/doctors',
        query: { specialization: dept.specialization },
      });
    },
    startAutoplay() {
      this.stopAutoplay();
      this.autoplayInterval = setInterval(() => {
        const slider = this.$refs.deptSlider;
        if (!slider) return;
        const maxScroll = slider.scrollWidth - slider.clientWidth;
        if (slider.scrollLeft >= maxScroll - 10) {
          slider.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          slider.scrollBy({ left: 200, behavior: 'smooth' });
        }
      }, 4000);
    },
    pauseAutoplay() {
      this.stopAutoplay();
    },
    resumeAutoplay() {
      this.startAutoplay();
    },
    stopAutoplay() {
      if (this.autoplayInterval) {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
      }
    },
  },
};
</script>

<style scoped>
.doctor-feature-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.doctor-feature-card:hover {
  transform: translateY(-4px);
}
.btn-doctor-card-profile {
  background: #ffffff !important;
  color: #334155 !important;
  border: 1.5px solid #cbd5e1 !important;
  text-decoration: none !important;
}
.btn-doctor-card-profile:hover {
  background: #f8fafc !important;
  border-color: #223a66 !important;
  color: #223a66 !important;
}
.btn-doctor-card-booking {
  background: linear-gradient(135deg, #e12454 0%, #c91b45 100%) !important;
  color: #ffffff !important;
  border: none !important;
  text-decoration: none !important;
  box-shadow: 0 3px 10px rgba(225, 36, 84, 0.25) !important;
}
.btn-doctor-card-booking:hover {
  background: linear-gradient(135deg, #c91b45 0%, #a81032 100%) !important;
  color: #ffffff !important;
  transform: translateY(-1px);
  box-shadow: 0 5px 14px rgba(225, 36, 84, 0.35) !important;
}

/* Department Slider Navigation Circular Buttons */
.slider-nav-circle {
  width: 38px !important;
  height: 38px !important;
  border-radius: 50% !important;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  border: 1.5px solid #cbd5e1 !important;
  outline: none !important;
  transition: all 0.2s ease !important;
}
.slider-nav-circle.active-nav {
  background: #ffffff !important;
  color: #334155 !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
}
.slider-nav-circle.active-nav:hover {
  background: #0284c7 !important;
  color: #ffffff !important;
  border-color: #0284c7 !important;
  box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25) !important;
  transform: scale(1.05);
}
.slider-nav-circle.disabled-nav {
  background: #f8fafc !important;
  color: #cbd5e1 !important;
  border-color: #e2e8f0 !important;
  opacity: 0.6 !important;
}

/* Department Slider Track & Card Styles */
.dept-slider-track::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}
.dept-slider-track {
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
  scroll-snap-type: x proximity;
  -webkit-overflow-scrolling: touch;
}
.dept-slider-card {
  scroll-snap-align: start;
  user-select: none;
}
.dept-slider-card:active {
  cursor: grabbing !important;
}
</style>
