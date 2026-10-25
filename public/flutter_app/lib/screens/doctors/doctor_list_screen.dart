import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/routes/app_routes.dart';
import '../../providers/doctor_provider.dart';
import '../../widgets/doctor_card.dart';
import '../../widgets/voice_text_field.dart';

class DoctorListScreen extends StatefulWidget {
  const DoctorListScreen({super.key});

  @override
  State<DoctorListScreen> createState() => _DoctorListScreenState();
}

class _DoctorListScreenState extends State<DoctorListScreen> {
  final TextEditingController _searchCtrl = TextEditingController();

  final List<String> _divisions = [
    'All Divisions',
    'Dhaka',
    'Chittagong',
    'Rajshahi',
    'Khulna',
    'Sylhet',
    'Barisal',
    'Rangpur',
    'Mymensingh',
  ];

  String _selectedDiv = 'All Divisions';
  bool _initialized = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!_initialized) {
      _initialized = true;
      final args = ModalRoute.of(context)?.settings.arguments;
      final docProv = Provider.of<DoctorProvider>(context, listen: false);
      if (args is String && args.isNotEmpty) {
        _searchCtrl.text = args;
        docProv.fetchDoctors(query: args);
      } else {
        docProv.fetchDoctors();
      }
      docProv.fetchLocations();
    }
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final docProv = Provider.of<DoctorProvider>(context);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Find Doctors & Specialists'),
        backgroundColor: Colors.white,
        elevation: 0,
      ),
      body: Column(
        children: [
          // Filter Bar
          Container(
            color: Colors.white,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Column(
              children: [
                // Voice-Enabled Doctor Search
                VoiceTextField(
                  controller: _searchCtrl,
                  hintText: 'ডাক্তারের নাম, পদবী বা হাসপাতাল...',
                  prefixIcon: const Icon(Icons.search, color: AppColors.primary),
                  onSubmitted: (val) {
                    docProv.fetchDoctors(query: val.trim());
                  },
                  onChanged: (val) {
                    if (val.isEmpty) {
                      docProv.fetchDoctors();
                    }
                  },
                ),
                const SizedBox(height: 10),
                // Division Selector Chips
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: _divisions.map((div) {
                      final isSelected = _selectedDiv == div;
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: ChoiceChip(
                          label: Text(div),
                          selected: isSelected,
                          selectedColor: AppColors.primary,
                          labelStyle: TextStyle(
                            color: isSelected ? Colors.white : AppColors.textPrimary,
                            fontWeight: FontWeight.w600,
                            fontSize: 12,
                          ),
                          onSelected: (selected) {
                            if (selected) {
                              setState(() => _selectedDiv = div);
                              docProv.fetchDoctors(
                                division: div == 'All Divisions' ? null : div,
                              );
                            }
                          },
                        ),
                      );
                    }).toList(),
                  ),
                ),
              ],
            ),
          ),

          // Results Count & Status
          if (!docProv.isLoading && docProv.doctors.isNotEmpty)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 4),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'মোট ${docProv.doctors.length} জন বিশেষজ্ঞ চিকিৎসক',
                    style: const TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                  if (_searchCtrl.text.isNotEmpty || _selectedDiv != 'All Divisions')
                    GestureDetector(
                      onTap: () {
                        setState(() {
                          _searchCtrl.clear();
                          _selectedDiv = 'All Divisions';
                        });
                        docProv.fetchDoctors();
                      },
                      child: const Text(
                        'ফিল্টার মুছুন ✕',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: Color(0xFFE11D48),
                        ),
                      ),
                    ),
                ],
              ),
            ),

          // Doctors List View
          Expanded(
            child: docProv.isLoading
                ? const Center(child: CircularProgressIndicator())
                : docProv.doctors.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.person_off_rounded, size: 64, color: AppColors.textMuted),
                            const SizedBox(height: 12),
                            const Text(
                              'No doctors found in this area.',
                              style: TextStyle(fontSize: 16, color: AppColors.textSecondary),
                            ),
                          ],
                        ),
                      )
                    : ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: docProv.doctors.length,
                        itemBuilder: (context, index) {
                          final doctor = docProv.doctors[index];
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 16),
                            child: DoctorCard(
                              doctor: doctor,
                              onTap: () {
                                Navigator.pushNamed(context, AppRoutes.doctorDetail, arguments: doctor);
                              },
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}
