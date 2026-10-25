<template>
  <div>
    <section class="page-title bg-1 py-4 py-md-5 text-white position-relative" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(34, 58, 102, 0.82) 100%), url('/images/bg/22.jpg') center/cover no-repeat;">
      <div class="overlay"></div>
      <div class="container position-relative" style="z-index: 2;">
        <div class="row">
          <div class="col-md-12">
            <div class="block text-center">
              <span class="text-white-50">Online Doctor Scheduling</span>
              <h1 class="text-capitalize mb-2 text-2xl md:text-3xl font-weight-bold text-white" style="color: #ffffff !important;">Book Doctor Consultation</h1>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Wizard Section -->
    <section class="section appoinment gray-bg">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <!-- Multi-step Indicator Bar -->
            <div class="bg-white p-4 rounded shadow-sm mb-4">
              <div class="row text-center align-items-center">
                <div class="col-3 cursor-pointer" @click="currentStep > 1 && !isBooked ? currentStep = 1 : null">
                  <div class="booking-step-badge mb-2" :class="getStepClass(1)">1</div>
                  <div class="small font-weight-bold" :class="currentStep === 1 ? 'text-color-2' : 'text-muted'">Choose Doctor</div>
                </div>
                <div class="col-3 cursor-pointer" @click="currentStep > 2 && !isBooked ? currentStep = 2 : null">
                  <div class="booking-step-badge mb-2" :class="getStepClass(2)">2</div>
                  <div class="small font-weight-bold" :class="currentStep === 2 ? 'text-color-2' : 'text-muted'">Chamber & Slot</div>
                </div>
                <div class="col-3 cursor-pointer" @click="currentStep > 3 && !isBooked ? currentStep = 3 : null">
                  <div class="booking-step-badge mb-2" :class="getStepClass(3)">3</div>
                  <div class="small font-weight-bold" :class="currentStep === 3 ? 'text-color-2' : 'text-muted'">Patient Details</div>
                </div>
                <div class="col-3">
                  <div class="booking-step-badge mb-2" :class="getStepClass(4)">4</div>
                  <div class="small font-weight-bold" :class="currentStep === 4 ? 'text-color-2' : 'text-muted'">Appointment Pass</div>
                </div>
              </div>
            </div>

            <!-- Booking Steps Container -->
            <div class="bg-white p-4 p-md-5 rounded shadow-sm">
              <!-- Error Alert -->
              <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="icofont-warning mr-2"></i> {{ errorMessage }}
                <button type="button" class="close" @click="errorMessage = ''">&times;</button>
              </div>

              <!-- ================= STEP 1: Doctor Selection ================= -->
              <div v-if="currentStep === 1" class="step-1-container">
                <h3 class="font-weight-bold mb-3 title-color">
                  <i class="icofont-stethoscope-alt text-color-2 mr-2"></i> Step 1: Select Specialist Doctor
                </h3>
                <p class="text-muted mb-4">Choose the doctor based on their specialization, consultation fee, and chamber address.</p>

                <!-- Filter by Specialization -->
                <!-- Filter by Specialization & Search Input -->
                <div class="row mb-4">
                  <div class="col-md-6 mb-3 mb-md-0">
                    <label class="font-weight-bold text-dark small mb-1">🩺 বিশেষত্ব অনুযায়ী ফিল্টার</label>
                    <select v-model="selectedSpecialization" class="form-control font-weight-bold" style="border-radius: 8px; border: 1.5px solid #cbd5e1; height: 46px; font-size: 14px; color: #1e293b;">
                      <option value="">সকল বিশেষজ্ঞ ({{ doctors.length }} জন ডাক্তার)</option>
                      <option 
                        v-for="spec in specializations" 
                        :key="spec.key || spec" 
                        :value="spec.key || spec"
                      >
                        {{ spec.name || spec }} {{ spec.count ? `(${spec.count} জন)` : '' }}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="font-weight-bold text-dark small mb-1">🔍 ডাক্তার, চেম্বার বা জেলা খুঁজুন</label>
                    <div class="input-voice-wrapper position-relative">
                      <input 
                        type="text" 
                        v-model="doctorSearch" 
                        data-no-voice
                        class="form-control" 
                        placeholder="যেমন: রংপুর শিশু ডাক্তার, ডা. মেজবাহুল..."
                      >
                      <button 
                        type="button" 
                        class="btn-voice-input-inside"
                        :class="{ 'is-listening': activeVoiceField === 'doctorSearch' }"
                        @click="toggleVoiceField('doctorSearch')"
                        :title="activeVoiceField === 'doctorSearch' ? 'শুনছি... ক্লিক করে থামান' : 'মুখে বলে ডাক্তার খুঁজুন'"
                      >
                        <i class="icofont-mic"></i>
                        <span v-if="activeVoiceField === 'doctorSearch'" class="listening-text">শুনছি...</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Top Alert if a Doctor is already Selected -->
                <div v-if="selectedDoctor" class="alert alert-info d-flex flex-wrap justify-content-between align-items-center mb-4 shadow-sm border" style="border-radius: 10px;">
                  <div class="mb-2 mb-md-0">
                    <span class="badge badge-success mr-2 font-weight-bold">নির্বাচিত ডাক্তার</span>
                    <strong>{{ selectedDoctor.name }}</strong> ({{ selectedDoctor.specialization }}) &bull; <small class="text-muted">{{ selectedDoctor.chamber_address }}</small>
                  </div>
                  <button type="button" class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm" @click="goToStep(2)">
                    স্লট ও তারিখ বেছে নিতে এগিয়ে যান &rarr;
                  </button>
                </div>

                <!-- Doctors Selection Grid -->
                <div class="row">
                  <div v-for="doctor in pagedDoctors" :key="doctor.id" class="col-md-6 mb-3">
                    <div 
                      class="doctor-card-select h-100 d-flex flex-column justify-content-between p-3 border rounded-lg shadow-sm bg-white"
                      :class="{ 'active': form.doctor_id === doctor.id }"
                      style="cursor: pointer; transition: all 0.2s ease; border-radius: 10px;"
                      @click="selectDoctorAndContinue(doctor)"
                    >
                      <div class="d-flex align-items-start">
                        <img 
                          :src="getDoctorAvatar(doctor)" 
                          :alt="doctor.name" 
                          @error="onAvatarError($event, doctor)"
                          class="rounded-circle mr-3 shadow-sm flex-shrink-0" 
                          style="width: 72px; height: 72px; object-fit: cover; border: 2px solid #223a66; background: #f8fafc;"
                        >
                        <div class="flex-grow-1" style="min-width: 0;">
                          <h6 class="mb-1 font-weight-bold text-dark text-truncate" :title="doctor.name">{{ doctor.name }}</h6>
                          <p class="mb-1 text-danger small font-weight-bold text-truncate">{{ doctor.specialization }}</p>
                          <small class="text-muted d-block text-truncate mb-1" :title="doctor.chamber_address">
                            <i class="icofont-location-pin text-danger"></i> {{ doctor.chamber_address }}
                          </small>
                          <div class="d-flex justify-content-between align-items-center mt-1 flex-wrap" style="gap: 4px;">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                              <span class="badge badge-light border text-dark py-1 px-2 font-weight-bold">ফি: ৳{{ doctor.consultation_fee }}</span>
                              <span v-if="doctor.free_consultation_enabled" class="badge badge-success py-1 px-2 font-weight-bold" style="font-size: 10.5px;">
                                🎁 ফ্রি সেবা
                              </span>
                            </div>
                            <span v-if="form.doctor_id === doctor.id" class="badge badge-success">
                              <i class="icofont-check-circled"></i> Selected
                            </span>
                          </div>
                        </div>
                      </div>

                      <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                          <i class="icofont-clock-time text-primary mr-1"></i>{{ doctor.start_time ? doctor.start_time.slice(0, 5) : '09:00' }} - {{ doctor.end_time ? doctor.end_time.slice(0, 5) : '17:00' }}
                        </small>
                        <button 
                          type="button" 
                          class="btn btn-sm btn-round-full px-3 py-1 font-weight-bold shadow-sm transition"
                          :class="form.doctor_id === doctor.id ? 'btn-success text-white' : 'btn-main-2'"
                          @click.stop="selectDoctorAndContinue(doctor)"
                        >
                          <span v-if="form.doctor_id === doctor.id">
                            <i class="icofont-check mr-1"></i> স্লট নির্বাচিত
                          </span>
                          <span v-else>
                            স্লট বেছে নিন <i class="icofont-simple-right ml-1"></i>
                          </span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Load More / Pagination -->
                <div v-if="filteredDoctors.length > pagedDoctors.length" class="text-center mt-3 pt-2">
                  <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold px-4 py-2 rounded-pill" @click="doctorDisplayLimit += 24">
                    আরো ডাক্তার দেখুন (বাকি {{ filteredDoctors.length - pagedDoctors.length }} জন) <i class="icofont-rounded-down ml-1"></i>
                  </button>
                </div>

                <div class="text-right mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                  <span class="text-muted small">মোট ডাক্তার: <strong>{{ filteredDoctors.length }}</strong> জন</span>
                  <button 
                    class="btn btn-main-2 btn-round-full px-4 font-weight-bold"
                    :disabled="!form.doctor_id"
                    @click="goToStep(2)"
                  >
                    Continue to Chamber & Slot <i class="icofont-simple-right ml-2"></i>
                  </button>
                </div>
              </div>

              <!-- ================= STEP 2: Date & Available Slot Picker ================= -->
              <div v-if="currentStep === 2" class="step-2-container">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3" style="gap: 8px;">
                  <h3 class="font-weight-bold mb-0 title-color" style="font-size: 18px;">
                    <i class="icofont-calendar text-color-2 mr-1"></i> Step 2: Choose Date & Slot
                  </h3>
                  <button class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-1 rounded-pill" @click="currentStep = 1" style="font-size: 12px;">
                    <i class="icofont-arrow-left mr-1"></i> অন্য ডাক্তার বেছে নিন
                  </button>
                </div>

                <!-- Selected Doctor Chamber Info Card (Centered Image on Top) -->
                <div v-if="selectedDoctor" class="bg-white p-3 p-md-4 rounded-lg mb-4 border shadow-sm text-center" style="border-radius: 14px;">
                  <div class="d-flex flex-column align-items-center">
                    <!-- Top Centered Avatar with Verified Badge -->
                    <div class="position-relative mb-2">
                      <img 
                        :src="getDoctorAvatar(selectedDoctor)" 
                        :alt="selectedDoctor.name"
                        @error="onAvatarError($event, selectedDoctor)"
                        class="rounded-circle shadow" 
                        style="width: 90px; height: 90px; object-fit: cover; border: 3px solid #223a66; background: #f8fafc;"
                      >
                      <span class="badge badge-success position-absolute px-2 py-1 shadow-sm" style="bottom: -2px; right: -6px; font-size: 10px; border-radius: 12px; border: 2px solid #fff;">
                        <i class="icofont-check-circled"></i> ভেরিফাইড
                      </span>
                    </div>

                    <!-- Doctor Name & Specialization -->
                    <h5 class="mb-1 font-weight-bold text-dark text-center" style="font-size: 16px; line-height: 1.3;">{{ selectedDoctor.name }}</h5>
                    <div class="d-flex flex-column align-items-center justify-content-center mb-2" style="gap: 6px;">
                      <span class="badge badge-light border text-danger font-weight-bold px-2 py-1 text-center" style="font-size: 11px; white-space: normal; line-height: 1.4; max-width: 100%;">
                        <i class="icofont-stethoscope mr-1"></i>{{ selectedDoctor.specialization }}
                      </span>
                      <div class="d-flex align-items-center justify-content-center flex-wrap" style="gap: 6px;">
                        <span 
                          class="badge font-weight-bold px-3 py-1" 
                          :class="(slotInfo && slotInfo.is_free_day) ? 'badge-success' : 'badge-primary'"
                          style="font-size: 12px; border-radius: 20px;"
                        >
                          {{ (slotInfo && slotInfo.is_free_day) ? 'পরামর্শ ফি: ৳০ (ফ্রি কনসালটেশন)' : 'পরামর্শ ফি: ৳' + selectedDoctor.consultation_fee }}
                        </span>
                        <span v-if="selectedDoctor.free_consultation_enabled" class="badge badge-success font-weight-bold px-2 py-1" style="font-size: 11px; border-radius: 20px;">
                          🎁 {{ (selectedDoctor.free_days && selectedDoctor.free_days.length) ? selectedDoctor.free_days.join(', ') : 'শুক্রবার' }} ফ্রি সেবা
                        </span>
                      </div>
                    </div>

                    <!-- Chamber & Serial Hotline Box -->
                    <div class="bg-light p-2 rounded-lg w-100 border text-left mt-1" style="max-width: 520px; font-size: 12px;">
                      <div class="text-muted mb-1 d-flex align-items-start">
                        <i class="icofont-hospital text-primary mr-2 mt-1 flex-shrink-0" style="font-size: 14px;"></i> 
                        <span><strong>চেম্বার:</strong> {{ selectedDoctor.chamber_address }}</span>
                      </div>
                      <div v-if="selectedDoctor.phone" class="text-muted d-flex align-items-center">
                        <i class="icofont-phone text-success mr-2 flex-shrink-0" style="font-size: 14px;"></i> 
                        <span><strong>সিরিয়াল ফোন:</strong> <a :href="'tel:' + selectedDoctor.phone" class="text-dark font-weight-bold">{{ selectedDoctor.phone }}</a></span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Special Free Friday / Charity Clinic Banner (When doctor has free consultation enabled) -->
                <div v-if="selectedDoctor?.free_consultation_enabled" class="alert alert-success border-success bg-white shadow-sm p-3 p-md-4 mb-4 rounded-12 position-relative">
                  <div class="d-flex align-items-center justify-content-between flex-wrap mb-2" style="gap: 8px;">
                    <div class="d-flex align-items-center">
                      <span class="font-24 mr-2" style="font-size: 24px;">🎁</span>
                      <div>
                        <h6 class="font-weight-bold text-success mb-0" style="font-size: 15px;">
                          ফ্রি কনসালটেশন ও চ্যারিটি ক্লিনিক (Free Consultation Service)
                        </h6>
                        <small class="text-muted">দরিদ্র ও সাধারণ রোগীদের জন্য সম্পূর্ণ বিনামূল্যে পরামর্শ সেবা</small>
                      </div>
                    </div>
                    <span class="badge badge-success px-3 py-1 font-weight-bold" style="font-size: 12px; border-radius: 20px;">
                      ফি: ৳০ (সম্পূর্ণ ফ্রি)
                    </span>
                  </div>

                  <p class="small text-dark mb-3 leading-relaxed">
                    {{ selectedDoctor.name }} সপ্তাহে প্রতি <strong>{{ (selectedDoctor.free_days && selectedDoctor.free_days.length) ? selectedDoctor.free_days.join(', ') : 'শুক্রবার' }}</strong> সাধারণ ও সুবিধাবঞ্চিত রোগীদের বিনামূল্যে চিকিৎসা পরামর্শ প্রদান করেন।
                  </p>

                  <div class="row g-2 small mb-3">
                    <div class="col-sm-4 mb-2 mb-sm-0">
                      <div class="bg-light p-2 rounded border h-100">
                        <strong class="text-muted d-block" style="font-size: 11px;">ফ্রি বার:</strong>
                        <span class="font-weight-bold text-dark">{{ (selectedDoctor.free_days && selectedDoctor.free_days.length) ? selectedDoctor.free_days.join(', ') : 'শুক্রবার (Friday)' }}</span>
                      </div>
                    </div>
                    <div class="col-sm-4 mb-2 mb-sm-0">
                      <div class="bg-light p-2 rounded border h-100">
                        <strong class="text-muted d-block" style="font-size: 11px;">কোটা ও সময়:</strong>
                        <span class="font-weight-bold text-primary">সর্বোচ্চ {{ selectedDoctor.free_patient_limit || 20 }} জন (সকাল ০৯:০০ - ১২:০০)</span>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="bg-light p-2 rounded border h-100">
                        <strong class="text-muted d-block" style="font-size: 11px;">পরামর্শ ফি:</strong>
                        <span class="font-weight-bold text-success">৳০ (ফ্রি রেজিস্ট্রেশন)</span>
                      </div>
                    </div>
                    <div v-if="selectedDoctor.free_chamber_address" class="col-12 mt-2">
                      <div class="bg-light p-2 rounded border">
                        <strong class="text-muted d-block" style="font-size: 11px;">ফ্রি চেম্বার লোকেশন:</strong>
                        <span class="font-weight-bold text-dark">
                          <i class="icofont-location-pin text-danger mr-1"></i>{{ selectedDoctor.free_chamber_address }}
                        </span>
                      </div>
                    </div>
                    <div v-if="selectedDoctor.free_service_note" class="col-12 mt-1">
                      <div class="text-muted" style="font-size: 11.5px;">
                        <i class="icofont-info-circle text-info mr-1"></i>{{ selectedDoctor.free_service_note }}
                      </div>
                    </div>
                  </div>

                  <div class="d-flex align-items-center justify-content-between flex-wrap pt-2 border-top" style="gap: 8px;">
                    <div class="small">
                      <span v-if="slotInfo && slotInfo.is_free_day" class="text-success font-weight-bold">
                        <i class="icofont-check-circled mr-1"></i> ফ্রি কনসালটেশন ডে ও স্লট সক্রিয় করা হয়েছে
                      </span>
                      <span v-else class="text-muted">
                        ফ্রি স্লট বুক করতে নিচের বাটনে ক্লিক করুন:
                      </span>
                    </div>

                    <button 
                      type="button" 
                      class="btn btn-sm font-weight-bold px-3 py-2 rounded-pill shadow-sm transition"
                      :class="(slotInfo && slotInfo.is_free_day) ? 'btn-outline-success font-weight-bold' : 'btn-success text-white'"
                      @click="selectFreeConsultationDay"
                    >
                      <i class="icofont-gift mr-1"></i>
                      {{ (slotInfo && slotInfo.is_free_day) ? '✓ ফ্রি দিন নির্বাচিত হয়েছে (স্লট নিচে দেখুন)' : '🎁 ফ্রি কনসালটেশন স্লট বুক করুন (পরবর্তী ' + ((selectedDoctor.free_days && selectedDoctor.free_days.length) ? selectedDoctor.free_days[0] : 'শুক্রবার') + ') →' }}
                    </button>
                  </div>
                </div>

                <!-- Multiple Chambers Selector for Doctor -->
                <div v-if="selectedDoctor?.chambers && selectedDoctor.chambers.length > 0" class="mb-4">
                  <label class="font-weight-bold text-dark d-flex align-items-center mb-2" style="font-size: 15px;">
                    <i class="icofont-hospital text-danger mr-2"></i> চেম্বার নির্বাচন করুন (Select Chamber)
                    <span class="badge badge-light border text-muted ml-2 font-weight-normal small">{{ selectedDoctor.chambers.length }}টি চেম্বার উপলব্ধ</span>
                  </label>
                  <div class="row">
                    <div 
                      v-for="ch in selectedDoctor.chambers" 
                      :key="ch.id" 
                      class="col-md-6 mb-2"
                    >
                      <div 
                        class="p-3 rounded-12 border cursor-pointer transition-all h-100 position-relative shadow-sm"
                        :style="selectedChamberId === ch.id ? 'border: 2px solid #e12454 !important; background: #fff5f7;' : 'border: 1px solid #e2e8f0; background: #ffffff;'"
                        @click="selectChamber(ch)"
                      >
                        <div class="d-flex justify-content-between align-items-start mb-1">
                          <strong class="text-dark font-weight-bold d-block" style="font-size: 14px;">
                            {{ ch.chamber_name }}
                          </strong>
                          <span v-if="ch.is_primary" class="badge badge-danger px-2 py-0" style="font-size: 10px;">প্রধান</span>
                        </div>
                        <div v-if="ch.branch_name" class="text-muted small mb-1">
                          <i class="icofont-location-pin text-danger"></i> {{ ch.branch_name }}
                        </div>
                        <div class="small text-muted mb-2 text-truncate" :title="ch.address">
                          {{ ch.address }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top small">
                          <span class="text-success font-weight-bold">ফি: ৳{{ ch.consultation_fee || selectedDoctor.consultation_fee }}</span>
                          <span class="text-muted"><i class="icofont-clock-time"></i> {{ formatTime(ch.start_time) }} - {{ formatTime(ch.end_time) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Date Picker -->
                <div class="form-group mb-4">
                  <label class="font-weight-bold text-dark">Select Consultation Date</label>
                  <input 
                    type="date" 
                    v-model="form.appointment_date" 
                    class="form-control"
                    :min="minDate"
                    @change="fetchSlots"
                  >
                </div>

                <!-- Slot Loading State -->
                <div v-if="loadingSlots" class="text-center py-4">
                  <div class="spinner-border text-danger" role="status"></div>
                  <p class="text-muted mt-2">Checking doctor's real-time chamber schedule...</p>
                </div>

                <!-- Slots Grid -->
                <div v-else>
                  <!-- Free Day Charity Banner -->
                  <div v-if="slotInfo && slotInfo.is_free_day" class="alert alert-success border-success bg-white shadow-sm p-3 mb-3 rounded-lg">
                    <div class="d-flex align-items-center mb-1">
                      <span class="font-22 mr-2">🎁</span>
                      <h6 class="font-weight-bold text-success mb-0">ফ্রি কনসালটেশন দিন (Free Consultation Day)</h6>
                    </div>
                    <p class="small text-dark mb-2">
                      আজকের দিনে ডাঃ {{ selectedDoctor?.name }} সাধারণ ও দরিদ্র রোগীদের <strong>বিনামূল্যে (৳০ ফি)</strong> সেবা দিচ্ছেন।
                    </p>
                    <div class="d-flex flex-wrap gap-2 small">
                      <span class="badge badge-success px-2 py-1">ফি: ৳০ (সম্পূর্ণ ফ্রি)</span>
                      <span v-if="slotInfo.remaining_quota !== null" class="badge" :class="slotInfo.remaining_quota > 0 ? 'badge-primary' : 'badge-danger'" style="padding: 4px 8px;">
                        {{ slotInfo.remaining_quota > 0 ? `অবশিষ্ট ফ্রি কোটা: ${slotInfo.remaining_quota} জন (মোট ${slotInfo.free_patient_limit} জন)` : 'ফ্রি কোটা পূর্ণ হয়ে গেছে' }}
                      </span>
                      <span v-if="slotInfo.free_chamber_address" class="badge badge-light border text-dark">
                        📍 চেম্বার: {{ slotInfo.free_chamber_address }}
                      </span>
                    </div>
                    <div v-if="slotInfo.free_service_note" class="mt-2 text-muted small">
                      <i class="icofont-info-circle text-info mr-1"></i> {{ slotInfo.free_service_note }}
                    </div>
                  </div>

                  <div v-if="availableSlots.length > 0">
                    <label class="font-weight-bold text-dark mb-2">Available Slots for {{ formattedDate }}</label>
                    <div class="row">
                      <div v-for="(slot, idx) in availableSlots" :key="idx" class="col-6 col-sm-4 col-md-3 mb-3">
                        <button 
                          type="button" 
                          class="time-slot-btn w-100"
                          :class="{ 'selected': form.appointment_time === slot.time }"
                          :disabled="!slot.available"
                          @click="selectSlot(slot.time)"
                        >
                          <i class="icofont-clock-time mr-1"></i> {{ slot.time }}
                          <div v-if="slot.is_booked" class="small text-danger" style="font-size: 10px;">Booked</div>
                          <div v-else-if="slot.is_past" class="small text-muted" style="font-size: 10px;">Passed</div>
                          <div v-else class="small text-success" style="font-size: 10px;">Available</div>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-else-if="form.appointment_date" class="alert alert-warning">
                    <i class="icofont-exclamation-tringle mr-2"></i> The doctor is not available at their chamber on this date. Please pick another date.
                  </div>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                  <button class="btn btn-main btn-round-full px-4" @click="currentStep = 1">
                    <i class="icofont-simple-left mr-2"></i> Back
                  </button>
                  <button 
                    class="btn btn-main-2 btn-round-full px-4"
                    :disabled="!form.appointment_time"
                    @click="goToStep(3)"
                  >
                    Enter Patient Details <i class="icofont-simple-right ml-2"></i>
                  </button>
                </div>
              </div>

              <!-- ================= STEP 3: Patient Information Form ================= -->
              <div v-if="currentStep === 3" class="step-3-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h3 class="font-weight-bold mb-0 title-color">
                    <i class="icofont-ui-user text-color-2 mr-2"></i> Step 3: Patient Information & Payment
                  </h3>
                  <button class="btn btn-sm btn-outline-secondary" @click="currentStep = 2">
                    <i class="icofont-arrow-left mr-1"></i> Change Slot
                  </button>
                </div>

                <!-- Booking Summary Card -->
                <div class="bg-light p-3 rounded mb-4">
                  <div class="row">
                    <div class="col-md-6">
                      <p class="mb-1 text-muted small">Specialist & Chamber:</p>
                      <h6 class="font-weight-bold mb-0 text-dark">{{ selectedDoctor?.name }} ({{ selectedDoctor?.specialization }})</h6>
                      <small class="text-muted d-block mt-1">
                        <i class="icofont-hospital text-danger mr-1"></i>
                        {{ selectedChamber ? (selectedChamber.chamber_name + (selectedChamber.branch_name ? ' - ' + selectedChamber.branch_name : '') + ', ' + selectedChamber.address) : (selectedDoctor?.chamber_address || 'হাসপাতাল চেম্বার') }}
                      </small>
                    </div>
                    <div class="col-md-6 text-md-right mt-2 mt-md-0">
                      <p class="mb-1 text-muted small">Scheduled Time & Fee:</p>
                      <h6 class="font-weight-bold text-color-2 mb-0">{{ formatDate(form.appointment_date) }} at {{ form.appointment_time }}</h6>
                      <span v-if="slotInfo && slotInfo.is_free_day" class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 12px; background-color: #28a745;">
                        <i class="icofont-gift mr-1"></i> ফ্রি চ্যারিটি কনসালটেশন (ফি: ৳০)
                      </span>
                      <span v-else class="badge badge-success">Consultation Fee: ৳{{ selectedChamber?.consultation_fee || selectedDoctor?.consultation_fee }}</span>
                    </div>
                  </div>
                </div>

                <form @submit.prevent="submitBooking">
                  <div class="row">
                    <!-- Patient Full Name -->
                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark mb-1">
                        রোগীর নাম (Patient Full Name) <span class="text-danger">*</span>
                      </label>
                      <div class="input-voice-wrapper position-relative">
                        <input 
                          type="text" 
                          v-model="form.patient_name" 
                          data-no-voice
                          class="form-control" 
                          placeholder="যেমনঃ মোঃ রহিম মিয়া..."
                          required
                        >
                        <button 
                          type="button" 
                          class="btn-voice-input-inside"
                          :class="{ 'is-listening': activeVoiceField === 'patient_name' }"
                          @click="toggleVoiceField('patient_name')"
                          :title="activeVoiceField === 'patient_name' ? 'শুনছি... ক্লিক করে থামান' : 'মুখে বলে রোগীর নাম লিখুন'"
                        >
                          <i class="icofont-mic"></i>
                          <span v-if="activeVoiceField === 'patient_name'" class="listening-text">শুনছি...</span>
                        </button>
                      </div>
                    </div>

                    <!-- Email Address -->
                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark mb-1">
                        ইমেইল এড্রেস (Email Address) <span class="text-danger">*</span>
                      </label>
                      <div class="input-voice-wrapper position-relative">
                        <input 
                          type="email" 
                          v-model="form.patient_email" 
                          data-no-voice
                          class="form-control" 
                          placeholder="যেমনঃ patient@gmail.com"
                          required
                        >
                        <button 
                          type="button" 
                          class="btn-voice-input-inside"
                          :class="{ 'is-listening': activeVoiceField === 'patient_email' }"
                          @click="toggleVoiceField('patient_email')"
                          :title="activeVoiceField === 'patient_email' ? 'শুনছি... ক্লিক করে থামান' : 'মুখে বলে ইমেইল লিখুন'"
                        >
                          <i class="icofont-mic"></i>
                          <span v-if="activeVoiceField === 'patient_email'" class="listening-text">শুনছি...</span>
                        </button>
                      </div>
                    </div>

                    <!-- Contact Phone Number -->
                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark mb-1">
                        মোবাইল নম্বর (Contact Phone Number) <span class="text-danger">*</span>
                      </label>
                      <div class="input-voice-wrapper position-relative">
                        <input 
                          type="tel" 
                          v-model="form.patient_phone" 
                          data-no-voice
                          class="form-control" 
                          placeholder="যেমনঃ 017XXXXXXXX"
                          required
                        >
                        <button 
                          type="button" 
                          class="btn-voice-input-inside"
                          :class="{ 'is-listening': activeVoiceField === 'patient_phone' }"
                          @click="toggleVoiceField('patient_phone')"
                          :title="activeVoiceField === 'patient_phone' ? 'শুনছি... ক্লিক করে থামান' : 'মুখে বলে মোবাইল নম্বর লিখুন'"
                        >
                          <i class="icofont-mic"></i>
                          <span v-if="activeVoiceField === 'patient_phone'" class="listening-text">শুনছি...</span>
                        </button>
                      </div>
                    </div>

                    <!-- Consultation Mode: Online Video vs In-Person -->
                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark">Consultation Mode (পরামর্শের ধরন) <span class="text-danger">*</span></label>
                      <select v-model="form.consultation_type" class="form-control font-weight-bold text-primary">
                        <option value="online_video">🎥 Online Live Video Consultation (ঘরে বসে ভিডিও কনসাল্টেশন)</option>
                        <option value="in_chamber">🏥 In-Person Chamber Visit (চেম্বারে সরাসরি সাক্ষাৎ)</option>
                      </select>
                    </div>

                    <div v-if="!slotInfo || !slotInfo.is_free_day" class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold text-dark">Payment Method (পেমেন্ট পদ্ধতি) <span class="text-danger">*</span></label>
                      <select v-model="form.payment_method" class="form-control font-weight-bold">
                        <option value="online_card">💳 Credit / Debit Card (Visa, Mastercard, Amex)</option>
                        <option value="bkash">📱 bKash / Nagad / Rocket (Mobile Banking)</option>
                        <option value="cash">💵 Pay at Doctor Chamber Counter (নগদ পেমেন্ট)</option>
                      </select>
                    </div>

                    <!-- Free Consultation Notice instead of payment for free days -->
                    <div v-else class="col-12 mb-3">
                      <div class="p-3 bg-light border border-success rounded text-dark shadow-sm">
                        <div class="d-flex align-items-center">
                          <span class="font-24 mr-2">🎁</span>
                          <div>
                            <h6 class="mb-1 font-weight-bold text-success">ফ্রি কনসালটেশন - কোনো ফি প্রযোজ্য নয় (৳০ ফি)</h6>
                            <p class="small text-muted mb-0">
                              এটি সম্পূর্ণ বিনামূল্যে মানবসেবামূলক সেবা। কোনো কার্ড বা মোবাইল ব্যাংকিং ছাড়াই নিচের বাটনে চাপ দিয়ে সরাসরি সিরিয়াল নিশ্চিত করুন।
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Dynamic Payment Details Box -->
                    <div v-if="!slotInfo || !slotInfo.is_free_day" class="col-12 mb-3">
                      <!-- 1. Card Payment Fields -->
                      <div v-if="form.payment_method === 'online_card'" class="p-3 bg-light border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                          <h6 class="mb-0 font-weight-bold text-dark"><i class="icofont-credit-card text-primary mr-1"></i> Card Information</h6>
                          <div class="small font-weight-bold text-muted">
                            <span class="badge badge-light border mr-1">VISA</span>
                            <span class="badge badge-light border mr-1">Mastercard</span>
                            <span class="badge badge-light border">AMEX</span>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6 form-group mb-2">
                            <label class="small font-weight-bold text-dark mb-1">Card Number <span class="text-danger">*</span></label>
                            <input 
                              type="text" 
                              v-model="cardDetails.number" 
                              class="form-control form-control-sm font-weight-bold" 
                              placeholder="4111 2222 3333 4444"
                              maxlength="19"
                              required
                            >
                          </div>
                          <div class="col-md-6 form-group mb-2">
                            <label class="small font-weight-bold text-dark mb-1">Cardholder Name <span class="text-danger">*</span></label>
                            <input 
                              type="text" 
                              v-model="cardDetails.name" 
                              class="form-control form-control-sm" 
                              placeholder="Name on card"
                              required
                            >
                          </div>
                          <div class="col-6 form-group mb-0">
                            <label class="small font-weight-bold text-dark mb-1">Expiry Date <span class="text-danger">*</span></label>
                            <input 
                              type="text" 
                              v-model="cardDetails.expiry" 
                              class="form-control form-control-sm text-center" 
                              placeholder="MM/YY"
                              maxlength="5"
                              required
                            >
                          </div>
                          <div class="col-6 form-group mb-0">
                            <label class="small font-weight-bold text-dark mb-1">CVC / CVV <span class="text-danger">*</span></label>
                            <input 
                              type="password" 
                              v-model="cardDetails.cvc" 
                              class="form-control form-control-sm text-center" 
                              placeholder="123"
                              maxlength="4"
                              required
                            >
                          </div>
                        </div>
                      </div>

                      <!-- 2. bKash / Mobile Banking Fields -->
                      <div v-else-if="form.payment_method === 'bkash'" class="p-3 bg-light border rounded" style="background-color: #fdf2f8 !important; border-color: #fbcfe8 !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                          <h6 class="mb-0 font-weight-bold text-danger"><i class="icofont-smart-phone text-danger mr-1"></i> bKash / Nagad / Rocket Payment</h6>
                          <span class="badge badge-danger">Merchant: 01700-123456</span>
                        </div>
                        <div class="small text-muted mb-3">
                          পরামর্শ ফি <strong>${{ selectedDoctor?.consultation_fee }}</strong> উপরের মার্চেন্ট নাম্বারে Send Money / Payment করুন এবং নিচের বক্সে আপনার নাম্বার ও TrxID দিন:
                        </div>
                        <div class="row">
                          <div class="col-md-6 form-group mb-2">
                            <label class="small font-weight-bold text-dark mb-1">Sender Mobile Number (আপনার মোবাইল নাম্বার) <span class="text-danger">*</span></label>
                            <input 
                              type="tel" 
                              v-model="bkashDetails.senderNumber" 
                              class="form-control form-control-sm font-weight-bold" 
                              placeholder="e.g. 017XXXXXXXX"
                              required
                            >
                          </div>
                          <div class="col-md-6 form-group mb-2">
                            <label class="small font-weight-bold text-dark mb-1">Transaction ID (TrxID) <span class="text-danger">*</span></label>
                            <input 
                              type="text" 
                              v-model="bkashDetails.trxId" 
                              class="form-control form-control-sm font-weight-bold uppercase-input" 
                              placeholder="e.g. 9J8K7L6M5N"
                              required
                            >
                          </div>
                        </div>
                      </div>

                      <!-- 3. Cash on Visit Information -->
                      <div v-else-if="form.payment_method === 'cash'" class="p-3 bg-light border rounded text-dark">
                        <div class="d-flex align-items-center">
                          <i class="icofont-info-circle text-success mr-2" style="font-size: 24px;"></i>
                          <div>
                            <h6 class="mb-1 font-weight-bold text-dark">Pay at Doctor Chamber Counter (নগদ পেমেন্ট)</h6>
                            <p class="small text-muted mb-0">
                              কোনো অগ্রিম পেমেন্টের প্রয়োজন নেই। অ্যাপয়েন্টমেন্ট কনফার্ম হয়ে যাবে এবং ডাক্তারের চেম্বারে প্রবেশের পূর্বে রিসিপশন কাউন্টারে সরাসরি ফি প্রদান করবেন।
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Symptoms & Reason for Consultation with Inside Voice Dictation -->
                    <div class="col-12 form-group mb-4">
                      <div class="d-flex justify-content-between align-items-center mb-1.5 flex-wrap" style="gap: 8px;">
                        <label class="font-weight-bold text-dark mb-0 d-flex align-items-center flex-wrap" style="gap: 6px;">
                          <i class="icofont-stethoscope-alt text-primary mr-1"></i>
                          <span>Symptoms / Reason for Consultation (রোগের লক্ষণ ও সমস্যা)</span>
                          <span class="badge badge-light border text-muted">Optional</span>
                        </label>
                        <span class="badge badge-success px-2 py-0.5 font-weight-bold small d-none d-sm-inline-block" style="font-size: 11px;">
                          🇧🇩 বাংলা ভয়েস সমর্থিত
                        </span>
                      </div>

                      <!-- Textarea with Integrated Inside Voice Button -->
                      <div class="textarea-voice-wrapper position-relative">
                        <textarea 
                          v-model="form.reason_for_visit" 
                          data-no-voice
                          class="form-control" 
                          rows="4" 
                          placeholder="যেমন: তীব্র মাথা ব্যথা, ২ দিন ধরে জ্বর, বুকে ব্যথা বা প্রেসারের সমস্যা... (টাইপ করুন অথবা ভেতরের '🎙️ মুখে বলুন' বাটনে চাপ দিন)"
                        ></textarea>

                        <!-- Live Listening Feedback Overlay inside textarea -->
                        <div v-if="isListeningVoice" class="textarea-listening-bar d-flex align-items-center">
                          <span class="spinner-grow spinner-grow-sm text-danger mr-2 flex-shrink-0" role="status"></span>
                          <span class="text-danger font-weight-bold small text-truncate">
                            🔴 শুনছি... বাংলায় বলুন<span v-if="voiceInterimText">: "{{ voiceInterimText }}"</span>
                          </span>
                        </div>

                        <!-- Inside Voice Action Button (Bottom-Right) -->
                        <div class="textarea-voice-action">
                          <button 
                            type="button" 
                            class="btn-voice-textarea-inside"
                            :class="{ 'is-listening': isListeningVoice }"
                            @click="toggleVoiceDictation"
                            :title="isListeningVoice ? 'রেকর্ডিং বন্ধ করুন' : 'বাংলায় মুখে বলে সমস্যা লিখুন'"
                          >
                            <i :class="isListeningVoice ? 'icofont-stop' : 'icofont-mic'"></i>
                            <span class="voice-label">{{ isListeningVoice ? 'থামান (Stop)' : 'মুখে বলুন (Voice)' }}</span>
                          </button>
                        </div>
                      </div>

                      <!-- Quick Symptom Tags (1-Click Insertion) -->
                      <div class="mt-2 d-flex align-items-center flex-wrap" style="gap: 6px;">
                        <span class="small text-muted font-weight-bold mr-1">কমন সমস্যাসমূহ:</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 11px;" @click="addQuickSymptom('জ্বর ও শরীর ব্যথা')">+ জ্বর ও শরীর ব্যথা</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 11px;" @click="addQuickSymptom('তীব্র মাথা ব্যথা')">+ তীব্র মাথা ব্যথা</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 11px;" @click="addQuickSymptom('বুকে ব্যথা ও শ্বাসকষ্ট')">+ বুকে ব্যথা / শ্বাসকষ্ট</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 11px;" @click="addQuickSymptom('পেটে ব্যথা ও বদহজম')">+ পেটে ব্যথা / গ্যাস্ট্রিক</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 11px;" @click="addQuickSymptom('উচ্চ রক্তচাপ ও ডায়াবেটিস চেকআপ')">+ প্রেসার / ডায়াবেটিস</button>
                      </div>

                      <small class="text-muted d-block mt-2">
                        <i class="icofont-info-circle mr-1 text-primary"></i> আপনি সম্পূর্ণ বাংলায় মুখে বলে আপনার সমস্যার কথা নির্ভুলভাবে লিখতে পারেন অথবা কিবোর্ডে টাইপ করতে পারেন।
                      </small>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between pt-3 border-top">
                    <button type="button" class="btn btn-main btn-round-full px-4" @click="currentStep = 2">
                      <i class="icofont-simple-left mr-2"></i> Back
                    </button>
                    <button 
                      type="submit" 
                      class="btn btn-main-2 btn-round-full px-5 shadow-sm"
                      :disabled="submitting"
                    >
                      <span v-if="submitting">
                        <span class="spinner-border spinner-border-sm mr-2" role="status"></span> Confirming...
                      </span>
                      <span v-else-if="slotInfo && slotInfo.is_free_day">
                        <i class="icofont-gift mr-1"></i> ফ্রি সিরিয়াল বুক করুন (Confirm Free Serial) &rarr;
                      </span>
                      <span v-else>
                        <i class="icofont-check-circled mr-1"></i> Confirm & Book Consultation
                      </span>
                    </button>
                  </div>
                </form>
              </div>

              <!-- ================= STEP 4: Confirmed Appointment Pass ================= -->
              <div v-if="currentStep === 4 && bookedAppointment" class="step-4-container text-center py-2">
                <div class="mb-4">
                  <i class="icofont-check-circled display-2 text-success"></i>
                  <h2 class="font-weight-bold title-color mt-2">
                    {{ bookedAppointment.is_free_consultation ? 'ফ্রি অ্যাপয়েন্টমেন্ট সফলভাবে নিশ্চিত হয়েছে!' : 'Appointment Confirmed Successfully!' }}
                  </h2>
                  <p class="text-muted">
                    {{ bookedAppointment.is_free_consultation ? 'আপনার ফ্রি ডিজিটাল সিরিয়াল পাস তৈরি হয়েছে। চেম্বারে গিয়ে এই টোকেন নাম্বারটি দেখান।' : 'Your official digital appointment pass is ready. Please bring this pass to the doctor\'s chamber.' }}
                  </p>
                </div>

                <!-- Printable Appointment Pass Card -->
                <div class="appointment-pass mx-auto text-left mb-4" id="appointment-ticket" style="max-width: 650px;">
                  <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center">
                      <img src="/images/logo/Logo2.png" alt="সুখী মানুষ (Sukhi Manush)" style="height: 42px;" class="mr-3">
                      <div>
                        <h6 class="font-weight-bold mb-0 text-dark">সুখী মানুষ (Sukhi Manush)</h6>
                        <span class="small text-muted">Official Doctor Appointment Pass</span>
                      </div>
                    </div>
                    <div>
                      <span class="status-pill confirmed">Confirmed</span>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-6 mb-3">
                      <span class="text-muted small">Tracking Reference:</span>
                      <h4 class="font-weight-bold text-color-2 mb-0">{{ bookedAppointment.appointment_number }}</h4>
                    </div>
                    <div class="col-sm-6 mb-3 text-sm-right">
                      <span class="text-muted small">Doctor Serial Number:</span>
                      <h4 class="font-weight-bold text-dark mb-0">#{{ bookedAppointment.serial_number }}</h4>
                    </div>

                    <div class="col-sm-6 mb-3">
                      <span class="text-muted small">Patient Name:</span>
                      <h6 class="font-weight-bold text-dark mb-0">{{ bookedAppointment.patient_name }}</h6>
                      <small class="text-muted">{{ bookedAppointment.patient_phone }}</small>
                    </div>
                    <div class="col-sm-6 mb-3 text-sm-right">
                      <span class="text-muted small">Specialist Doctor:</span>
                      <h6 class="font-weight-bold text-dark mb-0">{{ bookedAppointment.doctor?.name }}</h6>
                      <small class="text-danger font-weight-bold">{{ bookedAppointment.doctor?.specialization }}</small>
                    </div>

                    <div class="col-sm-6 mb-3">
                      <span class="text-muted small">Chamber Address:</span>
                      <h6 class="font-weight-bold text-dark mb-0">
                        {{ bookedAppointment.chamber ? (bookedAppointment.chamber.chamber_name + (bookedAppointment.chamber.branch_name ? ' - ' + bookedAppointment.chamber.branch_name : '') + ', ' + bookedAppointment.chamber.address) : (bookedAppointment.doctor?.chamber_address || 'চেম্বার') }}
                      </h6>
                    </div>
                    <div class="col-sm-6 mb-3 text-sm-right">
                      <span class="text-muted small">Appointment Date & Time:</span>
                      <h6 class="font-weight-bold text-color-2 mb-0">
                        {{ formatDate(bookedAppointment.appointment_date) }} at {{ bookedAppointment.appointment_time }}
                      </h6>
                    </div>

                    <!-- Free Consultation Pass Badge -->
                    <div v-if="bookedAppointment.is_free_consultation || bookedAppointment.consultation_fee == 0" class="col-12 mb-3">
                      <div class="alert alert-success py-2 px-3 small font-weight-bold d-flex align-items-center mb-0" style="border-radius: 8px;">
                        <i class="icofont-gift font-20 mr-2 text-success"></i>
                        <span>ফ্রি চ্যারিটি কনসালটেশন (পরামর্শ ফি: ৳০ - সম্পূর্ণ ফ্রি মানবসেবা)</span>
                      </div>
                    </div>
                  </div>

                  <div v-if="bookedAppointment.consultation_type === 'online_video' || bookedAppointment.meeting_link" class="border-top pt-3 mt-2 bg-light p-3 rounded text-center border-success">
                    <span class="badge badge-success px-3 py-1 mb-2" style="font-size: 13px;">🎥 Online Live Video Consultation</span>
                    <p class="small text-muted mb-2">
                      You can join the doctor's secure video consultation room directly from your phone or computer:
                    </p>
                    <a :href="'/video-chamber/' + bookedAppointment.appointment_number" target="_blank" class="btn btn-success btn-sm font-weight-bold px-4 shadow-sm">
                      <i class="icofont-video-cam mr-1"></i> Join Live Video Call Now
                    </a>
                  </div>

                  <div v-else class="border-top pt-3 mt-2 bg-light p-3 rounded">
                    <p class="small text-muted mb-0">
                      <i class="icofont-location-pin text-color-2 mr-1"></i> <strong>Chamber Visit:</strong> Please arrive 15 minutes before your scheduled slot at {{ bookedAppointment.chamber ? (bookedAppointment.chamber.chamber_name + ', ' + bookedAppointment.chamber.address) : bookedAppointment.doctor?.chamber_address }}.
                    </p>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3">
                  <a 
                    :href="getWhatsAppConfirmationUrl(bookedAppointment)" 
                    target="_blank" 
                    class="btn btn-round-full px-4 mr-3 shadow-sm font-weight-bold d-inline-flex align-items-center"
                    style="background: #25D366 !important; border-color: #25D366 !important; color: #ffffff !important;"
                    title="হোয়াটসঅ্যাপে বুকিং স্লিপ ও ভিডিও লিংক নিন"
                  >
                    <i class="fa-brands fa-whatsapp font-18 mr-2"></i> WhatsApp-এ স্লিপ ও লিংক নিন
                  </a>
                  <button class="btn btn-solid-border btn-round-full px-4 mr-3" @click="printPass">
                    <i class="icofont-print mr-2"></i> Print Pass
                  </button>
                  <router-link to="/patient/dashboard" class="btn btn-main btn-round-full px-4 mr-3">
                    <i class="icofont-prescription mr-2"></i> View My Portal
                  </router-link>
                  <button class="btn btn-main-2 btn-round-full px-4" @click="resetWizard">
                    <i class="icofont-plus mr-2"></i> Book Another Doctor
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
import confetti from 'canvas-confetti';
import speechService from '../utils/voiceInput';

export default {
  name: 'AppointmentBooking',
  data() {
    return {
      currentStep: 1,
      doctors: [],
      specializations: [],
      selectedSpecialization: '',
      doctorSearch: '',
      doctorDisplayLimit: 24,
      availableSlots: [],
      loadingSlots: false,
      slotInfo: {
        is_free_day: false,
        remaining_quota: null,
        free_patient_limit: 20,
        free_chamber_address: '',
        free_service_note: '',
        fee: 0,
      },
      submitting: false,
      errorMessage: '',
      isBooked: false,
      bookedAppointment: null,
      activeVoiceField: null,
      selectedChamberId: null,
      minDate: new Date().toISOString().split('T')[0],
      form: {
        doctor_id: '',
        chamber_id: null,
        appointment_date: new Date().toISOString().split('T')[0],
        appointment_time: '',
        consultation_type: 'online_video',
        patient_name: '',
        patient_email: '',
        patient_phone: '',
        reason_for_visit: '',
        payment_method: 'online_card',
        patient_id: null,
      },
      cardDetails: {
        number: '',
        name: '',
        expiry: '',
        cvc: '',
      },
      bkashDetails: {
        senderNumber: '',
        trxId: '',
      },
      // Voice Dictation (Speech Recognition)
      isListeningVoice: false,
      voiceLanguage: 'bn-BD',
      voiceInterimText: '',
      speechRecognitionInstance: null,
    };
  },
  computed: {
    filteredDoctors() {
      const selectedKey = this.selectedSpecialization;
      let matchedKeywords = [];
      if (selectedKey && Array.isArray(this.specializations)) {
        const found = this.specializations.find(s => (s.key === selectedKey || s.name === selectedKey));
        if (found && found.keywords) {
          matchedKeywords = found.keywords.map(k => k.toLowerCase());
        }
      }

      return this.doctors.filter(d => {
        let matchesSpec = true;
        if (selectedKey) {
          if (matchedKeywords.length > 0) {
            const docSpec = (d.specialization || '').toLowerCase();
            matchesSpec = matchedKeywords.some(kw => docSpec.includes(kw));
          } else {
            matchesSpec = d.specialization === selectedKey || (d.specialization && d.specialization.toLowerCase().includes(selectedKey.toLowerCase()));
          }
        }

        const q = this.doctorSearch ? this.doctorSearch.toLowerCase().trim() : '';
        const matchesSearch = !q ||
          d.name.toLowerCase().includes(q) ||
          (d.chamber_address && d.chamber_address.toLowerCase().includes(q)) ||
          (d.specialization && d.specialization.toLowerCase().includes(q)) ||
          (d.city && d.city.toLowerCase().includes(q)) ||
          (d.district && d.district.toLowerCase().includes(q));

        return matchesSpec && matchesSearch;
      });
    },
    pagedDoctors() {
      return this.filteredDoctors.slice(0, this.doctorDisplayLimit);
    },
    selectedDoctor() {
      return this.doctors.find(d => d.id === Number(this.form.doctor_id));
    },
    selectedChamber() {
      if (!this.selectedDoctor || !this.selectedDoctor.chambers || this.selectedDoctor.chambers.length === 0) return null;
      if (this.selectedChamberId) {
        return this.selectedDoctor.chambers.find(c => c.id === this.selectedChamberId) || this.selectedDoctor.chambers[0];
      }
      return this.selectedDoctor.chambers.find(c => c.is_primary) || this.selectedDoctor.chambers[0];
    },
    formattedDate() {
      return this.formatDate(this.form.appointment_date);
    },
  },
  async mounted() {
    await this.loadInitialData();

    const userStr = localStorage.getItem('saas_user');
    if (userStr) {
      const user = JSON.parse(userStr);
      this.form.patient_id = user.id;
      this.form.patient_name = user.name;
      this.form.patient_email = user.email;
      this.form.patient_phone = user.phone || '';
    }

    if (this.$route.query.doctor_id) {
      const docId = Number(this.$route.query.doctor_id);
      const doctor = this.doctors.find(d => d.id === docId);
      if (doctor) {
        if (this.$route.query.type) {
          this.form.consultation_type = this.$route.query.type;
        }
        if (this.$route.query.chamber_id) {
          this.selectedChamberId = Number(this.$route.query.chamber_id);
          this.form.chamber_id = Number(this.$route.query.chamber_id);
        }
        if (this.$route.query.free && doctor.free_consultation_enabled) {
          const targetDay = (doctor.free_days && doctor.free_days.length) ? doctor.free_days[0] : 'Friday';
          this.form.appointment_date = this.getNextDateForDay(targetDay);
        }
        this.selectDoctorAndContinue(doctor);
      }
    }
  },
  methods: {
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
    selectDoctor(doctor) {
      this.form.doctor_id = doctor.id;
      if (doctor.chambers && doctor.chambers.length > 0) {
        const primary = doctor.chambers.find(c => c.is_primary) || doctor.chambers[0];
        this.selectedChamberId = primary ? primary.id : null;
        this.form.chamber_id = primary ? primary.id : null;
      } else {
        this.selectedChamberId = null;
        this.form.chamber_id = null;
      }
    },
    selectDoctorAndContinue(doctor) {
      this.form.doctor_id = doctor.id;
      if (doctor.chambers && doctor.chambers.length > 0) {
        if (!this.selectedChamberId || !doctor.chambers.some(c => c.id === this.selectedChamberId)) {
          const primary = doctor.chambers.find(c => c.is_primary) || doctor.chambers[0];
          this.selectedChamberId = primary ? primary.id : null;
          this.form.chamber_id = primary ? primary.id : null;
        }
      } else {
        this.selectedChamberId = null;
        this.form.chamber_id = null;
      }
      this.currentStep = 2;
      this.errorMessage = '';
      this.fetchSlots();
      this.$nextTick(() => {
        window.scrollTo({ top: 320, behavior: 'smooth' });
      });
    },
    selectChamber(ch) {
      this.selectedChamberId = ch.id;
      this.form.chamber_id = ch.id;
      this.fetchSlots();
    },
    selectFreeConsultationDay() {
      if (!this.selectedDoctor) return;
      const targetDay = (this.selectedDoctor.free_days && this.selectedDoctor.free_days.length) 
        ? this.selectedDoctor.free_days[0] 
        : 'Friday';
      this.form.appointment_date = this.getNextDateForDay(targetDay);
      this.fetchSlots();
      this.$nextTick(() => {
        const el = document.querySelector('input[type="date"]');
        if (el) {
          el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    },
    formatTime(timeStr) {
      if (!timeStr) return '';
      const parts = timeStr.split(':');
      let hour = parseInt(parts[0], 10);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      hour = hour % 12;
      hour = hour ? hour : 12;
      return `${hour}:${parts[1] || '00'} ${ampm}`;
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const cleanDate = dateStr.includes('T') ? dateStr.split('T')[0] : dateStr;
      const parts = cleanDate.split('-');
      if (parts.length === 3) {
        const d = new Date(parts[0], parts[1] - 1, parts[2]);
        return d.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
        });
      }
      return cleanDate;
    },
    getNextDateForDay(dayName = 'Friday') {
      const dayMap = {
        'sunday': 0, 'monday': 1, 'tuesday': 2, 'wednesday': 3,
        'thursday': 4, 'friday': 5, 'saturday': 6,
        'রবিবার': 0, 'সোমবার': 1, 'মঙ্গলবার': 2, 'বুধবার': 3,
        'বৃহস্পতিবার': 4, 'শুক্রবার': 5, 'শনিবার': 6
      };
      const cleanDay = String(dayName).toLowerCase().trim();
      const targetIndex = dayMap[cleanDay] !== undefined ? dayMap[cleanDay] : 5; // Default Friday
      const today = new Date();
      const currentDayIndex = today.getDay();
      let diff = (targetIndex - currentDayIndex + 7) % 7;
      const targetDate = new Date();
      targetDate.setDate(today.getDate() + diff);
      const year = targetDate.getFullYear();
      const month = String(targetDate.getMonth() + 1).padStart(2, '0');
      const day = String(targetDate.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    },
    async loadInitialData() {
      try {
        const res = await axios.get('/doctors');
        this.doctors = res.data.doctors || [];
        this.specializations = res.data.specializations || [];
      } catch (err) {
        console.error('Error loading doctors:', err);
      }
    },
    async goToStep(step) {
      this.errorMessage = '';
      if (step === 2) {
        if (!this.form.doctor_id) {
          this.errorMessage = 'Please select a doctor first.';
          return;
        }
        this.currentStep = 2;
        await this.fetchSlots();
      } else if (step === 3) {
        if (!this.form.appointment_time) {
          this.errorMessage = 'Please select an available time slot.';
          return;
        }
        this.currentStep = 3;
      }
    },
    async fetchSlots() {
      if (!this.form.doctor_id || !this.form.appointment_date) return;
      this.loadingSlots = true;
      this.form.appointment_time = '';

      try {
        const res = await axios.get(`/doctors/${this.form.doctor_id}/slots`, {
          params: { date: this.form.appointment_date }
        });

        this.availableSlots = res.data.slots || [];
        this.slotInfo = {
          is_free_day: !!res.data.is_free_day,
          remaining_quota: res.data.remaining_quota,
          free_patient_limit: res.data.free_patient_limit || 20,
          free_chamber_address: res.data.free_chamber_address || '',
          free_service_note: res.data.free_service_note || '',
          fee: res.data.fee,
        };
      } catch (err) {
        this.availableSlots = [];
        this.slotInfo = { is_free_day: false, remaining_quota: null, free_patient_limit: 20, free_chamber_address: '', free_service_note: '', fee: 0 };
      } finally {
        this.loadingSlots = false;
      }
    },
    selectSlot(time) {
      this.form.appointment_time = time;
    },
    async submitBooking() {
      this.submitting = true;
      this.errorMessage = '';

      try {
        const res = await axios.post('/appointments', this.form);
        if (res.data.success) {
          this.bookedAppointment = res.data.appointment;
          this.isBooked = true;
          this.currentStep = 4;
          
          confetti({
            particleCount: 120,
            spread: 80,
            origin: { y: 0.6 },
          });
        }
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'An error occurred while booking. Please try again.';
      } finally {
        this.submitting = false;
      }
    },
    getStepClass(step) {
      if (this.currentStep === step) return 'active';
      if (this.currentStep > step) return 'completed';
      return 'pending';
    },
    printPass() {
      const printElement = document.getElementById('appointment-ticket');
      if (!printElement) {
        window.print();
        return;
      }
      
      const printWindow = window.open('', '_blank', 'width=750,height=600');
      if (!printWindow) {
        window.print();
        return;
      }

      printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
          <title>Appointment Pass - ${this.bookedAppointment?.appointment_number || 'Sukhi Manush'}</title>
          <link rel="stylesheet" href="/plugins/bootstrap/css/bootstrap.min.css">
          <link rel="stylesheet" href="/plugins/icofont/icofont.min.css">
          <link rel="stylesheet" href="/css/style.css">
          <style>
            body { font-family: 'Roboto', sans-serif; background: #fff; padding: 30px; }
            .appointment-pass { border: 2px solid #223a66 !important; border-radius: 12px; padding: 25px; background: #fff; }
            .text-color-2 { color: #e12454 !important; }
            .status-pill.confirmed { background-color: #d1e7dd; color: #0f5132; padding: 4px 14px; border-radius: 50px; font-weight: bold; }
          </style>
        </head>
        <body>
          <div class="appointment-pass">
            ${printElement.innerHTML}
          </div>
          <script>
            window.onload = function() {
              window.print();
              window.onafterprint = function() { window.close(); };
            };
          <\/script>
        </body>
        </html>
      `);
      printWindow.document.close();
    },
    resetWizard() {
      this.isBooked = false;
      this.bookedAppointment = null;
      this.currentStep = 1;
      this.form.appointment_time = '';
      this.form.reason_for_visit = '';
    },
    addQuickSymptom(symptom) {
      const current = (this.form.reason_for_visit || '').trim();
      this.form.reason_for_visit = current ? (current + ', ' + symptom) : symptom;
    },
    toggleVoiceDictation() {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      if (!SpeechRecognition) {
        Swal.fire({
          icon: 'warning',
          title: 'ভয়েস ইনপুট পাওয়া যায়নি',
          text: 'আপনার ব্রাউজারে স্পিচ রিকগনিশন সাপোর্ট নেই। অনুগ্রহ করে Google Chrome অথবা Microsoft Edge ব্রাউজার ব্যবহার করুন।',
        });
        return;
      }

      if (this.isListeningVoice) {
        if (this.speechRecognitionInstance) {
          try { this.speechRecognitionInstance.stop(); } catch (e) { console.warn(e); }
        }
        this.isListeningVoice = false;
        return;
      }

      try {
        const recognition = new SpeechRecognition();
        recognition.lang = this.voiceLanguage || 'bn-BD';
        recognition.continuous = true;
        recognition.interimResults = true;

        recognition.onstart = () => {
          this.isListeningVoice = true;
          this.voiceInterimText = '';
        };

        recognition.onresult = (event) => {
          let finalChunk = '';
          let interimChunk = '';

          for (let i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) {
              finalChunk += event.results[i][0].transcript;
            } else {
              interimChunk += event.results[i][0].transcript;
            }
          }

          this.voiceInterimText = interimChunk;

          if (finalChunk) {
            const current = (this.form.reason_for_visit || '').trim();
            this.form.reason_for_visit = current ? (current + ' ' + finalChunk.trim()) : finalChunk.trim();
            this.voiceInterimText = '';
          }
        };

        recognition.onerror = (event) => {
          console.error('Speech recognition error:', event.error);
          this.isListeningVoice = false;
          this.voiceInterimText = '';
          if (event.error === 'not-allowed') {
            Swal.fire({
              icon: 'error',
              title: 'মাইক্রোফোন পারমিশন দিন',
              text: 'ব্রাউজারের অ্যাড্রেস বার থেকে মাইক্রোফোন পারমিশন Allow করে দিন।',
            });
          }
        };

        recognition.onend = () => {
          this.isListeningVoice = false;
          this.voiceInterimText = '';
        };

        this.speechRecognitionInstance = recognition;
        recognition.start();
      } catch (err) {
        console.error('Voice dictation error:', err);
        this.isListeningVoice = false;
      }
    },
    toggleVoiceField(fieldName) {
      if (this.activeVoiceField === fieldName) {
        speechService.stopListening();
        this.activeVoiceField = null;
        return;
      }

      speechService.startListening({
        lang: 'bn-BD',
        onStart: () => {
          this.activeVoiceField = fieldName;
        },
        onResult: (text) => {
          if (fieldName === 'doctorSearch') {
            this.doctorSearch = text;
          } else if (fieldName === 'patient_name') {
            this.form.patient_name = text;
          } else if (fieldName === 'patient_phone') {
            let digits = speechService.convertToDigits(text);
            let cleanDigits = digits.replace(/[^\d+]/g, '');
            this.form.patient_phone = cleanDigits || text;
          } else if (fieldName === 'patient_email') {
            this.form.patient_email = text.replace(/\s+/g, '').toLowerCase();
          }
          this.activeVoiceField = null;
        },
        onEnd: () => {
          this.activeVoiceField = null;
        },
        onError: () => {
          this.activeVoiceField = null;
        }
      });
    },
    getWhatsAppConfirmationUrl(apt) {
      if (!apt) return '#';
      const origin = window.location.origin;
      const docName = apt.doctor?.name || 'বিশেষজ্ঞ ডাক্তার';
      const docSpecialty = apt.doctor?.specialization || '';
      const dateStr = this.formatDate(apt.appointment_date);
      const timeStr = apt.appointment_time || '';
      const isVideo = apt.consultation_type === 'online_video' || apt.meeting_link;
      const trackingLink = `${origin}/track-appointment?serial=${apt.appointment_number}`;
      const videoLink = `${origin}/video-chamber/${apt.appointment_number}`;

      let msg = `🏥 *সুখী মানুষ (Sukhi Manush) ডিজিটাল স্বাস্থ্যসেবা*\n` +
        `✅ *সিরিয়াল বুকিং নিশ্চিতকরণ স্লিপ*\n\n` +
        `👤 রোগীর নাম: ${apt.patient_name || ''}\n` +
        `🔢 সিরিয়াল নম্বর: #${apt.serial_number || ''}\n` +
        `🩺 বিশেষজ্ঞ ডাক্তার: ডাঃ ${docName} (${docSpecialty})\n` +
        `📅 অ্যাপয়েন্টমেন্ট তারিখ: ${dateStr} (${timeStr})\n` +
        `📍 চেম্বার / মাধ্যম: ${isVideo ? '🎥 অনলাইন লাইভ ভিডিও চেম্বার' : (apt.chamber?.chamber_name || apt.doctor?.chamber_address || 'চেম্বার')}\n\n`;

      if (isVideo) {
        msg += `🎥 *১-ক্লিক ভিডিও চেম্বারে যুক্ত হতে ট্যাপ করুন:*\n${videoLink}\n\n`;
      }
      msg += `🔍 *লাইভ চেম্বার কিউ ট্র্যাকিং লিংক:*\n${trackingLink}\n\n` +
        `জরুরি প্রয়োজনে হটলাইনে যোগাযোগ করুন। সুস্থ থাকুন, নিরাপদে থাকুন!`;

      let phone = (apt.patient_phone || '').replace(/[^\d]/g, '');
      if (phone.startsWith('01')) {
        phone = '88' + phone;
      }
      return phone ? `https://wa.me/${phone}?text=${encodeURIComponent(msg)}` : `https://wa.me/?text=${encodeURIComponent(msg)}`;
    },
  },
  beforeUnmount() {
    speechService.stopListening();
  },
};
</script>

<style scoped>
.animate-pulse-mic {
  animation: pulse-mic-anim 1.3s infinite;
}
@keyframes pulse-mic-anim {
  0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 36, 84, 0.7); }
  70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(225, 36, 84, 0); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 36, 84, 0); }
}

.pulse-voice-btn {
  animation: pulse-mic-anim 1.3s infinite;
}

/* Mobile Responsiveness for Rural Patients */
@media (max-width: 768px) {
  .appointment-booking {
    padding-left: 5px;
    padding-right: 5px;
  }
  .appoinment-wrap {
    padding: 1rem !important;
  }
  .wizard-progress {
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 5px;
  }
  .time-slot-btn {
    padding: 10px 6px !important;
    font-size: 13px !important;
    min-height: 48px !important;
  }
  .doctor-card-select {
    padding: 0.85rem !important;
  }
  .btn-main-2, .btn-main, .btn-solid-border {
    font-size: 14px;
    min-height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
}

.doctor-card-select {
  border: 2px solid #edf2f7 !important;
  border-radius: 12px !important;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  background: #ffffff;
}
.doctor-card-select:hover {
  border-color: #0284c7 !important;
  box-shadow: 0 10px 25px rgba(2, 132, 199, 0.12) !important;
  transform: translateY(-2px);
}
.doctor-card-select.active {
  border-color: #e12454 !important;
  background-color: #fffbfa !important;
  box-shadow: 0 10px 25px rgba(225, 36, 84, 0.15) !important;
}

/* Voice Input Inside Input Fields */
.input-voice-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}

.input-voice-wrapper .form-control {
  padding-right: 48px !important;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.input-voice-wrapper .form-control:focus {
  border-color: #223a66;
  box-shadow: 0 0 0 0.2rem rgba(34, 58, 102, 0.12);
}

.btn-voice-input-inside {
  position: absolute;
  right: 6px;
  top: 50%;
  transform: translateY(-50%);
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #e12454;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 3;
  padding: 0;
  outline: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.btn-voice-input-inside i {
  font-size: 15px;
  line-height: 1;
}

.btn-voice-input-inside:hover {
  background: #fff0f3;
  border-color: #e12454;
  color: #e12454;
  transform: translateY(-50%) scale(1.08);
  box-shadow: 0 2px 8px rgba(225, 36, 84, 0.2);
}

.btn-voice-input-inside.is-listening {
  background: linear-gradient(135deg, #e12454, #be123c) !important;
  border-color: #e12454 !important;
  color: #ffffff !important;
  width: auto;
  border-radius: 20px;
  padding: 0 10px;
  height: 30px;
  animation: pulse-mic-anim 1.2s infinite;
  box-shadow: 0 4px 12px rgba(225, 36, 84, 0.35);
}

.btn-voice-input-inside .listening-text {
  font-size: 11px;
  font-weight: 700;
  margin-left: 5px;
  letter-spacing: 0.2px;
}

/* Voice Input Inside Symptoms Textarea */
.textarea-voice-wrapper {
  position: relative;
  width: 100%;
}

.textarea-voice-wrapper .form-control {
  padding-bottom: 46px !important;
  min-height: 105px;
  border-radius: 10px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.textarea-voice-wrapper .form-control:focus {
  border-color: #223a66;
  box-shadow: 0 0 0 0.2rem rgba(34, 58, 102, 0.12);
}

.textarea-voice-action {
  position: absolute;
  right: 10px;
  bottom: 10px;
  z-index: 3;
}

.btn-voice-textarea-inside {
  border: 1px solid #fed7aa;
  background: #fff7ed;
  color: #ea580c;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  outline: none;
}

.btn-voice-textarea-inside i {
  font-size: 14px;
}

.btn-voice-textarea-inside:hover {
  background: #e12454;
  border-color: #e12454;
  color: #ffffff;
  transform: scale(1.04);
  box-shadow: 0 2px 8px rgba(225, 36, 84, 0.25);
}

.btn-voice-textarea-inside.is-listening {
  background: linear-gradient(135deg, #e12454, #be123c) !important;
  border-color: #e12454 !important;
  color: #ffffff !important;
  animation: pulse-mic-anim 1.2s infinite;
  box-shadow: 0 4px 12px rgba(225, 36, 84, 0.35);
}

.textarea-listening-bar {
  position: absolute;
  left: 10px;
  bottom: 10px;
  max-width: calc(100% - 165px);
  z-index: 2;
  background: rgba(254, 242, 242, 0.95);
  padding: 4px 10px;
  border-radius: 12px;
  border: 1px solid #fecaca;
  pointer-events: none;
}
</style>
