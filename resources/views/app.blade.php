<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="সুখী মানুষ (Sukhi Manush) - ডিজিটাল স্বাস্থ্যসেবা, হাসপাতাল ও বিশেষজ্ঞ ডাক্তার চেম্বার অ্যাপয়েন্টমেন্ট প্ল্যাটফর্ম">
    <meta name="author" content="সুখী মানুষ (Sukhi Manush) Medical Team">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>সুখী মানুষ (Sukhi Manush) - ডিজিটাল স্বাস্থ্যসেবা ও ডাক্তার সিরিয়াল</title>

    <!-- PWA Web App Manifest & Mobile App Capabilities -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0284C7">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="সুখী মানুষ">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico" />

    <!-- Google Web Fonts (Plus Jakarta Sans & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS (Ensures all grid layouts, cards, and forms render perfectly across all pages) -->
    <link rel="stylesheet" href="/plugins/bootstrap/css/bootstrap.min.css">

    <!-- FontAwesome 6 Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Icon Font CSS -->
    <link rel="stylesheet" href="/plugins/icofont/icofont.min.css">
    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="/plugins/slick-carousel/slick/slick.css">
    <link rel="stylesheet" href="/plugins/slick-carousel/slick/slick-theme.css">

    <!-- Custom Booking & SPA Transitions -->
    <style>
        .page-fade-enter-active,
        .page-fade-leave-active {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .page-fade-enter-from {
            opacity: 0;
            transform: translateY(6px);
        }
        .page-fade-leave-to {
            opacity: 0;
            transform: translateY(-6px);
        }

        /* Booking Wizard Custom Helpers */
        .booking-step-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .booking-step-badge.active {
            background-color: #e12454;
            color: #fff;
            box-shadow: 0 4px 14px rgba(225, 36, 84, 0.35);
        }
        .booking-step-badge.completed {
            background-color: #223a66;
            color: #fff;
        }
        .booking-step-badge.pending {
            background-color: #eef2f6;
            color: #8892b0;
        }

        .time-slot-btn {
            border: 2px solid #eef2f6;
            background: #fff;
            padding: 10px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            color: #223a66;
            transition: all 0.2s ease;
            text-align: center;
        }
        .time-slot-btn:hover:not(:disabled) {
            border-color: #e12454;
            color: #e12454;
        }
        .time-slot-btn.selected {
            background: #e12454 !important;
            border-color: #e12454 !important;
            color: #fff !important;
        }
        .time-slot-btn:disabled {
            background: #f8f9fa;
            border-color: #e9ecef;
            color: #adb5bd;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .doctor-card-select {
            border: 2px solid #edf2f7;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.25s ease;
            cursor: pointer;
            background: #fff;
        }
        .doctor-card-select:hover {
            border-color: #223a66;
            box-shadow: 0 6px 18px rgba(34, 58, 102, 0.08);
        }
        .doctor-card-select.active {
            border-color: #e12454;
            background: #fffbfa;
            box-shadow: 0 6px 20px rgba(225, 36, 84, 0.12);
        }

        .appointment-pass {
            background: #fff;
            border: 2px dashed #223a66;
            border-radius: 12px;
            padding: 25px;
            position: relative;
            box-shadow: 0 8px 24px rgba(34, 58, 102, 0.06);
        }

        .status-pill {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pill.confirmed { background-color: #d1e7dd; color: #0f5132; }
        .status-pill.pending { background-color: #fff3cd; color: #664d03; }
        .status-pill.completed { background-color: #cfe2ff; color: #084298; }
        .status-pill.cancelled { background-color: #f8d7da; color: #842029; }

        /* Page Header Banner & Dark Overlay Fix */
        .page-title {
            position: relative;
            background-color: #1a2e51;
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }
        .page-title.bg-1,
        .bg-1 {
            background-color: #1a2e51;
            background-image: linear-gradient(135deg, rgba(21, 36, 64, 0.88) 0%, rgba(34, 58, 102, 0.82) 100%), url("/images/bg/22.jpg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            position: relative;
        }
        .page-title.bg-2,
        .bg-2 {
            background-color: #1a2e51;
            background-image: linear-gradient(135deg, rgba(21, 36, 64, 0.88) 0%, rgba(34, 58, 102, 0.82) 100%), url("/images/bg/bg-2.jpg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            position: relative;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(34, 58, 102, 0.85) 100%);
            z-index: 1;
            pointer-events: none;
        }
        .page-title .container,
        .page-title .block,
        .page-title .row {
            position: relative;
            z-index: 2;
        }
        .page-title h1,
        .page-title .block h1 {
            color: #ffffff !important;
        }
        .page-title p,
        .page-title .block p {
            color: #f1f5f9 !important;
        }
        .gray-bg {
            background-color: #f8fafc;
        }

        @media print {
            header, footer, .page-title, .features, .cta-section, .btn, .booking-step-badge, nav {
                display: none !important;
            }
            #appointment-ticket {
                display: block !important;
                border: 2px solid #222 !important;
                box-shadow: none !important;
                margin: 20px auto !important;
                width: 100% !important;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="top">
    <div id="app"></div>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then((reg) => {
                        console.log('✅ সুখী মানুষ (Sukhi Manush) PWA Service Worker registered:', reg.scope);
                    })
                    .catch((err) => {
                        console.warn('⚠️ PWA Service Worker registration failed:', err);
                    });
            });
        }
    </script>
</body>
</html>
