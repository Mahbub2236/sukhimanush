<template>
  <div class="prescription-explainer-page py-6 sm:py-10 bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Top Hero Header -->
      <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 shadow-xs mb-3">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="text-xs font-bold text-emerald-800">
            <i class="fa-solid fa-wand-magic-sparkles text-emerald-600 mr-1"></i> AI বাংলা প্রেসক্রিপশন অনুবাদক ও নির্দেশিকা
          </span>
        </div>

        <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
          প্রেসক্রিপশনের ছবি দিন, <span class="text-emerald-600">সহজ বাংলায় জানুন</span> ঔষধের নিয়ম
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-2 sm:mt-3 leading-relaxed">
          ডাক্তারের হাতের লেখা ও প্রেসক্রিপশন বুঝতে আর কারো ওপর নির্ভর করতে হবে না। প্রেসক্রিপশনের ছবি আপলোড করুন অথবা নাম লিখুন—আমাদের এআই তাৎক্ষণিকভাবে বাংলায় ঔষধের নিয়ম, খাওয়ার সময় ও ল্যাব টেস্টের কারণ বুঝিয়ে দেবে।
        </p>
      </div>

      <!-- Quick 1-Click Samples Row -->
      <div class="mb-8 p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
              <i class="fa-solid fa-lightbulb"></i>
            </div>
            <div>
              <span class="text-xs font-bold text-slate-800">নমুনা প্রেসক্রিপশন দিয়ে টেস্ট করুন:</span>
              <p class="text-[11px] text-slate-400">ছবি না থাকলে যেকোনো একটিতে ক্লিক করে সরাসরি ফলাফল দেখুন</p>
            </div>
          </div>
          <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full self-start sm:self-auto border border-emerald-100">
            ১ ক্লিকে ফলাফল
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
          <button 
            v-for="sample in samplePresets" 
            :key="sample.id"
            type="button"
            @click="loadSample(sample.id)"
            class="p-3 rounded-xl border text-left transition-all duration-200 flex items-start gap-2.5 group cursor-pointer"
            :class="activeSampleId === sample.id ? 'border-emerald-500 bg-emerald-50/80 shadow-xs' : 'border-slate-200 hover:border-emerald-300 hover:bg-slate-50'"
          >
            <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 mt-0.5"
              :class="activeSampleId === sample.id ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-emerald-100 group-hover:text-emerald-700'"
            >
              <i class="fa-solid fa-file-prescription"></i>
            </span>
            <div class="min-w-0">
              <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 truncate">
                {{ sample.title }}
              </div>
              <div class="text-[11px] text-slate-400 mt-0.5 truncate">{{ sample.badge }}</div>
            </div>
          </button>
        </div>
      </div>

      <!-- Main Interaction Grid (Left: Upload/Input, Right: Output/Preview) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Upload & Input Panel -->
        <div class="lg:col-span-5 space-y-4">
          <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-5">
            
            <!-- Tab Switcher (Image vs Text) -->
            <div class="flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200 text-xs font-bold">
              <button 
                type="button"
                @click="inputMode = 'image'"
                class="flex-1 py-2 px-3 rounded-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
                :class="inputMode === 'image' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
              >
                <i class="fa-solid fa-camera text-xs"></i>
                <span>ছবি / ক্যামেরা স্ক্যান</span>
              </button>
              <button 
                type="button"
                @click="inputMode = 'text'"
                class="flex-1 py-2 px-3 rounded-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
                :class="inputMode === 'text' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
              >
                <i class="fa-solid fa-keyboard text-xs"></i>
                <span>ওষুধের নাম লিখুন</span>
              </button>
            </div>

            <!-- Image Upload Mode -->
            <div v-if="inputMode === 'image'" class="space-y-4">
              <!-- Upload Box -->
              <div 
                class="relative border-2 border-dashed rounded-2xl p-6 text-center transition-all cursor-pointer group"
                :class="dragActive ? 'border-emerald-500 bg-emerald-50/50' : (previewUrl ? 'border-emerald-300 bg-slate-50' : 'border-slate-300 hover:border-emerald-400 bg-white')"
                @dragover.prevent="dragActive = true"
                @dragleave.prevent="dragActive = false"
                @drop.prevent="handleFileDrop"
                @click="triggerFileInput"
              >
                <input 
                  ref="fileInput"
                  type="file" 
                  accept="image/*" 
                  class="hidden" 
                  @change="handleFileChange"
                />

                <!-- Image Selected State -->
                <div v-if="previewUrl" class="space-y-3">
                  <div class="relative max-h-56 mx-auto rounded-xl overflow-hidden border border-slate-200 shadow-inner group/img">
                    <img :src="previewUrl" alt="Prescription preview" class="w-full h-auto object-contain max-h-56 mx-auto" />
                    <!-- Scanning Radar Line animation during analysis -->
                    <div v-if="isAnalyzing" class="absolute inset-x-0 h-1 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_12px_rgba(16,185,129,0.9)] animate-scan-line"></div>
                    
                    <button 
                      type="button"
                      @click.stop="clearImage"
                      class="absolute top-2 right-2 w-7 h-7 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition cursor-pointer"
                      title="ছবি মুছুন"
                    >
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </div>
                  <div class="text-xs text-slate-500 font-medium">
                    <span class="text-emerald-600 font-bold">✓ ছবি নির্বাচিত হয়েছে</span> — পরিবর্তন করতে আবার ক্লিক করুন
                  </div>
                </div>

                <!-- Empty Upload Prompt -->
                <div v-else class="space-y-3 py-4">
                  <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-2xl group-hover:scale-110 transition-transform shadow-xs">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                  </div>
                  <div>
                    <h3 class="text-sm font-bold text-slate-800">প্রেসক্রিপশনের ছবি এখানে ছাড়ুন বা ব্রাউজ করুন</h3>
                    <p class="text-xs text-slate-400 mt-1">মোবাইল থেকে সরাসরি ছবি তুলুন অথবা গ্যালারি থেকে সিলেক্ট করুন</p>
                  </div>
                  <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                    <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                    <span>সম্পূর্ণ গোপনীয় ও নিরাপদ</span>
                  </div>
                </div>
              </div>

              <!-- Extra Notes / Doctor Instructions Input (Optional) -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">অতিরিক্ত কোনো নোট বা জিজ্ঞাসা (ঐচ্ছিক):</label>
                <input 
                  v-model="extraNotes"
                  type="text" 
                  placeholder="যেমন: রোগীর বয়স, বিশেষ কোনো সমস্যা থাকলে লিখুন..."
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                />
              </div>
            </div>

            <!-- Manual Text Mode -->
            <div v-else class="space-y-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  প্রেসক্রিপশনের ওষুধের নাম বা বিবরণ লিখুন:
                </label>
                <textarea 
                  v-model="manualText"
                  rows="6"
                  placeholder="উদাহরণ:
                    1. Cap. Seclo 20mg 1+0+1 খাওয়ার আগে
                    2. Tab. Napa Extend 1+0+1 ভরা পেটে
                    3. Tab. Fexo 120mg 0+0+1
                    টেস্ট: CBC, USG of W/A"
                  class="w-full p-3.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 leading-relaxed font-sans"
                ></textarea>
                <p class="text-[11px] text-slate-400 mt-1">
                  প্রতি লাইনে একটি করে ওষুধের নাম বা টেস্টের নাম লিখুন। বাংলা বা ইংরেজিতে লেখা যাবে।
                </p>
              </div>
            </div>

            <!-- Error Banner -->
            <div v-if="errorMessage" class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2.5">
              <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 shrink-0"></i>
              <div class="flex-1">
                <p class="font-semibold">{{ errorMessage }}</p>
                <p class="text-[11px] text-rose-500 mt-0.5">ছবিটি স্পষ্ট করে তুলুন অথবা 'ওষুধের নাম লিখুন' ট্যাবে গিয়ে নাম লিখুন।</p>
              </div>
            </div>

            <!-- Analyze CTA Button -->
            <button 
              type="button"
              @click="submitPrescription"
              :disabled="isAnalyzing || (!selectedFile && !manualText.trim() && !activeSampleId)"
              class="w-full py-3.5 px-4 rounded-xl text-white font-extrabold text-sm flex items-center justify-center gap-2 shadow-lg transition-all duration-200 active:scale-98 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
              :class="isAnalyzing ? 'bg-slate-700' : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-emerald-600/25'"
            >
              <i v-if="isAnalyzing" class="fa-solid fa-spinner fa-spin text-sm"></i>
              <i v-else class="fa-solid fa-wand-magic-sparkles text-sm"></i>
              <span>{{ isAnalyzing ? 'প্রেসক্রিপশন বিশ্লেষণ হচ্ছে...' : 'সহজ বাংলায় বিশ্লেষণ করুন' }}</span>
            </button>

            <!-- Scanning steps indicator while loading -->
            <div v-if="isAnalyzing" class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-2">
              <div class="flex items-center gap-2 text-slate-700 font-semibold text-[11px]">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>{{ analysisStepText }}</span>
              </div>
              <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-500" :style="{ width: analysisProgress + '%' }"></div>
              </div>
            </div>

            <!-- Compliance Pillar Badge -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
              <span class="flex items-center gap-1">
                <i class="fa-solid fa-lock text-emerald-600"></i>
                <span>DGHS ও স্বাস্থ্য নীতিমালা সম্মত</span>
              </span>
              <span>v2.5 এআই ইঞ্জিন</span>
            </div>

          </div>
        </div>

        <!-- Right: Results Display (Smart Bangla Prescription Card) -->
        <div class="lg:col-span-7">
          
          <!-- Empty State (When no analysis done yet) -->
          <div v-if="!currentResult && !isAnalyzing" class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm text-center space-y-4">
            <div class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mx-auto shadow-inner">
              <i class="fa-solid fa-receipt"></i>
            </div>
            <div class="max-w-md mx-auto">
              <h3 class="text-base sm:text-lg font-bold text-slate-800">কোনো প্রেসক্রিপশন বিশ্লেষণ করা হয়নি</h3>
              <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                বামের ফর্ম থেকে প্রেসক্রিপশনের ছবি আপলোড করুন অথবা উপরের যেকোনো একটি নমুনা প্রেসক্রিপশনে ক্লিক করে এখনই ফলাফল দেখুন।
              </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
              <button 
                type="button" 
                @click="loadSample('sample_gastric')" 
                class="px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-xs font-semibold text-slate-700 transition cursor-pointer"
              >
                👉 গ্যাস্ট্রিকের প্রেসক্রিপশন দেখুন
              </button>
              <button 
                type="button" 
                @click="loadSample('sample_cold_fever')" 
                class="px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-xs font-semibold text-slate-700 transition cursor-pointer"
              >
                👉 সর্দি-জ্বরের প্রেসক্রিপশন দেখুন
              </button>
            </div>
          </div>

          <!-- Loading State Placeholder -->
          <div v-else-if="isAnalyzing" class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm text-center space-y-5 animate-pulse">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 mx-auto"></div>
            <div class="h-4 bg-slate-200 rounded-full w-2/3 mx-auto"></div>
            <div class="h-3 bg-slate-100 rounded-full w-1/2 mx-auto"></div>
            <div class="space-y-3 pt-4">
              <div class="h-20 bg-slate-50 rounded-2xl border border-slate-100"></div>
              <div class="h-20 bg-slate-50 rounded-2xl border border-slate-100"></div>
            </div>
          </div>

          <!-- Active Prescription Analysis Card -->
          <div v-else-if="currentResult" class="space-y-5 animate-fade-in" id="printablePrescriptionArea">
            
            <!-- Result Main Card -->
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
              
              <!-- Card Top Header (Doctor & Meta Info) -->
              <div class="p-4 sm:p-6 bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white relative">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                  <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-400/30 uppercase tracking-wider mb-2">
                      <i class="fa-solid fa-circle-check"></i>
                      <span>{{ currentResult.specialty || 'মেডিসিন ও স্বাস্থ্যসেবা' }}</span>
                    </div>
                    <h2 class="text-base sm:text-xl font-extrabold text-white leading-tight">
                      {{ currentResult.title || 'প্রেসক্রিপশন সহজ বাংলা চার্ট' }}
                    </h2>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                      {{ currentResult.doctor_name || 'বিশেষজ্ঞ চিকিৎসক' }} &bull; {{ currentResult.hospital_name || 'চেম্বার ও ক্লিনিক' }}
                    </p>
                  </div>

                  <div class="flex flex-wrap sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-1 sm:gap-0.5 text-xs text-slate-300 pt-2 sm:pt-0 border-t border-white/10 sm:border-0 shrink-0">
                    <div>তারিখ: <strong class="text-white">{{ currentResult.date || todayDate }}</strong></div>
                    <div class="text-[11px] text-emerald-400 font-medium">রোগী: {{ currentResult.patient_name || 'সম্মানিত রোগী' }}</div>
                  </div>
                </div>

                <!-- Voice Audio Player Bar inside Header -->
                <div class="mt-3.5 pt-3 border-t border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white/5 p-3 sm:p-3.5 rounded-xl sm:rounded-2xl backdrop-blur-xs">
                  <div class="flex items-center gap-2.5">
                    <button 
                      type="button" 
                      @click="toggleVoiceAudio"
                      class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center text-sm font-bold shadow-md transition-all active:scale-95 cursor-pointer shrink-0"
                      :class="isSpeaking ? 'bg-rose-600 text-white animate-pulse' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950'"
                      :title="isSpeaking ? 'অডিও বন্ধ করুন' : 'বাংলায় পড়ে শুনুন'"
                    >
                      <i :class="isSpeaking ? 'fa-solid fa-stop' : 'fa-solid fa-volume-high'"></i>
                    </button>
                    <div class="min-w-0">
                      <div class="text-xs font-bold text-white flex items-center gap-1.5 flex-wrap">
                        <span>{{ isSpeaking ? 'ভয়েসে পড়া হচ্ছে...' : 'বাংলায় পড়ে শুনুন' }}</span>
                        <span v-if="isSpeaking" class="inline-flex gap-0.5 items-end h-3">
                          <span class="w-1 h-2 bg-emerald-400 rounded-full animate-bounce"></span>
                          <span class="w-1 h-3 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                          <span class="w-1 h-1 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                        </span>
                      </div>
                      <p class="text-[10px] text-slate-300 leading-tight">বয়স্ক বা পড়তে না জানা রোগীদের জন্য ভয়েস সুবিধা</p>
                    </div>
                  </div>

                  <!-- Quick Print & Share Buttons -->
                  <div class="flex items-center gap-2 w-full sm:w-auto pt-2 sm:pt-0 border-t border-white/10 sm:border-0">
                    <button 
                      type="button"
                      @click="shareOnWhatsApp"
                      class="flex-1 sm:flex-none px-3 sm:px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition active:scale-95 cursor-pointer"
                      title="WhatsApp-এ পাঠান"
                    >
                      <i class="fa-brands fa-whatsapp text-sm"></i>
                      <span>WhatsApp</span>
                    </button>
                    <button 
                      type="button"
                      @click="printPrescription"
                      class="flex-1 sm:flex-none px-3 sm:px-3.5 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition active:scale-95 cursor-pointer"
                      title="প্রেসক্রিপশন প্রিন্ট প্রিভিউ"
                    >
                      <i class="fa-solid fa-print text-sm"></i>
                      <span>প্রিন্ট / PDF</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Body: Medicines Routine Breakdown -->
              <div class="p-3.5 sm:p-6 space-y-5 sm:space-y-6">
                
                <!-- Section 1: Medicines List -->
                <div>
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-3.5">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 flex items-center gap-2">
                      <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-pills"></i>
                      </span>
                      <span>ঔষধ সেবনের নিয়ম <span class="text-emerald-700 font-bold">(মোট {{ (currentResult.medicines || []).length }}টি ঔষধ)</span>:</span>
                    </h3>
                    <span class="hidden sm:inline-block text-[11px] font-semibold text-slate-400">ডোজ ও খাওয়ার সময়সূচি</span>
                  </div>

                  <div class="space-y-2.5 sm:space-y-3">
                    <div 
                      v-for="(med, idx) in currentResult.medicines" 
                      :key="idx"
                      class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200/90 hover:border-emerald-300 bg-white shadow-2xs hover:shadow-md transition-all duration-200 space-y-3"
                    >
                      <!-- Row 1: Full-Width Medicine Name & Generic (Never squeezed) -->
                      <div class="flex items-start gap-3">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base sm:text-lg shrink-0 mt-0.5 border border-emerald-100">
                          <i :class="med.icon || 'fa-solid fa-tablets'"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                          <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-sm sm:text-base font-extrabold text-slate-900 leading-snug">
                              {{ med.name }}
                            </h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 font-bold text-slate-600 shrink-0">
                              {{ med.type || 'ঔষধ' }}
                            </span>
                          </div>
                          <p class="text-xs text-slate-400 font-mono mt-0.5">{{ med.generic }}</p>
                        </div>
                      </div>

                      <!-- Row 2: Dosage Schedule & Timing Bar (Clearly visible and spacious) -->
                      <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <!-- Dosage (e.g. ১ + ০ + ১ ...) -->
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-600 text-white font-bold text-xs sm:text-sm tracking-wide shadow-2xs">
                          <i class="fa-solid fa-clock-rotate-left text-[11px] opacity-80"></i>
                          <span>{{ med.dosage_bn }}</span>
                        </span>

                        <!-- Timing (e.g. ভরা পেটে / খাবারের পর) -->
                        <span v-if="med.timing_bn" class="inline-flex items-center gap-1 text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                          <i class="fa-regular fa-clock text-[10px] text-amber-600"></i>
                          <span>{{ med.timing_bn }}</span>
                        </span>

                        <!-- Duration with 1-click edit option -->
                        <div class="inline-flex items-center gap-1.5 flex-wrap">
                          <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                            <i class="fa-regular fa-calendar-check text-[10px] text-slate-500"></i>
                            <span>মেয়াদ: <strong class="text-slate-900">{{ med.duration_bn || '৭ দিন' }}</strong></span>
                          </span>
                          <button 
                            type="button" 
                            @click="toggleEditDuration(idx)"
                            class="text-[10px] text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-200/90 font-bold transition flex items-center gap-1 cursor-pointer active:scale-95"
                            title="প্রেসক্রিপশনের সাথে দিন না মিললে পরিবর্তন করুন"
                          >
                            <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                            <span>দিন বদলান</span>
                          </button>
                        </div>
                      </div>

                      <!-- Quick Duration Selector Dropdown/Bar (Appears when clicking 'দিন বদলান') -->
                      <div v-if="editingDurationIdx === idx" class="p-3 rounded-xl bg-emerald-50/90 border border-emerald-200 text-xs space-y-2 animate-fade-in shadow-2xs">
                        <div class="flex items-center justify-between text-[11px] font-bold text-emerald-900">
                          <span class="flex items-center gap-1">
                            <i class="fa-solid fa-wand-magic-sparkles text-emerald-600"></i>
                            <span>প্রেসক্রিপশনে কত দিন লেখা আছে? বেছে নিন:</span>
                          </span>
                          <button type="button" @click="editingDurationIdx = null" class="w-5 h-5 rounded-full hover:bg-emerald-200/60 flex items-center justify-center text-slate-500 hover:text-slate-800 text-sm font-bold cursor-pointer">&times;</button>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5">
                          <button 
                            v-for="dOption in ['৩ দিন', '৫ দিন', '৭ দিন', '১০ দিন', '১৪ দিন', '১৫ দিন', '২১ দিন', '১ মাস', 'চলমান']" 
                            :key="dOption"
                            type="button"
                            @click="updateMedicineDuration(idx, dOption)"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition active:scale-95 cursor-pointer"
                            :class="med.duration_bn === dOption ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200'"
                          >
                            {{ dOption }}
                          </button>
                        </div>
                        <p class="text-[10px] text-emerald-700/80">দিন পরিবর্তন করলে নিচের ফার্মেসি বাজেট স্বয়ংক্রিয়ভাবে সঠিক হিসাবে আপডেট হবে।</p>
                      </div>

                      <!-- Row 3: Purpose in Bengali -->
                      <div v-if="med.purpose_bn" class="text-xs font-semibold text-emerald-900 bg-emerald-50/80 px-2.5 py-1.5 rounded-lg flex items-start gap-1.5 border border-emerald-100">
                        <i class="fa-solid fa-heart-pulse text-emerald-600 text-xs mt-0.5 shrink-0"></i>
                        <span>{{ med.purpose_bn }}</span>
                      </div>

                      <!-- Row 4: Special Instructions if any -->
                      <div v-if="med.instructions_bn" class="pt-2 border-t border-slate-100 flex items-start gap-1.5 text-xs text-slate-600 leading-relaxed">
                        <i class="fa-solid fa-circle-info text-sky-600 mt-0.5 shrink-0 text-xs"></i>
                        <span>{{ med.instructions_bn }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section: Estimated Pharmacy Cost Card (ফার্মেসি বাজেট ও ওষুধের আনুমানিক খরচ) -->
                <div class="rounded-2xl border border-emerald-200/90 bg-gradient-to-br from-emerald-50/60 via-white to-teal-50/40 p-4 sm:p-5 shadow-xs space-y-4">
                  
                  <!-- Top Title & Badge -->
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-emerald-100/90 pb-3">
                    <div class="flex items-center gap-2.5">
                      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center text-lg shadow-xs shrink-0">
                        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                      </div>
                      <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 leading-snug">
                          ফার্মেসি বাজেট ও ওষুধের আনুমানিক খরচ
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                          খুচরা বাজারদর অনুযায়ী প্রেসক্রিপশনের মোট ওষুধের সম্ভাব্য খরচ
                        </p>
                      </div>
                    </div>

                    <!-- Live Total Badge -->
                    <div class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100/80 text-emerald-900 text-xs sm:text-sm font-black border border-emerald-200 shadow-2xs">
                      <i class="fa-solid fa-coins text-emerald-600"></i>
                      <span>মোট: {{ computedPricingSummary.formatted_range }}</span>
                    </div>
                  </div>

                  <!-- Itemized Table / Medicines with Estimated Price -->
                  <div class="space-y-2">
                    <div class="text-[11px] font-extrabold text-slate-600 flex items-center justify-between px-1 uppercase tracking-wider">
                      <span>ওষুধের নাম ও পরিমাণ</span>
                      <span>সম্ভাব্য মূল্য (BDT)</span>
                    </div>

                    <div class="divide-y divide-emerald-100/80 rounded-xl bg-white border border-emerald-100 overflow-hidden shadow-2xs">
                      <div 
                        v-for="(item, pIdx) in computedPricingSummary.items" 
                        :key="pIdx"
                        class="p-2.5 sm:p-3 flex items-center justify-between gap-3 text-xs hover:bg-emerald-50/30 transition"
                      >
                        <div class="min-w-0">
                          <div class="font-bold text-slate-900 truncate">{{ item.name }}</div>
                          <div class="text-[11px] text-slate-500 flex items-center gap-2 flex-wrap mt-0.5">
                            <span class="font-medium text-slate-600">{{ item.estimated_qty_text }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-emerald-700 font-semibold">{{ item.unit_price_note }}</span>
                            <span v-if="item.duration_text" class="text-slate-400">({{ item.duration_text }})</span>
                          </div>
                        </div>
                        <div class="text-right shrink-0">
                          <span class="font-extrabold text-slate-900 text-xs sm:text-sm">৳ {{ item.total_cost }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Footer Advisory & Pharmacy Copy CTA -->
                  <div class="pt-1 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-[11px] text-slate-500">
                    <div class="flex items-start gap-1.5 leading-relaxed">
                      <i class="fa-solid fa-circle-info text-emerald-600 mt-0.5 shrink-0 text-xs"></i>
                      <span>ফার্মেসি ও প্রস্তুতকারক ব্র্যান্ড (যেমন: স্কয়ার, ইনসেপ্টা, বেক্সিমকো) ভেদে খুচরা মূল্যে সামান্য পার্থক্য হতে পারে।</span>
                    </div>

                    <!-- Pharmacy Order Copy Button -->
                    <button 
                      type="button" 
                      @click="copyPharmacyOrderList"
                      class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-white hover:bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold text-xs flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer shrink-0 shadow-2xs"
                      title="ফার্মেসিতে দেখানোর জন্য তালিকা কপি করুন"
                    >
                      <i :class="isCopiedPharmacyList ? 'fa-solid fa-check text-emerald-600' : 'fa-solid fa-clipboard-list text-emerald-600'"></i>
                      <span>{{ isCopiedPharmacyList ? 'ফার্মেসি লিস্ট কপি হয়েছে!' : 'ফার্মেসির জন্য লিস্ট কপি' }}</span>
                    </button>
                  </div>

                </div>

                <!-- Section 2: Diagnostic Tests Explained -->
                <div v-if="currentResult.tests && currentResult.tests.length > 0">
                  <div class="flex flex-wrap items-center justify-between gap-1 mb-3 pt-2 border-t border-slate-100">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 flex items-center gap-2">
                      <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-microscope"></i>
                      </span>
                      <span>ল্যাব ও ডায়াগনস্টিক টেস্টের কারণ:</span>
                    </h3>
                    <router-link to="/diagnostic-centers" class="text-[11px] font-bold text-teal-600 hover:text-teal-700">
                      ল্যাব টেস্ট ছাড় বুকিং &rarr;
                    </router-link>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                    <div 
                      v-for="(test, tIdx) in currentResult.tests" 
                      :key="tIdx"
                      class="p-3 sm:p-3.5 rounded-xl bg-teal-50/50 border border-teal-100 space-y-1.5"
                    >
                      <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-flask text-teal-600"></i>
                        <span>{{ test.name }}</span>
                      </div>
                      <p class="text-[11px] text-slate-600 leading-relaxed">
                        <strong>কেন দেওয়া হয়েছে:</strong> {{ test.purpose_bn }}
                      </p>
                      <p v-if="test.preparation_bn" class="text-[10px] text-slate-500">
                        <strong>প্রস্তুতি:</strong> {{ test.preparation_bn }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Section 3: Lifestyle & Diet Advice -->
                <div v-if="currentResult.advice && currentResult.advice.length > 0">
                  <div class="mb-3 pt-2 border-t border-slate-100">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 flex items-center gap-2">
                      <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-apple-whole"></i>
                      </span>
                      <span>খাদ্যাভ্যাস ও স্বাস্থ্যবিধি পরামর্শ:</span>
                    </h3>
                  </div>

                  <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-2">
                    <div 
                      v-for="(tip, idx) in currentResult.advice" 
                      :key="idx"
                      class="flex items-start gap-2 text-xs text-slate-700 leading-relaxed"
                    >
                      <i class="fa-solid fa-circle-check text-emerald-600 mt-1 shrink-0 text-xs"></i>
                      <span>{{ tip }}</span>
                    </div>
                  </div>
                </div>

                <!-- Bottom Specialist Referral Card -->
                <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-sky-50 to-emerald-50 border border-sky-100 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-xs">
                  <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="w-10 h-10 rounded-xl text-white flex items-center justify-center text-lg shrink-0 shadow-sm"
                         style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                      <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                      <div class="text-xs sm:text-sm font-bold text-slate-900">ডাক্তার দেখাতে চান বা সিরিয়াল দরকার?</div>
                      <p class="text-[11px] text-slate-500 leading-tight">এই রোগের সেরা বিশেষজ্ঞ চিকিৎসকদের চেম্বারে সিরিয়াল নিন সরাসরি</p>
                    </div>
                  </div>

                  <router-link 
                    to="/doctors" 
                    class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl font-bold text-xs shrink-0 shadow-md transition-all active:scale-95 inline-flex items-center justify-center gap-2 group text-white cursor-pointer"
                    style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; color: #ffffff !important; text-decoration: none !important;"
                  >
                    <span class="tracking-wide">বিশেষজ্ঞ ডাক্তার খুঁজুন</span>
                    <i class="fa-solid fa-arrow-right text-[11px] transition-transform duration-200 group-hover:translate-x-1"></i>
                  </router-link>
                </div>

              </div>

              <!-- Footer Legal Note -->
              <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-100 text-[10px] sm:text-[11px] text-slate-400 text-center leading-relaxed">
                <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i>
                সতর্কতা: এটি রোগীর বোঝার সুবিধার্থে তৈরি শিক্ষামূলক সহায়িকা। নতুন কোনো ওষুধ শুরু বা বন্ধ করার পূর্বে অবশ্যই আপনার মূল প্রেসক্রিপশন প্রদানকারী চিকিৎসকের পরামর্শ নিন।
              </div>

            </div>

          </div>

        </div>

      </div>

      <!-- Dedicated Prescription Print Modal (A4 Standard Medical Pad Format) -->
      <PrescriptionPrintModal 
        :show="showPrintModal" 
        :prescription="activePrintPrescription" 
        :doctor="activePrintDoctor" 
        @close="showPrintModal = false" 
      />

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import PrescriptionPrintModal from '../components/PrescriptionPrintModal.vue';

export default {
  name: 'PrescriptionExplainer',
  components: {
    PrescriptionPrintModal
  },
  data() {
    return {
      inputMode: 'image', // 'image' | 'text'
      selectedFile: null,
      previewUrl: '',
      extraNotes: '',
      manualText: '',
      dragActive: false,
      isAnalyzing: false,
      analysisProgress: 10,
      analysisStepText: 'প্রেসক্রিপশন আপলোড হচ্ছে...',
      activeSampleId: null,
      currentResult: null,
      errorMessage: '',
      showPrintModal: false,
      isSpeaking: false,
      speechUtterance: null,
      samplePresets: [
        {
          id: 'sample_gastric',
          title: 'গ্যাস্ট্রিক ও বুক জ্বালা',
          badge: 'সেকলো, অ্যালারিড, এন্টাসিড'
        },
        {
          id: 'sample_cold_fever',
          title: 'সর্দি, কাশি ও সাধারণ জ্বর',
          badge: 'নাপা এক্সটেন্ড, ফেক্সো, তুসকা'
        },
        {
          id: 'sample_hypertension_diabetes',
          title: 'উচ্চ রক্তচাপ ও ডায়াবেটিস',
          badge: 'বাইজরান, কম্বিট, নিউরো-বি'
        }
      ],
      isCopiedPharmacyList: false,
      editingDurationIdx: null
    };
  },
  computed: {
    computedPricingSummary() {
      if (!this.currentResult || !this.currentResult.medicines) {
        return { formatted_range: '৳ ০০', items: [], min_total: 0, max_total: 0 };
      }

      // Comprehensive accurate Bangladesh retail prices (in BDT)
      const knownPrices = {
        // Paracetamol & Painkillers (Long Para, Napa, Ace, etc.)
        'long para': 2.5, 'napa extend': 2.5, 'napa': 2.5, 'ace plus': 3.0, 'ace': 2.5,
        'fast': 2.5, 'renova': 2.5, 'paracetamol': 2.5, 'para': 2.5, 'pyralgin': 2.5,
        'rolac': 12, 'torax': 12, 'ketorolac': 12, 'etoricoxib': 14, 'etorix': 14,
        'naproxen': 7, 'anaprox': 7, 'diclofenac': 4,

        // Antihistamines & Allergy / Respiratory
        'oradin': 3.5, 'loratin': 3.5, 'loratadine': 3.5, 'montair': 16, 'monas': 16,
        'lumona': 16, 'montelukast': 16, 'fexo': 8.5, 'fexofenadine': 8.5,
        'alatrol': 3.5, 'cetirizine': 3.5, 'rupatadine': 8.5, 'rupa': 8.5,
        'ebat': 8, 'ebastine': 8, 'tusca': 85,

        // Antibiotics
        'cefotil': 55, 'clavulanic': 55, 'ceftriaxone': 180, 'cef-3': 45, 'cefixime': 45,
        'cefuroxime': 45, 'azithromycin': 35, 'zimax': 35, 'azith': 35, 'moxifloxacin': 25,
        'ciprofloxacin': 15, 'amoxicillin': 10, 'moxacil': 10,

        // Gastric / PPI
        'seclo': 6, 'omeprazole': 6, 'losectil': 6, 'sergel': 8, 'maxpro': 8,
        'esomeprazole': 8, 'pantobex': 7, 'pantonix': 7, 'pantoprazole': 7,
        'finix': 7, 'rabeprazole': 7, 'antacid': 2.5,

        // Cardiac & Diabetes
        'bizoran': 14, 'bislol': 8, 'bisoprolol': 8, 'amlodipine': 6, 'olmesartan': 10,
        'combit': 6.5, 'metformin': 5, 'glucozen': 5, 'linagliptin': 18,

        // Vitamins & Minerals
        'neuro-b': 9, 'nerv-b': 9, 'vitamin b': 9, 'calbo': 7, 'calcium': 7, 'd-rise': 25
      };

      const bnMap = { '০':'0', '১':'1', '২':'2', '৩':'3', '৪':'4', '৫':'5', '৬':'6', '৭':'7', '৮':'8', '৯':'9' };
      const toEn = (s) => (s || '').replace(/[০-৯]/g, d => bnMap[d] || d);

      let grandTotal = 0;
      const items = (this.currentResult.medicines || []).map(med => {
        const name = med.name || 'ওষুধ';
        const nameLower = name.toLowerCase();
        const genericLower = (med.generic || '').toLowerCase();
        let uPrice = med.unit_price_approx || null;

        if (!uPrice) {
          for (const [k, p] of Object.entries(knownPrices)) {
            if (nameLower.includes(k) || genericLower.includes(k)) {
              uPrice = p;
              break;
            }
          }
        }

        if (!uPrice) {
          if (nameLower.includes('syp') || (med.type && med.type.includes('সিরাপ'))) {
            uPrice = 85;
          } else if (nameLower.includes('drop') || (med.type && med.type.includes('ড্রপ'))) {
            uPrice = 65;
          } else if (nameLower.includes('inj') || (med.type && med.type.includes('ইনজেকশন'))) {
            uPrice = 120;
          } else if (nameLower.includes('500') || (med.purpose_bn && med.purpose_bn.includes('অ্যান্টিবায়োটিক'))) {
            uPrice = 45;
          } else {
            uPrice = 5;
          }
        }

        // Daily qty from dosage (Bengali numerals handled!)
        const dosageEn = toEn(med.dosage_bn);
        let daily = 2;
        const match = dosageEn.match(/(\d+)\s*\+\s*(\d+)\s*\+\s*(\d+)/);
        if (match) {
          daily = (parseInt(match[1]) || 0) + (parseInt(match[2]) || 0) + (parseInt(match[3]) || 0) || 1;
        } else {
          const match2 = dosageEn.match(/(\d+)\s*\+\s*(\d+)/);
          if (match2) {
            daily = (parseInt(match2[1]) || 0) + (parseInt(match2[2]) || 0) || 1;
          } else if (dosageEn.includes('3') || dosageEn.includes('৩')) {
            daily = 3;
          }
        }

        // Days from duration (Bengali numerals handled!)
        const durationEn = toEn(med.duration_bn);
        let days = 7;
        const dMatch = durationEn.match(/(\d+)\s*(?:দিন|day|d)/i);
        if (dMatch) {
          days = parseInt(dMatch[1]) || 7;
        } else if (durationEn.includes('মাস') || durationEn.includes('month')) {
          days = 30;
        } else if (durationEn.includes('চলমান') || durationEn.includes('নিয়মিত') || durationEn.includes('continue')) {
          days = 30;
        }

        const isSyrup = (med.type && med.type.includes('সিরাপ')) || nameLower.includes('syp');
        const qty = isSyrup ? 1 : Math.max(1, daily * days);
        const lineTotal = Math.round(uPrice * qty);
        grandTotal += lineTotal;

        return {
          name: name,
          unit_price: Math.round(uPrice),
          unit_price_note: isSyrup ? `প্রতি বোতল ~৳${uPrice}` : `প্রতি পিস ~৳${uPrice}`,
          estimated_qty_text: isSyrup ? '১ বোতল/ফাইল' : `${qty}টি ${med.type || 'ট্যাবলেট'}`,
          duration_text: med.duration_bn || `${days} দিন`,
          total_cost: lineTotal
        };
      });

      const minTotal = Math.round(grandTotal * 0.9);
      const maxTotal = Math.round(grandTotal * 1.15);

      return {
        total_estimate: grandTotal,
        min_total: minTotal,
        max_total: maxTotal,
        formatted_range: `৳ ${minTotal} – ${maxTotal} টাকা`,
        items: items
      };
    },
    todayDate() {
      return new Date().toLocaleDateString('bn-BD', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    },
    activePrintPrescription() {
      if (!this.currentResult) return {};
      const res = this.currentResult;
      
      let pName = res.patient_name || 'রোগীর নাম';
      let pAgeGender = 'বয়স উল্লেখ নেই';
      if (pName.includes('(')) {
        const parts = pName.split('(');
        pName = parts[0].trim();
        pAgeGender = parts[1].replace(')', '').trim();
      }

      const mappedMeds = (res.medicines || []).map(m => ({
        name: m.name || '',
        dosage: m.dosage_bn || m.default_dosage || '১ + ০ + ১',
        duration: m.duration_bn || m.duration || '৭ দিন',
        instruction: [m.timing_bn, m.instructions_bn].filter(Boolean).join(' • ')
      }));

      const mappedTests = (res.tests || []).map(t => {
        if (typeof t === 'string') return t;
        return t.name ? (t.name + (t.purpose_bn ? ' (' + t.purpose_bn + ')' : '')) : '';
      }).filter(Boolean);

      let adviceText = '';
      if (Array.isArray(res.advice)) {
        adviceText = res.advice.join('\n• ');
        if (adviceText) adviceText = '• ' + adviceText;
      } else {
        adviceText = res.advice || '';
      }

      return {
        id: res.id || 'AI-' + Date.now(),
        prescription_number: 'RX-' + (res.date ? res.date.replace(/[^a-zA-Z0-9]/g, '') : 'AI-001'),
        patient_name: pName,
        patient_age_gender: pAgeGender,
        created_at: res.date || this.todayDate,
        diagnosis: res.title || 'শারীরিক অসুস্থতা ও ক্লিনিকাল পরীক্ষণ',
        medicines: mappedMeds,
        medical_tests: mappedTests,
        advice: adviceText,
        follow_up_date: null
      };
    },
    activePrintDoctor() {
      if (!this.currentResult) return {};
      const res = this.currentResult;
      
      let docName = res.doctor_name || 'বিশেষজ্ঞ চিকিৎসক';
      let qualifications = 'এমবিবিএস, বিসিএস, স্পেশালিস্ট কনসালট্যান্ট';
      if (docName.includes('(')) {
        const parts = docName.split('(');
        docName = parts[0].trim();
        qualifications = parts[1].replace(')', '').trim();
      }

      return {
        name: docName,
        qualifications: qualifications,
        designation: res.specialty || 'মেডিসিন ও হৃদরোগ বিশেষজ্ঞ',
        specialization: res.specialty || 'স্পেশালিস্ট কনসালট্যান্ট',
        chamber_address: res.hospital_name || 'চেম্বার ও ক্লিনিকাল কনসালটেশন সেন্টার',
        bmdc_number: 'BMDC নিবন্ধিত',
        phone: '০৯৬১ ০০০ ৯৮২০'
      };
    }
  },
  mounted() {
    // If route query has sample_id, auto-load it
    if (this.$route.query.sample_id) {
      this.loadSample(this.$route.query.sample_id);
    }
  },
  beforeUnmount() {
    this.stopVoiceAudio();
  },
  methods: {
    triggerFileInput() {
      if (this.$refs.fileInput) {
        this.$refs.fileInput.click();
      }
    },
    handleFileChange(e) {
      const file = e.target.files && e.target.files[0];
      if (file) {
        this.processSelectedFile(file);
      }
    },
    handleFileDrop(e) {
      this.dragActive = false;
      const file = e.dataTransfer.files && e.dataTransfer.files[0];
      if (file) {
        this.processSelectedFile(file);
      }
    },
    processSelectedFile(file) {
      this.errorMessage = '';
      this.selectedFile = file;
      this.activeSampleId = null;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.previewUrl = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    clearImage() {
      this.selectedFile = null;
      this.previewUrl = '';
      this.errorMessage = '';
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = '';
      }
    },
    async loadSample(sampleId) {
      this.errorMessage = '';
      this.activeSampleId = sampleId;
      this.isAnalyzing = true;
      this.analysisProgress = 30;
      this.analysisStepText = 'নমুনা প্রেসক্রিপশন লোড করা হচ্ছে...';
      this.stopVoiceAudio();

      try {
        const res = await axios.post('/ai/explain-prescription', {
          sample_id: sampleId
        });
        if (res.data && res.data.success) {
          this.currentResult = res.data.prescription;
          this.$nextTick(() => {
            const el = document.getElementById('printablePrescriptionArea');
            if (el && window.innerWidth < 1024) {
              el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          });
        }
      } catch (err) {
        console.error('Failed to load sample prescription', err);
        this.errorMessage = 'নমুনা প্রেসক্রিপশন লোড করা সম্ভব হয়নি।';
      } finally {
        this.isAnalyzing = false;
      }
    },
    async submitPrescription() {
      if (this.isAnalyzing) return;
      this.errorMessage = '';
      this.stopVoiceAudio();
      this.isAnalyzing = true;
      this.analysisProgress = 15;
      this.analysisStepText = 'প্রেসক্রিপশন প্রসেস করা হচ্ছে...';

      // Simulation steps for great user feedback
      const stepInterval = setInterval(() => {
        if (this.analysisProgress < 85) {
          this.analysisProgress += 20;
          if (this.analysisProgress === 35) {
            this.analysisStepText = 'ডাক্তারের হাতের লেখা ও ওষুধের নাম স্ক্যান হচ্ছে...';
          } else if (this.analysisProgress === 55) {
            this.analysisStepText = 'খাওয়ার নিয়ম ও ডোজের বাংলা অনুবাদ প্রস্তুত হচ্ছে...';
          } else if (this.analysisProgress === 75) {
            this.analysisStepText = 'ল্যাব টেস্ট ও ডায়েট চার্ট তৈরি হচ্ছে...';
          }
        }
      }, 500);

      try {
        const formData = new FormData();
        if (this.inputMode === 'image' && this.selectedFile) {
          formData.append('image', this.selectedFile);
          if (this.extraNotes) {
            formData.append('text', this.extraNotes);
          }
        } else if (this.inputMode === 'text' && this.manualText) {
          formData.append('text', this.manualText);
        } else if (this.activeSampleId) {
          formData.append('sample_id', this.activeSampleId);
        }

        const res = await axios.post('/ai/explain-prescription', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        clearInterval(stepInterval);
        this.analysisProgress = 100;

        if (res.data && res.data.success) {
          this.currentResult = res.data.prescription;
          this.$nextTick(() => {
            const el = document.getElementById('printablePrescriptionArea');
            if (el && window.innerWidth < 1024) {
              el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          });
        } else {
          this.errorMessage = res.data?.message || 'প্রেসক্রিপশন বিশ্লেষণ করা সম্ভব হয়নি।';
        }
      } catch (err) {
        clearInterval(stepInterval);
        console.error('Prescription analysis error:', err);
        let msg = err.response?.data?.message || 'প্রেসক্রিপশন বিশ্লেষণ করতে সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন বা ছবি স্পষ্ট করুন।';
        if (msg.includes('Maximum execution time') || msg.includes('timeout') || msg.includes('504')) {
          msg = 'এআই সার্ভারে ট্রাফিকের কারণে রেসপন্স পেতে সামান্য বিলম্ব হয়েছে। অনুগ্রহ করে আরেকবার "সহজ বাংলায় বিশ্লেষণ করুন" বাটনে ক্লিক করুন।';
        }
        this.errorMessage = msg;
      } finally {
        this.isAnalyzing = false;
      }
    },
    toggleVoiceAudio() {
      if (this.isSpeaking) {
        this.stopVoiceAudio();
      } else {
        this.playVoiceAudio();
      }
    },
    playVoiceAudio() {
      if (!this.currentResult) return;
      const textToSpeak = this.currentResult.voice_script || 
        (this.currentResult.medicines || []).map(m => `${m.name}, ${m.purpose_bn}. খাওয়ার নিয়ম: ${m.dosage_bn}, ${m.timing_bn}`).join('। ');

      if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(textToSpeak);
        utterance.lang = 'bn-BD';
        utterance.rate = 0.95;
        utterance.pitch = 1.0;

        // Try to pick a Bengali voice if available in browser
        const voices = window.speechSynthesis.getVoices();
        const bnVoice = voices.find(v => v.lang.includes('bn') || v.name.toLowerCase().includes('bangla') || v.name.toLowerCase().includes('bengali'));
        if (bnVoice) {
          utterance.voice = bnVoice;
        }

        utterance.onstart = () => {
          this.isSpeaking = true;
        };
        utterance.onend = () => {
          this.isSpeaking = false;
        };
        utterance.onerror = () => {
          this.isSpeaking = false;
        };

        this.speechUtterance = utterance;
        window.speechSynthesis.speak(utterance);
      } else {
        // Fallback to backend TTS if browser doesn't support Web Speech
        const audio = new Audio(`/api/ai/tts?text=${encodeURIComponent(textToSpeak.slice(0, 150))}`);
        audio.play();
        this.isSpeaking = true;
        audio.onended = () => {
          this.isSpeaking = false;
        };
      }
    },
    stopVoiceAudio() {
      if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
        window.speechSynthesis.cancel();
      }
      this.isSpeaking = false;
    },
    shareOnWhatsApp() {
      if (!this.currentResult) return;
      let text = `*${this.currentResult.title || 'সুখী মানুষ বাংলা প্রেসক্রিপশন'}*\n\n`;
      text += `*চিকিৎসক:* ${this.currentResult.doctor_name || 'বিশেষজ্ঞ ডাক্তার'}\n`;
      text += `*তারিখ:* ${this.currentResult.date || this.todayDate}\n\n`;
      text += `*ঔষধের নিয়মাবলী:*\n`;
      
      (this.currentResult.medicines || []).forEach((m, idx) => {
        text += `${idx + 1}. *${m.name}* (${m.purpose_bn})\n   নিয়ম: ${m.dosage_bn} - ${m.timing_bn}\n`;
      });

      text += `\nআরও বিস্তারিত দেখুন: ${window.location.origin}/prescription-explainer`;
      window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
    },
    copyPharmacyOrderList() {
      if (!this.currentResult) return;
      let text = `🏥 প্রেসক্রিপশনের ওষুধের তালিকা (ফার্মেসি অর্ডার):\n`;
      text += `রোগী: ${this.currentResult.patient_name || 'সম্মানিত রোগী'}\n`;
      text += `তারিখ: ${this.currentResult.date || this.todayDate}\n\n`;

      (this.currentResult.medicines || []).forEach((m, idx) => {
        text += `${idx + 1}. ${m.name}\n`;
        text += `   নিয়ম: ${m.dosage_bn || ''} (${m.timing_bn || ''}) • মেয়াদ: ${m.duration_bn || '৭ দিন'}\n`;
      });

      if (this.computedPricingSummary && this.computedPricingSummary.formatted_range) {
        text += `\n💰 আনুমানিক ফার্মেসি বাজেট: ${this.computedPricingSummary.formatted_range}\n`;
      }

      navigator.clipboard.writeText(text).then(() => {
        this.isCopiedPharmacyList = true;
        setTimeout(() => {
          this.isCopiedPharmacyList = false;
        }, 2500);
      }).catch(() => {
        // fallback prompt
      });
    },
    toggleEditDuration(idx) {
      this.editingDurationIdx = this.editingDurationIdx === idx ? null : idx;
    },
    updateMedicineDuration(idx, newDuration) {
      if (this.currentResult && this.currentResult.medicines && this.currentResult.medicines[idx]) {
        this.currentResult.medicines[idx].duration_bn = newDuration;
        this.editingDurationIdx = null;
      }
    },
    printPrescription() {
      this.showPrintModal = true;
    }
  }
};
</script>

<style scoped>
@keyframes scanLine {
  0% { top: 0%; opacity: 0.8; }
  50% { top: 95%; opacity: 1; }
  100% { top: 0%; opacity: 0.8; }
}
.animate-scan-line {
  animation: scanLine 2s ease-in-out infinite;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.35s ease-out forwards;
}
</style>
