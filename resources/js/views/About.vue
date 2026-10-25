<template>
  <div class="w-full bg-slate-50/60 min-h-screen">
    <!-- 1. Hero Banner with Floating Glass Badges -->
    <section class="relative bg-gradient-to-b from-sky-900 via-slate-900 to-slate-950 text-white py-16 sm:py-24 overflow-hidden border-b border-slate-800">
      <!-- Ambient Glow Blobs -->
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute top-1/2 -right-24 w-96 h-96 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/10 border border-sky-400/30 text-sky-300 text-xs font-bold uppercase tracking-wider mb-5 backdrop-blur-md">
          <span class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></span>
          <span>স্মার্ট স্বাস্থ্যসেবা ইকোসিস্টেম</span>
        </div>
        
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-tight max-w-4xl mx-auto" style="color: #ffffff !important;">
          প্রযুক্তির স্পর্শে মানবিক স্বাস্থ্যসেবা, <span class="bg-gradient-to-r from-sky-400 via-teal-300 to-emerald-400 bg-clip-text text-transparent">সবার জন্য উন্মুক্ত</span>
        </h1>
        
        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto mt-5 leading-relaxed font-normal" style="color: #cbd5e1 !important;">
          হাসপাতালের দীর্ঘ লাইনে ঘণ্টার পর ঘণ্টা ভোগান্তির দিন শেষ। আধুনিক বাংলাদেশ বিনির্মাণে রোগীকেন্দ্রিক ডিজিটাল সিরিয়াল, টেলিমেডিসিন, এবং তাৎক্ষণিক জরুরি চিকিৎসাসেবা নিশ্চিত করাই আমাদের অঙ্গীকার।
        </p>

        <!-- Key Metrics Strip -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 max-w-4xl mx-auto mt-12 text-left">
          <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
            <div class="text-2xl sm:text-3xl font-black text-sky-400">৩,৩০০+</div>
            <div class="text-xs text-slate-300 font-medium mt-1">BMDC সার্টিফাইড বিশেষজ্ঞ</div>
          </div>
          <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
            <div class="text-2xl sm:text-3xl font-black text-teal-400">৬৪ জেলা</div>
            <div class="text-xs text-slate-300 font-medium mt-1">উপজেলা পর্যায়ের নেটওয়ার্ক</div>
          </div>
          <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
            <div class="text-2xl sm:text-3xl font-black text-amber-400">০ মিনিট</div>
            <div class="text-xs text-slate-300 font-medium mt-1">অপ্রয়োজনীয় লাইন ও ভোগান্তি</div>
          </div>
          <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
            <div class="text-2xl sm:text-3xl font-black text-emerald-400">১০০% ফ্রি</div>
            <div class="text-xs text-slate-300 font-medium mt-1">লাইভ কিউ ট্র্যাকিং সেবা</div>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. The Core Problem & Our Vision Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Left Image / Vision Card -->
        <div class="lg:col-span-6 relative">
          <div class="relative rounded-3xl overflow-hidden bg-gradient-to-tr from-sky-600 to-teal-500 p-1 shadow-2xl">
            <div class="bg-white rounded-[22px] p-6 sm:p-8 space-y-6">
              <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-hospital-user"></i>
                  </div>
                  <div>
                    <h4 class="font-extrabold text-slate-900 text-base">কেন এই উদ্যোগ?</h4>
                    <p class="text-xs text-slate-500">রোগী ও পরিবারের দুর্ভোগ লাঘবে</p>
                  </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold">
                  বাস্তবমুখী সমাধান
                </span>
              </div>

              <div class="space-y-4 text-xs sm:text-sm text-slate-600 leading-relaxed">
                <div class="flex items-start gap-3">
                  <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center shrink-0 mt-0.5 font-bold">
                    <i class="fa-solid fa-xmark text-xs"></i>
                  </div>
                  <p><strong>পূর্বের বাস্তবতা:</strong> সকালে গিয়ে টিকিট কাটা, চেম্বারে ঘন্টার পর ঘন্টা বসে থাকা এবং ডাক্তার কখন ডাকবেন তার কোনো নির্দিষ্ট তথ্য না থাকা।</p>
                </div>
                <div class="flex items-start gap-3">
                  <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 font-bold">
                    <i class="fa-solid fa-check text-xs"></i>
                  </div>
                  <p><strong>আমাদের সমাধান:</strong> বাসায় বসেই মোবাইলে সিরিয়াল পর্যবেক্ষণ। আপনার সিরিয়ালের ৩ জন আগে স্বয়ংক্রিয় এসএমএস সতর্কবার্তা পৌঁছে যাবে।</p>
                </div>
                <div class="flex items-start gap-3">
                  <div class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 mt-0.5 font-bold">
                    <i class="fa-solid fa-check text-xs"></i>
                  </div>
                  <p><strong>প্রান্তিক মানুষের অন্তর্ভুক্তি:</strong> যাদের স্মার্টফোন নেই, তারা গ্রামের স্থানীয় ফার্মেসি বা ডিজিটাল হেলথ পয়েন্ট থেকে সরাসরি বুকিং সুবিধা পাচ্ছেন।</p>
                </div>
              </div>

              <!-- Quick Quote Box -->
              <div class="p-4 rounded-xl bg-slate-50 border-l-4 border-sky-500 text-xs text-slate-700 italic font-medium">
                "আমরা শুধু একটি সফটওয়্যার তৈরি করিনি, চিকিৎসকের প্রতি রোগীর আস্থা ও নির্ভরযোগ্যতার একটি সম্মানজনক সেতু তৈরি করেছি।"
              </div>
            </div>
          </div>
        </div>

        <!-- Right Content -->
        <div class="lg:col-span-6 space-y-6">
          <span class="text-xs font-extrabold text-sky-600 uppercase tracking-widest bg-sky-50 px-3 py-1 rounded-lg">আমাদের মূল দর্শন ও লক্ষ্য</span>
          <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
            স্বচ্ছতা, গতি এবং নৈতিক স্বাস্থ্যসেবায় এক অনন্য বিপ্লব
          </h2>
          <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            আমরা বিশ্বাস করি চিকিৎসা সেবা কোনো বাণিজ্যিক সুবিধা নয়, এটি প্রতিটি নাগরিকের মৌলিক অধিকার। রোগ নির্ণয় থেকে শুরু করে ওষুধ সেবন পর্যন্ত প্রতিটি ধাপে প্রযুক্তির সঠিক ব্যবহারের মাধ্যমে আমরা চিকিৎসা সেবা সহজলভ্য করে তুলছি।
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-1.5">
              <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-shield-heart"></i>
              </div>
              <h4 class="font-bold text-slate-900 text-sm">১০০% ভেরিফায়েড প্র্যাকটিশনার</h4>
              <p class="text-[11px] text-slate-500 leading-relaxed">বিএমডিসি নম্বর ও শিক্ষাগত যোগ্যতার ম্যানুয়াল যাচাইকরণ ছাড়া কোনো ডাক্তার তালিকাভুক্ত হয় না।</p>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-1.5">
              <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-hand-holding-dollar"></i>
              </div>
              <h4 class="font-bold text-slate-900 text-sm">কোনো লুকানো চার্জ নেই</h4>
              <p class="text-[11px] text-slate-500 leading-relaxed">ডাক্তারের নির্ধারিত ভিজিট সরাসরি দৃশ্যমান। প্ল্যাটফর্ম ফি ও পেমেন্ট সম্পূর্ণ স্বচ্ছ ও নিরাপদ।</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 3. UNIQUE SECTION: Our 5 Innovative Tech Pillars (স্মার্ট প্রযুক্তি ফিচারসমূহ) -->
    <section class="bg-white py-16 sm:py-20 border-y border-slate-200/70">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
          <span class="text-xs font-extrabold text-sky-600 uppercase tracking-widest bg-sky-50 px-3 py-1 rounded-lg">অনন্য বৈশিষ্ট্যসমূহ</span>
          <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-2">
            যে ৫টি কারণে আমরা প্রচলিত হাসপাতাল ওয়েবসাইট থেকে আলাদা
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-2">
            আমরা সাধারণ স্ট্যাটিক তথ্যের পরিবর্তে রিয়েল-টাইম ইন্টারেক্টিভ স্বাস্থ্য টুলস উপহার দিয়েছি।
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Feature 1: Live Chamber Queue -->
          <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-sky-300 hover:shadow-lg transition-all group flex flex-col justify-between">
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-2xl bg-sky-500 text-white flex items-center justify-center text-2xl shadow-md shadow-sky-500/30 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-stopwatch-20"></i>
              </div>
              <h3 class="text-lg font-extrabold text-slate-900">লাইভ চেম্বার সিরিয়াল রেডার</h3>
              <p class="text-xs text-slate-600 leading-relaxed">
                চেম্বারে কত নম্বর রোগী ঢুকছেন এবং আপনার সিরিয়াল আসতে কতক্ষণ বাকি, তা লাইভ আপডেট হয়। সিরিয়াল ডাকলে রোগীকে স্বয়ংক্রিয় এসএমএস ও ফোন নোটিফিকেশন পাঠানো হয়।
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-bold text-sky-600 group-hover:translate-x-1 transition-transform">
              <span>লাইভ ট্র্যাকার দেখুন</span>
              <i class="fa-solid fa-arrow-right ml-1.5 text-[11px]"></i>
            </div>
          </div>

          <!-- Feature 2: AI Health Assistant -->
          <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-teal-300 hover:shadow-lg transition-all group flex flex-col justify-between">
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-2xl bg-teal-500 text-white flex items-center justify-center text-2xl shadow-md shadow-teal-500/30 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-brain"></i>
              </div>
              <h3 class="text-lg font-extrabold text-slate-900">বাংলা এআই সিম্পটম চেকার</h3>
              <p class="text-xs text-slate-600 leading-relaxed">
                কোন লক্ষণ দেখা দিলে কোন বিশেষজ্ঞ ডাক্তার দেখানো উচিত? আমাদের এআই স্বাস্থ্য সহকারী বাংলায় সাধারণ লক্ষণ বিশ্লেষণ করে সঠিক ডিপার্টমেন্ট ও নিকটস্থ বিশেষজ্ঞ ডাক্তার সুপারিশ করে।
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-bold text-teal-600 group-hover:translate-x-1 transition-transform">
              <span>এআই সহকারী ব্যবহার করুন</span>
              <i class="fa-solid fa-arrow-right ml-1.5 text-[11px]"></i>
            </div>
          </div>

          <!-- Feature 3: Digital Health Passport (QR) -->
          <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-lg transition-all group flex flex-col justify-between">
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-500/30 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-qrcode"></i>
              </div>
              <h3 class="text-lg font-extrabold text-slate-900">ডিজিটাল হেলথ পাসপোর্ট</h3>
              <p class="text-xs text-slate-600 leading-relaxed">
                প্রতিটি রোগীর জন্য রয়েছে ব্যক্তিগত কিউআর কোড। যেকোনো সড়ক দুর্ঘটনা বা জরুরি মুহূর্তে চিকিৎসকরা কিউআর স্ক্যান করে রক্তের গ্রুপ, ডায়াবেটিস স্ট্যাটাস ও জরুরি কন্টাক্ট নম্বর জানতে পারেন।
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-bold text-emerald-600 group-hover:translate-x-1 transition-transform">
              <span>হেলথ কার্ড বিস্তারিত</span>
              <i class="fa-solid fa-arrow-right ml-1.5 text-[11px]"></i>
            </div>
          </div>

          <!-- Feature 4: 1-Tap Emergency Hub -->
          <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-rose-300 hover:shadow-lg transition-all group flex flex-col justify-between">
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-2xl bg-rose-500 text-white flex items-center justify-center text-2xl shadow-md shadow-rose-500/30 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-truck-medical"></i>
              </div>
              <h3 class="text-lg font-extrabold text-slate-900">১-ট্যাপ ইমার্জেন্সি হাব</h3>
              <p class="text-xs text-slate-600 leading-relaxed">
                জরুরি মুহূর্তে রক্তের গ্রুপ দিয়ে রক্তদাতা খোঁজা, নিকটস্থ ভেরিফায়েড এম্বুলেন্স চালকের সাথে সরাসরি কল সংযোগ এবং লাইভ আইসিইউ বেড খালি আছে কিনা তা দেখার একীভূত নেটওয়ার্ক।
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-bold text-rose-600 group-hover:translate-x-1 transition-transform">
              <span>জরুরি হাব খুলুন</span>
              <i class="fa-solid fa-arrow-right ml-1.5 text-[11px]"></i>
            </div>
          </div>

          <!-- Feature 5: Audio Prescription Reader -->
          <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-purple-300 hover:shadow-lg transition-all group flex flex-col justify-between">
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-2xl bg-purple-500 text-white flex items-center justify-center text-2xl shadow-md shadow-purple-500/30 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-volume-high"></i>
              </div>
              <h3 class="text-lg font-extrabold text-slate-900">অডিও প্রেসক্রিপশন স্পিকার</h3>
              <p class="text-xs text-slate-600 leading-relaxed">
                বয়োজ্যেষ্ঠ বা দৃষ্টিপ্রতিবন্ধী রোগীদের সুবিধার্থে প্রেসক্রিপশনে লিখিত ওষুধের নাম, সেবনের নিয়ম ও ডোজ বাংলায় পরিষ্কার কণ্ঠে পড়ে শোনানোর স্বয়ংক্রিয় অডিও প্লেয়ার সুবিধা।
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-bold text-purple-600 group-hover:translate-x-1 transition-transform">
              <span>স্মার্ট অডিও প্রযুক্তি</span>
              <i class="fa-solid fa-arrow-right ml-1.5 text-[11px]"></i>
            </div>
          </div>

          <!-- Feature 6: Rural Pharmacy Points -->
          <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-amber-300 hover:shadow-lg transition-all group flex flex-col justify-between">
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shadow-md shadow-amber-500/30 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-store"></i>
              </div>
              <h3 class="text-lg font-extrabold text-slate-900">গ্রামীণ ডিজিটাল হেলথ পয়েন্ট</h3>
              <p class="text-xs text-slate-600 leading-relaxed">
                গ্রাম ও প্রত্যন্ত অঞ্চলের রোগীরা তাদের নিকটস্থ লাইসেন্সপ্রাপ্ত ফার্মেসি থেকে সরাসরি বিশেষজ্ঞ ডাক্তারের অনলাইন ভিডিও কনসালটেশন ও চেম্বার বুকিং সেবা নিতে পারেন।
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-bold text-amber-600 group-hover:translate-x-1 transition-transform">
              <span>এজেন্ট মডেল জানুন</span>
              <i class="fa-solid fa-arrow-right ml-1.5 text-[11px]"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 4. UNIQUE SECTION: Step-by-Step Patient Journey (কীভাবে সহজে সেবা পাবেন) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
      <div class="text-center max-w-xl mx-auto mb-14">
        <span class="text-xs font-extrabold text-sky-600 uppercase tracking-widest bg-sky-50 px-3 py-1 rounded-lg">সহজ ও দ্রুত কার্যপ্রণালী</span>
        <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-2">
          মাত্র ৪টি সহজ ধাপে সম্পূর্ণ চিকিৎসা সেবা
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-2">
          কোনো জটিল ফর্ম পূরণ ছাড়া স্মার্টফোনে মাত্র ২ মিনিটে সিরিয়াল নিশ্চিত করুন
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
        <!-- Step 1 -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xs relative z-10 flex flex-col items-center text-center space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-lg font-black">
            ১
          </div>
          <h4 class="font-extrabold text-slate-900 text-base">ডাক্তার নির্বাচন</h4>
          <p class="text-xs text-slate-500 leading-relaxed">
            বিভাগ, জেলা, উপজেলা বা রোগের ধরন অনুযায়ী অভিজ্ঞ বিশেষজ্ঞ চিকিৎসক খুঁজে নিন।
          </p>
        </div>

        <!-- Step 2 -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xs relative z-10 flex flex-col items-center text-center space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center text-lg font-black">
            ২
          </div>
          <h4 class="font-extrabold text-slate-900 text-base">সময় ও চেম্বার পছন্দ</h4>
          <p class="text-xs text-slate-500 leading-relaxed">
            ডাক্তারের অ্যাভেইলেবল স্লট ক্যালেন্ডার দেখে আপনার সুবিধাজনক দিন ও সময় বুক করুন।
          </p>
        </div>

        <!-- Step 3 -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xs relative z-10 flex flex-col items-center text-center space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg font-black">
            ৩
          </div>
          <h4 class="font-extrabold text-slate-900 text-base">লাইভ কিউ ট্র্যাকিং</h4>
          <p class="text-xs text-slate-500 leading-relaxed">
            মোবাইলেই নিশ্চিত টোকেন কার্ড এবং লাইভ সিরিয়াল পর্যবেক্ষণ করে সঠিক সময়ে চেম্বারে যান।
          </p>
        </div>

        <!-- Step 4 -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xs relative z-10 flex flex-col items-center text-center space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-black">
            ৪
          </div>
          <h4 class="font-extrabold text-slate-900 text-base">ডিজিটাল প্রেসক্রিপশন</h4>
          <p class="text-xs text-slate-500 leading-relaxed">
            পরামর্শ শেষে ডিজিটাল প্রেসক্রিপশন আজীবনের জন্য প্রোফাইলে সুরক্ষিত থাকে।
          </p>
        </div>
      </div>
    </section>

    <!-- 5. UNIQUE SECTION: Free Friday Charity & Social Responsibility (সামাজিক দায়বদ্ধতা) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 mb-16">
      <div class="rounded-3xl bg-gradient-to-r from-teal-800 to-sky-900 text-white p-8 sm:p-12 shadow-xl relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
          <div class="lg:col-span-8 space-y-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-400/20 text-teal-200 text-xs font-bold uppercase tracking-wider">
              <i class="fa-solid fa-hand-holding-heart"></i>
              <span>সামাজিক স্বাস্থ্য দায়বদ্ধতা</span>
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-white" style="color: #ffffff !important;">
              সুবিধাবঞ্চিতদের জন্য বিশেষ ফ্রি কনসালটেশন কোটা
            </h2>
            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed max-w-2xl font-normal" style="color: #e2e8f0 !important;">
              আমাদের প্ল্যাটফর্মের চিকিৎসকরা প্রতি সপ্তাহে নির্দিষ্ট সংখ্যক সুবিধাবঞ্চিত রোগীকে সম্পূর্ণ বিনামূল্যে (Free Consultation) সেবা প্রদান করেন। অর্থের অভাবে যেন কোনো মানবিক জীবন চিকিৎসা থেকে বঞ্চিত না হয়, সেটিই আমাদের লক্ষ্য।
            </p>
            <div class="pt-2 flex flex-wrap gap-4">
              <router-link to="/doctors" class="px-5 py-2.5 rounded-full bg-white text-slate-900 font-extrabold text-xs shadow-md hover:bg-slate-100 transition">
                ফ্রি কনসালটেশন স্লট খুঁজুন
              </router-link>
              <router-link to="/blood-sos" class="px-5 py-2.5 rounded-full bg-white/10 text-white font-bold text-xs border border-white/20 hover:bg-white/20 transition">
                রক্তদান কর্মসূচিতে যোগ দিন
              </router-link>
            </div>
          </div>

          <div class="lg:col-span-4 grid grid-cols-2 gap-3 text-center">
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10">
              <div class="text-2xl font-black text-amber-300">১২,০০০+</div>
              <div class="text-[11px] text-slate-200 mt-1">বিনামূল্যে চিকিৎসাপ্রাপ্ত</div>
            </div>
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10">
              <div class="text-2xl font-black text-teal-300">৪৫০+</div>
              <div class="text-[11px] text-slate-200 mt-1">স্বেচ্ছাসেবী ডাক্তার</div>
            </div>
            <div class="col-span-2 p-3.5 rounded-2xl bg-emerald-400/20 border border-emerald-400/30 text-xs font-semibold text-emerald-100">
              <i class="fa-solid fa-circle-check mr-1.5"></i> সম্পূর্ণ দাতব্য ও অলাভজনক উদ্যোগ
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 6. UNIQUE SECTION: Frequently Asked Questions (স্বচ্ছতা ও প্রশ্নোত্তর) -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 mb-16">
      <div class="text-center mb-10">
        <span class="text-xs font-extrabold text-sky-600 uppercase tracking-widest bg-sky-50 px-3 py-1 rounded-lg">প্রশ্ন ও উত্তর</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">সাধারণ কিছু জিজ্ঞাসা</h2>
      </div>

      <div class="space-y-3.5">
        <details class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs group cursor-pointer">
          <summary class="font-extrabold text-slate-900 text-sm flex items-center justify-between list-none">
            <span>সিরিয়াল বুকিংয়ের জন্য কি কোনো অতিরিক্ত ফি নেওয়া হয়?</span>
            <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform text-xs"></i>
          </summary>
          <p class="text-xs text-slate-600 mt-3 leading-relaxed pt-2 border-t border-slate-100">
            না, কোনো লুকানো বা অপ্রয়োজনীয় সার্ভিস চার্জ নেই। ডাক্তারের নিজস্ব চেম্বারের কনসালটেশন ফি সরাসরি প্রদর্শিত হয় এবং রোগী স্বাচ্ছন্দ্যে তা অনলাইন বা চেম্বারে পরিশোধ করতে পারেন।
          </p>
        </details>

        <details class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs group cursor-pointer">
          <summary class="font-extrabold text-slate-900 text-sm flex items-center justify-between list-none">
            <span>আমার প্রেসক্রিপশন ও মেডিকেল রিপোর্ট কতটা সুরক্ষিত?</span>
            <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform text-xs"></i>
          </summary>
          <p class="text-xs text-slate-600 mt-3 leading-relaxed pt-2 border-t border-slate-100">
            আপনার স্বাস্থ্য সংক্রান্ত সকল তথ্য আন্তর্জাতিক মানসম্পন্ন ২৫৬-বিট এসএসএল (SSL) এনক্রিপশন দ্বারা সুরক্ষিত। আপনার ব্যক্তিগত অনুমতি বা পাসওয়ার্ড ছাড়া কোনো তৃতীয় পক্ষ আপনার প্রেসক্রিপশন দেখার সুযোগ পায় না।
          </p>
        </details>

        <details class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs group cursor-pointer">
          <summary class="font-extrabold text-slate-900 text-sm flex items-center justify-between list-none">
            <span>জরুরি মুহূর্তে অ্যাম্বুলেন্স বা রক্ত পেতে কীভাবে সাহায্য করে?</span>
            <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform text-xs"></i>
          </summary>
          <p class="text-xs text-slate-600 mt-3 leading-relaxed pt-2 border-t border-slate-100">
            আমাদের ১-ট্যাপ ইমার্জেন্সি হাব থেকে আপনার জেলা ও উপজেলার নিকটস্থ নিবন্ধিত এম্বুলেন্স চালকদের সাথে সরাসরি ফোনে যোগাযোগ করতে পারবেন। এছাড়া ব্লাড এসওএস ফিচারের মাধ্যমে তাৎক্ষণিক রক্তদাতা আহ্বান করা যায়।
          </p>
        </details>
      </div>
    </section>

    <!-- 7. Interactive Call to Action Banner -->
    <section class="bg-gradient-to-b from-white to-slate-100 py-16 border-t border-slate-200">
      <div class="max-w-4xl mx-auto px-4 text-center space-y-6">
        <div class="w-16 h-16 rounded-3xl bg-sky-600 text-white flex items-center justify-center text-3xl mx-auto shadow-xl shadow-sky-600/30">
          <i class="fa-solid fa-heart-pulse"></i>
        </div>
        <h2 class="text-2xl sm:text-4xl font-black text-slate-900">
          আপনার ও আপনার পরিবারের সুস্বাস্থ্য নিশ্চিত হোক আজই
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
          দেশসেরা চিকিৎসকদের পরামর্শ পেতে ভিজিট করুন আমাদের বিশেষজ্ঞ প্যানেল অথবা যেকোনো স্বাস্থ্য তথ্যের জন্য পরামর্শ নিন এআই সহকারীর।
        </p>
        <div class="pt-4 flex flex-wrap justify-center items-center gap-3.5">
          <router-link 
            to="/doctors" 
            class="btn-cta-doctor"
            style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; color: #ffffff !important; text-decoration: none !important;"
          >
            <i class="fa-solid fa-user-doctor mr-2 text-white"></i> 
            <span>বিশেষজ্ঞ ডাক্তার তালিকা</span>
          </router-link>
          <router-link 
            to="/ai-assistant" 
            class="btn-cta-ai"
            style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; color: #ffffff !important; text-decoration: none !important;"
          >
            <i class="fa-solid fa-robot mr-2 text-teal-400"></i> 
            <span>এআই লক্ষণ পরীক্ষা</span>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'About',
};
</script>

<style scoped>
.btn-cta-doctor {
  background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
  color: #ffffff !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 13px 30px !important;
  border-radius: 9999px !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  text-decoration: none !important;
  box-shadow: 0 10px 25px -4px rgba(2, 132, 199, 0.45) !important;
  transition: all 0.25s ease !important;
}

.btn-cta-doctor:hover {
  background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
  color: #ffffff !important;
  transform: translateY(-2px);
  box-shadow: 0 14px 28px -4px rgba(2, 132, 199, 0.55) !important;
}

.btn-cta-ai {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
  color: #ffffff !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 13px 30px !important;
  border-radius: 9999px !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  text-decoration: none !important;
  border: 1px solid rgba(255, 255, 255, 0.15) !important;
  box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.35) !important;
  transition: all 0.25s ease !important;
}

.btn-cta-ai:hover {
  background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
  color: #ffffff !important;
  transform: translateY(-2px);
  box-shadow: 0 14px 28px -4px rgba(15, 23, 42, 0.45) !important;
}
</style>
