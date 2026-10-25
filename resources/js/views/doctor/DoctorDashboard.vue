<template>
  <div class="doctor-dashboard-page bg-slate min-vh-100 pb-5" style="max-width: 100%; overflow-x: hidden;">
    <!-- Doctor Top Portal Header Bar -->
    <div class="portal-top-bar bg-white border-bottom py-2 shadow-sm mb-4">
      <div class="container d-flex justify-content-between align-items-center flex-wrap" style="max-width: 1200px; gap: 8px;">
        <div class="d-flex align-items-center flex-shrink-0" style="gap: 8px;">
          <div class="portal-badge-icon bg-danger-soft text-danger rounded-circle d-flex align-items-center justify-content-center shrink-0" style="width: 32px; height: 32px;">
            <i class="icofont-stethoscope font-weight-bold" style="font-size: 16px;"></i>
          </div>
          <div>
            <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 14px;">Doctor Control Portal</h6>
            <small class="text-muted d-none d-sm-inline" style="font-size: 11px;">Chamber Management & Smart Telemedicine</small>
          </div>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
          <router-link to="/doctors" class="btn btn-sm btn-outline-secondary py-1 px-2.5 d-inline-flex align-items-center rounded-pill" target="_blank" style="font-size: 12px;">
            <i class="icofont-eye mr-1 text-primary"></i> Public View
          </router-link>

          <span class="badge badge-success-soft text-success px-2 py-1 font-weight-bold d-none d-md-inline-flex align-items-center rounded-pill" style="font-size: 11px;">
            <span class="live-dot-green mr-1"></span> Live in Chamber
          </span>

          <button class="btn btn-sm btn-danger py-1 px-2.5 font-weight-bold d-inline-flex align-items-center rounded-pill shadow-sm" @click="logout" style="font-size: 12px;">
            <i class="icofont-logout mr-1"></i> Logout
          </button>
        </div>
      </div>
    </div>

    <div class="container px-3 px-md-4" style="max-width: 1200px;">
      <!-- Modern Light Medical Doctor Profile Banner -->
      <div class="doctor-hero-banner bg-white mb-4 p-3 p-md-4 rounded-20 shadow-sm position-relative overflow-hidden border">
        <div class="row align-items-center position-relative" style="z-index: 2;">
          <div class="col-lg-8 d-flex align-items-center flex-wrap flex-sm-nowrap">
            <!-- Doctor Avatar with Clean Border & Camera Trigger -->
            <div class="doctor-avatar-wrapper position-relative mr-3 mb-2 mb-sm-0 flex-shrink-0" style="width: 76px; height: 76px;">
              <img 
                :src="getDoctorAvatar(doctor)" 
                @error="onAvatarError($event, doctor)"
                alt="Doctor Avatar" 
                class="rounded-circle shadow-sm doctor-avatar-img" 
                style="width: 76px; height: 76px; object-fit: cover; border: 3px solid #f1f5f9; background: #f8fafc;"
              >
              <label 
                class="avatar-camera-btn rounded-circle position-absolute d-flex align-items-center justify-content-center cursor-pointer shadow-sm" 
                title="Change Doctor Profile Photo"
                style="bottom: 0; right: 0; width: 26px; height: 26px; background-color: #e12454; border: 2px solid #ffffff; z-index: 5;"
              >
                <i class="icofont-camera text-white" style="font-size: 11px;"></i>
                <input type="file" class="d-none" accept="image/*" @change="uploadDoctorAvatar">
              </label>
            </div>

            <!-- Doctor Bio & Metadata -->
            <div>
              <div class="d-flex align-items-center flex-wrap mb-1">
                <h3 class="font-weight-bold mb-0 text-navy mr-2" style="font-size: 20px; letter-spacing: -0.3px;">
                  {{ doctor?.name || 'Dr. Specialist' }}
                </h3>
                <span class="badge badge-specialty px-2 py-1 font-weight-bold mt-1 mt-sm-0" style="font-size: 11px;">
                  {{ doctor?.specialization || 'Medical Specialist' }}
                </span>

                <!-- Membership Plan Badge -->
                <span v-if="doctor?.membership_plan === 'vip'" class="badge text-white ml-2 px-2.5 py-1 font-weight-bold rounded-pill shadow-xs" style="background: linear-gradient(135deg, #7c3aed, #db2777); font-size: 11px;">
                  <i class="icofont-diamond mr-1"></i> VIP ELITE
                </span>
                <span v-else-if="doctor?.membership_plan === 'pro'" class="badge text-dark ml-2 px-2.5 py-1 font-weight-bold rounded-pill shadow-xs" style="background: linear-gradient(135deg, #fef08a, #f59e0b); font-size: 11px;">
                  <i class="icofont-star-shape mr-1"></i> PRO SPECIALIST
                </span>
                <span v-else class="badge badge-light border text-secondary ml-2 px-2 py-1 font-weight-bold rounded-pill" style="font-size: 11px;">
                  Free Plan
                </span>
              </div>
              <p class="text-muted small mb-1 d-flex align-items-center flex-wrap">
                <span class="mr-2"><i class="icofont-location-pin text-danger mr-1"></i> {{ doctor?.chamber_address || 'Chamber Address not set' }}</span>
                <span v-if="doctor?.bmdc_number" class="badge badge-light border text-secondary px-2 py-0 ml-1" style="font-size: 10px;">BMDC: {{ doctor.bmdc_number }}</span>
              </p>
              <div class="small text-secondary d-flex align-items-center flex-wrap" style="gap: 8px;">
                <span>{{ doctor?.qualifications }}</span>
                <span>&bull;</span>
                <span>Chamber Fee: <strong class="text-success font-weight-bold">৳{{ doctor?.consultation_fee || 0 }}</strong></span>
                <span v-if="doctor?.free_consultation_enabled" class="badge badge-emerald-soft text-emerald px-2 py-0 font-weight-bold rounded-pill">
                  <i class="icofont-gift mr-1"></i> Free Clinic Active
                </span>
              </div>
            </div>
          </div>

          <!-- Quick Action Buttons -->
          <div class="col-lg-4 text-lg-right mt-3 mt-lg-0 d-flex flex-wrap justify-content-lg-end" style="gap: 8px;">
            <button 
              type="button"
              @click="openUpgradeModal(doctor?.membership_plan === 'pro' ? 'vip' : 'pro')" 
              class="btn btn-warning font-weight-bold px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center text-dark"
              style="background: linear-gradient(135deg, #fef08a, #f59e0b); border: none;"
              title="প্রো বা ভিআইপি মেম্বারশিপে আপগ্রেড করুন"
            >
              <i class="icofont-crown mr-1"></i> {{ doctor?.is_pro ? '👑 মেম্বারশিপ স্ট্যাটাস' : '👑 আপগ্রেড টু প্রো' }}
            </button>
            <router-link 
              :to="'/dr/' + (doctor?.slug || doctor?.id)" 
              target="_blank" 
              class="btn btn-success font-weight-bold px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center text-white"
              style="background: linear-gradient(135deg, #10b981, #059669); border: none;"
              title="ডাক্তারের প্রিমিয়াম পার্সোনাল ওয়েবসাইট"
            >
              <i class="icofont-globe mr-1"></i> পোর্টফোলিও &rarr;
            </router-link>
            <button class="btn btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center" @click="activeTab = 'schedule'">
              <i class="icofont-ui-settings mr-1"></i> Manage Profile
            </button>
            <router-link :to="'/doctor/' + (doctor?.slug || doctor?.id)" target="_blank" class="btn btn-outline-secondary font-weight-bold px-3 py-2 rounded-pill d-inline-flex align-items-center">
              <i class="icofont-share-alt mr-1 text-primary"></i> View
            </router-link>
          </div>
        </div>

        <!-- Personal Portfolio Shareable Banner -->
        <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
          <div class="d-flex align-items-center flex-wrap" style="gap: 8px; font-size: 13px; max-width: 100%;">
            <span class="badge badge-success px-2 py-1 font-weight-bold rounded-pill shrink-0">
              <i class="icofont-link mr-1"></i> আপনার শেয়ারেবল ওয়েবসাইট লিংক:
            </span>
            <span class="text-primary font-weight-bold bg-light px-2 py-1 rounded border text-break" style="max-width: 100%; word-break: break-all; overflow-wrap: anywhere;">
              {{ portfolioShareUrl }}
            </span>
          </div>
          <div class="d-flex align-items-center" style="gap: 8px;">
            <button type="button" class="btn btn-sm btn-dark font-weight-bold px-3 py-1 rounded-pill" @click="copyPortfolioShareUrl">
              <i class="icofont-ui-copy mr-1"></i> {{ copiedPortfolio ? 'কপি হয়েছে!' : 'লিংক কপি করুন' }}
            </button>
            <router-link :to="'/dr/' + (doctor?.slug || doctor?.id)" target="_blank" class="btn btn-sm btn-outline-success font-weight-bold px-3 py-1 rounded-pill">
              <i class="icofont-external-link mr-1"></i> ওপেন করুন
            </router-link>
          </div>
        </div>
      </div>

      <!-- Pending Approval Warning Banner -->
      <div v-if="doctor && !doctor.is_approved" class="alert alert-warning border-0 shadow-sm rounded-16 p-3 mb-4 d-flex align-items-center flex-wrap" style="background: #fff8e6; border-left: 5px solid #f59e0b !important;">
        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center mr-3 mb-2 mb-sm-0 flex-shrink-0" style="width: 44px; height: 44px; font-size: 22px;">
          <i class="icofont-warning-alt"></i>
        </div>
        <div class="flex-grow-1 mr-3">
          <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 15px;">
            ⚠️ আপনার ডাক্তার প্রোফাইলটি বর্তমানে অ্যাডমিনের অনুমোদনের অপেক্ষায় রয়েছে (Pending Approval)
          </h6>
          <p class="mb-0 text-muted small">
            অ্যাডমিন আপনার বিএমডিসি (BMDC) রেজিস্ট্রেশন ও চেম্বার তথ্য যাচাই করার পর অনুমোদন করবেন। অনুমোদনের পর সাধারণ রোগীরা সরাসরি আপনার চেম্বারে অনলাইন সিরিয়াল বুকিং দিতে পারবেন।
          </p>
        </div>
        <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold rounded-pill mt-2 mt-sm-0">
          <i class="icofont-clock-time mr-1"></i> ভেরিফিকেশন চলমান
        </span>
      </div>

      <!-- Modern KPI Metrics Row -->
      <div class="row mb-4">
        <!-- Metric 1: Total Patients -->
        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
          <div class="card border-0 shadow-sm rounded-16 p-3 bg-white h-100 kpi-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 11px; letter-spacing: 0.5px;">All Patients</span>
                <h3 class="font-weight-bold mb-0 text-dark mt-1" style="font-size: 24px;">{{ stats.total_appointments || 0 }}</h3>
                <small class="text-muted" style="font-size: 11px;">Total registered</small>
              </div>
              <div class="kpi-icon-box bg-blue-soft text-primary rounded-14 d-flex align-items-center justify-content-center">
                <i class="icofont-users-alt-2" style="font-size: 24px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Metric 2: Today's Appointments -->
        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
          <div class="card border-0 shadow-sm rounded-16 p-3 bg-white h-100 kpi-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 11px; letter-spacing: 0.5px;">Today's Queue</span>
                <h3 class="font-weight-bold mb-0 text-danger mt-1" style="font-size: 24px;">{{ stats.today_appointments || 0 }}</h3>
                <small class="text-danger font-weight-bold" style="font-size: 11px;">Active appointments</small>
              </div>
              <div class="kpi-icon-box bg-rose-soft text-danger rounded-14 d-flex align-items-center justify-content-center">
                <i class="icofont-clock-time" style="font-size: 24px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Metric 3: Digital Prescriptions -->
        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
          <div class="card border-0 shadow-sm rounded-16 p-3 bg-white h-100 kpi-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 11px; letter-spacing: 0.5px;">Prescriptions</span>
                <h3 class="font-weight-bold mb-0 text-amber mt-1" style="font-size: 24px;">{{ stats.total_prescriptions || 0 }}</h3>
                <small class="text-muted" style="font-size: 11px;">E-Prescriptions issued</small>
              </div>
              <div class="kpi-icon-box bg-amber-soft text-amber rounded-14 d-flex align-items-center justify-content-center">
                <i class="icofont-prescription" style="font-size: 24px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Metric 4: Net Revenue -->
        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
          <div class="card border-0 shadow-sm rounded-16 p-3 bg-white h-100 kpi-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 11px; letter-spacing: 0.5px;">Net Earnings (90%)</span>
                <h3 class="font-weight-bold mb-0 text-emerald mt-1" style="font-size: 24px;">৳{{ stats.total_earnings || 0 }}</h3>
                <small class="text-success" style="font-size: 11px;">Consultation fees</small>
              </div>
              <div class="kpi-icon-box bg-emerald-soft text-emerald rounded-14 d-flex align-items-center justify-content-center">
                <i class="icofont-money-bag" style="font-size: 24px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Doctor Friendly Interactive Action & Module Cards (5-Grid: Responsive on mobile & desktop) -->
      <div class="row g-2 mb-3">
        <!-- Card 1: Appointments / Patient Roster -->
        <div class="col-6 col-md-4 col-lg mb-2 mb-lg-0">
          <div 
            class="doctor-nav-card p-3 rounded-16 cursor-pointer h-100 position-relative d-flex flex-column justify-content-between"
            :class="{ 'active': activeTab === 'appointments' }"
            @click="activeTab = 'appointments'"
          >
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div 
                class="nav-card-icon rounded-12 d-flex align-items-center justify-content-center"
                :class="activeTab === 'appointments' ? 'bg-danger text-white shadow-sm' : 'bg-danger-soft text-danger'"
              >
                <i class="icofont-calendar" style="font-size: 20px;"></i>
              </div>
              <span 
                class="badge rounded-pill font-weight-bold px-2 py-1"
                :class="activeTab === 'appointments' ? 'badge-danger' : 'badge-light border text-dark'"
                style="font-size: 11px;"
              >
                {{ appointments.length }} সিরিয়াল
              </span>
            </div>
            <div>
              <h6 class="font-weight-bold mb-0 nav-card-title text-dark" style="font-size: 14px;">রোগী ও সিরিয়াল</h6>
              <small class="text-muted d-block" style="font-size: 11px;">Patient Appointments</small>
            </div>
          </div>
        </div>

        <!-- Card 2: Chambers Management -->
        <div class="col-6 col-md-4 col-lg mb-2 mb-lg-0">
          <div 
            class="doctor-nav-card p-3 rounded-16 cursor-pointer h-100 position-relative d-flex flex-column justify-content-between"
            :class="{ 'active': activeTab === 'chambers' }"
            @click="activeTab = 'chambers'; fetchChambers()"
          >
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div 
                class="nav-card-icon rounded-12 d-flex align-items-center justify-content-center"
                :class="activeTab === 'chambers' ? 'bg-primary text-white shadow-sm' : 'bg-blue-soft text-primary'"
              >
                <i class="icofont-hospital" style="font-size: 20px;"></i>
              </div>
              <span 
                class="badge rounded-pill font-weight-bold px-2 py-1"
                :class="activeTab === 'chambers' ? 'badge-primary' : 'badge-light border text-dark'"
                style="font-size: 11px;"
              >
                {{ chambersList.length }} চেম্বার
              </span>
            </div>
            <div>
              <h6 class="font-weight-bold mb-0 nav-card-title text-dark" style="font-size: 14px;">চেম্বারসমূহ</h6>
              <small class="text-muted d-block" style="font-size: 11px;">Chamber Management</small>
            </div>
          </div>
        </div>

        <!-- Card 3: Schedule & Consultation Fee -->
        <div class="col-6 col-md-4 col-lg mb-2 mb-lg-0">
          <div 
            class="doctor-nav-card p-3 rounded-16 cursor-pointer h-100 position-relative d-flex flex-column justify-content-between"
            :class="{ 'active': activeTab === 'schedule' }"
            @click="activeTab = 'schedule'"
          >
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div 
                class="nav-card-icon rounded-12 d-flex align-items-center justify-content-center"
                :class="activeTab === 'schedule' ? 'bg-success text-white shadow-sm' : 'bg-emerald-soft text-emerald'"
              >
                <i class="icofont-ui-calendar" style="font-size: 20px;"></i>
              </div>
              <span 
                class="badge rounded-pill font-weight-bold px-2 py-1"
                :class="activeTab === 'schedule' ? 'badge-success' : 'badge-light border text-dark'"
                style="font-size: 11px;"
              >
                সেটিংস
              </span>
            </div>
            <div>
              <h6 class="font-weight-bold mb-0 nav-card-title text-dark" style="font-size: 14px;">সময়সূচি ও ফি</h6>
              <small class="text-muted d-block" style="font-size: 11px;">Schedule & Chamber Fee</small>
            </div>
          </div>
        </div>

        <!-- Card 4: Staff & Assistants -->
        <div class="col-6 col-md-4 col-lg mb-2 mb-lg-0">
          <div 
            class="doctor-nav-card p-3 rounded-16 cursor-pointer h-100 position-relative d-flex flex-column justify-content-between"
            :class="{ 'active': activeTab === 'assistants' }"
            @click="activeTab = 'assistants'; fetchAssistants()"
          >
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div 
                class="nav-card-icon rounded-12 d-flex align-items-center justify-content-center"
                :class="activeTab === 'assistants' ? 'bg-purple text-white shadow-sm' : 'bg-purple-soft text-purple'"
              >
                <i class="icofont-users-alt-5" style="font-size: 20px;"></i>
              </div>
              <span 
                class="badge rounded-pill font-weight-bold px-2 py-1"
                :class="activeTab === 'assistants' ? 'badge-dark' : 'badge-light border text-dark'"
                style="font-size: 11px;"
              >
                {{ assistantsList.length }} স্টাফ
              </span>
            </div>
            <div>
              <h6 class="font-weight-bold mb-0 nav-card-title text-dark" style="font-size: 14px;">সহকারী / স্টাফ</h6>
              <small class="text-muted d-block" style="font-size: 11px;">Chamber Assistants</small>
            </div>
          </div>
        </div>

        <!-- Card 5: Personal Portfolio & Short Videos -->
        <div class="col-12 col-md-4 col-lg mb-2 mb-lg-0">
          <div 
            class="doctor-nav-card p-3 rounded-16 cursor-pointer h-100 position-relative d-flex flex-column justify-content-between"
            :class="{ 'active': activeTab === 'portfolio' }"
            @click="activeTab = 'portfolio'; fetchPortfolio()"
          >
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div 
                class="nav-card-icon rounded-12 d-flex align-items-center justify-content-center"
                :class="activeTab === 'portfolio' ? 'bg-success text-white shadow-sm' : 'bg-emerald-soft text-emerald'"
              >
                <i class="icofont-web" style="font-size: 20px;"></i>
              </div>
              <span 
                class="badge rounded-pill font-weight-bold px-2 py-1"
                :class="activeTab === 'portfolio' ? 'badge-success' : 'badge-light border text-dark'"
                style="font-size: 11px;"
              >
                {{ portfolioForm.short_videos?.length || 0 }} ভিডিও
              </span>
            </div>
            <div>
              <h6 class="font-weight-bold mb-0 nav-card-title text-dark" style="font-size: 14px;">পোর্টফোলিও ও ভিডিও</h6>
              <small class="text-muted d-block" style="font-size: 11px;">Website & Reels Builder</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Doctor Tabs Container -->
      <div class="card border-0 shadow-sm rounded-20 overflow-hidden bg-white">
        <!-- Module Header & Controls Bar -->
        <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
          <div class="d-flex align-items-center">
            <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f1f5f9;">
              <i v-if="activeTab === 'appointments'" class="icofont-calendar text-danger font-weight-bold"></i>
              <i v-else-if="activeTab === 'chambers'" class="icofont-hospital text-primary font-weight-bold"></i>
              <i v-else-if="activeTab === 'schedule'" class="icofont-clock-time text-success font-weight-bold"></i>
              <i v-else-if="activeTab === 'assistants'" class="icofont-users-alt-5 text-purple font-weight-bold"></i>
              <i v-else-if="activeTab === 'portfolio'" class="icofont-web text-success font-weight-bold"></i>
            </div>
            <div>
              <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 15px;">
                <span v-if="activeTab === 'appointments'">রোগীর সিরিয়াল ও প্রেসক্রিপশন তালিকা</span>
                <span v-else-if="activeTab === 'chambers'">চেম্বার ও ক্লিনিক লোকেশন ব্যবস্থাপনা</span>
                <span v-else-if="activeTab === 'schedule'">চেম্বার সময়সূচি, টেলিমেডিসিন ও ফি সেটিং</span>
                <span v-else-if="activeTab === 'assistants'">চেম্বার সহকারী ও স্টাফদের অ্যাকাউন্ট</span>
                <span v-else-if="activeTab === 'portfolio'">ডাক্তারের পার্সোনাল ওয়েবসাইট ও শর্ট ভিডিও কাস্টমাইজেশন</span>
              </h6>
              <small class="text-muted" style="font-size: 11px;">
                <span v-if="activeTab === 'appointments'">দৈনিক লাইভ কিউ এবং সিরিয়াল অনুযায়ী সেবা দিন</span>
                <span v-else-if="activeTab === 'chambers'">আপনার চেম্বারসমূহ ও শিডিউল নিয়ন্ত্রণ করুন</span>
                <span v-else-if="activeTab === 'schedule'">রোগীদের জন্য ভিজিট ও পরামর্শ সময় নির্ধারণ করুন</span>
                <span v-else-if="activeTab === 'assistants'">সিরিয়াল পরিচালনার জন্য সহকারীকে অ্যাক্সেস দিন</span>
                <span v-else-if="activeTab === 'portfolio'">ওয়েবসাইটের প্রতিটি সেকশন, ব্যানার, সেবা, টিপস ও ভিডিও রিলস কাস্টমাইজ করুন</span>
              </small>
            </div>
          </div>

          <!-- Right side controls for appointments -->
          <div v-if="activeTab === 'appointments'" class="d-flex align-items-center">
            <select v-model="filterStatus" class="custom-select custom-select-sm font-weight-bold rounded-pill shadow-none" style="min-width: 140px;" @change="fetchAppointments(1)">
              <option value="all">🔍 All Status</option>
              <option value="pending">⏳ Pending</option>
              <option value="confirmed">✅ Confirmed</option>
              <option value="completed">🎉 Completed</option>
              <option value="cancelled">❌ Cancelled</option>
            </select>
          </div>

          <!-- Right side controls for portfolio -->
          <div v-else-if="activeTab === 'portfolio'" class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <router-link :to="'/dr/' + (doctor?.slug || doctor?.id)" target="_blank" class="btn btn-sm btn-outline-secondary font-weight-bold rounded-pill px-3 py-1">
              <i class="icofont-eye mr-1"></i> লাইভ ওয়েবসাইট
            </router-link>
            <button 
              type="button" 
              class="btn btn-sm btn-success font-weight-bold rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center text-white" 
              @click="savePortfolio"
              :disabled="savingPortfolio"
              style="background: linear-gradient(135deg, #10b981, #059669); border: none;"
            >
              <span v-if="savingPortfolio" class="spinner-border spinner-border-sm mr-1"></span>
              <i v-else class="icofont-save mr-1"></i>
              {{ savingPortfolio ? 'সংরক্ষণ হচ্ছে...' : 'সব পরিবর্তন সেভ করুন' }}
            </button>
          </div>
        </div>

        <div class="card-body p-0">
          <!-- TAB 1: APPOINTMENTS ROSTER -->
          <div v-if="activeTab === 'appointments'">
            <!-- Modern Live Chamber Queue Controller Card -->
            <div class="live-queue-strip p-3 p-md-4 border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; background: #f8fafc;">
              <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                <div class="d-flex align-items-center bg-white px-3 py-2 rounded-pill shadow-sm border">
                  <span class="live-radar-dot mr-2"></span>
                  <span class="font-weight-bold text-dark small text-uppercase mr-2">লাইভ চেম্বার সিরিয়াল:</span>
                  <span class="badge badge-danger px-3 py-1 font-weight-bold" style="font-size: 15px; border-radius: 8px;">#{{ queueServingSerial }}</span>
                </div>
                <span class="text-muted small font-weight-bold">মোট বুকিং: {{ appointments.length }} জন</span>
                
                <!-- Emergency Active Indicator Badge -->
                <div v-if="isEmergencyActive" class="badge badge-danger px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center font-weight-bold" style="font-size: 13px;">
                  <span class="spinner-grow spinner-grow-sm mr-2 text-warning" role="status"></span>
                  🚨 চেম্বারে জরুরি রোগী চলমান
                </div>
              </div>

              <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <!-- Emergency Bypass Trigger Button -->
                <button 
                  v-if="!isEmergencyActive" 
                  class="btn font-weight-bold px-3 py-2 shadow-sm d-inline-flex align-items-center rounded-pill" 
                  @click="openEmergencyBypassModal"
                  style="background: #fffbeb; color: #b45309; border: 1.5px solid #fde68a;"
                  title="জরুরি আশঙ্কাজনক রোগীর জন্য নিয়মিত সিরিয়াল সাময়িক স্থগিত রাখুন"
                >
                  <i class="fa-solid fa-truck-medical text-danger mr-2"></i> জরুরি রোগী (Emergency Bypass)
                </button>
                <button 
                  v-else 
                  class="btn font-weight-bold px-3 py-2 shadow-sm d-inline-flex align-items-center rounded-pill text-white" 
                  @click="stopEmergencyBypass"
                  style="background: linear-gradient(135deg, #10b981, #059669); border: none;"
                  title="জরুরি চিকিৎসা শেষ করে নিয়মিত সিরিয়ালে ফিরুন"
                >
                  <i class="fa-solid fa-circle-check mr-2"></i> জরুরি সম্পন্ন (স্বাভাবিক সিরিয়ালে ফিরুন)
                </button>

                <button 
                  class="btn font-weight-bold px-4 py-2 shadow-sm d-inline-flex align-items-center rounded-pill text-white" 
                  @click="callNextQueuePatient"
                  style="background: linear-gradient(135deg, #e12454, #be123c); border: none;"
                >
                  <i class="fa-solid fa-bullhorn mr-2"></i> পরবর্তী রোগীকে ডাকুন (Call Next)
                </button>
              </div>
            </div>

            <!-- Emergency Active Notice Strip for Doctor -->
            <div v-if="isEmergencyActive" class="p-3 bg-danger text-white d-flex justify-content-between align-items-center flex-wrap" style="border-left: 6px solid #fef08a;">
              <div class="d-flex align-items-center flex-wrap">
                <span class="mr-2 font-18">🚨</span>
                <div>
                  <strong>জরুরি মোড সক্রিয়:</strong>
                  <span class="ml-1" v-if="emergencyData?.patient_name">রোগী: {{ emergencyData.patient_name }}</span>
                  <span class="ml-1" v-if="emergencyData?.emergency_reason">({{ emergencyData.emergency_reason }})</span>
                  <span class="ml-2 small opacity-90">অপেক্ষমাণ সকল রোগীর লাইভ স্ক্রিনে জরুরি অ্যালার্ট ও আনুমানিক বিলম্ব (+১৫ মি.) প্রদর্শিত হচ্ছে।</span>
                </div>
              </div>
              <button class="btn btn-sm btn-light font-weight-bold rounded-pill text-danger mt-2 mt-md-0 shadow-sm" @click="stopEmergencyBypass">
                জরুরি চিকিৎসা শেষ করুন
              </button>
            </div>

            <div v-if="loadingAppointments" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
            </div>

            <div v-else-if="appointments.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-calendar display-4"></i>
              <p class="mt-2">No appointments found.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0 custom-appointment-table">
                <thead>
                  <tr class="bg-slate-50 text-slate-500 font-weight-bold text-uppercase border-bottom" style="font-size: 11px; letter-spacing: 0.5px;">
                    <th style="min-width: 100px;">সিরিয়াল ও রেফ</th>
                    <th style="min-width: 160px;">রোগীর বিবরণ</th>
                    <th style="min-width: 130px; white-space: nowrap;">তারিখ ও সময়</th>
                    <th style="min-width: 140px;">সমস্যা / উপসর্গ</th>
                    <th style="min-width: 115px;">স্ট্যাটাস</th>
                    <th class="text-right" style="min-width: 190px;">প্রেসক্রিপশন ও অ্যাকশন</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="apt in appointments" :key="apt.id" class="appointment-table-row">
                    <!-- 1. Serial & Ref -->
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="serial-badge mr-2">
                          #{{ apt.serial_number }}
                        </div>
                        <div>
                          <span class="font-weight-bold text-dark font-mono text-nowrap" style="font-size: 11.5px;">{{ apt.appointment_number }}</span>
                        </div>
                      </div>
                    </td>

                    <!-- 2. Patient Details -->
                    <td>
                      <div class="d-flex align-items-center flex-wrap">
                        <span class="font-weight-bold text-dark mr-1.5" style="font-size: 13.5px;">{{ apt.patient_name }}</span>
                        <span v-if="apt.consultation_type === 'online_video' || apt.meeting_link" class="badge-type-video">
                          <i class="fa-solid fa-video mr-1"></i> Video
                        </span>
                        <span v-else class="badge-type-chamber">
                          <i class="fa-solid fa-hospital mr-1"></i> Chamber
                        </span>
                      </div>
                      <div class="mt-0.5">
                        <a :href="'tel:' + apt.patient_phone" class="text-muted small hover-primary" style="font-size: 11.5px;">
                          <i class="fa-solid fa-phone mr-1 text-slate-400" style="font-size: 10px;"></i>{{ apt.patient_phone }}
                        </a>
                      </div>
                    </td>

                    <!-- 3. Date & Time -->
                    <td style="white-space: nowrap;">
                      <div class="font-weight-bold text-dark" style="font-size: 12px;">{{ formatDate(apt.appointment_date) }}</div>
                      <div class="text-primary font-weight-bold small d-flex align-items-center mt-0.5" style="font-size: 11.5px;">
                        <i class="fa-regular fa-clock mr-1"></i> {{ apt.appointment_time }}
                      </div>
                    </td>

                    <!-- 4. Symptoms / Reason -->
                    <td>
                      <span class="text-muted small text-truncate d-block" style="max-width: 160px; font-size: 12px;" :title="apt.reason_for_visit">
                        {{ apt.reason_for_visit || 'সাধারণ চেকআপ / পরামর্শ' }}
                      </span>
                    </td>

                    <!-- 5. Interactive Status Select Badge -->
                    <td>
                      <select 
                        :value="apt.status" 
                        class="status-dropdown-badge"
                        :class="'status-' + apt.status"
                        @change="updateStatus(apt, $event.target.value)"
                        title="ক্লিক করে স্ট্যাটাস পরিবর্তন করুন"
                      >
                        <option value="pending">⏳ Pending</option>
                        <option value="confirmed">✅ Confirmed</option>
                        <option value="completed">🎉 Completed</option>
                        <option value="cancelled">❌ Cancelled</option>
                      </select>
                    </td>

                    <!-- 6. Prescription & Action Toolbar -->
                    <td class="text-right">
                      <div class="d-inline-flex align-items-center justify-content-end" style="gap: 5px;">
                        
                        <!-- Primary 1: Video Call Button (If online video) -->
                        <a 
                          v-if="apt.consultation_type === 'online_video' || apt.meeting_link"
                          :href="'/video-chamber/' + apt.appointment_number"
                          target="_blank"
                          class="btn-row-action btn-row-video"
                          title="লাইভ ১-ক্লিক ভিডিও চেম্বার শুরু করুন"
                        >
                          <i class="fa-solid fa-video mr-1"></i> কল
                        </a>

                        <!-- Primary 2: Write/Edit Rx Button -->
                        <button 
                          class="btn-row-action"
                          :class="apt.prescription ? 'btn-row-rx-edit' : 'btn-row-rx-write'"
                          @click="openPrescriptionModal(apt)"
                          :title="apt.prescription ? 'প্রেসক্রিপশন এডিট করুন' : 'নতুন প্রেসক্রিপশন লিখুন'"
                        >
                          <i class="fa-solid" :class="apt.prescription ? 'fa-file-pen mr-1' : 'fa-plus mr-1'"></i>
                          <span>{{ apt.prescription ? 'Edit Rx' : 'Write Rx' }}</span>
                        </button>

                        <!-- Icon Action Bar (WhatsApp, Vitals, Reports, Print) -->
                        <div class="action-icons-cluster">
                          <!-- WhatsApp -->
                          <a 
                            v-if="apt.patient_phone"
                            :href="getWhatsAppPatientNotifyUrl(apt)"
                            target="_blank"
                            class="action-icon-btn action-whatsapp"
                            title="রোগীকে হোয়াটসঅ্যাপে ১-ক্লিক আপডেট বা ভিডিও লিংক পাঠান"
                          >
                            <i class="fa-brands fa-whatsapp"></i>
                          </a>

                          <!-- Vitals -->
                          <button 
                            v-if="apt.patient_id"
                            class="action-icon-btn action-vitals"
                            @click="openPatientMetricsModal(apt)"
                            title="রোগীর প্রেশার ও ডায়াবেটিস হিস্ট্রি দেখুন"
                          >
                            <i class="fa-solid fa-heart-pulse"></i>
                          </button>

                          <!-- Reports -->
                          <button 
                            v-if="apt.patient_id"
                            class="action-icon-btn action-reports"
                            @click="openPatientReportsModal(apt)"
                            title="রোগীর মেডিকেল টেস্ট রিপোর্ট দেখুন"
                          >
                            <i class="fa-solid fa-file-medical"></i>
                          </button>

                          <!-- Print Rx -->
                          <button 
                            v-if="apt.prescription"
                            class="action-icon-btn action-print"
                            @click="printDoctorPrescription(apt.prescription, apt)"
                            title="প্রেসক্রিপশন প্রিন্ট বা প্রিভিউ করুন"
                          >
                            <i class="fa-solid fa-print"></i>
                          </button>
                        </div>

                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- TAB 2: DOCTOR PROFILE, SCHEDULE & CHAMBER MANAGER -->
          <div v-if="activeTab === 'schedule'" class="p-4 p-md-5 bg-white">
            <h4 class="font-weight-bold text-dark mb-2">
              <i class="icofont-doctor text-danger mr-2"></i> Doctor Profile, Qualifications & Chamber Settings
            </h4>
            <p class="text-muted small mb-4">Update your personal details, medical degrees/qualifications, consultation fee, chamber address, and available hours.</p>

            <form @submit.prevent="saveSchedule">
              <!-- Section 1: Professional Information -->
              <div class="bg-light p-3 rounded mb-4 border">
                <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2">
                  <i class="icofont-id-card text-primary mr-1"></i> 1. Professional & Contact Details (ব্যক্তিগত ও ডিগ্রি তথ্য)
                </h6>
                <div class="row">
                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Doctor Full Name <span class="text-danger">*</span></label>
                    <input 
                      type="text" 
                      v-model="scheduleForm.name" 
                      class="form-control font-weight-bold" 
                      placeholder="e.g. Dr. Jane Doe"
                      required
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Specialization / Department <span class="text-danger">*</span></label>
                    <input 
                      type="text" 
                      v-model="scheduleForm.specialization" 
                      class="form-control" 
                      placeholder="e.g. Cardiology, Pediatrics, Gynecology, Dental Care"
                      required
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Medical Qualifications & Degrees <span class="text-danger">*</span></label>
                    <input 
                      type="text" 
                      v-model="scheduleForm.qualifications" 
                      class="form-control" 
                      placeholder="e.g. MBBS, FCPS, MD (Cardiology), MRCP (UK)"
                      required
                    >
                  </div>

                  <div class="col-md-3 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Designation</label>
                    <input 
                      type="text" 
                      v-model="scheduleForm.designation" 
                      class="form-control" 
                      placeholder="e.g. Senior Consultant"
                    >
                  </div>

                  <div class="col-md-3 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Experience (Years)</label>
                    <input 
                      type="number" 
                      v-model="scheduleForm.experience_years" 
                      class="form-control" 
                      min="0"
                      max="70"
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Contact Phone Number</label>
                    <input 
                      type="tel" 
                      v-model="scheduleForm.phone" 
                      class="form-control" 
                      placeholder="e.g. +880 1700 123456"
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Profile Bio / Summary</label>
                    <input 
                      type="text" 
                      v-model="scheduleForm.bio" 
                      class="form-control" 
                      placeholder="Brief summary of your clinical expertise..."
                    >
                  </div>
                </div>
              </div>

              <!-- Section 2: Chamber & Telemedicine -->
              <div class="bg-light p-3 rounded mb-4 border">
                <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2">
                  <i class="icofont-hospital text-danger mr-1"></i> 2. Chamber Location & Video Consultation (চেম্বার ও ফি)
                </h6>
                <div class="row">
                  <div class="col-md-8 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Chamber / Clinic Address <span class="text-danger">*</span></label>
                    <input 
                      type="text" 
                      v-model="scheduleForm.chamber_address" 
                      class="form-control" 
                      placeholder="e.g. Popular Diagnostic Center, Room 402, Dhanmondi, Dhaka"
                      required
                    >
                  </div>

                  <div class="col-md-4 form-group mb-3">
                    <label class="font-weight-bold text-dark small">City / Location <span class="text-danger">*</span></label>
                    <input 
                      type="text" 
                      v-model="scheduleForm.city" 
                      class="form-control" 
                      placeholder="e.g. Dhaka / Chittagong"
                      required
                    >
                  </div>

                  <!-- Google Meet / Video Consultation Link -->
                  <div class="col-12 form-group mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="font-weight-bold text-dark small mb-0">
                        <i class="icofont-video-cam text-success mr-1"></i> Google Meet / Video Call Link (অনলাইন ভিডিও কনসাল্টেশন লিঙ্ক)
                      </label>
                      <a href="https://meet.google.com/new" target="_blank" class="badge badge-light border text-primary py-1 px-2 text-decoration-none font-weight-bold">
                        <i class="icofont-plus-circle mr-1"></i> + 1-Click Create Google Meet
                      </a>
                    </div>
                    <input 
                      type="url" 
                      v-model="scheduleForm.telemedicine_link" 
                      class="form-control" 
                      placeholder="e.g. https://meet.google.com/abc-defg-hij"
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Consultation Fee ($) <span class="text-danger">*</span></label>
                    <input 
                      type="number" 
                      v-model="scheduleForm.consultation_fee" 
                      class="form-control font-weight-bold text-success" 
                      min="0"
                      required
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Patient Slot Duration (Minutes) <span class="text-danger">*</span></label>
                    <select v-model="scheduleForm.slot_duration_minutes" class="form-control" required>
                      <option :value="15">15 Minutes</option>
                      <option :value="20">20 Minutes</option>
                      <option :value="30">30 Minutes (Recommended)</option>
                      <option :value="45">45 Minutes</option>
                      <option :value="60">60 Minutes (1 Hour)</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Section 3: Consultation Hours & Security -->
              <div class="bg-light p-3 rounded mb-4 border">
                <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2">
                  <i class="icofont-ui-clock text-warning mr-1"></i> 3. Available Hours & Password (সময়সূচি ও পাসওয়ার্ড)
                </h6>
                <div class="row">
                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Chamber Start Time <span class="text-danger">*</span></label>
                    <input 
                      type="time" 
                      v-model="scheduleForm.start_time" 
                      class="form-control" 
                      required
                    >
                  </div>

                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Chamber End Time <span class="text-danger">*</span></label>
                    <input 
                      type="time" 
                      v-model="scheduleForm.end_time" 
                      class="form-control" 
                      required
                    >
                  </div>

                  <div class="col-12 form-group mb-3">
                    <label class="font-weight-bold text-dark small d-block">Weekly Available Chamber Days <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-3 mt-2">
                      <div v-for="day in allDays" :key="day" class="custom-control custom-checkbox mr-4 mb-2">
                        <input 
                          type="checkbox" 
                          :id="'day-' + day" 
                          :value="day" 
                          v-model="scheduleForm.available_days" 
                          class="custom-control-input"
                        >
                        <label :for="'day-' + day" class="custom-control-label font-weight-bold text-dark">
                          {{ day }}
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- FREE CONSULTATION & CHARITY CLINIC SECTION -->
                  <div class="col-12 mt-3 mb-4">
                    <div class="p-4 rounded-lg border border-success bg-light shadow-sm">
                      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <div>
                          <h6 class="font-weight-bold text-success mb-1">
                            🎁 ফ্রি কনসালটেশন ও চ্যারিটি ক্লিনিক (Free Consultation & Charity Days)
                          </h6>
                          <p class="text-muted small mb-0">
                            শুক্রবার বা যেকোনো নির্ধারিত দিনে দরিদ্র ও সাধারণ রোগীদের বিনামূল্যে রোগী দেখার শিডিউল সেট করুন।
                          </p>
                        </div>
                        <div class="custom-control custom-switch mt-2 mt-md-0">
                          <input 
                            type="checkbox" 
                            class="custom-control-input" 
                            id="freeConsultSwitch" 
                            v-model="scheduleForm.free_consultation_enabled"
                          >
                          <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="freeConsultSwitch">
                            {{ scheduleForm.free_consultation_enabled ? 'ফ্রি সেবা চালু আছে (Active)' : 'ফ্রি সেবা বন্ধ (Inactive)' }}
                          </label>
                        </div>
                      </div>

                      <div v-if="scheduleForm.free_consultation_enabled" class="pt-3 border-top">
                        <div class="row">
                          <div class="col-12 mb-3">
                            <label class="font-weight-bold text-dark small d-block">
                              কোন কোন দিন ফ্রি রোগী দেখবেন? <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                              <div v-for="day in allDays" :key="'free-' + day" class="custom-control custom-checkbox mr-3 mb-2">
                                <input 
                                  type="checkbox" 
                                  :id="'free-day-' + day" 
                                  :value="day" 
                                  v-model="scheduleForm.free_days" 
                                  class="custom-control-input"
                                >
                                <label :for="'free-day-' + day" class="custom-control-label font-weight-bold text-dark">
                                  {{ day }}
                                </label>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-3 form-group mb-3">
                            <label class="font-weight-bold text-dark small">ফ্রি সেশন শুরুর সময়</label>
                            <input 
                              type="time" 
                              v-model="scheduleForm.free_start_time" 
                              class="form-control"
                            >
                          </div>

                          <div class="col-md-3 form-group mb-3">
                            <label class="font-weight-bold text-dark small">ফ্রি সেশন শেষ সময়</label>
                            <input 
                              type="time" 
                              v-model="scheduleForm.free_end_time" 
                              class="form-control"
                            >
                          </div>

                          <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-dark small">
                              প্রতি সেশনে সর্বোচ্চ ফ্রি রোগী কোটা (Max Limit)
                            </label>
                            <input 
                              type="number" 
                              v-model="scheduleForm.free_patient_limit" 
                              class="form-control" 
                              min="1" 
                              max="200" 
                              placeholder="যেমন: 20"
                            >
                            <small class="text-muted">কোটা পূর্ণ হলে ওই দিনের ফ্রি সিরিয়াল স্বয়ংক্রিয়ভাবে বন্ধ থাকবে।</small>
                          </div>

                          <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-dark small">ফ্রি চেম্বার / ক্যাম্পের ঠিকানা (ঐচ্ছিক)</label>
                            <input 
                              type="text" 
                              v-model="scheduleForm.free_chamber_address" 
                              class="form-control" 
                              placeholder="খালি রাখলে আপনার মূল চেম্বারের ঠিকানাই ব্যবহার হবে"
                            >
                          </div>

                          <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-dark small">রোগীদের জন্য বিশেষ নির্দেশনা (ঐচ্ছিক)</label>
                            <input 
                              type="text" 
                              v-model="scheduleForm.free_service_note" 
                              class="form-control" 
                              placeholder="যেমন: পূর্বের সকল প্রেসক্রিপশন ও রিপোর্ট সাথে আনবেন।"
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-12 form-group mb-0 border-top pt-3">
                    <label class="font-weight-bold text-dark small">Change Password (পাসওয়ার্ড পরিবর্তন - ঐচ্ছিক)</label>
                    <input 
                      type="password" 
                      v-model="scheduleForm.password" 
                      class="form-control" 
                      placeholder="Leave blank to keep your current password"
                      minlength="6"
                    >
                  </div>
                </div>
              </div>

              <button 
                type="submit" 
                class="btn btn-main-2 btn-round-full px-5 shadow-sm"
                :disabled="savingSchedule"
              >
                <span v-if="savingSchedule">
                  <span class="spinner-border spinner-border-sm mr-2"></span> Saving Changes...
                </span>
                <span v-else>
                  <i class="icofont-check-circled mr-1"></i> Save Profile & Chamber Settings
                </span>
              </button>
            </form>
          </div>

          <!-- TAB: MULTIPLE CHAMBERS MANAGEMENT -->
          <div v-if="activeTab === 'chambers'" class="p-4 p-md-5 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 12px;">
              <div>
                <h4 class="font-weight-bold text-dark mb-1">
                  <i class="icofont-hospital text-danger mr-2"></i> আমার চেম্বারসমূহ (Manage Chambers)
                </h4>
                <p class="text-muted small mb-0">
                  আপনার একাধিক চেম্বারের ঠিকানা, বসার দিন, সময়সূচী এবং ফি সহজে যুক্ত ও নিয়ন্ত্রণ করুন। রোগীরা নির্দিষ্ট চেম্বার পছন্দ করে সিরিয়াল নিতে পারবে।
                </p>
              </div>
              <button class="btn btn-danger font-weight-bold px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center" @click="openAddChamberModal">
                <i class="icofont-plus mr-2"></i> নতুন চেম্বার যোগ করুন
              </button>
            </div>

            <!-- Loading State -->
            <div v-if="loadingChambers" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
              <p class="text-muted small mt-2">চেম্বার তথ্য লোড হচ্ছে...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="chambersList.length === 0" class="text-center py-5 bg-light rounded-16 border">
              <i class="icofont-hospital display-4 text-muted"></i>
              <h5 class="font-weight-bold text-dark mt-3">এখনো কোনো চেম্বার যোগ করা হয়নি</h5>
              <p class="text-muted small">আপনার হাসপাতাল বা ক্লিনিকের চেম্বার যুক্ত করে রোগীদের সিরিয়াল নেয়ার সুযোগ দিন।</p>
              <button class="btn btn-danger font-weight-bold px-4 py-2 rounded-pill shadow-sm mt-2" @click="openAddChamberModal">
                <i class="icofont-plus mr-1"></i> প্রথম চেম্বার যোগ করুন
              </button>
            </div>

            <!-- Chambers Cards Grid -->
            <div v-else class="row">
              <div v-for="ch in chambersList" :key="ch.id" class="col-lg-6 mb-4">
                <div class="card h-100 border shadow-sm rounded-16 p-3 p-md-4 position-relative" :class="{ 'border-danger': ch.is_primary, 'bg-white': true }">
                  <!-- Primary Badge -->
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                      <span v-if="ch.is_primary" class="badge badge-danger px-3 py-1 font-weight-bold rounded-pill">
                        <i class="icofont-star mr-1"></i> প্রধান চেম্বার (Primary)
                      </span>
                      <span v-else class="badge badge-light border text-muted px-3 py-1 rounded-pill">
                        অতিরিক্ত চেম্বার
                      </span>
                      <span v-if="ch.branch_name" class="badge badge-primary text-white border px-2 py-1 rounded-pill small font-weight-bold">
                        <i class="icofont-location-pin"></i> {{ ch.branch_name }}
                      </span>
                    </div>

                    <div class="d-flex" style="gap: 6px;">
                      <button class="btn btn-sm btn-outline-primary rounded-pill px-3 font-weight-bold" @click="openEditChamberModal(ch)">
                        <i class="icofont-edit mr-1"></i> এডিট
                      </button>
                      <button class="btn btn-sm btn-outline-danger rounded-pill px-2" :disabled="chambersList.length <= 1" @click="deleteChamber(ch)" title="মুছুন">
                        <i class="icofont-trash"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Chamber Name -->
                  <h5 class="font-weight-bold text-dark mb-1" style="font-size: 18px;">{{ ch.chamber_name }}</h5>
                  <p class="text-muted small mb-3">
                    <i class="icofont-google-map text-danger mr-1"></i> {{ ch.address }}
                    <span v-if="ch.upazila || ch.district"> ({{ ch.upazila ? ch.upazila + ', ' : '' }}{{ ch.district }})</span>
                  </p>

                  <!-- Schedule Details Box -->
                  <div class="bg-light p-3 rounded-12 mb-3">
                    <div class="row small">
                      <div class="col-6 mb-2">
                        <span class="text-muted d-block">পরামর্শ ফি:</span>
                        <strong class="text-success font-weight-bold font-15">৳{{ ch.consultation_fee || doctor?.consultation_fee || 0 }}</strong>
                      </div>
                      <div class="col-6 mb-2">
                        <span class="text-muted d-block">রোগী প্রতি সময়:</span>
                        <strong class="text-dark font-weight-bold">{{ ch.slot_duration_minutes || 20 }} মিনিট</strong>
                      </div>
                      <div class="col-12 mb-2">
                        <span class="text-muted d-block">বসার সময়:</span>
                        <strong class="text-dark font-weight-bold">
                          <i class="icofont-clock-time text-primary mr-1"></i> {{ formatTimeDisplay(ch.start_time) }} - {{ formatTimeDisplay(ch.end_time) }}
                        </strong>
                      </div>
                      <div v-if="ch.phone" class="col-12 mb-1">
                        <span class="text-muted d-block">সিরিয়াল যোগাযোগ:</span>
                        <strong class="text-primary font-weight-bold">
                          <i class="icofont-phone mr-1"></i> {{ ch.phone }}
                        </strong>
                      </div>
                    </div>
                  </div>

                  <!-- Visiting Days -->
                  <div class="mb-3">
                    <span class="text-muted small d-block font-weight-bold mb-1">বসার দিনসমূহ:</span>
                    <div class="d-flex flex-wrap" style="gap: 5px;">
                      <span 
                        v-for="d in allDays" 
                        :key="d" 
                        class="badge px-2 py-1 rounded font-weight-normal"
                        :class="isDayActive(ch, d) ? 'badge-danger font-weight-bold' : 'badge-light border text-muted opacity-50'"
                        style="font-size: 11px;"
                      >
                        {{ getDayBangla(d) }}
                      </span>
                    </div>
                  </div>

                  <!-- Quick Action Buttons -->
                  <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                    <button v-if="!ch.is_primary" class="btn btn-sm btn-outline-warning text-dark font-weight-bold rounded-pill px-3" @click="setPrimaryChamber(ch)">
                      <i class="icofont-star text-warning mr-1"></i> প্রধান চেম্বার করুন
                    </button>
                    <span v-else class="text-success small font-weight-bold">
                      <i class="icofont-check-circled"></i> ডিফল্ট প্রধান চেম্বার
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 3: ASSISTANT / COMPOUNDER MANAGEMENT -->
          <div v-if="activeTab === 'assistants'" class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
              <div>
                <h5 class="font-weight-bold text-dark mb-1">🧑‍⚕️ চেম্বার সহকারী / কম্পাউন্ডার সাব-একাউন্ট</h5>
                <p class="text-muted small mb-0">সহকারীকে সিরিয়াল এন্ট্রি, টোকেন কল এবং রিপোর্ট আপলোডের অ্যাক্সেস দিন।</p>
              </div>
              <button class="btn btn-sm btn-danger font-weight-bold px-3 py-2 btn-round-full shadow-sm" @click="showAddAssistantModal = true">
                <i class="icofont-plus mr-1"></i> নতুন সহকারী যুক্ত করুন
              </button>
            </div>

            <div v-if="assistantsList.length === 0" class="text-center py-5 bg-white rounded border">
              <i class="icofont-users-alt-5 display-4 text-muted"></i>
              <h6 class="font-weight-bold mt-2">এখনো কোনো সহকারী যুক্ত করা হয়নি</h6>
              <button class="btn btn-sm btn-danger btn-round-full mt-2 font-weight-bold" @click="showAddAssistantModal = true">সহকারী যুক্ত করুন</button>
            </div>

            <div v-else class="row">
              <div v-for="asst in assistantsList" :key="asst.id" class="col-md-6 col-lg-4 mb-3">
                <div class="card border rounded-lg shadow-sm p-3 bg-white">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="font-weight-bold text-dark mb-0">{{ asst.name }}</h6>
                    <button class="btn btn-sm btn-outline-danger py-0 px-2" @click="deleteAssistant(asst.id)"><i class="icofont-trash"></i></button>
                  </div>
                  <div class="small text-muted mb-2">
                    <div><strong>মোবাইল:</strong> {{ asst.phone }}</div>
                    <div><strong>অ্যাক্সেস পিন:</strong> <span class="badge badge-warning text-dark font-weight-bold">{{ asst.access_pin }}</span></div>
                  </div>
                  <span class="badge badge-success py-1">সক্রিয় সহকারী</span>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 5: DYNAMIC PORTFOLIO & SHORT VIDEOS BUILDER -->
          <div v-if="activeTab === 'portfolio'" class="p-3 p-md-4">
            <!-- Portfolio Top Sub-navigation Tabs -->
            <div class="d-flex flex-wrap border-bottom pb-3 mb-4" style="gap: 8px;">
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'template' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'template'"
              >
                <i class="icofont-paint mr-1"></i> 🎨 পোর্টফোলিও টেমপ্লেট (৫টি ডিজাইন)
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'reels' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'reels'"
              >
                <i class="icofont-video-alt mr-1"></i> 🎬 শর্ট ভিডিও ও রিলস ({{ portfolioForm.short_videos?.length || 0 }})
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'hero' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'hero'"
              >
                <i class="icofont-ui-user mr-1"></i> 👤 ব্যানার ও পরিচিতি
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'services' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'services'"
              >
                <i class="icofont-stethoscope-alt mr-1"></i> 🩺 চিকিৎসা সেবা ({{ portfolioForm.services?.length || 0 }})
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'education' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'education'"
              >
                <i class="icofont-graduate-alt mr-1"></i> 🎓 শিক্ষাজীবন ({{ portfolioForm.education?.length || 0 }})
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'tips' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'tips'"
              >
                <i class="icofont-bulb-alt mr-1"></i> 💡 স্বাস্থ্য টিপস ({{ portfolioForm.tips?.length || 0 }})
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'testimonials' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'testimonials'"
              >
                <i class="icofont-quote-left mr-1"></i> 💬 রোগীর রিভিউ ({{ portfolioForm.testimonials?.length || 0 }})
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'faqs' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'faqs'"
              >
                <i class="icofont-question-circle mr-1"></i> ❓ প্রশ্নোত্তর ({{ portfolioForm.faqs?.length || 0 }})
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'gallery' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'gallery'"
              >
                <i class="icofont-image mr-1"></i> 🖼️ গ্যালারি ({{ portfolioForm.gallery?.length || 0 }})
              </button>
              <button 
                type="button" 
                class="btn btn-sm rounded-pill font-weight-bold px-3 py-2 d-inline-flex align-items-center"
                :class="portfolioSubTab === 'contact' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-dark'"
                @click="portfolioSubTab = 'contact'"
              >
                <i class="icofont-phone mr-1"></i> 📞 চেম্বার ও সোশ্যাল
              </button>
            </div>

            <!-- Loading Spinner for Portfolio -->
            <div v-if="loadingPortfolio" class="text-center py-5">
              <div class="spinner-border text-success" role="status"></div>
              <p class="text-muted mt-2 font-weight-bold small">পোর্টফোলিও ডাটা লোড হচ্ছে...</p>
            </div>

            <div v-else>
              <!-- ================= 0. PORTFOLIO TEMPLATES SELECTION SUB-TAB ================= -->
              <div v-if="portfolioSubTab === 'template'">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 16px;">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">
                      🎨 পোর্টফোলিও টেমপ্লেট ডিজাইন নির্বাচন (৫টি প্রিমিয়াম লুক)
                    </h5>
                    <p class="text-muted small mb-0">
                      আপনার স্পেশালিটি ও পার্সোনালিটির সাথে মানানসই আধুনিক টেমপ্লেট বেছে নিন। যেকোনো সময় এক ক্লিকেই লাইভ ওয়েবসাইট আপডেট হয়ে যাবে।
                    </p>
                  </div>
                  <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <a 
                      v-if="doctor" 
                      :href="'/portfolio/' + (doctor.slug || doctor.id)" 
                      target="_blank" 
                      class="btn btn-sm btn-outline-success font-weight-bold px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center"
                    >
                      <i class="icofont-external-link mr-1"></i> লাইভ পোর্টফোলিও দেখুন
                    </a>
                    <button 
                      type="button" 
                      class="btn btn-sm btn-success font-weight-bold px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center" 
                      :disabled="savingPortfolio" 
                      @click="savePortfolio(false)"
                    >
                      <i class="icofont-save mr-1"></i>
                      <span v-if="savingPortfolio">সংরক্ষণ হচ্ছে...</span>
                      <span v-else>টেমপ্লেট সেভ করুন</span>
                    </button>
                  </div>
                </div>

                <!-- Template Cards Grid -->
                <div class="row">
                  <div 
                    v-for="tpl in portfolioTemplates" 
                    :key="tpl.id" 
                    class="col-12 col-md-6 col-xl-4 mb-4"
                  >
                    <div 
                      class="card h-100 border shadow-sm transition-all"
                      :style="{
                        borderRadius: '18px',
                        cursor: 'pointer',
                        borderColor: portfolioForm.template === tpl.id ? '#10b981' : '#e2e8f0',
                        boxShadow: portfolioForm.template === tpl.id ? '0 10px 30px -5px rgba(16, 185, 129, 0.25)' : 'none',
                        borderWidth: portfolioForm.template === tpl.id ? '2.5px' : '1px',
                        position: 'relative',
                        overflow: 'hidden'
                      }"
                      @click="portfolioForm.template = tpl.id"
                    >
                      <!-- Active Ribbon / Badge -->
                      <div 
                        v-if="portfolioForm.template === tpl.id" 
                        class="position-absolute"
                        style="top: 12px; right: 12px; z-index: 5;"
                      >
                        <span class="badge badge-success px-2 py-1 shadow-sm font-weight-bold d-inline-flex align-items-center" style="font-size: 0.8rem; border-radius: 99px;">
                          <i class="icofont-check-circled mr-1"></i> বর্তমান নির্বাচিত
                        </span>
                      </div>

                      <!-- Miniature UI Mockup Window -->
                      <div 
                        class="p-3" 
                        :style="{
                          backgroundColor: tpl.previewBg,
                          borderBottom: '1px solid rgba(0,0,0,0.08)',
                          minHeight: '175px',
                          display: 'flex',
                          flexDirection: 'column',
                          justifyContent: 'space-between',
                          color: tpl.isDark ? '#fff' : '#1e293b'
                        }"
                      >
                        <!-- Browser Window Bar -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <div class="d-flex align-items-center" style="gap: 5px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                          </div>
                          <span style="font-size: 0.68rem; opacity: 0.7; font-family: monospace;">dr/{{ doctor.slug || 'profile' }}</span>
                        </div>

                        <!-- Mini Mockup Content -->
                        <div class="d-flex align-items-center justify-content-between my-2" style="gap: 12px;">
                          <div>
                            <div 
                              style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;"
                              :style="{ color: tpl.previewAccent }"
                            >
                              ● {{ tpl.tag }}
                            </div>
                            <div style="font-size: 0.95rem; font-weight: bold; line-height: 1.2;">
                              {{ doctor.name || 'ডা. বিশেষজ্ঞ' }}
                            </div>
                            <div style="font-size: 0.7rem; opacity: 0.75; margin-top: 3px;">
                              {{ doctor.specialization || 'বিশেষজ্ঞ সার্জন ও কনসালটেন্ট' }}
                            </div>
                            <!-- Mini button pill -->
                            <div 
                              class="mt-2 d-inline-block px-2 py-1 rounded-pill"
                              :style="{
                                backgroundColor: tpl.previewAccent,
                                color: tpl.isDark ? '#000' : '#fff',
                                fontSize: '0.65rem',
                                fontWeight: 'bold'
                              }"
                            >
                              সিরিয়াল নিন ➜
                            </div>
                          </div>

                          <!-- Mini Mockup Avatar -->
                          <div 
                            style="width: 58px; height: 58px; border-radius: 14px; overflow: hidden; flex: none; border: 2px solid;"
                            :style="{ borderColor: tpl.previewAccent, backgroundColor: tpl.isDark ? '#1e293b' : '#e2e8f0' }"
                          >
                            <img 
                              :src="doctor.avatar_url || '/images/team/default-doctor-male.svg'" 
                              style="width: 100%; height: 100%; object-fit: cover;" 
                            />
                          </div>
                        </div>

                        <!-- Mini Mockup Footer line -->
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: rgba(255,255,255,0.1) !important; font-size: 0.68rem; opacity: 0.8;">
                          <span>⭐ ৫★ রোগী সন্তুষ্টি</span>
                          <span>🏥 চেম্বার ও সিরিয়াল</span>
                        </div>
                      </div>

                      <!-- Card Body -->
                      <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                          <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 1.05rem;">
                              {{ tpl.titleBn }}
                            </h6>
                          </div>

                          <p class="text-muted small mb-2" style="line-height: 1.5;">
                            {{ tpl.desc }}
                          </p>

                          <!-- Specialty match alert -->
                          <div class="p-2 rounded mb-3" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; font-size: 0.78rem;">
                            <strong class="text-secondary"><i class="icofont-info-circle mr-1"></i>সেরা মানানসই:</strong>
                            <span class="text-dark">{{ tpl.specialty }}</span>
                          </div>

                          <!-- Color Swatches -->
                          <div class="d-flex align-items-center mb-3" style="gap: 8px;">
                            <span class="text-muted small font-weight-bold" style="font-size: 0.75rem;">কালার প্যালেট:</span>
                            <div class="d-flex align-items-center" style="gap: 6px;">
                              <span 
                                v-for="(col, cIdx) in tpl.colors" 
                                :key="cIdx" 
                                :style="{
                                  backgroundColor: col,
                                  width: '18px',
                                  height: '18px',
                                  borderRadius: '50%',
                                  border: '1.5px solid #cbd5e1',
                                  display: 'inline-block'
                                }"
                                :title="col"
                              ></span>
                            </div>
                          </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="gap: 8px;">
                          <!-- Live Preview Link in new tab -->
                          <a 
                            :href="'/portfolio/' + (doctor.slug || doctor.id) + '?template=' + tpl.id" 
                            target="_blank"
                            class="btn btn-sm btn-light border text-primary font-weight-bold rounded-pill px-3 py-1 d-inline-flex align-items-center"
                            style="font-size: 0.82rem;"
                            @click.stop
                          >
                            <i class="icofont-eye-alt mr-1"></i> লাইভ প্রিভিউ
                          </a>

                          <!-- Select Button -->
                          <button 
                            type="button" 
                            class="btn btn-sm rounded-pill px-3 py-1 font-weight-bold"
                            :class="portfolioForm.template === tpl.id ? 'btn-success text-white' : 'btn-outline-secondary'"
                            style="font-size: 0.82rem;"
                            @click="portfolioForm.template = tpl.id; savePortfolio(true);"
                          >
                            <span v-if="portfolioForm.template === tpl.id">
                              <i class="icofont-check mr-1"></i> নির্বাচিত আছে
                            </span>
                            <span v-else>
                              নির্বাচন করুন
                            </span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Bottom Floating / Sticky Save Card -->
                <div class="card bg-light border-success p-3 rounded-xl mt-2 d-flex flex-row align-items-center justify-content-between flex-wrap" style="gap: 12px; border-width: 1.5px;">
                  <div class="d-flex align-items-center" style="gap: 12px;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                      <i class="icofont-check-circled"></i>
                    </div>
                    <div>
                      <div class="font-weight-bold text-dark" style="font-size: 0.98rem;">
                        বর্তমান সক্রিয় ডিজাইন: 
                        <span class="text-success font-weight-bold">
                          {{ (portfolioTemplates.find(t => t.id === portfolioForm.template) || {}).titleBn || portfolioForm.template }}
                        </span>
                      </div>
                      <div class="text-muted small">
                        Save বাটনে ক্লিক করলে আপনার রোগীদের জন্য তাৎক্ষণিকভাবে এই ডিজাইনটি সেট হয়ে যাবে।
                      </div>
                    </div>
                  </div>
                  <div>
                    <button 
                      type="button" 
                      class="btn btn-success font-weight-bold px-4 py-2 rounded-pill shadow-sm"
                      :disabled="savingPortfolio"
                      @click="savePortfolio(false)"
                    >
                      <i class="icofont-save mr-1"></i>
                      <span v-if="savingPortfolio">সংরক্ষণ হচ্ছে...</span>
                      <span v-else>টেমপ্লেট সেভ ও পাবলিশ করুন</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- ================= 1. SHORT VIDEOS & REELS SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'reels'">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">🎬 শর্ট ভিডিও ও হেলথ রিলস (Health Reels)</h5>
                    <p class="text-muted small mb-0">
                      YouTube Shorts, Facebook Reels, TikTok বা সরাসরি MP4 ভিডিও আপলোড করুন। রোগীরা আপনার পার্সোনাল ওয়েবসাইট ও রিলস প্লেয়ারে দেখতে পাবেন।
                    </p>
                  </div>
                  <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <!-- Direct Video File Upload trigger -->
                    <label class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-2 rounded-pill shadow-sm mb-0 cursor-pointer">
                      <i class="icofont-upload-alt mr-1"></i>
                      <span v-if="uploadingPortfolioVideo"><span class="spinner-border spinner-border-sm mr-1"></span> আপলোড হচ্ছে...</span>
                      <span v-else>📁 সরাসরি ভিডিও আপলোড (MP4)</span>
                      <input type="file" class="d-none" accept="video/mp4,video/webm,video/quicktime" @change="uploadPortfolioVideoFile">
                    </label>

                    <button class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm" @click="openAddVideoModal">
                      <i class="icofont-plus mr-1"></i> নতুন ভিডিও লিংক যোগ করুন
                    </button>
                  </div>
                </div>

                <!-- Video Reels Cards Grid -->
                <div v-if="!portfolioForm.short_videos || portfolioForm.short_videos.length === 0" class="text-center py-5 bg-light rounded-16 border">
                  <i class="icofont-video-cam display-4 text-muted"></i>
                  <h6 class="font-weight-bold mt-2 text-dark">এখনো কোনো শর্ট ভিডিও যুক্ত করা হয়নি</h6>
                  <p class="text-muted small mb-3">আপনার স্বাস্থ্য পরামর্শমূলক ভিডিও বা ইউটিউব শর্টস যোগ করে রোগীদের সচেতন করুন।</p>
                  <button class="btn btn-sm btn-danger font-weight-bold rounded-pill px-4 py-2" @click="openAddVideoModal">
                    <i class="icofont-plus mr-1"></i> প্রথম ভিডিও যুক্ত করুন
                  </button>
                </div>

                <div v-else class="row g-3">
                  <div 
                    v-for="(vid, idx) in portfolioForm.short_videos" 
                    :key="vid.id || idx" 
                    class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3"
                  >
                    <div class="card h-100 border rounded-16 overflow-hidden shadow-sm hover-shadow bg-white">
                      <!-- 9:16 Video Preview Card -->
                      <div class="position-relative" style="aspect-ratio: 9/16; background: #0b1410; overflow: hidden;">
                        <img 
                          v-if="vid.thumbnail_url" 
                          :src="vid.thumbnail_url" 
                          alt="Thumbnail" 
                          class="w-100 h-100" 
                          style="object-fit: cover;"
                        >
                        <div v-else class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white-50">
                          <i class="icofont-ui-video-play display-4 text-success"></i>
                          <small class="mt-2 text-muted">ভিডিও প্রিভিউ</small>
                        </div>

                        <!-- Play badge -->
                        <div class="position-absolute rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow" style="top: 50%; left: 50%; transform: translate(-50%, -50%); width: 44px; height: 44px;">
                          <i class="icofont-ui-play ml-1" style="font-size: 18px;"></i>
                        </div>

                        <!-- Tag & Duration -->
                        <span v-if="vid.tag" class="badge badge-success position-absolute" style="top: 10px; left: 10px; font-size: 11px;">
                          {{ vid.tag }}
                        </span>
                        <span v-if="vid.duration" class="badge badge-dark position-absolute" style="bottom: 10px; right: 10px; font-size: 11px; background: rgba(0,0,0,0.75);">
                          {{ vid.duration }}
                        </span>
                      </div>

                      <div class="card-body p-3 d-flex flex-column">
                        <h6 class="font-weight-bold text-dark mb-1 text-truncate" :title="vid.title" style="font-size: 14px;">
                          {{ vid.title || 'শিরোনাম নেই' }}
                        </h6>
                        <p class="text-muted small mb-2 text-truncate" :title="vid.description" style="font-size: 12px;">
                          {{ vid.description || 'বিবরণ নেই' }}
                        </p>
                        <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                          <span class="badge badge-light border text-secondary small">
                            <i class="icofont-eye mr-1"></i> {{ vid.views || '1.2K ভিউ' }}
                          </span>
                          <div class="d-flex" style="gap: 5px;">
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="openEditVideoModal(vid, idx)" title="সম্পাদনা">
                              <i class="icofont-edit text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="deleteShortVideo(idx)" title="মুছুন">
                              <i class="icofont-trash text-danger"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ================= 2. HERO & ABOUT SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'hero'">
                <div class="row">
                  <!-- Hero Section Card -->
                  <div class="col-lg-6 mb-4">
                    <div class="card border rounded-16 p-3 p-md-4 bg-white h-100 shadow-sm">
                      <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                        <i class="icofont-ui-user text-danger mr-1"></i> ১. প্রধান ব্যানার (Hero Section)
                      </h6>
                      
                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">স্পেশালিটি ব্যাজ / ক্যাটাগরি</label>
                        <input type="text" v-model="portfolioForm.hero.badge_text" class="form-control" placeholder="যেমন: শিশু ও নবজাতক বিশেষজ্ঞ">
                      </div>

                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">ডাক্তারের নাম (Hero Title)</label>
                        <input type="text" v-model="portfolioForm.hero.title" class="form-control" placeholder="ডা. মোশারফ হোসেন">
                      </div>

                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">ডিগ্রি ও পদবি (Hero Subtitle)</label>
                        <input type="text" v-model="portfolioForm.hero.subtitle" class="form-control" placeholder="এমবিবিএস, বিসিএস (স্বাস্থ্য), এমডি (শিশু)">
                      </div>

                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">সংক্ষিপ্ত ভূমিকা (Description)</label>
                        <textarea v-model="portfolioForm.hero.description" class="form-control" rows="3" placeholder="রোগীর সেবা ও শিশুর সুস্বাস্থ্যের নিশ্চয়তায় সার্বক্ষণিক পাশে আছি..."></textarea>
                      </div>

                      <!-- Hero Photo -->
                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark d-block">ব্যানার ছবি (Hero Photo)</label>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                          <img 
                            :src="portfolioForm.hero.photo || getDoctorAvatar(doctor)" 
                            alt="Hero" 
                            class="rounded-14 border shadow-sm" 
                            style="width: 70px; height: 85px; object-fit: cover; background: #f8fafc;"
                          >
                          <div class="flex-grow-1">
                            <label class="btn btn-sm btn-outline-danger font-weight-bold rounded-pill px-3 py-1 mb-1 cursor-pointer">
                              <i class="icofont-camera mr-1"></i> নতুন ছবি আপলোড
                              <input type="file" class="d-none" accept="image/*" @change="e => uploadPortfolioMediaFile(e, 'hero.photo')">
                            </label>
                            <input type="text" v-model="portfolioForm.hero.photo" class="form-control form-control-sm mt-1" placeholder="অথবা ছবির সরাসরি URL পেস্ট করুন">
                          </div>
                        </div>
                      </div>

                      <!-- Key Counters -->
                      <div class="row">
                        <div class="col-4 form-group mb-3">
                          <label class="font-weight-bold small text-dark">অভিজ্ঞতা</label>
                          <input type="text" v-model="portfolioForm.hero.stat_experience" class="form-control form-control-sm" placeholder="১৫+ বছর">
                        </div>
                        <div class="col-4 form-group mb-3">
                          <label class="font-weight-bold small text-dark">রোগী সেবা</label>
                          <input type="text" v-model="portfolioForm.hero.stat_patients" class="form-control form-control-sm" placeholder="১০,০০০+">
                        </div>
                        <div class="col-4 form-group mb-3">
                          <label class="font-weight-bold small text-dark">সফলতা</label>
                          <input type="text" v-model="portfolioForm.hero.stat_success" class="form-control form-control-sm" placeholder="৯৯%">
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- About Section Card -->
                  <div class="col-lg-6 mb-4">
                    <div class="card border rounded-16 p-3 p-md-4 bg-white h-100 shadow-sm">
                      <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                        <i class="icofont-book-alt text-primary mr-1"></i> ২. বিস্তারিত পরিচিতি ও চিকিৎসা দর্শন (About)
                      </h6>

                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">পরিচিতি সেকশনের শিরোনাম</label>
                        <input type="text" v-model="portfolioForm.about.heading" class="form-control" placeholder="আমার পরিচিতি ও চিকিৎসা দর্শন">
                      </div>

                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">প্রথম অনুচ্ছেদ (Paragraph 1)</label>
                        <textarea v-model="portfolioForm.about.text_1" class="form-control" rows="3" placeholder="বিগত দেড় দশক ধরে শিশু ও নবজাতকের নানা জটিল রোগের চিকিৎসা দিয়ে আসছি..."></textarea>
                      </div>

                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">দ্বিতীয় অনুচ্ছেদ (Paragraph 2)</label>
                        <textarea v-model="portfolioForm.about.text_2" class="form-control" rows="3" placeholder="আমার কাছে প্রতিটি রোগীই সমান গুরুত্ব পায়। সঠিক রোগ নির্ণয় এবং আন্তরিক সেবাই আমার মূল লক্ষ্য।"></textarea>
                      </div>

                      <!-- About Photo -->
                      <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark d-block">পরিচিতির ছবি (About Photo)</label>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                          <img 
                            :src="portfolioForm.about.photo || portfolioForm.hero.photo || getDoctorAvatar(doctor)" 
                            alt="About" 
                            class="rounded-14 border shadow-sm" 
                            style="width: 70px; height: 85px; object-fit: cover; background: #f8fafc;"
                          >
                          <div class="flex-grow-1">
                            <label class="btn btn-sm btn-outline-primary font-weight-bold rounded-pill px-3 py-1 mb-1 cursor-pointer">
                              <i class="icofont-camera mr-1"></i> ছবি আপলোড
                              <input type="file" class="d-none" accept="image/*" @change="e => uploadPortfolioMediaFile(e, 'about.photo')">
                            </label>
                            <input type="text" v-model="portfolioForm.about.photo" class="form-control form-control-sm mt-1" placeholder="অথবা ছবির URL পেস্ট করুন">
                          </div>
                        </div>
                      </div>

                      <!-- 2 Core Values -->
                      <div class="bg-light p-3 rounded-14 border">
                        <span class="font-weight-bold small text-dark d-block mb-2">⭐ সেবার মূল নীতিসমূহ (Core Values):</span>
                        <div class="row">
                          <div class="col-6 mb-2">
                            <input type="text" v-model="portfolioForm.about.values[0].title" class="form-control form-control-sm mb-1 font-weight-bold" placeholder="নীতি ১ শিরোনাম">
                            <input type="text" v-model="portfolioForm.about.values[0].description" class="form-control form-control-sm" placeholder="নীতি ১ বিবরণ">
                          </div>
                          <div class="col-6 mb-2">
                            <input type="text" v-model="portfolioForm.about.values[1].title" class="form-control form-control-sm mb-1 font-weight-bold" placeholder="নীতি ২ শিরোনাম">
                            <input type="text" v-model="portfolioForm.about.values[1].description" class="form-control form-control-sm" placeholder="নীতি ২ বিবরণ">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ================= 3. SERVICES SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'services'">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">🩺 বিশেষায়িত চিকিৎসা সেবা (Services)</h5>
                    <p class="text-muted small mb-0">আপনার রোগীরা যেসব রোগ বা স্বাস্থ্য সমস্যার চিকিৎসা পান তা যোগ বা আপডেট করুন।</p>
                  </div>
                  <button class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm" @click="openAddServiceModal">
                    <i class="icofont-plus mr-1"></i> নতুন সেবা যুক্ত করুন
                  </button>
                </div>

                <div v-if="!portfolioForm.services || portfolioForm.services.length === 0" class="text-center py-5 bg-light rounded-16 border">
                  <i class="icofont-stethoscope display-4 text-muted"></i>
                  <h6 class="font-weight-bold mt-2">এখনো কোনো সেবা যুক্ত করা হয়নি</h6>
                  <button class="btn btn-sm btn-danger rounded-pill px-4 py-2 font-weight-bold mt-2" @click="openAddServiceModal">সেবা যুক্ত করুন</button>
                </div>

                <div v-else class="row g-3">
                  <div v-for="(srv, idx) in portfolioForm.services" :key="srv.id || idx" class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 border rounded-16 p-3 shadow-sm bg-white d-flex flex-column justify-content-between">
                      <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <div class="rounded-12 bg-success-soft text-success d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i :class="srv.icon || 'icofont-stethoscope'" style="font-size: 20px;"></i>
                          </div>
                          <div class="d-flex" style="gap: 5px;">
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="openEditServiceModal(srv, idx)" title="সম্পাদনা">
                              <i class="icofont-edit text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="deleteService(idx)" title="মুছুন">
                              <i class="icofont-trash text-danger"></i>
                            </button>
                          </div>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 15px;">{{ srv.title }}</h6>
                        <p class="text-muted small mb-0">{{ srv.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ================= 4. EDUCATION & TIMELINE SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'education'">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">🎓 শিক্ষাজীবন, ডিগ্রি ও অর্জন (Education & Timeline)</h5>
                    <p class="text-muted small mb-0">আপনার মেডিকেল ডিগ্রি, ফেলোশিপ ও ক্লিনিক্যাল কর্মজীবনের সময়রেখা সাজান।</p>
                  </div>
                  <button class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm" @click="openAddTimelineModal">
                    <i class="icofont-plus mr-1"></i> নতুন ডিগ্রি বা পদবি যোগ করুন
                  </button>
                </div>

                <div v-if="!portfolioForm.education || portfolioForm.education.length === 0" class="text-center py-5 bg-light rounded-16 border">
                  <i class="icofont-graduate-alt display-4 text-muted"></i>
                  <h6 class="font-weight-bold mt-2">কোনো ডিগ্রি যুক্ত করা হয়নি</h6>
                  <button class="btn btn-sm btn-danger rounded-pill px-4 py-2 font-weight-bold mt-2" @click="openAddTimelineModal">ডিগ্রি যোগ করুন</button>
                </div>

                <div v-else class="list-group">
                  <div 
                    v-for="(edu, idx) in portfolioForm.education" 
                    :key="edu.id || idx" 
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-14 mb-2 border shadow-sm bg-white"
                  >
                    <div class="d-flex align-items-center">
                      <span class="badge badge-success px-3 py-2 mr-3 font-weight-bold rounded-pill" style="font-size: 12px;">
                        {{ edu.year }}
                      </span>
                      <div>
                        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 15px;">
                          {{ edu.title }}
                          <span v-if="edu.highlight" class="badge badge-warning text-dark ml-2">প্রধান অর্জন</span>
                        </h6>
                        <small class="text-muted">{{ edu.details }}</small>
                      </div>
                    </div>
                    <div class="d-flex" style="gap: 5px;">
                      <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="openEditTimelineModal(edu, idx)" title="সম্পাদনা">
                        <i class="icofont-edit text-primary"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="deleteTimeline(idx)" title="মুছুন">
                        <i class="icofont-trash text-danger"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ================= 5. HEALTH TIPS SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'tips'">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">💡 স্বাস্থ্য টিপস ও অভিভাবক পরামর্শ (Health Tips)</h5>
                    <p class="text-muted small mb-0">রোগীদের জন্য সচেতনতামূলক পরামর্শ ও আর্টিকেলের পয়েন্ট যোগ করুন।</p>
                  </div>
                  <button class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm" @click="openAddTipModal">
                    <i class="icofont-plus mr-1"></i> নতুন পরামর্শ যোগ করুন
                  </button>
                </div>

                <div v-if="!portfolioForm.tips || portfolioForm.tips.length === 0" class="text-center py-5 bg-light rounded-16 border">
                  <i class="icofont-bulb-alt display-4 text-muted"></i>
                  <h6 class="font-weight-bold mt-2">এখনো কোনো টিপস যুক্ত করা হয়নি</h6>
                  <button class="btn btn-sm btn-danger rounded-pill px-4 py-2 font-weight-bold mt-2" @click="openAddTipModal">টিপস যুক্ত করুন</button>
                </div>

                <div v-else class="row g-3">
                  <div v-for="(tip, idx) in portfolioForm.tips" :key="tip.id || idx" class="col-md-6 mb-3">
                    <div class="card h-100 border rounded-16 p-3 shadow-sm bg-white d-flex flex-column justify-content-between">
                      <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <span class="badge badge-warning text-dark font-weight-bold px-2 py-1 rounded-pill" style="font-size: 11px;">
                            {{ tip.tag || 'পরামর্শ' }}
                          </span>
                          <div class="d-flex" style="gap: 5px;">
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="openEditTipModal(tip, idx)">
                              <i class="icofont-edit text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="deleteTip(idx)">
                              <i class="icofont-trash text-danger"></i>
                            </button>
                          </div>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">{{ tip.title }}</h6>
                        <p class="text-muted small mb-2">{{ tip.excerpt }}</p>
                      </div>
                      <small class="text-success font-weight-bold mt-auto border-top pt-2">বিস্তারিত লেখা সংরক্ষিত</small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ================= 6. TESTIMONIALS SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'testimonials'">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">💬 রোগীর রিভিউ ও মতামত (Testimonials)</h5>
                    <p class="text-muted small mb-0">রোগী ও তাদের অভিভাবকদের কৃতজ্ঞতা বার্তা ও রেটিং প্রদর্শন করুন।</p>
                  </div>
                  <button class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm" @click="openAddTestimonialModal">
                    <i class="icofont-plus mr-1"></i> নতুন মতামত যোগ করুন
                  </button>
                </div>

                <div v-if="!portfolioForm.testimonials || portfolioForm.testimonials.length === 0" class="text-center py-5 bg-light rounded-16 border">
                  <i class="icofont-quote-left display-4 text-muted"></i>
                  <h6 class="font-weight-bold mt-2">কোনো মতামত যুক্ত করা হয়নি</h6>
                  <button class="btn btn-sm btn-danger rounded-pill px-4 py-2 font-weight-bold mt-2" @click="openAddTestimonialModal">মতামত যোগ করুন</button>
                </div>

                <div v-else class="row g-3">
                  <div v-for="(tst, idx) in portfolioForm.testimonials" :key="tst.id || idx" class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 border rounded-16 p-3 shadow-sm bg-white d-flex flex-column justify-content-between">
                      <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="text-warning small">
                            <i v-for="n in (tst.rating || 5)" :key="n" class="icofont-star text-warning"></i>
                          </div>
                          <div class="d-flex" style="gap: 5px;">
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="openEditTestimonialModal(tst, idx)">
                              <i class="icofont-edit text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="deleteTestimonial(idx)">
                              <i class="icofont-trash text-danger"></i>
                            </button>
                          </div>
                        </div>
                        <p class="text-muted small font-italic mb-3">"{{ tst.quote }}"</p>
                      </div>
                      <div class="d-flex align-items-center border-top pt-2">
                        <div class="rounded-circle bg-emerald-soft text-emerald d-flex align-items-center justify-content-center font-weight-bold mr-2" style="width: 34px; height: 34px;">
                          {{ (tst.name || 'র')[0] }}
                        </div>
                        <div>
                          <strong class="text-dark small d-block font-weight-bold">{{ tst.name }}</strong>
                          <small class="text-muted" style="font-size: 11px;">{{ tst.relation || 'রোগীর অভিভাবক' }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ================= 7. FAQS SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'faqs'">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">❓ সাধারণ জিজ্ঞাসা ও উত্তর (FAQs)</h5>
                    <p class="text-muted small mb-0">রোগীরা যেসব বিষয়ে সচরাচর প্রশ্ন করেন তাদের স্পষ্ট উত্তর এখানে সেট করুন।</p>
                  </div>
                  <button class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm" @click="openAddFaqModal">
                    <i class="icofont-plus mr-1"></i> নতুন প্রশ্ন ও উত্তর যোগ করুন
                  </button>
                </div>

                <div v-if="!portfolioForm.faqs || portfolioForm.faqs.length === 0" class="text-center py-5 bg-light rounded-16 border">
                  <i class="icofont-question-circle display-4 text-muted"></i>
                  <h6 class="font-weight-bold mt-2">কোনো প্রশ্নোত্তর যুক্ত করা হয়নি</h6>
                  <button class="btn btn-sm btn-danger rounded-pill px-4 py-2 font-weight-bold mt-2" @click="openAddFaqModal">প্রশ্ন যোগ করুন</button>
                </div>

                <div v-else class="list-group">
                  <div 
                    v-for="(faq, idx) in portfolioForm.faqs" 
                    :key="faq.id || idx" 
                    class="list-group-item rounded-14 mb-2 border shadow-sm bg-white p-3"
                  >
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <h6 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                        <span class="badge badge-light border text-danger mr-2">Q{{ idx + 1 }}</span>
                        {{ faq.question }}
                      </h6>
                      <div class="d-flex" style="gap: 5px;">
                        <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="openEditFaqModal(faq, idx)">
                          <i class="icofont-edit text-primary"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle" @click="deleteFaq(idx)">
                          <i class="icofont-trash text-danger"></i>
                        </button>
                      </div>
                    </div>
                    <p class="text-muted small mb-0 pl-4">{{ faq.answer }}</p>
                  </div>
                </div>
              </div>

              <!-- ================= 8. GALLERY SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'gallery'">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                  <div>
                    <h5 class="font-weight-bold text-dark mb-1">🖼️ ফটো গ্যালারি (Chamber & Clinical Gallery)</h5>
                    <p class="text-muted small mb-0">চেম্বার, সেমিনার বা বিশেষ মুহূর্তের ছবি যুক্ত করুন।</p>
                  </div>
                  <div class="d-flex align-items-center" style="gap: 8px;">
                    <label class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm mb-0 cursor-pointer">
                      <i class="icofont-camera mr-1"></i> ছবি আপলোড করুন
                      <input type="file" class="d-none" accept="image/*" @change="uploadGalleryImageFile">
                    </label>
                    <button class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm" @click="openAddGalleryModal">
                      <i class="icofont-plus mr-1"></i> ছবির লিংক যোগ
                    </button>
                  </div>
                </div>

                <div v-if="!portfolioForm.gallery || portfolioForm.gallery.length === 0" class="text-center py-5 bg-light rounded-16 border">
                  <i class="icofont-image display-4 text-muted"></i>
                  <h6 class="font-weight-bold mt-2">কোনো ছবি যুক্ত করা হয়নি</h6>
                  <button class="btn btn-sm btn-danger rounded-pill px-4 py-2 font-weight-bold mt-2" @click="openAddGalleryModal">ছবি যোগ করুন</button>
                </div>

                <div v-else class="row g-3">
                  <div v-for="(img, idx) in portfolioForm.gallery" :key="img.id || idx" class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="card h-100 border rounded-16 overflow-hidden shadow-sm bg-white">
                      <div class="position-relative" style="aspect-ratio: 1/1; background: #f8fafc;">
                        <img :src="img.image_url" alt="Gallery" class="w-100 h-100" style="object-fit: cover;">
                        <button 
                          type="button" 
                          class="btn btn-sm btn-danger position-absolute rounded-circle shadow" 
                          style="top: 8px; right: 8px; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;"
                          @click="deleteGalleryItem(idx)"
                          title="মুছে ফেলুন"
                        >
                          <i class="icofont-trash" style="font-size: 12px;"></i>
                        </button>
                      </div>
                      <div class="p-2 text-center">
                        <small class="text-muted d-block text-truncate" :title="img.caption">{{ img.caption || 'ক্যাপশন নেই' }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ================= 9. CONTACT & SOCIAL SUB-TAB ================= -->
              <div v-else-if="portfolioSubTab === 'contact'">
                <div class="card border rounded-16 p-3 p-md-4 bg-white shadow-sm">
                  <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                    <i class="icofont-phone text-success mr-1"></i> চেম্বার হটলাইন ও সোশ্যাল মিডিয়া প্রোফাইল লিংক
                  </h5>
                  <p class="text-muted small mb-4">আপনার পার্সোনাল পোর্টফোলিও থেকে রোগীরা সরাসরি কল বা হোয়াটসঅ্যাপে সিরিয়াল যোগাযোগ করতে পারবেন।</p>

                  <div class="row">
                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold small text-dark">সিরিয়াল হেল্পলাইন নম্বর</label>
                      <input type="text" v-model="portfolioForm.contact.phone" class="form-control" placeholder="যেমন: +৮৮০ ১৭০০-০০০০০০">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold small text-dark">চেম্বার সহকারী / স্টাফের নম্বর</label>
                      <input type="text" v-model="portfolioForm.contact.assistant_phone" class="form-control" placeholder="যেমন: +৮৮০ ১৯০০-০০০০০০">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold small text-dark">হোয়াটসঅ্যাপ নম্বর (WhatsApp Number)</label>
                      <input type="text" v-model="portfolioForm.contact.whatsapp" class="form-control" placeholder="যেমন: +8801700000000">
                      <small class="text-muted">রোগীরা সরাসরি ১-ক্লিকে হোয়াটসঅ্যাপ চ্যাট খুলতে পারবেন।</small>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold small text-dark">ইমেইল ঠিকানা (Email)</label>
                      <input type="email" v-model="portfolioForm.contact.email" class="form-control" placeholder="doctor@example.com">
                    </div>

                    <div class="col-12 form-group mb-3">
                      <label class="font-weight-bold small text-dark">চেম্বার / ক্লিনিকে সাক্ষাতের ঠিকানা</label>
                      <input type="text" v-model="portfolioForm.contact.chamber_address" class="form-control" placeholder="রুম নং ৩০২, পপুলার ডায়াগনস্টিক সেন্টার, ধানমন্ডি, ঢাকা">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold small text-dark">
                        <i class="icofont-facebook text-primary mr-1"></i> ফেসবুক পেজ বা প্রোফাইল লিংক
                      </label>
                      <input type="url" v-model="portfolioForm.contact.facebook_url" class="form-control" placeholder="https://facebook.com/dr.username">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                      <label class="font-weight-bold small text-dark">
                        <i class="icofont-youtube text-danger mr-1"></i> ইউটিউব চ্যানেল লিংক
                      </label>
                      <input type="url" v-model="portfolioForm.contact.youtube_url" class="form-control" placeholder="https://youtube.com/@channel">
                    </div>
                  </div>
                </div>
              </div>

              <!-- Prominent Bottom Save Strip -->
              <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                <span class="text-muted small">
                  <i class="icofont-info-circle text-primary mr-1"></i> যেকোনো পরিবর্তন শেষে অবশ্যই <strong>"সব পরিবর্তন সেভ করুন"</strong> বাটনে ক্লিক করে সংরক্ষণ করুন।
                </span>
                <div class="d-flex align-items-center" style="gap: 10px;">
                  <router-link :to="'/dr/' + (doctor?.slug || doctor?.id)" target="_blank" class="btn btn-outline-secondary font-weight-bold rounded-pill px-4">
                    <i class="icofont-eye mr-1"></i> লাইভ দেখুন
                  </router-link>
                  <button 
                    type="button" 
                    class="btn btn-success font-weight-bold px-4 py-2 rounded-pill shadow d-inline-flex align-items-center text-white" 
                    @click="savePortfolio"
                    :disabled="savingPortfolio"
                    style="background: linear-gradient(135deg, #10b981, #059669); border: none;"
                  >
                    <span v-if="savingPortfolio" class="spinner-border spinner-border-sm mr-2"></span>
                    <i v-else class="icofont-save mr-2 font-16"></i>
                    {{ savingPortfolio ? 'সংরক্ষণ হচ্ছে...' : 'সব পরিবর্তন সেভ করুন' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SMART DIGITAL PRESCRIPTION MAKER MODAL (Mobile Friendly + BD Medicines Autocomplete + Fast Dropdowns) -->
    <div 
      v-if="showPrescriptionModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.65); z-index: 1050; overflow-y: auto;"
    >
      <div class="modal-dialog modal-xl modal-dialog-centered my-3">
        <div class="modal-content border-0 shadow-lg rounded-lg">
          <!-- Modal Header -->
          <div class="modal-header bg-primary text-white py-3 px-4">
            <div class="d-flex align-items-center">
              <span class="badge badge-danger mr-2 px-2 py-1" style="font-size: 14px;">Rx</span>
              <h5 class="modal-title font-weight-bold text-white mb-0">Smart Digital Prescription Maker</h5>
            </div>
            <button type="button" class="close text-white" @click="showPrescriptionModal = false">&times;</button>
          </div>

          <div class="modal-body p-3 p-md-4 bg-light">
            <!-- Patient Header Banner -->
            <div class="bg-white p-3 rounded shadow-sm mb-3 border-left border-danger" style="border-left-width: 5px !important;">
              <div class="row align-items-center">
                <div class="col-sm-6">
                  <span class="text-muted small">Patient Name:</span>
                  <h5 class="font-weight-bold text-dark mb-0">{{ prescriptionForm.patient_name }}</h5>
                </div>
                <div class="col-sm-6 text-sm-right mt-2 mt-sm-0 d-flex justify-content-sm-end align-items-center flex-wrap" style="gap: 8px;">
                  <button 
                    v-if="currentApt?.patient_id" 
                    type="button" 
                    class="btn btn-sm btn-danger font-weight-bold shadow-sm"
                    @click="openPatientReportsModal(currentApt)"
                  >
                    <i class="icofont-file-document mr-1"></i> View Test Reports
                  </button>
                  <div>
                    <span class="text-muted small">Appointment Ref:</span>
                    <span class="font-weight-bold text-danger ml-1">{{ currentApt?.appointment_number }}</span>
                  </div>
                </div>
              </div>
            </div>

            <form @submit.prevent="savePrescription">
              <!-- Diagnosis & Demographics -->
              <div class="bg-white p-3 rounded shadow-sm mb-3">
                <div class="row">
                  <!-- Patient Age -->
                  <div class="col-md-3 col-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Age (বয়স)</label>
                    <div class="input-group input-group-sm">
                      <input 
                        type="number" 
                        v-model="prescriptionForm.patient_age" 
                        class="form-control font-weight-bold" 
                        placeholder="32"
                        min="1"
                        max="130"
                        required
                      >
                      <div class="input-group-append">
                        <select v-model="prescriptionForm.patient_age_unit" class="custom-select custom-select-sm border-left-0" style="background-color: #f8fafc; font-size: 11px;">
                          <option value="Yrs">Yrs (বছর)</option>
                          <option value="Months">Months (মাস)</option>
                          <option value="Days">Days (দিন)</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- Patient Gender -->
                  <div class="col-md-3 col-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Gender (লিঙ্গ)</label>
                    <select v-model="prescriptionForm.patient_gender" class="form-control form-control-sm font-weight-bold" required>
                      <option value="Male">Male (পুরুষ)</option>
                      <option value="Female">Female (মহিলা)</option>
                      <option value="Other">Other (অন্যান্য)</option>
                    </select>
                  </div>

                  <!-- Next Follow-up Date -->
                  <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-dark small">Next Follow-up Date (পরবর্তী সাক্ষাতের তারিখ)</label>
                    <input 
                      type="date" 
                      v-model="prescriptionForm.follow_up_date" 
                      class="form-control form-control-sm" 
                      :min="todayDate"
                    >
                  </div>

                  <!-- Clinical Diagnosis -->
                  <div class="col-12 form-group mb-0">
                    <label class="font-weight-bold text-dark small">Clinical Diagnosis (রোগের কারণ / ডায়াগনোসিস) <span class="text-danger">*</span></label>
                    <div class="position-relative">
                      <input 
                        type="text" 
                        v-model="prescriptionForm.diagnosis" 
                        data-no-voice
                        class="form-control" 
                        placeholder="e.g. Acute Bronchitis, Essential Hypertension, Gastric Ulcer..."
                        style="padding-right: 38px;"
                        required
                      >
                      <VoiceInputBtn embedded @result="val => prescriptionForm.diagnosis = val" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Prescribed Medicines Section -->
              <div class="bg-white p-3 rounded shadow-sm mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="font-weight-bold mb-0 text-dark">
                    <span class="badge badge-primary mr-1">Rx</span> Prescribed Medicines
                  </h6>
                  <button type="button" class="btn btn-sm btn-outline-primary" @click="addMedicineRow">
                    <i class="icofont-plus mr-1"></i> Add Medicine
                  </button>
                </div>

                <!-- Column Headers for Medicine Row -->
                <div class="row px-2 mb-1 d-none d-md-flex text-muted small font-weight-bold">
                  <div class="col-md-4">Medicine Name (Search BD Drugs)</div>
                  <div class="col-md-2">Dosage (মাত্রা)</div>
                  <div class="col-md-2">Duration (মেয়াদ)</div>
                  <div class="col-md-3">Instruction (খাওয়ার নিয়ম)</div>
                  <div class="col-md-1 text-right">Action</div>
                </div>

                <div 
                  v-for="(med, idx) in prescriptionForm.medicines" 
                  :key="idx" 
                  class="border p-2 rounded mb-2 bg-light position-relative" 
                  :style="activeMedDropdownIdx === idx ? 'z-index: 1065 !important; position: relative !important; overflow: visible !important;' : 'overflow: visible !important;'"
                >
                  <div class="row align-items-center" :style="activeMedDropdownIdx === idx ? 'z-index: 1065 !important; position: relative !important;' : ''">
                    <div class="col-md-4 mb-2 mb-md-0" :style="activeMedDropdownIdx === idx ? 'z-index: 1070 !important; position: relative !important;' : ''">
                      <div class="position-relative" :style="activeMedDropdownIdx === idx ? 'z-index: 1075 !important;' : ''">
                        <input 
                          type="text" 
                          v-model="med.name" 
                          data-no-voice
                          class="form-control form-control-sm font-weight-bold pr-4" 
                          placeholder="Search Medicine (e.g. Napa, Rupa, Etorix...)"
                          @input="onSearchMedicine(idx, $event.target.value)"
                          @focus="onFocusMedicine(idx)"
                          required
                          autocomplete="off"
                        >
                        <!-- Quick Search/Dropdown Trigger Icon -->
                        <span 
                          class="position-absolute cursor-pointer text-muted" 
                          style="right: 8px; top: 50%; transform: translateY(-50%); font-size: 14px; z-index: 5;"
                          @click.stop="toggleMedicineDropdown(idx)"
                          title="সব ঔষধের তালিকা দেখতে ক্লিক করুন"
                        >
                          <i :class="activeMedDropdownIdx === idx ? 'icofont-close-line text-danger font-weight-bold' : 'icofont-search-2 text-primary'"></i>
                        </span>

                        <!-- Autocomplete Suggestions Dropdown (Directly Anchored Under Input) -->
                        <div 
                          v-if="activeMedDropdownIdx === idx" 
                          class="medicine-autocomplete-dropdown shadow-lg rounded border bg-white position-absolute"
                          style="z-index: 99999 !important; max-height: 250px; overflow-y: auto; left: 0; right: 0; top: 100% !important; margin-top: 4px; width: 100%; box-shadow: 0 12px 36px rgba(0,0,0,0.3) !important;"
                          @pointerdown.stop
                          @click.stop
                        >
                          <div class="p-2 border-bottom bg-light d-flex justify-content-between align-items-center">
                            <small class="font-weight-bold text-dark d-flex align-items-center">
                              <i class="icofont-prescription mr-1 text-primary"></i> 
                              <span>জাতীয় ড্রাগ তালিকা ({{ medSuggestions ? medSuggestions.length : 0 }})</span>
                            </small>
                            <button 
                              type="button" 
                              class="btn btn-sm btn-outline-danger py-0 px-2 font-weight-bold" 
                              style="font-size: 11px;"
                              @click="activeMedDropdownIdx = null"
                            >
                              ✕ বন্ধ
                            </button>
                          </div>

                          <div v-if="searchingMed" class="p-2 text-center text-muted small">
                            <span class="spinner-border spinner-border-sm text-danger mr-1" role="status"></span> Searching BD Medicines...
                          </div>

                          <div 
                            v-else-if="medSuggestions && medSuggestions.length > 0"
                            v-for="sug in medSuggestions" 
                            :key="sug.id || sug.brand_name" 
                            class="p-2 border-bottom cursor-pointer hover-bg-light small"
                            @pointerdown.prevent="selectMedicineSuggestion(idx, sug)"
                            @click.prevent="selectMedicineSuggestion(idx, sug)"
                          >
                            <div class="d-flex justify-content-between align-items-center">
                              <strong class="text-primary">{{ sug.dosage_form }}. {{ sug.brand_name }}</strong>
                              <span class="badge badge-light border text-muted">{{ sug.company }}</span>
                            </div>
                            <span class="text-muted" style="font-size: 11px;">Generic: {{ sug.generic_name }}</span>
                          </div>

                          <div v-else class="p-2 text-center text-muted small">
                            <span>কোনো ঔষধ পাওয়া যায়নি (কাস্টম নাম হিসেবে সেভ হবে)</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Dosage Select Dropdown -->
                    <div class="col-6 col-md-2 mb-2 mb-md-0">
                      <label class="d-md-none text-muted small mb-1">Dosage (মাত্রা)</label>
                      <select 
                        v-model="med.dosage" 
                        class="custom-select custom-select-sm font-weight-bold" 
                        required
                      >
                        <option v-for="d in presets.dosages" :key="d.value" :value="d.value">
                          {{ d.label }}
                        </option>
                      </select>
                    </div>

                    <!-- Duration Select Dropdown -->
                    <div class="col-6 col-md-2 mb-2 mb-md-0">
                      <label class="d-md-none text-muted small mb-1">Duration (মেয়াদ)</label>
                      <select 
                        v-model="med.duration" 
                        class="custom-select custom-select-sm font-weight-bold" 
                        required
                      >
                        <option v-for="dur in presets.durations" :key="dur.value" :value="dur.value">
                          {{ dur.label }}
                        </option>
                      </select>
                    </div>

                    <!-- Instruction Select Dropdown -->
                    <div class="col-9 col-md-3 mb-2 mb-md-0">
                      <label class="d-md-none text-muted small mb-1">Instruction (নিয়ম)</label>
                      <select 
                        v-model="med.instruction" 
                        class="custom-select custom-select-sm font-weight-bold"
                      >
                        <option v-for="ins in presets.instructions" :key="ins.value" :value="ins.value">
                          {{ ins.label }}
                        </option>
                      </select>
                    </div>

                    <div class="col-3 col-md-1 text-right d-flex align-items-center justify-content-end">
                      <button 
                        type="button" 
                        class="btn btn-sm btn-outline-danger px-2 py-1 font-weight-bold" 
                        @click="removeMedicineRow(idx)"
                        :disabled="prescriptionForm.medicines.length === 1"
                        title="Remove Medicine"
                      >
                        <i class="icofont-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ================= SELECTABLE DIAGNOSTIC TESTS ================= -->
              <div class="bg-white p-3 p-md-4 rounded shadow-sm mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                    <i class="icofont-laboratory text-primary mr-2" style="font-size: 18px;"></i>
                    Diagnostic Medical Tests (ডায়াগনস্টিক টেস্ট)
                  </h6>
                  <span class="badge badge-primary px-2 py-1" style="border-radius: 12px; font-size: 12px;">
                    {{ selectedTests.length }} Selected
                  </span>
                </div>

                <!-- Active Selected Tests Highlight Bar (If Any) -->
                <div v-if="selectedTests.length > 0" class="p-2 mb-3 rounded" style="background-color: #f0f7ff; border: 1px dashed #b8daff;">
                  <span class="small text-muted font-weight-bold mr-2 d-block d-sm-inline mb-1 mb-sm-0">Selected for Rx:</span>
                  <span 
                    v-for="test in selectedTests" 
                    :key="'sel-' + test" 
                    class="badge badge-primary mr-1 mb-1 py-1 px-2 cursor-pointer"
                    style="font-size: 12px; border-radius: 12px; background-color: #223a66;"
                    @click="toggleTest(test)"
                    title="Click to remove"
                  >
                    {{ test }} &times;
                  </span>
                </div>

                <!-- Available Tests Clean Pill Cloud -->
                <div class="d-flex flex-wrap align-items-center mb-3">
                  <div 
                    v-for="test in allCommonTests" 
                    :key="test"
                    class="test-pill-item"
                    :class="{ 'active': selectedTests.includes(test) }"
                    @click="toggleTest(test)"
                  >
                    <i v-if="selectedTests.includes(test)" class="icofont-check mr-1 text-white"></i>
                    <i v-else class="icofont-plus mr-1 text-muted"></i>
                    {{ test }}
                  </div>
                </div>

                <!-- Add Custom Test Input -->
                <div class="input-group input-group-sm">
                  <input 
                    type="text" 
                    v-model="customTestInput" 
                    class="form-control" 
                    placeholder="Type other diagnostic test (e.g. Holter Monitor, DEXA Scan)..."
                    @keyup.enter="addCustomTest"
                    style="height: 38px; border-radius: 6px 0 0 6px;"
                  >
                  <div class="input-group-append">
                    <button class="btn btn-outline-primary px-3 font-weight-bold" type="button" @click="addCustomTest">
                      + Add Test
                    </button>
                  </div>
                </div>
              </div>

              <!-- ================= SELECTABLE DIETARY & LIFESTYLE ADVICE (2-Column Card Tiles) ================= -->
              <div class="bg-white p-3 p-md-4 rounded shadow-sm mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                    <i class="icofont-heart-beat-alt text-success mr-2" style="font-size: 18px;"></i>
                    Dietary & Lifestyle Advice (খাদ্য ও জীবনযাত্রা সংক্রান্ত পরামর্শ)
                  </h6>
                  <span class="badge badge-success px-2 py-1" style="border-radius: 12px; font-size: 12px;">
                    {{ selectedAdvices.length }} Selected
                  </span>
                </div>

                <!-- 2-Column Clean Advice Card Tiles -->
                <div class="row">
                  <div 
                    v-for="adv in allCommonAdvices" 
                    :key="adv.text" 
                    class="col-md-6 mb-2"
                  >
                    <div 
                      class="advice-card-tile h-100"
                      :class="{ 'active': selectedAdvices.includes(adv.text) }"
                      @click="toggleAdvice(adv.text)"
                    >
                      <div class="mr-2">
                        <i v-if="selectedAdvices.includes(adv.text)" class="icofont-check-circled text-success" style="font-size: 20px;"></i>
                        <i v-else class="icofont-circle text-muted" style="font-size: 20px; opacity: 0.5;"></i>
                      </div>
                      <div class="flex-grow-1 font-weight-bold" style="font-size: 13px; line-height: 1.4;">
                        {{ adv.text }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Add Custom Advice Input -->
                <div class="input-group input-group-sm mt-3">
                  <div class="position-relative flex-grow-1">
                    <input 
                      type="text" 
                      v-model="customAdviceInput" 
                      data-no-voice
                      class="form-control" 
                      placeholder="Type or speak custom doctor advice note..."
                      @keyup.enter="addCustomAdvice"
                      style="height: 38px; border-radius: 6px 0 0 6px; padding-right: 36px;"
                    >
                    <VoiceInputBtn embedded @result="val => customAdviceInput = val" />
                  </div>
                  <div class="input-group-append">
                    <button class="btn btn-outline-success px-3 font-weight-bold" type="button" @click="addCustomAdvice">
                      + Add Advice
                    </button>
                  </div>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-secondary mr-2" @click="showPrescriptionModal = false">Cancel</button>
                <button type="submit" class="btn btn-main-2 px-4 font-weight-bold" :disabled="savingPrescription">
                  <span v-if="savingPrescription">
                    <span class="spinner-border spinner-border-sm mr-1"></span> Issuing...
                  </span>
                  <span v-else>
                    <i class="icofont-check mr-1"></i> Issue Digital Prescription
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Doctor Patient Reports Modal -->
    <div 
      v-if="showPatientReportsModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.75); z-index: 1060; overflow-y: auto;"
    >
      <div class="modal-dialog modal-xl modal-dialog-centered my-3">
        <div class="modal-content border-0 shadow-lg rounded-lg">
          <div class="modal-header bg-dark text-white py-3 px-4">
            <div class="d-flex align-items-center">
              <i class="icofont-file-document text-danger mr-2" style="font-size: 24px;"></i>
              <div>
                <h5 class="modal-title font-weight-bold text-white mb-0">Patient Medical & Lab Reports</h5>
                <small class="text-light">Patient: <strong>{{ doctorSelectedPatient?.patient_name }}</strong> ({{ doctorPatientReportsList.length }} Reports Found)</small>
              </div>
            </div>
            <button type="button" class="close text-white" @click="showPatientReportsModal = false">&times;</button>
          </div>

          <div class="modal-body p-3 p-md-4 bg-light">
            <div v-if="loadingDoctorReports" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
              <p class="text-muted small mt-2">Loading patient reports...</p>
            </div>

            <div v-else-if="doctorPatientReportsList.length === 0" class="text-center py-5 bg-white rounded border">
              <i class="icofont-file-document display-4 text-muted"></i>
              <h5 class="mt-3 font-weight-bold text-dark">No Reports Uploaded by Patient</h5>
              <p class="text-muted small">This patient has not uploaded any test photos or lab reports yet.</p>
            </div>

            <div v-else class="row">
              <div v-for="rep in doctorPatientReportsList" :key="rep.id" class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border rounded shadow-sm overflow-hidden bg-white">
                  <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-start">
                    <div>
                      <span class="badge badge-primary px-2 py-1 small font-weight-bold mb-1">{{ rep.test_type_label }}</span>
                      <h6 class="font-weight-bold text-dark mb-0 text-truncate" style="max-width: 180px;">{{ rep.report_title }}</h6>
                    </div>
                    <span class="badge" :class="rep.status === 'reviewed' ? 'badge-success' : 'badge-warning text-dark'" style="font-size: 10px;">
                      {{ rep.status === 'reviewed' ? 'Reviewed' : 'Uploaded' }}
                    </span>
                  </div>

                  <div class="position-relative bg-dark cursor-pointer text-center" style="height: 180px; overflow: hidden;" @click="viewDoctorReportDetail(rep)">
                    <img v-if="rep.file_type === 'image'" :src="rep.file_url" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Report Preview">
                    <div v-else class="d-flex flex-column align-items-center justify-content-center h-100 text-white bg-secondary p-3">
                      <i class="icofont-file-pdf display-4 text-danger mb-1"></i>
                      <span class="font-weight-bold small">PDF Document ({{ rep.file_size }})</span>
                    </div>
                  </div>

                  <div class="p-3 small">
                    <div class="d-flex justify-content-between text-muted mb-1">
                      <span><strong>Date:</strong> {{ formatDate(rep.report_date) }}</span>
                      <span><strong>Size:</strong> {{ rep.file_size }}</span>
                    </div>
                    <div v-if="rep.diagnostic_center" class="text-muted mb-2">
                      <strong>Lab:</strong> {{ rep.diagnostic_center }}
                    </div>
                    <div v-if="rep.patient_notes" class="bg-light p-2 rounded mb-2 text-dark">
                      <strong>Patient Note:</strong> {{ rep.patient_notes }}
                    </div>

                    <!-- Doctor Remarks Section -->
                    <div class="border-top pt-2 mt-2">
                      <label class="font-weight-bold text-dark mb-1">Doctor Remarks / Review Note:</label>
                      <div class="input-group input-group-sm">
                        <input 
                          type="text" 
                          v-model="rep.doctor_remarks" 
                          class="form-control" 
                          placeholder="e.g. Normal Report, Continue Rx..."
                        >
                        <div class="input-group-append">
                          <button class="btn btn-outline-success font-weight-bold" type="button" @click="saveDoctorRemarks(rep)">
                            Save
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="p-2 border-top bg-light d-flex justify-content-between">
                    <button class="btn btn-sm btn-outline-primary py-1 px-2 font-weight-bold" @click="viewDoctorReportDetail(rep)">
                      <i class="icofont-eye mr-1"></i> Fullscreen View
                    </button>
                    <a :href="rep.file_url" target="_blank" download class="btn btn-sm btn-outline-dark py-1 px-2 font-weight-bold">
                      <i class="icofont-download mr-1"></i> Download
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-white py-2">
            <button type="button" class="btn btn-secondary btn-sm" @click="showPatientReportsModal = false">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Doctor Lightbox Modal for Full View -->
    <div v-if="showDoctorLightbox" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.88); z-index: 1070;">
      <div class="bg-white rounded-lg shadow-2xl d-flex flex-column" style="max-width: 900px; width: 95%; height: 90vh; overflow: hidden;">
        <div class="p-3 bg-dark text-white d-flex justify-content-between align-items-center">
          <div>
            <h6 class="font-weight-bold mb-0 text-white">{{ doctorActiveReport?.report_title }}</h6>
            <small class="text-light">{{ doctorActiveReport?.test_type_label }} &bull; {{ formatDate(doctorActiveReport?.report_date) }}</small>
          </div>
          <div class="d-flex align-items-center" style="gap: 6px;">
            <a :href="doctorActiveReport?.file_url" target="_blank" download class="btn btn-sm btn-success py-1 px-2 font-weight-bold">
              <i class="icofont-download mr-1"></i> Download
            </a>
            <button type="button" class="btn btn-sm btn-danger py-1 px-2 font-weight-bold ml-2" @click="showDoctorLightbox = false">&times; Close</button>
          </div>
        </div>
        <div class="flex-grow-1 bg-dark d-flex align-items-center justify-content-center overflow-auto p-2">
          <img v-if="doctorActiveReport?.file_type === 'image'" :src="doctorActiveReport.file_url" class="img-fluid rounded" style="max-height: 80vh; object-fit: contain;">
          <iframe v-else :src="doctorActiveReport?.file_url" class="w-100 h-100 border-0 rounded"></iframe>
        </div>
      </div>
    </div>

    <!-- Patient Chronic Disease & Vitals Modal for Doctor -->
    <div v-if="showPatientMetricsModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.75); z-index: 1070; overflow-y: auto;">
      <div class="bg-white rounded-lg shadow-lg p-4" style="max-width: 700px; width: 94%; max-height: 85vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <h5 class="font-weight-bold text-dark mb-0">
            <i class="icofont-heart-beat text-danger mr-2"></i> রোগীর ডায়াবেটিস ও রক্তচাপ (BP) হিস্ট্রি
          </h5>
          <button type="button" class="close text-muted" @click="showPatientMetricsModal = false">&times;</button>
        </div>

        <div v-if="!patientMetricsData || (!patientMetricsData.blood_sugar.length && !patientMetricsData.blood_pressure.length)" class="text-center py-4 text-muted">
          <p>এই রোগীর কোনো পূর্বের সুগার বা প্রেশারের রিডিং সেভ করা নেই।</p>
        </div>

        <div v-else>
          <!-- Sugar Readings -->
          <h6 class="font-weight-bold text-dark mb-2">🩸 ব্লাড সুগার রিডিংস (Fasting / After Meal)</h6>
          <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered small">
              <thead class="bg-light">
                <tr><th>তারিখ</th><th>মান (mmol/L)</th><th>সময়</th><th>নোট</th></tr>
              </thead>
              <tbody>
                <tr v-for="s in patientMetricsData.blood_sugar" :key="s.id">
                  <td>{{ formatDate(s.recorded_date) }}</td>
                  <td class="font-weight-bold" :class="s.value_primary > 10 ? 'text-danger' : 'text-success'">{{ s.value_primary }}</td>
                  <td>{{ s.measured_time || '-' }}</td>
                  <td>{{ s.notes || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- BP Readings -->
          <h6 class="font-weight-bold text-dark mb-2">💓 রক্তচাপ রিডিংস (BP mmHg)</h6>
          <div class="table-responsive">
            <table class="table table-sm table-bordered small">
              <thead class="bg-light">
                <tr><th>তারিখ</th><th>রক্তচাপ (Systolic/Diastolic)</th><th>নোট</th></tr>
              </thead>
              <tbody>
                <tr v-for="bp in patientMetricsData.blood_pressure" :key="bp.id">
                  <td>{{ formatDate(bp.recorded_date) }}</td>
                  <td class="font-weight-bold" :class="bp.value_primary > 140 ? 'text-danger' : 'text-success'">{{ bp.value_primary }} / {{ bp.value_secondary }}</td>
                  <td>{{ bp.notes || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="text-right border-top pt-2 mt-3">
          <button type="button" class="btn btn-secondary btn-sm" @click="showPatientMetricsModal = false">বন্ধ করুন</button>
        </div>
      </div>
    </div>

    <!-- Add Assistant Modal -->
    <div v-if="showAddAssistantModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1060;">
      <div class="bg-white rounded shadow-lg p-4" style="max-width: 450px; width: 92%;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <h5 class="font-weight-bold text-dark mb-0">🧑‍⚕️ নতুন সহকারী / কম্পাউন্ডার যুক্ত করুন</h5>
          <button type="button" class="close text-muted" @click="showAddAssistantModal = false">&times;</button>
        </div>
        <form @submit.prevent="submitAddAssistant">
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">সহকারীর নাম <span class="text-danger">*</span></label>
            <input type="text" v-model="assistantForm.name" class="form-control form-control-sm" placeholder="যেমন: মো: রফিক উদ্দিন" required>
          </div>
          <div class="form-group mb-2">
            <label class="small font-weight-bold text-dark">মোবাইল নম্বর <span class="text-danger">*</span></label>
            <input type="tel" v-model="assistantForm.phone" class="form-control form-control-sm" placeholder="01700-000000" required>
          </div>
          <div class="form-group mb-3">
            <label class="small font-weight-bold text-dark">লগইন পিন কোড (৪-৬ সংখ্যার) <span class="text-danger">*</span></label>
            <input type="text" v-model="assistantForm.access_pin" class="form-control form-control-sm" placeholder="e.g. 123456" required minlength="4" maxlength="6">
          </div>
          <div class="d-flex justify-content-end border-top pt-2">
            <button type="button" class="btn btn-light btn-sm mr-2" @click="showAddAssistantModal = false">বাতিল</button>
            <button type="submit" class="btn btn-danger btn-sm px-3 font-weight-bold">সেভ করুন</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ADD / EDIT CHAMBER MODAL -->
    <div v-if="showChamberModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1060; overflow-y: auto; padding: 20px 10px;">
      <div class="bg-white rounded-16 shadow-lg p-4 my-auto" style="max-width: 620px; width: 100%;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <h5 class="font-weight-bold text-dark mb-0">
            <i class="icofont-hospital text-danger mr-2"></i>
            {{ editingChamberId ? 'চেম্বার তথ্য এডিট করুন' : 'নতুন চেম্বার যুক্ত করুন' }}
          </h5>
          <button type="button" class="close text-muted cursor-pointer" @click="showChamberModal = false">&times;</button>
        </div>

        <form @submit.prevent="saveChamber">
          <div class="row">
            <div class="col-md-7 form-group mb-3">
              <label class="font-weight-bold text-dark small">হাসপাতাল / চেম্বারের নাম <span class="text-danger">*</span></label>
              <input type="text" v-model="chamberForm.chamber_name" class="form-control" placeholder="যেমন: পপুলার ডায়াগনস্টিক সেন্টার" required>
            </div>
            <div class="col-md-5 form-group mb-3">
              <label class="font-weight-bold text-dark small">শাখা / এরিয়া (Branch)</label>
              <input type="text" v-model="chamberForm.branch_name" class="form-control" placeholder="যেমন: ধানমন্ডি শাখা / মিরপুর">
            </div>

            <div class="col-12 form-group mb-3">
              <label class="font-weight-bold text-dark small">বিস্তারিত চেম্বার ঠিকানা <span class="text-danger">*</span></label>
              <textarea v-model="chamberForm.address" rows="2" class="form-control" placeholder="বাড়ি ১৬, রোড ২, ধানমন্ডি, ঢাকা..." required></textarea>
            </div>

            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">জেলা (District)</label>
              <input type="text" v-model="chamberForm.district" class="form-control" placeholder="যেমন: Dhaka">
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">উপজেলা / থানা</label>
              <input type="text" v-model="chamberForm.upazila" class="form-control" placeholder="যেমন: ধানমন্ডি">
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">সিরিয়াল হেল্পলাইন ফোন</label>
              <input type="tel" v-model="chamberForm.phone" class="form-control" placeholder="01700-000000">
            </div>

            <!-- Visiting Days Checkboxes -->
            <div class="col-12 form-group mb-3">
              <label class="font-weight-bold text-dark small d-block mb-2">সপ্তাহের বসার দিনসমূহ (Visiting Days):</label>
              <div class="d-flex flex-wrap" style="gap: 8px;">
                <label 
                  v-for="day in allDays" 
                  :key="day" 
                  class="btn btn-sm cursor-pointer rounded-pill px-3 py-1 font-weight-bold mb-0"
                  :class="chamberForm.visiting_days.includes(day) ? 'btn-danger text-white' : 'btn-light border text-dark'"
                >
                  <input 
                    type="checkbox" 
                    :value="day" 
                    v-model="chamberForm.visiting_days" 
                    class="d-none"
                  >
                  {{ getDayBangla(day) }}
                </label>
              </div>
            </div>

            <!-- Start Time & End Time -->
            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">বসার শুরু (Start Time)</label>
              <input type="time" v-model="chamberForm.start_time" class="form-control" required>
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">বসার সমাপ্তি (End Time)</label>
              <input type="time" v-model="chamberForm.end_time" class="form-control" required>
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="font-weight-bold text-dark small">স্লট মিনিট (প্রতি রোগী)</label>
              <input type="number" v-model.number="chamberForm.slot_duration_minutes" class="form-control" min="5" max="120" placeholder="20">
            </div>

            <!-- Consultation Fee & Primary -->
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold text-dark small">পরামর্শ ফি (৳ BDT)</label>
              <input type="number" v-model.number="chamberForm.consultation_fee" class="form-control font-weight-bold text-success" min="0" placeholder="500">
            </div>
            <div class="col-md-6 form-group mb-3 d-flex align-items-center pt-3">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" v-model="chamberForm.is_primary" class="custom-control-input" id="isPrimaryChamberCheckbox">
                <label class="custom-control-label font-weight-bold text-dark small cursor-pointer" for="isPrimaryChamberCheckbox">
                  ⭐ প্রধান চেম্বার হিসেবে নির্ধারণ করুন
                </label>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end border-top pt-3" style="gap: 8px;">
            <button type="button" class="btn btn-light px-4 rounded-pill font-weight-bold" @click="showChamberModal = false">বাতিল</button>
            <button type="submit" class="btn btn-danger px-4 rounded-pill font-weight-bold" :disabled="savingChamber">
              <span v-if="savingChamber"><span class="spinner-border spinner-border-sm mr-1"></span> সেভ হচ্ছে...</span>
              <span v-else><i class="icofont-check mr-1"></i> {{ editingChamberId ? 'আপডেট করুন' : 'যোগ করুন' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Dedicated Prescription Print Modal -->
    <PrescriptionPrintModal 
      :show="showPrintModal" 
      :prescription="activePrintPrescription" 
      :doctor="doctor" 
      @close="showPrintModal = false" 
    />

    <!-- ================= PORTFOLIO: VIDEO REEL MODAL ================= -->
    <div 
      v-if="showPortfolioVideoModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.7); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-20 shadow-lg overflow-hidden">
          <div class="modal-header bg-dark text-white py-3 px-4">
            <h6 class="modal-title font-weight-bold mb-0">
              <i class="icofont-video-alt text-danger mr-1"></i>
              {{ editingVideoIndex !== null ? 'শর্ট ভিডিও সম্পাদনা' : 'নতুন শর্ট ভিডিও ও রিলস যোগ' }}
            </h6>
            <button type="button" class="close text-white" @click="showPortfolioVideoModal = false">&times;</button>
          </div>
          <form @submit.prevent="saveVideoModal">
            <div class="modal-body p-4">
              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">ভিডিওর শিরোনাম <span class="text-danger">*</span></label>
                <input type="text" v-model="videoModalForm.title" class="form-control" placeholder="যেমন: শিশুর জ্বর হলে করণীয়" required>
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">ভিডিও লিংক (URL) বা ফাইল <span class="text-danger">*</span></label>
                <input type="text" v-model="videoModalForm.video_url" class="form-control mb-1" placeholder="https://youtube.com/shorts/... অথবা MP4 লিংক" required>
                <small class="text-muted d-block mb-2">ইউটিউব শর্টস (YouTube Shorts), ফেসবুক রিলস বা যেকোনো এমপি৪ লিঙ্ক সমর্থন করে।</small>
                
                <!-- Or Upload Video File directly -->
                <div class="p-2 bg-light rounded border">
                  <label class="font-weight-bold small text-primary mb-1 d-block cursor-pointer">
                    <i class="icofont-upload-alt mr-1"></i> অথবা সরাসরি কম্পিউটার / মোবাইল থেকে ভিডিও আপলোড করুন:
                  </label>
                  <input type="file" class="form-control-file" accept="video/mp4,video/webm" @change="uploadVideoModalFile">
                  <div v-if="uploadingPortfolioVideo" class="text-primary small font-weight-bold mt-1">
                    <span class="spinner-border spinner-border-sm mr-1"></span> আপলোড হচ্ছে...
                  </div>
                </div>
              </div>

              <!-- Thumbnail & Duration -->
              <div class="row">
                <div class="col-md-7 form-group mb-3">
                  <label class="font-weight-bold small text-dark">থাম্বনেইল কভার ছবি (Thumbnail URL)</label>
                  <input type="text" v-model="videoModalForm.thumbnail_url" class="form-control" placeholder="https://...jpg">
                  <label class="small text-primary cursor-pointer mt-1 d-inline-block">
                    <i class="icofont-camera mr-1"></i> থাম্বনেইল ছবি আপলোড
                    <input type="file" class="d-none" accept="image/*" @change="uploadThumbnailModalFile">
                  </label>
                </div>
                <div class="col-md-5 form-group mb-3">
                  <label class="font-weight-bold small text-dark">সময়সীমা (Duration)</label>
                  <input type="text" v-model="videoModalForm.duration" class="form-control" placeholder="0:45">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group mb-3">
                  <label class="font-weight-bold small text-dark">ক্যাটাগরি / ট্যাগ</label>
                  <input type="text" v-model="videoModalForm.tag" class="form-control" placeholder="যেমন: স্বাস্থ্য টিপস">
                </div>
                <div class="col-md-6 form-group mb-3">
                  <label class="font-weight-bold small text-dark">ভিউ সংখ্যা</label>
                  <input type="text" v-model="videoModalForm.views" class="form-control" placeholder="যেমন: 1.5K">
                </div>
              </div>

              <div class="form-group mb-0">
                <label class="font-weight-bold small text-dark">সংক্ষিপ্ত বিবরণ</label>
                <textarea v-model="videoModalForm.description" class="form-control" rows="2" placeholder="ভিডিওটি সম্পর্কে সংক্ষিপ্ত তথ্য..."></textarea>
              </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-end" style="gap: 8px;">
              <button type="button" class="btn btn-light rounded-pill px-3" @click="showPortfolioVideoModal = false">বাতিল</button>
              <button type="submit" class="btn btn-danger rounded-pill px-4 font-weight-bold">
                {{ editingVideoIndex !== null ? 'আপডেট করুন' : 'যোগ করুন' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ================= PORTFOLIO: SERVICE MODAL ================= -->
    <div 
      v-if="showPortfolioServiceModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.7); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-20 shadow-lg overflow-hidden">
          <div class="modal-header bg-dark text-white py-3 px-4">
            <h6 class="modal-title font-weight-bold mb-0">
              <i class="icofont-stethoscope text-success mr-1"></i>
              {{ editingServiceIndex !== null ? 'চিকিৎসা সেবা সম্পাদনা' : 'নতুন চিকিৎসা সেবা যোগ' }}
            </h6>
            <button type="button" class="close text-white" @click="showPortfolioServiceModal = false">&times;</button>
          </div>
          <form @submit.prevent="saveServiceModal">
            <div class="modal-body p-4">
              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">সেবার নাম / শিরোনাম <span class="text-danger">*</span></label>
                <input type="text" v-model="serviceModalForm.title" class="form-control" placeholder="যেমন: নবজাতক ও শিশু বিশেষজ্ঞ পরামর্শ" required>
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">আইকন ক্লাস (Icon)</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light"><i :class="serviceModalForm.icon || 'icofont-stethoscope'"></i></span>
                  </div>
                  <input type="text" v-model="serviceModalForm.icon" class="form-control" placeholder="icofont-stethoscope">
                </div>
                <div class="d-flex flex-wrap mt-2" style="gap: 5px;">
                  <span 
                    v-for="ico in ['icofont-stethoscope', 'icofont-heart-beat-alt', 'icofont-baby', 'icofont-brain-alt', 'icofont-capsule', 'icofont-hospital', 'icofont-crutch', 'icofont-eye-alt']" 
                    :key="ico"
                    class="badge badge-light border cursor-pointer p-2"
                    :class="{ 'badge-success text-white': serviceModalForm.icon === ico }"
                    @click="serviceModalForm.icon = ico"
                  >
                    <i :class="ico"></i>
                  </span>
                </div>
              </div>

              <div class="form-group mb-0">
                <label class="font-weight-bold small text-dark">সেবার বিবরণ <span class="text-danger">*</span></label>
                <textarea v-model="serviceModalForm.description" class="form-control" rows="3" placeholder="রোগীর উপসর্গ অনুযায়ী সঠিক চিকিৎসা ও নিয়মিত ফলোআপ সেবা প্রদান করা হয়।" required></textarea>
              </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-end" style="gap: 8px;">
              <button type="button" class="btn btn-light rounded-pill px-3" @click="showPortfolioServiceModal = false">বাতিল</button>
              <button type="submit" class="btn btn-success rounded-pill px-4 font-weight-bold">
                {{ editingServiceIndex !== null ? 'আপডেট করুন' : 'যোগ করুন' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ================= PORTFOLIO: EDUCATION TIMELINE MODAL ================= -->
    <div 
      v-if="showPortfolioTimelineModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.7); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-20 shadow-lg overflow-hidden">
          <div class="modal-header bg-dark text-white py-3 px-4">
            <h6 class="modal-title font-weight-bold mb-0">
              <i class="icofont-graduate-alt text-warning mr-1"></i>
              {{ editingTimelineIndex !== null ? 'ডিগ্রি / অভিজ্ঞতা সম্পাদনা' : 'নতুন ডিগ্রি বা কর্মঅভিজ্ঞতা যোগ' }}
            </h6>
            <button type="button" class="close text-white" @click="showPortfolioTimelineModal = false">&times;</button>
          </div>
          <form @submit.prevent="saveTimelineModal">
            <div class="modal-body p-4">
              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">সাল বা সময়কাল <span class="text-danger">*</span></label>
                <input type="text" v-model="timelineModalForm.year" class="form-control" placeholder="যেমন: ২০১৮ - বর্তমান অথবা ২০১২" required>
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">ডিগ্রি / পদবি / ফেলোশিপ <span class="text-danger">*</span></label>
                <input type="text" v-model="timelineModalForm.title" class="form-control" placeholder="যেমন: এমডি (শিশু স্বাস্থ্য) / কনসালটেন্ট" required>
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">প্রতিষ্ঠান ও বিস্তারিত তথ্য <span class="text-danger">*</span></label>
                <input type="text" v-model="timelineModalForm.details" class="form-control" placeholder="যেমন: বঙ্গবন্ধু শেখ মুজিব মেডিকেল বিশ্ববিদ্যালয় (BSMMU)" required>
              </div>

              <div class="form-check">
                <input type="checkbox" v-model="timelineModalForm.highlight" class="form-check-input" id="timelineHighlightCheck">
                <label class="form-check-label small font-weight-bold text-dark cursor-pointer" for="timelineHighlightCheck">
                  ⭐ প্রধান অর্জন হিসেবে হাইলাইট করুন
                </label>
              </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-end" style="gap: 8px;">
              <button type="button" class="btn btn-light rounded-pill px-3" @click="showPortfolioTimelineModal = false">বাতিল</button>
              <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 font-weight-bold">
                {{ editingTimelineIndex !== null ? 'আপডেট করুন' : 'যোগ করুন' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ================= PORTFOLIO: TIP MODAL ================= -->
    <div 
      v-if="showPortfolioTipModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.7); z-index: 1055;"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-20 shadow-lg overflow-hidden">
          <div class="modal-header bg-dark text-white py-3 px-4">
            <h6 class="modal-title font-weight-bold mb-0">
              <i class="icofont-bulb-alt text-warning mr-1"></i>
              {{ editingTipIndex !== null ? 'স্বাস্থ্য টিপস সম্পাদনা' : 'নতুন স্বাস্থ্য পরামর্শ যোগ' }}
            </h6>
            <button type="button" class="close text-white" @click="showPortfolioTipModal = false">&times;</button>
          </div>
          <form @submit.prevent="saveTipModal">
            <div class="modal-body p-4">
              <div class="row">
                <div class="col-md-4 form-group mb-3">
                  <label class="font-weight-bold small text-dark">ক্যাটাগরি ট্যাগ <span class="text-danger">*</span></label>
                  <input type="text" v-model="tipModalForm.tag" class="form-control" placeholder="যেমন: নবজাতক যত্ন" required>
                </div>
                <div class="col-md-8 form-group mb-3">
                  <label class="font-weight-bold small text-dark">পরামর্শের শিরোনাম <span class="text-danger">*</span></label>
                  <input type="text" v-model="tipModalForm.title" class="form-control" placeholder="যেমন: শীতে শিশুর ঠান্ডাজনিত রোগ প্রতিরোধে করণীয়" required>
                </div>
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">সংক্ষিপ্ত ভূমিকা (Excerpt) <span class="text-danger">*</span></label>
                <input type="text" v-model="tipModalForm.excerpt" class="form-control" placeholder="শীতের শুরুতে শিশুদের নিউমোনিয়া ও ব্রঙ্কিওলাইটিস থেকে বাঁচাতে এই সতর্কতাগুলো মেনে চলুন।" required>
              </div>

              <div class="form-group mb-0">
                <label class="font-weight-bold small text-dark">সম্পূর্ণ বিস্তারিত পরামর্শ (Full Text) <span class="text-danger">*</span></label>
                <textarea v-model="tipModalForm.full_text" class="form-control" rows="5" placeholder="১. শিশুকে সবসময় কুসুম গরম পোশাক পরিয়ে রাখুন...
২. ঠান্ডা বাতাসে সরাসরি বের করবেন না...
৩. পর্যাপ্ত পুষ্টিকর তরল খাবার খাওয়ান..." required></textarea>
              </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-end" style="gap: 8px;">
              <button type="button" class="btn btn-light rounded-pill px-3" @click="showPortfolioTipModal = false">বাতিল</button>
              <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold">
                {{ editingTipIndex !== null ? 'আপডেট করুন' : 'যোগ করুন' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ================= PORTFOLIO: TESTIMONIAL MODAL ================= -->
    <div 
      v-if="showPortfolioTestimonialModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.7); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-20 shadow-lg overflow-hidden">
          <div class="modal-header bg-dark text-white py-3 px-4">
            <h6 class="modal-title font-weight-bold mb-0">
              <i class="icofont-quote-left text-danger mr-1"></i>
              {{ editingTestimonialIndex !== null ? 'রোগীর রিভিউ সম্পাদনা' : 'নতুন রোগীর রিভিউ যোগ' }}
            </h6>
            <button type="button" class="close text-white" @click="showPortfolioTestimonialModal = false">&times;</button>
          </div>
          <form @submit.prevent="saveTestimonialModal">
            <div class="modal-body p-4">
              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">রোগী বা অভিভাবকের নাম <span class="text-danger">*</span></label>
                <input type="text" v-model="testimonialModalForm.name" class="form-control" placeholder="যেমন: রাশেদুল ইসলাম" required>
              </div>

              <div class="row">
                <div class="col-md-7 form-group mb-3">
                  <label class="font-weight-bold small text-dark">পরিচয় / সম্পর্ক</label>
                  <input type="text" v-model="testimonialModalForm.relation" class="form-control" placeholder="যেমন: রোগীর বাবা / রোগী">
                </div>
                <div class="col-md-5 form-group mb-3">
                  <label class="font-weight-bold small text-dark">রেটিং (স্টার)</label>
                  <select v-model.number="testimonialModalForm.rating" class="custom-select">
                    <option :value="5">⭐⭐⭐⭐⭐ (৫ স্টার)</option>
                    <option :value="4">⭐⭐⭐⭐ (৪ স্টার)</option>
                    <option :value="3">⭐⭐⭐ (৩ স্টার)</option>
                  </select>
                </div>
              </div>

              <div class="form-group mb-0">
                <label class="font-weight-bold small text-dark">মতামত ও রিভিউ বার্তা <span class="text-danger">*</span></label>
                <textarea v-model="testimonialModalForm.quote" class="form-control" rows="3" placeholder="ডাক্তার সাহেবের চিকিৎসায় আমার বাচ্চা খুব দ্রুত সুস্থ হয়ে উঠেছে। তাঁর অমায়িক ব্যবহার আমাদের মুগ্ধ করেছে।" required></textarea>
              </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-end" style="gap: 8px;">
              <button type="button" class="btn btn-light rounded-pill px-3" @click="showPortfolioTestimonialModal = false">বাতিল</button>
              <button type="submit" class="btn btn-danger rounded-pill px-4 font-weight-bold">
                {{ editingTestimonialIndex !== null ? 'আপডেট করুন' : 'যোগ করুন' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ================= PORTFOLIO: FAQ MODAL ================= -->
    <div 
      v-if="showPortfolioFaqModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.7); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-20 shadow-lg overflow-hidden">
          <div class="modal-header bg-dark text-white py-3 px-4">
            <h6 class="modal-title font-weight-bold mb-0">
              <i class="icofont-question-circle text-primary mr-1"></i>
              {{ editingFaqIndex !== null ? 'প্রশ্নোত্তর সম্পাদনা' : 'নতুন প্রশ্ন ও উত্তর যোগ' }}
            </h6>
            <button type="button" class="close text-white" @click="showPortfolioFaqModal = false">&times;</button>
          </div>
          <form @submit.prevent="saveFaqModal">
            <div class="modal-body p-4">
              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">প্রশ্ন <span class="text-danger">*</span></label>
                <input type="text" v-model="faqModalForm.question" class="form-control" placeholder="যেমন: অনলাইনে সিরিয়াল কীভাবে বুক করব?" required>
              </div>

              <div class="form-group mb-0">
                <label class="font-weight-bold small text-dark">উত্তর <span class="text-danger">*</span></label>
                <textarea v-model="faqModalForm.answer" class="form-control" rows="4" placeholder="ওয়েবসাইটের 'সিরিয়াল বুকিং' অপশনে গিয়ে চেম্বার ও তারিখ সিলেক্ট করে সরাসরি বুকিং দিন।" required></textarea>
              </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-end" style="gap: 8px;">
              <button type="button" class="btn btn-light rounded-pill px-3" @click="showPortfolioFaqModal = false">বাতিল</button>
              <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold">
                {{ editingFaqIndex !== null ? 'আপডেট করুন' : 'যোগ করুন' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ================= PORTFOLIO: GALLERY MODAL ================= -->
    <div 
      v-if="showPortfolioGalleryModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(0,0,0,0.7); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-20 shadow-lg overflow-hidden">
          <div class="modal-header bg-dark text-white py-3 px-4">
            <h6 class="modal-title font-weight-bold mb-0">
              <i class="icofont-image text-danger mr-1"></i> গ্যালারিতে ছবি যোগ
            </h6>
            <button type="button" class="close text-white" @click="showPortfolioGalleryModal = false">&times;</button>
          </div>
          <form @submit.prevent="saveGalleryModal">
            <div class="modal-body p-4">
              <div class="form-group mb-3">
                <label class="font-weight-bold small text-dark">ছবির লিংক (Image URL) <span class="text-danger">*</span></label>
                <input type="text" v-model="galleryModalForm.image_url" class="form-control mb-1" placeholder="https://...jpg" required>
                <label class="small text-primary cursor-pointer d-inline-block">
                  <i class="icofont-camera mr-1"></i> অথবা কম্পিউটার থেকে ছবি আপলোড করুন
                  <input type="file" class="d-none" accept="image/*" @change="uploadGalleryModalFile">
                </label>
              </div>

              <div class="form-group mb-0">
                <label class="font-weight-bold small text-dark">ক্যাপশন / শিরোনাম</label>
                <input type="text" v-model="galleryModalForm.caption" class="form-control" placeholder="যেমন: চেম্বার পরামর্শ কক্ষ">
              </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-end" style="gap: 8px;">
              <button type="button" class="btn btn-light rounded-pill px-3" @click="showPortfolioGalleryModal = false">বাতিল</button>
              <button type="submit" class="btn btn-danger rounded-pill px-4 font-weight-bold">যোগ করুন</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ================= DOCTOR PRO / VIP MEMBERSHIP MODAL ================= -->
    <div 
      v-if="showUpgradeModal" 
      class="modal fade show d-block" 
      tabindex="-1" 
      style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); z-index: 1060;"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-20 shadow-2xl overflow-hidden">
          <div class="modal-header py-3 px-4 text-white position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: 2px solid #f59e0b;">
            <div>
              <h5 class="modal-title font-weight-bold mb-1 text-white d-flex align-items-center" style="font-size: 18px;">
                <span class="mr-2" style="font-size: 22px;">👑</span> ডাক্তার মেম্বারশিপ প্যাকেজ আপগ্রেড
              </h5>
              <p class="mb-0 text-white-50 small" style="font-size: 12px;">
                আপনার ডাক্তার প্রোফাইলকে সবার শীর্ষে রাখুন এবং ৩ গুণ বেশি রোগী পান
              </p>
            </div>
            <button type="button" class="close text-white opacity-75 hover:opacity-100" @click="showUpgradeModal = false" style="font-size: 26px;">&times;</button>
          </div>

          <div class="modal-body p-4 bg-light">
            <!-- Current Status Alert -->
            <div class="alert bg-white border rounded-16 p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap shadow-xs">
              <div>
                <span class="text-muted small d-block">বর্তমান অ্যাক্টিভ মেম্বারশিপ:</span>
                <strong class="text-dark font-weight-bold text-uppercase" style="font-size: 15px;">
                  <span v-if="doctor?.membership_plan === 'vip'" class="text-primary font-weight-bold">💎 VIP Elite</span>
                  <span v-else-if="doctor?.membership_plan === 'pro'" class="text-warning font-weight-bold">👑 Pro Specialist</span>
                  <span v-else class="text-secondary">Free Plan (বেসিক)</span>
                </strong>
                <span v-if="doctor?.membership_expires_at" class="badge badge-light border text-muted ml-2">
                  মেয়াদ: {{ doctor.membership_expires_at }}
                </span>
              </div>
              <span class="badge badge-success-soft text-success px-3 py-1.5 font-weight-bold rounded-pill mt-2 mt-sm-0">
                <i class="icofont-check-circled mr-1"></i> লাইভ প্রোফাইল
              </span>
            </div>

            <!-- Pricing Tier Cards -->
            <div class="row mb-4">
              <!-- Tier 1: Free -->
              <div class="col-md-4 mb-3 mb-md-0">
                <div 
                  class="card h-100 rounded-16 border-2 transition p-3 cursor-pointer text-center position-relative"
                  :class="selectedUpgradePlan === 'free' ? 'border-primary bg-white shadow-sm' : 'border-light bg-white'"
                  @click="selectedUpgradePlan = 'free'"
                  style="border-style: solid;"
                >
                  <h6 class="font-weight-bold text-dark mb-1">বেসিক</h6>
                  <div class="my-2">
                    <span class="text-dark font-weight-bold" style="font-size: 24px;">৳০</span>
                    <small class="text-muted"> / আজীবন</small>
                  </div>
                  <ul class="list-unstyled text-left small text-muted mb-0 mt-3" style="line-height: 1.8;">
                    <li><i class="icofont-check text-success mr-1"></i> সাধারণ ডিরেক্টরি লিস্টিং</li>
                    <li><i class="icofont-check text-success mr-1"></i> ডিজিটাল প্রেসক্রিপশন</li>
                    <li class="text-muted opacity-50"><i class="icofont-close text-danger mr-1"></i> সার্চে টপ র‍্যাংক নেই</li>
                    <li class="text-muted opacity-50"><i class="icofont-close text-danger mr-1"></i> প্রো ব্যাজ নেই</li>
                  </ul>
                </div>
              </div>

              <!-- Tier 2: Pro Specialist -->
              <div class="col-md-4 mb-3 mb-md-0">
                <div 
                  class="card h-100 rounded-16 border-2 transition p-3 cursor-pointer text-center position-relative shadow-sm"
                  :class="selectedUpgradePlan === 'pro' ? 'border-warning bg-warning-soft' : 'border-light bg-white'"
                  @click="selectedUpgradePlan = 'pro'"
                  style="border-style: solid;"
                >
                  <div class="position-absolute" style="top: -10px; right: 15px;">
                    <span class="badge badge-warning text-dark font-weight-bold shadow-xs px-2 py-0.5 rounded-pill" style="font-size: 10px;">জনপ্রিয়</span>
                  </div>
                  <h6 class="font-weight-bold text-dark mb-1 d-flex align-items-center justify-content-center">
                    <span class="mr-1">👑</span> Pro Specialist
                  </h6>
                  <div class="my-2">
                    <span class="text-warning font-weight-bold" style="font-size: 24px;">৳৯৯৯</span>
                    <small class="text-muted"> / ৩০ দিন</small>
                  </div>
                  <ul class="list-unstyled text-left small text-dark mb-0 mt-3" style="line-height: 1.8;">
                    <li><i class="icofont-check text-success mr-1 font-weight-bold"></i> <strong>সার্চ রেজাল্টে সবার উপরে (Featured)</strong></li>
                    <li><i class="icofont-check text-success mr-1 font-weight-bold"></i> <strong>গোল্ডেন ★ PRO ব্যাজ</strong></li>
                    <li><i class="icofont-check text-success mr-1 font-weight-bold"></i> পার্সোনাল পোর্টফোলিও সাইট</li>
                    <li><i class="icofont-check text-success mr-1 font-weight-bold"></i> ৩ গুণ বেশি সিরিয়াল কল</li>
                  </ul>
                </div>
              </div>

              <!-- Tier 3: VIP Elite -->
              <div class="col-md-4">
                <div 
                  class="card h-100 rounded-16 border-2 transition p-3 cursor-pointer text-center position-relative shadow-sm"
                  :class="selectedUpgradePlan === 'vip' ? 'border-danger bg-danger-soft' : 'border-light bg-white'"
                  @click="selectedUpgradePlan = 'vip'"
                  style="border-style: solid;"
                >
                  <div class="position-absolute" style="top: -10px; right: 15px;">
                    <span class="badge text-white font-weight-bold shadow-xs px-2 py-0.5 rounded-pill" style="background: #7c3aed; font-size: 10px;">ম্যাক্সিমাম গ্রোথ</span>
                  </div>
                  <h6 class="font-weight-bold text-dark mb-1 d-flex align-items-center justify-content-center">
                    <span class="mr-1">💎</span> VIP Elite
                  </h6>
                  <div class="my-2">
                    <span class="text-danger font-weight-bold" style="font-size: 24px;">৳১,৯৯৯</span>
                    <small class="text-muted"> / ৩০ দিন</small>
                  </div>
                  <ul class="list-unstyled text-left small text-dark mb-0 mt-3" style="line-height: 1.8;">
                    <li><i class="icofont-check text-success mr-1 font-weight-bold"></i> <strong>হোমপেজ টপ স্পটলাইট ফিচার</strong></li>
                    <li><i class="icofont-check text-success mr-1 font-weight-bold"></i> প্রিমিয়াম ভিআইপি ডায়মন্ড ব্যাজ</li>
                    <li><i class="icofont-check text-success mr-1 font-weight-bold"></i> আনলিমিটেড পেশেন্ট রিপোর্ট ক্লাউড</li>
                    <li><i class="icofont-check text-success mr-1 font-weight-bold"></i> ডেডিকেটেড একাউন্ট ম্যানেজার</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Payment Details Form for Paid Tiers -->
            <div v-if="selectedUpgradePlan !== 'free'" class="bg-white p-3 rounded-16 border mb-3">
              <h6 class="font-weight-bold text-dark mb-2" style="font-size: 14px;">
                পেমেন্ট সম্পন্ন করুন (Instant Activation)
              </h6>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <label class="small font-weight-bold text-secondary mb-1">পেমেন্ট মেথড:</label>
                  <select v-model="upgradePaymentMethod" class="form-control form-control-sm">
                    <option value="bkash">বিকাশ (bKash মার্চেন্ট: 01700-112233)</option>
                    <option value="nagad">নগদ (Nagad মার্চেন্ট: 01800-445566)</option>
                    <option value="rocket">রকেট (Rocket মার্চেন্ট: 01900-778899)</option>
                    <option value="card">ভিসা / মাস্টারকার্ড / ব্যাংক ট্রান্সফার</option>
                  </select>
                </div>
                <div class="col-md-6 mb-2">
                  <label class="small font-weight-bold text-secondary mb-1">ট্রানজেকশন আইডি (TrxID) / রেফারেন্স:</label>
                  <input 
                    type="text" 
                    v-model="upgradeTxnId" 
                    class="form-control form-control-sm" 
                    placeholder="যেমন: 9J8B7A6C অথবা DEMO-AUTO"
                  />
                  <small class="text-muted" style="font-size: 11px;">(টেস্ট মোডে খালি রাখলেও অটো-জেনারেট হয়ে তাৎক্ষণিক চালু হবে)</small>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-white py-3 px-4 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-light rounded-pill px-4 text-muted" @click="showUpgradeModal = false">বন্ধ করুন</button>
            <button 
              type="button" 
              class="btn btn-warning rounded-pill px-4 font-weight-bold shadow-sm text-dark d-inline-flex align-items-center"
              style="background: linear-gradient(135deg, #fef08a, #f59e0b); border: none;"
              :disabled="upgradingMembership"
              @click="submitUpgradeMembership"
            >
              <span v-if="upgradingMembership" class="spinner-border spinner-border-sm mr-2"></span>
              <span v-else class="mr-1">👑</span>
              {{ selectedUpgradePlan === 'free' ? 'বেসিক প্যাকেজ সিলেক্ট করুন' : (selectedUpgradePlan === 'pro' ? '৳৯৯৯ প্রো প্যাকেজ অ্যাক্টিভ করুন' : '৳১,৯৯৯ ভিআইপি অ্যাক্টিভ করুন') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import PrescriptionPrintModal from '../../components/PrescriptionPrintModal.vue';

export default {
  name: 'DoctorDashboard',
  components: {
    PrescriptionPrintModal,
  },
  data() {
    return {
      showUpgradeModal: false,
      selectedUpgradePlan: 'pro',
      upgradingMembership: false,
      upgradePaymentMethod: 'bkash',
      upgradeTxnId: '',
      copiedPortfolio: false,
      showPrintModal: false,
      activePrintPrescription: {},
      doctor: null,
      stats: {},
      appointments: [],
      activeTab: 'appointments',
      filterStatus: 'all',
      loadingAppointments: false,
      savingSchedule: false,
      savingPrescription: false,
      showPrescriptionModal: false,
      currentApt: null,
      showPatientReportsModal: false,
      doctorSelectedPatient: null,
      doctorPatientReportsList: [],
      loadingDoctorReports: false,
      showDoctorLightbox: false,
      doctorActiveReport: null,
      queueServingSerial: 1,
      isEmergencyActive: false,
      emergencyData: null,
      showPatientMetricsModal: false,
      patientMetricsData: null,
      showAddAssistantModal: false,
      assistantsList: [],
      assistantForm: { name: '', phone: '', access_pin: '123456' },
      chambersList: [],
      loadingChambers: false,
      showChamberModal: false,
      editingChamberId: null,
      savingChamber: false,
      chamberForm: {
        chamber_name: '',
        branch_name: '',
        address: '',
        city: 'Dhaka',
        district: 'Dhaka',
        upazila: '',
        phone: '',
        visiting_days: ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
        start_time: '17:00',
        end_time: '21:00',
        slot_duration_minutes: 20,
        consultation_fee: 500,
        is_primary: false,
      },
      todayDate: new Date().toISOString().split('T')[0],
      allDays: ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
      scheduleForm: {
        name: '',
        specialization: '',
        designation: '',
        qualifications: '',
        experience_years: 5,
        chamber_address: '',
        telemedicine_link: '',
        city: 'New York',
        consultation_fee: 50,
        slot_duration_minutes: 30,
        start_time: '09:00',
        end_time: '17:00',
        available_days: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        free_consultation_enabled: false,
        free_days: ['Friday'],
        free_start_time: '09:00',
        free_end_time: '12:00',
        free_patient_limit: 20,
        free_chamber_address: '',
        free_service_note: '',
        bio: '',
        phone: '',
        password: '',
      },
      prescriptionForm: {
        appointment_id: null,
        patient_name: '',
        patient_age: 32,
        patient_age_unit: 'Yrs',
        patient_gender: 'Male',
        diagnosis: '',
        medicines: [
          { name: 'Tab. Napa Extra (500mg+65mg)', dosage: '1 + 0 + 1', duration: '7 Days', instruction: 'খাবার পর' }
        ],
        medical_tests: [],
        advice: '',
        follow_up_date: '',
      },
      selectedTests: [],
      customTestInput: '',
      selectedAdvices: [],
      customAdviceInput: '',
      allCommonTests: [
        'CBC (Complete Blood Count)',
        'FBS (Fasting Blood Sugar)',
        'HbA1c',
        'Serum Lipid Profile',
        'Serum Creatinine',
        'Serum Electrolytes',
        '12-Lead ECG',
        'Echocardiogram',
        'Chest X-Ray P/A View',
        'USG of Whole Abdomen',
        'Urine R/E & C/S',
        'Serum Bilirubin / SGPT',
        'TSH (Thyroid)',
        'Serum Uric Acid',
        'Vitamin D (25-OH)',
        'Endoscopy of Upper GI',
        'CT Scan Brain',
        'Stool R/E'
      ],
      allCommonAdvices: [
        { text: '💧 প্রতিদিন ২.৫ - ৩ লিটার বিশুদ্ধ পানি পান করুন' },
        { text: '🚫 অতিরিক্ত তেল, চর্বি ও ভাজাপোড়া খাবার পরিহার করুন' },
        { text: '🚶 দৈনিক অন্তত ৩০ মিনিট দ্রুত হাঁটা বা হালকা ব্যায়াম করুন' },
        { text: '🧂 খাবারে অতিরিক্ত কাঁচা লবণ সম্পূর্ণ বর্জন করুন' },
        { text: '🚭 ধূমপান, জর্দা ও তামাক সম্পূর্ণ পরিহার করুন' },
        { text: '😴 পর্যাপ্ত ঘুম (৭-৮ ঘণ্টা) নিশ্চিত করুন এবং মানসিক চাপ কমান' },
        { text: '📊 ডায়াবেটিস ও রক্তচাপ নিয়মিত মাপুন এবং চার্ট রাখুন' },
        { text: '🥗 প্রচুর শাকসবজি ও তাজা ফলমূল গ্রহণ করুন' },
        { text: '🍚 শর্করা ও মিষ্টি জাতীয় খাবার কম খান' },
        { text: '☕ চা, কফি ও অতিরিক্ত ক্যাফেইন পরিহার করুন' },
        { text: '🛌 ভারী ওজন উত্তোলন ও শক্ত কাজ থেকে বিরত থাকুন' },
        { text: '🩹 ক্ষতস্থান পরিষ্কার ও শুকনো রাখুন' },
      ],
      activeMedDropdownIdx: null,
      medSuggestions: [],
      searchingMed: false,
      searchTimeout: null,
      presets: {
        dosages: [
          { label: '1 + 0 + 1 (সকাল ও রাত)', value: '1 + 0 + 1' },
          { label: '1 + 1 + 1 (সকাল, দুপুর ও রাত)', value: '1 + 1 + 1' },
          { label: '1 + 0 + 0 (সকালে ১ বার)', value: '1 + 0 + 0' },
          { label: '0 + 0 + 1 (রাতে ১ বার)', value: '0 + 0 + 1' },
          { label: '0 + 1 + 0 (দুপুরে ১ বার)', value: '0 + 1 + 0' },
          { label: '1 + 1 + 1 + 1 (৪ বার)', value: '1 + 1 + 1 + 1' },
          { label: '1/2 + 0 + 1/2 (অর্ধেক করে)', value: '1/2 + 0 + 1/2' },
          { label: '2 চামচ করে ৩ বার (Syrup)', value: '2 চামচ করে ৩ বার' },
          { label: '1 চামচ করে ২ বার (Syrup)', value: '1 চামচ করে ২ বার' },
          { label: '2 ফোঁটা করে ৩ বার (Drop)', value: '2 ফোঁটা করে ৩ বার' },
          { label: 'প্রয়োজনে (As Needed / PRN)', value: 'প্রয়োজনে ১ বার' },
        ],
        durations: [
          { label: '৩ দিন (3 Days)', value: '3 Days' },
          { label: '৫ দিন (5 Days)', value: '5 Days' },
          { label: '৭ দিন (7 Days - ১ সপ্তাহ)', value: '7 Days' },
          { label: '১০ দিন (10 Days)', value: '10 Days' },
          { label: '১৪ দিন (14 Days - ২ সপ্তাহ)', value: '14 Days' },
          { label: '১ মাস (30 Days)', value: '30 Days' },
          { label: '২ মাস (60 Days)', value: '60 Days' },
          { label: '৩ মাস (90 Days)', value: '90 Days' },
          { label: 'চলবে (Continue)', value: 'Continue' },
        ],
        instructions: [
          { label: 'খাবার পর (After Meal)', value: 'খাবার পর' },
          { label: 'খাবার ৩০ মিনিট আগে (Before Meal)', value: 'খাবার ৩০ মিনিট আগে' },
          { label: 'ভরা পেটে (With Heavy Meal)', value: 'ভরা পেটে' },
          { label: 'রাতে ঘুমানোর আগে (Before Sleep)', value: 'রাতে ঘুমানোর আগে' },
          { label: 'খাওয়ার সাথে (With Meal)', value: 'খাওয়ার সাথে' },
          { label: 'ব্যথা হলে খাবেন (When in Pain)', value: 'ব্যথা হলে খাবেন' },
          { label: 'জ্বর আসলে খাবেন', value: 'জ্বর আসলে খাবেন' },
          { label: 'চুষে খাবেন (Chewable)', value: 'চুষে খাবেন' },
          { label: 'আক্রান্ত স্থানে লাগাবেন (Apply)', value: 'আক্রান্ত স্থানে লাগাবেন' },
        ],
        common_tests: [
          'CBC', 'FBS', 'HbA1c', 'Lipid Profile', 'Serum Creatinine', 'ECG 12-Lead', 'Chest X-Ray', 'USG Abdomen', 'Urine R/E', 'TSH'
        ],
        common_advices: [
          'প্রতিদিন ২.৫ - ৩ লিটার বিশুদ্ধ পানি পান করুন।',
          'অতিরিক্ত তেল, চর্বি ও ভাজাপোড়া খাবার পরিহার করুন।',
          'দৈনিক অন্তত ৩০ মিনিট হাঁটা বা হালকা ব্যায়াম করুন।',
          'খাবারে অতিরিক্ত কাঁচা লবণ সম্পূর্ণ বর্জন করুন।',
          'পরিমিত ঘুম (৭-৮ ঘণ্টা) নিশ্চিত করুন।',
        ],
      },
      portfolioSubTab: 'template',
      loadingPortfolio: false,
      savingPortfolio: false,
      uploadingPortfolioVideo: false,
      uploadingPortfolioMedia: false,
      portfolioTemplates: [
        {
          id: 'template_1_dark',
          name: 'Sukhi Manush Dark Pulse',
          titleBn: '১. সুখী মানুষ ডার্ক পালস (মডার্ন সাইবার-ডার্ক)',
          tag: 'ডিফল্ট ও টেক-স্যাভি',
          tagClass: 'badge-dark',
          desc: 'ডিপ ডার্ক ব্যাকগ্রাউন্ড (#090F0C) সাথে নিয়ন এমারেল্ড ও সায়ান গ্লো, ফিউচারিস্টিক গ্লাস কার্ড এবং হাই-টেক লুক।',
          specialty: 'আধুনিক বিশেষজ্ঞ, ডায়াগনস্টিক এক্সপার্ট ও টেক-স্যাভি ডাক্তারদের জন্য উপযোগী।',
          colors: ['#090F0C', '#22C55E', '#06B6D4', '#17241E'],
          previewBg: '#090F0C',
          previewAccent: '#22C55E',
          isDark: true
        },
        {
          id: 'template_2_clinical',
          name: 'Clinical Pure White',
          titleBn: '২. ক্লিনিক্যাল পিওর হোয়াইট (আন্তর্জাতিক হসপিটাল ক্লাসিক)',
          tag: 'সর্বোচ্চ ট্রাস্ট ও প্রফেশনাল',
          tagClass: 'badge-primary',
          desc: 'ঝকঝকে প্রিস্টিন হোয়াইট ও নরম গ্রে ব্যাকগ্রাউন্ড, রয়েল ব্লু (#0284C7) ও স্কাই অ্যাকসেন্ট, পরিষ্কার ও অত্যন্ত পরিচ্ছন্ন ক্লিনিক্যাল লুক।',
          specialty: 'মেডিসিন বিশেষজ্ঞ, কার্ডিওলজিস্ট, অর্থোপেডিক ও জেনারেল ফিজিশিয়ানদের জন্য পারফেক্ট।',
          colors: ['#FFFFFF', '#0284C7', '#2563EB', '#F1F5F9'],
          previewBg: '#F8FAFC',
          previewAccent: '#0284C7',
          isDark: false
        },
        {
          id: 'template_3_wellness',
          name: 'Serene Wellness & Aesthetics',
          titleBn: '৩. সেরিন ওয়েলনেস ও রোজ-গোল্ড (লাক্সারি এস্থেটিক্স)',
          tag: 'প্রিমিয়াম ও এলিগ্যান্ট',
          tagClass: 'badge-warning text-dark',
          desc: 'ওয়ার্ম ক্রিম ও আইভরি ব্যাকগ্রাউন্ড (#FDFBF7), রোজ গোল্ড ও সেজ গ্রিন স্পর্শ (#059669), আরামদায়ক সফট কার্ভস ও এলিগ্যান্ট ফন্ট।',
          specialty: 'ডার্মাটোলজিস্ট (ত্বক ও সৌন্দর্য), কসমেটিক/প্লাস্টিক সার্জন, গাইনিকোলজিস্ট ও নিউট্রিশনিস্টদের জন্য।',
          colors: ['#FDFBF7', '#D97706', '#059669', '#F4EFE6'],
          previewBg: '#FDFBF7',
          previewAccent: '#D97706',
          isDark: false
        },
        {
          id: 'template_4_executive',
          name: 'Executive Navy Authority',
          titleBn: '৪. এক্সিকিউটিভ নেভি অথরিটি (মডার্ন সিগনেচার ও প্রেস্টিজ)',
          tag: 'প্রেস্টিজ ও অথরিটি',
          tagClass: 'badge-info',
          desc: 'ডিপ রয়্যাল নেভি (#0B132B), স্যাফায়ার ব্লু ও আইস-সায়ান (#38BDF8) অ্যাকসেন্ট, প্রিসিশন গ্রিড লেআউট ও বোল্ড এক্সপেরিয়েন্স হাইলাইট।',
          specialty: 'সিনিয়র প্রফেসর, চিফ নিউরো/কার্ডিও সার্জন ও মেডিকেল বোর্ড বিশেষজ্ঞদের জন্য।',
          colors: ['#0B132B', '#38BDF8', '#818CF8', '#172554'],
          previewBg: '#0B132B',
          previewAccent: '#38BDF8',
          isDark: true
        },
        {
          id: 'template_5_lifecare',
          name: 'Dynamic LifeCare',
          titleBn: '৫. ডাইনামিক লাইফ-কেয়ার (প্রাণবন্ত ও বন্ধুসুলভ ভাইব)',
          tag: 'বন্ধুসুলভ ও প্রাণবন্ত',
          tagClass: 'badge-success',
          desc: 'সফট ভাইব্রেন্ট ব্যাকগ্রাউন্ড, চিয়ারফুল ইন্ডিগো-ভায়োলেট (#7C3AED) ও ফ্রেশ কোরাল-রোজ (#F43F5E), রাউন্ডেড ইন্টারেক্টিভ এলিমেন্ট।',
          specialty: 'শিশু বিশেষজ্ঞ (Pediatrician), মনোরোগ/সাইকিয়াট্রি, ডেন্টিস্ট ও ফ্যামিলি কনসালটেন্টদের জন্য।',
          colors: ['#FAF5FF', '#7C3AED', '#F43F5E', '#10B981'],
          previewBg: '#FAF5FF',
          previewAccent: '#7C3AED',
          isDark: false
        }
      ],
      portfolioForm: {
        template: 'template_1_dark',
        hero: {
          badge_text: '',
          title: '',
          subtitle: '',
          description: '',
          photo: '',
          stat_experience: '',
          stat_patients: '',
          stat_success: '',
          fee_text: '',
        },
        about: {
          heading: '',
          text_1: '',
          text_2: '',
          photo: '',
          values: [
            { title: 'রোগীকেন্দ্রিক সেবা', description: 'প্রতিটি রোগীর সুস্থতাকে সর্বোচ্চ অগ্রাধিকার দিয়ে চিকিৎসা প্রদান করি।', icon: 'icofont-heart-beat-alt' },
            { title: 'আধুনিক চিকিৎসা', description: 'সর্বাধুনিক ডায়াগনোসিস ও প্রটোকল মেনে আন্তর্জাতিক মানের সেবা।', icon: 'icofont-stethoscope-alt' },
          ],
        },
        services: [],
        education: [],
        tips: [],
        short_videos: [],
        testimonials: [],
        faqs: [],
        gallery: [],
        contact: {
          phone: '',
          assistant_phone: '',
          whatsapp: '',
          emergency_phone: '',
          email: '',
          chamber_address: '',
          facebook_url: '',
          youtube_url: '',
        },
      },
      showPortfolioVideoModal: false,
      editingVideoIndex: null,
      videoModalForm: {
        title: '',
        video_url: '',
        thumbnail_url: '',
        duration: '0:45',
        tag: 'স্বাস্থ্য পরামর্শ',
        description: '',
        views: '1.2K',
      },
      showPortfolioServiceModal: false,
      editingServiceIndex: null,
      serviceModalForm: {
        title: '',
        description: '',
        icon: 'icofont-stethoscope',
      },
      showPortfolioTimelineModal: false,
      editingTimelineIndex: null,
      timelineModalForm: {
        year: '',
        title: '',
        details: '',
        highlight: false,
      },
      showPortfolioTipModal: false,
      editingTipIndex: null,
      tipModalForm: {
        tag: 'পরামর্শ',
        title: '',
        excerpt: '',
        full_text: '',
      },
      showPortfolioTestimonialModal: false,
      editingTestimonialIndex: null,
      testimonialModalForm: {
        name: '',
        relation: 'রোগীর অভিভাবক',
        rating: 5,
        quote: '',
      },
      showPortfolioFaqModal: false,
      editingFaqIndex: null,
      faqModalForm: {
        question: '',
        answer: '',
      },
      showPortfolioGalleryModal: false,
      galleryModalForm: {
        image_url: '',
        caption: '',
      },
    };
  },
  async mounted() {
    const doc = localStorage.getItem('saas_doctor');
    if (!doc) {
      this.$router.push('/login');
      return;
    }
    this.doctor = JSON.parse(doc);
    this.populateScheduleForm();
    this.fetchDashboard();
    this.fetchAppointments();
    this.fetchPresets();
    this.fetchChambers();
    this.fetchQueueStatus();
    this.fetchPortfolio();
  },
  computed: {
    portfolioShareUrl() {
      if (!this.doctor) return '';
      const origin = window.location.origin;
      return `${origin}/dr/${this.doctor.slug || this.doctor.id}`;
    },
  },
  methods: {
    getWhatsAppPatientNotifyUrl(apt) {
      if (!apt) return '#';
      const origin = window.location.origin;
      const isVideo = apt.consultation_type === 'online_video' || apt.meeting_link;
      const docName = this.doctor?.name || 'ডাক্তার';
      const videoLink = `${origin}/video-chamber/${apt.appointment_number}`;
      const trackingLink = `${origin}/track-appointment?serial=${apt.appointment_number}`;

      let msg = `আসসালামু আলাইকুম ${apt.patient_name},\n` +
        `ডাঃ ${docName}-এর চেম্বার থেকে আপনার সিরিয়াল #${apt.serial_number} এর আপডেট পাঠানো হয়েছে।\n\n`;

      if (isVideo) {
        msg += `🎥 ডাক্তার ভিডিও চেম্বারে অপেক্ষা করছেন। এখনই নিচের লিংকে ক্লিক করে সরাসরি ভিডিও কলে যুক্ত হন:\n${videoLink}\n\n`;
      } else {
        msg += `📍 চেম্বারে আপনার ডাক পড়েছে। অনুগ্রহ করে ডাক্তারের রুমে আসুন।\n` +
          `🔍 লাইভ কিউ স্ট্যাটাস দেখতে: ${trackingLink}\n\n`;
      }
      msg += `ধন্যবাদ, সুখী মানুষ হেলথ কেয়ার।`;

      let phone = (apt.patient_phone || '').replace(/[^\d]/g, '');
      if (phone.startsWith('01')) phone = '88' + phone;
      return phone ? `https://wa.me/${phone}?text=${encodeURIComponent(msg)}` : `https://wa.me/?text=${encodeURIComponent(msg)}`;
    },
    copyPortfolioShareUrl() {
      if (!this.portfolioShareUrl) return;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(this.portfolioShareUrl);
      }
      this.copiedPortfolio = true;
      setTimeout(() => { this.copiedPortfolio = false; }, 3000);
      Swal.fire({
        icon: 'success',
        title: 'লিংক কপি হয়েছে!',
        text: 'আপনার পার্সোনাল পোর্টফোলিও ওয়েবসাইট লিংক সফলভাবে কপি হয়েছে।',
        timer: 2000,
        showConfirmButton: false,
      });
    },
    openUpgradeModal(plan = 'pro') {
      this.selectedUpgradePlan = plan || 'pro';
      this.showUpgradeModal = true;
    },
    async submitUpgradeMembership() {
      if (!this.selectedUpgradePlan) return;
      this.upgradingMembership = true;
      try {
        const payload = {
          doctor_id: this.doctor.id,
          membership_plan: this.selectedUpgradePlan,
          payment_method: this.upgradePaymentMethod,
          transaction_id: this.upgradeTxnId || ('TXN-PRO-' + Math.floor(Math.random() * 899999 + 100000)),
        };
        const res = await axios.post('/doctor-portal/membership/upgrade', payload);
        if (res.data?.doctor) {
          this.doctor = { ...this.doctor, ...res.data.doctor };
          localStorage.setItem('saas_doctor', JSON.stringify(this.doctor));
        }
        this.showUpgradeModal = false;
        await Swal.fire({
          icon: 'success',
          title: 'অভিনন্দন! প্যাকেজ আপডেট হয়েছে',
          text: res.data?.message || 'আপনার ডাক্তার মেম্বারশিপ প্যাকেজ সফলভাবে আপডেট করা হয়েছে।',
          confirmButtonColor: '#10b981',
        });
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'আপগ্রেড ব্যর্থ হয়েছে',
          text: err.response?.data?.message || 'মেম্বারশিপ আপগ্রেড সম্পন্ন করা সম্ভব হয়নি। অনুগ্রহ করে আবার চেষ্টা করুন।',
        });
      } finally {
        this.upgradingMembership = false;
      }
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
    getDayBangla(day) {
      const map = {
        Saturday: 'শনিবার',
        Sunday: 'রবিবার',
        Monday: 'সোমবার',
        Tuesday: 'মঙ্গলবার',
        Wednesday: 'বুধবার',
        Thursday: 'বৃহস্পতিবার',
        Friday: 'শুক্রবার',
      };
      return map[day] || day;
    },
    formatTimeDisplay(timeStr) {
      if (!timeStr) return '';
      const parts = timeStr.split(':');
      let hour = parseInt(parts[0], 10);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      hour = hour % 12;
      hour = hour ? hour : 12;
      return `${hour}:${parts[1] || '00'} ${ampm}`;
    },
    isDayActive(ch, day) {
      if (!ch || !ch.visiting_days) return false;
      return Array.isArray(ch.visiting_days) ? ch.visiting_days.includes(day) : false;
    },
    async fetchChambers() {
      if (!this.doctor?.id) return;
      this.loadingChambers = true;
      try {
        const res = await axios.get('/doctor-portal/chambers', {
          params: { doctor_id: this.doctor.id }
        });
        if (res.data.success) {
          this.chambersList = res.data.chambers || [];
        }
      } catch (err) {
        console.error('Error loading chambers:', err);
      } finally {
        this.loadingChambers = false;
      }
    },
    openAddChamberModal() {
      this.editingChamberId = null;
      this.chamberForm = {
        chamber_name: '',
        branch_name: '',
        address: '',
        city: this.doctor?.city || 'Dhaka',
        district: this.doctor?.district || 'Dhaka',
        upazila: this.doctor?.upazila || '',
        phone: this.doctor?.phone || '',
        visiting_days: ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
        start_time: '17:00',
        end_time: '21:00',
        slot_duration_minutes: 20,
        consultation_fee: this.doctor?.consultation_fee || 500,
        is_primary: this.chambersList.length === 0,
      };
      this.showChamberModal = true;
    },
    openEditChamberModal(ch) {
      this.editingChamberId = ch.id;
      this.chamberForm = {
        chamber_name: ch.chamber_name || '',
        branch_name: ch.branch_name || '',
        address: ch.address || '',
        city: ch.city || 'Dhaka',
        district: ch.district || 'Dhaka',
        upazila: ch.upazila || '',
        phone: ch.phone || '',
        visiting_days: Array.isArray(ch.visiting_days) ? [...ch.visiting_days] : ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
        start_time: ch.start_time ? ch.start_time.substring(0, 5) : '17:00',
        end_time: ch.end_time ? ch.end_time.substring(0, 5) : '21:00',
        slot_duration_minutes: ch.slot_duration_minutes || 20,
        consultation_fee: ch.consultation_fee || 500,
        is_primary: !!ch.is_primary,
      };
      this.showChamberModal = true;
    },
    async saveChamber() {
      if (!this.doctor?.id) return;
      this.savingChamber = true;
      try {
        const payload = {
          ...this.chamberForm,
          doctor_id: this.doctor.id,
        };

        let res;
        if (this.editingChamberId) {
          res = await axios.put(`/doctor-portal/chambers/${this.editingChamberId}`, payload);
        } else {
          res = await axios.post('/doctor-portal/chambers', payload);
        }

        if (res.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'সফল হয়েছে!',
            text: res.data.message,
            timer: 2000,
            showConfirmButton: false,
          });
          this.showChamberModal = false;
          if (res.data.chambers) {
            this.chambersList = res.data.chambers;
          } else {
            await this.fetchChambers();
          }
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'চেম্বার তথ্য সেভ করতে সমস্যা হয়েছে।';
        Swal.fire({ icon: 'error', title: 'ত্রুটি!', text: msg });
      } finally {
        this.savingChamber = false;
      }
    },
    async deleteChamber(ch) {
      if (this.chambersList.length <= 1) {
        Swal.fire({ icon: 'warning', title: 'সতর্কতা', text: 'অন্তত একটি চেম্বার থাকা আবশ্যক।' });
        return;
      }

      const result = await Swal.fire({
        title: 'আপনি কি নিশ্চিত?',
        text: `"${ch.chamber_name}" চেম্বারটি মুছে ফেলতে চান?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'হ্যাঁ, মুছুন',
        cancelButtonText: 'বাতিল',
      });

      if (result.isConfirmed) {
        try {
          const res = await axios.delete(`/doctor-portal/chambers/${ch.id}`, {
            params: { doctor_id: this.doctor.id }
          });
          if (res.data.success) {
            Swal.fire({ icon: 'success', title: 'মুছে ফেলা হয়েছে!', text: res.data.message, timer: 1500, showConfirmButton: false });
            if (res.data.chambers) {
              this.chambersList = res.data.chambers;
            } else {
              await this.fetchChambers();
            }
          }
        } catch (err) {
          const msg = err.response?.data?.message || 'চেম্বার মুছতে সমস্যা হয়েছে।';
          Swal.fire({ icon: 'error', title: 'ব্যর্থ হয়েছে', text: msg });
        }
      }
    },
    async setPrimaryChamber(ch) {
      try {
        const res = await axios.post(`/doctor-portal/chambers/${ch.id}/set-primary`, {
          doctor_id: this.doctor.id
        });
        if (res.data.success) {
          Swal.fire({ icon: 'success', title: 'সফল!', text: res.data.message, timer: 1500, showConfirmButton: false });
          if (res.data.chambers) {
            this.chambersList = res.data.chambers;
          } else {
            await this.fetchChambers();
          }
          if (this.doctor) {
            this.doctor.chamber_address = ch.address;
          }
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'প্রধান চেম্বার নির্ধারণ করতে সমস্যা হয়েছে।';
        Swal.fire({ icon: 'error', title: 'ব্যর্থ হয়েছে', text: msg });
      }
    },
    populateScheduleForm() {
      if (this.doctor) {
        this.scheduleForm.name = this.doctor.name || '';
        this.scheduleForm.specialization = this.doctor.specialization || '';
        this.scheduleForm.designation = this.doctor.designation || '';
        this.scheduleForm.qualifications = this.doctor.qualifications || '';
        this.scheduleForm.experience_years = this.doctor.experience_years || 5;
        this.scheduleForm.chamber_address = this.doctor.chamber_address || '';
        this.scheduleForm.telemedicine_link = this.doctor.telemedicine_link || '';
        this.scheduleForm.city = this.doctor.city || 'Dhaka';
        this.scheduleForm.consultation_fee = this.doctor.consultation_fee || 50;
        this.scheduleForm.slot_duration_minutes = this.doctor.slot_duration_minutes || 30;
        this.scheduleForm.start_time = this.doctor.start_time ? this.doctor.start_time.substring(0, 5) : '09:00';
        this.scheduleForm.end_time = this.doctor.end_time ? this.doctor.end_time.substring(0, 5) : '17:00';
        this.scheduleForm.available_days = this.doctor.available_days || ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        this.scheduleForm.free_consultation_enabled = !!this.doctor.free_consultation_enabled;
        this.scheduleForm.free_days = this.doctor.free_days || ['Friday'];
        this.scheduleForm.free_start_time = this.doctor.free_start_time ? this.doctor.free_start_time.substring(0, 5) : '09:00';
        this.scheduleForm.free_end_time = this.doctor.free_end_time ? this.doctor.free_end_time.substring(0, 5) : '12:00';
        this.scheduleForm.free_patient_limit = this.doctor.free_patient_limit || 20;
        this.scheduleForm.free_chamber_address = this.doctor.free_chamber_address || '';
        this.scheduleForm.free_service_note = this.doctor.free_service_note || '';
        this.scheduleForm.bio = this.doctor.bio || '';
        this.scheduleForm.phone = this.doctor.phone || '';
        this.scheduleForm.password = '';
      }
    },
    getDoctorAvatar(doctor) {
      if (!doctor) return '/images/team/1.jpg';
      if (doctor.avatar_url) return doctor.avatar_url;
      if (doctor.avatar) {
        const av = doctor.avatar.trim();
        if (av.startsWith('http://') || av.startsWith('https://')) return av;
        return '/' + av.replace(/^\//, '');
      }
      return doctor.gender === 'female' 
        ? '/images/team/default-doctor-female.svg' 
        : '/images/team/default-doctor-male.svg';
    },
    onAvatarError(event, doctor) {
      const fallback = doctor?.gender === 'female' 
        ? '/images/team/default-doctor-female.svg' 
        : '/images/team/default-doctor-male.svg';
      if (event.target.src !== fallback && !event.target.src.endsWith(fallback)) {
        event.target.src = fallback;
      }
    },
    async uploadDoctorAvatar(e) {
      const file = e.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append('avatar', file);
      if (this.doctor?.id) {
        formData.append('doctor_id', this.doctor.id);
      }

      try {
        const res = await axios.post('/profile/upload-avatar', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (res.data.success) {
          const cleanPath = res.data.avatar_url.replace(/^\//, '');
          this.doctor.avatar = cleanPath;
          localStorage.setItem('saas_doctor', JSON.stringify(this.doctor));
          window.dispatchEvent(new Event('auth-change'));
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Doctor photo updated!',
            showConfirmButton: false,
            timer: 2000
          });
        }
      } catch (err) {
        Swal.fire('Error', err.response?.data?.message || 'Failed to upload photo.', 'error');
      }
    },
    async editMeetingLink(apt) {
      const { value: url } = await Swal.fire({
        title: 'Video Consultation Link',
        input: 'url',
        inputLabel: 'Google Meet / Zoom URL for ' + apt.patient_name,
        inputValue: apt.meeting_link || 'https://meet.google.com/new',
        showCancelButton: true,
        confirmButtonText: 'Save Link',
        confirmButtonColor: '#28a745',
        inputPlaceholder: 'https://meet.google.com/abc-defg-hij'
      });

      if (url) {
        try {
          const res = await axios.patch(`/doctor-portal/appointments/${apt.id}/meeting-link`, {
            meeting_link: url
          });
          if (res.data.success) {
            apt.meeting_link = url;
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'Meeting link updated!',
              showConfirmButton: false,
              timer: 2000
            });
          }
        } catch (err) {
          Swal.fire('Error', 'Failed to update meeting link.', 'error');
        }
      }
    },
    async fetchPresets() {
      try {
        const res = await axios.get('/medicines/presets');
        if (res.data) {
          this.presets = { ...this.presets, ...res.data };
        }
      } catch (err) {
        console.error('Error fetching presets:', err);
      }
    },
    async fetchDashboard() {
      try {
        const res = await axios.get('/doctor-portal/dashboard', {
          params: { doctor_id: this.doctor.id }
        });
        this.stats = res.data.stats || {};
        if (res.data.doctor) {
          this.doctor = res.data.doctor;
          localStorage.setItem('saas_doctor', JSON.stringify(res.data.doctor));
        }
      } catch (err) {
        console.error('Error fetching doctor dashboard:', err);
      }
    },
    async fetchAppointments() {
      this.loadingAppointments = true;
      try {
        const res = await axios.get('/doctor-portal/appointments', {
          params: {
            doctor_id: this.doctor.id,
            status: this.filterStatus,
          }
        });
        this.appointments = res.data.data || [];
      } catch (err) {
        console.error('Error fetching appointments:', err);
      } finally {
        this.loadingAppointments = false;
      }
    },
    async updateStatus(apt, status) {
      try {
        const res = await axios.patch(`/doctor-portal/appointments/${apt.id}/status`, {
          status: status,
        });
        if (res.data.success) {
          apt.status = status;
          this.fetchDashboard();
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Appointment status: ${status}`,
            showConfirmButton: false,
            timer: 2000,
          });
        }
      } catch (err) {
        Swal.fire('Error', 'Failed to update status.', 'error');
      }
    },
    async saveSchedule() {
      this.savingSchedule = true;
      try {
        const payload = {
          ...this.scheduleForm,
          doctor_id: this.doctor.id,
        };
        const res = await axios.post('/doctor-portal/schedule/update', payload);
        if (res.data.success) {
          this.doctor = res.data.doctor;
          localStorage.setItem('saas_doctor', JSON.stringify(res.data.doctor));
          Swal.fire({
            icon: 'success',
            title: 'Schedule Updated!',
            text: res.data.message,
            confirmButtonColor: '#e12454',
          });
        }
      } catch (err) {
        Swal.fire('Error', err.response?.data?.message || 'Failed to update schedule.', 'error');
      } finally {
        this.savingSchedule = false;
      }
    },
    openPrescriptionModal(apt) {
      this.currentApt = apt;
      this.prescriptionForm.appointment_id = apt.id;
      this.prescriptionForm.patient_name = apt.patient_name;

      if (apt.prescription) {
        if (apt.prescription.patient_age_gender) {
          const parts = apt.prescription.patient_age_gender.split('/');
          if (parts.length === 2) {
            const ageTokens = parts[0].trim().split(' ');
            this.prescriptionForm.patient_age = parseInt(ageTokens[0]) || 30;
            this.prescriptionForm.patient_age_unit = ageTokens[1] || 'Yrs';
            this.prescriptionForm.patient_gender = parts[1].trim() || 'Male';
          }
        }
        this.prescriptionForm.diagnosis = apt.prescription.diagnosis || '';
        this.prescriptionForm.medicines = apt.prescription.medicines || [
          { name: 'Tab. Napa Extra (500mg+65mg)', dosage: '1 + 0 + 1', duration: '7 Days', instruction: 'খাবার পর' }
        ];
        this.prescriptionForm.follow_up_date = apt.prescription.follow_up_date || '';
        this.selectedTests = apt.prescription.medical_tests || [];
        this.selectedAdvices = apt.prescription.advice ? apt.prescription.advice.split('\n').map(x => x.trim()).filter(Boolean) : [];
      } else {
        this.prescriptionForm.patient_age = 30;
        this.prescriptionForm.patient_age_unit = 'Yrs';
        this.prescriptionForm.patient_gender = 'Male';
        this.prescriptionForm.diagnosis = apt.reason_for_visit || 'General Consultation';
        this.prescriptionForm.medicines = [
          { name: 'Tab. Napa Extra (500mg+65mg)', dosage: '1 + 0 + 1', duration: '7 Days', instruction: 'খাবার পর' }
        ];
        this.prescriptionForm.follow_up_date = '';
        this.selectedTests = ['CBC (Complete Blood Count)', 'Serum Lipid Profile'];
        this.selectedAdvices = [
          '💧 প্রতিদিন ২.৫ - ৩ লিটার বিশুদ্ধ পানি পান করুন',
          '🚫 অতিরিক্ত তেল, চর্বি ও ভাজাপোড়া খাবার পরিহার করুন',
          '🚶 দৈনিক অন্তত ৩০ মিনিট দ্রুত হাঁটা বা হালকা ব্যায়াম করুন'
        ];
      }

      this.showPrescriptionModal = true;
    },
    toggleTest(test) {
      if (this.selectedTests.includes(test)) {
        this.selectedTests = this.selectedTests.filter(t => t !== test);
      } else {
        this.selectedTests.push(test);
      }
    },
    addCustomTest() {
      const t = this.customTestInput.trim();
      if (t && !this.selectedTests.includes(t)) {
        this.selectedTests.push(t);
        if (!this.allCommonTests.includes(t)) {
          this.allCommonTests.push(t);
        }
      }
      this.customTestInput = '';
    },
    toggleAdvice(adviceText) {
      if (this.selectedAdvices.includes(adviceText)) {
        this.selectedAdvices = this.selectedAdvices.filter(a => a !== adviceText);
      } else {
        this.selectedAdvices.push(adviceText);
      }
    },
    addCustomAdvice() {
      const a = this.customAdviceInput.trim();
      if (a && !this.selectedAdvices.includes(a)) {
        this.selectedAdvices.push(a);
        this.allCommonAdvices.push({ text: a });
      }
      this.customAdviceInput = '';
    },
    addMedicineRow() {
      this.prescriptionForm.medicines.push({
        name: '',
        dosage: '1 + 0 + 1',
        duration: '7 Days',
        instruction: 'খাবার পর',
      });
    },
    removeMedicineRow(idx) {
      this.prescriptionForm.medicines.splice(idx, 1);
    },
    onFocusMedicine(idx) {
      this.activeMedDropdownIdx = idx;
      // Always trigger search when input is focused, even if it already has text (e.g. 'Rupa')
      this.onSearchMedicine(idx, this.prescriptionForm.medicines[idx].name || '');
    },
    toggleMedicineDropdown(idx) {
      if (this.activeMedDropdownIdx === idx) {
        this.activeMedDropdownIdx = null;
      } else {
        this.activeMedDropdownIdx = idx;
        this.onSearchMedicine(idx, this.prescriptionForm.medicines[idx].name || '');
      }
    },
    onBlurMedicine() {
      setTimeout(() => {
        this.activeMedDropdownIdx = null;
      }, 350);
    },
    onSearchMedicine(idx, query) {
      this.activeMedDropdownIdx = idx;
      clearTimeout(this.searchTimeout);
      this.searchingMed = true;
      const cleanQ = (query || '').trim();
      this.searchTimeout = setTimeout(async () => {
        try {
          const res = await axios.get('/medicines/search', {
            params: { q: cleanQ }
          });
          this.medSuggestions = Array.isArray(res.data) ? res.data : [];
        } catch (err) {
          this.medSuggestions = [];
        } finally {
          this.searchingMed = false;
        }
      }, 50);
    },
    selectMedicineSuggestion(idx, sug) {
      this.prescriptionForm.medicines[idx].name = `${sug.dosage_form}. ${sug.brand_name} (${sug.generic_name})`;
      this.activeMedDropdownIdx = null;
      this.medSuggestions = [];
    },
    async savePrescription() {
      this.savingPrescription = true;

      const ageGenderFormatted = `${this.prescriptionForm.patient_age || 30} ${this.prescriptionForm.patient_age_unit || 'Yrs'} / ${this.prescriptionForm.patient_gender || 'Male'}`;
      const payload = {
        ...this.prescriptionForm,
        patient_age_gender: ageGenderFormatted,
        medical_tests: this.selectedTests,
        advice: this.selectedAdvices.join('\n'),
      };

      try {
        const res = await axios.post('/doctor-portal/prescriptions', payload);
        if (res.data.success) {
          this.showPrescriptionModal = false;
          this.fetchAppointments();
          this.fetchDashboard();
          const savedRx = res.data.prescription;
          Swal.fire({
            icon: 'success',
            title: 'প্রেসক্রিপশন সফলভাবে সংরক্ষিত হয়েছে!',
            text: 'আপনি কি প্রেসক্রিপশনটি এখনই অফিসিয়াল প্যাডে প্রিন্ট বা প্রিভিউ করতে চান?',
            showCancelButton: true,
            confirmButtonText: '🖨️ প্রিন্ট প্যাড প্রিভিউ',
            cancelButtonText: 'পরে করব',
            confirmButtonColor: '#e12454',
          }).then((result) => {
            if (result.isConfirmed) {
              this.printDoctorPrescription(savedRx);
            }
          });
        }
      } catch (err) {
        Swal.fire('Error', err.response?.data?.message || 'Failed to issue prescription.', 'error');
      } finally {
        this.savingPrescription = false;
      }
    },
    printDoctorPrescription(rx, apt = null) {
      if (!rx) return;
      this.activePrintPrescription = {
        ...rx,
        doctor: rx.doctor || this.doctor,
        patient_phone: rx.patient_phone || apt?.patient_phone || this.currentApt?.patient_phone || '',
      };
      this.showPrintModal = true;
    },
    async openPatientReportsModal(apt) {
      this.doctorSelectedPatient = apt;
      this.doctorPatientReportsList = [];
      this.loadingDoctorReports = true;
      this.showPatientReportsModal = true;

      const patientId = apt.patient_id;
      if (!patientId) {
        this.loadingDoctorReports = false;
        return;
      }

      try {
        const res = await axios.get(`/doctor-portal/patient-reports/${patientId}`);
        if (res.data.success) {
          this.doctorPatientReportsList = res.data.reports || [];
        }
      } catch (err) {
        console.error('Error fetching patient reports for doctor:', err);
      } finally {
        this.loadingDoctorReports = false;
      }
    },
    viewDoctorReportDetail(report) {
      this.doctorActiveReport = report;
      this.showDoctorLightbox = true;
    },
    async saveDoctorRemarks(report) {
      try {
        const res = await axios.post(`/doctor-portal/reports/${report.id}/remarks`, {
          doctor_remarks: report.doctor_remarks,
          doctor_id: this.doctor?.id,
          status: 'reviewed',
        });
        if (res.data.success) {
          report.status = 'reviewed';
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'মতামত সংরক্ষণ করা হয়েছে!',
            showConfirmButton: false,
            timer: 2000
          });
        }
      } catch (err) {
        Swal.fire('Error', 'Failed to save remarks.', 'error');
      }
    },
    async fetchQueueStatus() {
      if (!this.doctor?.id) return;
      try {
        const res = await axios.get(`/doctor-portal/queue/status/${this.doctor.id}`);
        if (res.data.success && res.data.queue) {
          this.queueServingSerial = res.data.queue.current_serving_serial || 1;
          this.isEmergencyActive = !!res.data.queue.is_emergency_active;
          this.emergencyData = {
            patient_name: res.data.queue.emergency_patient_name,
            emergency_reason: res.data.queue.emergency_reason,
            started_at: res.data.queue.emergency_started_at,
          };
        }
      } catch (err) {
        console.error('Error fetching queue status:', err);
      }
    },
    async openEmergencyBypassModal() {
      const { value: formValues } = await Swal.fire({
        title: '🚨 জরুরি রোগী প্রবেশ (Emergency Bypass)',
        html: `
          <div class="text-left small mb-3 text-muted">
            চেম্বারে কোনো আশঙ্কাজনক জরুরি রোগী এলে এই অপশনটি ব্যবহার করুন। 
            নিয়মিত সিরিয়াল নম্বর ঠিক থাকবে এবং সকল অপেক্ষমাণ রোগীর ট্র্যাকিং স্ক্রিনে স্বয়ংক্রিয়ভাবে জরুরি অ্যালার্ট ও বিলম্ব বার্তা প্রদর্শিত হবে।
          </div>
          <div class="text-left mb-2">
            <label class="small font-weight-bold">রোগীর নাম (ঐচ্ছিক):</label>
            <input id="swal-input-patient" class="swal2-input m-0 w-100" placeholder="যেমন: রহিম সাহেব (জরুরি রোগী)">
          </div>
          <div class="text-left mb-2">
            <label class="small font-weight-bold">জরুরি কারণ / লক্ষণ (ঐচ্ছিক):</label>
            <input id="swal-input-reason" class="swal2-input m-0 w-100" placeholder="যেমন: বুকে তীব্র ব্যথা / শ্বাসকষ্ট / এক্সিডেন্ট">
          </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: '🚨 জরুরি রোগী চালু করুন',
        cancelButtonText: 'বাতিল',
        confirmButtonColor: '#dc2626',
        preConfirm: () => {
          return {
            patient_name: document.getElementById('swal-input-patient')?.value || '',
            emergency_reason: document.getElementById('swal-input-reason')?.value || ''
          };
        }
      });

      if (formValues) {
        try {
          const res = await axios.post(`/doctor-portal/queue/emergency-bypass/${this.doctor.id}`, {
            action: 'start',
            patient_name: formValues.patient_name,
            emergency_reason: formValues.emergency_reason
          });
          if (res.data.success) {
            this.isEmergencyActive = true;
            this.emergencyData = {
              patient_name: formValues.patient_name,
              emergency_reason: formValues.emergency_reason
            };
            Swal.fire({
              icon: 'warning',
              title: '🚨 জরুরি মোড সক্রিয়!',
              text: res.data.message,
              confirmButtonColor: '#dc2626'
            });
            this.fetchQueueStatus();
          }
        } catch (err) {
          Swal.fire('Error', 'জরুরি মোড চালু করতে সমস্যা হয়েছে।', 'error');
        }
      }
    },
    async stopEmergencyBypass() {
      const result = await Swal.fire({
        title: 'জরুরি সেবা সমাপ্ত?',
        text: 'জরুরি রোগীর চিকিৎসা শেষ করে কি আপনি নিয়মিত সিরিয়ালে ফিরতে চান?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'হ্যাঁ, নিয়মিত সিরিয়ালে ফিরুন',
        cancelButtonText: 'বাতিল',
        confirmButtonColor: '#16a34a'
      });

      if (result.isConfirmed) {
        try {
          const res = await axios.post(`/doctor-portal/queue/emergency-bypass/${this.doctor.id}`, {
            action: 'stop'
          });
          if (res.data.success) {
            this.isEmergencyActive = false;
            this.emergencyData = null;
            Swal.fire({
              icon: 'success',
              title: 'সফল!',
              text: res.data.message,
              confirmButtonColor: '#16a34a'
            });
            this.fetchQueueStatus();
          }
        } catch (err) {
          Swal.fire('Error', 'নিয়মিত সিরিয়ালে ফিরতে সমস্যা হয়েছে।', 'error');
        }
      }
    },
    async callNextQueuePatient() {
      if (!this.doctor?.id) return;

      if (this.isEmergencyActive) {
        const resumeConfirm = await Swal.fire({
          icon: 'warning',
          title: 'জরুরি রোগী মোড চালু রয়েছে!',
          text: 'চেম্বারে বর্তমানে জরুরি রোগী চলমান। আপনি কি জরুরি মোড বন্ধ করে পরবর্তী নিয়মিত রোগীকে ডাকতে চান?',
          showCancelButton: true,
          confirmButtonText: 'হ্যাঁ, নিয়মিত রোগী ডাকুন',
          cancelButtonText: 'বাতিল',
          confirmButtonColor: '#e12454'
        });
        if (!resumeConfirm.isConfirmed) return;

        // Auto stop emergency before calling next
        await axios.post(`/doctor-portal/queue/emergency-bypass/${this.doctor.id}`, { action: 'stop' });
        this.isEmergencyActive = false;
        this.emergencyData = null;
      }

      try {
        const res = await axios.post(`/doctor-portal/queue/call-next/${this.doctor.id}`);
        if (res.data.success) {
          this.queueServingSerial = res.data.current_serving_serial;
          Swal.fire({
            icon: 'success',
            title: 'পরবর্তী রোগীকে ডাকা হয়েছে!',
            text: res.data.message,
            confirmButtonColor: '#e12454'
          });
          this.fetchAppointments();
          this.fetchQueueStatus();
        } else {
          Swal.fire('তথ্য', res.data.message, 'info');
        }
      } catch (err) {
        Swal.fire('Error', 'সিরিয়াল পরিবর্তন করতে সমস্যা হয়েছে।', 'error');
      }
    },
    async openPatientMetricsModal(apt) {
      if (!apt.patient_id) return;
      try {
        const res = await axios.get(`/doctor-portal/patient-metrics/${apt.patient_id}`);
        if (res.data.success) {
          this.patientMetricsData = res.data;
          this.showPatientMetricsModal = true;
        }
      } catch (err) {
        console.error('Error fetching patient metrics:', err);
      }
    },
    async fetchAssistants() {
      if (!this.doctor?.id) return;
      try {
        const res = await axios.get(`/doctor-portal/assistants/${this.doctor.id}`);
        if (res.data.success) {
          this.assistantsList = res.data.assistants || [];
        }
      } catch (err) {
        console.error('Error loading assistants:', err);
      }
    },
    async submitAddAssistant() {
      if (!this.doctor?.id) return;
      try {
        const res = await axios.post(`/doctor-portal/assistants/${this.doctor.id}`, this.assistantForm);
        if (res.data.success) {
          Swal.fire('সফল!', res.data.message, 'success');
          this.showAddAssistantModal = false;
          this.assistantForm = { name: '', phone: '', access_pin: '123456' };
          this.fetchAssistants();
        }
      } catch (err) {
        Swal.fire('Error', 'সহকারী যোগ করতে সমস্যা হয়েছে।', 'error');
      }
    },
    async deleteAssistant(id) {
      const c = await Swal.fire({ title: 'সহকারী মুছে ফেলবেন?', showCancelButton: true });
      if (c.isConfirmed) {
        await axios.delete(`/doctor-portal/assistants/${id}`);
        this.fetchAssistants();
      }
    },
    /* ================= PORTFOLIO & REELS METHODS ================= */
    async fetchPortfolio() {
      if (!this.doctor?.id) return;
      this.loadingPortfolio = true;
      try {
        const res = await axios.get('/doctor-portal/portfolio', {
          params: { doctor_id: this.doctor.id }
        });
        if (res.data.success && res.data.portfolio_data) {
          const data = res.data.portfolio_data;
          this.portfolioForm = {
            template: data.template || 'template_1_dark',
            hero: { ...this.portfolioForm.hero, ...(data.hero || {}) },
            about: { ...this.portfolioForm.about, ...(data.about || {}) },
            services: Array.isArray(data.services) ? data.services : [],
            education: Array.isArray(data.education) ? data.education : [],
            tips: Array.isArray(data.tips) ? data.tips : [],
            short_videos: Array.isArray(data.short_videos) ? data.short_videos : [],
            testimonials: Array.isArray(data.testimonials) ? data.testimonials : [],
            faqs: Array.isArray(data.faqs) ? data.faqs : [],
            gallery: Array.isArray(data.gallery) ? data.gallery : [],
            contact: { ...this.portfolioForm.contact, ...(data.contact || {}) },
          };
          if (!this.portfolioForm.hero.title && this.doctor.name) {
            this.portfolioForm.hero.title = this.doctor.name;
          }
          if (!this.portfolioForm.hero.subtitle && this.doctor.qualifications) {
            this.portfolioForm.hero.subtitle = this.doctor.qualifications;
          }
          if (!this.portfolioForm.hero.badge_text && this.doctor.specialization) {
            this.portfolioForm.hero.badge_text = this.doctor.specialization;
          }
        }
      } catch (err) {
        console.error('Error fetching portfolio:', err);
      } finally {
        this.loadingPortfolio = false;
      }
    },
    async savePortfolio(silent = false) {
      if (!this.doctor?.id) return;
      this.savingPortfolio = true;
      try {
        const res = await axios.post('/doctor-portal/portfolio', {
          doctor_id: this.doctor.id,
          portfolio_data: this.portfolioForm,
        });
        if (res.data.success) {
          if (silent) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'সফলভাবে সংরক্ষিত ও লাইভ আপডেট হয়েছে!',
              showConfirmButton: false,
              timer: 2000,
            });
          } else {
            Swal.fire({
              icon: 'success',
              title: 'সফলভাবে সংরক্ষিত!',
              text: 'আপনার পোর্টফোলিও ও ভিডিওর সকল তথ্য লাইভ ওয়েবসাইটে আপডেট হয়েছে।',
              timer: 2500,
              showConfirmButton: false,
            });
          }
          if (res.data.doctor) {
            this.doctor = { ...this.doctor, ...res.data.doctor };
            localStorage.setItem('saas_doctor', JSON.stringify(this.doctor));
          }
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'পোর্টফোলিও সেভ করতে সমস্যা হয়েছে।';
        Swal.fire({ icon: 'error', title: 'ত্রুটি!', text: msg });
      } finally {
        this.savingPortfolio = false;
      }
    },
    async uploadPortfolioVideoFile(e) {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 50 * 1024 * 1024) {
        Swal.fire('ফাইলটি অনেক বড়', 'ভিডিওর সাইজ সর্বোচ্চ ৫০ মেগাবাইট (50MB) হতে পারে।', 'warning');
        return;
      }

      this.uploadingPortfolioVideo = true;
      const formData = new FormData();
      formData.append('video', file);
      if (this.doctor?.id) {
        formData.append('doctor_id', this.doctor.id);
      }

      try {
        const res = await axios.post('/doctor-portal/portfolio/upload-video', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (res.data.success) {
          const newVid = {
            id: Date.now(),
            title: file.name.replace(/\.[^/.]+$/, ''),
            video_url: res.data.video_url,
            thumbnail_url: res.data.thumbnail_url || '',
            duration: '0:45',
            tag: 'স্বাস্থ্য টিপস',
            description: 'ডাক্তারের পরামর্শমূলক শর্ট ভিডিও',
            views: '1.2K ভিউ',
          };
          if (!this.portfolioForm.short_videos) {
            this.portfolioForm.short_videos = [];
          }
          this.portfolioForm.short_videos.unshift(newVid);
          await this.savePortfolio(true);
          Swal.fire('ভিডিও সফলভাবে আপলোড হয়েছে!', 'ভিডিওটি আপনার শর্ট ভিডিও লিস্টে যুক্ত হয়েছে।', 'success');
        }
      } catch (err) {
        Swal.fire('ব্যর্থ হয়েছে', err.response?.data?.message || 'ভিডিও আপলোড করতে সমস্যা হয়েছে।', 'error');
      } finally {
        this.uploadingPortfolioVideo = false;
        e.target.value = '';
      }
    },
    async uploadVideoModalFile(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.uploadingPortfolioVideo = true;
      const formData = new FormData();
      formData.append('video', file);
      if (this.doctor?.id) formData.append('doctor_id', this.doctor.id);

      try {
        const res = await axios.post('/doctor-portal/portfolio/upload-video', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (res.data.success) {
          this.videoModalForm.video_url = res.data.video_url;
          if (res.data.thumbnail_url && !this.videoModalForm.thumbnail_url) {
            this.videoModalForm.thumbnail_url = res.data.thumbnail_url;
          }
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'ভিডিও আপলোড সম্পন্ন!', timer: 2000, showConfirmButton: false });
        }
      } catch (err) {
        Swal.fire('ত্রুটি', 'ভিডিও আপলোড ব্যর্থ হয়েছে।', 'error');
      } finally {
        this.uploadingPortfolioVideo = false;
      }
    },
    async uploadThumbnailModalFile(e) {
      const file = e.target.files[0];
      if (!file) return;
      const formData = new FormData();
      formData.append('media', file);
      if (this.doctor?.id) formData.append('doctor_id', this.doctor.id);

      try {
        const res = await axios.post('/doctor-portal/portfolio/upload-media', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (res.data.success) {
          this.videoModalForm.thumbnail_url = res.data.media_url;
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'থাম্বনেইল আপলোড সম্পন্ন!', timer: 2000, showConfirmButton: false });
        }
      } catch (err) {
        Swal.fire('ত্রুটি', 'ছবি আপলোড করতে সমস্যা হয়েছে।', 'error');
      }
    },
    async uploadPortfolioMediaFile(e, path) {
      const file = e.target.files[0];
      if (!file) return;
      const formData = new FormData();
      formData.append('media', file);
      if (this.doctor?.id) formData.append('doctor_id', this.doctor.id);

      try {
        const res = await axios.post('/doctor-portal/portfolio/upload-media', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (res.data.success) {
          if (path === 'hero.photo') {
            this.portfolioForm.hero.photo = res.data.media_url;
          } else if (path === 'about.photo') {
            this.portfolioForm.about.photo = res.data.media_url;
          }
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'ছবি আপলোড সম্পন্ন!', timer: 2000, showConfirmButton: false });
          this.savePortfolio(true);
        }
      } catch (err) {
        Swal.fire('ত্রুটি', 'ছবি আপলোড করতে সমস্যা হয়েছে।', 'error');
      }
    },
    async uploadGalleryImageFile(e) {
      const file = e.target.files[0];
      if (!file) return;
      const formData = new FormData();
      formData.append('media', file);
      if (this.doctor?.id) formData.append('doctor_id', this.doctor.id);

      try {
        const res = await axios.post('/doctor-portal/portfolio/upload-media', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (res.data.success) {
          if (!this.portfolioForm.gallery) this.portfolioForm.gallery = [];
          this.portfolioForm.gallery.unshift({
            id: Date.now(),
            image_url: res.data.media_url,
            caption: 'চেম্বার ও ক্লিনিক',
          });
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'গ্যালারিতে ছবি যোগ হয়েছে!', timer: 2000, showConfirmButton: false });
          this.savePortfolio(true);
        }
      } catch (err) {
        Swal.fire('ত্রুটি', 'ছবি আপলোড করতে সমস্যা হয়েছে।', 'error');
      } finally {
        e.target.value = '';
      }
    },
    async uploadGalleryModalFile(e) {
      const file = e.target.files[0];
      if (!file) return;
      const formData = new FormData();
      formData.append('media', file);
      if (this.doctor?.id) formData.append('doctor_id', this.doctor.id);

      try {
        const res = await axios.post('/doctor-portal/portfolio/upload-media', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (res.data.success) {
          this.galleryModalForm.image_url = res.data.media_url;
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'ছবি আপলোড সম্পন্ন!', timer: 2000, showConfirmButton: false });
        }
      } catch (err) {
        Swal.fire('ত্রুটি', 'ছবি আপলোড করতে সমস্যা হয়েছে।', 'error');
      }
    },

    /* Video CRUD */
    openAddVideoModal() {
      this.editingVideoIndex = null;
      this.videoModalForm = {
        title: '',
        video_url: '',
        thumbnail_url: '',
        duration: '0:45',
        tag: 'স্বাস্থ্য পরামর্শ',
        description: '',
        views: '1.2K',
      };
      this.showPortfolioVideoModal = true;
    },
    openEditVideoModal(v, idx) {
      this.editingVideoIndex = idx;
      this.videoModalForm = { ...v };
      this.showPortfolioVideoModal = true;
    },
    saveVideoModal() {
      if (!this.portfolioForm.short_videos) this.portfolioForm.short_videos = [];
      const item = { ...this.videoModalForm, id: this.videoModalForm.id || Date.now() };
      if (this.editingVideoIndex !== null) {
        this.portfolioForm.short_videos.splice(this.editingVideoIndex, 1, item);
      } else {
        this.portfolioForm.short_videos.unshift(item);
      }
      this.showPortfolioVideoModal = false;
      this.savePortfolio(true);
    },
    deleteShortVideo(idx) {
      this.portfolioForm.short_videos.splice(idx, 1);
      this.savePortfolio(true);
    },

    /* Service CRUD */
    openAddServiceModal() {
      this.editingServiceIndex = null;
      this.serviceModalForm = {
        title: '',
        description: '',
        icon: 'icofont-stethoscope',
      };
      this.showPortfolioServiceModal = true;
    },
    openEditServiceModal(s, idx) {
      this.editingServiceIndex = idx;
      this.serviceModalForm = { ...s };
      this.showPortfolioServiceModal = true;
    },
    saveServiceModal() {
      if (!this.portfolioForm.services) this.portfolioForm.services = [];
      const item = { ...this.serviceModalForm, id: this.serviceModalForm.id || Date.now() };
      if (this.editingServiceIndex !== null) {
        this.portfolioForm.services.splice(this.editingServiceIndex, 1, item);
      } else {
        this.portfolioForm.services.push(item);
      }
      this.showPortfolioServiceModal = false;
      this.savePortfolio(true);
    },
    deleteService(idx) {
      this.portfolioForm.services.splice(idx, 1);
      this.savePortfolio(true);
    },

    /* Timeline / Education CRUD */
    openAddTimelineModal() {
      this.editingTimelineIndex = null;
      this.timelineModalForm = {
        year: '',
        title: '',
        details: '',
        highlight: false,
      };
      this.showPortfolioTimelineModal = true;
    },
    openEditTimelineModal(t, idx) {
      this.editingTimelineIndex = idx;
      this.timelineModalForm = { ...t };
      this.showPortfolioTimelineModal = true;
    },
    saveTimelineModal() {
      if (!this.portfolioForm.education) this.portfolioForm.education = [];
      const item = { ...this.timelineModalForm, id: this.timelineModalForm.id || Date.now() };
      if (this.editingTimelineIndex !== null) {
        this.portfolioForm.education.splice(this.editingTimelineIndex, 1, item);
      } else {
        this.portfolioForm.education.push(item);
      }
      this.showPortfolioTimelineModal = false;
      this.savePortfolio(true);
    },
    deleteTimeline(idx) {
      this.portfolioForm.education.splice(idx, 1);
      this.savePortfolio(true);
    },

    /* Tips CRUD */
    openAddTipModal() {
      this.editingTipIndex = null;
      this.tipModalForm = {
        tag: 'পরামর্শ',
        title: '',
        excerpt: '',
        full_text: '',
      };
      this.showPortfolioTipModal = true;
    },
    openEditTipModal(t, idx) {
      this.editingTipIndex = idx;
      this.tipModalForm = { ...t };
      this.showPortfolioTipModal = true;
    },
    saveTipModal() {
      if (!this.portfolioForm.tips) this.portfolioForm.tips = [];
      const item = { ...this.tipModalForm, id: this.tipModalForm.id || Date.now() };
      if (this.editingTipIndex !== null) {
        this.portfolioForm.tips.splice(this.editingTipIndex, 1, item);
      } else {
        this.portfolioForm.tips.push(item);
      }
      this.showPortfolioTipModal = false;
      this.savePortfolio(true);
    },
    deleteTip(idx) {
      this.portfolioForm.tips.splice(idx, 1);
      this.savePortfolio(true);
    },

    /* Testimonials CRUD */
    openAddTestimonialModal() {
      this.editingTestimonialIndex = null;
      this.testimonialModalForm = {
        name: '',
        relation: 'রোগীর অভিভাবক',
        rating: 5,
        quote: '',
      };
      this.showPortfolioTestimonialModal = true;
    },
    openEditTestimonialModal(t, idx) {
      this.editingTestimonialIndex = idx;
      this.testimonialModalForm = { ...t };
      this.showPortfolioTestimonialModal = true;
    },
    saveTestimonialModal() {
      if (!this.portfolioForm.testimonials) this.portfolioForm.testimonials = [];
      const item = { ...this.testimonialModalForm, id: this.testimonialModalForm.id || Date.now() };
      if (this.editingTestimonialIndex !== null) {
        this.portfolioForm.testimonials.splice(this.editingTestimonialIndex, 1, item);
      } else {
        this.portfolioForm.testimonials.push(item);
      }
      this.showPortfolioTestimonialModal = false;
      this.savePortfolio(true);
    },
    deleteTestimonial(idx) {
      this.portfolioForm.testimonials.splice(idx, 1);
      this.savePortfolio(true);
    },

    /* FAQs CRUD */
    openAddFaqModal() {
      this.editingFaqIndex = null;
      this.faqModalForm = {
        question: '',
        answer: '',
      };
      this.showPortfolioFaqModal = true;
    },
    openEditFaqModal(f, idx) {
      this.editingFaqIndex = idx;
      this.faqModalForm = { ...f };
      this.showPortfolioFaqModal = true;
    },
    saveFaqModal() {
      if (!this.portfolioForm.faqs) this.portfolioForm.faqs = [];
      const item = { ...this.faqModalForm, id: this.faqModalForm.id || Date.now() };
      if (this.editingFaqIndex !== null) {
        this.portfolioForm.faqs.splice(this.editingFaqIndex, 1, item);
      } else {
        this.portfolioForm.faqs.push(item);
      }
      this.showPortfolioFaqModal = false;
      this.savePortfolio(true);
    },
    deleteFaq(idx) {
      this.portfolioForm.faqs.splice(idx, 1);
      this.savePortfolio(true);
    },

    /* Gallery CRUD */
    openAddGalleryModal() {
      this.galleryModalForm = {
        image_url: '',
        caption: '',
      };
      this.showPortfolioGalleryModal = true;
    },
    saveGalleryModal() {
      if (!this.portfolioForm.gallery) this.portfolioForm.gallery = [];
      const item = { ...this.galleryModalForm, id: Date.now() };
      this.portfolioForm.gallery.unshift(item);
      this.showPortfolioGalleryModal = false;
      this.savePortfolio(true);
    },
    deleteGalleryItem(idx) {
      this.portfolioForm.gallery.splice(idx, 1);
      this.savePortfolio(true);
    },

    logout() {
      localStorage.removeItem('saas_token');
      localStorage.removeItem('saas_user');
      localStorage.removeItem('saas_doctor');
      window.dispatchEvent(new Event('auth-change'));
      this.$router.push('/login');
    },
  },
};
</script>

<style scoped>
/* Modern SaaS Appointments Table */
.custom-appointment-table th {
  border-top: none;
  font-weight: 700;
  color: #64748b;
  padding: 12px 14px;
  background-color: #f8fafc;
  vertical-align: middle;
}
.custom-appointment-table td {
  padding: 12px 14px;
  vertical-align: middle;
  border-color: #f1f5f9;
}
.appointment-table-row:hover {
  background-color: #f8fafc !important;
}

/* Serial Badge */
.serial-badge {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: #eff6ff;
  color: #0284c7;
  font-weight: 800;
  font-size: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1.5px solid #bae6fd;
  flex-shrink: 0;
}

/* Consultation Type Badges */
.badge-type-video {
  display: inline-flex;
  align-items: center;
  background-color: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 9999px;
}
.badge-type-chamber {
  display: inline-flex;
  align-items: center;
  background-color: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 9999px;
}

/* Status Dropdown Badge */
.status-dropdown-badge {
  border-radius: 9999px;
  font-size: 11.5px;
  font-weight: 700;
  padding: 4px 10px;
  border: 1px solid transparent;
  outline: none !important;
  cursor: pointer;
  transition: all 0.2s ease;
  appearance: auto;
}
.status-dropdown-badge.status-pending {
  background-color: #fffbeb;
  color: #b45309;
  border-color: #fde68a;
}
.status-dropdown-badge.status-confirmed {
  background-color: #eff6ff;
  color: #0284c7;
  border-color: #bfdbfe;
}
.status-dropdown-badge.status-completed {
  background-color: #ecfdf5;
  color: #059669;
  border-color: #a7f3d0;
}
.status-dropdown-badge.status-cancelled {
  background-color: #fef2f2;
  color: #dc2626;
  border-color: #fecaca;
}

/* Primary Action Buttons */
.btn-row-action {
  padding: 5px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s ease;
  border: none;
  text-decoration: none !important;
  cursor: pointer;
}
.btn-row-video {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #ffffff !important;
  box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
}
.btn-row-video:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.4);
}
.btn-row-rx-write {
  background: linear-gradient(135deg, #0284c7, #0369a1);
  color: #ffffff !important;
  box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}
.btn-row-rx-write:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(2, 132, 199, 0.4);
}
.btn-row-rx-edit {
  background: #f0f9ff;
  color: #0284c7 !important;
  border: 1px solid #bae6fd !important;
}
.btn-row-rx-edit:hover {
  background: #e0f2fe;
}

/* Action Icons Cluster */
.action-icons-cluster {
  display: inline-flex;
  align-items: center;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 2px 4px;
  gap: 3px;
}
.action-icon-btn {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  cursor: pointer;
  transition: all 0.15s ease;
  text-decoration: none !important;
  font-size: 13px;
}
.action-icon-btn:hover {
  transform: scale(1.12);
}
.action-whatsapp {
  color: #16a34a !important;
}
.action-whatsapp:hover {
  background-color: #dcfce7 !important;
}
.action-vitals {
  color: #0891b2 !important;
}
.action-vitals:hover {
  background-color: #cffafe !important;
}
.action-reports {
  color: #e11d48 !important;
}
.action-reports:hover {
  background-color: #ffe4e6 !important;
}
.action-print {
  color: #475569 !important;
}
.action-print:hover {
  background-color: #e2e8f0 !important;
}

.hover-bg-light:hover {
  background-color: #f1f5f9;
}
.medicine-row-card {
  transition: all 0.2s ease;
}
.medicine-row-card:hover {
  border-color: #cbd5e1 !important;
}

/* Test Pill Items */
.test-pill-item {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin: 3px;
  cursor: pointer;
  background-color: #f8fafc;
  border: 1px solid #cbd5e1;
  color: #334155;
  transition: all 0.2s ease;
  user-select: none;
}
.test-pill-item:hover {
  background-color: #f1f5f9;
  border-color: #94a3b8;
}
.test-pill-item.active {
  background-color: #223a66 !important;
  border-color: #223a66 !important;
  color: #ffffff !important;
  box-shadow: 0 2px 4px rgba(34, 58, 102, 0.25);
}

/* Advice Card Tiles */
.advice-card-tile {
  display: flex;
  align-items: center;
  padding: 10px 14px;
  border-radius: 8px;
  cursor: pointer;
  background-color: #f8fafc;
  border: 1.5px solid #e2e8f0;
  color: #334155;
  transition: all 0.2s ease;
  user-select: none;
}
.advice-card-tile:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
}
.advice-card-tile.active {
  background-color: #ecfdf5 !important;
  border-color: #10b981 !important;
  color: #065f46 !important;
  box-shadow: 0 2px 5px rgba(16, 185, 129, 0.15);
}

/* Modern Dashboard Design System */
.bg-slate {
  background-color: #f8fafc;
}
.rounded-14 { border-radius: 14px; }
.rounded-16 { border-radius: 16px; }
.rounded-20 { border-radius: 20px; }
.rounded-24 { border-radius: 24px; }

.doctor-hero-banner {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-left: 5px solid #223a66 !important;
}

/* Doctor Avatar & Profile Styling */
.doctor-avatar-wrapper {
  width: 76px;
  height: 76px;
  position: relative;
  flex-shrink: 0;
}
.doctor-avatar-img {
  width: 76px !important;
  height: 76px !important;
  max-width: 76px !important;
  max-height: 76px !important;
  object-fit: cover !important;
  border-radius: 50% !important;
  border: 3px solid #f1f5f9;
  background-color: #f8fafc;
  display: block;
}
.avatar-camera-btn {
  bottom: 0;
  right: 0;
  width: 26px;
  height: 26px;
  background-color: #e12454;
  border: 2px solid #ffffff;
  border-radius: 50% !important;
  z-index: 5;
  transition: transform 0.2s ease, background-color 0.2s ease;
}
.avatar-camera-btn:hover {
  transform: scale(1.1);
  background-color: #c91b45;
}

@media (max-width: 575.98px) {
  .doctor-avatar-wrapper {
    width: 64px;
    height: 64px;
  }
  .doctor-avatar-img {
    width: 64px !important;
    height: 64px !important;
    max-width: 64px !important;
    max-height: 64px !important;
  }
  .doctor-hero-banner {
    padding: 14px !important;
    border-left-width: 4px !important;
  }
}

@media (max-width: 991.98px) {
  .doctor-dashboard-page {
    padding-bottom: 90px !important;
  }
}

.text-navy {
  color: #223a66;
}

.badge-specialty {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  border-radius: 6px;
}

.badge-emerald-soft {
  background: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
}

.kpi-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border: 1px solid #f1f5f9;
}
.kpi-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.06) !important;
  border-color: #e2e8f0;
}
.kpi-icon-box {
  width: 52px;
  height: 52px;
}
.bg-blue-soft { background: #eff6ff; }
.bg-rose-soft { background: #fff1f2; }
.bg-amber-soft { background: #fffbeb; }
.bg-emerald-soft { background: #ecfdf5; }
.bg-danger-soft { background: #fef2f2; }
.badge-success-soft {
  background: #ecfdf5;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

/* Doctor Navigation Cards Grid */
.doctor-nav-card {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 16px;
  overflow: hidden;
}
.doctor-nav-card:hover {
  transform: translateY(-2px);
  border-color: #cbd5e1;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}
.doctor-nav-card.active {
  background: #ffffff;
  border-color: #223a66 !important;
  box-shadow: 0 4px 18px rgba(34, 58, 102, 0.12) !important;
}
.doctor-nav-card.active::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3.5px;
  background: #223a66;
}
.doctor-nav-card.active .nav-card-title {
  color: #223a66 !important;
}
.nav-card-icon {
  width: 38px;
  height: 38px;
}
.rounded-12 {
  border-radius: 12px;
}
.bg-purple-soft {
  background: #f3e8ff;
}
.text-purple {
  color: #7e22ce;
}
.bg-purple {
  background: #7e22ce;
}

@media (max-width: 575.98px) {
  .doctor-nav-card {
    padding: 10px !important;
  }
  .nav-card-icon {
    width: 32px;
    height: 32px;
  }
  .nav-card-icon i {
    font-size: 16px !important;
  }
  .nav-card-title {
    font-size: 13px !important;
  }
}

.segmented-tabs-wrapper {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  max-width: 100%;
  overflow-x: auto;
  white-space: nowrap;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}
.segmented-tabs-wrapper::-webkit-scrollbar {
  display: none;
}
.tab-btn {
  background: transparent;
  color: #64748b;
  padding: 8px 18px;
  border-radius: 30px;
  font-size: 13px;
  transition: all 0.2s ease;
  white-space: nowrap;
  flex-shrink: 0;
}
.tab-btn:hover {
  color: #1e293b;
}
.tab-btn.active {
  background: #ffffff;
  color: #e12454;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
}

.live-radar-dot {
  width: 10px;
  height: 10px;
  background-color: #ef4444;
  border-radius: 50%;
  display: inline-block;
  animation: radarPulse 1.8s infinite;
}
.live-dot-green {
  width: 8px;
  height: 8px;
  background-color: #10b981;
  border-radius: 50%;
  display: inline-block;
  animation: greenPulse 2s infinite;
}

@keyframes radarPulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

@keyframes greenPulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.hero-ambient-glow-1 {
  position: absolute;
  width: 250px;
  height: 250px;
  background: radial-gradient(circle, rgba(225, 36, 84, 0.25) 0%, transparent 70%);
  top: -60px;
  right: -40px;
  pointer-events: none;
  border-radius: 50%;
}
.hero-ambient-glow-2 {
  position: absolute;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(6, 182, 212, 0.18) 0%, transparent 70%);
  bottom: -80px;
  left: 10%;
  pointer-events: none;
  border-radius: 50%;
}
</style>
