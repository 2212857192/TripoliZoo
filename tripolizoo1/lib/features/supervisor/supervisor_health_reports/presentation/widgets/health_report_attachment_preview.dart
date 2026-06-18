import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/api/token_storage.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class HealthReportAttachmentPreview extends StatefulWidget {
  const HealthReportAttachmentPreview({
    super.key,
    required this.attachmentUrl,
  });

  final String attachmentUrl;

  @override
  State<HealthReportAttachmentPreview> createState() =>
      _HealthReportAttachmentPreviewState();
}

class _HealthReportAttachmentPreviewState
    extends State<HealthReportAttachmentPreview> {
  final _tokenStorage = TokenStorage();
  Map<String, String>? _headers;

  @override
  void initState() {
    super.initState();
    _loadHeaders();
  }

  Future<void> _loadHeaders() async {
    final token = await _tokenStorage.readToken();
    if (!mounted) return;

    setState(() {
      _headers = token == null || token.isEmpty
          ? const {}
          : {'Authorization': 'Bearer $token'};
    });
  }

  @override
  Widget build(BuildContext context) {
    if (_headers == null) {
      return const SizedBox(
        height: 200,
        child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(12),
          child: Image.network(
            widget.attachmentUrl,
            headers: _headers,
            height: 200,
            width: double.infinity,
            fit: BoxFit.cover,
            loadingBuilder: (context, child, progress) {
              if (progress == null) return child;
              return Container(
                height: 200,
                color: const Color(0xFFF1F5F1),
                alignment: Alignment.center,
                child: const CircularProgressIndicator(strokeWidth: 2),
              );
            },
            errorBuilder: (context, error, stackTrace) {
              return Container(
                height: 120,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFFE8F5E9),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFC8E6C9)),
                ),
                child: Row(
                  children: [
                    const Icon(
                      Icons.image_not_supported_outlined,
                      color: AppColors.primary,
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        'تعذّر تحميل الصورة المرفقة',
                        style: GoogleFonts.cairo(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: AppColors.primaryDark,
                        ),
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ),
      ],
    );
  }
}
