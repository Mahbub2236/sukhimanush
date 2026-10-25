import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/emergency_provider.dart';
import '../../widgets/voice_text_field.dart';

class BloodSosScreen extends StatefulWidget {
  const BloodSosScreen({super.key});

  @override
  State<BloodSosScreen> createState() => _BloodSosScreenState();
}

class _BloodSosScreenState extends State<BloodSosScreen> {
  final List<String> _groups = ['All', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
  String _selectedGroup = 'All';
  final TextEditingController _districtCtrl = TextEditingController();
  int _activeTab = 0; // 0: Requests, 1: Donors

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
    });
  }

  void _loadData() {
    Provider.of<EmergencyProvider>(context, listen: false).fetchBloodData(
      bloodGroup: _selectedGroup == 'All' ? null : _selectedGroup,
      district: _districtCtrl.text.trim().isNotEmpty ? _districtCtrl.text.trim() : null,
    );
  }

  @override
  void dispose() {
    _districtCtrl.dispose();
    super.dispose();
  }

  Future<void> _makeCall(String phone) async {
    final uri = Uri.parse('tel:$phone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  // Dialog: Post Blood Request
  void _openCreateRequestDialog() {
    final nameCtrl = TextEditingController();
    final hospitalCtrl = TextEditingController();
    final districtCtrl = TextEditingController(text: 'Dhaka');
    final contactNameCtrl = TextEditingController();
    final phoneCtrl = TextEditingController();
    final unitsCtrl = TextEditingController(text: '1');
    final reasonCtrl = TextEditingController();
    String group = 'O+';
    String component = 'whole_blood';
    String urgency = 'critical_immediate';

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
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'জরুরি রক্তের আবেদন করুন',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF991B1B)),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close),
                      onPressed: () => Navigator.pop(ctx),
                    ),
                  ],
                ),
                const SizedBox(height: 14),

                VoiceTextField(
                  controller: nameCtrl,
                  labelText: 'রোগীর পূর্ণ নাম *',
                  hintText: 'রোগীর নাম লিখুন...',
                ),
                const SizedBox(height: 10),

                Row(
                  children: [
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: group,
                        decoration: const InputDecoration(labelText: 'রক্তের গ্রুপ *'),
                        items: ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']
                            .map((g) => DropdownMenuItem(value: g, child: Text(g)))
                            .toList(),
                        onChanged: (val) => setModalState(() => group = val!),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: component,
                        decoration: const InputDecoration(labelText: 'উপাদান *'),
                        items: const [
                          DropdownMenuItem(value: 'whole_blood', child: Text('সম্পূর্ণ রক্ত')),
                          DropdownMenuItem(value: 'platelets', child: Text('প্লাটিলেট')),
                          DropdownMenuItem(value: 'plasma', child: Text('প্লাজমা')),
                        ],
                        onChanged: (val) => setModalState(() => component = val!),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),

                Row(
                  children: [
                    Expanded(
                      flex: 2,
                      child: VoiceTextField(
                        controller: hospitalCtrl,
                        labelText: 'হাসপাতালের নাম ও ঠিকানা *',
                        hintText: 'যেমন: ঢাকা মেডিকেল',
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: VoiceTextField(
                        controller: unitsCtrl,
                        keyboardType: TextInputType.number,
                        labelText: 'ব্যাগ সংখ্যা *',
                        hintText: '১',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),

                Row(
                  children: [
                    Expanded(
                      child: VoiceTextField(
                        controller: districtCtrl,
                        labelText: 'জেলা *',
                        hintText: 'ঢাকা',
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: urgency,
                        decoration: const InputDecoration(labelText: 'জরুরিতা *'),
                        items: const [
                          DropdownMenuItem(value: 'critical_immediate', child: Text('তাত্ক্ষণিক (Emergency)')),
                          DropdownMenuItem(value: 'within_12h', child: Text('১২ ঘণ্টার মধ্যে')),
                          DropdownMenuItem(value: 'within_24h', child: Text('২৪ ঘণ্টার মধ্যে')),
                        ],
                        onChanged: (val) => setModalState(() => urgency = val!),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),

                Row(
                  children: [
                    Expanded(
                      child: VoiceTextField(
                        controller: contactNameCtrl,
                        labelText: 'স্বজনের নাম *',
                        hintText: 'নাম লিখুন...',
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: VoiceTextField(
                        controller: phoneCtrl,
                        keyboardType: TextInputType.phone,
                        labelText: 'যোগাযোগের মোবাইল *',
                        hintText: '017...',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),

                VoiceTextField(
                  controller: reasonCtrl,
                  labelText: 'রোগের বিবরণ / অপারেশন নোট',
                  hintText: 'রোগের বিস্তারিত বলুন...',
                ),
                const SizedBox(height: 18),

                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFDC2626),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                    onPressed: () async {
                      if (nameCtrl.text.isEmpty || hospitalCtrl.text.isEmpty || phoneCtrl.text.isEmpty) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('রোগীর নাম, হাসপাতাল ও মোবাইল নম্বর দিন।')),
                        );
                        return;
                      }

                      final prov = Provider.of<EmergencyProvider>(context, listen: false);
                      bool ok = await prov.createBloodRequest({
                        'patient_name': nameCtrl.text.trim(),
                        'blood_group': group,
                        'component': component,
                        'units_needed': int.tryParse(unitsCtrl.text) ?? 1,
                        'hospital_name': hospitalCtrl.text.trim(),
                        'district': districtCtrl.text.trim(),
                        'contact_name': contactNameCtrl.text.trim().isNotEmpty ? contactNameCtrl.text.trim() : 'স্বজন',
                        'contact_phone': phoneCtrl.text.trim(),
                        'urgency': urgency,
                        'reason': reasonCtrl.text.trim(),
                      });

                      if (mounted) {
                        Navigator.pop(ctx);
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(content: Text(ok ? 'রক্তের আবেদন পোস্ট সম্পন্ন হয়েছে!' : 'পোস্ট ব্যর্থ হয়েছে। পুনরায় চেষ্টা করুন।')),
                        );
                      }
                    },
                    child: const Text('জরুরি পোস্ট সম্পন্ন করুন', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Dialog: Register Donor
  void _openRegisterDonorDialog() {
    final nameCtrl = TextEditingController();
    final districtCtrl = TextEditingController(text: 'Dhaka');
    final phoneCtrl = TextEditingController();
    String group = 'O+';

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
                    'রক্তদাতা হিসেবে নাম লেখান',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close),
                    onPressed: () => Navigator.pop(ctx),
                  ),
                ],
              ),
              const SizedBox(height: 14),

              VoiceTextField(
                controller: nameCtrl,
                labelText: 'আপনার নাম *',
                hintText: 'পূর্ণ নাম লিখুন...',
              ),
              const SizedBox(height: 10),

              Row(
                children: [
                  Expanded(
                    child: DropdownButtonFormField<String>(
                        value: group,
                        decoration: const InputDecoration(labelText: 'রক্তের গ্রুপ *'),
                        items: ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']
                            .map((g) => DropdownMenuItem(value: g, child: Text(g)))
                            .toList(),
                        onChanged: (val) => setModalState(() => group = val!),
                      ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: VoiceTextField(
                      controller: districtCtrl,
                      labelText: 'জেলা *',
                      hintText: 'যেমন: ঢাকা',
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),

              VoiceTextField(
                controller: phoneCtrl,
                keyboardType: TextInputType.phone,
                labelText: 'মোবাইল নম্বর *',
                hintText: 'যেমন: 017...',
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
                    if (nameCtrl.text.isEmpty || phoneCtrl.text.isEmpty) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('নাম ও মোবাইল নম্বর দিন।')),
                      );
                      return;
                    }

                    final prov = Provider.of<EmergencyProvider>(context, listen: false);
                    bool ok = await prov.registerDonor({
                      'name': nameCtrl.text.trim(),
                      'blood_group': group,
                      'district': districtCtrl.text.trim(),
                      'phone': phoneCtrl.text.trim(),
                    });

                    if (mounted) {
                      Navigator.pop(ctx);
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text(ok ? 'অভিনন্দন! আপনি সফলভাবে রক্তদাতা হিসেবে যুক্ত হয়েছেন।' : 'নিবন্ধন ব্যর্থ হয়েছে।')),
                      );
                    }
                  },
                  child: const Text('রক্তদাতা তালিকায় যোগ দিন', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
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
    final prov = Provider.of<EmergencyProvider>(context);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('ব্লাড ও প্লাটিলেট SOS'),
        backgroundColor: const Color(0xFF991B1B),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: RefreshIndicator(
        onRefresh: () async => _loadData(),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            children: [
              // 1. Web Header Banner (Gradient Deep Red)
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(20, 24, 20, 28),
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFF991B1B), Color(0xFFB91C1C), Color(0xFFDC2626)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Column(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.2),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: const Text(
                        '🩸 জীবন বাঁচাতে রক্তদান',
                        style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
                      ),
                    ),
                    const SizedBox(height: 10),
                    const Text(
                      'জরুরি ব্লাড ও প্লাটিলেট SOS নেটওয়ার্ক',
                      textAlign: TextAlign.center,
                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white),
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'জরুরি রক্তের প্রয়োজনে তাৎক্ষণিক পোস্ট করুন অথবা রক্তদাতা হিসেবে যুক্ত হয়ে জীবন বাঁচান।',
                      textAlign: TextAlign.center,
                      style: TextStyle(fontSize: 12, color: Colors.white70),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.amber.shade400,
                              foregroundColor: const Color(0xFF0F172A),
                              padding: const EdgeInsets.symmetric(vertical: 11),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            onPressed: _openCreateRequestDialog,
                            icon: const Icon(Icons.campaign, size: 18),
                            label: const Text('রক্তের আবেদন করুন', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 12)),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: OutlinedButton.icon(
                            style: OutlinedButton.styleFrom(
                              foregroundColor: Colors.white,
                              side: const BorderSide(color: Colors.white),
                              padding: const EdgeInsets.symmetric(vertical: 11),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            onPressed: _openRegisterDonorDialog,
                            icon: const Icon(Icons.person_add, size: 18),
                            label: const Text('রক্তদাতা নিবন্ধন', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // 2. Filter & District Search
              Container(
                margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  children: [
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: _groups.map((grp) {
                          final isSelected = _selectedGroup == grp;
                          return Padding(
                            padding: const EdgeInsets.only(right: 6),
                            child: ChoiceChip(
                              label: Text(grp),
                              selected: isSelected,
                              selectedColor: const Color(0xFFDC2626),
                              labelStyle: TextStyle(
                                color: isSelected ? Colors.white : const Color(0xFF0F172A),
                                fontWeight: FontWeight.bold,
                                fontSize: 12,
                              ),
                              onSelected: (sel) {
                                if (sel) {
                                  setState(() => _selectedGroup = grp);
                                  _loadData();
                                }
                              },
                            ),
                          );
                        }).toList(),
                      ),
                    ),
                    const SizedBox(height: 8),
                    VoiceTextField(
                      controller: _districtCtrl,
                      hintText: 'জেলা দিয়ে খুঁজুন (যেমন: ঢাকা, সিলেট)...',
                      prefixIcon: const Icon(Icons.location_on, color: Color(0xFFDC2626), size: 18),
                      onSubmitted: (_) => _loadData(),
                    ),
                  ],
                ),
              ),

              // 3. Tab Switcher
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 14, 16, 8),
                child: Row(
                  children: [
                    Expanded(
                      child: GestureDetector(
                        onTap: () => setState(() => _activeTab = 0),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 10),
                          decoration: BoxDecoration(
                            color: _activeTab == 0 ? const Color(0xFFDC2626) : Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: _activeTab == 0 ? const Color(0xFFDC2626) : const Color(0xFFCBD5E1)),
                          ),
                          child: Center(
                            child: Text(
                              'জরুরি পোস্ট (${prov.bloodRequests.length})',
                              style: TextStyle(
                                color: _activeTab == 0 ? Colors.white : const Color(0xFF0F172A),
                                fontWeight: FontWeight.bold,
                                fontSize: 13,
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: GestureDetector(
                        onTap: () => setState(() => _activeTab = 1),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 10),
                          decoration: BoxDecoration(
                            color: _activeTab == 1 ? const Color(0xFF0F172A) : Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: _activeTab == 1 ? const Color(0xFF0F172A) : const Color(0xFFCBD5E1)),
                          ),
                          child: Center(
                            child: Text(
                              'স্বেচ্ছাসেবী রক্তদাতা (${prov.bloodDonors.length})',
                              style: TextStyle(
                                color: _activeTab == 1 ? Colors.white : const Color(0xFF0F172A),
                                fontWeight: FontWeight.bold,
                                fontSize: 13,
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // 4. Content List
              if (prov.isLoadingBlood)
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 40),
                  child: CircularProgressIndicator(color: Color(0xFFDC2626)),
                )
              else if (_activeTab == 0) ...[
                // TAB 0: REQUESTS
                if (prov.bloodRequests.isEmpty)
                  Container(
                    margin: const EdgeInsets.all(20),
                    padding: const EdgeInsets.all(30),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                    ),
                    child: const Column(
                      children: [
                        Icon(Icons.water_drop_outlined, size: 48, color: Color(0xFF94A3B8)),
                        SizedBox(height: 8),
                        Text(
                          'কোনো সক্রিয় জরুরি রক্তের আবেদন নেই',
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: Color(0xFF0F172A)),
                        ),
                        SizedBox(height: 4),
                        Text(
                          'রক্তের প্রয়োজন হলে উপরের বাটনে ক্লিক করে পোস্ট করুন।',
                          style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                        ),
                      ],
                    ),
                  )
                else
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    itemCount: prov.bloodRequests.length,
                    itemBuilder: (context, idx) {
                      final req = prov.bloodRequests[idx];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.03),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Row(
                          children: [
                            // Blood Group Badge
                            Container(
                              width: 54,
                              height: 54,
                              decoration: BoxDecoration(
                                color: const Color(0xFFFFF1F2),
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(color: const Color(0xFFDC2626), width: 1.5),
                              ),
                              child: Center(
                                child: Text(
                                  req.bloodGroup,
                                  style: const TextStyle(
                                    color: Color(0xFFDC2626),
                                    fontSize: 18,
                                    fontWeight: FontWeight.w900,
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    req.patientName,
                                    style: const TextStyle(
                                      fontSize: 15,
                                      fontWeight: FontWeight.w800,
                                      color: Color(0xFF0F172A),
                                    ),
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    '${req.hospitalName} • ${req.district}',
                                    style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                  ),
                                  const SizedBox(height: 4),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFFEF2F2),
                                      borderRadius: BorderRadius.circular(6),
                                    ),
                                    child: Text(
                                      '${req.unitsNeeded} ব্যাগ প্রয়োজন • ${req.urgency.replaceAll('_', ' ')}',
                                      style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFFDC2626)),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.phone, color: Colors.green, size: 24),
                              onPressed: () => _makeCall(req.contactPhone),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
              ] else ...[
                // TAB 1: DONORS
                if (prov.bloodDonors.isEmpty)
                  Container(
                    margin: const EdgeInsets.all(20),
                    padding: const EdgeInsets.all(30),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                    ),
                    child: const Center(
                      child: Text(
                        'কোনো রক্তদাতা পাওয়া যায়নি।',
                        style: TextStyle(fontSize: 13, color: Color(0xFF64748B)),
                      ),
                    ),
                  )
                else
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    itemCount: prov.bloodDonors.length,
                    itemBuilder: (context, idx) {
                      final donor = prov.bloodDonors[idx];
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
                            CircleAvatar(
                              radius: 24,
                              backgroundColor: const Color(0xFFEFF6FF),
                              child: Text(
                                donor.bloodGroup,
                                style: const TextStyle(
                                  color: Color(0xFF1D4ED8),
                                  fontWeight: FontWeight.w900,
                                  fontSize: 15,
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Text(
                                        donor.name,
                                        style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w800,
                                          color: Color(0xFF0F172A),
                                        ),
                                      ),
                                      const SizedBox(width: 6),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1),
                                        decoration: BoxDecoration(
                                          color: const Color(0xFFDCFCE7),
                                          borderRadius: BorderRadius.circular(10),
                                        ),
                                        child: const Text('রক্তদাতা', style: TextStyle(fontSize: 9, color: Color(0xFF15803D), fontWeight: FontWeight.bold)),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    '${donor.district} • মোট দান: ${donor.totalDonations} বার',
                                    style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                  ),
                                ],
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.phone, color: Colors.green, size: 24),
                              onPressed: () => _makeCall(donor.phone),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
              ],
              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }
}
