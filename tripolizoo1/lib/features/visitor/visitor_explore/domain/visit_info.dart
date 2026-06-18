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

  List<String> guidelinesWithNotes() {
    final items = [...guidelines];
    final note = lastTicketTimeNote;
    if (note != null && note.isNotEmpty) {
      items.add(note);
    }
    return items;
  }
}
