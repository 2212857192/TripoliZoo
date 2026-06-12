import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/domain/receiving_task.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/receiving_task_detail_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/receiving_tasks_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/widgets/receiving_task_card.dart';
import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/presentation/widgets/receiving_task_status_filter.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class ReceivingTasksScreen extends StatefulWidget {
  const ReceivingTasksScreen({super.key, this.initialFilterQuery});

  /// قيمة `filter` من الرابط، مثل `pending`.
  final String? initialFilterQuery;

  @override
  State<ReceivingTasksScreen> createState() => _ReceivingTasksScreenState();
}

class _ReceivingTasksScreenState extends State<ReceivingTasksScreen> {
  static const _bg = Color(0xFFF5F5F5);
  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  final _searchController = TextEditingController();
  late ReceivingTaskStatus? _statusFilter;

  @override
  void initState() {
    super.initState();
    _statusFilter = receivingTaskStatusFromQuery(widget.initialFilterQuery);
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _openDetail(ReceivingTask task) {
    ReceivingTaskDetailSheet.show(context, task.id);
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final query = _searchController.text;
    final tasks = context.watch<ReceivingTasksProvider>().filtered(
          status: _statusFilter,
          query: query,
        );

    return Scaffold(
      backgroundColor: _bg,
      body: SafeArea(
        top: false,
        bottom: false,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(
              child: Padding(
                padding: EdgeInsets.fromLTRB(20, topPad + 20, 20, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      'مهام الاستلام',
                      style: GoogleFonts.cairo(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF1A1A1A),
                        height: 1.2,
                      ),
                    ),
                    const SizedBox(height: 16),
                    TextField(
                      controller: _searchController,
                      onChanged: (_) => setState(() {}),
                      style: GoogleFonts.cairo(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF1A1A1A),
                      ),
                      decoration: InputDecoration(
                        hintText: 'ابحث برقم الحيوان أو رقم المهمة',
                        hintStyle: GoogleFonts.cairo(
                          fontSize: 13.5,
                          fontWeight: FontWeight.w500,
                          color: _muted,
                        ),
                        filled: true,
                        fillColor: Colors.white,
                        contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14,
                          vertical: 14,
                        ),
                        prefixIcon: const Icon(
                          Icons.search_rounded,
                          color: _muted,
                          size: 22,
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: const BorderSide(color: _border),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: const BorderSide(color: _border),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: const BorderSide(
                            color: AppColors.primary,
                            width: 1.5,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 14),
                    ReceivingTaskStatusFilter(
                      selected: _statusFilter,
                      onChanged: (v) => setState(() => _statusFilter = v),
                    ),
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
            if (tasks.isEmpty)
              SliverFillRemaining(
                hasScrollBody: false,
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 32),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.inventory_2_outlined,
                        size: 48,
                        color: _muted.withValues(alpha: 0.5),
                      ),
                      const SizedBox(height: 12),
                      Text(
                        query.isNotEmpty || _statusFilter != null
                            ? 'لا توجد مهام مطابقة'
                            : 'لا توجد مهام استلام',
                        textAlign: TextAlign.center,
                        style: GoogleFonts.cairo(
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          color: _muted,
                        ),
                      ),
                    ],
                  ),
                ),
              )
            else
              SliverPadding(
                padding: EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 100),
                sliver: SliverList.separated(
                  itemCount: tasks.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 12),
                  itemBuilder: (context, index) {
                    final task = tasks[index];
                    return ReceivingTaskCard(
                      task: task,
                      onTap: () => _openDetail(task),
                    );
                  },
                ),
              ),
          ],
        ),
      ),
    );
  }
}
