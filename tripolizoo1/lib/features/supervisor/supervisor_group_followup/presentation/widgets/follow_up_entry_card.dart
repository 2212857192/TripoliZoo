import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';
import 'package:tripolizoo/features/supervisor/supervisor_group_followup/presentation/widgets/follow_up_time_label.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class FollowUpEntryCard extends StatefulWidget {
  const FollowUpEntryCard({super.key, required this.entry});

  final FollowUpEntry entry;

  @override
  State<FollowUpEntryCard> createState() => _FollowUpEntryCardState();
}

class _FollowUpEntryCardState extends State<FollowUpEntryCard> {
  bool _expanded = false;

  static const _border = Color(0xFFE5E7EB);
  static const _iconBg = Color(0xFFE8F5E9);
  static const _muted = Color(0xFF6B7280);

  @override
  Widget build(BuildContext context) {
    final meta = _entryMeta(widget.entry);

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: _border),
      ),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: const BoxDecoration(
                    color: _iconBg,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(meta.icon, color: AppColors.primary, size: 22),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        meta.title,
                        style: GoogleFonts.cairo(
                          fontSize: 15,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF1A1A1A),
                          height: 1.2,
                        ),
                      ),
                      const SizedBox(height: 6),
                      ...meta.lines.map(
                        (line) => Padding(
                          padding: const EdgeInsets.only(bottom: 2),
                          child: Text(
                            line,
                            style: GoogleFonts.cairo(
                              fontSize: 13,
                              fontWeight: FontWeight.w500,
                              color: _muted,
                              height: 1.35,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        formatFollowUpTime(widget.entry.registeredAt),
                        style: GoogleFonts.cairo(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: _muted.withValues(alpha: 0.85),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            if (_canExpand) ...[
              const SizedBox(height: 8),
              Align(
                alignment: AlignmentDirectional.centerStart,
                child: TextButton(
                  onPressed: () => setState(() => _expanded = !_expanded),
                  style: TextButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 4),
                    minimumSize: Size.zero,
                    tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  ),
                  child: Text(
                    _expanded ? 'عرض أقل' : 'عرض المزيد',
                    style: GoogleFonts.cairo(
                      fontSize: 12.5,
                      fontWeight: FontWeight.w800,
                      color: AppColors.primary,
                    ),
                  ),
                ),
              ),
            ],
            if (_expanded) ...[
              const SizedBox(height: 8),
              ..._buildExpandedContent(),
            ],
          ],
        ),
      ),
    );
  }

  bool get _canExpand => switch (widget.entry) {
        HealthFollowUpEntry e => e.isExpandable,
        BirthFollowUpEntry e => e.isExpandable,
        MortalityFollowUpEntry e => e.isExpandable,
        OperationalNoteEntry e => e.isExpandable,
      };

  List<Widget> _buildExpandedContent() {
    return switch (widget.entry) {
      HealthFollowUpEntry e => [
          if (e.fullDescription != null) _expandedText(e.fullDescription!),
          if (e.extraNotes != null) _expandedText(e.extraNotes!),
          if (e.hasAttachment) _attachmentRow(),
        ],
      BirthFollowUpEntry e => [
          for (var i = 0; i < e.newborns.length; i++)
            _expandedText(
              'المولود ${i + 1}: ${e.newborns[i].genderLabel}'
              '${e.newborns[i].distinguishingMark != null && e.newborns[i].distinguishingMark!.isNotEmpty ? ' — ${e.newborns[i].distinguishingMark}' : ''}'
              '${e.newborns[i].note != null && e.newborns[i].note!.isNotEmpty ? '\n${e.newborns[i].note}' : ''}',
            ),
        ],
      MortalityFollowUpEntry e => [
          if (e.extraNotes != null) _expandedText(e.extraNotes!),
          if (e.hasAttachment) _attachmentRow(),
        ],
      OperationalNoteEntry e => [
          if (e.fullText != null) _expandedText(e.fullText!),
          if (e.extraNotes != null) _expandedText(e.extraNotes!),
          if (e.hasAttachment) _attachmentRow(),
        ],
    };
  }

  Widget _expandedText(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(
        text,
        style: GoogleFonts.cairo(
          fontSize: 13,
          fontWeight: FontWeight.w600,
          color: _muted,
          height: 1.45,
        ),
      ),
    );
  }

  Widget _attachmentRow() {
    return Row(
      children: [
        const Icon(Icons.attach_file_rounded,
            size: 16, color: AppColors.primary),
        const SizedBox(width: 6),
        Text(
          'مرفق ميداني',
          style: GoogleFonts.cairo(
            fontSize: 12,
            fontWeight: FontWeight.w700,
            color: _muted,
          ),
        ),
      ],
    );
  }

  static String _formatDate(DateTime date) {
    final d = date.day.toString().padLeft(2, '0');
    final m = date.month.toString().padLeft(2, '0');
    return '$d/$m/${date.year}';
  }

  _EntryMeta _entryMeta(FollowUpEntry entry) {
    return switch (entry) {
      HealthFollowUpEntry e => _EntryMeta(
          icon: Icons.medical_services_outlined,
          title: 'حالة صحية',
          lines: [
            'الحيوان: ${e.animalId}',
            e.description,
          ],
        ),
      BirthFollowUpEntry e => _EntryMeta(
          icon: Icons.child_care_outlined,
          title: 'ولادة جديدة',
          lines: [
            'الأم: ${e.motherId}',
            'تاريخ الولادة: ${_formatDate(e.birthDate)}',
            'عدد المواليد: ${e.birthCount}',
          ],
        ),
      MortalityFollowUpEntry e => _EntryMeta(
          icon: Icons.heart_broken_outlined,
          title: 'حالة نفوق',
          lines: [
            'الحيوان: ${e.animalId}',
            'سبب النفوق: ${e.displayCause}',
          ],
        ),
      OperationalNoteEntry e => _EntryMeta(
          icon: Icons.edit_note_outlined,
          title: 'ملاحظة تشغيلية',
          lines: [e.summary],
        ),
    };
  }
}

class _EntryMeta {
  const _EntryMeta({
    required this.icon,
    required this.title,
    required this.lines,
  });

  final IconData icon;
  final String title;
  final List<String> lines;
}
