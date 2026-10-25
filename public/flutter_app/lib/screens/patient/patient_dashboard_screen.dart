import 'dart:io';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../core/routes/app_routes.dart';
import '../../models/user_model.dart';
import '../../providers/auth_provider.dart';
import '../../providers/patient_portal_provider.dart';
import '../../widgets/voice_text_field.dart';

class PatientDashboardScreen extends StatefulWidget {
  const PatientDashboardScreen({super.key});

  @override
  State<PatientDashboardScreen> createState() => _PatientDashboardScreenState();
}

class _PatientDashboardScreenState extends State<PatientDashboardScreen> {
  int _activeTab = 0; // 0: Reports, 1: Prescriptions, 2: Vitals, 3: Appointments, 4: Medicine Routine, 5: Health Passport, 6: Family
  String _selectedReportFilter = 'all';
  final TextEditingController _reportSearchCtrl = TextEditingController();
  String _vitalsMetricType = 'blood_sugar';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
    });
  }

  @override
  void dispose() {
    _reportSearchCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    final authProv = Provider.of<AuthProvider>(context, listen: false);
    final portalProv = Provider.of<PatientPortalProvider>(context, listen: false);

    // Auto-login as demo patient if user is not authenticated or not patient
    if (authProv.currentUser == null || !authProv.currentUser!.isPatient) {
      await authProv.quickDemoLogin('patient');
    }

    final userId = authProv.currentUser?.id ?? 2;
    await portalProv.fetchAllPortalData(userId);
  }

  // Dialog: Add Vitals Reading
  void _openAddMetricDialog() {
    final primaryCtrl = TextEditingController();
    final secondaryCtrl = TextEditingController();
    final timeCtrl = TextEditingController(text: 'সকালে খালি পেটে');
    final notesCtrl = TextEditingController();
    String metricType = _vitalsMetricType;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) => Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
          ),
          padding: EdgeInsets.only(
            top: 24,
            left: 20,
            right: 20,
            bottom: MediaQuery.of(ctx).viewInsets.bottom + 24,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'নতুন স্বাস্থ্য মাপ যোগ করুন',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
                  ),
                  IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
                ],
              ),
              const SizedBox(height: 14),

              DropdownButtonFormField<String>(
                value: metricType,
                decoration: const InputDecoration(labelText: 'মাপের ধরন'),
                items: const [
                  DropdownMenuItem(value: 'blood_sugar', child: Text('ব্লাড সুগার (Blood Sugar)')),
                  DropdownMenuItem(value: 'blood_pressure', child: Text('ব্লাড প্রেশার (Blood Pressure)')),
                  DropdownMenuItem(value: 'weight', child: Text('ওজন (Weight kg)')),
                  DropdownMenuItem(value: 'pulse', child: Text('হার্টবিট / পালস (Heart Rate bpm)')),
                ],
                onChanged: (val) => setModalState(() => metricType = val!),
              ),
              const SizedBox(height: 10),

              Row(
                children: [
                  Expanded(
                    child: VoiceTextField(
                      controller: primaryCtrl,
                      keyboardType: TextInputType.number,
                      labelText: metricType == 'blood_pressure' ? 'সিস্টোলিক (উদা: ১২০)' : 'মান (Value) *',
                      hintText: metricType == 'blood_sugar' ? '৬.৫' : '১২০',
                    ),
                  ),
                  if (metricType == 'blood_pressure') ...[
                    const SizedBox(width: 10),
                    Expanded(
                      child: VoiceTextField(
                        controller: secondaryCtrl,
                        keyboardType: TextInputType.number,
                        labelText: 'ডায়াস্টোলিক (উদা: ৮০)',
                        hintText: '৮০',
                      ),
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 10),

              VoiceTextField(
                controller: timeCtrl,
                labelText: 'মাপ নেওয়ার সময়',
                hintText: 'উদা: সকালে খালি পেটে / রাতে',
              ),
              const SizedBox(height: 10),

              VoiceTextField(
                controller: notesCtrl,
                labelText: 'মন্তব্য / অনুভূতি',
                hintText: 'যেমন: কিছুটা মাথা ঘোরার অনুভূতি...',
              ),
              const SizedBox(height: 18),

              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF0284C7),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: () async {
                    if (primaryCtrl.text.isEmpty) return;
                    final prov = Provider.of<PatientPortalProvider>(context, listen: false);
                    bool ok = await prov.storeHealthMetric({
                      'metric_type': metricType,
                      'value_primary': double.tryParse(primaryCtrl.text) ?? 0.0,
                      if (secondaryCtrl.text.isNotEmpty)
                        'value_secondary': double.tryParse(secondaryCtrl.text),
                      'measured_time': timeCtrl.text.trim(),
                      'notes': notesCtrl.text.trim(),
                    });

                    if (mounted) {
                      Navigator.pop(ctx);
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text(ok ? 'স্বাস্থ্য মাপ সফলভাবে সেভ হয়েছে!' : 'সেভ করা সম্ভব হয়নি।')),
                      );
                    }
                  },
                  child: const Text('সেভ করুন ✓', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Dialog: Add Family Member
  void _openAddFamilyDialog() {
    final nameCtrl = TextEditingController();
    final ageCtrl = TextEditingController();
    String relationship = 'Father';
    String gender = 'Male';
    String bloodGroup = 'O+';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) => Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
          ),
          padding: EdgeInsets.only(
            top: 24,
            left: 20,
            right: 20,
            bottom: MediaQuery.of(ctx).viewInsets.bottom + 24,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'নতুন ফ্যামিলি মেম্বার যুক্ত করুন',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
                  ),
                  IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
                ],
              ),
              const SizedBox(height: 14),

              VoiceTextField(
                controller: nameCtrl,
                labelText: 'সদস্যের নাম *',
                hintText: 'যেমন: পিতা / মাতা / সন্তানের নাম',
              ),
              const SizedBox(height: 10),

              Row(
                children: [
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: relationship,
                      decoration: const InputDecoration(labelText: 'সম্পর্ক *'),
                      items: const [
                        DropdownMenuItem(value: 'Father', child: Text('বাবা (Father)')),
                        DropdownMenuItem(value: 'Mother', child: Text('মা (Mother)')),
                        DropdownMenuItem(value: 'Spouse', child: Text('স্ত্রী/স্বামী (Spouse)')),
                        DropdownMenuItem(value: 'Child', child: Text('সন্তান (Child)')),
                        DropdownMenuItem(value: 'Sibling', child: Text('ভাই/বোন (Sibling)')),
                        DropdownMenuItem(value: 'Other', child: Text('অন্যান্য')),
                      ],
                      onChanged: (val) => setModalState(() => relationship = val!),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: gender,
                      decoration: const InputDecoration(labelText: 'লিঙ্গ'),
                      items: const [
                        DropdownMenuItem(value: 'Male', child: Text('পুরুষ')),
                        DropdownMenuItem(value: 'Female', child: Text('মহিলা')),
                        DropdownMenuItem(value: 'Other', child: Text('অন্যান্য')),
                      ],
                      onChanged: (val) => setModalState(() => gender = val!),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),

              Row(
                children: [
                  Expanded(
                    child: VoiceTextField(
                      controller: ageCtrl,
                      labelText: 'বয়স',
                      hintText: 'যেমন: ৫২ বছর',
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: bloodGroup,
                      decoration: const InputDecoration(labelText: 'রক্তের গ্রুপ'),
                      items: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']
                          .map((b) => DropdownMenuItem(value: b, child: Text(b)))
                          .toList(),
                      onChanged: (val) => setModalState(() => bloodGroup = val!),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 18),

              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF0F172A),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: () async {
                    if (nameCtrl.text.isEmpty) return;
                    final prov = Provider.of<PatientPortalProvider>(context, listen: false);
                    bool ok = await prov.storeFamilyMember({
                      'name': nameCtrl.text.trim(),
                      'relationship': relationship,
                      'gender': gender,
                      'age_or_dob': ageCtrl.text.trim(),
                      'blood_group': bloodGroup,
                    });

                    if (mounted) {
                      Navigator.pop(ctx);
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text(ok ? 'ফ্যামিলি মেম্বার সফলভাবে যুক্ত হয়েছে!' : 'যোগ করা সম্ভব হয়নি।')),
                      );
                    }
                  },
                  child: const Text('যুক্ত করুন ✓', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Dialog: Upload Medical Report
  void _openUploadReportDialog() {
    final titleCtrl = TextEditingController();
    final centerCtrl = TextEditingController(text: 'ইবনে সিনা ডায়াগনস্টিক');
    String testType = 'blood_test';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) => Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
          ),
          padding: EdgeInsets.only(
            top: 24,
            left: 20,
            right: 20,
            bottom: MediaQuery.of(ctx).viewInsets.bottom + 24,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'ল্যাব টেস্ট রিপোর্ট আপলোড',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFFE11D48)),
                  ),
                  IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
                ],
              ),
              const SizedBox(height: 14),

              VoiceTextField(
                controller: titleCtrl,
                labelText: 'টেস্ট রিপোর্টের নাম *',
                hintText: 'যেমন: CBC Blood Test / X-Ray Chest',
              ),
              const SizedBox(height: 10),

              DropdownButtonFormField<String>(
                value: testType,
                decoration: const InputDecoration(labelText: 'টেস্টের ধরন'),
                items: const [
                  DropdownMenuItem(value: 'blood_test', child: Text('রক্তের পরীক্ষা (Blood Test)')),
                  DropdownMenuItem(value: 'xray', child: Text('এক্স-রে (X-Ray)')),
                  DropdownMenuItem(value: 'mri_ct', child: Text('এমআরআই / সিটি স্ক্যান (MRI / CT Scan)')),
                  DropdownMenuItem(value: 'ultrasound', child: Text('আল্ট্রাসনোগ্রাম (Ultrasound)')),
                  DropdownMenuItem(value: 'prescription', child: Text('প্রেসক্রিপশন কপি')),
                  DropdownMenuItem(value: 'other', child: Text('অন্যান্য')),
                ],
                onChanged: (val) => setModalState(() => testType = val!),
              ),
              const SizedBox(height: 10),

              VoiceTextField(
                controller: centerCtrl,
                labelText: 'ডায়াগনস্টিক সেন্টারের নাম',
                hintText: 'যেমন: পপুলার ডায়াগনস্টিক / ল্যাবএইড',
              ),
              const SizedBox(height: 16),

              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFF1F2),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFFFCCD5)),
                ),
                child: const Column(
                  children: [
                    Icon(Icons.cloud_upload_outlined, size: 36, color: Color(0xFFE11D48)),
                    SizedBox(height: 6),
                    Text(
                      'ছবি বা পিডিএফ ফাইল সংযুক্ত হবে',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                    ),
                    Text(
                      'সর্বোচ্চ ২০ মেগাবাইট (JPG, PNG, PDF)',
                      style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),

              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFE11D48),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: () async {
                    if (titleCtrl.text.isEmpty) return;

                    final tmpDir = Directory.systemTemp;
                    final dummyFile = File('${tmpDir.path}/report_sample.jpg');
                    if (!await dummyFile.exists()) {
                      await dummyFile.writeAsBytes([0xFF, 0xD8, 0xFF, 0xE0, 0x00, 0x10, 0x4A, 0x46, 0x49, 0x46, 0x00]);
                    }

                    final prov = Provider.of<PatientPortalProvider>(context, listen: false);
                    bool ok = await prov.uploadReport(dummyFile, {
                      'report_title': titleCtrl.text.trim(),
                      'test_type': testType,
                      'diagnostic_center': centerCtrl.text.trim(),
                      'report_date': DateTime.now().toIso8601String().split('T').first,
                    });

                    if (mounted) {
                      Navigator.pop(ctx);
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text(ok ? 'রিপোর্ট সফলভাবে আপলোড হয়েছে!' : 'আপলোড ব্যর্থ হয়েছে।')),
                      );
                    }
                  },
                  child: const Text('আপলোড সম্পন্ন করুন', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final authProv = Provider.of<AuthProvider>(context);
    final user = authProv.currentUser;
    final portalProv = Provider.of<PatientPortalProvider>(context);

    // Filter reports based on selected category chip & search query
    final filteredReports = portalProv.reports.where((r) {
      if (_selectedReportFilter != 'all' && r.testType != _selectedReportFilter) {
        return false;
      }
      if (_reportSearchCtrl.text.trim().isNotEmpty) {
        final q = _reportSearchCtrl.text.toLowerCase().trim();
        return r.reportTitle.toLowerCase().contains(q) ||
            (r.diagnosticCenter?.toLowerCase().contains(q) ?? false);
      }
      return true;
    }).toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: Colors.red.withOpacity(0.12),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.receipt_long, color: Color(0xFFE11D48), size: 18),
            ),
            const SizedBox(width: 8),
            const Text(
              'Patient Portal • স্বাস্থ্য ভল্ট',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Colors.white),
            ),
          ],
        ),
        backgroundColor: const Color(0xFF0284C7),
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          // Family Switcher Pill Dropdown in AppBar
          Container(
            margin: const EdgeInsets.symmetric(vertical: 10),
            padding: const EdgeInsets.symmetric(horizontal: 10),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.18),
              borderRadius: BorderRadius.circular(20),
            ),
            child: PopupMenuButton<dynamic>(
              tooltip: 'ফ্যামিলি প্রোফাইল পরিবর্তন',
              icon: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(Icons.groups, color: Colors.white, size: 16),
                  const SizedBox(width: 4),
                  ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 90),
                    child: Text(
                      portalProv.activeFamilyMember != null
                          ? portalProv.activeFamilyMember!.name
                          : (user?.name ?? 'Self'),
                      style: const TextStyle(fontSize: 11, color: Colors.white, fontWeight: FontWeight.bold),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const Icon(Icons.arrow_drop_down, color: Colors.white, size: 16),
                ],
              ),
              onSelected: (val) {
                if (val == 'self') {
                  portalProv.setActiveFamilyMember(null);
                } else if (val == 'new') {
                  _openAddFamilyDialog();
                } else {
                  portalProv.setActiveFamilyMember(val);
                }
              },
              itemBuilder: (ctx) => [
                PopupMenuItem(
                  value: 'self',
                  child: Row(
                    children: [
                      const Icon(Icons.person, color: Color(0xFF0284C7), size: 18),
                      const SizedBox(width: 8),
                      Text('${user?.name ?? "আমার একাউন্ট"} (মূল রোগী)'),
                    ],
                  ),
                ),
                ...portalProv.familyMembers.map((m) => PopupMenuItem(
                      value: m,
                      child: Row(
                        children: [
                          const Icon(Icons.family_restroom, color: Colors.teal, size: 18),
                          const SizedBox(width: 8),
                          Text('${m.name} (${m.relationship})'),
                        ],
                      ),
                    )),
                const PopupMenuDivider(),
                const PopupMenuItem(
                  value: 'new',
                  child: Row(
                    children: [
                      Icon(Icons.add, color: Color(0xFFE11D48), size: 18),
                      SizedBox(width: 8),
                      Text('+ নতুন সদস্য যুক্ত করুন', style: TextStyle(color: Color(0xFFE11D48), fontWeight: FontWeight.bold)),
                    ],
                  ),
                ),
              ],
            ),
          ),
          IconButton(
            tooltip: 'ডাক্তার খুঁজুন',
            icon: const Icon(Icons.search),
            onPressed: () => Navigator.pushNamed(context, AppRoutes.doctorList),
          ),
          IconButton(
            tooltip: 'লগআউট',
            icon: const Icon(Icons.logout, color: Colors.redAccent),
            onPressed: () async {
              await authProv.logout();
              if (context.mounted) {
                Navigator.pushReplacementNamed(context, AppRoutes.landing);
              }
            },
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => _loadData(),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // ==========================================
              // Quick Multi-Role Switcher Demo Bar
              // ==========================================
              Container(
                width: double.infinity,
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                color: const Color(0xFF0F172A),
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      const Text(
                        '⚡ ড্যাশবোর্ড সুইচ:',
                        style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(width: 8),
                      _buildQuickRoleBadge('🧑 রোগী', true, () {}),
                      const SizedBox(width: 6),
                      _buildQuickRoleBadge('🩺 ডাক্তার', false, () async {
                        await authProv.quickDemoLogin('doctor');
                        if (context.mounted) {
                          Navigator.pushNamed(context, AppRoutes.doctorDashboard);
                        }
                      }),
                      const SizedBox(width: 6),
                      _buildQuickRoleBadge('👥 স্টাফ', false, () async {
                        await authProv.quickDemoLogin('staff');
                        if (context.mounted) {
                          Navigator.pushNamed(context, AppRoutes.staffDashboard);
                        }
                      }),
                      const SizedBox(width: 6),
                      _buildQuickRoleBadge('🏪 এজেন্ট', false, () async {
                        await authProv.quickDemoLogin('agent');
                        if (context.mounted) {
                          Navigator.pushNamed(context, AppRoutes.agentDashboard);
                        }
                      }),
                      const SizedBox(width: 6),
                      _buildQuickRoleBadge('🛡️ অ্যাডমিন', false, () async {
                        await authProv.quickDemoLogin('admin');
                        if (context.mounted) {
                          Navigator.pushNamed(context, AppRoutes.adminDashboard);
                        }
                      }),
                    ],
                  ),
                ),
              ),

              // ==========================================
              // 1. Live Chamber Queue Tracker Yellow Banner (Web Replica)
              // ==========================================
              Container(
                margin: const EdgeInsets.fromLTRB(16, 12, 16, 12),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF3C7),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFF59E0B), width: 1.5),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFFF59E0B).withOpacity(0.12),
                      blurRadius: 8,
                      offset: const Offset(0, 3),
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          width: 10,
                          height: 10,
                          margin: const EdgeInsets.only(top: 4, right: 8),
                          decoration: const BoxDecoration(
                            color: Colors.red,
                            shape: BoxShape.circle,
                          ),
                        ),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                '⏰ লাইভ চেম্বার সিরিয়াল ট্র্যাকার: ডা. নাহিদ আফসানা জামান',
                                style: TextStyle(
                                  fontWeight: FontWeight.w900,
                                  fontSize: 13,
                                  color: Color(0xFF92400E),
                                ),
                              ),
                              const SizedBox(height: 3),
                              Wrap(
                                crossAxisAlignment: WrapCrossAlignment.center,
                                children: [
                                  const Text(
                                    'আপনার সিরিয়াল নং: #১  •  বর্তমানে চেম্বারে চলছে: #১  ',
                                    style: TextStyle(fontSize: 12, color: Color(0xFF78350F), fontWeight: FontWeight.w600),
                                  ),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFDC2626),
                                      borderRadius: BorderRadius.circular(6),
                                    ),
                                    child: const Text(
                                      'এখন আপনার ডাক পড়েছে!',
                                      style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        OutlinedButton.icon(
                          onPressed: () => _loadData(),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: const Color(0xFF78350F),
                            side: const BorderSide(color: Color(0xFFD97706)),
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                            minimumSize: Size.zero,
                          ),
                          icon: const Icon(Icons.refresh, size: 14),
                          label: const Text('লাইভ রিফ্রেশ', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        ),
                        const SizedBox(width: 8),
                        ElevatedButton.icon(
                          onPressed: () => Navigator.pushNamed(context, AppRoutes.liveQueue),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF16A34A),
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            minimumSize: Size.zero,
                            elevation: 0,
                          ),
                          icon: const Icon(Icons.videocam, size: 14),
                          label: const Text('ভিডিও কলে যোগ দিন', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 2. Patient Profile Card (Web Replica)
              // ==========================================
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 16),
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.04),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Patient Avatar with Camera badge
                        Stack(
                          children: [
                            Container(
                              width: 64,
                              height: 64,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                border: Border.all(color: const Color(0xFF0284C7), width: 2.5),
                                image: const DecorationImage(
                                  image: NetworkImage('https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'),
                                  fit: BoxFit.cover,
                                ),
                              ),
                            ),
                            Positioned(
                              bottom: 0,
                              right: 0,
                              child: GestureDetector(
                                onTap: _openUploadReportDialog,
                                child: Container(
                                  padding: const EdgeInsets.all(4),
                                  decoration: const BoxDecoration(
                                    color: Color(0xFFE11D48),
                                    shape: BoxShape.circle,
                                  ),
                                  child: const Icon(Icons.camera_alt, color: Colors.white, size: 12),
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  Flexible(
                                    child: Text(
                                      portalProv.activeFamilyMember != null
                                          ? portalProv.activeFamilyMember!.name
                                          : (user?.name ?? 'মো: মাহবুবুর রহমান'),
                                      style: const TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.w900,
                                        color: Color(0xFF0F172A),
                                      ),
                                    ),
                                  ),
                                  const SizedBox(width: 6),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFE0F2FE),
                                      borderRadius: BorderRadius.circular(6),
                                    ),
                                    child: Text(
                                      portalProv.activeFamilyMember != null
                                          ? '${portalProv.activeFamilyMember!.relationship} প্রোফাইল'
                                          : 'প্রধান রোগী একাউন্ট',
                                      style: const TextStyle(
                                        fontSize: 9,
                                        fontWeight: FontWeight.bold,
                                        color: Color(0xFF0369A1),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 3),
                              Text(
                                '${user?.email ?? "patient@mahriva.com"}  •  ${user?.phone ?? "01711223344"}',
                                style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                              ),
                              const SizedBox(height: 2),
                              Row(
                                children: [
                                  const Icon(Icons.location_on, size: 12, color: Color(0xFF94A3B8)),
                                  const SizedBox(width: 3),
                                  Text(
                                    user?.address ?? 'মিরপুর ডিওএইচএস, ঢাকা, বাংলাদেশ',
                                    style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    const Divider(height: 1),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: OutlinedButton.icon(
                            onPressed: () => setState(() => _activeTab = 6),
                            style: OutlinedButton.styleFrom(
                              foregroundColor: const Color(0xFF0F172A),
                              side: const BorderSide(color: Color(0xFFCBD5E1)),
                              padding: const EdgeInsets.symmetric(vertical: 10),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                            ),
                            icon: const Icon(Icons.groups_outlined, size: 16),
                            label: Text(
                              'ফ্যামিলি মেম্বার (${portalProv.familyMembers.length})',
                              style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                            ),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: ElevatedButton.icon(
                            onPressed: _openUploadReportDialog,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFFE11D48),
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 10),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              elevation: 0,
                            ),
                            icon: const Icon(Icons.camera_alt_outlined, size: 16),
                            label: const Text('রিপোর্ট আপলোড', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // ==========================================
              // 3. 5-Column Metrics Row (Web Replica)
              // ==========================================
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                child: Row(
                  children: [
                    _buildMetricCol('মেডিকেল রিপোর্ট', '${portalProv.reports.length}', 0),
                    _buildMetricCol('প্রেসক্রিপশন', '${portalProv.prescriptions.isNotEmpty ? portalProv.prescriptions.length : 1}', 1),
                    _buildMetricCol('সুগার / BP', '${portalProv.healthMetrics.isNotEmpty ? portalProv.healthMetrics.length : 1}', 2),
                    _buildMetricCol('অ্যাপয়েন্টমেন্ট', '${portalProv.appointments.isNotEmpty ? portalProv.appointments.length : 3}', 3),
                    _buildMetricCol('ফ্যামিলি মেম্বার', '${portalProv.familyMembers.isNotEmpty ? portalProv.familyMembers.length : 2}', 6),
                  ],
                ),
              ),

              // ==========================================
              // 4. Tab Navigation Chips (7 Tabs Matching Web)
              // ==========================================
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                child: Row(
                  children: [
                    _buildTabPill(0, 'টেস্ট রিপোর্ট (${portalProv.reports.length})', Icons.assignment_outlined, const Color(0xFFE11D48)),
                    const SizedBox(width: 8),
                    _buildTabPill(1, 'প্রেসক্রিপশন ও অডিও (${portalProv.prescriptions.isNotEmpty ? portalProv.prescriptions.length : 1})', Icons.receipt_long, const Color(0xFF0284C7)),
                    const SizedBox(width: 8),
                    _buildTabPill(2, 'সুগার ও প্রেশার ট্র্যাকার', Icons.favorite_border, const Color(0xFF10B981)),
                    const SizedBox(width: 8),
                    _buildTabPill(3, 'অ্যাপয়েন্টমেন্ট (${portalProv.appointments.isNotEmpty ? portalProv.appointments.length : 3})', Icons.calendar_month, const Color(0xFF8B5CF6)),
                    const SizedBox(width: 8),
                    _buildTabPill(4, 'ওষুধের রুটিন', Icons.medication_outlined, const Color(0xFFF59E0B)),
                    const SizedBox(width: 8),
                    _buildTabPill(5, 'হেলথ পাসপোর্ট ও কিউআর', Icons.qr_code_2, const Color(0xFF0F172A)),
                    const SizedBox(width: 8),
                    _buildTabPill(6, 'ফ্যামিলি মেম্বারস (${portalProv.familyMembers.isNotEmpty ? portalProv.familyMembers.length : 2})', Icons.groups, const Color(0xFF0D9488)),
                  ],
                ),
              ),

              const SizedBox(height: 8),

              // ==========================================
              // 5. Active Tab Body Content
              // ==========================================
              if (_activeTab == 0) ...[
                // TAB 0: TEST REPORTS (Web Screenshot Replica)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Column(
                    children: [
                      // Red Header Banner
                      Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFFF1F2),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: const Color(0xFFFFCCD5)),
                        ),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                        decoration: BoxDecoration(
                                          color: const Color(0xFFE11D48),
                                          borderRadius: BorderRadius.circular(6),
                                        ),
                                        child: const Text('আজীবন স্বাস্থ্য রেকর্ড', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold)),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 6),
                                  const Text(
                                    'মেডিকেল টেস্ট ও ল্যাব রিপোর্ট ভল্ট',
                                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Color(0xFF881337)),
                                  ),
                                  const SizedBox(height: 2),
                                  const Text(
                                    'ফোনের ক্যামেরা দিয়ে কাগজের রিপোর্টের ছবি তুলুন বা PDF আপলোড করুন। যেকোনো সময় ডাক্তারকে দেখাতে পারবেন।',
                                    style: TextStyle(fontSize: 11, color: Color(0xFF9F1239)),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(width: 8),
                            ElevatedButton.icon(
                              onPressed: _openUploadReportDialog,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFFE11D48),
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                elevation: 0,
                              ),
                              icon: const Icon(Icons.camera_alt, size: 14),
                              label: const Text('নতুন রিপোর্ট', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Test Categories Chips
                      SingleChildScrollView(
                        scrollDirection: Axis.horizontal,
                        child: Row(
                          children: [
                            _buildFilterChip('all', 'সব রিপোর্ট (${portalProv.reports.length})'),
                            const SizedBox(width: 6),
                            _buildFilterChip('blood_test', '🩸 রক্ত পরীক্ষা / CBC'),
                            const SizedBox(width: 6),
                            _buildFilterChip('xray', '🩻 এক্স-রে / X-Ray'),
                            const SizedBox(width: 6),
                            _buildFilterChip('ultrasound', '🔬 আল্ট্রাসনোগ্রাম / USG'),
                            const SizedBox(width: 6),
                            _buildFilterChip('ecg', '🫀 ইসিজি / ECG'),
                            const SizedBox(width: 6),
                            _buildFilterChip('urine', '🧪 প্রস্রাব পরীক্ষা'),
                            const SizedBox(width: 6),
                            _buildFilterChip('prescription', '📝 প্রেসক্রিপশন কপি'),
                          ],
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Search bar with voice search
                      VoiceTextField(
                        controller: _reportSearchCtrl,
                        hintText: 'রিপোর্ট বা ল্যাব নাম দিয়ে খুঁজুন... (মাইকে বলুন)',
                        prefixIcon: const Icon(Icons.search, size: 20, color: Color(0xFF94A3B8)),
                        fillColor: Colors.white,
                        onSubmitted: (_) => setState(() {}),
                      ),
                      const SizedBox(height: 16),

                      // Reports list or empty state box
                      if (filteredReports.isEmpty)
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.symmetric(vertical: 36, horizontal: 20),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(18),
                            border: Border.all(color: const Color(0xFFE2E8F0)),
                          ),
                          child: Column(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF1F5F9),
                                  shape: BoxShape.circle,
                                ),
                                child: const Icon(Icons.description_outlined, size: 40, color: Color(0xFF64748B)),
                              ),
                              const SizedBox(height: 14),
                              const Text(
                                'কোনো টেস্ট রিপোর্ট পাওয়া যায়নি',
                                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                              ),
                              const SizedBox(height: 4),
                              const Text(
                                'আপনার প্রেসক্রিপশন বা টেস্ট পেপারের ছবি তুলে এখানে আজীবন ক্লাউডে সংরক্ষণ করুন।',
                                textAlign: TextAlign.center,
                                style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                              ),
                              const SizedBox(height: 16),
                              ElevatedButton.icon(
                                onPressed: _openUploadReportDialog,
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: const Color(0xFFE11D48),
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                  elevation: 0,
                                ),
                                icon: const Icon(Icons.camera_alt, size: 16),
                                label: const Text('📥 প্রথম রিপোর্ট আপলোড করুন', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                              ),
                            ],
                          ),
                        )
                      else
                        ListView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: filteredReports.length,
                          itemBuilder: (ctx, idx) {
                            final r = filteredReports[idx];
                            return _buildReportItem(r, portalProv);
                          },
                        ),
                    ],
                  ),
                ),
              ] else if (_activeTab == 1) ...[
                // TAB 1: PRESCRIPTIONS & AUDIO STREAM
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'ডিজিটাল প্রেসক্রিপশন ও বাংলা অডিও গাইড',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                      ),
                      const SizedBox(height: 10),
                      _buildPrescriptionCard(portalProv),
                    ],
                  ),
                ),
              ] else if (_activeTab == 2) ...[
                // TAB 2: SUGAR & BLOOD PRESSURE VITALS
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'ডায়াবেটিস ও ব্লাড প্রেশার ট্র্যাকার',
                            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                          ),
                          ElevatedButton.icon(
                            onPressed: _openAddMetricDialog,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF10B981),
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              elevation: 0,
                            ),
                            icon: const Icon(Icons.add, size: 16),
                            label: const Text('নতুন মাপ', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      _buildVitalsCards(portalProv),
                    ],
                  ),
                ),
              ] else if (_activeTab == 3) ...[
                // TAB 3: APPOINTMENTS
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'আমার ডাক্তার অ্যাপয়েন্টমেন্টসমূহ',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                      ),
                      const SizedBox(height: 12),
                      _buildAppointmentsList(portalProv),
                    ],
                  ),
                ),
              ] else if (_activeTab == 4) ...[
                // TAB 4: MEDICINE ROUTINE
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: _buildMedicineRoutineTab(),
                ),
              ] else if (_activeTab == 5) ...[
                // TAB 5: HEALTH PASSPORT & EMERGENCY QR
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: _buildHealthPassportTab(portalProv, user),
                ),
              ] else if (_activeTab == 6) ...[
                // TAB 6: FAMILY MEMBERS
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: _buildFamilyMembersTab(portalProv),
                ),
              ],

              const SizedBox(height: 36),

              // ==========================================
              // 6. Floating AI Assistant Banner at Bottom (Web Replica)
              // ==========================================
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 16),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF0F172A), Color(0xFF1E293B), Color(0xFF0284C7)],
                  ),
                  borderRadius: BorderRadius.circular(18),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF0F172A).withOpacity(0.2),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.12),
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(Icons.smart_toy_rounded, color: Colors.cyanAccent, size: 24),
                    ),
                    const SizedBox(width: 12),
                    const Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'এআই হেলথ পরামর্শক',
                            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13),
                          ),
                          Text(
                            'শারীরিক সমস্যা বা লক্ষণ লিখে তাৎক্ষণিক সমাধান নিন',
                            style: TextStyle(color: Colors.white70, fontSize: 11),
                          ),
                        ],
                      ),
                    ),
                    ElevatedButton(
                      onPressed: () => Navigator.pushNamed(context, AppRoutes.aiSymptomChecker),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFE11D48),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                        elevation: 0,
                      ),
                      child: const Text('পরামর্শ নিন →', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 36),
            ],
          ),
        ),
      ),
    );
  }

  // Quick Role Badge for Testing All 5 User Portals
  Widget _buildQuickRoleBadge(String label, bool isActive, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        decoration: BoxDecoration(
          color: isActive ? const Color(0xFF0284C7) : Colors.white.withOpacity(0.1),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: isActive ? Colors.white : Colors.white24),
        ),
        child: Text(
          label,
          style: TextStyle(
            color: Colors.white,
            fontWeight: isActive ? FontWeight.bold : FontWeight.normal,
            fontSize: 11,
          ),
        ),
      ),
    );
  }

  // 5 Metric Columns (Web Replica)
  Widget _buildMetricCol(String label, String count, int tabIndex) {
    return Expanded(
      child: InkWell(
        onTap: () => setState(() => _activeTab = tabIndex),
        borderRadius: BorderRadius.circular(14),
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 2),
          padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 4),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: _activeTab == tabIndex ? const Color(0xFF0284C7) : const Color(0xFFE2E8F0),
              width: _activeTab == tabIndex ? 1.5 : 1,
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.02),
                blurRadius: 4,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                count,
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w900,
                  color: _activeTab == tabIndex ? const Color(0xFF0284C7) : const Color(0xFF0F172A),
                ),
              ),
              const SizedBox(height: 2),
              Text(
                label,
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontSize: 10, color: Color(0xFF64748B), height: 1.1),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Tab Pill Selector
  Widget _buildTabPill(int index, String label, IconData icon, Color color) {
    final isSelected = _activeTab == index;
    return InkWell(
      onTap: () => setState(() => _activeTab = index),
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? color : Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: isSelected ? color : const Color(0xFFCBD5E1)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 16, color: isSelected ? Colors.white : color),
            const SizedBox(width: 6),
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.w600,
                color: isSelected ? Colors.white : const Color(0xFF334155),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Filter Chip for Test Reports
  Widget _buildFilterChip(String value, String label) {
    final isSelected = _selectedReportFilter == value;
    return ChoiceChip(
      label: Text(label, style: TextStyle(fontSize: 11, fontWeight: isSelected ? FontWeight.bold : FontWeight.normal)),
      selected: isSelected,
      selectedColor: const Color(0xFFE11D48),
      labelStyle: TextStyle(color: isSelected ? Colors.white : const Color(0xFF0F172A)),
      backgroundColor: Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: BorderSide(color: isSelected ? const Color(0xFFE11D48) : const Color(0xFFCBD5E1)),
      ),
      onSelected: (_) => setState(() => _selectedReportFilter = value),
    );
  }

  // Report Item Widget
  Widget _buildReportItem(r, PatientPortalProvider portalProv) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: const Color(0xFFFFF1F2),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(Icons.picture_as_pdf_outlined, color: Color(0xFFE11D48), size: 24),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  r.reportTitle,
                  style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: Color(0xFF0F172A)),
                ),
                const SizedBox(height: 2),
                Text(
                  '${r.diagnosticCenter ?? "ডায়াগনস্টিক সেন্টার"}  •  ${r.reportDate}',
                  style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                ),
              ],
            ),
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.red, size: 20),
            onPressed: () => portalProv.deleteReport(r.id),
          ),
        ],
      ),
    );
  }

  // Prescription Card (Full Table Replica)
  Widget _buildPrescriptionCard(PatientPortalProvider portalProv) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: const Color(0xFFE0F2FE),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: const Text('RX-2026-1049', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF0369A1))),
                  ),
                  const SizedBox(height: 6),
                  const Text('ডাঃ থমাস হেনরি (Dr. Thomas Henry)', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: Color(0xFF0F172A))),
                  const Text('কার্ডিওলজি (Cardiology) • MBBS, MD (Cardiology)', style: TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                ],
              ),
              const Icon(Icons.verified, color: Color(0xFF0284C7), size: 24),
            ],
          ),
          const SizedBox(height: 12),
          const Divider(height: 1),
          const SizedBox(height: 12),

          // Audio Guide Button
          Builder(
            builder: (ctx) {
              final isPlaying = portalProv.isPlayingAudio && portalProv.playingPrescriptionId == 1049;
              final isLoading = portalProv.isAudioLoading && portalProv.playingPrescriptionId == 1049;

              return Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: isPlaying ? const Color(0xFFFEF2F2) : const Color(0xFFEFF6FF),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: isPlaying ? const Color(0xFFFECACA) : const Color(0xFFBFDBFE),
                    width: 1.5,
                  ),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: isPlaying ? const Color(0xFFE11D48) : const Color(0xFF0284C7),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(
                        isPlaying ? Icons.graphic_eq_rounded : Icons.volume_up,
                        color: Colors.white,
                        size: 16,
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            isPlaying ? 'ভয়েস অডিও গাইড চলছে...' : 'প্রেসক্রিপশন ভয়েস অডিও গাইড',
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                              color: isPlaying ? const Color(0xFF991B1B) : const Color(0xFF1E3A8A),
                            ),
                          ),
                          Text(
                            isPlaying
                                ? 'স্পষ্ট বাংলায় নিয়ম শুনুন, থামাতে বাটনে চাপুন'
                                : 'ওষুধ খাওয়ার নিয়ম বাংলায় শুনতে প্লে বাটনে চাপুন',
                            style: TextStyle(
                              fontSize: 10,
                              color: isPlaying ? const Color(0xFFDC2626) : const Color(0xFF3B82F6),
                            ),
                          ),
                        ],
                      ),
                    ),
                    ElevatedButton(
                      onPressed: () {
                        const prescriptionText =
                            "রোগীর ডিজিটাল প্রেসক্রিপশন নির্দেশিকা। "
                            "চিকিৎসক: ডাঃ থমাস হেনরি, কার্ডিওলজি বিশেষজ্ঞ। "
                            "রোগ নির্ণয়: মাইল্ড এসেনশিয়াল হাইপারটেনশন এবং প্রাথমিক ডিজলিপিডেমিয়া। "
                            "প্রেসক্রিপশনভুক্ত ওষুধ খাবার নিয়মাবলী: "
                            "এক নম্বর ওষুধ, ট্যাবলেট নেবিতা পাঁচ মিলিগ্রাম, প্রতিদিন সকালে একটি করে ভরা পেটে খাবেন। মোট মেয়াদ ৩০ দিন। "
                            "দুই নম্বর ওষুধ, ট্যাবলেট লিপিকন দশ মিলিগ্রাম, প্রতিদিন রাতে একটি করে খাবেন। মোট মেয়াদ ৩০ দিন। "
                            "তিন নম্বর ওষুধ, ক্যাপসুল ওমেগা থ্রি, প্রতিদিন দুপুরে একটি করে খাবেন। মোট মেয়াদ ৬০ দিন। "
                            "বিশেষ স্বাস্থ্য পরামর্শ: খাবারে বাড়তি কাঁচা লবণ সম্পূর্ণ পরিহার করুন, প্রতিদিন অন্তত ৩০ মিনিট দ্রুত হাঁটুন, এবং অতিরিক্ত তৈলাক্ত খাবার এড়িয়ে চলুন।";

                        portalProv.togglePrescriptionSpeech(
                          prescriptionId: 1049,
                          text: prescriptionText,
                        );
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: isPlaying ? const Color(0xFFE11D48) : const Color(0xFF0284C7),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                        minimumSize: Size.zero,
                      ),
                      child: isLoading
                          ? const SizedBox(
                              width: 14,
                              height: 14,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: Colors.white,
                              ),
                            )
                          : Text(
                              isPlaying ? 'থামান ⏹' : 'প্লে করুন ▶',
                              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold),
                            ),
                    ),
                  ],
                ),
              );
            },
          ),
          const SizedBox(height: 14),

          // Diagnosis
          const Text('রোগ নির্ণয় (Diagnosis):', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF0F172A))),
          const SizedBox(height: 4),
          const Text(
            'মাইল্ড এসেনশিয়াল হাইপারটেনশন (Mild Essential Hypertension), ডিজলিপিডেমিয়া প্রাথমিক পর্যায়।',
            style: TextStyle(fontSize: 12, color: Color(0xFF334155), height: 1.4),
          ),
          const SizedBox(height: 14),

          // Medicines Table
          const Text('প্রেসক্রিপশনভুক্ত ওষুধসমূহ:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF0F172A))),
          const SizedBox(height: 8),
          _buildMedicineRow('Tab. Nebita 5mg (Nebivolol)', '১ + ০ + ০ (সকালে)', '৩০ দিন', 'Square Pharmaceuticals'),
          _buildMedicineRow('Tab. Lipicon 10mg (Atorvastatin)', '০ + ০ + ১ (রাতে)', '৩০ দিন', 'Eskayef Pharmaceuticals'),
          _buildMedicineRow('Cap. Omega-3 1000mg', '০ + ১ + ০ (দুপুরে)', '৬০ দিন', 'Square Pharmaceuticals'),
          const SizedBox(height: 12),

          // Tests & Advice
          const Text('প্রস্তাবিত টেস্ট:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF0F172A))),
          const SizedBox(height: 4),
          const Text('• CBC, Lipid Profile, Serum Creatinine, 12-Lead ECG', style: TextStyle(fontSize: 12, color: Color(0xFF475569))),
          const SizedBox(height: 10),
          const Text('পরামর্শ (Advice):', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF0F172A))),
          const SizedBox(height: 4),
          const Text(
            '১. খাবারে বাড়তি কাঁচা লবণ সম্পূর্ণ পরিহার করুন।\n২. প্রতিদিন অন্তত ৩০ মিনিট নিয়মিত দ্রুত হাঁটবেন।\n৩. তৈলাক্ত ও অতিরিক্ত চর্বিজাতীয় খাবার এড়িয়ে চলুন।',
            style: TextStyle(fontSize: 12, color: Color(0xFF475569), height: 1.4),
          ),
        ],
      ),
    );
  }

  Widget _buildMedicineRow(String name, String dosage, String duration, String company) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(Icons.medication, color: Color(0xFF0284C7), size: 18),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF0F172A))),
                Text('$company • $duration', style: const TextStyle(fontSize: 10, color: Color(0xFF64748B))),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
            decoration: BoxDecoration(
              color: const Color(0xFFDCFCE7),
              borderRadius: BorderRadius.circular(6),
            ),
            child: Text(dosage, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF166534))),
          ),
        ],
      ),
    );
  }

  // Vitals Cards
  Widget _buildVitalsCards(PatientPortalProvider portalProv) {
    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(
                      children: [
                        Icon(Icons.water_drop, color: Colors.blue, size: 18),
                        SizedBox(width: 6),
                        Text('ব্লাড সুগার', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    const Text('৭.৪ mmol/L', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: Color(0xFF0F172A))),
                    const SizedBox(height: 2),
                    const Text('খালি পেটে (Fasting) • স্বাভাবিক', style: TextStyle(fontSize: 11, color: Color(0xFF16A34A))),
                  ],
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(
                      children: [
                        Icon(Icons.favorite, color: Colors.red, size: 18),
                        SizedBox(width: 6),
                        Text('ব্লাড প্রেশার', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    const Text('১৩০ / ৮৫ mmHg', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: Color(0xFF0F172A))),
                    const SizedBox(height: 2),
                    const Text('বিকেলে • নিয়ন্ত্রণে আছে', style: TextStyle(fontSize: 11, color: Color(0xFF16A34A))),
                  ],
                ),
              ),
            ),
          ],
        ),
      ],
    );
  }

  // Appointments List
  Widget _buildAppointmentsList(PatientPortalProvider portalProv) {
    return Column(
      children: [
        _buildAppointmentItem('DOC-2026-8942', 'ডাঃ থমাস হেনরি', 'কার্ডিওলজি (Cardiology)', 'আজ, বিকাল ৫:৩০', 'চলমান (#১)', true),
        _buildAppointmentItem('DOC-2026-6410', 'ডাঃ নাহিদ আফসানা জামান', 'গাইনী ও প্রসূতি বিশেষজ্ঞ', '১৫ অক্টোবর, সন্ধ্যা ৬:০০', 'বুকড', false),
        _buildAppointmentItem('DOC-2026-3199', 'ডাঃ মোঃ সিরাজুল ইসলাম', 'অর্থোপেডিক্স বিশেষজ্ঞ', '২২ সেপ্টেম্বর, সকাল ১১:০০', 'সম্পন্ন', false),
      ],
    );
  }

  Widget _buildAppointmentItem(String code, String doctor, String specialty, String time, String status, bool isLive) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: isLive ? const Color(0xFFF59E0B) : const Color(0xFFE2E8F0)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: isLive ? const Color(0xFFFEF3C7) : const Color(0xFFF1F5F9),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.calendar_today, color: isLive ? const Color(0xFFD97706) : const Color(0xFF64748B), size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Text(code, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF0284C7))),
                    const SizedBox(width: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: isLive ? const Color(0xFFDC2626) : const Color(0xFFE2E8F0),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(status, style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: isLive ? Colors.white : const Color(0xFF334155))),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(doctor, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: Color(0xFF0F172A))),
                Text('$specialty • $time', style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // Medicine Routine Tab
  Widget _buildMedicineRoutineTab() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('দৈনিক ওষুধের রুটিন ও অ্যালার্ম', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: Color(0xFF0F172A))),
          const SizedBox(height: 12),
          _buildRoutineRow('সকাল ৮:০০ (নাস্তার পর)', 'Tab. Nebita 5mg', true),
          _buildRoutineRow('দুপুর ২:০০ (দুপুরের খাবারের পর)', 'Cap. Omega-3 1000mg', false),
          _buildRoutineRow('রাত ৯:০০ (রাতের খাবারের পর)', 'Tab. Lipicon 10mg', false),
        ],
      ),
    );
  }

  Widget _buildRoutineRow(String time, String medicine, bool isDone) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: isDone ? const Color(0xFFF0FDF4) : const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: isDone ? const Color(0xFFBBF7D0) : const Color(0xFFE2E8F0)),
      ),
      child: Row(
        children: [
          Icon(isDone ? Icons.check_circle : Icons.radio_button_unchecked, color: isDone ? const Color(0xFF16A34A) : Colors.grey, size: 20),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(medicine, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A))),
                Text(time, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // Health Passport & Emergency QR
  Widget _buildHealthPassportTab(PatientPortalProvider portalProv, UserModel? user) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        children: [
          const Text(
            'ডিজিটাল ইমার্জেন্সি হেলথ পাসপোর্ট',
            style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF0F172A)),
          ),
          const SizedBox(height: 4),
          const Text(
            'জরুরি এক্সিডেন্ট বা চিকিৎসার ক্ষেত্রে কিউআর কোড স্ক্যান করে তথ্য দেখা যাবে',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
          ),
          const SizedBox(height: 16),
          QrImageView(
            data: 'https://sukhimanush.com/emergency/pass/${user?.id ?? 2}',
            version: QrVersions.auto,
            size: 160,
          ),
          const SizedBox(height: 14),
          Text(user?.name ?? 'মো: মাহবুবুর রহমান', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
          const Text('রক্তের গ্রুপ: B+ • এলার্জি: পেনিসিলিন এলার্জি', style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold, fontSize: 12)),
          const SizedBox(height: 4),
          Text('জরুরি যোগাযোগ: ${user?.phone ?? "01711223344"}', style: const TextStyle(fontSize: 12, color: Color(0xFF475569))),
        ],
      ),
    );
  }

  // Family Members Tab
  Widget _buildFamilyMembersTab(PatientPortalProvider portalProv) {
    return Column(
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text('যুক্ত থাকা ফ্যামিলি সদস্যগণ', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 15)),
            ElevatedButton.icon(
              onPressed: _openAddFamilyDialog,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF0284C7),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
              ),
              icon: const Icon(Icons.add, size: 14),
              label: const Text('নতুন সদস্য', style: TextStyle(fontSize: 11)),
            ),
          ],
        ),
        const SizedBox(height: 12),
        _buildFamilyCard('কামাল হোসেন (বাবা)', 'Father', '৬২ বছর', 'B+', 'ডায়াবেটিস ও উচ্চ রক্তচাপ', portalProv),
        _buildFamilyCard('সালমা বেগম (মা)', 'Mother', '৫৫ বছর', 'O+', 'হাঁটুর বাত ও গ্যাস্ট্রিক', portalProv),
      ],
    );
  }

  Widget _buildFamilyCard(String name, String rel, String age, String blood, String notes, PatientPortalProvider prov) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: const Color(0xFFE0F2FE),
            child: const Icon(Icons.person, color: Color(0xFF0284C7)),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A))),
                    const SizedBox(width: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(rel, style: const TextStyle(fontSize: 9, color: Color(0xFF475569))),
                    ),
                  ],
                ),
                Text('$age • রক্তের গ্রুপ: $blood', style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                Text(notes, style: const TextStyle(fontSize: 11, color: Color(0xFF0284C7))),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
