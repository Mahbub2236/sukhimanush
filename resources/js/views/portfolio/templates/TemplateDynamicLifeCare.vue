<template>
  <div class="doctor-portfolio-theme theme-dynamic-lifecare">
    <!-- TOP NOTIFICATION STRIP -->
    <div class="lifecare-topbar">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <span class="live-pulse-badge">● চেম্বার সিরিয়াল গ্রহণ চলছে</span>
          <span class="topbar-sep">|</span>
          <span class="bmdc-label">বিএমডিসি রেজিঃ {{ doctor.bmdc_number || 'A-89421' }}</span>
        </div>
        <div>
          <a :href="'tel:' + (contactPhone || doctor.phone)" class="hotline-text">
            📞 হটলাইন: {{ contactPhone || doctor.phone || 'কল করুন' }}
          </a>
        </div>
      </div>
    </div>

    <!-- MAIN NAVBAR -->
    <header class="lifecare-header" :class="{ 'scrolled': isScrolled }">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="brand">
          <router-link :to="'/portfolio/' + (doctor.slug || doctor.id)" class="brand-link">
            <span class="lifecare-spark">✦</span>
            <div>
              <span class="brand-title">{{ doctorDisplayName }}</span>
              <span class="brand-tag">{{ doctorSpecialtyEyebrow || 'সিনিয়র কনসালটেন্ট' }}</span>
            </div>
          </router-link>
        </div>

        <nav class="nav-links">
          <a href="#about-bento">প্রোফাইল</a>
          <a href="#services-bento">সেবাসমূহ</a>
          <a href="#pathway-bento">পদ্ধতি</a>
          <a href="#chambers-bento">চেম্বার</a>
          <a href="#education-bento">ডিগ্রি</a>
          <a href="#reels-bento">ভিডিও বার্তা</a>
          <a href="#tips-bento">টিপস</a>
          <a href="#reviews-bento">রিভিউ</a>
          <a href="#faq-bento">জিজ্ঞাসা</a>
        </nav>

        <div class="header-action">
          <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-vibrant">
            সিরিয়াল বুকিং
          </router-link>
        </div>

        <button class="burger" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="মেনু">
          <span></span><span></span><span></span>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div class="mobile-menu" :class="{ 'open': mobileMenuOpen }">
        <a href="#about-bento" @click="mobileMenuOpen = false">প্রোফাইল</a>
        <a href="#services-bento" @click="mobileMenuOpen = false">সেবাসমূহ</a>
        <a href="#pathway-bento" @click="mobileMenuOpen = false">পদ্ধতি</a>
        <a href="#chambers-bento" @click="mobileMenuOpen = false">চেম্বার</a>
        <a href="#education-bento" @click="mobileMenuOpen = false">ডিগ্রি</a>
        <a href="#reels-bento" @click="mobileMenuOpen = false">ভিডিও বার্তা</a>
        <a href="#tips-bento" @click="mobileMenuOpen = false">টিপস</a>
        <a href="#reviews-bento" @click="mobileMenuOpen = false">রিভিউ</a>
        <a href="#faq-bento" @click="mobileMenuOpen = false">জিজ্ঞাসা</a>
      </div>
    </header>

    <!-- ================= 1. THE SIGNATURE BENTO GRID HERO SUITE ================= -->
    <section class="bento-hero-section">
      <div class="wrap">
        <div class="bento-grid">
          <!-- BENTO BOX 1: Main Doctor Profile (Large Span 2 cols) -->
          <div class="bento-box bento-main-profile">
            <div class="bento-profile-inner">
              <div class="bento-badge-eyebrow">
                <span class="dot-violet"></span> {{ doctorSpecialtyEyebrow || 'সিনিয়র কনসালটেন্ট' }}
              </div>
              <h1 class="bento-name">{{ doctorDisplayName }}</h1>
              
              <div class="quals-pills-row">
                <span v-for="(q, qIdx) in qualificationBadges" :key="qIdx" class="pill-badge">
                  {{ q }}
                </span>
              </div>

              <p class="bento-hosp" v-if="doctorHospitalText">
                🏥 <strong>{{ doctorHospitalText }}</strong>
              </p>

              <p class="bento-bio">
                {{ aboutBio || defaultDynamicBio }}
              </p>

              <div class="bento-actions">
                <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-vibrant">
                  অ্যাপয়েন্টমেন্ট বুকিং ➜
                </router-link>
                <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-outline-vibrant">
                  📞 কল করুন
                </a>
              </div>
            </div>

            <div class="bento-avatar-side">
              <div class="bento-portrait-frame">
                <img :src="doctorHeroPhoto" :alt="doctor.name" class="bento-img" />
              </div>
            </div>
          </div>

          <!-- BENTO BOX 2: Stats & Credibility -->
          <div class="bento-box bento-stats-box">
            <div class="stats-icon-banner">🏆</div>
            <div class="stat-highlight">
              <div class="num">{{ doctor.experience_years ? formatBnNumber(doctor.experience_years) + '+' : '১৫+' }}</div>
              <div class="lbl">বছরের চিকিৎসা অভিজ্ঞতা</div>
            </div>
            <div class="stat-mini-row">
              <div class="mini-stat">
                <strong>১২,৫০০+</strong>
                <span>সুস্থ রোগী</span>
              </div>
              <div class="mini-stat">
                <strong>৯৯.২%</strong>
                <span>সন্তুষ্টি রেটিং</span>
              </div>
            </div>
            <div class="bmdc-badge-pill mt-3">
              🛡️ বিএমডিসি রেজিঃ {{ doctor.bmdc_number || 'যাচাইকৃত' }}
            </div>
          </div>

          <!-- BENTO BOX 3: Next Available Chamber & Status -->
          <div class="bento-box bento-chamber-widget" id="chambers-bento">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="widget-tag">🏥 চেম্বার ও সিরিয়াল</span>
              <span class="open-status-pill">● সিরিয়াল গ্রহণ চলছে</span>
            </div>
            
            <div v-if="displayChambers && displayChambers[0]" class="chamber-quick-detail">
              <h4 class="primary-ch-name">{{ displayChambers[0].name }}</h4>
              <p class="primary-ch-addr">📍 {{ displayChambers[0].address }}</p>
              <div class="schedule-pill">
                🕒 {{ displayChambers[0].schedule || 'বিকাল ৫:০০ - রাত ৮:৩০' }}
              </div>
            </div>
            
            <div class="all-chambers-count mt-2" v-if="displayChambers.length > 1">
              + আরও {{ formatBnNumber(displayChambers.length - 1) }}টি চেম্বার সক্রিয় রয়েছে
            </div>

            <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-outline-vibrant btn-block mt-3">
              চেম্বারের সিরিয়াল বুক করুন ➜
            </router-link>
          </div>

          <!-- BENTO BOX 4: WhatsApp Direct Connect Box -->
          <div class="bento-box bento-whatsapp">
            <div class="wa-icon">💬</div>
            <h4>হোয়াটসঅ্যাপ সরাসরি মেসেজ</h4>
            <p>জরুরী প্রয়োজনে বা প্রেসক্রিপশন সংক্রান্ত তথ্যের জন্য সরাসরি কথা বলুন।</p>
            <a :href="'https://wa.me/' + (contactPhone || doctor.phone || '').replace(/[^0-9]/g, '')" target="_blank" class="btn btn-wa-chat">
              হোয়াটসঅ্যাপে চ্যাট করুন
            </a>
          </div>

          <!-- BENTO BOX 5: Featured Video Reel -->
          <div class="bento-box bento-video-highlight" id="reels-bento" v-if="displayShortVideos && displayShortVideos.length > 0">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="widget-tag">🎬 স্বাস্থ্য রিলস বার্তা</span>
              <span class="reel-count-pill">{{ formatBnNumber(displayShortVideos.length) }}টি ভিডিও</span>
            </div>
            <div class="highlight-reel-preview" @click="$emit('open-video', displayShortVideos[0])">
              <img :src="displayShortVideos[0].thumbnail_url || '/images/team/1.jpg'" class="mini-reel-img" />
              <div class="reel-hover-play">▶</div>
              <div class="reel-caption">{{ displayShortVideos[0].title }}</div>
            </div>
          </div>

          <!-- BENTO BOX 6: Daily Health Tip Pill -->
          <div class="bento-box bento-health-tip" v-if="displayTips && displayTips.length > 0">
            <div class="tip-spark">💡 আজকের পরামর্শ</div>
            <h4>{{ displayTips[0].title }}</h4>
            <p>{{ displayTips[0].shortDesc }}</p>
            <button class="btn-read-more-tip" @click="$emit('open-tip', displayTips[0])">
              পুরো পরামর্শ পড়ুন ➜
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 2. BENTO ABOUT & PHILOSOPHY STRIP ================= -->
    <section class="about-bento-section" id="about-bento">
      <div class="wrap">
        <div class="about-bento-card">
          <div class="about-left-avatar">
            <img :src="doctorAboutPhoto || doctorHeroPhoto" :alt="doctorDisplayName" />
          </div>
          <div class="about-right-info">
            <span class="badge-pill mb-2">চিকিৎসক পরিচিতি</span>
            <h2>{{ aboutHeading }}</h2>
            <p class="lead-text">{{ aboutBio || defaultDynamicBio }}</p>
            <p class="body-text">
              চিকিৎসাসেবা কেবল একটি পেশা নয়, এটি পরম দায়বদ্ধতা। আধুনিক চিকিৎসার ক্ষেত্রে আন্তর্জাতিক স্ট্যান্ডার্ড মেনে সঠিক রোগ নির্ণয় এবং রোগীর শারীরিক ও মানসিক প্রশান্তি নিশ্চিত করাই আমার মূল লক্ষ্য।
            </p>
            <div class="stats-row-strip">
              <div v-for="(st, sIdx) in displayStats" :key="sIdx" class="stat-col">
                <strong>{{ st.num }}</strong>
                <span>{{ st.label }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 3. BENTO SERVICES & SPECIALTIES ================= -->
    <section class="services-bento-section" id="services-bento">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">🩺 সেবা ক্ষেত্রসমূহ</span>
          <h2>বিশেষায়িত চিকিৎসা ও সেবা</h2>
          <p>সঠিক সময়ে সঠিক পরামর্শ ও আন্তর্জাতিক স্ট্যান্ডার্ড মেনে চিকিৎসা সেবা প্রদান</p>
        </div>

        <div class="services-card-grid">
          <div v-for="(serv, idx) in displayServices" :key="idx" class="bento-service-card">
            <div class="service-color-dot"></div>
            <h3>{{ serv.title }}</h3>
            <p>{{ serv.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 4. 4-STEP BENTO PATIENT CARE PATHWAY ================= -->
    <section class="pathway-bento-section" id="pathway-bento">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">রোগী সেবা পদ্ধতি</span>
          <h2>সহজ ৪ ধাপে চিকিৎসকের সেবা নিন</h2>
          <p>ঝামেলাহীন ও সুশৃঙ্খলভাবে পরামর্শ গ্রহণের প্রক্রিয়া</p>
        </div>

        <div class="bento-process-grid">
          <div v-for="(pr, pIdx) in displayProcess" :key="pIdx" class="bento-process-card">
            <div class="process-step-pill">{{ pr.step }}</div>
            <div class="process-ico">{{ pr.icon }}</div>
            <h4>{{ pr.title }}</h4>
            <p>{{ pr.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 5. ALL CHAMBERS & VISITING SCHEDULE ================= -->
    <section class="chambers-full-section" id="chambers-bento">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">চেম্বার ও সময়সূচি</span>
          <h2>রোগী দেখার নিয়মিত হাসপাতাল ও চেম্বার</h2>
          <p>সরাসরি পরামর্শের জন্য নিচে উল্লেখিত চেম্বারে যোগাযোগ করুন</p>
        </div>

        <div class="chambers-bento-grid">
          <div v-for="(ch, cIdx) in displayChambers" :key="cIdx" class="chamber-card-bento">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge-branch">শাখা #{{ formatBnNumber(cIdx + 1) }}</span>
              <span class="badge-status">● সক্রিয় চেম্বার</span>
            </div>
            <h3>{{ ch.name }}</h3>
            <p class="ch-address-line">📍 {{ ch.address }}</p>
            <div class="ch-schedule-box" v-if="ch.schedule">
              <span>🕒 <strong>সময়সূচি:</strong> {{ ch.schedule }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
              <a :href="'tel:' + (ch.phone || contactPhone)" class="btn btn-outline-vibrant btn-sm">
                📞 {{ ch.phone || contactPhone }}
              </a>
              <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-vibrant btn-sm">
                সিরিয়াল বুক করুন
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 6. ACADEMIC CREDENTIALS & DEGREES ================= -->
    <section class="education-bento-section" id="education-bento">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">শিক্ষাজীবন ও ডিগ্রি</span>
          <h2>উচ্চতর চিকিৎসা শিক্ষা ও ফেলোশিপ</h2>
          <p>স্বনামধন্য মেডিকেল কলেজ ও বিশ্ববিদ্যালয় থেকে অর্জিত প্রাতিষ্ঠানিক ডিগ্রি</p>
        </div>

        <div class="education-bento-grid">
          <div v-for="(edu, eIdx) in displayEducation" :key="eIdx" class="edu-bento-card">
            <div class="edu-year-tag">{{ edu.year }}</div>
            <h3>{{ edu.institution }}</h3>
            <p>{{ edu.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 7. SHORT VIDEO REELS SHOWCASE ================= -->
    <section class="reels-bento-section" id="reels-bento" v-if="displayShortVideos && displayShortVideos.length > 0">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">ভিডিও বার্তা</span>
          <h2>চিকিৎসকের জরুরি স্বাস্থ্য পরামর্শ ও রিলস</h2>
          <p>ক্লিক করে সরাসরি চিকিৎসকের প্রয়োজনীয় ভিডিও শুনুন</p>
        </div>

        <div class="reels-grid-bento">
          <div v-for="(v, vIdx) in displayShortVideos" :key="vIdx" class="reel-card-bento" @click="$emit('open-video', v)">
            <div class="reel-thumb-box">
              <img :src="v.thumbnail_url || '/images/team/1.jpg'" class="reel-img" />
              <div class="play-overlay">
                <div class="play-button-vibrant">▶</div>
              </div>
              <span class="duration-badge" v-if="v.duration">{{ v.duration }}</span>
            </div>
            <div class="reel-content">
              <span class="reel-tag" v-if="v.tag">{{ v.tag }}</span>
              <h4>{{ v.title }}</h4>
              <span class="watch-link">ভিডিও দেখুন ➜</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 8. HEALTH AWARENESS TIPS ================= -->
    <section class="tips-bento-section" id="tips-bento">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">স্বাস্থ্য সচেতনতা</span>
          <h2>দৈনন্দিন সুস্থতার জরুরি স্বাস্থ্য বার্তা</h2>
          <p>সুস্থ জীবনযাপনে অভিজ্ঞ চিকিৎসকের প্রমাণিত পরামর্শ ও নির্দেশিকা</p>
        </div>

        <div class="tips-bento-grid">
          <div v-for="(tip, tIdx) in displayTips" :key="tIdx" class="tip-card-bento" @click="$emit('open-tip', tip)">
            <span class="tip-cat-badge">{{ tip.tag }}</span>
            <h4>{{ tip.title }}</h4>
            <p>{{ tip.shortDesc }}</p>
            <div class="tip-read-action">পুরো পরামর্শ পড়ুন ➜</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 9. PATIENT REVIEWS & TESTIMONIALS ================= -->
    <section class="reviews-bento-section" id="reviews-bento">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">রোগীদের অভিজ্ঞতা</span>
          <h2>রোগীদের আস্থা ও সুস্থতার বাস্তব গল্প</h2>
          <p>সুস্থ হওয়া রোগী ও তাদের পরিবারের আন্তরিক রিভিউ</p>
        </div>

        <div class="reviews-bento-grid">
          <div v-for="(test, tIdx) in displayTestimonials" :key="tIdx" class="review-card-bento">
            <div class="review-stars">★★★★★</div>
            <p class="review-quote">"{{ test.comment }}"</p>
            <div class="review-author-row">
              <div class="author-circle">{{ test.author_name.charAt(0) }}</div>
              <div>
                <strong>{{ test.author_name }}</strong>
                <span v-if="test.author_location"> • {{ test.author_location }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 10. CLINIC GALLERY ================= -->
    <section class="gallery-bento-section" id="gallery-bento" v-if="displayGallery && displayGallery.length > 0">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">পরিবেশ ও অবকাঠামো</span>
          <h2>চেম্বার ও ক্লিনিক্যাল পরিবেশ</h2>
        </div>

        <div class="gallery-bento-grid">
          <div v-for="(img, gIdx) in displayGallery" :key="gIdx" class="gallery-bento-box">
            <img :src="img" :alt="'Clinic ' + (gIdx + 1)" />
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 11. FAQ BENTO ACCORDION ================= -->
    <section class="faq-bento-section" id="faq-bento">
      <div class="wrap">
        <div class="section-badge-center text-center">
          <span class="badge-pill">সাধারণ জিজ্ঞাসা</span>
          <h2>সচরাচর জিজ্ঞাসিত প্রশ্ন ও চিকিৎসকের উত্তর</h2>
          <p>সিরিয়াল বুকিং ও চেম্বার সংক্রান্ত প্রয়োজনীয় তথ্য</p>
        </div>

        <div class="faq-bento-wrap">
          <div 
            v-for="(faq, fIdx) in displayFaqs" 
            :key="fIdx" 
            class="faq-bento-item" 
            :class="{ 'open': activeFaqIndex === fIdx }"
            @click="activeFaqIndex = activeFaqIndex === fIdx ? null : fIdx"
          >
            <div class="faq-q-line">
              <h4>{{ faq.question }}</h4>
              <span class="faq-toggle">{{ activeFaqIndex === fIdx ? '−' : '+' }}</span>
            </div>
            <div class="faq-a-line" v-show="activeFaqIndex === fIdx">
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 12. INSTANT BOOKING BENTO BANNER ================= -->
    <section class="banner-bento-section">
      <div class="wrap">
        <div class="banner-bento-card d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h3>আপনার পরিবারের বিশ্বস্ত স্বাস্থ্য পরামর্শ নিন</h3>
            <p>চেম্বারে সরাসরি আসুন অথবা অনলাইন ভিডিও কলে পরামর্শ নিন।</p>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-outline-vibrant btn-lg" style="background:#fff;">
              📞 {{ contactPhone || doctor.phone }}
            </a>
            <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-vibrant btn-lg">
              অনলাইন অ্যাপয়েন্টমেন্ট বুকিং ➜
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="lifecare-footer">
      <div class="wrap text-center">
        <h3>{{ doctorDisplayName }}</h3>
        <p>{{ doctorSpecialtyEyebrow }} — আপনার ও আপনার পরিবারের বিশ্বস্ত চিকিৎসা সঙ্গী।</p>
        <div class="copy-line">© {{ new Date().getFullYear() }} সর্বস্বত্ব সংরক্ষিত।</div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'TemplateDynamicLifeCare',
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
.theme-dynamic-lifecare {
  background-color: #FAF5FF;
  color: #1E1B4B;
  font-family: 'Hind Siliguri', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  line-height: 1.6;
}

.wrap { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* Topbar */
.lifecare-topbar { background: #F3E8FF; border-bottom: 1px solid #E9D5FF; padding: 8px 0; font-size: 0.88rem; color: #6B21A8; }
.live-pulse-badge { color: #15803D; font-weight: bold; }
.topbar-sep { margin: 0 10px; color: #D8B4FE; }
.bmdc-label { color: #7C3AED; font-weight: 600; }
.hotline-text { color: #7C3AED; font-weight: bold; text-decoration: none; }

/* Header */
.lifecare-header { position: sticky; top: 0; z-index: 100; background: rgba(250,245,255,0.92); backdrop-filter: blur(14px); border-bottom: 1px solid #E9D5FF; padding: 14px 0; transition: all 0.3s; }
.lifecare-header.scrolled { box-shadow: 0 4px 20px rgba(124,58,237,0.08); }
.brand-link { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
.lifecare-spark { font-size: 1.4rem; color: #7C3AED; }
.brand-title { font-size: 1.15rem; font-weight: 800; color: #1E1B4B; display: block; line-height: 1.2; }
.brand-tag { font-size: 0.78rem; color: #7C3AED; font-weight: 600; display: block; }
.nav-links { display: flex; gap: 20px; }
.nav-links a { color: #4C1D95; text-decoration: none; font-size: 0.92rem; font-weight: 600; transition: color 0.2s; }
.nav-links a:hover { color: #7C3AED; }
.btn-vibrant { background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%); color: #fff; font-weight: bold; padding: 10px 22px; border-radius: 99px; text-decoration: none; border: none; box-shadow: 0 4px 14px rgba(124,58,237,0.3); display: inline-flex; align-items: center; }
.btn-vibrant:hover { background: #6D28D9; color: #fff; }
.btn-outline-vibrant { background: transparent; border: 1.5px solid #7C3AED; color: #7C3AED; font-weight: bold; padding: 10px 22px; border-radius: 99px; text-decoration: none; display: inline-flex; align-items: center; }
.btn-outline-vibrant:hover { background: rgba(124,58,237,0.08); }
.burger { display: none; background: none; border: none; flex-direction: column; gap: 5px; cursor: pointer; }
.burger span { width: 24px; height: 2px; background: #1E1B4B; }
.mobile-menu { display: none; flex-direction: column; padding: 16px; background: #FAF5FF; border-bottom: 1px solid #E9D5FF; gap: 10px; }
.mobile-menu.open { display: flex; }

/* Bento Hero */
.bento-hero-section { padding: 40px 0 60px; }
.bento-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.bento-box { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 28px; padding: 28px; box-shadow: 0 10px 30px rgba(124,58,237,0.04); transition: transform 0.2s, box-shadow 0.2s; }
.bento-box:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(124,58,237,0.08); }

/* Bento 1: Main */
.bento-main-profile { grid-column: span 2; display: flex; justify-content: space-between; gap: 30px; align-items: center; background: linear-gradient(135deg, #FFFFFF 0%, #F5F3FF 100%); }
.bento-badge-eyebrow { display: inline-flex; align-items: center; gap: 6px; background: #EDE9FE; color: #6D28D9; padding: 4px 12px; border-radius: 99px; font-size: 0.82rem; font-weight: 700; margin-bottom: 12px; }
.dot-violet { width: 6px; height: 6px; border-radius: 50%; background: #7C3AED; }
.bento-name { font-size: 2.5rem; font-weight: 900; color: #1E1B4B; line-height: 1.2; margin-bottom: 12px; }
.quals-pills-row { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
.pill-badge { background: #F3E8FF; color: #6D28D9; font-size: 0.8rem; font-weight: 600; padding: 2px 10px; border-radius: 99px; }
.bento-hosp { font-size: 0.95rem; color: #4C1D95; margin-bottom: 10px; }
.bento-bio { font-size: 0.95rem; color: #5B21B6; margin-bottom: 22px; line-height: 1.6; }
.bento-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.bento-avatar-side { flex: none; width: 220px; height: 280px; }
.bento-portrait-frame { width: 100%; height: 100%; border-radius: 20px; overflow: hidden; border: 3px solid #DDD6FE; box-shadow: 0 10px 25px rgba(124,58,237,0.15); }
.bento-img { width: 100%; height: 100%; object-fit: cover; }

/* Bento 2: Stats */
.bento-stats-box { background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%); color: #fff; text-align: center; display: flex; flex-direction: column; justify-content: center; }
.stats-icon-banner { font-size: 32px; margin-bottom: 8px; }
.stat-highlight .num { font-size: 3rem; font-weight: 900; line-height: 1; }
.stat-highlight .lbl { font-size: 0.92rem; opacity: 0.9; margin-bottom: 16px; }
.stat-mini-row { display: flex; justify-content: space-around; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 14px; }
.mini-stat strong { display: block; font-size: 1.25rem; font-weight: 800; }
.mini-stat span { font-size: 0.78rem; opacity: 0.85; }
.bmdc-badge-pill { background: rgba(255,255,255,0.15); font-size: 0.78rem; font-weight: bold; padding: 4px 10px; border-radius: 99px; display: inline-block; margin: 0 auto; }

/* Bento 3: Chamber Widget */
.widget-tag { font-size: 0.78rem; font-weight: bold; color: #7C3AED; text-transform: uppercase; }
.open-status-pill { font-size: 0.78rem; color: #15803D; font-weight: bold; }
.primary-ch-name { font-size: 1.2rem; font-weight: 800; color: #1E1B4B; margin: 8px 0 4px; }
.primary-ch-addr { font-size: 0.88rem; color: #6D28D9; margin-bottom: 8px; }
.schedule-pill { background: #F3E8FF; color: #5B21B6; font-size: 0.85rem; font-weight: bold; padding: 6px 12px; border-radius: 10px; }
.all-chambers-count { font-size: 0.82rem; color: #7C3AED; font-weight: 600; }

/* Bento 4: WhatsApp */
.bento-whatsapp { background: #ECFDF5; border-color: #A7F3D0; }
.wa-icon { font-size: 28px; margin-bottom: 8px; }
.bento-whatsapp h4 { font-size: 1.2rem; font-weight: 800; color: #065F46; margin-bottom: 6px; }
.bento-whatsapp p { font-size: 0.88rem; color: #047857; margin-bottom: 16px; }
.btn-wa-chat { background: #10B981; color: #fff; font-weight: bold; border-radius: 99px; padding: 8px 18px; text-decoration: none; display: inline-block; font-size: 0.9rem; }

/* Bento 5: Video */
.bento-video-highlight { cursor: pointer; }
.reel-count-pill { font-size: 0.75rem; background: #F3E8FF; color: #6D28D9; padding: 2px 8px; border-radius: 99px; font-weight: bold; }
.highlight-reel-preview { position: relative; border-radius: 16px; overflow: hidden; height: 160px; margin-top: 10px; }
.mini-reel-img { width: 100%; height: 100%; object-fit: cover; }
.reel-hover-play { position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #fff; }
.reel-caption { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: #fff; font-size: 0.78rem; padding: 6px 10px; font-weight: 600; }

/* Bento 6: Health Tip */
.bento-health-tip { background: #FFFBEB; border-color: #FDE68A; }
.tip-spark { font-size: 0.78rem; font-weight: bold; color: #B45309; text-transform: uppercase; margin-bottom: 6px; }
.bento-health-tip h4 { font-size: 1.15rem; font-weight: 800; color: #92400E; margin-bottom: 8px; line-height: 1.4; }
.bento-health-tip p { font-size: 0.88rem; color: #78350F; margin-bottom: 14px; }
.btn-read-more-tip { background: none; border: none; color: #B45309; font-weight: bold; padding: 0; cursor: pointer; font-size: 0.88rem; }

/* About Bento Strip */
.about-bento-section { padding: 50px 0; }
.about-bento-card { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 28px; padding: 40px; display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 40px; align-items: center; box-shadow: 0 10px 30px rgba(124,58,237,0.04); }
.about-left-avatar { border-radius: 24px; overflow: hidden; height: 380px; }
.about-left-avatar img { width: 100%; height: 100%; object-fit: cover; }
.about-right-info h2 { font-size: 2.2rem; font-weight: 800; color: #1E1B4B; margin-bottom: 12px; }
.lead-text { font-size: 1.1rem; font-weight: 600; color: #4C1D95; margin-bottom: 10px; line-height: 1.7; }
.body-text { font-size: 0.95rem; color: #6D28D9; margin-bottom: 24px; line-height: 1.7; }
.stats-row-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; border-top: 1px solid #F3E8FF; padding-top: 20px; }
.stat-col strong { display: block; font-size: 1.3rem; font-weight: 800; color: #7C3AED; }
.stat-col span { font-size: 0.78rem; color: #6D28D9; }

/* Badge Center */
.section-badge-center { max-width: 720px; margin: 0 auto 40px; }
.badge-pill { background: #F3E8FF; color: #7C3AED; font-size: 0.85rem; font-weight: 700; padding: 4px 14px; border-radius: 99px; display: inline-block; margin-bottom: 8px; }
.section-badge-center h2 { font-size: 2.2rem; font-weight: 800; color: #1E1B4B; margin-bottom: 8px; }
.section-badge-center p { color: #6D28D9; font-size: 0.98rem; margin: 0; }

/* Services Bento */
.services-bento-section { padding: 60px 0; }
.services-card-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.bento-service-card { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 22px; padding: 26px; transition: transform 0.2s; position: relative; }
.bento-service-card:hover { transform: translateY(-4px); border-color: #7C3AED; }
.service-color-dot { width: 10px; height: 10px; border-radius: 50%; background: #7C3AED; margin-bottom: 12px; }
.bento-service-card h3 { font-size: 1.2rem; font-weight: 800; color: #1E1B4B; margin-bottom: 6px; }
.bento-service-card p { font-size: 0.9rem; color: #6D28D9; margin: 0; }

/* Pathway Bento */
.pathway-bento-section { padding: 60px 0; background: #F5F3FF; }
.bento-process-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.bento-process-card { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 22px; padding: 26px 20px; text-align: center; position: relative; }
.process-step-pill { position: absolute; top: 12px; right: 14px; background: #EDE9FE; color: #7C3AED; font-weight: 800; font-size: 0.78rem; padding: 2px 8px; border-radius: 99px; }
.process-ico { font-size: 30px; margin-bottom: 10px; }
.bento-process-card h4 { font-size: 1.15rem; font-weight: 800; color: #1E1B4B; margin-bottom: 6px; }
.bento-process-card p { font-size: 0.88rem; color: #6D28D9; margin: 0; }

/* Chambers */
.chambers-full-section { padding: 60px 0; }
.chambers-bento-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; max-width: 980px; margin: 0 auto; }
.chamber-card-bento { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 24px; padding: 28px; }
.badge-branch { font-size: 0.75rem; font-weight: bold; color: #7C3AED; }
.badge-status { font-size: 0.75rem; color: #15803D; font-weight: bold; }
.chamber-card-bento h3 { font-size: 1.3rem; font-weight: 800; color: #1E1B4B; margin: 6px 0; }
.ch-address-line { font-size: 0.9rem; color: #6D28D9; margin-bottom: 10px; }
.ch-schedule-box { background: #F3E8FF; padding: 10px 14px; border-radius: 12px; font-size: 0.9rem; color: #5B21B6; }

/* Education */
.education-bento-section { padding: 60px 0; background: #F5F3FF; }
.education-bento-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; max-width: 920px; margin: 0 auto; }
.edu-bento-card { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 20px; padding: 24px; }
.edu-year-tag { display: inline-block; background: #EDE9FE; color: #6D28D9; font-size: 0.78rem; font-weight: bold; padding: 2px 10px; border-radius: 99px; margin-bottom: 8px; }
.edu-bento-card h3 { font-size: 1.15rem; font-weight: 800; color: #1E1B4B; margin-bottom: 4px; }
.edu-bento-card p { font-size: 0.9rem; color: #6D28D9; margin: 0; }

/* Reels */
.reels-bento-section { padding: 60px 0; }
.reels-grid-bento { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.reel-card-bento { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 20px; overflow: hidden; cursor: pointer; transition: transform 0.2s; }
.reel-card-bento:hover { transform: translateY(-4px); border-color: #7C3AED; }
.reel-thumb-box { position: relative; width: 100%; height: 260px; background: #000; }
.reel-thumb-box img { width: 100%; height: 100%; object-fit: cover; }
.play-button-vibrant { width: 48px; height: 48px; border-radius: 50%; background: #7C3AED; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.reel-content { padding: 18px; }
.reel-tag { background: #EDE9FE; color: #6D28D9; font-size: 0.75rem; font-weight: bold; padding: 2px 8px; border-radius: 99px; display: inline-block; margin-bottom: 6px; }
.reel-content h4 { font-size: 1.05rem; font-weight: 700; color: #1E1B4B; margin-bottom: 8px; line-height: 1.4; }
.watch-link { color: #7C3AED; font-weight: bold; font-size: 0.88rem; }

/* Tips */
.tips-bento-section { padding: 60px 0; background: #F5F3FF; }
.tips-bento-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.tip-card-bento { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 20px; padding: 26px; cursor: pointer; transition: transform 0.2s; }
.tip-card-bento:hover { transform: translateY(-4px); border-color: #7C3AED; }
.tip-cat-badge { color: #7C3AED; font-weight: bold; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 6px; display: block; }
.tip-card-bento h4 { font-size: 1.2rem; font-weight: 800; color: #1E1B4B; margin-bottom: 8px; line-height: 1.4; }
.tip-card-bento p { font-size: 0.9rem; color: #6D28D9; margin-bottom: 12px; }
.tip-read-action { color: #7C3AED; font-weight: bold; font-size: 0.88rem; }

/* Reviews */
.reviews-bento-section { padding: 60px 0; }
.reviews-bento-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.review-card-bento { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 20px; padding: 26px; }
.review-stars { color: #F59E0B; font-size: 1.1rem; margin-bottom: 10px; }
.review-quote { color: #4C1D95; font-size: 0.92rem; font-style: italic; line-height: 1.6; margin-bottom: 16px; }
.review-author-row { display: flex; align-items: center; gap: 10px; }
.author-circle { width: 38px; height: 38px; border-radius: 50%; background: #7C3AED; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; }

/* Gallery */
.gallery-bento-section { padding: 60px 0; background: #F5F3FF; }
.gallery-bento-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.gallery-bento-box { border-radius: 18px; overflow: hidden; height: 200px; border: 1px solid #E9D5FF; }
.gallery-bento-box img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.gallery-bento-box:hover img { transform: scale(1.05); }

/* FAQ */
.faq-bento-section { padding: 60px 0; }
.faq-bento-wrap { max-width: 840px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
.faq-bento-item { background: #FFFFFF; border: 1px solid #E9D5FF; border-radius: 16px; padding: 20px 24px; cursor: pointer; }
.faq-bento-item.open { border-color: #7C3AED; }
.faq-q-line { display: flex; justify-content: space-between; align-items: center; }
.faq-q-line h4 { font-size: 1.1rem; font-weight: 700; color: #1E1B4B; margin: 0; }
.faq-toggle { font-size: 1.4rem; font-weight: bold; color: #7C3AED; }
.faq-a-line { padding-top: 12px; margin-top: 12px; border-top: 1px solid #F3E8FF; color: #6D28D9; font-size: 0.95rem; line-height: 1.6; }

/* Banner */
.banner-bento-section { padding: 60px 0; }
.banner-bento-card { background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%); color: #fff; border-radius: 28px; padding: 48px; }
.banner-bento-card h3 { font-size: 2.2rem; font-weight: 800; margin-bottom: 8px; }
.banner-bento-card p { font-size: 1.05rem; opacity: 0.9; margin: 0; }

/* Footer */
.lifecare-footer { background: #1E1B4B; color: #C7D2FE; padding: 40px 0; }
.lifecare-footer h3 { color: #fff; margin-bottom: 4px; }
.copy-line { opacity: 0.7; font-size: 0.85rem; margin-top: 10px; }

/* Responsive */
@media (max-width: 992px) {
  .bento-grid { grid-template-columns: 1fr; }
  .bento-main-profile { grid-column: span 1; flex-direction: column; }
  .about-bento-card { grid-template-columns: 1fr; }
  .services-card-grid { grid-template-columns: 1fr; }
  .bento-process-grid { grid-template-columns: repeat(2, 1fr); }
  .chambers-bento-grid { grid-template-columns: 1fr; }
  .education-bento-grid { grid-template-columns: 1fr; }
  .reels-grid-bento { grid-template-columns: repeat(2, 1fr); }
  .reviews-bento-grid { grid-template-columns: 1fr; }
  .gallery-bento-grid { grid-template-columns: repeat(2, 1fr); }
  .stats-row-strip { grid-template-columns: repeat(2, 1fr); }
  .nav-links { display: none; }
  .burger { display: flex; }
}
@media (max-width: 600px) {
  .bento-process-grid { grid-template-columns: 1fr; }
  .tips-bento-grid { grid-template-columns: 1fr; }
  .reels-grid-bento { grid-template-columns: 1fr; }
  .gallery-bento-grid { grid-template-columns: 1fr; }
  .stats-row-strip { grid-template-columns: 1fr; }
}
</style>
