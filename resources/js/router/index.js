import { createRouter, createWebHistory } from 'vue-router';

// Eagerly loaded for instant first paint
import Home from '../views/Home.vue';

// Lazy-loaded Views (Code Splitting)
const About = () => import('../views/About.vue');
const Doctors = () => import('../views/Doctors.vue');
const DoctorDetail = () => import('../views/DoctorDetail.vue');
const DoctorPortfolio = () => import('../views/DoctorPortfolio.vue');
const AppointmentBooking = () => import('../views/AppointmentBooking.vue');
const AppointmentTracker = () => import('../views/AppointmentTracker.vue');
const Blog = () => import('../views/Blog.vue');
const BlogDetail = () => import('../views/BlogDetail.vue');
const Contact = () => import('../views/Contact.vue');
const VideoChamber = () => import('../views/VideoChamber.vue');
const PrivacyPolicy = () => import('../views/PrivacyPolicy.vue');
const TermsOfService = () => import('../views/TermsOfService.vue');

// Auth Views
const Login = () => import('../views/auth/Login.vue');
const Register = () => import('../views/auth/Register.vue');

// Portal Views
const PatientDashboard = () => import('../views/patient/PatientDashboard.vue');
const DoctorDashboard = () => import('../views/doctor/DoctorDashboard.vue');
const AdminDashboard = () => import('../views/admin/Dashboard.vue');
const StaffDashboard = () => import('../views/staff/StaffDashboard.vue');
const AgentDashboard = () => import('../views/AgentDashboard.vue');

// Bangladesh Health-Tech Ecosystem Views
const EmergencyHub = () => import('../views/EmergencyHub.vue');
const BloodSos = () => import('../views/BloodSos.vue');
const EmergencyResources = () => import('../views/EmergencyResources.vue');
const CostEstimator = () => import('../views/CostEstimator.vue');
const EmergencyCardView = () => import('../views/EmergencyCardView.vue');
const AiAssistant = () => import('../views/AiAssistant.vue');
const DiagnosticBooking = () => import('../views/DiagnosticBooking.vue');
const PrescriptionExplainer = () => import('../views/PrescriptionExplainer.vue');

