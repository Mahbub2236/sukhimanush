import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../core/routes/app_routes.dart';
import '../../providers/home_provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/voice_text_field.dart';

class LandingScreen extends StatefulWidget {
  const LandingScreen({super.key});

  @override
  State<LandingScreen> createState() => _LandingScreenState();
}

class _LandingScreenState extends State<LandingScreen> {
  final TextEditingController _searchCtrl = TextEditingController();
  final PageController _bannerController = PageController();
  int _currentBannerIndex = 0;
  Timer? _bannerTimer;

  final List<Map<String, dynamic>> _promoBanners = [
    {
      'title': 'সম্পূর্ণ ফুল বডি হেলথ চেকআপ প্যাকেজ',
      'subtitle': '৫৫টি প্রয়োজনীয় ল্যাব টেস্ট ও বিশেষজ্ঞ ডাক্তারের ফ্রি রিপোর্ট পর্যালোচনা',
      'badge': '৩৫% বিশেষ ছাড়',
      'badgeColor': const Color(0xFFE11D48),
      'image': 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?q=80&w=800&auto=format&fit=crop',
      'btnText': 'প্যাকেজ বুকিং',
      'route': AppRoutes.doctorList,
    },
    {
      'title': 'ঘরে বসেই ফ্রি রক্ত ও ল্যাব স্যাম্পল দিন',
      'subtitle': 'ভেরিফায়েড মেডিকেল টেকনিশিয়ান সরাসরি বাসায় গিয়ে স্যাম্পল সংগ্রহ করবেন',
      'badge': 'ফ্রি হোম কালেকশন',
      'badgeColor': const Color(0xFF059669),
      'image': 'https://images.unsplash.com/photo-1581595220892-b0739db3ba8c?q=80&w=800&auto=format&fit=crop',
      'btnText': 'হোম স্যাম্পল বুক করুন',
      'route': AppRoutes.emergencyHub,
    },
    {
      'title': 'জরুরি মুহূর্তে ভেরিফায়েড অ্যাম্বুলেন্স ও ব্লাড SOS',
      'subtitle': 'নিকটস্থ অক্সিজেন সিলিন্ডার, আইসিইউ অ্যাম্বুলেন্স ও রক্তদাতা পান ১ ক্লিকে',
      'badge': '২৪/৭ ইমার্জেন্সি লাইভ',
      'badgeColor': const Color(0xFFDC2626),
      'image': 'https://images.unsplash.com/photo-1587745416684-47953f16f02f?q=80&w=800&auto=format&fit=crop',
      'btnText': 'ইমার্জেন্সি হাব',
      'route': AppRoutes.emergencyHub,
    },
    {
      'title': 'মা ও শিশুর জন্য বিশেষায়িত স্বাস্থ্যসেবা',
      'subtitle': 'দেশসেরা অভিজ্ঞ গাইনী ও শিশু বিশেষজ্ঞদের নিয়মিত চেম্বার সিরিয়াল',
      'badge': 'শীর্ষ স্পেশালিস্ট কেয়ার',
      'badgeColor': const Color(0xFF7C3AED),
      'image': 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?q=80&w=800&auto=format&fit=crop',
      'btnText': 'ডাক্তার খুঁজুন',
      'route': AppRoutes.doctorList,
    },
  ];

  final List<Map<String, dynamic>> _healthPackages = [
    {
      'title': 'সুস্থ হার্ট স্ক্রিনিং প্যাকেজ',
      'tests': 'ECG + Lipid Profile + Troponin-I + Dr. Visit',
      'originalPrice': '৳৩,৮০০',
      'offerPrice': '৳২,৪৫০',
      'discount': '৩৫% ছাড়',
      'tag': 'কার্ডিয়াক কেয়ার',
      'tagColor': const Color(0xFFE11D48),
      'image': 'https://images.unsplash.com/photo-1628348068343-c6a848d2b6dd?q=80&w=600&auto=format&fit=crop',
    },
    {
      'title': 'কমপ্লিট ডায়াবেটিস মনিটর প্যাক',
      'tests': 'HbA1c + Fasting Sugar + Serum Creatinine + Urine R/E',
      'originalPrice': '৳২,২০০',
      'offerPrice': '৳১,৩৫০',
      'discount': '৪০% ছাড়',
      'tag': 'ডায়াবেটিস প্রটেক্ট',
      'tagColor': const Color(0xFF0284C7),
      'image': 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=600&auto=format&fit=crop',
    },
    {
      'title': 'নারী ও প্রসূতি স্বাস্থ্য স্ক্রিনিং',
      'tests': 'USG Abdomen + Thyroid TSH + CBC + Consultation',
      'originalPrice': '৳৪,৫০০',
      'offerPrice': '৳২,৯০০',
      'discount': '৩৫% ছাড়',
      'tag': 'উইমেন কেয়ার',
      'tagColor': const Color(0xFF9333EA),
      'image': 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?q=80&w=600&auto=format&fit=crop',
    },
    {
      'title': 'সিনিয়র সিটিজেন কমপ্লিট কেয়ার',
      'tests': 'Kidney & Liver Panel + Electrolytes + Bone Density',
      'originalPrice': '৳৪,৮০০',
      'offerPrice': '৳৩,১০০',
      'discount': '৩৬% ছাড়',
      'tag': 'সিনিয়র সিটিজেন',
      'tagColor': const Color(0xFF059669),
      'image': 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=600&auto=format&fit=crop',
    },
  ];

  final List<Map<String, dynamic>> _homeServices = [
    {
      'title': 'বাসায় নার্সিং ও স্যালাইন',
      'desc': 'প্রশিক্ষিত নার্স দ্বারা ইনজেকশন, স্যালাইন ও ক্ষত ড্রেসিং সেবা নিন।',
      'icon': Icons.medical_services_outlined,
      'color': const Color(0xFF0284C7),
      'image': 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?q=80&w=500&auto=format&fit=crop',
    },
    {
      'title': 'ফিজিওথেরাপি ও রিহ্যাব',
      'desc': 'কোমর, হাঁটু বা প্যারালাইসিস রোগীদের অভিজ্ঞ ফিজিওথেরাপিস্ট সেবা।',
      'icon': Icons.accessibility_new,
      'color': const Color(0xFF059669),
      'image': 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=500&auto=format&fit=crop',
    },
    {
      'title': 'বাসায় ডিজিটাল ইসিজি টেস্ট',
      'desc': 'পোর্টেবল ইসিজি মেশিন নিয়ে বিশেষজ্ঞ টিম আপনার বাসায় পৌঁছাবে।',
      'icon': Icons.monitor_heart_outlined,
      'color': const Color(0xFFE11D48),
      'image': 'https://images.unsplash.com/photo-1628348068343-c6a848d2b6dd?q=80&w=500&auto=format&fit=crop',
    },
    {
      'title': 'জরুরি অক্সিজেন সাপোর্ট',
      'desc': 'দ্রুততম সময়ে সিলিন্ডার ও কনসেনট্রেটর ডেলিভারি এবং সেটআপ।',
      'icon': Icons.air,
      'color': const Color(0xFF7C3AED),
      'image': 'https://images.unsplash.com/photo-1587745416684-47953f16f02f?q=80&w=500&auto=format&fit=crop',
    },
  ];

  final List<Map<String, dynamic>> _healthBlogs = [
    {
      'title': 'হৃদরোগের প্রাথমিক লক্ষণ ও তৎক্ষণাৎ জরুরি করণীয় কী?',
      'category': 'কার্ডিওলজি গাইড',
      'author': 'ডাঃ এম. এ. রহমান (হৃদরোগ বিশেষজ্ঞ)',
      'readTime': '৪ মিনিট পাঠ',
      'image': 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?q=80&w=600&auto=format&fit=crop',
    },
    {
      'title': 'উচ্চ রক্তচাপ নিয়ন্ত্রণে খাদ্যতালিকায় যেসব পরিবর্তন আনা জরুরি',
      'category': 'লাইফস্টাইল ও পুষ্টি',
      'author': 'ডাঃ ফারহানা ইসলাম (নিউট্রিশনিস্ট)',
      'readTime': '৩ মিনিট পাঠ',
      'image': 'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?q=80&w=600&auto=format&fit=crop',
    },
    {
      'title': 'শিশুদের সিজনাল ফ্লু ও ভাইরাল জ্বর হলে যেসব সতর্কতা প্রয়োজন',
      'category': 'শিশু স্বাস্থ্য',
      'author': 'ডাঃ তানভীর হাসান (শিশু বিশেষজ্ঞ)',
      'readTime': '৫ মিনিট পাঠ',
      'image': 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?q=80&w=600&auto=format&fit=crop',
    },
  ];

