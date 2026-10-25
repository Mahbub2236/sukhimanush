<template>
  <div class="doctor-portfolio-wrapper" :class="currentTemplateClass">
    <!-- Loading State -->
    <div v-if="loading" class="portfolio-loading-screen">
      <div class="custom-spinner"></div>
      <p>ডাক্তারের পোর্টফোলিও লোড হচ্ছে...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="!doctor" class="portfolio-error-screen">
      <div class="error-box">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="error-icon">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <h2>চিকিৎসক পাওয়া যায়নি</h2>
        <p>অনুগ্রহ করে লিংকটি পুনরায় পরীক্ষা করুন।</p>
        <router-link to="/doctors" class="btn btn-primary" style="margin-top: 16px;">
          সকল ডাক্তার দেখুন
        </router-link>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="portfolio-body">
      <!-- Active Template Component Render (1 of 5 Unique Designs) -->
      <component 
        :is="activeTemplateComponent"
        :doctor="doctor"
        :portfolio-data="portfolioData"
        :display-services="displayServices"
        :display-education="displayEducation"
        :display-tips="displayTips"
        :display-short-videos="displayShortVideos"
        :display-testimonials="displayTestimonials"
        :display-faqs="displayFaqs"
        :display-gallery="displayGallery"
        :display-chambers="displayChambers"
        :display-stats="displayStats"
        :display-process="displayProcess"
        :doctor-display-name="doctorDisplayName"
        :doctor-initial="doctorInitial"
        :doctor-specialty-eyebrow="doctorSpecialtyEyebrow"
        :doctor-department-text="doctorDepartmentText"
        :doctor-hospital-text="doctorHospitalText"
        :doctor-hero-photo="doctorHeroPhoto"
        :doctor-about-photo="doctorAboutPhoto"
        :doctor-stamp-title="doctorStampTitle"
        :doctor-stamp-sub="doctorStampSub"
        :qualification-badges="qualificationBadges"
        :about-heading="aboutHeading"
        :about-bio="aboutBio"
        :default-dynamic-bio="defaultDynamicBio"
        :contact-phone="contactPhone"
        :contact-assistant-phone="contactAssistantPhone"
        :contact-email="contactEmail"
        :contact-address="contactAddress"
        :contact-whatsapp="contactWhatsapp"
        :contact-social-links="contactSocialLinks"
        :format-bn-number="formatBnNumber"
        @open-tip="openTip"
        @open-video="openVideoModal"
        @share="showShareModal = true"
      />

      <!-- ================= TIP DETAIL MODAL ================= -->
      <div class="tip-modal-overlay" :class="{ 'open': selectedTip !== null }">
        <div class="tip-modal" role="dialog" aria-modal="true" v-if="selectedTip">
          <button class="tip-modal-close" aria-label="বন্ধ করুন" @click="closeTip">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
          <span class="tip-tag">{{ selectedTip.tag }}</span>
          <h3>{{ selectedTip.title }}</h3>
          <div class="tip-modal-content">
            <p v-for="(pText, pIdx) in selectedTip.body" :key="pIdx">{{ pText }}</p>
          </div>
        </div>
      </div>

      <!-- ================= SHARE & QR MODAL ================= -->
      <div class="portfolio-share-overlay" :class="{ 'open': showShareModal }">
        <div class="portfolio-share-modal" role="dialog" aria-modal="true">
          <button class="tip-modal-close" aria-label="বন্ধ করুন" @click="showShareModal = false">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
          
          <div class="share-modal-head">
            <div class="icon-badge-share">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
              </svg>
            </div>
            <h3>ডাক্তারের পোর্টফোলিও শেয়ার করুন</h3>
            <p>এই লিংক ও কিউআর কোড ব্যবহার করে যেকোনো স্থান থেকে রোগী সরাসরি ডাক্তারের প্রোফাইল দেখতে ও সিরিয়াল নিতে পারবেন।</p>
          </div>

          <!-- QR Code Display -->
          <div class="share-qr-wrap">
            <img :src="qrCodeUrl" alt="Doctor Portfolio QR Code" class="qr-image" />
            <span class="qr-caption">স্ক্যান করে সহজেই মোবাইলে খুলুন</span>
          </div>

          <!-- Copy Link Input -->
          <div class="share-input-row">
            <input type="text" readonly :value="portfolioShareUrl" class="share-link-input" ref="shareInput" />
            <button class="btn btn-primary btn-copy" @click="copyPortfolioLink">
              {{ copied ? 'কপি হয়েছে!' : 'লিংক কপি' }}
            </button>
          </div>

          <!-- Quick Social Share -->
          <div class="share-social-row">
            <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent((doctor.name || '') + ' - এর ডিজিটাল পোর্টফোলিও দেখুন: ' + portfolioShareUrl)" target="_blank" class="social-share-btn wa">
              হোয়াটসঅ্যাপে শেয়ার
            </a>
            <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(portfolioShareUrl)" target="_blank" class="social-share-btn fb">
              ফেসবুকে শেয়ার
            </a>
          </div>
        </div>
      </div>

      <!-- ================= SHORT VIDEO PLAYER MODAL ================= -->
      <div class="video-modal-overlay" :class="{ 'open': selectedVideo !== null }" @click.self="closeVideoModal">
        <div class="video-modal" role="dialog" aria-modal="true" v-if="selectedVideo">
          <button class="video-modal-close" aria-label="বন্ধ করুন" @click="closeVideoModal">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
          
          <div class="video-player-container">
            <video 
              v-if="isDirectVideo(selectedVideo.video_url)" 
              :src="selectedVideo.video_url" 
              controls 
              autoplay 
              playsinline
              class="direct-video-player"
            ></video>
            
            <iframe 
              v-else-if="getEmbedUrl(selectedVideo.video_url)" 
              :src="getEmbedUrl(selectedVideo.video_url)" 
              frameborder="0" 
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
              allowfullscreen 
              class="embed-video-player"
            ></iframe>

            <div v-else class="video-fallback-box">
              <p>এই ভিডিওটি সরাসরি প্লেয়ারে সাপোর্ট করছে না। নিচের লিংকে ক্লিক করে ভিডিওটি দেখুন:</p>
              <a :href="selectedVideo.video_url" target="_blank" class="btn btn-primary" style="margin-top: 12px; display: inline-flex;">
                ভিডিও ওপেন করুন ➜
              </a>
            </div>
          </div>

          <div class="video-modal-info">
            <div class="video-modal-meta">
              <span class="reel-tag" v-if="selectedVideo.tag">{{ selectedVideo.tag }}</span>
              <span class="video-modal-duration" v-if="selectedVideo.duration">{{ selectedVideo.duration }}</span>
            </div>
            <h3>{{ selectedVideo.title }}</h3>
            <p v-if="selectedVideo.description">{{ selectedVideo.description }}</p>
            <div class="video-modal-cta">
              <router-link :to="'/appoinment?doctor_id=' + doctor.id" class="btn btn-primary" @click="closeVideoModal">
                ডাক্তারের সাথে অ্যাপয়েন্টমেন্ট বুকিং ➜
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- ================= FLOATING TEMPLATE SWITCHER (INSTANT LIVE PREVIEW) ================= -->
      <div class="portfolio-theme-floating-switcher">
        <button 
          type="button"
          class="switcher-toggle-btn" 
          @click="showThemePicker = !showThemePicker" 
          title="টেমপ্লেট পরিবর্তন ও প্রিভিউ দেখুন"
        >
          <span class="icon">🎨</span>
          <span class="label">থিম:</span>
          <span class="theme-name">{{ currentTemplateInfo.bnName }}</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left: 2px;">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <div v-if="showThemePicker" class="switcher-dropdown">
          <div class="switcher-header">
            <div>
              <strong>পছন্দের টেমপ্লেট নির্বাচন করুন</strong>
              <div class="switcher-sub">যেকোনো একটি ক্লিক করে লাইভ লুক দেখুন</div>
            </div>
            <button type="button" class="switcher-close-btn" @click="showThemePicker = false">✕</button>
          </div>
          <div class="switcher-list">
            <button 
              type="button"
              v-for="tpl in availableTemplates" 
              :key="tpl.id"
              class="switcher-item"
              :class="{ 'active': activeTemplateId === tpl.id }"
              @click="selectTheme(tpl.id)"
            >
              <div class="d-flex align-items-center" style="gap: 10px;">
                <span 
                  class="color-dot" 
                  :style="{ backgroundColor: tpl.colors[1], border: '2px solid ' + tpl.colors[0] }"
                ></span>
                <div class="text-left">
                  <div class="item-title">{{ tpl.bnName }}</div>
                  <div class="item-badge">{{ tpl.badge }}</div>
                </div>
              </div>
              <span v-if="activeTemplateId === tpl.id" class="check">✓</span>
            </button>
          </div>
          <div class="switcher-footer">
            <span class="hint">💡 ডাক্তার তার ড্যাশবোর্ড থেকেও এটি স্থায়ীভাবে সংরক্ষণ করতে পারেন</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import TemplateDarkPulse from './portfolio/templates/TemplateDarkPulse.vue';
