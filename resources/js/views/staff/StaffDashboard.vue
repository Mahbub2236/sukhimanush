<template>
  <div class="staff-dashboard bg-light min-vh-100 py-4">
    <div class="container">
      <!-- Top Banner / Header -->
      <div class="card border-0 shadow-sm rounded-lg mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center mb-3 mb-md-0">
              <div class="avatar-box bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 58px; height: 58px; font-size: 26px;">
                <i class="icofont-users-alt-5"></i>
              </div>
              <div>
                <div class="d-flex align-items-center flex-wrap">
                  <h4 class="font-weight-bold text-dark mb-0 mr-2">{{ assistant.name }}</h4>
                  <span class="badge badge-success px-2 py-1">সহকারী / কম্পাউন্ডার পোর্টাল</span>
                </div>
                <p class="text-muted mb-0 small mt-1">
                  <i class="icofont-doctor-alt text-primary mr-1"></i>
                  <strong>ডাক্তার:</strong> {{ doctor.name }} ({{ doctor.specialization }}) | 
                  <i class="icofont-location-pin text-danger ml-2 mr-1"></i>
                  <span>{{ doctor.chamber_address || 'চেম্বার কাউন্টার' }}</span>
                </p>
              </div>
            </div>

            <div class="d-flex align-items-center" style="gap: 10px;">
              <div class="text-right d-none d-md-block mr-2">
                <span class="d-block text-muted small">আজকের তারিখ</span>
                <strong class="text-dark">{{ todayFormatted }}</strong>
              </div>
              <button class="btn btn-outline-danger btn-sm font-weight-bold px-3 py-2 btn-round-full" @click="handleLogout">
                <i class="icofont-logout mr-1"></i> লগআউট
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Live Token Call Alert Banner -->
      <div v-if="stats.called_patient_name" class="alert alert-warning border-warning shadow-sm rounded-lg p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center">
          <span class="badge badge-danger p-2 mr-3 font-weight-bold" style="font-size: 16px;">
            <i class="icofont-megaphone mr-1"></i> এখন ডাক পড়েছে: সিরিয়াল #{{ stats.current_serving_serial }}
          </span>
          <span class="font-weight-bold text-dark" style="font-size: 16px;">
            {{ stats.called_patient_name }}
          </span>
        </div>
        <span class="text-muted small">চেম্বারে প্রবেশের জন্য প্রস্তুত রাখা হচ্ছে</span>
      </div>

      <!-- KPI Summary Cards -->
      <div class="row mb-4">
        <div class="col-6 col-lg-3 mb-3">
          <div class="card border-0 shadow-sm rounded-lg text-center p-3 bg-white h-100 border-left border-primary" style="border-left-width: 4px !important;">
            <span class="text-muted small font-weight-bold d-block">মোট সিরিয়াল (আজকে)</span>
            <h2 class="font-weight-bold text-primary mb-0 mt-1">{{ stats.total_serials }}</h2>
          </div>
        </div>

        <div class="col-6 col-lg-3 mb-3">
          <div class="card border-0 shadow-sm rounded-lg text-center p-3 bg-white h-100 border-left border-danger" style="border-left-width: 4px !important;">
            <span class="text-muted small font-weight-bold d-block">বর্তমান টোকেন #</span>
            <h2 class="font-weight-bold text-danger mb-0 mt-1">#{{ stats.current_serving_serial }}</h2>
          </div>
        </div>

        <div class="col-6 col-lg-3 mb-3">
          <div class="card border-0 shadow-sm rounded-lg text-center p-3 bg-white h-100 border-left border-warning" style="border-left-width: 4px !important;">
            <span class="text-muted small font-weight-bold d-block">অপেক্ষমাণ রোগী</span>
            <h2 class="font-weight-bold text-warning mb-0 mt-1">{{ stats.waiting_count }}</h2>
          </div>
        </div>

        <div class="col-6 col-lg-3 mb-3">
          <div class="card border-0 shadow-sm rounded-lg text-center p-3 bg-white h-100 border-left border-success" style="border-left-width: 4px !important;">
            <span class="text-muted small font-weight-bold d-block">সম্পন্ন হয়েছে</span>
            <h2 class="font-weight-bold text-success mb-0 mt-1">{{ stats.completed_count }}</h2>
          </div>
        </div>
      </div>

      <!-- Action Buttons Bar -->
      <div class="card border-0 shadow-sm rounded-lg mb-4 bg-white p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
          <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            <!-- Call Next Patient (Compounder Main Tool) -->
            <button 
              class="btn btn-danger font-weight-bold px-4 py-2 shadow-sm btn-round-full"
              :disabled="callingNext || stats.waiting_count === 0"
              @click="callNextPatient"
              style="min-height: 44px; font-size: 15px;"
            >
              <span v-if="callingNext" class="spinner-border spinner-border-sm mr-1"></span>
              <i v-else class="icofont-megaphone mr-1 font-18"></i>
              পরবর্তী রোগী ডাকুন (Call Next Token)
            </button>

            <!-- Emergency Bypass Button for Compounder / Assistant -->
            <button 
              v-if="!isEmergencyActive"
              class="btn btn-warning font-weight-bold px-3 py-2 btn-round-full shadow-sm text-dark d-inline-flex align-items-center"
              @click="openEmergencyBypassModal"
              style="min-height: 44px;"
              title="আশঙ্কাজনক জরুরি রোগীর জন্য নিয়মিত সিরিয়াল সাময়িক হোল্ড করুন"
            >
              <i class="icofont-ambulance-cross mr-1 text-danger font-18"></i>
              🚨 জরুরি রোগী প্রবেশ (Emergency)
            </button>
            <button 
              v-else
              class="btn btn-success font-weight-bold px-3 py-2 btn-round-full shadow-sm d-inline-flex align-items-center"
              @click="stopEmergencyBypass"
              style="min-height: 44px;"
              title="জরুরি সেবা সমাপ্ত করে নিয়মিত সিরিয়ালে ফিরুন"
            >
              <i class="icofont-check-circled mr-1 font-18"></i>
              ✅ জরুরি সম্পন্ন (স্বাভাবিক সিরিয়াল)
            </button>

            <!-- Doctor Presence Status Selector -->
            <div class="d-inline-flex align-items-center" style="gap: 6px;">
              <select v-model="doctorStatus" class="form-control form-control-sm font-weight-bold rounded-pill text-dark border-primary" style="height: 40px;" @change="setDoctorStatus(doctorStatus)">
                <option value="consultation_started">✅ চেম্বারে উপস্থিত (রোগী দেখছেন)</option>
                <option value="on_the_way">🚗 ডাক্তার পথে আছেন</option>
                <option value="paused">☕ সাময়িক বিরতি (Pause)</option>
                <option value="not_arrived">⏳ এখনো পৌঁছাননি</option>
                <option value="completed">🏁 আজকের চেম্বার শেষ</option>
              </select>
            </div>

            <!-- Walk-in Patient Entry -->
            <button 
              class="btn btn-primary font-weight-bold px-3 py-2 btn-round-full shadow-sm"
              @click="showWalkinModal = true"
              style="min-height: 44px;"
            >
              <i class="icofont-plus-circle mr-1"></i>
              চেম্বারে নতুন রোগী এন্ট্রি (Walk-in Serial)
            </button>
          </div>

          <div class="d-flex align-items-center" style="gap: 8px;">
            <button class="btn btn-light btn-sm font-weight-bold px-3 py-2 border rounded-pill" @click="fetchDashboard">
              <i class="icofont-refresh mr-1"></i> রিফ্রেশ
            </button>
          </div>
        </div>
      </div>

      <!-- Emergency Active Alert Banner for Staff -->
      <div v-if="isEmergencyActive" class="card border-0 shadow-sm rounded-lg mb-4 bg-danger text-white p-3" style="border-left: 6px solid #fef08a !important;">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="d-flex align-items-center">
            <span class="mr-2 font-22">🚨</span>
            <div>
              <strong class="font-16">চেম্বারে জরুরি রোগীর চিকিৎসা চলমান (Emergency Bypass Active)</strong>
              <div class="small opacity-90 mt-1">
                <span v-if="emergencyPatientName">রোগী: <strong>{{ emergencyPatientName }}</strong></span>
                <span class="ml-2" v-if="emergencyReason">| কারণ: {{ emergencyReason }}</span>
                <span class="ml-2">| রোগীদের লাইভ মনিটরে সাময়িক বিলম্ব নোটিশ পাঠানো হয়েছে।</span>
              </div>
            </div>
          </div>
          <button class="btn btn-light btn-sm font-weight-bold text-danger rounded-pill px-3 shadow-sm mt-2 mt-md-0" @click="stopEmergencyBypass">
            ✅ জরুরি চিকিৎসা সমাপ্ত করুন
          </button>
        </div>
      </div>

      <!-- Today's Queue Management Table -->
      <div class="card border-0 shadow-sm rounded-lg bg-white overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap border-bottom">
          <div>
            <h5 class="font-weight-bold text-dark mb-0">
              <i class="icofont-listine-dots text-primary mr-1"></i>
              আজকের রোগীর তালিকা ও লাইভ সিরিয়াল কিউ (Queue List)
            </h5>
            <small class="text-muted">রোগীর টোকেন কল করুন, চেম্বারে পাঠান এবং টেস্ট রিপোর্ট আপলোড করুন</small>
          </div>

          <div class="mt-2 mt-md-0" style="min-width: 240px;">
            <input 
              type="text" 
              v-model="searchQuery" 
              class="form-control form-control-sm rounded-pill" 
              placeholder="🔍 নাম, মোবাইল বা সিরিয়াল দিয়ে খুঁজুন..."
            >
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="thead-light">
              <tr>
                <th class="text-center" style="width: 90px;">সিরিয়াল</th>
                <th>রোগীর নাম ও তথ্য</th>
                <th>মোবাইল নম্বর</th>
                <th>সময় ও ধরন</th>
                <th>স্ট্যাটাস</th>
                <th class="text-right">একশন</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredAppointments.length === 0">
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="icofont-exclamation-circle display-4 d-block mb-2 text-muted"></i>
                  কোনো রোগী পাওয়া যায়নি।
                </td>
              </tr>

              <tr 
                v-for="apt in filteredAppointments" 
                :key="apt.id"
                :class="{ 'table-warning font-weight-bold': apt.is_called && apt.status !== 'completed' }"
              >
                <!-- Serial Number -->
                <td class="text-center">
                  <span 
                    class="badge badge-pill px-3 py-2 font-weight-bold" 
                    :class="apt.is_called && apt.status !== 'completed' ? 'badge-danger' : (apt.status === 'completed' ? 'badge-secondary' : 'badge-primary')"
                    style="font-size: 14px;"
                  >
                    #{{ apt.serial_number || 1 }}
                  </span>
                </td>

                <!-- Patient Name -->
                <td>
                  <div class="font-weight-bold text-dark">{{ apt.patient_name }}</div>
                  <div class="small text-muted">
                    {{ apt.reason_for_visit || 'পরামর্শের জন্য সাক্ষাত' }}
                  </div>
                </td>

                <!-- Phone -->
                <td>
                  <a :href="'tel:' + apt.patient_phone" class="text-dark font-weight-bold">
                    <i class="icofont-phone text-success mr-1"></i> {{ apt.patient_phone }}
                  </a>
                </td>

                <!-- Time & Type -->
                <td>
                  <div><i class="icofont-clock-time text-primary mr-1"></i>{{ apt.appointment_time }}</div>
                  <span v-if="apt.consultation_type === 'online_video'" class="badge badge-info">অনলাইন ভিডিও</span>
                  <span v-else class="badge badge-light border">চেম্বার সাক্ষাৎ</span>
                </td>

                <!-- Status -->
                <td>
                  <span v-if="apt.status === 'completed'" class="badge badge-success px-2 py-1">
                    <i class="icofont-check mr-1"></i> সম্পন্ন
                  </span>
                  <span v-else-if="apt.is_called" class="badge badge-danger px-2 py-1 animate-pulse">
                    <i class="icofont-megaphone mr-1"></i> ডাক পড়েছে
                  </span>
                  <span v-else-if="apt.status === 'confirmed'" class="badge badge-primary px-2 py-1">
                    অপেক্ষমাণ
                  </span>
                  <span v-else class="badge badge-warning px-2 py-1">
                    {{ apt.status }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="text-right">
                  <div class="btn-group btn-group-sm" role="group">
                    <!-- Call Specific Patient -->
                    <button 
                      v-if="apt.status !== 'completed'" 
                      class="btn btn-outline-danger" 
                      title="এই রোগীকে ডাকুন"
                      @click="callSpecificPatient(apt)"
                    >
                      <i class="icofont-bullhorn mr-1"></i> ডাকুন
                    </button>

                    <!-- Mark as In-Chamber / Confirmed -->
                    <button 
                      v-if="apt.status !== 'completed'" 
                      class="btn btn-outline-primary" 
                      title="চেম্বারে প্রবেশ নিশ্চিত করুন"
                      @click="markStatus(apt.id, 'confirmed')"
                    >
                      <i class="icofont-stethoscope"></i>
                    </button>

                    <!-- Mark as Completed -->
                    <button 
                      v-if="apt.status !== 'completed'" 
                      class="btn btn-outline-success" 
                      title="দেখা সম্পন্ন হয়েছে"
                      @click="markStatus(apt.id, 'completed')"
                    >
                      <i class="icofont-check"></i>
                    </button>

                    <!-- Upload Diagnostic Report -->
                    <button 
                      class="btn btn-outline-secondary" 
                      title="রিপোর্ট আপলোড করুন"
                      @click="openReportModal(apt)"
                    >
                      <i class="icofont-upload-alt"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- WALK-IN PATIENT MODAL -->
    <div v-if="showWalkinModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1060;">
      <div class="modal-dialog modal-dialog-centered w-100" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg rounded-lg">
          <div class="modal-header bg-primary text-white py-3 px-4">
            <h5 class="modal-title font-weight-bold text-white mb-0">
              <i class="icofont-plus-circle mr-1"></i> চেম্বার ওয়াক-ইন রোগীর সিরিয়াল এন্ট্রি
            </h5>
            <button type="button" class="close text-white" @click="closeWalkinModal">&times;</button>
          </div>
          <form @submit.prevent="submitWalkin">
            <div class="modal-body p-4 bg-white">
              <!-- Patient Name with Voice input -->
              <div class="form-group mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="font-weight-bold text-dark small mb-0">রোগীর পুরো নাম <span class="text-danger">*</span></label>
                  <button 
                    type="button" 
                    class="btn btn-xs py-0 px-2 rounded-pill font-weight-bold"
                    :class="isListeningWalkin && walkinVoiceField === 'patient_name' ? 'btn-danger animate-pulse-mic' : 'btn-outline-secondary'"
                    style="font-size: 11px;"
                    @click="toggleWalkinVoice('patient_name')"
                  >
                    <i :class="isListeningWalkin && walkinVoiceField === 'patient_name' ? 'icofont-stop' : 'icofont-mic'" class="mr-1"></i>
                    <span>{{ isListeningWalkin && walkinVoiceField === 'patient_name' ? 'থামান' : '🎙️ মুখে নাম বলুন' }}</span>
                  </button>
                </div>

                <!-- Live Voice Feedback -->
                <div v-if="isListeningWalkin && walkinVoiceField === 'patient_name'" class="alert alert-danger py-1 px-2 mb-2 small d-flex align-items-center justify-content-between rounded border-danger" style="font-size: 12px;">
                  <div class="d-flex align-items-center text-truncate">
                    <span class="spinner-grow spinner-grow-sm text-danger mr-2" style="width: 10px; height: 10px;"></span>
                    <strong class="text-danger mr-1">🔴 শুনছি:</strong>
                    <span class="text-dark">{{ walkinInterimText || 'রোগীর নাম বাংলায় বলুন...' }}</span>
                  </div>
                  <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 ml-1" @click="toggleWalkinVoice('patient_name')">থামান</button>
                </div>

                <input 
                  type="text" 
                  v-model="walkinForm.patient_name" 
                  data-no-voice
                  class="form-control" 
                  placeholder="যেমন: মো: আবুল বাশার" 
                  required
                >
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold text-dark small">মোবাইল নম্বর <span class="text-danger">*</span></label>
                <input 
                  type="tel" 
                  v-model="walkinForm.patient_phone" 
                  data-no-voice
                  class="form-control" 
                  placeholder="যেমন: 01712345678" 
                  required
                >
              </div>

              <!-- Reason for Visit with High Accuracy Bangla Voice Input -->
              <div class="form-group mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="font-weight-bold text-dark small mb-0">
                    রোগীর প্রধান সমস্যা / কারণ
                  </label>
                  <button 
                    type="button" 
                    class="btn btn-xs py-1 px-2 rounded-pill font-weight-bold shadow-sm d-inline-flex align-items-center"
                    :class="isListeningWalkin && walkinVoiceField === 'reason_for_visit' ? 'btn-danger animate-pulse-mic' : 'btn-outline-danger'"
                    style="font-size: 11px;"
                    @click="toggleWalkinVoice('reason_for_visit')"
                  >
                    <i :class="isListeningWalkin && walkinVoiceField === 'reason_for_visit' ? 'icofont-stop' : 'icofont-mic'" class="mr-1"></i>
                    <span>{{ isListeningWalkin && walkinVoiceField === 'reason_for_visit' ? 'রেকর্ডিং বন্ধ করুন' : '🎙️ বাংলায় মুখে বলুন (Voice)' }}</span>
                  </button>
                </div>

                <!-- Live Voice Feedback -->
                <div v-if="isListeningWalkin && walkinVoiceField === 'reason_for_visit'" class="alert alert-danger py-1 px-2 mb-2 small d-flex align-items-center justify-content-between rounded border-danger shadow-sm" style="font-size: 12px;">
                  <div class="d-flex align-items-center text-truncate">
                    <span class="spinner-grow spinner-grow-sm text-danger mr-2" style="width: 10px; height: 10px;"></span>
                    <strong class="text-danger mr-1">🔴 শুনছি:</strong>
                    <span class="text-dark">{{ walkinInterimText || 'রোগীর সমস্যার কথা স্পষ্ট করে বাংলায় বলুন...' }}</span>
                  </div>
                  <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 ml-1" @click="toggleWalkinVoice('reason_for_visit')">থামান</button>
                </div>

                <input 
                  type="text" 
                  v-model="walkinForm.reason_for_visit" 
                  data-no-voice
                  class="form-control" 
                  placeholder="যেমন: তীব্র মাথা ব্যথা ও জ্বর (মুখে বলতে পারেন)"
                >
                <small class="text-muted mt-1 d-block">
                  <i class="icofont-info-circle mr-1 text-primary"></i> রোগী যে সমস্যা বলবেন তা বাংলায় মুখে বললেই নির্ভুলভাবে এখানে লেখা হয়ে যাবে।
                </small>
              </div>

              <div class="p-2 bg-light border rounded text-muted small">
                <i class="icofont-info-circle text-primary mr-1"></i>
                এই সিরিয়ালটি আজকের দিনের পরবর্তী নম্বর হিসেবে সরাসরি কিউ-তে যুক্ত হবে।
              </div>
            </div>

            <div class="modal-footer bg-light py-2 px-4 justify-content-between">
              <button type="button" class="btn btn-secondary btn-sm rounded-pill" @click="closeWalkinModal">বাতিল</button>
              <button type="submit" class="btn btn-primary btn-sm font-weight-bold rounded-pill px-4" :disabled="submittingWalkin">
                <span v-if="submittingWalkin" class="spinner-border spinner-border-sm mr-1"></span>
                সিরিয়াল নিশ্চিত করুন
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- UPLOAD REPORT MODAL -->
    <div v-if="showReportModal" class="custom-modal-backdrop d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.65); z-index: 1060;">
      <div class="modal-dialog modal-dialog-centered w-100" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg rounded-lg">
          <div class="modal-header bg-secondary text-white py-3 px-4">
            <h5 class="modal-title font-weight-bold text-white mb-0">
              <i class="icofont-upload-alt mr-1"></i> রোগীর ডায়াগনস্টিক রিপোর্ট আপলোড
            </h5>
            <button type="button" class="close text-white" @click="showReportModal = false">&times;</button>
          </div>
          <form @submit.prevent="submitReportUpload">
            <div class="modal-body p-4 bg-white">
              <div class="mb-3 p-2 bg-light rounded border">
                <strong>রোগী:</strong> {{ selectedAptForReport?.patient_name }} ({{ selectedAptForReport?.patient_phone }})
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold text-dark small">টেস্ট / রিপোর্টের নাম <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  v-model="reportForm.test_name" 
                  class="form-control" 
                  placeholder="যেমন: CBC, Lipid Profile, Chest X-Ray" 
                  required
                >
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold text-dark small">ল্যাব / ডায়াগনস্টিক সেন্টারের নাম</label>
                <input 
                  type="text" 
                  v-model="reportForm.lab_name" 
                  class="form-control" 
                  placeholder="যেমন: Popular Diagnostic Center"
                >
              </div>

              <div class="form-group mb-3">
                <label class="font-weight-bold text-dark small">রিপোর্ট ফাইল (PDF / Image) <span class="text-danger">*</span></label>
                <input 
                  type="file" 
                  ref="reportFileInput" 
                  class="form-control-file border p-2 rounded w-100" 
                  accept=".pdf,.jpg,.jpeg,.png" 
                  required
                >
              </div>
            </div>

            <div class="modal-footer bg-light py-2 px-4 justify-content-between">
              <button type="button" class="btn btn-secondary btn-sm rounded-pill" @click="showReportModal = false">বাতিল</button>
              <button type="submit" class="btn btn-success btn-sm font-weight-bold rounded-pill px-4" :disabled="uploadingReport">
                <span v-if="uploadingReport" class="spinner-border spinner-border-sm mr-1"></span>
                আপলোড করুন
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'StaffDashboard',
  data() {
    return {
      assistant: {},
      doctor: {},
      doctorStatus: 'consultation_started',
      stats: {
        total_serials: 0,
        completed_count: 0,
        waiting_count: 0,
        current_serving_serial: 1,
        called_patient_name: null,
      },
      appointments: [],
      searchQuery: '',
      callingNext: false,
      showWalkinModal: false,
      submittingWalkin: false,
      walkinForm: {
        patient_name: '',
        patient_phone: '',
        reason_for_visit: '',
      },
      // Bangla Voice Dictation for Walk-in Modal
      walkinVoiceField: 'reason_for_visit',
      isListeningWalkin: false,
      walkinInterimText: '',
      walkinRecognition: null,
      showReportModal: false,
      selectedAptForReport: null,
      uploadingReport: false,
      reportForm: {
        test_name: '',
        lab_name: '',
      },
      isEmergencyActive: false,
      emergencyReason: '',
      emergencyPatientName: '',
    };
  },
  computed: {
    todayFormatted() {
      return new Date().toLocaleDateString('bn-BD', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
    filteredAppointments() {
      if (!this.searchQuery) return this.appointments;
      const q = this.searchQuery.toLowerCase();
      return this.appointments.filter(apt => 
        (apt.patient_name && apt.patient_name.toLowerCase().includes(q)) ||
        (apt.patient_phone && apt.patient_phone.includes(q)) ||
        (apt.serial_number && apt.serial_number.toString().includes(q))
      );
    },
  },
  mounted() {
    const userStr = localStorage.getItem('saas_user');
    if (!userStr) {
      this.$router.push('/login');
      return;
    }
    const user = JSON.parse(userStr);
    if (user.role !== 'staff') {
      this.$router.push('/login');
      return;
    }

    const asstStr = localStorage.getItem('saas_assistant');
    if (asstStr) {
      this.assistant = JSON.parse(asstStr);
    } else {
      this.assistant = { id: user.assistant_id || user.id, name: user.name, phone: user.phone };
    }

    const docStr = localStorage.getItem('saas_doctor');
    if (docStr) {
      this.doctor = JSON.parse(docStr);
    }

    this.fetchDashboard();
  },
  methods: {
    async fetchDashboard() {
      try {
        const assistantId = this.assistant.id;
        const res = await axios.get(`/staff-portal/dashboard/${assistantId}`);
        if (res.data.success) {
          this.stats = res.data.stats;
          this.appointments = res.data.appointments;
          this.doctor = res.data.doctor;
          this.assistant = res.data.assistant;
          this.isEmergencyActive = !!res.data.stats?.is_emergency_active;
          this.emergencyReason = res.data.stats?.emergency_reason || '';
          this.emergencyPatientName = res.data.stats?.emergency_patient_name || '';
        }
      } catch (err) {
        console.error('Failed to load staff dashboard:', err);
      }
    },
    async openEmergencyBypassModal() {
      if (!this.doctor?.id) return;
      const { value: formValues } = await Swal.fire({
        title: '🚨 জরুরি রোগী প্রবেশ (Emergency Bypass)',
        html: `
          <div class="text-left small mb-3 text-muted">
            চেম্বারে কোনো আশঙ্কাজনক জরুরি রোগী এলে এই অপশনটি দিন। 
            নিয়মিত সিরিয়াল নম্বর ঠিক থাকবে এবং সকল অপেক্ষমাণ রোগীর ট্র্যাকিং স্ক্রিনে স্বয়ংক্রিয়ভাবে জরুরি অ্যালার্ট ও বিলম্ব বার্তা প্রদর্শিত হবে।
          </div>
          <div class="text-left mb-2">
            <label class="small font-weight-bold">রোগীর নাম (ঐচ্ছিক):</label>
            <input id="swal-staff-patient" class="swal2-input m-0 w-100" placeholder="যেমন: জরুরি রোগী">
          </div>
          <div class="text-left mb-2">
            <label class="small font-weight-bold">জরুরি কারণ (ঐচ্ছিক):</label>
            <input id="swal-staff-reason" class="swal2-input m-0 w-100" placeholder="যেমন: তীব্র বুকে ব্যথা / শ্বাসকষ্ট">
          </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: '🚨 জরুরি মোড চালু করুন',
        cancelButtonText: 'বাতিল',
        confirmButtonColor: '#dc2626',
        preConfirm: () => {
          return {
            patient_name: document.getElementById('swal-staff-patient')?.value || '',
            emergency_reason: document.getElementById('swal-staff-reason')?.value || ''
          };
        }
      });

      if (formValues) {
        try {
          const res = await axios.post(`/queue/emergency-bypass/${this.doctor.id}`, {
            action: 'start',
            patient_name: formValues.patient_name,
            emergency_reason: formValues.emergency_reason
          });
          if (res.data.success) {
            this.isEmergencyActive = true;
            this.emergencyPatientName = formValues.patient_name;
            this.emergencyReason = formValues.emergency_reason;
            Swal.fire({
              icon: 'warning',
              title: '🚨 জরুরি মোড সক্রিয়!',
              text: res.data.message,
              confirmButtonColor: '#dc2626'
            });
            this.fetchDashboard();
          }
        } catch (err) {
          Swal.fire('Error', 'জরুরি মোড সক্রিয় করতে সমস্যা হয়েছে।', 'error');
        }
      }
    },
    async stopEmergencyBypass() {
      if (!this.doctor?.id) return;
      const result = await Swal.fire({
        title: 'জরুরি সেবা সমাপ্ত?',
        text: 'জরুরি রোগীর চিকিৎসা শেষ করে কি আপনি নিয়মিত সিরিয়ালে ফিরতে চান?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'হ্যাঁ, নিয়মিত সিরিয়ালে ফিরুন',
        cancelButtonText: 'বাতিল',
        confirmButtonColor: '#16a34a'
      });

      if (result.isConfirmed) {
        try {
          const res = await axios.post(`/queue/emergency-bypass/${this.doctor.id}`, {
            action: 'stop'
          });
          if (res.data.success) {
            this.isEmergencyActive = false;
            this.emergencyPatientName = '';
            this.emergencyReason = '';
            Swal.fire({
              icon: 'success',
              title: 'সফল!',
              text: res.data.message,
              confirmButtonColor: '#16a34a'
            });
            this.fetchDashboard();
          }
        } catch (err) {
          Swal.fire('Error', 'নিয়মিত সিরিয়ালে ফিরতে সমস্যা হয়েছে।', 'error');
        }
      }
    },
    async callNextPatient() {
      if (this.isEmergencyActive) {
        const resumeConfirm = await Swal.fire({
          icon: 'warning',
          title: 'জরুরি রোগী মোড চালু রয়েছে!',
          text: 'চেম্বারে বর্তমানে জরুরি রোগী চিকিৎসাধীন। আপনি কি জরুরি মোড বন্ধ করে নিয়মিত সিরিয়ালে পরবর্তী রোগীকে ডাকতে চান?',
          showCancelButton: true,
          confirmButtonText: 'হ্যাঁ, নিয়মিত রোগী ডাকুন',
          cancelButtonText: 'বাতিল',
          confirmButtonColor: '#e12454'
        });
        if (!resumeConfirm.isConfirmed) return;

        await axios.post(`/queue/emergency-bypass/${this.doctor.id}`, { action: 'stop' });
        this.isEmergencyActive = false;
      }
      this.callingNext = true;
      try {
        const assistantId = this.assistant.id;
        const res = await axios.post(`/staff-portal/queue/call-next/${assistantId}`);
        if (res.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'টোকেন কল করা হয়েছে!',
            text: res.data.message,
            timer: 3000,
            showConfirmButton: false,
          });
          this.fetchDashboard();
        } else {
          Swal.fire({
            icon: 'info',
            title: 'তথ্য',
            text: res.data.message,
          });
        }
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'এরর',
          text: err.response?.data?.message || 'রোগী কল করতে সমস্যা হয়েছে।',
        });
      } finally {
        this.callingNext = false;
      }
    },
    async setDoctorStatus(status) {
      this.doctorStatus = status;
      try {
        await axios.post(`/queue/doctor-status/${this.doctor.id}`, {
          doctor_status: status,
        });
        Swal.fire({
          icon: 'success',
          title: 'অবস্থান আপডেট হয়েছে',
          text: 'রোগীদের স্ক্রিনে ডাক্তারের লাইভ স্ট্যাটাস আপডেট হয়েছে।',
          timer: 1800,
          showConfirmButton: false,
        });
      } catch (err) {
        console.error('Failed to update doctor status:', err);
      }
    },
    async skipPatient(apt) {
      try {
        const res = await axios.post(`/queue/skip/${this.doctor.id}/${apt.id}`);
        if (res.data.success) {
          Swal.fire('স্কিপ করা হয়েছে', res.data.message, 'info');
          this.fetchDashboard();
        }
      } catch (err) {
        console.error('Failed to skip patient:', err);
      }
    },
    async callSpecificPatient(apt) {
      try {
        await axios.patch(`/staff-portal/appointments/${apt.id}/status`, {
          status: 'confirmed',
          is_called: true,
        });
        Swal.fire({
          icon: 'success',
          title: 'রোগীকে ডাকা হয়েছে!',
          text: `সিরিয়াল #${apt.serial_number} (${apt.patient_name})-কে ডাকা হয়েছে।`,
          timer: 2500,
          showConfirmButton: false,
        });
        this.fetchDashboard();
      } catch (err) {
        console.error(err);
      }
    },
    async markStatus(id, status) {
      try {
        await axios.patch(`/staff-portal/appointments/${id}/status`, { status });
        this.fetchDashboard();
      } catch (err) {
        console.error(err);
      }
    },
    async submitWalkin() {
      this.submittingWalkin = true;
      if (this.walkinRecognition) {
        try { this.walkinRecognition.stop(); } catch (e) { console.warn(e); }
      }
      this.isListeningWalkin = false;

      try {
        const assistantId = this.assistant.id;
        const res = await axios.post(`/staff-portal/walkin/${assistantId}`, this.walkinForm);
        if (res.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'সিরিয়াল যুক্ত হয়েছে!',
            text: res.data.message,
            timer: 3000,
            showConfirmButton: false,
          });
          this.showWalkinModal = false;
          this.walkinForm = { patient_name: '', patient_phone: '', reason_for_visit: '' };
          this.fetchDashboard();
        }
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'ব্যর্থ হয়েছে',
          text: err.response?.data?.message || 'সিরিয়াল যুক্ত করা যায়নি।',
        });
      } finally {
        this.submittingWalkin = false;
      }
    },
    closeWalkinModal() {
      if (this.walkinRecognition) {
        try { this.walkinRecognition.stop(); } catch (e) { console.warn(e); }
      }
      this.isListeningWalkin = false;
      this.walkinInterimText = '';
      this.showWalkinModal = false;
    },
    toggleWalkinVoice(fieldName) {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      if (!SpeechRecognition) {
        Swal.fire({
          icon: 'warning',
          title: 'ভয়েস ইনপুট পাওয়া যায়নি',
          text: 'আপনার ব্রাউজারে স্পিচ রিকগনিশন সাপোর্ট নেই। অনুগ্রহ করে Google Chrome বা Microsoft Edge ব্যবহার করুন।',
        });
        return;
      }

      if (this.isListeningWalkin) {
        const wasSameField = this.walkinVoiceField === fieldName;
        if (this.walkinRecognition) {
          try { this.walkinRecognition.stop(); } catch (e) { console.warn(e); }
        }
        this.isListeningWalkin = false;
        this.walkinInterimText = '';
        if (wasSameField) {
          return;
        }
      }

      this.walkinVoiceField = fieldName;

      try {
        const recognition = new SpeechRecognition();
        recognition.lang = 'bn-BD'; // High accuracy native Bangla
        recognition.continuous = true;
        recognition.interimResults = true;

        recognition.onstart = () => {
          this.isListeningWalkin = true;
          this.walkinInterimText = '';
        };

        recognition.onresult = (event) => {
          let finalChunk = '';
          let interimChunk = '';

          for (let i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) {
              finalChunk += event.results[i][0].transcript;
            } else {
              interimChunk += event.results[i][0].transcript;
            }
          }

          this.walkinInterimText = interimChunk;

          if (finalChunk) {
            const current = (this.walkinForm[this.walkinVoiceField] || '').trim();
            this.walkinForm[this.walkinVoiceField] = current ? (current + ' ' + finalChunk.trim()) : finalChunk.trim();
            this.walkinInterimText = '';
          }
        };

        recognition.onerror = (event) => {
          console.error('Speech recognition error:', event.error);
          this.isListeningWalkin = false;
          this.walkinInterimText = '';
          if (event.error === 'not-allowed') {
            Swal.fire({
              icon: 'error',
              title: 'মাইক্রোফোন পারমিশন দিন',
              text: 'ব্রাউজারের অ্যাড্রেস বার থেকে মাইক্রোফোন পারমিশন Allow করে দিন।',
            });
          }
        };

        recognition.onend = () => {
          this.isListeningWalkin = false;
          this.walkinInterimText = '';
        };

        this.walkinRecognition = recognition;
        recognition.start();
      } catch (err) {
        console.error('Walkin voice error:', err);
        this.isListeningWalkin = false;
      }
    },
    openReportModal(apt) {
      this.selectedAptForReport = apt;
      this.reportForm = { test_name: '', lab_name: '' };
      this.showReportModal = true;
    },
    async submitReportUpload() {
      const fileInput = this.$refs.reportFileInput;
      if (!fileInput || !fileInput.files[0]) {
        alert('অনুগ্রহ করে একটি রিপোর্ট ফাইল নির্বাচন করুন');
        return;
      }

      this.uploadingReport = true;
      const formData = new FormData();
      formData.append('report_file', fileInput.files[0]);
      formData.append('test_name', this.reportForm.test_name);
      formData.append('lab_name', this.reportForm.lab_name || 'Chamber Lab');
      formData.append('patient_id', this.selectedAptForReport.patient_id || '');
      formData.append('appointment_id', this.selectedAptForReport.id);
      formData.append('doctor_id', this.doctor.id);

      try {
        await axios.post('/doctor-portal/reports/upload', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        Swal.fire({
          icon: 'success',
          title: 'আপলোড সম্পন্ন!',
          text: 'রোগীর রিপোর্ট সফলভাবে যুক্ত হয়েছে। ডাক্তার এটি দেখতে পারবেন।',
        });
        this.showReportModal = false;
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'আপলোড ব্যর্থ',
          text: err.response?.data?.message || 'ফাইল আপলোড করতে সমস্যা হয়েছে।',
        });
      } finally {
        this.uploadingReport = false;
      }
    },
    handleLogout() {
      localStorage.removeItem('saas_token');
      localStorage.removeItem('saas_user');
      localStorage.removeItem('saas_assistant');
      localStorage.removeItem('saas_doctor');
      window.dispatchEvent(new Event('auth-change'));
      this.$router.push('/login');
    },
  },
  beforeUnmount() {
    if (this.walkinRecognition) {
      try { this.walkinRecognition.stop(); } catch (e) { console.warn(e); }
    }
  },
};
</script>

<style scoped>
.avatar-box {
  background: linear-gradient(135deg, #223a66 0%, #e12454 100%);
}
.animate-pulse {
  animation: pulse-red 1.5s infinite;
}
@keyframes pulse-red {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.05); opacity: 0.85; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-pulse-mic {
  animation: pulse-mic-anim 1.3s infinite;
}
@keyframes pulse-mic-anim {
  0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 36, 84, 0.7); }
  70% { transform: scale(1.05); box-shadow: 0 0 0 8px rgba(225, 36, 84, 0); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 36, 84, 0); }
}
</style>
