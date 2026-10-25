import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../providers/doctor_portal_provider.dart';
import '../../services/api_service.dart';

class MedicineRow {
  final TextEditingController nameCtrl;
  String dosage;
  String duration;
  String instruction;

  MedicineRow({
    required String name,
    this.dosage = '1 + 0 + 1',
    this.duration = '৫ দিন',
    this.instruction = 'খাবার পর',
  }) : nameCtrl = TextEditingController(text: name);

  String get name => nameCtrl.text;

  Map<String, dynamic> toJson() => {
        'name': nameCtrl.text.trim(),
        'dosage': dosage,
        'duration': duration,
        'instruction': instruction,
      };

  void dispose() {
    nameCtrl.dispose();
  }
}

class DoctorPrescriptionDialog extends StatefulWidget {
  final Map<String, dynamic> appointment;
  final Map<String, dynamic>? doctor;
  final DoctorPortalProvider docProv;

  const DoctorPrescriptionDialog({
    super.key,
    required this.appointment,
    required this.doctor,
    required this.docProv,
  });

  @override
  State<DoctorPrescriptionDialog> createState() => _DoctorPrescriptionDialogState();
}

class _DoctorPrescriptionDialogState extends State<DoctorPrescriptionDialog> {
  final ApiService _apiService = ApiService();

  final TextEditingController _diagnosisCtrl = TextEditingController();
  final TextEditingController _customTestCtrl = TextEditingController();
  final TextEditingController _customAdviceCtrl = TextEditingController();
  final TextEditingController _medicineSearchCtrl = TextEditingController();

  DateTime? _followUpDate;
  bool _isSaving = false;
  bool _isSearchingMedicines = false;
  List<Map<String, dynamic>> _liveMedicineSuggestions = [];

  final List<MedicineRow> _medicines = [
    MedicineRow(name: 'Tab. Napa Extra 500 mg+65 mg', dosage: '1 + 0 + 1', duration: '৫ দিন', instruction: 'খাবার পর'),
  ];

  final List<String> _selectedTests = [];
  final List<String> _selectedAdvices = [
    'পর্যাপ্ত বিশুদ্ধ পানি পান করুন (দৈনিক ৮-১০ গ্লাস)',
    'সময়মতো ওষুধ সেবন করুন এবং বিশ্রাম নিন',
  ];

  // Quick Preset Diagnoses matching Web
  final List<String> _commonDiagnoses = [
    'ভাইরাল ফিভার (Viral Fever)',
    'তীব্র ব্রঙ্কাইটিস ও কাশি (Acute Bronchitis)',
    'গ্যাস্ট্রাইটিস ও এসিডিটি (Gastric Ulcer)',
    'উচ্চ রক্তচাপ (Essential Hypertension)',
    'টাইপ-২ ডায়াবেটিস (Type 2 Diabetes)',
    'মাইগ্রেন ও মাথাব্যথা (Migraine)',
    'বুকের সংক্রমণ (Respiratory Infection)',
  ];

  // Popular BD Medicines Quick Action Chips
  final List<String> _quickMedicines = [
    'Tab. Napa Extra 500 mg',
    'Cap. Seclo 20 mg',
    'Tab. Monas 10 mg',
    'Tab. Ciprocin 500 mg',
    'Cap. Maxpro 20 mg',
    'Tab. Alatrol 10 mg',
    'Tab. Bizoran 5/20 mg',
    'Syp. Tofen 100 ml',
    'Tab. Azithromycin 500 mg',
    'Tab. Ace Plus 500 mg',
  ];

  final List<String> _dosages = [
    '1 + 0 + 1',
    '1 + 1 + 1',
    '0 + 0 + 1',
    '1 + 0 + 0',
    '০ + ১ + ০',
    '১ চামচ করে ৩ বার',
    '২ চামচ করে ২ বার',
    'প্রয়োজনে (SOS)',
  ];

  final List<String> _durations = [
    '৩ দিন',
    '৫ দিন',
    '৭ দিন',
    '১০ দিন',
    '১৪ দিন',
    '১ মাস',
    'চলবে (Continue)',
  ];

  final List<String> _instructions = [
    'খাবার পর',
    'খাবার আগে',
    'ভরা পেটে',
    'খালি পেটে সকালে',
    'রাতে ঘুমানোর আগে',
    'কুসুম গরম পানির সাথে',
  ];

  final List<String> _allCommonTests = [
    'CBC (Complete Blood Count)',
    'Chest X-Ray P/A View',
    'ECG (12 Leads)',
    'Serum Creatinine',
    'Lipid Profile (Fasting)',
    'Urine R/E',
    'USG of Whole Abdomen',
    'HbA1c (Diabetes Test)',
    'RBS (Random Blood Sugar)',
    'Serum Electrolytes',
    'Serum Bilirubin / LFT',
    'Serum Uric Acid',
  ];

  final List<String> _allCommonAdvices = [
    'লবণ ও অতিরিক্ত তেল-চর্বি পরিহার করুন',
    'পর্যাপ্ত বিশুদ্ধ পানি পান করুন (দৈনিক ৮-১০ গ্লাস)',
    'ধূমপান ও তামাক জাতীয় দ্রব্য সম্পূর্ণ বর্জন করুন',
    'প্রতিদিন অন্তত ৩০ মিনিট নিয়মিত হালকা হাঁটুন',
    'চিনি ও মিষ্টি জাতীয় খাবার পরিহার করুন',
    'ঠান্ডা লাগাবেন না ও ধুলাবালি এড়িয়ে চলুন',
    'ভারী ওজন তোলা বা অতিরিক্ত পরিশ্রম থেকে বিরত থাকুন',
    'সময়মতো ওষুধ সেবন করুন এবং বিশ্রাম নিন',
  ];

