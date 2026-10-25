<template>
  <div class="patient-dashboard-page bg-light min-vh-100 pb-5">
    <!-- Patient Top Header -->
    <nav class="navbar navbar-dark bg-primary py-2 px-3 shadow-sm sticky-top">
      <div class="container-fluid px-0 d-flex justify-content-between align-items-center">
        <router-link to="/patient/dashboard" class="navbar-brand d-flex align-items-center mb-0 py-0 text-white font-weight-bold" style="font-size: 15px;">
          <i class="icofont-prescription mr-2 text-danger" style="font-size: 20px;"></i>
          <span>Patient Portal &bull; স্বাস্থ্য ভল্ট</span>
        </router-link>

        <div class="d-flex align-items-center" style="gap: 8px;">
          <!-- Family Member Active Selector Dropdown -->
          <div class="dropdown d-inline-block">
            <button class="btn btn-sm btn-light dropdown-toggle py-1 px-2 font-weight-bold shadow-sm d-flex align-items-center" type="button" @click="showFamilyDropdown = !showFamilyDropdown" style="font-size: 12px; border-radius: 20px;">
              <i class="icofont-users-alt-5 text-primary mr-1"></i>
              <span>{{ activeFamilyMember ? activeFamilyMember.name + ' (' + activeFamilyMember.relationship + ')' : (user?.name || 'Self') }}</span>
            </button>
            <div v-if="showFamilyDropdown" class="dropdown-menu dropdown-menu-right show shadow-lg mt-1 p-2" style="position: absolute; right: 0; min-width: 220px; z-index: 1060;">
              <h6 class="dropdown-header px-2 py-1 text-muted font-weight-bold small">ফ্যামিলি প্রোফাইল পরিবর্তন করুন</h6>
              <a class="dropdown-item rounded py-1 px-2 small font-weight-bold cursor-pointer" :class="{ 'bg-primary text-white': !activeFamilyMember }" @click="selectFamilyMember(null)">
                <i class="icofont-user mr-1"></i> {{ user?.name }} (আমার প্রোফাইল)
              </a>
              <div class="dropdown-divider my-1"></div>
              <a 
                v-for="member in familyMembers" 
                :key="member.id" 
                class="dropdown-item rounded py-1 px-2 small cursor-pointer d-flex justify-content-between align-items-center"
                :class="{ 'bg-primary text-white': activeFamilyMember?.id === member.id }"
                @click="selectFamilyMember(member)"
              >
                <span><i class="icofont-user-alt-7 mr-1"></i> {{ member.name }}</span>
                <span class="badge badge-light border text-dark">{{ member.relationship }}</span>
              </a>
              <div class="dropdown-divider my-1"></div>
              <button class="btn btn-sm btn-outline-danger btn-block py-1 font-weight-bold" style="font-size: 11px;" @click="openAddFamilyModal">
                <i class="icofont-plus mr-1"></i> নতুন সদস্য যুক্ত করুন
              </button>
            </div>
          </div>

          <router-link to="/doctors" class="btn btn-sm btn-outline-light py-1 px-2 d-none d-sm-inline-flex align-items-center font-weight-bold" style="font-size: 12px; border-radius: 20px;">
            <i class="icofont-search mr-1"></i> ডাক্তার খুঁজুন
          </router-link>

          <button class="btn btn-sm btn-danger py-1 px-2 font-weight-bold d-inline-flex align-items-center" @click="logout" style="font-size: 12px; border-radius: 6px;">
            <i class="icofont-logout mr-1"></i> লগআউট
          </button>
        </div>
      </div>
    </nav>

    <div class="container-fluid px-2 px-md-4 px-lg-5 mt-3 mt-md-4">
      <!-- Live Chamber Queue Tracker Floating Banner (If patient has an appointment) -->
      <div v-if="todayAppointment" class="alert alert-warning border-warning shadow-sm rounded-lg p-3 mb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div class="d-flex align-items-center mb-2 mb-md-0">
          <div class="spinner-grow spinner-grow-sm text-danger mr-2" role="status"></div>
          <div>
            <h6 class="font-weight-bold text-dark mb-0">
              <i class="icofont-clock-time text-danger mr-1"></i> লাইভ চেম্বার সিরিয়াল ট্র্যাকার: 
              <span class="text-danger">{{ todayAppointment.doctor?.name }}</span>
            </h6>
            <small class="text-muted">
              আপনার সিরিয়াল নং: <strong>#{{ todayAppointment.serial_number }}</strong> &bull; 
              বর্তমানে চেম্বারে চলছে: <strong class="text-success">#{{ liveQueueServingSerial }}</strong> 
              <span v-if="todayAppointment.serial_number > liveQueueServingSerial">
                (আপনার পূর্বে অপেক্ষমাণ: {{ Math.max(0, todayAppointment.serial_number - liveQueueServingSerial) }} জন)
              </span>
              <span v-else-if="todayAppointment.serial_number === liveQueueServingSerial" class="badge badge-danger ml-1 font-weight-bold">
                এখন আপনার ডাক পড়েছে!
              </span>
            </small>
          </div>
        </div>
        <div class="d-flex align-items-center" style="gap: 6px;">
          <button class="btn btn-sm btn-outline-dark py-1 px-2 font-weight-bold" @click="refreshLiveQueue" style="font-size: 11px;">
            <i class="icofont-refresh mr-1"></i> লাইভ রিফ্রেশ
          </button>
          <a 
            v-if="todayAppointment.consultation_type === 'online_video'"
            :href="'/video-chamber/' + todayAppointment.appointment_number" 
            target="_blank"
            class="btn btn-sm btn-success font-weight-bold py-1 px-3 shadow-sm" 
            style="font-size: 12px;"
          >
            <i class="icofont-video-cam mr-1"></i> ভিডিও কলে যোগ দিন
          </a>
        </div>
      </div>

      <!-- Patient Profile Summary Card -->
      <div class="card border-0 shadow-sm rounded-lg p-3 p-md-4 bg-white mb-3 mb-md-4">
        <div class="row align-items-center">
          <div class="col-md-8 d-flex align-items-center flex-wrap flex-sm-nowrap">
            <!-- Patient Profile Avatar with 1-Click Change Camera Button -->
            <div class="position-relative mr-3 mb-2 mb-sm-0 flex-shrink-0">
              <img 
                :src="user?.avatar ? ('/' + user.avatar) : '/images/team/1.jpg'" 
                class="rounded-circle shadow-sm" 
                style="width: 70px; height: 70px; object-fit: cover; border: 3px solid #223a66;"
                alt="Patient Avatar"
              >
              <label 
                class="btn btn-sm btn-primary rounded-circle position-absolute p-0 d-flex align-items-center justify-content-center cursor-pointer shadow" 
                style="width: 26px; height: 26px; bottom: 0; right: 0; margin-bottom: 0; background-color: #e12454; border-color: #e12454;" 
                title="প্রোফাইল ছবি পরিবর্তন করুন"
              >
                <i class="icofont-camera text-white" style="font-size: 12px;"></i>
                <input type="file" class="d-none" accept="image/*" @change="uploadPatientAvatar">
              </label>
            </div>
            <div>
              <div class="d-flex align-items-center flex-wrap">
                <h4 class="font-weight-bold mb-1 text-dark mr-2" style="font-size: 18px;">
                  {{ activeFamilyMember ? activeFamilyMember.name : user?.name }}
                </h4>
                <span class="badge badge-info px-2 py-1 mb-1 font-weight-bold" style="font-size: 10px;">
                  {{ activeFamilyMember ? activeFamilyMember.relationship + ' প্রোফাইল' : 'প্রধান রোগী একাউন্ট' }}
                </span>
                <span v-if="familyMembers.length > 0" class="badge badge-light border text-muted ml-2 mb-1" style="font-size: 10px;">
                  👨‍👩‍👧‍👦 {{ familyMembers.length }} ফ্যামিলি মেম্বার যুক্ত
                </span>
              </div>
              <p class="text-muted small mb-1">
                <i class="icofont-email mr-1 text-primary"></i> {{ user?.email }} &bull; 
                <i class="icofont-phone mr-1 text-success"></i> {{ user?.phone || 'ফোন নাম্বার যুক্ত নেই' }}
              </p>
              <small class="text-muted"><i class="icofont-location-pin text-danger"></i> {{ user?.address || 'বাংলাদেশ' }}</small>
            </div>
          </div>
          <div class="col-md-4 text-md-right mt-3 mt-md-0 d-flex justify-content-md-end align-items-center flex-wrap gap-2">
            <button class="btn btn-outline-dark btn-round-full btn-sm mr-2" @click="openAddFamilyModal" style="font-size: 12px;">
              <i class="icofont-users-alt-5 mr-1"></i> ফ্যামিলি মেম্বার
            </button>
            <button class="btn btn-danger btn-round-full btn-sm font-weight-bold shadow-sm" @click="openUploadReportModal" style="font-size: 12px;">
              <i class="icofont-camera mr-1"></i> রিপোর্ট আপলোড
            </button>
          </div>
        </div>
      </div>

      <!-- Quick Metrics Row: 5 compact columns -->
      <div class="row mb-3 mb-md-4">
        <div class="col-6 col-md mb-2 mb-md-0">
          <div class="card border-0 shadow-sm rounded-lg p-2 p-md-3 bg-white text-center h-100 cursor-pointer metric-card" @click="activeTab = 'reports'">
            <span class="text-muted small font-weight-bold text-uppercase d-block" style="font-size: 10px;">মেডিকেল রিপোর্ট</span>
            <h4 class="font-weight-bold mb-0 text-danger" style="font-size: 20px;">{{ myReportsList.length }}</h4>
          </div>
        </div>

        <div class="col-6 col-md mb-2 mb-md-0">
          <div class="card border-0 shadow-sm rounded-lg p-2 p-md-3 bg-white text-center h-100 cursor-pointer metric-card" @click="activeTab = 'prescriptions'">
            <span class="text-muted small font-weight-bold text-uppercase d-block" style="font-size: 10px;">প্রেসক্রিপশন</span>
            <h4 class="font-weight-bold mb-0 text-primary" style="font-size: 20px;">{{ prescriptions.length }}</h4>
          </div>
        </div>

        <div class="col-6 col-md mb-2 mb-md-0">
          <div class="card border-0 shadow-sm rounded-lg p-2 p-md-3 bg-white text-center h-100 cursor-pointer metric-card" @click="activeTab = 'health_tracker'">
            <span class="text-muted small font-weight-bold text-uppercase d-block" style="font-size: 10px;">সুগার / BP ট্র্যাকার</span>
            <h4 class="font-weight-bold mb-0 text-info" style="font-size: 20px;">{{ healthMetricsList.length }}</h4>
          </div>
        </div>

        <div class="col-6 col-md mb-2 mb-md-0">
          <div class="card border-0 shadow-sm rounded-lg p-2 p-md-3 bg-white text-center h-100 cursor-pointer metric-card" @click="activeTab = 'appointments'">
            <span class="text-muted small font-weight-bold text-uppercase d-block" style="font-size: 10px;">অ্যাপয়েন্টমেন্ট</span>
            <h4 class="font-weight-bold mb-0 text-success" style="font-size: 20px;">{{ appointments.length }}</h4>
          </div>
        </div>

        <div class="col-6 col-md mb-2 mb-md-0">
          <div class="card border-0 shadow-sm rounded-lg p-2 p-md-3 bg-white text-center h-100 cursor-pointer metric-card" @click="activeTab = 'family'">
            <span class="text-muted small font-weight-bold text-uppercase d-block" style="font-size: 10px;">ফ্যামিলি মেম্বার</span>
            <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 20px;">{{ familyMembers.length }}</h4>
          </div>
        </div>
      </div>

      <!-- Main Patient Tabs Container -->
      <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap">
          <ul class="nav nav-pills custom-nav-tabs">
            <li class="nav-item">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-danger text-white shadow-sm': activeTab === 'reports' }"
                @click="activeTab = 'reports'"
              >
                <i class="icofont-file-document mr-1"></i> টেস্ট রিপোর্ট ({{ myReportsList.length }})
              </a>
            </li>
            <li class="nav-item ml-md-2 mt-1 mt-md-0">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-primary text-white shadow-sm': activeTab === 'prescriptions' }"
                @click="activeTab = 'prescriptions'"
              >
                <i class="icofont-prescription mr-1"></i> প্রেসক্রিপশন ও অডিও ({{ prescriptions.length }})
              </a>
            </li>
            <li class="nav-item ml-md-2 mt-1 mt-md-0">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-info text-white shadow-sm': activeTab === 'health_tracker' }"
                @click="activeTab = 'health_tracker'"
              >
                <i class="icofont-heart-beat mr-1"></i> সুগার ও প্রেশার ট্র্যাকার ({{ healthMetricsList.length }})
              </a>
            </li>
            <li class="nav-item ml-md-2 mt-1 mt-md-0">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-primary text-white shadow-sm': activeTab === 'appointments' }"
                @click="activeTab = 'appointments'"
              >
                <i class="icofont-calendar mr-1"></i> অ্যাপয়েন্টমেন্ট ({{ appointments.length }})
              </a>
            </li>
            <li class="nav-item ml-md-2 mt-1 mt-md-0">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-success text-white shadow-sm': activeTab === 'medicines' }"
                @click="activeTab = 'medicines'"
              >
                <i class="icofont-capsule mr-1"></i> ঔষধের রুটিন ({{ medicineSchedules.length }})
              </a>
            </li>
            <li class="nav-item ml-md-2 mt-1 mt-md-0">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-warning text-dark shadow-sm': activeTab === 'passport' }"
                @click="activeTab = 'passport'"
              >
                <i class="icofont-id-card mr-1"></i> হেলথ পাসপোর্ট ও কিউআর
              </a>
            </li>
            <li class="nav-item ml-md-2 mt-1 mt-md-0">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-dark text-white shadow-sm': activeTab === 'family' }"
                @click="activeTab = 'family'"
              >
                <i class="icofont-users-alt-5 mr-1"></i> ফ্যামিলি মেম্বারস ({{ familyMembers.length }})
              </a>
            </li>
          </ul>
        </div>

        <div class="card-body p-3 p-md-4">
          <!-- ========================================================= -->
          <!-- TAB 0: MY MEDICAL & LAB REPORTS VAULT -->
          <!-- ========================================================= -->
          <div v-if="activeTab === 'reports'">
            <div class="bg-gradient-soft p-3 p-md-4 rounded-lg mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center">
              <div class="mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                  <span class="badge badge-danger mr-2 px-2 py-1 font-weight-bold">আজীবন স্বাস্থ্য রেকর্ড</span>
                  <h5 class="font-weight-bold text-dark mb-0">মেডিকেল টেস্ট ও ল্যাব রিপোর্ট ভল্ট</h5>
                </div>
                <p class="text-muted small mb-0 mt-1">
                  ফোনের ক্যামেরা দিয়ে কাগজের রিপোর্টের ছবি তুলুন বা PDF আপলোড করুন। যেকোনো সময় ডাক্তারকে দেখাতে পারবেন।
                </p>
              </div>
              <button class="btn btn-danger font-weight-bold btn-round-full px-4 py-2 shadow-sm" @click="openUploadReportModal">
                <i class="icofont-camera mr-1"></i> 📸 নতুন রিপোর্ট আপলোড করুন
              </button>
            </div>

            <!-- Filter Chips & Search -->
            <div class="row align-items-center mb-4">
              <div class="col-lg-8 mb-2 mb-lg-0">
                <div class="d-flex flex-wrap align-items-center filter-chips" style="gap: 6px;">
                  <button class="chip-btn" :class="{ 'active': selectedReportCategory === 'all' }" @click="selectedReportCategory = 'all'">সব রিপোর্ট ({{ myReportsList.length }})</button>
                  <button class="chip-btn" :class="{ 'active': selectedReportCategory === 'blood_test' }" @click="selectedReportCategory = 'blood_test'">🩸 রক্ত পরীক্ষা / CBC</button>
                  <button class="chip-btn" :class="{ 'active': selectedReportCategory === 'xray' }" @click="selectedReportCategory = 'xray'">🩻 এক্স-রে / X-Ray</button>
                  <button class="chip-btn" :class="{ 'active': selectedReportCategory === 'usg' }" @click="selectedReportCategory = 'usg'">🩺 আল্ট্রাসনোগ্রাম / USG</button>
                  <button class="chip-btn" :class="{ 'active': selectedReportCategory === 'ecg' }" @click="selectedReportCategory = 'ecg'">💓 ইসিজি / ECG</button>
                  <button class="chip-btn" :class="{ 'active': selectedReportCategory === 'urine' }" @click="selectedReportCategory = 'urine'">🧪 প্রস্রাব পরীক্ষা</button>
                  <button class="chip-btn" :class="{ 'active': selectedReportCategory === 'prescription_copy' }" @click="selectedReportCategory = 'prescription_copy'">📝 প্রেসক্রিপশন কপি</button>
                </div>
              </div>
              <div class="col-lg-4">
                <input type="text" v-model="reportSearchQuery" class="form-control form-control-sm" placeholder="রিপোর্ট বা ল্যাব নাম দিয়ে খুঁজুন...">
              </div>
            </div>

            <div v-if="filteredReports.length === 0" class="text-center py-5 bg-white rounded border">
              <i class="icofont-file-document display-3 text-muted"></i>
              <h5 class="font-weight-bold text-dark mt-2">কোনো টেস্ট রিপোর্ট পাওয়া যায়নি</h5>
              <p class="text-muted small">আপনার প্রেসক্রিপশন বা টেস্ট পেপারের ছবি তুলে এখানে সংরক্ষণ করুন।</p>
              <button class="btn btn-danger btn-round-full btn-sm px-4 mt-2 font-weight-bold" @click="openUploadReportModal">
                <i class="icofont-camera mr-1"></i> প্রথম রিপোর্ট আপলোড করুন
              </button>
            </div>

            <div v-else class="row">
              <div v-for="report in filteredReports" :key="report.id" class="col-md-6 col-lg-4 mb-4">
                <div class="report-card card h-100 border rounded-lg shadow-sm overflow-hidden bg-white">
                  <div class="p-3 border-bottom d-flex justify-content-between align-items-start bg-light">
                    <div>
                      <span class="badge badge-pill badge-primary px-2 py-1 small font-weight-bold mb-1">{{ report.test_type_label }}</span>
                      <h6 class="font-weight-bold text-dark mb-0 text-truncate" style="max-width: 190px;">{{ report.report_title }}</h6>
                    </div>
                    <span class="badge" :class="report.status === 'reviewed' ? 'badge-success' : 'badge-light border text-muted'" style="font-size: 10px;">
                      {{ report.status === 'reviewed' ? 'ডাক্তার দেখেছেন' : 'আপলোডকৃত' }}
                    </span>
                  </div>

                  <div class="report-preview-box position-relative cursor-pointer bg-dark" @click="viewReport(report)">
                    <img v-if="report.file_type === 'image'" :src="report.file_url" class="img-fluid w-100 report-thumb-img" alt="Report">
                    <div v-else class="pdf-placeholder d-flex flex-column align-items-center justify-content-center py-4 text-white bg-secondary">
                      <i class="icofont-file-pdf display-4 text-danger mb-1"></i>
                      <span class="font-weight-bold small">PDF ডকুমেন্ট</span>
                    </div>
                  </div>

                  <div class="p-3">
                    <div class="d-flex justify-content-between text-muted small mb-1">
                      <span><i class="icofont-calendar text-danger mr-1"></i> {{ formatDate(report.report_date) }}</span>
                      <span class="badge badge-light border">{{ report.file_size }}</span>
                    </div>
                    <div v-if="report.diagnostic_center" class="text-muted small mb-1">
                      <i class="icofont-hospital text-primary mr-1"></i> {{ report.diagnostic_center }}
                    </div>
                    <div v-if="report.doctor_remarks" class="alert alert-success p-2 mb-0 small rounded mt-2">
                      <strong>ডাক্তারের মতামত:</strong> {{ report.doctor_remarks }}
                    </div>
                  </div>

                  <div class="p-2 border-top bg-light d-flex justify-content-between align-items-center">
                    <button class="btn btn-sm btn-outline-primary py-1 px-2 font-weight-bold" @click="viewReport(report)" style="font-size: 11px;">
                      <i class="icofont-eye mr-1"></i> প্রিভিউ
                    </button>
                    <a :href="report.file_url" target="_blank" download class="btn btn-sm btn-outline-dark py-1 px-2" style="font-size: 11px;">
                      <i class="icofont-download mr-1"></i> ডাউনলোড
                    </a>
                    <button class="btn btn-sm btn-outline-danger py-1 px-2" @click="confirmDeleteReport(report)" style="font-size: 11px;">
                      <i class="icofont-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 1: DIGITAL PRESCRIPTIONS WITH BANGLA AUDIO & REMINDERS -->
          <!-- ========================================================= -->
          <div v-if="activeTab === 'prescriptions'">
            <div v-if="prescriptions.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-prescription display-3 text-muted"></i>
              <h5 class="mt-3">এখনো কোনো প্রেসক্রিপশন নেই</h5>
              <router-link to="/doctors" class="btn btn-main btn-round-full btn-sm mt-2">ডাক্তার অ্যাপয়েন্টমেন্ট নিন</router-link>
            </div>

            <div v-else class="row">
              <div v-for="rx in prescriptions" :key="rx.id" class="col-lg-6 mb-4">
                <div class="bg-white border rounded shadow-sm p-4 h-100 position-relative">
                  <!-- Header with Bangla Audio Button -->
                  <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                      <span class="badge badge-danger mb-1 font-weight-bold">{{ rx.prescription_number }}</span>
                      <h5 class="font-weight-bold text-dark mb-0">{{ rx.doctor?.name }}</h5>
                      <span class="text-primary small font-weight-bold">{{ rx.doctor?.specialization }}</span>
                    </div>
                    <div class="text-right">
                      <!-- 📢 BANGLA AUDIO PRESCRIPTION READER BUTTON -->
                      <button class="btn btn-sm btn-warning font-weight-bold text-dark shadow-sm py-1 px-2 d-inline-flex align-items-center" @click="playBanglaAudioRx(rx)" style="font-size: 12px; border-radius: 20px;">
                        <i class="icofont-volume-up mr-1 text-danger"></i> 📢 বাংলায় শুনুন
                      </button>
                      <button class="btn btn-sm btn-outline-dark ml-1 py-1 px-2" @click="printPrescription(rx)" style="font-size: 12px;">
                        <i class="icofont-print"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Patient & Diagnosis -->
                  <div class="bg-light p-2 rounded mb-3 small">
                    <div class="row">
                      <div class="col-6"><strong>রোগী:</strong> {{ rx.patient_name }}</div>
                      <div class="col-6 text-right"><strong>ডায়াগনসিস:</strong> <span class="text-danger font-weight-bold">{{ rx.diagnosis }}</span></div>
                    </div>
                  </div>

                  <!-- Daily Medicine Checklist (সকাল, দুপুর, রাত) -->
                  <h6 class="font-weight-bold text-dark mb-2">
                    <i class="icofont-capsule mr-1 text-primary"></i> ওষুধের নিয়ম ও দৈনিক শিডিউল
                  </h6>
                  <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered small mb-0">
                      <thead class="bg-light">
                        <tr>
                          <th>ওষুধ ও কোম্পানি</th>
                          <th>মাত্রা (ডোজ)</th>
                          <th>সময়কাল ও নিয়ম</th>
                          <th>আনুমানিক মূল্য</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(m, mIdx) in rx.medicines" :key="mIdx">
                          <td class="font-weight-bold text-dark">
                            <div>{{ m.name }}</div>
                            <span v-if="m.company" class="badge badge-light border text-secondary px-1.5 py-0 mt-0.5 d-inline-block" style="font-size: 10px;">
                              {{ m.company }}
                            </span>
                          </td>
                          <td class="text-primary font-weight-bold">{{ m.dosage }}</td>
                          <td>
                            <span>{{ m.duration }}</span>
                            <div v-if="m.instruction" class="text-muted" style="font-size: 11px;">{{ m.instruction }}</div>
                          </td>
                          <td>
                            <div v-if="m.unit_price" class="text-success font-weight-bold">
                              ৳{{ m.unit_price }} <small class="text-muted">/পিস</small>
                            </div>
                            <div v-if="m.item_total" class="text-muted" style="font-size: 11px;">
                              মোট: ৳{{ m.item_total }}
                            </div>
                            <span v-if="!m.unit_price" class="text-muted">-</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Estimated Total Rx Cost & 1-Click Medicine Order Banner -->
                  <div v-if="rx.estimated_total_cost > 0" class="p-2.5 rounded-12 mb-3 d-flex justify-content-between align-items-center flex-wrap shadow-2xs" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #bbf7d0;">
                    <div>
                      <small class="text-secondary d-block font-weight-bold" style="font-size: 11px;">ঔষধ কোর্সের আনুমানিক খরচ:</small>
                      <span class="text-success font-weight-bold" style="font-size: 16px;">৳{{ rx.estimated_total_cost }}</span>
                      <span class="badge badge-success text-white ml-2 px-2 py-0.5" style="font-size: 10px;">১০% ছাড়ে পাওয়া যাবে</span>
                    </div>
                    <button 
                      type="button" 
                      class="btn btn-sm btn-success font-weight-bold px-3 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center mt-2 mt-sm-0"
                      style="background: #16a34a; border: none; font-size: 12px;"
                      @click="orderMedicinesOnline(rx)"
                    >
                      <i class="icofont-fast-delivery mr-1"></i> 🛍️ ওষুধ হোম ডেলিভারি নিন
                    </button>
                  </div>

                  <!-- 1-Click Home Sample Collection Lab Booking -->
                  <div v-if="rx.medical_tests && rx.medical_tests.length" class="alert alert-info p-3 rounded mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                      <div>
                        <strong class="text-dark"><i class="icofont-test-tube-alt mr-1"></i> প্রস্তাবিত টেস্টসমূহ:</strong>
                        <div class="d-flex flex-wrap mt-1" style="gap: 4px;">
                          <span v-for="(t, tIdx) in rx.medical_tests" :key="tIdx" class="badge badge-light border text-dark font-weight-bold">
                            {{ t }}
                          </span>
                        </div>
                      </div>
                      <div class="mt-2 mt-md-0 d-flex gap-2">
                        <router-link to="/diagnostic-centers" class="btn btn-sm btn-outline-danger font-weight-bold mr-1">
                          ল্যাব তালিকা &rarr;
                        </router-link>
                        <button class="btn btn-sm btn-danger font-weight-bold shadow-sm" @click="openDiagnosticBookingModal(rx)">
                          <i class="icofont-home mr-1"></i> 🧪 বাড়ি বসে টেস্ট করান
                        </button>
                      </div>
                    </div>
                  </div>

                  <div v-if="rx.advice" class="mb-2 small">
                    <strong class="text-dark">ডাক্তারের পরামর্শ:</strong>
                    <p class="text-muted mb-0 bg-light p-2 rounded mt-1">{{ rx.advice }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 2: CHRONIC HEALTH TRACKER (DIABETES & BLOOD PRESSURE) -->
          <!-- ========================================================= -->
          <div v-if="activeTab === 'health_tracker'">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
              <div>
                <h5 class="font-weight-bold text-dark mb-1">🩸 ডায়াবেটিস ও রক্তচাপ (BP) ট্র্যাকার</h5>
                <p class="text-muted small mb-0">প্রতিদিনের ব্লাড সুগার ও প্রেসার পরিমাপ লিখে রাখুন। ডাক্তার এক নজরে গ্রাফ দেখতে পাবেন।</p>
              </div>
              <button class="btn btn-danger btn-sm font-weight-bold px-3 py-2 btn-round-full shadow-sm" @click="openAddMetricModal">
                <i class="icofont-plus mr-1"></i> নতুন রিডিং যুক্ত করুন
              </button>
            </div>

            <!-- Metric Filter Chips -->
            <div class="d-flex mb-3" style="gap: 6px;">
              <button class="chip-btn" :class="{ 'active': activeMetricTab === 'blood_sugar' }" @click="changeMetricType('blood_sugar')">
                🩸 ব্লাড সুগার (Diabetes)
              </button>
              <button class="chip-btn" :class="{ 'active': activeMetricTab === 'blood_pressure' }" @click="changeMetricType('blood_pressure')">
                💓 রক্তচাপ (Blood Pressure)
              </button>
              <button class="chip-btn" :class="{ 'active': activeMetricTab === 'weight' }" @click="changeMetricType('weight')">
                ⚖️ ওজন (Weight)
              </button>
            </div>

            <div v-if="healthMetricsList.length === 0" class="text-center py-5 bg-white rounded border">
              <i class="icofont-heart-beat display-3 text-muted"></i>
              <h5 class="font-weight-bold text-dark mt-2">কোনো পরিমাপের রেকর্ড নেই</h5>
              <p class="text-muted small">আপনার প্রতিদিনের ডায়াবেটিস বা ব্লাড প্রেশার সেভ করে ট্র্যাকিং শুরু করুন।</p>
              <button class="btn btn-danger btn-sm btn-round-full font-weight-bold" @click="openAddMetricModal">
                প্রথম রিডিং যোগ করুন
              </button>
            </div>

            <!-- Health Metrics Table / Visual Grid -->
            <div v-else class="table-responsive bg-white rounded border shadow-sm">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>তারিখ</th>
                    <th>পরিমাপের ধরন</th>
                    <th>রিডিং / মান</th>
                    <th>সময় / অবস্থা</th>
                    <th>স্ট্যাটাস</th>
                    <th>নোট</th>
                    <th>অ্যাকশন</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in healthMetricsList" :key="item.id">
                    <td class="font-weight-bold">{{ formatDate(item.recorded_date) }}</td>
                    <td>{{ item.metric_label }}</td>
                    <td>
                      <span v-if="item.metric_type === 'blood_sugar'" class="font-weight-bold text-dark" style="font-size: 16px;">
                        {{ item.value_primary }} <small class="text-muted">mmol/L</small>
                      </span>
                      <span v-else-if="item.metric_type === 'blood_pressure'" class="font-weight-bold text-dark" style="font-size: 16px;">
                        {{ item.value_primary }} / {{ item.value_secondary }} <small class="text-muted">mmHg</small>
                      </span>
                      <span v-else class="font-weight-bold text-dark">
                        {{ item.value_primary }} <small class="text-muted">kg</small>
                      </span>
                    </td>
                    <td><span class="badge badge-light border text-dark">{{ item.measured_time || 'সাধারণ' }}</span></td>
                    <td>
                      <span class="badge" :class="'badge-' + item.status_color">
                        {{ item.status_color === 'danger' ? 'ঝুঁকিপূর্ণ / বেশি' : (item.status_color === 'warning' ? 'সতর্কতা' : 'স্বাভাবিক') }}
                      </span>
                    </td>
                    <td class="small text-muted">{{ item.notes || '-' }}</td>
                    <td>
                      <button class="btn btn-sm btn-outline-danger py-0 px-2" @click="deleteMetric(item.id)">
                        <i class="icofont-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 3: APPOINTMENTS HISTORY & LIVE TOKEN -->
          <!-- ========================================================= -->
          <div v-if="activeTab === 'appointments'">
            <div v-if="appointments.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-calendar display-3 text-muted"></i>
              <h5 class="mt-3">কোনো অ্যাপয়েন্টমেন্ট হিস্ট্রি নেই</h5>
              <router-link to="/doctors" class="btn btn-main btn-round-full btn-sm mt-2">নতুন অ্যাপয়েন্টমেন্ট নিন</router-link>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="bg-light">
                  <tr>
                    <th>সিরিয়াল / রেফারেন্স</th>
                    <th>ডাক্তার</th>
                    <th>ধরন ও তারিখ</th>
                    <th>ফি</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="apt in appointments" :key="apt.id">
                    <td>
                      <span class="badge badge-secondary mr-1">#{{ apt.serial_number }}</span>
                      <strong class="text-dark">{{ apt.appointment_number }}</strong>
                    </td>
                    <td>
                      <div class="font-weight-bold">{{ apt.doctor?.name }}</div>
                      <small class="text-muted">{{ apt.doctor?.specialization }}</small>
                    </td>
                    <td>
                      <div>
                        <span v-if="apt.consultation_type === 'online_video'" class="badge badge-success mr-1">
                          <i class="icofont-video-cam"></i> ভিডিও কল
                        </span>
                        <span v-else class="badge badge-light border text-muted mr-1">
                          <i class="icofont-hospital"></i> চেম্বার
                        </span>
                        <span>{{ formatDate(apt.appointment_date) }}</span>
                      </div>
                      <small class="text-muted">{{ apt.appointment_time }}</small>
                    </td>
                    <td><strong class="text-success">৳{{ apt.consultation_fee }}</strong></td>
                    <td><span class="status-pill" :class="apt.status">{{ apt.status }}</span></td>
                    <td>
                      <a 
                        v-if="apt.consultation_type === 'online_video' && apt.status !== 'cancelled'"
                        :href="'/video-chamber/' + apt.appointment_number"
                        target="_blank"
                        class="btn btn-sm btn-success py-1 px-2 font-weight-bold shadow-sm"
                      >
                        <i class="icofont-video-cam mr-1"></i> জয়েন কল
                      </a>
                      <span v-else class="small text-muted">চেম্বার সিরিয়াল</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 4: FAMILY MEMBERS PROFILES -->
          <!-- ========================================================= -->
          <div v-if="activeTab === 'family'">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
              <div>
                <h5 class="font-weight-bold text-dark mb-1">👨‍👩‍👧‍👦 পরিবারের সদস্যদের প্রোফাইল</h5>
                <p class="text-muted small mb-0">বাবা, মা, সন্তান বা স্ত্রীর রিপোর্ট ও প্রেসক্রিপশন এক একাউন্ট থেকেই ম্যানেজ করুন।</p>
              </div>
              <button class="btn btn-danger btn-sm font-weight-bold px-3 py-2 btn-round-full shadow-sm" @click="openAddFamilyModal">
                <i class="icofont-plus mr-1"></i> নতুন সদস্য যুক্ত করুন
              </button>
            </div>

            <div v-if="familyMembers.length === 0" class="text-center py-5 bg-white rounded border">
              <i class="icofont-users-alt-5 display-3 text-muted"></i>
              <h5 class="font-weight-bold text-dark mt-2">এখনো কোনো ফ্যামিলি মেম্বার যুক্ত করেননি</h5>
              <button class="btn btn-danger btn-sm btn-round-full font-weight-bold mt-2" @click="openAddFamilyModal">
                সদস্য যুক্ত করুন
              </button>
            </div>

            <div v-else class="row">
              <div v-for="member in familyMembers" :key="member.id" class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border rounded-lg shadow-sm p-3 bg-white">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center">
                      <div class="metric-icon-box bg-primary-light text-primary mr-2">
                        <i class="icofont-user-alt-7"></i>
                      </div>
                      <div>
                        <h6 class="font-weight-bold text-dark mb-0">{{ member.name }}</h6>
                        <span class="badge badge-danger" style="font-size: 10px;">{{ member.relationship }}</span>
                      </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger py-0 px-2" @click="deleteFamilyMember(member.id)">
                      <i class="icofont-trash"></i>
                    </button>
                  </div>
                  <div class="small text-muted mb-3 border-top pt-2">
                    <div><strong>লিঙ্গ / বয়স:</strong> {{ member.gender }} &bull; {{ member.age_or_dob || '-' }}</div>
                    <div><strong>রক্তের গ্রুপ:</strong> <span class="text-danger font-weight-bold">{{ member.blood_group || '-' }}</span></div>
                    <div v-if="member.medical_notes"><strong>নোট:</strong> {{ member.medical_notes }}</div>
                  </div>
                  <button class="btn btn-sm btn-outline-primary btn-round-full mt-auto font-weight-bold" @click="selectFamilyMember(member)">
                    এই প্রোফাইলে সুইচ করুন
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 5: DAILY MEDICINE SCHEDULE & AI PRESCRIPTION READER -->
          <!-- ========================================================= -->
          <div v-if="activeTab === 'medicines'">
            <!-- Action Bar -->
            <div class="bg-gradient-soft p-3 p-md-4 rounded-lg mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center">
              <div class="mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                  <span class="badge badge-success mr-2 px-2 py-1 font-weight-bold">দৈনিক সুস্থতা ট্র্যাকার</span>
                  <h5 class="font-weight-bold text-dark mb-0">দৈনিক ঔষধের রুটিন ও রিমাইন্ডার</h5>
                </div>
                <p class="text-muted small mb-0 mt-1">
                  প্রতিদিনের প্রেসক্রিপশনের ঔষধ সময়মতো খেয়ে ট্র্যাকার আপডেট করুন। বাদ দিলে ডাক্তার পরবর্তী ভিজিটে দেখতে পারবেন।
                </p>
              </div>
              <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <button class="btn btn-outline-success font-weight-bold btn-round-full px-3 py-2 btn-sm shadow-sm" @click="showAddMedicineModal = true">
                  <i class="icofont-plus mr-1"></i> নতুন ঔষধ যোগ করুন
                </button>
                <button class="btn btn-primary font-weight-bold btn-round-full px-3 py-2 btn-sm shadow-sm" @click="openAiRxModal">
                  <i class="icofont-camera mr-1"></i> 📸 AI প্রেসক্রিপশন স্ক্যানার
                </button>
              </div>
            </div>

            <!-- Routine Sections: Morning, Noon, Evening, Night -->
            <div v-if="medicineSchedules.length === 0" class="text-center py-5 bg-white rounded border">
              <i class="icofont-capsule display-3 text-muted"></i>
              <h5 class="font-weight-bold text-dark mt-2">এখনো কোনো নিয়মিত ঔষধ যোগ করা হয়নি</h5>
              <p class="text-muted small">আপনার প্রেসক্রিপশনের ছবি তুলে AI দিয়ে স্বয়ংক্রিয় রুটিন বানিয়ে নিন।</p>
              <button class="btn btn-primary btn-sm btn-round-full font-weight-bold mt-2" @click="openAiRxModal">
                <i class="icofont-magic mr-1"></i> প্রেসক্রিপশন স্ক্যান করুন
              </button>
            </div>

            <div v-else>
              <!-- Morning Slot -->
              <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge badge-warning text-dark px-2 py-1 mr-2 font-weight-bold">
                    <i class="icofont-sun-alt mr-1"></i> সকাল (Morning)
                  </span>
                  <small class="text-muted">সকালের নাস্তার আগে বা পরের ঔষধসমূহ</small>
                </div>
                <div class="row">
                  <div 
                    v-for="med in getSlotMedicines('morning')" 
                    :key="'morning_' + med.id" 
                    class="col-md-6 col-lg-4 mb-3"
                  >
                    <div class="card h-100 border rounded-lg shadow-sm p-3 bg-white">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                          <h6 class="font-weight-bold text-dark mb-0">{{ med.medicine_name }}</h6>
                          <span class="badge badge-light border text-primary font-weight-bold" style="font-size: 11px;">{{ med.dosage }}</span>
                        </div>
                        <span 
                          class="badge font-weight-bold"
                          :class="getAdherenceBadge(med.id).class"
                          style="font-size: 11px;"
                        >
                          {{ getAdherenceBadge(med.id).text }}
                        </span>
                      </div>
                      <p class="small text-muted mb-2">
                        <strong>খাওয়ার নিয়ম:</strong> {{ formatTimingInstruction(med.timing_instruction) }}
                        <span v-if="med.days_duration"> &bull; {{ med.days_duration }} দিন</span>
                      </p>
                      <div class="d-flex align-items-center mt-auto pt-2 border-top" style="gap: 6px;">
                        <button 
                          class="btn btn-xs btn-success font-weight-bold flex-fill py-1"
                          @click="logAdherence(med.id, 'taken')"
                        >
                          <i class="icofont-check mr-1"></i> খেয়েছি
                        </button>
                        <button 
                          class="btn btn-xs btn-outline-secondary font-weight-bold flex-fill py-1"
                          @click="logAdherence(med.id, 'skipped')"
                        >
                          <i class="icofont-close mr-1"></i> বাদ দিয়েছি
                        </button>
                        <button class="btn btn-xs btn-outline-danger py-1 px-2" @click="deleteMedicineSchedule(med.id)">
                          <i class="icofont-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-if="getSlotMedicines('morning').length === 0" class="col-12">
                    <p class="text-muted small italic pl-2">সকালের কোনো ওষুধ নেই</p>
                  </div>
                </div>
              </div>

              <!-- Noon Slot -->
              <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge badge-info px-2 py-1 mr-2 font-weight-bold">
                    <i class="icofont-sun mr-1"></i> দুপুর (Noon)
                  </span>
                  <small class="text-muted">দুপুরের খাবারের সময়কার ঔষধসমূহ</small>
                </div>
                <div class="row">
                  <div 
                    v-for="med in getSlotMedicines('noon')" 
                    :key="'noon_' + med.id" 
                    class="col-md-6 col-lg-4 mb-3"
                  >
                    <div class="card h-100 border rounded-lg shadow-sm p-3 bg-white">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                          <h6 class="font-weight-bold text-dark mb-0">{{ med.medicine_name }}</h6>
                          <span class="badge badge-light border text-primary font-weight-bold" style="font-size: 11px;">{{ med.dosage }}</span>
                        </div>
                        <span 
                          class="badge font-weight-bold"
                          :class="getAdherenceBadge(med.id).class"
                          style="font-size: 11px;"
                        >
                          {{ getAdherenceBadge(med.id).text }}
                        </span>
                      </div>
                      <p class="small text-muted mb-2">
                        <strong>খাওয়ার নিয়ম:</strong> {{ formatTimingInstruction(med.timing_instruction) }}
                        <span v-if="med.days_duration"> &bull; {{ med.days_duration }} দিন</span>
                      </p>
                      <div class="d-flex align-items-center mt-auto pt-2 border-top" style="gap: 6px;">
                        <button 
                          class="btn btn-xs btn-success font-weight-bold flex-fill py-1"
                          @click="logAdherence(med.id, 'taken')"
                        >
                          <i class="icofont-check mr-1"></i> খেয়েছি
                        </button>
                        <button 
                          class="btn btn-xs btn-outline-secondary font-weight-bold flex-fill py-1"
                          @click="logAdherence(med.id, 'skipped')"
                        >
                          <i class="icofont-close mr-1"></i> বাদ দিয়েছি
                        </button>
                        <button class="btn btn-xs btn-outline-danger py-1 px-2" @click="deleteMedicineSchedule(med.id)">
                          <i class="icofont-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-if="getSlotMedicines('noon').length === 0" class="col-12">
                    <p class="text-muted small italic pl-2">দুপুরের কোনো ওষুধ নেই</p>
                  </div>
                </div>
              </div>

              <!-- Night Slot -->
              <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge badge-dark px-2 py-1 mr-2 font-weight-bold">
                    <i class="icofont-moon mr-1"></i> রাত (Night)
                  </span>
                  <small class="text-muted">রাতের খাবার বা ঘুমানোর পূর্বের ঔষধসমূহ</small>
                </div>
                <div class="row">
                  <div 
                    v-for="med in getSlotMedicines('night')" 
                    :key="'night_' + med.id" 
                    class="col-md-6 col-lg-4 mb-3"
                  >
                    <div class="card h-100 border rounded-lg shadow-sm p-3 bg-white">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                          <h6 class="font-weight-bold text-dark mb-0">{{ med.medicine_name }}</h6>
                          <span class="badge badge-light border text-primary font-weight-bold" style="font-size: 11px;">{{ med.dosage }}</span>
                        </div>
                        <span 
                          class="badge font-weight-bold"
                          :class="getAdherenceBadge(med.id).class"
                          style="font-size: 11px;"
                        >
                          {{ getAdherenceBadge(med.id).text }}
                        </span>
                      </div>
                      <p class="small text-muted mb-2">
                        <strong>খাওয়ার নিয়ম:</strong> {{ formatTimingInstruction(med.timing_instruction) }}
                        <span v-if="med.days_duration"> &bull; {{ med.days_duration }} দিন</span>
                      </p>
                      <div class="d-flex align-items-center mt-auto pt-2 border-top" style="gap: 6px;">
                        <button 
                          class="btn btn-xs btn-success font-weight-bold flex-fill py-1"
                          @click="logAdherence(med.id, 'taken')"
                        >
                          <i class="icofont-check mr-1"></i> খেয়েছি
                        </button>
                        <button 
                          class="btn btn-xs btn-outline-secondary font-weight-bold flex-fill py-1"
                          @click="logAdherence(med.id, 'skipped')"
                        >
                          <i class="icofont-close mr-1"></i> বাদ দিয়েছি
                        </button>
                        <button class="btn btn-xs btn-outline-danger py-1 px-2" @click="deleteMedicineSchedule(med.id)">
                          <i class="icofont-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-if="getSlotMedicines('night').length === 0" class="col-12">
                    <p class="text-muted small italic pl-2">রাতের কোনো ওষুধ নেই</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 6: DIGITAL HEALTH PASSPORT & PVC EMERGENCY QR CARD -->
          <!-- ========================================================= -->
          <div v-if="activeTab === 'passport'">
            <div class="bg-gradient-soft p-3 p-md-4 rounded-lg mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center">
              <div class="mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                  <span class="badge badge-danger mr-2 px-2 py-1 font-weight-bold">লাইফ সেভিং আইডি</span>
                  <h5 class="font-weight-bold text-dark mb-0">ডিজিটাল ইমার্জেন্সি হেলথ পাসপোর্ট ও কিউআর কার্ড</h5>
                </div>
                <p class="text-muted small mb-0 mt-1">
                  জরুরি পরিস্থিতিতে বা পথ দুর্ঘটনায় যেকোনো উদ্ধারকারী বা ডাক্তার এই কিউআর কোড স্ক্যান করে তাৎক্ষণিক আপনার রক্তের গ্রুপ ও জরুরি ফোন পাবেন।
                </p>
              </div>
              <div class="d-flex align-items-center" style="gap: 8px;">
                <button class="btn btn-outline-dark btn-sm font-weight-bold rounded-pill" @click="generatePassportToken">
                  <i class="icofont-refresh mr-1"></i> কিউআর কোড নবায়ন
                </button>
                <button class="btn btn-danger btn-sm font-weight-bold rounded-pill shadow-sm" @click="printEmergencyCard">
                  <i class="icofont-print mr-1"></i> পিভিসি কার্ড প্রিন্ট করুন
                </button>
              </div>
            </div>

            <div class="row">
              <!-- PVC Style Emergency Health Card -->
              <div class="col-lg-6 mb-4">
                <div 
                  id="pvc-health-card" 
                  class="rounded-xl shadow-lg p-4 text-white position-relative" 
                  style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); border-radius: 16px; border: 1px solid rgba(255,255,255,0.15); min-height: 260px;"
                >
                  <!-- Card Header -->
                  <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary mb-3">
                    <div class="d-flex align-items-center">
                      <i class="icofont-heart-beat text-danger mr-2" style="font-size: 22px;"></i>
                      <div>
                        <div class="font-weight-bold text-white text-uppercase" style="font-size: 13px; letter-spacing: 1px;">BANGLADESH EMERGENCY HEALTH PASS</div>
                        <div class="text-muted" style="font-size: 10px;">Sukhi Manush Digital Health Ecosystem</div>
                      </div>
                    </div>
                    <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 11px;">EMERGENCY SOS</span>
                  </div>

                  <!-- Card Body -->
                  <div class="row align-items-center">
                    <div class="col-8">
                      <div class="small text-secondary mb-1">রোগীর নাম (PATIENT NAME)</div>
                      <h5 class="font-weight-bold text-white mb-2">{{ user?.name }}</h5>

                      <div class="row mb-2">
                        <div class="col-6">
                          <div class="small text-secondary">রক্তের গ্রুপ (BLOOD)</div>
                          <div class="font-weight-bold text-danger" style="font-size: 18px;">
                            {{ passportProfileForm.blood_group || user?.blood_group || 'অনির্ধারিত' }}
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="small text-secondary">জাতীয় আইডি / রোল</div>
                          <div class="font-weight-bold text-white small">NID-{{ user?.id ? (user.id * 84930) : '849301' }}</div>
                        </div>
                      </div>

                      <div class="mb-2">
                        <div class="small text-secondary">জরুরি ফোন (EMERGENCY CONTACT)</div>
                        <div class="font-weight-bold text-warning" style="font-size: 14px;">
                          <i class="icofont-phone mr-1"></i> {{ passportProfileForm.emergency_contact_phone || user?.emergency_contact_phone || user?.phone || 'যোগ করা হয়নি' }}
                        </div>
                      </div>

                      <div>
                        <div class="small text-secondary">মেডিকেল অ্যালার্ট (ALLERGIES / NOTES)</div>
                        <div class="small text-light font-weight-bold">
                          {{ passportProfileForm.emergency_medical_notes || user?.emergency_medical_notes || 'কোনো পরিচিত অ্যালার্জি নেই' }}
                        </div>
                      </div>
                    </div>

                    <!-- QR Code Column -->
                    <div class="col-4 text-center">
                      <div class="bg-white p-2 rounded-lg shadow d-inline-block">
                        <img 
                          v-if="passportToken" 
                          :src="'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' + encodeURIComponent(passportPublicUrl)" 
                          alt="Emergency QR" 
                          class="img-fluid rounded" 
                          style="width: 110px; height: 110px;"
                        >
                        <div v-else class="spinner-border spinner-border-sm text-danger" role="status"></div>
                      </div>
                      <div class="text-secondary mt-1 font-weight-bold" style="font-size: 9px; letter-spacing: 0.5px;">SCAN IN EMERGENCY</div>
                    </div>
                  </div>

                  <!-- Card Footer -->
                  <div class="pt-2 mt-3 border-top border-secondary d-flex justify-content-between align-items-center text-muted" style="font-size: 9px;">
                    <span>🛡️ এন্ড-টু-এন্ড এনক্রিপ্টেড সেফ ডাটা</span>
                    <span>sukhimanush.com/emergency-pass</span>
                  </div>
                </div>
              </div>

              <!-- Profile Details & Emergency Settings -->
              <div class="col-lg-6 mb-4">
                <div class="card border rounded-lg shadow-sm p-3 p-md-4 bg-white h-100">
                  <h6 class="font-weight-bold text-dark mb-3">
                    <i class="icofont-ui-edit text-primary mr-1"></i> জরুরি তথ্য ও অ্যালার্ট আপডেট করুন
                  </h6>
                  <form @submit.prevent="saveEmergencyProfile">
                    <div class="form-group mb-2">
                      <label class="small font-weight-bold text-dark">রক্তের গ্রুপ (Blood Group)</label>
                      <select v-model="passportProfileForm.blood_group" class="form-control form-control-sm">
                        <option value="">নির্বাচন করুন</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                      </select>
                    </div>

                    <div class="form-group mb-2">
                      <label class="small font-weight-bold text-dark">জরুরি যোগাযোগের ফোন নম্বর (অভিভাবক / স্বজন)</label>
                      <input 
                        type="tel" 
                        v-model="passportProfileForm.emergency_contact_phone" 
                        class="form-control form-control-sm" 
                        placeholder="যেমন: 01712-345678" 
                        required
                      >
                    </div>

                    <div class="form-group mb-3">
                      <label class="small font-weight-bold text-dark">মেডিকেল শর্তাবলী ও অ্যালার্জি (পেনিসিলিন, ডায়াবেটিস ইত্যাদি)</label>
                      <textarea 
                        v-model="passportProfileForm.emergency_medical_notes" 
                        class="form-control form-control-sm" 
                        rows="2" 
                        placeholder="যেমন: ডায়াবেটিস রোগী, অ্যাসপিরিন অ্যালার্জি আছে..."
                      ></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                      <a :href="passportPublicUrl" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold">
                        <i class="icofont-external-link mr-1"></i> পাবলিক ভিউ দেখুন
                      </a>
                      <button type="submit" class="btn btn-sm btn-success px-3 font-weight-bold">
                        <i class="icofont-check mr-1"></i> তথ্য সংরক্ষণ করুন
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL: ADD FAMILY MEMBER -->
    <!-- ========================================================= -->
    <div v-if="showFamilyModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1060;">
      <div class="custom-modal bg-white rounded shadow-lg p-4" style="max-width: 480px; width: 92%;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <h5 class="font-weight-bold text-dark mb-0">👨‍👩‍👧‍👦 ফ্যামিলি মেম্বার যুক্ত করুন</h5>
          <button type="button" class="close text-muted" @click="showFamilyModal = false">&times;</button>
        </div>
        <form @submit.prevent="submitFamilyMember">
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">সদস্যের পুরো নাম <span class="text-danger">*</span></label>
            <input type="text" v-model="familyForm.name" class="form-control form-control-sm" placeholder="যেমন: সালমা বেগম" required>
          </div>
          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">সম্পর্ক <span class="text-danger">*</span></label>
              <select v-model="familyForm.relationship" class="form-control form-control-sm" required>
                <option value="Father">বাবা (Father)</option>
                <option value="Mother">মা (Mother)</option>
                <option value="Spouse">স্বামী / স্ত্রী (Spouse)</option>
                <option value="Child">সন্তান (Child)</option>
                <option value="Sibling">ভাই / বোন (Sibling)</option>
                <option value="Other">অন্যান্য (Other)</option>
              </select>
            </div>
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">লিঙ্গ</label>
              <select v-model="familyForm.gender" class="form-control form-control-sm">
                <option value="Male">পুরুষ (Male)</option>
                <option value="Female">মহিলা (Female)</option>
                <option value="Other">অন্যান্য</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">বয়স</label>
              <input type="text" v-model="familyForm.age_or_dob" class="form-control form-control-sm" placeholder="যেমন: ৫৮ বছর">
            </div>
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">রক্তের গ্রুপ</label>
              <select v-model="familyForm.blood_group" class="form-control form-control-sm">
                <option value="">সিলেক্ট করুন</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
              </select>
            </div>
          </div>
          <div class="form-group mb-3">
            <label class="small font-weight-bold text-dark">মেডিকেল নোট (ঐচ্ছিক)</label>
            <input type="text" v-model="familyForm.medical_notes" class="form-control form-control-sm" placeholder="যেমন: ডায়াবেটিস বা অ্যালার্জি আছে">
          </div>
          <div class="d-flex justify-content-end border-top pt-2">
            <button type="button" class="btn btn-light btn-sm mr-2" @click="showFamilyModal = false">বাতিল</button>
            <button type="submit" class="btn btn-danger btn-sm px-3 font-weight-bold">যুক্ত করুন</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL: ADD HEALTH READING (SUGAR / BP) -->
    <!-- ========================================================= -->
    <div v-if="showMetricModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1060;">
      <div class="custom-modal bg-white rounded shadow-lg p-4" style="max-width: 480px; width: 92%;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <h5 class="font-weight-bold text-dark mb-0">🩸 নতুন স্বাস্থ্য পরিমাপ সেভ করুন</h5>
          <button type="button" class="close text-muted" @click="showMetricModal = false">&times;</button>
        </div>
        <form @submit.prevent="submitHealthMetric">
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">পরিমাপের ধরন <span class="text-danger">*</span></label>
            <select v-model="metricForm.metric_type" class="form-control form-control-sm" required>
              <option value="blood_sugar">ব্লাড সুগার / ডায়াবেটিস (mmol/L)</option>
              <option value="blood_pressure">রক্তচাপ / Blood Pressure (mmHg)</option>
              <option value="weight">শরীরের ওজন / Weight (kg)</option>
            </select>
          </div>

          <div v-if="metricForm.metric_type === 'blood_sugar'" class="row">
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">সুগার রিডিং (mmol/L) <span class="text-danger">*</span></label>
              <input type="number" step="0.1" v-model="metricForm.value_primary" class="form-control form-control-sm" placeholder="e.g. 7.2" required>
            </div>
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">পরিমাপের সময়</label>
              <select v-model="metricForm.measured_time" class="form-control form-control-sm">
                <option value="খালি পেটে (Fasting)">খালি পেটে (Fasting)</option>
                <option value="খাওয়ার ২ ঘণ্টা পর">খাওয়ার ২ ঘণ্টা পর</option>
                <option value="ঘুমানোর আগে">ঘুমানোর আগে</option>
                <option value="যেকোনো সময় (Random)">যেকোনো সময় (Random)</option>
              </select>
            </div>
          </div>

          <div v-else-if="metricForm.metric_type === 'blood_pressure'" class="row">
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">Systolic (উপরের) <span class="text-danger">*</span></label>
              <input type="number" v-model="metricForm.value_primary" class="form-control form-control-sm" placeholder="e.g. 120" required>
            </div>
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">Diastolic (নিচের) <span class="text-danger">*</span></label>
              <input type="number" v-model="metricForm.value_secondary" class="form-control form-control-sm" placeholder="e.g. 80" required>
            </div>
          </div>

          <div v-else class="form-group mb-2">
            <label class="small font-weight-bold text-dark">ওজন (kg) <span class="text-danger">*</span></label>
            <input type="number" step="0.1" v-model="metricForm.value_primary" class="form-control form-control-sm" placeholder="e.g. 68.5" required>
          </div>

          <div class="form-group mb-3">
            <label class="small font-weight-bold text-dark">তারিখ</label>
            <input type="date" v-model="metricForm.recorded_date" class="form-control form-control-sm">
          </div>

          <div class="d-flex justify-content-end border-top pt-2">
            <button type="button" class="btn btn-light btn-sm mr-2" @click="showMetricModal = false">বাতিল</button>
            <button type="submit" class="btn btn-danger btn-sm px-3 font-weight-bold">সেভ করুন</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL: DIAGNOSTIC HOME SAMPLE BOOKING -->
    <!-- ========================================================= -->
    <div v-if="showDiagnosticModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1060;">
      <div class="custom-modal bg-white rounded shadow-lg p-4" style="max-width: 520px; width: 92%;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <h5 class="font-weight-bold text-dark mb-0">🧪 বাড়ি বসে টেস্ট স্যাম্পল কালেকশন বুকিং</h5>
          <button type="button" class="close text-muted" @click="showDiagnosticModal = false">&times;</button>
        </div>
        <form @submit.prevent="submitDiagnosticOrder">
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">ডায়াগনস্টিক পার্টনার নির্বাচন করুন <span class="text-danger">*</span></label>
            <select v-model="diagnosticForm.diagnostic_center" class="form-control form-control-sm" required>
              <option value="Popular Diagnostic Center">পপুলার ডায়াগনস্টিক সেন্টার (Popular Diagnostic)</option>
              <option value="Ibn Sina Diagnostic Center">ইবনে সিনা ডায়াগনস্টিক সেন্টার (Ibn Sina)</option>
              <option value="Labaid Diagnostic Center">ল্যাবএইড ডায়াগনস্টিক সেন্টার (Labaid)</option>
              <option value="District Sadar Hospital Lab">জেলা সদর হাসপাতাল ল্যাব</option>
            </select>
          </div>
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">টেস্টের নামসমূহ</label>
            <div class="bg-light p-2 rounded small text-dark font-weight-bold">
              {{ diagnosticForm.test_names.join(', ') }}
            </div>
          </div>
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">স্যাম্পল কালেকশন ঠিকানা <span class="text-danger">*</span></label>
            <textarea v-model="diagnosticForm.collection_address" class="form-control form-control-sm" rows="2" placeholder="আপনার বাসার ঠিকানা লিখুন..." required></textarea>
          </div>
          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">তারিখ <span class="text-danger">*</span></label>
              <input type="date" v-model="diagnosticForm.preferred_date" class="form-control form-control-sm" required>
            </div>
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">যোগাযোগের ফোন নম্বর <span class="text-danger">*</span></label>
              <input type="tel" v-model="diagnosticForm.contact_phone" class="form-control form-control-sm" required>
            </div>
          </div>
          <div class="d-flex justify-content-end border-top pt-2 mt-2">
            <button type="button" class="btn btn-light btn-sm mr-2" @click="showDiagnosticModal = false">বাতিল</button>
            <button type="submit" class="btn btn-danger btn-sm px-4 font-weight-bold">বুকিং নিশ্চিত করুন</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- UPLOAD MEDICAL REPORT MODAL -->
    <!-- ========================================================= -->
    <div v-if="showUploadModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1055;">
      <div class="custom-modal bg-white rounded shadow-lg p-3 p-md-4" style="max-width: 550px; width: 94%; max-height: 92vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
          <h5 class="font-weight-bold text-dark mb-0">📸 নতুন টেস্ট রিপোর্ট আপলোড</h5>
          <button type="button" class="close text-muted" @click="closeUploadModal">&times;</button>
        </div>
        <form @submit.prevent="submitReportUpload">
          <div class="upload-dropzone p-3 rounded border text-center mb-3 bg-light position-relative">
            <input type="file" ref="reportFileInput" class="d-none" accept="image/*,application/pdf" capture="environment" @change="onReportFileSelected">
            <div v-if="selectedFile" class="d-flex flex-column align-items-center">
              <img v-if="filePreviewUrl && isImageFile" :src="filePreviewUrl" class="img-thumbnail rounded mb-2" style="max-height: 150px;">
              <i v-else class="icofont-file-pdf text-danger display-4 mb-2"></i>
              <span class="font-weight-bold text-dark small">{{ selectedFile.name }}</span>
              <button type="button" class="btn btn-sm btn-outline-danger mt-2 py-0 px-2" @click="triggerFileInput">পরিবর্তন করুন</button>
            </div>
            <div v-else class="py-3 cursor-pointer" @click="triggerFileInput">
              <i class="icofont-camera display-4 text-danger mb-2"></i>
              <h6 class="font-weight-bold text-dark">ছবি তুলুন বা ফাইল পছন্দ করুন</h6>
              <button type="button" class="btn btn-sm btn-danger font-weight-bold px-3 mt-1">ফাইল নির্বাচন করুন</button>
            </div>
          </div>
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">রিপোর্টের শিরোনাম <span class="text-danger">*</span></label>
            <input type="text" v-model="uploadForm.report_title" class="form-control form-control-sm" required>
          </div>
          <div class="row">
            <div class="col-7 form-group mb-2">
              <label class="small font-weight-bold text-dark">ল্যাব / ডায়াগনস্টিক সেন্টার</label>
              <input type="text" v-model="uploadForm.diagnostic_center" class="form-control form-control-sm" placeholder="e.g. Popular, Ibn Sina">
            </div>
            <div class="col-5 form-group mb-2">
              <label class="small font-weight-bold text-dark">তারিখ</label>
              <input type="date" v-model="uploadForm.report_date" class="form-control form-control-sm">
            </div>
          </div>
          <div class="d-flex justify-content-end border-top pt-2">
            <button type="button" class="btn btn-light btn-sm mr-2" @click="closeUploadModal">বাতিল</button>
            <button type="submit" class="btn btn-danger btn-sm px-4 font-weight-bold" :disabled="uploadingReport || !selectedFile">
              <span v-if="uploadingReport">আপলোড হচ্ছে...</span>
              <span v-else>আপলোড করুন</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div v-if="showLightbox" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.85); z-index: 1060;">
      <div class="bg-white rounded-lg shadow-2xl d-flex flex-column" style="max-width: 900px; width: 95%; height: 90vh; overflow: hidden;">
        <div class="p-3 bg-dark text-white d-flex justify-content-between align-items-center">
          <h6 class="font-weight-bold mb-0 text-white">{{ activeReport?.report_title }}</h6>
          <button type="button" class="btn btn-sm btn-danger py-1 px-2 font-weight-bold" @click="showLightbox = false">&times; বন্ধ করুন</button>
        </div>
        <div class="flex-grow-1 bg-dark d-flex align-items-center justify-content-center overflow-auto p-2">
          <img v-if="activeReport?.file_type === 'image'" :src="activeReport.file_url" class="img-fluid rounded" style="max-height: 75vh; object-fit: contain;">
          <iframe v-else :src="activeReport?.file_url" class="w-100 h-100 border-0 rounded"></iframe>
        </div>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- SMART BANGLA AUDIO PRESCRIPTION PLAYER MODAL -->
    <!-- ========================================================= -->
    <div v-if="showAudioRxModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.75); z-index: 1070;">
      <div class="custom-modal bg-white rounded-lg shadow-2xl overflow-hidden" style="max-width: 600px; width: 95%;">
        <!-- Header -->
        <div class="bg-primary text-white p-3 px-4 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <div class="sound-wave mr-3" v-if="isAudioPlaying">
              <span></span><span></span><span></span><span></span><span></span>
            </div>
            <i v-else class="icofont-volume-up display-5 mr-2"></i>
            <div>
              <h5 class="mb-0 font-weight-bold text-white">প্রেসক্রিপশন অডিও সহায়ক (Human Bengali Voice)</h5>
              <small class="text-white-50">
                {{ activeAudioRx?.prescription_number }} | {{ activeAudioRx?.doctor?.name }}
              </small>
            </div>
          </div>
          <button type="button" class="close text-white" @click="closeAudioRxModal">&times;</button>
        </div>

        <!-- Voice Quality Badge, Download & Speed Controls -->
        <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
          <div>
            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 12px;">
              <i class="icofont-check-circled mr-1"></i> ১০০% প্রাকৃতিক মানুষের বাংলা কণ্ঠস্বর (Human Voice MP3)
            </span>
          </div>

          <div class="d-flex align-items-center" style="gap: 6px;">
            <!-- Download Audio Link -->
            <a 
              v-if="activeAudioRx" 
              :href="'/api/prescriptions/' + activeAudioRx.id + '/audio'" 
              :download="'Prescription_' + activeAudioRx.prescription_number + '_Audio.mp3'"
              class="btn btn-xs btn-outline-dark rounded-pill py-1 px-2 font-weight-bold mr-1"
              style="font-size: 11px;"
              title="অডিও ডাউনলোড করুন"
            >
              <i class="icofont-download mr-1"></i> ডাউনলোড
            </a>

            <!-- Speed Controls -->
            <span class="small text-muted font-weight-bold">গতি:</span>
            <button 
              type="button" 
              class="btn btn-xs py-0 px-2 rounded-pill font-weight-bold" 
              :class="audioSpeechRate === 0.75 ? 'btn-danger' : 'btn-outline-secondary'"
              style="font-size: 11px;"
              @click="changeSpeechRate(0.75)"
            >
              0.75x (ধীর)
            </button>
            <button 
              type="button" 
              class="btn btn-xs py-0 px-2 rounded-pill font-weight-bold" 
              :class="audioSpeechRate === 0.85 ? 'btn-danger' : 'btn-outline-secondary'"
              style="font-size: 11px;"
              @click="changeSpeechRate(0.85)"
            >
              0.85x
            </button>
            <button 
              type="button" 
              class="btn btn-xs py-0 px-2 rounded-pill font-weight-bold" 
              :class="audioSpeechRate === 1.0 ? 'btn-danger' : 'btn-outline-secondary'"
              style="font-size: 11px;"
              @click="changeSpeechRate(1.0)"
            >
              1.0x (স্বাভাবিক)
            </button>
          </div>
        </div>

        <!-- Audio Player Timeline Scrubber -->
        <div class="px-4 py-2 bg-light border-bottom">
          <div class="d-flex justify-content-between text-muted small font-weight-bold mb-1" style="font-size: 11px;">
            <span>{{ formatAudioTime(audioCurrentTime) }}</span>
            <span v-if="isAudioLoading" class="text-danger font-weight-bold">
              <span class="spinner-border spinner-border-sm mr-1" style="width: 12px; height: 12px;"></span> মানুষের কণ্ঠস্বর প্রস্তুত হচ্ছে...
            </span>
            <span v-else class="text-success font-weight-bold">
              <i class="icofont-ui-play mr-1"></i> এইচডি বাংলা অডিও
            </span>
            <span>{{ formatAudioTime(audioDuration) }}</span>
          </div>
          <div class="progress cursor-pointer shadow-sm" style="height: 8px; border-radius: 4px; background: #e2e8f0;" @click="seekAudio">
            <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" role="progressbar" :style="{ width: audioProgressPercent + '%' }"></div>
          </div>
        </div>

        <!-- Body: Clean Spoken Transcript & Medicine Breakdown -->
        <div class="p-4" style="max-height: 50vh; overflow-y: auto;">
          <!-- Status Banner -->
          <div class="alert alert-success py-2 px-3 small rounded-lg mb-3 d-flex align-items-center justify-content-between border-success">
            <div>
              <i class="icofont-headphone-alt text-success mr-1"></i>
              <span v-if="isAudioPlaying" class="font-weight-bold text-dark">🔊 প্রাকৃতিক মানুষের কণ্ঠে প্রেসক্রিপশন বাজছে... শুনতে থাকুন।</span>
              <span v-else class="font-weight-bold text-muted">⏹️ অডিও থেমে আছে। পুনরায় শুনতে নিচের বাটনে চাপ দিন।</span>
            </div>
            <span class="badge badge-light border">প্রাকৃতিক উচ্চারণ</span>
          </div>

          <!-- Diagnosis -->
          <div class="mb-3 p-3 bg-light rounded border-left border-primary" style="border-left-width: 4px !important;">
            <span class="small text-muted font-weight-bold d-block">রোগ নির্ণয় বা শারীরিক সমস্যা:</span>
            <strong class="text-dark" style="font-size: 15px;">{{ activeAudioRx?.diagnosis || 'চিকিৎসা পরামর্শ' }}</strong>
          </div>

          <!-- Medicines Spoken Schedule Cards -->
          <h6 class="font-weight-bold text-dark mb-2">
            <i class="icofont-capsule mr-1 text-danger"></i> ওষুধের স্পষ্ট নিয়মাবলী:
          </h6>
          <div v-for="(m, idx) in activeAudioRx?.medicines || []" :key="idx" class="p-3 mb-2 bg-white rounded border shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <strong class="text-dark" style="font-size: 15px;">{{ idx + 1 }}. {{ m.name }}</strong>
              <span class="badge badge-primary px-2 py-1 font-weight-bold">{{ formatDosageBangla(m.dosage) }}</span>
            </div>
            <div class="small text-muted mt-2">
              <div><strong>খাওয়ার নিয়ম:</strong> <span class="text-success font-weight-bold">{{ formatInstructionBangla(m.instruction) }}</span></div>
              <div><strong>কতদিন খাবেন:</strong> <span class="text-dark font-weight-bold">{{ formatDurationBangla(m.duration) }}</span></div>
            </div>
          </div>

          <!-- Advice -->
          <div v-if="activeAudioRx?.advice" class="mt-3 p-3 bg-light rounded border-left border-warning" style="border-left-width: 4px !important;">
            <span class="small text-muted font-weight-bold d-block">ডাক্তারের বিশেষ উপদেশ:</span>
            <span class="text-dark font-weight-bold small">{{ activeAudioRx.advice }}</span>
          </div>
        </div>

        <!-- Footer Control Buttons -->
        <div class="modal-footer bg-light p-3 justify-content-between">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill font-weight-bold px-3" @click="closeAudioRxModal">
            বন্ধ করুন
          </button>

          <div class="d-flex align-items-center" style="gap: 8px;">
            <!-- Replay from start -->
            <button 
              type="button" 
              class="btn btn-outline-dark btn-sm rounded-pill font-weight-bold px-3"
              @click="restartAudio"
            >
              <i class="icofont-refresh mr-1"></i> শুরু থেকে
            </button>

            <!-- Pause / Resume -->
            <button 
              type="button" 
              class="btn btn-danger btn-sm rounded-pill font-weight-bold px-4 shadow-sm"
              @click="togglePauseAudio"
            >
              <i :class="isAudioPlaying ? 'icofont-pause' : 'icofont-play'" class="mr-1"></i>
              <span>{{ isAudioPlaying ? 'বিরতি (Pause)' : 'চালু করুন (Play)' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL: ADD MEDICINE MANUALLY -->
    <!-- ========================================================= -->
    <div v-if="showAddMedicineModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1060;">
      <div class="custom-modal bg-white rounded-lg shadow-lg p-4" style="max-width: 500px; width: 92%;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <h5 class="font-weight-bold text-dark mb-0">💊 নতুন ঔষধের রুটিন যুক্ত করুন</h5>
          <button type="button" class="close text-muted" @click="showAddMedicineModal = false">&times;</button>
        </div>
        <form @submit.prevent="saveManualMedicine">
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">ঔষধের নাম (Medicine Name) <span class="text-danger">*</span></label>
            <input type="text" v-model="newMedicineForm.medicine_name" class="form-control form-control-sm" placeholder="যেমন: Napa Extra 500mg, Seclo 20mg" required>
          </div>
          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">ডোজ (Dosage)</label>
              <input type="text" v-model="newMedicineForm.dosage" class="form-control form-control-sm" placeholder="যেমন: ১+০+১ বা 1-0-1" required>
            </div>
            <div class="col-6 form-group mb-2">
              <label class="small font-weight-bold text-dark">কতদিন খাবেন (দিন)</label>
              <input type="number" v-model="newMedicineForm.days_duration" class="form-control form-control-sm" placeholder="7">
            </div>
          </div>
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">খাওয়ার সময়কাল নির্ধারণ করুন</label>
            <div class="d-flex flex-wrap gap-2">
              <label class="mr-3 small font-weight-bold cursor-pointer">
                <input type="checkbox" v-model="newMedicineForm.morning"> সকাল (Morning)
              </label>
              <label class="mr-3 small font-weight-bold cursor-pointer">
                <input type="checkbox" v-model="newMedicineForm.noon"> দুপুর (Noon)
              </label>
              <label class="mr-3 small font-weight-bold cursor-pointer">
                <input type="checkbox" v-model="newMedicineForm.evening"> বিকাল (Evening)
              </label>
              <label class="small font-weight-bold cursor-pointer">
                <input type="checkbox" v-model="newMedicineForm.night"> রাত (Night)
              </label>
            </div>
          </div>
          <div class="form-group mb-3">
            <label class="small font-weight-bold text-dark">খাবার আগে না পরে?</label>
            <select v-model="newMedicineForm.timing_instruction" class="form-control form-control-sm">
              <option value="after_meal">খাবার পরে (After Meal)</option>
              <option value="before_meal">খাবার আগে (Before Meal)</option>
              <option value="with_meal">খাবারের সাথে (With Meal)</option>
            </select>
          </div>
          <div class="d-flex justify-content-end border-top pt-2">
            <button type="button" class="btn btn-light btn-sm mr-2" @click="showAddMedicineModal = false">বাতিল</button>
            <button type="submit" class="btn btn-success btn-sm px-4 font-weight-bold">যুক্ত করুন</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL: AI PRESCRIPTION READER & SCHEDULE ACTIVATOR -->
    <!-- ========================================================= -->
    <div v-if="showAiRxModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.7); z-index: 1065;">
      <div class="custom-modal bg-white rounded-lg shadow-xl p-4" style="max-width: 620px; width: 94%; max-height: 90vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <div class="d-flex align-items-center">
            <span class="badge badge-primary mr-2 px-2 py-1 font-weight-bold">এআই প্রেসক্রিপশন রিডার</span>
            <h5 class="font-weight-bold text-dark mb-0">স্মার্ট প্রেসক্রিপশন রিডার ও রুটিন সক্রিয়কারী</h5>
          </div>
          <button type="button" class="close text-muted" @click="showAiRxModal = false">&times;</button>
        </div>

        <!-- Strict Medical Disclaimer -->
        <div class="alert alert-warning border-warning p-2 rounded small mb-3">
          <strong>⚠️ সতর্কীকরণ:</strong> এই কৃত্রিম বুদ্ধিমত্তা সহকারী শুধুমাত্র প্রেসক্রিপশন থেকে ওষুধের নাম ও সময় স্বয়ংক্রিয়ভাবে সাজানোর একটি সহায়ক মাধ্যম। এটি চিকিৎসকের বিকল্প নয়। ওষুধ সেবনের পূর্বে মূল প্রেসক্রিপশনটি নিজে যাচাই করুন।
        </div>

        <div v-if="aiExtractedMedicines.length === 0">
          <div class="upload-dropzone p-4 rounded border text-center mb-3 bg-light">
            <input type="file" ref="aiRxInput" class="d-none" accept="image/*" @change="onAiRxFileSelected">
            <div v-if="aiRxPreview" class="mb-2">
              <img :src="aiRxPreview" class="img-thumbnail rounded mb-2" style="max-height: 180px;">
              <div>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" @click="$refs.aiRxInput.click()">ছবি পরিবর্তন করুন</button>
              </div>
            </div>
            <div v-else class="cursor-pointer" @click="$refs.aiRxInput.click()">
              <i class="icofont-camera display-4 text-primary mb-2"></i>
              <h6 class="font-weight-bold text-dark">প্রেসক্রিপশনের স্পষ্ট ছবি তুলুন বা আপলোড করুন</h6>
              <p class="text-muted small mb-0">ক্যামেরার ছবি বা প্রেসক্রিপশনের ফটোকপি সিলেক্ট করুন</p>
            </div>
          </div>

          <div class="text-center mt-3">
            <button 
              class="btn btn-primary font-weight-bold btn-round-full px-4 py-2 shadow-sm"
              :disabled="!aiRxFile || aiRxAnalyzing"
              @click="analyzePrescriptionAi"
            >
              <span v-if="aiRxAnalyzing">
                <span class="spinner-border spinner-border-sm mr-2" role="status"></span>
                AI প্রেসক্রিপশন বিশ্লেষণ করছে...
              </span>
              <span v-else>
                <i class="icofont-magic mr-1"></i> AI দিয়ে বিশ্লেষণ করুন
              </span>
            </button>
          </div>
        </div>

        <!-- Extracted Medicines Table Review -->
        <div v-else>
          <div class="alert alert-success py-2 px-3 small rounded mb-3">
            <strong>✅ প্রেসক্রিপশন পড়া সম্পন্ন হয়েছে!</strong> নিচের ওষুধগুলোর তালিকা দেখে আপনার দৈনিক রুটিনে যুক্ত করতে "রুটিনে যোগ করুন" চাপুন।
          </div>

          <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered">
              <thead class="thead-light">
                <tr style="font-size: 11px;">
                  <th>নির্বাচন</th>
                  <th>ঔষধের নাম</th>
                  <th>ডোজ</th>
                  <th>নিয়ম</th>
                  <th>সময়কাল</th>
                </tr>
              </thead>
              <tbody style="font-size: 12px;">
                <tr v-for="(item, idx) in aiExtractedMedicines" :key="idx">
                  <td class="text-center align-middle">
                    <input type="checkbox" v-model="item.selected">
                  </td>
                  <td>
                    <input type="text" v-model="item.medicine_name" class="form-control form-control-sm font-weight-bold">
                  </td>
                  <td>
                    <input type="text" v-model="item.dosage" class="form-control form-control-sm" style="width: 80px;">
                  </td>
                  <td>
                    <select v-model="item.timing_instruction" class="form-control form-control-sm">
                      <option value="after_meal">খাবার পর</option>
                      <option value="before_meal">খাবার আগে</option>
                      <option value="with_meal">খাবারের সাথে</option>
                    </select>
                  </td>
                  <td>
                    <input type="number" v-model="item.days_duration" class="form-control form-control-sm" style="width: 60px;">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center border-top pt-3">
            <button class="btn btn-sm btn-outline-secondary" @click="aiExtractedMedicines = []">
              <i class="icofont-refresh mr-1"></i> পুনরায় আপলোড
            </button>
            <button class="btn btn-sm btn-success font-weight-bold px-4 py-2" @click="applyAiMedicinesToSchedule">
              <i class="icofont-check mr-1"></i> নির্বাচিত ঔষধগুলো রুটিনে যুক্ত করুন
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Dedicated Prescription Print Modal -->
    <PrescriptionPrintModal 
      :show="showPrintModal" 
      :prescription="activePrintPrescription" 
      @close="showPrintModal = false" 
    />
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import PrescriptionPrintModal from '../../components/PrescriptionPrintModal.vue';

export default {
  name: 'PatientDashboard',
  components: {
    PrescriptionPrintModal,
  },
  data() {
    return {
      showPrintModal: false,
      activePrintPrescription: {},
      user: null,
      activeTab: 'reports',
      activeFamilyMember: null,
      showFamilyDropdown: false,
      familyMembers: [],
      myDoctorsList: [],
      prescriptions: [],
      appointments: [],
      myReportsList: [],
      healthMetricsList: [],
      activeMetricTab: 'blood_sugar',
      liveQueueServingSerial: 1,

      // Lightbox
      showLightbox: false,
      activeReport: null,
      selectedReportCategory: 'all',
      reportSearchQuery: '',

      // Upload Modal
      showUploadModal: false,
      uploadingReport: false,
      selectedFile: null,
      filePreviewUrl: null,
      isImageFile: false,
      uploadForm: {
        report_title: 'Complete Blood Count (CBC)',
        test_type: 'blood_test',
        diagnostic_center: '',
        report_date: new Date().toISOString().split('T')[0],
      },

      // Family Modal
      showFamilyModal: false,
      familyForm: {
        name: '',
        relationship: 'Mother',
        gender: 'Female',
        age_or_dob: '',
        blood_group: '',
        medical_notes: '',
      },

      // Metric Modal
      showMetricModal: false,
      metricForm: {
        metric_type: 'blood_sugar',
        value_primary: '',
        value_secondary: '',
        measured_time: 'খালি পেটে (Fasting)',
        recorded_date: new Date().toISOString().split('T')[0],
      },

      // Diagnostic Modal
      showDiagnosticModal: false,
      diagnosticForm: {
        prescription_id: null,
        doctor_id: null,
        diagnostic_center: 'Popular Diagnostic Center',
        test_names: [],
        collection_address: '',
        preferred_date: new Date().toISOString().split('T')[0],
        contact_phone: '',
      },

      // Smart Human Voice Prescription Player
      showAudioRxModal: false,
      activeAudioRx: null,
      isAudioPlaying: false,
      isAudioLoading: false,
      audioCurrentTime: 0,
      audioDuration: 0,
      audioProgressPercent: 0,
      audioSpeechRate: 1.0,
      htmlAudio: null,

      // Daily Medicine Routine & AI Scanner
      medicineSchedules: [],
      medicineAdherences: [],
      showAddMedicineModal: false,
      newMedicineForm: {
        medicine_name: '',
        dosage: '1+0+1',
        days_duration: 7,
        morning: true,
        noon: false,
        evening: false,
        night: true,
        timing_instruction: 'after_meal',
      },
      showAiRxModal: false,
      aiRxFile: null,
      aiRxPreview: null,
      aiRxAnalyzing: false,
      aiExtractedMedicines: [],

      // Digital Health Passport & PVC Card
      passportToken: '',
      passportProfileForm: {
        blood_group: '',
        emergency_contact_phone: '',
        emergency_medical_notes: '',
      },
    };
  },
  computed: {
    passportPublicUrl() {
      if (!this.passportToken) return '';
      return `${window.location.origin}/emergency-pass/${this.passportToken}`;
    },
    todayAppointment() {
      if (!this.appointments.length) return null;
      const todayStr = new Date().toISOString().split('T')[0];
      return this.appointments.find(a => a.appointment_date === todayStr && a.status !== 'cancelled') || this.appointments[0];
    },
    filteredReports() {
      return this.myReportsList.filter(r => {
        const matchesCategory = this.selectedReportCategory === 'all' || r.test_type === this.selectedReportCategory;
        const s = this.reportSearchQuery.toLowerCase().trim();
        const matchesSearch = !s || (r.report_title && r.report_title.toLowerCase().includes(s));
        return matchesCategory && matchesSearch;
      });
    },
  },
  mounted() {
    const userStr = localStorage.getItem('saas_user');
    if (!userStr) {
      this.$router.push('/login');
      return;
    }
    this.user = JSON.parse(userStr);
    this.fetchPatientData();
    this.fetchPatientReports();
    this.fetchFamilyMembers();
    this.fetchHealthMetrics();
    this.refreshLiveQueue();
    this.fetchMedicineSchedules();
    this.fetchPassportToken();
    if (this.user) {
      this.passportProfileForm.blood_group = this.user.blood_group || '';
      this.passportProfileForm.emergency_contact_phone = this.user.emergency_contact_phone || this.user.phone || '';
      this.passportProfileForm.emergency_medical_notes = this.user.emergency_medical_notes || '';
    }
    this.initSpeechVoices();
    if ('speechSynthesis' in window) {
      window.speechSynthesis.onvoiceschanged = () => {
        this.initSpeechVoices();
      };
    }
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '';
      const cleanDate = dateStr.includes('T') ? dateStr.split('T')[0] : dateStr;
      return cleanDate;
    },

    async fetchPatientData() {
      try {
        const [docRes, rxRes, aptRes] = await Promise.all([
          axios.get('/patient-portal/my-doctors', { params: { user_id: this.user.id } }),
          axios.get('/patient-portal/my-prescriptions', { params: { user_id: this.user.id } }),
          axios.get('/patient-portal/my-appointments', { params: { user_id: this.user.id } }),
        ]);
        this.myDoctorsList = docRes.data || [];
        this.prescriptions = rxRes.data || [];
        this.appointments = aptRes.data.data || aptRes.data || [];
      } catch (err) {
        console.error('Error fetching patient data:', err);
      }
    },

    async fetchPatientReports() {
      try {
        const res = await axios.get('/patient-portal/my-reports', { params: { user_id: this.user.id } });
        if (res.data.success) {
          this.myReportsList = res.data.reports || [];
        }
      } catch (err) {
        console.error('Error fetching reports:', err);
      }
    },

    async fetchFamilyMembers() {
      try {
        const res = await axios.get('/patient-portal/family-members', { params: { user_id: this.user.id } });
        if (res.data.success) {
          this.familyMembers = res.data.family_members || [];
        }
      } catch (err) {
        console.error('Error loading family:', err);
      }
    },

    async fetchHealthMetrics() {
      try {
        const res = await axios.get('/patient-portal/health-metrics', {
          params: { user_id: this.user.id, metric_type: this.activeMetricTab }
        });
        if (res.data.success) {
          this.healthMetricsList = res.data.metrics || [];
        }
      } catch (err) {
        console.error('Error loading health metrics:', err);
      }
    },

    changeMetricType(type) {
      this.activeMetricTab = type;
      this.fetchHealthMetrics();
    },

    async refreshLiveQueue() {
      if (this.todayAppointment?.doctor_id) {
        try {
          const res = await axios.get(`/patient-portal/queue/token/${this.todayAppointment.id}`);
          if (res.data.success) {
            this.liveQueueServingSerial = res.data.current_serving_serial || 1;
          }
        } catch (err) {
          console.error('Error fetching queue status:', err);
        }
      }
    },

    // 📢 BANGLA AUDIO PRESCRIPTION READER (Studio Human Voice MP3)
    formatAudioTime(seconds) {
      if (!seconds || isNaN(seconds)) return '00:00';
      const m = Math.floor(seconds / 60);
      const s = Math.floor(seconds % 60);
      return `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
    },

    formatDosageBangla(dosage) {
      if (!dosage) return 'নিয়ম অনুযায়ী';
      const clean = dosage.toString().replace(/\s+/g, '').toLowerCase();
      const map = {
        '1+0+0': 'সকালে ১টি',
        '0+1+0': 'দুপুরে ১টি',
        '0+0+1': 'রাতে ১টি',
        '1+0+1': 'সকালে ১টি এবং রাতে ১টি',
        '1+1+0': 'সকালে ১টি এবং দুপুরে ১টি',
        '0+1+1': 'দুপুরে ১টি এবং রাতে ১টি',
        '1+1+1': 'সকালে ১টি, দুপুরে ১টি এবং রাতে ১টি',
        '1/2+0+1/2': 'সকালে অর্ধেক এবং রাতে অর্ধেক',
        '1/2+1/2+1/2': 'সকালে অর্ধেক, দুপুরে অর্ধেক এবং রাতে অর্ধেক',
        '1+0+0+1': 'সকালে ১টি ও রাতে শোবার আগে ১টি',
        '2+0+2': 'সকালে ২টি এবং রাতে ২টি',
      };
      return map[clean] || `প্রতিবারে ${dosage}`;
    },

    formatDurationBangla(dur) {
      if (!dur) return 'ডাক্তারের পরামর্শ অনুযায়ী';
      return dur
        .replace(/(\d+)\s*days?/i, '$1 দিন যাবত')
        .replace(/(\d+)\s*weeks?/i, '$1 সপ্তাহ')
        .replace(/(\d+)\s*months?/i, '$1 মাস')
        .replace(/continue/i, 'নিয়মিত চলবে');
    },

    formatInstructionBangla(inst) {
      if (!inst) return 'খাবার খাওয়ার পর সেব্য';
      const lower = inst.toLowerCase();
      if (lower.includes('after') || lower.includes('পর')) return 'খাবার খাওয়ার পর সেব্য';
      if (lower.includes('before') || lower.includes('আগে')) return 'খাবার খাওয়ার আগে খালি পেটে সেব্য';
      if (lower.includes('bedtime') || lower.includes('ঘুমানো')) return 'রাতে ঘুমানোর পূর্বে সেব্য';
      return inst;
    },

    playBanglaAudioRx(rx) {
      this.activeAudioRx = rx;
      this.showAudioRxModal = true;
      this.isAudioLoading = true;
      this.isAudioPlaying = false;
      this.audioCurrentTime = 0;
      this.audioDuration = 0;
      this.audioProgressPercent = 0;

      if (this.htmlAudio) {
        this.htmlAudio.pause();
        this.htmlAudio = null;
      }

      // Load studio quality Human Voice MP3 stream
      const audio = new Audio(`/api/prescriptions/${rx.id}/audio`);
      audio.playbackRate = this.audioSpeechRate || 1.0;
      this.htmlAudio = audio;

      audio.onloadedmetadata = () => {
        this.audioDuration = audio.duration;
        this.isAudioLoading = false;
      };

      audio.ontimeupdate = () => {
        this.audioCurrentTime = audio.currentTime;
        if (this.audioDuration > 0) {
          this.audioProgressPercent = (audio.currentTime / this.audioDuration) * 100;
        }
      };

      audio.onplay = () => {
        this.isAudioPlaying = true;
        this.isAudioLoading = false;
      };

      audio.onpause = () => {
        this.isAudioPlaying = false;
      };

      audio.onended = () => {
        this.isAudioPlaying = false;
        this.audioProgressPercent = 100;
      };

      audio.onerror = (e) => {
        console.error('Audio play error:', e);
        this.isAudioLoading = false;
        this.isAudioPlaying = false;
      };

      audio.play().catch(err => {
        console.warn('Autoplay prevented:', err);
        this.isAudioLoading = false;
      });
    },

    togglePauseAudio() {
      if (!this.htmlAudio) {
        if (this.activeAudioRx) {
          this.playBanglaAudioRx(this.activeAudioRx);
        }
        return;
      }
      if (this.isAudioPlaying) {
        this.htmlAudio.pause();
      } else {
        this.htmlAudio.play();
      }
    },

    restartAudio() {
      if (this.htmlAudio) {
        this.htmlAudio.currentTime = 0;
        this.htmlAudio.play();
      }
    },

    seekAudio(e) {
      if (!this.htmlAudio || !this.audioDuration) return;
      const rect = e.currentTarget.getBoundingClientRect();
      const clickX = e.clientX - rect.left;
      const pct = Math.max(0, Math.min(1, clickX / rect.width));
      this.htmlAudio.currentTime = pct * this.audioDuration;
    },

    changeSpeechRate(rate) {
      this.audioSpeechRate = rate;
      if (this.htmlAudio) {
        this.htmlAudio.playbackRate = rate;
      }
    },

    closeAudioRxModal() {
      if (this.htmlAudio) {
        this.htmlAudio.pause();
        this.htmlAudio = null;
      }
      this.isAudioPlaying = false;
      this.showAudioRxModal = false;
    },

    selectFamilyMember(member) {
      this.activeFamilyMember = member;
      this.showFamilyDropdown = false;
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: member ? `${member.name}-এর প্রোফাইলে সুইচ হয়েছে` : 'প্রধান প্রোফাইলে সুইচ হয়েছে',
        showConfirmButton: false,
        timer: 1500
      });
    },

    openAddFamilyModal() {
      this.showFamilyDropdown = false;
      this.familyForm = {
        name: '',
        relationship: 'Mother',
        gender: 'Female',
        age_or_dob: '',
        blood_group: 'B+',
        medical_notes: '',
      };
      this.showFamilyModal = true;
    },

    async submitFamilyMember() {
      try {
        const res = await axios.post('/patient-portal/family-members', {
          ...this.familyForm,
          user_id: this.user.id,
        });
        if (res.data.success) {
          Swal.fire('সফল!', res.data.message, 'success');
          this.showFamilyModal = false;
          this.fetchFamilyMembers();
        }
      } catch (err) {
        Swal.fire('ত্রুটি', 'সদস্য যুক্ত করতে সমস্যা হয়েছে।', 'error');
      }
    },

    async deleteFamilyMember(id) {
      const confirm = await Swal.fire({
        title: 'সদস্য মুছে ফেলবেন?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'হ্যাঁ',
        cancelButtonText: 'না'
      });
      if (confirm.isConfirmed) {
        await axios.delete(`/patient-portal/family-members/${id}`, { params: { user_id: this.user.id } });
        this.fetchFamilyMembers();
      }
    },

    openAddMetricModal() {
      this.metricForm = {
        metric_type: this.activeMetricTab,
        value_primary: '',
        value_secondary: '',
        measured_time: 'খালি পেটে (Fasting)',
        recorded_date: new Date().toISOString().split('T')[0],
      };
      this.showMetricModal = true;
    },

    async submitHealthMetric() {
      try {
        const res = await axios.post('/patient-portal/health-metrics', {
          ...this.metricForm,
          user_id: this.user.id,
          family_member_id: this.activeFamilyMember?.id || null,
        });
        if (res.data.success) {
          Swal.fire('সংরক্ষিত!', 'স্বাস্থ্য পরিমাপ সফলভাবে সেভ হয়েছে।', 'success');
          this.showMetricModal = false;
          this.fetchHealthMetrics();
        }
      } catch (err) {
        Swal.fire('ত্রুটি', 'তথ্য সেভ করতে সমস্যা হয়েছে।', 'error');
      }
    },

    async deleteMetric(id) {
      await axios.delete(`/patient-portal/health-metrics/${id}`, { params: { user_id: this.user.id } });
      this.fetchHealthMetrics();
    },

    openDiagnosticBookingModal(rx) {
      this.diagnosticForm = {
        prescription_id: rx.id,
        doctor_id: rx.doctor_id,
        diagnostic_center: 'Popular Diagnostic Center',
        test_names: rx.medical_tests || ['Complete Blood Count (CBC)'],
        collection_address: this.user?.address || '',
        preferred_date: new Date().toISOString().split('T')[0],
        contact_phone: this.user?.phone || '',
      };
      this.showDiagnosticModal = true;
    },

    async submitDiagnosticOrder() {
      try {
        const res = await axios.post('/patient-portal/diagnostic-orders', {
          ...this.diagnosticForm,
          user_id: this.user.id,
          total_amount: 1500.00,
          sample_collection_type: 'home_collection',
        });
        if (res.data.success) {
          Swal.fire('বুকিং সম্পন্ন!', res.data.message, 'success');
          this.showDiagnosticModal = false;
        }
      } catch (err) {
        Swal.fire('ত্রুটি', 'বুকিং করতে সমস্যা হয়েছে।', 'error');
      }
    },

    orderMedicinesOnline(rx) {
      const total = rx.estimated_total_cost ? Math.round(rx.estimated_total_cost * 0.9) : 0;
      Swal.fire({
        title: '🛍️ প্রেসক্রিপশনের ওষুধ হোম ডেলিভারি',
        html: `
          <div style="text-align: left; font-size: 13px; line-height: 1.6; color: #334155;">
            <p style="margin-bottom: 4px;"><strong>প্রেসক্রিপশন নম্বর:</strong> ${rx.prescription_number}</p>
            <p style="margin-bottom: 4px;"><strong>ডাক্তার:</strong> ${rx.doctor?.name || ''}</p>
            <p style="margin-bottom: 4px;"><strong>মোট ওষুধ:</strong> ${rx.medicines?.length || 0} টি পদ</p>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 8px 12px; border-radius: 8px; margin: 8px 0;">
              <strong style="color: #16a34a; font-size: 15px;">১০% ছাড়ে সর্বমোট বিল: ৳${total}</strong>
              <small style="display: block; color: #64748b;">(ক্যাশ অন ডেলিভারি / ওষুধ হাতে পেয়ে মূল্য পরিশোধ)</small>
            </div>
            <label style="display: block; font-weight: bold; margin-top: 10px; margin-bottom: 4px; color: #0f172a;">মোবাইল নম্বর:</label>
            <input id="swal-deliv-phone" class="swal2-input" style="margin: 0; width: 100%; font-size: 13px;" value="${this.user?.phone || ''}" placeholder="017xxxxxxxx">
            <label style="display: block; font-weight: bold; margin-top: 8px; margin-bottom: 4px; color: #0f172a;">হোম ডেলিভারির ঠিকানা:</label>
            <textarea id="swal-deliv-address" class="swal2-textarea" style="margin: 0; width: 100%; font-size: 13px; height: 70px;" placeholder="বাসা নম্বর, রোড, এলাকা, জেলা">${this.user?.address || ''}</textarea>
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'অর্ডার নিশ্চিত করুন (Confirm Order)',
        cancelButtonText: 'বাতিল',
        confirmButtonColor: '#16a34a',
        preConfirm: () => {
          const phone = document.getElementById('swal-deliv-phone').value;
          const addr = document.getElementById('swal-deliv-address').value;
          if (!phone || !addr) {
            Swal.showValidationMessage('অনুগ্রহ করে মোবাইল নম্বর ও পূর্ণাঙ্গ ঠিকানা প্রদান করুন');
            return false;
          }
          return { phone, address: addr };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            icon: 'success',
            title: 'অর্ডার গ্রহণ করা হয়েছে!',
            text: 'আমাদের পার্টনার ফার্মেসি থেকে অতিসত্বর যোগাযোগ করে আপনার দেওয়া ঠিকানায় জেনুইন ওষুধ হোম ডেলিভারি পৌঁছে দেওয়া হবে।',
            confirmButtonColor: '#16a34a',
          });
        }
      });
    },

    // Report Upload & Lightbox
    viewReport(r) { this.activeReport = r; this.showLightbox = true; },
    openUploadReportModal() { this.showUploadModal = true; },
    closeUploadModal() { this.showUploadModal = false; this.selectedFile = null; },
    triggerFileInput() { this.$refs.reportFileInput.click(); },
    onReportFileSelected(e) {
      const f = e.target.files[0];
      if (!f) return;
      this.selectedFile = f;
      this.isImageFile = f.type.startsWith('image/');
      this.filePreviewUrl = this.isImageFile ? URL.createObjectURL(f) : null;
    },
    async submitReportUpload() {
      if (!this.selectedFile) return;
      this.uploadingReport = true;
      const fd = new FormData();
      fd.append('user_id', this.user.id);
      fd.append('report_file', this.selectedFile);
      fd.append('report_title', this.uploadForm.report_title);
      fd.append('test_type', this.uploadForm.test_type);
      fd.append('diagnostic_center', this.uploadForm.diagnostic_center || '');
      fd.append('report_date', this.uploadForm.report_date);
      try {
        const res = await axios.post('/patient-portal/reports/upload', fd);
        if (res.data.success) {
          Swal.fire('আপলোড সম্পন্ন!', res.data.message, 'success');
          this.closeUploadModal();
          this.fetchPatientReports();
        }
      } catch (err) {
        Swal.fire('Error', 'আপলোড করতে সমস্যা হয়েছে।', 'error');
      } finally {
        this.uploadingReport = false;
      }
    },
    async confirmDeleteReport(r) {
      const res = await Swal.fire({ title: 'রিপোর্ট মুছে ফেলবেন?', showCancelButton: true });
      if (res.isConfirmed) {
        await axios.delete(`/patient-portal/reports/${r.id}`, { params: { user_id: this.user.id } });
        this.fetchPatientReports();
      }
    },
    async uploadPatientAvatar(e) {
      const f = e.target.files[0];
      if (!f) return;
      const fd = new FormData();
      fd.append('avatar', f);
      fd.append('user_id', this.user.id);
      const res = await axios.post('/profile/upload-avatar', fd);
      if (res.data.success) {
        this.user.avatar = res.data.avatar_url.replace(/^\//, '');
        localStorage.setItem('saas_user', JSON.stringify(this.user));
      }
    },
    // ==========================================
    // MEDICINE SCHEDULES & ADHERENCE METHODS
    // ==========================================
    async fetchMedicineSchedules() {
      if (!this.user) return;
      try {
        const res = await axios.get('/medicines/schedules', {
          params: {
            user_id: this.user.id,
            family_member_id: this.activeFamilyMember ? this.activeFamilyMember.id : null,
          }
        });
        if (res.data.success) {
          this.medicineSchedules = res.data.data || [];
          this.medicineAdherences = res.data.adherence_today || [];
        }
      } catch (err) {
        console.error('Error fetching medicine schedules:', err);
      }
    },
    getSlotMedicines(slot) {
      return this.medicineSchedules.filter(m => m[slot] === true || m[slot] === 1 || m[slot] === '1');
    },
    formatTimingInstruction(inst) {
      if (inst === 'after_meal') return 'খাবার পর';
      if (inst === 'before_meal') return 'খাবার পূর্বে';
      if (inst === 'with_meal') return 'খাবারের সাথে';
      return inst || 'নিয়ম অনুযায়ী';
    },
    getAdherenceBadge(scheduleId) {
      const adh = this.medicineAdherences.find(a => a.medicine_schedule_id === scheduleId);
      if (adh && adh.status === 'taken') {
        return { text: 'খাওয়া হয়েছে ✅', class: 'badge-success' };
      }
      if (adh && adh.status === 'skipped') {
        return { text: 'বাদ দেওয়া হয়েছে ❌', class: 'badge-secondary' };
      }
      return { text: 'এখনো খাওয়া হয়নি ⏳', class: 'badge-warning text-dark' };
    },
    async logAdherence(scheduleId, status) {
      try {
        const res = await axios.post(`/medicines/schedules/${scheduleId}/adherence`, {
          user_id: this.user.id,
          family_member_id: this.activeFamilyMember ? this.activeFamilyMember.id : null,
          status: status,
          logged_date: new Date().toISOString().split('T')[0],
        });
        if (res.data.success) {
          const idx = this.medicineAdherences.findIndex(a => a.medicine_schedule_id === scheduleId);
          if (idx >= 0) {
            this.medicineAdherences[idx] = res.data.data;
          } else {
            this.medicineAdherences.push(res.data.data);
          }
          Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            icon: 'success',
            title: status === 'taken' ? 'ঔষধ খাওয়ার রেকর্ড সেভ হয়েছে!' : 'ঔষধ বাদ দেওয়া হয়েছে'
          });
        }
      } catch (err) {
        Swal.fire('Error', 'রেকর্ড সংরক্ষণ করা যায়নি।', 'error');
      }
    },
    async deleteMedicineSchedule(id) {
      const result = await Swal.fire({
        title: 'ঔষধটি মুছে ফেলবেন?',
        text: 'এটি আপনার দৈনিক তালিকা থেকে মুছে যাবে।',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e12454',
        confirmButtonText: 'হ্যাঁ, মুছুন',
        cancelButtonText: 'বাতিল'
      });
      if (result.isConfirmed) {
        try {
          await axios.delete(`/medicines/schedules/${id}`);
          this.medicineSchedules = this.medicineSchedules.filter(m => m.id !== id);
          Swal.fire('মুছে ফেলা হয়েছে!', 'ঔষধটি সরানো হয়েছে।', 'success');
        } catch (err) {
          Swal.fire('Error', 'মুছে ফেলা যায়নি।', 'error');
        }
      }
    },
    async saveManualMedicine() {
      try {
        const payload = {
          user_id: this.user.id,
          family_member_id: this.activeFamilyMember ? this.activeFamilyMember.id : null,
          medicine_name: this.newMedicineForm.medicine_name,
          dosage: this.newMedicineForm.dosage,
          days_duration: this.newMedicineForm.days_duration,
          morning: this.newMedicineForm.morning ? 1 : 0,
          noon: this.newMedicineForm.noon ? 1 : 0,
          evening: this.newMedicineForm.evening ? 1 : 0,
          night: this.newMedicineForm.night ? 1 : 0,
          timing_instruction: this.newMedicineForm.timing_instruction,
          start_date: new Date().toISOString().split('T')[0],
        };
        const res = await axios.post('/medicines/schedules', payload);
        if (res.data.success) {
          this.medicineSchedules.unshift(res.data.data);
          this.showAddMedicineModal = false;
          this.newMedicineForm.medicine_name = '';
          Swal.fire('যুক্ত হয়েছে!', 'নতুন ওষুধ আপনার রুটিনে যোগ হয়েছে।', 'success');
        }
      } catch (err) {
        Swal.fire('Error', 'সংরক্ষণ করা যায়নি।', 'error');
      }
    },

    // ==========================================
    // AI PRESCRIPTION SCANNER METHODS
    // ==========================================
    openAiRxModal() {
      this.aiRxFile = null;
      this.aiRxPreview = null;
      this.aiExtractedMedicines = [];
      this.showAiRxModal = true;
    },
    onAiRxFileSelected(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.aiRxFile = file;
      this.aiRxPreview = URL.createObjectURL(file);
    },
    async analyzePrescriptionAi() {
      if (!this.aiRxFile) return;
      this.aiRxAnalyzing = true;
      try {
        const fd = new FormData();
        fd.append('prescription_image', this.aiRxFile);
        const res = await axios.post('/medicines/ai-extract', fd);
        if (res.data.success) {
          this.aiExtractedMedicines = (res.data.medicines || []).map(m => ({
            ...m,
            selected: true,
            timing_instruction: m.instruction === 'খাবার পর' ? 'after_meal' : (m.instruction === 'খাবার পূর্বে' ? 'before_meal' : 'after_meal'),
            days_duration: parseInt(m.duration) || 7,
          }));
          if (this.aiExtractedMedicines.length === 0) {
            Swal.fire('তথ্য পাওয়া যায়নি', 'প্রেসক্রিপশনটি পরিষ্কার নয় বা ওষুধ পাওয়া যায়নি। নিজে তথ্য যুক্ত করতে পারেন।', 'info');
          }
        }
      } catch (err) {
        Swal.fire('বিশ্লেষণ ব্যর্থ', 'AI প্রেসক্রিপশন পড়তে পারেনি। অনুগ্রহ করে স্পষ্ট ছবি তুলুন।', 'error');
      } finally {
        this.aiRxAnalyzing = false;
      }
    },
    async applyAiMedicinesToSchedule() {
      const selected = this.aiExtractedMedicines.filter(m => m.selected);
      if (selected.length === 0) {
        Swal.fire('সতর্কতা', 'কমপক্ষে একটি ওষুধ নির্বাচন করুন।', 'warning');
        return;
      }
      try {
        for (const item of selected) {
          const parts = (item.dosage || '1+0+1').split(/[\+-]/);
          const morning = parts[0] && parseInt(parts[0]) > 0 ? 1 : 0;
          const noon = parts[1] && parseInt(parts[1]) > 0 ? 1 : 0;
          const night = (parts[2] && parseInt(parts[2]) > 0) || parts.length < 3 ? 1 : 0;

          await axios.post('/medicines/schedules', {
            user_id: this.user.id,
            family_member_id: this.activeFamilyMember ? this.activeFamilyMember.id : null,
            medicine_name: item.medicine_name,
            dosage: item.dosage || '1+0+1',
            days_duration: item.days_duration || 7,
            morning: morning,
            noon: noon,
            evening: 0,
            night: night,
            timing_instruction: item.timing_instruction || 'after_meal',
            start_date: new Date().toISOString().split('T')[0],
          });
        }
        Swal.fire('সফল!', 'নির্বাচিত ওষুধগুলো আপনার রুটিনে যুক্ত হয়েছে।', 'success');
        this.showAiRxModal = false;
        this.fetchMedicineSchedules();
      } catch (err) {
        Swal.fire('Error', 'কিছু ওষুধ যুক্ত করা যায়নি।', 'error');
      }
    },

    // ==========================================
    // DIGITAL HEALTH PASSPORT METHODS
    // ==========================================
    async fetchPassportToken() {
      if (!this.user) return;
      try {
        const res = await axios.get(`/health-passport/token/${this.user.id}`);
        if (res.data.success && res.data.token) {
          this.passportToken = res.data.token;
        }
      } catch (err) {
        console.error('Error fetching passport token:', err);
      }
    },
    async generatePassportToken() {
      if (!this.user) return;
      try {
        const res = await axios.post(`/health-passport/token/${this.user.id}`);
        if (res.data.success) {
          this.passportToken = res.data.token;
          Swal.fire('সফল!', 'নতুন কিউআর কোড টোকেন জেনারেট হয়েছে।', 'success');
        }
      } catch (err) {
        Swal.fire('Error', 'টোকেন তৈরি করা যায়নি।', 'error');
      }
    },
    async saveEmergencyProfile() {
      if (!this.user) return;
      try {
        const res = await axios.post('/profile/emergency-update', {
          user_id: this.user.id,
          blood_group: this.passportProfileForm.blood_group,
          emergency_contact_phone: this.passportProfileForm.emergency_contact_phone,
          emergency_medical_notes: this.passportProfileForm.emergency_medical_notes,
        });
        if (res.data.success) {
          this.user.blood_group = this.passportProfileForm.blood_group;
          this.user.emergency_contact_phone = this.passportProfileForm.emergency_contact_phone;
          this.user.emergency_medical_notes = this.passportProfileForm.emergency_medical_notes;
          localStorage.setItem('saas_user', JSON.stringify(this.user));
          Swal.fire('সংরক্ষিত!', 'আপনার ইমার্জেন্সি হেলথ প্রোফাইল আপডেট করা হয়েছে।', 'success');
        }
      } catch (err) {
        Swal.fire('Error', 'আপডেট সেভ করা যায়নি।', 'error');
      }
    },
    printEmergencyCard() {
      window.print();
    },
    printPrescription(rx) {
      this.activePrintPrescription = rx;
      this.showPrintModal = true;
    },
    logout() {
      localStorage.clear();
      this.$router.push('/login');
    },
  },
};
</script>

