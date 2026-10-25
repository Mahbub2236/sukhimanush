import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../core/constants/app_colors.dart';
import '../../core/routes/app_routes.dart';
import '../../models/doctor_model.dart';
import '../../providers/doctor_provider.dart';

class DoctorDetailScreen extends StatefulWidget {
  const DoctorDetailScreen({super.key});

  @override
  State<DoctorDetailScreen> createState() => _DoctorDetailScreenState();
}

class _DoctorDetailScreenState extends State<DoctorDetailScreen> {
  DoctorModel? _doctor;
  bool _initialized = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!_initialized) {
      _initialized = true;
      final args = ModalRoute.of(context)?.settings.arguments;
      if (args is DoctorModel) {
        _doctor = args;
        // Optionally fetch full details
        Provider.of<DoctorProvider>(context, listen: false)
            .fetchDoctorDetail(args.slug);
      } else if (args is String) {
        Provider.of<DoctorProvider>(context, listen: false).fetchDoctorDetail(args);
      }
    }
  }

  Future<void> _makeCall(String phone) async {
    final uri = Uri.parse('tel:$phone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    final docProv = Provider.of<DoctorProvider>(context);
    final doctor = docProv.selectedDoctor ?? _doctor;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('ডাক্তার প্রোফাইল ও বিস্তারিত', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        elevation: 0,
      ),
      body: doctor == null
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Hero Doctor Profile Header Card
                  _buildHeroHeader(doctor),
                  const SizedBox(height: 16),

                  // Quick Highlights (Rating, Fee, BMDC)
                  _buildHighlightsRow(doctor),
                  const SizedBox(height: 20),

                  // Chambers & Consultation Schedule
                  _buildChambersSection(doctor),
                  const SizedBox(height: 20),

                  // About & Specialization Details
                  _buildAboutSection(doctor),
                  const SizedBox(height: 80), // padding for bottom bar
                ],
              ),
            ),
      bottomNavigationBar: doctor == null ? null : _buildBottomActionBar(doctor),
    );
  }

  Widget _buildHeroHeader(DoctorModel doctor) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Stack(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: doctor.image != null && doctor.image!.isNotEmpty
                    ? Image.network(
                        doctor.image!,
                        width: 84,
                        height: 84,
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => _avatarFallback(),
                      )
                    : _avatarFallback(),
              ),
              Positioned(
                bottom: 0,
                right: 0,
                child: Container(
                  width: 16,
                  height: 16,
                  decoration: BoxDecoration(
                    color: const Color(0xFF10B981),
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.white, width: 2),
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
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    doctor.specialization,
                    style: const TextStyle(
                      color: AppColors.primary,
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  doctor.name,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w900,
                    color: Color(0xFF0F172A),
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  doctor.qualifications ?? 'MBBS, Specialist Physician',
                  style: const TextStyle(fontSize: 12, color: Color(0xFF64748B), height: 1.3),
                ),
                if (doctor.bmdcNumber != null) ...[
                  const SizedBox(height: 6),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: Colors.blue.shade50,
                      borderRadius: BorderRadius.circular(6),
                      border: Border.all(color: Colors.blue.shade200),
                    ),
                    child: Text(
                      'BMDC Reg: ${doctor.bmdcNumber}',
                      style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.blue.shade800),
                    ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _avatarFallback() {
    return Container(
      width: 84,
      height: 84,
      color: AppColors.primary.withOpacity(0.1),
      child: const Icon(Icons.person, size: 48, color: AppColors.primary),
    );
  }

  Widget _buildHighlightsRow(DoctorModel doctor) {
    return Row(
      children: [
        Expanded(
          child: _highlightBox(
            title: 'পরামর্শ ফি',
            value: '৳${doctor.consultationFee.toInt()}',
            color: const Color(0xFF059669),
            icon: Icons.payments_outlined,
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _highlightBox(
            title: 'রোগী রেটিং',
            value: '${doctor.rating.toStringAsFixed(1)} ★',
            color: Colors.amber.shade800,
            icon: Icons.star_outline,
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _highlightBox(
            title: 'অভিজ্ঞতা',
            value: '${doctor.experienceYears} বছর',
            color: Colors.blue.shade700,
            icon: Icons.workspace_premium_outlined,
          ),
        ),
      ],
    );
  }

  Widget _highlightBox({
    required String title,
    required String value,
    required Color color,
    required IconData icon,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        children: [
          Icon(icon, size: 20, color: color),
          const SizedBox(height: 4),
          Text(
            value,
            style: TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: color),
          ),
          Text(
            title,
            style: const TextStyle(fontSize: 10, color: AppColors.textSecondary),
          ),
        ],
      ),
    );
  }

  Widget _buildChambersSection(DoctorModel doctor) {
    final chambers = doctor.chambers;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'চেম্বার ও রোগী দেখার সময়সূচী (Chambers)',
          style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Color(0xFF1E293B)),
        ),
        const SizedBox(height: 10),
        if (chambers.isEmpty)
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: Row(
              children: [
                const Icon(Icons.location_on, color: Colors.redAccent, size: 22),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('মূল চেম্বার', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                      Text(
                        doctor.district != null ? '${doctor.district}, ${doctor.division ?? ''}' : 'চেম্বার কাউন্টার',
                        style: const TextStyle(fontSize: 12, color: AppColors.textSecondary),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          )
        else
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: chambers.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (context, i) {
              final c = chambers[i];
              return Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: c.isPrimary ? AppColors.primary.withOpacity(0.4) : Colors.grey.shade200,
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          c.chamberName,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF0F172A)),
                        ),
                        if (c.isPrimary)
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                            decoration: BoxDecoration(
                              color: AppColors.primary.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: const Text('প্রধান চেম্বার', style: TextStyle(fontSize: 10, color: AppColors.primary, fontWeight: FontWeight.bold)),
                          ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        const Icon(Icons.location_on_outlined, size: 14, color: Colors.redAccent),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(c.address, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                        ),
                      ],
                    ),
                    if (c.consultationDays != null || c.consultationTime != null) ...[
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          const Icon(Icons.access_time, size: 14, color: Colors.blueGrey),
                          const SizedBox(width: 4),
                          Text(
                            'বার: ${c.consultationDays ?? 'প্রতিদিন'} • সময়: ${c.consultationTime ?? 'বিকাল ৫টা - রাত ৯টা'}',
                            style: const TextStyle(fontSize: 11, color: Colors.blueGrey),
                          ),
                        ],
                      ),
                    ],
                  ],
                ),
              );
            },
          ),
      ],
    );
  }

  Widget _buildAboutSection(DoctorModel doctor) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'ডাক্তার সম্পর্কে (About Doctor)',
          style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Color(0xFF1E293B)),
        ),
        const SizedBox(height: 8),
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: Colors.grey.shade200),
          ),
          child: Text(
            doctor.bio ??
                'ডাঃ ${doctor.name} একজন অভিজ্ঞ ও ভেরিফায়েড বিশেষজ্ঞ চিকিৎসক। তিনি আধুনিক চিকিৎসা পদ্ধতি ও স্বাস্থ্যসেবায় বিশ্বস্ততার সাথে চিকিৎসা প্রদান করে আসছেন।',
            style: const TextStyle(fontSize: 13, color: Color(0xFF475569), height: 1.5),
          ),
        ),
      ],
    );
  }

  Widget _buildBottomActionBar(DoctorModel doctor) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 10,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: SafeArea(
        child: Row(
          children: [
            InkWell(
              onTap: () => _makeCall('106'),
              borderRadius: BorderRadius.circular(12),
              child: Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.red.shade200),
                ),
                child: const Icon(Icons.phone_in_talk, color: Color(0xFFE11D48)),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: ElevatedButton.icon(
                onPressed: () {
                  Navigator.pushNamed(context, AppRoutes.bookAppointment, arguments: doctor);
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF0F172A),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                icon: const Icon(Icons.calendar_today, size: 18),
                label: const Text(
                  'সিরিয়াল বুকিং নিন (Book Appointment)',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
