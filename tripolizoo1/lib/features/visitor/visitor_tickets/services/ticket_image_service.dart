import 'dart:typed_data';
import 'dart:ui' as ui;

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/shared/utils/date_formatters.dart';

abstract final class TicketImageService {
  static const _channel = MethodChannel('tripolizoo/ticket_images');
  static const _green = Color(0xFF2E7D32);

  static Future<int> saveTickets(
    List<PurchasedTicket> tickets, {
    String languageCode = 'ar',
  }) async {
    var savedCount = 0;
    for (final ticket in tickets) {
      final bytes = await _renderTicket(ticket, languageCode);
      final saved = await _channel.invokeMethod<bool>('saveImage', {
        'bytes': bytes,
        'name': 'TripoliZoo-${ticket.id}.png',
      });
      if (saved == true) savedCount++;
    }
    return savedCount;
  }

  static Future<Uint8List> _renderTicket(
    PurchasedTicket ticket,
    String languageCode,
  ) async {
    final isArabic = languageCode == 'ar';
    const width = 1080;
    const height = 1600;
    final recorder = ui.PictureRecorder();
    final canvas = Canvas(recorder);

    canvas.drawColor(const Color(0xFFF2F6F2), BlendMode.srcOver);
    canvas.drawRRect(
      RRect.fromRectAndRadius(
        const Rect.fromLTWH(60, 60, 960, 1480),
        const Radius.circular(52),
      ),
      Paint()..color = Colors.white,
    );
    canvas.drawRRect(
      RRect.fromRectAndCorners(
        const Rect.fromLTWH(60, 60, 960, 250),
        topLeft: const Radius.circular(52),
        topRight: const Radius.circular(52),
      ),
      Paint()..color = _green,
    );

    _drawCenteredText(
      canvas,
      isArabic ? 'حديقة طرابلس' : 'Tripoli Zoo',
      y: 105,
      style: const TextStyle(
        color: Colors.white,
        fontSize: 54,
        fontWeight: FontWeight.w800,
      ),
    );
    _drawCenteredText(
      canvas,
      isArabic ? 'تذكرة دخول' : 'Entry Ticket',
      y: 190,
      style: const TextStyle(
        color: Color(0xFFE8F5E9),
        fontSize: 34,
        fontWeight: FontWeight.w600,
      ),
    );

    final qrPainter = QrPainter(
      data: ticket.qrData,
      version: QrVersions.auto,
      gapless: true,
      eyeStyle: const QrEyeStyle(
        eyeShape: QrEyeShape.square,
        color: _green,
      ),
      dataModuleStyle: const QrDataModuleStyle(
        dataModuleShape: QrDataModuleShape.square,
        color: _green,
      ),
    );
    canvas.save();
    canvas.translate(290, 370);
    qrPainter.paint(canvas, const Size(500, 500));
    canvas.restore();

    _drawCenteredText(
      canvas,
      isArabic ? 'رقم التذكرة' : 'Ticket Number',
      y: 920,
      style: const TextStyle(
        color: Color(0xFF6B7280),
        fontSize: 27,
        fontWeight: FontWeight.w600,
      ),
    );
    _drawCenteredText(
      canvas,
      ticket.id,
      y: 965,
      direction: TextDirection.ltr,
      style: const TextStyle(
        color: Color(0xFF1A1A1A),
        fontSize: 28,
        fontWeight: FontWeight.w800,
      ),
    );

    canvas.drawLine(
      const Offset(120, 1045),
      const Offset(960, 1045),
      Paint()
        ..color = const Color(0xFFE5E7EB)
        ..strokeWidth = 2,
    );

    final date = formatArabicDate(ticket.visitDate);
    _drawDetail(
      canvas,
      isArabic ? 'الفئة' : 'Category',
      ticket.localizedCategoryLabel(languageCode),
      1100,
    );
    _drawDetail(canvas, isArabic ? 'التاريخ' : 'Date', date, 1175);
    _drawDetail(
      canvas,
      isArabic ? 'الوقت' : 'Time',
      AppConstants.workingHours,
      1250,
    );
    _drawDetail(
      canvas,
      isArabic ? 'السعر' : 'Price',
      '${ticket.price} ${isArabic ? 'د.ل' : 'LYD'}',
      1325,
      valueColor: _green,
    );

    _drawCenteredText(
      canvas,
      isArabic ? 'صالحة لدخول شخص واحد فقط' : 'Valid for one person only',
      y: 1440,
      style: const TextStyle(
        color: Color(0xFF6B7280),
        fontSize: 26,
        fontWeight: FontWeight.w600,
      ),
    );

    final picture = recorder.endRecording();
    final image = await picture.toImage(width, height);
    final data = await image.toByteData(format: ui.ImageByteFormat.png);
    image.dispose();
    if (data == null) {
      throw StateError('تعذر إنشاء صورة التذكرة');
    }
    return data.buffer.asUint8List();
  }

  static void _drawDetail(
    Canvas canvas,
    String label,
    String value,
    double y, {
    Color valueColor = const Color(0xFF1A1A1A),
  }) {
    _drawText(
      canvas,
      label,
      x: 120,
      y: y,
      width: 380,
      align: TextAlign.right,
      style: const TextStyle(
        color: Color(0xFF6B7280),
        fontSize: 29,
        fontWeight: FontWeight.w600,
      ),
    );
    _drawText(
      canvas,
      value,
      x: 580,
      y: y,
      width: 380,
      align: TextAlign.left,
      style: TextStyle(
        color: valueColor,
        fontSize: 30,
        fontWeight: FontWeight.w800,
      ),
    );
  }

  static void _drawCenteredText(
    Canvas canvas,
    String text, {
    required double y,
    required TextStyle style,
    TextDirection direction = TextDirection.rtl,
  }) {
    _drawText(
      canvas,
      text,
      x: 100,
      y: y,
      width: 880,
      align: TextAlign.center,
      direction: direction,
      style: style,
    );
  }

  static void _drawText(
    Canvas canvas,
    String text, {
    required double x,
    required double y,
    required double width,
    required TextStyle style,
    TextAlign align = TextAlign.start,
    TextDirection direction = TextDirection.rtl,
  }) {
    final painter = TextPainter(
      text: TextSpan(text: text, style: style),
      textDirection: direction,
      textAlign: align,
      maxLines: 1,
    )..layout(maxWidth: width);
    painter.paint(canvas, Offset(x, y));
  }
}
