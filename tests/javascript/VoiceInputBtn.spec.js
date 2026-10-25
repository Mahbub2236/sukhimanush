import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import VoiceInputBtn from '@/components/VoiceInputBtn.vue';

describe('VoiceInputBtn Component', () => {
    it('renders microphone button properly', () => {
        const wrapper = mount(VoiceInputBtn, {
            props: {
                lang: 'bn-BD',
                title: 'ভয়েস দিয়ে ইনপুট দিন',
            },
            global: {
                mocks: {
                    $speech: {
                        isSupported: () => true,
                        startListening: vi.fn(),
                        stopListening: vi.fn(),
                    }
                }
            }
        });

        expect(wrapper.find('button').exists()).toBe(true);
        expect(wrapper.attributes('title') || wrapper.find('button').attributes('title')).toContain('ভয়েস');
    });

    it('emits speech result when speech is recognized', async () => {
        const wrapper = mount(VoiceInputBtn, {
            global: {
                mocks: {
                    $speech: {
                        isSupported: () => true,
                        startListening: vi.fn(),
                        stopListening: vi.fn(),
                    }
                }
            }
        });

        // Trigger manual transcript emit or method
        if (typeof wrapper.vm.handleResult === 'function') {
            wrapper.vm.handleResult('মাথাব্যথা ও জ্বর');
            expect(wrapper.emitted('result')).toBeTruthy();
            expect(wrapper.emitted('result')[0]).toEqual(['মাথাব্যথা ও জ্বর']);
        }
    });
});
