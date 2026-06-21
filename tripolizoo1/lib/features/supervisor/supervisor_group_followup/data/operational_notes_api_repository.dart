import 'package:image_picker/image_picker.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class OperationalNoteApiResult {
  const OperationalNoteApiResult({
    required this.noteNumber,
    required this.summary,
    required this.hasAttachment,
  });

  final String noteNumber;
  final String summary;
  final bool hasAttachment;
}

class OperationalNotesApiRepository {
  OperationalNotesApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<OperationalNoteEntry>> fetchNotes({DateTime? date}) async {
    var path = ApiConfig.supervisorOperationalNotes;
    if (date != null) {
      final formatted =
          '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
      path = '$path?date=$formatted';
    }

    final json = await _client.get(path);
    final list = json['data'] as List<dynamic>? ?? [];

    return list
        .map((item) => _parseNote(item as Map<String, dynamic>))
        .toList();
  }

  Future<OperationalNoteApiResult> createNote({
    required String noteKind,
    required String summary,
    String? details,
    XFile? attachment,
  }) async {
    final fields = <String, String>{
      'note_kind': noteKind,
      'summary': summary.trim(),
      if (details != null && details.trim().isNotEmpty)
        'details': details.trim(),
    };

    final Map<String, dynamic> json;
    if (attachment != null) {
      final filename = _attachmentFilename(attachment);
      final bytes = await attachment.readAsBytes();

      json = await _client.postMultipart(
        ApiConfig.supervisorOperationalNotes,
        fields: fields,
        files: {
          'attachment': MultipartUpload(
            bytes: bytes,
            filename: filename,
          ),
        },
      );
    } else {
      json = await _client.post(
        ApiConfig.supervisorOperationalNotes,
        body: fields,
      );
    }

    final data = json['data'] as Map<String, dynamic>;

    return OperationalNoteApiResult(
      noteNumber: data['note_number']?.toString() ?? '',
      summary: data['summary']?.toString() ?? summary,
      hasAttachment: data['has_attachment'] == true,
    );
  }

  OperationalNoteEntry _parseNote(Map<String, dynamic> data) {
    final kind = data['note_kind']?.toString();

    return OperationalNoteEntry(
      id: data['note_number']?.toString() ?? data['id']?.toString() ?? '',
      registeredAt:
          DateTime.tryParse(data['registered_at']?.toString() ?? '') ??
              DateTime.now(),
      noteKind: kind == 'general'
          ? OperationalNoteKind.general
          : OperationalNoteKind.feeding,
      summary: data['summary']?.toString() ?? '',
      fullText: _nullIfEmpty(data['details']?.toString()),
      hasAttachment: data['has_attachment'] == true,
      attachmentUrl: ApiConfig.resolveAssetUrl(
        _nullIfEmpty(data['attachment_url']?.toString()),
      ),
    );
  }

  String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }

  String _attachmentFilename(XFile attachment) {
    final name = attachment.name.trim();
    if (name.isNotEmpty) {
      return name.contains('.') ? name : '$name.jpg';
    }

    final path = attachment.path.trim();
    if (path.contains('.')) {
      final segment = path.split('/').last.split('\\').last;
      if (segment.isNotEmpty) {
        return segment;
      }
    }

    return 'attachment.jpg';
  }
}
