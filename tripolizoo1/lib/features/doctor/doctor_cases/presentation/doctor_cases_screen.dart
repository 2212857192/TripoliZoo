import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/forms/open_field_case_form_sheet.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/widgets/medical_case_card.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_form_launcher.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorCasesScreen extends StatefulWidget {
  const DoctorCasesScreen({super.key});

  @override
  State<DoctorCasesScreen> createState() => _DoctorCasesScreenState();
}

class _DoctorCasesScreenState extends State<DoctorCasesScreen> {
  final _searchController = TextEditingController();
  MedicalCaseFilter _filter = MedicalCaseFilter.all;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<MedicalCasesProvider>().load();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _reload() async {
    await context.read<MedicalCasesProvider>().load();
  }

  Future<void> _openFieldCaseForm() async {
    final caseId = await OpenFieldCaseFormSheet.show(context);
    if (!mounted || caseId == null) return;
    showDoctorSuccessSnackBar(context, message: 'تم فتح الحالة بنجاح');
    context.push('/doctor/cases/$caseId');
  }

  void _openDetail(MedicalCase medicalCase) {
    context.push('/doctor/cases/${medicalCase.id}');
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final provider = context.watch<MedicalCasesProvider>();
    final cases = provider.filtered(
      filter: _filter,
      query: _searchController.text,
    );

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: DoctorUi.background,
        body: SafeArea(
          top: false,
          bottom: false,
          child: RefreshIndicator(
            color: AppColors.primary,
            onRefresh: _reload,
            child: CustomScrollView(
              physics: const AlwaysScrollableScrollPhysics(
                parent: BouncingScrollPhysics(),
              ),
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
                          bottom: BorderSide(color: DoctorUi.border, width: 1.5),
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
                            'الحالات الطبية',
                            style: GoogleFonts.cairo(
                              fontSize: 22,
                              fontWeight: FontWeight.w900,
                              color: DoctorUi.textPrimary,
                              height: 1.2,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'متابعة الحالات الميدانية وحالات المستشفى',
                            style: GoogleFonts.cairo(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: DoctorUi.muted,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                // ── Search + Filter + Button ──
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 18, 20, 0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            boxShadow: DoctorUi.softShadow,
                          ),
                          child: TextField(
                            controller: _searchController,
                            onChanged: (_) => setState(() {}),
                            style: GoogleFonts.cairo(
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                              color: DoctorUi.textPrimary,
                            ),
                            decoration: InputDecoration(
                              hintText: 'ابحث برقم الحيوان أو نوع الحيوان',
                              hintStyle: GoogleFonts.cairo(
                                fontSize: 13.5,
                                fontWeight: FontWeight.w500,
                                color: DoctorUi.muted,
                              ),
                              filled: true,
                              fillColor: Colors.white,
                              prefixIcon: const Icon(
                                Icons.search_rounded,
                                color: AppColors.primary,
                                size: 22,
                              ),
                              contentPadding: const EdgeInsets.symmetric(
                                horizontal: 16,
                                vertical: 14,
                              ),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(16),
                                borderSide: const BorderSide(
                                    color: DoctorUi.border, width: 1.2),
                              ),
                              enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(16),
                                borderSide: const BorderSide(
                                    color: DoctorUi.border, width: 1.2),
                              ),
                              focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(16),
                                borderSide: const BorderSide(
                                  color: AppColors.primary,
                                  width: 1.5,
                                ),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 12),
                        _TypeFilterBar(
                          selected: _filter,
                          onChanged: (v) => setState(() => _filter = v),
                        ),
                        const SizedBox(height: 14),
                        Container(
                          decoration: BoxDecoration(
                            gradient: AppColors.primaryGradient,
                            borderRadius: BorderRadius.circular(16),
                            boxShadow: [
                              BoxShadow(
                                color: AppColors.primary.withValues(alpha: 0.25),
                                blurRadius: 12,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: Material(
                            color: Colors.transparent,
                            borderRadius: BorderRadius.circular(16),
                            child: InkWell(
                              onTap: _openFieldCaseForm,
                              borderRadius: BorderRadius.circular(16),
                              child: Padding(
                                padding:
                                    const EdgeInsets.symmetric(vertical: 14),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    const Icon(
                                      Icons.add_rounded,
                                      color: Colors.white,
                                      size: 20,
                                    ),
                                    const SizedBox(width: 6),
                                    Text(
                                      'فتح حالة طبية ميدانية',
                                      style: GoogleFonts.cairo(
                                        fontSize: 14.5,
                                        fontWeight: FontWeight.w800,
                                        color: Colors.white,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 18),
                      ],
                    ),
                  ),
                ),

                // ── List / States ──
                if (provider.isLoading && cases.isEmpty)
                  const SliverFillRemaining(
                    hasScrollBody: false,
                    child: Center(
                      child:
                          CircularProgressIndicator(color: AppColors.primary),
                    ),
                  )
                else if (provider.error != null && cases.isEmpty)
                  SliverFillRemaining(
                    hasScrollBody: false,
                    child: Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            provider.error!,
                            style: GoogleFonts.cairo(
                              fontWeight: FontWeight.w700,
                              color: DoctorUi.muted,
                            ),
                          ),
                          const SizedBox(height: 12),
                          TextButton(
                            onPressed: _reload,
                            child: Text(
                              'إعادة المحاولة',
                              style: GoogleFonts.cairo(
                                fontWeight: FontWeight.w800,
                                color: AppColors.primary,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  )
                else if (cases.isEmpty)
                  SliverFillRemaining(
                    hasScrollBody: false,
                    child: Center(
                      child: Text(
                        'لا توجد حالات مطابقة',
                        style: GoogleFonts.cairo(
                          fontWeight: FontWeight.w700,
                          color: DoctorUi.muted,
                        ),
                      ),
                    ),
                  )
                else
                  SliverPadding(
                    padding:
                        EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 100),
                    sliver: SliverList.separated(
                      itemCount: cases.length,
                      separatorBuilder: (_, __) =>
                          const SizedBox(height: 12),
                      itemBuilder: (context, index) {
                        final item = cases[index];
                        return MedicalCaseCard(
                          medicalCase: item,
                          onViewDetails: () => _openDetail(item),
                        );
                      },
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _TypeFilterBar extends StatelessWidget {
  const _TypeFilterBar({
    required this.selected,
    required this.onChanged,
  });

  final MedicalCaseFilter selected;
  final ValueChanged<MedicalCaseFilter> onChanged;

  static const _labels = {
    MedicalCaseFilter.all: 'الكل',
    MedicalCaseFilter.field: 'الحالات الميدانية',
    MedicalCaseFilter.hospital: 'الحالات داخل المستشفى',
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
            if (entry.key != MedicalCaseFilter.hospital)
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
        borderRadius: BorderRadius.circular(16),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: selected ? AppColors.primary : DoctorUi.border,
              width: 1.2,
            ),
            boxShadow: DoctorUi.softShadow,
          ),
          child: Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 12.5,
              fontWeight: FontWeight.w800,
              color: selected ? Colors.white : DoctorUi.textSecondary,
            ),
          ),
        ),
      ),
    );
  }
}
