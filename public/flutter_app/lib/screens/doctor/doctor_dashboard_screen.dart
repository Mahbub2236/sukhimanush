import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/routes/app_routes.dart';
import '../../providers/auth_provider.dart';
import '../../providers/doctor_portal_provider.dart';
import 'doctor_prescription_dialog.dart';

class DoctorDashboardScreen extends StatefulWidget {
  const DoctorDashboardScreen({super.key});

  @override
  State<DoctorDashboardScreen> createState() => _DoctorDashboardScreenState();
}

class _DoctorDashboardScreenState extends State<DoctorDashboardScreen> {
  String _selectedFilter = 'all';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final authProv = Provider.of<AuthProvider>(context, listen: false);
      if (authProv.currentUser == null || !authProv.currentUser!.isDoctor) {
        await authProv.quickDemoLogin('doctor');
      }
      if (mounted) {
        final docProv = Provider.of<DoctorPortalProvider>(context, listen: false);
        await docProv.fetchDashboard();
        await docProv.fetchAppointments();
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
          color: isActive ? AppColors.primary : Colors.white.withOpacity(0.12),
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
    final docProv = Provider.of<DoctorPortalProvider>(context);
    final doctor = docProv.doctor;
    final stats = docProv.stats;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.12),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.medical_services_outlined, color: AppColors.primary, size: 20),
            ),
            const SizedBox(width: 10),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Doctor Control Portal', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                Text('Chamber Management & E-Prescriptions', style: TextStyle(fontSize: 10, color: AppColors.textSecondary)),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'রিলোড করুন',
            onPressed: () {
              docProv.fetchDashboard();
              docProv.fetchAppointments();
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
      body: docProv.isLoading && doctor == null
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                await docProv.fetchDashboard();
                await docProv.fetchAppointments();
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
                            _buildQuickRoleBadge('🩺 ডাক্তার', true, () {}),
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
                          // Doctor Profile Header
                          _buildDoctorHeader(doctor),
                          const SizedBox(height: 16),

                          // KPI Metrics Row
                          _buildKpiGrid(stats),
                          const SizedBox(height: 18),

                          // Chamber Queue Live Controller
                          _buildQueueController(docProv),
                          const SizedBox(height: 24),

                          // Patient Roster Header & Filter
                          _buildRosterHeader(docProv),
                          const SizedBox(height: 12),

                          // Patient Cards
                          _buildAppointmentList(docProv),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildDoctorHeader(Map<String, dynamic>? doctor) {
    final name = doctor?['name'] ?? 'Dr. Specialist';
    final specialty = doctor?['specialization'] ?? 'Medical Specialist';
    final fee = doctor?['consultation_fee'] ?? 500;
    final chamber = doctor?['chamber_address'] ?? 'চেম্বার কাউন্টার';
    final bmdc = doctor?['bmdc_number'];

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxInsets.shadow,
        ],
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 30,
            backgroundColor: AppColors.primary.withOpacity(0.1),
            child: const Icon(Icons.person, size: 36, color: AppColors.primary),
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
                        name,
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 17, color: Color(0xFF1E293B)),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: Colors.green.shade50,
                        borderRadius: BorderRadius.circular(6),
                        border: Border.all(color: Colors.green.shade200),
                      ),
                      child: const Text('Live', style: TextStyle(color: Colors.green, fontSize: 10, fontWeight: FontWeight.bold)),
                    ),
                  ],
                ),
                Text(specialty, style: const TextStyle(fontSize: 13, color: AppColors.primary, fontWeight: FontWeight.w600)),
                const SizedBox(height: 4),
                Row(
                  children: [
                    const Icon(Icons.location_on_outlined, size: 14, color: Colors.redAccent),
                    const SizedBox(width: 4),
                    Expanded(
                      child: Text(chamber, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary), overflow: TextOverflow.ellipsis),
                    ),
                  ],
                ),
                const SizedBox(height: 2),
                Row(
                  children: [
                    if (bmdc != null)
                      Text('BMDC: $bmdc • ', style: const TextStyle(fontSize: 11, color: Colors.blueGrey)),
                    Text('ফি: ৳$fee', style: const TextStyle(fontSize: 11, color: Colors.green, fontWeight: FontWeight.bold)),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildKpiGrid(Map<String, dynamic> stats) {
    return GridView.count(
      crossAxisCount: 2,
      crossAxisSpacing: 10,
      mainAxisSpacing: 10,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      childAspectRatio: 1.6,
      children: [
        _kpiCard('মোট রোগী', '${stats['total_appointments'] ?? 0}', Icons.people_outline, Colors.blue),
        _kpiCard('আজকের সিরিয়াল', '${stats['today_appointments'] ?? 0}', Icons.schedule, AppColors.primary),
        _kpiCard('প্রেসক্রিপশন', '${stats['total_prescriptions'] ?? 0}', Icons.receipt_long_outlined, Colors.amber.shade800),
        _kpiCard('নেট আয় (৯০%)', '৳${stats['total_earnings'] ?? 0}', Icons.account_balance_wallet_outlined, Colors.green),
      ],
    );
  }

  Widget _kpiCard(String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [BoxInsets.shadow],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(label, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary, fontWeight: FontWeight.w600)),
              Icon(icon, size: 18, color: color),
            ],
          ),
          const SizedBox(height: 4),
          Text(value, style: TextStyle(fontSize: 19, fontWeight: FontWeight.w900, color: color)),
        ],
      ),
    );
  }

  Widget _buildQueueController(DoctorPortalProvider docProv) {
    final doctorId = docProv.doctor?['id'];

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [AppColors.primary, AppColors.primary.withBlue(160)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withOpacity(0.3),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.campaign_outlined, color: Colors.white, size: 20),
              SizedBox(width: 8),
              Text(
                'চেম্বার লাইভ সিরিয়াল কন্ট্রোল (Queue Control)',
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: ElevatedButton.icon(
                  onPressed: docProv.isCallingNext || doctorId == null
                      ? null
                      : () async {
                          bool ok = await docProv.callNext(doctorId);
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
                    backgroundColor: Colors.white,
                    foregroundColor: AppColors.primary,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  icon: docProv.isCallingNext
                      ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2))
                      : const Icon(Icons.arrow_forward),
                  label: const Text(
                    'পরবর্তী রোগী ডাকুন (Call Next)',
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildRosterHeader(DoctorPortalProvider docProv) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'রোগীদের তালিকা (Appointments)',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Color(0xFF1E293B)),
            ),
            Text(
              'মোট: ${docProv.appointments.length} জন',
              style: const TextStyle(fontSize: 12, color: AppColors.textSecondary),
            ),
          ],
        ),
        const SizedBox(height: 8),
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(
            children: [
              _filterChip('all', 'সকল রোগী'),
              const SizedBox(width: 6),
              _filterChip('pending', 'অপেক্ষমাণ (Pending)'),
              const SizedBox(width: 6),
              _filterChip('confirmed', 'নিশ্চিত (Confirmed)'),
              const SizedBox(width: 6),
              _filterChip('completed', 'সম্পন্ন (Completed)'),
            ],
          ),
        ),
      ],
    );
  }

  Widget _filterChip(String key, String label) {
    final active = _selectedFilter == key;
    return ChoiceChip(
      label: Text(label, style: TextStyle(fontSize: 11, fontWeight: active ? FontWeight.bold : FontWeight.normal)),
      selected: active,
      selectedColor: AppColors.primary.withOpacity(0.15),
      labelStyle: TextStyle(color: active ? AppColors.primary : Colors.black87),
      onSelected: (_) => setState(() => _selectedFilter = key),
    );
  }

  Widget _buildAppointmentList(DoctorPortalProvider docProv) {
    final list = docProv.appointments.where((a) {
      if (_selectedFilter == 'all') return true;
      return a['status'] == _selectedFilter;
    }).toList();

    if (list.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(32),
        width: double.infinity,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.grey.shade200),
        ),
        child: const Column(
          children: [
            Icon(Icons.event_busy, size: 42, color: Colors.grey),
            SizedBox(height: 8),
            Text('এই ফিল্টারে কোনো অ্যাপয়েন্টমেন্ট পাওয়া যায়নি।', style: TextStyle(color: Colors.grey, fontSize: 13)),
          ],
        ),
      );
    }

    return ListView.separated(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: list.length,
      separatorBuilder: (_, __) => const SizedBox(height: 10),
      itemBuilder: (context, index) {
        final apt = list[index];
        final serial = apt['serial_number'] ?? (index + 1);
        final name = apt['patient_name'] ?? 'রোগী';
        final phone = apt['patient_phone'] ?? '';
        final status = apt['status'] ?? 'pending';
        final time = apt['appointment_time'] ?? '';
        final aptId = apt['id'];

        return Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: status == 'confirmed' ? AppColors.primary.withOpacity(0.3) : Colors.grey.shade200,
            ),
            boxShadow: [BoxInsets.shadow],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Container(
                    width: 38,
                    height: 38,
                    decoration: BoxDecoration(
                      color: AppColors.primary.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    alignment: Alignment.center,
                    child: Text(
                      '#$serial',
                      style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.primary, fontSize: 13),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                        Text('ফোন: $phone • সময়: $time', style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
                      ],
                    ),
                  ),
                  _statusBadge(status),
                ],
              ),
              const Divider(height: 20),
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  OutlinedButton.icon(
                    onPressed: () => _openPrescriptionDialog(context, docProv, apt),
                    icon: const Icon(Icons.edit_note, size: 16),
                    label: const Text('প্রেসক্রিপশন দিন', style: TextStyle(fontSize: 12)),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppColors.primary,
                      side: const BorderSide(color: AppColors.primary),
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    ),
                  ),
                  const SizedBox(width: 8),
                  PopupMenuButton<String>(
                    tooltip: 'স্ট্যাটাস পরিবর্তন',
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(
                        color: Colors.grey.shade100,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Row(
                        children: [
                          Text('অ্যাকশন', style: TextStyle(fontSize: 12)),
                          Icon(Icons.arrow_drop_down, size: 16),
                        ],
                      ),
                    ),
                    onSelected: (newStatus) async {
                      await docProv.updateStatus(aptId, newStatus);
                    },
                    itemBuilder: (_) => [
                      const PopupMenuItem(value: 'confirmed', child: Text('নিশ্চিত করুন (Confirm)')),
                      const PopupMenuItem(value: 'completed', child: Text('সম্পন্ন করুন (Complete)')),
                      const PopupMenuItem(value: 'cancelled', child: Text('বাতিল করুন (Cancel)')),
                    ],
                  ),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _statusBadge(String status) {
    Color bg;
    Color fg;
    String text;

    switch (status) {
      case 'completed':
        bg = Colors.green.shade50;
        fg = Colors.green.shade700;
        text = 'সম্পন্ন';
        break;
      case 'confirmed':
        bg = Colors.blue.shade50;
        fg = Colors.blue.shade700;
        text = 'চেম্বারে ডাক';
        break;
      case 'cancelled':
        bg = Colors.red.shade50;
        fg = Colors.red.shade700;
        text = 'বাতিল';
        break;
      default:
        bg = Colors.orange.shade50;
        fg = Colors.orange.shade700;
        text = 'অপেক্ষমাণ';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(6)),
      child: Text(text, style: TextStyle(color: fg, fontSize: 10, fontWeight: FontWeight.bold)),
    );
  }

  void _openPrescriptionDialog(BuildContext context, DoctorPortalProvider docProv, Map<String, dynamic> apt) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => DoctorPrescriptionDialog(
        appointment: apt,
        doctor: docProv.doctor,
        docProv: docProv,
      ),
    );
  }
}

class BoxInsets {
  static final shadow = BoxShadow(
    color: Colors.black.withOpacity(0.04),
    blurRadius: 8,
    offset: const Offset(0, 2),
  );
}
