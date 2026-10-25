import { describe, it, expect, beforeEach, vi } from 'vitest';
import router from '@/router';

describe('Vue Router Navigation Guards', () => {
    beforeEach(() => {
        localStorage.clear();
        vi.clearAllMocks();
    });

    it('allows public routes without authentication', async () => {
        const publicPaths = ['/', '/about', '/doctors', '/doctors/dr-test'];

        for (const path of publicPaths) {
            await router.push(path);
            expect(router.currentRoute.value.path).toBe(path);
        }
    });

    it('redirects unauthenticated user trying to access protected portal to login', async () => {
        await router.push('/patient/dashboard');
        expect(router.currentRoute.value.path).toBe('/login');
        expect(router.currentRoute.value.query.redirect).toBe('/patient/dashboard');
    });

    it('allows patient with valid token to access patient dashboard', async () => {
        localStorage.setItem('saas_token', 'sample-valid-patient-token');
        localStorage.setItem('saas_user', JSON.stringify({ id: 1, role: 'patient', name: 'John Doe' }));

        await router.push('/patient/dashboard');
        expect(router.currentRoute.value.path).toBe('/patient/dashboard');
    });

    it('blocks patient from accessing admin dashboard and redirects to home', async () => {
        localStorage.setItem('saas_token', 'sample-valid-patient-token');
        localStorage.setItem('saas_user', JSON.stringify({ id: 1, role: 'patient', name: 'John Doe' }));

        await router.push('/admin/dashboard');
        expect(router.currentRoute.value.path).toBe('/');
    });

    it('blocks patient from accessing doctor dashboard and redirects to home', async () => {
        localStorage.setItem('saas_token', 'sample-valid-patient-token');
        localStorage.setItem('saas_user', JSON.stringify({ id: 1, role: 'patient', name: 'John Doe' }));

        await router.push('/doctor/dashboard');
        expect(router.currentRoute.value.path).toBe('/');
    });

    it('allows admin to access admin portal', async () => {
        localStorage.setItem('admin_token', 'sample-admin-token');
        localStorage.setItem('saas_user', JSON.stringify({ id: 99, role: 'admin', name: 'Super Admin' }));

        await router.push('/admin/dashboard');
        expect(router.currentRoute.value.path).toBe('/admin/dashboard');
    });

    it('clears corrupted localStorage JSON and redirects to login', async () => {
        localStorage.setItem('saas_token', 'some-token');
        localStorage.setItem('saas_user', '{corrupted_json:invalid');

        await router.push('/patient/dashboard');
        expect(localStorage.getItem('saas_token')).toBeNull();
        expect(localStorage.getItem('saas_user')).toBeNull();
        expect(router.currentRoute.value.path).toBe('/login');
    });
});
