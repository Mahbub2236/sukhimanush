<template>
  <div class="doctor-profile-page bg-light min-vh-100 pb-5">
    <!-- Breadcrumb Header -->
    <div class="bg-white border-bottom py-2">
      <div class="container">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 bg-transparent p-0 small">
            <li class="breadcrumb-item"><router-link to="/" class="text-primary font-weight-bold"><i class="icofont-home"></i> হোম</router-link></li>
            <li class="breadcrumb-item"><router-link to="/doctors" class="text-primary font-weight-bold">সকল ডাক্তার</router-link></li>
            <li v-if="doctor && doctor.district" class="breadcrumb-item text-muted">{{ doctor.district }}</li>
            <li class="breadcrumb-item active text-dark font-weight-bold text-truncate" aria-current="page" style="max-width: 320px;">
              {{ doctor?.name || 'ডাক্তার প্রোফাইল' }}
            </li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
      <p class="text-muted mt-3 font-weight-bold">ডাক্তারের বিস্তারিত প্রোফাইল লোড হচ্ছে...</p>
    </div>

    <!-- Doctor Not Found -->
    <div v-else-if="!doctor" class="container py-5 text-center">
      <div class="bg-white p-5 rounded-lg shadow-sm border">
        <i class="icofont-doctor display-1 text-muted"></i>
        <h3 class="mt-3 font-weight-bold text-dark">দুঃখিত, ডাক্তারের প্রোফাইলটি পাওয়া যায়নি</h3>
        <p class="text-muted">ডাক্তার তালিকা থেকে অন্য কোনো বিশেষজ্ঞ ডাক্তার খুঁজুন।</p>
        <router-link to="/doctors" class="btn btn-main-2 btn-round-full px-4 mt-3">
          সকল ডাক্তারদের তালিকা দেখুন
        </router-link>
      </div>
    </div>

    <!-- Doctor Profile Main Content -->
    <div v-else class="container pt-4">
      <!-- Toast Alert for Copy Link -->
      <transition name="fade">
        <div v-if="toastMessage" class="alert alert-success position-fixed shadow-lg d-flex align-items-center" style="top: 20px; right: 20px; z-index: 9999; border-radius: 10px;">
          <i class="icofont-check-circled font-20 mr-2"></i>
          <strong>{{ toastMessage }}</strong>
        </div>
      </transition>

      <!-- 1. HERO TOP PROFILE CARD (Matching DoctorList BD Format) -->
      <div class="card border-0 shadow-sm rounded-lg overflow-hidden mb-4 bg-white">
        <!-- Top Gradient Banner -->
        <div class="hero-header-banner p-4 text-white position-relative" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #0284c7 100%);">
          <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="badge badge-light text-primary font-weight-bold px-3 py-2 rounded-pill shadow-sm mb-2 mb-md-0">
              <i class="icofont-stethoscope mr-1"></i> {{ doctor.specialization }}
            </div>
            <div class="d-flex align-items-center text-white small">
              <i class="icofont-calendar mr-1"></i> <span>সপ্তাহে {{ (doctor.available_days && doctor.available_days.length) || 6 }} দিন রোগী দেখেন</span>
            </div>
          </div>
        </div>

        <!-- Doctor Inset Information -->
        <div class="card-body p-4 pt-0 position-relative">
          <div class="row align-items-start">
            <!-- Doctor Avatar with Overlapping Offset -->
            <div class="col-lg-3 col-md-4 text-center text-md-left mb-3 mb-md-0 hero-avatar-container" style="margin-top: -55px;">
              <div class="position-relative d-inline-block shadow rounded-circle p-1 bg-white border" style="border-width: 3px !important; border-color: #ffffff !important;">
                <img 
                  :src="getDoctorAvatar(doctor)" 
                  :alt="doctor.name" 
                  @error="onAvatarError($event, doctor)"
                  class="rounded-circle hero-avatar-img" 
                  style="width: 170px; height: 170px; object-fit: cover; background: #f8fafc;"
                >
                <span 
                  class="badge badge-success position-absolute px-2 py-1 shadow" 
                  style="bottom: 8px; right: 8px; font-size: 11px; border-radius: 20px;"
                  title="BMDC রেজিস্টার্ড চিকিৎসক"
                >
                  <i class="icofont-check-circled"></i> Verified
                </span>
              </div>
            </div>

            <!-- Doctor Main Title & Specs -->
            <div class="col-lg-9 col-md-8 pt-3 text-center text-md-left">
              <!-- Rating & Tags -->
              <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start mb-2" style="gap: 8px;">
                <div class="rating-stars text-warning font-weight-bold">
                  <i class="icofont-star text-warning"></i>
                  <i class="icofont-star text-warning"></i>
                  <i class="icofont-star text-warning"></i>
                  <i class="icofont-star text-warning"></i>
                  <i class="icofont-star text-warning"></i>
                  <span class="text-dark ml-1">5.0</span>
                </div>
                <a href="#reviews-section" class="text-primary small font-weight-bold">({{ reviewCount }} টি রোগী রিভিউ)</a>
                <span class="badge badge-primary px-2 py-1" style="font-size: 11px;">স্পেশালিস্ট</span>
                <span v-if="doctor.is_emergency_available" class="badge badge-danger px-2 py-1" style="font-size: 11px;">জরুরি সেবা</span>
              </div>

              <!-- Full Name -->
              <h2 class="font-weight-bold text-dark mb-1" style="font-size: 1.75rem;">
                {{ doctor.name }}
              </h2>

              <!-- Designation & Hospital Affiliation -->
              <h5 class="text-primary font-weight-bold mb-2" style="font-size: 1.05rem;">
                {{ doctor.designation }}
              </h5>

              <!-- Highlighted Degrees / Qualifications Box (Amber theme matching DoctorList BD) -->
              <div class="qualifications-box p-3 rounded mb-3 text-left" style="background-color: #fff8e1; border: 1px solid #ffe082; color: #5d4037;">
                <div class="d-flex align-items-start">
                  <span class="font-20 mr-2" style="line-height: 1;">🎓</span>
                  <div class="font-weight-bold small" style="line-height: 1.6;">
                    {{ doctor.qualifications }}
                  </div>
                </div>
              </div>

              <!-- Location Tag -->
              <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start text-muted small mb-3" style="gap: 12px;">
                <div class="d-flex align-items-center">
                  <i class="icofont-location-pin text-danger font-18 mr-1"></i>
                  <span class="font-weight-bold text-dark">
                    <span v-if="doctor.upazila">{{ doctor.upazila }}, </span>
                    {{ doctor.district || doctor.city || 'ঢাকা' }}, {{ doctor.division || 'বাংলাদেশ' }}
                  </span>
                </div>
                <div v-if="doctor.bmdc_number" class="text-secondary">
                  <i class="icofont-badge text-primary mr-1"></i> <strong>BMDC রেজিঃ</strong> {{ doctor.bmdc_number }}
                </div>
              </div>

              <!-- Stats Highlights Row -->
              <div class="row g-2 mb-3">
                <div class="col-4">
                  <div class="bg-light p-2 rounded text-center border">
                    <div class="font-weight-bold text-primary font-18">5.0 / ৫.০</div>
                    <div class="text-muted" style="font-size: 11px;">রোগীর সন্তুষ্টি</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="bg-light p-2 rounded text-center border">
                    <div class="font-weight-bold text-success font-18">{{ doctor.experience_years || 12 }}+ বছর</div>
                    <div class="text-muted" style="font-size: 11px;">অভিজ্ঞতা</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="bg-light p-2 rounded text-center border">
                    <div class="font-weight-bold text-dark font-18">১,৫০০+</div>
                    <div class="text-muted" style="font-size: 11px;">রোগী দেখেছেন</div>
                  </div>
                </div>
              </div>

              <!-- Green Verified Pill -->
              <div class="alert alert-success py-2 px-3 mb-3 d-inline-flex align-items-center small font-weight-bold" style="border-radius: 8px;">
                <i class="icofont-check-circled font-18 text-success mr-2"></i>
                ভেরিফাইড প্রোফাইল (বাংলাদেশ মেডিকেল অ্যান্ড ডেন্টাল কাউন্সিল রেজিস্টার্ড চিকিৎসক)
              </div>

              <!-- Share Now Buttons Bar (Matching DoctorList BD Format) -->
              <div class="share-action-bar bg-light p-3 rounded border">
                <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                  <span class="font-weight-bold text-dark mr-2 small share-title"><i class="icofont-share mr-1"></i> শেয়ার ও যোগাযোগ:</span>
                  
                  <button type="button" class="btn btn-sm btn-dark font-weight-bold px-3 py-2 shadow-sm rounded" @click="copyDoctorLink">
                    <i class="icofont-ui-copy mr-1"></i> Copy Link
                  </button>

                  <button type="button" class="btn btn-sm text-white font-weight-bold px-3 py-2 shadow-sm rounded" style="background: #0084ff; border-color: #0084ff;" @click="shareMessenger">
                    <i class="icofont-facebook-messenger mr-1"></i> Messenger
                  </button>

                  <button type="button" class="btn btn-sm text-white font-weight-bold px-3 py-2 shadow-sm rounded" style="background: #25d366; border-color: #25d366;" @click="openWhatsApp">
                    <i class="icofont-brand-whatsapp mr-1"></i> WhatsApp
                  </button>

                  <a v-if="doctor.phone" :href="'tel:' + doctor.phone" class="btn btn-sm text-white font-weight-bold px-3 py-2 shadow-sm rounded" style="background: #0284c7; border-color: #0284c7;">
                    <i class="icofont-phone mr-1"></i> সরাসরি কল
                  </a>

                  <button type="button" class="btn btn-sm text-white font-weight-bold px-3 py-2 shadow-sm rounded" style="background: #1877f2; border-color: #1877f2;" @click="shareFacebook">
                    <i class="icofont-facebook mr-1"></i> Facebook
                  </button>

                  <router-link 
                    :to="'/dr/' + (doctor.slug || doctor.id)" 
                    class="btn btn-sm text-white font-weight-bold px-3 py-2 shadow-sm rounded ml-auto d-inline-flex align-items-center"
                    style="background: linear-gradient(135deg, #10b981, #059669); border: none;"
                  >
                    <i class="icofont-globe mr-1.5"></i> পার্সোনাল পোর্টফোলিও ওয়েবসাইট &rarr;
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- MAIN BODY: 2 COLUMNS (Left 8 cols, Right 4 cols) -->
      <div class="row">
        <!-- LEFT COLUMN: CHAMBERS, BIOGRAPHY, REVIEWS, FAQS -->
        <div class="col-lg-8">
          <!-- SECTION 1: CHAMBER & VISITING SCHEDULE -->
          <div class="card border-0 shadow-sm rounded-lg mb-4 bg-white overflow-hidden">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
              <h5 class="mb-0 font-weight-bold text-white d-flex align-items-center flex-wrap" style="gap: 8px;">
                <span><i class="icofont-hospital mr-2"></i> চেম্বার ও রোগী দেখার সময়সূচী</span>
                <span v-if="doctor.chambers && doctor.chambers.length > 1" class="badge badge-light text-primary font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 12px;">
                  {{ doctor.chambers.length }}টি চেম্বার উপলব্ধ
                </span>
              </h5>
              <router-link :to="'/appoinment?doctor_id=' + doctor.id + '&type=in_chamber'" class="btn btn-sm btn-light text-primary font-weight-bold px-3 rounded-pill shadow-sm">
                <i class="icofont-calendar mr-1"></i> সিরিয়াল বুক করুন
              </router-link>
            </div>
            <div class="card-body p-4">
              <!-- Special Free Friday / Charity Clinic Banner (if enabled) -->
              <div v-if="doctor.free_consultation_enabled" class="alert alert-success border-success p-3 rounded mb-4 shadow-sm bg-white">
                <div class="d-flex align-items-center mb-1">
                  <span class="font-20 mr-2">🎁</span>
                  <h6 class="font-weight-bold text-success mb-0">ফ্রি কনসালটেশন ও চ্যারিটি ক্লিনিক (Free Patient Service)</h6>
                </div>
                <p class="mb-2 text-dark small">
                  {{ doctor.name }} সপ্তাহে <strong>{{ (doctor.free_days && doctor.free_days.length) ? doctor.free_days.join(', ') : 'শুক্রবার' }}</strong> সাধারণ ও দরিদ্র রোগীদের বিনামূল্যে পরামর্শ সেবা দেন।
                </p>
                <div class="row g-2 small">
                  <div class="col-md-4">
                    <div class="bg-light p-2 rounded border">
                      <strong class="text-muted d-block">ফ্রি সময়:</strong>
                      <span class="font-weight-bold text-dark">{{ doctor.free_start_time ? doctor.free_start_time.substring(0,5) : 'সকাল ৯:০০' }} - {{ doctor.free_end_time ? doctor.free_end_time.substring(0,5) : 'দুপুর ১২:০০' }}</span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="bg-light p-2 rounded border">
                      <strong class="text-muted d-block">ফি:</strong>
                      <span class="font-weight-bold text-success">৳০ (সম্পূর্ণ ফ্রি)</span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="bg-light p-2 rounded border">
                      <strong class="text-muted d-block">কোটা সীমা:</strong>
                      <span class="font-weight-bold text-primary">সর্বোচ্চ {{ doctor.free_patient_limit || 20 }} জন</span>
                    </div>
                  </div>
                  <div v-if="doctor.free_chamber_address" class="col-12 mt-1">
                    <div class="bg-light p-2 rounded border">
                      <strong class="text-muted d-block">ফ্রি চেম্বার লোকেশন:</strong>
                      <span class="font-weight-bold text-dark"><i class="icofont-location-pin text-danger"></i> {{ doctor.free_chamber_address }}</span>
                    </div>
                  </div>
                  <div v-if="doctor.free_service_note" class="col-12 mt-1">
                    <small class="text-muted"><i class="icofont-info-circle text-info mr-1"></i>{{ doctor.free_service_note }}</small>
                  </div>
                  <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                    <span class="text-success small font-weight-bold">
                      <i class="icofont-check-circled mr-1"></i> অনলাইন ফ্রি রেজিস্ট্রেশন উন্মুক্ত
                    </span>
                    <router-link :to="'/appoinment?doctor_id=' + doctor.id + '&type=in_chamber&free=1'" class="btn btn-success btn-sm px-4 rounded-pill font-weight-bold shadow-sm">
                      🎁 ফ্রি সিরিয়াল বুক করুন (৳০ ফি) &rarr;
                    </router-link>
                  </div>
                </div>
              </div>

              <!-- MULTIPLE CHAMBERS LIST -->
              <div v-if="doctor.chambers && doctor.chambers.length > 0">
                <div 
                  v-for="(ch, cIdx) in doctor.chambers" 
                  :key="ch.id" 
                  class="chamber-card border rounded-lg p-3 p-md-4 mb-4 bg-light shadow-2xs position-relative"
                  :style="ch.is_primary ? 'border-left: 5px solid #223a66 !important;' : 'border-left: 5px solid #e12454 !important;'"
                >
                  <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
                    <div>
                      <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h5 class="font-weight-bold text-dark mb-0">
                          <i class="icofont-institution text-primary mr-1"></i>
                          {{ ch.chamber_name }}
                        </h5>
                        <span v-if="ch.branch_name" class="badge badge-light border text-muted px-2 py-0.5 font-weight-bold" style="font-size: 11px;">
                          {{ ch.branch_name }}
                        </span>
                        <span v-if="ch.is_primary" class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 10px; border-radius: 12px;">
                          প্রধান চেম্বার
                        </span>
                        <span v-else class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 10px; border-radius: 12px;">
                          চেম্বার #{{ cIdx + 1 }}
                        </span>
                      </div>
                      <p class="text-muted small mb-2">
                        <i class="icofont-google-map text-danger mr-1"></i>
                        {{ ch.address }}
                      </p>
                    </div>
                    <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12px; border-radius: 20px;">
                      সরাসরি চেম্বার
                    </span>
                  </div>

                  <!-- Day Schedule Badges -->
                  <div class="schedule-days-box bg-white p-3 rounded border mb-3">
                    <div class="small font-weight-bold text-dark mb-2">রোগী দেখার দিনসমূহ:</div>
                    <div class="d-flex flex-wrap" style="gap: 6px;">
                      <span 
                        v-for="day in banglaDays" 
                        :key="day.en" 
                        class="badge px-3 py-2 font-weight-bold"
                        :class="isChamberAvailableDay(ch, day.en) ? 'badge-primary' : 'badge-light text-muted border'"
                      >
                        {{ day.bn }}
                      </span>
                    </div>
                  </div>

                  <!-- Time, Fees & Phone Info Grid -->
                  <div class="row g-2 text-dark small">
                    <div class="col-sm-6 col-md-4 mb-2">
                      <div class="bg-white p-2 rounded border h-100">
                        <strong class="text-muted d-block">ভিজিটিং সময়:</strong>
                        <span class="font-weight-bold text-primary font-14">
                          <i class="icofont-clock-time mr-1"></i>
                          {{ formatTime(ch.start_time) }} - {{ formatTime(ch.end_time) }}
                        </span>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4 mb-2">
                      <div class="bg-white p-2 rounded border h-100">
                        <strong class="text-muted d-block">কনসালটেশন ফি:</strong>
                        <span class="font-weight-bold text-success font-14">
                          <i class="icofont-money mr-1"></i> নতুন ৳{{ ch.consultation_fee || doctor.consultation_fee }}
                        </span>
                        <small class="text-muted d-block font-11">পুরাতন/রিপোর্ট: ৳{{ Math.round((ch.consultation_fee || doctor.consultation_fee) * 0.6) }}</small>
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-4 mb-2">
                      <div class="bg-white p-2 rounded border h-100">
                        <strong class="text-muted d-block">সিরিয়াল হটলাইন:</strong>
                        <a v-if="ch.phone || doctor.phone" :href="'tel:' + (ch.phone || doctor.phone)" class="font-weight-bold text-danger font-14">
                          <i class="icofont-phone mr-1"></i> {{ ch.phone || doctor.phone }}
                        </a>
                        <span v-else class="text-muted font-13">অনলাইন বুকিং উপলব্ধ</span>
                      </div>
                    </div>
                  </div>

                  <div class="mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center">
                    <small class="text-muted">
                      <i class="icofont-info-circle mr-1"></i> প্রতি রোগীর জন্য পর্যাপ্ত সময় ({{ ch.slot_duration_minutes || doctor.slot_duration_minutes || 15 }} মিনিট) বরাদ্দ থাকে।
                    </small>
                    <router-link :to="'/appoinment?doctor_id=' + doctor.id + '&chamber_id=' + ch.id + '&type=in_chamber'" class="btn btn-main-2 btn-sm px-4 rounded-pill font-weight-bold shadow-sm mt-2 mt-sm-0">
                      {{ ch.chamber_name }}-এ সিরিয়াল বুক করুন &rarr;
                    </router-link>
                  </div>
                </div>
              </div>

              <!-- SINGLE FALLBACK CHAMBER (if no chambers array) -->
              <div v-else class="chamber-card border rounded-lg p-3 p-md-4 mb-3 bg-light">
                <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">
                      <i class="icofont-institution text-primary mr-1"></i>
                      {{ getChamberName(doctor.chamber_address) }}
                    </h5>
                    <p class="text-muted small mb-2">
                      <i class="icofont-google-map text-danger mr-1"></i>
                      {{ doctor.chamber_address }}
                    </p>
                  </div>
                  <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12px; border-radius: 20px;">
                    সরাসরি চেম্বার
                  </span>
                </div>

                <!-- Day Schedule Badges -->
                <div class="schedule-days-box bg-white p-3 rounded border mb-3">
                  <div class="small font-weight-bold text-dark mb-2">রোগী দেখার দিনসমূহ:</div>
                  <div class="d-flex flex-wrap" style="gap: 6px;">
                    <span 
                      v-for="day in banglaDays" 
                      :key="day.en" 
                      class="badge px-3 py-2 font-weight-bold"
                      :class="isAvailableDay(day.en) ? 'badge-primary' : 'badge-light text-muted border'"
                    >
                      {{ day.bn }}
                    </span>
                  </div>
                </div>

                <!-- Time, Fees & Phone Info Grid -->
                <div class="row g-2 text-dark small">
                  <div class="col-sm-6 col-md-4 mb-2">
                    <div class="bg-white p-2 rounded border h-100">
                      <strong class="text-muted d-block">ভিজিটিং সময়:</strong>
                      <span class="font-weight-bold text-primary font-14">
                        <i class="icofont-clock-time mr-1"></i>
                        {{ doctor.start_time || 'বিকাল ৪:০০' }} - {{ doctor.end_time || 'রাত ৮:০০' }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-4 mb-2">
                    <div class="bg-white p-2 rounded border h-100">
                      <strong class="text-muted d-block">কনসালটেশন ফি:</strong>
                      <span class="font-weight-bold text-success font-14">
                        <i class="icofont-money mr-1"></i> নতুন ৳{{ doctor.consultation_fee }}
                      </span>
                      <small class="text-muted d-block font-11">পুরাতন/রিপোর্ট: ৳{{ Math.round(doctor.consultation_fee * 0.6) }}</small>
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-4 mb-2">
                    <div class="bg-white p-2 rounded border h-100">
                      <strong class="text-muted d-block">সিরিয়াল হটলাইন:</strong>
                      <a v-if="doctor.phone" :href="'tel:' + doctor.phone" class="font-weight-bold text-danger font-14">
                        <i class="icofont-phone mr-1"></i> {{ doctor.phone }}
                      </a>
                      <span v-else class="text-muted font-13">অনলাইন বুকিং উপলব্ধ</span>
                    </div>
                  </div>
                </div>

                <div class="mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center">
                  <small class="text-muted">
                    <i class="icofont-info-circle mr-1"></i> প্রতি রোগীর জন্য পর্যাপ্ত সময় ({{ doctor.slot_duration_minutes || 20 }} মিনিট) বরাদ্দ থাকে।
                  </small>
                  <router-link :to="'/appoinment?doctor_id=' + doctor.id + '&type=in_chamber'" class="btn btn-main-2 btn-sm px-4 rounded-pill font-weight-bold shadow-sm mt-2 mt-sm-0">
                    চেম্বার সিরিয়াল বুক করুন &rarr;
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 2: DOCTOR BIOGRAPHY & SPECIALTY DETAILS -->
          <div class="card border-0 shadow-sm rounded-lg mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3">
              <h5 class="mb-0 font-weight-bold text-dark">
                <i class="icofont-doctor-alt text-primary mr-2"></i> চিকিৎসক পরিচিতি ও বিস্তারিত বিবরণ
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="bio-content text-dark" style="line-height: 1.85; font-size: 0.98rem;">
                <p v-if="doctor.bio">{{ doctor.bio }}</p>
                <div v-else>
                  <p>
                    <strong>{{ doctor.name }}</strong> একজন স্বনামধন্য ও অভিজ্ঞ <strong>{{ doctor.specialization }}</strong>। তিনি সফলতার সাথে দীর্ঘদিন যাবত রোগীদের স্বাস্থ্যসেবা প্রদান করে আসছেন। বর্তমানে তিনি <strong>{{ doctor.designation }}</strong> হিসেবে কর্মরত আছেন।
                  </p>
                  <p>
                    তিনি <strong>{{ doctor.qualifications }}</strong> ডিগ্রি অর্জন করেছেন এবং আধুনিক চিকিৎসা পদ্ধতিতে রোগীদের নিবিড় পর্যবেক্ষণ ও উপযুক্ত পরামর্শ প্রদান করে থাকেন।
                  </p>
                </div>

                <div class="mt-4 pt-3 border-top">
                  <h6 class="font-weight-bold text-dark mb-3">
                    <i class="icofont-check-circled text-success mr-2"></i> বিশেষ সেবা ও চিকিৎসার ক্ষেত্রসমূহ:
                  </h6>
                  <div class="row">
                    <div class="col-md-6 mb-2">
                      <div class="d-flex align-items-center">
                        <i class="icofont-check text-primary mr-2"></i>
                        <span>{{ doctor.specialization }} সম্পর্কিত সকল জটিল রোগের চিকিৎসা</span>
                      </div>
                    </div>
                    <div class="col-md-6 mb-2">
                      <div class="d-flex align-items-center">
                        <i class="icofont-check text-primary mr-2"></i>
                        <span>রোগীর সঠিক রোগ নির্ণয় ও প্রেসক্রিপশন প্রদান</span>
                      </div>
                    </div>
                    <div class="col-md-6 mb-2">
                      <div class="d-flex align-items-center">
                        <i class="icofont-check text-primary mr-2"></i>
                        <span>ফলোআপ কেয়ার ও রিপোর্ট পর্যালোচনা</span>
                      </div>
                    </div>
                    <div class="col-md-6 mb-2">
                      <div class="d-flex align-items-center">
                        <i class="icofont-check text-primary mr-2"></i>
                        <span>প্রয়োজনে বিশেষজ্ঞ পরামর্শ ও টেলিমেডিসিন সহায়তা</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 3: PATIENT REVIEWS & RATINGS -->
          <div id="reviews-section" class="card border-0 shadow-sm rounded-lg mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
              <h5 class="mb-0 font-weight-bold text-dark">
                <i class="icofont-star text-warning mr-2"></i> রোগীদের অভিজ্ঞতা ও মতামত
              </h5>
              <span class="badge badge-primary px-3 py-1 font-weight-bold">৫.০ স্কোর</span>
            </div>
            <div class="card-body p-4">
              <!-- Overall Score Breakdown -->
              <div class="row align-items-center mb-4 pb-3 border-bottom">
                <div class="col-md-4 text-center border-right mb-3 mb-md-0">
                  <div class="display-4 font-weight-bold text-primary">5.0</div>
                  <div class="text-warning mb-1">
                    <i class="icofont-star"></i>
                    <i class="icofont-star"></i>
                    <i class="icofont-star"></i>
                    <i class="icofont-star"></i>
                    <i class="icofont-star"></i>
                  </div>
                  <div class="text-muted small">সর্বমোট {{ reviewCount }} টি রোগী রেটিংয়ের ভিত্তিতে</div>
                </div>
                <div class="col-md-8">
                  <div class="d-flex align-items-center mb-1 small">
                    <span class="mr-2" style="width: 50px;">৫ স্টার</span>
                    <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                      <div class="progress-bar bg-success" style="width: 95%;"></div>
                    </div>
                    <span class="text-muted">৯৫%</span>
                  </div>
                  <div class="d-flex align-items-center mb-1 small">
                    <span class="mr-2" style="width: 50px;">৪ স্টার</span>
                    <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                      <div class="progress-bar bg-info" style="width: 5%;"></div>
                    </div>
                    <span class="text-muted">৫%</span>
                  </div>
                  <div class="d-flex align-items-center mb-1 small">
                    <span class="mr-2" style="width: 50px;">৩ স্টার</span>
                    <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                      <div class="progress-bar bg-warning" style="width: 0%;"></div>
                    </div>
                    <span class="text-muted">০%</span>
                  </div>
                </div>
              </div>

              <!-- Reviews List -->
              <div class="reviews-list">
                <div v-for="(rev, idx) in reviews" :key="idx" class="review-item mb-3 pb-3 border-bottom">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center">
                      <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 40px; height: 40px;">
                        {{ rev.name.charAt(0) }}
                      </div>
                      <div>
                        <h6 class="font-weight-bold text-dark mb-0">{{ rev.name }}</h6>
                        <small class="text-success font-weight-bold"><i class="icofont-check-circled"></i> ভেরিফাইড রোগী</small>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-warning small">
                        <i v-for="s in rev.stars" :key="s" class="icofont-star"></i>
                      </div>
                      <small class="text-muted">{{ rev.date }}</small>
                    </div>
                  </div>
                  <p class="text-muted mb-0 small" style="line-height: 1.6;">"{{ rev.comment }}"</p>
                </div>
              </div>

              <!-- Write Review Form with Voice Input -->
              <div class="write-review-box bg-light p-3 rounded border mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="font-weight-bold text-dark mb-0">
                    <i class="icofont-edit text-primary mr-1"></i> আপনার অভিজ্ঞতা লিখুন:
                  </h6>
                  <button 
                    type="button" 
                    class="btn btn-sm btn-outline-danger font-weight-bold rounded-pill px-2 py-1 shadow-sm d-flex align-items-center"
                    :class="{ 'pulse-voice-btn text-white bg-danger': isListeningReview }"
                    @click="toggleVoiceReview"
                    title="মুখে বলে বাংলায় লিখুন (Voice Input)"
                  >
                    <i class="icofont-mic mr-1"></i> 
                    <span>{{ isListeningReview ? 'শুনছি... বলুন...' : 'মুখে বলে লিখুন' }}</span>
                  </button>
                </div>

                <!-- Voice Listening Indicator Alert -->
                <div v-if="isListeningReview" class="alert alert-danger py-2 px-3 small mb-2 d-flex align-items-center rounded">
                  <span class="spinner-grow spinner-grow-sm text-danger mr-2" role="status"></span>
                  <span><strong>মাইক্রোফোন চালু আছে!</strong> পরিষ্কার ভাষায় আপনার মতামত বাংলায় বলুন...</span>
                </div>

                <div class="form-group mb-2">
                  <div class="d-flex align-items-center mb-2">
                    <span class="text-muted small mr-2">রেটিং দিন:</span>
                    <span class="text-warning cursor-pointer font-18">
                      <i v-for="n in 5" :key="n" class="icofont-star" :class="{'text-warning': n <= newReview.stars, 'text-muted': n > newReview.stars}" @click="newReview.stars = n"></i>
                    </span>
                  </div>
                  <div class="position-relative mb-2">
                    <input type="text" v-model="newReview.name" data-no-voice class="form-control form-control-sm pr-4" placeholder="আপনার নাম...">
                    <button type="button" class="btn btn-link text-muted position-absolute p-0" style="right: 8px; top: 4px;" @click="toggleVoiceInput('name')" title="নাম মুখে বলুন">
                      <i class="icofont-mic" :class="{'text-danger': activeVoiceField === 'name'}"></i>
                    </button>
                  </div>
                  <div class="position-relative">
                    <textarea v-model="newReview.comment" data-no-voice class="form-control form-control-sm pr-4" rows="2" placeholder="ডাক্তারের সেবা বা চেম্বার সম্পর্কে মতামত লিখুন (বা উপরের 'মুখে বলে লিখুন' চাপুন)..."></textarea>
                  </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm font-weight-bold px-3 rounded shadow-sm" @click="submitReview">
                  মতামত সাবমিট করুন
                </button>
              </div>
            </div>
          </div>

          <!-- SECTION 4: FREQUENTLY ASKED QUESTIONS (FAQ) -->
          <div class="card border-0 shadow-sm rounded-lg mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3">
              <h5 class="mb-0 font-weight-bold text-dark">
                <i class="icofont-question-circle text-primary mr-2"></i> সাধারণ জিজ্ঞাসা ও উত্তর (FAQ)
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="accordion" id="faqAccordion">
                <div v-for="(faq, fIdx) in faqs" :key="fIdx" class="card mb-2 border rounded overflow-hidden">
                  <div class="card-header p-3 bg-light cursor-pointer d-flex justify-content-between align-items-center" @click="toggleFaq(fIdx)">
                    <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 0.95rem;">
                      {{ faq.question }}
                    </h6>
                    <i class="icofont-rounded-down font-18 text-primary transition-transform" :class="{'rotate-180': openFaq === fIdx}"></i>
                  </div>
                  <div v-show="openFaq === fIdx" class="card-body p-3 bg-white text-muted small" style="line-height: 1.7;">
                    {{ faq.answer }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: SIDEBAR QUICK BOOKING & RELATED SPECIALISTS -->
        <div class="col-lg-4">
          <!-- Quick Booking Card -->
          <div class="card border-0 shadow-sm rounded-lg mb-4 bg-white sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-primary text-white py-3">
              <h5 class="mb-0 font-weight-bold text-white text-center">
                <i class="icofont-calendar mr-2"></i> অ্যাপয়েন্টমেন্ট বুকিং
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="bg-light p-3 rounded mb-3 border text-center">
                <div class="text-muted small">চেম্বার কনসালটেশন ফি</div>
                <div class="font-weight-bold text-success font-28">৳{{ doctor.consultation_fee }}</div>
                <small class="text-muted">ভ্যাট ও প্ল্যাটফর্ম চার্জ অন্তর্ভুক্ত</small>
              </div>

              <!-- Available Chambers Quick Pick (if multiple) -->
              <div v-if="doctor.chambers && doctor.chambers.length > 1" class="mb-3 p-2.5 bg-light border rounded">
                <div class="small font-weight-bold text-dark mb-2 d-flex justify-content-between align-items-center">
                  <span><i class="icofont-hospital text-danger mr-1"></i> উপলব্ধ চেম্বারসমূহ:</span>
                  <span class="badge badge-secondary" style="font-size: 10px;">{{ doctor.chambers.length }}টি</span>
                </div>
                <div class="d-flex flex-column" style="gap: 6px;">
                  <router-link 
                    v-for="ch in doctor.chambers" 
                    :key="ch.id"
                    :to="'/appoinment?doctor_id=' + doctor.id + '&chamber_id=' + ch.id + '&type=in_chamber'"
                    class="btn btn-sm btn-white border d-flex justify-content-between align-items-center py-2 px-2.5 text-left shadow-2xs hover:border-danger transition"
                    style="font-size: 11px; background: #fff;"
                  >
                    <span class="text-truncate font-weight-bold text-dark" style="max-width: 170px;">
                      <i class="icofont-location-pin text-danger mr-1"></i>
                      {{ ch.chamber_name }}
                    </span>
                    <span class="badge badge-success font-weight-bold">৳{{ ch.consultation_fee || doctor.consultation_fee }}</span>
                  </router-link>
                </div>
              </div>

              <!-- Action 1: In Chamber Booking -->
              <router-link 
                :to="'/appoinment?doctor_id=' + doctor.id + '&type=in_chamber'" 
                class="btn btn-main-2 btn-round-full w-100 font-weight-bold py-3 mb-2 shadow-sm d-flex align-items-center justify-content-center"
              >
                <i class="icofont-hospital mr-2 font-18"></i> চেম্বার সিরিয়াল বুকিং
              </router-link>

              <!-- Action 2: Video Call Consultation -->
              <router-link 
                :to="'/appoinment?doctor_id=' + doctor.id + '&type=online_video'" 
                class="btn btn-success btn-round-full w-100 font-weight-bold py-3 mb-3 shadow-sm d-flex align-items-center justify-content-center"
              >
                <i class="icofont-video-cam mr-2 font-18"></i> ভিডিও কনসালটেশন বুকিং
              </router-link>

              <div class="border-top pt-3 text-center small text-muted">
                <div class="mb-2">
                  <i class="icofont-clock-time text-primary"></i> চেম্বার সময়: <strong>{{ doctor.start_time || '৪:০০' }} - {{ doctor.end_time || '৮:০০' }}</strong>
                </div>
                <div v-if="doctor.phone">
                  <i class="icofont-headphone-alt text-danger"></i> বুকিং সহায়তা হটলাইন: 
                  <a :href="'tel:' + doctor.phone" class="font-weight-bold text-primary">{{ doctor.phone }}</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Related Specialist Doctors -->
          <div v-if="relatedDoctors.length > 0" class="card border-0 shadow-sm rounded-lg mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3">
              <h6 class="mb-0 font-weight-bold text-dark">
                <i class="icofont-users-alt-5 text-primary mr-2"></i> একই বিষয়ের অন্যান্য বিশেষজ্ঞ ডাক্তার
              </h6>
            </div>
            <div class="card-body p-3">
              <div v-for="related in relatedDoctors" :key="related.id" class="related-doctor-card p-2 mb-2 rounded border bg-light d-flex align-items-center">
                <img 
                  :src="getDoctorAvatar(related)" 
                  :alt="related.name" 
                  @error="onAvatarError($event, related)"
                  class="rounded-circle mr-3 border" 
                  style="width: 55px; height: 55px; object-fit: cover; background: #fff;"
                >
                <div class="flex-grow-1 overflow-hidden">
                  <router-link :to="'/doctor/' + related.slug" class="font-weight-bold text-dark d-block text-truncate small">
                    {{ related.name }}
                  </router-link>
                  <small class="text-primary font-weight-bold d-block text-truncate">{{ related.specialization }}</small>
                  <small class="text-muted d-block text-truncate">{{ related.district || related.city }}</small>
                </div>
                <router-link :to="'/doctor/' + related.slug" class="btn btn-outline-primary btn-sm rounded-pill ml-2 font-weight-bold px-2 py-1" style="font-size: 11px; white-space: nowrap;">
                  প্রোফাইল
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Sticky Quick-Action Bar (গ্রামের রোগীদের সুবিধার্থে মোবাইলের নিচে ফিক্সড বুকিং ও কল বাটন) -->
    <div v-if="doctor" class="mobile-sticky-bar d-md-none bg-white border-top shadow-lg p-2 position-fixed" style="bottom: 0; left: 0; right: 0; z-index: 1050;">
      <div class="d-flex align-items-center justify-content-between" style="gap: 8px;">
        <div class="px-2">
          <div class="text-muted" style="font-size: 10px; line-height: 1;">ভিজিট ফি</div>
          <div class="font-weight-bold text-success font-18">৳{{ doctor.consultation_fee }}</div>
        </div>
        <div class="d-flex flex-grow-1" style="gap: 6px;">
          <router-link 
            :to="'/appoinment?doctor_id=' + doctor.id + '&type=in_chamber'" 
            class="btn btn-main-2 btn-sm flex-grow-1 font-weight-bold rounded-pill py-2 shadow-sm d-flex align-items-center justify-content-center"
            style="font-size: 13px; min-height: 44px;"
          >
            <i class="icofont-calendar mr-1"></i> সিরিয়াল বুকিং
          </router-link>
          <a 
            v-if="doctor.phone" 
            :href="'tel:' + doctor.phone" 
            class="btn btn-success btn-sm rounded-pill px-3 py-2 font-weight-bold shadow-sm d-flex align-items-center justify-content-center"
            style="font-size: 13px; min-height: 44px;"
            title="সরাসরি ফোন দিন"
          >
            <i class="icofont-phone mr-1"></i> কল
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { startVoiceRecognition, isSpeechSupported } from '../utils/voiceInput';

export default {
  name: 'DoctorDetail',
  props: ['slug'],
  data() {
    return {
      doctor: null,
      relatedDoctors: [],
      loading: true,
      toastMessage: '',
      openFaq: 0,
      reviewCount: 2,
      isListeningReview: false,
      activeVoiceField: null,
      reviews: [
        {
          name: 'মোঃ কামরুল হাসান',
          stars: 5,
          date: '২ দিন আগে',
          comment: 'ডাক্তার সাহেব অত্যন্ত যত্নসহকারে সময় নিয়ে রোগ শোনেন এবং সঠিক পরামর্শ দেন। চেম্বারের পরিবেশও খুব ভালো ছিল।'
        },
        {
          name: 'নাজনীন সুলতানা',
          stars: 5,
          date: '১ সপ্তাহ আগে',
          comment: 'ওষুধের পার্শ্বপ্রতিক্রিয়া ও খাবারের নিয়ম খুব সুন্দর করে বুঝিয়ে দিয়েছেন। ওনার চিকিৎসায় আলহামদুলিল্লাহ খুব দ্রুত সুস্থ হয়েছি।'
        }
      ],
      newReview: {
        name: '',
        stars: 5,
        comment: ''
      },
      banglaDays: [
        { en: 'Saturday', bn: 'শনিবার' },
        { en: 'Sunday', bn: 'রবিবার' },
        { en: 'Monday', bn: 'সোমবার' },
        { en: 'Tuesday', bn: 'মঙ্গলবার' },
        { en: 'Wednesday', bn: 'বুধবার' },
        { en: 'Thursday', bn: 'বৃহস্পতিবার' },
        { en: 'Friday', bn: 'শুক্রবার' },
      ],
      faqs: [
        {
          question: 'অনলাইনে সিরিয়াল বা অ্যাপয়েন্টমেন্ট কীভাবে বুক করব?',
          answer: 'উপরে থাকা "চেম্বার সিরিয়াল বুকিং" বাটনে ক্লিক করে পছন্দসই তারিখ ও স্লট নির্বাচন করে নাম-মোবাইল নম্বর দিলেই তাৎক্ষণিক কনফার্মেশন পাবেন।'
        },
        {
          question: 'চেম্বারে ভিজিট ফি কত এবং কীভাবে পরিশোধ করতে হবে?',
          answer: 'ডাক্তার সাহেবের নতুন রোগীর ভিজিট ফি প্রদর্শিত কার্ড অনুযায়ী প্রযোজ্য। আপনি অনলাইনে বিকাশ, নগদ বা চেম্বারে উপস্থিত হয়ে সরাসরি ফি পরিশোধ করতে পারবেন।'
        },
        {
          question: 'রিপোর্ট দেখানোর নিয়ম ও সময়সীমা কী?',
          answer: 'সাধারণত প্রথম ভিজিটের ৭ থেকে ১৪ দিনের মধ্যে টেস্ট রিপোর্ট দেখালে কোনো অতিরিক্ত ফি বা সীমিত ফলোআপ ফি প্রযোজ্য হয়।'
        },
        {
          question: 'জরুরি প্রয়োজনে ডাক্তারের সাথে কীভাবে যোগাযোগ করব?',
          answer: 'প্রোফাইলে দেওয়া সিরিয়াল হটলাইন নম্বরে সরাসরি কল করতে পারেন অথবা আমাদের ২৪/৭ সাপোর্ট নাম্বারে যোগাযোগ করতে পারেন।'
        },
        {
          question: 'অনলাইন ভিডিও কনসালটেশন কীভাবে নেওয়া যাবে?',
          answer: '"ভিডিও কনসালটেশন বুকিং" বাটনে ক্লিক করে বুকিং সম্পন্ন করলে নির্ধারিত সময়ে হোয়াটসঅ্যাপ বা ভিডিও লিংকের মাধ্যমে ডাক্তারের সাথে পরামর্শ করতে পারবেন।'
        }
      ]
    };
  },
  watch: {
    '$route.params.slug': {
      immediate: true,
      handler(newSlug) {
        if (newSlug) {
          this.fetchDoctor(newSlug);
        }
      }
    }
  },
  methods: {
    async fetchDoctor(slug) {
      this.loading = true;
      try {
        const res = await axios.get(`/doctors/${slug}`);
        if (res.data && res.data.doctor) {
          this.doctor = res.data.doctor;
          this.relatedDoctors = res.data.related_doctors || [];
        } else if (res.data) {
          this.doctor = res.data;
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } catch (err) {
        console.error('Error fetching doctor details:', err);
        this.doctor = null;
      } finally {
        this.loading = false;
      }
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
    getChamberName(address) {
      if (!address) return 'প্রধান চেম্বার';
      const parts = address.split(',');
      return parts[0].trim();
    },
    isAvailableDay(dayEn) {
      if (!this.doctor || !this.doctor.available_days) return true;
      return this.doctor.available_days.includes(dayEn);
    },
    isChamberAvailableDay(chamber, dayEn) {
      if (!chamber || !chamber.visiting_days) {
        return this.isAvailableDay(dayEn);
      }
      if (Array.isArray(chamber.visiting_days)) {
        return chamber.visiting_days.includes(dayEn);
      }
      if (typeof chamber.visiting_days === 'string') {
        try {
          const parsed = JSON.parse(chamber.visiting_days);
          if (Array.isArray(parsed)) return parsed.includes(dayEn);
        } catch (e) {
          return chamber.visiting_days.includes(dayEn);
        }
      }
      return true;
    },
    formatTime(timeStr) {
      if (!timeStr) return '';
      try {
        const parts = timeStr.split(':');
        let hours = parseInt(parts[0], 10);
        const minutes = parts[1] || '00';
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours}:${minutes} ${ampm}`;
      } catch (e) {
        return timeStr;
      }
    },
    toggleFaq(idx) {
      this.openFaq = this.openFaq === idx ? null : idx;
    },
    toggleVoiceReview() {
      if (this.isListeningReview) {
        this.isListeningReview = false;
        return;
      }
      if (!isSpeechSupported()) {
        alert('আপনার মোবাইলের ব্রাউজার ভয়েস রিকগনিশন সমর্থন করে না। অনুগ্রহ করে ক্রোম (Chrome) ব্রাউজার ব্যবহার করুন বা টাইপ করুন।');
        return;
      }

      startVoiceRecognition({
        lang: 'bn-BD',
        onStart: () => {
          this.isListeningReview = true;
          this.showToast('মাইক্রোফোন চালু হয়েছে। পরিষ্কার বাংলায় বলুন...');
        },
        onResult: (text) => {
          this.newReview.comment = this.newReview.comment ? (this.newReview.comment + ' ' + text) : text;
          this.showToast('কথা রেকর্ড হয়েছে: ' + text);
        },
        onError: (err) => {
          this.isListeningReview = false;
          console.warn('Voice error:', err);
        },
        onEnd: () => {
          this.isListeningReview = false;
        }
      });
    },
    toggleVoiceInput(field) {
      if (this.activeVoiceField === field) {
        this.activeVoiceField = null;
        return;
      }
      if (!isSpeechSupported()) {
        alert('ভয়েস ইনপুট সমর্থিত নয়। অনুগ্রহ করে টাইপ করুন।');
        return;
      }
      startVoiceRecognition({
        lang: 'bn-BD',
        onStart: () => {
          this.activeVoiceField = field;
          this.showToast('শুনছি... নাম বলুন...');
        },
        onResult: (text) => {
          if (field === 'name') {
            this.newReview.name = text;
          }
          this.showToast('নাম রেকর্ড হয়েছে: ' + text);
        },
        onError: () => {
          this.activeVoiceField = null;
        },
        onEnd: () => {
          this.activeVoiceField = null;
        }
      });
    },
    submitReview() {
      if (!this.newReview.name || !this.newReview.comment) {
        alert('অনুগ্রহ করে আপনার নাম এবং মতামত লিখুন বা মুখে বলুন।');
        return;
      }
      this.reviews.unshift({
        name: this.newReview.name,
        stars: this.newReview.stars,
        date: 'এখনই',
        comment: this.newReview.comment
      });
      this.reviewCount++;
      this.newReview = { name: '', stars: 5, comment: '' };
      this.showToast('আপনার মূল্যবান মতামতের জন্য ধন্যবাদ!');
    },
    showToast(msg) {
      this.toastMessage = msg;
      setTimeout(() => {
        this.toastMessage = '';
      }, 3500);
    },
    getDoctorShareUrl() {
      return window.location.href;
    },
    async copyDoctorLink() {
      const shareUrl = this.getDoctorShareUrl();
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
        this.showToast('ডাক্তারের প্রোফাইল লিংক কপি করা হয়েছে!');
      } catch (err) {
        console.error('Failed to copy: ', err);
      }
    },
    openWhatsApp() {
      const shareUrl = this.getDoctorShareUrl();
      const docName = this.doctor?.name || '';
      const spec = this.doctor?.specialization || '';
      const text = encodeURIComponent(`ডাঃ ${docName} - ${spec}\nবিস্তারিত ও সিরিয়াল বুকিং লিংক: ${shareUrl}`);
      if (this.doctor?.phone) {
        let cleanPhone = this.doctor.phone.replace(/[^\d]/g, '');
        if (cleanPhone.startsWith('0')) cleanPhone = '88' + cleanPhone;
        window.open(`https://api.whatsapp.com/send?phone=${cleanPhone}&text=${text}`, '_blank');
      } else {
        window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
      }
    },
    shareFacebook() {
      const shareUrl = encodeURIComponent(this.getDoctorShareUrl());
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`, '_blank');
    },
    shareMessenger() {
      const shareUrl = encodeURIComponent(this.getDoctorShareUrl());
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`, '_blank');
    }
  }
};
</script>

<style scoped>
.hero-header-banner {
  border-top-left-radius: 0.5rem;
  border-top-right-radius: 0.5rem;
}

.qualifications-box {
  border-left: 4px solid #f59e0b !important;
}

.btn-main-2 {
  background: #e12454;
  color: #fff;
  border: 1px solid #e12454;
  transition: all 0.3s ease;
}

.btn-main-2:hover {
  background: #c21945;
  color: #fff;
  border-color: #c21945;
}

.btn-round-full {
  border-radius: 50px;
}

.cursor-pointer {
  cursor: pointer;
}

.font-11 { font-size: 11px; }
.font-14 { font-size: 14px; }
.font-18 { font-size: 18px; }
.font-20 { font-size: 20px; }
.font-28 { font-size: 28px; }

.rotate-180 {
  transform: rotate(180deg);
}

.transition-transform {
  transition: transform 0.2s ease-in-out;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.related-doctor-card:hover {
  background: #eef2ff !important;
  border-color: #c7d2fe !important;
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

/* Mobile-Friendly Optimizations for Rural Users */
@media (max-width: 768px) {
  .doctor-profile-page {
    padding-bottom: 75px !important;
  }
  .hero-header-banner {
    padding: 1rem !important;
  }
  .card-body {
    padding: 1.1rem !important;
  }
  .hero-avatar-container {
    margin-top: -35px !important;
  }
  .hero-avatar-img {
    width: 125px !important;
    height: 125px !important;
  }
  .share-action-bar .d-flex {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 8px !important;
  }
  .share-action-bar .share-title {
    grid-column: 1 / -1 !important;
    margin-bottom: 4px;
    font-size: 13px;
  }
  .share-action-bar button, 
  .share-action-bar a {
    width: 100% !important;
    text-align: center !important;
    justify-content: center !important;
    min-height: 42px !important;
    display: inline-flex !important;
    align-items: center !important;
  }
  .qualifications-box {
    padding: 0.85rem !important;
    font-size: 0.88rem !important;
  }
  h2 {
    font-size: 1.35rem !important;
    line-height: 1.3 !important;
  }
  h5 {
    font-size: 0.95rem !important;
  }
  .btn-main-2, .btn-success, .btn-primary {
    min-height: 44px;
  }
}
</style>
