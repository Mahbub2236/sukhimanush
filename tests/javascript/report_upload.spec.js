import { describe, it, expect } from 'vitest';

describe('Patient Medical Report Upload Helper Logic', () => {
    const formatFileSize = (bytes) => {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    const isAllowedReportFile = (file) => {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        const ext = file.name.split('.').pop()?.toLowerCase();
        const maxSize = 20 * 1024 * 1024; // 20MB

        if (!allowedTypes.includes(file.type)) return { valid: false, error: 'Invalid file format.' };
        if (!ext || !allowedExtensions.includes(ext)) return { valid: false, error: 'Disallowed file extension.' };
        if (file.size > maxSize) return { valid: false, error: 'File exceeds 20MB limit.' };

        return { valid: true, isPdf: file.type === 'application/pdf' || ext === 'pdf' };
    };

    it('formats file sizes accurately for display', () => {
        expect(formatFileSize(500)).toBe('500 B');
        expect(formatFileSize(1024)).toBe('1 KB');
        expect(formatFileSize(2.5 * 1024 * 1024)).toBe('2.5 MB');
    });

    it('accepts valid JPEG, PNG, WEBP and PDF files within 20MB', () => {
        const validJpg = { name: 'blood_report.jpg', type: 'image/jpeg', size: 1024 * 500 };
        const validPdf = { name: 'prescription_scan.pdf', type: 'application/pdf', size: 1024 * 1024 * 3 };

        expect(isAllowedReportFile(validJpg)).toEqual({ valid: true, isPdf: false });
        expect(isAllowedReportFile(validPdf)).toEqual({ valid: true, isPdf: true });
    });

    it('rejects disallowed file formats like executables, PHP scripts, and HTML', () => {
        const badPhp = { name: 'malicious.php', type: 'application/x-php', size: 500 };
        const badExe = { name: 'virus.exe', type: 'application/x-msdownload', size: 1000 };

        expect(isAllowedReportFile(badPhp).valid).toBe(false);
        expect(isAllowedReportFile(badExe).valid).toBe(false);
    });

    it('rejects files larger than 20MB', () => {
        const oversizedPdf = { name: 'huge_mri_scan.pdf', type: 'application/pdf', size: 25 * 1024 * 1024 };
        const result = isAllowedReportFile(oversizedPdf);

        expect(result.valid).toBe(false);
        expect(result.error).toContain('20MB');
    });

    it('assembles complete upload payload with required medical metadata', () => {
        const formData = new FormData();
        const mockFile = new File(['dummy report data'], 'test_cbc.pdf', { type: 'application/pdf' });

        formData.append('user_id', '42');
        formData.append('report_file', mockFile);
        formData.append('report_title', 'Complete Blood Count (CBC)');
        formData.append('test_type', 'blood_test');
        formData.append('diagnostic_center', 'Popular Diagnostic Center');
        formData.append('report_date', '2026-10-23');

        expect(formData.get('user_id')).toBe('42');
        expect(formData.get('report_title')).toBe('Complete Blood Count (CBC)');
        expect(formData.get('test_type')).toBe('blood_test');
        expect(formData.get('diagnostic_center')).toBe('Popular Diagnostic Center');
        expect(formData.get('report_file')).toBeDefined();
    });
});
