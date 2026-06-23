import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:video_player/video_player.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/router/post_login_route.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late VideoPlayerController _videoController;
  bool _isVideoInitialized = false;
  late AnimationController _fadeController;
  late Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    );
    _fadeAnimation = CurvedAnimation(
      parent: _fadeController,
      curve: Curves.easeIn,
    );
    _initializeVideo();
  }

  Future<void> _initializeVideo() async {
    _videoController = VideoPlayerController.asset('assets/welcome.MP4');
    try {
      await _videoController.initialize();
      await _videoController.setVolume(0.0);
      _videoController.setLooping(false);
      _videoController.play();
      _videoController.addListener(_videoListener);
      if (mounted) {
        setState(() => _isVideoInitialized = true);
        _fadeController.forward();
      }
    } catch (e) {
      debugPrint('Video error: $e');
      _navigateToNextScreen();
    }
  }

  void _videoListener() {
    if (_videoController.value.position >= _videoController.value.duration) {
      _videoController.removeListener(_videoListener);
      _navigateToNextScreen();
    }
  }

  void _navigateToNextScreen() {
    if (!mounted) return;
    final auth = context.read<AuthProvider>();
    if (auth.isAuthenticated) {
      context.go(postLoginRoute(auth.user));
    } else {
      context.go('/login');
    }
  }

  @override
  void dispose() {
    _videoController.removeListener(_videoListener);
    _videoController.dispose();
    _fadeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.primaryDark,
      body: Stack(
        fit: StackFit.expand,
        children: [
          // ── 1. فيديو كامل الشاشة ──────────────────────────────
          if (_isVideoInitialized)
            FittedBox(
              fit: BoxFit.cover,
              clipBehavior: Clip.hardEdge,
              child: SizedBox(
                width: _videoController.value.size.width,
                height: _videoController.value.size.height,
                child: VideoPlayer(_videoController),
              ),
            )
          else
            Container(color: AppColors.primaryDark),

          // ── 2. Overlay فقط في الأسفل ──────────────────────────
          Positioned(
            left: 0,
            right: 0,
            bottom: 0,
            height: MediaQuery.of(context).size.height * 0.52,
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.transparent,
                    AppColors.primaryDark.withValues(alpha: 0.55),
                    AppColors.primaryDark.withValues(alpha: 0.88),
                    AppColors.primaryDark.withValues(alpha: 0.97),
                  ],
                  stops: const [0.0, 0.35, 0.65, 1.0],
                ),
              ),
            ),
          ),

          // ── 3. المحتوى في الأسفل ────────────────────────────────
          FadeTransition(
            opacity: _fadeAnimation,
            child: SafeArea(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  // شعار دائري مع إطار أخضر مميز مثل الصورة تماماً
                  Container(
                    width: 90,
                    height: 90,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: const Color(0xFF2E7D32),
                        width: 3,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.3),
                          blurRadius: 15,
                          offset: const Offset(0, 5),
                        ),
                      ],
                    ),
                    child: ClipOval(
                      child: Image.asset(
                        'assets/images/app_logo.jpg',
                        fit: BoxFit.cover,
                      ),
                    ),
                  ),

                  const SizedBox(height: 12),

                  // اسم الحديقة بالعربي
                  Text(
                    AppConstants.appName,
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      fontFamily: 'Cairo',
                      color: Colors.white,
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 0.5,
                      shadows: [
                        Shadow(
                          color: Colors.black45,
                          offset: Offset(0, 1.5),
                          blurRadius: 6,
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 4),

                  // اسم بالإنجليزي
                  Text(
                    AppConstants.appNameEn,
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.white.withValues(alpha: 0.75),
                      fontSize: 13,
                      letterSpacing: 1.2,
                      fontWeight: FontWeight.w500,
                      shadows: const [
                        Shadow(
                          color: Colors.black38,
                          offset: Offset(0, 1),
                          blurRadius: 4,
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),

                  // ── زر سهم مزدوج لأسفل بدون خلفية دائرية ────────
                  GestureDetector(
                    onTap: _navigateToNextScreen,
                    child: const Icon(
                      Icons.keyboard_double_arrow_down_rounded,
                      color: Color(0xFF9CCC65), // لون أخضر فاتح/ليموني مطبق مثل الصورة تماماً
                      size: 46,
                    ),
                  ),

                  const SizedBox(height: 16),
                ],
              ),
            ),
          ),


        ],
      ),
    );
  }
}
