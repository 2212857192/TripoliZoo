import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

enum FollowUpDatePreset {
  today,
  yesterday,
  custom,
}

class FollowUpDateFilter extends StatelessWidget {
  const FollowUpDateFilter({
    super.key,
    required this.preset,
    required this.customDate,
    required this.onPresetChanged,
    required this.onCustomDatePicked,
  });

  final FollowUpDatePreset preset;
  final DateTime? customDate;
  final ValueChanged<FollowUpDatePreset> onPresetChanged;
  final ValueChanged<DateTime> onCustomDatePicked;

  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  Future<void> _pickDate(BuildContext context) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: customDate ?? DateTime.now(),
      firstDate: DateTime.now().subtract(const Duration(days: 365)),
      lastDate: DateTime.now(),
      locale: const Locale('ar'),
    );
    if (picked != null) onCustomDatePicked(picked);
  }

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        _Chip(
          label: 'اليوم',
          selected: preset == FollowUpDatePreset.today,
          onTap: () => onPresetChanged(FollowUpDatePreset.today),
        ),
        const SizedBox(width: 8),
        _Chip(
          label: 'أمس',
          selected: preset == FollowUpDatePreset.yesterday,
          onTap: () => onPresetChanged(FollowUpDatePreset.yesterday),
        ),
        const SizedBox(width: 8),
        _Chip(
          label: preset == FollowUpDatePreset.custom && customDate != null
              ? _formatShort(customDate!)
              : 'اختيار تاريخ',
          selected: preset == FollowUpDatePreset.custom,
          showCalendarIcon: true,
          onTap: () {
            onPresetChanged(FollowUpDatePreset.custom);
            _pickDate(context);
          },
        ),
      ],
    );
  }

  static String _formatShort(DateTime date) {
    return '${date.day}/${date.month}';
  }
}

class _Chip extends StatelessWidget {
  const _Chip({
    required this.label,
    required this.selected,
    required this.onTap,
    this.showCalendarIcon = false,
  });

  final String label;
  final bool selected;
  final VoidCallback onTap;
  final bool showCalendarIcon;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: selected ? AppColors.primary : FollowUpDateFilter._border,
            ),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              if (showCalendarIcon) ...[
                Icon(
                  Icons.calendar_today_outlined,
                  size: 14,
                  color: selected ? Colors.white : FollowUpDateFilter._muted,
                ),
                const SizedBox(width: 6),
              ],
              Text(
                label,
                style: GoogleFonts.cairo(
                  fontSize: 12.5,
                  fontWeight: FontWeight.w700,
                  color: selected ? Colors.white : FollowUpDateFilter._muted,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
