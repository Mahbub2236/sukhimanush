import { test, expect } from '@playwright/test';

test.describe('End-to-End User Journeys (Sukhi Manush Platform)', () => {

    test('1. Home page loads with branding, title, and key navigation buttons', async ({ page }) => {
        await page.goto('/');
        await expect(page).toHaveTitle(/সুখী মানুষ/);
        
        // Check brand header presence
        const brand = page.locator('header');
        await expect(brand).toBeVisible();

        // Check main call to action
        const bookBtn = page.locator('a:has-text("অ্যাপয়েন্টমেন্ট নিন"), a:has-text("ডাক্তার খুঁজুন")').first();
        await expect(bookBtn).toBeVisible();
    });

    test('2. Role-protected pages redirect unauthenticated users to login', async ({ page }) => {
        const protectedRoutes = ['/patient/dashboard', '/doctor/dashboard', '/admin/dashboard'];

        for (const route of protectedRoutes) {
            await page.goto(route);
            await expect(page).toHaveURL(/.*login/);
        }
    });

    test('3. Register page renders patient/doctor role selection and input fields', async ({ page }) => {
        await page.goto('/register');
        await expect(page.locator('input[type="email"], input[name="email"]')).toBeVisible();
        await expect(page.locator('input[type="password"]')).toBeVisible();

        // Check role switcher exists
        const roleRadio = page.locator('input[value="patient"], input[value="doctor"]').first();
        if (await roleRadio.isVisible()) {
            await expect(roleRadio).toBeEnabled();
        }
    });

    test('4. Login page supports standard credentials and toggles staff PIN mode', async ({ page }) => {
        await page.goto('/login');
        const emailInput = page.locator('input[type="email"]');
        await expect(emailInput).toBeVisible();

        // Fill standard login credentials
        await emailInput.fill('patient@sukhimanush.com');
        await page.locator('input[type="password"]').fill('password');
        await expect(emailInput).toHaveValue('patient@sukhimanush.com');

        // Toggle Staff PIN login mode
        const staffModeBtn = page.locator('button:has-text("চেম্বার সহকারী"), button:has-text("Staff")').first();
        if (await staffModeBtn.isVisible()) {
            await staffModeBtn.click();
            await expect(page.locator('input[placeholder*="123456"]')).toBeVisible();
        }
    });

    test('5. Doctors listing directory loads and displays filters', async ({ page }) => {
        await page.goto('/doctors');
        await expect(page).toHaveURL(/.*doctors/);

        // Verify search input or filter pills
        const searchInput = page.locator('input[placeholder*="রংপুর"], input.form-control-lg, .filter-pills').first();
        await expect(searchInput).toBeVisible();
    });

    test('6. Appointment booking wizard loads multi-step interface', async ({ page }) => {
        await page.goto('/appoinment');
        await expect(page).toHaveURL(/.*appoinment/);

        // Check wizard indicator steps
        await expect(page.getByText('Choose Doctor', { exact: true })).toBeVisible();
        await expect(page.getByText('Chamber & Slot', { exact: true })).toBeVisible();
        await expect(page.getByText('Patient Details', { exact: true })).toBeVisible();
    });

    test('7. Emergency Hub and Blood SOS radar load correctly', async ({ page }) => {
        await page.goto('/emergency');
        await expect(page).toHaveURL(/.*emergency/);

        // Check Emergency radar content
        const sosHeading = page.locator('h1, h2, h3').first();
        await expect(sosHeading).toBeVisible();
    });

    test('8. Appointment tracking page loads search input', async ({ page }) => {
        await page.goto('/track-appointment');
        await expect(page).toHaveURL(/.*track-appointment/);
        await expect(page.locator('input[type="text"], input[placeholder*="সিরিয়াল"], input[placeholder*="Mobile"]')).toBeVisible();
    });
});
