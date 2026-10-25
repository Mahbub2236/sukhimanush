import 'package:flutter/material.dart';

class AppColors {
  // Brand Primary & Accents (Matches Novena Web Styling)
  static const Color primary = Color(0xFF223A66);       // Deep Hospital Navy
  static const Color primaryDark = Color(0xFF142442);   // Dark Slate Navy
  static const Color primaryLight = Color(0xFF33538A);  // Soft Navy

  static const Color accent = Color(0xFFE12454);        // Medical Crimson Red (Novena Red)
  static const Color accentLight = Color(0xFFFF5277);   // Vivid Rose Red
  static const Color accentSoft = Color(0xFFFFE8EE);    // Light Rose Tint

  // Functional & State Colors
  static const Color success = Color(0xFF28A745);
  static const Color successLight = Color(0xFFE8F5E9);
  static const Color warning = Color(0xFFFF9800);
  static const Color warningLight = Color(0xFFFFF3E0);
  static const Color info = Color(0xFF0288D1);
  static const Color infoLight = Color(0xFFE1F5FE);
  static const Color error = Color(0xFFDC3545);

  // Neutral Colors
  static const Color background = Color(0xFFF7FAFD);    // Ultra Clean Crisp White/Blue
  static const Color cardBg = Color(0xFFFFFFFF);
  static const Color textPrimary = Color(0xFF223A66);
  static const Color textSecondary = Color(0xFF6F8BA4);
  static const Color textMuted = Color(0xFF9EAFBE);
  static const Color border = Color(0xFFE2E8F0);
  static const Color divider = Color(0xFFEDF2F7);

  // Dark Mode Tokens
  static const Color darkBackground = Color(0xFF0F172A);
  static const Color darkCard = Color(0xFF1E293B);
  static const Color darkTextPrimary = Color(0xFFF8FAFC);
  static const Color darkTextSecondary = Color(0xFF94A3B8);
  static const Color darkBorder = Color(0xFF334155);

  // Gradients for WOW visual appeal
  static const LinearGradient heroGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFF223A66), Color(0xFF182B4D)],
  );

  static const LinearGradient accentGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFFE12454), Color(0xFFFF5277)],
  );

  static const LinearGradient glassGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [
      Color(0x33FFFFFF),
      Color(0x0DFFFFFF),
    ],
  );

  static const LinearGradient emergencyGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFFDC2626), Color(0xFF991B1B)],
  );
}
