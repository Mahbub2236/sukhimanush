import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/routes/app_routes.dart';
import '../../providers/auth_provider.dart';
import '../../providers/staff_provider.dart';

class StaffDashboardScreen extends StatefulWidget {
  const StaffDashboardScreen({super.key});

  @override
  State<StaffDashboardScreen> createState() => _StaffDashboardScreenState();
}

class _StaffDashboardScreenState extends State<StaffDashboardScreen> {
  String _selectedFilter = 'all';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final authProv = Provider.of<AuthProvider>(context, listen: false);
      if (authProv.currentUser == null || !authProv.currentUser!.isStaff) {
        await authProv.quickDemoLogin('staff');
      }
      if (mounted) {
        final staffProv = Provider.of<StaffProvider>(context, listen: false);
        final assistantId = authProv.currentUser?.assistantId ?? authProv.currentUser?.id ?? 1;
        staffProv.fetchDashboard(assistantId);
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
          color: isActive ? Colors.teal : Colors.white.withOpacity(0.12),
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
    final staffProv = Provider.of<StaffProvider>(context);
    final assistant = staffProv.assistant;
    final doctor = staffProv.doctor;
    final stats = staffProv.stats;
    final assistantId = authProv.currentUser?.assistantId ?? authProv.currentUser?.id ?? 1;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: Colors.teal.withOpacity(0.12),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.people_alt_outlined, color: Colors.teal, size: 20),
            ),
            const SizedBox(width: 10),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('সহকারী / কম্পাউন্ডার পোর্টাল', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                Text('Chamber Queue Counter Management', style: TextStyle(fontSize: 10, color: AppColors.textSecondary)),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'রিফ্রেশ করুন',
            onPressed: () {
              staffProv.fetchDashboard(assistantId);
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
      body: staffProv.isLoading && assistant == null
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                await staffProv.fetchDashboard(assistantId);
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
                            _buildQuickRoleBadge('👥 স্টাফ', true, () {}),
                            const SizedBox(width: 6),
                            _buildQuickRoleBadge('🏪 এজেন্ট', false, () async {
                              await authProv.quickDemoLogin('agent');
                              if (context.mounted) Navigator.pushNamed(context, AppRoutes.agentDashboard);
                            }),
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
                          // Header Banner
                          _buildStaffHeader(assistant, doctor),
                          const SizedBox(height: 14),

                          // Live Serving Token Alert Banner
                          if (stats['called_patient_name'] != null || stats['current_serving_serial'] != null)
                            _buildServingBanner(stats),
                          const SizedBox(height: 14),

                          // 4 KPI Summary Cards
                          _buildKpiCards(stats),
                          const SizedBox(height: 16),

                          // Quick Actions (Call Next & Walk-in Registration)
                          _buildActionBar(staffProv, assistantId),
                          const SizedBox(height: 20),

                          // Appointments List Header & Filter
                          _buildAppointmentsSection(staffProv, assistantId),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildStaffHeader(Map<String, dynamic>? assistant, Map<String, dynamic>? doctor) {
    final staffName = assistant?['name'] ?? 'সহকারী';
    final docName = doctor?['name'] ?? 'ডাক্তার';
    final docSpecialty = doctor?['specialization'] ?? 'বিশেষজ্ঞ';
    final docChamber = doctor?['chamber_address'] ?? 'চেম্বার কাউন্টার';

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 8, offset: const Offset(0, 2)),
        ],
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 26,
            backgroundColor: Colors.teal.shade50,
            child: const Icon(Icons.person_pin, size: 32, color: Colors.teal),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Flexible(
                      child: Text(
                        staffName,
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF1E293B)),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: Colors.teal.shade50,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: const Text('স্টাফ', style: TextStyle(color: Colors.teal, fontSize: 10, fontWeight: FontWeight.bold)),
                    ),
                  ],
                ),
                const SizedBox(height: 2),
                Text('ডাক্তার: $docName ($docSpecialty)', style: const TextStyle(fontSize: 12, color: AppColors.primary, fontWeight: FontWeight.w600)),
                Text('চেম্বার: $docChamber', style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildServingBanner(Map<String, dynamic> stats) {
    final serial = stats['current_serving_serial'] ?? 1;
    final patientName = stats['called_patient_name'] ?? 'চলমান রোগী';

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.amber.shade50,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.amber.shade300),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.red.shade600,
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              'এখন ডাক পড়েছে: #$serial',
              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              patientName,
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF1E293B)),
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildKpiCards(Map<String, dynamic> stats) {
    return GridView.count(
      crossAxisCount: 2,
      crossAxisSpacing: 10,
      mainAxisSpacing: 10,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      childAspectRatio: 1.6,
      children: [
        _kpiItem('মোট সিরিয়াল', '${stats['total_serials'] ?? 0}', Colors.blue, Icons.assignment_outlined),
        _kpiItem('বর্তমান টোকেন', '#${stats['current_serving_serial'] ?? 0}', Colors.red, Icons.campaign),
        _kpiItem('অপেক্ষমাণ রোগী', '${stats['waiting_count'] ?? 0}', Colors.amber.shade800, Icons.hourglass_top),
        _kpiItem('সম্পন্ন হয়েছে', '${stats['completed_count'] ?? 0}', Colors.green, Icons.check_circle_outline),
      ],
    );
  }

  Widget _kpiItem(String title, String val, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 6, offset: const Offset(0, 2)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(title, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary, fontWeight: FontWeight.w600)),
              Icon(icon, size: 18, color: color),
            ],
          ),
          const SizedBox(height: 4),
          Text(val, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: color)),
        ],
      ),
    );
  }

  Widget _buildActionBar(StaffProvider staffProv, dynamic assistantId) {
    return Column(
      children: [
        SizedBox(
          width: double.infinity,
          child: ElevatedButton.icon(
            onPressed: staffProv.isCallingNext || assistantId == null
                ? null
                : () async {
                    bool ok = await staffProv.callNext(assistantId);
                    if (mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(ok ? '📢 পরবর্তী রোগীকে সিরিয়ালে ডাকা হয়েছে!' : 'কোনো অপেক্ষমাণ রোগী পাওয়া যায়নি।'),
                          backgroundColor: ok ? Colors.green : Colors.orange,
                        ),
                      );
                    }
                  },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red.shade600,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(vertical: 14),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            icon: staffProv.isCallingNext
                ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                : const Icon(Icons.campaign),
            label: const Text(
              'পরবর্তী রোগী ডাকুন (Call Next Token)',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
            ),
          ),
        ),
        const SizedBox(height: 10),
        SizedBox(
          width: double.infinity,
          child: OutlinedButton.icon(
            onPressed: () => _openWalkinDialog(context, staffProv, assistantId),
            style: OutlinedButton.styleFrom(
              foregroundColor: Colors.teal,
              side: const BorderSide(color: Colors.teal),
              padding: const EdgeInsets.symmetric(vertical: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            icon: const Icon(Icons.person_add_alt_1),
            label: const Text('স্পট ওয়াক-ইন সিরিয়াল বুকিং (Add Walk-in)', style: TextStyle(fontWeight: FontWeight.bold)),
          ),
        ),
      ],
    );
  }

  Widget _buildAppointmentsSection(StaffProvider staffProv, dynamic assistantId) {
    final list = staffProv.appointments.where((a) {
      if (_selectedFilter == 'all') return true;
      return a['status'] == _selectedFilter;
    }).toList();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'আজকের সিরিয়াল খাতা (Queue Roster)',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Color(0xFF1E293B)),
            ),
            Text('মোট: ${staffProv.appointments.length} জন', style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
          ],
        ),
        const SizedBox(height: 8),
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(
            children: [
              _filterChip('all', 'সকল সিরিয়াল'),
              const SizedBox(width: 6),
              _filterChip('pending', 'অপেক্ষমাণ'),
              const SizedBox(width: 6),
              _filterChip('confirmed', 'চেম্বারে ডাক পড়েছে'),
              const SizedBox(width: 6),
              _filterChip('completed', 'সম্পন্ন'),
            ],
          ),
        ),
        const SizedBox(height: 12),
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
              child: Text('এই ক্যাটাগরিতে কোনো রোগী নেই।', style: TextStyle(color: Colors.grey)),
            ),
          )
        else
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: list.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (context, i) {
              final apt = list[i];
              final serial = apt['serial_number'] ?? (i + 1);
              final name = apt['patient_name'] ?? 'রোগী';
              final phone = apt['patient_phone'] ?? '';
              final status = apt['status'] ?? 'pending';
              final aptId = apt['id'];

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
                        color: Colors.teal.shade50,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      alignment: Alignment.center,
                      child: Text('#$serial', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.teal)),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                          Text('ফোন: $phone', style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
                        ],
                      ),
                    ),
                    PopupMenuButton<String>(
                      tooltip: 'স্ট্যাটাস বদলান',
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.grey.shade100,
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          status == 'completed' ? 'সম্পন্ন' : status == 'confirmed' ? 'চেম্বারে' : 'অপেক্ষমাণ',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: status == 'completed' ? Colors.green : status == 'confirmed' ? Colors.blue : Colors.orange,
                          ),
                        ),
                      ),
                      onSelected: (newStatus) async {
                        await staffProv.updateStatus(assistantId, aptId, newStatus);
                      },
                      itemBuilder: (_) => [
                        const PopupMenuItem(value: 'confirmed', child: Text('চেম্বারে প্রবেশ করান')),
                        const PopupMenuItem(value: 'completed', child: Text('সম্পন্ন মার্ক করুন')),
                        const PopupMenuItem(value: 'cancelled', child: Text('সিরিয়াল বাতিল করুন')),
                      ],
                    ),
                  ],
                ),
              );
            },
          ),
      ],
    );
  }

  Widget _filterChip(String key, String label) {
    final active = _selectedFilter == key;
    return ChoiceChip(
      label: Text(label, style: TextStyle(fontSize: 11, fontWeight: active ? FontWeight.bold : FontWeight.normal)),
      selected: active,
      selectedColor: Colors.teal.withOpacity(0.15),
      labelStyle: TextStyle(color: active ? Colors.teal : Colors.black87),
      onSelected: (_) => setState(() => _selectedFilter = key),
    );
  }

  void _openWalkinDialog(BuildContext context, StaffProvider staffProv, dynamic assistantId) {
    final nameCtrl = TextEditingController();
    final phoneCtrl = TextEditingController();
    final reasonCtrl = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('ওয়াক-ইন রোগী নিবন্ধন (Walk-in Serial)', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: nameCtrl,
              decoration: const InputDecoration(labelText: 'রোগীর নাম *', hintText: 'যেমন: মো: রফিকুল ইসলাম'),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: phoneCtrl,
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(labelText: 'মোবাইল নম্বর *', hintText: '017XXXXXXXX'),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: reasonCtrl,
              decoration: const InputDecoration(labelText: 'সমস্যার বিবরণ', hintText: 'যেমন: জ্বর ও গলা ব্যথা'),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('বাতিল'),
          ),
          ElevatedButton(
            onPressed: () async {
              if (nameCtrl.text.trim().isEmpty || phoneCtrl.text.trim().isEmpty) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('নাম ও মোবাইল নম্বর দেওয়া বাধ্যতামূলক!')),
                );
                return;
              }

              Navigator.pop(ctx);
              bool ok = await staffProv.createWalkin(assistantId, {
                'patient_name': nameCtrl.text.trim(),
                'patient_phone': phoneCtrl.text.trim(),
                'reason_for_visit': reasonCtrl.text.trim(),
              });

              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(ok ? 'ওয়াক-ইন রোগীর সিরিয়াল সফলভাবে সম্পন্ন হয়েছে!' : 'সিরিয়াল বুক করতে সমস্যা হয়েছে।'),
                    backgroundColor: ok ? Colors.green : Colors.red,
                  ),
                );
              }
            },
            child: const Text('সিরিয়াল কনফার্ম করুন'),
          ),
        ],
      ),
    );
  }
}
