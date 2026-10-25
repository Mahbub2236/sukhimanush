<template>
  <div class="emergency-hub-page pb-5">
    <!-- Emergency Red Hero Banner -->
    <section class="page-title py-4 py-md-5 text-white position-relative" style="background: linear-gradient(135deg, #b91c1c 0%, #dc2626 50%, #991b1b 100%);">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-12 text-center">
            <span class="badge badge-warning px-3 py-1 font-weight-bold mb-2 text-dark font-14">
              🚨 ২৪/৭ জরুরি স্বাস্থ্য সহায়তা
            </span>
            <h1 class="text-white font-weight-bold mb-2">জরুরি সেবা হাব (Emergency Hub)</h1>
            <p class="text-white-50 small max-w-600 mx-auto mb-0">
              জরুরি প্রয়োজনে এম্বুলেন্স, রক্তদাতা, লাইভ আইসিইউ বেড এবং হটলাইন সেবা এক ক্লিকে পান।
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Emergency Fast Launchers -->
    <div class="container mt-n4 position-relative" style="z-index: 10;">
      <div class="row g-3">
        <!-- 1. Blood SOS -->
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card border-0 shadow rounded-lg h-100 emergency-card bg-white p-3 text-center transition-all">
            <div class="icon-circle bg-danger-light text-danger mx-auto mb-3">
              <i class="icofont-blood-drop font-32"></i>
            </div>
            <h5 class="font-weight-bold text-dark mb-1">ব্লাড ও প্লাটিলেট SOS</h5>
            <p class="text-muted small mb-3">জরুরি রক্তের জন্য আশেপাশের রক্তদাতাদের সাথে দ্রুত যোগাযোগ করুন।</p>
            <router-link to="/blood-sos" class="btn btn-danger btn-round-full font-weight-bold btn-block mt-auto shadow-sm">
              <i class="icofont-megaphone mr-1"></i> রক্তের অনুরোধ করুন &rarr;
            </router-link>
          </div>
        </div>

        <!-- 2. Ambulance -->
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card border-0 shadow rounded-lg h-100 emergency-card bg-white p-3 text-center transition-all">
            <div class="icon-circle bg-primary-light text-primary mx-auto mb-3">
              <i class="icofont-ambulance-cross font-32"></i>
            </div>
            <h5 class="font-weight-bold text-dark mb-1">এম্বুলেন্স কল করুন</h5>
            <p class="text-muted small mb-3">এসি, নন-এসি ও আইসিইউ এম্বুলেন্স চালকদের সাথে সরাসরি কথা বলুন।</p>
            <router-link to="/emergency-resources?tab=ambulance" class="btn btn-primary btn-round-full font-weight-bold btn-block mt-auto shadow-sm">
              <i class="icofont-phone mr-1"></i> এম্বুলেন্স খুঁজুন &rarr;
            </router-link>
          </div>
        </div>

        <!-- 3. ICU / CCU Bed Availability -->
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card border-0 shadow rounded-lg h-100 emergency-card bg-white p-3 text-center transition-all">
            <div class="icon-circle bg-success-light text-success mx-auto mb-3">
              <i class="icofont-hospital font-32"></i>
            </div>
            <h5 class="font-weight-bold text-dark mb-1">আইসিইউ বেড রেডার</h5>
            <p class="text-muted small mb-3">বিভিন্ন হাসপাতালের আইসিইউ, সিসিইউ ও এনআইসিইউ খালি সিটের লাইভ তথ্য।</p>
            <router-link to="/emergency-resources?tab=icu" class="btn btn-success btn-round-full font-weight-bold btn-block mt-auto shadow-sm">
              <i class="icofont-search-stock mr-1"></i> বেড স্ট্যাটাস দেখুন &rarr;
            </router-link>
          </div>
        </div>

        <!-- 4. Emergency Health QR Card -->
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card border-0 shadow rounded-lg h-100 emergency-card bg-white p-3 text-center transition-all">
            <div class="icon-circle bg-warning-light text-warning mx-auto mb-3">
              <i class="icofont-qr-code font-32 text-dark"></i>
            </div>
            <h5 class="font-weight-bold text-dark mb-1">ইমার্জেন্সি হেলথ কার্ড</h5>
            <p class="text-muted small mb-3">জরুরি অ্যালার্জি, রক্তের গ্রুপ ও কন্টাক্ট কিউআর কোড ডাউনলোড করুন।</p>
            <router-link to="/patient/dashboard?tab=passport" class="btn btn-warning text-dark btn-round-full font-weight-bold btn-block mt-auto shadow-sm">
              <i class="icofont-id-card mr-1"></i> কিউআর কার্ড তৈরি &rarr;
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- National Emergency Hotlines Bar -->
    <div class="container mt-4 mb-4">
      <div class="bg-white p-3 p-md-4 rounded-lg shadow-sm border" style="border-radius: 14px;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap">
          <h5 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
            <i class="icofont-headphone-alt text-danger mr-2" style="font-size: 22px;"></i>
            <span>জাতীয় জরুরি সরকারি হটলাইন নম্বরসমূহ</span>
          </h5>
          <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 11px;">
            <i class="icofont-clock-time mr-1"></i> ২৪/৭ ফ্রি কল
          </span>
        </div>

        <div class="row">
          <!-- 1. National 999 -->
          <div class="col-6 col-lg-3 mb-3">
            <a href="tel:999" class="hotline-card p-3 rounded-lg border text-decoration-none d-flex flex-column align-items-center text-center h-100 shadow-sm bg-white">
              <div class="hotline-badge bg-danger text-white font-weight-bold shadow-sm mb-2">
                <i class="icofont-phone mr-1"></i> 999
              </div>
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">জাতীয় জরুরি সেবা</h6>
              <p class="text-muted small mb-3" style="font-size: 11px; line-height: 1.3;">পুলিশ &bull; এম্বুলেন্স &bull; ফায়ার সার্ভিস</p>
              <span class="btn btn-sm btn-danger text-white font-weight-bold btn-round-full w-100 py-1 mt-auto shadow-sm" style="font-size: 11px;">
                <i class="icofont-ui-call mr-1"></i> ডায়াল করুন
              </span>
            </a>
          </div>

          <!-- 2. Shasthya Batayan 16263 -->
          <div class="col-6 col-lg-3 mb-3">
            <a href="tel:16263" class="hotline-card p-3 rounded-lg border text-decoration-none d-flex flex-column align-items-center text-center h-100 shadow-sm bg-white">
              <div class="hotline-badge bg-success text-white font-weight-bold shadow-sm mb-2">
                <i class="icofont-phone mr-1"></i> 16263
              </div>
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">স্বাস্থ্য বাতায়ন</h6>
              <p class="text-muted small mb-3" style="font-size: 11px; line-height: 1.3;">সরকারি স্বাস্থ্য পরামর্শ ও সেবা</p>
              <span class="btn btn-sm btn-success text-white font-weight-bold btn-round-full w-100 py-1 mt-auto shadow-sm" style="font-size: 11px;">
                <i class="icofont-ui-call mr-1"></i> ডায়াল করুন
              </span>
            </a>
          </div>

          <!-- 3. IEDCR 10655 -->
          <div class="col-6 col-lg-3 mb-3">
            <a href="tel:10655" class="hotline-card p-3 rounded-lg border text-decoration-none d-flex flex-column align-items-center text-center h-100 shadow-sm bg-white">
              <div class="hotline-badge bg-primary text-white font-weight-bold shadow-sm mb-2">
                <i class="icofont-phone mr-1"></i> 10655
              </div>
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">আইইডিসিআর কন্ট্রোল</h6>
              <p class="text-muted small mb-3" style="font-size: 11px; line-height: 1.3;">সংক্রামক রোগ ও মহামারি</p>
              <span class="btn btn-sm btn-primary text-white font-weight-bold btn-round-full w-100 py-1 mt-auto shadow-sm" style="font-size: 11px;">
                <i class="icofont-ui-call mr-1"></i> ডায়াল করুন
              </span>
            </a>
          </div>

          <!-- 4. Red Crescent Blood Bank 02-9330188 -->
          <div class="col-6 col-lg-3 mb-3">
            <a href="tel:029330188" class="hotline-card p-3 rounded-lg border text-decoration-none d-flex flex-column align-items-center text-center h-100 shadow-sm bg-white">
              <div class="hotline-badge bg-dark text-white font-weight-bold shadow-sm mb-2" style="font-size: 11px; padding: 4px 8px;">
                <i class="icofont-phone mr-1"></i> 02-9330188
              </div>
              <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">রেড ক্রিসেন্ট ব্লাড</h6>
              <p class="text-muted small mb-3" style="font-size: 11px; line-height: 1.3;">জরুরি রক্তের সংকট ও ব্যাংক</p>
              <span class="btn btn-sm btn-dark text-white font-weight-bold btn-round-full w-100 py-1 mt-auto shadow-sm" style="font-size: 11px;">
                <i class="icofont-ui-call mr-1"></i> ডায়াল করুন
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EmergencyHub',
};
</script>

<style scoped>
.icon-circle {
  width: 65px;
  height: 65px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.bg-danger-light { background: #fee2e2; }
.bg-primary-light { background: #dbeafe; }
.bg-success-light { background: #dcfce7; }
.bg-warning-light { background: #fef9c3; }

.emergency-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.emergency-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}
.hotline-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  border: 1px solid #e2e8f0;
}
.hotline-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
  border-color: #cbd5e1;
}
.hotline-badge {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  letter-spacing: 0.5px;
}
</style>
