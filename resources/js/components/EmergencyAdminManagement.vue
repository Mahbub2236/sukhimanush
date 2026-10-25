<template>
  <div class="emergency-admin-management">
    <!-- Sub-tab Navigation Pills -->
    <div class="bg-light p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
      <ul class="nav nav-pills" style="gap: 6px;">
        <li class="nav-item">
          <button 
            class="nav-link font-weight-bold btn-sm rounded-pill px-3" 
            :class="{ 'active bg-danger text-white': subTab === 'ambulances', 'btn-light border text-dark': subTab !== 'ambulances' }"
            @click="subTab = 'ambulances'; fetchAmbulances();"
          >
            <i class="icofont-ambulance-cross mr-1"></i> এম্বুলেন্স ({{ ambulanceTotal }})
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link font-weight-bold btn-sm rounded-pill px-3" 
            :class="{ 'active bg-success text-white': subTab === 'beds', 'btn-light border text-dark': subTab !== 'beds' }"
            @click="subTab = 'beds'; fetchBeds();"
          >
            <i class="icofont-hospital mr-1"></i> আইসিইউ ও বেড ({{ bedTotal }})
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link font-weight-bold btn-sm rounded-pill px-3" 
            :class="{ 'active bg-primary text-white': subTab === 'blood', 'btn-light border text-dark': subTab !== 'blood' }"
            @click="subTab = 'blood'; fetchBloodRequests(); fetchBloodDonors();"
          >
            <i class="icofont-blood-drop mr-1"></i> ব্লাড এসওএস ও ডোনার
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link font-weight-bold btn-sm rounded-pill px-3" 
            :class="{ 'active bg-dark text-white': subTab === 'passports', 'btn-light border text-dark': subTab !== 'passports' }"
            @click="subTab = 'passports'; fetchPassports();"
          >
            <i class="icofont-id-card mr-1"></i> ডিজিটাল হেলথ পাসপোর্ট
          </button>
        </li>
      </ul>

      <!-- Action Buttons per Tab -->
      <div>
        <button v-if="subTab === 'ambulances'" class="btn btn-sm btn-danger font-weight-bold rounded-pill" @click="openAddAmbulanceModal">
          <i class="icofont-plus mr-1"></i> নতুন এম্বুলেন্স যোগ করুন
        </button>
        <button v-if="subTab === 'beds'" class="btn btn-sm btn-success font-weight-bold rounded-pill" @click="openAddBedModal">
          <i class="icofont-plus mr-1"></i> নতুন হাসপাতাল বেড যোগ করুন
        </button>
      </div>
    </div>

    <!-- Alert Messages -->
    <div v-if="successMsg" class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
      <i class="icofont-check-circled mr-1"></i> {{ successMsg }}
      <button type="button" class="close" @click="successMsg = ''">&times;</button>
    </div>

    <!-- SUB-TAB 1: AMBULANCES -->
    <div v-if="subTab === 'ambulances'" class="p-3">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 10px;">
        <div class="input-group input-group-sm" style="max-width: 300px;">
          <input 
            type="text" 
            v-model="ambulanceSearch" 
            class="form-control" 
            placeholder="এম্বুলেন্স, জেলা বা ফোন দিয়ে খুঁজুন..." 
            @keyup.enter="fetchAmbulances"
          >
          <div class="input-group-append">
            <button class="btn btn-danger" @click="fetchAmbulances"><i class="icofont-search"></i></button>
          </div>
        </div>

        <select v-model="ambulanceTypeFilter" class="form-control form-control-sm" style="max-width: 180px;" @change="fetchAmbulances">
          <option value="all">সব ধরনের গাড়ি</option>
          <option value="ac">এসি এম্বুলেন্স</option>
          <option value="non_ac">নন-এসি সাধারণ</option>
          <option value="icu">আইসিইউ সাপোর্ট</option>
          <option value="freezer">ফ্রিজার গাড়ি</option>
        </select>
      </div>

      <div v-if="loadingAmbulances" class="text-center py-5">
        <div class="spinner-border text-danger" role="status"></div>
      </div>
      <div v-else-if="ambulances.length === 0" class="text-center py-5 text-muted bg-white border rounded">
        <i class="icofont-ambulance display-4"></i>
        <p class="mt-2">কোনো এম্বুলেন্স রেকর্ড পাওয়া যায়নি।</p>
      </div>
      <div v-else class="table-responsive bg-white rounded border">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th>আইডি</th>
              <th>সার্ভিসের নাম</th>
              <th>চালক / পরিচালকের নাম</th>
              <th>যোগাযোগের ফোন</th>
              <th>গাড়ির ধরন</th>
              <th>জেলা / উপজেলা</th>
              <th>ভাড়া (বেস / প্রতি কিমি)</th>
              <th>স্ট্যাটাস</th>
              <th class="text-right">অ্যাকশন</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="amb in ambulances" :key="amb.id">
              <td><strong>#{{ amb.id }}</strong></td>
              <td><strong class="text-dark">{{ amb.service_name }}</strong></td>
              <td>{{ amb.driver_or_contact_name }}</td>
              <td><a :href="'tel:' + amb.phone" class="text-primary font-weight-bold"><i class="icofont-phone mr-1"></i>{{ amb.phone }}</a></td>
              <td>
                <span class="badge px-2 py-1 text-uppercase" :class="getVehicleBadge(amb.vehicle_type)">
                  {{ amb.vehicle_type }}
                </span>
              </td>
              <td><i class="icofont-location-pin text-danger"></i> {{ amb.district }} <span v-if="amb.upazila">({{ amb.upazila }})</span></td>
              <td><strong>৳{{ amb.base_fare }}</strong> / ৳{{ amb.per_km_rate }}</td>
              <td>
                <button 
                  class="btn btn-xs rounded-pill font-weight-bold" 
                  :class="amb.is_available_24_7 ? 'btn-success' : 'btn-secondary'"
                  @click="toggleAmbulance(amb)"
                >
                  {{ amb.is_available_24_7 ? '২৪/৭ সচল' : 'বন্ধ' }}
                </button>
              </td>
              <td class="text-right">
                <button class="btn btn-sm btn-outline-danger" @click="deleteAmbulance(amb)" title="মুছে ফেলুন">
                  <i class="icofont-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SUB-TAB 2: HOSPITAL ICU BEDS -->
    <div v-if="subTab === 'beds'" class="p-3">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 10px;">
        <div class="input-group input-group-sm" style="max-width: 300px;">
          <input 
            type="text" 
            v-model="bedSearch" 
            class="form-control" 
            placeholder="হাসপাতাল বা জেলা দিয়ে খুঁজুন..." 
            @keyup.enter="fetchBeds"
          >
          <div class="input-group-append">
            <button class="btn btn-success" @click="fetchBeds"><i class="icofont-search"></i></button>
          </div>
        </div>

        <select v-model="bedTypeFilter" class="form-control form-control-sm" style="max-width: 180px;" @change="fetchBeds">
          <option value="all">সব ধরনের হাসপাতাল</option>
          <option value="government">সরকারি হাসপাতাল</option>
          <option value="private">বেসরকারি ক্লিনিক</option>
          <option value="specialized">স্পেশালাইজড</option>
        </select>
      </div>

      <div v-if="loadingBeds" class="text-center py-5">
        <div class="spinner-border text-success" role="status"></div>
      </div>
      <div v-else-if="hospitalBeds.length === 0" class="text-center py-5 text-muted bg-white border rounded">
        <i class="icofont-hospital display-4"></i>
        <p class="mt-2">কোনো হাসপাতাল বেড রেকর্ড পাওয়া যায়নি।</p>
      </div>
      <div v-else class="table-responsive bg-white rounded border">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th>হাসপাতাল</th>
              <th>ধরন</th>
              <th>জেলা</th>
              <th>আইসিইউ খালি / মোট</th>
              <th>সিসিইউ (CCU)</th>
              <th>এনআইসিইউ (NICU)</th>
              <th>ইমার্জেন্সি ফোন</th>
              <th>যাচাইকৃত সময়</th>
              <th class="text-right">অ্যাকশন</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="hosp in hospitalBeds" :key="hosp.id">
              <td><strong class="text-dark">{{ hosp.hospital_name }}</strong></td>
              <td><span class="badge badge-light border text-capitalize">{{ hosp.hospital_type }}</span></td>
              <td><i class="icofont-location-pin text-danger"></i> {{ hosp.district }}</td>
              <td>
                <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 13px;">
                  {{ hosp.icu_available }} / {{ hosp.icu_total }} টি
                </span>
              </td>
              <td><strong>{{ hosp.ccu_available || 0 }}</strong> টি</td>
              <td><strong>{{ hosp.nicu_available || 0 }}</strong> টি</td>
              <td><a :href="'tel:' + (hosp.emergency_phone || hosp.hotline)" class="text-success font-weight-bold">{{ hosp.emergency_phone || hosp.hotline }}</a></td>
              <td>
                <small class="text-muted d-block">{{ formatDate(hosp.last_verified_at) }}</small>
                <button class="btn btn-xs btn-outline-info mt-1" @click="quickVerifyBed(hosp)">
                  <i class="icofont-check mr-1"></i> এইমাত্র যাচাইকৃত
                </button>
              </td>
              <td class="text-right">
                <button class="btn btn-sm btn-outline-danger" @click="deleteHospitalBed(hosp)" title="মুছে ফেলুন">
                  <i class="icofont-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SUB-TAB 3: BLOOD SOS & DONORS -->
    <div v-if="subTab === 'blood'" class="p-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <ul class="nav nav-tabs border-0">
          <li class="nav-item">
            <button 
              class="nav-link font-weight-bold" 
              :class="{ 'active border-primary text-primary': bloodSubMode === 'requests' }"
              @click="bloodSubMode = 'requests'"
            >
              রক্তের আবেদন / SOS Requests ({{ bloodRequests.length }})
            </button>
          </li>
          <li class="nav-item">
            <button 
              class="nav-link font-weight-bold" 
              :class="{ 'active border-primary text-primary': bloodSubMode === 'donors' }"
              @click="bloodSubMode = 'donors'"
            >
              স্বেচ্ছাসেবী রক্তদাতা / Donors ({{ bloodDonors.length }})
            </button>
          </li>
        </ul>
      </div>

      <!-- Blood Requests Table -->
      <div v-if="bloodSubMode === 'requests'">
        <div v-if="bloodRequests.length === 0" class="text-center py-5 text-muted bg-white border rounded">
          <i class="icofont-blood-drop display-4"></i>
          <p class="mt-2">কোনো ব্লাড রিকোয়েস্ট পাওয়া যায়নি।</p>
        </div>
        <div v-else class="table-responsive bg-white rounded border">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th>কোড</th>
                <th>রোগীর নাম</th>
                <th>রক্তের গ্রুপ</th>
                <th>ব্যাগ / উপাদান</th>
                <th>হাসপাতাল ও জেলা</th>
                <th>যোগাযোগ</th>
                <th>স্ট্যাটাস</th>
                <th class="text-right">অ্যাকশন</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="req in bloodRequests" :key="req.id">
                <td><span class="badge badge-secondary">{{ req.request_code }}</span></td>
                <td><strong>{{ req.patient_name }}</strong></td>
                <td><span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 13px;">{{ req.blood_group }}</span></td>
                <td>{{ req.units_needed }} ব্যাগ ({{ req.component }})</td>
                <td>{{ req.hospital_name }}, {{ req.district }}</td>
                <td><a :href="'tel:' + req.contact_phone" class="font-weight-bold text-primary">{{ req.contact_phone }}</a></td>
                <td>
                  <select 
                    v-model="req.status" 
                    class="form-control form-control-sm font-weight-bold" 
                    :class="{ 'text-danger': req.status === 'open', 'text-success': req.status === 'fulfilled' }"
                    @change="updateBloodStatus(req)"
                  >
                    <option value="open">Open (জরুরি প্রয়োজন)</option>
                    <option value="fulfilled">Fulfilled (রক্ত পাওয়া গেছে)</option>
                    <option value="cancelled">Cancelled (বাতিল)</option>
                  </select>
                </td>
                <td class="text-right">
                  <button class="btn btn-sm btn-outline-danger" @click="deleteBloodRequest(req)" title="মুছে ফেলুন">
                    <i class="icofont-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Blood Donors Table -->
      <div v-if="bloodSubMode === 'donors'">
        <div v-if="bloodDonors.length === 0" class="text-center py-5 text-muted bg-white border rounded">
          <i class="icofont-users display-4"></i>
          <p class="mt-2">কোনো নিবন্ধিত রক্তদাতা পাওয়া যায়নি।</p>
        </div>
        <div v-else class="table-responsive bg-white rounded border">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th>নাম</th>
                <th>রক্তের গ্রুপ</th>
                <th>জেলা / এলাকা</th>
                <th>ফোন নম্বর</th>
                <th>মোট রক্তদান</th>
                <th>স্ট্যাটাস</th>
                <th class="text-right">অ্যাকশন</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="donor in bloodDonors" :key="donor.id">
                <td><strong>{{ donor.donor_name }}</strong></td>
                <td><span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 13px;">{{ donor.blood_group }}</span></td>
                <td>{{ donor.district }} <span v-if="donor.upazila">({{ donor.upazila }})</span></td>
                <td><a :href="'tel:' + donor.phone" class="font-weight-bold text-dark">{{ donor.phone }}</a></td>
                <td><strong>{{ donor.donation_count || 0 }}</strong> বার</td>
                <td>
                  <button 
                    class="btn btn-xs rounded-pill font-weight-bold" 
                    :class="donor.is_available ? 'btn-success' : 'btn-secondary'"
                    @click="toggleDonorStatus(donor)"
                  >
                    {{ donor.is_available ? 'রক্তদানে প্রস্তুত' : 'অনুপলব্ধ' }}
                  </button>
                </td>
                <td class="text-right">
                  <button class="btn btn-sm btn-outline-danger" @click="deleteDonor(donor)" title="মুছে ফেলুন">
                    <i class="icofont-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- SUB-TAB 4: HEALTH PASSPORTS -->
    <div v-if="subTab === 'passports'" class="p-3">
      <div v-if="loadingPassports" class="text-center py-5">
        <div class="spinner-border text-dark" role="status"></div>
      </div>
      <div v-else-if="healthPassports.length === 0" class="text-center py-5 text-muted bg-white border rounded">
        <i class="icofont-id-card display-4"></i>
        <p class="mt-2">কোনো হেলথ পাসপোর্ট টোকেন পাওয়া যায়নি।</p>
      </div>
      <div v-else class="table-responsive bg-white rounded border">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th>রোগীর নাম</th>
              <th>মোবাইল নম্বর</th>
              <th>রক্তের গ্রুপ</th>
              <th>ইমার্জেন্সি কন্টাক্ট</th>
              <th>অ্যালার্জি ও রোগ</th>
              <th>টোকেন / কিউআর কোড</th>
              <th class="text-right">অ্যাকশন</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="pass in healthPassports" :key="pass.id">
              <td><strong class="text-dark">{{ pass.user?.name || 'N/A' }}</strong></td>
              <td>{{ pass.user?.phone || 'N/A' }}</td>
              <td><span class="badge badge-danger px-2 py-1 font-weight-bold">{{ pass.user?.blood_group || 'N/A' }}</span></td>
              <td><strong class="text-danger">{{ pass.user?.emergency_contact_phone || 'N/A' }}</strong></td>
              <td>
                <small class="d-block text-muted">রোগ: {{ pass.user?.chronic_conditions || 'None' }}</small>
                <small class="d-block text-danger">অ্যালার্জি: {{ pass.user?.allergies || 'None' }}</small>
              </td>
              <td>
                <code class="small text-dark">{{ pass.token?.substring(0, 16) }}...</code>
              </td>
              <td class="text-right">
                <button class="btn btn-sm btn-outline-warning" @click="regenerateToken(pass)" title="হারিয়ে গেলে নতুন কিউআর টোকেন তৈরি করুন">
                  <i class="icofont-refresh mr-1"></i> টোকেন রি-জেনারেট
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL: ADD AMBULANCE -->
    <div v-if="showAddAmbulanceModal" class="modal-backdrop fade show" style="opacity: 0.5; z-index: 1050;"></div>
    <div v-if="showAddAmbulanceModal" class="modal fade show d-block" style="z-index: 1055;">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
          <div class="modal-header bg-danger text-white py-2">
            <h5 class="modal-title font-weight-bold h6"><i class="icofont-ambulance-cross mr-1"></i> নতুন এম্বুলেন্স সার্ভিস যোগ করুন</h5>
            <button type="button" class="close text-white" @click="showAddAmbulanceModal = false">&times;</button>
          </div>
          <form @submit.prevent="submitAmbulance">
            <div class="modal-body p-3">
              <div class="form-group mb-2">
                <label class="small font-weight-bold">সার্ভিস / কোম্পানির নাম *</label>
                <input type="text" v-model="ambulanceForm.service_name" class="form-control form-control-sm" required placeholder="যেমন: ঢাকা সেন্ট্রাল এম্বুলেন্স">
              </div>
              <div class="form-group mb-2">
                <label class="small font-weight-bold">ড্রাইভার / কন্টাক্ট পার্সন *</label>
                <input type="text" v-model="ambulanceForm.driver_or_contact_name" class="form-control form-control-sm" required placeholder="মোঃ রফিক">
              </div>
              <div class="row">
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">মোবাইল ফোন *</label>
                  <input type="text" v-model="ambulanceForm.phone" class="form-control form-control-sm" required placeholder="017xxxxxxxx">
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">গাড়ির ধরন *</label>
                  <select v-model="ambulanceForm.vehicle_type" class="form-control form-control-sm">
                    <option value="ac">এসি এম্বুলেন্স</option>
                    <option value="non_ac">নন-এসি সাধারণ</option>
                    <option value="icu">আইসিইউ সাপোর্ট</option>
                    <option value="freezer">ফ্রিজার গাড়ি</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">জেলা *</label>
                  <input type="text" v-model="ambulanceForm.district" class="form-control form-control-sm" required placeholder="ঢাকা">
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">উপজেলা / এলাকা</label>
                  <input type="text" v-model="ambulanceForm.upazila" class="form-control form-control-sm" placeholder="ধানমন্ডি">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">বেস ভাড়া (৳) *</label>
                  <input type="number" v-model="ambulanceForm.base_fare" class="form-control form-control-sm" required placeholder="1500">
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">প্রতি কিমি ভাড়া (৳) *</label>
                  <input type="number" v-model="ambulanceForm.per_km_rate" class="form-control form-control-sm" required placeholder="30">
                </div>
              </div>
            </div>
            <div class="modal-footer py-2">
              <button type="button" class="btn btn-sm btn-secondary" @click="showAddAmbulanceModal = false">বাতিল</button>
              <button type="submit" class="btn btn-sm btn-danger font-weight-bold">সেভ করুন</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL: ADD HOSPITAL BED -->
    <div v-if="showAddBedModal" class="modal-backdrop fade show" style="opacity: 0.5; z-index: 1050;"></div>
    <div v-if="showAddBedModal" class="modal fade show d-block" style="z-index: 1055;">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
          <div class="modal-header bg-success text-white py-2">
            <h5 class="modal-title font-weight-bold h6"><i class="icofont-hospital mr-1"></i> নতুন হাসপাতাল বেড যোগ করুন</h5>
            <button type="button" class="close text-white" @click="showAddBedModal = false">&times;</button>
          </div>
          <form @submit.prevent="submitHospitalBed">
            <div class="modal-body p-3">
              <div class="form-group mb-2">
                <label class="small font-weight-bold">হাসপাতালের নাম *</label>
                <input type="text" v-model="bedForm.hospital_name" class="form-control form-control-sm" required placeholder="ঢাকা মেডিকেল কলেজ হাসপাতাল">
              </div>
              <div class="row">
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">ধরন *</label>
                  <select v-model="bedForm.hospital_type" class="form-control form-control-sm">
                    <option value="government">সরকারি হাসপাতাল</option>
                    <option value="private">বেসরকারি ক্লিনিক</option>
                    <option value="specialized">স্পেশালাইজড</option>
                  </select>
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">জেলা *</label>
                  <input type="text" v-model="bedForm.district" class="form-control form-control-sm" required placeholder="ঢাকা">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">জরুরি ফোন / হটলাইন *</label>
                  <input type="text" v-model="bedForm.emergency_phone" class="form-control form-control-sm" required placeholder="017xxxxxxxx">
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">উপজেলা / এলাকা</label>
                  <input type="text" v-model="bedForm.upazila" class="form-control form-control-sm" placeholder="শাহবাগ">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">আইসিইউ খালি (ICU Available) *</label>
                  <input type="number" v-model="bedForm.icu_available" class="form-control form-control-sm" required placeholder="4">
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">আইসিইউ মোট (ICU Total) *</label>
                  <input type="number" v-model="bedForm.icu_total" class="form-control form-control-sm" required placeholder="12">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">সিসিইউ খালি (CCU)</label>
                  <input type="number" v-model="bedForm.ccu_available" class="form-control form-control-sm" placeholder="2">
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="small font-weight-bold">এনআইসিইউ খালি (NICU)</label>
                  <input type="number" v-model="bedForm.nicu_available" class="form-control form-control-sm" placeholder="1">
                </div>
              </div>
            </div>
            <div class="modal-footer py-2">
              <button type="button" class="btn btn-sm btn-secondary" @click="showAddBedModal = false">বাতিল</button>
              <button type="submit" class="btn btn-sm btn-success font-weight-bold">সেভ করুন</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'EmergencyAdminManagement',
  data() {
    return {
      subTab: 'ambulances',
      successMsg: '',
      
      // Ambulances
      ambulances: [],
      ambulanceTotal: 0,
      loadingAmbulances: false,
      ambulanceSearch: '',
      ambulanceTypeFilter: 'all',
      showAddAmbulanceModal: false,
      ambulanceForm: {
        service_name: '',
        driver_or_contact_name: '',
        phone: '',
        vehicle_type: 'ac',
        district: 'Dhaka',
        upazila: '',
        base_fare: 1500,
        per_km_rate: 35,
        is_available_24_7: true,
      },

      // Hospital Beds
      hospitalBeds: [],
      bedTotal: 0,
      loadingBeds: false,
      bedSearch: '',
      bedTypeFilter: 'all',
      showAddBedModal: false,
      bedForm: {
        hospital_name: '',
        hospital_type: 'government',
        district: 'Dhaka',
        upazila: '',
        emergency_phone: '',
        icu_available: 5,
        icu_total: 10,
        ccu_available: 2,
        nicu_available: 1,
      },

      // Blood SOS
      bloodSubMode: 'requests',
      bloodRequests: [],
      bloodDonors: [],

      // Passports
      healthPassports: [],
      loadingPassports: false,
    };
  },
  mounted() {
    this.fetchAmbulances();
  },
  methods: {
    /* AMBULANCES */
    async fetchAmbulances() {
      this.loadingAmbulances = true;
      try {
        const params = {
          search: this.ambulanceSearch,
          vehicle_type: this.ambulanceTypeFilter,
        };
        const res = await axios.get('/admin/ambulances', { params });
        this.ambulances = res.data.data || [];
        this.ambulanceTotal = res.data.total || this.ambulances.length;
      } catch (err) {
        console.error('Error loading ambulances:', err);
      } finally {
        this.loadingAmbulances = false;
      }
    },
    openAddAmbulanceModal() {
      this.showAddAmbulanceModal = true;
    },
    async submitAmbulance() {
      try {
        await axios.post('/admin/ambulances', this.ambulanceForm);
        this.showAddAmbulanceModal = false;
        this.successMsg = 'নতুন এম্বুলেন্স সফলভাবে যুক্ত হয়েছে!';
        this.fetchAmbulances();
      } catch (err) {
        alert(err.response?.data?.message || 'Error saving ambulance');
      }
    },
    async toggleAmbulance(amb) {
      try {
        const res = await axios.patch(`/admin/ambulances/${amb.id}/toggle`);
        amb.is_available_24_7 = res.data.is_available_24_7;
      } catch (err) {
        alert('Status update failed');
      }
    },
    async deleteAmbulance(amb) {
      if (!confirm(`আপনি কি "${amb.service_name}" মুছে ফেলতে চান?`)) return;
      try {
        await axios.delete(`/admin/ambulances/${amb.id}`);
        this.fetchAmbulances();
      } catch (err) {
        alert('Delete failed');
      }
    },

    /* BEDS */
    async fetchBeds() {
      this.loadingBeds = true;
      try {
        const params = {
          search: this.bedSearch,
          hospital_type: this.bedTypeFilter,
        };
        const res = await axios.get('/admin/hospital-beds', { params });
        this.hospitalBeds = res.data.data || [];
        this.bedTotal = res.data.total || this.hospitalBeds.length;
      } catch (err) {
        console.error('Error loading beds:', err);
      } finally {
        this.loadingBeds = false;
      }
    },
    openAddBedModal() {
      this.showAddBedModal = true;
    },
    async submitHospitalBed() {
      try {
        await axios.post('/admin/hospital-beds', this.bedForm);
        this.showAddBedModal = false;
        this.successMsg = 'হাসপাতাল আইসিইউ বেড এন্ট্রি সফলভাবে যুক্ত হয়েছে!';
        this.fetchBeds();
      } catch (err) {
        alert(err.response?.data?.message || 'Error saving hospital bed');
      }
    },
    async quickVerifyBed(hosp) {
      try {
        const res = await axios.patch(`/admin/hospital-beds/${hosp.id}/verify`);
        hosp.last_verified_at = res.data.last_verified_at;
        this.successMsg = `"${hosp.hospital_name}" এর তথ্য এইমাত্র ভেরিফাইড হিসেবে আপডেট করা হলো!`;
      } catch (err) {
        alert('Verification failed');
      }
    },
    async deleteHospitalBed(hosp) {
      if (!confirm(`আপনি কি "${hosp.hospital_name}" মুছে ফেলতে চান?`)) return;
      try {
        await axios.delete(`/admin/hospital-beds/${hosp.id}`);
        this.fetchBeds();
      } catch (err) {
        alert('Delete failed');
      }
    },

    /* BLOOD SOS */
    async fetchBloodRequests() {
      try {
        const res = await axios.get('/admin/blood-requests');
        this.bloodRequests = res.data.data || [];
      } catch (err) {
        console.error(err);
      }
    },
    async updateBloodStatus(req) {
      try {
        await axios.patch(`/admin/blood-requests/${req.id}/status`, { status: req.status });
        this.successMsg = `ব্লাড রিকোয়েস্ট স্ট্যাটাস ${req.status} করা হয়েছে!`;
      } catch (err) {
        alert('Status update failed');
      }
    },
    async deleteBloodRequest(req) {
      if (!confirm('মুছে ফেলতে নিশ্চিত?')) return;
      try {
        await axios.delete(`/admin/blood-requests/${req.id}`);
        this.fetchBloodRequests();
      } catch (err) {
        alert('Delete failed');
      }
    },
    async fetchBloodDonors() {
      try {
        const res = await axios.get('/admin/blood-donors');
        this.bloodDonors = res.data.data || [];
      } catch (err) {
        console.error(err);
      }
    },
    async toggleDonorStatus(donor) {
      try {
        const res = await axios.patch(`/admin/blood-donors/${donor.id}/toggle`);
        donor.is_available = res.data.is_available;
      } catch (err) {
        alert('Update failed');
      }
    },
    async deleteDonor(donor) {
      if (!confirm('ডোনার রেকর্ড মুছে ফেলবেন?')) return;
      try {
        await axios.delete(`/admin/blood-donors/${donor.id}`);
        this.fetchBloodDonors();
      } catch (err) {
        alert('Delete failed');
      }
    },

    /* HEALTH PASSPORTS */
    async fetchPassports() {
      this.loadingPassports = true;
      try {
        const res = await axios.get('/admin/health-passports');
        this.healthPassports = res.data.data || [];
      } catch (err) {
        console.error(err);
      } finally {
        this.loadingPassports = false;
      }
    },
    async regenerateToken(pass) {
      if (!confirm(`"${pass.user?.name}" এর জন্য নতুন হেলথ পাসপোর্ট টোকেন জেনারেট করবেন? পূর্বের কিউআর কোড অকার্যকর হবে।`)) return;
      try {
        const res = await axios.post(`/admin/health-passports/${pass.user_id}/regenerate`);
        pass.token = res.data.passport.token;
        this.successMsg = 'নতুন হেলথ পাসপোর্ট টোকেন সফলভাবে জেনারেট হয়েছে!';
      } catch (err) {
        alert('Regeneration failed');
      }
    },

    /* HELPERS */
    getVehicleBadge(type) {
      switch (type) {
        case 'icu': return 'badge-danger';
        case 'freezer': return 'badge-dark';
        case 'non_ac': return 'badge-secondary';
        default: return 'badge-primary';
      }
    },
    formatDate(dateStr) {
      if (!dateStr) return 'N/A';
      return new Date(dateStr).toLocaleString('bn-BD', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
  },
};
</script>

<style scoped>
.btn-xs {
  padding: 0.15rem 0.5rem;
  font-size: 0.75rem;
}
</style>
