String formatFollowUpTime(DateTime registeredAt) {
  final now = DateTime.now();
  final today = DateTime(now.year, now.month, now.day);
  final registeredDay = DateTime(
    registeredAt.year,
    registeredAt.month,
    registeredAt.day,
  );
  final time =
      '${registeredAt.hour.toString().padLeft(2, '0')}:${registeredAt.minute.toString().padLeft(2, '0')}';

  if (registeredDay == today) {
    return 'اليوم $time';
  }
  if (registeredDay == today.subtract(const Duration(days: 1))) {
    return 'أمس $time';
  }
  return '${registeredAt.day}/${registeredAt.month} $time';
}
