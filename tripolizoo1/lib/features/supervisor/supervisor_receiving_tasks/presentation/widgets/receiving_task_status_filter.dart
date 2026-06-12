import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/domain/receiving_task.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class ReceivingTaskStatusFilter extends StatelessWidget {
  const ReceivingTaskStatusFilter({
    super.key,
    required this.selected,
    required this.onChanged,
  });

  final ReceivingTaskStatus? selected;
  final ValueChanged<ReceivingTaskStatus?> onChanged;

  static const _labels = <ReceivingTaskStatus?, String>{
    null: 'الكل',
    ReceivingTaskStatus.pending: 'بانتظار الاستلام',
    ReceivingTaskStatus.temporarilyUnable: 'تعذر مؤقتًا',
    ReceivingTaskStatus.received: 'تم الاستلام',
  };

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: const BouncingScrollPhysics(),
      child: Row(
        children: [
          for (final entry in _labels.entries) ...[
            _FilterChip(
              label: entry.value,
              selected: selected == entry.key,
              onTap: () => onChanged(entry.key),
            ),
            if (entry.key != ReceivingTaskStatus.received)
              const SizedBox(width: 8),
          ],
        ],
      ),
    );
  }
}

class _FilterChip extends StatelessWidget {
  const _FilterChip({
    required this.label,
    required this.selected,
    required this.onTap,
  });

  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: selected ? AppColors.primaryDark : const Color(0xFFE8F5E9),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: selected ? AppColors.primaryDark : const Color(0xFFC8E6C9),
            ),
          ),
          child: Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 12.5,
              fontWeight: FontWeight.w700,
              color: selected ? Colors.white : AppColors.primaryDark,
            ),
          ),
        ),
      ),
    );
  }
}
