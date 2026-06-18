import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/quarantine_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/widgets/quarantine_card.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorQuarantineScreen extends StatefulWidget {
  const DoctorQuarantineScreen({super.key});

  @override
  State<DoctorQuarantineScreen> createState() => _DoctorQuarantineScreenState();
}

class _DoctorQuarantineScreenState extends State<DoctorQuarantineScreen> {
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<QuarantineProvider>().load();
    });
  }

  Future<void> _refresh() async {
    await context.read<QuarantineProvider>().load();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _openDetail(String id) {
    context.push('/doctor/quarantine/$id');
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final provider = context.watch<QuarantineProvider>();
    final records = provider.filtered(query: _searchController.text);

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: DoctorUi.background,
        body: SafeArea(
          top: false,
          bottom: false,
          child: RefreshIndicator(
            onRefresh: _refresh,
            color: AppColors.primary,
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
                          bottom:
                              BorderSide(color: DoctorUi.border, width: 1.5),
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
                            'الحجر الصحي',
                            style: GoogleFonts.cairo(
                              fontSize: 22,
                              fontWeight: FontWeight.w900,
                              color: DoctorUi.textPrimary,
                              height: 1.2,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'متابعة الحيوانات قيد الحجر الصحي',
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

                // ── Search Bar ──
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 18, 20, 18),
                    child: Container(
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
                          hintText: 'ابحث بالرقم المؤقت أو نوع الحيوان',
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
                  ),
                ),

                // ── List / States ──
                if (provider.isLoading && records.isEmpty)
                  const SliverFillRemaining(
                    hasScrollBody: false,
                    child: Center(child: CircularProgressIndicator()),
                  )
                else if (provider.error != null && records.isEmpty)
                  SliverFillRemaining(
                    hasScrollBody: false,
                    child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(
                            Icons.cloud_off_outlined,
                            size: 48,
                            color: DoctorUi.muted,
                          ),
                          const SizedBox(height: 12),
                          Text(
                            'تعذّر تحميل بيانات الحجر الصحي',
                            textAlign: TextAlign.center,
                            style: GoogleFonts.cairo(
                              fontSize: 15,
                              fontWeight: FontWeight.w800,
                              color: DoctorUi.textPrimary,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            provider.error!,
                            textAlign: TextAlign.center,
                            style: GoogleFonts.cairo(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: DoctorUi.muted,
                              height: 1.4,
                            ),
                          ),
                          if (kDebugMode && provider.debugDetail != null) ...[
                            const SizedBox(height: 12),
                            Container(
                              width: double.infinity,
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(
                                color: const Color(0xFFF3F4F6),
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(color: DoctorUi.border),
                              ),
                              child: Text(
                                provider.debugDetail!,
                                style: GoogleFonts.robotoMono(
                                  fontSize: 10,
                                  color: const Color(0xFF374151),
                                  height: 1.35,
                                ),
                              ),
                            ),
                          ],
                          const SizedBox(height: 16),
                          FilledButton.icon(
                            onPressed: provider.isLoading ? null : _refresh,
                            icon: const Icon(Icons.refresh_rounded, size: 18),
                            label: Text(
                              'إعادة المحاولة',
                              style: GoogleFonts.cairo(
                                  fontWeight: FontWeight.w800),
                            ),
                          ),
                        ],
                      ),
                    ),
                  )
                else if (records.isEmpty)
                  SliverFillRemaining(
                    hasScrollBody: false,
                    child: Center(
                      child: Text(
                        'لا توجد سجلات مطابقة',
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
                      itemCount: records.length,
                      separatorBuilder: (_, __) =>
                          const SizedBox(height: 12),
                      itemBuilder: (context, index) {
                        final record = records[index];
                        return QuarantineCard(
                          record: record,
                          onTap: () => _openDetail(record.id),
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
