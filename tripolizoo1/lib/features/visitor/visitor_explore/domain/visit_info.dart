class VisitLocation {
  const VisitLocation({
    this.address,
    this.googleMapsUrl,
    this.latitude,
    this.longitude,
  });

  final String? address;
  final String? googleMapsUrl;
  final double? latitude;
  final double? longitude;

  bool get hasAddress => address != null && address!.trim().isNotEmpty;

  String? get mapsUrl {
    if (googleMapsUrl != null && googleMapsUrl!.trim().isNotEmpty) {
      return googleMapsUrl;
    }
    if (latitude != null && longitude != null) {
      return 'https://www.google.com/maps/search/?api=1&query=$latitude,$longitude';
    }
    return null;
  }
}

class VisitInfo {
  const VisitInfo({
    required this.workingHours,
    required this.workingDays,
    required this.guidelines,
    this.statusText,
    this.statusVisible = false,
    this.urgentAlert,
    this.ambulancePhone,
    this.securityPhone,
    this.lastTicketTimeNote,
    this.closedDaysLabel,
    this.location,
  });

  final String workingHours;
  final String workingDays;
  final List<String> guidelines;
  final String? statusText;
  final bool statusVisible;
  final String? urgentAlert;
  final String? ambulancePhone;
  final String? securityPhone;
  final String? lastTicketTimeNote;
  final String? closedDaysLabel;
  final VisitLocation? location;

  List<String> guidelinesWithNotes() {
    final items = [...guidelines];
    final note = lastTicketTimeNote;
    if (note != null && note.isNotEmpty) {
      items.add(note);
    }
    return items;
  }
}
