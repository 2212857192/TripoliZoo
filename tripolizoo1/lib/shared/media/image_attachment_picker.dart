import 'package:file_picker/file_picker.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';

/// اختيار صورة للمرفقات.
/// - الموبايل: image_picker من المعرض (الآلية الأساسية)
/// - الويب: file_picker
class ImageAttachmentPicker {
  ImageAttachmentPicker({ImagePicker? imagePicker})
      : _imagePicker = imagePicker ?? ImagePicker();

  final ImagePicker _imagePicker;

  Future<XFile?> pickFromGallery() async {
    if (kIsWeb) {
      return _pickWithFilePicker();
    }

    return _pickWithImagePicker();
  }

  Future<XFile?> _pickWithImagePicker() async {
    return _imagePicker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 85,
      maxWidth: 2048,
    );
  }

  Future<XFile?> _pickWithFilePicker() async {
    try {
      final result = await FilePicker.platform.pickFiles(
        type: FileType.image,
        withData: true,
        allowMultiple: false,
      );

      if (result == null || result.files.isEmpty) {
        return null;
      }

      final file = result.files.single;
      final name = file.name.trim().isNotEmpty ? file.name : 'attachment.jpg';
      final mimeType = _mimeFromName(name);

      if (file.bytes != null && file.bytes!.isNotEmpty) {
        return XFile.fromData(
          file.bytes!,
          name: name,
          mimeType: mimeType,
        );
      }

      if (file.path != null && file.path!.isNotEmpty) {
        return XFile(file.path!, name: name, mimeType: mimeType);
      }

      return null;
    } on MissingPluginException {
      return null;
    }
  }

  String _mimeFromName(String name) {
    final lower = name.toLowerCase();
    if (lower.endsWith('.png')) return 'image/png';
    if (lower.endsWith('.webp')) return 'image/webp';
    return 'image/jpeg';
  }
}