<style scoped>
.cursor-pointer { cursor: pointer; }
.metric-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.metric-card:hover { transform: translateY(-2px); }
.metric-icon-box {
  width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
}
.bg-primary-light { background-color: #e0e7ff; }
.bg-gradient-soft { background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%); }
.filter-chips .chip-btn {
  background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 20px; cursor: pointer;
}
.filter-chips .chip-btn.active {
  background: #e12454; color: #ffffff; border-color: #e12454;
}
.report-card { transition: transform 0.2s ease; }
.report-preview-box { height: 160px; overflow: hidden; background-color: #1e293b; }
.report-thumb-img { height: 100%; object-fit: cover; }
.btn-round-full { border-radius: 50px; }
.btn-main-2 { background: #223a66; color: #fff; }

/* Sound wave animation for audio player */
.sound-wave {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  height: 20px;
}
.sound-wave span {
  display: inline-block;
  width: 4px;
  background: #e12454;
  border-radius: 2px;
  animation: sound-bars 1s infinite ease-in-out alternate;
}
.sound-wave span:nth-child(1) { height: 6px; animation-delay: 0.1s; }
.sound-wave span:nth-child(2) { height: 16px; animation-delay: 0.3s; }
.sound-wave span:nth-child(3) { height: 10px; animation-delay: 0.2s; }
.sound-wave span:nth-child(4) { height: 18px; animation-delay: 0.4s; }
.sound-wave span:nth-child(5) { height: 8px; animation-delay: 0.25s; }

@keyframes sound-bars {
  0% { height: 4px; }
  100% { height: 20px; }
}
</style>
