import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/qr_code_parser.dart';
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
  final MobileScannerController _controller = MobileScannerController(
    detectionSpeed: DetectionSpeed.noDuplicates,
    facing: CameraFacing.back,
    formats: const [BarcodeFormat.qrCode],
  );
  StreamSubscription<BarcodeCapture>? _barcodeSubscription;

  bool _processing = false;
  bool _permissionGranted = false;
  bool _checkingPermission = true;
  bool _permissionPermanentlyDenied = false;
  String? _lastScannedCode;

  @override
  void initState() {
    super.initState();
    _barcodeSubscription = _controller.barcodes.listen(
      _onDetect,
      onError: _onDetectError,
    );
    _requestPermission();
  }

  Future<void> _requestPermission() async {
    setState(() {
      _checkingPermission = true;
      _permissionPermanentlyDenied = false;
    });

    final isGranted = widget.requestCameraPermission != null
        ? await widget.requestCameraPermission!()
        : await _resolveCameraPermission();

    if (!mounted) return;
    setState(() {
      _permissionGranted = isGranted;
      _checkingPermission = false;
    });
  }

  Future<bool> _resolveCameraPermission() async {
    if (kIsWeb) {
      // MobileScanner requests camera access directly in the browser.
      return true;
    }

    var status = await Permission.camera.status;
    if (status.isGranted) return true;

    status = await Permission.camera.request();
    if (status.isGranted) return true;

    if (status.isPermanentlyDenied && mounted) {
      setState(() => _permissionPermanentlyDenied = true);
    }
    return false;
  }

  void _onDetectError(Object error, StackTrace stackTrace) {
    if (!mounted || kDebugMode) {
      debugPrint('QR scanner error: $error');
    }
  }

  Future<void> _onDetect(BarcodeCapture capture) async {
    if (_processing) return;

    String? code;
    for (final barcode in capture.barcodes) {
      code = readBarcodeValue(barcode);
      if (code != null) break;
    }
    if (code == null || code.isEmpty) return;

    // Ignore repeated detections of the same code while the scanner is active.
    if (code == _lastScannedCode) return;
    _lastScannedCode = code;
    _processing = true;

    try {
      await _controller.stop();
      if (!mounted) return;

      final path = animalDetailPathFromQr(code);
      if (path == null) {
        await _showInvalidScan();
        return;
      }

      // Same flow as the website: /app/animals/{profile} → animal details page.
      if (!mounted) return;
      context.pushReplacement(path);
    } finally {
      if (mounted) {
        setState(() => _processing = false);
        _lastScannedCode = null;
        if (_permissionGranted) {
          await _controller.start();
        }
      }
    }
  }

  Future<void> _showInvalidScan() {
    return showDialog<void>(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Text(
          context.localized(
            ar: 'رمز غير صالح',
            en: 'Invalid code',
          ),
          textAlign: TextAlign.center,
        ),
        content: Text(
          context.localized(
            ar: 'لم نتمكن من قراءة رمز الحيوان. حاول تقريب الكاميرا أو تحسين الإضاءة.',
            en: 'Could not read the animal code. Move closer or improve lighting.',
          ),
          textAlign: TextAlign.center,
          style: const TextStyle(color: AppColors.textSecondary),
        ),
        actions: [
          Center(
            child: ElevatedButton(
              onPressed: () => Navigator.pop(ctx),
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
                if (_permissionPermanentlyDenied) ...[
                  const SizedBox(height: 10),
                  TextButton(
                    onPressed: openAppSettings,
                    child: Text(
                      context.localized(
                        ar: 'فتح إعدادات التطبيق',
                        en: 'Open app settings',
                      ),
                    ),
                  ),
                ],
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
          LayoutBuilder(
            builder: (context, constraints) {
              final layoutSize = constraints.biggest;
              final scanSide = (layoutSize.shortestSide * 0.62).clamp(220.0, 300.0);
              final scanWindow = Rect.fromCenter(
                center: layoutSize.center(Offset.zero),
                width: scanSide,
                height: scanSide,
              );

              return MobileScanner(
                controller: _controller,
                fit: BoxFit.cover,
                scanWindow: kIsWeb ? null : scanWindow,
                errorBuilder: (context, error) => _ScannerErrorView(
                  message: error.errorDetails?.message ?? error.toString(),
                  onRetry: () async {
                    await _controller.start();
                  },
                  onClose: () => context.pop(),
                ),
                overlayBuilder: (context, constraints) =>
                    _ScannerOverlay(scanWindow: scanWindow),
              );
            },
          ),
          if (_processing)
            const ColoredBox(
              color: Color(0x66000000),
              child: Center(
                child: CircularProgressIndicator(color: Colors.white),
              ),
            ),
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
    _barcodeSubscription?.cancel();
    _controller.dispose();
    super.dispose();
  }
}

class _ScannerOverlay extends StatelessWidget {
  const _ScannerOverlay({required this.scanWindow});

  final Rect scanWindow;

  @override
  Widget build(BuildContext context) {
    return Stack(
      fit: StackFit.expand,
      children: [
        CustomPaint(
          painter: _ScanMaskPainter(scanWindow: scanWindow),
        ),
        Positioned(
          left: scanWindow.left,
          top: scanWindow.top,
          width: scanWindow.width,
          height: scanWindow.height,
          child: DecoratedBox(
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

class _ScanMaskPainter extends CustomPainter {
  const _ScanMaskPainter({required this.scanWindow});

  final Rect scanWindow;

  @override
  void paint(Canvas canvas, Size size) {
    final background = Paint()..color = Colors.black.withValues(alpha: 0.55);
    final full = Path()..addRect(Offset.zero & size);
    final hole = Path()
      ..addRRect(
        RRect.fromRectAndRadius(scanWindow, const Radius.circular(28)),
      );
    final mask = Path.combine(PathOperation.difference, full, hole);
    canvas.drawPath(mask, background);
  }

  @override
  bool shouldRepaint(covariant _ScanMaskPainter oldDelegate) =>
      oldDelegate.scanWindow != scanWindow;
}

class _ScannerErrorView extends StatelessWidget {
  const _ScannerErrorView({
    required this.message,
    required this.onRetry,
    required this.onClose,
  });

  final String message;
  final VoidCallback onRetry;
  final VoidCallback onClose;

  @override
  Widget build(BuildContext context) {
    return ColoredBox(
      color: Colors.black,
      child: Center(
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(Icons.videocam_off_rounded,
                  color: Colors.white70, size: 56),
              const SizedBox(height: 14),
              Text(
                context.localized(
                  ar: 'تعذر تشغيل الكاميرا',
                  en: 'Unable to start the camera',
                ),
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                message,
                textAlign: TextAlign.center,
                style: const TextStyle(color: Colors.white70),
              ),
              const SizedBox(height: 18),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  OutlinedButton(
                    onPressed: onClose,
                    style: OutlinedButton.styleFrom(
                      foregroundColor: Colors.white,
                      side: const BorderSide(color: Colors.white54),
                    ),
                    child: Text(context.localized(ar: 'إغلاق', en: 'Close')),
                  ),
                  const SizedBox(width: 10),
                  ElevatedButton(
                    onPressed: onRetry,
                    child: Text(
                      context.localized(ar: 'إعادة المحاولة', en: 'Retry'),
                    ),
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
