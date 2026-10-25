<template>
  <div v-if="show" class="prescription-modal-backdrop" @click.self="$emit('close')">
    <div class="prescription-modal-dialog">
      <!-- Top Action Bar (Hidden during printing) -->
      <div class="modal-top-actions no-print p-2 p-md-3 bg-white border-bottom rounded-top">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
          
          <!-- Title & Icon -->
          <div class="d-flex align-items-center">
            <div class="print-icon-circle mr-2 bg-primary-soft text-primary d-flex align-items-center justify-content-center rounded-circle">
              <i class="icofont-prescription" style="font-size: 18px;"></i>
            </div>
            <div>
              <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 15px;">মেডিকেল প্রেসক্রিপশন প্রিন্ট প্রিভিউ</h6>
              <small class="text-muted d-none d-sm-inline">A4 সাইজ স্ট্যান্ডার্ড মেডিকেল প্রেসক্রিপশন প্যাড</small>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex align-items-center flex-wrap modal-action-btns" style="gap: 8px;">
            <!-- WhatsApp Direct Share -->
            <a 
              v-if="whatsAppShareUrl"
              :href="whatsAppShareUrl"
              target="_blank"
              class="btn btn-success btn-sm px-2 px-sm-3 py-2 font-weight-bold shadow-sm d-inline-flex align-items-center"
              title="রোগীর হোয়াটসঅ্যাপে প্রেসক্রিপশন তথ্য পাঠান"
            >
              <i class="icofont-brand-whatsapp mr-1" style="font-size: 15px;"></i> WhatsApp
            </a>

            <!-- Print / Save as PDF Button -->
            <button 
              class="btn btn-main btn-sm px-3 py-2 font-weight-bold shadow-sm d-inline-flex align-items-center" 
              @click="handlePrint"
            >
              <i class="icofont-print mr-1" style="font-size: 15px;"></i> প্রিন্ট / PDF সেভ করুন
            </button>

            <!-- Close Button -->
            <button 
              class="btn btn-outline-secondary btn-sm px-2 px-sm-3 py-2 font-weight-bold d-inline-flex align-items-center" 
              @click="$emit('close')"
            >
              <i class="icofont-close mr-1"></i> বন্ধ করুন
            </button>
          </div>

        </div>
      </div>

      <!-- Printable Prescription Document Body -->
      <div class="prescription-scroll-container p-2 p-md-4 bg-light">
        <div id="printable-prescription-pad" class="prescription-pad bg-white mx-auto shadow-sm position-relative">
          
          <!-- ================= HEADER SECTION ================= -->
          <div class="pad-header border-bottom pb-3 mb-3">
            <div class="row align-items-start">
              <!-- Doctor Info (Left) -->
              <div class="col-12 col-md-7 doctor-meta-col mb-2 mb-md-0">
                <div class="d-flex align-items-center mb-1">
                  <h4 class="doctor-name font-weight-bold mb-0 text-navy">
                    {{ currentDoctor.name || 'ডাক্তারের নাম' }}
                  </h4>
                </div>
                <div class="doctor-degrees text-secondary font-weight-bold" style="font-size: 13px;">
                  {{ currentDoctor.qualifications || 'MBBS, FCPS' }}
                </div>
                <div class="doctor-designation text-primary font-weight-bold" style="font-size: 13px;">
                  {{ currentDoctor.designation || 'Medical Consultant' }}
                </div>
                <div class="doctor-spec text-muted" style="font-size: 12px;">
                  {{ currentDoctor.specialization || 'General Medicine' }}
                </div>
                <div v-if="currentDoctor.bmdc_number" class="bmdc-badge d-inline-block mt-1 px-2 py-0.5 border rounded text-dark font-weight-bold" style="font-size: 11px; background: #f8fafc;">
                  BMDC Reg: {{ currentDoctor.bmdc_number }}
                </div>
              </div>

              <!-- Chamber & Hospital Info (Right) -->
              <div class="col-12 col-md-5 text-left text-md-right chamber-meta-col">
                <div class="hospital-brand d-inline-flex align-items-center justify-content-md-end mb-1">
                  <i class="icofont-hospital text-danger mr-1" style="font-size: 18px;"></i>
                  <span class="font-weight-bold text-dark" style="font-size: 15px;">মাহিরাভা ডিজিটাল হেলথকেয়ার</span>
                </div>
                <div class="chamber-address text-muted" style="font-size: 12px; line-height: 1.4;">
                  {{ currentDoctor.chamber_address || 'চেম্বার: সেন্ট্রাল ডায়াগনস্টিক কমপ্লেক্স' }}
                </div>
                <div v-if="currentDoctor.phone" class="chamber-phone text-muted" style="font-size: 12px;">
                  <i class="icofont-phone mr-1 text-primary"></i> {{ currentDoctor.phone }}
                </div>
                <div class="visiting-hours text-muted" style="font-size: 11px;">
                  সাক্ষাতের সময়: {{ currentDoctor.start_time ? formatTime(currentDoctor.start_time) + ' - ' + formatTime(currentDoctor.end_time) : 'সকাল ৯টা - বিকাল ৫টা' }}
                </div>
              </div>
            </div>
          </div>

          <!-- ================= PATIENT DEMOGRAPHICS BAR ================= -->
          <div class="patient-bar bg-light p-2 px-3 rounded mb-3 border">
            <div class="row align-items-center text-dark" style="font-size: 13px;">
              <div class="col-6 col-md-3 patient-bar-item mb-1 mb-md-0">
                <span class="text-muted">রোগীর নাম: </span>
                <strong class="text-dark">{{ prescription.patient_name || 'রোগীর নাম' }}</strong>
              </div>
              <div class="col-6 col-md-3 patient-bar-item mb-1 mb-md-0">
                <span class="text-muted">বয়স/লিঙ্গ: </span>
                <strong>{{ prescription.patient_age_gender || 'বয়স উল্লেখ নেই' }}</strong>
              </div>
              <div class="col-6 col-md-3 patient-bar-item">
                <span class="text-muted">প্রেসক্রিপশন নং: </span>
                <strong class="text-danger">{{ prescription.prescription_number || ('RX-' + (prescription.id || '001')) }}</strong>
              </div>
              <div class="col-6 col-md-3 patient-bar-item text-left text-md-right">
                <span class="text-muted">তারিখ: </span>
                <strong>{{ formatRxDate(prescription.created_at) }}</strong>
              </div>
            </div>
          </div>

          <!-- ================= MAIN BODY (TWO COLUMNS) ================= -->
          <div class="pad-body row" style="min-height: 660px;">
            
            <!-- LEFT COLUMN: DIAGNOSIS, TESTS & ADVICES (32% width on desktop/print) -->
            <div class="col-12 col-md-4 border-right pr-md-3 left-clinical-col mb-3 mb-md-0">
              
              <!-- 1. Clinical Diagnosis -->
              <div class="clinical-block mb-3">
                <h6 class="section-title text-navy font-weight-bold d-flex align-items-center pb-1 border-bottom">
                  <i class="icofont-stethoscope mr-1 text-danger"></i> রোগ / Diagnosis
                </h6>
                <div class="diagnosis-box p-2 rounded bg-light border text-danger font-weight-bold" style="font-size: 13px;">
                  {{ prescription.diagnosis || 'প্রাথমিক পরীক্ষা সম্পন্ন' }}
                </div>
              </div>

              <!-- 2. Advised Medical Tests (ল্যাব টেস্টসমূহ) -->
              <div v-if="parsedTests && parsedTests.length" class="clinical-block mb-3">
                <h6 class="section-title text-navy font-weight-bold d-flex align-items-center pb-1 border-bottom">
                  <i class="icofont-test-tube-alt mr-1 text-primary"></i> টেস্ট / Investigations
                </h6>
                <ul class="test-list pl-3 mb-0" style="font-size: 12px; line-height: 1.6;">
                  <li v-for="(test, tIdx) in parsedTests" :key="tIdx" class="text-dark font-weight-bold">
                    {{ test }}
                  </li>
                </ul>
              </div>

              <!-- 3. Doctor's Advice & Guidelines (উপদেশ) -->
              <div v-if="prescription.advice" class="clinical-block mb-3">
                <h6 class="section-title text-navy font-weight-bold d-flex align-items-center pb-1 border-bottom">
                  <i class="icofont-info-circle mr-1 text-success"></i> পরামর্শ / Advice
                </h6>
                <div class="advice-content text-muted" style="font-size: 12px; line-height: 1.5; white-space: pre-line;">
                  {{ prescription.advice }}
                </div>
              </div>

              <!-- Follow Up Date Box -->
              <div v-if="prescription.follow_up_date" class="follow-up-card p-2 rounded bg-success-soft border border-success mt-4">
                <div class="d-flex align-items-center text-success font-weight-bold" style="font-size: 12px;">
                  <i class="icofont-calendar mr-1"></i> পরবর্তী সাক্ষাত:
                </div>
                <div class="font-weight-bold text-dark mt-1" style="font-size: 13px;">
                  {{ formatRxDate(prescription.follow_up_date) }}
                </div>
              </div>

            </div>

            <!-- RIGHT COLUMN: PRESCRIPTION MEDICINES (68% width on desktop/print) -->
            <div class="col-12 col-md-8 pl-md-4 right-medicine-col">
              
              <!-- Classic Rx Header -->
              <div class="rx-symbol text-primary font-weight-bold mb-3 d-flex align-items-baseline">
                <span style="font-family: 'Times New Roman', serif; font-size: 32px; line-height: 1;">℞</span>
                <span class="text-muted ml-2 small" style="font-size: 12px; font-weight: normal;">(ঔষধসমূহ ও সেবনের নিয়মাবলী)</span>
              </div>

              <!-- Medicines Table / List -->
              <div class="medicines-container">
                <div 
                  v-for="(med, mIdx) in parsedMedicines" 
                  :key="mIdx" 
                  class="medicine-item border-bottom pb-2 mb-2"
                >
                  <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 5px;">
                    <div class="d-flex align-items-baseline" style="max-width: 80%;">
                      <span class="med-index font-weight-bold text-muted mr-2" style="font-size: 13px;">{{ mIdx + 1 }}.</span>
                      <div>
                        <strong class="med-name text-dark font-weight-bold" style="font-size: 14px;">
                          {{ med.name }}
                        </strong>
                      </div>
                    </div>

                    <!-- Dosage badge e.g. 1 + 0 + 1 -->
                    <div class="med-dosage badge badge-light border text-primary font-weight-bold px-2 py-1" style="font-size: 13px; letter-spacing: 1px;">
                      {{ med.dosage || '১ বার' }}
                    </div>
                  </div>

                  <!-- Instruction & Duration line -->
                  <div class="d-flex align-items-center text-muted pl-3 pl-sm-4 mt-1 flex-wrap" style="font-size: 12px; gap: 12px;">
                    <span v-if="med.duration" class="d-inline-flex align-items-center">
                      <i class="icofont-clock-time mr-1 text-secondary"></i> {{ med.duration }}
                    </span>
                    <span v-if="med.instruction" class="d-inline-flex align-items-center text-danger font-weight-bold">
                      <i class="icofont-warning-alt mr-1"></i> {{ med.instruction }}
                    </span>
                  </div>
                </div>

                <div v-if="!parsedMedicines.length" class="text-center py-4 text-muted">
                  কোনো ঔষধ যোগ করা হয়নি।
                </div>
              </div>

            </div>

          </div>

          <!-- ================= FOOTER & SIGNATURE ================= -->
          <div class="pad-footer border-top pt-3 mt-3">
            <div class="row align-items-end">
              <div class="col-8 col-sm-8 footer-notes-col">
                <div class="emergency-disclaimer text-muted" style="font-size: 11px; line-height: 1.4;">
                  <span class="text-danger font-weight-bold">⚠️ সতর্কবার্তা:</span> ডাক্তারের পরামর্শ ব্যতীত কোনো ঔষধের মাত্রা পরিবর্তন করবেন না। জরুরি অবস্থায় নিকটস্থ সরকারি বা বিশেষায়িত হাসপাতালে যোগাযোগ করুন।
                </div>
                <div class="system-watermark mt-2 text-muted" style="font-size: 10px;">
                  Computer Generated Digital Prescription • Powered by সুখী মানুষ (Sukhi Manush) Health Cloud Platform
                </div>
              </div>

              <!-- Doctor's Signature Seal Box -->
              <div class="col-4 col-sm-4 text-center footer-sig-col">
                <div class="signature-line mx-auto mb-1 border-bottom" style="width: 120px; height: 35px;"></div>
                <div class="text-dark font-weight-bold" style="font-size: 12px;">
                  {{ currentDoctor.name || 'ডাক্তারের স্বাক্ষর' }}
                </div>
                <small class="text-muted" style="font-size: 10px;">
                  Registered Medical Practitioner
                </small>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PrescriptionPrintModal',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    prescription: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    doctor: {
      type: Object,
      default: null,
    },
  },
  computed: {
    currentDoctor() {
      return this.prescription.doctor || this.doctor || {};
    },
    parsedMedicines() {
      const meds = this.prescription.medicines;
      if (!meds) return [];
      if (Array.isArray(meds)) return meds;
      try {
        return typeof meds === 'string' ? JSON.parse(meds) : [];
      } catch (e) {
        return [];
      }
    },
    parsedTests() {
      const tests = this.prescription.medical_tests;
      if (!tests) return [];
      if (Array.isArray(tests)) return tests;
      try {
        return typeof tests === 'string' ? JSON.parse(tests) : [];
      } catch (e) {
        return [];
      }
    },
    whatsAppShareUrl() {
      const p = this.prescription;
      if (!p || !p.patient_name) return null;
      const docName = this.currentDoctor?.name || 'ডাক্তার';
      const rxNum = p.prescription_number || ('RX-' + (p.id || '001'));
      const patientName = p.patient_name || 'রোগী';
      const dateStr = this.formatRxDate(p.created_at);

      let msg = `🩺 *প্রেসক্রিপশন বিবরণী - মাহিরাভা ডিজিটাল হেলথকেয়ার*\n\n` +
        `👤 *রোগীর নাম:* ${patientName}\n` +
        `🔖 *প্রেসক্রিপশন নং:* ${rxNum}\n` +
        `📅 *তারিখ:* ${dateStr}\n` +
        `👨‍⚕️ *চিকিৎসক:* ডাঃ ${docName}\n\n`;

      if (p.diagnosis) {
        msg += `🩺 *ডায়াগনোসিস:* ${p.diagnosis}\n\n`;
      }

      if (this.parsedMedicines && this.parsedMedicines.length > 0) {
        msg += `💊 *ঔষধসমূহ ও সেবনের নিয়ম:*\n`;
        this.parsedMedicines.forEach((med, idx) => {
          msg += `${idx + 1}. *${med.name}*\n   - মাত্রা: ${med.dosage || '১ বার'}\n   - মেয়াদ: ${med.duration || '৭ দিন'}${med.instruction ? ' (' + med.instruction + ')' : ''}\n`;
        });
        msg += `\n`;
      }

      if (this.parsedTests && this.parsedTests.length > 0) {
        msg += `🧪 *ল্যাব টেস্ট:* ${this.parsedTests.join(', ')}\n\n`;
      }

      if (p.advice) {
        msg += `💡 *পরামর্শ:* ${p.advice}\n\n`;
      }

      if (p.follow_up_date) {
        msg += `🗓️ *পরবর্তী সাক্ষাত:* ${this.formatRxDate(p.follow_up_date)}\n\n`;
      }

      msg += `সুস্থ থাকুন, ধন্যবাদ!`;

      let phone = (p.patient_phone || '').replace(/[^\d]/g, '');
      if (phone.startsWith('01')) phone = '88' + phone;
      return phone ? `https://wa.me/${phone}?text=${encodeURIComponent(msg)}` : `https://wa.me/?text=${encodeURIComponent(msg)}`;
    },
  },
  methods: {
    formatTime(timeStr) {
      if (!timeStr) return '';
      const [h, m] = timeStr.split(':');
      let hour = parseInt(h, 10);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      hour = hour % 12 || 12;
      return `${hour}:${m} ${ampm}`;
    },
    formatRxDate(dateStr) {
      if (!dateStr) {
        return new Date().toLocaleDateString('bn-BD', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
        });
      }
      const d = new Date(dateStr);
      if (isNaN(d.getTime())) {
        return dateStr;
      }
      return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    },
    handlePrint() {
      window.print();
    },
  },
};
</script>

