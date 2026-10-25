<template>
  <div class="emergency-pass-page min-vh-100 bg-light py-4 py-md-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-danger" role="status"></div>
            <p class="text-muted mt-2 font-weight-bold">জরুরি মেডিকেল প্রোফাইল ডিক্রিপ্ট হচ্ছে...</p>
          </div>

          <!-- Error / Invalid Token -->
          <div v-else-if="!profile" class="card border-0 shadow-sm p-4 text-center bg-white rounded-lg">
            <i class="icofont-warning-alt display-3 text-danger mb-3"></i>
            <h4 class="font-weight-bold text-dark">অবৈধ বা মেয়াদোত্তীর্ণ কার্ড</h4>
            <p class="text-muted small">এই জরুরি হেলথ কার্ডের কিউআর কোডটি সঠিক নয় অথবা অ্যাক্সেস প্রত্যাহার করা হয়েছে।</p>
            <router-link to="/" class="btn btn-main-2 btn-round-full mt-2">হোমে ফিরে যান</router-link>
          </div>

          <!-- Active Emergency Health Passport Profile -->
          <div v-else class="card border-0 shadow-lg rounded-lg overflow-hidden bg-white">
            <!-- Red Emergency Header -->
            <div class="p-3 p-md-4 text-white text-center position-relative" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);">
              <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mb-2">
                🚨 EMERGENCY MEDICAL PASSPORT
              </span>
              <h4 class="text-white font-weight-bold mb-0">জরুরি স্বাস্থ্য পরিচয়পত্র</h4>
              <small class="text-white-50">প্রথম-প্রতিক্রিয়াশীল ও জরুরি চিকিৎসকের জন্য</small>
            </div>

            <!-- Profile Info Body -->
            <div class="p-4">
              <!-- Avatar & Blood Group Highlight -->
              <div class="text-center mb-4">
                <div class="position-relative d-inline-block">
                  <img 
                    :src="profile.avatar" 
                    :alt="profile.name" 
                    class="rounded-circle shadow border" 
                    style="width: 100px; height: 100px; object-fit: cover; border-width: 3px !important; border-color: #dc2626 !important;"
                  >
                  <div class="blood-pill position-absolute shadow">
                    {{ profile.blood_group }}
                  </div>
                </div>
                <h4 class="font-weight-bold text-dark mt-3 mb-1">{{ profile.name }}</h4>
                <span class="badge badge-success px-3 py-1">
                  <i class="icofont-check-circled"></i> ভেরিফাইড হেলথ রেকর্ড
                </span>
              </div>

              <!-- Critical Vitals Box -->
              <div class="bg-light p-3 rounded-lg mb-3 border">
                <div class="mb-3 border-bottom pb-2">
                  <small class="text-muted d-block">রক্তের গ্রুপ (Blood Group):</small>
                  <strong class="font-20 text-danger">{{ profile.blood_group }}</strong>
                </div>

                <div class="mb-3 border-bottom pb-2">
                  <small class="text-muted d-block">জরুরি যোগাযোগের ফোন নম্বর (Emergency Contact):</small>
                  <div class="d-flex justify-content-between align-items-center mt-1">
                    <strong class="font-18 text-dark">{{ profile.emergency_contact_phone }}</strong>
                    <a :href="'tel:' + profile.emergency_contact_phone" class="btn btn-sm btn-danger font-weight-bold rounded-pill px-3">
                      <i class="icofont-phone mr-1"></i> সরাসরি কল দিন
                    </a>
                  </div>
                </div>

                <div>
                  <small class="text-muted d-block">অ্যালার্জি ও জরুরি মেডিকেল নোট (Allergies & Conditions):</small>
                  <div class="p-2 bg-white rounded border mt-1 text-danger font-weight-bold small">
                    <i class="icofont-exclamation-tringle mr-1"></i>
                    {{ profile.emergency_medical_notes }}
                  </div>
                </div>
              </div>

              <!-- Privacy Notice -->
              <div class="alert alert-secondary p-2 small text-muted text-center mb-0" style="font-size: 11px;">
                <i class="icofont-shield-alt text-success mr-1"></i>
                {{ privacyNotice }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'EmergencyCardView',
  data() {
    return {
      profile: null,
      privacyNotice: '',
      loading: true,
    };
  },
  async mounted() {
    const token = this.$route.params.token;
    if (!token) {
      this.loading = false;
      return;
    }

    try {
      const res = await axios.get(`/health-passport/emergency/${token}`);
      if (res.data.success) {
        this.profile = res.data.emergency_profile;
        this.privacyNotice = res.data.privacy_notice;
      }
    } catch (err) {
      console.error('Error fetching emergency pass:', err);
    } finally {
      this.loading = false;
    }
  },
};
</script>

<style scoped>
.blood-pill {
  bottom: 0;
  right: 0;
  background: #dc2626;
  color: #fff;
  font-weight: 900;
  font-size: 14px;
  padding: 3px 10px;
  border-radius: 20px;
  border: 2px solid #fff;
}
</style>
