import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_launcher.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/domain/receiving_task.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/forms/confirm_receipt_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/forms/temporary_delay_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/receiving_tasks_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/widgets/receiving_task_status_badge.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/widgets/receiving_task_time_label.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class ReceivingTaskDetailSheet extends StatelessWidget {
  const ReceivingTaskDetailSheet({super.key, required this.taskId});

  final String taskId;

  static Future<void> show(BuildContext context, String taskId) {
    return showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      barrierColor: Colors.black.withValues(alpha: 0.45),
      useSafeArea: true,
      builder: (ctx) => ReceivingTaskDetailSheet(taskId: taskId),
    );
  }

  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  Future<void> _confirmReceipt(BuildContext context, ReceivingTask task) async {
    final note = await ConfirmReceiptFormSheet.show(context);
    if (!context.mounted || note == null) return;

    context.read<ReceivingTasksProvider>().confirmReceipt(
          task.id,
          note: note.isEmpty ? null : note,
        );
    if (!context.mounted) return;
    Navigator.of(context).pop();
    showSupervisorSuccessSnackBar(
      context,
      message: 'تم تأكيد استلام الحيوان',
    );
  }

  Future<void> _recordDelay(BuildContext context, ReceivingTask task) async {
    final result = await TemporaryDelayFormSheet.show(context);
    if (!context.mounted || result == null) return;

    context.read<ReceivingTasksProvider>().recordTemporaryDelay(
          task.id,
          reason: result.reason,
          extraNote: result.extraNote,
        );
    if (!context.mounted) return;
    showSupervisorSuccessSnackBar(
      context,
      message: 'تم تسجيل تعذر الاستلام مؤقتًا',
    );
  }

  @override
  Widget build(BuildContext context) {
    final task = context.watch<ReceivingTasksProvider>().findById(taskId);
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final sheetHeight = MediaQuery.sizeOf(context).height * 0.92;

    if (task == null) {
      return const SizedBox.shrink();
    }

    return Padding(
      padding:
          EdgeInsets.only(bottom: MediaQuery.viewInsetsOf(context).bottom),
      child: Align(
        alignment: Alignment.bottomCenter,
        child: Container(
          height: sheetHeight,
          width: double.infinity,
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Column(
            children: [
              const SizedBox(height: 10),
              Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: const Color(0xFFE2E8E2),
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 12, 12, 0),
                child: Row(
                  children: [
                    Expanded(
                      child: Text(
                        'تفاصيل المهمة ${task.taskNumber}',
                        style: GoogleFonts.cairo(
                          fontSize: 17,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF1A1A1A),
                        ),
                      ),
                    ),
                    Material(
                      color: AppColors.primaryDark,
                      shape: const CircleBorder(),
                      child: InkWell(
                        onTap: () => Navigator.of(context).pop(),
                        customBorder: const CircleBorder(),
                        child: const Padding(
                          padding: EdgeInsets.all(8),
                          child: Icon(
                            Icons.close_rounded,
                            color: Colors.white,
                            size: 20,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: ListView(
                  physics: const BouncingScrollPhysics(),
                  padding: EdgeInsets.fromLTRB(20, 12, 20, 12),
                  children: [
                    _SectionCard(
                      title: 'بيانات المهمة',
                      children: [
                        _DetailRow(
                          label: 'رقم المهمة',
                          value: task.taskNumber,
                        ),
                        _DetailRow(
                          label: 'حالة المهمة',
                          valueWidget:
                              ReceivingTaskStatusBadge(status: task.status),
                        ),
                        _DetailRow(
                          label: 'نوع المهمة',
                          value: task.taskType.label,
                        ),
                        _DetailRow(
                          label: 'مصدر الاستلام',
                          value: task.source.label,
                        ),
                        _DetailRow(
                          label: 'تاريخ الإنشاء',
                          value: formatReceivingTaskTime(task.createdAt),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    _SectionCard(
                      title: 'بيانات الحيوان',
                      children: [
                        _DetailRow(label: 'رقم الحيوان', value: task.animalId),
                        _DetailRow(
                          label: 'نوع الحيوان',
                          value: task.animalType,
                        ),
                        if (task.animalGender != null)
                          _DetailRow(
                            label: 'الجنس',
                            value: task.animalGender!,
                          ),
                        _DetailRow(label: 'المجموعة', value: task.groupName),
                        if (task.animalImageUrl != null) ...[
                          const SizedBox(height: 8),
                          ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: Image.network(
                              task.animalImageUrl!,
                              height: 120,
                              width: double.infinity,
                              fit: BoxFit.cover,
                              errorBuilder: (_, __, ___) =>
                                  _animalImagePlaceholder(),
                            ),
                          ),
                        ],
                      ],
                    ),
                    const SizedBox(height: 12),
                    _SectionCard(
                      title: 'بيانات القرار المرتبط',
                      children: [
                        _DetailRow(
                          label: 'نوع القرار',
                          value: task.decisionType.label,
                        ),
                        _DetailRow(
                          label: 'تاريخ القرار',
                          value: formatReceivingTaskTime(task.decisionDate),
                        ),
                        _DetailRow(
                          label: 'صادر عن',
                          value: task.decisionIssuedBy,
                        ),
                        if (task.decisionNotes != null &&
                            task.decisionNotes!.trim().isNotEmpty)
                          Padding(
                            padding: const EdgeInsets.only(top: 4),
                            child: Text(
                              'ملاحظات: ${task.decisionNotes}',
                              style: GoogleFonts.cairo(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: _muted,
                                height: 1.5,
                              ),
                            ),
                          ),
                      ],
                    ),
                    if (task.showDelayInfo) ...[
                      const SizedBox(height: 12),
                      _SectionCard(
                        title: 'معلومات التعذر',
                        children: [
                          _DetailRow(
                            label: 'سبب التعذر',
                            value: task.delayReason!,
                          ),
                          if (task.delayExtraNote != null)
                            _DetailRow(
                              label: 'ملاحظة إضافية',
                              value: task.delayExtraNote!,
                            ),
                          if (task.delayRecordedAt != null)
                            _DetailRow(
                              label: 'تاريخ التسجيل',
                              value: formatReceivingTaskTime(
                                task.delayRecordedAt!,
                              ),
                            ),
                        ],
                      ),
                    ],
                    if (task.status == ReceivingTaskStatus.received &&
                        task.receiptNote != null) ...[
                      const SizedBox(height: 12),
                      _SectionCard(
                        title: 'ملاحظة الاستلام',
                        children: [
                          Text(
                            task.receiptNote!,
                            style: GoogleFonts.cairo(
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                              color: const Color(0xFF374151),
                              height: 1.55,
                            ),
                          ),
                        ],
                      ),
                    ],
                    SizedBox(height: task.canConfirmReceipt ? 16 : bottomPad),
                  ],
                ),
              ),
              if (task.canConfirmReceipt)
                Padding(
                  padding: EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      _PrimaryActionButton(
                        label: 'تأكيد استلام الحيوان',
                        onTap: () => _confirmReceipt(context, task),
                      ),
                      if (task.canRecordDelay) ...[
                        const SizedBox(height: 10),
                        _OutlinedActionButton(
                          label: 'تسجيل تعذر الاستلام مؤقتًا',
                          onTap: () => _recordDelay(context, task),
                        ),
                      ],
                    ],
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}

Widget _animalImagePlaceholder() {
  return Container(
    height: 100,
    decoration: BoxDecoration(
      color: const Color(0xFFF3F4F6),
      borderRadius: BorderRadius.circular(12),
      border: Border.all(color: ReceivingTaskDetailSheet._border),
    ),
    child: const Center(
      child: Icon(Icons.pets_outlined, color: AppColors.primary, size: 32),
    ),
  );
}

class _PrimaryActionButton extends StatelessWidget {
  const _PrimaryActionButton({required this.label, required this.onTap});

  final String label;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.primaryDark,
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 15),
          child: Center(
            child: Text(
              label,
              style: GoogleFonts.cairo(
                fontSize: 15,
                fontWeight: FontWeight.w800,
                color: Colors.white,
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _OutlinedActionButton extends StatelessWidget {
  const _OutlinedActionButton({required this.label, required this.onTap});

  final String label;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Ink(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppColors.primaryDark, width: 1.5),
          ),
          padding: const EdgeInsets.symmetric(vertical: 14),
          child: Center(
            child: Text(
              label,
              style: GoogleFonts.cairo(
                fontSize: 14.5,
                fontWeight: FontWeight.w800,
                color: AppColors.primaryDark,
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _SectionCard extends StatelessWidget {
  const _SectionCard({required this.title, required this.children});

  final String title;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: ReceivingTaskDetailSheet._border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            title,
            style: GoogleFonts.cairo(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: AppColors.primaryDark,
            ),
          ),
          const SizedBox(height: 12),
          ...children,
        ],
      ),
    );
  }
}

class _DetailRow extends StatelessWidget {
  const _DetailRow({
    required this.label,
    this.value,
    this.valueWidget,
  });

  final String label;
  final String? value;
  final Widget? valueWidget;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Expanded(
            child: Align(
              alignment: AlignmentDirectional.centerStart,
              child: valueWidget ??
                  Text(
                    value ?? '',
                    textAlign: TextAlign.start,
                    style: GoogleFonts.cairo(
                      fontSize: 13.5,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF1A1A1A),
                      height: 1.35,
                    ),
                  ),
            ),
          ),
          const SizedBox(width: 12),
          Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: ReceivingTaskDetailSheet._muted,
            ),
          ),
        ],
      ),
    );
  }
}
