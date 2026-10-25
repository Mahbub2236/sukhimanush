<template>
  <div class="doctor-portfolio-theme theme-executive-navy">
    <!-- TOP EXECUTIVE VERIFICATION STRIP -->
    <div class="navy-top-strip">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <span class="navy-verified-badge">✓ বিএমডিসি ভেরিফাইড কনসালটেন্ট</span>
          <span class="navy-bmdc-no">রেজিঃ নং {{ doctor.bmdc_number || 'A-89421' }}</span>
        </div>
        <div class="navy-top-contact">
          <a :href="'tel:' + (contactPhone || doctor.phone)">
            📞 প্রাতিষ্ঠানিক হেল্পলাইন: {{ contactPhone || doctor.phone || 'কল করুন' }}
          </a>
        </div>
      </div>
    </div>

    <!-- EXECUTIVE HEADER -->
    <header class="executive-header" :class="{ 'scrolled': isScrolled }">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="brand">
          <router-link :to="'/portfolio/' + (doctor.slug || doctor.id)" class="brand-link">
            <span class="brand-crest">⚓</span>
            <div>
              <span class="brand-title">{{ doctorDisplayName }}</span>
              <span class="brand-role">{{ doctorSpecialtyEyebrow || 'সিনিয়র কনসালটেন্ট ও সার্জন' }}</span>
            </div>
          </router-link>
        </div>

        <nav class="nav-links">
          <a href="#about">প্রোফাইল</a>
          <a href="#disciplines">সেবাসমূহ</a>
          <a href="#pathway">চিকিৎসা ধাপ</a>
          <a href="#hospitals">চেম্বার ও হাসপাতাল</a>
          <a href="#fellowships">ডিগ্রি ও ফেলোশিপ</a>
          <a href="#videos">ভিডিও বার্তা</a>
          <a href="#tips">পরামর্শ</a>
          <a href="#testimonials">রোগীর রিভিউ</a>
          <a href="#faq">জিজ্ঞাসা</a>
        </nav>

        <div class="header-action">
          <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cyan-primary">
            সিরিয়াল নির্ধারণ করুন
          </router-link>
        </div>

        <button class="burger" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="মেনু">
          <span></span><span></span><span></span>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div class="mobile-menu" :class="{ 'open': mobileMenuOpen }">
        <a href="#about" @click="mobileMenuOpen = false">প্রোফাইল</a>
        <a href="#disciplines" @click="mobileMenuOpen = false">সেবাসমূহ</a>
        <a href="#pathway" @click="mobileMenuOpen = false">চিকিৎসা ধাপ</a>
        <a href="#hospitals" @click="mobileMenuOpen = false">চেম্বার ও হাসপাতাল</a>
        <a href="#fellowships" @click="mobileMenuOpen = false">ডিগ্রি ও ফেলোশিপ</a>
        <a href="#videos" @click="mobileMenuOpen = false">ভিডিও বার্তা</a>
        <a href="#tips" @click="mobileMenuOpen = false">পরামর্শ</a>
        <a href="#testimonials" @click="mobileMenuOpen = false">রোগীর রিভিউ</a>
        <a href="#faq" @click="mobileMenuOpen = false">জিজ্ঞাসা</a>
      </div>
    </header>

    <!-- ================= 1. FULL-WIDTH NAVY BANNER HERO ================= -->
    <section class="navy-hero">
      <div class="wrap hero-layout">
        <div class="hero-left-content">
          <div class="authority-badge">
            <span class="shield-icon">🛡️</span> {{ doctorSpecialtyEyebrow || 'সিনিয়র অধ্যাপক ও বিভাগীয় প্রধান' }}
          </div>

          <h1 class="hero-doc-name">
            {{ doctorDisplayName }}
          </h1>

          <div class="executive-badges-row">
            <span v-for="(badge, bIdx) in qualificationBadges" :key="bIdx" class="badge-navy">
              {{ badge }}
            </span>
          </div>

          <p class="hero-hospital" v-if="doctorHospitalText">
            🏥 <strong>{{ doctorHospitalText }}</strong>
          </p>

          <p class="hero-statement">
            {{ aboutBio || defaultDynamicBio }}
          </p>

          <div class="hero-action-buttons">
            <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cyan-primary btn-lg">
              অ্যাপয়েন্টমেন্ট বুকিং ➜
            </router-link>
            <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-navy-outline btn-lg">
              📞 সরাসরি কল: {{ contactPhone || doctor.phone }}
            </a>
          </div>
        </div>

        <div class="hero-right-portrait">
          <div class="executive-portrait-box">
            <img :src="doctorHeroPhoto" :alt="doctor.name" class="executive-img" />
            <div class="authority-floating-card">
              <span class="star-badge">★★★★★</span>
              <strong>১৫,০০০+ রোগী পরামর্শ</strong>
              <div class="sub">নির্ভরযোগ্য ও নিরাপদ চিকিৎসা সেবা</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 2. THREE-COLUMN GIGANTIC IMPACT METRICS ================= -->
    <section class="impact-metrics-section">
      <div class="wrap">
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-val">{{ doctor.experience_years ? formatBnNumber(doctor.experience_years) + '+ বছর' : '২০+ বছর' }}</div>
            <div class="metric-title">চিকিৎসা ও প্রাতিষ্ঠানিক অভিজ্ঞতা</div>
            <div class="metric-desc">দেশের শীর্ষ সরকারি ও বেসরকারি মেডিকেল কলেজ ও হাসপাতালে সিনিয়র কনসালটেন্ট হিসেবে দীর্ঘদিনের সফল সেবা।</div>
          </div>
          <div class="metric-card">
            <div class="metric-val">১২,৫০০+</div>
            <div class="metric-title">সফল চিকিৎসা ও পরামর্শ</div>
            <div class="metric-desc">জটিল ও দীর্ঘমেয়াদী রোগের সঠিক রোগ নির্ণয় এবং আন্তর্জাতিক প্রটোকল অনুসারে কার্যকর ব্যবস্থাপত্র প্রদান।</div>
          </div>
          <div class="metric-card">
            <div class="metric-val">১০০%</div>
            <div class="metric-title">এভিডেন্স-বেসড মেডিসিন প্র্যাকটিস</div>
            <div class="metric-desc">আন্তর্জাতিক বিশ্বমানের স্বাস্থ্য প্রটোকল ও বিএমডিসি স্বীকৃত নিয়মে শতভাগ রোগী কল্যাণ নিশ্চিতকরণ।</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 3. EXECUTIVE CLINICAL PROFILE & LEADERSHIP ================= -->
    <section class="profile-section" id="about">
      <div class="wrap">
        <div class="profile-grid">
          <div class="profile-photo-wrap">
            <img :src="doctorAboutPhoto || doctorHeroPhoto" :alt="doctorDisplayName" class="profile-img" />
            <div class="creds-overlay">
              <strong>{{ doctorDisplayName }}</strong>
              <div class="small text-cyan">{{ doctorSpecialtyEyebrow }}</div>
            </div>
          </div>
          <div class="profile-content">
            <span class="navy-tag">চিকিৎসক পরিচিতি ও প্রোফাইল</span>
            <h2>{{ aboutHeading }}</h2>
            <p class="profile-lead">{{ aboutBio || defaultDynamicBio }}</p>
            <p class="profile-text">
              চিকিৎসাবিজ্ঞানে প্রতিনিয়ত নতুন প্রযুক্তি ও ডায়াগনস্টিক পদ্ধতির বিকাশ ঘটছে। রোগীদের অপ্রয়োজনীয় পরীক্ষা-নিরীক্ষার আর্থিক ও মানসিক চাপমুক্ত রেখে কেবল প্রয়োজনীয় ও বিজ্ঞানসম্মত পরামর্শ প্রদানের মাধ্যমে সঠিক সময়ে সঠিক চিকিৎসা পৌঁছানোই আমার প্রধান লক্ষ্য।
            </p>

            <div class="executive-stats-pills">
              <div v-for="(st, sIdx) in displayStats" :key="sIdx" class="stat-pill-box">
                <div class="st-num">{{ st.num }}</div>
                <div class="st-lbl">{{ st.label }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 4. SPECIALIZED CLINICAL DISCIPLINES ================= -->
    <section class="disciplines-section" id="disciplines">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">চিকিৎসা বিভাগসমূহ</span>
          <h2>বিশেষায়িত চিকিৎসা ডিসিপ্লিন ও ক্লিনিক্যাল সেবা</h2>
          <p>সর্বাধুনিক ডায়াগনোসিস ও বিশ্বমানের গাইডলাইন অনুসারে প্রদত্ত সেবাসমূহ</p>
        </div>

        <div class="disciplines-grid">
          <div v-for="(serv, sIdx) in displayServices" :key="sIdx" class="discipline-card">
            <div class="discipline-icon">🩺</div>
            <h3>{{ serv.title }}</h3>
            <p>{{ serv.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 5. CLINICAL PATHWAY (PROCESS) ================= -->
    <section class="pathway-section" id="pathway">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">রোগী সেবা পদ্ধতি</span>
          <h2>পর্যায়ক্রমিক ৪ ধাপে চিকিৎসা গ্রহণ প্রক্রিয়া</h2>
          <p>সুশৃঙ্খল ও পেশাদার পরিবেশে সেবা গ্রহণের আন্তর্জাতিক ধাপ</p>
        </div>

        <div class="pathway-grid">
          <div v-for="(pr, pIdx) in displayProcess" :key="pIdx" class="pathway-card">
            <div class="pathway-step-badge">{{ pr.step }}</div>
            <div class="pathway-icon">{{ pr.icon }}</div>
            <h4>{{ pr.title }}</h4>
            <p>{{ pr.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 6. HOSPITAL APPOINTMENTS & CHAMBERS ================= -->
    <section class="hospitals-section" id="hospitals">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">চেম্বার ও হাসপাতাল</span>
          <h2>রোগী দেখার নিয়মিত হাসপাতাল ও চেম্বার</h2>
          <p>সরাসরি পরামর্শের জন্য নিচে উল্লেখিত চেম্বার নম্বরে কল করে সিরিয়াল নিশ্চিত করুন।</p>
        </div>

        <div class="hospitals-grid">
          <div v-for="(ch, idx) in displayChambers" :key="idx" class="navy-chamber-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="navy-hosp-pill">হাসপাতাল শাখা #{{ formatBnNumber(idx + 1) }}</span>
              <span class="navy-status-live">● চেম্বার সক্রিয়</span>
            </div>

            <h3>{{ ch.name }}</h3>
            <p class="navy-ch-address">📍 {{ ch.address }}</p>

            <div class="navy-timing-box" v-if="ch.schedule">
              <span class="clock-ico">🕒</span>
              <span><strong>সময়সূচি:</strong> {{ ch.schedule }}</span>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-4" style="gap: 10px;">
              <a :href="'tel:' + (ch.phone || contactPhone)" class="btn btn-navy-call">
                📞 {{ ch.phone || contactPhone }}
              </a>
              <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cyan-primary">
                সিরিয়াল বুক করুন
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 7. ACADEMIC FELLOWSHIPS & EDUCATION ================= -->
    <section class="fellowships-section" id="fellowships">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">শিক্ষাজীবন ও ফেলোশিপ</span>
          <h2>উচ্চতর ডিগ্রি ও আন্তর্জাতিক ফেলোশিপ</h2>
          <p>চিকিৎসাবিজ্ঞানে উচ্চতর ডিগ্রি অর্জন ও প্রখ্যাত মেডিকেল ইনস্টিটিউট থেকে গবেষণার রূপরেখা।</p>
        </div>

        <div class="credentials-grid">
          <div v-for="(edu, eIdx) in displayEducation" :key="eIdx" class="credential-item">
            <div class="cred-left">
              <span class="cred-year">{{ edu.year || 'ডিগ্রি' }}</span>
            </div>
            <div class="cred-right">
              <h3>{{ edu.institution }}</h3>
              <p>{{ edu.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 8. VIDEO LECTURES & REELS ================= -->
    <section class="videos-section" id="videos" v-if="displayShortVideos && displayShortVideos.length > 0">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">ভিডিও বার্তা</span>
          <h2>চিকিৎসকের জরুরি স্বাস্থ্য পরামর্শ ও সচেতনতা</h2>
          <p>ক্লিক করে সরাসরি ভিডিওটি দেখুন এবং গুরুত্বপূর্ণ রোগ সতর্কতাগুলো জানুন</p>
        </div>

        <div class="videos-grid">
          <div v-for="(v, vIdx) in displayShortVideos" :key="vIdx" class="video-item" @click="$emit('open-video', v)">
            <div class="video-thumb">
              <img :src="v.thumbnail_url || '/images/team/1.jpg'" class="v-img" />
              <div class="v-overlay">
                <div class="v-play-btn">▶</div>
              </div>
              <span class="v-duration" v-if="v.duration">{{ v.duration }}</span>
            </div>
            <div class="v-details">
              <span class="v-tag" v-if="v.tag">{{ v.tag }}</span>
              <h4>{{ v.title }}</h4>
              <span class="v-watch">ভিডিও প্লে করুন ➜</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 9. EVIDENCE-BASED HEALTH TIPS ================= -->
    <section class="tips-section" id="tips">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">চিকিৎসা টিপস</span>
          <h2>প্রমাণভিত্তিক স্বাস্থ্য পরামর্শ ও গাইডলাইন</h2>
          <p>প্রতিদিনের সুস্থতায় অভিজ্ঞ চিকিৎসকের বিজ্ঞানসম্মত পরামর্শ</p>
        </div>

        <div class="tips-grid">
          <div v-for="(tip, tIdx) in displayTips" :key="tIdx" class="tip-card" @click="$emit('open-tip', tip)">
            <span class="tip-tag">{{ tip.tag }}</span>
            <h4>{{ tip.title }}</h4>
            <p>{{ tip.shortDesc }}</p>
            <div class="tip-action">সম্পূর্ণ পড়ুন ➜</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 10. PATIENT RECOVERY STORIES ================= -->
    <section class="reviews-section" id="testimonials">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">রোগীদের অভিজ্ঞতা</span>
          <h2>রোগীদের আস্থা ও সুস্থতার বিবরণ</h2>
          <p>সফল চিকিৎসা গ্রহণকারী রোগী ও তাদের পরিবারের প্রতিক্রিয়া</p>
        </div>

        <div class="reviews-grid">
          <div v-for="(test, tIdx) in displayTestimonials" :key="tIdx" class="review-card">
            <div class="stars">★★★★★</div>
            <p class="review-text">"{{ test.comment }}"</p>
            <div class="reviewer-info">
              <div class="reviewer-circle">{{ test.author_name.charAt(0) }}</div>
              <div>
                <strong>{{ test.author_name }}</strong>
                <span v-if="test.author_location"> • {{ test.author_location }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 11. CLINICAL FACILITY GALLERY ================= -->
    <section class="gallery-section" id="gallery" v-if="displayGallery && displayGallery.length > 0">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">ক্লিনিক্যাল পরিবেশ</span>
          <h2>হাসপাতাল ও চেম্বার অবকাঠামো</h2>
        </div>

        <div class="gallery-grid">
          <div v-for="(img, gIdx) in displayGallery" :key="gIdx" class="gallery-card">
            <img :src="img" :alt="'Facility ' + (gIdx + 1)" />
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 12. FREQUENTLY ASKED QUESTIONS (FAQ) ================= -->
    <section class="faq-section" id="faq">
      <div class="wrap">
        <div class="navy-head text-center">
          <span class="navy-tag">সাধারণ জিজ্ঞাসা</span>
          <h2>সচরাচর জিজ্ঞাসিত প্রশ্ন ও চিকিৎসকের উত্তর</h2>
          <p>সিরিয়াল গ্রহণ ও চেম্বার সংক্রান্ত প্রয়োজনীয় তথ্য</p>
        </div>

        <div class="faq-wrap">
          <div 
            v-for="(faq, fIdx) in displayFaqs" 
            :key="fIdx" 
            class="faq-box" 
            :class="{ 'open': activeFaqIndex === fIdx }"
            @click="activeFaqIndex = activeFaqIndex === fIdx ? null : fIdx"
          >
            <div class="faq-q-row">
              <h4>{{ faq.question }}</h4>
              <span class="faq-arrow">{{ activeFaqIndex === fIdx ? '−' : '+' }}</span>
            </div>
            <div class="faq-a-row" v-show="activeFaqIndex === fIdx">
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 13. DIRECT RESERVATION CALLOUT ================= -->
    <section class="reservation-banner">
      <div class="wrap d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h3>পেশাদার চিকিৎসাসেবা পেতে আজই যোগাযোগ করুন</h3>
          <p>আপনার স্বাস্থ্য সুরক্ষায় আমরা প্রদান করছি পূর্ণাঙ্গ কনসালটেশন ও ফলোআপ কেয়ার।</p>
        </div>
        <div class="cta-actions">
          <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-navy-outline btn-lg">
            📞 {{ contactPhone || doctor.phone }}
          </a>
          <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cyan-primary btn-lg">
            অনলাইন সিরিয়াল নির্ধারণ ➜
          </router-link>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="navy-footer">
      <div class="wrap d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 style="color:#fff;">{{ doctorDisplayName }}</h4>
          <p style="color:#94A3B8; margin:0;">{{ doctorSpecialtyEyebrow }} — সর্বোচ্চ পেশাদারিত্ব ও বিশ্বস্ততায় নিবেদিত।</p>
        </div>
        <div style="color:#64748B; font-size:0.9rem;">
          © {{ new Date().getFullYear() }} সর্বস্বত্ব সংরক্ষিত।
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'TemplateExecutiveNavy',
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
.theme-executive-navy {
  background-color: #070D1E;
  color: #E2E8F0;
  font-family: 'Hind Siliguri', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  line-height: 1.6;
}

.wrap { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* Top Strip */
.navy-top-strip { background: #040814; border-bottom: 1px solid rgba(56,189,248,0.15); padding: 8px 0; font-size: 0.88rem; color: #94A3B8; }
.navy-verified-badge { color: #38BDF8; font-weight: bold; margin-right: 8px; }
.navy-bmdc-no { color: #CBD5E1; }
.navy-top-contact a { color: #38BDF8; font-weight: bold; text-decoration: none; }

/* Header */
.executive-header { position: sticky; top: 0; z-index: 100; background: rgba(7,13,30,0.92); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.06); padding: 14px 0; transition: all 0.3s; }
.executive-header.scrolled { border-bottom-color: rgba(56,189,248,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
.brand-link { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
.brand-crest { font-size: 1.4rem; color: #38BDF8; }
.brand-title { font-size: 1.15rem; font-weight: 800; color: #fff; display: block; line-height: 1.2; }
.brand-role { font-size: 0.78rem; color: #38BDF8; display: block; }
.nav-links { display: flex; gap: 20px; }
.nav-links a { color: #94A3B8; text-decoration: none; font-size: 0.92rem; font-weight: 600; transition: color 0.2s; }
.nav-links a:hover { color: #38BDF8; }
.btn-cyan-primary { background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%); color: #fff; font-weight: bold; padding: 10px 22px; border-radius: 99px; text-decoration: none; border: none; box-shadow: 0 4px 14px rgba(2,132,199,0.3); display: inline-flex; align-items: center; }
.btn-cyan-primary:hover { background: #38BDF8; color: #000; }
.btn-navy-outline { background: transparent; border: 1.5px solid rgba(56,189,248,0.5); color: #38BDF8; font-weight: bold; padding: 10px 22px; border-radius: 99px; text-decoration: none; display: inline-flex; align-items: center; }
.btn-navy-outline:hover { background: rgba(56,189,248,0.1); color: #fff; border-color: #38BDF8; }
.burger { display: none; background: none; border: none; flex-direction: column; gap: 5px; cursor: pointer; }
.burger span { width: 24px; height: 2px; background: #fff; }
.mobile-menu { display: none; flex-direction: column; padding: 16px; background: #0A122A; border-bottom: 1px solid rgba(255,255,255,0.1); gap: 10px; }
.mobile-menu.open { display: flex; }

/* Hero */
.navy-hero { padding: 70px 0 90px; }
.hero-layout { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 50px; align-items: center; }
.authority-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(56,189,248,0.12); color: #38BDF8; border: 1px solid rgba(56,189,248,0.3); padding: 6px 14px; border-radius: 99px; font-size: 0.88rem; font-weight: 700; margin-bottom: 16px; }
.hero-doc-name { font-size: 3.2rem; font-weight: 900; color: #fff; line-height: 1.2; margin-bottom: 16px; }
.executive-badges-row { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
.badge-navy { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #38BDF8; padding: 4px 12px; border-radius: 99px; font-size: 0.85rem; font-weight: 600; }
.hero-hospital { font-size: 1.05rem; color: #CBD5E1; margin-bottom: 14px; }
.hero-statement { font-size: 1.1rem; color: #94A3B8; margin-bottom: 28px; line-height: 1.75; }
.hero-action-buttons { display: flex; gap: 14px; flex-wrap: wrap; }
.executive-portrait-box { position: relative; width: 340px; height: 440px; border-radius: 28px; overflow: hidden; border: 2px solid rgba(56,189,248,0.4); box-shadow: 0 20px 50px rgba(0,0,0,0.5); margin: 0 auto; }
.executive-img { width: 100%; height: 100%; object-fit: cover; }
.authority-floating-card { position: absolute; bottom: 20px; left: 16px; right: 16px; background: rgba(7,13,30,0.92); backdrop-filter: blur(10px); border: 1px solid rgba(56,189,248,0.3); border-radius: 16px; padding: 14px; text-align: center; }
.star-badge { color: #F59E0B; font-size: 0.9rem; display: block; margin-bottom: 2px; }
.authority-floating-card strong { color: #fff; font-size: 0.95rem; display: block; }
.authority-floating-card .sub { color: #94A3B8; font-size: 0.78rem; }

/* Impact Metrics */
.impact-metrics-section { background: #040814; border-top: 1px solid rgba(255,255,255,0.06); border-bottom: 1px solid rgba(255,255,255,0.06); padding: 40px 0; }
.metrics-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.metric-card { padding: 10px; border-right: 1px solid rgba(255,255,255,0.08); }
.metric-card:last-child { border-right: none; }
.metric-val { font-size: 2.6rem; font-weight: 900; color: #38BDF8; line-height: 1.1; margin-bottom: 8px; }
.metric-title { font-size: 1.1rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
.metric-desc { font-size: 0.88rem; color: #94A3B8; line-height: 1.6; margin: 0; }

/* Profile */
.profile-section { padding: 90px 0; background: #070D1E; }
.profile-grid { display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 50px; align-items: center; }
.profile-photo-wrap { position: relative; border-radius: 24px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); }
.profile-img { width: 100%; height: 460px; object-fit: cover; }
.creds-overlay { position: absolute; bottom: 20px; left: 20px; right: 20px; background: rgba(7,13,30,0.9); padding: 14px 18px; border-radius: 14px; border: 1px solid rgba(56,189,248,0.3); }
.profile-lead { font-size: 1.15rem; font-weight: 600; color: #CBD5E1; margin-bottom: 14px; line-height: 1.7; }
.profile-text { color: #94A3B8; margin-bottom: 28px; line-height: 1.7; }
.executive-stats-pills { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
.stat-pill-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 14px; }
.st-num { font-size: 1.4rem; font-weight: 800; color: #38BDF8; margin-bottom: 2px; }
.st-lbl { font-size: 0.82rem; color: #94A3B8; margin: 0; }

/* Head */
.navy-head { max-width: 720px; margin: 0 auto 48px; }
.navy-tag { color: #38BDF8; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; display: inline-block; margin-bottom: 8px; }
.navy-head h2 { font-size: 2.3rem; font-weight: 800; color: #fff; margin-bottom: 10px; }
.navy-head p { color: #94A3B8; font-size: 1rem; margin: 0; }

/* Disciplines */
.disciplines-section { padding: 90px 0; background: #040814; border-top: 1px solid rgba(255,255,255,0.06); border-bottom: 1px solid rgba(255,255,255,0.06); }
.disciplines-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.discipline-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 26px; transition: transform 0.2s, border-color 0.2s; }
.discipline-card:hover { transform: translateY(-4px); border-color: #38BDF8; box-shadow: 0 0 20px rgba(56,189,248,0.15); }
.discipline-icon { font-size: 28px; margin-bottom: 14px; }
.discipline-card h3 { font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.discipline-card p { font-size: 0.92rem; color: #94A3B8; margin: 0; }

/* Pathway */
.pathway-section { padding: 90px 0; background: #070D1E; }
.pathway-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.pathway-card { background: rgba(255,255,255,0.02); border: 1px solid rgba(56,189,248,0.2); border-radius: 20px; padding: 28px 20px; position: relative; text-align: center; }
.pathway-step-badge { position: absolute; top: 12px; right: 14px; font-size: 0.8rem; font-weight: 800; color: #38BDF8; background: rgba(56,189,248,0.1); padding: 2px 8px; border-radius: 99px; }
.pathway-icon { font-size: 32px; margin-bottom: 12px; }
.pathway-card h4 { font-size: 1.15rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.pathway-card p { font-size: 0.88rem; color: #94A3B8; margin: 0; }

/* Hospitals */
.hospitals-section { padding: 90px 0; background: #040814; border-top: 1px solid rgba(255,255,255,0.06); }
.hospitals-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; max-width: 980px; margin: 0 auto; }
.navy-chamber-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 28px; }
.navy-hosp-pill { font-size: 0.75rem; color: #38BDF8; font-weight: bold; text-transform: uppercase; }
.navy-status-live { font-size: 0.75rem; color: #22C55E; font-weight: 600; }
.navy-chamber-card h3 { font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
.navy-ch-address { color: #94A3B8; font-size: 0.92rem; margin-bottom: 14px; }
.navy-timing-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); padding: 10px 14px; border-radius: 12px; font-size: 0.92rem; color: #CBD5E1; display: flex; align-items: center; gap: 8px; }
.btn-navy-call { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-weight: bold; padding: 8px 18px; border-radius: 99px; text-decoration: none; font-size: 0.9rem; }

/* Fellowships */
.fellowships-section { padding: 90px 0; background: #070D1E; }
.credentials-grid { display: flex; flex-direction: column; gap: 18px; max-width: 860px; margin: 0 auto; }
.credential-item { display: flex; gap: 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 22px 28px; align-items: center; }
.cred-left { flex: none; }
.cred-year { background: #38BDF8; color: #000; font-weight: 800; font-size: 0.85rem; padding: 4px 14px; border-radius: 99px; }
.cred-right h3 { font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 4px; }
.cred-right p { color: #94A3B8; font-size: 0.9rem; margin: 0; }

/* Videos */
.videos-section { padding: 90px 0; background: #040814; border-top: 1px solid rgba(255,255,255,0.06); }
.videos-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.video-item { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; overflow: hidden; cursor: pointer; transition: transform 0.2s; }
.video-item:hover { transform: translateY(-5px); border-color: #38BDF8; }
.video-thumb { position: relative; width: 100%; height: 260px; background: #000; }
.v-img { width: 100%; height: 100%; object-fit: cover; }
.v-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; }
.v-play-btn { width: 48px; height: 48px; border-radius: 50%; background: #0284C7; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.v-duration { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.8); color: #fff; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; }
.v-details { padding: 20px; }
.v-tag { background: rgba(56,189,248,0.15); color: #38BDF8; font-size: 0.75rem; font-weight: bold; padding: 2px 8px; border-radius: 99px; display: inline-block; margin-bottom: 6px; }
.v-details h4 { font-size: 1.05rem; font-weight: 700; color: #fff; margin-bottom: 8px; line-height: 1.4; }
.v-watch { color: #38BDF8; font-weight: bold; font-size: 0.88rem; }

/* Tips */
.tips-section { padding: 90px 0; background: #070D1E; }
.tips-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
.tip-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 28px; cursor: pointer; transition: transform 0.2s; }
.tip-card:hover { transform: translateY(-4px); border-color: #38BDF8; }
.tip-tag { color: #38BDF8; font-weight: bold; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 8px; display: block; }
.tip-card h4 { font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 8px; line-height: 1.4; }
.tip-card p { color: #94A3B8; font-size: 0.92rem; margin-bottom: 14px; }
.tip-action { color: #38BDF8; font-weight: bold; font-size: 0.9rem; }

/* Reviews */
.reviews-section { padding: 90px 0; background: #040814; border-top: 1px solid rgba(255,255,255,0.06); }
.reviews-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.review-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 28px; }
.stars { color: #F59E0B; font-size: 1.1rem; margin-bottom: 12px; }
.review-text { color: #CBD5E1; font-size: 0.95rem; font-style: italic; line-height: 1.6; margin-bottom: 20px; }
.reviewer-info { display: flex; align-items: center; gap: 12px; }
.reviewer-circle { width: 40px; height: 40px; border-radius: 50%; background: #38BDF8; color: #000; display: flex; align-items: center; justify-content: center; font-weight: bold; }

/* Gallery */
.gallery-section { padding: 90px 0; background: #070D1E; }
.gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
.gallery-card { border-radius: 16px; overflow: hidden; height: 200px; border: 1px solid rgba(255,255,255,0.1); }
.gallery-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.gallery-card:hover img { transform: scale(1.05); }

/* FAQ */
.faq-section { padding: 90px 0; background: #040814; border-top: 1px solid rgba(255,255,255,0.06); }
.faq-wrap { max-width: 840px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
.faq-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 20px 24px; cursor: pointer; }
.faq-box.open { border-color: #38BDF8; }
.faq-q-row { display: flex; justify-content: space-between; align-items: center; }
.faq-q-row h4 { font-size: 1.1rem; font-weight: 700; color: #fff; margin: 0; }
.faq-arrow { font-size: 1.4rem; font-weight: bold; color: #38BDF8; }
.faq-a-row { padding-top: 14px; margin-top: 12px; border-top: 1px solid rgba(255,255,255,0.06); color: #94A3B8; font-size: 0.95rem; line-height: 1.6; }

/* Banner */
.reservation-banner { padding: 70px 0; background: linear-gradient(135deg, rgba(2,132,199,0.2) 0%, rgba(56,189,248,0.1) 100%); border-top: 1px solid rgba(56,189,248,0.3); border-bottom: 1px solid rgba(56,189,248,0.3); }
.reservation-banner h3 { font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.reservation-banner p { font-size: 1.05rem; color: #94A3B8; margin: 0; }
.cta-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 16px; }

/* Footer */
.navy-footer { background: #040814; border-top: 1px solid rgba(255,255,255,0.06); padding: 40px 0; font-size: 0.95rem; }

/* Responsive */
@media (max-width: 992px) {
  .hero-layout, .profile-grid { grid-template-columns: 1fr; }
  .metrics-grid { grid-template-columns: 1fr; }
  .disciplines-grid { grid-template-columns: 1fr; }
  .pathway-grid { grid-template-columns: repeat(2, 1fr); }
  .hospitals-grid { grid-template-columns: 1fr; }
  .videos-grid { grid-template-columns: repeat(2, 1fr); }
  .reviews-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: repeat(2, 1fr); }
  .nav-links { display: none; }
  .burger { display: flex; }
}
@media (max-width: 600px) {
  .pathway-grid { grid-template-columns: 1fr; }
  .tips-grid { grid-template-columns: 1fr; }
  .videos-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: 1fr; }
  .cta-actions { flex-direction: column; width: 100%; }
}
</style>
