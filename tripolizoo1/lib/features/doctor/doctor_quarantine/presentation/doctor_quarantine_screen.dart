import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/quarantine_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/widgets/quarantine_card.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorQuarantineScreen extends StatefulWidget {
  const DoctorQuarantineScreen({super.key});

  @override
  State<DoctorQuarantineScreen> createState() => _DoctorQuarantineScreenState();
}

class _DoctorQuarantineScreenState extends State<DoctorQuarantineScreen> {
  static const _bg = Color(0xFFF5F5F5);
  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  final _searchController = TextEditingController();

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
    final records = context.watch<QuarantineProvider>().filtered(
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
                      'الحجر الصحي',
                      style: GoogleFonts.cairo(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF1A1A1A),
                        height: 1.2,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'متابعة الحيوانات قيد الحجر الصحي',
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
                        hintText: 'ابحث بالرقم المؤقت أو نوع الحيوان',
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
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
            if (records.isEmpty)
              SliverFillRemaining(
                hasScrollBody: false,
                child: Center(
                  child: Text(
                    'لا توجد سجلات مطابقة',
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
                  itemCount: records.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 12),
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
    );
  }
}
