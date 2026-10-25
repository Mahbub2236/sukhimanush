import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/routes/app_routes.dart';
import '../../models/user_model.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/voice_text_field.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  int _activeTab = 0; // 0 = Standard (Email), 1 = Staff (Phone & PIN)

  final _emailCtrl = TextEditingController(text: 'patient@mahriva.com');
  final _passCtrl = TextEditingController(text: 'password');

  final _staffPhoneCtrl = TextEditingController(text: '01700000001');
  final _staffPinCtrl = TextEditingController(text: '1234');

  bool _obscure = true;

  @override
  void dispose() {
    _emailCtrl.dispose();
    _passCtrl.dispose();
    _staffPhoneCtrl.dispose();
    _staffPinCtrl.dispose();
    super.dispose();
  }

  void _redirectBasedOnRole(UserModel user) {
    if (user.isDoctor) {
      Navigator.pushReplacementNamed(context, AppRoutes.doctorDashboard);
    } else if (user.isStaff) {
      Navigator.pushReplacementNamed(context, AppRoutes.staffDashboard);
    } else if (user.isAgent) {
      Navigator.pushReplacementNamed(context, AppRoutes.agentDashboard);
    } else if (user.isAdmin) {
      Navigator.pushReplacementNamed(context, AppRoutes.adminDashboard);
    } else {
      Navigator.pushReplacementNamed(context, AppRoutes.patientDashboard);
    }
  }

  @override
  Widget build(BuildContext context) {
    final authProv = Provider.of<AuthProvider>(context);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('সাইন ইন (Sign In)'),
        backgroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'সুখী মানুষ (Sukhi Manush)',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.w900,
                color: AppColors.primary,
              ),
            ),
            const SizedBox(height: 6),
            const Text(
              'রোগী, ডাক্তার, চেম্বার সহকারী, গ্রামীণ এজেন্ট ও অ্যাডমিন - সকলের জন্য সম্পূর্ণ অ্যাপ সার্ভিস।',
              style: TextStyle(fontSize: 13, color: AppColors.textSecondary),
            ),
            const SizedBox(height: 24),

            // Tab Switcher between Email and Staff PIN
            Container(
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(12),
              ),
              padding: const EdgeInsets.all(4),
              child: Row(
                children: [
                  Expanded(
                    child: GestureDetector(
                      onTap: () => setState(() => _activeTab = 0),
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 10),
                        decoration: BoxDecoration(
                          color: _activeTab == 0 ? Colors.white : Colors.transparent,
                          borderRadius: BorderRadius.circular(10),
                          boxShadow: _activeTab == 0
                              ? [BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 4)]
                              : null,
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          'সাধারণ লগইন (ইমেইল)',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: _activeTab == 0 ? AppColors.primary : Colors.grey.shade600,
                          ),
                        ),
                      ),
                    ),
                  ),
                  Expanded(
                    child: GestureDetector(
                      onTap: () => setState(() => _activeTab = 1),
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 10),
                        decoration: BoxDecoration(
                          color: _activeTab == 1 ? Colors.white : Colors.transparent,
                          borderRadius: BorderRadius.circular(10),
                          boxShadow: _activeTab == 1
                              ? [BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 4)]
                              : null,
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          'সহকারী / স্টাফ (পিন)',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: _activeTab == 1 ? Colors.teal : Colors.grey.shade600,
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            if (authProv.errorMessage != null)
              Container(
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 20),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.red.shade200),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.error_outline, color: Colors.red, size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        authProv.errorMessage!,
                        style: const TextStyle(color: Colors.red, fontSize: 13),
                      ),
                    ),
                  ],
                ),
              ),

            // Tab 0: Standard Email/Password
            if (_activeTab == 0) ...[
              VoiceTextField(
                controller: _emailCtrl,
                labelText: 'ইমেইল অ্যাড্রেস (Email Address)',
                hintText: 'ইমেইল মুখে বলুন বা লিখুন...',
                prefixIcon: const Icon(Icons.email_outlined, color: AppColors.primary),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: _passCtrl,
                obscureText: _obscure,
                decoration: InputDecoration(
                  labelText: 'পাসওয়ার্ড (Password)',
                  prefixIcon: const Icon(Icons.lock_outline, color: AppColors.primary),
                  suffixIcon: IconButton(
                    icon: Icon(_obscure ? Icons.visibility_off : Icons.visibility),
                    onPressed: () => setState(() => _obscure = !_obscure),
                  ),
                ),
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: authProv.isLoading
                      ? null
                      : () async {
                          bool ok = await authProv.login(_emailCtrl.text.trim(), _passCtrl.text);
                          if (ok && mounted && authProv.currentUser != null) {
                            _redirectBasedOnRole(authProv.currentUser!);
                          }
                        },
                  child: authProv.isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text('সাইন ইন করুন (Sign In)'),
                ),
              ),
            ] else ...[
              // Tab 1: Staff Phone + PIN
              TextField(
                controller: _staffPhoneCtrl,
                keyboardType: TextInputType.phone,
                decoration: const InputDecoration(
                  labelText: 'সহকারীর মোবাইল নম্বর (Mobile Number)',
                  hintText: '017XXXXXXXX',
                  prefixIcon: Icon(Icons.phone_outlined, color: Colors.teal),
                ),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: _staffPinCtrl,
                keyboardType: TextInputType.number,
                obscureText: true,
                maxLength: 6,
                decoration: const InputDecoration(
                  labelText: 'অ্যাক্সেস পিন (Access PIN)',
                  hintText: '৪ বা ৬ ডিজিটের গোপন পিন',
                  prefixIcon: Icon(Icons.pin_outlined, color: Colors.teal),
                ),
              ),
              const SizedBox(height: 16),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: authProv.isLoading
                      ? null
                      : () async {
                          bool ok = await authProv.staffLogin(
                            _staffPhoneCtrl.text.trim(),
                            _staffPinCtrl.text.trim(),
                          );
                          if (ok && mounted && authProv.currentUser != null) {
                            _redirectBasedOnRole(authProv.currentUser!);
                          }
                        },
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.teal),
                  child: authProv.isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text('স্টাফ পোর্টালে প্রবেশ করুন'),
                ),
              ),
            ],

            const SizedBox(height: 28),

            // One-Tap Quick Test Account Switcher
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: AppColors.background,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    '⚡ এক ক্লিকে পরীক্ষামূলক অ্যাকাউন্ট নির্বাচন করুন:',
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
                  ),
                  const SizedBox(height: 10),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [
                      _roleChip('🧑 রোগী (Patient)', () {
                        setState(() {
                          _activeTab = 0;
                          _emailCtrl.text = 'patient@mahriva.com';
                          _passCtrl.text = 'password';
                        });
                      }),
                      _roleChip('🩺 ডাক্তার (Doctor)', () {
                        setState(() {
                          _activeTab = 0;
                          _emailCtrl.text = 'dr.thomas@mahriva.com';
                          _passCtrl.text = 'password';
                        });
                      }),
                      _roleChip('👥 স্টাফ (Staff PIN)', () {
                        setState(() {
                          _activeTab = 1;
                          _staffPhoneCtrl.text = '01700000001';
                          _staffPinCtrl.text = '1234';
                        });
                      }),
                      _roleChip('🏪 গ্রামীণ এজেন্ট (Agent)', () {
                        setState(() {
                          _activeTab = 0;
                          _emailCtrl.text = 'agent@mahriva.com';
                          _passCtrl.text = 'password';
                        });
                      }),
                      _roleChip('🛡️ অ্যাডমিন (Admin)', () {
                        setState(() {
                          _activeTab = 0;
                          _emailCtrl.text = 'admin@mahriva.com';
                          _passCtrl.text = 'password';
                        });
                      }),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _roleChip(String label, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(8),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: Colors.grey.shade300),
        ),
        child: Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600)),
      ),
    );
  }
}
