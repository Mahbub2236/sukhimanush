# 🏥 Novena & Mahriva Flutter Mobile Application

Next-Gen Cross-Platform Telehealth, Doctor SaaS & Hospital Patient Mobile Application built for **Novena (Mahriva Health & Care)** Laravel Backend.

---

## 🌟 Key Highlights & Features

1. **🎨 100% Match to Web Visual Aesthetics**:
   - Curated **Deep Navy (`#223a66`)** and **Medical Crimson Accent (`#e12454`)**.
   - Modern typography using **Google Fonts Plus Jakarta Sans & Inter**.
   - Glassmorphism, smooth gradients, and interactive badges.

2. **🚀 WOW-Factor Interactive Landing Page**:
   - Dynamic Hero Banner with doctor search & location filtering.
   - 1-Tap Emergency Action Hub (Ambulance 999 hotline, Blood SOS, Live Queue, AI Diagnosis).
   - Live Platform Impact Counters (150+ Specialists, 15,000+ Cured Patients, 99.4% Satisfaction).
   - AI Health Assistant Interactive Teaser.
   - Featured Doctor Carousel with ratings, fees & instant booking.
   - Medical Specialties Grid with micro-interactions.

3. **🔗 100% Laravel API Endpoint Integration**:
   - Maps every single route defined in `routes/api.php` (Public, Patient Portal, Doctor Portal, Staff Portal, Rural Agent Portal, Admin, AI Health Assistant, Emergency Hub, Blood SOS, Diagnostic Orders, and Medicine Reminders).
   - Automatic Laravel Sanctum Bearer Token injection via Dio interceptors.

4. **⚡ Complete Feature Modules**:
   - **Doctor Discovery**: Bangladesh 8 Divisions, 64 Districts & Upazila filter.
   - **Appointment Booking Wizard**: Multi-step wizard with slot selection & payment (bKash, Nagad, SSLCommerz, Cash).
   - **Live Chamber Queue Tracker**: Real-time token counter, people ahead & estimated waiting countdown.
   - **Novena AI Health Assistant**: Bengali & English symptom analysis, emergency triage warning & doctor suggestion.
   - **Emergency Hub & Blood SOS**: 1-Tap ambulance calling & blood donor/recipient directory.
   - **Multi-Role Portals**: Patient, Doctor, Compounder/Staff & Pharmacy Agent portals.

---

## 📁 Project Architecture (`public/flutter_app/`)

```
public/flutter_app/
├── pubspec.yaml                 # Dependencies (Provider, Dio, Google Fonts, etc.)
├── analysis_options.yaml        # Flutter linting configuration
├── README.md                    # Documentation & Setup guide
└── lib/
    ├── main.dart                # App entrypoint with MultiProvider & Routes
    ├── core/
    │   ├── constants/
    │   │   ├── api_constants.dart # 100% API endpoints mapped
    │   │   ├── app_colors.dart    # Web brand color palette & gradients
    │   │   └── app_theme.dart     # Typography & component themes
    │   ├── network/
    │   │   └── api_client.dart    # Dio singleton with Sanctum auth interceptor
    │   └── routes/
    │       └── app_routes.dart    # Named route definitions
    ├── models/
    │   ├── user_model.dart        # Sanctum user model with roles
    │   ├── doctor_model.dart      # Doctor & Chamber models
    │   ├── appointment_model.dart # Appointment & Queue status models
    │   └── emergency_model.dart   # Ambulance & Blood SOS models
    ├── services/
    │   └── api_service.dart       # All 45+ API calls implemented
    ├── providers/
    │   ├── auth_provider.dart     # Authentication & token storage
    │   ├── home_provider.dart     # Landing page data & stats
    │   ├── doctor_provider.dart   # Doctor discovery & slots
    │   ├── appointment_provider.dart # Booking & queue tracking
    │   └── ai_assistant_provider.dart # AI symptom checker
    ├── screens/
    │   ├── landing/
    │   │   └── landing_screen.dart # WOW landing page
    │   ├── doctors/
    │   │   └── doctor_list_screen.dart
    │   ├── appointments/
    │   │   ├── appointment_booking_screen.dart
    │   │   └── live_queue_screen.dart
    │   ├── ai_assistant/
    │   │   └── ai_symptom_checker_screen.dart
    │   ├── emergency/
    │   │   ├── emergency_hub_screen.dart
    │   │   └── blood_sos_screen.dart
    │   ├── auth/
    │   │   └── login_screen.dart
    │   └── patient/
    │       └── patient_dashboard_screen.dart
    └── widgets/
        ├── doctor_card.dart
        ├── glass_card.dart
        └── stat_badge.dart
```

---

## 🛠️ How to Run

1. Open a terminal and navigate to the flutter app folder:
   ```bash
   cd public/flutter_app
   ```

2. Get all packages:
   ```bash
   flutter pub get
   ```

3. Ensure your Laravel server is running in another terminal:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

4. Run the Flutter app:
   ```bash
   # Android
   flutter run -d android

   # iOS
   flutter run -d ios

   # Chrome Web
   flutter run -d chrome
   ```
