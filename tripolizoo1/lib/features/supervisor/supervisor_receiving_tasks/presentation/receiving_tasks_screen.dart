import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_ui.dart';
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
  final _searchController = TextEditingController();
  late ReceivingTaskStatus? _statusFilter;

  @override
  void initState() {
    super.initState();
    _statusFilter = receivingTaskStatusFromQuery(widget.initialFilterQuery);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ReceivingTasksProvider>().load();
    });
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

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: SupervisorUi.background,
        body: SafeArea(
          top: false,
          bottom: false,
          child: CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              // ── Premium Header ──
              SliverToBoxAdapter(
                child: AnnotatedRegion<SystemUiOverlayStyle>(
                  value: SystemUiOverlayStyle.dark,
                  child: Container(
                    padding: EdgeInsets.fromLTRB(20, topPad + 18, 20, 20),
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.vertical(
                        bottom: Radius.circular(28),
                      ),
                      border: Border(
                        bottom: BorderSide(color: SupervisorUi.border, width: 1.5),
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Color(0x0D142E1B),
                          blurRadius: 16,
                          offset: Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Text(
                          'مهام الاستلام',
                          style: GoogleFonts.cairo(
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            color: SupervisorUi.textPrimary,
                            height: 1.2,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'استلام وتأكيد وصول الحيوانات أو المواد للمجموعة',
                          style: GoogleFonts.cairo(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: SupervisorUi.muted,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              // ── Search & Filter ──
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 18, 20, 0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          boxShadow: SupervisorUi.softShadow,
                        ),
                        child: TextField(
                          controller: _searchController,
                          onChanged: (_) => setState(() {}),
                          style: GoogleFonts.cairo(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: SupervisorUi.textPrimary,
                          ),
                          decoration: InputDecoration(
                            hintText: 'ابحث برقم الحيوان أو رقم المهمة',
                            hintStyle: GoogleFonts.cairo(
                              fontSize: 13.5,
                              fontWeight: FontWeight.w500,
                              color: SupervisorUi.muted,
                            ),
                            filled: true,
                            fillColor: Colors.white,
                            contentPadding: const EdgeInsets.symmetric(
                              horizontal: 14,
                              vertical: 14,
                            ),
                            prefixIcon: const Icon(
                              Icons.search_rounded,
                              color: SupervisorUi.muted,
                              size: 22,
                            ),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(14),
                              borderSide: const BorderSide(color: SupervisorUi.border, width: 1.5),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(14),
                              borderSide: const BorderSide(color: SupervisorUi.border, width: 1.5),
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

              // ── List or Empty State ──
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
                          color: SupervisorUi.muted.withValues(alpha: 0.5),
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
                            color: SupervisorUi.muted,
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
      ),
    );
  }
}
