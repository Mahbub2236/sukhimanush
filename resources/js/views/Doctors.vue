<template>
  <div class="doctors-directory-page">
    <!-- Page Header Banner -->
    <section class="page-title bg-1 py-4 py-md-5 text-white position-relative" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(34, 58, 102, 0.82) 100%), url('/images/bg/22.jpg') center/cover no-repeat;">
      <div class="overlay"></div>
      <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative" style="z-index: 2;">
        <div class="row">
          <div class="col-md-12 text-center text-white">
            <div class="block">
              <span class="badge badge-danger px-3 py-1 font-weight-bold mb-2" style="font-size: 12px;">বাংলাদেশ ডিজিটাল স্বাস্থ্য সেবা</span>
              <h1 class="text-capitalize mb-2 text-2xl md:text-3xl font-weight-bold text-white" style="color: #ffffff !important;">BMDC ভেরিফাইড বিশেষজ্ঞ ডাক্তার ও চেম্বার</h1>
              <p class="text-light small max-w-600 mx-auto mb-0" style="color: #e2e8f0 !important;">
                বিভাগ, ৬৪ জেলা ও উপজেলাভিত্তিক বিশেষজ্ঞ ডাক্তার খুঁজুন। চেম্বারে সিরিয়াল নিন অথবা ঘরে বসেই অনলাইন ভিডিও কনসালটেশন নিন।
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Doctors Filter & Listing Section -->
    <section class="section doctors gray-bg pt-4 pb-5">
      <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Quick Mode Filter Badges (Telemedicine vs Chamber vs Emergency) -->
        <div class="d-flex flex-wrap align-items-center justify-content-center mb-4 filter-pills" style="gap: 8px;">
          <button 
            class="pill-btn" 
            :class="{ 'active': activeModeFilter === 'all' }" 
            @click="activeModeFilter = 'all'"
          >
            সব ডাক্তার ({{ doctors.length }})
          </button>
          <button 
            class="pill-btn" 
            :class="{ 'active': activeModeFilter === 'telemedicine' }" 
            @click="activeModeFilter = 'telemedicine'"
          >
            🌐 অনলাইন ভিডিও কনসালটেশন (টেলিমেডিসিন)
          </button>
          <button 
            class="pill-btn" 
            :class="{ 'active': activeModeFilter === 'verified' }" 
            @click="activeModeFilter = 'verified'"
          >
            ✅ BMDC ভেরিফাইড ডাক্তার
          </button>
          <button 
            class="pill-btn" 
            :class="{ 'active': activeModeFilter === 'emergency' }" 
            @click="activeModeFilter = 'emergency'"
          >
            🚨 ২৪/৭ ইমার্জেন্সি ডাক্তার
          </button>
          <button 
            class="pill-btn pill-btn-free font-weight-bold" 
            :class="{ 'active': activeModeFilter === 'free' }" 
            @click="activeModeFilter = 'free'"
            style="border-color: #28a745; color: #28a745;"
          >
            🎁 ফ্রি কনসালটেশন / শুক্রবার সেবা
          </button>
        </div>

        <!-- Voice Search & Direct Query Bar for Village / Mobile Patients -->
        <div class="voice-search-card bg-white p-3 p-md-4 rounded-lg shadow-sm mb-4 border" style="border-left: 5px solid #e12454 !important;">
          <div class="row align-items-center">
            <div class="col-lg-7 col-md-12 mb-3 mb-lg-0">
              <label class="font-weight-bold text-dark mb-1 d-flex align-items-center">
                <i class="icofont-search text-danger mr-2 font-20"></i> 
                <span>মুখে বলে বা লিখে ডাক্তার খুঁজুন (নাম, রোগ বা জেলা):</span>
              </label>
              <div class="input-group">
                <input 
                  type="text" 
                  v-model="nameSearch" 
                  data-no-voice
                  class="form-control form-control-lg font-weight-bold text-dark" 
                  placeholder="যেমনঃ রংপুর শিশু ডাক্তার, ডাঃ মেজবাহুল, গ্যাস্ট্রিক..."
                  style="border-radius: 8px 0 0 8px; font-size: 15px;"
                >
                <div class="input-group-append">
                  <button 
                    type="button" 
                    class="btn btn-danger px-3 px-md-4 font-weight-bold d-flex align-items-center shadow-sm"
                    :class="{ 'pulse-voice-btn bg-dark border-dark': isListeningDoctorSearch }"
                    @click="toggleVoiceDoctorSearch"
                    title="মাইক্রোফোনে মুখে বলে খুঁজুন"
                    style="border-radius: 0 8px 8px 0;"
                  >
                    <i class="icofont-mic font-20 mr-1"></i>
                    <span class="d-none d-sm-inline">{{ isListeningDoctorSearch ? 'শুনছি...' : 'ভয়েস সার্চ' }}</span>
                  </button>
                </div>
              </div>

              <!-- Voice Listening Feedback Alert -->
              <div v-if="isListeningDoctorSearch" class="alert alert-danger py-2 px-3 small mt-2 d-flex align-items-center rounded shadow-sm">
                <span class="spinner-grow spinner-grow-sm text-danger mr-2" role="status"></span>
                <span><strong>শুনছি...</strong> বাংলায় আপনার রোগ, ডাক্তারের নাম বা জেলার নাম বলুন...</span>
              </div>
              <div v-else-if="voiceFeedbackMessage" class="alert alert-success py-2 px-3 small mt-2 d-flex align-items-center rounded shadow-sm">
                <i class="icofont-check-circled font-18 mr-2 text-success"></i>
                <span class="font-weight-bold">{{ voiceFeedbackMessage }}</span>
              </div>
              <div v-else class="text-muted small mt-1">
                <i class="icofont-info-circle text-primary"></i> টাইপ করতে সমস্যা হলে লাল <strong>"ভয়েস সার্চ"</strong> চাপ দিয়ে বাংলায় কথা বলুন (যেমন: <em>"ডাক্তার আনিসুর রহমান"</em> বা <em>"রংপুরের শিশু ডাক্তার"</em>)।
              </div>
            </div>

            <!-- Quick Common Health Chips for Rural Patients -->
            <div class="col-lg-5 col-md-12">
              <label class="font-weight-bold text-muted small d-block mb-1">জনপ্রিয় রোগের তালিকা (ক্লিক করে ডাক্তার খুঁজুন):</label>
              <div class="d-flex flex-wrap" style="gap: 6px;">
                <button 
                  v-for="chip in quickChips" 
                  :key="chip.label" 
                  type="button"
                  class="btn btn-sm border font-weight-bold rounded-pill px-2 py-1"
                  :class="nameSearch === chip.keyword ? 'btn-danger text-white' : 'btn-light text-dark hover-primary'"
                  @click="applyQuickChip(chip)"
                >
                  {{ chip.label }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Search & Cascading Location Filter Card (Division -> District -> Upazila -> Specialization) -->
        <div class="bg-white p-3 p-md-4 rounded-lg shadow-sm mb-4 border">
          <div class="row">
            <!-- Division -->
            <div class="col-6 col-md-3 mb-2 mb-md-0">
              <label class="font-weight-bold text-dark small">🏛️ বিভাগ</label>
              <select v-model="selectedDivision" class="form-control form-control-sm" @change="onDivisionChange">
                <option value="">সকল বিভাগ</option>
                <option v-for="(districts, div) in locations" :key="div" :value="div">
                  {{ formatDivisionLabel(div) }}
                </option>
              </select>
            </div>

            <!-- 64 District Filter -->
            <div class="col-6 col-md-3 mb-2 mb-md-0">
              <label class="font-weight-bold text-dark small">📍 জেলা (64 Districts)</label>
              <select v-model="selectedDistrict" class="form-control form-control-sm" @change="onDistrictChange">
                <option value="">সকল জেলা</option>
                <option v-for="dist in availableDistricts" :key="dist" :value="dist">
                  {{ formatDistrictLabel(dist) }}
                </option>
              </select>
            </div>

            <!-- Upazila / Thana Filter -->
            <div class="col-6 col-md-3 mb-2 mb-md-0">
              <label class="font-weight-bold text-dark small">🏘️ উপজেলা / থানা</label>
              <select v-model="selectedUpazila" class="form-control form-control-sm" @change="currentPage = 1">
                <option value="">সকল উপজেলা</option>
                <option v-for="upa in availableUpazilas" :key="upa" :value="upa">{{ upa }}</option>
              </select>
            </div>

            <!-- Specialization -->
            <div class="col-6 col-md-3 mb-2 mb-md-0">
              <label class="font-weight-bold text-dark small">🩺 বিশেষজ্ঞ বিভাগ</label>
              <select v-model="selectedSpecialization" class="form-control form-control-sm font-weight-bold" @change="currentPage = 1">
                <option value="">সকল বিশেষজ্ঞ</option>
                <option 
                  v-for="spec in specializations" 
                  :key="spec.key || spec" 
                  :value="spec.key || spec"
                >
                  {{ spec.name || spec }} {{ spec.count ? `(${spec.count})` : '' }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Doctors Grid Anchor -->
        <div id="doctors-list-section"></div>

        <!-- Results Counter & Reset Filter Bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 px-1 small">
          <div class="text-muted mb-1 mb-sm-0">
            ফিল্টারে মোট <strong class="text-dark font-weight-bold">{{ filteredDoctors.length }}</strong> জন ডাক্তার পাওয়া গেছে 
            <span v-if="totalPages > 1" class="text-primary">(পৃষ্ঠা {{ currentPage }} / {{ totalPages }})</span>
          </div>
          <button 
            v-if="hasActiveFilters" 
            type="button" 
            class="btn btn-sm btn-outline-danger font-weight-bold rounded-pill px-3 py-1"
            @click="resetFilters"
          >
            <i class="icofont-close-circled mr-1"></i> ফিল্টার রিসেট করুন
          </button>
        </div>

        <!-- Doctors Grid -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
          <p class="text-muted mt-2">ডাক্তারদের তথ্য লোড হচ্ছে...</p>
        </div>

        <div v-else-if="filteredDoctors.length === 0" class="text-center py-5 text-muted bg-white rounded border">
          <i class="icofont-doctor display-3 text-muted"></i>
          <h5 class="mt-3 font-weight-bold text-dark">কোনো ডাক্তার পাওয়া যায়নি</h5>
          <p class="text-muted small">আপনার সার্চ ফিল্টারের সাথে মিল রয়েছে এমন কোনো ডাক্তার নেই।</p>
          <button class="btn btn-outline-danger btn-sm mt-2 font-weight-bold rounded-pill px-4" @click="resetFilters">ফিল্টার রিসেট করুন</button>
        </div>

        <div v-else>
          <div class="row">
            <div 
              v-for="doctor in paginatedDoctors" 
              :key="doctor.id" 
              class="col-xl-4 col-lg-4 col-md-6 mb-4"
            >
              <div 
                class="modern-doctor-card bg-white h-100 d-flex flex-column justify-content-between position-relative"
                :class="{
                  'is-vip-plan': doctor.membership_plan === 'vip',
                  'is-pro-plan': doctor.membership_plan === 'pro' || doctor.is_pro
                }"
              >
                <!-- Card Body (Top Information) -->
                <div>
                  <!-- Category Pill & VIP/PRO Badges Row -->
                  <div class="d-flex align-items-center justify-content-between mb-3" style="gap: 8px;">
                    <span class="modern-spec-pill text-truncate" :title="doctor.specialization">
                      <i class="fa-solid fa-stethoscope mr-1 text-primary"></i>
                      <span>{{ doctor.specialization }}</span>
                    </span>

                    <div class="d-flex align-items-center flex-shrink-0" style="gap: 6px;">
                      <span v-if="doctor.membership_plan === 'vip'" class="modern-vip-pill" title="ভিআইপি বিশেষজ্ঞ">
                        <i class="fa-solid fa-crown mr-1"></i> VIP
                      </span>
                      <span v-else-if="doctor.membership_plan === 'pro' || doctor.is_pro" class="modern-pro-pill" title="ভেরিফাইড প্রো">
                        <i class="fa-solid fa-certificate mr-1"></i> PRO
                      </span>
                      <span v-if="doctor.distance_km" class="modern-distance-pill" :title="'দূরত্ব: ' + doctor.distance_text">
                        <i class="fa-solid fa-location-arrow mr-1 text-danger"></i> {{ doctor.distance_text }}
                      </span>
                    </div>
                  </div>

                  <!-- Doctor Profile Header (Avatar + Name & Designation) -->
                  <div class="d-flex align-items-start mb-3" style="gap: 14px;">
                    <router-link :to="'/doctor/' + doctor.slug" class="position-relative flex-shrink-0 d-block">
                      <div class="modern-avatar-wrapper">
                        <img 
                          :src="getDoctorAvatar(doctor)" 
                          :alt="doctor.name" 
                          @error="onAvatarError($event, doctor)"
                          class="modern-avatar-img"
                        >
                        <!-- BMDC Verified Tick Badge -->
                        <span 
                          v-if="doctor.is_verified" 
                          class="avatar-verified-badge" 
                          title="BMDC ভেরিফাইড চিকিৎসক"
                        >
                          <i class="fa-solid fa-check"></i>
                        </span>
                      </div>
                    </router-link>

                    <div class="min-w-0 flex-grow-1">
                      <h5 class="modern-doc-name mb-1" :title="doctor.name">
                        <router-link :to="'/doctor/' + doctor.slug" class="text-decoration-none">
                          {{ doctor.name }}
                        </router-link>
                      </h5>
                      <p class="modern-doc-designation mb-1 text-truncate" :title="doctor.designation">
                        {{ doctor.designation || 'বিশেষজ্ঞ চিকিৎসক' }}
                      </p>
                      <p class="modern-doc-qualifications mb-1 text-truncate" :title="doctor.qualifications">
                        {{ doctor.qualifications }}
                      </p>
                      <span v-if="doctor.bmdc_number" class="modern-bmdc-tag">
                        BMDC Reg: {{ doctor.bmdc_number }}
                      </span>
                    </div>
                  </div>

                  <!-- Middle Info Box: Chamber, Location, Fee & Experience -->
                  <div class="modern-info-panel mb-3">
                    <!-- Chamber Address -->
                    <div class="d-flex align-items-start mb-2" :title="doctor.chamber_address">
                      <i class="fa-solid fa-hospital info-icon-pin text-primary mt-1 mr-2 flex-shrink-0"></i>
                      <div class="min-w-0 flex-grow-1" style="font-size: 12.5px;">
                        <span class="text-dark font-weight-bold">চেম্বার: </span>
                        <span class="text-secondary text-truncate d-inline-block align-bottom" style="max-width: 82%;">
                          {{ doctor.chamber_address || 'ধানমন্ডি, ঢাকা' }}
                        </span>
                      </div>
                    </div>

                    <!-- District / Location -->
                    <div class="d-flex align-items-center mb-2.5">
                      <i class="fa-solid fa-location-dot info-icon-pin text-danger mr-2 flex-shrink-0"></i>
                      <span class="text-secondary text-truncate" style="font-size: 12px;">
                        <span v-if="doctor.upazila" class="text-dark font-weight-bold">{{ doctor.upazila }}, </span>
                        <span>{{ doctor.district || doctor.city || 'ঢাকা' }}, {{ doctor.division || 'ঢাকা' }}</span>
                      </span>
                    </div>

                    <!-- Fee & Experience Divider Row -->
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top info-highlight-row">
                      <div class="d-flex align-items-center">
                        <span class="fee-currency-symbol">৳</span>
                        <span class="fee-text-wrapper">
                          ফি: <strong class="fee-highlight">৳{{ doctor.consultation_fee }}</strong>
                        </span>
                      </div>
                      <span class="exp-badge-pill">
                        <i class="fa-solid fa-award mr-1 text-warning"></i>{{ doctor.experience_years }}+ বছর অভিজ্ঞতা
                      </span>
                    </div>

                    <!-- Free Consultation Notice Strip (If Active) -->
                    <div v-if="doctor.free_consultation_enabled" class="modern-free-strip mt-2">
                      <i class="fa-solid fa-gift mr-1.5 text-success"></i>
                      <span class="text-truncate">ফ্রি কনসালটেশন: <strong>{{ (doctor.free_days && doctor.free_days.length) ? doctor.free_days.join(', ') : 'শুক্রবার' }}</strong></span>
                      <span v-if="doctor.free_patient_limit" class="badge-free-counter ml-auto flex-shrink-0">({{ doctor.free_patient_limit }} জন)</span>
                    </div>
                  </div>
                </div>

                <!-- Card Bottom Actions & Contacts -->
                <div class="modern-card-bottom pt-2">
                  <!-- Quick Contact Strip -->
                  <div class="d-flex align-items-center justify-content-between mb-2.5 px-0.5">
                    <span class="quick-contact-label">সরাসরি যোগাযোগ:</span>
                    <div class="d-flex align-items-center" style="gap: 7px;">
                      <button 
                        type="button" 
                        class="modern-contact-btn btn-wa" 
                        title="হোয়াটসঅ্যাপে বুকিং বা মেসেজ পাঠান" 
                        @click="openWhatsApp(doctor)"
                      >
                        <i class="fa-brands fa-whatsapp"></i>
                      </button>
                      <button 
                        type="button" 
                        class="modern-contact-btn btn-msgr" 
                        title="মেসেঞ্জারে শেয়ার করুন" 
                        @click="shareMessenger(doctor)"
                      >
                        <i class="fa-brands fa-facebook-messenger"></i>
                      </button>
                      <a 
                        v-if="doctor.phone" 
                        :href="'tel:' + doctor.phone" 
                        class="modern-contact-btn btn-tel" 
                        :title="'সরাসরি ফোন করুন: ' + doctor.phone"
                      >
                        <i class="fa-solid fa-phone"></i>
                      </a>
                      <button 
                        type="button" 
                        class="modern-contact-btn btn-copy" 
                        title="ডাক্তারের লিংক কপি করুন" 
                        @click="copyDoctorLink(doctor)"
                      >
                        <i class="fa-regular fa-copy"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Primary Action Buttons Row -->
                  <div class="modern-action-row d-flex align-items-center" style="gap: 8px;">
                    <router-link 
                      :to="'/doctor/' + doctor.slug" 
                      class="btn-modern-action btn-modern-profile flex-grow-1"
                    >
                      <i class="fa-regular fa-user mr-1.5"></i>
                      <span>প্রোফাইল</span>
                    </router-link>

                    <router-link 
                      :to="'/appoinment?doctor_id=' + doctor.id + '&type=in_chamber'" 
                      class="btn-modern-action btn-modern-serial flex-grow-1"
                    >
                      <i class="fa-regular fa-calendar-check mr-1.5"></i>
                      <span>সিরিয়াল নিন</span>
                    </router-link>

                    <router-link 
                      :to="'/appoinment?doctor_id=' + doctor.id + '&type=online_video'" 
                      class="btn-modern-action btn-modern-video flex-shrink-0"
                      title="অনলাইন ভিডিও কনসালটেশন নিন"
                    >
                      <i class="fa-solid fa-video"></i>
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination Bar (মোবাইলে দ্রুত লোডিং ও সহজ নেভিগেশন) -->
          <div v-if="totalPages > 1" class="pagination-bar bg-white p-3 rounded-lg border shadow-sm my-4 d-flex flex-column flex-md-row justify-content-between align-items-center" style="gap: 12px;">
            <div class="small text-muted text-center text-md-left">
              মোট <strong class="text-dark font-weight-bold">{{ filteredDoctors.length }}</strong> জনের মধ্যে 
              <strong>{{ (currentPage - 1) * perPage + 1 }} - {{ Math.min(currentPage * perPage, filteredDoctors.length) }}</strong> নম্বর ডাক্তার দেখানো হচ্ছে
            </div>

            <div class="d-flex align-items-center flex-wrap justify-content-center" style="gap: 5px;">
              <button 
                class="btn btn-sm btn-outline-secondary font-weight-bold px-3 py-1 rounded-pill shadow-sm" 
                :disabled="currentPage === 1" 
                @click="goToPage(currentPage - 1)"
              >
                &larr; পূর্ববর্তী
              </button>

              <button 
                v-for="p in visiblePages" 
                :key="p"
                class="btn btn-sm font-weight-bold rounded-circle shadow-sm"
                :class="currentPage === p ? 'btn-danger text-white' : 'btn-light border text-dark'"
                style="width: 35px; height: 35px; padding: 0;"
                @click="goToPage(p)"
              >
                {{ p }}
              </button>

              <button 
                class="btn btn-sm btn-outline-secondary font-weight-bold px-3 py-1 rounded-pill shadow-sm" 
                :disabled="currentPage === totalPages" 
                @click="goToPage(currentPage + 1)"
              >
                পরবর্তী &rarr;
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
import { startVoiceRecognition, isSpeechSupported } from '../utils/voiceInput';

export default {
  name: 'Doctors',
  data() {
    return {
      doctors: [],
      specializations: [],
      locations: {},
      selectedSpecialization: '',
      selectedDivision: '',
      selectedDistrict: '',
      selectedUpazila: '',
      activeModeFilter: 'all',
      nameSearch: '',
      loading: true,
      patientCoords: null,
      currentPage: 1,
      perPage: 12,
      isListeningDoctorSearch: false,
      voiceFeedbackMessage: '',
      quickChips: [
        { label: '👶 শিশু বিশেষজ্ঞ', keyword: 'শিশু' },
        { label: '🤰 গাইনী ও প্রসূতি', keyword: 'গাইনী' },
        { label: '❤️ হৃদরোগ / হার্ট', keyword: 'হৃদরোগ' },
        { label: '🦴 হাড় ও বাতব্যথা', keyword: 'অর্থোপেডিক' },
        { label: '👁️ চক্ষু বিশেষজ্ঞ', keyword: 'চক্ষু' },
        { label: '💧 কিডনি ও মূত্র', keyword: 'ইউরোলজি' },
        { label: '🧠 ব্রেন ও স্নায়ু', keyword: 'নিউরো' },
        { label: '🦷 ডেন্টাল / দাঁত', keyword: 'ডেন্টাল' },
        { label: '💊 মেডিসিন', keyword: 'মেডিসিন' },
        { label: '🩸 চর্ম ও যৌন', keyword: 'চর্ম' },
        { label: '👂 নাক কান গলা', keyword: 'নাক' },
        { label: '🍔 গ্যাস্ট্রো ও লিভার', keyword: 'গ্যাস্ট্রো' },
        { label: '🩺 ডায়াবেটিস ও হরমোন', keyword: 'ডায়াবেটিস' },
        { label: '🫁 বক্ষব্যাধি ও ফুসফুস', keyword: 'বক্ষ' },
        { label: '🧠 মানসিক রোগ', keyword: 'মানসিক' },
        { label: '🔪 জেনারেল সার্জারি', keyword: 'সার্জারি' },
        { label: '🎗️ ক্যান্সার', keyword: 'ক্যান্সার' },
        { label: '🥗 পুষ্টিবিদ ও ডায়েট', keyword: 'পুষ্টি' },
      ],
      divisionBanglaMap: {
        'Dhaka': 'ঢাকা',
        'Chittagong': 'চট্টগ্রাম',
        'Rajshahi': 'রাজশাহী',
        'Khulna': 'খুলনা',
        'Sylhet': 'সিলেট',
        'Barisal': 'বরিশাল',
        'Rangpur': 'রংপুর',
        'Mymensingh': 'ময়মনসিংহ',
      },
      districtBanglaMap: {
        'Bagerhat': 'বাগেরহাট',
        'Bandarban': 'বান্দরবান',
        'Barguna': 'বরগুনা',
        'Barisal': 'বরিশাল',
        'Bhola': 'ভোলা',
        'Bogra': 'বগুড়া',
        'Brahmanbaria': 'ব্রাহ্মণবাড়িয়া',
        'Chandpur': 'চাঁদপুর',
        'Chapai Nawabganj': 'চাঁপাইনবাবগঞ্জ',
        'Chittagong': 'চট্টগ্রাম',
        'Chuadanga': 'চুয়াডাঙ্গা',
        'Cox\'s Bazar': 'কক্সবাজার',
        'Cumilla': 'কুমিল্লা',
        'Dhaka': 'ঢাকা',
        'Dinajpur': 'দিনাজপুর',
        'Faridpur': 'ফরিদপুর',
        'Feni': 'ফেনী',
        'Gaibandha': 'গাইবান্ধা',
        'Gazipur': 'গাজীপুর',
        'Gopalganj': 'গোপালগঞ্জ',
        'Habiganj': 'হবিগঞ্জ',
        'Jamalpur': 'জামালপুর',
        'Jashore': 'যশোর',
        'Jhalokati': 'ঝালকাঠি',
        'Jhenaidah': 'ঝিনাইদহ',
        'Joypurhat': 'জয়পুরহাট',
        'Khagrachhari': 'খাগড়াছড়ি',
        'Khulna': 'খুলনা',
        'Kishoreganj': 'কিশোরগঞ্জ',
        'Kurigram': 'কুড়িগ্রাম',
        'Kushtia': 'কুষ্টিয়া',
        'Lakshmipur': 'লক্ষ্মীপুর',
        'Lalmonirhat': 'লালমনিরহাট',
        'Madaripur': 'মাদারীপুর',
        'Magura': 'মাগুরা',
        'Manikganj': 'মানিকগঞ্জ',
        'Meherpur': 'মেহেরপুর',
        'Moulvibazar': 'মৌলভীবাজার',
        'Munshiganj': 'মুন্সীগঞ্জ',
        'Mymensingh': 'ময়মনসিংহ',
        'Naogaon': 'নওগাঁ',
        'Narail': 'নড়াইল',
        'Narayanganj': 'নারায়ণগঞ্জ',
        'Narsingdi': 'নরসিংদী',
        'Natore': 'নাটোর',
        'Netrokona': 'নেত্রকোণা',
        'Nilphamari': 'নীলফামারী',
        'Noakhali': 'নোয়াখালী',
        'Pabna': 'পাবনা',
        'Panchagarh': 'পঞ্চগড়',
        'Patuakhali': 'পটুয়াখালী',
        'Pirojpur': 'পিরোজপুর',
        'Rajbari': 'রাজবাড়ী',
        'Rajshahi': 'রাজশাহী',
        'Rangamati': 'রাঙ্গামাটি',
        'Rangpur': 'রংপুর',
        'Satkhira': 'সাতক্ষীরা',
        'Shariatpur': 'শরীয়তপুর',
        'Sherpur': 'শেরপুর',
        'Sirajganj': 'সিরাজগঞ্জ',
        'Sunamganj': 'সুনামগঞ্জ',
        'Sylhet': 'সিলেট',
        'Tangail': 'টাঙ্গাইল',
        'Thakurgaon': 'ঠাকুরগাঁও',
      },
    };
  },
  computed: {
    availableDistricts() {
      if (!this.locations) return [];
      if (this.selectedDivision && this.locations[this.selectedDivision]) {
        return Object.keys(this.locations[this.selectedDivision]).sort();
      }
      // Combine all 64 districts across all divisions
      const allDistricts = [];
      Object.values(this.locations).forEach(divObj => {
        if (typeof divObj === 'object') {
          allDistricts.push(...Object.keys(divObj));
        }
      });
      return Array.from(new Set(allDistricts)).sort();
    },
    availableUpazilas() {
      if (!this.locations) return [];
      
      // If a specific district is selected, find its upazilas
      if (this.selectedDistrict) {
        if (this.selectedDivision && this.locations[this.selectedDivision]?.[this.selectedDistrict]) {
          return this.locations[this.selectedDivision][this.selectedDistrict];
        }
        for (const divKey in this.locations) {
          if (this.locations[divKey]?.[this.selectedDistrict]) {
            return this.locations[divKey][this.selectedDistrict];
          }
        }
      }

      // If only division is selected, combine all upazilas in that division
      if (this.selectedDivision && this.locations[this.selectedDivision]) {
        const upazilas = [];
        Object.values(this.locations[this.selectedDivision]).forEach(arr => {
          if (Array.isArray(arr)) upazilas.push(...arr);
        });
        return Array.from(new Set(upazilas)).sort();
      }

      return [];
    },
    filteredDoctors() {
      return this.doctors.filter(d => {
        // Specialization
        let matchesSpec = true;
        if (this.selectedSpecialization) {
          const specQuery = (this.selectedSpecialization || '').trim().toLowerCase();
          const found = Array.isArray(this.specializations)
            ? this.specializations.find(s => 
                (s.key && s.key.toLowerCase() === specQuery) || 
                (s.name && s.name.toLowerCase() === specQuery) ||
                (s.name && s.name.toLowerCase().includes(specQuery))
              )
            : null;
          if (found && found.keywords && found.keywords.length) {
            const docSpec = (d.specialization || '').toLowerCase();
            matchesSpec = found.keywords.some(kw => docSpec.includes(kw.toLowerCase()));
          } else {
            matchesSpec = d.specialization === this.selectedSpecialization || 
              (d.specialization && d.specialization.toLowerCase().includes(specQuery));
          }
        }
        
        // Division
        const matchesDivision = !this.selectedDivision || d.division === this.selectedDivision;

        // District
        const matchesDistrict = !this.selectedDistrict || 
          (d.district && d.district.toLowerCase() === this.selectedDistrict.toLowerCase()) ||
          (d.city && d.city.toLowerCase().includes(this.selectedDistrict.toLowerCase())) ||
          (d.chamber_address && d.chamber_address.toLowerCase().includes(this.selectedDistrict.toLowerCase()));

        // Upazila
        const matchesUpazila = !this.selectedUpazila ||
          (d.upazila && (d.upazila.toLowerCase().includes(this.selectedUpazila.toLowerCase()) || this.selectedUpazila.toLowerCase().includes(d.upazila.toLowerCase()))) ||
          (d.chamber_address && d.chamber_address.toLowerCase().includes(this.selectedUpazila.toLowerCase()));

        // Mode Filters (Telemedicine, Verified, Emergency, Free)
        let matchesMode = true;
        if (this.activeModeFilter === 'telemedicine') {
          matchesMode = Boolean(d.telemedicine_link);
        } else if (this.activeModeFilter === 'verified') {
          matchesMode = Boolean(d.is_verified);
        } else if (this.activeModeFilter === 'emergency') {
          matchesMode = Boolean(d.is_emergency_available);
        } else if (this.activeModeFilter === 'free') {
          matchesMode = Boolean(d.free_consultation_enabled);
        }

        // Search text with Unicode normalization, tokenized matching and dual-script (Bangla + English)
        const s = this.nameSearch.trim();
        if (!s) return matchesSpec && matchesDivision && matchesDistrict && matchesUpazila && matchesMode;

        const normS = this.normalizeBengali(s);
        const cleanS = normS
          .replace(/^(ডাক্তার|ডাঃ|ডা\.|ডক্টর|doctor|dr\.?|অধ্যাপক|প্রফেসর|prof\.?)\s+/i, '')
          .replace(/\s+(ডাক্তার|সাহেব|স্যার|এর কাছে|দেখাব|সিরিয়াল|চেম্বার)$/i, '')
          .trim();

        const targetS = cleanS || normS;
        const tokens = targetS.split(/\s+/).filter(t => t.length > 0);
        const sPhonetic = this.normalizePhonetic(s);
        const cleanSPhonetic = this.normalizePhonetic(cleanS || s);
        const phoneticTokens = cleanSPhonetic.split(/\s+/).filter(t => t.length > 0);

        // Check 1: All Bengali tokens must appear across doctor's name, specialization, or chamber
        const allTokensMatchBengali = tokens.length > 0 && tokens.every(token => 
          (d._normName && d._normName.includes(token)) ||
          (d._normSpecialization && d._normSpecialization.includes(token)) ||
          (d._normAddress && d._normAddress.includes(token)) ||
          (d.name && d.name.toLowerCase().includes(token)) ||
          (d.specialization && d.specialization.toLowerCase().includes(token))
        );

        // Check 2: All Romanized/English phonetic tokens match
        const allTokensMatchRoman = phoneticTokens.length > 0 && phoneticTokens.every(pToken =>
          (d._romanName && d._romanName.includes(pToken)) ||
          (d._cleanRomanName && d._cleanRomanName.includes(pToken)) ||
          (d.specialization && d.specialization.toLowerCase().includes(pToken)) ||
          (d.district && d.district.toLowerCase().includes(pToken)) ||
          (d.city && d.city.toLowerCase().includes(pToken))
        );

        // Check 3: Substring match fallback
        const matchesName = allTokensMatchBengali || 
          allTokensMatchRoman || 
          (d._normName && d._normName.includes(targetS)) ||
          (d.name && d.name.toLowerCase().includes(s.toLowerCase()));

        return matchesSpec && matchesDivision && matchesDistrict && matchesUpazila && matchesMode && matchesName;
      });
    },
    hasActiveFilters() {
      return Boolean(
        this.selectedSpecialization ||
        this.selectedDivision ||
        this.selectedDistrict ||
        this.selectedUpazila ||
        this.activeModeFilter !== 'all' ||
        this.nameSearch
      );
    },
    paginatedDoctors() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.filteredDoctors.slice(start, start + this.perPage);
    },
    totalPages() {
      return Math.ceil(this.filteredDoctors.length / this.perPage) || 1;
    },
    visiblePages() {
      const pages = [];
      const total = this.totalPages;
      const current = this.currentPage;
      if (total <= 5) {
        for (let i = 1; i <= total; i++) pages.push(i);
      } else {
        let start = Math.max(1, current - 2);
        let end = Math.min(total, start + 4);
        if (end - start < 4) {
          start = Math.max(1, end - 4);
        }
        for (let i = start; i <= end; i++) pages.push(i);
      }
      return pages;
    },
  },
  watch: {
    nameSearch() { this.currentPage = 1; },
    selectedSpecialization() { this.currentPage = 1; },
    selectedDivision() { this.currentPage = 1; },
    selectedDistrict() { 
      this.currentPage = 1; 
      this.loadDoctors();
    },
    selectedUpazila() { this.currentPage = 1; },
    activeModeFilter() { this.currentPage = 1; },
    '$route.query'() {
      this.applyQueryParams();
    }
  },
  async mounted() {
    this.applyQueryParams();
    this.detectPatientLocation();
  },
  methods: {
    detectPatientLocation() {
      if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
          (pos) => {
            this.patientCoords = {
              lat: pos.coords.latitude,
              lng: pos.coords.longitude,
            };
            this.loadDoctors();
          },
          () => {
            this.loadDoctors();
          },
          { timeout: 6000 }
        );
      } else {
        this.loadDoctors();
      }
    },
    async loadDoctors() {
      try {
        const params = {};
        if (this.patientCoords) {
          params.patient_lat = this.patientCoords.lat;
          params.patient_lng = this.patientCoords.lng;
        }
        if (this.selectedDistrict) {
          params.patient_district = this.selectedDistrict;
        }

        const res = await axios.get('/doctors', { params });
        const rawDoctors = res.data.doctors || [];
        this.doctors = rawDoctors.map(d => {
          d._normName = this.normalizeBengali(d.name);
          d._normSpecialization = this.normalizeBengali(d.specialization);
          d._normAddress = this.normalizeBengali(d.chamber_address);
          d._romanName = this.romanizeBangla(d.name);
          d._cleanRomanName = d._romanName.replace(/^(dr|prof|md|mohammad)\s*/gi, '').trim();
          return d;
        });
        this.specializations = res.data.specializations || [];
        this.locations = res.data.locations || {};
      } catch (err) {
        console.error('Error loading doctors:', err);
      } finally {
        this.loading = false;
      }
    },
    normalizeBengali(str) {
      if (!str) return '';
      return str
        .normalize('NFC')
        .replace(/\u09AF\u09BC/g, '\u09DF') // Decomposed য + nukta -> য়
        .replace(/\u09A1\u09BC/g, '\u09DC') // Decomposed ড + nukta -> ড়
        .replace(/\u09A2\u09BC/g, '\u09DD') // Decomposed ঢ + nukta -> ঢ়
        .replace(/\u09C7\u09BE/g, '\u09CB') // Composite O-kar
        .replace(/\u09C7\u09D7/g, '\u09CC') // Composite OU-kar
        .replace(/[\u200B-\u200D\uFEFF]/g, '') // Zero-width invisible characters
        // Homophone Normalization (হ্রস্ব ও দীর্ঘ কার সমতা: ী=ি, ূ=ু, ণ=ন, স/ষ=শ)
        .replace(/ী/g, 'ি') // Dirgho-I kar -> Roshwo-I kar
        .replace(/ঈ/g, 'ই') // Dirgho-I -> Roshwo-I
        .replace(/ূ/g, 'ু') // Dirgho-U kar -> Roshwo-U kar
        .replace(/ঊ/g, 'উ') // Dirgho-U -> Roshwo-U
        .replace(/ণ/g, 'ন') // Murdhonno-N -> Donto-N
        .replace(/ষ/g, 'শ') // Murdhonno-Sh -> Talobbo-Sh
        .replace(/স/g, 'শ') // Donto-Sh -> Talobbo-Sh
        .replace(/য/g, 'জ') // Ontostho-J -> Borgio-J
        .replace(/।/g, ' ')
        .trim()
        .toLowerCase();
    },
    romanizeBangla(text) {
      if (!text) return '';
      const map = {
        'অ': 'o', 'আ': 'a', 'ই': 'i', 'ঈ': 'i', 'উ': 'u', 'ঊ': 'u', 'ঋ': 'ri',
        'এ': 'e', 'ঐ': 'oi', 'ও': 'o', 'ঔ': 'ou',
        'ক': 'k', 'খ': 'kh', 'গ': 'g', 'ঘ': 'gh', 'ঙ': 'ng',
        'চ': 'ch', 'ছ': 'ch', 'জ': 'j', 'ঝ': 'jh', 'ঞ': 'n',
        'ট': 't', 'ঠ': 'th', 'ড': 'd', 'ঢ': 'dh', 'ণ': 'n',
        'ত': 't', 'থ': 'th', 'দ': 'd', 'ধ': 'dh', 'ন': 'n',
        'প': 'p', 'ফ': 'f', 'ব': 'b', 'ভ': 'bh', 'ম': 'm',
        'য': 'j', 'র': 'r', 'ল': 'l', 'শ': 'sh', 'ষ': 'sh', 'স': 's', 'হ': 'h',
        'ড়': 'r', 'ঢ়': 'rh', 'য়': 'y', 'ৎ': 't', 'ং': 'ng', 'ঃ': '', 'ঁ': '',
        'া': 'a', 'ি': 'i', 'ী': 'i', 'ু': 'u', 'ূ': 'u', 'ৃ': 'ri',
        'ে': 'e', 'ৈ': 'oi', 'ো': 'o', 'ৌ': 'ou', '্': ''
      };
      let result = '';
      for (let i = 0; i < text.length; i++) {
        const char = text[i];
        result += map[char] !== undefined ? map[char] : char.toLowerCase();
      }
      return this.normalizePhonetic(result);
    },
    normalizePhonetic(str) {
      if (!str) return '';
      return str
        .toLowerCase()
        .replace(/q|c/g, 'k')
        .replace(/z/g, 'j')
        .replace(/ph/g, 'f')
        .replace(/ee/g, 'i')
        .replace(/oo/g, 'u')
        .replace(/sh/g, 's')
        .replace(/bh|v/g, 'b')
        .replace(/th/g, 't')
        .replace(/dh/g, 'd')
        .replace(/kh/g, 'k')
        .replace(/gh/g, 'g')
        .replace(/[^a-z0-9\s]/g, '')
        .trim();
    },
    applyQueryParams() {
      const q = this.$route.query;
      if (q.specialization) {
        this.selectedSpecialization = q.specialization;
      }
      if (q.search) {
        this.nameSearch = q.search;
      }
      if (q.district) {
        this.selectedDistrict = q.district;
      }
      if (q.division) {
        this.selectedDivision = q.division;
      }
    },
    formatDistrictLabel(dist) {
      if (!dist) return '';
      const bn = this.districtBanglaMap[dist];
      return bn ? `${dist} (${bn})` : dist;
    },
    formatDivisionLabel(div) {
      if (!div) return '';
      const bn = this.divisionBanglaMap[div];
      return bn ? `${div} বিভাগ (${bn})` : `${div} বিভাগ`;
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
    getDoctorShareUrl(doctor) {
      const baseUrl = window.location.origin;
      return `${baseUrl}/appoinment?doctor_id=${doctor.id}`;
    },
    openWhatsApp(doctor) {
      const shareUrl = this.getDoctorShareUrl(doctor);
      const text = encodeURIComponent(`ডাঃ ${doctor.name} - ${doctor.specialization} (${doctor.chamber_address})\nসিরিয়াল ও অ্যাপয়েন্টমেন্ট নিন: ${shareUrl}`);
      
      // If doctor has phone, open direct chat, else general share
      if (doctor.phone) {
        let cleanPhone = doctor.phone.replace(/[^\d]/g, '');
        if (cleanPhone.startsWith('0')) {
          cleanPhone = '88' + cleanPhone;
        }
        window.open(`https://api.whatsapp.com/send?phone=${cleanPhone}&text=${text}`, '_blank');
      } else {
        window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
      }
    },
    shareMessenger(doctor) {
      const shareUrl = encodeURIComponent(this.getDoctorShareUrl(doctor));
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`, '_blank');
    },
    async copyDoctorLink(doctor) {
      const shareUrl = this.getDoctorShareUrl(doctor);
      try {
        if (navigator.clipboard) {
          await navigator.clipboard.writeText(shareUrl);
        } else {
          const input = document.createElement('input');
          input.value = shareUrl;
          document.body.appendChild(input);
          input.select();
          document.execCommand('copy');
          document.body.removeChild(input);
        }
        alert(`ডাঃ ${doctor.name} এর অ্যাপয়েন্টমেন্ট লিংক কপি করা হয়েছে!`);
      } catch (err) {
        console.error('Failed to copy: ', err);
      }
    },
    onDivisionChange() {
      this.selectedDistrict = '';
      this.selectedUpazila = '';
      this.currentPage = 1;
    },
    onDistrictChange() {
      this.selectedUpazila = '';
      this.currentPage = 1;
    },
    goToPage(p) {
      if (p >= 1 && p <= this.totalPages) {
        this.currentPage = p;
        const el = document.getElementById('doctors-list-section');
        if (el) {
          el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    },
    resetFilters() {
      this.selectedSpecialization = '';
      this.selectedDivision = '';
      this.selectedDistrict = '';
      this.selectedUpazila = '';
      this.activeModeFilter = 'all';
      this.nameSearch = '';
      this.currentPage = 1;
    },
    toggleVoiceDoctorSearch() {
      if (this.isListeningDoctorSearch) {
        this.isListeningDoctorSearch = false;
        return;
      }
      if (!isSpeechSupported()) {
        alert('আপনার ব্রাউজারে ভয়েস রিকগনিশন সমর্থন করে না। ক্রোম (Chrome) ব্যবহার করুন বা কিবোর্ডে লিখুন।');
        return;
      }
      startVoiceRecognition({
        lang: 'bn-BD',
        onStart: () => {
          this.isListeningDoctorSearch = true;
          this.voiceFeedbackMessage = '';
        },
        onResult: (text) => {
          this.parseSpokenMedicalQuery(text);
        },
        onError: () => {
          this.isListeningDoctorSearch = false;
        },
        onEnd: () => {
          this.isListeningDoctorSearch = false;
        }
      });
    },
    parseSpokenMedicalQuery(rawText) {
      if (!rawText) return;
      let text = this.normalizeBengali(rawText).trim();

      // 1. Check for District mentions (64 Districts)
      for (const div in this.locations) {
        for (const dist in this.locations[div]) {
          const distLower = dist.toLowerCase();
          if (text.toLowerCase().includes(distLower) || text.includes(dist)) {
            this.selectedDistrict = dist;
            this.selectedDivision = div;
            text = text.replace(new RegExp(dist, 'gi'), '').replace(new RegExp(dist + 'ের', 'gi'), '').trim();
            break;
          }
        }
      }

      // 2. Comprehensive Medical Departments & Symptom Keyword Mapping
      const departmentRules = [
        {
          key: 'cardiology',
          name: 'হৃদরোগ ও কার্ডিওলজি',
          keywords: ['হার্ট', 'হৃদরোগ', 'কার্ডিওলজি', 'কার্ডিওলজিস্ট', 'বুকে ব্যথা', 'ইসিজি', 'এনজিওগ্রাম', 'cardiology', 'heart']
        },
        {
          key: 'pediatrics',
          name: 'শিশু ও কিশোর রোগ',
          keywords: ['শিশু', 'বাচ্চা', 'নবজাতক', 'কিশোর', 'pediatric', 'pediatrics', 'child', 'পেক্স']
        },
        {
          key: 'gynecology',
          name: 'গাইনী ও প্রসূতিরোগ',
          keywords: ['গাইনী', 'স্ত্রী', 'প্রসূতি', 'মহিলা', 'গর্ভবতী', 'প্রেগন্যান্ট', 'ডেলিভারি', 'মাসিক', 'gynecology', 'obs', 'obstetrics']
        },
        {
          key: 'orthopedics',
          name: 'অর্থোপেডিক ও হাড়-জোড়া',
          keywords: ['হাড়', 'অর্থোপেডিক', 'বাত', 'কোমর ব্যথা', 'হাঁটু', 'ভাঙা', 'জয়েন্ট', 'প্লাস্টার', 'orthopedic', 'orthopaedics', 'bone']
        },
        {
          key: 'ophthalmology',
          name: 'চক্ষু বিশেষজ্ঞ',
          keywords: ['চক্ষু', 'চোখ', 'চোখের', 'দৃষ্টি', 'ছানি', 'অক্ষি', 'eye', 'ophthalmology']
        },
        {
          key: 'dental',
          name: 'ডেন্টাল ও মুখরোগ',
          keywords: ['দাঁত', 'ডেন্টাল', 'দন্ত', 'মাড়ি', 'দাঁতের', 'রুট ক্যানেল', 'dental', 'dentist', 'tooth']
        },
        {
          key: 'urology',
          name: 'কিডনি ও ইউরোলজি',
          keywords: ['ইউরোলজি', 'কিডনি', 'মূত্র', 'প্রস্রাব', 'পাথর', 'প্রস্টেট', 'urology', 'kidney', 'urinary']
        },
        {
          key: 'nephrology',
          name: 'কিডনি ও নেফ্রোলজি',
          keywords: ['নেফ্রোলজি', 'ডায়ালাইসিস', 'nephrology']
        },
        {
          key: 'neurology',
          name: 'নিউরোলজি ও ব্রেন',
          keywords: ['নিউরো', 'ব্রেন', 'মাথাব্যথা', 'স্ট্রোক', 'প্যারালাইসিস', 'স্নায়ু', 'মস্তিষ্ক', 'মাইগ্রেন', 'neurology', 'neuro']
        },
        {
          key: 'neurosurgery',
          name: 'নিউরোসার্জারি ও স্পাইন',
          keywords: ['নিউরোসার্জারি', 'মেরুদণ্ড', 'স্পাইন', 'neurosurgery']
        },
        {
          key: 'dermatology',
          name: 'চর্ম, যৌন ও অ্যালার্জি',
          keywords: ['চর্ম', 'যৌন', 'স্কিন', 'অ্যালার্জি', 'দাদ', 'চুলকানি', 'মেছতা', 'ব্রণ', 'কুষ্ঠ', 'dermatology', 'skin']
        },
        {
          key: 'ent',
          name: 'নাক, কান ও গলা (ENT)',
          keywords: ['নাক', 'কান', 'গলা', 'টনসিল', 'সাইনোসাইটিস', 'ent', 'otolaryngology', 'throat', 'ear']
        },
        {
          key: 'gastroenterology',
          name: 'গ্যাস্ট্রোএন্টারোলজি ও লিভার',
          keywords: ['গ্যাস্ট্রিক', 'গ্যাস্ট্রো', 'লিভার', 'জন্ডিস', 'পেট ব্যথা', 'আলসার', 'বমি', 'বদহজম', 'হেপাটাইটিস', 'এসিডিটি', 'gastroenterology', 'hepatology', 'liver', 'gastric']
        },
        {
          key: 'endocrinology',
          name: 'ডায়াবেটিস ও হরমোন',
          keywords: ['ডায়াবেটিস', 'ডায়াবেটিস', 'সুগার', 'হরমোন', 'থাইরয়েড', 'endocrinology', 'diabetes', 'thyroid']
        },
        {
          key: 'pulmonology',
          name: 'বক্ষব্যাধি ও ফুসফুস',
          keywords: ['বক্ষব্যাধি', 'ফুসফুস', 'কাশি', 'শ্বাসকষ্ট', 'অ্যাজমা', 'হাঁপানি', 'যক্ষ্মা', 'pulmonology', 'chest', 'asthma']
        },
        {
          key: 'psychiatry',
          name: 'মানসিক রোগ ও মনোরোগ',
          keywords: ['মানসিক', 'মনোরোগ', 'ডিপ্রেশন', 'উদ্বেগ', 'অনিদ্রা', 'মন', 'psychiatry', 'mental']
        },
        {
          key: 'surgery',
          name: 'জেনারেল সার্জারি',
          keywords: ['সার্জারি', 'অপারেশন', 'সার্জন', 'অ্যাপেন্ডিসাইটিস', 'হার্নিয়া', 'পাইলস', 'surgery', 'surgeon']
        },
        {
          key: 'cardiac_surgery',
          name: 'কার্ডিয়াক সার্জারি',
          keywords: ['কার্ডিয়াক সার্জারি', 'হার্ট সার্জারি', 'বাইপাস সার্জারি', 'cardiac surgery']
        },
        {
          key: 'pediatric_surgery',
          name: 'শিশু সার্জারি',
          keywords: ['শিশু সার্জারি', 'বাচ্চাদের অপারেশন', 'pediatric surgery']
        },
        {
          key: 'plastic_surgery',
          name: 'প্লাস্টিক ও কসমেটিক সার্জারি',
          keywords: ['প্লাস্টিক সার্জারি', 'কসমেটিক সার্জারি', 'plastic surgery', 'cosmetic']
        },
        {
          key: 'oncology',
          name: 'ক্যান্সার বিশেষজ্ঞ',
          keywords: ['ক্যান্সার', 'টিউমার', 'অঙ্কোলজি', 'oncology', 'cancer']
        },
        {
          key: 'hematology',
          name: 'রক্তরোগ বিশেষজ্ঞ',
          keywords: ['রক্তরোগ', 'থ্যালাসেমিয়া', 'রক্তশূন্যতা', 'অ্যানিমিয়া', 'hematology']
        },
        {
          key: 'physiotherapy',
          name: 'ফিজিওথেরাপি ও রিহ্যাব',
          keywords: ['ফিজিওথেরাপি', 'থেরাপি', 'রিহ্যাব', 'physiotherapy']
        },
        {
          key: 'nutrition',
          name: 'পুষ্টিবিদ ও ডায়েট',
          keywords: ['পুষ্টি', 'ডায়েট', 'ওজন কমানো', 'ডায়েটিশিয়ান', 'nutrition', 'diet']
        },
        {
          key: 'rheumatology',
          name: 'বাত ও রিউম্যাটোলজি',
          keywords: ['রিউম্যাটোলজি', 'গাঁটে ব্যথা', 'রিউম্যাটয়েড', 'rheumatology']
        },
        {
          key: 'medicine',
          name: 'মেডিসিন বিশেষজ্ঞ',
          keywords: ['মেডিসিন', 'জ্বর', 'সাধারণ রোগ', 'শারীরিক দুর্বলতা', 'medicine', 'internal medicine']
        }
      ];

      // Check if speech matches any department
      let matchedDept = null;
      const lowerText = text.toLowerCase();
      for (const dept of departmentRules) {
        for (const kw of dept.keywords) {
          if (lowerText.includes(kw.toLowerCase())) {
            matchedDept = dept;
            break;
          }
        }
        if (matchedDept) break;
      }

      if (matchedDept) {
        this.selectedSpecialization = matchedDept.key;
        this.nameSearch = '';
        this.currentPage = 1;
        this.voiceFeedbackMessage = `✅ স্বয়ংক্রিয়ভাবে "${matchedDept.name}" বিভাগ সিলেক্ট করা হয়েছে।`;
        setTimeout(() => { this.voiceFeedbackMessage = ''; }, 6000);
        return;
      }

      // 3. Otherwise, it is a doctor name! Clean prefixes & suffixes:
      let cleanedName = text
        .replace(/^(ডাক্তার|ডাঃ|ডা\.|ডক্টর|doctor|dr\.?|অধ্যাপক|প্রফেসর|prof\.?)\s+/i, '')
        .replace(/\s+(ডাক্তার|সাহেব|স্যার|এর কাছে|দেখাব|সিরিয়াল|চেম্বার)$/i, '')
        .trim();

      this.nameSearch = cleanedName;
      this.currentPage = 1;
      this.voiceFeedbackMessage = `🔍 "${cleanedName}" নামের ডাক্তার ফিল্টার করা হয়েছে।`;
      setTimeout(() => { this.voiceFeedbackMessage = ''; }, 5000);
    },
    applyQuickChip(chip) {
      if (this.nameSearch === chip.keyword) {
        this.nameSearch = '';
      } else {
        this.nameSearch = chip.keyword;
      }
    },
  },
};
</script>

<style scoped>
.doctors-directory-page .container {
  max-width: 1280px !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding-left: 1.25rem !important;
  padding-right: 1.25rem !important;
}
@media (min-width: 640px) {
  .doctors-directory-page .container {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
  }
}
@media (min-width: 1024px) {
  .doctors-directory-page .container {
    padding-left: 2rem !important;
    padding-right: 2rem !important;
  }
}

.filter-pills .pill-btn {
  background: #ffffff;
  border: 1.5px solid #cbd5e1;
  color: #334155;
  font-size: 13px;
  font-weight: 600;
  padding: 8px 16px;
  border-radius: 30px;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.03);
}
.filter-pills .pill-btn:hover {
  border-color: #e12454;
  color: #e12454;
}
.filter-pills .pill-btn.active {
  background: #e12454;
  border-color: #e12454;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(225, 36, 84, 0.25);
}

/* ========================================================
   MODERN DOCTOR CARDS (Clean, Premium, High Aesthetics)
   ======================================================== */
.modern-doctor-card {
  padding: 1.3rem;
  background: #ffffff;
  border: 1px solid rgba(226, 232, 240, 0.85) !important;
  border-radius: 20px;
  box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02) !important;
  transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-doctor-card:hover {
  transform: translateY(-5px);
  border-color: rgba(186, 230, 253, 0.9) !important;
  box-shadow: 0 20px 35px -4px rgba(15, 23, 42, 0.09), 0 8px 16px -2px rgba(2, 132, 199, 0.06) !important;
}

/* Plan Badges Modifier */
.modern-doctor-card.is-vip-plan {
  border: 1px solid rgba(251, 191, 36, 0.35) !important;
  background: #ffffff !important;
  box-shadow: 0 6px 24px -2px rgba(245, 158, 11, 0.10), 0 2px 6px -1px rgba(15, 23, 42, 0.02) !important;
}

.modern-doctor-card.is-vip-plan:hover {
  border-color: rgba(245, 158, 11, 0.6) !important;
  box-shadow: 0 22px 38px -4px rgba(245, 158, 11, 0.15), 0 8px 16px -2px rgba(245, 158, 11, 0.06) !important;
}

.modern-doctor-card.is-pro-plan {
  border: 1px solid rgba(226, 232, 240, 0.9) !important;
  background: #ffffff !important;
  box-shadow: 0 6px 24px -2px rgba(14, 165, 233, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.02) !important;
}

.modern-doctor-card.is-pro-plan:hover {
  border-color: rgba(56, 189, 248, 0.6) !important;
  box-shadow: 0 22px 38px -4px rgba(14, 165, 233, 0.14), 0 8px 16px -2px rgba(15, 23, 42, 0.05) !important;
}

/* Specialization Pill */
.modern-spec-pill {
  display: inline-flex;
  align-items: center;
  font-size: 11.5px;
  font-weight: 700;
  color: #0284c7;
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  padding: 3px 10px;
  border-radius: 20px;
  max-width: 170px;
  letter-spacing: 0.2px;
}

.modern-vip-pill {
  font-size: 10px;
  font-weight: 800;
  color: #92400e;
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  border: 1px solid #fcd34d;
  padding: 3px 8px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(245, 158, 11, 0.15);
}

.modern-pro-pill {
  font-size: 10px;
  font-weight: 800;
  color: #1e40af;
  background: #dbeafe;
  border: 1px solid #bfdbfe;
  padding: 3px 8px;
  border-radius: 12px;
}

.modern-distance-pill {
  font-size: 10px;
  font-weight: 700;
  color: #b91c1c;
  background: #fef2f2;
  border: 1px solid #fecaca;
  padding: 3px 8px;
  border-radius: 12px;
}

/* Avatar Box */
.modern-avatar-wrapper {
  position: relative;
  width: 72px;
  height: 72px;
}

.modern-avatar-img {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  object-fit: cover;
  background: #f1f5f9;
  border: 2px solid #e2e8f0;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
  transition: transform 0.3s ease;
}

.modern-doctor-card:hover .modern-avatar-img {
  transform: scale(1.03);
  border-color: #cbd5e1;
}

.avatar-verified-badge {
  position: absolute;
  bottom: -4px;
  right: -4px;
  width: 20px;
  height: 20px;
  background: #10b981;
  color: #ffffff;
  border: 2px solid #ffffff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 9px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
}

/* Doctor Name & Designation */
.modern-doc-name {
  font-size: 15.5px;
  font-weight: 800;
  line-height: 1.35;
  color: #0f172a;
}

.modern-doc-name a {
  color: #0f172a;
  transition: color 0.2s ease;
}

.modern-doc-name a:hover {
  color: #e12454;
}

.modern-doc-designation {
  font-size: 12.5px;
  font-weight: 600;
  color: #475569;
  line-height: 1.3;
}

.modern-doc-qualifications {
  font-size: 11.5px;
  color: #64748b;
  line-height: 1.3;
}

.modern-bmdc-tag {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  color: #64748b;
  background: #f1f5f9;
  padding: 1px 7px;
  border-radius: 6px;
  letter-spacing: 0.3px;
}

/* Info Panel */
.modern-info-panel {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 11px 13px;
}

.info-icon-pin {
  font-size: 12px;
}

.info-highlight-row {
  border-top-color: #e2e8f0 !important;
}

.fee-currency-symbol {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #dcfce7;
  color: #15803d;
  font-size: 11px;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.fee-text-wrapper {
  font-size: 12px;
  color: #475569;
  margin-left: 6px;
}

.fee-highlight {
  font-size: 14px;
  color: #15803d;
  font-weight: 800;
}

.exp-badge-pill {
  font-size: 11px;
  font-weight: 600;
  color: #334155;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  padding: 2px 8px;
  border-radius: 8px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

.modern-free-strip {
  background: #ecfdf5;
  border: 1px solid #a7f3d0;
  border-radius: 8px;
  padding: 5px 8px;
  font-size: 11px;
  color: #065f46;
  font-weight: 600;
  display: flex;
  align-items: center;
}

.badge-free-counter {
  font-size: 10px;
  background: #10b981;
  color: #fff;
  padding: 1px 5px;
  border-radius: 10px;
}

/* Quick Contacts */
.quick-contact-label {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
}

.modern-contact-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.modern-contact-btn:hover {
  transform: translateY(-2px);
}

.modern-contact-btn.btn-wa {
  color: #16a34a;
  background: #f0fdf4;
  border-color: #bbf7d0;
}
.modern-contact-btn.btn-wa:hover {
  background: #16a34a;
  color: #ffffff;
  border-color: #16a34a;
  box-shadow: 0 4px 10px rgba(22, 163, 74, 0.25);
}

.modern-contact-btn.btn-msgr {
  color: #2563eb;
  background: #eff6ff;
  border-color: #bfdbfe;
}
.modern-contact-btn.btn-msgr:hover {
  background: #2563eb;
  color: #ffffff;
  border-color: #2563eb;
  box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
}

.modern-contact-btn.btn-tel {
  color: #0284c7;
  background: #f0f9ff;
  border-color: #bae6fd;
}
.modern-contact-btn.btn-tel:hover {
  background: #0284c7;
  color: #ffffff;
  border-color: #0284c7;
  box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
}

.modern-contact-btn.btn-copy {
  color: #64748b;
  background: #f8fafc;
  border-color: #e2e8f0;
}
.modern-contact-btn.btn-copy:hover {
  background: #334155;
  color: #ffffff;
  border-color: #334155;
  box-shadow: 0 4px 10px rgba(51, 65, 85, 0.25);
}

/* Bottom CTA Actions */
.btn-modern-action {
  height: 40px;
  border-radius: 12px;
  font-size: 12.5px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  text-decoration: none;
}

.btn-modern-profile {
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #e2e8f0;
}

.btn-modern-profile:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.btn-modern-serial {
  background: linear-gradient(135deg, #e12454 0%, #be123c 100%);
  color: #ffffff !important;
  border: none;
  box-shadow: 0 4px 12px rgba(225, 36, 84, 0.28);
}

.btn-modern-serial:hover {
  background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
  color: #ffffff !important;
  box-shadow: 0 6px 16px rgba(225, 36, 84, 0.38);
  transform: translateY(-1px);
}

.btn-modern-video {
  width: 40px;
  background: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
  font-size: 14px;
}

.btn-modern-video:hover {
  background: #059669;
  color: #ffffff !important;
  border-color: #059669;
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
}

/* Voice Input Pulsing Animation */
.pulse-voice-btn {
  animation: pulse-microphone 1.4s infinite;
}

@keyframes pulse-microphone {
  0% {
    box-shadow: 0 0 0 0 rgba(225, 36, 84, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(225, 36, 84, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(225, 36, 84, 0);
  }
}

/* Responsive Mobile Optimizations */
@media (max-width: 767.98px) {
  .page-title h1 {
    font-size: 1.45rem !important;
    line-height: 1.35 !important;
  }
  .filter-pills {
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    justify-content: flex-start !important;
    padding-bottom: 8px !important;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
  }
  .filter-pills::-webkit-scrollbar {
    height: 4px;
  }
  .filter-pills::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .filter-pills .pill-btn {
    flex-shrink: 0 !important;
    white-space: nowrap !important;
    font-size: 12px !important;
    padding: 6px 14px !important;
  }
  .voice-search-card {
    padding: 1rem !important;
  }
  .modern-doctor-card {
    padding: 1rem !important;
    border-radius: 16px !important;
  }
  .modern-avatar-wrapper,
  .modern-avatar-img {
    width: 64px !important;
    height: 64px !important;
    border-radius: 14px !important;
  }
  .modern-doc-name {
    font-size: 14.5px !important;
  }
  .btn-modern-action {
    height: 38px !important;
    font-size: 12px !important;
  }
  .btn-modern-video {
    width: 38px !important;
  }
  .pagination-bar {
    padding: 1rem !important;
  }
  .pagination-bar button {
    min-height: 38px !important;
  }
}

@media (max-width: 400px) {
  .modern-contact-btn {
    width: 28px !important;
    height: 28px !important;
    font-size: 12px !important;
  }
}
</style>