const routes = [
    // Public Routes
    {
        path: '/',
        name: 'Home',
        component: Home,
    },
    {
        path: '/prescription-explainer',
        alias: ['/ai-prescription', '/prescription-reader', '/prescription-ai'],
        name: 'PrescriptionExplainer',
        component: PrescriptionExplainer,
    },
    {
        path: '/ai-assistant',
        alias: ['/symptom-checker', '/ai-doctor'],
        name: 'AiAssistant',
        component: AiAssistant,
    },
    {
        path: '/about',
        name: 'About',
        component: About,
    },
    {
        path: '/doctors',
        name: 'Doctors',
        component: Doctors,
    },
    {
        path: '/diagnostic-centers',
        alias: ['/diagnostic-booking', '/diagnostic-register', '/diagnostics', '/diagnostic'],
        name: 'DiagnosticBooking',
        component: DiagnosticBooking,
    },
    {
        path: '/doctor/:slug',
        alias: '/doctors/:slug',
        name: 'DoctorDetail',
        component: DoctorDetail,
        props: true,
    },
    {
        path: '/portfolio/:slug',
        alias: ['/dr/:slug', '/p/:slug'],
        name: 'DoctorPortfolio',
        component: DoctorPortfolio,
        props: true,
    },
    {
        path: '/appoinment',
        alias: ['/book-appointment', '/appointment'],
        name: 'AppointmentBooking',
        component: AppointmentBooking,
    },
    {
        path: '/track-appointment',
        name: 'AppointmentTracker',
        component: AppointmentTracker,
    },
    {
        path: '/video-chamber/:appointment_number',
        name: 'VideoChamber',
        component: VideoChamber,
        props: true,
    },
    {
        path: '/blog',
        name: 'Blog',
        component: Blog,
    },
    {
        path: '/blog/:slug',
        name: 'BlogDetail',
        component: BlogDetail,
        props: true,
    },
    {
        path: '/contact',
        name: 'Contact',
        component: Contact,
    },
    {
        path: '/privacy-policy',
        alias: ['/privacy', '/policy', '/privacy-terms'],
        name: 'PrivacyPolicy',
        component: PrivacyPolicy,
    },
    {
        path: '/terms-of-service',
        alias: ['/terms', '/tos', '/terms-and-conditions'],
        name: 'TermsOfService',
        component: TermsOfService,
    },

    // Auth Routes
    {
        path: '/login',
        alias: '/admin/login',
        name: 'Login',
        component: Login,
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
    },

    // Patient Health Portal
    {
        path: '/patient/dashboard',
        alias: '/patient',
        name: 'PatientDashboard',
        component: PatientDashboard,
        meta: { requiresAuth: true, roles: ['patient', 'user', 'doctor', 'staff', 'admin', 'super_admin'] },
    },

    // Doctor Portal
    {
        path: '/doctor/dashboard',
        alias: '/doctor',
        name: 'DoctorDashboard',
        component: DoctorDashboard,
        meta: { requiresAuth: true, roles: ['doctor', 'staff', 'admin', 'super_admin'] },
    },

    // Super Admin Control Center
    {
        path: '/admin/dashboard',
        alias: '/admin',
        name: 'AdminDashboard',
        component: AdminDashboard,
        meta: { requiresAuth: true, roles: ['admin', 'super_admin'] },
    },

    // Chamber Assistant / Staff Portal
    {
        path: '/staff/dashboard',
        alias: '/staff',
        name: 'StaffDashboard',
        component: StaffDashboard,
        meta: { requiresAuth: true, roles: ['staff', 'doctor', 'admin', 'super_admin'] },
    },

    // Rural Digital Health Point / Pharmacy Agent Portal
    {
        path: '/agent/dashboard',
        alias: '/agent',
        name: 'AgentDashboard',
        component: AgentDashboard,
        meta: { requiresAuth: true, roles: ['agent', 'admin', 'super_admin'] },
    },

    // 1-Tap Emergency Hub
    {
        path: '/emergency',
        alias: '/sos',
        name: 'EmergencyHub',
        component: EmergencyHub,
    },

    // Blood & Platelet SOS
    {
        path: '/blood-sos',
        alias: '/blood',
        name: 'BloodSos',
        component: BloodSos,
    },

    // Verified Ambulance & Live ICU Bed Radar
    {
        path: '/emergency-resources',
        alias: ['/ambulances', '/icu-beds'],
        name: 'EmergencyResources',
        component: EmergencyResources,
    },

    // Transparent Medical Cost Estimator
    {
        path: '/cost-estimator',
        alias: '/medical-costs',
        name: 'CostEstimator',
        component: CostEstimator,
    },

    // Digital Health Passport Emergency QR Public View
    {
        path: '/emergency-pass/:token',
        name: 'EmergencyCardView',
        component: EmergencyCardView,
        props: true,
    },

    // Catch-All
    {
        path: '/:pathMatch(.*)*',
        redirect: '/',
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0, behavior: 'smooth' };
        }
    },
});

// Navigation Guard: Protect private health and administration portals
router.beforeEach((to, from, next) => {
    // 1. Explicitly allow public doctor routes (never require login)
    if (
        to.path === '/doctors' ||
        to.path.startsWith('/doctors?') ||
        to.path.startsWith('/doctors/') ||
        to.name === 'Doctors' ||
        to.name === 'DoctorDetail' ||
        to.name === 'DoctorPortfolio'
    ) {
        return next();
    }

    // 2. Determine if the target route requires authentication
    const isProtectedPortal = 
        to.matched.some(record => record.meta && record.meta.requiresAuth) ||
        to.path === '/doctor' || 
        to.path.startsWith('/doctor/dashboard') ||
        ['/patient', '/admin', '/staff', '/agent'].some(prefix => to.path.startsWith(prefix));

    if (isProtectedPortal) {
        const token = localStorage.getItem('saas_token') || localStorage.getItem('admin_token');
        if (!token) {
            return next({ path: '/login', query: { redirect: to.fullPath } });
        }

        const rawUser = localStorage.getItem('saas_user');
        if (rawUser) {
            try {
                const user = JSON.parse(rawUser);
                const role = user.role;

                if (to.path.startsWith('/admin') && !['admin', 'super_admin'].includes(role)) {
                    return next('/');
                }
                if ((to.path === '/doctor' || to.path.startsWith('/doctor/dashboard')) && !['doctor', 'staff', 'admin', 'super_admin'].includes(role)) {
                    return next('/');
                }
                if (to.path.startsWith('/staff') && !['staff', 'doctor', 'admin', 'super_admin'].includes(role)) {
                    return next('/');
                }
                if (to.path.startsWith('/agent') && !['agent', 'admin', 'super_admin'].includes(role)) {
                    return next('/');
                }
            } catch (e) {
                localStorage.removeItem('saas_user');
                localStorage.removeItem('saas_token');
                return next('/login');
            }
        }
    }

    next();
});

export default router;
