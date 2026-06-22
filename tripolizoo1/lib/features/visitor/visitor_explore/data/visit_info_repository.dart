import 'package:tripolizoo/features/visitor/visitor_explore/domain/visit_info.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';

abstract class VisitInfoRepository {
  Future<VisitInfo> fetch();
}

class ApiVisitInfoRepository implements VisitInfoRepository {
  ApiVisitInfoRepository({ApiClient? apiClient}) : _apiClient = apiClient ?? ApiClient();

  final ApiClient _apiClient;

  @override
  Future<VisitInfo> fetch() async {
    final response = await _apiClient.get(ApiConfig.visitInfo, auth: false);
    final data = response['data'];
    if (data is! Map<String, dynamic>) {
      throw const FormatException('Invalid visit info response');
    }
    return _fromJson(data);
  }

  VisitInfo _fromJson(Map<String, dynamic> json) {
    final hours = json['hours'];
    final status = json['status'];
    final location = json['location'];

    return VisitInfo(
      workingHours: hours is Map
          ? hours['working_hours_label']?.toString() ?? AppConstants.workingHours
          : AppConstants.workingHours,
      workingDays: hours is Map
          ? hours['working_days_label']?.toString() ?? AppConstants.workingDays
          : AppConstants.workingDays,
      closedDaysLabel: hours is Map
          ? hours['closed_days_label']?.toString()
          : null,
      statusText: status is Map ? status['text']?.toString() : null,
      statusVisible: status is Map && status['visible'] == true,
      urgentAlert: json['urgent_alert']?.toString(),
      ambulancePhone: json['ambulance_phone']?.toString(),
      securityPhone: json['security_phone']?.toString(),
      lastTicketTimeNote: hours is Map
          ? hours['last_ticket_time_note']?.toString()
          : null,
      guidelines: (json['guidelines'] as List?)
              ?.map((item) => item.toString())
              .where((item) => item.isNotEmpty)
              .toList() ??
          const [],
      location: location is Map
          ? _locationFromJson(Map<String, dynamic>.from(location))
          : null,
    );
  }

  VisitLocation _locationFromJson(Map<String, dynamic> json) {
    return VisitLocation(
      address: json['address']?.toString(),
      googleMapsUrl: json['google_maps_url']?.toString(),
      latitude: (json['latitude'] as num?)?.toDouble(),
      longitude: (json['longitude'] as num?)?.toDouble(),
    );
  }
}

class MockVisitInfoRepository implements VisitInfoRepository {
  static const List<String> _defaultGuidelines = [
    'يجب الإشراف على الأطفال طوال وقت الزيارة.',
    'الالتزام بالمسارات واللوحات الإرشادية وتعليمات الموظفين.',
    'يمنع إطعام الحيوانات أو الاقتراب من الحواجز.',
  ];

  @override
  Future<VisitInfo> fetch() async {
    await Future<void>.delayed(const Duration(milliseconds: 120));

    return VisitInfo(
      workingHours: AppConstants.workingHours,
      workingDays: AppConstants.workingDays,
      guidelines: _defaultGuidelines,
      statusText: 'مفتوحة — أهلاً بزوارنا',
      statusVisible: true,
      ambulancePhone: '193',
      securityPhone: '091-555-0123',
      lastTicketTimeNote: 'قبل ساعة واحدة من موعد الإغلاق',
      location: VisitLocation(
        address: 'حديقة حيوان طرابلس، طرابلس، ليبيا',
        googleMapsUrl:
            'https://www.google.com/maps/search/?api=1&query=${AppConstants.zooLatitude},${AppConstants.zooLongitude}',
        latitude: AppConstants.zooLatitude,
        longitude: AppConstants.zooLongitude,
      ),
    );
  }
}
