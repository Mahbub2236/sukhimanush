<template>
  <div class="doctor-portfolio-theme theme-serene-wellness">
    <!-- TOP SERENE BAR -->
    <div class="serene-topbar">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="topbar-left">
          <span>🌿 ব্যক্তিগত যত্ন, মানসিক প্রশান্তি ও আন্তর্জাতিক মানের চিকিৎসা</span>
        </div>
        <div class="topbar-right">
          <a :href="'tel:' + (contactPhone || doctor.phone)" class="serene-phone">
            পরামর্শ ও সিরিয়াল: {{ contactPhone || doctor.phone || 'কল করুন' }}
          </a>
        </div>
      </div>
    </div>

    <!-- EDITORIAL HEADER -->
    <header class="serene-header" :class="{ 'scrolled': isScrolled }">
      <div class="wrap d-flex justify-content-between align-items-center">
        <div class="brand">
          <router-link :to="'/portfolio/' + (doctor.slug || doctor.id)" class="brand-link">
            <span class="gold-flower">✦</span>
            <div>
              <span class="brand-name">{{ doctorDisplayName }}</span>
              <span class="brand-sub">{{ doctorSpecialtyEyebrow || 'ওয়েলনেস ও বিশেষায়িত চিকিৎসা' }}</span>
            </div>
          </router-link>
        </div>

        <nav class="nav-links">
          <a href="#philosophy">দর্শন</a>
          <a href="#treatments">সেবাসমূহ</a>
          <a href="#journey">চিকিৎসা যাত্রা</a>
          <a href="#wellness-chambers">চেম্বার</a>
          <a href="#credentials">যোগ্যতা</a>
          <a href="#videos">ভিডিও</a>
          <a href="#lifestyle-tips">টিপস</a>
          <a href="#client-stories">রিভিউ</a>
          <a href="#faq">জিজ্ঞাসা</a>
        </nav>

        <div class="header-action">
          <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-gold">
            সিরিয়াল বুক করুন
          </router-link>
        </div>

        <button class="burger" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="মেনু">
          <span></span><span></span><span></span>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div class="mobile-menu" :class="{ 'open': mobileMenuOpen }">
        <a href="#philosophy" @click="mobileMenuOpen = false">দর্শন</a>
        <a href="#treatments" @click="mobileMenuOpen = false">সেবাসমূহ</a>
        <a href="#journey" @click="mobileMenuOpen = false">চিকিৎসা যাত্রা</a>
        <a href="#wellness-chambers" @click="mobileMenuOpen = false">চেম্বার</a>
        <a href="#credentials" @click="mobileMenuOpen = false">যোগ্যতা</a>
        <a href="#videos" @click="mobileMenuOpen = false">ভিডিও</a>
        <a href="#lifestyle-tips" @click="mobileMenuOpen = false">টিপস</a>
        <a href="#client-stories" @click="mobileMenuOpen = false">রিভিউ</a>
        <a href="#faq" @click="mobileMenuOpen = false">জিজ্ঞাসা</a>
      </div>
    </header>

    <!-- ================= 1. ARCH-CURVED EDITORIAL HERO ================= -->
    <section class="editorial-hero">
      <div class="wrap hero-wrap">
        <div class="hero-left">
          <div class="wellness-kicker">
            <span class="gold-star">✦</span> {{ doctorSpecialtyEyebrow || 'সিনিয়র ওয়েলনেস বিশেষজ্ঞ ও কনসালটেন্ট' }}
          </div>

          <h1 class="hero-title">
            {{ doctorDisplayName }}
          </h1>

          <div class="hero-quals-strip">
            <span v-for="(badge, bIdx) in qualificationBadges" :key="bIdx" class="badge-gold">
              {{ badge }}
            </span>
          </div>

          <p class="hero-hospital" v-if="doctorHospitalText">
            🏥 <strong>{{ doctorHospitalText }}</strong>
          </p>

          <p class="hero-bio-lead">
            {{ aboutBio || defaultDynamicBio }}
          </p>

          <div class="hero-cta-group">
            <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-gold btn-lg">
              পরামর্শের জন্য সিরিয়াল নিন ✦
            </router-link>
            <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-outline-gold btn-lg">
              সরাসরি কল: {{ contactPhone || doctor.phone }}
            </a>
          </div>
        </div>

        <!-- Right Column: Arch-Curved Frame -->
        <div class="hero-right">
          <div class="arch-portrait-card">
            <div class="arch-frame">
              <img :src="doctorHeroPhoto" :alt="doctor.name" class="arch-img" />
            </div>
            <!-- Gold Seal Badge -->
            <div class="gold-seal-badge">
              <span class="seal-sparkle">★</span>
              <div>
                <strong>বিএমডিসি নিবন্ধিত</strong>
                <div class="small">রেজিঃ {{ doctor.bmdc_number || 'যাচাইকৃত' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 2. PHILOSOPHY QUOTE BLOCK ================= -->
    <section class="philosophy-section" id="philosophy">
      <div class="wrap">
        <div class="quote-card">
          <div class="quote-mark">“</div>
          <p class="quote-text">
            রোগ কেবল দূর করাই নয়, রোগীর মানসিক স্বস্তি ফিরিয়ে দিয়ে দীর্ঘমেয়াদী পূর্ণাঙ্গ সুস্থতা নিশ্চিত করাই আমাদের চিকিৎসার মূল ব্রত। প্রতিটি মানুষকে পরম আন্তরিকতা ও সহানুভূতিতে সেবা দেওয়াতেই সার্থকতা।
          </p>
          <div class="quote-author">
            <strong>— {{ doctorDisplayName }}</strong>
            <span>{{ doctorSpecialtyEyebrow }}</span>
          </div>
        </div>

        <!-- 4 Golden Pillars -->
        <div class="pillars-grid">
          <div class="pillar-card">
            <div class="pillar-icon">🌿</div>
            <h3>সহানুভূতিশীল যত্ন</h3>
            <p>রোগীর প্রতিটি সমস্যা মনোযোগ দিয়ে শুনে ব্যক্তিগত স্বাস্থ্য পরিকল্পনা ও সময়োপযোগী দিকনির্দেশনা।</p>
          </div>
          <div class="pillar-card">
            <div class="pillar-icon">✨</div>
            <h3>আধুনিক প্রযুক্তি</h3>
            <p>সর্বাধুনিক আন্তর্জাতিক চিকিৎসা পদ্ধতি ও নির্ভুল রোগ নির্ণয়ের মাধ্যমে সর্বোচ্চ নিরাপদ ফলাফল।</p>
          </div>
          <div class="pillar-card">
            <div class="pillar-icon">🤍</div>
            <h3>নিরাপত্তা ও গোপনীয়তা</h3>
            <p>সম্পূর্ণ পেশাদারিত্ব, সর্বোচ্চ গোপনীয়তা এবং রোগীর মানসিক আরাম ও স্বস্তির অটুট নিশ্চয়তা।</p>
          </div>
          <div class="pillar-card">
            <div class="pillar-icon">🌱</div>
            <h3>প্রিভেন্টিভ লাইফস্টাইল</h3>
            <p>ওষুধের পাশাপাশি খাদ্যাভ্যাস ও মনস্তাত্ত্বিক স্বাস্থ্যের উন্নয়নে সামগ্রিক সুস্থতার সঠিক রূপরেখা।</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 3. TREATMENTS & SERVICES (PILL TILES) ================= -->
    <section class="treatments-section" id="treatments">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">সেবাসমূহ</span>
          <h2>বিশেষায়িত চিকিৎসা ক্ষেত্র ও থেরাপি</h2>
          <p>বিজ্ঞানসম্মত ও প্রমাণভিত্তিক চিকিৎসার মাধ্যমে সামগ্রিক সুস্থতা নিশ্চিতকরণ</p>
        </div>

        <div class="treatments-grid">
          <div v-for="(item, idx) in displayServices" :key="idx" class="treatment-pill-card">
            <div class="treatment-num">{{ formatBnNumber(idx + 1) }}</div>
            <div class="treatment-info">
              <h4>{{ item.title }}</h4>
              <p>{{ item.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 4. 4-STEP HEALING JOURNEY ================= -->
    <section class="journey-section" id="journey">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">চিকিৎসার ধাপ</span>
          <h2>সুস্থতার দিকে আপনার ৪টি সহজ ধাপ</h2>
          <p>পরামর্শ গ্রহণ থেকে পূর্ণ আরোগ্য লাভ পর্যন্ত প্রতিটি স্তর সুপরিকল্পিত</p>
        </div>

        <div class="journey-grid">
          <div v-for="(pr, pIdx) in displayProcess" :key="pIdx" class="journey-step-card">
            <div class="journey-step-num">{{ pr.step }}</div>
            <div class="journey-icon">{{ pr.icon }}</div>
            <h4>{{ pr.title }}</h4>
            <p>{{ pr.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 5. ACADEMIC CREDENTIALS & TRAINING ================= -->
    <section class="credentials-section" id="credentials">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">মেডিকেল প্রোফাইল</span>
          <h2>উচ্চতর ডিগ্রি ও ক্লিনিক্যাল ফেলোশিপ</h2>
          <p>চিকিৎসাবিজ্ঞানে আন্তর্জাতিক মানের প্রশিক্ষণ ও প্রাতিষ্ঠানিক ডিগ্রি</p>
        </div>

        <div class="credentials-timeline">
          <div v-for="(edu, eIdx) in displayEducation" :key="eIdx" class="cred-card">
            <div class="cred-year">{{ edu.year }}</div>
            <h3>{{ edu.institution }}</h3>
            <p>{{ edu.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 6. WELLNESS CHAMBERS ================= -->
    <section class="wellness-chambers-section" id="wellness-chambers">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">সাক্ষাতের ঠিকানা</span>
          <h2>চেম্বার ও ভিজিটিং সময়সূচি</h2>
          <p>প্রশান্ত পরিবেশে সরাসরি পরামর্শের জন্য নিচের চেম্বারে যোগাযোগ করুন</p>
        </div>

        <div class="wellness-chambers-grid">
          <div v-for="(ch, cIdx) in displayChambers" :key="cIdx" class="wellness-chamber-card">
            <div class="chamber-flower-icon">🌸</div>
            <div class="chamber-num-badge">চেম্বার শাখা #{{ formatBnNumber(cIdx + 1) }}</div>
            <h3>{{ ch.name }}</h3>
            <p class="ch-address">📍 {{ ch.address }}</p>
            <div class="ch-schedule-box" v-if="ch.schedule">
              <span>🕒 সময়সূচি: <strong>{{ ch.schedule }}</strong></span>
            </div>
            <div class="ch-phone-line" v-if="ch.phone">
              <span>📞 সিরিয়াল: <a :href="'tel:' + ch.phone">{{ ch.phone }}</a></span>
            </div>
            <div class="mt-4">
              <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-gold btn-block">
                সিরিয়াল নিশ্চিত করুন ✦
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 7. SHORT VIDEOS & REELS ================= -->
    <section class="videos-section" id="videos" v-if="displayShortVideos && displayShortVideos.length > 0">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">ভিডিও রিলস</span>
          <h2>বিশেষজ্ঞ চিকিৎসকের পরামর্শ ভিডিও</h2>
          <p>ক্লিক করে সরাসরি স্বাস্থ্য বার্তা ও সচেতনতামূলক ভিডিও শুনুন</p>
        </div>

        <div class="videos-grid">
          <div v-for="(v, vIdx) in displayShortVideos" :key="vIdx" class="video-card" @click="$emit('open-video', v)">
            <div class="video-thumb-box">
              <img :src="v.thumbnail_url || '/images/team/1.jpg'" class="video-img" />
              <div class="video-play-gold">▶</div>
              <span class="video-duration-pill" v-if="v.duration">{{ v.duration }}</span>
            </div>
            <div class="video-meta">
              <span class="video-tag" v-if="v.tag">{{ v.tag }}</span>
              <h4>{{ v.title }}</h4>
              <span class="video-link">ভিডিও দেখুন ✦</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 8. LIFESTYLE & HEALTH TIPS ================= -->
    <section class="lifestyle-section" id="lifestyle-tips">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">সুস্থ জীবনযাপন</span>
          <h2>দৈনন্দিন ওয়েলনেস ও স্বাস্থ্য টিপস</h2>
          <p>সুস্থ ও নীরোগ থাকতে চিকিৎসকের প্রমাণিত পরামর্শ ও নির্দেশিকা</p>
        </div>

        <div class="lifestyle-grid">
          <div v-for="(tip, tIdx) in displayTips" :key="tIdx" class="lifestyle-card" @click="$emit('open-tip', tip)">
            <span class="tip-gold-tag">{{ tip.tag }}</span>
            <h4>{{ tip.title }}</h4>
            <p>{{ tip.shortDesc }}</p>
            <div class="read-more-gold">পুরো টিপস পড়ুন ➜</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 9. CLIENT STORIES & REVIEWS ================= -->
    <section class="client-stories-section" id="client-stories">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">প্রশংসাপত্র</span>
          <h2>আস্থা ও ভালোবাসার বাস্তব গল্প</h2>
          <p>চিকিৎসা নিয়ে সুস্থ হওয়া রোগী ও তাদের পরিবারের আন্তরিক রিভিউ</p>
        </div>

        <div class="stories-grid">
          <div v-for="(t, tIdx) in displayTestimonials" :key="tIdx" class="story-card">
            <div class="stars-gold">★★★★★</div>
            <p class="story-comment">"{{ t.comment }}"</p>
            <div class="story-author">
              <div class="story-avatar">{{ t.author_name.charAt(0) }}</div>
              <div>
                <strong>{{ t.author_name }}</strong>
                <span v-if="t.author_location">({{ t.author_location }})</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 10. SERENE AESTHETIC GALLERY ================= -->
    <section class="gallery-section" id="gallery" v-if="displayGallery && displayGallery.length > 0">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">পরিবেশ ও ক্লিনিক</span>
          <h2>প্রশান্ত ও আধুনিক চিকিৎসা পরিবেশ</h2>
        </div>

        <div class="gallery-grid">
          <div v-for="(img, gIdx) in displayGallery" :key="gIdx" class="gallery-arch-item">
            <img :src="img" :alt="'Clinic ' + (gIdx + 1)" />
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 11. FAQ ACCORDION ================= -->
    <section class="faq-section" id="faq">
      <div class="wrap">
        <div class="editorial-head text-center">
          <span class="sub-kicker">সাধারণ জিজ্ঞাসা</span>
          <h2>সচরাচর জিজ্ঞাসিত প্রশ্ন ও উত্তর</h2>
          <p>সিরিয়াল বুকিং ও সেবা গ্রহণের সার্বিক নিয়মাবলী</p>
        </div>

        <div class="faq-serene-wrap">
          <div 
            v-for="(faq, fIdx) in displayFaqs" 
            :key="fIdx" 
            class="faq-serene-card" 
            :class="{ 'open': activeFaqIndex === fIdx }"
            @click="activeFaqIndex = activeFaqIndex === fIdx ? null : fIdx"
          >
            <div class="faq-serene-head">
              <h4>{{ faq.question }}</h4>
              <span class="faq-ico">{{ activeFaqIndex === fIdx ? '−' : '+' }}</span>
            </div>
            <div class="faq-serene-content" v-show="activeFaqIndex === fIdx">
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= 12. INVITATION CTA ================= -->
    <section class="invitation-banner">
      <div class="wrap text-center">
        <span class="gold-flower mb-2">✦</span>
        <h2>আপনার পূর্ণ সুস্থতার পথে প্রথম পদক্ষেপ নিন</h2>
        <p>সরাসরি চেম্বারে আসুন অথবা অনলাইন ভিডিও কলে পরামর্শ নিন</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
          <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-gold btn-lg">
            অনলাইনে সিরিয়াল নিশ্চিত করুন ✦
          </router-link>
          <a :href="'tel:' + (contactPhone || doctor.phone)" class="btn btn-outline-gold btn-lg">
            📞 সরাসরি কল: {{ contactPhone || doctor.phone }}
          </a>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="serene-footer">
      <div class="wrap d-flex justify-content-between align-items-center flex-wrap">
        <div class="footer-left">
          <strong>{{ doctorDisplayName }}</strong> — {{ doctorSpecialtyEyebrow }}
          <div class="small-text">পেশাদার চিকিৎসা সেবা ও আন্তরিক যত্নের সমন্বয়।</div>
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
  name: 'TemplateSereneWellness',
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
.theme-serene-wellness {
  background-color: #FDFBF7;
  color: #2D3748;
  font-family: 'Hind Siliguri', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, serif;
  line-height: 1.7;
}

.wrap { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* Topbar */
.serene-topbar { background: #F7F3EB; border-bottom: 1px solid #EADDCD; padding: 8px 0; font-size: 0.88rem; color: #718096; }
.serene-phone { color: #B45309; font-weight: bold; text-decoration: none; }

/* Header */
.serene-header { position: sticky; top: 0; z-index: 100; background: rgba(253,251,247,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #EADDCD; padding: 14px 0; transition: all 0.3s; }
.serene-header.scrolled { box-shadow: 0 4px 20px rgba(180,83,9,0.06); }
.brand-link { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
.gold-flower { color: #B45309; font-size: 1.4rem; }
.brand-name { font-size: 1.15rem; font-weight: 800; color: #1A202C; display: block; line-height: 1.2; font-family: serif; }
.brand-sub { font-size: 0.78rem; color: #B45309; font-weight: 600; display: block; }
.nav-links { display: flex; gap: 20px; }
.nav-links a { color: #4A5568; text-decoration: none; font-size: 0.92rem; font-weight: 600; transition: color 0.2s; }
.nav-links a:hover { color: #B45309; }
.btn-gold { background: linear-gradient(135deg, #D97706 0%, #B45309 100%); color: #fff; font-weight: bold; padding: 10px 22px; border-radius: 99px; text-decoration: none; box-shadow: 0 4px 14px rgba(180,83,9,0.25); border: none; display: inline-flex; align-items: center; }
.btn-gold:hover { background: #B45309; color: #fff; }
.btn-outline-gold { background: transparent; border: 1.5px solid #D97706; color: #B45309; font-weight: bold; padding: 10px 22px; border-radius: 99px; text-decoration: none; display: inline-flex; align-items: center; }
.btn-outline-gold:hover { background: rgba(217,119,6,0.08); }
.burger { display: none; background: none; border: none; flex-direction: column; gap: 5px; cursor: pointer; }
.burger span { width: 24px; height: 2px; background: #2D3748; }
.mobile-menu { display: none; flex-direction: column; padding: 16px; background: #FDFBF7; border-bottom: 1px solid #EADDCD; gap: 10px; }
.mobile-menu.open { display: flex; }

/* Hero */
.editorial-hero { padding: 70px 0 90px; }
.hero-wrap { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 50px; align-items: center; }
.wellness-kicker { display: inline-flex; align-items: center; gap: 6px; background: #FEF3C7; color: #92400E; padding: 6px 14px; border-radius: 99px; font-size: 0.88rem; font-weight: 700; margin-bottom: 16px; }
.hero-title { font-size: 3rem; font-weight: 900; color: #1A202C; line-height: 1.2; margin-bottom: 16px; font-family: serif; }
.hero-quals-strip { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
.badge-gold { background: #FFFBEB; border: 1px solid #FDE68A; color: #B45309; padding: 4px 12px; border-radius: 99px; font-size: 0.85rem; font-weight: 600; }
.hero-hospital { font-size: 1.05rem; color: #4A5568; margin-bottom: 14px; }
.hero-bio-lead { font-size: 1.1rem; color: #4A5568; margin-bottom: 28px; line-height: 1.75; }
.hero-cta-group { display: flex; gap: 14px; flex-wrap: wrap; }
.arch-portrait-card { position: relative; display: flex; justify-content: center; }
.arch-frame { width: 340px; height: 440px; border-top-left-radius: 170px; border-top-right-radius: 170px; border-bottom-left-radius: 24px; border-bottom-right-radius: 24px; overflow: hidden; border: 4px solid #FDE68A; box-shadow: 0 20px 50px rgba(180,83,9,0.12); }
.arch-img { width: 100%; height: 100%; object-fit: cover; }
.gold-seal-badge { position: absolute; bottom: 20px; right: 10px; background: #FFFBEB; border: 1.5px solid #F59E0B; padding: 10px 16px; border-radius: 16px; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(180,83,9,0.15); }
.seal-sparkle { font-size: 20px; color: #D97706; }

/* Quote */
.philosophy-section { padding: 90px 0; background: #F7F3EB; }
.quote-card { max-width: 860px; margin: 0 auto 60px; text-align: center; position: relative; }
.quote-mark { font-size: 6rem; line-height: 1; color: #FDE68A; font-family: Georgia, serif; margin-bottom: -30px; }
.quote-text { font-size: 1.45rem; font-style: italic; color: #1A202C; line-height: 1.8; margin-bottom: 20px; font-family: serif; }
.quote-author strong { font-size: 1.15rem; color: #B45309; display: block; }
.quote-author span { font-size: 0.9rem; color: #718096; }
.pillars-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.pillar-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 20px; padding: 26px 20px; text-align: center; }
.pillar-icon { font-size: 30px; margin-bottom: 12px; }
.pillar-card h3 { font-size: 1.15rem; font-weight: 800; color: #1A202C; margin-bottom: 8px; font-family: serif; }
.pillar-card p { font-size: 0.88rem; color: #718096; margin: 0; }

/* Section Head */
.editorial-head { max-width: 720px; margin: 0 auto 48px; }
.sub-kicker { color: #B45309; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 8px; }
.editorial-head h2 { font-size: 2.3rem; font-weight: 800; color: #1A202C; margin-bottom: 10px; font-family: serif; }
.editorial-head p { color: #718096; font-size: 1rem; margin: 0; }

/* Treatments */
.treatments-section { padding: 90px 0; background: #FDFBF7; }
.treatments-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.treatment-pill-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 20px; padding: 26px; display: flex; gap: 16px; align-items: flex-start; transition: transform 0.2s, border-color 0.2s; }
.treatment-pill-card:hover { transform: translateY(-4px); border-color: #D97706; }
.treatment-num { width: 36px; height: 36px; border-radius: 50%; background: #FEF3C7; color: #B45309; font-weight: bold; display: flex; align-items: center; justify-content: center; flex: none; font-size: 0.88rem; }
.treatment-info h4 { font-size: 1.15rem; font-weight: 700; color: #1A202C; margin-bottom: 6px; font-family: serif; }
.treatment-info p { color: #718096; font-size: 0.9rem; margin: 0; }

/* Journey */
.journey-section { padding: 90px 0; background: #F7F3EB; }
.journey-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.journey-step-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 20px; padding: 28px 20px; text-align: center; position: relative; }
.journey-step-num { position: absolute; top: 12px; right: 14px; font-size: 0.8rem; font-weight: 800; color: #B45309; background: #FEF3C7; padding: 2px 8px; border-radius: 99px; }
.journey-icon { font-size: 32px; margin-bottom: 12px; }
.journey-step-card h4 { font-size: 1.15rem; font-weight: 800; color: #1A202C; margin-bottom: 8px; font-family: serif; }
.journey-step-card p { font-size: 0.88rem; color: #718096; margin: 0; }

/* Credentials */
.credentials-section { padding: 90px 0; background: #FDFBF7; }
.credentials-timeline { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; max-width: 920px; margin: 0 auto; }
.cred-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 18px; padding: 24px; }
.cred-year { display: inline-block; background: #FEF3C7; color: #92400E; font-size: 0.8rem; font-weight: bold; padding: 2px 10px; border-radius: 99px; margin-bottom: 8px; }
.cred-card h3 { font-size: 1.15rem; font-weight: 800; color: #1A202C; margin-bottom: 6px; font-family: serif; }
.cred-card p { font-size: 0.9rem; color: #718096; margin: 0; }

/* Chambers */
.wellness-chambers-section { padding: 90px 0; background: #F7F3EB; }
.wellness-chambers-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; max-width: 980px; margin: 0 auto; }
.wellness-chamber-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 24px; padding: 32px; position: relative; }
.chamber-flower-icon { font-size: 26px; margin-bottom: 10px; }
.chamber-num-badge { font-size: 0.75rem; font-weight: bold; color: #B45309; text-transform: uppercase; margin-bottom: 4px; }
.wellness-chamber-card h3 { font-size: 1.35rem; font-weight: 800; color: #1A202C; margin-bottom: 8px; font-family: serif; }
.ch-address { color: #718096; font-size: 0.92rem; margin-bottom: 14px; }
.ch-schedule-box { background: #FFFBEB; border: 1px solid #FDE68A; padding: 10px 14px; border-radius: 10px; font-size: 0.92rem; color: #92400E; margin-bottom: 10px; }
.ch-phone-line { font-size: 0.92rem; color: #4A5568; }
.ch-phone-line a { color: #B45309; font-weight: bold; }

/* Videos */
.videos-section { padding: 90px 0; background: #FDFBF7; }
.videos-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.video-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 20px; overflow: hidden; cursor: pointer; transition: transform 0.2s; }
.video-card:hover { transform: translateY(-5px); border-color: #D97706; }
.video-thumb-box { position: relative; width: 100%; height: 260px; background: #000; }
.video-img { width: 100%; height: 100%; object-fit: cover; }
.video-play-gold { position: absolute; inset: 0; background: rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #F59E0B; }
.video-duration-pill { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #fff; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; }
.video-meta { padding: 20px; }
.video-tag { background: #FEF3C7; color: #92400E; font-size: 0.75rem; font-weight: bold; padding: 2px 8px; border-radius: 99px; display: inline-block; margin-bottom: 6px; }
.video-meta h4 { font-size: 1.05rem; font-weight: 700; color: #1A202C; margin-bottom: 8px; font-family: serif; line-height: 1.4; }
.video-link { color: #B45309; font-weight: bold; font-size: 0.88rem; }

/* Lifestyle Tips */
.lifestyle-section { padding: 90px 0; background: #F7F3EB; }
.lifestyle-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
.lifestyle-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 20px; padding: 28px; cursor: pointer; transition: transform 0.2s; }
.lifestyle-card:hover { transform: translateY(-4px); border-color: #D97706; }
.tip-gold-tag { color: #B45309; font-weight: bold; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 8px; display: block; }
.lifestyle-card h4 { font-size: 1.25rem; font-weight: 800; color: #1A202C; margin-bottom: 8px; font-family: serif; line-height: 1.4; }
.lifestyle-card p { color: #718096; font-size: 0.92rem; margin-bottom: 14px; }
.read-more-gold { color: #B45309; font-weight: bold; font-size: 0.9rem; }

/* Client Stories */
.client-stories-section { padding: 90px 0; background: #FDFBF7; }
.stories-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.story-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 20px; padding: 28px; }
.stars-gold { color: #D97706; font-size: 1.1rem; margin-bottom: 12px; }
.story-comment { color: #4A5568; font-size: 0.95rem; font-style: italic; line-height: 1.6; margin-bottom: 20px; }
.story-author { display: flex; align-items: center; gap: 12px; }
.story-avatar { width: 40px; height: 40px; border-radius: 50%; background: #D97706; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; }

/* Gallery */
.gallery-section { padding: 90px 0; background: #F7F3EB; }
.gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
.gallery-arch-item { border-radius: 20px; overflow: hidden; height: 200px; border: 1px solid #EADDCD; }
.gallery-arch-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.gallery-arch-item:hover img { transform: scale(1.05); }

/* FAQ */
.faq-section { padding: 90px 0; background: #FDFBF7; }
.faq-serene-wrap { max-width: 840px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
.faq-serene-card { background: #FFFFFF; border: 1px solid #EADDCD; border-radius: 16px; padding: 20px 24px; cursor: pointer; }
.faq-serene-card.open { border-color: #D97706; }
.faq-serene-head { display: flex; justify-content: space-between; align-items: center; }
.faq-serene-head h4 { font-size: 1.1rem; font-weight: 700; color: #1A202C; margin: 0; font-family: serif; }
.faq-ico { font-size: 1.4rem; font-weight: bold; color: #B45309; }
.faq-serene-content { padding-top: 14px; margin-top: 12px; border-top: 1px solid #F7F3EB; color: #718096; font-size: 0.95rem; line-height: 1.6; }

/* Invitation Banner */
.invitation-banner { padding: 80px 0; background: #FEF3C7; border-top: 1px solid #FDE68A; border-bottom: 1px solid #FDE68A; }
.invitation-banner h2 { font-size: 2.2rem; font-weight: 800; color: #78350F; margin-bottom: 8px; font-family: serif; }
.invitation-banner p { font-size: 1.1rem; color: #92400E; margin: 0; }

/* Footer */
.serene-footer { background: #292524; color: #D6D3D1; padding: 40px 0; font-size: 0.95rem; }
.serene-footer strong { color: #fff; font-family: serif; }
.small-text { color: #A8A29E; font-size: 0.85rem; margin-top: 4px; }

/* Responsive */
@media (max-width: 992px) {
  .hero-wrap { grid-template-columns: 1fr; }
  .pillars-grid { grid-template-columns: repeat(2, 1fr); }
  .treatments-grid { grid-template-columns: 1fr; }
  .journey-grid { grid-template-columns: repeat(2, 1fr); }
  .credentials-timeline { grid-template-columns: 1fr; }
  .wellness-chambers-grid { grid-template-columns: 1fr; }
  .videos-grid { grid-template-columns: repeat(2, 1fr); }
  .lifestyle-grid { grid-template-columns: 1fr; }
  .stories-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: repeat(2, 1fr); }
  .nav-links { display: none; }
  .burger { display: flex; }
}
@media (max-width: 600px) {
  .pillars-grid { grid-template-columns: 1fr; }
  .journey-grid { grid-template-columns: 1fr; }
  .videos-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: 1fr; }
}
</style>
