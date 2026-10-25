import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/routes/app_routes.dart';
import '../../providers/auth_provider.dart';
import '../../providers/agent_provider.dart';
import '../../providers/doctor_provider.dart';

class AgentDashboardScreen extends StatefulWidget {
  const AgentDashboardScreen({super.key});

  @override
  State<AgentDashboardScreen> createState() => _AgentDashboardScreenState();
}

class _AgentDashboardScreenState extends State<AgentDashboardScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final authProv = Provider.of<AuthProvider>(context, listen: false);
      if (authProv.currentUser == null || !authProv.currentUser!.isAgent) {
        await authProv.quickDemoLogin('agent');
      }
      if (mounted) {
        final agentProv = Provider.of<AgentProvider>(context, listen: false);
        final docProv = Provider.of<DoctorProvider>(context, listen: false);
        final agentId = authProv.currentUser?.id ?? 1;
        agentProv.fetchDashboard(agentId);
        docProv.fetchDoctors();
      }
    });
  }

  Widget _buildQuickRoleBadge(String label, bool isActive, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        decoration: BoxDecoration(
          color: isActive ? const Color(0xFF059669) : Colors.white.withOpacity(0.12),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: isActive ? Colors.white : Colors.white24,
            width: isActive ? 1.5 : 1,
          ),
        ),
        child: Text(
          label,
          style: TextStyle(
            color: Colors.white,
            fontSize: 11,
            fontWeight: isActive ? FontWeight.bold : FontWeight.normal,
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final authProv = Provider.of<AuthProvider>(context);
    final agentProv = Provider.of<AgentProvider>(context);
    final agent = agentProv.agent;
    final stats = agentProv.stats;
    final agentId = authProv.currentUser?.id ?? 1;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: const Color(0xFF059669).withOpacity(0.12),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.storefront_outlined, color: Color(0xFF059669), size: 20),
            ),
            const SizedBox(width: 10),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('গ্রামীণ হেলথ পয়েন্ট', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                Text('Rural Digital Pharmacy Portal', style: TextStyle(fontSize: 10, color: AppColors.textSecondary)),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'রিফ্রেশ',
            onPressed: () {
              agentProv.fetchDashboard(agentId);
            },
          ),
          IconButton(
            icon: const Icon(Icons.logout, color: Colors.redAccent),
            tooltip: 'লগআউট',
            onPressed: () async {
              await authProv.logout();
              if (mounted) {
                Navigator.pushReplacementNamed(context, AppRoutes.landing);
              }
            },
          ),
        ],
      ),
      body: agentProv.isLoading && agent == null
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                await agentProv.fetchDashboard(agentId);
              },
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Quick Multi-Role Switcher Demo Bar
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
                            _buildQuickRoleBadge('🧑 রোগী', false, () async {
                              await authProv.quickDemoLogin('patient');
                              if (context.mounted) Navigator.pushNamed(context, AppRoutes.patientDashboard);
                            }),
                            const SizedBox(width: 6),
                            _buildQuickRoleBadge('🩺 ডাক্তার', false, () async {
                              await authProv.quickDemoLogin('doctor');
                              if (context.mounted) Navigator.pushNamed(context, AppRoutes.doctorDashboard);
                            }),
                            const SizedBox(width: 6),
                            _buildQuickRoleBadge('👥 স্টাফ', false, () async {
                              await authProv.quickDemoLogin('staff');
                              if (context.mounted) Navigator.pushNamed(context, AppRoutes.staffDashboard);
                            }),
                            const SizedBox(width: 6),
                            _buildQuickRoleBadge('🏪 এজেন্ট', true, () {}),
                            const SizedBox(width: 6),
                            _buildQuickRoleBadge('🛡️ অ্যাডমিন', false, () async {
                              await authProv.quickDemoLogin('admin');
                              if (context.mounted) Navigator.pushNamed(context, AppRoutes.adminDashboard);
                            }),
                          ],
                        ),
                      ),
                    ),

                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Top Emerald Pharmacy Banner
                          _buildAgentBanner(agent),
                          const SizedBox(height: 16),

                          // 3 KPI Cards
                          _buildKpiCards(stats, agent),
                          const SizedBox(height: 16),

                          // Primary Action: Book New Patient
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton.icon(
                              onPressed: () => _openBookingDialog(context, agentProv, agentId),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF059669),
                                foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        icon: const Icon(Icons.add_circle_outline),
                        label: const Text(
                          'নতুন রোগী বুকিং দিন (Book New Patient)',
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Recent Bookings Ledger
                    _buildRecentBookingsLedger(agentProv),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildAgentBanner(Map<String, dynamic>? agent) {
    final pharmacy = agent?['pharmacy_name'] ?? 'গ্রামীণ ডিজিটাল স্বাস্থ্য পয়েন্ট';
    final name = agent?['name'] ?? 'ফার্মাসিস্ট';
    final balance = agent?['commission_balance'] ?? '0.00';

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF065F46), Color(0xFF047857), Color(0xFF059669)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF059669).withOpacity(0.3),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.amber.shade400,
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Text(
              '🏪 ডিজিটাল স্বাস্থ্য এজেন্ট',
              style: TextStyle(color: Colors.black87, fontWeight: FontWeight.bold, fontSize: 11),
            ),
          ),
          const SizedBox(height: 10),
          Text(
            pharmacy,
            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18),
          ),
          Text('পরিচালক: $name', style: const TextStyle(color: Colors.white70, fontSize: 12)),
          const Divider(color: Colors.white24, height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('কমিশন ব্যালান্স:', style: TextStyle(color: Colors.white70, fontSize: 13)),
              Text(
                '৳$balance',
                style: const TextStyle(color: Colors.amberAccent, fontWeight: FontWeight.w900, fontSize: 22),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildKpiCards(Map<String, dynamic> stats, Map<String, dynamic>? agent) {
    final todayCount = stats['today_count'] ?? 0;
    final todayComm = stats['today_commission'] ?? '0.00';
    final totalComm = agent?['total_commission_earned'] ?? '0.00';

    return Row(
      children: [
        Expanded(
          child: _kpiBox('আজকের বুকিং', '$todayCount জন', Colors.green, Icons.calendar_today),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: _kpiBox('আজকের কমিশন', '৳$todayComm', Colors.amber.shade800, Icons.monetization_on),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: _kpiBox('মোট কমিশন', '৳$totalComm', Colors.blue, Icons.account_balance_wallet),
        ),
      ],
    );
  }

  Widget _kpiBox(String title, String val, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 16, color: color),
          const SizedBox(height: 6),
          Text(val, style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: color)),
          const SizedBox(height: 2),
          Text(title, style: const TextStyle(fontSize: 10, color: AppColors.textSecondary)),
        ],
      ),
    );
  }

  Widget _buildRecentBookingsLedger(AgentProvider agentProv) {
    final list = agentProv.recentBookings;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'রোগী বুকিং খাতা (Patient Ledger)',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Color(0xFF1E293B)),
            ),
            Text('মোট: ${list.length} টি', style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
          ],
        ),
        const SizedBox(height: 10),
        if (list.isEmpty)
          Container(
            padding: const EdgeInsets.all(28),
            width: double.infinity,
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: const Center(
              child: Text(
                'এখনো কোনো রোগীর বুকিং করা হয়নি।\nউপরে ক্লিক করে নতুন রোগীর বুকিং দিন।',
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.grey, fontSize: 13),
              ),
            ),
          )
        else
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: list.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (context, i) {
              final b = list[i];
              final patientName = b['patient_name'] ?? 'রোগী';
              final phone = b['patient_phone'] ?? '';
              final doctorName = b['doctor']?['name'] ?? 'ডাক্তার';
              final serial = b['serial_number'] ?? (i + 1);
              final comm = b['agent_commission'] ?? 40.0;
              final date = b['appointment_date'] != null ? b['appointment_date'].toString().split('T').first : '';

              return Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey.shade200),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 36,
                      height: 36,
                      decoration: BoxDecoration(
                        color: const Color(0xFF059669).withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      alignment: Alignment.center,
                      child: Text('#$serial', style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF059669))),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(patientName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                          Text('ডাক্তার: $doctorName', style: const TextStyle(fontSize: 11, color: AppColors.primary)),
                          Text('তারিখ: $date • ফোন: $phone', style: const TextStyle(fontSize: 10, color: AppColors.textSecondary)),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.amber.shade50,
                        borderRadius: BorderRadius.circular(6),
                        border: Border.all(color: Colors.amber.shade200),
                      ),
                      child: Text(
                        '+৳$comm',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Colors.amber.shade900),
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
      ],
    );
  }

  void _openBookingDialog(BuildContext context, AgentProvider agentProv, dynamic agentId) {
    final docProv = Provider.of<DoctorProvider>(context, listen: false);
    final doctors = docProv.doctors;
    int? selectedDoctorId = doctors.isNotEmpty ? doctors.first.id : null;
    final nameCtrl = TextEditingController();
    final phoneCtrl = TextEditingController();
    final reasonCtrl = TextEditingController();
    String consultType = 'in_chamber';
    DateTime selectedDate = DateTime.now();

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setDialogState) {
          return AlertDialog(
            title: const Text('নতুন রোগী বুকিং (Agent Booking)', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            content: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('ডাক্তার নির্বাচন করুন *', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 4),
                  DropdownButtonFormField<int>(
                    value: selectedDoctorId,
                    isExpanded: true,
                    items: doctors.map((d) {
                      return DropdownMenuItem<int>(
                        value: d.id,
                        child: Text('${d.name} (${d.specialization})', style: const TextStyle(fontSize: 12), overflow: TextOverflow.ellipsis),
                      );
                    }).toList(),
                    onChanged: (val) => setDialogState(() => selectedDoctorId = val),
                    decoration: const InputDecoration(contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 8)),
                  ),
                  const SizedBox(height: 10),
                  TextField(
                    controller: nameCtrl,
                    decoration: const InputDecoration(labelText: 'রোগীর নাম *', hintText: 'যেমন: রফিক উদ্দিন'),
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: phoneCtrl,
                    keyboardType: TextInputType.phone,
                    decoration: const InputDecoration(labelText: 'মোবাইল নম্বর *', hintText: '017XXXXXXXX'),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      const Text('পদ্ধতি: ', style: TextStyle(fontSize: 12)),
                      Radio<String>(
                        value: 'in_chamber',
                        groupValue: consultType,
                        onChanged: (v) => setDialogState(() => consultType = v!),
                      ),
                      const Text('চেম্বার', style: TextStyle(fontSize: 12)),
                      Radio<String>(
                        value: 'online_video',
                        groupValue: consultType,
                        onChanged: (v) => setDialogState(() => consultType = v!),
                      ),
                      const Text('ভিডিও', style: TextStyle(fontSize: 12)),
                    ],
                  ),
                  TextField(
                    controller: reasonCtrl,
                    decoration: const InputDecoration(labelText: 'সমস্যার বিবরণ', hintText: 'যেমন: গ্যাস্ট্রিক সমস্যা'),
                  ),
                ],
              ),
            ),
            actions: [
              TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('বাতিল')),
              ElevatedButton(
                onPressed: () async {
                  if (selectedDoctorId == null || nameCtrl.text.trim().isEmpty || phoneCtrl.text.trim().isEmpty) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('ডাক্তার, রোগীর নাম ও মোবাইল নম্বর পূরণ করুন!')),
                    );
                    return;
                  }

                  final dateStr = "${selectedDate.year}-${selectedDate.month.toString().padLeft(2, '0')}-${selectedDate.day.toString().padLeft(2, '0')}";

                  Navigator.pop(ctx);
                  bool ok = await agentProv.createBooking(agentId, {
                    'doctor_id': selectedDoctorId,
                    'patient_name': nameCtrl.text.trim(),
                    'patient_phone': phoneCtrl.text.trim(),
                    'appointment_date': dateStr,
                    'consultation_type': consultType,
                    'reason_for_visit': reasonCtrl.text.trim(),
                  });

                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text(ok ? 'রোগীর বুকিং সফল হয়েছে! +৳৪০ কমিশন যোগ হয়েছে।' : (agentProv.errorMessage ?? 'বুকিং ত্রুটি।')),
                        backgroundColor: ok ? Colors.green : Colors.red,
                      ),
                    );
                  }
                },
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF059669)),
                child: const Text('বুকিং সম্পন্ন করুন'),
              ),
            ],
          );
        },
      ),
    );
  }
}
