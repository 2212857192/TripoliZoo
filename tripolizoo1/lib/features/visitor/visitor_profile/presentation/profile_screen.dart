import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/data/visit_info_repository.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/domain/ticket_type.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/providers/locale_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_tickets/presentation/ticket_cart_provider.dart';
import 'package:tripolizoo/shared/utils/date_formatters.dart';
import 'package:tripolizoo/shared/utils/localized_text.dart';
import 'package:tripolizoo/shared/widgets/white_pinned_sliver_header.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  void _openSection(BuildContext context, String section) {
    Navigator.of(context, rootNavigator: true).push(
      MaterialPageRoute<void>(
        builder: (pageContext) {
          final onBack = () => Navigator.of(pageContext).pop();
          final content = switch (section) {
            'info' => _InfoSection(onBack: onBack),
            'tickets' => _TicketsSection(onBack: onBack),
            'language' => _LanguageSection(onBack: onBack),
            'emergency' => _EmergencyNumbersSection(onBack: onBack),
            _ => const SizedBox.shrink(),
          };
          return Scaffold(
            backgroundColor: Colors.white,
            body: content,
          );
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: _MainSection(
        onSelect: (section) => _openSection(context, section),
      ),
    );
  }
}

class _MainSection extends StatelessWidget {
  final ValueChanged<String> onSelect;

  const _MainSection({required this.onSelect});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final hasAccount = context.watch<AuthProvider>().hasAccount;
    final locale = context.watch<LocaleProvider>();
    final isArabic = Localizations.localeOf(context).languageCode == 'ar';
    final name =
        user?.name ?? context.localized(ar: 'زائر الحديقة', en: 'Zoo Guest');

    final localeLabels = {
      AppLocale.ar: 'العربية',
      AppLocale.en: 'English',
    };
    final currentLanguage = localeLabels[locale.locale] ?? 'العربية';

    return Directionality(
      textDirection: isArabic ? TextDirection.rtl : TextDirection.ltr,
      child: CustomScrollView(
        slivers: [
          WhitePinnedSliverHeader(
            toolbarHeight: 72,
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
              child: CenteredPageHeader(
                title: context.localized(ar: 'الحساب', en: 'Account'),
              ),
            ),
          ),
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(24, 24, 24, 100),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                // ── Settings Label ──
                Padding(
                  padding: const EdgeInsets.only(right: 8, bottom: 8),
                  child: Text(
                    context.localized(ar: 'الإعدادات', en: 'SETTINGS'),
                    style: GoogleFonts.cairo(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: Colors.grey.shade500,
                      letterSpacing: 1.5,
                    ),
                  ),
                ),

                // ── Settings Card Group ──
                Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(28),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.03),
                        blurRadius: 16,
                        offset: const Offset(0, 8),
                      ),
                    ],
                  ),
                  child: Column(
                    children: [
                      if (hasAccount) ...[
                        _SettingsItem(
                          icon: Icons.person_outline_rounded,
                          title: context.localized(
                            ar: 'المعلومات الشخصية',
                            en: 'Personal Information',
                          ),
                          subtitle: name,
                          onTap: () => onSelect('info'),
                        ),
                        const Divider(height: 1, indent: 64, endIndent: 20),
                        _SettingsItem(
                          icon: Icons.confirmation_number_outlined,
                          title: context.localized(
                            ar: 'تذاكري الرقمية',
                            en: 'My Digital Tickets',
                          ),
                          onTap: () => onSelect('tickets'),
                        ),
                        const Divider(height: 1, indent: 64, endIndent: 20),
                      ],
                      _SettingsItem(
                        icon: Icons.translate_rounded,
                        title: context.localized(
                          ar: 'إعدادات اللغة',
                          en: 'Language Settings',
                        ),
                        subtitle: currentLanguage,
                        onTap: () => onSelect('language'),
                      ),
                      const Divider(height: 1, indent: 64, endIndent: 20),
                      _SettingsItem(
                        icon: Icons.wifi_calling_3_rounded,
                        title: context.localized(
                          ar: 'أرقام الطوارئ',
                          en: 'Emergency Numbers',
                        ),
                        onTap: () => onSelect('emergency'),
                        isLast: true,
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),

                // ── Logout Card ──
                Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(28),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.03),
                        blurRadius: 16,
                        offset: const Offset(0, 8),
                      ),
                    ],
                  ),
                  child: _SettingsItem(
                    icon: Icons.logout_rounded,
                    iconColor: const Color(0xFFE65100),
                    iconBgColor: const Color(0xFFFFF3E0),
                    title: hasAccount
                        ? context.localized(
                            ar: 'تسجيل الخروج',
                            en: 'Log Out',
                          )
                        : context.localized(
                            ar: 'تسجيل الدخول أو إنشاء حساب',
                            en: 'Sign In or Create Account',
                          ),
                    titleColor: const Color(0xFFE65100),
                    isLast: true,
                    onTap: () {
                      if (hasAccount) {
                        context.read<AuthProvider>().logout();
                      }
                      context.go('/login');
                    },
                  ),
                ),
                const SizedBox(height: 32),

                // ── Footer ──
                Center(
                  child: Text(
                    'Tripoli Zoo - v1.0.0',
                    style: GoogleFonts.cairo(
                      fontSize: 12,
                      color: Colors.grey.shade400,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ]),
            ),
          ),
        ],
      ),
    );
  }
}