  final List<Map<String, dynamic>> _departments = [
    {
      'name': 'মেডিসিন',
      'subtitle': 'General Medicine',
      'icon': Icons.medical_services_outlined,
      'color': const Color(0xFF0284C7),
      'bg': const Color(0xFFF0F9FF),
    },
    {
      'name': 'কার্ডিওলজি',
      'subtitle': 'হৃদরোগ বিশেষজ্ঞ',
      'icon': Icons.favorite_outline,
      'color': const Color(0xFFE11D48),
      'bg': const Color(0xFFFFF1F2),
    },
    {
      'name': 'গাইনী ও প্রসূতি',
      'subtitle': 'Gynecology & Obs',
      'icon': Icons.pregnant_woman,
      'color': const Color(0xFF9333EA),
      'bg': const Color(0xFFFAF5FF),
    },
    {
      'name': 'শিশু বিশেষজ্ঞ',
      'subtitle': 'Pediatrics Care',
      'icon': Icons.child_care,
      'color': const Color(0xFFEA580C),
      'bg': const Color(0xFFFFF7ED),
    },
    {
      'name': 'অর্থোপেডিক্স',
      'subtitle': 'হাড় ও জোড়া রোগ',
      'icon': Icons.accessibility_new,
      'color': const Color(0xFF059669),
      'bg': const Color(0xFFECFDF5),
    },
    {
      'name': 'নিউরোলজি',
      'subtitle': 'মস্তিষ্ক ও স্নায়ু',
      'icon': Icons.psychology_outlined,
      'color': const Color(0xFF4F46E5),
      'bg': const Color(0xFFEEF2FF),
    },
    {
      'name': 'চর্ম ও যৌন',
      'subtitle': 'Dermatology',
      'icon': Icons.spa_outlined,
      'color': const Color(0xFFD97706),
      'bg': const Color(0xFFFEF3C7),
    },
    {
      'name': 'নাক কান গলা',
      'subtitle': 'ENT Specialist',
      'icon': Icons.hearing_outlined,
      'color': const Color(0xFF0D9488),
      'bg': const Color(0xFFCCFBF1),
    },
  ];

  final List<Map<String, String>> _hospitalPartners = [
    {'name': 'ইবনে সিনা ডায়াগনস্টিক', 'city': 'ধানমন্ডি, ঢাকা', 'tag': 'স্পেশালাইজড সেন্টার'},
    {'name': 'পপুলার ডায়াগনস্টিক', 'city': 'শান্তিনগর, ঢাকা', 'tag': 'সারাদেশে শাখা'},
    {'name': 'ল্যাবএইড স্পেশালাইজড', 'city': 'গুলশান, ঢাকা', 'tag': 'কার্ডিয়াক সেন্টার'},
    {'name': 'স্কয়ার হসপিটাল', 'city': 'পান্থপথ, ঢাকা', 'tag': 'মাল্টি-ডিসিপ্লিনারি'},
    {'name': 'এভারকেয়ার হাসপাতাল', 'city': 'বসুন্ধরা, ঢাকা', 'tag': 'JCI অ্যাক্রিডিটেড'},
    {'name': 'ইউনাইটেড হসপিটাল', 'city': 'গুলশান-২, ঢাকা', 'tag': 'টারশিয়ারি কেয়ার'},
  ];

  final List<Map<String, dynamic>> _faqs = [
    {
      'q': 'আমি কিভাবে ডাক্তারের চেম্বার সিরিয়াল বুক করব?',
      'a': 'হোমপেজ বা ডক্টর লিস্ট থেকে আপনার কাঙ্ক্ষিত ডাক্তার নির্বাচন করে "সিরিয়াল নিন" বাটনে ক্লিক করুন। আপনার পছন্দমতো তারিখ ও চেম্বার নির্বাচন করে রোগীর নাম ও ফোন নম্বর দিয়ে নিশ্চিত করলেই ডিজিটাল সিরিয়াল টোকেন পেয়ে যাবেন।',
    },
    {
      'q': 'লাইভ কিউ ট্র্যাকিং কিভাবে কাজ করে?',
      'a': 'চেম্বারের সহকারী বা কম্পাউন্ডার যখনই চেম্বারে রোগী প্রবেশের জন্য পরবর্তী টোকেন ডাকবেন, সাথে সাথে আপনার অ্যাপের লাইভ কিউ স্ক্রিনে বর্তমান চলমান নম্বর আপডেট হয়ে যাবে। ফলে চেম্বারে ঘণ্টার পর ঘণ্টা বসে থাকার প্রয়োজন হয় না।',
    },
    {
      'q': 'জরুরি মুহূর্তে রক্ত বা অ্যাম্বুলেন্স কিভাবে পাব?',
      'a': 'অ্যাপের "ইমার্জেন্সি হাব"-এ গিয়ে এক ক্লিকেই আপনার নিকটস্থ ভেরিফায়েড অ্যাম্বুলেন্স চালককে সরাসরি ফোন দিতে পারবেন কিংবা রক্তদাতা তালিকায় ব্লাড গ্রুপ অনুযায়ী ডোনারদের সাথে যোগাযোগ করতে পারবেন।',
    },
    {
      'q': 'প্রেসক্রিপশন কি আজীবন সংরক্ষণ থাকবে?',
      'a': 'হ্যাঁ, আপনার প্রেসক্রিপশন ও ডায়াগনস্টিক রিপোর্টগুলো আজীবন এনক্রিপ্টেড অবস্থায় আপনার পেশেন্ট পোর্টালে সংরক্ষিত থাকবে। যেকোনো সময় তা দেখতে বা পিডিএফ আকারে ডাউনলোড করতে পারবেন।',
    },
  ];