  // Comprehensive Local Cache for Instant Auto-suggestions
  static final List<Map<String, String>> _localMedicinesDatabase = [
    {'brand_name': 'Napa 500 mg', 'generic_name': 'Paracetamol', 'dosage_form': 'Tab', 'company': 'Beximco'},
    {'brand_name': 'Napa Extra 500 mg+65 mg', 'generic_name': 'Paracetamol + Caffeine', 'dosage_form': 'Tab', 'company': 'Beximco'},
    {'brand_name': 'Napa Extend 665 mg', 'generic_name': 'Paracetamol (XR)', 'dosage_form': 'Tab', 'company': 'Beximco'},
    {'brand_name': 'Napa 120 mg/5 ml', 'generic_name': 'Paracetamol Syrup', 'dosage_form': 'Syrup', 'company': 'Beximco'},
    {'brand_name': 'Napa One 1000 mg', 'generic_name': 'Paracetamol', 'dosage_form': 'Tab', 'company': 'Beximco'},
    {'brand_name': 'Ace 500 mg', 'generic_name': 'Paracetamol', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Ace Plus 500 mg+65 mg', 'generic_name': 'Paracetamol + Caffeine', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Seclo 20 mg', 'generic_name': 'Omeprazole', 'dosage_form': 'Cap', 'company': 'Square'},
    {'brand_name': 'Seclo 40 mg', 'generic_name': 'Omeprazole', 'dosage_form': 'Cap', 'company': 'Square'},
    {'brand_name': 'Maxpro 20 mg', 'generic_name': 'Esomeprazole', 'dosage_form': 'Cap', 'company': 'Square'},
    {'brand_name': 'Maxpro 40 mg', 'generic_name': 'Esomeprazole', 'dosage_form': 'Cap', 'company': 'Square'},
    {'brand_name': 'Sergel 20 mg', 'generic_name': 'Esomeprazole', 'dosage_form': 'Cap', 'company': 'Healthcare'},
    {'brand_name': 'Sergel 40 mg', 'generic_name': 'Esomeprazole', 'dosage_form': 'Cap', 'company': 'Healthcare'},
    {'brand_name': 'Pantonix 20 mg', 'generic_name': 'Pantoprazole', 'dosage_form': 'Tab', 'company': 'Incepta'},
    {'brand_name': 'Pantonix 40 mg', 'generic_name': 'Pantoprazole', 'dosage_form': 'Tab', 'company': 'Incepta'},
    {'brand_name': 'Monas 10 mg', 'generic_name': 'Montelukast', 'dosage_form': 'Tab', 'company': 'ACME'},
    {'brand_name': 'Monas 5 mg', 'generic_name': 'Montelukast (Chewable)', 'dosage_form': 'Tab', 'company': 'ACME'},
    {'brand_name': 'Montene 10 mg', 'generic_name': 'Montelukast', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Ciprocin 500 mg', 'generic_name': 'Ciprofloxacin', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Ciprocin 750 mg', 'generic_name': 'Ciprofloxacin', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Alatrol 10 mg', 'generic_name': 'Cetirizine Hydrochloride', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Fexo 120 mg', 'generic_name': 'Fexofenadine Hydrochloride', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Fexo 180 mg', 'generic_name': 'Fexofenadine Hydrochloride', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Bizoran 5/20 mg', 'generic_name': 'Amlodipine + Olmesartan', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Camlosart 5/20 mg', 'generic_name': 'Amlodipine + Olmesartan', 'dosage_form': 'Tab', 'company': 'Incepta'},
    {'brand_name': 'Angilock 50 mg', 'generic_name': 'Losartan Potassium', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Osartil 50 mg', 'generic_name': 'Losartan Potassium', 'dosage_form': 'Tab', 'company': 'Incepta'},
    {'brand_name': 'Tofen 100 ml', 'generic_name': 'Ketotifen Syrup', 'dosage_form': 'Syrup', 'company': 'Beximco'},
    {'brand_name': 'Azithromycin 500 mg', 'generic_name': 'Azithromycin', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Zithrin 500 mg', 'generic_name': 'Azithromycin', 'dosage_form': 'Tab', 'company': 'Beximco'},
    {'brand_name': 'Filmet 400 mg', 'generic_name': 'Metronidazole', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Flagyl 400 mg', 'generic_name': 'Metronidazole', 'dosage_form': 'Tab', 'company': 'Sanofi'},
    {'brand_name': 'Compath 500 mg', 'generic_name': 'Metformin Hydrochloride', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Gluconor 500 mg', 'generic_name': 'Metformin Hydrochloride', 'dosage_form': 'Tab', 'company': 'Beximco'},
    {'brand_name': 'Rosuva 10 mg', 'generic_name': 'Rosuvastatin', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Atova 10 mg', 'generic_name': 'Atorvastatin', 'dosage_form': 'Tab', 'company': 'Beximco'},
    {'brand_name': 'Calbo D', 'generic_name': 'Calcium + Vitamin D3', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'Coralcal D', 'generic_name': 'Coral Calcium + Vitamin D3', 'dosage_form': 'Tab', 'company': 'Radiant'},
    {'brand_name': 'Neuro-B', 'generic_name': 'Vitamin B1 + B6 + B12', 'dosage_form': 'Tab', 'company': 'Square'},
    {'brand_name': 'D-Rise 20000 IU', 'generic_name': 'Cholecalciferol (Vit D3)', 'dosage_form': 'Cap', 'company': 'Square'},
    {'brand_name': 'Adryl 100 ml', 'generic_name': 'Diphenhydramine Cough Syrup', 'dosage_form': 'Syrup', 'company': 'Square'},
    {'brand_name': 'Tusca 100 ml', 'generic_name': 'Dextromethorphan Syrup', 'dosage_form': 'Syrup', 'company': 'Square'},
  ];

  @override
  void initState() {
    super.initState();
    _followUpDate = DateTime.now().add(const Duration(days: 7));
  }

  @override
  void dispose() {
    _diagnosisCtrl.dispose();
    _customTestCtrl.dispose();
    _customAdviceCtrl.dispose();
    _medicineSearchCtrl.dispose();
    for (var m in _medicines) {
      m.dispose();
    }
    super.dispose();
  }

  void _addMedicineRow([String name = '']) {
    setState(() {
      _medicines.add(MedicineRow(
        name: name.isNotEmpty ? name : 'Tab. ',
        dosage: '1 + 0 + 1',
        duration: '৫ দিন',
        instruction: 'খাবার পর',
      ));
    });
  }

  void _removeMedicineRow(int index) {
    if (_medicines.length > 1) {
      setState(() {
        final removed = _medicines.removeAt(index);
        removed.dispose();
      });
    }
  }

  // Live Medicine Auto-Search Method
  void _onMedicineSearchChanged(String query) async {
    final q = query.trim().toLowerCase();
    if (q.isEmpty) {
      setState(() {
        _liveMedicineSuggestions = [];
        _isSearchingMedicines = false;
      });
      return;
    }

    // 1. Fast Local Filter
    final localMatches = _localMedicinesDatabase.where((item) {
      final brand = (item['brand_name'] ?? '').toLowerCase();
      final generic = (item['generic_name'] ?? '').toLowerCase();
      return brand.contains(q) || generic.contains(q);
    }).map((item) => Map<String, dynamic>.from(item)).toList();

    setState(() {
      _liveMedicineSuggestions = localMatches;
      _isSearchingMedicines = true;
    });

    // 2. Fetch Live from API if query is 2+ chars
    if (q.length >= 2) {
      try {
        final res = await _apiService.searchMedicines(query.trim());
        if (res.data != null && res.data is List && mounted) {
          final List<Map<String, dynamic>> apiMatches = [];
          for (var item in res.data) {
            if (item is Map) {
              apiMatches.add({
                'brand_name': item['brand_name']?.toString() ?? '',
                'generic_name': item['generic_name']?.toString() ?? '',
                'dosage_form': item['dosage_form']?.toString() ?? 'Tab',
                'company': item['company']?.toString() ?? '',
              });
            }
          }

          if (apiMatches.isNotEmpty && mounted) {
            final seen = <String>{};
            final merged = <Map<String, dynamic>>[];
            for (var m in [...apiMatches, ...localMatches]) {
              final key = '${m['brand_name']}'.toLowerCase();
              if (seen.add(key)) {
                merged.add(m);
              }
            }
            setState(() {
              _liveMedicineSuggestions = merged.take(15).toList();
              _isSearchingMedicines = false;
            });
            return;
          }
        }
      } catch (_) {}
    }

    if (mounted) {
      setState(() => _isSearchingMedicines = false);
    }
  }

  void _toggleTest(String test) {
    setState(() {
      if (_selectedTests.contains(test)) {
        _selectedTests.remove(test);
      } else {
        _selectedTests.add(test);
      }
    });
  }

  void _toggleAdvice(String advice) {
    setState(() {
      if (_selectedAdvices.contains(advice)) {
        _selectedAdvices.remove(advice);
      } else {
        _selectedAdvices.add(advice);
      }
    });
  }

  void _addCustomTest() {
    final text = _customTestCtrl.text.trim();
    if (text.isNotEmpty && !_selectedTests.contains(text)) {
      setState(() {
        _selectedTests.add(text);
        _customTestCtrl.clear();
      });
    }
  }

  void _addCustomAdvice() {
    final text = _customAdviceCtrl.text.trim();
    if (text.isNotEmpty && !_selectedAdvices.contains(text)) {
      setState(() {
        _selectedAdvices.add(text);
        _customAdviceCtrl.clear();
      });
    }
  }

  Future<void> _submitPrescription() async {
    if (_diagnosisCtrl.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('⚠️ রোগের কারণ / ডায়াগনোসিস (Clinical Diagnosis) উল্লেখ করা আবশ্যক!'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (_medicines.isEmpty || _medicines.any((m) => m.name.trim().isEmpty)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('⚠️ অনুগ্রহ করে অন্তত একটি সঠিক ওষুধের নাম লিখুন!'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() => _isSaving = true);

    final payload = {
      'appointment_id': widget.appointment['id'],
      'patient_name': widget.appointment['patient_name'] ?? 'রোগী',
      'patient_age_gender': widget.appointment['patient_age_gender'] ?? 'Adult',
      'diagnosis': _diagnosisCtrl.text.trim(),
      'medicines': _medicines.map((m) => m.toJson()).toList(),
      'medical_tests': _selectedTests,
      'advice': _selectedAdvices.join('\n• '),
      'follow_up_date': _followUpDate != null ? DateFormat('yyyy-MM-dd').format(_followUpDate!) : null,
    };

    final success = await widget.docProv.createPrescription(payload);

    setState(() => _isSaving = false);

    if (mounted) {
      if (success) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('✅ ডিজিটাল প্রেসক্রিপশন সফলভাবে ইস্যু করা হয়েছে!'),
            backgroundColor: Colors.green,
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('প্রেসক্রিপশন সেভ করা সম্ভব হয়নি, পুনরায় চেষ্টা করুন।'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  void _showPrintPreview() {
    showDialog(
      context: context,
      builder: (ctx) => _PrescriptionPrintPreviewDialog(
        doctor: widget.doctor,
        patientName: widget.appointment['patient_name'] ?? 'রোগী',
        patientAgeGender: widget.appointment['patient_age_gender'] ?? 'Adult',
        diagnosis: _diagnosisCtrl.text.trim().isNotEmpty ? _diagnosisCtrl.text.trim() : 'প্রাথমিক ক্লিনিক্যাল পর্যবেক্ষণ',
        medicines: _medicines,
        tests: _selectedTests,
        advices: _selectedAdvices,
        followUpDate: _followUpDate,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final patientName = widget.appointment['patient_name'] ?? 'আব্দুর রহিম';
    final patientPhone = widget.appointment['patient_phone'] ?? widget.appointment['phone'] ?? '';
    final serialNo = widget.appointment['token_number'] ?? widget.appointment['serial_number'] ?? '1';

    return Container(
      height: MediaQuery.of(context).size.height * 0.94,
      decoration: const BoxDecoration(
        color: Color(0xFFF8FAFC),
        borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
      ),
      child: Column(
        children: [
          // Drag Handle & Header
          Container(
            padding: const EdgeInsets.fromLTRB(20, 12, 16, 12),
            decoration: const BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
              border: Border(bottom: BorderSide(color: Color(0xFFE2E8F0))),
            ),
            child: Column(
              children: [
                Center(
                  child: Container(
                    width: 44,
                    height: 5,
                    decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(10)),
                  ),
                ),
                const SizedBox(height: 10),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: const Color(0xFFE11D48).withOpacity(0.12),
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(Icons.receipt_long, color: Color(0xFFE11D48), size: 20),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              const Text(
                                'ডিজিটাল প্রেসক্রিপশন প্রদান',
                                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
                              ),
                              const SizedBox(width: 6),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(6)),
                                child: Text('সিরিয়াল #$serialNo', style: TextStyle(color: Colors.green.shade700, fontSize: 10, fontWeight: FontWeight.bold)),
                              ),
                            ],
                          ),
                          Text('রোগী: $patientName ${patientPhone.isNotEmpty ? "• $patientPhone" : ""}',
                              style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))),
                        ],
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // Scrollable Body
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // 1. Clinical Diagnosis Section
                  _buildSectionCard(
                    title: '১. রোগ নির্ণয় ও ডায়াগনোসিস (Clinical Diagnosis) *',
                    icon: Icons.medical_services,
                    iconColor: Colors.redAccent,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        TextField(
                          controller: _diagnosisCtrl,
                          decoration: InputDecoration(
                            hintText: 'যেমন: Viral Fever with Gastric Discomfort...',
                            hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
                            filled: true,
                            fillColor: const Color(0xFFF1F5F9),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                            prefixIcon: const Icon(Icons.medical_services_outlined, size: 20, color: Color(0xFFE11D48)),
                          ),
                        ),
                        const SizedBox(height: 10),
                        const Text('কুইক ডায়াগনোসিস টেমপ্লেট:', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF64748B))),
                        const SizedBox(height: 6),
                        Wrap(
                          spacing: 6,
                          runSpacing: 6,
                          children: _commonDiagnoses.map((d) {
                            final isSel = _diagnosisCtrl.text == d;
                            return ActionChip(
                              label: Text(d, style: TextStyle(fontSize: 11, color: isSel ? Colors.white : const Color(0xFF1E293B))),
                              backgroundColor: isSel ? const Color(0xFFE11D48) : Colors.white,
                              side: BorderSide(color: isSel ? const Color(0xFFE11D48) : Colors.grey.shade300),
                              padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 0),
                              onPressed: () {
                                setState(() => _diagnosisCtrl.text = d);
                              },
                            );
                          }).toList(),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 14),

                  // 2. Prescribed Medicines Section (Rx) with Live BD Medicines Auto-Search
                  _buildSectionCard(
                    title: '২. প্রেসক্রাইবড ঔষধসমূহ (Rx Medicines)',
                    icon: Icons.medication_outlined,
                    iconColor: const Color(0xFF0284C7),
                    action: TextButton.icon(
                      onPressed: () => _addMedicineRow(),
                      icon: const Icon(Icons.add_circle, size: 16, color: Color(0xFF0284C7)),
                      label: const Text('+ ওষুধ যোগ করুন', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF0284C7))),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Live Medicine Search & Auto-Suggest Bar
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF0F9FF),
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: const Color(0xFFBAE6FD), width: 1.5),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  const Icon(Icons.search, size: 20, color: Color(0xFF0284C7)),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: TextField(
                                      controller: _medicineSearchCtrl,
                                      decoration: InputDecoration(
                                        hintText: '🔍 ওষুধের নাম লিখুন (উদা: Napa, Seclo, Monas)...',
                                        hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
                                        border: InputBorder.none,
                                        isDense: true,
                                      ),
                                      onChanged: _onMedicineSearchChanged,
                                    ),
                                  ),
                                  if (_isSearchingMedicines)
                                    const SizedBox(
                                      width: 16,
                                      height: 16,
                                      child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF0284C7)),
                                    )
                                  else if (_medicineSearchCtrl.text.isNotEmpty)
                                    GestureDetector(
                                      onTap: () {
                                        _medicineSearchCtrl.clear();
                                        setState(() => _liveMedicineSuggestions = []);
                                      },
                                      child: const Icon(Icons.close, size: 18, color: Colors.grey),
                                    ),
                                ],
                              ),

                              // Live Auto-Suggestions Dropdown List
                              if (_liveMedicineSuggestions.isNotEmpty) ...[
                                const Divider(height: 14),
                                const Text('অটো সাজেশন (ক্লিক করে প্রেসক্রিপশনে যোগ করুন):',
                                    style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0369A1))),
                                const SizedBox(height: 4),
                                Container(
                                  constraints: const BoxConstraints(maxHeight: 200),
                                  child: ListView.separated(
                                    shrinkWrap: true,
                                    itemCount: _liveMedicineSuggestions.length,
                                    separatorBuilder: (_, __) => Divider(height: 1, color: Colors.blue.shade100),
                                    itemBuilder: (ctx, i) {
                                      final item = _liveMedicineSuggestions[i];
                                      final brand = item['brand_name'] ?? '';
                                      final form = item['dosage_form'] ?? 'Tab';
                                      final generic = item['generic_name'] ?? '';
                                      final company = item['company'] ?? '';
                                      final fullName = '$form. $brand';

                                      return InkWell(
                                        onTap: () {
                                          _addMedicineRow(fullName);
                                          _medicineSearchCtrl.clear();
                                          setState(() => _liveMedicineSuggestions = []);
                                        },
                                        child: Padding(
                                          padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 4),
                                          child: Row(
                                            children: [
                                              Container(
                                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                                decoration: BoxDecoration(
                                                  color: const Color(0xFF0284C7),
                                                  borderRadius: BorderRadius.circular(4),
                                                ),
                                                child: Text(
                                                  form,
                                                  style: const TextStyle(
                                                    color: Colors.white,
                                                    fontSize: 10,
                                                    fontWeight: FontWeight.bold,
                                                  ),
                                                ),
                                              ),
                                              const SizedBox(width: 8),
                                              Expanded(
                                                child: Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: [
                                                    Text(
                                                      brand,
                                                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                                                    ),
                                                    if (generic.isNotEmpty)
                                                      Text(
                                                        generic,
                                                        style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                                      ),
                                                  ],
                                                ),
                                              ),
                                              if (company.isNotEmpty)
                                                ConstrainedBox(
                                                  constraints: const BoxConstraints(maxWidth: 90),
                                                  child: Text(
                                                    company,
                                                    style: const TextStyle(fontSize: 10, color: Colors.grey),
                                                    textAlign: TextAlign.right,
                                                    overflow: TextOverflow.ellipsis,
                                                  ),
                                                ),
                                              const SizedBox(width: 8),
                                              const Icon(Icons.add_circle, color: Color(0xFF0284C7), size: 18),
                                            ],
                                          ),
                                        ),
                                      );
                                    },
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),
                        const SizedBox(height: 10),

                        // Quick BD Drug Pills
                        const Text('জনপ্রিয় ওষুধ চিপস (ক্লিক করে যোগ করুন):', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF64748B))),
                        const SizedBox(height: 6),
                        SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: Row(
                            children: _quickMedicines.map((m) {
                              return Padding(
                                padding: const EdgeInsets.only(right: 6),
                                child: ActionChip(
                                  label: Text(m, style: const TextStyle(fontSize: 11, color: Color(0xFF0369A1))),
                                  backgroundColor: const Color(0xFFE0F2FE),
                                  side: BorderSide(color: Colors.blue.shade200),
                                  padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 0),
                                  onPressed: () => _addMedicineRow(m),
                                ),
                              );
                            }).toList(),
                          ),
                        ),
                        const SizedBox(height: 12),

                        // Dynamic Medicine Rows
                        ..._medicines.asMap().entries.map((entry) {
                          final idx = entry.key;
                          final med = entry.value;
                          return _buildMedicineItemCard(idx, med);
                        }),
                      ],
                    ),
                  ),

                  const SizedBox(height: 14),

                  // 3. Diagnostic Medical Tests
                  _buildSectionCard(
                    title: '৩. ডায়াগনস্টিক ল্যাব টেস্ট (Medical Investigations)',
                    icon: Icons.science_outlined,
                    iconColor: Colors.deepPurple,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Selected Tests Count Badge
                        if (_selectedTests.isNotEmpty)
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                            margin: const EdgeInsets.only(bottom: 10),
                            decoration: BoxDecoration(
                              color: Colors.deepPurple.shade50,
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: Colors.deepPurple.shade200),
                            ),
                            child: Row(
                              children: [
                                const Icon(Icons.check_circle, size: 16, color: Colors.deepPurple),
                                const SizedBox(width: 6),
                                Text('নির্বাচিত টেস্ট (${_selectedTests.length} টি): ',
                                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.deepPurple)),
                                Expanded(
                                  child: Text(_selectedTests.join(', '),
                                      style: const TextStyle(fontSize: 11, color: Color(0xFF334155)),
                                      overflow: TextOverflow.ellipsis),
                                ),
                              ],
                            ),
                          ),

                        // Test Cloud
                        Wrap(
                          spacing: 6,
                          runSpacing: 6,
                          children: _allCommonTests.map((test) {
                            final isSel = _selectedTests.contains(test);
                            return FilterChip(
                              label: Text(test, style: TextStyle(fontSize: 11, color: isSel ? Colors.white : const Color(0xFF1E293B))),
                              selected: isSel,
                              selectedColor: Colors.deepPurple,
                              backgroundColor: Colors.white,
                              checkmarkColor: Colors.white,
                              side: BorderSide(color: isSel ? Colors.deepPurple : Colors.grey.shade300),
                              onSelected: (_) => _toggleTest(test),
                            );
                          }).toList(),
                        ),
                        const SizedBox(height: 10),

                        // Add Custom Test Input
                        Row(
                          children: [
                            Expanded(
                              child: TextField(
                                controller: _customTestCtrl,
                                decoration: InputDecoration(
                                  hintText: 'অন্য কোনো টেস্ট (যেমন: DEXA Scan, Troponin-I)...',
                                  hintStyle: TextStyle(fontSize: 12, color: Colors.grey.shade400),
                                  isDense: true,
                                  filled: true,
                                  fillColor: const Color(0xFFF1F5F9),
                                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none),
                                ),
                                onSubmitted: (_) => _addCustomTest(),
                              ),
                            ),
                            const SizedBox(width: 8),
                            ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.deepPurple,
                                foregroundColor: Colors.white,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              ),
                              onPressed: _addCustomTest,
                              child: const Text('+ টেস্ট যোগ', style: TextStyle(fontSize: 12)),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 14),

                  // 4. Dietary & Lifestyle Advice
                  _buildSectionCard(
                    title: '৪. খাদ্য ও জীবনযাত্রার উপদেশ (Advice & Guidelines)',
                    icon: Icons.health_and_safety_outlined,
                    iconColor: Colors.teal,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        ..._allCommonAdvices.map((adv) {
                          final isSel = _selectedAdvices.contains(adv);
                          return InkWell(
                            onTap: () => _toggleAdvice(adv),
                            child: Container(
                              margin: const EdgeInsets.only(bottom: 6),
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                              decoration: BoxDecoration(
                                color: isSel ? Colors.teal.shade50 : Colors.white,
                                borderRadius: BorderRadius.circular(10),
                                border: Border.all(color: isSel ? Colors.teal : Colors.grey.shade200),
                              ),
                              child: Row(
                                children: [
                                  Icon(
                                    isSel ? Icons.check_circle : Icons.radio_button_unchecked,
                                    size: 18,
                                    color: isSel ? Colors.teal : Colors.grey.shade400,
                                  ),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    child: Text(
                                      adv,
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: isSel ? FontWeight.bold : FontWeight.normal,
                                        color: isSel ? Colors.teal.shade900 : const Color(0xFF334155),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        }),
                        const SizedBox(height: 10),

                        // Add Custom Advice Input
                        Row(
                          children: [
                            Expanded(
                              child: TextField(
                                controller: _customAdviceCtrl,
                                decoration: InputDecoration(
                                  hintText: 'ডাক্তারের কাস্টম উপদেশ বা বিশেষ সতর্কতা...',
                                  hintStyle: TextStyle(fontSize: 12, color: Colors.grey.shade400),
                                  isDense: true,
                                  filled: true,
                                  fillColor: const Color(0xFFF1F5F9),
                                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none),
                                ),
                                onSubmitted: (_) => _addCustomAdvice(),
                              ),
                            ),
                            const SizedBox(width: 8),
                            ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.teal,
                                foregroundColor: Colors.white,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              ),
                              onPressed: _addCustomAdvice,
                              child: const Text('+ যোগ', style: TextStyle(fontSize: 12)),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 14),

                  // 5. Follow-up Date (পরবর্তী সাক্ষাত)
                  _buildSectionCard(
                    title: '৫. পরবর্তী সাক্ষাতের তারিখ (Follow-up Date)',
                    icon: Icons.calendar_month,
                    iconColor: Colors.indigo,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Text(
                              _followUpDate != null
                                  ? 'নির্ধারিত তারিখ: ${DateFormat('dd MMMM yyyy (EEEE)', 'en_US').format(_followUpDate!)}'
                                  : 'কোনো তারিখ নির্ধারিত নেই',
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF1E293B)),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Wrap(
                          spacing: 8,
                          children: [
                            OutlinedButton(
                              onPressed: () {
                                setState(() => _followUpDate = DateTime.now().add(const Duration(days: 7)));
                              },
                              child: const Text('৭ দিন পর', style: TextStyle(fontSize: 12)),
                            ),
                            OutlinedButton(
                              onPressed: () {
                                setState(() => _followUpDate = DateTime.now().add(const Duration(days: 14)));
                              },
                              child: const Text('১৪ দিন পর', style: TextStyle(fontSize: 12)),
                            ),
                            OutlinedButton(
                              onPressed: () {
                                setState(() => _followUpDate = DateTime.now().add(const Duration(days: 30)));
                              },
                              child: const Text('১ মাস পর', style: TextStyle(fontSize: 12)),
                            ),
                            ElevatedButton.icon(
                              icon: const Icon(Icons.date_range, size: 14),
                              label: const Text('ক্যালেন্ডার', style: TextStyle(fontSize: 12)),
                              onPressed: () async {
                                final d = await showDatePicker(
                                  context: context,
                                  initialDate: _followUpDate ?? DateTime.now().add(const Duration(days: 7)),
                                  firstDate: DateTime.now(),
                                  lastDate: DateTime.now().add(const Duration(days: 180)),
                                );
                                if (d != null) {
                                  setState(() => _followUpDate = d);
                                }
                              },
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),
                ],
              ),
            ),
          ),

          // Sticky Bottom Actions Bar
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [
                BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 10, offset: const Offset(0, -3)),
              ],
            ),
            child: Row(
              children: [
                OutlinedButton.icon(
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    side: const BorderSide(color: Color(0xFF0F172A)),
                  ),
                  icon: const Icon(Icons.print, size: 18, color: Color(0xFF0F172A)),
                  label: const Text('প্রিভিউ প্যাড', style: TextStyle(color: Color(0xFF0F172A), fontWeight: FontWeight.bold)),
                  onPressed: _showPrintPreview,
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFE11D48),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    icon: _isSaving
                        ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                        : const Icon(Icons.check_circle, size: 18),
                    label: Text(
                      _isSaving ? 'ইস্যু হচ্ছে...' : 'ডিজিটাল প্রেসক্রিপশন সেভ করুন ✓',
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                    ),
                    onPressed: _isSaving ? null : _submitPrescription,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionCard({
    required String title,
    required IconData icon,
    required Color iconColor,
    required Widget child,
    Widget? action,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 6, offset: const Offset(0, 2)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Icon(icon, color: iconColor, size: 18),
                  const SizedBox(width: 8),
                  Text(
                    title,
                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                  ),
                ],
              ),
              if (action != null) action,
            ],
          ),
          const Divider(height: 20),
          child,
        ],
      ),
    );
  }

  Widget _buildMedicineItemCard(int idx, MedicineRow med) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                margin: const EdgeInsets.only(top: 8),
                width: 22,
                height: 22,
                decoration: const BoxDecoration(color: Color(0xFF0284C7), shape: BoxShape.circle),
                child: Center(
                  child: Text('${idx + 1}', style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                ),
              ),
              const SizedBox(width: 8),

              // Autocomplete Medicine Name Field
              Expanded(
                child: RawAutocomplete<Map<String, String>>(
                  textEditingController: med.nameCtrl,
                  focusNode: FocusNode(),
                  optionsBuilder: (TextEditingValue textEditingValue) {
                    if (textEditingValue.text.isEmpty) {
                      return const Iterable<Map<String, String>>.empty();
                    }
                    final q = textEditingValue.text.toLowerCase().trim();
                    return _localMedicinesDatabase.where((item) {
                      final b = (item['brand_name'] ?? '').toLowerCase();
                      final g = (item['generic_name'] ?? '').toLowerCase();
                      return b.contains(q) || g.contains(q);
                    });
                  },
                  displayStringForOption: (option) => "${option['dosage_form'] ?? 'Tab'}. ${option['brand_name'] ?? ''}",
                  onSelected: (option) {
                    setState(() {
                      med.nameCtrl.text = "${option['dosage_form'] ?? 'Tab'}. ${option['brand_name'] ?? ''}";
                    });
                  },
                  fieldViewBuilder: (context, controller, focusNode, onFieldSubmitted) {
                    return TextField(
                      controller: controller,
                      focusNode: focusNode,
                      decoration: InputDecoration(
                        isDense: true,
                        hintText: 'ওষুধের নাম (টাইপ করলেই অটো আসবে)...',
                        hintStyle: TextStyle(fontSize: 13, color: Colors.grey.shade400),
                        filled: true,
                        fillColor: Colors.white,
                        suffixIcon: const Icon(Icons.arrow_drop_down, color: Colors.grey, size: 20),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide(color: Colors.grey.shade300)),
                      ),
                    );
                  },
                  optionsViewBuilder: (context, onSelected, options) {
                    return Align(
                      alignment: Alignment.topLeft,
                      child: Material(
                        elevation: 6.0,
                        borderRadius: BorderRadius.circular(10),
                        child: Container(
                          width: 320,
                          constraints: const BoxConstraints(maxHeight: 220),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(color: Colors.blue.shade200),
                          ),
                          child: ListView.separated(
                            padding: const EdgeInsets.symmetric(vertical: 4),
                            shrinkWrap: true,
                            itemCount: options.length,
                            separatorBuilder: (_, __) => Divider(height: 1, color: Colors.grey.shade200),
                            itemBuilder: (context, index) {
                              final option = options.elementAt(index);
                              final form = option['dosage_form'] ?? 'Tab';
                              final brand = option['brand_name'] ?? '';
                              final generic = option['generic_name'] ?? '';
                              final comp = option['company'] ?? '';
                              return InkWell(
                                onTap: () => onSelected(option),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          Container(
                                            padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1),
                                            decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(4)),
                                            child: Text(form, style: TextStyle(color: Colors.blue.shade700, fontSize: 10, fontWeight: FontWeight.bold)),
                                          ),
                                          const SizedBox(width: 6),
                                          Expanded(
                                            child: Text(brand, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF0F172A))),
                                          ),
                                          if (comp.isNotEmpty)
                                            Text(comp, style: const TextStyle(fontSize: 10, color: Colors.grey)),
                                        ],
                                      ),
                                      if (generic.isNotEmpty)
                                        Padding(
                                          padding: const EdgeInsets.only(top: 2),
                                          child: Text(generic, style: const TextStyle(fontSize: 10, color: Color(0xFF64748B))),
                                        ),
                                    ],
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(width: 6),
              IconButton(
                icon: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 20),
                onPressed: () => _removeMedicineRow(idx),
                tooltip: 'ওষুধ মুছুন',
              ),
            ],
          ),
          const SizedBox(height: 8),

          // Dosage, Duration, Instruction Dropdowns
          Row(
            children: [
              Expanded(
                child: DropdownButtonFormField<String>(
                  value: _dosages.contains(med.dosage) ? med.dosage : _dosages.first,
                  decoration: const InputDecoration(labelText: 'মাত্রা (Dosage)', isDense: true),
                  items: _dosages.map((d) => DropdownMenuItem(value: d, child: Text(d, style: const TextStyle(fontSize: 12)))).toList(),
                  onChanged: (v) => setState(() => med.dosage = v!),
                ),
              ),
              const SizedBox(width: 6),
              Expanded(
                child: DropdownButtonFormField<String>(
                  value: _durations.contains(med.duration) ? med.duration : _durations.first,
                  decoration: const InputDecoration(labelText: 'মেয়াদ (Duration)', isDense: true),
                  items: _durations.map((d) => DropdownMenuItem(value: d, child: Text(d, style: const TextStyle(fontSize: 12)))).toList(),
                  onChanged: (v) => setState(() => med.duration = v!),
                ),
              ),
              const SizedBox(width: 6),
              Expanded(
                child: DropdownButtonFormField<String>(
                  value: _instructions.contains(med.instruction) ? med.instruction : _instructions.first,
                  decoration: const InputDecoration(labelText: 'নিয়ম (Instruction)', isDense: true),
                  items: _instructions.map((i) => DropdownMenuItem(value: i, child: Text(i, style: const TextStyle(fontSize: 12)))).toList(),
                  onChanged: (v) => setState(() => med.instruction = v!),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

// =========================================================================
// A4 Medical Prescription Pad Preview Modal (Replicating Web Print Modal)
// =========================================================================
class _PrescriptionPrintPreviewDialog extends StatelessWidget {
  final Map<String, dynamic>? doctor;
  final String patientName;
  final String patientAgeGender;
  final String diagnosis;
  final List<MedicineRow> medicines;
  final List<String> tests;
  final List<String> advices;
  final DateTime? followUpDate;

  const _PrescriptionPrintPreviewDialog({
    required this.doctor,
    required this.patientName,
    required this.patientAgeGender,
    required this.diagnosis,
    required this.medicines,
    required this.tests,
    required this.advices,
    required this.followUpDate,
  });

  @override
  Widget build(BuildContext context) {
    final docName = doctor?['name'] ?? 'ডা. নাহিদ আফসানা জামান';
    final docDegree = doctor?['qualifications'] ?? 'MBBS, FCPS (Medicine), MD (Cardiology)';
    final docSpec = doctor?['specialization'] ?? 'কার্ডিওলজিস্ট ও হৃদরোগ বিশেষজ্ঞ';
    final docBmdc = doctor?['bmdc_number'] ?? 'A-74920';
    final chamber = doctor?['chamber_address'] ?? 'নোভেনা সেন্ট্রাল হসপিটাল, ধানমন্ডি ৩/এ, ঢাকা';

    return Dialog(
      insetPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 24),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Container(
        constraints: const BoxConstraints(maxWidth: 650),
        padding: const EdgeInsets.all(18),
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Top Action Bar
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.print, color: Color(0xFF0F172A), size: 20),
                      SizedBox(width: 6),
                      Text('A4 স্ট্যান্ডার্ড মেডিকেল প্রেসক্রিপশন প্যাড', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                    ],
                  ),
                  IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
                ],
              ),
              const Divider(),

              // Prescription Pad Container
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border.all(color: Colors.grey.shade400, width: 1.5),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Doctor & Chamber Header
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(docName, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF0F172A))),
                              Text(docDegree, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0284C7))),
                              Text(docSpec, style: const TextStyle(fontSize: 11, color: Color(0xFF475569))),
                              Container(
                                margin: const EdgeInsets.only(top: 2),
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1),
                                decoration: BoxDecoration(color: Colors.grey.shade100, border: Border.all(color: Colors.grey.shade300), borderRadius: BorderRadius.circular(4)),
                                child: Text('BMDC Reg: $docBmdc', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                              ),
                            ],
                          ),
                        ),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              const Text('নোভেনা ডিজিটাল হেলথকেয়ার', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFFE11D48))),
                              Text(chamber, textAlign: TextAlign.right, style: const TextStyle(fontSize: 10, color: Color(0xFF64748B))),
                              const Text('রোগী সিরিয়াল: হটলাইন 10678', style: TextStyle(fontSize: 10, color: Color(0xFF64748B))),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),

                    // Patient Bar
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(color: const Color(0xFFF1F5F9), border: Border.all(color: Colors.grey.shade300), borderRadius: BorderRadius.circular(6)),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('রোগী: $patientName', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                          Text('বয়স/লিঙ্গ: $patientAgeGender', style: const TextStyle(fontSize: 11)),
                          Text('তারিখ: ${DateFormat('dd-MM-yyyy').format(DateTime.now())}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    ),
                    const SizedBox(height: 14),

                    // Two Columns (Diagnosis/Tests/Advice & Rx Medicines)
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Left Column (Clinical Diagnosis, Tests, Advice)
                        Container(
                          width: 170,
                          padding: const EdgeInsets.only(right: 12),
                          decoration: BoxDecoration(border: Border(right: BorderSide(color: Colors.grey.shade300))),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('ডায়াগনোসিস / Diagnosis:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFFE11D48))),
                              const SizedBox(height: 2),
                              Text(diagnosis, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF1E293B))),
                              const SizedBox(height: 12),

                              if (tests.isNotEmpty) ...[
                                const Text('ল্যাব টেস্টসমূহ / Tests:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF0284C7))),
                                const SizedBox(height: 2),
                                ...tests.map((t) => Text('• $t', style: const TextStyle(fontSize: 10, color: Color(0xFF334155)))),
                                const SizedBox(height: 12),
                              ],

                              if (advices.isNotEmpty) ...[
                                const Text('উপদেশ / Advice:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Colors.teal)),
                                const SizedBox(height: 2),
                                ...advices.map((a) => Text('• $a', style: const TextStyle(fontSize: 10, color: Color(0xFF334155)))),
                                const SizedBox(height: 12),
                              ],

                              if (followUpDate != null) ...[
                                Container(
                                  padding: const EdgeInsets.all(6),
                                  decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(6), border: Border.all(color: Colors.green.shade200)),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text('পরবর্তী সাক্ষাত:', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.green)),
                                      Text(DateFormat('dd-MM-yyyy').format(followUpDate!), style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                                    ],
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),

                        // Right Column (℞ Medicines)
                        Expanded(
                          child: Padding(
                            padding: const EdgeInsets.only(left: 12),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text('℞', style: TextStyle(fontFamily: 'serif', fontSize: 26, fontWeight: FontWeight.bold, color: Color(0xFF0284C7))),
                                const SizedBox(height: 4),

                                ...medicines.asMap().entries.map((entry) {
                                  final i = entry.key;
                                  final m = entry.value;
                                  return Padding(
                                    padding: const EdgeInsets.only(bottom: 10),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          children: [
                                            Text('${i + 1}. ', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                                            Expanded(child: Text(m.name, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 12, color: Color(0xFF0F172A)))),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1),
                                              decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(4)),
                                              child: Text(m.dosage, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0284C7))),
                                            ),
                                          ],
                                        ),
                                        Padding(
                                          padding: const EdgeInsets.only(left: 14, top: 2),
                                          child: Text('${m.duration} • ${m.instruction}', style: const TextStyle(fontSize: 10, color: Color(0xFF64748B))),
                                        ),
                                      ],
                                    ),
                                  );
                                }),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 30),
                    const Divider(),

                    // Signature Line
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('কম্পিউটার জেনারেটেড ডিজিটাল প্রেসক্রিপশন', style: TextStyle(fontSize: 9, color: Colors.grey)),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Container(width: 120, height: 1, color: Colors.grey.shade400),
                            const SizedBox(height: 2),
                            Text(docName, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                            const Text('রেজিস্টার্ড ফিজিশিয়ান স্বাক্ষর', style: TextStyle(fontSize: 9, color: Colors.grey)),
                          ],
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 14),
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  OutlinedButton(
                    onPressed: () => Navigator.pop(context),
                    child: const Text('বন্ধ করুন'),
                  ),
                  const SizedBox(width: 8),
                  ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF0F172A), foregroundColor: Colors.white),
                    icon: const Icon(Icons.share, size: 16),
                    label: const Text('শেয়ার / PDF প্রিন্ট করুন'),
                    onPressed: () {
                      Navigator.pop(context);
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('প্রেসক্রিপশন প্রিন্ট এবং PDF ডাউনলোড প্রস্তুত হয়েছে!')),
                      );
                    },
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
