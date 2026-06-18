import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/domain/animal.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/animal_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/presentation/animal_detail_screen.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';

class QrScannerScreen extends StatefulWidget {
  const QrScannerScreen({
    super.key,
    this.requestCameraPermission,
  });

  final Future<bool> Function()? requestCameraPermission;

  @override
  State<QrScannerScreen> createState() => _QrScannerScreenState();
}

class _QrScannerScreenState extends State<QrScannerScreen> {
  final MobileScannerController _controller = MobileScannerController();
  final _repo = ApiAnimalRepository();
  bool _processing = false;
  bool _permissionGranted = false;
  bool _checkingPermission = true;

  @override
  void initState() {
    super.initState();
    _requestPermission();
  }

  Future<void> _requestPermission() async {
    final isGranted = widget.requestCameraPermission != null
        ? await widget.requestCameraPermission!()
        : (await Permission.camera.request()).isGranted;
    if (mounted) {
      setState(() {
        _permissionGranted = isGranted;
        _checkingPermission = false;
      });
    }
  }

  Future<void> _onDetect(BarcodeCapture capture) async {
    if (_processing) return;
    for (final barcode in capture.barcodes) {
      final code = barcode.rawValue;
      if (code == null) continue;
      setState(() => _processing = true);
      await _controller.stop();
      if (!mounted) return;
      final animal = await _repo.getByQrCode(code);
      if (!mounted) return;
      await _showResult(code, animal);
      if (mounted) setState(() => _processing = false);
      return;
    }
  }

  Future<void> _showResult(String code, Animal? animal) async {
    if (animal != null) {
      await context.push('/animals/${animal.id}');
      if (mounted) {
        _controller.start();
      }
      return;
    }

    await showDialog<void>(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Text(
          animal != null
              ? context.localized(
                  ar: 'تم التعرف على الحيوان!',
                  en: 'Animal identified!',
                )
              : context.localized(
                  ar: 'تم المسح بنجاح',
                  en: 'Scan completed',
                ),
          textAlign: TextAlign.center,
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (animal != null) ...[
              ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: _AnimalPreviewImage(animal: animal),
              ),
              const SizedBox(height: 12),
              Text(animal.name,
                  style: const TextStyle(
                      fontWeight: FontWeight.bold, fontSize: 18)),
              Text(animal.location,
                  style: const TextStyle(color: AppColors.accent)),
            ] else
              Text(
                '${context.localized(ar: 'الكود', en: 'Code')}: $code',
                textAlign: TextAlign.center,
              ),
          ],
        ),
        actions: [
          if (animal != null)
            TextButton(
              onPressed: () {
                Navigator.pop(ctx);
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => AnimalDetailScreen(animal: animal),
                  ),
                );
              },
              child: Text(
                context.localized(ar: 'عرض التفاصيل', en: 'View details'),
              ),
            ),
          Center(
            child: ElevatedButton(
              onPressed: () {
                Navigator.pop(ctx);
                _controller.start();
              },
              child: Text(
                context.localized(ar: 'مسح آخر', en: 'Scan another'),
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_checkingPermission) {
      return const Scaffold(
        body:
            Center(child: CircularProgressIndicator(color: AppColors.primary)),
      );
    }

    if (!_permissionGranted) {
      return Scaffold(
        appBar: AppBar(
          leading: IconButton(
            icon: const Icon(Icons.close),
            onPressed: () => context.pop(),
          ),
        ),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(32),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.camera_alt_outlined,
                    size: 64, color: Colors.grey),
                const SizedBox(height: 16),
                Text(
                  context.localized(
                    ar: 'يحتاج التطبيق إذن الكاميرا لمسح QR',
                    en: 'Camera permission is required to scan QR codes',
                  ),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 10),
                Text(
                  context.localized(
                    ar: 'امسح رمز الحيوان لاستكشاف معلوماته والتعرّف عليه.',
                    en: 'Scan an animal code to explore its information and learn more about it.',
                  ),
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: AppColors.textSecondary),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: _requestPermission,
                  child: Text(
                    context.localized(
                      ar: 'منح الإذن',
                      en: 'Grant Permission',
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        children: [
          MobileScanner(controller: _controller, onDetect: _onDetect),
          _Overlay(),
          Positioned(
            top: 48,
            right: 16,
            child: CircleAvatar(
              backgroundColor: Colors.black54,
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white),
                onPressed: () => context.pop(),
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }
}

class _Overlay extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        ColorFiltered(
          colorFilter: ColorFilter.mode(
            Colors.black.withValues(alpha: 0.55),
            BlendMode.srcOut,
          ),
          child: Stack(
            children: [
              Container(
                decoration: const BoxDecoration(
                  color: Colors.black,
                  backgroundBlendMode: BlendMode.dstOut,
                ),
              ),
              Center(
                child: Container(
                  width: 260,
                  height: 260,
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(28),
                  ),
                ),
              ),
            ],
          ),
        ),
        Center(
          child: Container(
            width: 260,
            height: 260,
            decoration: BoxDecoration(
              border: Border.all(color: AppColors.accent, width: 3),
              borderRadius: BorderRadius.circular(28),
            ),
          ),
        ),
        Positioned(
          top: 110,
          left: 32,
          right: 32,
          child: Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  context.localized(
                    ar: 'وجّه الكاميرا نحو رمز QR الخاص بالحيوان',
                    en: 'Point the camera at the animal QR code',
                  ),
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  context.localized(
                    ar: 'لاستكشاف معلومات الحيوان والتعرّف عليه.',
                    en: 'Explore the animal information and learn more about it.',
                  ),
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: 0.82),
                    fontSize: 12,
                    height: 1.5,
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}

class _AnimalPreviewImage extends StatelessWidget {
  const _AnimalPreviewImage({required this.animal});

  final Animal animal;

  @override
  Widget build(BuildContext context) {
    Widget fallback() => Container(
          height: 120,
          width: 180,
          color: AppColors.primary,
          child: const Icon(Icons.pets, color: Colors.white38, size: 42),
        );

    if (animal.image.isEmpty) return fallback();

    if (animal.hasNetworkImage) {
      return Image.network(
        animal.image,
        height: 120,
        fit: BoxFit.cover,
        errorBuilder: (_, __, ___) => fallback(),
      );
    }

    return Image.asset(
      animal.image,
      height: 120,
      fit: BoxFit.cover,
      errorBuilder: (_, __, ___) => fallback(),
    );
  }
}
