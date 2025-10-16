import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/balance_provider.dart';
import '../l10n/app_localizations.dart';

class BalanceScreen extends StatefulWidget {
  const BalanceScreen({super.key});

  @override
  State<BalanceScreen> createState() => _BalanceScreenState();
}

class _BalanceScreenState extends State<BalanceScreen> {
  @override
  void initState() {
    super.initState();
    // load provider data
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<BalanceProvider>(context, listen: false);
      provider.loadBalanceData();
      provider.loadWithdrawalHistory(refresh: true);
    });
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    
    return Scaffold(
      appBar: AppBar(
        title: const Text('Balance & Withdrawals'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              final prov = Provider.of<BalanceProvider>(context, listen: false);
              prov.loadBalanceData();
              prov.loadWithdrawalHistory(refresh: true);
            },
          ),
        ],
      ),
      body: Consumer<BalanceProvider>(
        builder: (ctx, provider, _) {
          if (provider.isLoading && provider.balanceData == null) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.error != null && provider.balanceData == null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.error_outline, size: 64, color: Colors.red[300]),
                  const SizedBox(height: 16),
                  Text(provider.error!, textAlign: TextAlign.center),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () {
                      provider.loadBalanceData();
                      provider.loadWithdrawalHistory(refresh: true);
                    },
                    child: Text(localizations.retry),
                  ),
                ],
              ),
            );
          }

          final bd = provider.balanceData;
          final withdrawals = provider.withdrawalHistory;

          return RefreshIndicator(
            onRefresh: () async {
              await provider.loadBalanceData();
              await provider.loadWithdrawalHistory(refresh: true);
            },
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Balance Overview Card
                  Card(
                    elevation: 4,
                    child: Padding(
                      padding: const EdgeInsets.all(20.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            localizations.availableBalance,
                            style: Theme.of(context).textTheme.titleLarge?.copyWith(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 12),
                          Text(
                            bd != null ? provider.formatCurrency(bd.currentBalance) : '\$0.00',
                            style: const TextStyle(
                              fontSize: 32,
                              fontWeight: FontWeight.bold,
                              color: Colors.green,
                            ),
                          ),
                          const SizedBox(height: 16),
                          Row(
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      localizations.totalSales,
                                      style: Theme.of(context).textTheme.bodySmall,
                                    ),
                                    Text(
                                      bd != null ? provider.formatCurrency(bd.totalEarnings) : '\$0.00',
                                      style: const TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.w600,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      localizations.totalWithdrawals,
                                      style: Theme.of(context).textTheme.bodySmall,
                                    ),
                                    Text(
                                      bd != null ? provider.formatCurrency(bd.totalWithdrawals) : '\$0.00',
                                      style: const TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.w600,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Row(
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      localizations.pendingWithdrawals,
                                      style: Theme.of(context).textTheme.bodySmall,
                                    ),
                                    Text(
                                      bd != null ? provider.formatCurrency(bd.pendingBalance) : '\$0.00',
                                      style: const TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.w600,
                                        color: Colors.orange,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Withdrawal Button
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: () async {
                        final res = await Navigator.of(context).pushNamed('/withdraw');
                        if (res == true) {
                          final provider = Provider.of<BalanceProvider>(context, listen: false);
                          await provider.loadBalanceData();
                          await provider.loadWithdrawalHistory(refresh: true);
                        }
                      },
                      icon: const Icon(Icons.account_balance_wallet),
                      label: Text(localizations.requestWithdrawal),
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                      ),
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Recent Withdrawals Section
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        localizations.recentWithdrawals,
                        style: Theme.of(context).textTheme.titleLarge?.copyWith(
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      TextButton(
                        onPressed: () {
                          Navigator.of(context).pushNamed('/withdrawals');
                        },
                        child: Text(localizations.viewAll),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Withdrawals List
                  if (withdrawals.isEmpty)
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(20.0),
                        child: Center(
                          child: Column(
                            children: [
                              Icon(
                                Icons.history,
                                size: 48,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 12),
                              Text(
                                localizations.noWithdrawalHistoryYet,
                                style: TextStyle(
                                  color: Colors.grey[600],
                                  fontSize: 16,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    )
                  else
                    ...withdrawals.map<Widget>((w) {
                      final status = w.status;
                      Color statusColor = Colors.grey;
                      IconData statusIcon = Icons.help_outline;

                      switch (status.toLowerCase()) {
                        case 'completed':
                          statusColor = Colors.green;
                          statusIcon = Icons.check_circle;
                          break;
                        case 'pending':
                          statusColor = Colors.orange;
                          statusIcon = Icons.schedule;
                          break;
                        case 'rejected':
                        case 'cancelled':
                          statusColor = Colors.red;
                          statusIcon = Icons.cancel;
                          break;
                      }

                      return Card(
                        margin: const EdgeInsets.only(bottom: 8),
                        child: ListTile(
                          leading: Icon(
                            statusIcon,
                            color: statusColor,
                          ),
                          title: Text(
                            provider.formatCurrency(w.amount),
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          subtitle: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Status: ${status.toUpperCase()}',
                                style: TextStyle(color: statusColor),
                              ),
                              Text(
                                'Requested: ${w.requestedAt.toLocal()}',
                                style: Theme.of(context).textTheme.bodySmall,
                              ),
                            ],
                          ),
                          trailing: status.toLowerCase() == 'pending'
                              ? TextButton(
                                  onPressed: () {
                                    provider.cancelWithdrawalRequest(w.id);
                                  },
                                  child: Text(localizations.cancel),
                                )
                              : null,
                        ),
                      );
                    }),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
