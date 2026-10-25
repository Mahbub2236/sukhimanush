import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Login from '@/views/auth/Login.vue';

// Mock router and axios
const mockRouter = {
    push: vi.fn(),
    currentRoute: { value: { query: {} } }
};

describe('Auth Login Component', () => {
    it('renders login form properly with role switcher', () => {
        const wrapper = mount(Login, {
            global: {
                mocks: {
                    $router: mockRouter,
                },
                stubs: ['router-link']
            }
        });

        expect(wrapper.text()).toContain('Sign In to Portal');
        expect(wrapper.find('input[type="email"]').exists()).toBe(true);
        expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    });

    it('sets demo credentials accurately when clicking demo buttons', async () => {
        const wrapper = mount(Login, {
            global: {
                mocks: {
                    $router: mockRouter,
                },
                stubs: ['router-link']
            }
        });

        // Click Patient demo
        wrapper.vm.setDemo('patient');
        expect(wrapper.vm.form.email).toBe('patient@sukhimanush.com');
        expect(wrapper.vm.form.password).toBe('password');

        // Click Doctor demo
        wrapper.vm.setDemo('doctor');
        expect(wrapper.vm.form.email).toBe('dr.thomas@sukhimanush.com');
        expect(wrapper.vm.form.password).toBe('password');

        // Click Agent demo
        wrapper.vm.setDemo('agent');
        expect(wrapper.vm.form.email).toBe('agent@sukhimanush.local');
        expect(wrapper.vm.form.password).toBe('password');
    });

    it('switches to staff PIN login mode correctly', async () => {
        const wrapper = mount(Login, {
            global: {
                mocks: {
                    $router: mockRouter,
                },
                stubs: ['router-link']
            }
        });

        wrapper.vm.setDemo('staff');
        expect(wrapper.vm.activeMode).toBe('staff');
        expect(wrapper.vm.staffForm.phone).toBe('01838046508');
        expect(wrapper.vm.staffForm.access_pin).toBe('123456');
    });
});
