import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/routes/app_routes.dart';
import '../../models/doctor_model.dart';
import '../../providers/appointment_provider.dart';
import '../../providers/home_provider.dart';
import '../../services/api_service.dart';
import '../../widgets/voice_text_field.dart';

class AppointmentBookingScreen extends StatefulWidget {
  const AppointmentBookingScreen({super.key});

  @override
  State<AppointmentBookingScreen> createState() => _AppointmentBookingScreenState();
}

class _AppointmentBookingScreenState extends State<AppointmentBookingScreen> {
  int _currentStep = 0;
  DoctorModel? _doctor;
  bool _initialized = false;

  final _nameCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _ageCtrl = TextEditingController(text: '28');
  final _problemCtrl = TextEditingController();

  String _gender = 'Male';
  String _paymentMethod = 'cash'; // cash, bkash, nagad
  String _selectedSlot = '';
  DateTime _selectedDate = DateTime.now().add(const Duration(days: 1));
  ChamberModel? _selectedChamber;

  List<String> _dynamicSlots = [];
  bool _isLoadingSlots = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!_initialized) {
      _initialized = true;
      final args = ModalRoute.of(context)?.settings.arguments;
      if (args is DoctorModel) {
        _doctor = args;
      } else {
        final homeProv = Provider.of<HomeProvider>(context, listen: false);
        _doctor = homeProv.heroDoctor;
      }

      if (_doctor != null && _doctor!.chambers.isNotEmpty) {
        _selectedChamber = _doctor!.chambers.first;
      }

      _fetchSlotsForDate(_selectedDate);
    }
  }

  Future<void> _fetchSlotsForDate(DateTime date) async {
    if (_doctor == null) return;
    setState(() => _isLoadingSlots = true);
    final dateStr = date.toIso8601String().split('T').first;

    try {
      final res = await ApiService().getDoctorSlots(_doctor!.id, date: dateStr);
      var slots = res.data['slots'] as List? ?? [];
      setState(() {
        _dynamicSlots = slots.map((s) => s.toString()).toList();
        if (_dynamicSlots.isNotEmpty) {
          _selectedSlot = _dynamicSlots.first;
        } else {
          _dynamicSlots = ['10:00 AM', '11:00 AM', '04:30 PM', '06:00 PM', '07:30 PM'];
          _selectedSlot = _dynamicSlots.first;
        }
      });
    } catch (_) {
      setState(() {
        _dynamicSlots = ['10:00 AM', '11:00 AM', '04:30 PM', '06:00 PM', '07:30 PM'];
        _selectedSlot = _dynamicSlots.first;
      });
    } finally {
      setState(() => _isLoadingSlots = false);
    }
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    _emailCtrl.dispose();
    _ageCtrl.dispose();
    _problemCtrl.dispose();
    super.dispose();
  }

  void _showConfirmationDialog(dynamic apt) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(16),
              decoration: const BoxDecoration(
                color: Color(0xFFDCFCE7),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.check_circle_rounded, color: Color(0xFF16A34A), size: 48),
            ),
            const SizedBox(height: 14),
            const Text(
              'সিরিয়াল বুকিং নিশ্চিত হয়েছে!',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
            ),
            const SizedBox(height: 6),
            Text(
              'আপনার বুকিং রেফারেন্স: ${apt.appointmentNumber}\nসিরিয়াল নম্বর: #${apt.serialNumber ?? 1}',
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 13, color: Color(0xFF64748B), height: 1.4),
            ),
            const SizedBox(height: 20),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () {
                      Navigator.pop(ctx);
                      Navigator.pushNamed(context, AppRoutes.landing);
                    },
                    child: const Text('হোমে যান'),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0284C7),
                      foregroundColor: Colors.white,
                    ),
                    onPressed: () {
                      Navigator.pop(ctx);
                      Navigator.pushNamed(context, AppRoutes.liveQueue);
                    },
                    child: const Text('লাইভ কিউ ট্র্যাক', style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final apptProv = Provider.of<AppointmentProvider>(context);
    final doctor = _doctor;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('সিরিয়াল বুকিং (Appointment)'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF0F172A),
        elevation: 0,
      ),
      body: doctor == null
          ? const Center(child: Text('কোনো ডাক্তার নির্বাচিত হয়নি।'))
          : SingleChildScrollView(
              child: Column(
                children: [
                  // Doctor Header Summary Card
                  Container(
                    margin: const EdgeInsets.all(16),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
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
                        ClipRRect(
                          borderRadius: BorderRadius.circular(16),
                          child: doctor.image != null
                              ? Image.network(
                                  doctor.image!,
                                  width: 60,
                                  height: 60,
                                  fit: BoxFit.cover,
                                  errorBuilder: (_, __, ___) => _avatarFallback(),
                                )
                              : _avatarFallback(),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                doctor.name,
                                style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: Color(0xFF0F172A)),
                              ),
                              Text(
                                doctor.specialization,
                                style: const TextStyle(fontSize: 12, color: Color(0xFF0284C7), fontWeight: FontWeight.bold),
                              ),
                              Text(
                                'ভিজিট ফি: ৳${doctor.consultationFee.toInt()}',
                                style: const TextStyle(fontSize: 12, color: Color(0xFF059669), fontWeight: FontWeight.w900),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),

                  // Stepper Container
                  Theme(
                    data: Theme.of(context).copyWith(
                      colorScheme: const ColorScheme.light(primary: Color(0xFF0284C7)),
                    ),
                    child: Stepper(
                      physics: const NeverScrollableScrollPhysics(),
                      currentStep: _currentStep,
                      elevation: 0,
                      onStepContinue: () {
                        if (_currentStep == 0) {
                          if (_nameCtrl.text.trim().isEmpty || _phoneCtrl.text.trim().isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(content: Text('রোগীর নাম এবং মোবাইল নম্বর প্রদান করুন।')),
                            );
                            return;
                          }
                          setState(() => _currentStep = 1);
                        } else if (_currentStep == 1) {
                          setState(() => _currentStep = 2);
                        }
                      },
                      onStepCancel: () {
                        if (_currentStep > 0) {
                          setState(() => _currentStep--);
                        }
                      },
                      controlsBuilder: (context, details) {
                        return Padding(
                          padding: const EdgeInsets.only(top: 20),
                          child: Row(
                            children: [
                              if (_currentStep < 2)
                                Expanded(
                                  child: ElevatedButton(
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: const Color(0xFF0284C7),
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(vertical: 12),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                    ),
                                    onPressed: details.onStepContinue,
                                    child: const Text('পরবর্তী ধাপ →', style: TextStyle(fontWeight: FontWeight.bold)),
                                  ),
                                ),
                              if (_currentStep == 2)
                                Expanded(
                                  child: ElevatedButton(
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: const Color(0xFF059669),
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(vertical: 12),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                    ),
                                    onPressed: apptProv.isSubmitting
                                        ? null
                                        : () async {
                                            final bookingData = {
                                              'doctor_id': doctor.id,
                                              'doctor_chamber_id': _selectedChamber?.id,
                                              'patient_name': _nameCtrl.text.trim(),
                                              'patient_phone': _phoneCtrl.text.trim(),
                                              'patient_email': _emailCtrl.text.trim().isNotEmpty
                                                  ? _emailCtrl.text.trim()
                                                  : 'patient_${DateTime.now().millisecondsSinceEpoch}@novena.com',
                                              'patient_gender': _gender,
                                              'patient_age': int.tryParse(_ageCtrl.text) ?? 30,
                                              'appointment_date': _selectedDate.toIso8601String().split('T').first,
                                              'time_slot': _selectedSlot,
                                              'appointment_time': _selectedSlot,
                                              'reason_for_visit': _problemCtrl.text.trim(),
                                              'disease_description': _problemCtrl.text.trim(),
                                              'payment_method': _paymentMethod,
                                            };

                                            bool ok = await apptProv.bookAppointment(bookingData);
                                            if (ok && mounted && apptProv.createdAppointment != null) {
                                              _showConfirmationDialog(apptProv.createdAppointment!);
                                            }
                                          },
                                    child: apptProv.isSubmitting
                                        ? const SizedBox(
                                            width: 18,
                                            height: 18,
                                            child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                                          )
                                        : const Text('সিরিয়াল নিশ্চিত করুন ✓', style: TextStyle(fontWeight: FontWeight.bold)),
                                  ),
                                ),
                              if (_currentStep > 0) ...[
                                const SizedBox(width: 10),
                                OutlinedButton(
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(vertical: 12),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                  ),
                                  onPressed: details.onStepCancel,
                                  child: const Text('পেছনে'),
                                ),
                              ],
                            ],
                          ),
                        );
                      },
                      steps: [
                        // STEP 1: PATIENT INFO
                        Step(
                          title: const Text('রোগীর তথ্য', style: TextStyle(fontWeight: FontWeight.bold)),
                          isActive: _currentStep >= 0,
                          state: _currentStep > 0 ? StepState.complete : StepState.indexed,
                          content: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              VoiceTextField(
                                controller: _nameCtrl,
                                labelText: 'রোগীর পূর্ণ নাম *',
                                hintText: 'যেমন: মোহাম্মদ করিম',
                              ),
                              const SizedBox(height: 10),
                              VoiceTextField(
                                controller: _phoneCtrl,
                                keyboardType: TextInputType.phone,
                                labelText: 'মোবাইল নম্বর *',
                                hintText: '017...',
                              ),
                              const SizedBox(height: 10),
                              Row(
                                children: [
                                  Expanded(
                                    child: VoiceTextField(
                                      controller: _ageCtrl,
                                      keyboardType: TextInputType.number,
                                      labelText: 'বয়স *',
                                      hintText: '২৮',
                                    ),
                                  ),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    child: DropdownButtonFormField<String>(
                                      value: _gender,
                                      decoration: const InputDecoration(labelText: 'লিঙ্গ'),
                                      items: const [
                                        DropdownMenuItem(value: 'Male', child: Text('পুরুষ')),
                                        DropdownMenuItem(value: 'Female', child: Text('মহিলা')),
                                        DropdownMenuItem(value: 'Other', child: Text('অন্যান্য')),
                                      ],
                                      onChanged: (val) => setState(() => _gender = val!),
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 10),
                              VoiceTextField(
                                controller: _problemCtrl,
                                labelText: 'শারীরিক সমস্যা / লক্ষণ',
                                hintText: 'সমস্যা মুখে বলুন বা লিখুন...',
                              ),
                            ],
                          ),
                        ),

                        // STEP 2: DATE & TIME SLOTS
                        Step(
                          title: const Text('তারিখ ও সময়', style: TextStyle(fontWeight: FontWeight.bold)),
                          isActive: _currentStep >= 1,
                          state: _currentStep > 1 ? StepState.complete : StepState.indexed,
                          content: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Chamber selection if multiple
                              if (doctor.chambers.isNotEmpty) ...[
                                const Text('চেম্বার নির্বাচন করুন:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                                const SizedBox(height: 6),
                                DropdownButtonFormField<ChamberModel>(
                                  value: _selectedChamber ?? doctor.chambers.first,
                                  decoration: const InputDecoration(labelText: 'চেম্বার'),
                                  items: doctor.chambers
                                      .map((c) => DropdownMenuItem(value: c, child: Text(c.chamberName, maxLines: 1)))
                                      .toList(),
                                  onChanged: (val) => setState(() => _selectedChamber = val),
                                ),
                                const SizedBox(height: 12),
                              ],

                              // Date selector
                              const Text('অ্যাপয়েন্টমেন্টের তারিখ:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                              const SizedBox(height: 6),
                              InkWell(
                                onTap: () async {
                                  final picked = await showDatePicker(
                                    context: context,
                                    initialDate: _selectedDate,
                                    firstDate: DateTime.now(),
                                    lastDate: DateTime.now().add(const Duration(days: 30)),
                                  );
                                  if (picked != null) {
                                    setState(() => _selectedDate = picked);
                                    _fetchSlotsForDate(picked);
                                  }
                                },
                                child: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(color: const Color(0xFFCBD5E1)),
                                  ),
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        '${_selectedDate.day}/${_selectedDate.month}/${_selectedDate.year}',
                                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                      ),
                                      const Icon(Icons.calendar_month, color: Color(0xFF0284C7)),
                                    ],
                                  ),
                                ),
                              ),
                              const SizedBox(height: 14),

                              // Available Time Slots
                              const Text('সময় স্লট নির্বাচন করুন:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                              const SizedBox(height: 8),
                              if (_isLoadingSlots)
                                const Center(child: Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator()))
                              else
                                Wrap(
                                  spacing: 8,
                                  runSpacing: 8,
                                  children: _dynamicSlots.map((slot) {
                                    final isSelected = _selectedSlot == slot;
                                    return ChoiceChip(
                                      label: Text(slot),
                                      selected: isSelected,
                                      selectedColor: const Color(0xFF0284C7),
                                      labelStyle: TextStyle(
                                        color: isSelected ? Colors.white : const Color(0xFF0F172A),
                                        fontWeight: FontWeight.bold,
                                        fontSize: 11,
                                      ),
                                      onSelected: (sel) {
                                        if (sel) setState(() => _selectedSlot = slot);
                                      },
                                    );
                                  }).toList(),
                                ),
                            ],
                          ),
                        ),

                        // STEP 3: PAYMENT & CONFIRMATION
                        Step(
                          title: const Text('পেমেন্ট ও নিশ্চিতকরণ', style: TextStyle(fontWeight: FontWeight.bold)),
                          isActive: _currentStep >= 2,
                          state: StepState.indexed,
                          content: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                padding: const EdgeInsets.all(14),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF1F5F9),
                                  borderRadius: BorderRadius.circular(14),
                                ),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    const Text('সর্বমোট কনসালটেশন ফি:', style: TextStyle(fontWeight: FontWeight.bold)),
                                    Text(
                                      '৳${doctor.consultationFee.toInt()}',
                                      style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: Color(0xFF059669)),
                                    ),
                                  ],
                                ),
                              ),
                              const SizedBox(height: 14),
                              const Text('পেমেন্ট মাধ্যম:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                              const SizedBox(height: 6),
                              Row(
                                children: [
                                  Expanded(
                                    child: RadioListTile<String>(
                                      contentPadding: EdgeInsets.zero,
                                      title: const Text('চেম্বারে ক্যাশ', style: TextStyle(fontSize: 13)),
                                      value: 'cash',
                                      groupValue: _paymentMethod,
                                      onChanged: (val) => setState(() => _paymentMethod = val!),
                                    ),
                                  ),
                                  Expanded(
                                    child: RadioListTile<String>(
                                      contentPadding: EdgeInsets.zero,
                                      title: const Text('বিকাশ / নগদ', style: TextStyle(fontSize: 13)),
                                      value: 'bkash',
                                      groupValue: _paymentMethod,
                                      onChanged: (val) => setState(() => _paymentMethod = val!),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 40),
                ],
              ),
            ),
    );
  }

  Widget _avatarFallback() {
    return Container(
      width: 60,
      height: 60,
      color: const Color(0xFFEFF6FF),
      child: const Icon(Icons.person, color: Color(0xFF0284C7), size: 30),
    );
  }
}
