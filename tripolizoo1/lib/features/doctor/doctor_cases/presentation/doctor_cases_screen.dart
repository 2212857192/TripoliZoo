import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/forms/open_field_case_form_sheet.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/widgets/medical_case_card.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_form_launcher.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorCasesScreen extends StatefulWidget {
  const DoctorCasesScreen({super.key});

  @override
  State<DoctorCasesScreen> createState() => _DoctorCasesScreenState();
}

class _DoctorCasesScreenState extends State<DoctorCasesScreen> {
  static const _bg = Color(0xFFF5F5F5);
  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  final _searchController = TextEditingController();
  MedicalCaseFilter _filter = MedicalCaseFilter.all;

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
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
    final cases = context.watch<MedicalCasesProvider>().filtered(
          filter: _filter,
          query: _searchController.text,
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
                      'الحالات',
                      style: GoogleFonts.cairo(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF1A1A1A),
                        height: 1.2,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'متابعة الحالات الميدانية وحالات المستشفى',
                      style: GoogleFonts.cairo(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: _muted,
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
                        hintText: 'ابحث برقم الحيوان أو نوع الحيوان',
                        hintStyle: GoogleFonts.cairo(
                          fontSize: 13.5,
                          fontWeight: FontWeight.w500,
                          color: _muted,
                        ),
                        filled: true,
                        fillColor: Colors.white,
                        prefixIcon: const Icon(
                          Icons.search_rounded,
                          color: _muted,
                          size: 22,
                        ),
                        contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14,
                          vertical: 14,
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
                    _TypeFilterBar(
                      selected: _filter,
                      onChanged: (v) => setState(() => _filter = v),
                    ),
                    const SizedBox(height: 16),
                    Material(
                      color: AppColors.primaryDark,
                      borderRadius: BorderRadius.circular(14),
                      child: InkWell(
                        onTap: _openFieldCaseForm,
                        borderRadius: BorderRadius.circular(14),
                        child: Padding(
                          padding: const EdgeInsets.symmetric(vertical: 14),
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
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
            if (cases.isEmpty)
              SliverFillRemaining(
                hasScrollBody: false,
                child: Center(
                  child: Text(
                    'لا توجد حالات مطابقة',
                    style: GoogleFonts.cairo(
                      fontWeight: FontWeight.w700,
                      color: _muted,
                    ),
                  ),
                ),
              )
            else
              SliverPadding(
                padding: EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 100),
                sliver: SliverList.separated(
                  itemCount: cases.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 12),
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
