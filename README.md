# Mahriva - Smart Hospital, Doctor Appointment & Telemedicine SaaS Platform

[![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5%2B-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0%2B-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-Commercial-blue?style=for-the-badge)](license)

**Mahriva** is an enterprise-grade digital healthcare, clinic management, and telemedicine Single Page Application (SPA). Designed for hospitals, private clinics, diagnostic centers, and individual doctors to manage patient appointments, digital prescriptions, real-time chamber queues, and audio-assisted diagnosis.

---

## 🌟 Key Features

### 1. 🏥 Patient & Public Experience

- **Interactive Doctor Directory**: Search by specialization, district, chamber address, or consultation fee.
- **Bangla Voice Search**: Search doctors and clinics simply by speaking in Bangla.
- **3-Step Appointment Booking**: Pick available doctor time slot, fill patient details, pay online or on-spot.
- **Live Chamber Queue Tracker**: Real-time counter showing current serial number being served in chamber.
- **AI Symptom Analyzer**: 24/7 smart assistant providing guidance based on symptoms and recommending relevant specialists.
- **Emergency SOS & Blood Donors**: Quick emergency hotline and blood/platelet donor locator.
- **100% Mobile Responsive**: App-like mobile experience with sticky bottom navigation bar and slide-over drawer.

### 2. 👨‍⚕️ Doctor & Chamber Portal

- **Chamber Management**: Define multiple chamber locations, schedule timings, and slot limits.
- **Digital Rx & Prescription Generation**: Prescribe medicines with dosage guidelines and diagnostic tests.
- **Bangla Audio Prescription**: Record doctor advice audio directly for rural/elderly patients.
- **Assistant/Compounder PIN Login**: Staff can manage queue, call serial numbers, and check in patients without doctor's main password.
- **Earnings & Payouts**: Real-time wallet tracking and withdrawal requests.

### 3. 🛡️ Super Admin & Staff Portal

- Comprehensive overview of all doctors, hospitals, appointments, and diagnostic orders.
- Role-based permissions (Super Admin, Doctor, Staff, Agent, Patient).
- Integrated Payment Gateways (bKash, Nagad, SSLCommerz, Stripe).

---

## 🛠️ Technology Stack

- **Backend**: Laravel 12 / PHP 8.2+
- **Frontend**: Vue.js 3 (Composition & Options API) + Vue Router SPA
- **Styling**: Tailwind CSS v4 (Modern Design Tokens, Mobile First)
- **Database**: MySQL 8.0+
- **Icons**: FontAwesome 6 & Lucide
- **Realtime / Audio**: Web Speech API, Native MediaRecorder, Webhooks

---

## 🚀 Quick Installation Guide

### Prerequisites

- PHP >= 8.2 with PDO, cURL, OpenSSL, Mbstring, Tokenizer, XML extensions
- Composer >= 2.x
- Node.js >= 18.x and NPM
- MySQL >= 8.0 or MariaDB >= 10.4

### Setup Steps

1. **Clone or Extract the Source Code**:

    ```bash
    cd /path/to/sukhimanush
    ```

2. **Install PHP & Node Dependencies**:

    ```bash
    composer install
    npm install
    ```

3. **Configure Environment**:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    Open `.env` and set your database credentials:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=mahriva_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4. **Run Database Migrations & Seeds**:

    ```bash
    php artisan migrate --seed
    ```

5. **Compile Frontend Assets**:

    ```bash
    npm run build
    # Or for local development:
    npm run dev
    ```

6. **Start Application Server**:
    ```bash
    php artisan serve
    ```
    Visit `http://127.0.0.1:8000` in your web browser.

---

## 📱 Mobile Responsiveness & Device Support

Mahriva has been crafted with a **Mobile-First** architecture:

- Tested on iPhone SE (320px), iPhone 14/15 Pro Max (430px), Android devices, iPad/Tablets, and 4K Desktop screens.
- Zero horizontal overflow.
- Native mobile bottom navigation for one-thumb navigation.
- Touch-friendly tap areas (minimum 44x44px target sizes).

---

## 📄 License & Support

Distributed under commercial terms on Envato Market (CodeCanyon).
For documentation, customizations, or support, contact: `support@mahriva.com`.
