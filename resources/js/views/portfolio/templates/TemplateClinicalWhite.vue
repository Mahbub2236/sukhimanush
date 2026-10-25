<template>
  <div class="doctor-portfolio-theme theme-clinical-white">
    <!-- Top Emergency Helpline Bar -->
    <div class="clinical-topbar">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="topbar-left">
          <span class="emergency-badge">জরুরী হটলাইন</span>
          <a :href="'tel:' + (contactPhone || doctor.phone)" class="hotline-link">
            📞 {{ contactPhone || doctor.phone || '০১৭০০-০০০০০০' }}
          </a>
          <span class="topbar-divider">|</span>
          <span class="bmdc-text">🛡️ BMDC রেজিঃ {{ doctor.bmdc_number || 'যাচাইকৃত' }}</span>
        </div>
        <div class="topbar-right">
          <span>🕒 চেম্বার সিরিয়াল সকাল ৯:০০ - রাত ৯:০০</span>
        </div>
      </div>
    </div>

    <!-- Main Clinical Header / Navbar -->
    <header class="clinical-header" :class="{ 'scrolled': isScrolled }">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="brand">
          <router-link :to="'/portfolio/' + (doctor.slug || doctor.id)" class="brand-link">
            <span class="brand-cross">✚</span>
            <div>
              <span class="brand-name">{{ doctorDisplayName }}</span>
              <span class="brand-sub">{{ doctorSpecialtyEyebrow || 'বিশেষজ্ঞ কনসালটেন্ট' }}</span>
            </div>
          </router-link>
        </div>

        <nav class="nav-links">
          <a href="#about">পরিচিতি</a>
          <a href="#services">সেবাসমূহ</a>
          <a href="#process">চিকিৎসা পদ্ধতি</a>
          <a href="#chambers">চেম্বার শিডিউল</a>
          <a href="#education">যোগ্যতা</a>
          <a href="#tips">স্বাস্থ্য টিপস</a>
          <a href="#testimonials">রোগীর রিভিউ</a>
          <a href="#faq">জিজ্ঞাসা</a>
        </nav>

        <div class="header-action">
          <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-primary btn-sm rounded-pill font-weight-bold px-3">
            সিরিয়াল বুকিং
          </router-link>
        </div>

        <button class="burger" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="মেনু">
          <span></span><span></span><span></span>
        </button>
      </div>

      <!-- Mobile Menu Dropdown -->
      <div class="mobile-menu" :class="{ 'open': mobileMenuOpen }">
        <a href="#about" @click="mobileMenuOpen = false">পরিচিতি</a>
        <a href="#services" @click="mobileMenuOpen = false">সেবাসমূহ</a>
        <a href="#process" @click="mobileMenuOpen = false">চিকিৎসা পদ্ধতি</a>
        <a href="#chambers" @click="mobileMenuOpen = false">চেম্বার শিডিউল</a>
        <a href="#education" @click="mobileMenuOpen = false">যোগ্যতা</a>
        <a href="#tips" @click="mobileMenuOpen = false">স্বাস্থ্য টিপস</a>
        <a href="#testimonials" @click="mobileMenuOpen = false">রোগীর রিভিউ</a>
        <a href="#faq" @click="mobileMenuOpen = false">জিজ্ঞাসা</a>
      </div>
    </header>

    <!-- ================= 1. IN-HERO BOOKING WIDGET HERO ================= -->
    <section class="hero-section">
      <div class="wrap hero-container">
        <!-- Left Column: Doctor Intro -->
        <div class="hero-left">
          <div class="eyebrow-pill">
            <span class="pulse-dot"></span>
            {{ doctorSpecialtyEyebrow || 'সিনিয়র কনসালটেন্ট ও সার্জন' }}
          </div>

          <h1 class="hero-title">
            {{ doctorDisplayName }}
          </h1>

          <div class="qualification-strip">
            <span v-for="(badge, bIdx) in qualificationBadges" :key="bIdx" class="badge-pill">
              {{ badge }}
            </span>
          </div>

          <p class="hero-hospital" v-if="doctorHospitalText">
            🏥 <strong>{{ doctorHospitalText }}</strong>
          </p>

          <p class="hero-bio">
            {{ aboutBio || defaultDynamicBio }}
          </p>

          <div class="hero-quick-ctas">
            <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-call">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;">
                <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .7 2.9a2 2 0 0 1-.4 2.1L8 10a16 16 0 0 0 6 6l1.3-1.4a2 2 0 0 1 2.1-.4c.9.4 1.9.6 2.9.7a2 2 0 0 1 1.7 2z"/>
              </svg>
              সরাসরি কল: {{ contactPhone || doctor.phone || 'কল করুন' }}
            </a>
            <a href="#chambers" class="btn btn-outline-clinical">
              চেম্বার তালিকা ও সময়সূচি
            </a>
          </div>
        </div>

        <!-- Right Column: Quick Appointment Booking Box + Doctor Photo -->
        <div class="hero-right">
          <div class="booking-widget-card">
            <!-- Doctor mini banner inside widget -->
            <div class="widget-header">
              <div class="doc-avatar-wrap">
                <img :src="doctorHeroPhoto" :alt="doctor.name" class="doc-mini-avatar" />
                <span class="active-status-dot" title="সিরিয়াল গ্রহণ চলছে"></span>
              </div>
              <div>
                <h4 class="widget-title">ইনস্ট্যান্ট সিরিয়াল বুকিং</h4>
                <p class="widget-sub">রোগী দেখার সুবিধাজনক স্লট নির্বাচন করুন</p>
              </div>
            </div>

            <!-- Widget Chamber Selector Form -->
            <div class="widget-form">
              <div class="form-group mb-3">
                <label class="form-label font-weight-bold text-dark small">চেম্বার নির্বাচন করুন:</label>
                <div class="custom-select-box">
                  <span class="select-icon">🏥</span>
                  <select v-model="selectedChamberIndex" class="form-control">
                    <option v-for="(ch, cIdx) in displayChambers" :key="cIdx" :value="cIdx">
                      {{ ch.name }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Selected Chamber Schedule Info Box -->
              <div class="chamber-info-card" v-if="displayChambers[selectedChamberIndex]">
                <div class="info-row">
                  <span class="lbl">📍 ঠিকানা:</span>
                  <span class="val">{{ displayChambers[selectedChamberIndex].address }}</span>
                </div>
                <div class="info-row" v-if="displayChambers[selectedChamberIndex].schedule">
                  <span class="lbl">🕒 সময়সূচি:</span>
                  <span class="val highlight">{{ displayChambers[selectedChamberIndex].schedule }}</span>
                </div>
                <div class="info-row" v-if="displayChambers[selectedChamberIndex].phone">
                  <span class="lbl">📞 সিরিয়াল হেল্পলাইন:</span>
                  <span class="val"><a :href="'tel:' + displayChambers[selectedChamberIndex].phone">{{ displayChambers[selectedChamberIndex].phone }}</a></span>
                </div>
              </div>

              <!-- Booking CTA Button -->
              <router-link 
                :to="'/appoinment?doctor_id=' + doctor.id + (displayChambers[selectedChamberIndex]?.id ? '&chamber_id=' + displayChambers[selectedChamberIndex].id : '')" 
                class="btn btn-confirm-booking"
              >
                অনলাইনে সরাসরি কনফার্ম করুন ➜
              </router-link>

              <div class="widget-guarantee">
                🔒 তাৎক্ষণিক কনফার্মেশন ও এসএমএস নোটিফিকেশন প্রদান করা হয়।
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 2. FOUR-COLUMN CLINICAL STATS BAR ================= -->
    <section class="stats-strip">
      <div class="wrap">
        <div class="stats-grid">
          <div v-for="(st, stIdx) in displayStats" :key="stIdx" class="stat-card">
            <div class="stat-icon-wrap">{{ st.icon }}</div>
            <div class="stat-num">{{ st.num }}</div>
            <div class="stat-lbl">{{ st.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 3. ABOUT DOCTOR & CLINICAL VALUES ================= -->
    <section class="about-section" id="about">
      <div class="wrap">
        <div class="about-grid">
          <div class="about-photo-col">
            <div class="about-photo-card">
              <img :src="doctorAboutPhoto || doctorHeroPhoto" :alt="doctorDisplayName" class="about-img" />
              <div class="bmdc-verified-box">
                <span class="seal-icon">🛡️</span>
                <div>
                  <strong>বিএমডিসি অনুমোদিত</strong>
                  <div>রেজিঃ {{ doctor.bmdc_number || 'A-89421' }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="about-content-col">
            <span class="section-tag">চিকিৎসক পরিচিতি</span>
            <h2>{{ aboutHeading }}</h2>
            <p class="lead-bio">{{ aboutBio || defaultDynamicBio }}</p>
            <p class="secondary-bio">
              আধুনিক স্বাস্থ্যসেবায় রোগী ও চিকিৎসকের মধ্যে আন্তরিক সম্পর্ক সবচেয়ে গুরুত্বপূর্ণ। প্রতিটি রোগীর শারীরিক পরীক্ষা ও সঠিক রোগ নির্ণয়পূর্বক আন্তর্জাতিক চিকিৎসা প্রটোকল মেনে সর্বোত্তম ব্যবস্থাপত্র নিশ্চিত করাই আমাদের লক্ষ্য।
            </p>

            <div class="clinical-pillars-row">
              <div class="pillar-box">
                <div class="pillar-ico">❤️</div>
                <strong>রোগীকেন্দ্রিক যত্ন</strong>
                <p>মনোযোগ দিয়ে কথা শোনা ও পূর্ণ সময় নিয়ে সেবা।</p>
              </div>
              <div class="pillar-box">
                <div class="pillar-ico">🔬</div>
                <strong>সঠিক ডায়াগনোসিস</strong>
                <p>অপ্রয়োজনীয় টেস্ট এড়িয়ে নির্ভুল রোগ নিরূপণ।</p>
              </div>
              <div class="pillar-box">
                <div class="pillar-ico">🤝</div>
                <strong>ফলোআপ কেয়ার</strong>
                <p>সুস্থ না হওয়া পর্যন্ত সার্বক্ষণিক দিকনির্দেশনা।</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 4. SPECIALIZED SERVICES (CLINICAL GRID) ================= -->
    <section class="services-section" id="services">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">চিকিৎসা সেবা</span>
          <h2>বিশেষায়িত চিকিৎসা ও ডায়াগনস্টিক সেবাসমূহ</h2>
          <p>আধুনিক চিকিৎসা পদ্ধতি ও নিয়মিত ফলোআপের সমন্বয়ে পরিপূর্ণ স্বাস্থ্য সুরক্ষা।</p>
        </div>

        <div class="clinical-services-grid">
          <div v-for="(serv, sIdx) in displayServices" :key="sIdx" class="clinical-service-item">
            <div class="service-left-icon">
              <span>🩺</span>
            </div>
            <div class="service-right-content">
              <h4>{{ serv.title }}</h4>
              <p>{{ serv.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 5. PATIENT CARE PATHWAY (PROCESS) ================= -->
    <section class="process-section" id="process">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">রোগী দেখার ধাপ</span>
          <h2>সহজ ৪ ধাপে চিকিৎসকের পরামর্শ নিন</h2>
          <p>ঝামেলাহীন ও সুশৃঙ্খল উপায়ে সেবা গ্রহণের প্রক্রিয়া</p>
        </div>

        <div class="process-steps-grid">
          <div v-for="(pr, pIdx) in displayProcess" :key="pIdx" class="process-step-card">
            <div class="step-badge">{{ pr.step }}</div>
            <div class="step-icon">{{ pr.icon }}</div>
            <h4>{{ pr.title }}</h4>
            <p>{{ pr.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 6. CHAMBER & VISITING SCHEDULE BOARD ================= -->
    <section class="chambers-section" id="chambers">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">হাসপাতাল ও চেম্বার সমূহ</span>
          <h2>চেম্বার শিডিউল ও অ্যাপয়েন্টমেন্ট তথ্য</h2>
          <p>সরাসরি চেম্বারে গিয়ে পরামর্শ নিতে নিচের সময়সূচি অনুযায়ী যোগাযোগ করুন।</p>
        </div>

        <div class="schedule-table-wrap">
          <div v-for="(chamber, idx) in displayChambers" :key="idx" class="schedule-board-row">
            <div class="schedule-col-name">
              <div class="hosp-badge">চেম্বার শাখা #{{ formatBnNumber(idx + 1) }}</div>
              <h3>{{ chamber.name }}</h3>
              <p class="hosp-address">📍 {{ chamber.address }}</p>
            </div>
            
            <div class="schedule-col-time">
              <div class="time-pill">
                <span class="clock-icon">🕒</span>
                <span>{{ chamber.schedule || 'প্রতিদিন বিকাল ৫টা - রাত ৮টা' }}</span>
              </div>
            </div>

            <div class="schedule-col-contact">
              <a :href="'tel:' + (chamber.phone || contactPhone)" class="btn btn-sm btn-phone-call">
                📞 {{ chamber.phone || contactPhone }}
              </a>
              <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-sm btn-book-slot">
                সিরিয়াল নিন
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 7. ACADEMIC CREDENTIALS & EDUCATION ================= -->
    <section class="education-section" id="education">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">শিক্ষাজীবন ও ডিগ্রি</span>
          <h2>উচ্চতর চিকিৎসা শিক্ষা ও ফেলোশিপ</h2>
          <p>দেশ-বিদেশের স্বনামধন্য মেডিকেল প্রতিষ্ঠান থেকে অর্জিত ডিগ্রি ও বিশেষ প্রশিক্ষণ</p>
        </div>

        <div class="education-grid">
          <div v-for="(edu, eIdx) in displayEducation" :key="eIdx" class="edu-card">
            <div class="edu-year-badge">{{ edu.year }}</div>
            <h3>{{ edu.institution }}</h3>
            <p>{{ edu.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 8. SHORT VIDEOS & REELS ================= -->
    <section class="reels-section" id="reels" v-if="displayShortVideos && displayShortVideos.length > 0">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">ভিডিও বার্তা</span>
          <h2>চিকিৎসকের জরুরি স্বাস্থ্য পরামর্শ ও রিলস</h2>
          <p>ক্লিক করে সরাসরি ভিডিওটি দেখুন এবং গুরুত্বপূর্ণ সতর্কতাগুলো জেনে নিন।</p>
        </div>

        <div class="clinical-reels-row">
          <div v-for="(v, vIdx) in displayShortVideos" :key="vIdx" class="clinical-reel-card" @click="$emit('open-video', v)">
            <div class="reel-box">
              <img :src="v.thumbnail_url || '/images/team/1.jpg'" class="reel-img" />
              <div class="play-overlay">
                <span class="play-btn-circle">▶</span>
              </div>
              <span class="duration-badge" v-if="v.duration">{{ v.duration }}</span>
            </div>
            <div class="reel-meta">
              <span class="reel-tag-pill" v-if="v.tag">{{ v.tag }}</span>
              <h5>{{ v.title }}</h5>
              <span class="watch-text">ভিডিও দেখুন ➜</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 9. HEALTH AWARENESS TIPS ================= -->
    <section class="tips-section" id="tips">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">স্বাস্থ্য পরামর্শ</span>
          <h2>দৈনন্দিন সুস্থতার জরুরি স্বাস্থ্য বার্তা</h2>
          <p>সুস্থ থাকার বৈজ্ঞানিক উপায় ও রোগ প্রতিরোধের মূল্যবান পরামর্শ</p>
        </div>

        <div class="tips-grid">
          <div v-for="(tip, tIdx) in displayTips" :key="tIdx" class="tip-card" @click="$emit('open-tip', tip)">
            <span class="tip-kicker">{{ tip.tag }}</span>
            <h4>{{ tip.title }}</h4>
            <p>{{ tip.shortDesc }}</p>
            <div class="tip-action">বিস্তারিত পড়ুন ➜</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 10. PATIENT TESTIMONIALS & REVIEWS ================= -->
    <section class="testimonials-section" id="testimonials">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">রোগীদের অভিজ্ঞতা</span>
          <h2>সুস্থ হওয়া রোগীদের আস্থা ও কৃতজ্ঞতা</h2>
          <p>রোগী ও তাদের স্বজনদের বাস্তব অভিজ্ঞতা ও ফিডব্যাক</p>
        </div>

        <div class="testimonials-grid">
          <div v-for="(test, tIdx) in displayTestimonials" :key="tIdx" class="testimonial-card">
            <div class="stars-row">★★★★★</div>
            <p class="test-comment">"{{ test.comment }}"</p>
            <div class="test-author-box">
              <div class="author-avatar">{{ test.author_name.charAt(0) }}</div>
              <div>
                <strong>{{ test.author_name }}</strong>
                <span class="author-loc" v-if="test.author_location"> • {{ test.author_location }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 11. CLINIC & CHAMBER GALLERY ================= -->
    <section class="gallery-section" id="gallery" v-if="displayGallery && displayGallery.length > 0">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">ক্লিনিক্যাল পরিবেশ</span>
          <h2>চেম্বার ও চিকিৎসা পরিবেশের একাংশ</h2>
        </div>

        <div class="gallery-grid">
          <div v-for="(img, gIdx) in displayGallery" :key="gIdx" class="gallery-item">
            <img :src="img" :alt="'Chamber ' + (gIdx + 1)" />
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 12. FREQUENTLY ASKED QUESTIONS (FAQ) ================= -->
    <section class="faq-section" id="faq">
      <div class="wrap">
        <div class="section-title-box text-center">
          <span class="section-tag">সাধারণ জিজ্ঞাসা</span>
          <h2>রোগীদের সচরাচর জিজ্ঞাসিত প্রশ্নাবলী</h2>
          <p>সিরিয়াল বুকিং, রিপোর্ট দেখানো ও নিয়মাবলী সম্পর্কে স্পষ্ট ধারণা</p>
        </div>

        <div class="faq-accordion-wrap">
          <div 
            v-for="(faq, fIdx) in displayFaqs" 
            :key="fIdx" 
            class="faq-card" 
            :class="{ 'active': activeFaqIndex === fIdx }"
            @click="activeFaqIndex = activeFaqIndex === fIdx ? null : fIdx"
          >
            <div class="faq-question">
              <h4>{{ faq.question }}</h4>
              <span class="faq-icon">{{ activeFaqIndex === fIdx ? '−' : '+' }}</span>
            </div>
            <div class="faq-answer" v-show="activeFaqIndex === fIdx">
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 13. EMERGENCY CTA BANNER ================= -->
    <section class="cta-banner">
      <div class="wrap">
        <div class="cta-box d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h3>আপনার স্বাস্থ্য সুরক্ষায় আমরা সদা প্রস্তুত</h3>
            <p>সরাসরি চেম্বারে এসে বা অনলাইন ভিডিও কলের মাধ্যমে পরামর্শের জন্য এখনই যোগাযোগ করুন।</p>
          </div>
          <div class="cta-buttons">
            <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-cta-phone">
              📞 {{ contactPhone || doctor.phone }}
            </a>
            <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cta-booking">
              অনলাইনে সিরিয়াল বুক করুন ➜
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="clinical-footer">
      <div class="wrap d-flex justify-content-between align-items-center flex-wrap">
        <div class="footer-left">
          <strong>{{ doctorDisplayName }}</strong> — {{ doctorSpecialtyEyebrow }}
          <div class="small text-muted mt-1">সর্বাধুনিক চিকিৎসা ও রোগীর পূর্ণ সুস্থতায় নিবেদিতপ্রাণ।</div>
        </div>
        <div class="footer-right">
          © {{ new Date().getFullYear() }} সর্বস্বত্ব সংরক্ষিত।
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'TemplateClinicalWhite',
  props: {
    doctor: { type: Object, required: true },
    portfolioData: { type: Object, default: () => ({}) },
    displayServices: { type: Array, default: () => [] },
    displayEducation: { type: Array, default: () => [] },
    displayTips: { type: Array, default: () => [] },
    displayShortVideos: { type: Array, default: () => [] },
    displayTestimonials: { type: Array, default: () => [] },
    displayFaqs: { type: Array, default: () => [] },
    displayGallery: { type: Array, default: () => [] },
    displayChambers: { type: Array, default: () => [] },
    displayStats: { type: Array, default: () => [] },
    displayProcess: { type: Array, default: () => [] },
    doctorDisplayName: { type: String, default: '' },
    doctorInitial: { type: String, default: 'ড' },
    doctorSpecialtyEyebrow: { type: String, default: '' },
    doctorDepartmentText: { type: String, default: '' },
    doctorHospitalText: { type: String, default: '' },
    doctorHeroPhoto: { type: String, default: '' },
    doctorAboutPhoto: { type: String, default: '' },
    doctorStampHtml: { type: String, default: '' },
    qualificationBadges: { type: Array, default: () => [] },
    aboutHeading: { type: String, default: '' },
    aboutBio: { type: String, default: '' },
    defaultDynamicBio: { type: String, default: '' },
    contactPhone: { type: String, default: '' },
    contactEmail: { type: String, default: '' },
    formatBnNumber: { type: Function, required: true },
  },
  data() {
    return {
      isScrolled: false,
      mobileMenuOpen: false,
      selectedChamberIndex: 0,
      activeFaqIndex: 0,
    };
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    handleScroll() {
      this.isScrolled = window.scrollY > 40;
    }
  }
};
</script>

<style scoped>
.theme-clinical-white {
  background-color: #FFFFFF;
  color: #0F172A;
  font-family: 'Hind Siliguri', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  line-height: 1.6;
}

.wrap { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* Topbar */
.clinical-topbar { background: #F8FAFC; border-bottom: 1px solid #E2E8F0; padding: 8px 0; font-size: 0.88rem; color: #475569; }
.emergency-badge { background: #EF4444; color: #fff; font-size: 0.75rem; font-weight: bold; padding: 2px 8px; border-radius: 4px; margin-right: 8px; }
.hotline-link { color: #0284C7; font-weight: bold; text-decoration: none; }
.topbar-divider { margin: 0 10px; color: #CBD5E1; }
.bmdc-text { color: #15803D; font-weight: 600; }

/* Navbar */
.clinical-header { position: sticky; top: 0; z-index: 100; background: #FFFFFF; border-bottom: 1px solid #E2E8F0; transition: all 0.3s; padding: 14px 0; }
.clinical-header.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
.brand-link { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
.brand-cross { width: 34px; height: 34px; border-radius: 8px; background: #0284C7; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; }
.brand-name { font-size: 1.15rem; font-weight: 800; color: #0F172A; display: block; line-height: 1.2; }
.brand-sub { font-size: 0.78rem; color: #0284C7; font-weight: 600; display: block; }
.nav-links { display: flex; gap: 20px; }
.nav-links a { color: #334155; text-decoration: none; font-size: 0.92rem; font-weight: 600; transition: color 0.2s; }
.nav-links a:hover { color: #0284C7; }
.burger { display: none; background: none; border: none; flex-direction: column; gap: 5px; cursor: pointer; }
.burger span { width: 24px; height: 2px; background: #0F172A; }
.mobile-menu { display: none; flex-direction: column; padding: 16px; background: #fff; border-bottom: 1px solid #E2E8F0; gap: 10px; }
.mobile-menu.open { display: flex; }

/* Hero */
.hero-section { padding: 60px 0 80px; background: linear-gradient(180deg, #F0F9FF 0%, #FFFFFF 100%); }
.hero-container { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 40px; align-items: center; }
.eyebrow-pill { display: inline-flex; align-items: center; gap: 8px; background: #E0F2FE; color: #0284C7; padding: 6px 14px; border-radius: 99px; font-size: 0.88rem; font-weight: 700; margin-bottom: 16px; }
.pulse-dot { width: 8px; height: 8px; border-radius: 50%; background: #0284C7; animation: pulse 1.5s infinite; }
@keyframes pulse { 0% { transform: scale(0.95); opacity: 1; } 50% { transform: scale(1.4); opacity: 0.4; } 100% { transform: scale(0.95); opacity: 1; } }
.hero-title { font-size: 2.8rem; font-weight: 900; color: #0F172A; line-height: 1.2; margin-bottom: 16px; }
.qualification-strip { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
.badge-pill { background: #FFFFFF; border: 1px solid #BAE6FD; color: #0369A1; padding: 4px 12px; border-radius: 99px; font-size: 0.85rem; font-weight: 600; }
.hero-hospital { font-size: 1.05rem; color: #334155; margin-bottom: 14px; }
.hero-bio { font-size: 1.05rem; color: #475569; margin-bottom: 24px; line-height: 1.7; }
.hero-quick-ctas { display: flex; gap: 14px; flex-wrap: wrap; }
.btn-call { background: #0284C7; color: #fff; padding: 12px 24px; border-radius: 99px; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; box-shadow: 0 4px 14px rgba(2,132,199,0.3); }
.btn-outline-clinical { background: #FFFFFF; border: 1.5px solid #CBD5E1; color: #334155; padding: 12px 24px; border-radius: 99px; font-weight: bold; text-decoration: none; }

/* Booking Widget Card */
.booking-widget-card { background: #FFFFFF; border: 1px solid #BAE6FD; border-radius: 24px; padding: 28px; box-shadow: 0 16px 40px rgba(2,132,199,0.08); }
.widget-header { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #F1F5F9; }
.doc-avatar-wrap { position: relative; width: 64px; height: 64px; border-radius: 16px; overflow: hidden; border: 2px solid #0284C7; flex: none; }
.doc-mini-avatar { width: 100%; height: 100%; object-fit: cover; }
.active-status-dot { position: absolute; bottom: 2px; right: 2px; width: 12px; height: 12px; border-radius: 50%; background: #22C55E; border: 2px solid #fff; }
.widget-title { font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0; }
.widget-sub { font-size: 0.85rem; color: #64748B; margin: 0; }
.custom-select-box { position: relative; }
.select-icon { position: absolute; left: 12px; top: 10px; font-size: 1rem; z-index: 2; }
.custom-select-box select { padding-left: 36px; border-radius: 10px; border-color: #CBD5E1; font-weight: 600; font-size: 0.95rem; }
.chamber-info-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 14px; margin-bottom: 18px; font-size: 0.88rem; }
.info-row { display: flex; gap: 8px; margin-bottom: 6px; }
.info-row:last-child { margin-bottom: 0; }
.info-row .lbl { color: #64748B; font-weight: 600; flex: none; }
.info-row .val { color: #0F172A; font-weight: 600; }
.info-row .val.highlight { color: #0284C7; }
.btn-confirm-booking { display: block; width: 100%; background: #0284C7; color: #fff; text-align: center; padding: 14px; border-radius: 12px; font-weight: 800; font-size: 1rem; text-decoration: none; box-shadow: 0 4px 14px rgba(2,132,199,0.3); transition: background 0.2s; }
.btn-confirm-booking:hover { background: #0369A1; }
.widget-guarantee { text-align: center; font-size: 0.78rem; color: #64748B; margin-top: 12px; }

/* Stats Strip */
.stats-strip { background: #FFFFFF; border-top: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0; padding: 36px 0; }
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; text-align: center; }
.stat-card { border-right: 1px solid #F1F5F9; }
.stat-card:last-child { border-right: none; }
.stat-icon-wrap { font-size: 26px; margin-bottom: 6px; }
.stat-num { font-size: 2.2rem; font-weight: 900; color: #0284C7; line-height: 1.1; margin-bottom: 4px; }
.stat-lbl { font-size: 0.92rem; color: #475569; font-weight: 600; }

/* Section Header Generic */
.section-title-box { max-width: 720px; margin: 0 auto 48px; }
.section-tag { background: #E0F2FE; color: #0284C7; font-size: 0.85rem; font-weight: 700; padding: 4px 14px; border-radius: 99px; display: inline-block; margin-bottom: 10px; }
.section-title-box h2 { font-size: 2.2rem; font-weight: 800; color: #0F172A; margin-bottom: 10px; }
.section-title-box p { color: #475569; font-size: 1rem; margin: 0; }

/* About Section */
.about-section { padding: 90px 0; background: #FFFFFF; }
.about-grid { display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 50px; align-items: center; }
.about-photo-card { position: relative; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); }
.about-img { width: 100%; height: 480px; object-fit: cover; }
.bmdc-verified-box { position: absolute; bottom: 20px; left: 20px; right: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 14px 18px; border-radius: 16px; border: 1px solid #BAE6FD; display: flex; align-items: center; gap: 14px; }
.seal-icon { font-size: 28px; }
.lead-bio { font-size: 1.15rem; font-weight: 600; color: #1E293B; margin-bottom: 14px; line-height: 1.7; }
.secondary-bio { color: #475569; margin-bottom: 28px; line-height: 1.7; }
.clinical-pillars-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.pillar-box { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 14px; padding: 16px; }
.pillar-ico { font-size: 22px; margin-bottom: 6px; }
.pillar-box strong { font-size: 0.98rem; display: block; margin-bottom: 4px; color: #0F172A; }
.pillar-box p { font-size: 0.82rem; color: #64748B; margin: 0; }

/* Services */
.services-section { padding: 90px 0; background: #F8FAFC; border-top: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0; }
.clinical-services-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.clinical-service-item { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 18px; padding: 24px; display: flex; gap: 16px; align-items: flex-start; box-shadow: 0 4px 12px rgba(15,23,42,0.03); transition: transform 0.2s, border-color 0.2s; }
.clinical-service-item:hover { transform: translateY(-4px); border-color: #0284C7; }
.service-left-icon { width: 48px; height: 48px; border-radius: 14px; background: #E0F2FE; font-size: 24px; display: flex; align-items: center; justify-content: center; flex: none; }
.service-right-content h4 { font-size: 1.15rem; font-weight: 700; margin-bottom: 6px; }
.service-right-content p { color: #64748B; font-size: 0.9rem; margin: 0; }

/* Process */
.process-section { padding: 90px 0; background: #FFFFFF; }
.process-steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.process-step-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 20px; padding: 28px 20px; position: relative; text-align: center; }
.step-badge { position: absolute; top: 14px; right: 14px; background: #E0F2FE; color: #0284C7; font-weight: 800; font-size: 0.8rem; padding: 2px 8px; border-radius: 99px; }
.step-icon { font-size: 32px; margin-bottom: 14px; }
.process-step-card h4 { font-size: 1.1rem; font-weight: 800; color: #0F172A; margin-bottom: 8px; }
.process-step-card p { font-size: 0.88rem; color: #64748B; margin: 0; }

/* Chambers Schedule */
.chambers-section { padding: 90px 0; background: #F8FAFC; border-top: 1px solid #E2E8F0; }
.schedule-table-wrap { display: flex; flex-direction: column; gap: 16px; max-width: 980px; margin: 0 auto; }
.schedule-board-row { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 18px; padding: 22px 28px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 14px rgba(15,23,42,0.04); }
.hosp-badge { font-size: 0.75rem; color: #0369A1; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
.schedule-col-name h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: 4px; }
.hosp-address { color: #64748B; font-size: 0.9rem; margin: 0; }
.time-pill { background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; padding: 8px 16px; border-radius: 99px; font-weight: 600; font-size: 0.92rem; display: flex; align-items: center; gap: 6px; }
.schedule-col-contact { display: flex; gap: 10px; align-items: center; }
.btn-phone-call { background: #F8FAFC; border: 1px solid #CBD5E1; color: #0F172A; font-weight: 600; padding: 8px 16px; border-radius: 99px; text-decoration: none; }
.btn-book-slot { background: #0284C7; color: #fff; font-weight: 600; padding: 8px 18px; border-radius: 99px; text-decoration: none; }

/* Education */
.education-section { padding: 90px 0; background: #FFFFFF; }
.education-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; max-width: 980px; margin: 0 auto; }
.edu-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 18px; padding: 24px; position: relative; }
.edu-year-badge { display: inline-block; background: #0284C7; color: #fff; font-size: 0.78rem; font-weight: bold; padding: 2px 10px; border-radius: 99px; margin-bottom: 10px; }
.edu-card h3 { font-size: 1.15rem; font-weight: 800; color: #0F172A; margin-bottom: 6px; }
.edu-card p { font-size: 0.9rem; color: #64748B; margin: 0; }

/* Reels */
.reels-section { padding: 90px 0; background: #F8FAFC; border-top: 1px solid #E2E8F0; }
.clinical-reels-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.clinical-reel-card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 18px; overflow: hidden; cursor: pointer; transition: transform 0.25s; box-shadow: 0 4px 14px rgba(15,23,42,0.04); }
.clinical-reel-card:hover { transform: translateY(-5px); border-color: #0284C7; }
.reel-box { position: relative; width: 100%; height: 260px; background: #000; }
.reel-img { width: 100%; height: 100%; object-fit: cover; }
.play-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; }
.play-btn-circle { width: 50px; height: 50px; border-radius: 50%; background: #0284C7; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.duration-badge { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #fff; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; }
.reel-meta { padding: 18px; }
.reel-tag-pill { background: #E0F2FE; color: #0369A1; font-size: 0.75rem; font-weight: bold; padding: 2px 8px; border-radius: 99px; display: inline-block; margin-bottom: 6px; }
.reel-meta h5 { font-size: 1.05rem; font-weight: 700; margin-bottom: 8px; line-height: 1.4; }
.watch-text { color: #0284C7; font-size: 0.88rem; font-weight: 700; }

/* Health Tips */
.tips-section { padding: 90px 0; background: #FFFFFF; }
.tips-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
.tip-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 18px; padding: 28px; cursor: pointer; transition: transform 0.2s, border-color 0.2s; }
.tip-card:hover { transform: translateY(-4px); border-color: #0284C7; }
.tip-kicker { color: #0284C7; font-weight: bold; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 8px; display: block; }
.tip-card h4 { font-size: 1.2rem; font-weight: 800; color: #0F172A; margin-bottom: 8px; line-height: 1.4; }
.tip-card p { color: #64748B; font-size: 0.92rem; margin-bottom: 14px; }
.tip-action { color: #0284C7; font-weight: 700; font-size: 0.9rem; }

/* Testimonials */
.testimonials-section { padding: 90px 0; background: #F8FAFC; border-top: 1px solid #E2E8F0; }
.testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.testimonial-card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 20px; padding: 28px; box-shadow: 0 4px 14px rgba(15,23,42,0.03); }
.stars-row { color: #F59E0B; font-size: 1.1rem; margin-bottom: 12px; }
.test-comment { color: #334155; font-size: 0.95rem; font-style: italic; line-height: 1.6; margin-bottom: 20px; }
.test-author-box { display: flex; align-items: center; gap: 12px; }
.author-avatar { width: 40px; height: 40px; border-radius: 50%; background: #0284C7; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; }
.author-loc { color: #64748B; font-size: 0.85rem; }

/* Gallery */
.gallery-section { padding: 90px 0; background: #FFFFFF; }
.gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
.gallery-item { border-radius: 16px; overflow: hidden; height: 200px; }
.gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.gallery-item:hover img { transform: scale(1.05); }

/* FAQ */
.faq-section { padding: 90px 0; background: #F8FAFC; border-top: 1px solid #E2E8F0; }
.faq-accordion-wrap { max-width: 840px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
.faq-card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px 24px; cursor: pointer; transition: border-color 0.2s; }
.faq-card.active { border-color: #0284C7; box-shadow: 0 4px 16px rgba(2,132,199,0.06); }
.faq-question { display: flex; justify-content: space-between; align-items: center; }
.faq-question h4 { font-size: 1.1rem; font-weight: 700; color: #0F172A; margin: 0; }
.faq-icon { font-size: 1.4rem; font-weight: bold; color: #0284C7; }
.faq-answer { padding-top: 14px; margin-top: 12px; border-top: 1px solid #F1F5F9; color: #475569; font-size: 0.95rem; line-height: 1.6; }

/* CTA Banner */
.cta-banner { padding: 70px 0; background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%); color: #fff; }
.cta-box h3 { font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.cta-box p { font-size: 1.05rem; opacity: 0.9; margin: 0; }
.cta-buttons { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 16px; }
.btn-cta-phone { background: #fff; color: #0284C7; font-weight: 800; padding: 14px 26px; border-radius: 99px; text-decoration: none; }
.btn-cta-booking { background: #0F172A; color: #fff; font-weight: 800; padding: 14px 26px; border-radius: 99px; text-decoration: none; }

/* Footer */
.clinical-footer { background: #0F172A; color: #CBD5E1; padding: 40px 0; font-size: 0.95rem; }
.clinical-footer strong { color: #fff; }

/* Responsive */
@media (max-width: 992px) {
  .hero-container, .about-grid { grid-template-columns: 1fr; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .clinical-services-grid { grid-template-columns: 1fr; }
  .process-steps-grid { grid-template-columns: repeat(2, 1fr); }
  .education-grid { grid-template-columns: 1fr; }
  .clinical-reels-row { grid-template-columns: repeat(2, 1fr); }
  .testimonials-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: repeat(2, 1fr); }
  .schedule-board-row { flex-direction: column; align-items: flex-start; gap: 14px; }
  .nav-links { display: none; }
  .burger { display: flex; }
}
@media (max-width: 600px) {
  .stats-grid { grid-template-columns: 1fr; }
  .clinical-pillars-row { grid-template-columns: 1fr; }
  .process-steps-grid { grid-template-columns: 1fr; }
  .tips-grid { grid-template-columns: 1fr; }
  .clinical-reels-row { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: 1fr; }
  .cta-buttons { flex-direction: column; width: 100%; }
}
</style>
