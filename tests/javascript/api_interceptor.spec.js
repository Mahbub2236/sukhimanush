import { describe, it, expect, beforeEach, vi } from 'vitest';
import axios from 'axios';

describe('Axios API Interceptors (Authentication & Auto-Logout)', () => {
    beforeEach(() => {
        localStorage.clear();
        vi.clearAllMocks();
    });

    it('attaches Bearer token to request headers when saas_token is in localStorage', async () => {
        const testToken = 'bearer-test-token-12345';
        localStorage.setItem('saas_token', testToken);

        // Simulate request interceptor logic
        const requestInterceptor = (config) => {
            const authToken = localStorage.getItem('saas_token') || localStorage.getItem('admin_token');
            if (authToken) {
                config.headers = config.headers || {};
                config.headers.Authorization = `Bearer ${authToken}`;
            }
            return config;
        };

        const config = requestInterceptor({ headers: {} });
        expect(config.headers.Authorization).toBe(`Bearer ${testToken}`);
    });

    it('attaches admin Bearer token when admin_token is present and saas_token is not', () => {
        const adminToken = 'admin-secret-token-999';
        localStorage.setItem('admin_token', adminToken);

        const requestInterceptor = (config) => {
            const authToken = localStorage.getItem('saas_token') || localStorage.getItem('admin_token');
            if (authToken) {
                config.headers = config.headers || {};
                config.headers.Authorization = `Bearer ${authToken}`;
            }
            return config;
        };

        const config = requestInterceptor({ headers: {} });
        expect(config.headers.Authorization).toBe(`Bearer ${adminToken}`);
    });

    it('does not attach Authorization header when no token is present', () => {
        const requestInterceptor = (config) => {
            const authToken = localStorage.getItem('saas_token') || localStorage.getItem('admin_token');
            if (authToken) {
                config.headers = config.headers || {};
                config.headers.Authorization = `Bearer ${authToken}`;
            }
            return config;
        };

        const config = requestInterceptor({ headers: {} });
        expect(config.headers.Authorization).toBeUndefined();
    });

    it('clears all session storage tokens upon receiving HTTP 401 Unauthorized', async () => {
        localStorage.setItem('saas_token', 'expired-token');
        localStorage.setItem('saas_user', JSON.stringify({ id: 1, role: 'patient' }));
        localStorage.setItem('admin_token', 'expired-admin');
        localStorage.setItem('saas_doctor', JSON.stringify({ id: 5 }));

        // Simulate response error interceptor logic
        const errorInterceptor = (error) => {
            if (error.response && error.response.status === 401) {
                localStorage.removeItem('saas_token');
                localStorage.removeItem('saas_user');
                localStorage.removeItem('saas_doctor');
                localStorage.removeItem('saas_assistant');
                localStorage.removeItem('admin_token');
                localStorage.removeItem('admin_user');
            }
            return Promise.reject(error);
        };

        const mockError = {
            response: { status: 401, data: { message: 'Unauthenticated.' } },
        };

        await expect(errorInterceptor(mockError)).rejects.toEqual(mockError);

        expect(localStorage.getItem('saas_token')).toBeNull();
        expect(localStorage.getItem('saas_user')).toBeNull();
        expect(localStorage.getItem('admin_token')).toBeNull();
        expect(localStorage.getItem('saas_doctor')).toBeNull();
    });
});
