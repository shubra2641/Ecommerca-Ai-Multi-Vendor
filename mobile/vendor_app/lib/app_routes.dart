import 'package:flutter/material.dart';
import 'screens/splash_screen.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';
import 'screens/products_screen.dart';
import 'screens/orders_screen.dart';
import 'screens/main_shell.dart';
import 'screens/product_edit_screen.dart';
import 'screens/balance_screen.dart';
import 'screens/withdraw_request_screen.dart';
import 'screens/withdrawals_screen.dart';
import 'screens/settings_screen.dart';
import 'screens/notifications_screen.dart';
import 'screens/account_not_activated_screen.dart';
import 'screens/offline_screen.dart';
import 'screens/maintenance_screen.dart';
import 'screens/profile_screen.dart';

class AppRoutes {
  static const splash = '/';
  static const login = '/login';
  static const dashboard = '/dashboard';
  static const shell = '/shell';
  static const products = '/products';
  static const orders = '/orders';
  static const productEdit = '/products/edit';
  static const balance = '/balance';
  static const withdraw = '/withdraw';
  static const withdrawals = '/withdrawals';
  static const settingsRoute = '/settings';
  static const notifications = '/notifications';
  static const profile = '/profile';
  static const accountNotActivated = '/account-not-activated';
  static const offline = '/offline';
  static const maintenance = '/maintenance';

  static Route<dynamic>? onGenerateRoute(RouteSettings settings) {
    switch (settings.name) {
      case splash:
        return MaterialPageRoute(builder: (_) => const SplashScreen());
      case login:
        return MaterialPageRoute(builder: (_) => const LoginScreen());
      case dashboard:
        return MaterialPageRoute(builder: (_) => const DashboardScreen());
      case shell:
        return MaterialPageRoute(builder: (_) => const MainShell());
      case products:
        return MaterialPageRoute(builder: (_) => const ProductsScreen());
      case orders:
        return MaterialPageRoute(builder: (_) => const OrdersScreen());
      case productEdit:
        final args = settings.arguments;
        return MaterialPageRoute(builder: (_) => ProductEditScreen(product: args is Map<String, dynamic> ? args : null));
      case settingsRoute:
        return MaterialPageRoute(builder: (_) => const SettingsScreen());
      case balance:
        return MaterialPageRoute(builder: (_) => const BalanceScreen());
      case withdraw:
        return MaterialPageRoute(builder: (_) => const WithdrawRequestScreen());
      case withdrawals:
        return MaterialPageRoute(builder: (_) => const WithdrawalsScreen());
      case notifications:
        return MaterialPageRoute(builder: (_) => const NotificationsScreen());
      case profile:
        return MaterialPageRoute(builder: (_) => const ProfileScreen());
      case accountNotActivated:
        return MaterialPageRoute(builder: (_) => const AccountNotActivatedScreen());
      case offline:
        return MaterialPageRoute(builder: (_) => const OfflineScreen());
      case maintenance:
        final args = settings.arguments;
        return MaterialPageRoute(builder: (_) => MaintenanceScreen(
          message: args is Map<String, dynamic> ? args['message'] : null,
          reopenAt: args is Map<String, dynamic> ? args['reopenAt'] : null,
        ));
      default:
        return null;
    }
  }
}
