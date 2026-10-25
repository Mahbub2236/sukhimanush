<template>
  <div class="w-full bg-slate-50/50 min-h-screen">
    <!-- Header Banner -->
    <section class="bg-gradient-to-b from-sky-50 via-white to-slate-50 py-12 sm:py-16 border-b border-slate-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-bold uppercase tracking-wider mb-3">
          সার্বক্ষণিক সহায়তা ও সাপোর্ট
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
          যোগাযোগ করুন <span class="text-sky-600">(Contact Us)</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto mt-3 leading-relaxed">
          যেকোনো প্রশ্ন, সিরিয়াল সংক্রান্ত জটিলতা বা কারিগরি সহায়তার জন্য আমাদের মেডিকেল কো-অর্ডিনেটর টিমের সাথে যোগাযোগ করুন।
        </p>
      </div>
    </section>

    <!-- 3 Info Cards Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 sm:-mt-8 relative z-20">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Phone Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-lg shadow-slate-900/5 text-center flex flex-col items-center justify-center space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-phone-volume"></i>
          </div>
          <h3 class="text-base font-bold text-slate-900">জরুরি কল ও হেল্পলাইন</h3>
          <p class="text-xs text-slate-500 leading-relaxed">সকাল ৮:৩০ থেকে রাত ১০:০০ পর্যন্ত সরাসরি কথা বলুন।</p>
          <a href="tel:+8801712345678" class="text-sm font-extrabold text-emerald-600 hover:text-emerald-700">
            +৮৮০ ১৭১২-৩৪৫৬৭৮
          </a>
        </div>

        <!-- Email Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-lg shadow-slate-900/5 text-center flex flex-col items-center justify-center space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <h3 class="text-base font-bold text-slate-900">ইমেইল সহায়তা</h3>
          <p class="text-xs text-slate-500 leading-relaxed">যেকোনো ফিডব্যাক বা অনুসন্ধানের জন্য ইমেইল করুন।</p>
          <a href="mailto:support@sukhimanush.com" class="text-sm font-extrabold text-sky-600 hover:text-sky-700">
            support@sukhimanush.com
          </a>
        </div>

        <!-- Location Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-lg shadow-slate-900/5 text-center flex flex-col items-center justify-center space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <h3 class="text-base font-bold text-slate-900">হেড অফিস ও সেন্ট্রাল হাব</h3>
          <p class="text-xs text-slate-500 leading-relaxed">ধানমন্ডি মেডিকেল জোন, রোড ৮/এ, ঢাকা ১২০৯</p>
          <span class="text-xs font-semibold text-rose-600">ঢাকা, বাংলাদেশ</span>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="p-8 sm:p-10 rounded-3xl bg-white border border-slate-200 shadow-xl space-y-6">
        <div class="text-center max-w-lg mx-auto mb-6">
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">সরাসরি বার্তা পাঠান</h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            আপনার নাম, ফোন এবং বার্তা লিখে সাবমিট করুন। আমাদের প্রতিনিধি দ্রুত আপনার সাথে যোগাযোগ করবেন।
          </p>
        </div>

        <form @submit.prevent="submitContact" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Name -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700">
                আপনার পুরো নাম <span class="text-rose-500">*</span>
              </label>
              <div class="relative flex items-center">
                <input 
                  type="text" 
                  v-model="form.name" 
                  data-no-voice
                  class="w-full pl-3.5 pr-11 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                  placeholder="যেমনঃ মোঃ সাইফুল ইসলাম" 
                  required
                />
                <VoiceInputBtn 
                  embedded 
                  title="পুরো নাম মুখে বলুন"
                  @result="val => form.name = val" 
                />
              </div>
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700">
                ইমেইল ঠিকানা <span class="text-rose-500">*</span>
              </label>
              <div class="relative flex items-center">
                <input 
                  type="email" 
                  v-model="form.email" 
                  data-no-voice
                  class="w-full pl-3.5 pr-11 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                  placeholder="email@example.com" 
                  required
                />
                <VoiceInputBtn 
                  embedded 
                  asEmail
                  title="ইমেইল মুখে বলুন (যেমনঃ name@gmail.com)"
                  @result="val => form.email = val" 
                />
              </div>
            </div>

            <!-- Phone -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700">
                ফোন নম্বর
              </label>
              <div class="relative flex items-center">
                <input 
                  type="tel" 
                  v-model="form.phone" 
                  data-no-voice
                  class="w-full pl-3.5 pr-11 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                  placeholder="০১৭১২-৩৪৫৬৭৮"
                />
                <VoiceInputBtn 
                  embedded 
                  asDigits 
                  title="ফোন নম্বর মুখে বলুন"
                  @result="val => form.phone = val" 
                />
              </div>
            </div>

            <!-- Subject -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700">
                বিষয় / বিষয়বস্তু <span class="text-rose-500">*</span>
              </label>
              <div class="relative flex items-center">
                <input 
                  type="text" 
                  v-model="form.subject" 
                  data-no-voice
                  class="w-full pl-3.5 pr-11 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                  placeholder="যেমনঃ সিরিয়াল পরিবর্তন বা রিপোর্ট জিজ্ঞাসা" 
                  required
                />
                <VoiceInputBtn 
                  embedded 
                  title="বিষয় মুখে বলুন"
                  @result="val => form.subject = val" 
                />
              </div>
            </div>
          </div>

          <!-- Message -->
          <div class="space-y-1.5 pt-1">
            <label class="block text-xs font-bold text-slate-700">
              আপনার বিস্তারিত বার্তা <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <textarea 
                v-model="form.message" 
                data-no-voice
                rows="4" 
                class="w-full pl-3.5 pr-11 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                placeholder="আপনার প্রশ্ন বা মতামত বাংলায় লিখুন বা মুখে বলুন..." 
                required
              ></textarea>
              <VoiceInputBtn 
                embedded 
                topRight
                title="বার্তা বাংলায় মুখে বলুন"
                @result="val => form.message = (form.message ? form.message + ' ' + val : val)" 
              />
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button 
              type="submit" 
              class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-sm shadow-md shadow-sky-600/25 flex items-center justify-center gap-2 transition active:scale-95 disabled:opacity-50"
              :disabled="submitting"
              style="color: #ffffff !important; background-color: #0284c7 !important;"
            >
              <span v-if="submitting" class="flex items-center gap-2">
                <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span>বার্তা পাঠানো হচ্ছে...</span>
              </span>
              <span v-else class="flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-white"></i>
                <span class="text-white">বার্তা পাঠান</span>
              </span>
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'Contact',
  data() {
    return {
      submitting: false,
      form: {
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: '',
      },
    };
  },
  methods: {
    async submitContact() {
      this.submitting = true;
      try {
        const res = await axios.post('/contact', this.form);
        if (res.data.success) {
          Swal.fire({
            title: 'বার্তা পাঠানো হয়েছে!',
            text: res.data.message || 'আপনার বার্তা সফলভাবে গ্রহণ করা হয়েছে।',
            icon: 'success',
            confirmButtonColor: '#0284c7',
          });
          this.form = { name: '', email: '', phone: '', subject: '', message: '' };
        }
      } catch (err) {
        Swal.fire({
          title: 'সমস্যা হয়েছে',
          text: err.response?.data?.message || 'বার্তা পাঠাতে সমস্যা হয়েছে। দয়া করে আবার চেষ্টা করুন।',
          icon: 'error',
          confirmButtonColor: '#0f172a',
        });
      } finally {
        this.submitting = false;
      }
    },
  },
};
</script>
