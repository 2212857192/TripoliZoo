import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class WhitePinnedSliverHeader extends StatelessWidget {
  const WhitePinnedSliverHeader({
    super.key,
    required this.child,
    required this.toolbarHeight,
  });

  final Widget child;
  final double toolbarHeight;

  @override
  Widget build(BuildContext context) {
    return SliverAppBar(
      pinned: true,
      floating: false,
      automaticallyImplyLeading: false,
      toolbarHeight: toolbarHeight,
      backgroundColor: Colors.white,
      foregroundColor: Colors.black87,
      surfaceTintColor: Colors.white,
      systemOverlayStyle: SystemUiOverlayStyle.dark,
      elevation: 0,
      scrolledUnderElevation: 1,
      shadowColor: Colors.black.withValues(alpha: 0.12),
      flexibleSpace: SafeArea(
        bottom: false,
        child: child,
      ),
    );
  }
}

class CenteredPageHeader extends StatelessWidget {
  const CenteredPageHeader({
    super.key,
    required this.title,
    this.leading,
    this.trailing,
  });

  final String title;
  final Widget? leading;
  final Widget? trailing;

  @override
  Widget build(BuildContext context) {
    return Stack(
      alignment: Alignment.center,
      children: [
        Center(
          child: Text(
            title,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            textAlign: TextAlign.center,
            style: GoogleFonts.cairo(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.primaryDark,
            ),
          ),
        ),
        if (leading != null)
          PositionedDirectional(
            start: 0,
            child: leading!,
          ),
        if (trailing != null)
          PositionedDirectional(
            end: 0,
            child: trailing!,
          ),
      ],
    );
  }
}
