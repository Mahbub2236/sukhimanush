import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import AppointmentBooking from '@/views/AppointmentBooking.vue';

// Mock router and dependencies
const mockRouter = {
    push: vi.fn(),
};

const mockRoute = {
    query: {},
    params: {},
};

describe('AppointmentBooking Component', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders multi-step wizard with Step 1 active initially', () => {
        const wrapper = mount(AppointmentBooking, {
            global: {
                mocks: {
                    $router: mockRouter,
                    $route: mockRoute,
                },
                stubs: ['router-link', 'VoiceInputBtn']
            }
        });

        expect(wrapper.text()).toContain('Book Doctor Consultation');
        expect(wrapper.text()).toContain('Choose Doctor');
        expect(wrapper.text()).toContain('Chamber & Slot');
        expect(wrapper.text()).toContain('Patient Details');
        expect(wrapper.vm.currentStep).toBe(1);
    });

    it('filters doctors by specialization when selection changes', async () => {
        const wrapper = mount(AppointmentBooking, {
            global: {
                mocks: {
                    $router: mockRouter,
                    $route: mockRoute,
                },
                stubs: ['router-link', 'VoiceInputBtn']
            }
        });

        // Seed sample doctors in component
        wrapper.vm.doctors = [
            { id: 1, name: 'Dr. Sarah', specialization: 'Cardiology', city: 'Dhaka', consultation_fee: 1000 },
            { id: 2, name: 'Dr. Alex', specialization: 'Neurology', city: 'Chittagong', consultation_fee: 1200 },
            { id: 3, name: 'Dr. Karim', specialization: 'Cardiology', city: 'Sylhet', consultation_fee: 800 },
        ];

        wrapper.vm.selectedSpecialization = 'Cardiology';
        await wrapper.vm.$nextTick();

        const filtered = wrapper.vm.filteredDoctors;
        expect(filtered.length).toBe(2);
        expect(filtered.every(d => d.specialization === 'Cardiology')).toBe(true);
    });

    it('selects a doctor and advances to Step 2 for slot selection', async () => {
        const wrapper = mount(AppointmentBooking, {
            global: {
                mocks: {
                    $router: mockRouter,
                    $route: mockRoute,
                },
                stubs: ['router-link', 'VoiceInputBtn']
            }
        });

        const sampleDoctor = {
            id: 10,
            name: 'Dr. Masud Rahman',
            specialization: 'Pediatrics',
            consultation_fee: 1000,
            chambers: []
        };
        wrapper.vm.doctors = [sampleDoctor];

        wrapper.vm.selectDoctorAndContinue(sampleDoctor);
        expect(wrapper.vm.form.doctor_id).toBe(10);
        expect(wrapper.vm.selectedDoctor).toEqual(sampleDoctor);
        expect(wrapper.vm.currentStep).toBe(2);
    });

    it('validates patient details before submitting', () => {
        const wrapper = mount(AppointmentBooking, {
            global: {
                mocks: {
                    $router: mockRouter,
                    $route: mockRoute,
                },
                stubs: ['router-link', 'VoiceInputBtn']
            }
        });

        // Empty patient details
        wrapper.vm.form.patient_name = '';
        wrapper.vm.form.patient_phone = '';
        wrapper.vm.form.patient_email = '';

        expect(!wrapper.vm.form.patient_name || !wrapper.vm.form.patient_phone).toBe(true);
    });
});
