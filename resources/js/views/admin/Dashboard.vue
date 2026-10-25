<template>
  <div class="admin-dashboard-page bg-light min-vh-100 pb-5">
    <!-- Admin Topbar Nav -->
    <nav class="navbar navbar-expand navbar-dark bg-primary py-3 shadow-sm">
      <div class="container-fluid px-lg-5">
        <router-link to="/admin/dashboard" class="navbar-brand d-flex align-items-center">
          <img src="/images/logo/Logo2.png" alt="সুখী মানুষ (Sukhi Manush)" style="height: 44px; background: rgba(255,255,255,0.92); padding: 2px 8px; border-radius: 8px;" class="mr-2">
          <span class="font-weight-bold h5 mb-0 text-white">Super Admin Control Center</span>
        </router-link>

        <div class="ml-auto d-flex align-items-center gap-3">
          <router-link to="/" class="btn btn-sm btn-outline-light mr-3" target="_blank">
            <i class="icofont-external-link mr-1"></i> Public Site
          </router-link>
          
          <span class="text-white small mr-3 d-none d-sm-inline">
            <i class="icofont-user-alt-4 mr-1"></i> Super Admin
          </span>

          <button class="btn btn-sm btn-danger" @click="logout">
            <i class="icofont-logout mr-1"></i> Logout
          </button>
        </div>
      </div>
    </nav>

    <div class="container-fluid px-lg-5 mt-4">
      <!-- SaaS Revenue & Aggregate Metrics Row -->
      <!-- SaaS Revenue & Multi-Role Metrics Grid -->
      <div class="row mb-4">
        <!-- Platform Revenue -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Revenue</span>
                <h4 class="font-weight-bold mb-0 text-primary">${{ stats.total_revenue || '0.00' }}</h4>
              </div>
              <div class="p-2 bg-light rounded-circle text-primary">
                <i class="icofont-chart-growth" style="font-size: 22px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Admin Commission -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Admin Fee</span>
                <h4 class="font-weight-bold mb-0 text-success">${{ stats.admin_commission || '0.00' }}</h4>
              </div>
              <div class="p-2 bg-light rounded-circle text-success">
                <i class="icofont-money-bag" style="font-size: 22px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Doctors -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Doctors</span>
                <h4 class="font-weight-bold mb-0 text-info">{{ stats.total_doctors || 0 }}</h4>
                <small v-if="stats.pending_doctors" class="text-warning font-weight-bold d-block" style="font-size: 11px;">
                  <i class="icofont-clock-time"></i> {{ stats.pending_doctors }} pending approval
                </small>
              </div>
              <div class="p-2 bg-light rounded-circle text-info">
                <i class="icofont-doctor-alt" style="font-size: 22px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Rural Agents -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Agents</span>
                <h4 class="font-weight-bold mb-0 text-success">{{ stats.total_agents || 0 }}</h4>
                <small v-if="stats.pending_agents" class="text-warning font-weight-bold d-block" style="font-size: 11px;">
                  <i class="icofont-clock-time"></i> {{ stats.pending_agents }} pending approval
                </small>
              </div>
              <div class="p-2 bg-light rounded-circle text-success">
                <i class="icofont-hospital" style="font-size: 22px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Chamber Staff -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Chamber Staff</span>
                <h4 class="font-weight-bold mb-0 text-danger">{{ stats.total_staff || 0 }}</h4>
              </div>
              <div class="p-2 bg-light rounded-circle text-danger">
                <i class="icofont-users-alt-5" style="font-size: 22px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Registered Patients -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Patients</span>
                <h4 class="font-weight-bold mb-0 text-dark">{{ stats.total_patients || 0 }}</h4>
              </div>
              <div class="p-2 bg-light rounded-circle text-dark">
                <i class="icofont-patient-file" style="font-size: 22px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Super Admin Tabs Card -->
      <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap">
          <ul class="nav nav-pills">
            <li class="nav-item">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-primary text-white': activeTab === 'appointments' }"
                @click="activeTab = 'appointments'"
              >
                <i class="icofont-calendar mr-1"></i> Appointments ({{ appointments.length }})
              </a>
            </li>
            <li class="nav-item ml-2">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-primary text-white': activeTab === 'doctors' }"
                @click="activeTab = 'doctors'; if (doctors.length === 0) fetchDoctors(1);"
              >
                <i class="icofont-doctor-alt mr-1"></i> Doctors ({{ doctorPagination.total || stats.total_doctors || 0 }})
                <span v-if="stats.pending_doctors" class="badge badge-warning text-dark ml-1 font-weight-bold">{{ stats.pending_doctors }} New</span>
              </a>
            </li>
            <li class="nav-item ml-2">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-success text-white': activeTab === 'agents' }"
                @click="activeTab = 'agents'; fetchAgents(1);"
              >
                <i class="icofont-hospital mr-1"></i> Agents ({{ agentPagination.total || stats.total_agents || 0 }})
                <span v-if="stats.pending_agents" class="badge badge-warning text-dark ml-1 font-weight-bold">{{ stats.pending_agents }} New</span>
              </a>
            </li>
            <li class="nav-item ml-2">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-danger text-white': activeTab === 'staff' }"
                @click="activeTab = 'staff'; fetchStaff(1);"
              >
                <i class="icofont-users-alt-5 mr-1"></i> Staff ({{ staffPagination.total || stats.total_staff || 0 }})
              </a>
            </li>
            <li class="nav-item ml-2">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-info text-white': activeTab === 'patients' }"
                @click="activeTab = 'patients'; fetchPatients(1);"
              >
                <i class="icofont-patient-file mr-1"></i> Patients ({{ patientPagination.total || stats.total_patients || 0 }})
              </a>
            </li>
            <li class="nav-item ml-2">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-primary text-white': activeTab === 'payments' }"
                @click="activeTab = 'payments'; fetchPayments();"
              >
                <i class="icofont-pay mr-1"></i> Payments
              </a>
            </li>
            <li class="nav-item ml-2">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-primary text-white': activeTab === 'messages' }"
                @click="activeTab = 'messages'; fetchMessages();"
              >
                <i class="icofont-envelope mr-1"></i> Inquiries ({{ stats.unread_messages || 0 }})
              </a>
            </li>
            <li class="nav-item ml-2">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-danger text-white': activeTab === 'emergency' }"
                @click="activeTab = 'emergency'"
              >
                <i class="icofont-ambulance-cross mr-1"></i> Emergency Hub
              </a>
            </li>
            <li class="nav-item ml-2">
              <a 
                class="nav-link cursor-pointer font-weight-bold" 
                :class="{ 'active bg-dark text-white': activeTab === 'policies' }"
                @click="activeTab = 'policies'"
              >
                <i class="icofont-law-order mr-1"></i> প্রাইভেসি ও শর্তাবলী (Policies)
              </a>
            </li>
          </ul>

          <!-- Appointments filter bar -->
          <div v-if="activeTab === 'appointments'" class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
            <select v-model="filterStatus" class="form-control form-control-sm mr-2" @change="fetchAppointments(1)">
              <option value="all">All Status</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <input 
              type="text" 
              v-model="searchQuery" 
              class="form-control form-control-sm" 
              placeholder="Search reference, patient..."
              @keyup.enter="fetchAppointments(1)"
            >
          </div>

          <!-- Doctors filter & search bar -->
          <div v-if="activeTab === 'doctors'" class="d-flex align-items-center gap-2 mt-2 mt-sm-0 flex-wrap">
            <select v-model="doctorFilterStatus" class="form-control form-control-sm mr-1" style="width: auto;" @change="fetchDoctors(1)">
              <option value="all">All Approval</option>
              <option value="approved">Approved</option>
              <option value="pending">Pending</option>
            </select>
            <select v-model="doctorFilterVerified" class="form-control form-control-sm mr-1" style="width: auto;" @change="fetchDoctors(1)">
              <option value="all">All BMDC</option>
              <option value="verified">BMDC Verified</option>
              <option value="unverified">Unverified</option>
            </select>
            <div class="input-group input-group-sm" style="width: 250px;">
              <input 
                type="text" 
                v-model="doctorSearch" 
                class="form-control" 
                placeholder="Search name, phone, BMDC..."
                @input="onDoctorSearchInput"
                @keyup.enter="fetchDoctors(1)"
              >
              <div class="input-group-append">
                <button class="btn btn-primary" type="button" @click="fetchDoctors(1)" title="Search">
                  <i class="icofont-search"></i>
                </button>
                <button v-if="doctorSearch" class="btn btn-outline-secondary" type="button" @click="doctorSearch = ''; fetchDoctors(1)" title="Clear">
                  <i class="icofont-close-line"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Agents filter & action bar -->
          <div v-if="activeTab === 'agents'" class="d-flex align-items-center gap-2 mt-2 mt-sm-0 flex-wrap">
            <select v-model="agentStatusFilter" class="form-control form-control-sm mr-2" style="width: 155px;" @change="fetchAgents(1)">
              <option value="all">সব এজেন্ট (All)</option>
              <option value="approved">অনুমোদিত (Approved)</option>
              <option value="pending">অপেক্ষমাণ (Pending)</option>
            </select>
            <div class="input-group input-group-sm mr-2" style="width: 230px;">
              <input 
                type="text" 
                v-model="agentSearch" 
                class="form-control" 
                placeholder="খুঁজুন (নাম, ফোন, ফার্মেসি)..."
                @input="onAgentSearchInput"
                @keyup.enter="fetchAgents(1)"
              >
              <div class="input-group-append">
                <button class="btn btn-success" type="button" @click="fetchAgents(1)" title="Search">
                  <i class="icofont-search"></i>
                </button>
                <button v-if="agentSearch" class="btn btn-outline-secondary" type="button" @click="agentSearch = ''; fetchAgents(1)" title="Clear">
                  <i class="icofont-close-line"></i>
                </button>
              </div>
            </div>
            <button class="btn btn-sm btn-success font-weight-bold" @click="openAddAgentModal">
              <i class="icofont-plus mr-1"></i> নতুন এজেন্ট যোগ করুন
            </button>
          </div>

          <!-- Staff filter & action bar -->
          <div v-if="activeTab === 'staff'" class="d-flex align-items-center gap-2 mt-2 mt-sm-0 flex-wrap">
            <select v-model="staffFilterStatus" class="form-control form-control-sm mr-2" style="width: auto;" @change="fetchStaff(1)">
              <option value="all">সকল অবস্থা (All Status)</option>
              <option value="active">সক্রিয় (Active)</option>
              <option value="inactive">নিষ্ক্রিয় (Inactive)</option>
            </select>
            <div class="input-group input-group-sm mr-2" style="width: 250px;">
              <input 
                type="text" 
                v-model="staffSearch" 
                class="form-control" 
                placeholder="খুঁজুন (স্টাফ নাম, ফোন, ডাক্তার)..."
                @input="onStaffSearchInput"
                @keyup.enter="fetchStaff(1)"
              >
              <div class="input-group-append">
                <button class="btn btn-danger" type="button" @click="fetchStaff(1)" title="Search">
                  <i class="icofont-search"></i>
                </button>
                <button v-if="staffSearch" class="btn btn-outline-secondary" type="button" @click="staffSearch = ''; fetchStaff(1)" title="Clear">
                  <i class="icofont-close-line"></i>
                </button>
              </div>
            </div>
            <button class="btn btn-sm btn-danger font-weight-bold" @click="openAddStaffModal">
              <i class="icofont-plus mr-1"></i> নতুন স্টাফ যোগ করুন
            </button>
          </div>

          <!-- Patients filter bar -->
          <div v-if="activeTab === 'patients'" class="d-flex align-items-center gap-2 mt-2 mt-sm-0 flex-wrap">
            <div class="input-group input-group-sm" style="width: 270px;">
              <input 
                type="text" 
                v-model="patientSearch" 
                class="form-control" 
                placeholder="রোগী খুঁজুন (নাম, মোবাইল, ইমেইল)..."
                @input="onPatientSearchInput"
                @keyup.enter="fetchPatients(1)"
              >
              <div class="input-group-append">
                <button class="btn btn-info" type="button" @click="fetchPatients(1)" title="Search">
                  <i class="icofont-search"></i>
                </button>
                <button v-if="patientSearch" class="btn btn-outline-secondary" type="button" @click="patientSearch = ''; fetchPatients(1)" title="Clear">
                  <i class="icofont-close-line"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body p-0">
          <!-- TAB 1: ALL APPOINTMENTS -->
          <div v-if="activeTab === 'appointments'">
            <div v-if="loadingAppointments" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
            </div>

            <div v-else-if="appointments.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-calendar display-4"></i>
              <p class="mt-2">No appointments found.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Ref #</th>
                    <th>Patient Name</th>
                    <th>Doctor & Chamber</th>
                    <th>Date & Time</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th class="text-center">Status Control</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="apt in appointments" :key="apt.id">
                    <td>
                      <span class="font-weight-bold text-danger">{{ apt.appointment_number }}</span>
                    </td>
                    <td>
                      <div class="font-weight-bold text-dark">{{ apt.patient_name }}</div>
                      <small class="text-muted">{{ apt.patient_phone }}</small>
                    </td>
                    <td>
                      <div class="font-weight-bold">{{ apt.doctor?.name }}</div>
                      <small class="text-muted"><i class="icofont-location-pin text-danger"></i> {{ apt.doctor?.chamber_address }}</small>
                    </td>
                    <td>
                      <div>{{ formatDate(apt.appointment_date) }}</div>
                      <small class="text-danger font-weight-bold">{{ apt.appointment_time }}</small>
                    </td>
                    <td>
                      <span class="font-weight-bold text-success">${{ apt.consultation_fee }}</span>
                    </td>
                    <td>
                      <span class="status-pill" :class="apt.status">{{ apt.status }}</span>
                    </td>
                    <td class="text-center">
                      <select 
                        :value="apt.status" 
                        class="form-control form-control-sm d-inline-block" 
                        style="width: auto;"
                        @change="updateAppointmentStatus(apt, $event.target.value)"
                      >
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                      </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- TAB 2: DOCTORS MANAGEMENT -->
          <div v-if="activeTab === 'doctors'">
            <div v-if="loadingDoctors" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
              <p class="mt-2 text-muted font-weight-bold">ডাক্তার তালিকা লোড হচ্ছে...</p>
            </div>

            <div v-else-if="doctors.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-doctor-alt display-4 text-secondary"></i>
              <p class="mt-2 font-weight-bold">কোনো ডাক্তারের তথ্য পাওয়া যায়নি (No doctors found matching search).</p>
              <button class="btn btn-sm btn-outline-primary mt-2" @click="doctorSearch = ''; doctorFilterStatus = 'all'; doctorFilterVerified = 'all'; fetchDoctors(1);">
                <i class="icofont-refresh mr-1"></i> ফিল্টার রিসেট করুন
              </button>
            </div>

            <div v-else>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th style="min-width: 260px;">Doctor Profile</th>
                      <th>Specialization & Rank</th>
                      <th style="min-width: 220px;">Chamber Address</th>
                      <th>Fee</th>
                      <th>Working Schedule</th>
                      <th>Status</th>
                      <th class="text-center" style="min-width: 200px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="doc in doctors" :key="doc.id">
                      <td>
                        <div class="d-flex align-items-center">
                          <img 
                            :src="doc.avatar_url || '/' + (doc.avatar || 'images/team/1.jpg')" 
                            class="rounded-circle mr-2 border" 
                            style="width: 46px; height: 46px; object-fit: cover;"
                            @error="$event.target.src = '/images/team/1.jpg'"
                          >
                          <div>
                            <div class="d-flex align-items-center">
                              <h6 class="font-weight-bold mb-0 text-dark mr-1">{{ doc.name }}</h6>
                              <span class="badge badge-light border text-muted" style="font-size: 10px;">#{{ doc.id }}</span>
                            </div>
                            <small class="text-muted d-block">{{ doc.email || 'No email' }}</small>
                            <small class="text-primary font-weight-bold"><i class="icofont-phone"></i> {{ doc.phone || 'No phone' }}</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge badge-primary">{{ doc.specialization }}</span>
                        <div v-if="doc.designation" class="small text-secondary mt-1 font-weight-bold">{{ doc.designation }}</div>
                      </td>
                      <td>
                        <small class="text-dark d-block font-weight-bold" style="max-width: 240px; line-height: 1.3;">{{ doc.chamber_address || 'Chamber not set' }}</small>
                        <small class="text-muted">{{ doc.district ? doc.district : '' }} {{ doc.upazila ? '(' + doc.upazila + ')' : '' }}</small>
                        <div v-if="doc.bmdc_number" class="small text-primary font-weight-bold mt-1">BMDC: {{ doc.bmdc_number }}</div>
                      </td>
                      <td>
                        <strong class="text-success font-weight-bold" style="font-size: 15px;">৳{{ doc.consultation_fee }}</strong>
                      </td>
                      <td>
                        <small class="text-muted d-block">{{ (doc.available_days || []).join(', ') || 'Sat - Thu' }}</small>
                        <small class="text-dark font-weight-bold">{{ doc.start_time || '05:00 PM' }} - {{ doc.end_time || '09:00 PM' }}</small>
                      </td>
                      <td>
                        <div class="mb-1">
                          <span v-if="doc.is_approved" class="badge badge-success">Approved</span>
                          <span v-else class="badge badge-warning">Pending</span>
                        </div>
                        <div>
                          <span v-if="doc.is_verified" class="badge badge-info">Verified</span>
                          <span v-if="doc.is_featured" class="badge badge-warning ml-1">Featured</span>
                        </div>
                      </td>
                      <td class="text-center text-nowrap">
                        <!-- Edit Button -->
                        <button 
                          class="btn btn-sm btn-primary mr-1"
                          @click="openEditDoctor(doc)"
                          title="ডাক্তারের তথ্য পরিবর্তন করুন (Edit Doctor)"
                          style="font-size: 11px; padding: 4px 8px;"
                        >
                          <i class="icofont-ui-edit"></i> Edit
                        </button>

                        <!-- Verify BMDC Button -->
                        <button 
                          class="btn btn-sm mr-1 font-weight-bold"
                          :class="doc.is_verified ? 'btn-success' : 'btn-outline-info'"
                          @click="toggleDoctorVerification(doc)"
                          :title="doc.is_verified ? 'BMDC ভেরিফাইড (ক্লিক করে আনভেরিফাই করুন)' : 'BMDC ভেরিফাই করুন'"
                          style="font-size: 11px; padding: 4px 8px;"
                        >
                          <i class="icofont-check-circled"></i> {{ doc.is_verified ? 'Verified' : 'Verify' }}
                        </button>

                        <!-- Toggle Approve/Disable -->
                        <button 
                          class="btn btn-sm mr-1"
                          :class="doc.is_approved ? 'btn-outline-warning' : 'btn-outline-success'"
                          @click="toggleDoctorApproval(doc)"
                          style="font-size: 11px; padding: 4px 8px;"
                        >
                          {{ doc.is_approved ? 'Disable' : 'Approve' }}
                        </button>

                        <!-- Delete Button -->
                        <button 
                          class="btn btn-sm btn-outline-danger"
                          @click="deleteDoctor(doc)"
                          title="ডাক্তার মুছে ফেলুন"
                          style="font-size: 11px; padding: 4px 8px;"
                        >
                          <i class="icofont-ui-delete"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- DOCTORS PAGINATION BAR -->
              <div class="bg-light border-top py-3 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                  Showing <span class="font-weight-bold text-dark">{{ doctorPagination.from || 0 }}</span> to 
                  <span class="font-weight-bold text-dark">{{ doctorPagination.to || 0 }}</span> of 
                  <span class="font-weight-bold text-primary">{{ doctorPagination.total || 0 }}</span> doctors
                </div>

                <nav v-if="doctorPagination.last_page > 1" aria-label="Doctor pagination">
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: doctorPagination.current_page <= 1 }">
                      <button class="page-link" @click="fetchDoctors(1)" :disabled="doctorPagination.current_page <= 1">« First</button>
                    </li>
                    <li class="page-item" :class="{ disabled: doctorPagination.current_page <= 1 }">
                      <button class="page-link" @click="fetchDoctors(doctorPagination.current_page - 1)" :disabled="doctorPagination.current_page <= 1">‹ Prev</button>
                    </li>

                    <li 
                      v-for="(p, idx) in visibleDoctorPages" 
                      :key="'doc_page_' + idx" 
                      class="page-item"
                      :class="{ active: p === doctorPagination.current_page, disabled: p === '...' }"
                    >
                      <button class="page-link" @click="p !== '...' && fetchDoctors(p)">{{ p }}</button>
                    </li>

                    <li class="page-item" :class="{ disabled: doctorPagination.current_page >= doctorPagination.last_page }">
                      <button class="page-link" @click="fetchDoctors(doctorPagination.current_page + 1)" :disabled="doctorPagination.current_page >= doctorPagination.last_page">Next ›</button>
                    </li>
                    <li class="page-item" :class="{ disabled: doctorPagination.current_page >= doctorPagination.last_page }">
                      <button class="page-link" @click="fetchDoctors(doctorPagination.last_page)" :disabled="doctorPagination.current_page >= doctorPagination.last_page">Last »</button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>

          <!-- TAB: AGENTS MANAGEMENT -->
          <div v-if="activeTab === 'agents'">
            <div v-if="loadingAgents" class="text-center py-5">
              <div class="spinner-border text-success" role="status"></div>
            </div>

            <div v-else-if="agents.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-hospital display-4 text-success"></i>
              <p class="mt-2">কোনো এজেন্ট পাওয়া যায়নি (No agents found).</p>
              <button class="btn btn-sm btn-success mt-2" @click="openAddAgentModal">
                <i class="icofont-plus mr-1"></i> নতুন এজেন্ট যোগ করুন
              </button>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>এজেন্ট আইডি / নাম</th>
                    <th>ফার্মেসি ও অবস্থান</th>
                    <th>যোগাযোগ (ফোন / ইমেইল)</th>
                    <th>স্ট্যাটাস</th>
                    <th>ওয়ালেট ব্যালেন্স</th>
                    <th>বুকিং ও কমিশন</th>
                    <th>নিবন্ধন তারিখ</th>
                    <th class="text-right">অ্যাকশন</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ag in agents" :key="ag.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 38px; height: 38px; font-size: 15px;">
                          {{ ag.name ? ag.name.charAt(0).toUpperCase() : 'A' }}
                        </div>
                        <div>
                          <span class="font-weight-bold text-dark d-block">{{ ag.name }}</span>
                          <span class="badge badge-light border text-muted">ID: #{{ ag.id }}</span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="font-weight-bold text-dark">
                        <i class="icofont-medical-sign text-success mr-1"></i>
                        {{ ag.pharmacy_name || 'সাধারণ এজেন্ট' }}
                      </div>
                      <small class="text-muted">
                        <i class="icofont-location-pin text-danger mr-1"></i>
                        {{ ag.upazila ? ag.upazila + ', ' : '' }}{{ ag.district || 'বাংলাদেশ' }}
                      </small>
                    </td>
                    <td>
                      <div><i class="icofont-phone text-muted mr-1"></i><strong>{{ ag.phone || 'N/A' }}</strong></div>
                      <small class="text-muted"><i class="icofont-envelope text-muted mr-1"></i>{{ ag.email }}</small>
                    </td>
                    <td>
                      <span v-if="ag.is_approved" class="badge badge-success px-2 py-1">
                        <i class="icofont-check-circled mr-1"></i>Approved
                      </span>
                      <span v-else class="badge badge-warning text-dark px-2 py-1 font-weight-bold">
                        <i class="icofont-clock-time mr-1"></i>Pending
                      </span>
                    </td>
                    <td>
                      <span class="badge badge-success px-2 py-1 font-size-14" style="font-size: 14px;">
                        ৳ {{ Number(ag.agent_commission_balance || 0).toLocaleString() }}
                      </span>
                    </td>
                    <td>
                      <div><strong>{{ ag.agent_appointments_count || 0 }}</strong> টি বুকিং</div>
                      <small class="text-success font-weight-bold">
                        মোট কমিশন: ৳ {{ Number(ag.agent_appointments_sum_agent_commission || 0).toLocaleString() }}
                      </small>
                    </td>
                    <td>
                      <small class="text-muted">{{ formatDate(ag.created_at) }}</small>
                    </td>
                    <td class="text-right text-nowrap">
                      <button 
                        class="btn btn-sm mr-1 font-weight-bold"
                        :class="ag.is_approved ? 'btn-outline-warning' : 'btn-success'"
                        @click="toggleAgentApproval(ag)"
                        :title="ag.is_approved ? 'এজেন্ট সাময়িক স্থগিত করুন' : 'এজেন্ট অনুমোদন করুন'"
                      >
                        <i :class="ag.is_approved ? 'icofont-ban mr-1' : 'icofont-check-circled mr-1'"></i>
                        {{ ag.is_approved ? 'Disable' : 'Approve' }}
                      </button>
                      <button 
                        class="btn btn-sm btn-outline-success mr-1" 
                        @click="openWalletModal(ag)" 
                        title="ওয়ালেট রিচার্জ বা টাকা প্রত্যাহার"
                      >
                        <i class="icofont-wallet mr-1"></i>ব্যালেন্স
                      </button>
                      <button 
                        class="btn btn-sm btn-outline-danger" 
                        @click="deleteAgent(ag)" 
                        title="মুছে ফেলুন"
                      >
                        <i class="icofont-ui-delete"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Agents Pagination -->
              <div v-if="agentPagination.total > agentPagination.per_page" class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3 flex-wrap">
                <div class="small text-muted mb-2 mb-sm-0">
                  Showing {{ agentPagination.from || 0 }} - {{ agentPagination.to || 0 }} of {{ agentPagination.total }} agents
                </div>
                <nav>
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: agentPagination.current_page <= 1 }">
                      <button class="page-link" @click="fetchAgents(agentPagination.current_page - 1)" :disabled="agentPagination.current_page <= 1">‹ Prev</button>
                    </li>
                    <li class="page-item active">
                      <span class="page-link bg-success border-success">{{ agentPagination.current_page }} / {{ agentPagination.last_page }}</span>
                    </li>
                    <li class="page-item" :class="{ disabled: agentPagination.current_page >= agentPagination.last_page }">
                      <button class="page-link" @click="fetchAgents(agentPagination.current_page + 1)" :disabled="agentPagination.current_page >= agentPagination.last_page">Next ›</button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>

          <!-- TAB: STAFF MANAGEMENT -->
          <div v-if="activeTab === 'staff'">
            <div v-if="loadingStaff" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
            </div>

            <div v-else-if="staff.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-users-alt-5 display-4 text-danger"></i>
              <p class="mt-2">কোনো সহকারী স্টাফ পাওয়া যায়নি (No staff found).</p>
              <button class="btn btn-sm btn-danger mt-2" @click="openAddStaffModal">
                <i class="icofont-plus mr-1"></i> নতুন স্টাফ যোগ করুন
              </button>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>স্টাফের নাম</th>
                    <th>নিযুক্ত ডাক্তার ও চেম্বার</th>
                    <th>মোবাইল নম্বর (লগইন আইডি)</th>
                    <th>সিক্রেট পিন</th>
                    <th>স্ট্যাটাস</th>
                    <th>যোগদানের তারিখ</th>
                    <th class="text-right">অ্যাকশন</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="st in staff" :key="st.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 38px; height: 38px; font-size: 15px;">
                          {{ st.name ? st.name.charAt(0).toUpperCase() : 'S' }}
                        </div>
                        <div>
                          <span class="font-weight-bold text-dark d-block">{{ st.name }}</span>
                          <span class="badge badge-light border text-muted">Assistant ID #{{ st.id }}</span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div v-if="st.doctor">
                        <div class="font-weight-bold text-primary">
                          <i class="icofont-doctor-alt mr-1"></i>{{ st.doctor.name }}
                        </div>
                        <small class="text-muted">{{ st.doctor.specialization }} &bull; {{ st.doctor.chamber_address || 'চেম্বার' }}</small>
                      </div>
                      <span v-else class="badge badge-secondary">ডাক্তার সংযুক্ত নেই</span>
                    </td>
                    <td>
                      <strong><i class="icofont-phone mr-1 text-muted"></i>{{ st.phone }}</strong>
                    </td>
                    <td>
                      <span class="badge badge-dark font-monospace px-2 py-1" style="letter-spacing: 2px;">
                        ••••
                      </span>
                    </td>
                    <td>
                      <button 
                        class="btn btn-sm py-0 px-2 font-weight-bold"
                        :class="st.is_active ? 'btn-success' : 'btn-secondary'"
                        @click="toggleStaffStatus(st)"
                        title="স্ট্যাটাস পরিবর্তন করতে ক্লিক করুন"
                      >
                        <i :class="st.is_active ? 'icofont-check-circled' : 'icofont-close-circled'" class="mr-1"></i>
                        {{ st.is_active ? 'সক্রিয় (Active)' : 'স্থগিত (Inactive)' }}
                      </button>
                    </td>
                    <td>
                      <small class="text-muted">{{ formatDate(st.created_at) }}</small>
                    </td>
                    <td class="text-right">
                      <button 
                        class="btn btn-sm btn-outline-warning mr-1" 
                        @click="openResetPinModal(st)" 
                        title="নতুন পিন সেট করুন"
                      >
                        <i class="icofont-key mr-1"></i>পিন পরিবর্তন
                      </button>
                      <button 
                        class="btn btn-sm btn-outline-danger" 
                        @click="deleteStaff(st)" 
                        title="মুছে ফেলুন"
                      >
                        <i class="icofont-ui-delete"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Staff Pagination -->
              <div v-if="staffPagination.total > staffPagination.per_page" class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3 flex-wrap">
                <div class="small text-muted mb-2 mb-sm-0">
                  Showing {{ staffPagination.from || 0 }} - {{ staffPagination.to || 0 }} of {{ staffPagination.total }} staff
                </div>
                <nav>
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: staffPagination.current_page <= 1 }">
                      <button class="page-link" @click="fetchStaff(staffPagination.current_page - 1)" :disabled="staffPagination.current_page <= 1">‹ Prev</button>
                    </li>
                    <li class="page-item active">
                      <span class="page-link bg-danger border-danger">{{ staffPagination.current_page }} / {{ staffPagination.last_page }}</span>
                    </li>
                    <li class="page-item" :class="{ disabled: staffPagination.current_page >= staffPagination.last_page }">
                      <button class="page-link" @click="fetchStaff(staffPagination.current_page + 1)" :disabled="staffPagination.current_page >= staffPagination.last_page">Next ›</button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>

          <!-- TAB: PATIENTS MANAGEMENT -->
          <div v-if="activeTab === 'patients'">
            <div v-if="loadingPatients" class="text-center py-5">
              <div class="spinner-border text-info" role="status"></div>
            </div>

            <div v-else-if="patients.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-patient-file display-4 text-info"></i>
              <p class="mt-2">কোনো রোগীর রেকর্ড পাওয়া যায়নি (No patients found).</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>রোগী আইডি / নাম</th>
                    <th>মোবাইল নম্বর</th>
                    <th>ইমেইল এড্রেস</th>
                    <th>মোট বুকিং ও সিরিয়াল</th>
                    <th>নিবন্ধনের তারিখ</th>
                    <th class="text-right">অ্যাকশন</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pt in patients" :key="pt.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 38px; height: 38px; font-size: 15px;">
                          {{ pt.name ? pt.name.charAt(0).toUpperCase() : 'P' }}
                        </div>
                        <div>
                          <span class="font-weight-bold text-dark d-block">{{ pt.name }}</span>
                          <span class="badge badge-light border text-muted">Patient ID #{{ pt.id }}</span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <strong><i class="icofont-phone mr-1 text-muted"></i>{{ pt.phone || 'N/A' }}</strong>
                    </td>
                    <td>
                      <span class="text-muted"><i class="icofont-envelope mr-1 text-muted"></i>{{ pt.email || 'N/A' }}</span>
                    </td>
                    <td>
                      <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 13px;">
                        <i class="icofont-calendar mr-1"></i>{{ pt.patient_appointments_count || 0 }} টি অ্যাপয়েন্টমেন্ট
                      </span>
                    </td>
                    <td>
                      <small class="text-muted">{{ formatDate(pt.created_at) }}</small>
                    </td>
                    <td class="text-right">
                      <button 
                        class="btn btn-sm btn-outline-danger" 
                        @click="deletePatient(pt)" 
                        title="রোগীর অ্যাকাউন্ট মুছে ফেলুন"
                      >
                        <i class="icofont-ui-delete mr-1"></i>মুছুন
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Patients Pagination -->
              <div v-if="patientPagination.total > patientPagination.per_page" class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3 flex-wrap">
                <div class="small text-muted mb-2 mb-sm-0">
                  Showing {{ patientPagination.from || 0 }} - {{ patientPagination.to || 0 }} of {{ patientPagination.total }} patients
                </div>
                <nav>
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: patientPagination.current_page <= 1 }">
                      <button class="page-link" @click="fetchPatients(patientPagination.current_page - 1)" :disabled="patientPagination.current_page <= 1">‹ Prev</button>
                    </li>
                    <li class="page-item active">
                      <span class="page-link bg-info border-info">{{ patientPagination.current_page }} / {{ patientPagination.last_page }}</span>
                    </li>
                    <li class="page-item" :class="{ disabled: patientPagination.current_page >= patientPagination.last_page }">
                      <button class="page-link" @click="fetchPatients(patientPagination.current_page + 1)" :disabled="patientPagination.current_page >= patientPagination.last_page">Next ›</button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>

          <!-- TAB 3: SAAS PAYMENTS LEDGER -->
          <div v-if="activeTab === 'payments'">
            <div v-if="loadingPayments" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
            </div>

            <div v-else-if="payments.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-pay display-4"></i>
              <p class="mt-2">No payment transactions recorded.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Txn ID</th>
                    <th>Date</th>
                    <th>Doctor</th>
                    <th>Patient</th>
                    <th>Total Amount</th>
                    <th>Admin Commission (10%)</th>
                    <th>Doctor Net (90%)</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pay in payments" :key="pay.id">
                    <td>
                      <span class="badge badge-secondary">{{ pay.transaction_id }}</span>
                    </td>
                    <td>{{ new Date(pay.created_at).toLocaleDateString() }}</td>
                    <td>
                      <div class="font-weight-bold text-dark">{{ pay.doctor?.name }}</div>
                      <small class="text-primary">{{ pay.doctor?.specialization }}</small>
                    </td>
                    <td>
                      <div class="font-weight-bold">{{ pay.appointment?.patient_name }}</div>
                    </td>
                    <td>
                      <strong class="text-dark">${{ pay.amount }}</strong>
                    </td>
                    <td>
                      <strong class="text-success">+${{ pay.admin_commission }}</strong>
                    </td>
                    <td>
                      <strong class="text-primary">${{ pay.doctor_earning }}</strong>
                    </td>
                    <td>
                      <span class="badge badge-success">{{ pay.status }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- TAB 4: INQUIRIES -->
          <div v-if="activeTab === 'messages'">
            <div v-if="loadingMessages" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
            </div>

            <div v-else-if="messages.length === 0" class="text-center py-5 text-muted">
              <i class="icofont-envelope display-4"></i>
              <p class="mt-2">No inquiries.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Date</th>
                    <th>Sender</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="msg in messages" :key="msg.id">
                    <td>{{ new Date(msg.created_at).toLocaleDateString() }}</td>
                    <td>
                      <div class="font-weight-bold">{{ msg.name }}</div>
                      <small class="text-muted">{{ msg.email }} &bull; {{ msg.phone }}</small>
                    </td>
                    <td><strong>{{ msg.subject }}</strong></td>
                    <td><p class="mb-0 small text-muted" style="max-width: 350px;">{{ msg.message }}</p></td>
                    <td>
                      <span v-if="msg.is_read" class="badge badge-light border">Read</span>
                      <button v-else class="btn btn-sm btn-outline-success" @click="markRead(msg)">Mark Read</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- TAB: EMERGENCY HUB (Ambulance, ICU Beds, Blood SOS, Health Passports) -->
          <div v-if="activeTab === 'emergency'">
            <EmergencyAdminManagement />
          </div>

          <!-- TAB: LEGAL POLICIES & TERMS (Dynamic Privacy Policy & Terms of Service) -->
          <div v-if="activeTab === 'policies'">
            <PolicyAdminManagement />
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- EDIT DOCTOR MODAL -->
  <div v-if="editingDoctor" class="modal-backdrop fade show" style="opacity: 0.6; z-index: 1040;"></div>
  <div 
    v-if="editingDoctor" 
    class="modal fade show d-block" 
    tabindex="-1" 
    style="z-index: 1050; overflow-y: auto;"
  >
    <div class="modal-dialog modal-lg modal-dialog-centered my-4">
      <div class="modal-content shadow-lg border-0 rounded-lg">
        <div class="modal-header bg-primary text-white py-3">
          <h5 class="modal-title font-weight-bold mb-0">
            <i class="icofont-ui-edit mr-2"></i>ডাক্তারের তথ্য সম্পাদনা (Edit Doctor ID #{{ editForm.id }})
          </h5>
          <button type="button" class="close text-white" @click="closeEditDoctor" aria-label="Close" style="opacity: 0.9;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form @submit.prevent="saveDoctorEdit">
          <div class="modal-body p-4" style="max-height: 72vh; overflow-y: auto;">
            <div class="row">
              <!-- Name -->
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">ডাক্তারের নাম (Doctor Name) <span class="text-danger">*</span></label>
                <input type="text" v-model="editForm.name" class="form-control" required placeholder="ডাঃ মোঃ ...">
              </div>

              <!-- Specialization -->
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">বিশেষজ্ঞতা (Specialization) <span class="text-danger">*</span></label>
                <input type="text" v-model="editForm.specialization" class="form-control" required placeholder="মেডিসিন / কার্ডিওলজি...">
              </div>

              <!-- Designation -->
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">পদবি (Designation)</label>
                <input type="text" v-model="editForm.designation" class="form-control" placeholder="সহকারী অধ্যাপক / সহযোগী অধ্যাপক / কনসালট্যান্ট">
              </div>

              <!-- BMDC Number -->
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">BMDC রেজিস্ট্রেশন নম্বর</label>
                <input type="text" v-model="editForm.bmdc_number" class="form-control" placeholder="A-12345">
              </div>

              <!-- Qualifications -->
              <div class="col-12 form-group">
                <label class="font-weight-bold text-dark">ডিগ্রি ও শিক্ষাগত যোগ্যতা (Qualifications)</label>
                <textarea v-model="editForm.qualifications" class="form-control" rows="2" placeholder="MBBS, FCPS (Medicine), MD..."></textarea>
              </div>

              <!-- Chamber Address -->
              <div class="col-12 form-group">
                <label class="font-weight-bold text-dark">চেম্বারের ঠিকানা ও হাসপাতাল (Chamber Address)</label>
                <input type="text" v-model="editForm.chamber_address" class="form-control" placeholder="ইবনে সিনা ডায়াগনস্টিক সেন্টার, ধানমন্ডি...">
              </div>

              <!-- District & Upazila -->
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">জেলা (District)</label>
                <input type="text" v-model="editForm.district" class="form-control" placeholder="ঢাকা / চট্টগ্রাম...">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">উপজেলা / এলাকা (Upazila / Area)</label>
                <input type="text" v-model="editForm.upazila" class="form-control" placeholder="ধানমন্ডি / গুলশান...">
              </div>

              <!-- Phone & Email -->
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">মোবাইল নম্বর (Phone)</label>
                <input type="text" v-model="editForm.phone" class="form-control" placeholder="017XXXXXXXX">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">ইমেইল (Email)</label>
                <input type="email" v-model="editForm.email" class="form-control" placeholder="doctor@example.com">
              </div>

              <!-- Fees -->
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">চেম্বার ফি (৳ Consultation Fee)</label>
                <input type="number" v-model.number="editForm.consultation_fee" class="form-control" min="0" placeholder="1000">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">অনলাইন টেলিমেডিসিন ফি (৳ Fee)</label>
                <input type="number" v-model.number="editForm.telemedicine_fee" class="form-control" min="0" placeholder="800">
              </div>

              <!-- Times -->
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">শুরুর সময় (Start Time)</label>
                <input type="text" v-model="editForm.start_time" class="form-control" placeholder="05:00 PM">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">শেষের সময় (End Time)</label>
                <input type="text" v-model="editForm.end_time" class="form-control" placeholder="09:00 PM">
              </div>

              <!-- Available Days -->
              <div class="col-12 form-group">
                <label class="font-weight-bold text-dark d-block">রোগী দেখার দিনসমূহ (Available Days)</label>
                <div class="d-flex flex-wrap gap-2">
                  <div v-for="day in weekDays" :key="day.en" class="form-check form-check-inline mr-3">
                    <input 
                      class="form-check-input cursor-pointer" 
                      type="checkbox" 
                      :id="'day_' + day.en" 
                      :value="day.bn" 
                      v-model="editForm.available_days"
                    >
                    <label class="form-check-label cursor-pointer" :for="'day_' + day.en">{{ day.bn }} ({{ day.en }})</label>
                  </div>
                </div>
              </div>

              <!-- Status Switches -->
              <div class="col-12 bg-light p-3 rounded">
                <label class="font-weight-bold text-dark d-block mb-2">স্ট্যাটাস ও ভেরিফিকেশন (Status Settings)</label>
                <div class="d-flex flex-wrap gap-3">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input cursor-pointer" type="checkbox" id="edit_is_approved" v-model="editForm.is_approved">
                    <label class="form-check-label font-weight-bold cursor-pointer text-success" for="edit_is_approved">
                      <i class="icofont-check-circled mr-1"></i>Approved (অনুমোদিত)
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input cursor-pointer" type="checkbox" id="edit_is_verified" v-model="editForm.is_verified">
                    <label class="form-check-label font-weight-bold cursor-pointer text-primary" for="edit_is_verified">
                      <i class="icofont-badge mr-1"></i>BMDC Verified (ভেরিফাইড ব্যাজ)
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input cursor-pointer" type="checkbox" id="edit_is_featured" v-model="editForm.is_featured">
                    <label class="form-check-label font-weight-bold cursor-pointer text-warning" for="edit_is_featured">
                      <i class="icofont-star mr-1"></i>Featured (শীর্ষে প্রদর্শন)
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input cursor-pointer" type="checkbox" id="edit_is_emergency" v-model="editForm.is_emergency_available">
                    <label class="form-check-label font-weight-bold cursor-pointer text-danger" for="edit_is_emergency">
                      <i class="icofont-ambulance-cross mr-1"></i>Emergency Ready (জরুরি সেবা)
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light py-3">
            <button type="button" class="btn btn-secondary" @click="closeEditDoctor" :disabled="savingDoctor">
              বাতিল (Cancel)
            </button>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold" :disabled="savingDoctor">
              <span v-if="savingDoctor" class="spinner-border spinner-border-sm mr-1"></span>
              <i v-else class="icofont-save mr-1"></i>
              {{ savingDoctor ? 'সংরক্ষণ হচ্ছে...' : 'পরিবর্তন সংরক্ষণ করুন (Save)' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ADD AGENT MODAL -->
  <div v-if="showingAddAgentModal" class="modal-backdrop fade show" style="opacity: 0.6; z-index: 1040;"></div>
  <div 
    v-if="showingAddAgentModal" 
    class="modal fade show d-block" 
    tabindex="-1" 
    style="z-index: 1050; overflow-y: auto;"
  >
    <div class="modal-dialog modal-lg modal-dialog-centered my-4">
      <div class="modal-content shadow-lg border-0 rounded-lg">
        <div class="modal-header bg-success text-white py-3">
          <h5 class="modal-title font-weight-bold mb-0">
            <i class="icofont-plus-circle mr-2"></i>নতুন হেলথ এজেন্ট নিবন্ধন (Add Health Agent)
          </h5>
          <button type="button" class="close text-white" @click="closeAddAgentModal" aria-label="Close" style="opacity: 0.9;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form @submit.prevent="saveNewAgent">
          <div class="modal-body p-4">
            <div class="row">
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">এজেন্টের নাম <span class="text-danger">*</span></label>
                <input type="text" v-model="agentForm.name" class="form-control" required placeholder="মোঃ রফিকুল ইসলাম">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">ফার্মেসি বা সেন্টারের নাম <span class="text-danger">*</span></label>
                <input type="text" v-model="agentForm.pharmacy_name" class="form-control" required placeholder="জনসেবা ফার্মেসি">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">মোবাইল নম্বর <span class="text-danger">*</span></label>
                <input type="text" v-model="agentForm.phone" class="form-control" required placeholder="017XXXXXXXX">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">ইমেইল এড্রেস <span class="text-danger">*</span></label>
                <input type="email" v-model="agentForm.email" class="form-control" required placeholder="agent@sukhimanush.com">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">পাসওয়ার্ড <span class="text-danger">*</span></label>
                <input type="password" v-model="agentForm.password" class="form-control" required minlength="6" placeholder="কমপক্ষে ৬ ডিজিট">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">প্রারম্ভিক ওয়ালেট ব্যালেন্স (৳)</label>
                <input type="number" v-model.number="agentForm.balance" class="form-control" min="0" placeholder="0">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">জেলা (District) <span class="text-danger">*</span></label>
                <input type="text" v-model="agentForm.district" class="form-control" required placeholder="বগুড়া / রাজশাহী / ঢাকা...">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold text-dark">উপজেলা / এলাকা (Upazila) <span class="text-danger">*</span></label>
                <input type="text" v-model="agentForm.upazila" class="form-control" required placeholder="শিবগঞ্জ / গাবতলী...">
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light py-3">
            <button type="button" class="btn btn-secondary" @click="closeAddAgentModal" :disabled="savingAgent">বাতিল</button>
            <button type="submit" class="btn btn-success px-4 font-weight-bold" :disabled="savingAgent">
              <span v-if="savingAgent" class="spinner-border spinner-border-sm mr-1"></span>
              <i v-else class="icofont-check-circled mr-1"></i>
              {{ savingAgent ? 'সংরক্ষণ হচ্ছে...' : 'নিবন্ধন সম্পন্ন করুন' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ADJUST AGENT WALLET MODAL -->
  <div v-if="showingWalletModal" class="modal-backdrop fade show" style="opacity: 0.6; z-index: 1040;"></div>
  <div 
    v-if="showingWalletModal" 
    class="modal fade show d-block" 
    tabindex="-1" 
    style="z-index: 1050;"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg border-0 rounded-lg">
        <div class="modal-header bg-success text-white py-3">
          <h5 class="modal-title font-weight-bold mb-0">
            <i class="icofont-wallet mr-2"></i>এজেন্ট ওয়ালেট ব্যালেন্স সমন্বয়
          </h5>
          <button type="button" class="close text-white" @click="closeWalletModal" aria-label="Close" style="opacity: 0.9;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form @submit.prevent="saveWalletAdjustment">
          <div class="modal-body p-4">
            <div class="alert alert-light border mb-3">
              <div class="font-weight-bold text-dark">{{ walletForm.agent_name }}</div>
              <div class="small text-muted">বর্তমান ব্যালেন্স: <strong class="text-success">৳ {{ Number(walletForm.current_balance || 0).toLocaleString() }}</strong></div>
            </div>

            <div class="form-group">
              <label class="font-weight-bold text-dark">অ্যাকশন ধরণ</label>
              <div class="d-flex gap-3">
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="walletCredit" value="credit" v-model="walletForm.action" class="custom-control-input">
                  <label class="custom-control-label font-weight-bold text-success cursor-pointer" for="walletCredit">টাকা রিচার্জ / যোগ করুন (Credit)</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="walletDebit" value="debit" v-model="walletForm.action" class="custom-control-input">
                  <label class="custom-control-label font-weight-bold text-danger cursor-pointer" for="walletDebit">টাকা কর্তন / উত্তোলন (Debit)</label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="font-weight-bold text-dark">টাকার পরিমাণ (৳) <span class="text-danger">*</span></label>
              <input type="number" v-model.number="walletForm.amount" min="1" step="0.01" class="form-control form-control-lg font-weight-bold" required placeholder="যেমন: 500">
            </div>

            <div class="form-group mb-0">
              <label class="font-weight-bold text-dark">মন্তব্য / রেফারেন্স নোট</label>
              <input type="text" v-model="walletForm.note" class="form-control" placeholder="যেমন: ব্যাংক ডিপোজিট স্লিপ #TX-8292">
            </div>
          </div>

          <div class="modal-footer bg-light py-3">
            <button type="button" class="btn btn-secondary" @click="closeWalletModal" :disabled="savingWallet">বাতিল</button>
            <button type="submit" class="btn btn-success px-4 font-weight-bold" :disabled="savingWallet">
              <span v-if="savingWallet" class="spinner-border spinner-border-sm mr-1"></span>
              <i v-else class="icofont-check mr-1"></i>
              {{ savingWallet ? 'আপডেট হচ্ছে...' : 'ব্যালেন্স আপডেট করুন' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ADD STAFF MODAL -->
  <div v-if="showingAddStaffModal" class="modal-backdrop fade show" style="opacity: 0.6; z-index: 1040;"></div>
  <div 
    v-if="showingAddStaffModal" 
    class="modal fade show d-block" 
    tabindex="-1" 
    style="z-index: 1050; overflow-y: auto;"
  >
    <div class="modal-dialog modal-dialog-centered my-4">
      <div class="modal-content shadow-lg border-0 rounded-lg">
        <div class="modal-header bg-danger text-white py-3">
          <h5 class="modal-title font-weight-bold mb-0">
            <i class="icofont-user-suited mr-2"></i>নতুন চেম্বার সহকারী / স্টাফ যোগ করুন
          </h5>
          <button type="button" class="close text-white" @click="closeAddStaffModal" aria-label="Close" style="opacity: 0.9;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form @submit.prevent="saveNewStaff">
          <div class="modal-body p-4">
            <div class="form-group">
              <label class="font-weight-bold text-dark">নিযুক্ত ডাক্তার নির্বাচন করুন <span class="text-danger">*</span></label>
              <select v-model="staffForm.doctor_id" class="form-control" required>
                <option value="" disabled>-- ডাক্তার নির্বাচন করুন --</option>
                <option v-for="doc in allDoctorsList" :key="doc.id" :value="doc.id">
                  {{ doc.name }} ({{ doc.specialization }}) - {{ doc.chamber_address || 'চেম্বার' }}
                </option>
              </select>
              <small class="text-muted">এই সহকারী স্টাফ শুধুমাত্র নির্বাচিত ডাক্তারের সিরিয়াল ও লাইভ কলিং পরিচালনা করতে পারবেন।</small>
            </div>

            <div class="form-group">
              <label class="font-weight-bold text-dark">সহকারীর পুরো নাম <span class="text-danger">*</span></label>
              <input type="text" v-model="staffForm.name" class="form-control" required placeholder="যেমন: মোঃ কামরুল হাসান">
            </div>

            <div class="form-group">
              <label class="font-weight-bold text-dark">মোবাইল নম্বর (লগইন আইডি হিসেবে ব্যবহার হবে) <span class="text-danger">*</span></label>
              <input type="text" v-model="staffForm.phone" class="form-control" required placeholder="01XXXXXXXXX">
            </div>

            <div class="form-group mb-0">
              <label class="font-weight-bold text-dark">৪ ডিজিটের গোপন পিন (PIN) <span class="text-danger">*</span></label>
              <input type="password" v-model="staffForm.pin" class="form-control" required maxlength="6" minlength="4" placeholder="যেমন: 1234">
              <small class="text-muted">স্টাফ এই মোবাইল নম্বর এবং পিন দিয়ে অ্যাসিস্ট্যান্ট পোর্টালে লগইন করবেন।</small>
            </div>
          </div>

          <div class="modal-footer bg-light py-3">
            <button type="button" class="btn btn-secondary" @click="closeAddStaffModal" :disabled="savingStaff">বাতিল</button>
            <button type="submit" class="btn btn-danger px-4 font-weight-bold" :disabled="savingStaff">
              <span v-if="savingStaff" class="spinner-border spinner-border-sm mr-1"></span>
              <i v-else class="icofont-check-circled mr-1"></i>
              {{ savingStaff ? 'সংরক্ষণ হচ্ছে...' : 'স্টাফ সংরক্ষণ করুন' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- RESET STAFF PIN MODAL -->
  <div v-if="showingResetPinModal" class="modal-backdrop fade show" style="opacity: 0.6; z-index: 1040;"></div>
  <div 
    v-if="showingResetPinModal" 
    class="modal fade show d-block" 
    tabindex="-1" 
    style="z-index: 1050;"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg border-0 rounded-lg">
        <div class="modal-header bg-warning text-dark py-3">
          <h5 class="modal-title font-weight-bold mb-0">
            <i class="icofont-key mr-2"></i>স্টাফের নতুন পিন সেট করুন
          </h5>
          <button type="button" class="close text-dark" @click="closeResetPinModal" aria-label="Close" style="opacity: 0.9;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form @submit.prevent="saveResetPin">
          <div class="modal-body p-4">
            <div class="alert alert-light border mb-3">
              <div class="font-weight-bold text-dark">{{ pinForm.staff_name }}</div>
              <small class="text-muted">লগইন আইডি / ফোন: <strong>{{ pinForm.phone }}</strong></small>
            </div>

            <div class="form-group mb-0">
              <label class="font-weight-bold text-dark">নতুন গোপন পিন (New PIN) <span class="text-danger">*</span></label>
              <input type="password" v-model="pinForm.pin" class="form-control form-control-lg font-weight-bold text-center" required maxlength="6" minlength="4" placeholder="••••">
              <small class="text-muted">কমপক্ষে ৪ ডিজিটের সংখ্যা লিখুন।</small>
            </div>
          </div>

          <div class="modal-footer bg-light py-3">
            <button type="button" class="btn btn-secondary" @click="closeResetPinModal" :disabled="savingPin">বাতিল</button>
            <button type="submit" class="btn btn-warning px-4 font-weight-bold" :disabled="savingPin">
              <span v-if="savingPin" class="spinner-border spinner-border-sm mr-1"></span>
              <i v-else class="icofont-check mr-1"></i>
              {{ savingPin ? 'রিসেট হচ্ছে...' : 'পিন আপডেট করুন' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import EmergencyAdminManagement from '../../components/EmergencyAdminManagement.vue';
import PolicyAdminManagement from '../../components/PolicyAdminManagement.vue';

export default {
  name: 'SuperAdminDashboard',
  components: {
    EmergencyAdminManagement,
    PolicyAdminManagement,
  },
  data() {
    return {
      activeTab: 'appointments',
      stats: {},
      appointments: [],
      doctors: [],
      payments: [],
      messages: [],
      filterStatus: 'all',
      searchQuery: '',
      loadingAppointments: false,
      loadingDoctors: false,
      loadingPayments: false,
      loadingMessages: false,

      // Doctors Management Search, Filter & Pagination
      doctorSearch: '',
      doctorFilterStatus: 'all',
      doctorFilterVerified: 'all',
      doctorPagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
        from: 0,
        to: 0,
        per_page: 15,
      },
      doctorSearchTimeout: null,

      // Edit Doctor Modal
      editingDoctor: false,
      savingDoctor: false,
      editForm: {
        id: null,
        name: '',
        specialization: '',
        designation: '',
        qualifications: '',
        chamber_address: '',
        phone: '',
        email: '',
        district: '',
        upazila: '',
        consultation_fee: 1000,
        telemedicine_fee: 800,
        bmdc_number: '',
        start_time: '',
        end_time: '',
        available_days: [],
        is_approved: true,
        is_verified: false,
        is_featured: false,
        is_emergency_available: false,
      },
      weekDays: [
        { en: 'Saturday', bn: 'শনিবার' },
        { en: 'Sunday', bn: 'রবিবার' },
        { en: 'Monday', bn: 'সোমবার' },
        { en: 'Tuesday', bn: 'মঙ্গলবার' },
        { en: 'Wednesday', bn: 'বুধবার' },
        { en: 'Thursday', bn: 'বৃহস্পতিবার' },
        { en: 'Friday', bn: 'শুক্রবার' },
      ],

      // Agents Management
      agents: [],
      loadingAgents: false,
      agentSearch: '',
      agentStatusFilter: 'all',
      agentPagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
        from: 0,
        to: 0,
        per_page: 15,
      },
      agentSearchTimeout: null,
      showingAddAgentModal: false,
      savingAgent: false,
      agentForm: {
        name: '',
        pharmacy_name: '',
        phone: '',
        email: '',
        password: '',
        balance: 0,
        district: '',
        upazila: '',
      },
      showingWalletModal: false,
      savingWallet: false,
      walletForm: {
        agent_id: null,
        agent_name: '',
        current_balance: 0,
        action: 'credit',
        amount: '',
        note: '',
      },

      // Staff (Assistants) Management
      staff: [],
      loadingStaff: false,
      staffSearch: '',
      staffFilterStatus: 'all',
      staffPagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
        from: 0,
        to: 0,
        per_page: 15,
      },
      staffSearchTimeout: null,
      showingAddStaffModal: false,
      savingStaff: false,
      staffForm: {
        doctor_id: '',
        name: '',
        phone: '',
        pin: '',
      },
      showingResetPinModal: false,
      savingPin: false,
      pinForm: {
        staff_id: null,
        staff_name: '',
        phone: '',
        pin: '',
      },
      allDoctorsList: [],

      // Patients Management
      patients: [],
      loadingPatients: false,
      patientSearch: '',
      patientPagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
        from: 0,
        to: 0,
        per_page: 15,
      },
      patientSearchTimeout: null,
    };
  },
  computed: {
    visibleDoctorPages() {
      const current = this.doctorPagination.current_page || 1;
      const last = this.doctorPagination.last_page || 1;
      const delta = 2;
      const range = [];
      const rangeWithDots = [];
      let l;

      for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
          range.push(i);
        }
      }

      for (const i of range) {
        if (l) {
          if (i - l === 2) {
            rangeWithDots.push(l + 1);
          } else if (i - l !== 1) {
            rangeWithDots.push('...');
          }
        }
        rangeWithDots.push(i);
        l = i;
      }

      return rangeWithDots;
    },
  },
  mounted() {
    this.fetchStats();
    this.fetchAppointments();
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '';
      const cleanDate = dateStr.includes('T') ? dateStr.split('T')[0] : dateStr;
      const parts = cleanDate.split('-');
      if (parts.length === 3) {
        const d = new Date(parts[0], parts[1] - 1, parts[2]);
        return d.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
        });
      }
      return cleanDate;
    },
    async fetchStats() {
      try {
        const res = await axios.get('/admin/stats');
        this.stats = res.data;
      } catch (err) {
        console.error('Error fetching admin stats:', err);
      }
    },
    async fetchAppointments(page = 1) {
      this.loadingAppointments = true;
      try {
        const res = await axios.get('/admin/appointments', {
          params: { status: this.filterStatus, search: this.searchQuery, page }
        });
        this.appointments = res.data.data || [];
      } catch (err) {
        console.error('Error fetching appointments:', err);
      } finally {
        this.loadingAppointments = false;
      }
    },
    async updateAppointmentStatus(apt, newStatus) {
      try {
        const res = await axios.patch(`/admin/appointments/${apt.id}/status`, { status: newStatus });
        if (res.data.success) {
          apt.status = newStatus;
          this.fetchStats();
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Appointment status: ${newStatus}`,
            showConfirmButton: false,
            timer: 2000,
          });
        }
      } catch (err) {
        Swal.fire('Error', 'Failed to update status.', 'error');
      }
    },
    onDoctorSearchInput() {
      clearTimeout(this.doctorSearchTimeout);
      this.doctorSearchTimeout = setTimeout(() => {
        this.fetchDoctors(1);
      }, 400);
    },
    async fetchDoctors(page = 1) {
      this.loadingDoctors = true;
      try {
        const res = await axios.get('/admin/doctors', {
          params: {
            page,
            search: this.doctorSearch,
            status: this.doctorFilterStatus,
            verified: this.doctorFilterVerified,
            per_page: 15,
          }
        });
        if (res.data && res.data.data) {
          this.doctors = res.data.data;
          this.doctorPagination = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total,
            from: res.data.from,
            to: res.data.to,
            per_page: res.data.per_page,
          };
        } else {
          this.doctors = Array.isArray(res.data) ? res.data : [];
        }
      } catch (err) {
        console.error('Error fetching doctors:', err);
      } finally {
        this.loadingDoctors = false;
      }
    },
    openEditDoctor(doc) {
      this.editForm = {
        id: doc.id,
        name: doc.name || '',
        specialization: doc.specialization || '',
        designation: doc.designation || '',
        qualifications: doc.qualifications || '',
        chamber_address: doc.chamber_address || '',
        phone: doc.phone || '',
        email: doc.email || '',
        district: doc.district || '',
        upazila: doc.upazila || '',
        consultation_fee: doc.consultation_fee ? Number(doc.consultation_fee) : 1000,
        telemedicine_fee: doc.telemedicine_fee ? Number(doc.telemedicine_fee) : 800,
        bmdc_number: doc.bmdc_number || '',
        start_time: doc.start_time || '',
        end_time: doc.end_time || '',
        available_days: Array.isArray(doc.available_days) ? [...doc.available_days] : [],
        is_approved: !!doc.is_approved,
        is_verified: !!doc.is_verified,
        is_featured: !!doc.is_featured,
        is_emergency_available: !!doc.is_emergency_available,
      };
      this.editingDoctor = true;
    },
    closeEditDoctor() {
      this.editingDoctor = false;
    },
    async saveDoctorEdit() {
      if (!this.editForm.name || !this.editForm.specialization) {
        Swal.fire('সতর্কতা', 'নাম এবং বিশেষজ্ঞতা পূরণ করা আবশ্যক।', 'warning');
        return;
      }
      this.savingDoctor = true;
      try {
        const res = await axios.put(`/admin/doctors/${this.editForm.id}`, this.editForm);
        if (res.data.success) {
          const idx = this.doctors.findIndex(d => d.id === this.editForm.id);
          if (idx !== -1) {
            this.doctors[idx] = { ...this.doctors[idx], ...res.data.doctor };
          }
          this.closeEditDoctor();
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: res.data.message || 'ডাক্তারের তথ্য সফলভাবে আপডেট হয়েছে',
            showConfirmButton: false,
            timer: 2500,
          });
          this.fetchStats();
        }
      } catch (err) {
        console.error('Error saving doctor edit:', err);
        Swal.fire('ত্রুটি', err.response?.data?.message || 'তথ্য সংরক্ষণ করা সম্ভব হয়নি।', 'error');
      } finally {
        this.savingDoctor = false;
      }
    },
    async deleteDoctor(doc) {
      const result = await Swal.fire({
        title: 'আপনি কি নিশ্চিত?',
        text: `"${doc.name}" ডাক্তারকে কি স্থায়ীভাবে তালিকা থেকে মুছে ফেলতে চান?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'হ্যাঁ, মুছে ফেলুন',
        cancelButtonText: 'বাতিল'
      });

      if (result.isConfirmed) {
        try {
          const res = await axios.delete(`/admin/doctors/${doc.id}`);
          if (res.data.success) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: res.data.message,
              showConfirmButton: false,
              timer: 2500
            });
            this.fetchDoctors(this.doctorPagination.current_page);
            this.fetchStats();
          }
        } catch (err) {
          console.error('Error deleting doctor:', err);
          Swal.fire('ত্রুটি', 'ডাক্তারকে মুছে ফেলা সম্ভব হয়নি।', 'error');
        }
      }
    },
    async toggleDoctorApproval(doc) {
      const newStatus = !doc.is_approved;
      try {
        const res = await axios.patch(`/admin/doctors/${doc.id}/status`, { is_approved: newStatus });
        if (res.data.success) {
          doc.is_approved = newStatus;
        }
      } catch (err) {
        Swal.fire('Error', 'Failed to update doctor status.', 'error');
      }
    },
    async toggleDoctorVerification(doc) {
      const newStatus = !doc.is_verified;
      try {
        const res = await axios.patch(`/admin/doctors/${doc.id}/status`, { is_verified: newStatus });
        if (res.data.success) {
          doc.is_verified = newStatus;
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Doctor ${doc.name} BMDC verification updated.`,
            showConfirmButton: false,
            timer: 2000
          });
        }
      } catch (err) {
        Swal.fire('Error', 'Failed to update doctor verification.', 'error');
      }
    },
    async fetchPayments() {
      this.loadingPayments = true;
      try {
        const res = await axios.get('/admin/payments');
        this.payments = res.data.data || [];
      } catch (err) {
        console.error('Error fetching payments:', err);
      } finally {
        this.loadingPayments = false;
      }
    },
    async fetchMessages() {
      this.loadingMessages = true;
      try {
        const res = await axios.get('/admin/messages');
        this.messages = res.data.data || [];
      } catch (err) {
        console.error('Error fetching messages:', err);
      } finally {
        this.loadingMessages = false;
      }
    },
    async markRead(msg) {
      try {
        await axios.post(`/admin/messages/${msg.id}/read`);
        msg.is_read = true;
        this.fetchStats();
      } catch (err) {
        console.error('Error marking message read:', err);
      }
    },
    // ==================== AGENTS METHODS ====================
    onAgentSearchInput() {
      clearTimeout(this.agentSearchTimeout);
      this.agentSearchTimeout = setTimeout(() => {
        this.fetchAgents(1);
      }, 400);
    },
    async fetchAgents(page = 1) {
      this.loadingAgents = true;
      try {
        const res = await axios.get('/admin/agents', {
          params: { 
            page, 
            search: this.agentSearch, 
            status: this.agentStatusFilter,
            per_page: 15 
          }
        });
        if (res.data && res.data.data) {
          this.agents = res.data.data;
          this.agentPagination = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total,
            from: res.data.from,
            to: res.data.to,
            per_page: res.data.per_page,
          };
        }
      } catch (err) {
        console.error('Error fetching agents:', err);
      } finally {
        this.loadingAgents = false;
      }
    },
    async toggleAgentApproval(ag) {
      const newStatus = !ag.is_approved;
      try {
        const res = await axios.patch(`/admin/agents/${ag.id}/status`, { is_approved: newStatus });
        if (res.data.success) {
          ag.is_approved = newStatus;
          this.fetchStats();
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: res.data.message || 'স্ট্যাটাস আপডেট সফল হয়েছে।',
            showConfirmButton: false,
            timer: 2200
          });
        }
      } catch (err) {
        console.error('Error toggling agent approval:', err);
        Swal.fire('Error', 'এজেন্ট স্ট্যাটাস আপডেট করা সম্ভব হয়নি।', 'error');
      }
    },
    openAddAgentModal() {
      this.agentForm = {
        name: '',
        pharmacy_name: '',
        phone: '',
        email: '',
        password: '',
        balance: 0,
        district: '',
        upazila: '',
      };
      this.showingAddAgentModal = true;
    },
    closeAddAgentModal() {
      this.showingAddAgentModal = false;
    },
    async saveNewAgent() {
      this.savingAgent = true;
      try {
        const res = await axios.post('/admin/agents', this.agentForm);
        if (res.data.success) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: res.data.message || 'এজেন্ট সফলভাবে নিবন্ধিত হয়েছে',
            showConfirmButton: false,
            timer: 2500,
          });
          this.closeAddAgentModal();
          this.fetchAgents(1);
          this.fetchStats();
        }
      } catch (err) {
        console.error('Error saving agent:', err);
        Swal.fire('ত্রুটি', err.response?.data?.message || 'এজেন্ট যোগ করা সম্ভব হয়নি।', 'error');
      } finally {
        this.savingAgent = false;
      }
    },
    openWalletModal(agent) {
      this.walletForm = {
        agent_id: agent.id,
        agent_name: agent.name + (agent.pharmacy_name ? ` (${agent.pharmacy_name})` : ''),
        current_balance: agent.agent_commission_balance || 0,
        action: 'credit',
        amount: '',
        note: '',
      };
      this.showingWalletModal = true;
    },
    closeWalletModal() {
      this.showingWalletModal = false;
    },
    async saveWalletAdjustment() {
      if (!this.walletForm.amount || this.walletForm.amount <= 0) {
        Swal.fire('সতর্কতা', 'সঠিক টাকার পরিমাণ লিখুন।', 'warning');
        return;
      }
      this.savingWallet = true;
      try {
        const res = await axios.patch(`/admin/agents/${this.walletForm.agent_id}/wallet`, {
          action: this.walletForm.action,
          amount: this.walletForm.amount,
          note: this.walletForm.note,
        });
        if (res.data.success) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: res.data.message || 'ওয়ালেট ব্যালেন্স সফলভাবে আপডেট হয়েছে',
            showConfirmButton: false,
            timer: 2500,
          });
          const idx = this.agents.findIndex(a => a.id === this.walletForm.agent_id);
          if (idx !== -1) {
            this.agents[idx].agent_commission_balance = res.data.new_balance;
          }
          this.closeWalletModal();
        }
      } catch (err) {
        console.error('Error adjusting wallet:', err);
        Swal.fire('ত্রুটি', err.response?.data?.message || 'ব্যালেন্স আপডেট করা সম্ভব হয়নি।', 'error');
      } finally {
        this.savingWallet = false;
      }
    },
    async deleteAgent(agent) {
      const result = await Swal.fire({
        title: 'আপনি কি নিশ্চিত?',
        text: `"${agent.name}" এজেন্টকে কি মুছে ফেলতে চান?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'হ্যাঁ, মুছুন',
        cancelButtonText: 'বাতিল'
      });

      if (result.isConfirmed) {
        try {
          const res = await axios.delete(`/admin/agents/${agent.id}`);
          if (res.data.success) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: res.data.message,
              showConfirmButton: false,
              timer: 2500
            });
            this.fetchAgents(this.agentPagination.current_page);
            this.fetchStats();
          }
        } catch (err) {
          console.error('Error deleting agent:', err);
          Swal.fire('ত্রুটি', 'এজেন্টকে মুছে ফেলা সম্ভব হয়নি।', 'error');
        }
      }
    },

    // ==================== STAFF (ASSISTANT) METHODS ====================
    onStaffSearchInput() {
      clearTimeout(this.staffSearchTimeout);
      this.staffSearchTimeout = setTimeout(() => {
        this.fetchStaff(1);
      }, 400);
    },
    async fetchStaff(page = 1) {
      this.loadingStaff = true;
      try {
        const res = await axios.get('/admin/staff', {
          params: { page, search: this.staffSearch, status: this.staffFilterStatus, per_page: 15 }
        });
        if (res.data && res.data.data) {
          this.staff = res.data.data;
          this.staffPagination = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total,
            from: res.data.from,
            to: res.data.to,
            per_page: res.data.per_page,
          };
        }
      } catch (err) {
        console.error('Error fetching staff:', err);
      } finally {
        this.loadingStaff = false;
      }
    },
    async loadAllDoctors() {
      if (this.allDoctorsList.length > 0) return;
      try {
        const res = await axios.get('/admin/doctors', { params: { per_page: 100 } });
        if (res.data && res.data.data) {
          this.allDoctorsList = res.data.data;
        } else if (Array.isArray(res.data)) {
          this.allDoctorsList = res.data;
        }
      } catch (err) {
        console.error('Error loading doctor list for staff:', err);
      }
    },
    openAddStaffModal() {
      this.loadAllDoctors();
      this.staffForm = {
        doctor_id: '',
        name: '',
        phone: '',
        pin: '',
      };
      this.showingAddStaffModal = true;
    },
    closeAddStaffModal() {
      this.showingAddStaffModal = false;
    },
    async saveNewStaff() {
      if (!this.staffForm.doctor_id || !this.staffForm.name || !this.staffForm.phone || !this.staffForm.pin) {
        Swal.fire('সতর্কতা', 'সকল প্রয়োজনীয় তথ্য প্রদান করুন।', 'warning');
        return;
      }
      this.savingStaff = true;
      try {
        const res = await axios.post('/admin/staff', this.staffForm);
        if (res.data.success) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: res.data.message || 'সহকারী স্টাফ সফলভাবে তৈরি হয়েছে',
            showConfirmButton: false,
            timer: 2500,
          });
          this.closeAddStaffModal();
          this.fetchStaff(1);
          this.fetchStats();
        }
      } catch (err) {
        console.error('Error creating staff:', err);
        Swal.fire('ত্রুটি', err.response?.data?.message || 'স্টাফ যোগ করা সম্ভব হয়নি।', 'error');
      } finally {
        this.savingStaff = false;
      }
    },
    async toggleStaffStatus(item) {
      try {
        const res = await axios.patch(`/admin/staff/${item.id}/toggle`);
        if (res.data.success) {
          item.is_active = res.data.is_active;
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: res.data.message,
            showConfirmButton: false,
            timer: 2000,
          });
        }
      } catch (err) {
        console.error('Error toggling staff status:', err);
        Swal.fire('ত্রুটি', 'স্ট্যাটাস পরিবর্তন করা সম্ভব হয়নি।', 'error');
      }
    },
    openResetPinModal(item) {
      this.pinForm = {
        staff_id: item.id,
        staff_name: item.name,
        phone: item.phone,
        pin: '',
      };
      this.showingResetPinModal = true;
    },
    closeResetPinModal() {
      this.showingResetPinModal = false;
    },
    async saveResetPin() {
      if (!this.pinForm.pin || this.pinForm.pin.length < 4) {
        Swal.fire('সতর্কতা', 'কমপক্ষে ৪ ডিজিটের পিন লিখুন।', 'warning');
        return;
      }
      this.savingPin = true;
      try {
        const res = await axios.patch(`/admin/staff/${this.pinForm.staff_id}/pin`, { pin: this.pinForm.pin });
        if (res.data.success) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: res.data.message || 'পিন সফলভাবে আপডেট হয়েছে',
            showConfirmButton: false,
            timer: 2500,
          });
          this.closeResetPinModal();
        }
      } catch (err) {
        console.error('Error resetting pin:', err);
        Swal.fire('ত্রুটি', err.response?.data?.message || 'পিন পরিবর্তন করা সম্ভব হয়নি।', 'error');
      } finally {
        this.savingPin = false;
      }
    },
    async deleteStaff(item) {
      const result = await Swal.fire({
        title: 'আপনি কি নিশ্চিত?',
        text: `"${item.name}" সহকারী স্টাফকে কি মুছে ফেলতে চান?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'হ্যাঁ, মুছুন',
        cancelButtonText: 'বাতিল'
      });

      if (result.isConfirmed) {
        try {
          const res = await axios.delete(`/admin/staff/${item.id}`);
          if (res.data.success) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: res.data.message,
              showConfirmButton: false,
              timer: 2500
            });
            this.fetchStaff(this.staffPagination.current_page);
            this.fetchStats();
          }
        } catch (err) {
          console.error('Error deleting staff:', err);
          Swal.fire('ত্রুটি', 'সহকারীকে মুছে ফেলা সম্ভব হয়নি।', 'error');
        }
      }
    },

    // ==================== PATIENT METHODS ====================
    onPatientSearchInput() {
      clearTimeout(this.patientSearchTimeout);
      this.patientSearchTimeout = setTimeout(() => {
        this.fetchPatients(1);
      }, 400);
    },
    async fetchPatients(page = 1) {
      this.loadingPatients = true;
      try {
        const res = await axios.get('/admin/patients', {
          params: { page, search: this.patientSearch, per_page: 15 }
        });
        if (res.data && res.data.data) {
          this.patients = res.data.data;
          this.patientPagination = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total,
            from: res.data.from,
            to: res.data.to,
            per_page: res.data.per_page,
          };
        }
      } catch (err) {
        console.error('Error fetching patients:', err);
      } finally {
        this.loadingPatients = false;
      }
    },
    async deletePatient(patient) {
      const result = await Swal.fire({
        title: 'আপনি কি নিশ্চিত?',
        text: `"${patient.name}" রোগীর অ্যাকাউন্ট কি স্থায়ীভাবে মুছে ফেলতে চান?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'হ্যাঁ, মুছে ফেলুন',
        cancelButtonText: 'বাতিল'
      });

      if (result.isConfirmed) {
        try {
          const res = await axios.delete(`/admin/patients/${patient.id}`);
          if (res.data.success) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: res.data.message,
              showConfirmButton: false,
              timer: 2500
            });
            this.fetchPatients(this.patientPagination.current_page);
            this.fetchStats();
          }
        } catch (err) {
          console.error('Error deleting patient:', err);
          Swal.fire('ত্রুটি', 'রোগীকে মুছে ফেলা সম্ভব হয়নি।', 'error');
        }
      }
    },
    logout() {
      localStorage.removeItem('saas_token');
      localStorage.removeItem('saas_user');
      localStorage.removeItem('saas_doctor');
      window.dispatchEvent(new Event('auth-change'));
      this.$router.push('/login');
    },
  },
};
</script>