  @override
  void initState() {
    super.initState();
    _startBannerAutoScroll();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<HomeProvider>(context, listen: false).fetchHomeData();
    });
  }

  void _startBannerAutoScroll() {
    _bannerTimer?.cancel();
    _bannerTimer = Timer.periodic(const Duration(seconds: 4), (timer) {
      if (_bannerController.hasClients) {
        int nextIndex = (_currentBannerIndex + 1) % _promoBanners.length;
        _bannerController.animateToPage(
          nextIndex,
          duration: const Duration(milliseconds: 550),
          curve: Curves.easeInOut,
        );
      }
    });
  }

  @override
  void dispose() {
    _bannerTimer?.cancel();
    _bannerController.dispose();
    _searchCtrl.dispose();
    super.dispose();
  }

  Future<void> _makeCall(String phone) async {
    final uri = Uri.parse('tel:$phone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    final homeProvider = Provider.of<HomeProvider>(context);
    final authProvider = Provider.of<AuthProvider>(context);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      // 1. Navigation Header Bar
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 1,
        shadowColor: Colors.black.withValues(alpha: 0.05),
        titleSpacing: 16,
        title: Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF0284C7), Color(0xFF0EA5E9)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(10),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF0284C7).withValues(alpha: 0.3),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: const Icon(Icons.local_hospital_rounded, color: Colors.white, size: 20),
            ),
            const SizedBox(width: 10),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  'SUKHI MANUSH',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                    color: Color(0xFF0F172A),
                    letterSpacing: -0.5,
                  ),
                ),
                Text(
                  'সুখী মানুষ হেলথকেয়ার',
                  style: TextStyle(
                    fontSize: 8,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF64748B),
                    letterSpacing: 1.2,
                  ),
                ),
              ],
            ),
          ],
        ),
        actions: [
          // Emergency 106 Hotline Chip
          GestureDetector(
            onTap: () => Navigator.pushNamed(context, AppRoutes.emergencyHub),
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 11),
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: const Color(0xFFFFF1F2),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: const Color(0xFFFFCCD5)),
              ),
              child: const Row(
                children: [
                  Icon(Icons.phone_in_talk, color: Color(0xFFE11D48), size: 13),
                  SizedBox(width: 4),
                  Text(
                    '১০৬ হটলাইন',
                    style: TextStyle(
                      color: Color(0xFFE11D48),
                      fontWeight: FontWeight.bold,
                      fontSize: 11,
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(width: 8),
          IconButton(
            icon: Icon(
              authProvider.isAuthenticated ? Icons.account_circle : Icons.login_rounded,
              color: const Color(0xFF0F172A),
              size: 24,
            ),
            onPressed: () {
              if (authProvider.isAuthenticated) {
                final user = authProvider.currentUser;
                if (user != null) {
                  if (user.isDoctor) {
                    Navigator.pushNamed(context, AppRoutes.doctorDashboard);
                  } else if (user.isStaff) {
                    Navigator.pushNamed(context, AppRoutes.staffDashboard);
                  } else if (user.isAgent) {
                    Navigator.pushNamed(context, AppRoutes.agentDashboard);
                  } else if (user.isAdmin) {
                    Navigator.pushNamed(context, AppRoutes.adminDashboard);
                  } else {
                    Navigator.pushNamed(context, AppRoutes.patientDashboard);
                  }
                } else {
                  Navigator.pushNamed(context, AppRoutes.patientDashboard);
                }
              } else {
                Navigator.pushNamed(context, AppRoutes.login);
              }
            },
          ),
          const SizedBox(width: 8),
        ],
      ),

      body: RefreshIndicator(
        onRefresh: () => homeProvider.fetchHomeData(),
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // ==========================================
              // 1. HERO SECTION & LIVE SEARCH BAR
              // ==========================================
              Container(
                width: double.infinity,
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFFF0F9FF), Color(0xFFF8FAFC), Colors.white],
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                  ),
                ),
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Badge pill
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: const Color(0xFFE0F2FE),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: const Color(0xFFBAE6FD)),
                      ),
                      child: const Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.verified, color: Color(0xFF0284C7), size: 14),
                          SizedBox(width: 5),
                          Text(
                            'মাহরিভা ডিজিটাল স্বাস্থ্যসেবা প্ল্যাটফর্ম',
                            style: TextStyle(
                              color: Color(0xFF0369A1),
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 12),

                    // Title with gradient accent
                    RichText(
                      text: const TextSpan(
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.w900,
                          color: Color(0xFF0F172A),
                          height: 1.25,
                        ),
                        children: [
                          TextSpan(text: 'বিশেষজ্ঞ ডাক্তার খুঁজুন ও ঘরে বসেই '),
                          TextSpan(
                            text: 'চেম্বার সিরিয়াল',
                            style: TextStyle(color: Color(0xFF0284C7)),
                          ),
                          TextSpan(text: ' নিন'),
                        ],
                      ),
                    ),
                    const SizedBox(height: 8),

                    // Subtitle
                    const Text(
                      'বাংলাদেশের শীর্ষ হাসপাতাল ও ডায়াগনস্টিক সেন্টারের ভেরিফায়েড বিশেষজ্ঞ চিকিৎসক খুঁজুন, সরাসরি চেম্বারে নিশ্চিত সিরিয়াল নিন কিংবা ঘরে বসে লাইভ কিউ ট্র্যাক করুন।',
                      style: TextStyle(
                        fontSize: 12.5,
                        color: Color(0xFF64748B),
                        height: 1.45,
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Hero Search Card Widget
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(18),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.05),
                            blurRadius: 16,
                            offset: const Offset(0, 8),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          VoiceTextField(
                            controller: _searchCtrl,
                            hintText: 'ডাক্তার, রোগ বা হাসপাতালের নাম...',
                            prefixIcon: const Icon(Icons.person_search_outlined, color: Color(0xFF94A3B8)),
                            fillColor: const Color(0xFFF8FAFC),
                            onSubmitted: (val) {
                              Navigator.pushNamed(
                                context,
                                AppRoutes.doctorList,
                                arguments: val.trim(),
                              );
                            },
                          ),
                          const SizedBox(height: 10),
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton.icon(
                              onPressed: () {
                                Navigator.pushNamed(
                                  context,
                                  AppRoutes.doctorList,
                                  arguments: _searchCtrl.text.trim(),
                                );
                              },
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF0284C7),
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(vertical: 12),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                elevation: 0,
                              ),
                              icon: const Icon(Icons.search, size: 18),
                              label: const Text(
                                'ডাক্তার ও চেম্বার সার্চ করুন',
                                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // 4 Stat Metric Counters
                    Row(
                      children: [
                        _buildWebStatBox('৩,৩০০+', 'ভেরিফায়েড ডাক্তার'),
                        const SizedBox(width: 8),
                        _buildWebStatBox('৫০,০০০+', 'সিরিয়াল বুকিং'),
                        const SizedBox(width: 8),
                        _buildWebStatBox('৯৯.৪%', 'রোগী সন্তুষ্টি'),
                      ],
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 1.5. 🔥 ইন্টারেক্টিভ প্রমোশনাল ব্যানার ক্যারোসেল (Hero Promotional Carousel)
              // ==========================================
              const SizedBox(height: 12),
              _buildHeroPromoBannerSlider(),
              const SizedBox(height: 8),

              // ==========================================
              // 2. TODAY'S LIVE CHAMBER & QUEUE SIMULATOR
              // ==========================================
              if (homeProvider.heroDoctor != null) ...[
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(22),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.04),
                          blurRadius: 14,
                          offset: const Offset(0, 6),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Stack(
                              children: [
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(16),
                                  child: homeProvider.heroDoctor!.image != null
                                      ? Image.network(
                                          homeProvider.heroDoctor!.image!,
                                          width: 64,
                                          height: 64,
                                          fit: BoxFit.cover,
                                          alignment: Alignment.topCenter,
                                          errorBuilder: (_, __, ___) => _avatarFallback(),
                                        )
                                      : _avatarFallback(),
                                ),
                                Positioned(
                                  bottom: 0,
                                  right: 0,
                                  child: Container(
                                    width: 14,
                                    height: 14,
                                    decoration: BoxDecoration(
                                      color: const Color(0xFF10B981),
                                      shape: BoxShape.circle,
                                      border: Border.all(color: Colors.white, width: 2),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFE0F2FE),
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: const Text(
                                      'আজকের চেম্বার খোলা',
                                      style: TextStyle(
                                        color: Color(0xFF0369A1),
                                        fontSize: 10,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    homeProvider.heroDoctor!.name,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(
                                      fontSize: 15,
                                      fontWeight: FontWeight.w800,
                                      color: Color(0xFF0F172A),
                                    ),
                                  ),
                                  Text(
                                    homeProvider.heroDoctor!.qualifications ?? homeProvider.heroDoctor!.specialization,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),

                        // Live Serving Token Simulator
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF0FDF4),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: const Color(0xFFDCFCE7)),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFF16A34A),
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                    child: const Text(
                                      '#১২',
                                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  const Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text('এখন চলমান সিরিয়াল', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF14532D))),
                                      Text('ইবনে সিনা ডায়াগনস্টিক কাউন্টার', style: TextStyle(fontSize: 10, color: Color(0xFF15803D))),
                                    ],
                                  ),
                                ],
                              ),
                              GestureDetector(
                                onTap: () => Navigator.pushNamed(context, AppRoutes.liveQueue),
                                child: const Text('লাইভ ট্র্যাক →', style: TextStyle(color: Color(0xFF16A34A), fontWeight: FontWeight.bold, fontSize: 11)),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 12),

                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'ভিজিট ফি: ৳${homeProvider.heroDoctor!.consultationFee.toInt()}',
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                                color: Color(0xFF059669),
                              ),
                            ),
                            Row(
                              children: [
                                OutlinedButton(
                                  onPressed: () => Navigator.pushNamed(
                                    context,
                                    AppRoutes.doctorDetail,
                                    arguments: homeProvider.heroDoctor,
                                  ),
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                    side: const BorderSide(color: Color(0xFFCBD5E1)),
                                  ),
                                  child: const Text('প্রোফাইল', style: TextStyle(fontSize: 11, color: Color(0xFF334155))),
                                ),
                                const SizedBox(width: 8),
                                ElevatedButton(
                                  onPressed: () => Navigator.pushNamed(context, AppRoutes.bookAppointment, arguments: homeProvider.heroDoctor),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF0F172A),
                                    foregroundColor: Colors.white,
                                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                                  ),
                                  child: const Text('সিরিয়াল নিন', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ],

              // ==========================================
              // 3. 🚨 24/7 জরুরী স্বাস্থ্যসেবা ও ইমার্জেন্সি হাব
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'জরুরী স্বাস্থ্যসেবা (24/7 Emergency)',
                          style: TextStyle(
                            fontSize: 17,
                            fontWeight: FontWeight.w900,
                            color: Color(0xFF0F172A),
                          ),
                        ),
                        GestureDetector(
                          onTap: () => Navigator.pushNamed(context, AppRoutes.emergencyHub),
                          child: const Text(
                            'সব দেখুন →',
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFFE11D48)),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        Expanded(
                          child: _buildEmergencyQuickCard(
                            title: 'অ্যাম্বুলেন্স',
                            subtitle: 'সরাসরি কল',
                            icon: Icons.airport_shuttle,
                            color: const Color(0xFFE11D48),
                            bg: const Color(0xFFFFF1F2),
                            onTap: () => Navigator.pushNamed(context, AppRoutes.emergencyHub),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: _buildEmergencyQuickCard(
                            title: 'ব্লাড SOS',
                            subtitle: 'রক্তদাতা খুঁজুন',
                            icon: Icons.bloodtype,
                            color: const Color(0xFFBE123C),
                            bg: const Color(0xFFFFE4E6),
                            onTap: () => Navigator.pushNamed(context, AppRoutes.bloodSos),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: _buildEmergencyQuickCard(
                            title: 'আইসিইউ বেড',
                            subtitle: 'লাইভ রাডার',
                            icon: Icons.bed_outlined,
                            color: const Color(0xFF0284C7),
                            bg: const Color(0xFFF0F9FF),
                            onTap: () => Navigator.pushNamed(context, AppRoutes.emergencyHub),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: _buildEmergencyQuickCard(
                            title: '১০৬ হটলাইন',
                            subtitle: 'ফ্রি সেবা',
                            icon: Icons.phone_in_talk,
                            color: const Color(0xFF059669),
                            bg: const Color(0xFFECFDF5),
                            onTap: () => _makeCall('106'),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 3.5. 🩺 জনপ্রিয় হেলথ ও ডায়াগনস্টিক প্যাকেজ (Health Packages)
              // ==========================================
              _buildHealthPackagesSection(),

              // ==========================================
              // 4. 💎 কেন মাহরিভা বেছে নেবেন? (User Benefits)
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'কেন আমাদের প্ল্যাটফর্ম?',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF0284C7),
                        letterSpacing: 1,
                      ),
                    ),
                    const SizedBox(height: 2),
                    const Text(
                      'রোগী ও পরিবারের জন্য বিশেষ সুবিধাসমূহ',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'হাসপাতালে ঘণ্টার পর ঘণ্টা অপেক্ষার দিন শেষ। ডিজিটাল প্রযুক্তির মাধ্যমে সময়, শ্রম ও খরচ বাঁচান।',
                      style: TextStyle(fontSize: 11.5, color: Color(0xFF64748B)),
                    ),
                    const SizedBox(height: 12),

                    GridView.count(
                      crossAxisCount: 2,
                      crossAxisSpacing: 10,
                      mainAxisSpacing: 10,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      childAspectRatio: 1.25,
                      children: [
                        _buildBenefitCard(
                          title: 'ঘণ্টার পর ঘণ্টা লাইনে দাঁড়ানো শেষ',
                          desc: 'লাইভ কিউ ট্র্যাকিং দিয়ে ঘরে বসেই দেখুন এখন কোন সিরিয়াল চলছে।',
                          icon: Icons.timer_off_outlined,
                          color: const Color(0xFF0284C7),
                        ),
                        _buildBenefitCard(
                          title: '১০০% BMDC ভেরিফায়েড ডাক্তার',
                          desc: 'সকল চিকিৎসকের জাতীয় বিএমডিসি সনদ ও ডিগ্রি পুঙ্খানুপুঙ্খ যাচাইকৃত।',
                          icon: Icons.verified_user_outlined,
                          color: const Color(0xFF059669),
                        ),
                        _buildBenefitCard(
                          title: 'ভয়েস প্রেসক্রিপশন অডিও',
                          desc: 'প্রেসক্রিপশনে কোন ওষুধ কীভাবে খাবেন তা বাংলায় অডিও আকারে শুনুন।',
                          icon: Icons.record_voice_over_outlined,
                          color: const Color(0xFF7C3AED),
                        ),
                        _buildBenefitCard(
                          title: 'ডিজিটাল হেলথ পাসপোর্ট QR',
                          desc: 'জরুরি মুহূর্তে যেকেউ আপনার কিউআর কোড স্ক্যান করে রক্তের গ্রুপ জানতে পারবে।',
                          icon: Icons.qr_code_scanner_outlined,
                          color: const Color(0xFFE11D48),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 5. 🔄 কিভাবে অ্যাপটি কাজ করে? (4 Simple Steps)
              // ==========================================
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(22),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      '৪টি সহজ ধাপে সেবা গ্রহণ করুন',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                    const SizedBox(height: 14),
                    _buildStepRow('১', 'ডাক্তার খুঁজুন', 'রোগের ধরন বা বিশেষজ্ঞ বিভাগ দিয়ে উপযুক্ত চিকিৎসক বাছাই করুন।', Icons.search),
                    const Divider(height: 20),
                    _buildStepRow('২', 'তারিখ ও চেম্বার নির্বাচন', 'আপনার সুবিধাজনক দিন ও চেম্বার লোকেশন সিলেক্ট করুন।', Icons.event_available),
                    const Divider(height: 20),
                    _buildStepRow('৩', 'ডিজিটাল টোকেন গ্রহণ', 'মুহূর্তের মধ্যে আপনার সিরিয়াল নম্বর নিশ্চিত হয়ে এসএমএস ও অ্যাপে চলে আসবে।', Icons.confirmation_number_outlined),
                    const Divider(height: 20),
                    _buildStepRow('৪', 'লাইভ কিউ দেখে ভিজিট', 'অ্যাপেই রিয়েল-টাইম কিউ পর্যবেক্ষণ করে সঠিক সময়ে চেম্বারে প্রবেশ করুন।', Icons.check_circle_outline),
                  ],
                ),
              ),

              // ==========================================
              // 5.5. 📢 স্পন্সরড পার্টনার অ্যাডভার্টাইজমেন্ট ব্যানার (Sponsored Ad Banner)
              // ==========================================
              _buildSponsoredAdBanner(),

              // ==========================================
              // 6. TOP MEDICAL SPECIALTIES (ডিপার্টমেন্টসমূহ)
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'ডিপার্টমেন্টসমূহ',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF0284C7),
                        letterSpacing: 1,
                      ),
                    ),
                    const SizedBox(height: 2),
                    const Text(
                      'বিশেষায়িত বিভাগ অনুযায়ী ডাক্তার খুঁজুন',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'আপনার প্রয়োজনীয় স্বাস্থ্য বিভাগের ওপর ক্লিক করে অভিজ্ঞ চিকিৎসকদের তালিকা দেখুন।',
                      style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                    ),
                  ],
                ),
              ),

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                child: GridView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 4,
                    crossAxisSpacing: 8,
                    mainAxisSpacing: 8,
                    childAspectRatio: 0.85,
                  ),
                  itemCount: _departments.length,
                  itemBuilder: (context, index) {
                    final dept = _departments[index];
                    return GestureDetector(
                      onTap: () => Navigator.pushNamed(
                        context,
                        AppRoutes.doctorList,
                        arguments: dept['name'],
                      ),
                      child: Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: dept['bg'],
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: Icon(dept['icon'], color: dept['color'], size: 20),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              dept['name'],
                              textAlign: TextAlign.center,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: Color(0xFF0F172A),
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              ),

              // ==========================================
              // 7. FEATURED DOCTORS CAROUSEL / LIST
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 8),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'ভেরিফায়েড বিশেষজ্ঞ',
                          style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0284C7)),
                        ),
                        Text(
                          'শীর্ষ চিকিৎসক ও চেম্বারসমূহ',
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                        ),
                      ],
                    ),
                    TextButton(
                      onPressed: () => Navigator.pushNamed(context, AppRoutes.doctorList),
                      child: const Text('সব দেখুন →', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                    ),
                  ],
                ),
              ),

              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 16),
                itemCount: homeProvider.featuredDoctors.length,
                itemBuilder: (context, index) {
                  final doc = homeProvider.featuredDoctors[index];
                  return Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(18),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.03),
                          blurRadius: 10,
                          offset: const Offset(0, 3),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Stack(
                              children: [
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(14),
                                  child: doc.image != null
                                      ? Image.network(
                                          doc.image!,
                                          width: 60,
                                          height: 60,
                                          fit: BoxFit.cover,
                                          alignment: Alignment.topCenter,
                                          errorBuilder: (_, __, ___) => _avatarFallback(),
                                        )
                                      : _avatarFallback(),
                                ),
                                Positioned(
                                  bottom: 0,
                                  right: 0,
                                  child: Container(
                                    width: 12,
                                    height: 12,
                                    decoration: BoxDecoration(
                                      color: const Color(0xFF10B981),
                                      shape: BoxShape.circle,
                                      border: Border.all(color: Colors.white, width: 2),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                        decoration: BoxDecoration(
                                          color: const Color(0xFFE0F2FE),
                                          borderRadius: BorderRadius.circular(6),
                                        ),
                                        child: Text(
                                          doc.specialization,
                                          style: const TextStyle(
                                            color: Color(0xFF0369A1),
                                            fontSize: 9.5,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                      ),
                                      Row(
                                        children: [
                                          const Icon(Icons.star, color: Colors.amber, size: 12),
                                          const SizedBox(width: 2),
                                          Text(
                                            doc.rating.toStringAsFixed(1),
                                            style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    doc.name,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w800,
                                      color: Color(0xFF0F172A),
                                    ),
                                  ),
                                  Text(
                                    doc.qualifications ?? 'বিশেষজ্ঞ চিকিৎসক',
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 10),

                        // Chamber info & Fee
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF8FAFC),
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(color: const Color(0xFFF1F5F9)),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  const Icon(Icons.location_on, color: Color(0xFFE11D48), size: 13),
                                  const SizedBox(width: 4),
                                  Text(
                                    doc.district ?? 'ঢাকা',
                                    style: const TextStyle(fontSize: 11, color: Color(0xFF334155)),
                                  ),
                                ],
                              ),
                              Text(
                                'ফি: ৳${doc.consultationFee.toInt()}',
                                style: const TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w900,
                                  color: Color(0xFF059669),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 10),

                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            IconButton(
                              onPressed: () => _makeCall('106'),
                              icon: const Icon(Icons.phone_outlined, size: 16),
                              style: IconButton.styleFrom(
                                backgroundColor: const Color(0xFFF1F5F9),
                                padding: const EdgeInsets.all(6),
                              ),
                              tooltip: 'কল',
                            ),
                            Row(
                              children: [
                                OutlinedButton(
                                  onPressed: () => Navigator.pushNamed(
                                    context,
                                    AppRoutes.doctorDetail,
                                    arguments: doc,
                                  ),
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                    side: const BorderSide(color: Color(0xFFE2E8F0)),
                                  ),
                                  child: const Text('প্রোফাইল', style: TextStyle(fontSize: 11, color: Color(0xFF334155))),
                                ),
                                const SizedBox(width: 8),
                                ElevatedButton(
                                  onPressed: () => Navigator.pushNamed(context, AppRoutes.bookAppointment, arguments: doc),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF0F172A),
                                    foregroundColor: Colors.white,
                                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                                  ),
                                  child: const Text('সিরিয়াল নিন', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ],
                    ),
                  );
                },
              ),

              // ==========================================
              // 7.5. 🏠 হোম ডায়াগনস্টিক ও স্বাস্থ্যসেবা (Home Healthcare Services)
              // ==========================================
              _buildHomeServicesSection(),

              // ==========================================
              // 8. 🌐 সকল ইউজার রোল পোর্টাল (All User Roles Hub)
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 8),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'রোল পোর্টাল হাব',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0284C7)),
                    ),
                    const Text(
                      'সকল ইউজারদের ডেডিকেটেড পোর্টাল',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 10),
                    _buildFeatureCard(
                      tag: 'পেশেন্ট পোর্টাল',
                      title: 'রোগী ড্যাশবোর্ড ও রিপোর্ট খাতা',
                      desc: 'প্রেসক্রিপশন, অডিও ভয়েস গাইড, টেস্ট রিপোর্ট ও ফ্যামিলি প্রোফাইল সংরক্ষণ করুন।',
                      icon: Icons.receipt_long_rounded,
                      iconBg: const Color(0xFFDCFCE7),
                      iconColor: const Color(0xFF16A34A),
                      route: AppRoutes.patientDashboard,
                      actionText: 'আমার প্রেসক্রিপশন খুলুন →',
                    ),
                    const SizedBox(height: 10),
                    _buildFeatureCard(
                      tag: 'ডাক্তার কন্ট্রোল',
                      title: 'ডাক্তার পোর্টাল ও প্রেসক্রিপশন',
                      desc: 'লাইভ চেম্বার সিরিয়াল ডাকা, ই-প্রেসক্রিপশন প্রদান এবং রোগী তালিকা নিয়ন্ত্রণ করুন।',
                      icon: Icons.medical_services_outlined,
                      iconBg: const Color(0xFFEFF6FF),
                      iconColor: const Color(0xFF2563EB),
                      route: AppRoutes.doctorDashboard,
                      actionText: 'ডাক্তার পোর্টাল খুলুন →',
                    ),
                    const SizedBox(height: 10),
                    _buildFeatureCard(
                      tag: 'চেম্বার সহকারী',
                      title: 'কম্পাউন্ডার / স্টাফ পোর্টাল',
                      desc: 'চেম্বারের রোগী কাউন্টারে পরবর্তী টোকেন ডাকা এবং স্পট ওয়াক-ইন রোগী এন্ট্রি দিন।',
                      icon: Icons.people_alt_outlined,
                      iconBg: const Color(0xFFF0FDFA),
                      iconColor: const Color(0xFF0D9488),
                      route: AppRoutes.staffDashboard,
                      actionText: 'স্টাফ কাউন্টার খুলুন →',
                    ),
                    const SizedBox(height: 10),
                    _buildFeatureCard(
                      tag: 'গ্রামীণ ডিজিটাল স্বাস্থ্য',
                      title: 'ফার্মেসি এজেন্ট পোর্টাল',
                      desc: 'গ্রামের রোগীদের জন্য ডাক্তারের সিরিয়াল বুকিং দিয়ে প্রতি বুকিংয়ে ৪০ টাকা কমিশন আয় করুন।',
                      icon: Icons.storefront_outlined,
                      iconBg: const Color(0xFFECFDF5),
                      iconColor: const Color(0xFF059669),
                      route: AppRoutes.agentDashboard,
                      actionText: 'এজেন্ট পোর্টাল খুলুন →',
                    ),
                    const SizedBox(height: 10),
                    _buildFeatureCard(
                      tag: 'সুপার অ্যাডমিন',
                      title: 'প্ল্যাটফর্ম অ্যাডমিন প্যানেল',
                      desc: 'মোট রাজস্ব হিসাব, ১০% প্ল্যাটফর্ম ফি, ডাক্তার অনুমোদন ও সামগ্রিক অ্যাপ নিয়ন্ত্রণ।',
                      icon: Icons.admin_panel_settings_outlined,
                      iconBg: const Color(0xFFEEF2FF),
                      iconColor: const Color(0xFF4F46E5),
                      route: AppRoutes.adminDashboard,
                      actionText: 'অ্যাডমিন প্যানেল খুলুন →',
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 9. 🏥 পার্টনার হাসপাতাল নেটওয়ার্ক
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 24, 16, 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'সহযোগী নেটওয়ার্ক',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0284C7)),
                    ),
                    const Text(
                      'শীর্ষ হাসপাতাল ও ডায়াগনস্টিক পার্টনার্স',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 12),
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: _hospitalPartners.map((p) {
                          return Container(
                            width: 170,
                            margin: const EdgeInsets.only(right: 10),
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: const Color(0xFFE2E8F0)),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFE0F2FE),
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  child: Text(
                                    p['tag']!,
                                    style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Color(0xFF0369A1)),
                                  ),
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  p['name']!,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  p['city']!,
                                  style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                ),
                              ],
                            ),
                          );
                        }).toList(),
                      ),
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 9.5. 📖 দৈনিক স্বাস্থ্য টিপস ও ডক্টরস ব্লগ (Health Tips & Doctor Blogs)
              // ==========================================
              _buildHealthBlogsSection(),

              // ==========================================
              // 10. 💬 রোগীদের বাস্তব অভিজ্ঞতা ও রিভিউ
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'রোগীদের আস্থা',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0284C7)),
                    ),
                    const Text(
                      'রোগীদের অভিজ্ঞতা ও সন্তুষ্টি',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 12),
                    _buildTestimonialCard(
                      name: 'মো: রফিকুল ইসলাম',
                      location: 'ধানমন্ডি, ঢাকা',
                      comment: 'আগে ডাক্তারের চেম্বারে ৩ ঘণ্টা বসে থাকতে হতো। এখন অ্যাপে লাইভ কিউ দেখে সঠিক সময়ে পৌঁছাই। অসাধারণ সময় বাঁচানো সেবা!',
                      rating: 5,
                    ),
                    const SizedBox(height: 8),
                    _buildTestimonialCard(
                      name: 'নাজমুন নাহার বেগম',
                      location: 'রাজশাহী',
                      comment: 'ভয়েস প্রেসক্রিপশন ফিচারটা দারুণ! বয়স্ক মায়ের ওষুধ খাওয়ার সঠিক নিয়ম বাংলায় শুনে সহজেই বুঝিয়ে দিতে পেরেছি।',
                      rating: 5,
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 11. DOCTOR REGISTRATION CTA
              // ==========================================
              Container(
                margin: const EdgeInsets.all(16),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
                  ),
                  borderRadius: BorderRadius.circular(24),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: const Color(0xFFE11D48).withValues(alpha: 0.2),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: const Color(0xFFE11D48).withValues(alpha: 0.4)),
                      ),
                      child: const Text(
                        'ডাক্তার ও চেম্বারদের জন্য',
                        style: TextStyle(color: Color(0xFFFDA4AF), fontSize: 10, fontWeight: FontWeight.bold),
                      ),
                    ),
                    const SizedBox(height: 10),
                    const Text(
                      'আপনি কি একজন চিকিৎসক?\nআজই আপনার চেম্বারকে স্মার্ট করুন!',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                        height: 1.3,
                      ),
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'সহকারী পিন লগইন, লাইভ সিরিয়াল ডিসপ্লে, ডিজিটাল প্রেসক্রিপশন এবং রোগীদের সঠিক ট্র্যাকিং নিশ্চিত করুন মাত্র ১ ক্লিকে।',
                      style: TextStyle(fontSize: 11, color: Color(0xFF94A3B8), height: 1.4),
                    ),
                    const SizedBox(height: 14),
                    ElevatedButton(
                      onPressed: () => Navigator.pushNamed(context, AppRoutes.login),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF0284C7),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                      ),
                      child: const Text('ডাক্তার রেজিস্ট্রেশন বা লগইন →', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 12. ❓ সচরাচর জিজ্ঞাসিত প্রশ্নাবলী (FAQ Accordion)
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'প্রশ্নোত্তর',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0284C7)),
                    ),
                    const Text(
                      'সচরাচর জিজ্ঞাসিত প্রশ্নাবলী (FAQ)',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 10),
                    ..._faqs.map((faq) {
                      return Container(
                        margin: const EdgeInsets.only(bottom: 8),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: ExpansionTile(
                          title: Text(
                            faq['q']!,
                            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                          ),
                          children: [
                            Padding(
                              padding: const EdgeInsets.fromLTRB(16, 0, 16, 14),
                              child: Text(
                                faq['a']!,
                                style: const TextStyle(fontSize: 12, color: Color(0xFF475569), height: 1.45),
                              ),
                            ),
                          ],
                        ),
                      );
                    }),
                  ],
                ),
              ),

              // ==========================================
              // 13. FOOTER
              // ==========================================
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(24),
                color: const Color(0xFF0F172A),
                child: Column(
                  children: [
                    const Text(
                      'মাহরিভা ও নোভেনা হেলথ প্ল্যাটফর্ম',
                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      '২৪/৭ কাস্টমার সাপোর্ট হেল্পলাইন: ১০৬ বা ০১৭০০-০০০০০০\nইমেইল: support@sukhimanush.com',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Color(0xFF94A3B8), fontSize: 11, height: 1.5),
                    ),
                    const Divider(color: Colors.white12, height: 28),
                    const Text(
                      '© ২০২৬ সুখী মানুষ (Sukhi Manush). সর্বস্বত্ব সংরক্ষিত।',
                      style: TextStyle(color: Colors.white38, fontSize: 10),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 40),
            ],
          ),
        ),
      ),

      // Web-styled Bottom Navigation Bar
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 0,
        type: BottomNavigationBarType.fixed,
        selectedItemColor: const Color(0xFF0284C7),
        unselectedItemColor: const Color(0xFF64748B),
        backgroundColor: Colors.white,
        elevation: 8,
        onTap: (index) {
          if (index == 1) Navigator.pushNamed(context, AppRoutes.doctorList);
          if (index == 2) Navigator.pushNamed(context, AppRoutes.liveQueue);
          if (index == 3) Navigator.pushNamed(context, AppRoutes.aiSymptomChecker);
          if (index == 4) {
            if (authProvider.isAuthenticated) {
              Navigator.pushNamed(context, AppRoutes.patientDashboard);
            } else {
              Navigator.pushNamed(context, AppRoutes.login);
            }
          }
        },
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_filled), label: 'হোম'),
          BottomNavigationBarItem(icon: Icon(Icons.people_alt_outlined), label: 'ডাক্তার'),
          BottomNavigationBarItem(icon: Icon(Icons.confirmation_number_outlined), label: 'লাইভ কিউ'),
          BottomNavigationBarItem(icon: Icon(Icons.psychology_outlined), label: 'AI লক্ষণ'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'প্রোফাইল'),
        ],
      ),
    );
  }

  Widget _buildWebStatBox(String val, String title) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 8),
        decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: 0.9),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: const Color(0xFFE2E8F0)),
        ),
        child: Column(
          children: [
            Text(
              val,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w900,
                color: Color(0xFF0F172A),
              ),
            ),
            const SizedBox(height: 2),
            Text(
              title,
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 10, color: Color(0xFF64748B)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmergencyQuickCard({
    required String title,
    required String subtitle,
    required IconData icon,
    required Color color,
    required Color bg,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 4),
        decoration: BoxDecoration(
          color: bg,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: color.withValues(alpha: 0.2)),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 22),
            const SizedBox(height: 4),
            Text(
              title,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: color),
            ),
            Text(
              subtitle,
              style: TextStyle(fontSize: 9, color: color.withValues(alpha: 0.8)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBenefitCard({
    required String title,
    required String desc,
    required IconData icon,
    required Color color,
  }) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 22),
          const SizedBox(height: 6),
          Text(
            title,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
          ),
          const SizedBox(height: 2),
          Text(
            desc,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(fontSize: 10, color: Color(0xFF64748B), height: 1.3),
          ),
        ],
      ),
    );
  }

  Widget _buildStepRow(String num, String title, String desc, IconData icon) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 32,
          height: 32,
          decoration: BoxDecoration(
            color: const Color(0xFF0284C7).withValues(alpha: 0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          alignment: Alignment.center,
          child: Text(
            num,
            style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF0284C7), fontSize: 14),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(icon, size: 16, color: const Color(0xFF0284C7)),
                  const SizedBox(width: 6),
                  Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A))),
                ],
              ),
              const SizedBox(height: 2),
              Text(desc, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B), height: 1.3)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildTestimonialCard({
    required String name,
    required String location,
    required String comment,
    required int rating,
  }) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A))),
                  Text(location, style: const TextStyle(fontSize: 10.5, color: Color(0xFF64748B))),
                ],
              ),
              Row(
                children: List.generate(
                  rating,
                  (_) => const Icon(Icons.star, color: Colors.amber, size: 14),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            '“$comment”',
            style: const TextStyle(fontSize: 11.5, color: Color(0xFF475569), fontStyle: FontStyle.italic, height: 1.4),
          ),
        ],
      ),
    );
  }

  Widget _buildFeatureCard({
    required String tag,
    required String title,
    required String desc,
    required IconData icon,
    required Color iconBg,
    required Color iconColor,
    required String route,
    required String actionText,
  }) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: iconBg,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: iconColor, size: 20),
          ),
          const SizedBox(height: 8),
          Text(
            tag,
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.bold,
              color: iconColor,
              letterSpacing: 0.5,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            title,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: Color(0xFF0F172A),
            ),
          ),
          const SizedBox(height: 4),
          Text(
            desc,
            style: const TextStyle(fontSize: 11, color: Color(0xFF64748B), height: 1.35),
          ),
          const SizedBox(height: 8),
          GestureDetector(
            onTap: () => Navigator.pushNamed(context, route),
            child: Text(
              actionText,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: iconColor,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _avatarFallback() {
    return Container(
      width: 60,
      height: 60,
      color: const Color(0xFFF1F5F9),
      child: const Icon(Icons.person, color: Color(0xFF94A3B8), size: 30),
    );
  }

  // ===================================================
  // 🌟 HELPER: ROBUST NETWORK IMAGE WITH GRADIENT FALLBACK
  // ===================================================
  Widget _buildNetworkImageWithFallback(
    String url, {
    double? width,
    double? height,
    BoxFit fit = BoxFit.cover,
    IconData fallbackIcon = Icons.medical_services_outlined,
  }) {
    return Image.network(
      url,
      width: width,
      height: height,
      fit: fit,
      loadingBuilder: (context, child, loadingProgress) {
        if (loadingProgress == null) return child;
        return Container(
          width: width,
          height: height,
          color: const Color(0xFFE2E8F0),
          child: const Center(
            child: SizedBox(
              width: 24,
              height: 24,
              child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF0284C7)),
            ),
          ),
        );
      },
      errorBuilder: (context, error, stackTrace) {
        return Container(
          width: width,
          height: height,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF0284C7), Color(0xFF0F172A)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
          child: Center(
            child: Icon(fallbackIcon, color: Colors.white54, size: 36),
          ),
        );
      },
    );
  }

  // ===================================================
  // 1.5. 🔥 HERO PROMOTIONAL BANNER SLIDER & INDICATOR
  // ===================================================
  Widget _buildHeroPromoBannerSlider() {
    return Column(
      children: [
        SizedBox(
          height: 175,
          child: PageView.builder(
            controller: _bannerController,
            itemCount: _promoBanners.length,
            onPageChanged: (index) {
              setState(() => _currentBannerIndex = index);
            },
            itemBuilder: (context, index) {
              final banner = _promoBanners[index];
              return Container(
                margin: const EdgeInsets.symmetric(horizontal: 16),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.12),
                      blurRadius: 16,
                      offset: const Offset(0, 6),
                    ),
                  ],
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(20),
                  child: Stack(
                    children: [
                      // Background Image
                      Positioned.fill(
                        child: _buildNetworkImageWithFallback(banner['image']),
                      ),

                      // Gradient Overlay for readability
                      Positioned.fill(
                        child: Container(
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              begin: Alignment.centerLeft,
                              end: Alignment.centerRight,
                              colors: [
                                const Color(0xFF0F172A).withValues(alpha: 0.95),
                                const Color(0xFF0F172A).withValues(alpha: 0.75),
                                const Color(0xFF0F172A).withValues(alpha: 0.2),
                              ],
                            ),
                          ),
                        ),
                      ),

                      // Text and Action Content
                      Padding(
                        padding: const EdgeInsets.fromLTRB(16, 14, 16, 14),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            // Badge pill
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 3.5),
                              decoration: BoxDecoration(
                                color: banner['badgeColor'],
                                borderRadius: BorderRadius.circular(20),
                                boxShadow: [
                                  BoxShadow(
                                    color: (banner['badgeColor'] as Color).withValues(alpha: 0.4),
                                    blurRadius: 6,
                                  ),
                                ],
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.bolt, color: Colors.white, size: 12),
                                  const SizedBox(width: 4),
                                  Text(
                                    banner['badge'],
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 10.5,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ],
                              ),
                            ),

                            // Main Title & Subtitle
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  banner['title'],
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    fontSize: 15.5,
                                    fontWeight: FontWeight.w900,
                                    color: Colors.white,
                                    height: 1.25,
                                  ),
                                ),
                                const SizedBox(height: 3),
                                Text(
                                  banner['subtitle'],
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    fontSize: 10.5,
                                    color: Color(0xFFE2E8F0),
                                  ),
                                ),
                              ],
                            ),

                            // Action Button
                            Align(
                              alignment: Alignment.centerLeft,
                              child: ElevatedButton.icon(
                                onPressed: () {
                                  Navigator.pushNamed(context, banner['route']);
                                },
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: const Color(0xFF0284C7),
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                                  elevation: 0,
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                ),
                                icon: const Icon(Icons.arrow_forward_rounded, size: 14),
                                label: Text(
                                  banner['btnText'],
                                  style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 8),

        // Dots Indicator
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: List.generate(_promoBanners.length, (i) {
            final isActive = i == _currentBannerIndex;
            return AnimatedContainer(
              duration: const Duration(milliseconds: 300),
              margin: const EdgeInsets.symmetric(horizontal: 3),
              width: isActive ? 18 : 6,
              height: 6,
              decoration: BoxDecoration(
                color: isActive ? const Color(0xFF0284C7) : const Color(0xFFCBD5E1),
                borderRadius: BorderRadius.circular(10),
              ),
            );
          }),
        ),
      ],
    );
  }

  // ===================================================
  // 3.5. 🩺 POPULAR HEALTH & DIAGNOSTIC PACKAGES SECTION
  // ===================================================
  Widget _buildHealthPackagesSection() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'স্পেশাল অফার ও টেস্ট প্যাকেজ',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0284C7),
                      letterSpacing: 1,
                    ),
                  ),
                  SizedBox(height: 2),
                  Text(
                    'জনপ্রিয় প্রিভেন্টিভ হেলথ প্যাকেজ',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                ],
              ),
              GestureDetector(
                onTap: () => Navigator.pushNamed(context, AppRoutes.doctorList),
                child: const Text(
                  'সব দেখুন →',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF0284C7),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 4),
          const Text(
            'অভিজ্ঞ ডায়াগনস্টিক ল্যাব ও স্পেশালিস্ট ডাক্তার সমন্বয়ে পরিবারের জন্য বিশেষ ছাড়ের স্বাস্থ্য প্যাকেজ।',
            style: TextStyle(fontSize: 11.5, color: Color(0xFF64748B)),
          ),
          const SizedBox(height: 12),

          SizedBox(
            height: 255,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: _healthPackages.length,
              itemBuilder: (context, index) {
                final pkg = _healthPackages[index];
                return Container(
                  width: 235,
                  margin: const EdgeInsets.only(right: 12, bottom: 6),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(18),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.04),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Image with overlay tags
                      ClipRRect(
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(18)),
                        child: Stack(
                          children: [
                            SizedBox(
                              height: 105,
                              width: double.infinity,
                              child: _buildNetworkImageWithFallback(pkg['image']),
                            ),
                            Positioned(
                              top: 8,
                              left: 8,
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                                decoration: BoxDecoration(
                                  color: pkg['tagColor'],
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  pkg['tag'],
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 9.5,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ),
                            Positioned(
                              top: 8,
                              right: 8,
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                                decoration: BoxDecoration(
                                  color: const Color(0xFF059669),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  pkg['discount'],
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 9.5,
                                    fontWeight: FontWeight.w900,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                      // Package Details
                      Padding(
                        padding: const EdgeInsets.all(10),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              pkg['title'],
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                                color: Color(0xFF0F172A),
                              ),
                            ),
                            const SizedBox(height: 3),
                            Text(
                              pkg['tests'],
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 10,
                                color: Color(0xFF64748B),
                                height: 1.3,
                              ),
                            ),
                            const SizedBox(height: 8),

                            // Pricing & CTA
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      pkg['offerPrice'],
                                      style: const TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.w900,
                                        color: Color(0xFF059669),
                                      ),
                                    ),
                                    Text(
                                      pkg['originalPrice'],
                                      style: const TextStyle(
                                        fontSize: 10,
                                        color: Color(0xFF94A3B8),
                                        decoration: TextDecoration.lineThrough,
                                      ),
                                    ),
                                  ],
                                ),
                                ElevatedButton(
                                  onPressed: () {
                                    Navigator.pushNamed(context, AppRoutes.doctorList);
                                  },
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF0F172A),
                                    foregroundColor: Colors.white,
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                    elevation: 0,
                                  ),
                                  child: const Text('বুক করুন', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  // ===================================================
  // 4.5. 📢 SPONSORED PARTNER ADVERTISEMENT BANNER
  // ===================================================
  Widget _buildSponsoredAdBanner() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF0284C7), Color(0xFF0F172A)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(22),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0284C7).withValues(alpha: 0.25),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(22),
        child: Stack(
          children: [
            // Decorative background pattern
            Positioned(
              right: -20,
              bottom: -20,
              child: Icon(
                Icons.local_hospital_rounded,
                color: Colors.white.withValues(alpha: 0.08),
                size: 160,
              ),
            ),

            Padding(
              padding: const EdgeInsets.all(18),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: Colors.amber,
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: const Text(
                          'স্পন্সরড পার্টনার ক্যাম্পেইন',
                          style: TextStyle(
                            color: Color(0xFF0F172A),
                            fontSize: 9.5,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 0.5,
                          ),
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: const Row(
                          children: [
                            Icon(Icons.timer_outlined, color: Colors.white, size: 12),
                            SizedBox(width: 4),
                            Text(
                              'সীমিত সময়ের অফার',
                              style: TextStyle(color: Colors.white, fontSize: 10),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  const Text(
                    'ইবনে সিনা & পপুলার হেলথ ক্যাম্প\nসকল ল্যাব ও স্ক্যানে ২৫% ছাড়!',
                    style: TextStyle(
                      fontSize: 17,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                      height: 1.3,
                    ),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'নোভেনা অ্যাপের মাধ্যমে সিরিয়াল ও ইনভেস্টিগেশন বুকিং দিলে যেকোনো রক্ত ও রেডিওলজি টেস্টে নিশ্চিত ২৫% ক্যাশব্যাক ছাড়।',
                    style: TextStyle(fontSize: 11, color: Color(0xFFE2E8F0), height: 1.4),
                  ),
                  const SizedBox(height: 12),

                  Row(
                    children: [
                      // Coupon Box
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.white30),
                        ),
                        child: const Row(
                          children: [
                            Icon(Icons.confirmation_number_outlined, color: Colors.amber, size: 14),
                            SizedBox(width: 5),
                            Text(
                              'কুপন: NOVENA25',
                              style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 11),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 10),

                      // CTA Button
                      ElevatedButton(
                        onPressed: () => Navigator.pushNamed(context, AppRoutes.doctorList),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.white,
                          foregroundColor: const Color(0xFF0F172A),
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                          elevation: 0,
                        ),
                        child: const Text('অফারটি নিন →', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ===================================================
  // 7.5. 🏠 HOME HEALTHCARE & SAMPLE COLLECTION SECTION
  // ===================================================
  Widget _buildHomeServicesSection() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 20, 16, 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'হোম ডায়াগনস্টিক ও নার্সিং',
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: Color(0xFF0284C7),
              letterSpacing: 1,
            ),
          ),
          const SizedBox(height: 2),
          const Text(
            'বাসায় বসেই বিশেষায়িত চিকিৎসা সেবা',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w800,
              color: Color(0xFF0F172A),
            ),
          ),
          const SizedBox(height: 4),
          const Text(
            'হাসপাতালে না গিয়েও আপনার বয়োবৃদ্ধ বা অসুস্থ প্রিয়জনের জন্য ভেরিফায়েড মেডিকেল টিম সরাসরি বাসায় পৌঁছে যাবে।',
            style: TextStyle(fontSize: 11.5, color: Color(0xFF64748B)),
          ),
          const SizedBox(height: 12),

          GridView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _homeServices.length,
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              crossAxisSpacing: 10,
              mainAxisSpacing: 10,
              childAspectRatio: 0.95,
            ),
            itemBuilder: (context, index) {
              final srv = _homeServices[index];
              return Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.03),
                      blurRadius: 8,
                      offset: const Offset(0, 3),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    ClipRRect(
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                      child: SizedBox(
                        height: 72,
                        width: double.infinity,
                        child: _buildNetworkImageWithFallback(srv['image']),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(8),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Icon(srv['icon'] as IconData, size: 14, color: srv['color'] as Color),
                              const SizedBox(width: 4),
                              Expanded(
                                child: Text(
                                  srv['title'],
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    fontSize: 11.5,
                                    fontWeight: FontWeight.w800,
                                    color: Color(0xFF0F172A),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 3),
                          Text(
                            srv['desc'],
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(fontSize: 9.5, color: Color(0xFF64748B), height: 1.25),
                          ),
                          const SizedBox(height: 6),
                          GestureDetector(
                            onTap: () => Navigator.pushNamed(context, AppRoutes.emergencyHub),
                            child: Text(
                              'সার্ভিস বুকিং →',
                              style: TextStyle(
                                fontSize: 10.5,
                                fontWeight: FontWeight.bold,
                                color: srv['color'] as Color,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  // ===================================================
  // 9.5. 📖 DAILY HEALTH TIPS & DOCTORS BLOGS SECTION
  // ===================================================
  Widget _buildHealthBlogsSection() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 20, 16, 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'ডক্টরস ব্লগ ও টিপস',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0284C7),
                      letterSpacing: 1,
                    ),
                  ),
                  SizedBox(height: 2),
                  Text(
                    'দৈনন্দিন স্বাস্থ্য সচেতনতা ও গাইড',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                ],
              ),
              GestureDetector(
                onTap: () => Navigator.pushNamed(context, AppRoutes.aiSymptomChecker),
                child: const Text(
                  'AI চেক করুন →',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF0284C7),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 4),
          const Text(
            'শীর্ষ বিশেষজ্ঞ ডাক্তারদের তথ্যবহুল স্বাস্থ্য পরামর্শ ও সিজনাল রোগ প্রতিরোধের সহজ উপায়।',
            style: TextStyle(fontSize: 11.5, color: Color(0xFF64748B)),
          ),
          const SizedBox(height: 12),

          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _healthBlogs.length,
            itemBuilder: (context, index) {
              final blog = _healthBlogs[index];
              return Container(
                margin: const EdgeInsets.only(bottom: 10),
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.03),
                      blurRadius: 8,
                      offset: const Offset(0, 3),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(12),
                      child: SizedBox(
                        width: 78,
                        height: 78,
                        child: _buildNetworkImageWithFallback(blog['image']),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF1F5F9),
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Text(
                                  blog['category'],
                                  style: const TextStyle(
                                    fontSize: 9.5,
                                    fontWeight: FontWeight.bold,
                                    color: Color(0xFF0284C7),
                                  ),
                                ),
                              ),
                              Row(
                                children: [
                                  const Icon(Icons.schedule, size: 10, color: Color(0xFF94A3B8)),
                                  const SizedBox(width: 3),
                                  Text(
                                    blog['readTime'],
                                    style: const TextStyle(fontSize: 9.5, color: Color(0xFF94A3B8)),
                                  ),
                                ],
                              ),
                            ],
                          ),
                          const SizedBox(height: 4),
                          Text(
                            blog['title'],
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              fontSize: 12.5,
                              fontWeight: FontWeight.w800,
                              color: Color(0xFF0F172A),
                              height: 1.25,
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            blog['author'],
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(fontSize: 10, color: Color(0xFF64748B)),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
    );
  }
}
