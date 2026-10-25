import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/appointment_provider.dart';
import '../../widgets/voice_text_field.dart';

class LiveQueueScreen extends StatefulWidget {
  const LiveQueueScreen({super.key});

  @override
  State<LiveQueueScreen> createState() => _LiveQueueScreenState();
}

class _LiveQueueScreenState extends State<LiveQueueScreen> {
  final TextEditingController _queryCtrl = TextEditingController(text: '01712345678');
  bool _hasSearched = false;

  @override
  void dispose() {
    _queryCtrl.dispose();
    super.dispose();
  }

  void _search() {
    if (_queryCtrl.text.trim().isEmpty) return;
    setState(() => _hasSearched = true);
    Provider.of<AppointmentProvider>(context, listen: false)
        .trackAppointmentQuery(_queryCtrl.text.trim());
  }

  Future<void> _openVideo(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    final apptProv = Provider.of<AppointmentProvider>(context);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('লাইভ চেম্বার সিরিয়াল ট্র্যাকার'),
        backgroundColor: const Color(0xFF0F172A),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // 1. Dark Web Header Banner
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(20, 24, 20, 28),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [Color(0xFF0F172A), Color(0xFF1E293B), Color(0xFF223A66)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                    decoration: BoxDecoration(
                      color: const Color(0xFFE11D48),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Text(
                      'রিয়েল-টাইম স্বাস্থ্য সেবা',
                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 11),
                    ),
                  ),
                  const SizedBox(height: 10),
                  const Text(
                    'লাইভ চেম্বার সিরিয়াল ট্র্যাকার',
                    textAlign: TextAlign.center,
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'চেম্বারে বসে না থেকে মোবাইলেই লাইভ দেখুন কত নম্বর সিরিয়াল চলছে এবং আপনার ডাকের সম্ভাব্য সময়।',
                    textAlign: TextAlign.center,
                    style: TextStyle(fontSize: 12, color: Colors.white70),
                  ),
                ],
              ),
            ),

            // 2. Lookup Form Card
            Container(
              margin: const EdgeInsets.all(16),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
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
                  const Text(
                    'সিরিয়াল কোড অথবা মোবাইল নম্বর দিন:',
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'বুকিং রেফারেন্স (যেমনঃ NOV-2026-...) বা ফোন নম্বর লিখুন',
                    style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                  ),
                  const SizedBox(height: 12),
                  VoiceTextField(
                    controller: _queryCtrl,
                    hintText: 'যেমন: 01711223344 বা NOV-...',
                    prefixIcon: const Icon(Icons.confirmation_number_outlined, color: Color(0xFF0284C7)),
                    onSubmitted: (_) => _search(),
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFE11D48),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: apptProv.isTracking ? null : _search,
                      icon: apptProv.isTracking
                          ? const SizedBox(
                              width: 16,
                              height: 16,
                              child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                            )
                          : const Icon(Icons.search, size: 18),
                      label: Text(
                        apptProv.isTracking ? 'অনুসন্ধান চলছে...' : 'লাইভ ট্র্যাকিং দেখুন',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // 3. Results Section
            if (apptProv.isTracking)
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 40),
                child: CircularProgressIndicator(color: Color(0xFFE11D48)),
              )
            else if (_hasSearched && apptProv.trackedAppointments.isEmpty)
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  children: [
                    const Icon(Icons.search_off_rounded, size: 48, color: Color(0xFFE11D48)),
                    const SizedBox(height: 8),
                    const Text(
                      'কোনো সক্রিয় সিরিয়াল পাওয়া যায়নি',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      '"${_queryCtrl.text}" নম্বরের সাথে মিল রয়েছে এমন কোনো সক্রিয় বুকিং নেই। অনুগ্রহ করে নম্বরটি যাচাই করুন।',
                      textAlign: TextAlign.center,
                      style: const TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                    ),
                  ],
                ),
              )
            else if (apptProv.trackedAppointments.isNotEmpty)
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 16),
                itemCount: apptProv.trackedAppointments.length,
                itemBuilder: (context, idx) {
                  final apt = apptProv.trackedAppointments[idx];
                  final queue = apptProv.doctorQueueStatuses[apt.doctorId];
                  final currentServing = queue != null ? (queue['current_serving_serial'] ?? 0) : 0;
                  final yourSerial = apt.serialNumber ?? 0;
                  final peopleAhead = (yourSerial > currentServing) ? (yourSerial - currentServing) : 0;
                  final isEmergency = queue != null && (queue['is_emergency_active'] == true || queue['is_emergency_active'] == 1);

                  return Container(
                    margin: const EdgeInsets.only(bottom: 16),
                    padding: const EdgeInsets.all(18),
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
                        // Emergency Bypass Alert if active in doctor chamber
                        if (isEmergency)
                          Container(
                            margin: const EdgeInsets.only(bottom: 12),
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: const Color(0xFFFEF2F2),
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: const Color(0xFFF87171)),
                            ),
                            child: const Row(
                              children: [
                                Text('🚨', style: TextStyle(fontSize: 18)),
                                SizedBox(width: 8),
                                Expanded(
                                  child: Text(
                                    'চেম্বারে জরুরি রোগী প্রবেশ করেছে (Emergency Bypass Active)',
                                    style: TextStyle(color: Color(0xFFDC2626), fontWeight: FontWeight.bold, fontSize: 11),
                                  ),
                                ),
                              ],
                            ),
                          ),

                        // Ticket Header
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  apt.doctorName ?? 'ডাঃ বিশেষজ্ঞ চিকিৎসক',
                                  style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF0F172A)),
                                ),
                                Text(
                                  apt.doctorSpecialization ?? 'চেম্বার কনসালটেশন',
                                  style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                ),
                              ],
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                              decoration: BoxDecoration(
                                color: const Color(0xFFE0F2FE),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Text(
                                apt.status.toUpperCase(),
                                style: const TextStyle(color: Color(0xFF0284C7), fontSize: 10, fontWeight: FontWeight.bold),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 14),

                        // Big Live Token Numbers Card (Hero Gradient)
                        Container(
                          padding: const EdgeInsets.all(18),
                          decoration: BoxDecoration(
                            gradient: const LinearGradient(
                              colors: [Color(0xFF223A66), Color(0xFF0F172A)],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                            borderRadius: BorderRadius.circular(18),
                          ),
                          child: Column(
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceAround,
                                children: [
                                  // Serving Now
                                  Column(
                                    children: [
                                      const Text('বর্তমানে চলছে', style: TextStyle(color: Colors.white70, fontSize: 11)),
                                      const SizedBox(height: 4),
                                      Text(
                                        currentServing > 0 ? '#$currentServing' : '#-',
                                        style: const TextStyle(color: Color(0xFF10B981), fontSize: 36, fontWeight: FontWeight.w900),
                                      ),
                                    ],
                                  ),
                                  Container(height: 40, width: 1, color: Colors.white24),
                                  // Your Serial
                                  Column(
                                    children: [
                                      const Text('আপনার সিরিয়াল', style: TextStyle(color: Colors.white70, fontSize: 11)),
                                      const SizedBox(height: 4),
                                      Text(
                                        '#$yourSerial',
                                        style: const TextStyle(color: Colors.amberAccent, fontSize: 36, fontWeight: FontWeight.w900),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                              const SizedBox(height: 10),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: Colors.white.withValues(alpha: 0.1),
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: Text(
                                  (yourSerial == currentServing && currentServing > 0)
                                      ? '🔔 এখন আপনার ডাক পড়েছে! চেম্বারে প্রবেশ করুন।'
                                      : (peopleAhead > 0)
                                          ? 'আপনার পূর্বে অপেক্ষমাণ: $peopleAhead জন রোগী'
                                          : 'সিরিয়াল অপেক্ষা করছে...',
                                  style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 12),

                        // Appointment Info Details
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text('রেফারেন্স: ${apt.appointmentNumber}', style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                            Text('তারিখ: ${apt.appointmentDate}', style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                          ],
                        ),
                        const SizedBox(height: 10),

                        // Actions: Live Refresh & Video Call Link
                        Row(
                          children: [
                            Expanded(
                              child: OutlinedButton.icon(
                                style: OutlinedButton.styleFrom(
                                  side: const BorderSide(color: Color(0xFFCBD5E1)),
                                  padding: const EdgeInsets.symmetric(vertical: 10),
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                ),
                                onPressed: () {
                                  apptProv.fetchDoctorQueue(apt.doctorId);
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(content: Text('লাইভ কিউ স্ট্যাটাস রিফ্রেশ হয়েছে!'), duration: Duration(seconds: 1)),
                                  );
                                },
                                icon: const Icon(Icons.refresh, size: 16, color: Color(0xFF0F172A)),
                                label: const Text('লাইভ রিফ্রেশ', style: TextStyle(color: Color(0xFF0F172A), fontSize: 12, fontWeight: FontWeight.bold)),
                              ),
                            ),
                            if (apt.meetingLink != null && apt.meetingLink!.isNotEmpty) ...[
                              const SizedBox(width: 8),
                              Expanded(
                                child: ElevatedButton.icon(
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF10B981),
                                    foregroundColor: Colors.white,
                                    padding: const EdgeInsets.symmetric(vertical: 10),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                  ),
                                  onPressed: () => _openVideo(apt.meetingLink!),
                                  icon: const Icon(Icons.video_call, size: 18),
                                  label: const Text('ভিডিও চেম্বার', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                                ),
                              ),
                            ],
                          ],
                        ),
                      ],
                    ),
                  );
                },
              ),

            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }
}
