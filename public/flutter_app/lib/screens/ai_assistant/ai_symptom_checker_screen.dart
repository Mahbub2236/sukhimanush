import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/routes/app_routes.dart';
import '../../providers/ai_assistant_provider.dart';
import '../../widgets/doctor_card.dart';
import '../../widgets/voice_text_field.dart';

class AiSymptomCheckerScreen extends StatefulWidget {
  const AiSymptomCheckerScreen({super.key});

  @override
  State<AiSymptomCheckerScreen> createState() => _AiSymptomCheckerScreenState();
}

class _AiSymptomCheckerScreenState extends State<AiSymptomCheckerScreen> {
  final TextEditingController _symptomCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AiAssistantProvider>(context, listen: false).fetchQuickSymptoms();
    });
  }

  @override
  void dispose() {
    _symptomCtrl.dispose();
    super.dispose();
  }

  void _triggerAnalysis([String? text]) {
    final query = (text ?? _symptomCtrl.text).trim();
    if (query.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('অনুগ্রহ করে আপনার শারীরিক সমস্যা বা লক্ষণ লিখুন বা নির্বাচন করুন।'),
          backgroundColor: AppColors.primary,
          behavior: SnackBarBehavior.floating,
        ),
      );
      return;
    }
    _symptomCtrl.text = query;
    FocusScope.of(context).unfocus();
    Provider.of<AiAssistantProvider>(context, listen: false).analyzeSymptoms(query);
  }

  @override
  Widget build(BuildContext context) {
    final aiProv = Provider.of<AiAssistantProvider>(context);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('AI Health Symptom Checker'),
        actions: [
          IconButton(
            tooltip: 'রিসেট করুন',
            icon: const Icon(Icons.refresh_rounded),
            onPressed: () {
              aiProv.resetAnalysis();
              _symptomCtrl.clear();
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // AI Header Card
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF223A66), Color(0xFF1E3A8A)],
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF223A66).withOpacity(0.2),
                    blurRadius: 12,
                    offset: const Offset(0, 5),
                  ),
                ],
              ),
              child: const Row(
                children: [
                  Icon(Icons.smart_toy_rounded, color: Colors.white, size: 40),
                  SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'সুখী মানুষ Intelligent Symptom Analyzer',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                        SizedBox(height: 4),
                        Text(
                          'বর্ণনা করুন আপনার শারীরিক সমস্যা বা লক্ষণ (বাংলা বা ইংরেজি)',
                          style: TextStyle(color: Colors.white70, fontSize: 12),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Quick Symptom Suggestions
            const Text(
              'দ্রুত নির্বাচন করুন:',
              style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppColors.primary),
            ),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: aiProv.quickSymptoms.map((sym) {
                final isSelected = _symptomCtrl.text.trim() == sym.trim();
                return ActionChip(
                  label: Text(
                    sym,
                    style: TextStyle(
                      color: isSelected ? Colors.white : AppColors.textPrimary,
                      fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                    ),
                  ),
                  backgroundColor: isSelected ? AppColors.primary : Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20),
                    side: BorderSide(color: isSelected ? AppColors.primary : AppColors.border),
                  ),
                  onPressed: () => _triggerAnalysis(sym),
                );
              }).toList(),
            ),
            const SizedBox(height: 18),

            // Voice-Enabled Symptom Input Box
            VoiceTextField(
              controller: _symptomCtrl,
              maxLines: 4,
              hintText: 'উদা: গত ২ দিন ধরে বুকে চাপ ও অতিরিক্ত ঘাম হচ্ছে... (মাইকে ক্লিক করে বলুন)',
              fillColor: Colors.white,
            ),
            const SizedBox(height: 14),

            // Analyze Button
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton.icon(
                onPressed: aiProv.isAnalyzing ? null : () => _triggerAnalysis(),
                icon: aiProv.isAnalyzing
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                      )
                    : const Icon(Icons.auto_awesome),
                label: Text(
                  aiProv.isAnalyzing ? 'বিশ্লেষণ চলছে...' : 'Analyze Symptoms Now',
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                ),
              ),
            ),

            // Loading Indicator State
            if (aiProv.isAnalyzing) ...[
              const SizedBox(height: 24),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppColors.primary.withOpacity(0.2)),
                ),
                child: const Column(
                  children: [
                    CircularProgressIndicator(color: AppColors.primary),
                    SizedBox(height: 14),
                    Text(
                      'আপনার লক্ষণ বিশ্লেষণ করা হচ্ছে...',
                      style: TextStyle(fontWeight: FontWeight.bold, color: AppColors.primary, fontSize: 15),
                    ),
                    SizedBox(height: 4),
                    Text(
                      'উপযুক্ত বিশেষজ্ঞ নির্বাচন এবং ক্লিনিক্যাল পরামর্শ প্রস্তুত হচ্ছে',
                      style: TextStyle(color: AppColors.textMuted, fontSize: 12),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              ),
            ],

            // Error Display Banner
            if (!aiProv.isAnalyzing && aiProv.errorMessage != null) ...[
              const SizedBox(height: 20),
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF2F2),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: const Color(0xFFF87171)),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.error_outline, color: Color(0xFFDC2626)),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        aiProv.errorMessage!,
                        style: const TextStyle(color: Color(0xFF991B1B), fontSize: 13),
                      ),
                    ),
                    TextButton(
                      onPressed: () => _triggerAnalysis(),
                      child: const Text('আবার চেষ্টা'),
                    ),
                  ],
                ),
              ),
            ],

            const SizedBox(height: 24),

            // AI Results Card
            if (!aiProv.isAnalyzing && aiProv.analysisResult != null) ...[
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: aiProv.analysisResult!.isEmergency ? Colors.red : AppColors.primary.withOpacity(0.3),
                    width: aiProv.analysisResult!.isEmergency ? 2 : 1.5,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: aiProv.analysisResult!.isEmergency
                          ? Colors.red.withOpacity(0.08)
                          : AppColors.primary.withOpacity(0.08),
                      blurRadius: 15,
                      offset: const Offset(0, 6),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Emergency Alert
                    if (aiProv.analysisResult!.isEmergency) ...[
                      Container(
                        padding: const EdgeInsets.all(14),
                        margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFEF2F2),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.red.shade300),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Icon(Icons.warning_amber_rounded, color: Colors.red, size: 28),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text(
                                    '🚨 জরুরি সতর্কবার্তা (Medical Red Flag Alert)',
                                    style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold, fontSize: 14),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    aiProv.analysisResult!.emergencyWarnings.isNotEmpty
                                        ? aiProv.analysisResult!.emergencyWarnings.first
                                        : 'আপনার লক্ষণটি তাৎক্ষণিক জরুরি চিকিৎসার দাবি রাখে। কালক্ষেপণ না করে দ্রুত হাসপাতালে যান বা ৯৯৯ এ কল করুন।',
                                    style: TextStyle(color: Colors.red.shade900, fontSize: 13, height: 1.4),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],

                    // Specialty Header
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: const Color(0xFFEFF6FF),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Icon(Icons.local_hospital_rounded, color: AppColors.primary, size: 24),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'প্রস্তাবিত বিশেষজ্ঞ বিভাগ:',
                                style: TextStyle(fontSize: 12, color: AppColors.textMuted, fontWeight: FontWeight.w600),
                              ),
                              Text(
                                aiProv.analysisResult!.specialtyBn,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w800,
                                  color: AppColors.primary,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const Divider(height: 28),

                    // AI Clinical Advice
                    const Row(
                      children: [
                        Icon(Icons.tips_and_updates_outlined, color: Color(0xFF0284C7), size: 18),
                        SizedBox(width: 6),
                        Text('স্বাস্থ্য মূল্যায়ন ও পরামর্শ:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF8FAFC),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Text(
                        aiProv.analysisResult!.advice,
                        style: const TextStyle(fontSize: 14, height: 1.6, color: Color(0xFF1E293B)),
                      ),
                    ),

                    // Suggested Questions
                    if (aiProv.analysisResult!.suggestedQuestions.isNotEmpty) ...[
                      const SizedBox(height: 16),
                      const Text(
                        'প্রাসঙ্গিক প্রশ্নাবলী (জানতে ট্যাপ করুন):',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.textPrimary),
                      ),
                      const SizedBox(height: 8),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: aiProv.analysisResult!.suggestedQuestions.map((q) {
                          return InkWell(
                            onTap: () => _triggerAnalysis(q),
                            borderRadius: BorderRadius.circular(20),
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                              decoration: BoxDecoration(
                                color: const Color(0xFFF0FDF4),
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(color: const Color(0xFFBBF7D0)),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.help_outline_rounded, size: 14, color: Color(0xFF16A34A)),
                                  const SizedBox(width: 6),
                                  Text(
                                    q,
                                    style: const TextStyle(fontSize: 12, color: Color(0xFF166534), fontWeight: FontWeight.w600),
                                  ),
                                ],
                              ),
                            ),
                          );
                        }).toList(),
                      ),
                    ],

                    const SizedBox(height: 20),

                    // CTA: Explore all doctors in specialty
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton.icon(
                        style: OutlinedButton.styleFrom(
                          foregroundColor: AppColors.primary,
                          side: const BorderSide(color: AppColors.primary),
                          padding: const EdgeInsets.symmetric(vertical: 12),
                        ),
                        onPressed: () => Navigator.pushNamed(context, AppRoutes.doctorList),
                        icon: const Icon(Icons.people_alt_outlined),
                        label: const Text('এই বিভাগের সকল ডাক্তার খুঁজুন'),
                      ),
                    ),
                  ],
                ),
              ),

              // Recommended Doctors List
              if (aiProv.analysisResult!.recommendedDoctors.isNotEmpty) ...[
                const SizedBox(height: 24),
                Row(
                  children: [
                    const Icon(Icons.verified_user_rounded, color: AppColors.primary, size: 20),
                    const SizedBox(width: 8),
                    Text(
                      'নিকটবর্তী বিশেষজ্ঞ ডাক্তারগণ (${aiProv.analysisResult!.recommendedDoctors.length} জন):',
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: AppColors.primary,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                ...aiProv.analysisResult!.recommendedDoctors.map((doc) {
                  return DoctorCard(doctor: doc);
                }),
              ],
            ],
          ],
        ),
      ),
    );
  }
}
