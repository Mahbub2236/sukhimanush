import 'package:flutter/material.dart';
import '../services/voice_service.dart';

class VoiceTextField extends StatefulWidget {
  final TextEditingController controller;
  final String? hintText;
  final String? labelText;
  final Widget? prefixIcon;
  final int maxLines;
  final TextInputType keyboardType;
  final bool obscureText;
  final ValueChanged<String>? onSubmitted;
  final ValueChanged<String>? onChanged;
  final Color? fillColor;
  final String languageCode; // 'bn_BD' or 'en_US'

  const VoiceTextField({
    super.key,
    required this.controller,
    this.hintText,
    this.labelText,
    this.prefixIcon,
    this.maxLines = 1,
    this.keyboardType = TextInputType.text,
    this.obscureText = false,
    this.onSubmitted,
    this.onChanged,
    this.fillColor,
    this.languageCode = 'bn_BD',
  });

  @override
  State<VoiceTextField> createState() => _VoiceTextFieldState();
}

class _VoiceTextFieldState extends State<VoiceTextField> with SingleTickerProviderStateMixin {
  final VoiceService _voiceService = VoiceService();
  bool _isListening = false;
  late AnimationController _animCtrl;

  @override
  void initState() {
    super.initState();
    _animCtrl = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _animCtrl.dispose();
    if (_isListening) {
      _voiceService.stopListening();
    }
    super.dispose();
  }

  String _baseText = '';

  void _toggleVoiceListening() async {
    if (_isListening) {
      await _voiceService.stopListening();
      if (mounted) {
        setState(() => _isListening = false);
        if (widget.onSubmitted != null && widget.controller.text.isNotEmpty) {
          widget.onSubmitted!(widget.controller.text.trim());
        }
      }
      return;
    }

    _baseText = widget.controller.text.trim();
    setState(() => _isListening = true);

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            const Icon(Icons.mic, color: Colors.white, size: 20),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                widget.languageCode == 'bn_BD'
                    ? 'কথা বলুন (বাংলা ভয়েস ইনপুট শুনছি...)'
                    : 'Speak now (Listening...)',
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
        backgroundColor: const Color(0xFFE11D48),
        duration: const Duration(seconds: 4),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    );

    await _voiceService.startListening(
      localeId: widget.languageCode,
      onResult: (spokenText) {
        if (spokenText.isNotEmpty && mounted) {
          final cleanSpoken = spokenText.trim();
          setState(() {
            if (_baseText.isEmpty) {
              widget.controller.text = cleanSpoken;
            } else {
              widget.controller.text = '$_baseText $cleanSpoken';
            }
            widget.controller.selection = TextSelection.fromPosition(
              TextPosition(offset: widget.controller.text.length),
            );
          });
          if (widget.onChanged != null) {
            widget.onChanged!(widget.controller.text);
          }
        }
      },
    );

    // Auto reset state and submit when listening ends
    Future.delayed(const Duration(seconds: 5), () {
      if (mounted && _isListening) {
        setState(() => _isListening = false);
        if (widget.onSubmitted != null && widget.controller.text.isNotEmpty) {
          widget.onSubmitted!(widget.controller.text.trim());
        }
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: widget.controller,
      maxLines: widget.maxLines,
      keyboardType: widget.keyboardType,
      obscureText: widget.obscureText,
      onSubmitted: widget.onSubmitted,
      onChanged: widget.onChanged,
      decoration: InputDecoration(
        hintText: widget.hintText,
        labelText: widget.labelText,
        prefixIcon: widget.prefixIcon,
        filled: true,
        fillColor: widget.fillColor ?? Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFF0284C7), width: 1.5),
        ),
        suffixIcon: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (widget.controller.text.isNotEmpty)
              IconButton(
                icon: const Icon(Icons.clear, size: 18, color: Color(0xFF94A3B8)),
                onPressed: () {
                  widget.controller.clear();
                  setState(() {});
                  if (widget.onChanged != null) widget.onChanged!('');
                },
              ),
            // Dedicated Voice Input Button
            Tooltip(
              message: 'ভয়েস দিয়ে টাইপ করুন (Voice Input)',
              child: GestureDetector(
                onTap: _toggleVoiceListening,
                child: AnimatedBuilder(
                  animation: _animCtrl,
                  builder: (context, child) {
                    return Container(
                      margin: const EdgeInsets.only(right: 8),
                      padding: const EdgeInsets.all(7),
                      decoration: BoxDecoration(
                        color: _isListening
                            ? Color.lerp(const Color(0xFFE11D48), const Color(0xFFFDA4AF), _animCtrl.value)
                            : const Color(0xFFF1F5F9),
                        shape: BoxShape.circle,
                        boxShadow: _isListening
                            ? [
                                BoxShadow(
                                  color: const Color(0xFFE11D48).withValues(alpha: 0.4),
                                  blurRadius: 10,
                                  spreadRadius: 2,
                                ),
                              ]
                            : null,
                      ),
                      child: Icon(
                        _isListening ? Icons.mic : Icons.mic_none_rounded,
                        color: _isListening ? Colors.white : const Color(0xFF0284C7),
                        size: 20,
                      ),
                    );
                  },
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