import TemplateClinicalWhite from './portfolio/templates/TemplateClinicalWhite.vue';
import TemplateSereneWellness from './portfolio/templates/TemplateSereneWellness.vue';
import TemplateExecutiveNavy from './portfolio/templates/TemplateExecutiveNavy.vue';
import TemplateDynamicLifeCare from './portfolio/templates/TemplateDynamicLifeCare.vue';

export default {
  name: 'DoctorPortfolio',
  components: {
    TemplateDarkPulse,
    TemplateClinicalWhite,
    TemplateSereneWellness,
    TemplateExecutiveNavy,
    TemplateDynamicLifeCare,
  },
  data() {
    return {
      loading: true,
      doctor: null,
      selectedTip: null,
      selectedVideo: null,
      showShareModal: false,
      copied: false,
      currentYear: new Date().getFullYear(),
      activeTemplateOverride: null,
      showThemePicker: false,
      availableTemplates: [
        {
          id: 'template_1_dark',
          name: 'Sukhi Manush Dark Pulse',
          bnName: '১. সুখী মানুষ ডার্ক পালস',
          badge: 'সাইবার ডার্ক লুক',
          colors: ['#090F0C', '#22C55E', '#06B6D4'],
          isDark: true
        },
        {
          id: 'template_2_clinical',
          name: 'Clinical Pure White',
          bnName: '২. ক্লিনিক্যাল পিওর হোয়াইট',
          badge: 'কুইক বুকিং ও স্ট্যাটস',
          colors: ['#F8FAFC', '#0284C7', '#2563EB'],
          isDark: false
        },
        {
          id: 'template_3_wellness',
          name: 'Serene Wellness',
          bnName: '৩. সেরিন ওয়েলনেস ও রোজ গোল্ড',
          badge: 'এডিটরিয়াল আর্চ লাক্সারি',
          colors: ['#FDFBF7', '#D97706', '#059669'],
          isDark: false
        },
        {
          id: 'template_4_executive',
          name: 'Executive Navy Authority',
          bnName: '৪. এক্সিকিউটিভ নেভি অথরিটি',
          badge: 'মেট্রিক ও অথরিটি',
          colors: ['#0B132B', '#38BDF8', '#818CF8'],
          isDark: true
        },
        {
          id: 'template_5_lifecare',
          name: 'Dynamic LifeCare',
          bnName: '৫. ডাইনামিক লাইফ-কেয়ার',
          badge: '২০২৬ মডার্ন বেন্টো-গ্রিড',
          colors: ['#FAF5FF', '#7C3AED', '#F43F5E'],
          isDark: false
        }
      ],
    };
  },
  computed: {
    activeTemplateId() {
      if (this.activeTemplateOverride) {
        return this.activeTemplateOverride;
      }
      if (this.$route.query.template) {
        return this.$route.query.template;
      }
      return this.portfolioData?.template || 'template_1_dark';
    },
    activeTemplateComponent() {
      const map = {
        template_1_dark: 'TemplateDarkPulse',
        template_2_clinical: 'TemplateClinicalWhite',
        template_3_wellness: 'TemplateSereneWellness',
        template_4_executive: 'TemplateExecutiveNavy',
        template_5_lifecare: 'TemplateDynamicLifeCare',
      };
      return map[this.activeTemplateId] || 'TemplateDarkPulse';
    },
    currentTemplateClass() {
      return 'theme-' + (this.activeTemplateId || 'template_1_dark');
    },
    currentTemplateInfo() {
      return this.availableTemplates.find(t => t.id === this.activeTemplateId) || this.availableTemplates[0];
    },
    portfolioData() {
      if (!this.doctor) return {};
      return this.doctor.portfolio_settings || this.doctor.portfolio_data || {};
    },
    doctorInitial() {
      if (!this.doctor || !this.doctor.name) return 'ড';
      const clean = this.doctor.name.replace(/^(সহকারী অধ্যাপক|অধ্যাপক|সহযোগী অধ্যাপক|ডা\.|ডাঃ|ড\.|Dr\.|Prof\.)\s*/gi, '').trim();
      return clean.charAt(0) || 'ড';
    },
    doctorDisplayName() {
      if (this.portfolioData?.hero?.title) {
        return this.portfolioData.hero.title;
      }
      if (!this.doctor || !this.doctor.name) return 'ডা. বিশেষজ্ঞ';
      return this.doctor.name;
    },
    doctorSpecialtyEyebrow() {
      if (this.portfolioData?.hero?.badge_text) {
        return this.portfolioData.hero.badge_text;
      }
      if (this.portfolioData?.hero?.eyebrow) {
        return this.portfolioData.hero.eyebrow;
      }
      if (this.doctor && this.doctor.specialization) {
        return `${this.doctor.specialization} বিশেষজ্ঞ`;
      }
      return 'বিশেষজ্ঞ চিকিৎসক';
    },
    doctorDepartmentText() {
      if (this.doctor && this.doctor.department) {
        return `(${this.doctor.department})`;
      }
      if (this.doctor && this.doctor.specialization) {
        return `(${this.doctor.specialization})`;
      }
      return '';
    },
    doctorHospitalText() {
      if (this.doctor && this.doctor.hospital_name) {
        return this.doctor.hospital_name;
      }
      return 'নভেনা স্পেশালাইজড হসপিটাল';
    },
    doctorHeroPhoto() {
      if (this.portfolioData?.hero?.photo) {
        return this.portfolioData.hero.photo;
      }
      if (this.portfolioData?.hero?.hero_photo) {
        return this.portfolioData.hero.hero_photo;
      }
      if (this.doctor?.avatar_url) {
        return this.doctor.avatar_url;
      }
      return '/images/team/default-doctor-male.svg';
    },
    doctorAboutPhoto() {
      if (this.portfolioData?.about?.photo) {
        return this.portfolioData.about.photo;
      }
      if (this.portfolioData?.about?.about_photo) {
        return this.portfolioData.about.about_photo;
      }
      return this.doctorHeroPhoto;
    },
    doctorStampTitle() {
      if (this.portfolioData?.hero?.stamp_title) {
        return this.portfolioData.hero.stamp_title;
      }
      if (this.doctor && this.doctor.experience_years) {
        return `${this.formatBnNumber(this.doctor.experience_years)}+ বছর`;
      }
      return '১৫+ বছর';
    },
    doctorStampSub() {
      if (this.portfolioData?.hero?.stamp_sub) {
        return this.portfolioData.hero.stamp_sub;
      }
      return 'অভিজ্ঞতা';
    },
    qualificationBadges() {
      if (this.portfolioData?.hero?.subtitle) {
        return this.portfolioData.hero.subtitle.split(',').map(s => s.trim()).filter(Boolean);
      }
      if (this.portfolioData?.hero?.badges && this.portfolioData.hero.badges.length > 0) {
        return this.portfolioData.hero.badges;
      }
      if (this.doctor && this.doctor.qualifications) {
        return this.doctor.qualifications.split(',').map(q => q.trim()).filter(Boolean);
      }
      return ['এমবিবিএস (ডিইউ)', 'এফসিপিএস (মেডিসিন)', 'এমডি'];
    },
    aboutHeading() {
      return this.portfolioData?.about?.heading || `${this.doctorDisplayName}-এর পরিচিতি ও চিকিৎসা দর্শন`;
    },
    aboutBio() {
      return this.portfolioData?.about?.bio || this.doctor?.bio || '';
    },
    defaultDynamicBio() {
      const exp = this.doctor?.experience_years ? `দীর্ঘ ${this.formatBnNumber(this.doctor.experience_years)}+ বছরেরও বেশি সময় ধরে ` : '';
      const spec = this.doctor?.specialization || 'বিশেষজ্ঞ চিকিৎসা';
      const hosp = this.doctor?.hospital_name || 'শীর্ষস্থানীয় হাসপাতালে';
      return `আমি ${this.doctorDisplayName}, ${exp}${spec} সেবায় নিয়োজিত। ${hosp} কর্মরত থেকে আধুনিক চিকিৎসা পদ্ধতি ও পরম যত্নে রোগীদের সুস্থতা নিশ্চিতে নিবেদিতপ্রাণ।`;
    },
    displayStats() {
      const exp = this.doctor?.experience_years ? this.formatBnNumber(this.doctor.experience_years) + '+' : '১৫+';
      return [
        { num: `${exp} বছর`, label: 'চিকিৎসা অভিজ্ঞতা ও সেবা', icon: '🩺' },
        { num: '১২,৫০০+', label: 'সফল রোগী ও সুস্থতা', icon: '❤️' },
        { num: '৯৯.২%', label: 'রোগীর সন্তুষ্টি ও আস্থা', icon: '⭐' },
        { num: '১০০%', label: 'বিএমডিসি অনুমোদিত প্রটোকল', icon: '🛡️' }
      ];
    },
    displayProcess() {
      return [
        {
          step: '০১',
          title: 'সহজ সিরিয়াল বুকিং',
          description: 'অনলাইনে অথবা সরাসরি ফোন কলের মাধ্যমে আপনার সুবিধাজনক সময়ে ডাক্তারের চেম্বার স্লট নির্ধারণ করুন।',
          icon: '📅'
        },
        {
          step: '০২',
          title: 'রোগের ইতিহাস ও রিপোর্ট পর্যালোচনা',
          description: 'চেম্বারে পূর্ববর্তী সকল মেডিকেল রিপোর্ট, প্রেসক্রিপশন ও লক্ষণসমূহ পুঙ্খানুপুঙ্খভাবে পর্যবেক্ষণ করা হয়।',
          icon: '🔬'
        },
        {
          step: '০৩',
          title: 'ব্যক্তিগত চিকিৎসা ও প্রেসক্রিপশন',
          description: 'আন্তর্জাতিক মেডিক্যাল গাইডলাইন অনুযায়ী সঠিক রোগ নির্ণয়পূর্বক কার্যকর ব্যবস্থাপত্র ও জীবনযাত্রা পরামর্শ।',
          icon: '💊'
        },
        {
          step: '০৪',
          title: 'সার্বক্ষণিক ফলোআপ কেয়ার',
          description: 'রোগীর দ্রুত সুস্থতা নিশ্চিতে নির্ধারিত সময় পর পর ফলোআপ চেকআপ ও টেলিমেডিসিন সহায়তা প্রদান।',
          icon: '🤝'
        }
      ];
    },
    displayServices() {
      if (this.portfolioData?.services && this.portfolioData.services.length > 0) {
        return this.portfolioData.services;
      }
      const spec = this.doctor?.specialization || 'বিশেষজ্ঞ চিকিৎসা';
      return [
        { title: `${spec} ও রোগ নির্ণয়`, description: 'রোগীর পূর্বের ইতিহাস ও সাম্প্রতিক লক্ষণাবলী বিশ্লেষণ করে সুনির্দিষ্ট রোগ নির্ণয় ও নিবিড় পরামর্শ।' },
        { title: 'উচ্চতর ডায়াগনস্টিক রিপোর্ট রিভিউ', description: 'ইকো, ইসিজি, রক্ত পরীক্ষা ও রেডিওলজি রিপোর্টের আধুনিক গাইডলাইন অনুযায়ী নির্ভুল পর্যালোচনা।' },
        { title: 'ক্রনিক ডিজিজ ও দীর্ঘমেয়াদী কেয়ার', description: 'ডায়াবেটিস, উচ্চ রক্তচাপ, এজমা ও জটিল দীর্ঘমেয়াদী রোগের আধুনিক নিয়ন্ত্রণ ও সঠিক ড্রাগ ম্যানেজমেন্ট।' },
        { title: 'জরুরি ও টেলিমেডিসিন সহায়তা', description: 'চেম্বারের পাশাপাশি প্রয়োজন সাপেক্ষে ভিডিও কলের মাধ্যমে জরুরি প্রেসক্রিপশন ও ফলোআপ গাইড।' },
        { title: 'প্রিভেন্টিভ হেলথ ও লাইফস্টাইল কেয়ার', description: 'অসুখ হওয়ার আগেই শরীরকে সুস্থ ও ফিট রাখতে বিজ্ঞানসম্মত খাদ্যাভ্যাস ও রুটিন চেকআপ পরিকল্পনা।' },
        { title: 'সার্জারি পূর্ববর্তী ও পরবর্তী কাউন্সেলিং', description: 'প্রয়োজনীয় ক্ষেত্রে বিশেষজ্ঞ রেফারেল এবং রিকভারি দ্রুততর করতে সার্বিক চিকিৎসা সহায়তা।' }
      ];
    },
    displayEducation() {
      if (this.portfolioData?.education && this.portfolioData.education.length > 0) {
        return this.portfolioData.education;
      }
      const quals = this.doctor?.qualifications ? this.doctor.qualifications.split(',').map(q => q.trim()).filter(Boolean) : [];
      if (quals.length > 0) {
        return quals.map((q, idx) => ({
          year: `মাইলফলক #${this.formatBnNumber(idx + 1)}`,
          institution: q,
          description: 'সফলভাবে উচ্চতর চিকিৎসা প্রশিক্ষণ ও পেশাদার কোর্স সম্পন্ন।'
        }));
      }
      return [
        { year: '২০০৮', institution: 'এমবিবিএস (MBBS)', description: 'শীর্ষস্থানীয় সরকারি মেডিকেল কলেজ থেকে প্রথম শ্রেণীতে স্নাতক ডিগ্রী অর্জন।' },
        { year: '২০১৪', institution: 'এফসিপিএস / এমডি (FCPS / MD)', description: 'বাংলাদেশ কলেজ অব ফিজিশিয়ান্স অ্যান্ড সার্জনস থেকে উচ্চতর ক্লিনিক্যাল ডিগ্রি।' },
        { year: '২০১৮', institution: 'ইন্টারন্যাশনাল ক্লিনিক্যাল ফেলোশিপ', description: 'বিদেশে আধুনিক ডায়াগনোসিস ও উন্নত সার্জিক্যাল পদ্ধতির উপর বিশেষ প্রশিক্ষণ।' },
        { year: '২০২২', institution: 'আন্তর্জাতিক গবেষণা ও সেমিনার', description: 'বিভিন্ন আন্তর্জাতিক মেডিকেল কনফারেন্সে নিয়মিত বৈজ্ঞানিক প্রবন্ধ উপস্থাপন ও প্রকাশনা।' }
      ];
    },
    displayTips() {
      if (this.portfolioData?.tips && this.portfolioData.tips.length > 0) {
        return this.portfolioData.tips.map(t => ({
          tag: t.tag || 'স্বাস্থ্য বার্তা',
          title: t.title || 'সুস্থ থাকার নিয়মাবলী',
          shortDesc: t.shortDesc || t.description || '',
          body: t.body ? (Array.isArray(t.body) ? t.body : [t.body]) : [t.shortDesc || t.description || '']
        }));
      }
      return [
        {
          tag: 'কার্ডিও ও রক্তচাপ',
          title: 'উচ্চ রক্তচাপ নিয়ন্ত্রণে রাখতে প্রতিদিনের ৫টি জরুরি স্বাস্থ্য অভ্যাস',
          shortDesc: 'লবণ গ্রহণ সীমিত রাখুন, প্রতিদিন অন্তত ৩০ মিনিট হাঁটুন এবং নিয়মিত প্রেসার মেপে চার্ট রাখুন।',
          body: [
            '১. খাবারে অতিরিক্ত কাঁচা লবণ বর্জন করুন এবং প্রক্রিয়াজাত খাদ্য এড়িয়ে চলুন।',
            '২. প্রতিদিন সকালে বা বিকালে অন্তত ৩০ মিনিট দ্রুত হাঁটার অভ্যাস গড়ে তুলুন।',
            '৩. সপ্তাহে অন্তত দুই দিন রক্তচাপ মেপে একটি ডায়েরিতে লিখে রাখুন।',
            '৪. ডাক্তারের পরামর্শ ব্যতীত হঠাৎ প্রেসারের ওষুধ বন্ধ বা ডোজ পরিবর্তন করবেন না।'
          ]
        },
        {
          tag: 'ডায়াবেটিস নিয়ন্ত্রণ',
          title: 'ডায়াবেটিসের জটিলতা এড়িয়ে সম্পূর্ণ সুস্থ ও কর্মক্ষম থাকার উপায়',
          shortDesc: 'সুষম খাদ্য তালিকা, রক্তে গ্লুকোজের নিয়মিত পরীক্ষা এবং সঠিক সময়ে ঔষধ গ্রহণ অত্যন্ত জরুরি।',
          body: [
            '১. অতিরিক্ত শর্করা ও মিষ্টি জাতীয় খাবার নিয়ন্ত্রণ করুন।',
            '২. তিন মাস পর পর HbA1c টেস্ট করে গড়ে রক্তে চিনির মাত্রা পর্যবেক্ষণ করুন।',
            '৩. পায়ের যত্ন নিন এবং কোনো ছোট ক্ষত হলেও অবহেলা না করে চিকিৎসকের শরণাপন্ন হন।'
          ]
        },
        {
          tag: 'ঋতুভিত্তিক স্বাস্থ্য',
          title: 'ঋতু পরিবর্তনের সময় সাধারণ ঠান্ডা-কাশি ও ভাইরাল সংক্রমণ থেকে সতর্কতা',
          shortDesc: 'হালকা গরম পানি পান করুন, ধুলাবালি থেকে বাঁচতে মাস্ক ব্যবহার করুন এবং পুষ্টিকর তরল খাবার খান।',
          body: [
            '১. কুসুম গরম পানি ও আদা-লেবু চা গলা খুসখুস কমাতে দারুণ কার্যকর।',
            '২. জ্বর বা কাশির শুরুতে অপ্রয়োজনে নিজের ইচ্ছায় অ্যান্টিবায়োটিক খাবেন না।',
            '৩. শিশুদের বিশেষ যত্ন নিন এবং শরীর উষ্ণ রাখতে সুতি পোশাক ব্যবহার করুন।'
          ]
        },
        {
          tag: 'ঔষধ সতর্কতা',
          title: 'চিকিৎসকের প্রেসক্রিপশন ছাড়া অতিরিক্ত ব্যথানাশক সেবনের মারাত্মক ক্ষতি',
          shortDesc: 'অপ্রয়োজনীয় পেইনকিলার কিডনি ও লিভারের স্থায়ী ক্ষতি করতে পারে। তাই সর্বদা সতর্ক থাকুন।',
          body: [
            '১. সামান্য মাথা বা কোমর ব্যথায় তাৎক্ষণিক তীব্র ব্যথানাশক ওষুধ খাওয়া বন্ধ করুন।',
            '২. গ্যাস্ট্রিক ও কিডনির সুরক্ষায় চিকিৎসকের সঠিক ব্যবস্থাপত্র মেনে ওষুধ খান।'
          ]
        }
      ];
    },
    displayShortVideos() {
      if (this.portfolioData?.short_videos && this.portfolioData.short_videos.length > 0) {
        return this.portfolioData.short_videos;
      }
      return [
        {
          title: 'বুকের তীব্র ব্যথা বা শ্বাসকষ্ট হলে কখন দ্রুত হাসপাতালে যাবেন?',
          description: 'লক্ষণ চিনে সঠিক সময়ে চিকিৎসা গ্রহণ জীবন বাঁচাতে পারে। বিস্তারিত জানুন এই ভিডিওতে।',
          tag: 'জরুরি কার্ডিয়াক বার্তা',
          duration: '০২:১৫ মিনিট',
          thumbnail_url: '/images/team/1.jpg',
          video_url: 'https://www.youtube.com/watch?v=ScMzIvxBSi4'
        },
        {
          title: 'উচ্চ রক্তচাপ কি স্থায়ীভাবে নিয়ন্ত্রণ করা সম্ভব? জানুন বিশেষজ্ঞের পরামর্শ',
          description: 'ওষুধের পাশাপাশি জীবনযাত্রার ছোট পরিবর্তনে প্রেসার নিয়ন্ত্রণে রাখার বৈজ্ঞানিক উপায়।',
          tag: 'লাইফস্টাইল গাইড',
          duration: '০১:৫০ মিনিট',
          thumbnail_url: '/images/team/2.jpg',
          video_url: 'https://www.youtube.com/watch?v=ScMzIvxBSi4'
        },
        {
          title: 'অ্যান্টিবায়োটিকের অপব্যবহার: মানবদেহে কেন এটি বিপদ ডেকে আনছে?',
          description: 'কখন অ্যান্টিবায়োটিক দরকার এবং কেন পুরো কোর্স শেষ করা বাধ্যতামূলক।',
          tag: 'জনসচেতনতা',
          duration: '০২:০৫ মিনিট',
          thumbnail_url: '/images/team/3.jpg',
          video_url: 'https://www.youtube.com/watch?v=ScMzIvxBSi4'
        }
      ];
    },
    displayTestimonials() {
      if (this.portfolioData?.testimonials && this.portfolioData.testimonials.length > 0) {
        return this.portfolioData.testimonials;
      }
      return [
        {
          author_name: 'মুহাম্মদ রফিকুল ইসলাম',
          author_location: 'ধানমন্ডি, ঢাকা',
          relation: 'রোগী',
          rating: 5,
          comment: 'ডাক্তার সাহেবের চিকিৎসায় আমার দীর্ঘদিনের পেটের সমস্যা সম্পূর্ণ নিরাময় হয়েছে। উনি রোগীকে খুব মনোযোগ দিয়ে শোনেন এবং অহেতুক টেস্ট দেন না। ওনার ব্যবহার সত্যিই প্রশংসনীয়।'
        },
        {
          author_name: 'বেগম নাসরিন সুলতানা',
          author_location: 'পাঁচলাইশ, চট্টগ্রাম',
          relation: 'রোগীর মেয়ে',
          rating: 5,
          comment: 'আমার মায়ের হার্টের সমস্যার সময় স্যারের দ্রুত ও সঠিক সিদ্ধান্তে মা আজ সম্পূর্ণ সুস্থ। ওনার নিয়মিত ফলোআপ ও সহানুভূতিশীল মনোভাব আমাদের পুরো পরিবারকে স্বস্তি দিয়েছে।'
        },
        {
          author_name: 'তানভীর আহমেদ চৌধুরী',
          author_location: 'সিলেট সদর',
          relation: 'রোগী',
          rating: 5,
          comment: 'উচ্চ রক্তচাপ ও ডায়াবেটিস নিয়ন্ত্রণে স্যারের পরামর্শ আমার লাইফস্টাইল সম্পূর্ণ বদলে দিয়েছে। এখন কোনো অস্বস্তি ছাড়াই স্বাভাবিক কর্মচঞ্চল জীবন উপভোগ করছি।'
        }
      ];
    },
    displayFaqs() {
      if (this.portfolioData?.faqs && this.portfolioData.faqs.length > 0) {
        return this.portfolioData.faqs;
      }
      return [
        {
          question: 'চেম্বারে ডাক্তারের অগ্রিম সিরিয়াল কীভাবে নিশ্চিত করব?',
          answer: 'আপনি এই পোর্টফোলিও থেকেই সরাসরি "সিরিয়াল নিন" বাটনে ক্লিক করে তারিখ ও চেম্বার নির্বাচন করতে পারেন অথবা চেম্বারের হেল্পলাইন নম্বরে সরাসরি ফোন দিয়ে তাৎক্ষণিক সিরিয়াল বুকিং কনফার্ম করতে পারেন।'
        },
        {
          question: 'পূর্বে করা ডায়াগনস্টিক রিপোর্ট কি সাথে আনতে হবে?',
          answer: 'হ্যাঁ, রোগ নির্ণয় নির্ভুল ও দ্রুততর করতে পূর্ববর্তী সকল প্রেসক্রিপশন, সাম্প্রতিক রক্ত পরীক্ষা, এক্স-রে, সিটি স্ক্যান বা ইসিজি রিপোর্টের মূল কপি সাথে নিয়ে আসা অত্যন্ত জরুরি।'
        },
        {
          question: 'দূরবর্তী বা প্রবাসী রোগীদের জন্য কি অনলাইন ভিডিও কনসালটেশনের ব্যবস্থা আছে?',
          answer: 'হ্যাঁ, ঢাকার বাইরের রোগী ও প্রবাসীদের সুবিধার্থে নির্দিষ্ট স্লটে ভিডিও কনসালটেশনের মাধ্যমে স্বাস্থ্য পরামর্শ ও ডিজিটাল ই-প্রেসক্রিপশন প্রদান করা হয়।'
        },
        {
          question: 'নির্ধারিত সিরিয়ালের কতক্ষণ আগে চেম্বারে উপস্থিত থাকা উচিত?',
          answer: 'ভিড় ও অনাকাঙ্ক্ষিত বিলম্ব এড়াতে আপনার নির্ধারিত সিরিয়াল সময়ের অন্তত ১৫ থেকে ২০ মিনিট পূর্বে চেম্বারে উপস্থিত থাকার অনুরোধ করা হচ্ছে, যাতে প্রাথমিক ভাইটালস (ব্লাড প্রেশার, পালস, ওজন) রেকর্ড করা যায়।'
        },
        {
          question: 'প্রথম সাক্ষাতের কতদিনের মধ্যে রিপোর্ট দেখালে ফি লাগবে না?',
          answer: 'সাধারণত প্রথম সাক্ষাতের পর ডাক্তারের নির্দেশিত ডায়াগনস্টিক রিপোর্ট ৭ থেকে ১৪ দিনের মধ্যে চেম্বারে দেখালে বাড়তি ভিজিট ফি নেওয়া হয় না (চেম্বারের অভ্যন্তরীণ নিয়ম প্রযোজ্য)।'
        }
      ];
    },
    displayGallery() {
      if (this.portfolioData?.gallery && this.portfolioData.gallery.length > 0) {
        return this.portfolioData.gallery.map(g => typeof g === 'string' ? g : (g.image_url || g.url || ''));
      }
      return [
        '/images/service/service-1.jpg',
        '/images/service/service-2.jpg',
        '/images/service/service-3.jpg',
        '/images/service/service-4.jpg'
      ];
    },
    contactPhone() {
      return this.portfolioData?.contact?.phone || this.portfolioData?.contact?.emergency_phone || this.doctor?.phone || '';
    },
    contactAssistantPhone() {
      return this.portfolioData?.contact?.assistant_phone || this.doctor?.phone || '';
    },
    contactEmail() {
      return this.portfolioData?.contact?.email || this.doctor?.email || '';
    },
    contactAddress() {
      return this.portfolioData?.contact?.chamber_address || this.portfolioData?.contact?.home_address || this.doctor?.chamber_address || (this.doctor?.city ? `${this.doctor.city}, বাংলাদেশ` : '');
    },
    contactWhatsapp() {
      return this.portfolioData?.contact?.whatsapp || this.doctor?.phone || '';
    },
    contactSocialLinks() {
      return this.portfolioData?.contact || {};
    },
    displayChambers() {
      if (this.doctor && this.doctor.chambers && this.doctor.chambers.length > 0) {
        return this.doctor.chambers.map(ch => ({
          id: ch.id,
          name: ch.chamber_name || ch.name || ch.hospital_name || 'চেম্বার',
          address: ch.address || ch.location || this.doctor.chamber_address || '',
          schedule: ch.schedule || (ch.visiting_days ? `${Array.isArray(ch.visiting_days) ? ch.visiting_days.join(', ') : ch.visiting_days} (${ch.start_time || ''} - ${ch.end_time || ''})` : 'সরাসরি যোগাযোগের জন্য কল করুন'),
          phone: ch.phone || this.doctor.phone || ''
        }));
      }
      if (this.doctor && this.doctor.chamber_address) {
        return [
          {
            name: this.doctor.name ? `${this.doctor.name}-এর প্রধান কনসালটেশন চেম্বার` : 'প্রধান কনসালটেশন চেম্বার',
            address: this.doctor.chamber_address,
            schedule: this.doctor.available_days ? `${Array.isArray(this.doctor.available_days) ? this.doctor.available_days.join(', ') : this.doctor.available_days} (${this.doctor.start_time || ''} - ${this.doctor.end_time || ''})` : 'শনি থেকে বৃহস্পতি (বিকাল ৫:০০ - রাত ৯:০০)',
            phone: this.doctor.phone || ''
          },
          {
            name: this.doctor.hospital_name ? `${this.doctor.hospital_name} (বহিঃবিভাগ)` : 'স্পেশালাইজড ডায়াগনস্টিক ও চেম্বার সেন্টার',
            address: this.doctor.city ? `${this.doctor.city}, বাংলাদেশ` : 'প্রধান সড়ক, সেন্ট্রাল পয়েন্ট',
            schedule: 'রবি ও মঙ্গল (সকাল ১০:০০ - দুপুর ১:০০)',
            phone: this.doctor.phone || ''
          }
        ];
      }
      return [
        {
          name: this.doctor.name ? `${this.doctor.name}-এর প্রধান কনসালটেশন চেম্বার` : 'প্রধান কনসালটেশন চেম্বার',
          address: this.doctor.city ? `সেন্ট্রাল মেডিকেল টাওয়ার, ${this.doctor.city}` : 'সেন্ট্রাল মেডিকেল টাওয়ার, ঢাকা',
          schedule: 'শনি থেকে বৃহস্পতি (বিকাল ৫:০০ - রাত ৯:০০)',
          phone: this.doctor.phone || '০১৭১১-০০০০০০'
        },
        {
          name: 'ইভনিং স্পেশালাইজড কেয়ার ক্লিনিক',
          address: 'গ্রীন রোড মোড়, ঢাকা',
          schedule: 'সোম ও বুধ (সন্ধ্যা ৬:০০ - রাত ৯:৩০)',
          phone: this.doctor.phone || '০১৮১১-০০০০০০'
        }
      ];
    },
    portfolioShareUrl() {
      if (typeof window !== 'undefined') {
        return window.location.href;
      }
      return '';
    },
    qrCodeUrl() {
      return `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(this.portfolioShareUrl)}`;
    }
  },
  created() {
    this.fetchDoctor();
  },
  methods: {
    selectTheme(id) {
      this.activeTemplateOverride = id;
      this.showThemePicker = false;
      try {
        const query = { ...this.$route.query, template: id };
        this.$router.replace({ query }).catch(() => {});
      } catch (e) {}
    },
    openTip(tip) {
      this.selectedTip = tip;
      document.body.style.overflow = 'hidden';
    },
    closeTip() {
      this.selectedTip = null;
      document.body.style.overflow = '';
    },
    openVideoModal(video) {
      this.selectedVideo = video;
      document.body.style.overflow = 'hidden';
    },
    closeVideoModal() {
      this.selectedVideo = null;
      document.body.style.overflow = '';
    },
    getEmbedUrl(url) {
      if (!url) return '';
      const shortsMatch = url.match(/shorts\/([a-zA-Z0-9_-]+)/);
      if (shortsMatch && shortsMatch[1]) {
        return `https://www.youtube.com/embed/${shortsMatch[1]}?autoplay=1&rel=0`;
      }
      const ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?(?:.*&)?v=|(?:embed|v)\/))([a-zA-Z0-9_-]+)/);
      if (ytMatch && ytMatch[1]) {
        return `https://www.youtube.com/embed/${ytMatch[1]}?autoplay=1&rel=0`;
      }
      return '';
    },
    isDirectVideo(url) {
      if (!url) return false;
      return /\.(mp4|webm|mov|ogg)($|\?)/i.test(url) || url.includes('/uploads/portfolio/videos/');
    },
    copyPortfolioLink() {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(this.portfolioShareUrl).then(() => {
          this.copied = true;
          setTimeout(() => { this.copied = false; }, 2500);
        });
      } else {
        const input = this.$refs.shareInput;
        if (input) {
          input.select();
          document.execCommand('copy');
          this.copied = true;
          setTimeout(() => { this.copied = false; }, 2500);
        }
      }
    },
    formatBnNumber(num) {
      if (!num) return '';
      const bnDigits = {'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯'};
      return String(num).replace(/[0-9]/g, d => bnDigits[d] || d);
    },
    async fetchDoctor() {
      this.loading = true;
      const slugOrId = this.$route.params.slug;
      if (!slugOrId) {
        this.loading = false;
        return;
      }

      try {
        const res = await axios.get(`/doctors/${encodeURIComponent(slugOrId)}`);
        if (res.data && res.data.doctor) {
          this.doctor = res.data.doctor;
        } else if (res.data && res.data.data) {
          this.doctor = res.data.data;
        } else {
          this.doctor = res.data;
        }
      } catch (err) {
        try {
          const allRes = await axios.get('/doctors');
          const docs = allRes.data.doctors || allRes.data.data || allRes.data || [];
          const matched = docs.find(d => 
            String(d.slug || '').toLowerCase() === String(slugOrId).toLowerCase() ||
            String(d.id) === String(slugOrId) ||
            String(d.name || '').toLowerCase().includes(String(slugOrId).toLowerCase().replace(/[-_]/g, ' '))
          );
          this.doctor = matched || null;
        } catch (e2) {
          this.doctor = null;
        }
      } finally {
        this.loading = false;
        if (this.doctor && this.doctor.name) {
          document.title = `${this.doctor.name} | ${this.doctor.specialization || 'বিশেষজ্ঞ চিকিৎসক'}`;
        }
      }
    }
  },
  watch: {
    '$route.params.slug': {
      handler(newSlug) {
        if (newSlug) {
          this.fetchDoctor();
        }
      },
      immediate: false
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');

.doctor-portfolio-wrapper {
  position: relative;
  min-height: 100vh;
  font-family: 'Hind Siliguri', sans-serif;
  overflow-x: hidden;
}

/* Loading & Error Screens */
.portfolio-loading-screen, .portfolio-error-screen {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  text-align: center;
  background: #090F0C;
  color: #ECF5F1;
}
.custom-spinner {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(255,255,255,0.1);
  border-top-color: #22C55E;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 20px;
}
@keyframes spin { to { transform: rotate(360deg); } }

.error-box {
  background: #17241E;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 20px;
  padding: 40px 30px;
  max-width: 460px;
}
.error-icon { color: #EF4444; margin-bottom: 14px; }

/* Tip Modal */
.tip-modal-overlay {
  position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center;
  padding: 24px; z-index: 2000; opacity: 0; pointer-events: none; transition: opacity 0.3s;
}
.tip-modal-overlay.open { opacity: 1; pointer-events: auto; }
.tip-modal {
  background: #FFFFFF; border-radius: 24px; max-width: 540px; width: 100%;
  max-height: 85vh; overflow-y: auto; padding: 36px; position: relative; color: #1E293B;
  box-shadow: 0 25px 60px rgba(0,0,0,0.4);
}
.tip-modal-close {
  position: absolute; top: 18px; right: 18px; width: 34px; height: 34px; border-radius: 50%;
  background: #F1F5F9; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
}
.tip-modal-close:hover { background: #EF4444; color: #fff; }
.tip-tag { background: #FEF3C7; color: #92400E; font-size: 0.82rem; font-weight: bold; padding: 4px 12px; border-radius: 99px; display: inline-block; margin-bottom: 12px; }
.tip-modal h3 { font-family: 'Tiro Bangla', serif; font-size: 1.45rem; margin-bottom: 14px; color: #0F172A; }
.tip-modal p { font-size: 1rem; color: #475569; line-height: 1.8; margin-bottom: 12px; }

/* Share Modal */
.portfolio-share-overlay {
  position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center;
  padding: 24px; z-index: 2000; opacity: 0; pointer-events: none; transition: opacity 0.3s;
}
.portfolio-share-overlay.open { opacity: 1; pointer-events: auto; }
.portfolio-share-modal {
  background: #FFFFFF; border-radius: 24px; max-width: 480px; width: 100%;
  padding: 34px; position: relative; color: #1E293B; text-align: center;
  box-shadow: 0 25px 60px rgba(0,0,0,0.4);
}
.icon-badge-share {
  width: 54px; height: 54px; border-radius: 50%; background: #E0F2FE; color: #0284C7;
  display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
}
.share-modal-head h3 { font-family: 'Tiro Bangla', serif; font-size: 1.4rem; margin-bottom: 8px; color: #0F172A; }
.share-modal-head p { font-size: 0.92rem; color: #64748B; margin-bottom: 20px; }
.share-qr-wrap { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 16px; padding: 18px; display: inline-block; margin-bottom: 20px; }
.qr-image { width: 180px; height: 180px; display: block; margin: 0 auto; }
.qr-caption { font-size: 0.8rem; color: #64748B; margin-top: 8px; display: block; }
.share-input-row { display: flex; gap: 8px; margin-bottom: 18px; }
.share-link-input { flex: 1; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 12px; font-size: 0.85rem; background: #F8FAFC; }
.btn-copy { background: #0284C7; color: #fff; border: none; border-radius: 12px; padding: 10px 16px; font-weight: bold; cursor: pointer; }
.share-social-row { display: flex; gap: 10px; }
.social-share-btn { flex: 1; padding: 10px; border-radius: 12px; font-weight: bold; font-size: 0.88rem; text-decoration: none; display: block; }
.social-share-btn.wa { background: #22C55E; color: #000; }
.social-share-btn.fb { background: #1877F2; color: #fff; }

/* Video Reel Modal */
.video-modal-overlay {
  position: fixed; inset: 0; background: rgba(0, 0, 0, 0.88);
  backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center;
  padding: 20px; z-index: 2000; opacity: 0; pointer-events: none; transition: opacity 0.3s;
}
.video-modal-overlay.open { opacity: 1; pointer-events: auto; }
.video-modal {
  background: #0B132B; border: 1px solid rgba(255,255,255,0.15); border-radius: 24px;
  max-width: 820px; width: 100%; max-height: 90vh; overflow: hidden; display: grid;
  grid-template-columns: 1fr 1fr; position: relative; color: #fff; box-shadow: 0 30px 70px rgba(0,0,0,0.6);
}
.video-modal-close {
  position: absolute; top: 14px; right: 14px; width: 34px; height: 34px; border-radius: 50%;
  background: rgba(255,255,255,0.15); border: none; color: #fff; cursor: pointer;
  display: flex; align-items: center; justify-content: center; z-index: 10;
}
.video-player-container { position: relative; aspect-ratio: 9/16; background: #000; display: flex; align-items: center; justify-content: center; }
.direct-video-player, .embed-video-player { width: 100%; height: 100%; border: none; }
.video-fallback-box { padding: 20px; text-align: center; }
.video-modal-info { padding: 32px; display: flex; flex-direction: column; justify-content: space-between; }
.video-modal-meta { display: flex; gap: 8px; margin-bottom: 12px; }
.reel-tag { background: #22C55E; color: #000; font-size: 0.75rem; font-weight: bold; padding: 2px 8px; border-radius: 99px; }
.video-modal-duration { background: rgba(255,255,255,0.1); color: #fff; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; }
.video-modal-info h3 { font-family: 'Tiro Bangla', serif; font-size: 1.35rem; margin-bottom: 12px; color: #fff; }
.video-modal-info p { color: #94A3B8; font-size: 0.92rem; line-height: 1.6; }
.video-modal-cta .btn-primary { background: #22C55E; color: #000; font-weight: bold; padding: 12px; border-radius: 99px; width: 100%; display: block; text-align: center; text-decoration: none; }

/* Floating Switcher */
.portfolio-theme-floating-switcher {
  position: fixed; bottom: 24px; right: 24px; z-index: 1500; font-family: 'Hind Siliguri', sans-serif;
}
.switcher-toggle-btn {
  background: #0F172A; color: #fff; border: 1.5px solid #38BDF8; padding: 10px 18px;
  border-radius: 99px; display: inline-flex; align-items: center; gap: 8px;
  box-shadow: 0 10px 28px rgba(0,0,0,0.35); cursor: pointer; transition: all 0.25s; font-size: 0.9rem; font-weight: 600;
}
.switcher-toggle-btn:hover { transform: translateY(-3px); box-shadow: 0 14px 34px rgba(0,0,0,0.45); }
.switcher-toggle-btn .theme-name { color: #38BDF8; font-weight: bold; }
.switcher-dropdown {
  position: absolute; bottom: calc(100% + 12px); right: 0; width: 330px;
  background: #0F172A; border: 1.5px solid rgba(255,255,255,0.15); border-radius: 20px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.5); overflow: hidden; color: #fff;
}
.switcher-header { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: flex-start; }
.switcher-header strong { font-size: 0.92rem; display: block; }
.switcher-sub { font-size: 0.75rem; color: #94A3B8; }
.switcher-close-btn { background: none; border: none; color: #94A3B8; cursor: pointer; font-size: 1rem; }
.switcher-list { padding: 8px; display: flex; flex-direction: column; gap: 4px; }
.switcher-item {
  display: flex; align-items: center; justify-content: space-between; padding: 10px 12px;
  border-radius: 12px; background: transparent; border: 1px solid transparent; cursor: pointer; width: 100%; color: #fff;
}
.switcher-item:hover { background: rgba(255,255,255,0.06); }
.switcher-item.active { background: rgba(56,189,248,0.15); border-color: #38BDF8; }
.switcher-item .color-dot { width: 18px; height: 18px; border-radius: 50%; flex: none; }
.switcher-item .item-title { font-size: 0.88rem; font-weight: 600; text-align: left; }
.switcher-item .item-badge { font-size: 0.72rem; color: #94A3B8; text-align: left; }
.switcher-item .check { color: #38BDF8; font-weight: bold; }
.switcher-footer { padding: 10px 14px; border-top: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); font-size: 0.75rem; color: #94A3B8; text-align: center; }

@media (max-width: 768px) {
  .video-modal { grid-template-columns: 1fr; }
  .video-modal-info { padding: 20px; }
}
</style>