<style scoped>
.prescription-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(15, 23, 42, 0.7);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15px;
  backdrop-filter: blur(4px);
}

.prescription-modal-dialog {
  background: #ffffff;
  width: 100%;
  max-width: 900px;
  max-height: 94vh;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.prescription-scroll-container {
  overflow-y: auto;
  max-height: calc(94vh - 75px);
}

.prescription-pad {
  width: 100%;
  max-width: 800px;
  padding: 35px 35px 25px 35px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  box-sizing: border-box;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #1e293b;
}

.pad-body {
  min-height: 660px;
}

.bg-primary-soft { background-color: #e0f2fe; }
.bg-danger-soft { background-color: #ffe4e6; }
.bg-success-soft { background-color: #f0fdf4; }
.text-navy { color: #0f172a; }

.section-title {
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* ============================================================== */
/* MOBILE RESPONSIVE PREVIEW                                      */
/* ============================================================== */
@media screen and (max-width: 768px) {
  .prescription-modal-backdrop {
    padding: 0 !important;
  }

  .prescription-modal-dialog {
    max-height: 100vh !important;
    height: 100vh !important;
    border-radius: 0 !important;
  }

  .prescription-scroll-container {
    max-height: calc(100vh - 110px) !important;
    padding: 10px 8px !important;
  }

  .prescription-pad {
    padding: 18px 14px 16px 14px !important;
    border-radius: 6px !important;
  }

  .pad-body {
    min-height: auto !important;
  }

  .left-clinical-col {
    border-right: none !important;
    border-bottom: 1px dashed #cbd5e1 !important;
    padding-right: 0 !important;
    padding-bottom: 16px !important;
  }

  .right-medicine-col {
    padding-left: 0 !important;
  }

  .modal-action-btns {
    width: 100%;
  }

  .modal-action-btns .btn {
    flex: 1 1 auto;
    font-size: 12px;
    padding: 7px 8px;
    justify-content: center;
    border-radius: 8px;
  }
}

@media screen and (max-width: 576px) {
  .patient-bar-item {
    font-size: 11.5px !important;
    word-break: break-word !important;
    padding: 2px 5px !important;
  }
  .doctor-name {
    font-size: 16px !important;
  }
  .chamber-meta-col {
    text-align: left !important;
    margin-top: 8px !important;
  }
  .footer-notes-col {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
    margin-bottom: 15px !important;
  }
  .footer-sig-col {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
    text-align: center !important;
  }
  .pad-header {
    padding-bottom: 10px !important;
    margin-bottom: 10px !important;
  }
}

/* ============================================================== */
/* PRINT MEDIA STYLES: STRICT ORIGINAL A4 LAYOUT                  */
/* ============================================================== */
@media print {
  body * {
    visibility: hidden;
  }

  .prescription-modal-backdrop {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    padding: 0 !important;
    background: transparent !important;
    backdrop-filter: none !important;
    width: 100% !important;
    height: auto !important;
    display: block !important;
  }

  .prescription-modal-dialog {
    max-width: 100% !important;
    max-height: 100% !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    border: none !important;
    overflow: visible !important;
  }

  .prescription-scroll-container {
    overflow: visible !important;
    max-height: none !important;
    padding: 0 !important;
    background: transparent !important;
  }

  #printable-prescription-pad,
  #printable-prescription-pad * {
    visibility: visible;
  }

  #printable-prescription-pad {
    position: absolute;
    left: 0;
    top: 0;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 10mm 15mm !important;
    border: none !important;
    box-shadow: none !important;
  }

  .doctor-meta-col {
    width: 58.333333% !important;
    max-width: 58.333333% !important;
    flex: 0 0 58.333333% !important;
    float: left !important;
  }

  .chamber-meta-col {
    width: 41.666667% !important;
    max-width: 41.666667% !important;
    flex: 0 0 41.666667% !important;
    float: right !important;
    text-align: right !important;
  }

  .patient-bar-item {
    width: 25% !important;
    max-width: 25% !important;
    flex: 0 0 25% !important;
    float: left !important;
  }

  .pad-body {
    display: flex !important;
    flex-direction: row !important;
    min-height: 660px !important;
  }

  .left-clinical-col {
    width: 33.333333% !important;
    max-width: 33.333333% !important;
    flex: 0 0 33.333333% !important;
    border-right: 1px solid #dee2e6 !important;
    border-bottom: none !important;
    padding-right: 15px !important;
    margin-bottom: 0 !important;
  }

  .right-medicine-col {
    width: 66.666667% !important;
    max-width: 66.666667% !important;
    flex: 0 0 66.666667% !important;
    padding-left: 20px !important;
  }

  .footer-notes-col {
    width: 66.666667% !important;
    max-width: 66.666667% !important;
    flex: 0 0 66.666667% !important;
  }

  .footer-sig-col {
    width: 33.333333% !important;
    max-width: 33.333333% !important;
    flex: 0 0 33.333333% !important;
    text-align: center !important;
  }

  .pad-footer {
    margin-top: 65mm !important;
  }

  .no-print {
    display: none !important;
  }

  @page {
    size: A4 portrait;
    margin: 8mm 10mm;
  }
}
</style>

