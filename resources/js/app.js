import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Configure Axios defaults
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Automatically attach Sanctum Bearer Token to all Axios API requests
axios.interceptors.request.use((config) => {
    const authToken = localStorage.getItem('saas_token') || localStorage.getItem('admin_token');
    if (authToken) {
        config.headers.Authorization = `Bearer ${authToken}`;
    }
    return config;
}, (error) => Promise.reject(error));

// Global Response Interceptor: Auto-logout on 401 (token expiration or invalidation)
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('saas_token');
            localStorage.removeItem('saas_user');
            localStorage.removeItem('saas_doctor');
            localStorage.removeItem('saas_assistant');
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');

            const path = window.location.pathname;
            if (path !== '/login' && path !== '/admin/login' && path !== '/register') {
                window.location.href = '/login?expired=1';
            }
        }
        return Promise.reject(error);
    }
);

// Verify server-side role and session authenticity on boot
const bootToken = localStorage.getItem('saas_token');
if (bootToken) {
    axios.get('/auth/me')
        .then((res) => {
            if (res.data && res.data.user) {
                localStorage.setItem('saas_user', JSON.stringify(res.data.user));
                if (res.data.doctor) {
                    localStorage.setItem('saas_doctor', JSON.stringify(res.data.doctor));
                }
            }
        })
        .catch(() => {
            localStorage.removeItem('saas_token');
            localStorage.removeItem('saas_user');
            localStorage.removeItem('saas_doctor');
        });
}

import speechService from './utils/voiceInput';
import VoiceInputBtn from './components/VoiceInputBtn.vue';

const app = createApp(App);
app.config.globalProperties.$axios = axios;
app.config.globalProperties.$speech = speechService;
app.component('VoiceInputBtn', VoiceInputBtn);
app.use(router);
app.mount('#app');
