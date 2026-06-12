String formatReceivingTaskTime(DateTime at) {
  final now = DateTime.now();
  final today = DateTime(now.year, now.month, now.day);
  final day = DateTime(at.year, at.month, at.day);
  final diff = today.difference(day).inDays;
  final time =
      '${at.hour.toString().padLeft(2, '0')}:${at.minute.toString().padLeft(2, '0')}';

  if (diff == 0) return 'اليوم $time';
  if (diff == 1) return 'أمس $time';
  if (diff > 1 && diff < 7) return 'منذ $diff أيام';
  return '${at.day}/${at.month} $time';
}
