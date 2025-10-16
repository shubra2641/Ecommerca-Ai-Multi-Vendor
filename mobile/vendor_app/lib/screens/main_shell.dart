import 'package:flutter/material.dart';
import 'dashboard_screen.dart';
import 'products_screen.dart';
import 'orders_screen.dart';
import 'notifications_screen.dart';
import 'settings_screen.dart';
import 'profile_screen.dart';
import 'package:provider/provider.dart';
import '../l10n/app_localizations.dart';
import '../providers/auth_provider.dart';
import '../providers/notification_provider.dart';

class MainShell extends StatefulWidget {
  const MainShell({super.key});

  @override
  State<MainShell> createState() => _MainShellState();
}

class _MainShellState extends State<MainShell> {
  int _index = 0;
  final _pages = [const DashboardScreen(), const ProductsScreen(), const OrdersScreen(), const NotificationsScreen(), const ProfileScreen()];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(AppLocalizations.of(context)!.vendorAdmin)),
      drawer: Drawer(
        child: SafeArea(
          child: Consumer<AuthProvider>(builder: (ctx, auth, _) {
            final email = auth.token != null ? AppLocalizations.of(context)!.signedIn : AppLocalizations.of(context)!.notSigned;
            return Column(children: [
              UserAccountsDrawerHeader(accountName: Text(AppLocalizations.of(context)!.vendor), accountEmail: Text(email)),
              ListTile(leading: const Icon(Icons.dashboard), title: Text(AppLocalizations.of(context)!.dashboard), onTap: () { setState(() { _index = 0; }); Navigator.of(context).pop(); }),
              ListTile(leading: const Icon(Icons.inventory), title: Text(AppLocalizations.of(context)!.products), onTap: () { setState(() { _index = 1; }); Navigator.of(context).pop(); }),
              ListTile(leading: const Icon(Icons.receipt_long), title: Text(AppLocalizations.of(context)!.orders), onTap: () { setState(() { _index = 2; }); Navigator.of(context).pop(); }),
              Consumer<NotificationProvider>(
                builder: (context, notificationProvider, child) {
                  return ListTile(
                    leading: Stack(
                      children: [
                        const Icon(Icons.notifications),
                        if (notificationProvider.unreadCount > 0)
                          Positioned(
                            right: 0,
                            top: 0,
                            child: Container(
                              padding: const EdgeInsets.all(2),
                              decoration: BoxDecoration(
                                color: Theme.of(context).colorScheme.error,
                                borderRadius: BorderRadius.circular(10),
                              ),
                              constraints: const BoxConstraints(
                                minWidth: 16,
                                minHeight: 16,
                              ),
                              child: Text(
                                notificationProvider.unreadCount > 99 ? '99+' : notificationProvider.unreadCount.toString(),
                                style: TextStyle(
                                  color: Theme.of(context).colorScheme.onError,
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ),
                          ),
                      ],
                    ),
                    title: Text(AppLocalizations.of(context)!.notifications),
                    onTap: () {
                      setState(() { _index = 3; });
                      Navigator.of(context).pop();
                    },
                  );
                },
              ),
              // Balance entry removed per request
              ListTile(leading: const Icon(Icons.history), title: Text(AppLocalizations.of(context)!.withdrawals), onTap: () { Navigator.of(context).pop(); Navigator.of(context).pushNamed('/withdrawals'); }),
              const Spacer(),
              ListTile(leading: const Icon(Icons.settings), title: Text(AppLocalizations.of(context)!.settings), onTap: () { Navigator.of(context).pop(); Navigator.of(context).push(MaterialPageRoute(builder: (_) => const SettingsScreen())); }),
              ListTile(leading: const Icon(Icons.logout), title: Text(AppLocalizations.of(context)!.logout), onTap: () { auth.signOut(); Navigator.of(context).pushNamedAndRemoveUntil('/login', (r) => false); }),
            ]);
          }),
        ),
      ),
      body: _pages[_index],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _index,
        onTap: (i) => setState(() { _index = i; }),
        items: [
          BottomNavigationBarItem(icon: const Icon(Icons.dashboard), label: AppLocalizations.of(context)!.dashboard),
          BottomNavigationBarItem(icon: const Icon(Icons.inventory), label: AppLocalizations.of(context)!.products),
          BottomNavigationBarItem(icon: const Icon(Icons.receipt_long), label: AppLocalizations.of(context)!.orders),
          BottomNavigationBarItem(
            icon: Consumer<NotificationProvider>(
              builder: (context, notificationProvider, child) {
                return Stack(
                  children: [
                    const Icon(Icons.notifications),
                    if (notificationProvider.unreadCount > 0)
                      Positioned(
                        right: 0,
                        top: 0,
                        child: Container(
                          padding: const EdgeInsets.all(2),
                          decoration: BoxDecoration(
                            color: Theme.of(context).colorScheme.error,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          constraints: const BoxConstraints(
                            minWidth: 12,
                            minHeight: 12,
                          ),
                          child: Text(
                            notificationProvider.unreadCount > 9 ? '9+' : notificationProvider.unreadCount.toString(),
                            style: TextStyle(
                              color: Theme.of(context).colorScheme.onError,
                              fontSize: 8,
                              fontWeight: FontWeight.bold,
                            ),
                            textAlign: TextAlign.center,
                          ),
                        ),
                      ),
                  ],
                );
              },
            ),
            label: AppLocalizations.of(context)!.notifications,
          ),
          BottomNavigationBarItem(icon: const Icon(Icons.person), label: AppLocalizations.of(context)!.profile),
        ],
      ),
    );
  }
}
