<template>
  <footer class="bg-slate-950 text-slate-300 text-sm border-t border-slate-900 pt-16 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 pb-12 border-b border-slate-900">
        <!-- Col 1: Brand & Intro -->
        <div class="space-y-4">
          <router-link to="/" class="inline-flex items-center gap-2.5">
            <img src="/images/logo/Logo2.png" alt="সুখী মানুষ - Sukhi Manush" class="h-12 w-auto object-contain bg-white/95 p-1.5 rounded-xl shadow-md" />
          </router-link>

          <p class="text-xs sm:text-sm text-slate-300 leading-relaxed" style="color: #cbd5e1 !important;">
            সুখী মানুষ একটি সমন্বিত ডিজিটাল স্বাস্থ্যসেবা ও স্মার্ট হাসপাতাল প্ল্যাটফর্ম। ২৪/৭ জরুরি সেবা, ভেরিফায়েড বিশেষজ্ঞ চিকিৎসক, এআই লক্ষণ পরামর্শ ও লাইভ সিরিয়াল ট্র্যাকিংয়ে আমরা নিবেদিত।
          </p>

          <!-- Social Links with High Contrast -->
          <div class="pt-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-3">সোশ্যাল কানেক্ট</span>
            <div class="flex items-center gap-3">
              <a 
                href="https://facebook.com" 
                target="_blank" 
                rel="noopener" 
                class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-sky-600 text-white flex items-center justify-center transition-all shadow-sm border border-slate-700" 
                title="Facebook"
                style="color: #ffffff !important;"
              >
                <i class="fa-brands fa-facebook-f text-sm text-white"></i>
              </a>
              <a 
                href="https://twitter.com" 
                target="_blank" 
                rel="noopener" 
                class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-sky-500 text-white flex items-center justify-center transition-all shadow-sm border border-slate-700" 
                title="Twitter"
                style="color: #ffffff !important;"
              >
                <i class="fa-brands fa-x-twitter text-sm text-white"></i>
              </a>
              <a 
                href="https://linkedin.com" 
                target="_blank" 
                rel="noopener" 
                class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-sky-700 text-white flex items-center justify-center transition-all shadow-sm border border-slate-700" 
                title="LinkedIn"
                style="color: #ffffff !important;"
              >
                <i class="fa-brands fa-linkedin-in text-sm text-white"></i>
              </a>
              <a 
                href="https://youtube.com" 
                target="_blank" 
                rel="noopener" 
                class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-red-600 text-white flex items-center justify-center transition-all shadow-sm border border-slate-700" 
                title="YouTube"
                style="color: #ffffff !important;"
              >
                <i class="fa-brands fa-youtube text-sm text-white"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Col 2: Specialist Departments -->
        <div>
          <h4 class="text-white text-sm font-extrabold uppercase tracking-wider mb-4 flex items-center gap-2" style="color: #ffffff !important;">
            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
            <span>বিশেষজ্ঞ বিভাগ</span>
          </h4>
          <ul class="space-y-3 text-xs sm:text-sm">
            <li>
              <router-link :to="{ path: '/doctors', query: { specialization: 'Cardiology' } }" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>❤️</span> <span>কার্ডিওলজি (হৃদরোগ)</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{ path: '/doctors', query: { specialization: 'Neurology' } }" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>🧠</span> <span>নিউরোলজি (স্নায়ুরোগ)</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{ path: '/doctors', query: { specialization: 'Pediatrics' } }" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>👶</span> <span>পেডিয়াট্রিক্স (শিশু বিভাগ)</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{ path: '/doctors', query: { specialization: 'Orthopedics' } }" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>🦴</span> <span>অর্থোপেডিক্স (হাড় ও জোড়া)</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{ path: '/doctors', query: { specialization: 'Gynecology' } }" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>🤰</span> <span>গাইনী ও প্রসূতি বিভাগ</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{ path: '/doctors', query: { specialization: 'Dental Care' } }" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>🦷</span> <span>ডেন্টাল কেয়ার (দন্ত বিভাগ)</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Col 3: Services & Quick Links -->
        <div>
          <h4 class="text-white text-sm font-extrabold uppercase tracking-wider mb-4 flex items-center gap-2" style="color: #ffffff !important;">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>সেবা ও সহায়িকা</span>
          </h4>
          <ul class="space-y-3 text-xs sm:text-sm">
            <li>
              <router-link to="/about" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>🏥</span> <span>আমাদের সম্পর্কে (About Us)</span>
              </router-link>
            </li>
            <li>
              <router-link to="/services" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>🩺</span> <span>হাসপাতাল ও ডায়াগনস্টিক সেবা</span>
              </router-link>
            </li>
            <li>
              <router-link to="/doctors" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>👨‍⚕️</span> <span>ডাক্তার খুঁজুন ও তালিকা</span>
              </router-link>
            </li>
            <li>
              <router-link to="/appoinment" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>📅</span> <span>চেম্বার সিরিয়াল বুকিং</span>
              </router-link>
            </li>
            <li>
              <router-link to="/track-appointment" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>⏱️</span> <span>লাইভ সিরিয়াল ট্র্যাকিং</span>
              </router-link>
            </li>
            <li>
              <router-link to="/ai-assistant" class="text-slate-300 hover:text-sky-400 hover:translate-x-1 inline-flex items-center gap-1.5 transition-all" style="color: #cbd5e1 !important;">
                <span>🤖</span> <span>এআই লক্ষণ পরামর্শক</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Col 4: Emergency Contacts with High Contrast Cards -->
        <div>
          <h4 class="text-white text-sm font-extrabold uppercase tracking-wider mb-4 flex items-center gap-2" style="color: #ffffff !important;">
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            <span>জরুরি যোগাযোগ</span>
          </h4>

          <div class="space-y-3.5">
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
              <div class="flex items-center gap-2 text-rose-400 text-xs font-bold mb-1.5">
                <i class="fa-solid fa-phone-volume text-sm"></i>
                <span style="color: #fb7185 !important;">২৪/৭ জরুরি হটলাইন</span>
              </div>
              <a 
                href="tel:+8801712345678" 
                class="text-white font-black text-sm sm:text-base hover:text-sky-400 transition-colors block"
                style="color: #ffffff !important;"
              >
                +৮৮০ ১৭১২-৩৪৫৬৭৮
              </a>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
              <div class="flex items-center gap-2 text-sky-400 text-xs font-bold mb-1.5">
                <i class="fa-solid fa-headset text-sm"></i>
                <span style="color: #38bdf8 !important;">কাস্টমার সাপোর্ট ইমেইল</span>
              </div>
              <a 
                href="mailto:support@sukhimanush.com" 
                class="text-sky-300 font-bold text-xs sm:text-sm hover:text-white transition-colors break-all block"
                style="color: #7dd3fc !important;"
              >
                support@sukhimanush.com
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
        <div>
          &copy; {{ currentYear }} <span class="text-white font-bold" style="color: #ffffff !important;">সুখী মানুষ (Sukhi Manush)</span>. সর্বস্বত্ব সংরক্ষিত।
        </div>
        <div class="flex items-center gap-4">
          <router-link to="/privacy-policy" class="text-slate-400 hover:text-white transition-colors">প্রাইভেসি পলিসি</router-link>
          <span>&bull;</span>
          <router-link to="/terms-of-service" class="text-slate-400 hover:text-white transition-colors">টার্মস অব সার্ভিস</router-link>
          <span>&bull;</span>
          <a href="#top" class="text-sky-400 hover:text-sky-300 font-bold transition-colors flex items-center gap-1">
            <span>শীর্ষে যান</span>
            <i class="fa-solid fa-arrow-up text-[10px]"></i>
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter',
  computed: {
    currentYear() {
      return new Date().getFullYear();
    },
  },
};
</script>
