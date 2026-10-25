import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/routes/app_routes.dart';
import '../../providers/auth_provider.dart';
import '../../providers/admin_provider.dart';

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({super.key});

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final authProv = Provider.of<AuthProvider>(context, listen: false);
      if (authProv.currentUser == null || !authProv.currentUser!.isAdmin) {
        await authProv.quickDemoLogin('admin');
      }
      if (mounted) {
        final adminProv = Provider.of<AdminProvider>(context, listen: false);
        adminProv.fetchStats();
        adminProv.fetchDoctors();
        adminProv.fetchAppointments();
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
          color: isActive ? Colors.indigo : Colors.white.withOpacity(0.12),
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
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final authProv = Provider.of<AuthProvider>(context);
    final adminProv = Provider.of<AdminProvider>(context);
    final stats = adminProv.stats;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: Colors.indigo.withOpacity(0.12),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.admin_panel_settings_outlined, color: Colors.indigo, size: 20),
            ),
            const SizedBox(width: 10),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('সুপার অ্যাডমিন কন্ট্রোল', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                Text('SaaS Platform Admin Panel', style: TextStyle(fontSize: 10, color: AppColors.textSecondary)),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'রিফ্রেশ',
            onPressed: () {
              adminProv.fetchStats();
              adminProv.fetchDoctors();
              adminProv.fetchAppointments();
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
        bottom: TabBar(
          controller: _tabController,
          labelColor: Colors.indigo,
          unselectedLabelColor: Colors.grey,
          indicatorColor: Colors.indigo,
          indicatorWeight: 3,
          tabs: const [
            Tab(icon: Icon(Icons.dashboard_outlined, size: 20), text: 'ড্যাশবোর্ড ও ডাক্তার'),
            Tab(icon: Icon(Icons.calendar_month_outlined, size: 20), text: 'অ্যাপয়েন্টমেন্ট খাতা'),
          ],
        ),
      ),
      body: adminProv.isLoading && stats.isEmpty
          ? const Center(child: CircularProgressIndicator())
          : Column(
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
                        _buildQuickRoleBadge('🏪 এজেন্ট', false, () async {
                          await authProv.quickDemoLogin('agent');
                          if (context.mounted) Navigator.pushNamed(context, AppRoutes.agentDashboard);
                        }),
                        const SizedBox(width: 6),
                        _buildQuickRoleBadge('🛡️ অ্যাডমিন', true, () {}),
                      ],
                    ),
                  ),
                ),
                Expanded(
                  child: TabBarView(
                    controller: _tabController,
                    children: [
                // Tab 1: Stats & Doctor Approvals
                RefreshIndicator(
                  onRefresh: () async {
                    await adminProv.fetchStats();
                    await adminProv.fetchDoctors();
                  },
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildFinancialStats(stats),
                        const SizedBox(height: 16),
                        _buildPlatformStats(stats),
                        const SizedBox(height: 22),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text(
                              'ডাক্তার ভেরিফিকেশন ও অনুমোদন',
                              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: Color(0xFF1E293B)),
                            ),
                            Text('মোট: ${adminProv.doctors.length} জন', style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                          ],
                        ),
                        const SizedBox(height: 10),
                        _buildDoctorsList(adminProv),
                      ],
                    ),
                  ),
                ),

                // Tab 2: All Platform Appointments
                RefreshIndicator(
                  onRefresh: () async {
                    await adminProv.fetchAppointments();
                  },
                  child: _buildAppointmentsTab(adminProv),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFinancialStats(Map<String, dynamic> stats) {
    final revenue = stats['total_revenue'] ?? 0;
    final commission = stats['admin_commission'] ?? 0;
    final doctorPayout = stats['doctor_payouts'] ?? 0;

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF1E1B4B), Color(0xFF312E81), Color(0xFF4338CA)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('মোট প্ল্যাটফর্ম রাজস্ব (Total Revenue)', style: TextStyle(color: Colors.white70, fontSize: 12)),
          const SizedBox(height: 4),
          Text('৳$revenue', style: const TextStyle(color: Colors.white, fontSize: 26, fontWeight: FontWeight.w900)),
          const Divider(color: Colors.white24, height: 20),
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('অ্যাডমিন ফি (১০%)', style: TextStyle(color: Colors.amberAccent, fontSize: 11)),
                    Text('৳$commission', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                  ],
                ),
              ),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('ডাক্তার পাওনা (৯০%)', style: TextStyle(color: Colors.lightGreenAccent, fontSize: 11)),
                    Text('৳$doctorPayout', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildPlatformStats(Map<String, dynamic> stats) {
    return GridView.count(
      crossAxisCount: 2,
      crossAxisSpacing: 10,
      mainAxisSpacing: 10,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      childAspectRatio: 1.6,
      children: [
        _statBox('মোট ডাক্তার', '${stats['total_doctors'] ?? 0}', Colors.blue, Icons.medical_services_outlined),
        _statBox('অনুমোদন বাকি', '${stats['pending_doctors'] ?? 0}', Colors.orange, Icons.hourglass_top),
        _statBox('মোট রোগী', '${stats['total_patients'] ?? 0}', Colors.purple, Icons.people_outline),
        _statBox('গ্রামীণ এজেন্ট', '${stats['total_agents'] ?? 0}', Colors.teal, Icons.storefront_outlined),
      ],
    );
  }

  Widget _statBox(String title, String val, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(title, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
              Icon(icon, size: 16, color: color),
            ],
          ),
          const SizedBox(height: 4),
          Text(val, style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: color)),
        ],
      ),
    );
  }

  Widget _buildDoctorsList(AdminProvider adminProv) {
    final list = adminProv.doctors;

    if (list.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(24),
        width: double.infinity,
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
        child: const Center(child: Text('কোনো ডাক্তার অ্যাকাউন্ট পাওয়া যায়নি।', style: TextStyle(color: Colors.grey))),
      );
    }

    return ListView.separated(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: list.length,
      separatorBuilder: (_, __) => const SizedBox(height: 8),
      itemBuilder: (context, i) {
        final d = list[i];
        final name = d['name'] ?? 'ডাক্তার';
        final specialty = d['specialization'] ?? '';
        final bmdc = d['bmdc_number'] ?? 'N/A';
        final isApproved = d['is_approved'] == true || d['is_approved'] == 1;
        final docId = d['id'];

        return Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.grey.shade200),
          ),
          child: Row(
            children: [
              CircleAvatar(
                radius: 20,
                backgroundColor: isApproved ? Colors.green.shade50 : Colors.orange.shade50,
                child: Icon(Icons.person, color: isApproved ? Colors.green : Colors.orange),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                    Text('$specialty • BMDC: $bmdc', style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
                  ],
                ),
              ),
              ElevatedButton(
                onPressed: () async {
                  bool ok = await adminProv.toggleDoctorStatus(docId);
                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text(ok ? 'ডাক্তার অনুমোদন স্ট্যাটাস আপডেট হয়েছে।' : 'আপডেট ব্যর্থ হয়েছে।')),
                    );
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: isApproved ? Colors.green.shade600 : Colors.orange.shade600,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
                child: Text(isApproved ? 'অনুমোদিত' : 'অনুমোদন দিন', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildAppointmentsTab(AdminProvider adminProv) {
    final list = adminProv.appointments;

    if (list.isEmpty) {
      return const Center(
        child: Text('কোনো অ্যাপয়েন্টমেন্ট পাওয়া যায়নি।', style: TextStyle(color: Colors.grey)),
      );
    }

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: list.length,
      separatorBuilder: (_, __) => const SizedBox(height: 10),
      itemBuilder: (context, i) {
        final apt = list[i];
        final id = apt['id'];
        final patientName = apt['patient_name'] ?? 'রোগী';
        final doctorName = apt['doctor']?['name'] ?? 'ডাক্তার';
        final phone = apt['patient_phone'] ?? '';
        final status = apt['status'] ?? 'pending';
        final date = apt['appointment_date'] != null ? apt['appointment_date'].toString().split('T').first : '';

        return Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.grey.shade200),
          ),
          child: Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(patientName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                    Text('ডাক্তার: $doctorName', style: const TextStyle(fontSize: 11, color: AppColors.primary)),
                    Text('ফোন: $phone • তারিখ: $date', style: const TextStyle(fontSize: 10, color: AppColors.textSecondary)),
                  ],
                ),
              ),
              PopupMenuButton<String>(
                tooltip: 'স্ট্যাটাস আপডেট',
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.grey.shade100,
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text(
                    status,
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: status == 'completed' ? Colors.green : status == 'confirmed' ? Colors.blue : Colors.orange,
                    ),
                  ),
                ),
                onSelected: (newStatus) async {
                  await adminProv.updateAppointmentStatus(id, newStatus);
                },
                itemBuilder: (_) => [
                  const PopupMenuItem(value: 'confirmed', child: Text('নিশ্চিত (Confirmed)')),
                  const PopupMenuItem(value: 'completed', child: Text('সম্পন্ন (Completed)')),
                  const PopupMenuItem(value: 'cancelled', child: Text('বাতিল (Cancelled)')),
                ],
              ),
            ],
          ),
        );
      },
    );
  }
}
