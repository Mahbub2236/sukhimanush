<template>
  <div class="doctor-portfolio-theme theme-dark-pulse">
    <!-- TOP CYBER ALERT BAR -->
    <div class="cyber-alert-bar">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="alert-left">
          <span class="cyber-live-pulse"></span>
          <span class="live-status-text">অনলাইন সিরিয়াল ও চেম্বার সার্ভিস সক্রিয়</span>
          <span class="alert-divider">|</span>
          <span class="bmdc-badge-cyber">🛡️ BMDC REG: {{ doctor.bmdc_number || 'যাচাইকৃত' }}</span>
        </div>
        <div class="alert-right">
          <a :href="'tel:' + (contactPhone || doctor.phone)" class="hotline-cyber">
            জরুরী হটলাইন: {{ contactPhone || doctor.phone || 'কল করুন' }}
          </a>
        </div>
      </div>
    </div>

    <!-- MAIN NAVBAR -->
    <header class="dark-header" :class="{ 'scrolled': isScrolled }">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="brand">
          <router-link :to="'/portfolio/' + (doctor.slug || doctor.id)" class="brand-link">
            <span class="brand-symbol">◈</span>
            <div>
              <span class="brand-title">{{ doctorDisplayName }}</span>
              <span class="brand-subtitle">{{ doctorSpecialtyEyebrow || 'সিনিয়র কনসালটেন্ট' }}</span>
            </div>
          </router-link>
        </div>

        <nav class="nav-links">
          <a href="#about">পরিচিতি</a>
          <a href="#services">সেবাসমূহ</a>
          <a href="#protocol">প্রটোকল</a>
          <a href="#practice">চেম্বার</a>
          <a href="#education">ডিগ্রি</a>
          <a href="#videos">ভিডিও</a>
          <a href="#tips">টিপস</a>
          <a href="#testimonials">রিভিউ</a>
          <a href="#faq">জিজ্ঞাসা</a>
        </nav>

        <div class="nav-cta">
          <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cyber-primary">
            সিরিয়াল বুকিং
          </router-link>
        </div>

        <button class="burger" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="মেনু">
          <span></span><span></span><span></span>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div class="mobile-menu" :class="{ 'open': mobileMenuOpen }">
        <a href="#about" @click="mobileMenuOpen = false">পরিচিতি</a>
        <a href="#services" @click="mobileMenuOpen = false">সেবাসমূহ</a>
        <a href="#protocol" @click="mobileMenuOpen = false">প্রটোকল</a>
        <a href="#practice" @click="mobileMenuOpen = false">চেম্বার</a>
        <a href="#education" @click="mobileMenuOpen = false">ডিগ্রি</a>
        <a href="#videos" @click="mobileMenuOpen = false">ভিডিও</a>
        <a href="#tips" @click="mobileMenuOpen = false">টিপস</a>
        <a href="#testimonials" @click="mobileMenuOpen = false">রিভিউ</a>
        <a href="#faq" @click="mobileMenuOpen = false">জিজ্ঞাসা</a>
      </div>
    </header>

    <!-- ================= 1. HERO SECTION ================= -->
    <section class="hero">
      <div class="wrap hero-grid">
        <div class="hero-content">
          <div class="hero-eyebrow">
            <span class="pulse-point"></span>
            {{ doctorSpecialtyEyebrow || 'সিনিয়র কনসালটেন্ট ও বিশেষজ্ঞ সার্জন' }}
          </div>

          <h1 class="hero-name">
            {{ doctorDisplayName }}
          </h1>

          <div class="quals-row">
            <span v-for="(badge, bIdx) in qualificationBadges" :key="bIdx" class="qual-badge">
              {{ badge }}
            </span>
          </div>

          <p class="hero-hospital" v-if="doctorHospitalText">
            🏥 <strong>{{ doctorHospitalText }}</strong>
          </p>

          <p class="hero-desc">
            {{ aboutBio || defaultDynamicBio }}
          </p>

          <div class="hero-actions">
            <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cyber-primary btn-lg">
              অনলাইন অ্যাপয়েন্টমেন্ট নিন ➜
            </router-link>
            <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-cyber-ghost btn-lg">
              📞 সরাসরি কল: {{ contactPhone || doctor.phone }}
            </a>
          </div>
        </div>

        <div class="hero-visual">
          <div class="portrait-glow-frame">
            <img :src="doctorHeroPhoto" :alt="doctor.name" class="portrait-img" />
            <div class="cyber-scan-line"></div>
          </div>

          <!-- Rotating Stamp -->
          <div class="rotating-stamp">
            <div class="stamp-circle">
              <span class="stamp-title">{{ doctorStampTitle }}</span>
              <br v-if="doctorStampSub" />
              <span class="stamp-sub" v-if="doctorStampSub">{{ doctorStampSub }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 2. CYBER METRICS COUNTER BAR ================= -->
    <section class="metrics-section">
      <div class="wrap">
        <div class="metrics-grid">
          <div v-for="(st, sIdx) in displayStats" :key="sIdx" class="metric-box">
            <div class="metric-icon-cyber">{{ st.icon }}</div>
            <div class="metric-val-cyber">{{ st.num }}</div>
            <div class="metric-lbl-cyber">{{ st.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 3. ABOUT & CARE VALUES ================= -->
    <section class="values-section" id="about">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">চিকিৎসা দর্শন ও পরিচিতি</span>
          <h2>{{ aboutHeading }}</h2>
          <p>{{ aboutBio || defaultDynamicBio }}</p>
        </div>

        <div class="values-grid">
          <div class="value-card">
            <div class="icon">❤️</div>
            <h4>রোগীকেন্দ্রিক সেবা</h4>
            <p>প্রতিটি রোগীর রোগ নিরাময় ও শারীরিক স্বস্তিকে সর্বোচ্চ অগ্রাধিকার দিয়ে চিকিৎসা প্রদান।</p>
          </div>
          <div class="value-card">
            <div class="icon">🔬</div>
            <h4>সর্বাধুনিক ডায়াগনোসিস</h4>
            <p>আন্তর্জাতিক মেডিক্যাল গাইডলাইন ও প্রটোকল অনুসারে নির্ভুল রোগ নির্ণয় ও পরীক্ষা।</p>
          </div>
          <div class="value-card">
            <div class="icon">🎓</div>
            <h4>উচ্চতর ক্লিনিক্যাল দক্ষতা</h4>
            <p>{{ doctor.qualifications || 'উচ্চতর প্রশিক্ষণ ও দেশ-বিদেশের গবেষণা সম্পন্ন বিশেষজ্ঞ।' }}</p>
          </div>
          <div class="value-card">
            <div class="icon">🛡️</div>
            <h4>বিএমডিসি অনুমোদিত প্র্যাকটিস</h4>
            <p>রেজিঃ নং-{{ doctor.bmdc_number || 'যাচাইকৃত' }} অনুমোদিত চিকিৎসা সেবা ও ব্যবস্থাপত্র।</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 4. SPECIALIZED SERVICES ================= -->
    <section class="services" id="services">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">সেবা ক্ষেত্রসমূহ</span>
          <h2>বিশেষায়িত চিকিৎসা সেবাসমূহ</h2>
          <p>আধুনিক চিকিৎসা পদ্ধতি ও নিয়মিত ফলোআপের সমন্বয়ে পরিপূর্ণ স্বাস্থ্য সুরক্ষা।</p>
        </div>

        <div class="services-grid">
          <div v-for="(service, sIdx) in displayServices" :key="sIdx" class="service-card">
            <div class="service-icon-box">🩺</div>
            <h3>{{ service.title }}</h3>
            <p>{{ service.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 5. CLINICAL PROTOCOL (PROCESS) ================= -->
    <section class="protocol-section" id="protocol">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">চিকিৎসা প্রটোকল</span>
          <h2>পর্যায়ক্রমিক ৪ ধাপে সুশৃঙ্খল রোগী সেবা</h2>
          <p>সঠিক রোগ নির্ণয় ও চিকিৎসার প্রতিটি স্তরকে নিয়মতান্ত্রিকভাবে সাজানো হয়েছে</p>
        </div>

        <div class="protocol-grid">
          <div v-for="(pr, pIdx) in displayProcess" :key="pIdx" class="protocol-card">
            <div class="protocol-step-num">{{ pr.step }}</div>
            <div class="protocol-icon">{{ pr.icon }}</div>
            <h4>{{ pr.title }}</h4>
            <p>{{ pr.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 6. CHAMBERS & VISITING SCHEDULE ================= -->
    <section class="practice" id="practice">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">চেম্বার ও সিরিয়াল</span>
          <h2>রোগী দেখার চেম্বার ও সময়সূচি</h2>
          <p>নিচে উল্লিখিত যেকোনো শাখায় ক্লিক করে সরাসরি সিরিয়াল সংগ্রহ করতে পারেন</p>
        </div>

        <div class="practice-grid">
          <div v-for="(chamber, idx) in displayChambers" :key="idx" class="p-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="chamber-tag">চেম্বার শাখা #{{ formatBnNumber(idx + 1) }}</span>
              <span class="chamber-active-dot">● খোলা আছে</span>
            </div>
            <h3>{{ chamber.name }}</h3>
            <p class="ch-loc">📍 {{ chamber.address }}</p>
            <div class="line" v-if="chamber.schedule">🕒 {{ chamber.schedule }}</div>
            <div class="line" v-if="chamber.phone">📞 <a :href="'tel:' + chamber.phone">{{ chamber.phone }}</a></div>
            <div class="chamber-card-cta">
              <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cyber-primary chamber-book-btn">
                অনলাইনে সিরিয়াল নিন ➜
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 7. EDUCATION TIMELINE ================= -->
    <section class="education-section" id="education">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">শিক্ষাগত যোগ্যতা</span>
          <h2>উচ্চতর চিকিৎসা শিক্ষা ও ফেলোশিপ</h2>
          <p>দেশ-বিদেশের স্বনামধন্য প্রতিষ্ঠান থেকে অর্জিত ডিগ্রির মাইলফলক</p>
        </div>

        <div class="timeline">
          <div v-for="(item, eIdx) in displayEducation" :key="eIdx" class="t-item">
            <div class="t-dot">{{ formatBnNumber(eIdx + 1) }}</div>
            <div class="t-card">
              <span class="t-year" v-if="item.year">{{ item.year }}</span>
              <h3>{{ item.institution }}</h3>
              <p>{{ item.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 8. SHORT VIDEOS / REELS ================= -->
    <section class="short-videos-section" id="videos" v-if="displayShortVideos && displayShortVideos.length > 0">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">🎬 স্বাস্থ্য রিলস ও ভিডিও</span>
          <h2>শর্ট ভিডিও ও সচেতনতামূলক বার্তা</h2>
          <p>ক্লিক করে সরাসরি চিকিৎসকের প্রয়োজনীয় স্বাস্থ্য পরামর্শ শুনুন</p>
        </div>

        <div class="video-reels-grid">
          <div v-for="(video, vIdx) in displayShortVideos" :key="vIdx" class="video-reel-card" @click="$emit('open-video', video)">
            <div class="reel-thumbnail-wrap">
              <img :src="video.thumbnail_url || '/images/team/1.jpg'" class="reel-thumb" />
              <div class="reel-play-badge">▶</div>
              <span class="reel-duration" v-if="video.duration">{{ video.duration }}</span>
              <span class="reel-tag" v-if="video.tag">{{ video.tag }}</span>
            </div>
            <div class="reel-info">
              <h4>{{ video.title }}</h4>
              <p v-if="video.description">{{ video.description }}</p>
              <span class="reel-link">ভিডিও প্লে করুন ➜</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 9. DOCTOR'S HEALTH TIPS ================= -->
    <section class="tips-section" id="tips">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">স্বাস্থ্য টিপস</span>
          <h2>সুস্থ জীবনযাপনে বিশেষজ্ঞ চিকিৎসকের পরামর্শ</h2>
          <p>ক্লিক করে প্রতিটি পরামর্শের বিস্তারিত নির্দেশিকা পড়ুন</p>
        </div>

        <div class="tips-grid">
          <div v-for="(tip, tIdx) in displayTips" :key="tIdx" class="cyber-tip-card" @click="$emit('open-tip', tip)">
            <span class="tip-badge">{{ tip.tag }}</span>
            <h4>{{ tip.title }}</h4>
            <p>{{ tip.shortDesc }}</p>
            <div class="tip-read-more">বিস্তারিত পড়ুন ➜</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 10. PATIENT TESTIMONIALS ================= -->
    <section class="testimonials-section" id="testimonials">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">রোগীদের মতামত</span>
          <h2>আস্থা ও কৃতজ্ঞতার বাস্তব অভিজ্ঞতা</h2>
          <p>সুস্থ হওয়া রোগী ও তাদের পরিবারের আন্তরিক রিভিউ</p>
        </div>

        <div class="testimonials-grid">
          <div v-for="(test, tIdx) in displayTestimonials" :key="tIdx" class="test-cyber-card">
            <div class="cyber-stars">★★★★★</div>
            <p class="test-body">"{{ test.comment }}"</p>
            <div class="test-author-row">
              <div class="author-circle">{{ test.author_name.charAt(0) }}</div>
              <div>
                <strong>{{ test.author_name }}</strong>
                <span class="author-sub" v-if="test.author_location"> • {{ test.author_location }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 11. CLINICAL GALLERY ================= -->
    <section class="gallery-section" id="gallery" v-if="displayGallery && displayGallery.length > 0">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">ক্লিনিক্যাল গ্যালারি</span>
          <h2>চেম্বার ও চিকিৎসা পরিবেশের চিত্র</h2>
        </div>

        <div class="gallery-grid">
          <div v-for="(img, gIdx) in displayGallery" :key="gIdx" class="cyber-gallery-item">
            <img :src="img" :alt="'Chamber ' + (gIdx + 1)" />
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 12. FAQ ACCORDION ================= -->
    <section class="faq-section" id="faq">
      <div class="wrap">
        <div class="section-head text-center">
          <span class="kicker">সাধারণ প্রশ্নোত্তর</span>
          <h2>সচরাচর জিজ্ঞাসিত প্রশ্ন ও উত্তর</h2>
          <p>সিরিয়াল বুকিং ও চেম্বার সার্ভিস সম্পর্কে বিস্তারিত জেনে নিন</p>
        </div>

        <div class="faq-cyber-wrap">
          <div 
            v-for="(faq, fIdx) in displayFaqs" 
            :key="fIdx" 
            class="faq-cyber-card" 
            :class="{ 'open': activeFaqIndex === fIdx }"
            @click="activeFaqIndex = activeFaqIndex === fIdx ? null : fIdx"
          >
            <div class="faq-head-row">
              <h4>{{ faq.question }}</h4>
              <span class="faq-toggle-ico">{{ activeFaqIndex === fIdx ? '−' : '+' }}</span>
            </div>
            <div class="faq-content-row" v-show="activeFaqIndex === fIdx">
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 13. HOTLINE CALLOUT BANNER ================= -->
    <section class="cyber-cta-banner">
      <div class="wrap d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h3>বিশেষজ্ঞ চিকিৎসকের পরামর্শ নিন আজই</h3>
          <p>সরাসরি চেম্বারে আসুন অথবা অনলাইন ভিডিও কনসালটেশনের মাধ্যমে জরুরি প্রেসক্রিপশন গ্রহণ করুন।</p>
        </div>
        <div class="cta-actions">
          <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-cyber-ghost btn-lg">
            📞 {{ contactPhone || doctor.phone }}
          </a>
          <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-cyber-primary btn-lg">
            অনলাইনে সিরিয়াল নিন ➜
          </router-link>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="cyber-footer">
      <div class="wrap d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <strong style="color:#22C55E;">{{ doctorDisplayName }}</strong> — {{ doctorSpecialtyEyebrow }}
          <div style="color:#94A3B8; font-size:0.85rem; margin-top:4px;">আধুনিক চিকিৎসাসেবা ও রোগীর পূর্ণ সুস্থতায় নিয়োজিত।</div>
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
  name: 'TemplateDarkPulse',
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
    doctorStampTitle: { type: String, default: '' },
    doctorStampSub: { type: String, default: '' },
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
.theme-dark-pulse {
  background-color: #090F0C;
  color: #E2E8F0;
  font-family: 'Hind Siliguri', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  line-height: 1.6;
}

.wrap { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* Alert Bar */
.cyber-alert-bar { background: #050B08; border-bottom: 1px solid rgba(34,197,94,0.15); padding: 8px 0; font-size: 0.88rem; color: #94A3B8; }
.cyber-live-pulse { width: 8px; height: 8px; border-radius: 50%; background: #22C55E; display: inline-block; box-shadow: 0 0 10px #22C55E; margin-right: 6px; }
.live-status-text { color: #E2E8F0; font-weight: 600; }
.alert-divider { margin: 0 10px; color: #334155; }
.bmdc-badge-cyber { color: #06B6D4; font-weight: 600; }
.hotline-cyber { color: #22C55E; font-weight: bold; text-decoration: none; }

/* Header */
.dark-header { position: sticky; top: 0; z-index: 100; background: rgba(9,15,12,0.92); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.06); padding: 14px 0; transition: all 0.3s; }
.dark-header.scrolled { border-bottom-color: rgba(34,197,94,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
.brand-link { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
.brand-symbol { font-size: 1.4rem; color: #22C55E; }
.brand-title { font-size: 1.15rem; font-weight: 800; color: #fff; display: block; line-height: 1.2; }
.brand-subtitle { font-size: 0.78rem; color: #06B6D4; display: block; }
.nav-links { display: flex; gap: 20px; }
.nav-links a { color: #94A3B8; text-decoration: none; font-size: 0.92rem; font-weight: 600; transition: color 0.2s; }
.nav-links a:hover { color: #22C55E; }
.btn-cyber-primary { background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%); color: #000; font-weight: 800; padding: 8px 20px; border-radius: 99px; text-decoration: none; box-shadow: 0 0 16px rgba(34,197,94,0.4); border: none; display: inline-flex; align-items: center; }
.btn-cyber-primary:hover { background: #4ADE80; color: #000; }
.btn-cyber-ghost { background: transparent; border: 1.5px solid rgba(34,197,94,0.5); color: #22C55E; font-weight: 800; padding: 10px 24px; border-radius: 99px; text-decoration: none; display: inline-flex; align-items: center; }
.btn-cyber-ghost:hover { background: rgba(34,197,94,0.1); color: #fff; border-color: #22C55E; }
.burger { display: none; background: none; border: none; flex-direction: column; gap: 5px; cursor: pointer; }
.burger span { width: 24px; height: 2px; background: #fff; }
.mobile-menu { display: none; flex-direction: column; padding: 16px; background: #0F172A; border-bottom: 1px solid rgba(255,255,255,0.1); gap: 10px; }
.mobile-menu.open { display: flex; }

/* Hero */
.hero { padding: 70px 0 90px; }
.hero-grid { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 40px; align-items: center; }
.hero-eyebrow { display: inline-flex; align-items: center; gap: 8px; background: rgba(34,197,94,0.12); color: #22C55E; border: 1px solid rgba(34,197,94,0.3); padding: 6px 14px; border-radius: 99px; font-size: 0.88rem; font-weight: 700; margin-bottom: 16px; }
.pulse-point { width: 8px; height: 8px; border-radius: 50%; background: #22C55E; box-shadow: 0 0 8px #22C55E; }
.hero-name { font-size: 3rem; font-weight: 900; color: #fff; line-height: 1.2; margin-bottom: 16px; }
.quals-row { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
.qual-badge { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #38BDF8; padding: 4px 12px; border-radius: 99px; font-size: 0.85rem; font-weight: 600; }
.hero-hospital { font-size: 1.05rem; color: #CBD5E1; margin-bottom: 14px; }
.hero-desc { font-size: 1.05rem; color: #94A3B8; margin-bottom: 28px; line-height: 1.7; }
.hero-actions { display: flex; gap: 14px; flex-wrap: wrap; }
.hero-visual { position: relative; display: flex; justify-content: center; align-items: center; }
.portrait-glow-frame { width: 340px; height: 420px; border-radius: 28px; overflow: hidden; border: 2px solid rgba(34,197,94,0.5); box-shadow: 0 0 40px rgba(34,197,94,0.25); position: relative; background: #000; }
.portrait-img { width: 100%; height: 100%; object-fit: cover; }
.rotating-stamp { position: absolute; bottom: -20px; right: -10px; width: 120px; height: 120px; border-radius: 50%; background: #090F0C; border: 2px dashed #22C55E; display: flex; align-items: center; justify-content: center; text-align: center; color: #22C55E; font-size: 0.72rem; font-weight: bold; animation: spin 20s linear infinite; }
@keyframes spin { 100% { transform: rotate(360deg); } }

/* Metrics */
.metrics-section { background: rgba(255,255,255,0.02); border-top: 1px solid rgba(255,255,255,0.06); border-bottom: 1px solid rgba(255,255,255,0.06); padding: 36px 0; }
.metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: center; }
.metric-box { padding: 10px; border-right: 1px solid rgba(255,255,255,0.06); }
.metric-box:last-child { border-right: none; }
.metric-icon-cyber { font-size: 26px; margin-bottom: 6px; }
.metric-val-cyber { font-size: 2.2rem; font-weight: 900; color: #22C55E; line-height: 1.1; margin-bottom: 4px; }
.metric-lbl-cyber { font-size: 0.92rem; color: #94A3B8; font-weight: 600; }

/* Section Head Generic */
.section-head { max-width: 720px; margin: 0 auto 48px; }
.kicker { color: #22C55E; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 8px; }
.section-head h2 { font-size: 2.2rem; font-weight: 800; color: #fff; margin-bottom: 10px; }
.section-head p { color: #94A3B8; font-size: 1rem; margin: 0; }

/* Values */
.values-section { padding: 90px 0; }
.values-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.value-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 26px 20px; text-align: center; transition: transform 0.2s, border-color 0.2s; }
.value-card:hover { transform: translateY(-4px); border-color: #22C55E; }
.value-card .icon { font-size: 32px; margin-bottom: 14px; }
.value-card h4 { font-size: 1.15rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.value-card p { font-size: 0.88rem; color: #94A3B8; margin: 0; }

/* Services */
.services { padding: 90px 0; background: rgba(0,0,0,0.3); border-top: 1px solid rgba(255,255,255,0.06); border-bottom: 1px solid rgba(255,255,255,0.06); }
.services-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.service-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 26px; transition: transform 0.2s, border-color 0.2s; }
.service-card:hover { transform: translateY(-4px); border-color: #06B6D4; box-shadow: 0 0 20px rgba(6,182,212,0.15); }
.service-icon-box { font-size: 28px; margin-bottom: 14px; }
.service-card h3 { font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.service-card p { font-size: 0.92rem; color: #94A3B8; margin: 0; }

/* Protocol */
.protocol-section { padding: 90px 0; }
.protocol-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.protocol-card { background: rgba(255,255,255,0.02); border: 1px solid rgba(34,197,94,0.2); border-radius: 20px; padding: 28px 20px; position: relative; text-align: center; }
.protocol-step-num { position: absolute; top: 12px; right: 14px; font-size: 0.8rem; font-weight: 800; color: #22C55E; background: rgba(34,197,94,0.1); padding: 2px 8px; border-radius: 99px; }
.protocol-icon { font-size: 32px; margin-bottom: 12px; }
.protocol-card h4 { font-size: 1.15rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.protocol-card p { font-size: 0.88rem; color: #94A3B8; margin: 0; }

/* Practice / Chambers */
.practice { padding: 90px 0; background: rgba(0,0,0,0.3); border-top: 1px solid rgba(255,255,255,0.06); }
.practice-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
.p-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 28px; }
.chamber-tag { font-size: 0.78rem; font-weight: bold; color: #22C55E; text-transform: uppercase; }
.chamber-active-dot { font-size: 0.78rem; color: #22C55E; font-weight: 600; }
.p-card h3 { font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
.ch-loc { color: #94A3B8; font-size: 0.92rem; margin-bottom: 12px; }
.line { color: #CBD5E1; font-size: 0.92rem; margin-bottom: 6px; }
.line a { color: #22C55E; text-decoration: none; }
.chamber-card-cta { margin-top: 20px; }
.chamber-book-btn { width: 100%; text-align: center; display: block; }

/* Education Timeline */
.education-section { padding: 90px 0; }
.timeline { display: flex; flex-direction: column; gap: 18px; max-width: 860px; margin: 0 auto; }
.t-item { display: flex; gap: 20px; align-items: flex-start; }
.t-dot { width: 36px; height: 36px; border-radius: 50%; background: #22C55E; color: #000; font-weight: 800; display: flex; align-items: center; justify-content: center; flex: none; font-size: 0.9rem; }
.t-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 22px; flex: 1; }
.t-year { color: #22C55E; font-size: 0.82rem; font-weight: bold; margin-bottom: 4px; display: block; }
.t-card h3 { font-size: 1.15rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
.t-card p { font-size: 0.9rem; color: #94A3B8; margin: 0; }

/* Reels */
.short-videos-section { padding: 90px 0; background: rgba(0,0,0,0.3); border-top: 1px solid rgba(255,255,255,0.06); }
.video-reels-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.video-reel-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; overflow: hidden; cursor: pointer; transition: transform 0.2s, border-color 0.2s; }
.video-reel-card:hover { transform: translateY(-5px); border-color: #22C55E; }
.reel-thumbnail-wrap { position: relative; width: 100%; height: 260px; background: #000; }
.reel-thumb { width: 100%; height: 100%; object-fit: cover; }
.reel-play-badge { position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #22C55E; }
.reel-duration { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.8); color: #fff; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; }
.reel-tag { position: absolute; top: 8px; left: 8px; background: #22C55E; color: #000; font-size: 0.75rem; font-weight: bold; padding: 2px 8px; border-radius: 99px; }
.reel-info { padding: 20px; }
.reel-info h4 { font-size: 1.1rem; font-weight: 800; color: #fff; margin-bottom: 8px; line-height: 1.4; }
.reel-info p { color: #94A3B8; font-size: 0.9rem; margin-bottom: 12px; }
.reel-link { color: #22C55E; font-weight: bold; font-size: 0.88rem; }

/* Tips */
.tips-section { padding: 90px 0; }
.tips-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
.cyber-tip-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 28px; cursor: pointer; transition: transform 0.2s, border-color 0.2s; }
.cyber-tip-card:hover { transform: translateY(-4px); border-color: #22C55E; }
.tip-badge { color: #06B6D4; font-weight: bold; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 8px; display: block; }
.cyber-tip-card h4 { font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 8px; line-height: 1.4; }
.cyber-tip-card p { color: #94A3B8; font-size: 0.92rem; margin-bottom: 14px; }
.tip-read-more { color: #22C55E; font-weight: 700; font-size: 0.9rem; }

/* Testimonials */
.testimonials-section { padding: 90px 0; background: rgba(0,0,0,0.3); border-top: 1px solid rgba(255,255,255,0.06); }
.testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.test-cyber-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 28px; }
.cyber-stars { color: #22C55E; font-size: 1.1rem; margin-bottom: 12px; }
.test-body { color: #CBD5E1; font-size: 0.95rem; font-style: italic; line-height: 1.6; margin-bottom: 20px; }
.test-author-row { display: flex; align-items: center; gap: 12px; }
.author-circle { width: 40px; height: 40px; border-radius: 50%; background: #22C55E; color: #000; display: flex; align-items: center; justify-content: center; font-weight: bold; }
.author-sub { color: #94A3B8; font-size: 0.85rem; }

/* Gallery */
.gallery-section { padding: 90px 0; }
.gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
.cyber-gallery-item { border-radius: 16px; overflow: hidden; height: 200px; border: 1px solid rgba(255,255,255,0.1); }
.cyber-gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.cyber-gallery-item:hover img { transform: scale(1.05); }

/* FAQ */
.faq-section { padding: 90px 0; background: rgba(0,0,0,0.3); border-top: 1px solid rgba(255,255,255,0.06); }
.faq-cyber-wrap { max-width: 840px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
.faq-cyber-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 20px 24px; cursor: pointer; transition: border-color 0.2s; }
.faq-cyber-card.open { border-color: #22C55E; box-shadow: 0 0 16px rgba(34,197,94,0.1); }
.faq-head-row { display: flex; justify-content: space-between; align-items: center; }
.faq-head-row h4 { font-size: 1.1rem; font-weight: 700; color: #fff; margin: 0; }
.faq-toggle-ico { font-size: 1.4rem; font-weight: bold; color: #22C55E; }
.faq-content-row { padding-top: 14px; margin-top: 12px; border-top: 1px solid rgba(255,255,255,0.06); color: #94A3B8; font-size: 0.95rem; line-height: 1.6; }

/* CTA Banner */
.cyber-cta-banner { padding: 70px 0; background: linear-gradient(135deg, rgba(34,197,94,0.15) 0%, rgba(6,182,212,0.15) 100%); border-top: 1px solid rgba(34,197,94,0.3); border-bottom: 1px solid rgba(34,197,94,0.3); }
.cyber-cta-banner h3 { font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.cyber-cta-banner p { font-size: 1.05rem; color: #94A3B8; margin: 0; }
.cta-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 16px; }

/* Footer */
.cyber-footer { background: #050B08; border-top: 1px solid rgba(255,255,255,0.06); padding: 40px 0; font-size: 0.95rem; }

/* Responsive */
@media (max-width: 992px) {
  .hero-grid { grid-template-columns: 1fr; }
  .metrics-grid { grid-template-columns: repeat(2, 1fr); }
  .values-grid { grid-template-columns: repeat(2, 1fr); }
  .services-grid { grid-template-columns: 1fr; }
  .protocol-grid { grid-template-columns: repeat(2, 1fr); }
  .practice-grid { grid-template-columns: 1fr; }
  .video-reels-grid { grid-template-columns: repeat(2, 1fr); }
  .testimonials-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: repeat(2, 1fr); }
  .nav-links { display: none; }
  .burger { display: flex; }
}
@media (max-width: 600px) {
  .metrics-grid { grid-template-columns: 1fr; }
  .values-grid { grid-template-columns: 1fr; }
  .protocol-grid { grid-template-columns: 1fr; }
  .tips-grid { grid-template-columns: 1fr; }
  .video-reels-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: 1fr; }
  .cta-actions { flex-direction: column; width: 100%; }
}
</style>