class _InfoSection extends StatefulWidget {
  final VoidCallback onBack;

  const _InfoSection({required this.onBack});

  @override
  State<_InfoSection> createState() => _InfoSectionState();
}

class _InfoSectionState extends State<_InfoSection> {
  final _profileFormKey = GlobalKey<FormState>();
  final _passwordFormKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _currentPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _controllersInitialized = false;
  bool _passwordExpanded = false;
  bool _hideCurrentPassword = true;
  bool _hideNewPassword = true;
  bool _hideConfirmPassword = true;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_controllersInitialized) return;
    final user = context.read<AuthProvider>().user;
    _nameController.text = user?.name ?? '';
    _emailController.text = user?.email ?? '';
    _phoneController.text = user?.phone ?? '';
    _controllersInitialized = true;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _currentPasswordController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  String? _requiredValidator(String? value) {
    if (value == null || value.trim().isEmpty) {
      return context.localized(
        ar: 'هذا الحقل مطلوب',
        en: 'This field is required',
      );
    }
    return null;
  }

  String? _emailValidator(String? value) {
    final requiredError = _requiredValidator(value);
    if (requiredError != null) return requiredError;
    if (!RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$').hasMatch(value!.trim())) {
      return context.localized(
        ar: 'أدخل بريدًا إلكترونيًا صحيحًا',
        en: 'Enter a valid email address',
      );
    }
    return null;
  }

  void _saveProfile() {
    if (!(_profileFormKey.currentState?.validate() ?? false)) return;
    context.read<AuthProvider>().updateProfile(
          name: _nameController.text.trim(),
          email: _emailController.text.trim(),
          phone: _phoneController.text.trim(),
        );
    FocusScope.of(context).unfocus();
    _showMessage(
      context.localized(
        ar: 'تم تحديث المعلومات الشخصية',
        en: 'Personal information updated',
      ),
    );
  }

  Future<void> _changePassword() async {
    if (!(_passwordFormKey.currentState?.validate() ?? false)) return;

    final auth = context.read<AuthProvider>();
    final changed = await auth.changePassword(
      currentPassword: _currentPasswordController.text,
      newPassword: _newPasswordController.text,
    );
    if (!mounted) return;

    if (changed) {
      _currentPasswordController.clear();
      _newPasswordController.clear();
      _confirmPasswordController.clear();
      FocusScope.of(context).unfocus();
      _showMessage(
        context.localized(
          ar: 'تم تغيير كلمة المرور بنجاح',
          en: 'Password changed successfully',
        ),
      );
      return;
    }

    final error = switch (auth.error) {
      'كلمة المرور الحالية غير صحيحة' => context.localized(
          ar: 'كلمة المرور الحالية غير صحيحة',
          en: 'The current password is incorrect',
        ),
      'كلمة المرور الجديدة ضعيفة' => context.localized(
          ar: 'كلمة المرور الجديدة ضعيفة',
          en: 'The new password is too weak',
        ),
      _ => context.localized(
          ar: 'تعذر تغيير كلمة المرور',
          en: 'Unable to change the password',
        ),
    };
    _showMessage(error, isError: true);
  }

  void _showMessage(String message, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, textAlign: TextAlign.center),
        backgroundColor: isError ? Colors.red.shade700 : AppColors.primary,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final auth = context.watch<AuthProvider>();

    return CustomScrollView(
      slivers: [
        _PinnedSubHeader(
          title: context.localized(
            ar: 'معلوماتي الشخصية',
            en: 'Personal Information',
          ),
          onBack: widget.onBack,
        ),
        SliverPadding(
          padding: const EdgeInsets.fromLTRB(24, 24, 24, 40),
          sliver: SliverList(
            delegate: SliverChildListDelegate([
              Form(
                key: _profileFormKey,
                child: Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(24),
                    border: Border.all(color: Colors.grey.shade100),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.04),
                        blurRadius: 14,
                        offset: const Offset(0, 6),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Text(
                        context.localized(
                          ar: 'بيانات الحساب',
                          en: 'Account Details',
                        ),
                        style: GoogleFonts.cairo(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: AppColors.primaryDark,
                        ),
                      ),
                      const SizedBox(height: 16),
                      TextFormField(
                        key: const ValueKey('profile-name-field'),
                        controller: _nameController,
                        textInputAction: TextInputAction.next,
                        decoration: InputDecoration(
                          labelText: context.localized(ar: 'الاسم', en: 'Name'),
                          prefixIcon: const Icon(Icons.person_outline_rounded),
                        ),
                        validator: _requiredValidator,
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        key: const ValueKey('profile-email-field'),
                        controller: _emailController,
                        keyboardType: TextInputType.emailAddress,
                        textInputAction: TextInputAction.next,
                        textDirection: TextDirection.ltr,
                        decoration: InputDecoration(
                          labelText: context.localized(
                            ar: 'البريد الإلكتروني',
                            en: 'Email',
                          ),
                          prefixIcon: const Icon(Icons.email_outlined),
                        ),
                        validator: _emailValidator,
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        key: const ValueKey('profile-phone-field'),
                        controller: _phoneController,
                        keyboardType: TextInputType.phone,
                        textInputAction: TextInputAction.done,
                        textDirection: TextDirection.ltr,
                        decoration: InputDecoration(
                          labelText: context.localized(
                            ar: 'رقم الهاتف',
                            en: 'Phone Number',
                          ),
                          prefixIcon: const Icon(Icons.phone_outlined),
                        ),
                        validator: _requiredValidator,
                        onFieldSubmitted: (_) => _saveProfile(),
                      ),
                      const SizedBox(height: 18),
                      FilledButton.icon(
                        key: const ValueKey('save-profile-button'),
                        onPressed: user == null ? null : _saveProfile,
                        style: FilledButton.styleFrom(
                          backgroundColor: AppColors.primary,
                          minimumSize: const Size.fromHeight(52),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        icon: const Icon(Icons.check_rounded),
                        label: Text(
                          context.localized(
                            ar: 'حفظ البيانات',
                            en: 'Save Details',
                          ),
                          style: GoogleFonts.cairo(
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Material(
                color: Colors.white,
                elevation: 1,
                shadowColor: Colors.black.withValues(alpha: 0.08),
                clipBehavior: Clip.antiAlias,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(24),
                  side: BorderSide(color: Colors.grey.shade100),
                ),
                child: user?.isGuest ?? true
                    ? Padding(
                        padding: const EdgeInsets.all(18),
                        child: Row(
                          children: [
                            const Icon(
                              Icons.lock_outline_rounded,
                              color: AppColors.primary,
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Text(
                                context.localized(
                                  ar: 'سجّل الدخول بحساب لتتمكن من تغيير كلمة المرور.',
                                  en: 'Sign in with an account to change your password.',
                                ),
                                style: GoogleFonts.cairo(
                                  fontSize: 13,
                                  height: 1.5,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                            ),
                          ],
                        ),
                      )
                    : Form(
                        key: _passwordFormKey,
                        child: ExpansionTile(
                          key: const ValueKey('change-password-tile'),
                          initiallyExpanded: _passwordExpanded,
                          onExpansionChanged: (expanded) {
                            setState(() => _passwordExpanded = expanded);
                          },
                          tilePadding:
                              const EdgeInsets.symmetric(horizontal: 18),
                          childrenPadding:
                              const EdgeInsets.fromLTRB(18, 0, 18, 18),
                          leading: const Icon(
                            Icons.lock_reset_rounded,
                            color: AppColors.primary,
                          ),
                          title: Text(
                            context.localized(
                              ar: 'تغيير كلمة المرور',
                              en: 'Change Password',
                            ),
                            style: GoogleFonts.cairo(
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                          children: [
                            TextFormField(
                              key: const ValueKey('current-password-field'),
                              controller: _currentPasswordController,
                              obscureText: _hideCurrentPassword,
                              textInputAction: TextInputAction.next,
                              decoration: InputDecoration(
                                labelText: context.localized(
                                  ar: 'كلمة المرور الحالية',
                                  en: 'Current Password',
                                ),
                                prefixIcon:
                                    const Icon(Icons.lock_outline_rounded),
                                suffixIcon: IconButton(
                                  onPressed: () => setState(() {
                                    _hideCurrentPassword =
                                        !_hideCurrentPassword;
                                  }),
                                  icon: Icon(
                                    _hideCurrentPassword
                                        ? Icons.visibility_outlined
                                        : Icons.visibility_off_outlined,
                                  ),
                                ),
                              ),
                              validator: _requiredValidator,
                            ),
                            const SizedBox(height: 12),
                            TextFormField(
                              key: const ValueKey('new-password-field'),
                              controller: _newPasswordController,
                              obscureText: _hideNewPassword,
                              textInputAction: TextInputAction.next,
                              decoration: InputDecoration(
                                labelText: context.localized(
                                  ar: 'كلمة المرور الجديدة',
                                  en: 'New Password',
                                ),
                                prefixIcon: const Icon(Icons.password_rounded),
                                suffixIcon: IconButton(
                                  onPressed: () => setState(() {
                                    _hideNewPassword = !_hideNewPassword;
                                  }),
                                  icon: Icon(
                                    _hideNewPassword
                                        ? Icons.visibility_outlined
                                        : Icons.visibility_off_outlined,
                                  ),
                                ),
                              ),
                              validator: (value) {
                                final requiredError = _requiredValidator(value);
                                if (requiredError != null) return requiredError;
                                if (value!.length < 8) {
                                  return context.localized(
                                    ar: 'يجب أن تكون 8 أحرف على الأقل',
                                    en: 'Use at least 8 characters',
                                  );
                                }
                                return null;
                              },
                            ),
                            const SizedBox(height: 12),
                            TextFormField(
                              key: const ValueKey('confirm-password-field'),
                              controller: _confirmPasswordController,
                              obscureText: _hideConfirmPassword,
                              textInputAction: TextInputAction.done,
                              decoration: InputDecoration(
                                labelText: context.localized(
                                  ar: 'تأكيد كلمة المرور الجديدة',
                                  en: 'Confirm New Password',
                                ),
                                prefixIcon: const Icon(Icons.password_rounded),
                                suffixIcon: IconButton(
                                  onPressed: () => setState(() {
                                    _hideConfirmPassword =
                                        !_hideConfirmPassword;
                                  }),
                                  icon: Icon(
                                    _hideConfirmPassword
                                        ? Icons.visibility_outlined
                                        : Icons.visibility_off_outlined,
                                  ),
                                ),
                              ),
                              validator: (value) {
                                final requiredError = _requiredValidator(value);
                                if (requiredError != null) return requiredError;
                                if (value != _newPasswordController.text) {
                                  return context.localized(
                                    ar: 'كلمتا المرور غير متطابقتين',
                                    en: 'Passwords do not match',
                                  );
                                }
                                return null;
                              },
                              onFieldSubmitted: (_) => _changePassword(),
                            ),
                            const SizedBox(height: 18),
                            FilledButton(
                              key: const ValueKey('change-password-button'),
                              onPressed:
                                  auth.isLoading ? null : _changePassword,
                              style: FilledButton.styleFrom(
                                backgroundColor: AppColors.primary,
                                minimumSize: const Size.fromHeight(50),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(16),
                                ),
                              ),
                              child: auth.isLoading
                                  ? const SizedBox(
                                      width: 22,
                                      height: 22,
                                      child: CircularProgressIndicator(
                                        strokeWidth: 2,
                                        color: Colors.white,
                                      ),
                                    )
                                  : Text(
                                      context.localized(
                                        ar: 'تغيير كلمة المرور',
                                        en: 'Change Password',
                                      ),
                                      style: GoogleFonts.cairo(
                                        fontWeight: FontWeight.w800,
                                      ),
                                    ),
                            ),
                          ],
                        ),
                      ),
              ),
            ]),
          ),
        ),
      ],
    );
  }
}

class _TicketsSection extends StatelessWidget {
  final VoidCallback onBack;

  const _TicketsSection({required this.onBack});

  void _openTicket(BuildContext context, PurchasedTicket ticket) {
    Navigator.of(context, rootNavigator: true).push(
      MaterialPageRoute<void>(
        builder: (_) => _TicketPreviewScreen(ticket: ticket),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final tickets = context.watch<TicketCartProvider>().purchasedTickets;

    return CustomScrollView(
      slivers: [
        _PinnedSubHeader(
          title: context.localized(
            ar: 'تذاكري الرقمية',
            en: 'My Digital Tickets',
          ),
          onBack: onBack,
        ),
        SliverPadding(
          padding: const EdgeInsets.fromLTRB(24, 24, 24, 40),
          sliver: SliverList(
            delegate: SliverChildListDelegate([
              if (tickets.isEmpty)
                const Center(
                  child: Padding(
                    padding: EdgeInsets.all(40),
                    child: Column(
                      children: [
                        Icon(Icons.confirmation_number_outlined,
                            size: 64, color: Colors.grey),
                        SizedBox(height: 16),
                        Text('لا توجد تذاكر حالياً',
                            style: TextStyle(color: AppColors.textSecondary)),
                      ],
                    ),
                  ),
                )
              else
                ...tickets.reversed.map(
                  (ticket) => Padding(
                    padding: const EdgeInsets.only(bottom: 12),
                    child: Material(
                      color: AppColors.background,
                      borderRadius: BorderRadius.circular(20),
                      child: InkWell(
                        onTap: () => _openTicket(context, ticket),
                        borderRadius: BorderRadius.circular(20),
                        child: Ink(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(color: Colors.grey.shade100),
                          ),
                          child: Row(
                            children: [
                              Text(
                                '${ticket.price} د.ل',
                                style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.primary,
                                ),
                              ),
                              const SizedBox(width: 10),
                              const Icon(
                                Icons.arrow_back_ios_new_rounded,
                                size: 14,
                                color: AppColors.textSecondary,
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.end,
                                  children: [
                                    Text(
                                      ticket.typeTitle,
                                      style: const TextStyle(
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    Text(
                                      '${ticket.visitDate.day}/${ticket.visitDate.month}/${ticket.visitDate.year} · ${ticket.id}',
                                      maxLines: 1,
                                      style: const TextStyle(
                                        fontSize: 12,
                                        color: AppColors.textSecondary,
                                      ),
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
            ]),
          ),
        ),
      ],
    );
  }
}

class _TicketPreviewScreen extends StatelessWidget {
  final PurchasedTicket ticket;

  const _TicketPreviewScreen({required this.ticket});

  @override
  Widget build(BuildContext context) {
    final languageCode = Localizations.localeOf(context).languageCode;
    final isArabic = languageCode == 'ar';

    return Scaffold(
      key: ValueKey('ticket-preview-${ticket.id}'),
      backgroundColor: const Color(0xFFF2F6F2),
      body: Directionality(
        textDirection: isArabic ? TextDirection.rtl : TextDirection.ltr,
        child: CustomScrollView(
          slivers: [
            WhitePinnedSliverHeader(
              toolbarHeight: 64,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: CenteredPageHeader(
                  title:
                      context.localized(ar: 'صورة التذكرة', en: 'Ticket Image'),
                  leading: IconButton(
                    onPressed: () => Navigator.of(context).pop(),
                    icon: const Icon(Icons.arrow_forward_ios_rounded),
                  ),
                ),
              ),
            ),
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(24, 24, 24, 40),
              sliver: SliverList(
                delegate: SliverChildListDelegate([
                  Container(
                    clipBehavior: Clip.antiAlias,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(28),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.08),
                          blurRadius: 22,
                          offset: const Offset(0, 10),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.symmetric(vertical: 24),
                          color: AppColors.primary,
                          child: Column(
                            children: [
                              Text(
                                AppConstants.appName,
                                style: GoogleFonts.cairo(
                                  color: Colors.white,
                                  fontSize: 22,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                context.localized(
                                  ar: 'تذكرة دخول',
                                  en: 'Entry Ticket',
                                ),
                                style: GoogleFonts.cairo(
                                  color: Colors.white70,
                                  fontSize: 14,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ),
                        Padding(
                          padding: const EdgeInsets.all(28),
                          child: Column(
                            children: [
                              QrImageView(
                                data: ticket.qrData,
                                version: QrVersions.auto,
                                size: 220,
                                eyeStyle: const QrEyeStyle(
                                  eyeShape: QrEyeShape.square,
                                  color: AppColors.primary,
                                ),
                                dataModuleStyle: const QrDataModuleStyle(
                                  dataModuleShape: QrDataModuleShape.square,
                                  color: AppColors.primary,
                                ),
                              ),
                              const SizedBox(height: 14),
                              Text(
                                context.localized(
                                  ar: 'رقم التذكرة',
                                  en: 'Ticket Number',
                                ),
                                style: GoogleFonts.cairo(
                                  color: AppColors.textSecondary,
                                  fontSize: 12,
                                ),
                              ),
                              Text(
                                ticket.id,
                                textDirection: TextDirection.ltr,
                                style: GoogleFonts.cairo(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const Divider(height: 1),
                        Padding(
                          padding: const EdgeInsets.all(22),
                          child: Column(
                            children: [
                              _TicketPreviewRow(
                                label: context.localized(
                                  ar: 'الفئة',
                                  en: 'Category',
                                ),
                                value: ticket.localizedCategoryLabel(
                                  languageCode,
                                ),
                              ),
                              const SizedBox(height: 12),
                              _TicketPreviewRow(
                                label: context.localized(
                                    ar: 'التاريخ', en: 'Date'),
                                value: formatArabicDate(ticket.visitDate),
                              ),
                              const SizedBox(height: 12),
                              _TicketPreviewRow(
                                label:
                                    context.localized(ar: 'الوقت', en: 'Time'),
                                value: AppConstants.workingHours,
                              ),
                              const SizedBox(height: 12),
                              _TicketPreviewRow(
                                label:
                                    context.localized(ar: 'السعر', en: 'Price'),
                                value:
                                    '${ticket.price} ${context.localized(ar: 'د.ل', en: 'LYD')}',
                                highlighted: true,
                              ),
                            ],
                          ),
                        ),
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(16),
                          color: const Color(0xFFE8F5E9),
                          child: Text(
                            context.localized(
                              ar: 'صالحة لدخول شخص واحد فقط',
                              en: 'Valid for one person only',
                            ),
                            textAlign: TextAlign.center,
                            style: GoogleFonts.cairo(
                              color: AppColors.primary,
                              fontSize: 13,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ]),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _TicketPreviewRow extends StatelessWidget {
  final String label;
  final String value;
  final bool highlighted;

  const _TicketPreviewRow({
    required this.label,
    required this.value,
    this.highlighted = false,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: GoogleFonts.cairo(
            color: AppColors.textSecondary,
            fontSize: 14,
          ),
        ),
        Text(
          value,
          style: GoogleFonts.cairo(
            color: highlighted ? AppColors.primary : AppColors.textPrimary,
            fontSize: 14,
            fontWeight: FontWeight.w800,
          ),
        ),
      ],
    );
  }
}

class _LanguageSection extends StatelessWidget {
  final VoidCallback onBack;

  const _LanguageSection({required this.onBack});

  @override
  Widget build(BuildContext context) {
    final locale = context.watch<LocaleProvider>();

    return CustomScrollView(
      slivers: [
        _PinnedSubHeader(
          title: context.localized(
            ar: 'إعدادات اللغة',
            en: 'Language Settings',
          ),
          onBack: onBack,
        ),
        SliverPadding(
          padding: const EdgeInsets.fromLTRB(24, 24, 24, 40),
          sliver: SliverList(
            delegate: SliverChildListDelegate([
              ...AppLocale.values.map((l) {
                final labels = {
                  AppLocale.ar: 'العربية',
                  AppLocale.en: 'English',
                };
                final selected = locale.locale == l;
                return Container(
                  margin: const EdgeInsets.only(bottom: 10),
                  child: ListTile(
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                      side: BorderSide(
                        color:
                            selected ? AppColors.primary : Colors.grey.shade100,
                        width: selected ? 2 : 1,
                      ),
                    ),
                    title: Text(labels[l]!,
                        textAlign: TextAlign.right,
                        style: TextStyle(
                            fontWeight: selected
                                ? FontWeight.bold
                                : FontWeight.normal)),
                    trailing: selected
                        ? const Icon(Icons.check_circle,
                            color: AppColors.primary)
                        : null,
                    onTap: () => locale.setLocale(l),
                  ),
                );
              }),
            ]),
          ),
        ),
      ],
    );
  }
}

class _EmergencyNumbersSection extends StatefulWidget {
  final VoidCallback onBack;

  const _EmergencyNumbersSection({required this.onBack});

  @override
  State<_EmergencyNumbersSection> createState() =>
      _EmergencyNumbersSectionState();
}

class _EmergencyNumbersSectionState extends State<_EmergencyNumbersSection> {
  String? _ambulancePhone;
  String? _securityPhone;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadPhones();
  }

  Future<void> _loadPhones() async {
    try {
      final info = await ApiVisitInfoRepository().fetch();
      if (!mounted) return;
      setState(() {
        _ambulancePhone = _normalizePhone(
          info.ambulancePhone ?? AppConstants.emergencyMedical,
        );
        _securityPhone = _normalizePhone(
          info.securityPhone ?? AppConstants.emergencySecurity,
        );
        _loading = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _ambulancePhone = AppConstants.emergencyMedical;
        _securityPhone = AppConstants.emergencySecurity;
        _loading = false;
      });
    }
  }

  String _normalizePhone(String value) {
    final trimmed = value.trim();
    if (trimmed.isEmpty) return trimmed;
    final digits = trimmed.replaceAll(RegExp(r'[^0-9+]'), '');
    return digits.isEmpty ? trimmed : digits;
  }

  Future<void> _callNumber(BuildContext context, String number) async {
    final normalized = _normalizePhone(number);
    if (normalized.isEmpty) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'رقم الطوارئ غير متاح حالياً',
            style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
          ),
        ),
      );
      return;
    }

    final dialerUri = Uri(scheme: 'tel', path: normalized);
    var launched = false;
    try {
      launched = await launchUrl(
        dialerUri,
        mode: LaunchMode.externalApplication,
      );
    } catch (_) {
      launched = false;
    }
    if (!context.mounted || launched) return;

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          'تعذر بدء الاتصال بالرقم $normalized',
          style: GoogleFonts.cairo(fontWeight: FontWeight.w700),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: CustomScrollView(
        slivers: [
          _PinnedSubHeader(
            title: context.localized(
              ar: 'أرقام الطوارئ',
              en: 'Emergency Numbers',
            ),
            onBack: widget.onBack,
          ),
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(24, 24, 24, 40),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                Text(
                  'للحالات العاجلة داخل الحديقة، اختر الجهة المناسبة للاتصال مباشرة.',
                  style: GoogleFonts.cairo(
                    fontSize: 14,
                    height: 1.7,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textSecondary,
                  ),
                ),
                const SizedBox(height: 20),
                if (_loading)
                  const Center(child: CircularProgressIndicator())
                else ...[
                  if (_ambulancePhone != null && _ambulancePhone!.isNotEmpty)
                    _EmergencyNumberCard(
                      icon: Icons.medical_services_rounded,
                      title: 'الإسعاف',
                      number: _ambulancePhone!,
                      color: const Color(0xFFDC2626),
                      onTap: () =>
                          _callNumber(context, _ambulancePhone!),
                    ),
                  if (_ambulancePhone != null &&
                      _ambulancePhone!.isNotEmpty &&
                      _securityPhone != null &&
                      _securityPhone!.isNotEmpty)
                    const SizedBox(height: 12),
                  if (_securityPhone != null && _securityPhone!.isNotEmpty)
                    _EmergencyNumberCard(
                      icon: Icons.security_rounded,
                      title: 'الأمن',
                      number: _securityPhone!,
                      color: AppColors.primary,
                      onTap: () =>
                          _callNumber(context, _securityPhone!),
                    ),
                ],
              ]),
            ),
          ),
        ],
      ),
    );
  }
}

class _EmergencyNumberCard extends StatelessWidget {
  final IconData icon;
  final String title;
  final String number;
  final Color color;
  final VoidCallback onTap;

  const _EmergencyNumberCard({
    required this.icon,
    required this.title,
    required this.number,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(24),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(24),
        child: Container(
          padding: const EdgeInsets.all(18),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: color.withValues(alpha: 0.12)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.03),
                blurRadius: 16,
                offset: const Offset(0, 6),
              ),
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, color: color, size: 23),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.cairo(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF1A1A1A),
                      ),
                    ),
                    Directionality(
                      textDirection: TextDirection.ltr,
                      child: Text(
                        number,
                        style: GoogleFonts.cairo(
                          fontSize: 19,
                          fontWeight: FontWeight.w800,
                          color: color,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: Text(
                  'اتصال',
                  style: GoogleFonts.cairo(
                    fontSize: 12,
                    fontWeight: FontWeight.w800,
                    color: color,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _SubHeader extends StatelessWidget {
  final String title;
  final VoidCallback onBack;
  final Widget? trailing;

  const _SubHeader({
    required this.title,
    required this.onBack,
    this.trailing,
  });

  @override
  Widget build(BuildContext context) {
    return CenteredPageHeader(
      title: title,
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_ios_new),
        onPressed: onBack,
      ),
      trailing: trailing,
    );
  }
}

class _PinnedSubHeader extends StatelessWidget {
  final String title;
  final VoidCallback onBack;
  final Widget? trailing;

  const _PinnedSubHeader({
    required this.title,
    required this.onBack,
    this.trailing,
  });

  @override
  Widget build(BuildContext context) {
    return WhitePinnedSliverHeader(
      toolbarHeight: 72,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
        child: _SubHeader(
          title: title,
          onBack: onBack,
          trailing: trailing,
        ),
      ),
    );
  }
}

class _MenuTile extends StatelessWidget {
  final IconData icon;
  final Color color;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  const _MenuTile({
    required this.icon,
    required this.color,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Material(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(24),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(24),
          child: Ink(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: Colors.grey.shade100),
              boxShadow: [
                BoxShadow(
                    color: Colors.black.withValues(alpha: 0.03),
                    blurRadius: 10),
              ],
            ),
            child: Row(
              children: [
                Icon(Icons.arrow_back_ios_new,
                    size: 14, color: Colors.grey.shade400),
                const Spacer(),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(title,
                        style: const TextStyle(fontWeight: FontWeight.bold)),
                    if (subtitle.isNotEmpty)
                      Text(subtitle,
                          style: const TextStyle(
                              fontSize: 11, color: AppColors.textSecondary)),
                  ],
                ),
                const SizedBox(width: 14),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(icon, color: color, size: 22),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _SettingsItem extends StatelessWidget {
  final IconData icon;
  final String title;
  final String? subtitle;
  final VoidCallback onTap;
  final Color? iconColor;
  final Color? iconBgColor;
  final Color? titleColor;
  final bool isLast;

  const _SettingsItem({
    required this.icon,
    required this.title,
    this.subtitle,
    required this.onTap,
    this.iconColor,
    this.iconBgColor,
    this.titleColor,
    this.isLast = false,
  });

  @override
  Widget build(BuildContext context) {
    final defaultIconColor = const Color(0xFF2E7D32);
    final defaultIconBgColor = const Color(0xFFE8F5E9);

    return InkWell(
      onTap: onTap,
      borderRadius: isLast
          ? const BorderRadius.only(
              bottomLeft: Radius.circular(28),
              bottomRight: Radius.circular(28),
            )
          : null,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        child: Row(
          children: [
            // Icon
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: iconBgColor ?? defaultIconBgColor,
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon,
                color: iconColor ?? defaultIconColor,
                size: 20,
              ),
            ),
            const SizedBox(width: 16),
            // Title & Subtitle
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: GoogleFonts.cairo(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: titleColor ?? const Color(0xFF1A1A1A),
                    ),
                  ),
                  if (subtitle != null) ...[
                    const SizedBox(height: 2),
                    Text(
                      subtitle!,
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        color: Colors.grey.shade400,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ],
              ),
            ),
            // Chevron arrow (RTL points left)
            Icon(
              Icons.arrow_back_ios_new_rounded,
              size: 14,
              color: titleColor ?? Colors.grey.shade300,
            ),
          ],
        ),
      ),
    );
  }
}
