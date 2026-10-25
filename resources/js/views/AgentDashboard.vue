<template>
  <div class="agent-dashboard-page pb-5 bg-light min-vh-100">
    <!-- Top Header Banner -->
    <section class="page-title py-4 text-white position-relative" style="background: linear-gradient(135deg, #065f46 0%, #047857 60%, #059669 100%);">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
          <div>
            <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mb-2">
              🏪 গ্রামীণ ডিজিটাল স্বাস্থ্য পয়েন্ট
            </span>
            <h2 class="text-white font-weight-bold mb-1">{{ agent?.pharmacy_name || 'ডিজিটাল স্বাস্থ্য পয়েন্ট' }}</h2>
            <p class="text-white-50 small mb-0">
              পরিচালক: <strong>{{ agent?.name }}</strong> &bull; ফোন: {{ agent?.phone }}
            </p>
          </div>

          <div class="mt-3 mt-md-0 text-md-right">
            <div class="small text-white-50">কমিশন ব্যালান্স</div>
            <div class="display-4 font-weight-bold text-warning font-32">৳{{ agent?.commission_balance || '0.00' }}</div>
            <button class="btn btn-sm btn-light font-weight-bold rounded-pill px-3 mt-1" @click="handleOpenBookingModal">
              <i class="icofont-plus-circle text-success mr-1"></i> নতুন রোগী বুকিং
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Content Area -->
    <div class="container mt-4">
      <!-- Pending Approval Banner -->
      <div v-if="agent && !agent.is_approved" class="alert alert-warning border-0 shadow-sm rounded-lg p-3 mb-4 d-flex align-items-center flex-wrap" style="background: #fff8e6; border-left: 5px solid #f59e0b !important;">
        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center mr-3 mb-2 mb-sm-0 flex-shrink-0" style="width: 44px; height: 44px; font-size: 22px;">
          <i class="icofont-warning-alt"></i>
        </div>
        <div class="flex-grow-1 mr-3">
          <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 15px;">
            ⚠️ আপনার ডিজিটাল হেলথ পয়েন্টটি বর্তমানে অ্যাডমিনের অনুমোদনের অপেক্ষায় রয়েছে (Pending Approval)
          </h6>
          <p class="mb-0 text-muted small">
            অ্যাডমিন আপনার ফার্মেসি ও যোগাযোগের তথ্য পর্যালোচনা করে অনুমোদন দেবেন। অনুমোদনের পরেই আপনি রোগীদের পক্ষে সিরিয়াল বুকিং সম্পন্ন করতে পারবেন এবং কমিশন আয় করতে পারবেন।
          </p>
        </div>
        <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold rounded-pill mt-2 mt-sm-0">
          <i class="icofont-sand-clock mr-1"></i> পর্যালোচনাধীন
        </span>
      </div>

      <!-- 3 Metrics Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-4 mb-2">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white border-left-success">
            <div class="small text-muted font-weight-bold">আজকের রোগী বুকিং</div>
            <div class="font-weight-bold text-dark font-28">{{ stats.today_count || 0 }} জন</div>
            <small class="text-success font-weight-bold"><i class="icofont-calendar"></i> আজ সম্পন্ন</small>
          </div>
        </div>

        <div class="col-md-4 mb-2">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white border-left-warning">
            <div class="small text-muted font-weight-bold">আজকের অর্জিত কমিশন</div>
            <div class="font-weight-bold text-success font-28">৳{{ stats.today_commission || '0.00' }}</div>
            <small class="text-muted"><i class="icofont-money-bag text-warning"></i> প্রতি বুকিংয়ে ৳৪০ কমিশন</small>
          </div>
        </div>

        <div class="col-md-4 mb-2">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white border-left-primary">
            <div class="small text-muted font-weight-bold">মোট লাইফটাইম কমিশন</div>
            <div class="font-weight-bold text-primary font-28">৳{{ agent?.total_commission_earned || '0.00' }}</div>
            <small class="text-muted">সর্বমোট বুকিং: {{ agent?.total_bookings_count || 0 }} টি</small>
          </div>
        </div>
      </div>

      <!-- Bookings Table Card -->
      <div class="card border-0 shadow-sm rounded-lg overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap justify-content-between align-items-center">
          <h5 class="font-weight-bold text-dark mb-0">
            <i class="icofont-listing-box text-success mr-2"></i> সাম্প্রতিক রোগী বুকিং খাতা (Patient Ledger)
          </h5>
          <button class="btn btn-success btn-sm font-weight-bold rounded-pill px-3 shadow-sm" @click="handleOpenBookingModal">
            <i class="icofont-ui-add mr-1"></i> নতুন রোগী যোগ করুন
          </button>
        </div>

        <div class="card-body p-0">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="text-muted small mt-2">তথ্য লোড হচ্ছে...</p>
          </div>

          <div v-else-if="recentBookings.length === 0" class="text-center py-5 text-muted">
            <i class="icofont-prescription display-4 text-muted"></i>
            <h6 class="mt-3 font-weight-bold">এখনো কোনো রোগীর বুকিং করা হয়নি</h6>
            <p class="small">উপরের বাটনে ক্লিক করে গ্রামের রোগীদের জন্য ডাক্তারের সিরিয়াল বা ভিডিও কল বুক করুন।</p>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
              <thead class="bg-light small font-weight-bold text-muted">
                <tr>
                  <th>টোকেন / সিরিয়াল</th>
                  <th>রোগীর নাম ও ফোন</th>
                  <th>ডাক্তার ও বিভাগ</th>
                  <th>পদ্ধতি</th>
                  <th>তারিখ</th>
                  <th>কমিশন</th>
                  <th class="text-right">অ্যাকশন</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="b in recentBookings" :key="b.id">
                  <td>
                    <strong class="text-primary">{{ b.appointment_number }}</strong>
                    <span class="badge badge-secondary ml-1">Serial #{{ b.serial_number }}</span>
                  </td>
                  <td>
                    <div class="font-weight-bold text-dark">{{ b.patient_name }}</div>
                    <small class="text-muted">{{ b.patient_phone }}</small>
                  </td>
                  <td>
                    <div class="font-weight-bold text-dark">{{ b.doctor?.name }}</div>
                    <small class="text-muted">{{ b.doctor?.specialization }}</small>
                  </td>
                  <td>
                    <span class="badge" :class="b.consultation_type === 'online_video' ? 'badge-success' : 'badge-primary'">
                      {{ b.consultation_type === 'online_video' ? 'ভিডিও কল' : 'চেম্বারে' }}
                    </span>
                  </td>
                  <td class="small">{{ b.appointment_date }}</td>
                  <td>
                    <strong class="text-success">+৳{{ b.agent_commission }}</strong>
                  </td>
                  <td class="text-right">
                    <div class="btn-group btn-group-sm">
                      <button @click="shareWhatsApp(b)" class="btn btn-outline-success font-weight-bold py-1 px-2" title="রোগীর হোয়াটসঅ্যাপে স্লিপ পাঠান" style="font-size: 11px;">
                        <i class="icofont-whatsapp"></i>
                      </button>
                      <button @click="printAgentSlip(b)" class="btn btn-outline-dark font-weight-bold py-1 px-2" title="স্লিপ প্রিন্ট দিন" style="font-size: 11px;">
                        <i class="icofont-print"></i>
                      </button>
                      <router-link :to="'/track-appointment?query=' + b.appointment_number" class="btn btn-outline-primary font-weight-bold px-2 py-1" style="font-size: 11px;" title="লাইভ কিউ ট্র্যাকার">
                        <i class="icofont-clock-time"></i>
                      </router-link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Walk-in Booking Modal -->
    <div v-if="showBookingModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3">
      <div class="modal-card bg-white rounded-lg shadow-lg p-4 w-100" style="max-width: 540px; max-height: 90vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="font-weight-bold text-success mb-0">
            <i class="icofont-prescription mr-1"></i> গ্রামীণ রোগী বুকিং ফরম
          </h5>
          <button type="button" class="close font-24" @click="showBookingModal = false">&times;</button>
        </div>

        <form @submit.prevent="submitAgentBooking">
          <div class="form-group mb-2">
            <label class="font-weight-bold small">রোগীর পুরো নাম *</label>
            <input type="text" v-model="newBooking.patient_name" class="form-control form-control-sm" required placeholder="যেমন: আব্দুল কাদের">
          </div>

          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">রোগীর মোবাইল নম্বর *</label>
              <input type="text" v-model="newBooking.patient_phone" class="form-control form-control-sm" required placeholder="017XXXXXXXX">
            </div>
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">পরামর্শের ধরন *</label>
              <select v-model="newBooking.consultation_type" class="form-control form-control-sm" required>
                <option value="in_chamber">সরাসরি চেম্বারে সিরিয়াল</option>
                <option value="online_video">অনলাইন ভিডিও কনসালটেশন</option>
              </select>
            </div>
          </div>

          <div class="form-group mb-2">
            <label class="font-weight-bold small">ডাক্তার নির্বাচন করুন *</label>
            <select v-model="newBooking.doctor_id" class="form-control form-control-sm" required>
              <option value="">ডাক্তার বেছে নিন</option>
              <option v-for="doc in doctorsList" :key="doc.id" :value="doc.id">
                ডাঃ {{ doc.name }} - {{ doc.specialization }} ({{ doc.district }}) - ফি ৳{{ doc.consultation_fee }}
              </option>
            </select>
          </div>

          <div class="row">
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">তারিখ *</label>
              <input type="date" v-model="newBooking.appointment_date" class="form-control form-control-sm" required :min="todayDate">
            </div>
            <div class="col-6 form-group mb-2">
              <label class="font-weight-bold small">জেলা</label>
              <input type="text" v-model="newBooking.district" class="form-control form-control-sm" placeholder="যেমন: রংপুর">
            </div>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold small">রোগ বা সমস্যার বিবরণ</label>
            <textarea v-model="newBooking.reason_for_visit" rows="2" class="form-control form-control-sm" placeholder="যেমন: পেটে ব্যথা ও বমি বমি ভাব..."></textarea>
          </div>

          <div class="alert alert-success p-2 small font-weight-bold mb-3 d-flex justify-content-between align-items-center">
            <span>এই বুকিংয়ে আপনার আয়:</span>
            <span class="font-16 text-dark">+৳৪০.০০ কমিশন</span>
          </div>

          <button type="submit" class="btn btn-success btn-block font-weight-bold rounded-pill py-2 shadow-sm" :disabled="bookingSubmitting">
            {{ bookingSubmitting ? 'বুকিং হচ্ছে...' : 'বুকিং নিশ্চিত করুন ও টোকেন প্রিন্ট দিন' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'AgentDashboard',
  data() {
    return {
      agent: null,
      stats: {},
      recentBookings: [],
      doctorsList: [],
      loading: true,
      showBookingModal: false,
      bookingSubmitting: false,
      todayDate: new Date().toISOString().split('T')[0],
      newBooking: {
        patient_name: '',
        patient_phone: '',
        doctor_id: '',
        appointment_date: new Date().toISOString().split('T')[0],
        consultation_type: 'in_chamber',
        district: 'Rangpur',
        reason_for_visit: '',
      },
    };
  },
  mounted() {
    this.initAgent();
  },
  methods: {
    async initAgent() {
      // Check logged in user or default to test agent id
      const userStr = localStorage.getItem('saas_user');
      const user = userStr ? JSON.parse(userStr) : null;
      const agentId = (user && user.role === 'agent') ? user.id : 1;

      this.loading = true;
      try {
        const [dashRes, docRes] = await Promise.all([
          axios.get(`/agent-portal/dashboard/${agentId}`),
          axios.get('/doctors'),
        ]);

        if (dashRes.data.success) {
          this.agent = dashRes.data.agent;
          this.stats = dashRes.data.stats;
          this.recentBookings = dashRes.data.recent_bookings || [];
        }
        this.doctorsList = docRes.data.doctors || [];
      } catch (err) {
        console.error('Error loading agent dashboard:', err);
      } finally {
        this.loading = false;
      }
    },
    handleOpenBookingModal() {
      if (this.agent && !this.agent.is_approved) {
        Swal.fire({
          icon: 'warning',
          title: 'অনুমোদন অপেক্ষমাণ',
          text: 'আপনার ডিজিটাল হেলথ পয়েন্টটি এখনও অ্যাডমিন কর্তৃক অনুমোদিত হয়নি। অ্যাডমিন অনুমোদনের পর আপনি রোগী বুকিং সম্পন্ন করতে পারবেন।',
          confirmButtonText: 'ঠিক আছে',
          confirmButtonColor: '#059669',
        });
        return;
      }
      this.showBookingModal = true;
    },
    async submitAgentBooking() {
      this.bookingSubmitting = true;
      try {
        const agentId = this.agent ? this.agent.id : 1;
        const res = await axios.post(`/agent-portal/bookings/${agentId}`, this.newBooking);
        if (res.data.success) {
          const apt = res.data.appointment;
          const waUrl = res.data.whatsapp_url;
          
          Swal.fire({
            title: 'বুকিং সম্পন্ন হয়েছে!',
            html: `
              <div class="text-center">
                <div class="badge badge-success px-3 py-2 font-16 mb-2">সিরিয়াল নং: #${apt.serial_number}</div>
                <p class="mb-1">রোগী: <strong>${apt.patient_name}</strong> (${apt.patient_phone})</p>
                <p class="text-muted small mb-2">রেফারেন্স কোড: <strong>${res.data.tracking_code}</strong></p>
                <div class="alert alert-success font-weight-bold py-2 mb-3">আপনার একাউন্টে ৳${res.data.commission_earned} কমিশন যোগ হয়েছে!</div>
                <div class="d-flex justify-content-center" style="gap: 10px;">
                  ${waUrl ? `<a href="${waUrl}" target="_blank" class="btn btn-success btn-sm font-weight-bold px-3 py-2 rounded-pill"><i class="icofont-whatsapp mr-1"></i> হোয়াটসঅ্যাপে স্লিপ দিন</a>` : ''}
                </div>
              </div>
            `,
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'টোকেন প্রিন্ট করুন',
            cancelButtonText: 'বন্ধ করুন',
            confirmButtonColor: '#059669',
          }).then((result) => {
            if (result.isConfirmed) {
              this.printAgentSlip(apt);
            }
          });

          this.showBookingModal = false;
          this.initAgent();
        }
      } catch (err) {
        Swal.fire('ত্রুটি', err.response?.data?.message || 'বুকিং ব্যর্থ হয়েছে।', 'error');
      } finally {
        this.bookingSubmitting = false;
      }
    },
    shareWhatsApp(b) {
      const docName = b.doctor?.name || 'বিশেষজ্ঞ ডাক্তার';
      const docDept = b.doctor?.specialization || 'মেডিকেল';
      const chamber = b.doctor?.chamber_address || 'চেম্বার';
      const patient = b.patient_name;
      const serial = b.serial_number;
      const date = b.appointment_date;
      const code = b.appointment_number;
      const trackUrl = `${window.location.origin}/track-appointment?query=${code}`;

      let phone = (b.patient_phone || '').replace(/[^0-9]/g, '');
      if (phone.startsWith('880')) {
        phone = phone;
      } else if (phone.startsWith('0')) {
        phone = '88' + phone;
      } else {
        phone = '880' + phone;
      }

      const text = `*🏥 সুখী মানুষ (Sukhi Manush) - অ্যাপয়েন্টমেন্ট টোকেন স্লিপ*\n` +
        `━━━━━━━━━━━━━━━━━━\n` +
        `👤 *রোগী:* ${patient}\n` +
        `🔢 *সিরিয়াল নম্বর:* #${serial}\n` +
        `👨‍⚕️ *ডাক্তার:* ডাঃ ${docName} (${docDept})\n` +
        `📍 *চেম্বার:* ${chamber}\n` +
        `📅 *তারিখ:* ${date}\n` +
        `🔖 *টোকেন কোড:* ${code}\n` +
        `━━━━━━━━━━━━━━━━━━\n` +
        `🏪 *ইস্যুকারী:* ${this.agent?.pharmacy_name || 'গ্রামীণ ডিজিটাল হেলথ পয়েন্ট'}\n` +
        `📱 *লাইভ সিরিয়াল ট্র্যাকিং লিংক:* \n${trackUrl}\n\n` +
        `_(চেম্বারে সিরিয়াল ডাকার সময় সতর্ক থাকুন)_`;

      const waLink = `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(text)}`;
      window.open(waLink, '_blank');
    },
    printAgentSlip(b) {
      const docName = b.doctor?.name || 'বিশেষজ্ঞ ডাক্তার';
      const docDept = b.doctor?.specialization || 'মেডিকেল স্পেশালিস্ট';
      const chamber = b.doctor?.chamber_address || 'চেম্বার কাউন্টার';
      const pharmacy = this.agent?.pharmacy_name || 'গ্রামীণ ডিজিটাল স্বাস্থ্য পয়েন্ট';
      const agentPhone = this.agent?.phone || '';
      const patient = b.patient_name;
      const patientPhone = b.patient_phone;
      const serial = b.serial_number;
      const date = b.appointment_date;
      const code = b.appointment_number;
      const trackUrl = `${window.location.origin}/track-appointment?query=${code}`;

      const printWindow = window.open('', '_blank', 'width=420,height=600');
      if (!printWindow) {
        window.print();
        return;
      }

      printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
          <title>টোকেন স্লিপ - #${serial}</title>
          <style>
            @page { size: 80mm auto; margin: 5mm; }
            body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 10px; color: #111; font-size: 13px; line-height: 1.4; }
            .ticket { border: 2px dashed #333; padding: 12px; border-radius: 6px; text-align: center; }
            .header { font-size: 16px; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; }
            .sub { font-size: 11px; color: #555; margin-bottom: 8px; }
            .serial-box { background: #111; color: #fff; padding: 8px 0; margin: 8px 0; border-radius: 4px; }
            .serial-title { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
            .serial-num { font-size: 32px; font-weight: 900; line-height: 1; margin-top: 4px; }
            .info-table { width: 100%; text-align: left; margin: 10px 0; font-size: 12px; border-collapse: collapse; }
            .info-table td { padding: 4px 0; border-bottom: 1px dotted #ccc; }
            .info-table td.label { font-weight: bold; width: 38%; color: #333; }
            .footer { font-size: 10px; color: #666; margin-top: 10px; border-top: 1px solid #ccc; padding-top: 6px; }
          </style>
        </head>
        <body>
          <div class="ticket">
            <div class="header">সুখী মানুষ (SUKHI MANUSH)</div>
            <div class="sub">ডিজিটাল সিরিয়াল ও অ্যাপয়েন্টমেন্ট স্লিপ</div>

            <div class="serial-box">
              <div class="serial-title">আপনার সিরিয়াল নম্বর</div>
              <div class="serial-num">#${serial}</div>
            </div>

            <table class="info-table">
              <tr><td class="label">রোগীর নাম:</td><td><strong>${patient}</strong></td></tr>
              <tr><td class="label">মোবাইল:</td><td>${patientPhone}</td></tr>
              <tr><td class="label">ডাক্তার:</td><td>ডাঃ ${docName}</td></tr>
              <tr><td class="label">বিভাগ:</td><td>${docDept}</td></tr>
              <tr><td class="label">চেম্বার:</td><td>${chamber}</td></tr>
              <tr><td class="label">তারিখ:</td><td>${date}</td></tr>
              <tr><td class="label">টোকেন কোড:</td><td><strong>${code}</strong></td></tr>
            </table>

            <div class="footer">
              <div><strong>ইস্যুকারী:</strong> ${pharmacy} ${agentPhone ? '(' + agentPhone + ')' : ''}</div>
              <div style="margin-top: 3px;">লাইভ সিরিয়াল ট্র্যাক করুন: <strong>${trackUrl}</strong></div>
              <div style="margin-top: 4px; font-style: italic;">চেম্বারের সিরিয়াল ডাকার সময় সাথে রাখুন</div>
            </div>
          </div>
          <script>
            window.onload = function() {
              window.print();
              window.onafterprint = function() { window.close(); };
            };
          <\/script>
        </body>
        </html>
      `);
      printWindow.document.close();
    },
  },
};
</script>

<style scoped>
.border-left-success { border-left: 5px solid #10b981 !important; }
.border-left-warning { border-left: 5px solid #f59e0b !important; }
.border-left-primary { border-left: 5px solid #3b82f6 !important; }
.modal-backdrop-custom {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  z-index: 9999;
}
</style>
