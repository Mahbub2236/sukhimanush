import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../core/routes/app_routes.dart';
import '../../providers/emergency_provider.dart';
import '../../widgets/voice_text_field.dart';

class EmergencyHubScreen extends StatefulWidget {
  const EmergencyHubScreen({super.key});

  @override
  State<EmergencyHubScreen> createState() => _EmergencyHubScreenState();
}

class _EmergencyHubScreenState extends State<EmergencyHubScreen> {
  int _activeResourceTab = 0; // 0: Ambulances, 1: ICU Beds
  final TextEditingController _districtCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadResources();
    });
  }

  void _loadResources() {
    final prov = Provider.of<EmergencyProvider>(context, listen: false);
    final district = _districtCtrl.text.trim().isNotEmpty ? _districtCtrl.text.trim() : null;
    prov.fetchEmergencyOverview();
    prov.fetchAmbulances(district: district);
    prov.fetchHospitalBeds(district: district);
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

  @override
  Widget build(BuildContext context) {
    final prov = Provider.of<EmergencyProvider>(context);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('জরুরি সেবা হাব (Emergency Hub)'),
        backgroundColor: const Color(0xFFB91C1C),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: RefreshIndicator(
        onRefresh: () async => _loadResources(),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. Red Hero Banner
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(20, 24, 20, 28),
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFFB91C1C), Color(0xFFDC2626), Color(0xFF991B1B)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Column(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.amber.shade400,
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: const Text(
                        '🚨 ২৪/৭ জরুরি স্বাস্থ্য সহায়তা',
                        style: TextStyle(color: Color(0xFF0F172A), fontWeight: FontWeight.bold, fontSize: 12),
                      ),
                    ),
                    const SizedBox(height: 10),
                    const Text(
                      'জরুরি সেবা ও লাইভ রিসোর্স হাব',
                      textAlign: TextAlign.center,
                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white),
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'জরুরি প্রয়োজনে এম্বুলেন্স, রক্তদাতা, লাইভ আইসিইউ বেড এবং সরকারি হটলাইন সেবা ১-ট্যাপে পান।',
                      textAlign: TextAlign.center,
                      style: TextStyle(fontSize: 12, color: Colors.white70),
                    ),
                  ],
                ),
              ),

              // 2. Fast Launchers Cards
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                child: Row(
                  children: [
                    Expanded(
                      child: _buildActionLauncher(
                        title: 'ব্লাড SOS',
                        subtitle: 'রক্তের অনুরোধ',
                        icon: Icons.water_drop,
                        color: const Color(0xFFDC2626),
                        bgColor: const Color(0xFFFFF1F2),
                        onTap: () => Navigator.pushNamed(context, AppRoutes.bloodSos),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: _buildActionLauncher(
                        title: 'হেলথ কার্ড',
                        subtitle: 'জরুরি QR পাস',
                        icon: Icons.qr_code_2,
                        color: const Color(0xFFD97706),
                        bgColor: const Color(0xFFFEF3C7),
                        onTap: () => Navigator.pushNamed(context, AppRoutes.patientDashboard),
                      ),
                    ),
                  ],
                ),
              ),

              // 3. National Hotlines Horizontal Scroll Bar
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
                child: Container(
                  padding: const EdgeInsets.all(14),
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
                          Icon(Icons.headset_mic_rounded, color: Color(0xFFDC2626), size: 18),
                          SizedBox(width: 6),
                          Text(
                            'জাতীয় জরুরি সরকারি হটলাইন (২৪/৭ ফ্রি কল)',
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                          ),
                        ],
                      ),
                      const SizedBox(height: 10),
                      Row(
                        children: [
                          _buildHotlineButton('৯৯৯', 'পুলিশ/অ্যাম্বুলেন্স', '999'),
                          const SizedBox(width: 8),
                          _buildHotlineButton('১৬২৬৩', 'স্বাস্থ্য বাতায়ন', '16263'),
                          const SizedBox(width: 8),
                          _buildHotlineButton('১০৬৫৫', 'IEDCR কন্ট্রোল', '10655'),
                        ],
                      ),
                    ],
                  ),
                ),
              ),

              // 4. District Search
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: VoiceTextField(
                  controller: _districtCtrl,
                  hintText: 'জেলা দিয়ে এম্বুলেন্স বা বেড খুঁজুন (যেমন: ঢাকা)...',
                  prefixIcon: const Icon(Icons.search, color: Color(0xFF0284C7)),
                  onSubmitted: (_) => _loadResources(),
                ),
              ),
              const SizedBox(height: 12),

              // 5. Tabs for Ambulances and ICU Beds
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Row(
                  children: [
                    Expanded(
                      child: GestureDetector(
                        onTap: () => setState(() => _activeResourceTab = 0),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 11),
                          decoration: BoxDecoration(
                            color: _activeResourceTab == 0 ? const Color(0xFF0284C7) : Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: _activeResourceTab == 0 ? const Color(0xFF0284C7) : const Color(0xFFCBD5E1)),
                          ),
                          child: Center(
                            child: Text(
                              'ভেরিফায়েড এম্বুলেন্স (${prov.ambulances.length})',
                              style: TextStyle(
                                color: _activeResourceTab == 0 ? Colors.white : const Color(0xFF0F172A),
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
                        onTap: () => setState(() => _activeResourceTab = 1),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 11),
                          decoration: BoxDecoration(
                            color: _activeResourceTab == 1 ? const Color(0xFF16A34A) : Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: _activeResourceTab == 1 ? const Color(0xFF16A34A) : const Color(0xFFCBD5E1)),
                          ),
                          child: Center(
                            child: Text(
                              'লাইভ আইসিইউ বেড (${prov.hospitalBeds.length})',
                              style: TextStyle(
                                color: _activeResourceTab == 1 ? Colors.white : const Color(0xFF0F172A),
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

              // 6. Resources List View
              if (prov.isLoadingAmbulances || prov.isLoadingBeds)
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 40),
                  child: Center(child: CircularProgressIndicator(color: Color(0xFF0284C7))),
                )
              else if (_activeResourceTab == 0) ...[
                // TAB 0: AMBULANCES
                if (prov.ambulances.isEmpty)
                  Container(
                    margin: const EdgeInsets.all(20),
                    padding: const EdgeInsets.all(30),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                    ),
                    child: const Center(
                      child: Text('এই জেলায় কোনো সক্রিয় এম্বুলেন্স পাওয়া যায়নি।', style: TextStyle(color: Color(0xFF64748B), fontSize: 13)),
                    ),
                  )
                else
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    itemCount: prov.ambulances.length,
                    itemBuilder: (context, idx) {
                      final amb = prov.ambulances[idx];
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
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: const Color(0xFFEFF6FF),
                                borderRadius: BorderRadius.circular(14),
                              ),
                              child: const Icon(Icons.airport_shuttle_rounded, color: Color(0xFF0284C7), size: 26),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Expanded(
                                        child: Text(
                                          amb.serviceName,
                                          style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: Color(0xFF0F172A)),
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1),
                                        decoration: BoxDecoration(
                                          color: const Color(0xFFE0F2FE),
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Text(
                                          amb.vehicleType.toUpperCase(),
                                          style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Color(0xFF0284C7)),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    'চালক: ${amb.driverName} • ${amb.district}',
                                    style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                  ),
                                  if (amb.distanceText != null)
                                    Text(
                                      'দূরত্ব: ${amb.distanceText}',
                                      style: const TextStyle(fontSize: 10, color: Color(0xFF059669), fontWeight: FontWeight.bold),
                                    ),
                                ],
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.call, color: Colors.green, size: 24),
                              onPressed: () => _makeCall(amb.phone),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
              ] else ...[
                // TAB 1: HOSPITAL BEDS
                if (prov.hospitalBeds.isEmpty)
                  Container(
                    margin: const EdgeInsets.all(20),
                    padding: const EdgeInsets.all(30),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                    ),
                    child: const Center(
                      child: Text('এই জেলায় কোনো আইসিইউ বেডের তথ্য পাওয়া যায়নি।', style: TextStyle(color: Color(0xFF64748B), fontSize: 13)),
                    ),
                  )
                else
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    itemCount: prov.hospitalBeds.length,
                    itemBuilder: (context, idx) {
                      final bed = prov.hospitalBeds[idx];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
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
                                Expanded(
                                  child: Text(
                                    bed.hospitalName,
                                    style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: Color(0xFF0F172A)),
                                  ),
                                ),
                                if (bed.emergencyPhone != null)
                                  IconButton(
                                    icon: const Icon(Icons.call, color: Colors.green, size: 22),
                                    onPressed: () => _makeCall(bed.emergencyPhone!),
                                  ),
                              ],
                            ),
                            Text(
                              '${bed.district} • ${bed.address ?? ''}',
                              style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                            ),
                            const SizedBox(height: 10),
                            Row(
                              children: [
                                _buildBedBadge('ICU খালি', '${bed.icuAvailable}/${bed.icuTotal}', const Color(0xFF16A34A)),
                                const SizedBox(width: 8),
                                _buildBedBadge('NICU খালি', '${bed.nicuAvailable}', const Color(0xFF0284C7)),
                                const SizedBox(width: 8),
                                _buildBedBadge('CCU খালি', '${bed.ccuAvailable}', const Color(0xFF9333EA)),
                              ],
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

  Widget _buildActionLauncher({
    required String title,
    required String subtitle,
    required IconData icon,
    required Color color,
    required Color bgColor,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
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
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: bgColor,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: color, size: 22),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: Color(0xFF0F172A))),
                  Text(subtitle, style: const TextStyle(fontSize: 10, color: Color(0xFF64748B))),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHotlineButton(String number, String label, String phone) {
    return Expanded(
      child: GestureDetector(
        onTap: () => _makeCall(phone),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 6),
          decoration: BoxDecoration(
            color: const Color(0xFFF1F5F9),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Column(
            children: [
              Text(
                number,
                style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14, color: Color(0xFFDC2626)),
              ),
              Text(
                label,
                style: const TextStyle(fontSize: 9, color: Color(0xFF64748B)),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBedBadge(String label, String value, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text('$label: ', style: TextStyle(fontSize: 10, color: color)),
          Text(value, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: color)),
        ],
      ),
    );
  }
}
