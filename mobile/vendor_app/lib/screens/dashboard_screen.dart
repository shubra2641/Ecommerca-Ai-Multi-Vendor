// ignore_for_file: deprecated_member_use, use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../l10n/app_localizations.dart';
import '../providers/dashboard_provider.dart';
import '../widgets/dashboard_card.dart';
import '../widgets/chart_widget.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DashboardProvider>().loadData();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).colorScheme.surface,
      body: Consumer<DashboardProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading && provider.data == null) {
            return const Center(
              child: CircularProgressIndicator(),
            );
          }

          if (provider.errorMessage != null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.error_outline,
                    size: 64,
                    color: Theme.of(context).colorScheme.error,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    AppLocalizations.of(context)!.failedToLoadDashboard,
                    style: Theme.of(context).textTheme.headlineSmall,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    provider.errorMessage ?? AppLocalizations.of(context)!.unknownErrorOccurred,
                    style: Theme.of(context).textTheme.bodyMedium,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton.icon(
                    onPressed: () => provider.loadData(),
                    icon: const Icon(Icons.refresh),
                    label: Text(AppLocalizations.of(context)!.retry),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () => provider.refreshData(),
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Welcome Section
                  _buildWelcomeSection(context),
                  const SizedBox(height: 24),
                  
                  // Stats Cards Grid
                  _buildStatsGrid(context, provider),
                  const SizedBox(height: 24),
                  
                  // Charts Section
                  _buildChartsSection(context, provider),
                  const SizedBox(height: 24),
                  
                  // Recent Orders Section
                  _buildRecentOrdersSection(context, provider),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildWelcomeSection(BuildContext context) {
  final localizations = AppLocalizations.of(context);
    final now = DateTime.now();
    final hour = now.hour;
    String greeting;
    if (hour < 12) {
      greeting = localizations.goodMorning;
    } else if (hour < 17) {
      greeting = localizations.goodAfternoon;
    } else {
      greeting = localizations.goodEvening;
    }

    return Card(
      elevation: 2,
      shadowColor: Theme.of(context).colorScheme.shadow.withOpacity(0.1),
      surfaceTintColor: Theme.of(context).colorScheme.primary.withOpacity(0.05),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [
              Theme.of(context).colorScheme.primaryContainer,
              Theme.of(context).colorScheme.primaryContainer.withOpacity(0.8),
            ],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(20),
        ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            greeting,
            style: Theme.of(context).textTheme.headlineMedium?.copyWith(
              color: Theme.of(context).colorScheme.onPrimaryContainer,
              fontWeight: FontWeight.w600,
              letterSpacing: -0.5,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            localizations.welcomeToVendorDashboard,
            style: Theme.of(context).textTheme.bodyLarge?.copyWith(
              color: Theme.of(context).colorScheme.onPrimaryContainer.withOpacity(0.8),
              fontWeight: FontWeight.w400,
            ),
          ),
        ],
      ),
      ),
    );
  }

  Widget _buildStatsGrid(BuildContext context, DashboardProvider provider) {
  final localizations = AppLocalizations.of(context);
    final data = provider.data;
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      childAspectRatio: 1.1,
      children: [
        DashboardCard(
          title: localizations.totalSales,
          value: provider.formattedTotalSales,
          icon: Icons.attach_money,
          color: Colors.green,
          trend: data?.salesGrowth,
        ),
        DashboardCard(
          title: localizations.orders,
          value: provider.totalOrders.toString(),
          icon: Icons.shopping_cart,
          color: Colors.blue,
          trend: data?.ordersGrowth,
        ),
        DashboardCard(
          title: localizations.products,
          value: provider.totalProducts.toString(),
          icon: Icons.inventory,
          color: Colors.orange,
        ),
        DashboardCard(
          title: 'Active Products',
          value: data?.activeProducts.toString() ?? '0',
          icon: Icons.check_circle_outline,
          color: Colors.teal,
        ),
        DashboardCard(
          title: 'Pending Products',
          value: data?.pendingProducts.toString() ?? '0',
          icon: Icons.hourglass_top,
          color: Colors.amber,
        ),
  // Balance card removed per request (use profile page balance)
        DashboardCard(
          title: localizations.pendingWithdrawals,
          value: provider.formattedPendingWithdrawals,
          icon: Icons.account_balance_wallet,
          color: Colors.red,
        ),
      ],
    );
  }

  List<dynamic> _convertChartData(Map<String, dynamic>? chartData) {
    if (chartData == null) return [];
    
    return chartData.entries.map((entry) {
      return {
        'label': entry.key,
        'value': entry.value,
      };
    }).toList();
  }

  Widget _buildChartsSection(BuildContext context, DashboardProvider provider) {
    final localizations = AppLocalizations.of(context)!;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          localizations.salesOverview,
          style: Theme.of(context).textTheme.titleLarge?.copyWith(
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 16),
        Container(
          height: 160,
          width: double.infinity,
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: Theme.of(context).colorScheme.surface,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 10,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: ChartWidget(
            data: _convertChartData(provider.data?.salesChart),
            title: localizations.salesTrend,
          ),
        ),
      ],
    );
  }

  Widget _buildRecentOrdersSection(BuildContext context, DashboardProvider provider) {
    final localizations = AppLocalizations.of(context)!;
    final recentOrders = provider.data?.recentOrders ?? [];
    
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Flexible(
              child: Text(
                localizations.recentOrders,
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
                overflow: TextOverflow.ellipsis,
              ),
            ),
            TextButton(
              onPressed: () {
                // Navigate to orders screen
                Navigator.pushNamed(context, '/orders');
              },
              child: Text(localizations.viewAll),
            ),
          ],
        ),
        const SizedBox(height: 16),
        if (recentOrders.isEmpty)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(32),
            decoration: BoxDecoration(
              color: Theme.of(context).colorScheme.surface,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
              ),
            ),
            child: Column(
              children: [
                Icon(
                  Icons.shopping_cart_outlined,
                  size: 48,
                  color: Theme.of(context).colorScheme.outline,
                ),
                const SizedBox(height: 16),
                Text(
                  localizations.noRecentOrders,
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    color: Theme.of(context).colorScheme.outline,
                  ),
                ),
              ],
            ),
          )
        else
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: recentOrders.length.clamp(0, 5),
            separatorBuilder: (context, index) => const SizedBox(height: 8),
            itemBuilder: (context, index) {
              final order = recentOrders[index];
              return Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.surface,
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.05),
                      blurRadius: 5,
                      offset: const Offset(0, 1),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(
                        color: Theme.of(context).colorScheme.primary.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Icon(
                        Icons.shopping_bag,
                        color: Theme.of(context).colorScheme.primary,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            '${localizations.order} #${order['id']}',
                            style: Theme.of(context).textTheme.titleMedium?.copyWith(
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            order['created_at'] ?? '',
                            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                              color: Theme.of(context).colorScheme.outline,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Flexible(
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: _getStatusColor(order['status']).withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          order['status'] ?? '',
                          style: TextStyle(
                            color: _getStatusColor(order['status']),
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
      ],
    );
  }

  Color _getStatusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'completed':
        return Colors.green;
      case 'pending':
        return Colors.orange;
      case 'cancelled':
        return Colors.red;
      case 'processing':
        return Colors.blue;
      default:
        return Colors.grey;
    }
  }
}
