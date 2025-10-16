// ignore_for_file: unused_field, deprecated_member_use
import 'package:flutter/material.dart';
import '../services/api_client.dart';
import 'withdraw_request_screen.dart';
import '../l10n/app_localizations.dart';

class WithdrawalsScreen extends StatefulWidget {
  const WithdrawalsScreen({super.key});

  @override
  State<WithdrawalsScreen> createState() => _WithdrawalsScreenState();
}

class _WithdrawalsScreenState extends State<WithdrawalsScreen> {
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';
  String _selectedStatus = 'all';
  DateTime? _startDate;
  DateTime? _endDate;
  bool _showFilters = false;
  bool _isLoading = true;
  List<dynamic> _withdrawals = [];
  Map<String, dynamic>? _summary;
  Map<String, dynamic>? _profile;

  @override
  void initState() {
    super.initState();
    _loadWithdrawals();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadWithdrawals() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final client = ApiClient();
      
      // Ensure token is loaded before making API calls
      await client.ensureTokenLoaded();
      
      final profileRes = await client.getProfile();
      final result = await ApiClient.getWithdrawalHistory();

      // Prepare temporary holders
      List<dynamic> withdrawalsList = [];
      Map<String, dynamic>? newSummary;
      Map<String, dynamic>? profilePayload;

      if (result['success'] == true) {
        final dataBlock = result['data'] as Map<String, dynamic>? ?? {};
        print('Data block: $dataBlock');

        // Check if data has nested structure
        Map<String, dynamic> actualData = dataBlock;
        if (dataBlock['data'] is Map) {
          actualData = dataBlock['data'] as Map<String, dynamic>;
          print('Found nested data structure');
        }

        // Withdrawals list may be under 'withdrawals' or 'data' (legacy)
        if (actualData['withdrawals'] is List) {
          withdrawalsList = List<dynamic>.from(actualData['withdrawals'] as List);
          print('Found withdrawals list with ${withdrawalsList.length} items');
        } else if (actualData['data'] is List) {
          withdrawalsList = List<dynamic>.from(actualData['data'] as List);
          print('Found data list with ${withdrawalsList.length} items');
        } else {
          print('No withdrawals list found in actual data');
        }

        // Statistics block
        if (actualData['statistics'] is Map) {
          newSummary = Map<String, dynamic>.from(actualData['statistics'] as Map);
          print('Found statistics: $newSummary');
        } else if (actualData['summary'] is Map) {
          newSummary = Map<String, dynamic>.from(actualData['summary'] as Map);
          print('Found summary: $newSummary');
        } else {
          print('No statistics/summary found in actual data');
        }
      } else {
        print('API call failed: ${result['message'] ?? 'Unknown error'}');
      }

      if (profileRes != null) {
        if (profileRes['data'] is Map) {
          profilePayload = Map<String, dynamic>.from(profileRes['data'] as Map);
        } else if (profileRes['profile'] is Map) {
          profilePayload = Map<String, dynamic>.from(profileRes['profile'] as Map);
        } else {
          // fallback: sometimes API returns raw profile object
          profilePayload = Map<String, dynamic>.from(profileRes);
        }
      }

      // If profile provided a balance, prefer it for available_balance
      if (profilePayload != null) {
        newSummary = newSummary ?? {};
        if (profilePayload['balance'] != null) {
          newSummary['available_balance'] = profilePayload['balance'];
        }
      }

      setState(() {
        _withdrawals = withdrawalsList;
        _summary = newSummary;
        _profile = profilePayload;
      });
      
      print('Final state set:');
      print('- Withdrawals count: ${_withdrawals.length}');
      print('- Summary: $_summary');
      print('- Profile: $_profile');
      print('=== WITHDRAWALS DEBUG END ===');
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error loading data: $e')),
        );
      }
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  List<dynamic> get _filteredWithdrawals {
    var items = _withdrawals;
    
    // Apply search filter
    if (_searchQuery.isNotEmpty) {
      items = items.where((item) {
        final reference = item['reference']?.toString().toLowerCase() ?? '';
        final amount = item['amount']?.toString().toLowerCase() ?? '';
        final query = _searchQuery.toLowerCase();
        return reference.contains(query) || amount.contains(query);
      }).toList();
    }
    
    // Apply status filter
    if (_selectedStatus != 'all') {
      items = items.where((item) => item['status'] == _selectedStatus).toList();
    }
    
    return items;
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    
    return Scaffold(
      appBar: AppBar(
        title: Text(localizations.withdrawals),
        actions: [
          IconButton(
            icon: Icon(_showFilters ? Icons.filter_list : Icons.filter_list_outlined),
            onPressed: () {
              setState(() {
                _showFilters = !_showFilters;
              });
            },
          ),
          IconButton(
            icon: const Icon(Icons.add),
            onPressed: () async {
              final result = await Navigator.of(context).push(
                MaterialPageRoute(
                  builder: (_) => const WithdrawRequestScreen(),
                ),
              );
              if (result == true) {
                _loadWithdrawals();
              }
            },
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadWithdrawals,
          ),
        ],
      ),
      body: Column(
        children: [
          if (_summary != null) _buildSummaryCards(),
          if (_showFilters) _buildFiltersSection(),
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _buildWithdrawalsList(),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryCards() {
  // Theme is accessed inline where needed
    // Map API statistics keys to UI values. Ensure we show the stored user balance (available_balance).
    final availableBalance = double.tryParse(_summary?['available_balance']?.toString() ?? '0') ?? 0.0;
    final totalWithdrawn = double.tryParse(_summary?['total_withdrawals']?.toString() ?? '0') ?? 0.0;
    final pendingAmount = double.tryParse(_summary?['pending_withdrawals']?.toString() ?? '0') ?? 0.0;
    final currency = _summary?['currency'] ?? 'USD';

  return Container(
      padding: const EdgeInsets.all(16),
      child: Row(
        children: [
          Expanded(
            child: _buildSummaryCard(
              AppLocalizations.of(context)!.availableBalance,
              '${availableBalance.toStringAsFixed(2)} $currency',
              Icons.account_balance_wallet_outlined,
              Colors.green,
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _buildSummaryCard(
              AppLocalizations.of(context)!.pending,
              '${pendingAmount.toStringAsFixed(2)} $currency',
              Icons.schedule,
              Colors.orange,
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _buildSummaryCard(
              AppLocalizations.of(context)!.totalWithdrawn,
              '${totalWithdrawn.toStringAsFixed(2)} $currency',
              Icons.show_chart,
              Colors.blue,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryCard(String title, String value, IconData icon, Color color) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          children: [
            Icon(icon, color: color, size: 24),
            const SizedBox(height: 8),
            Text(
              title,
              style: const TextStyle(fontSize: 12, color: Colors.grey),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
                color: color,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFiltersSection() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.surface,
        border: Border(
          bottom: BorderSide(
            color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
          ),
        ),
      ),
      child: Column(
        children: [
          // Search field
          TextField(
            controller: _searchController,
            decoration: InputDecoration(
              hintText: AppLocalizations.of(context)!.searchByReferenceOrAmount,
              prefixIcon: Icon(Icons.search),
              border: OutlineInputBorder(),
            ),
            onChanged: (value) {
              setState(() {
                _searchQuery = value;
              });
            },
          ),
          const SizedBox(height: 12),
          // Status filter
          Row(
            children: [
              const Text('Status: '),
              const SizedBox(width: 8),
              Expanded(
                child: DropdownButtonFormField<String>(
                  initialValue: _selectedStatus,
                  decoration: const InputDecoration(
                    border: OutlineInputBorder(),
                    contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  ),
                  items: [
                    DropdownMenuItem(value: 'all', child: Text(AppLocalizations.of(context)!.allStatus)),
                    DropdownMenuItem(value: 'pending', child: Text(AppLocalizations.of(context)!.pending)),
                    DropdownMenuItem(value: 'approved', child: Text(AppLocalizations.of(context)!.approved)),
                    DropdownMenuItem(value: 'completed', child: Text(AppLocalizations.of(context)!.completed)),
                    DropdownMenuItem(value: 'rejected', child: Text(AppLocalizations.of(context)!.rejected)),
                  ],
                  onChanged: (value) {
                    setState(() {
                      _selectedStatus = value ?? 'all';
                    });
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildWithdrawalsList() {
    final filteredItems = _filteredWithdrawals;
    
    if (filteredItems.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.account_balance_wallet_outlined,
              size: 64,
              color: Theme.of(context).colorScheme.outline,
            ),
            const SizedBox(height: 16),
            Text(
              AppLocalizations.of(context)!.noWithdrawalRequests,
              style: Theme.of(context).textTheme.titleMedium?.copyWith(
                color: Theme.of(context).colorScheme.outline,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              AppLocalizations.of(context)!.noWithdrawalRequestsFoundYet,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: Theme.of(context).colorScheme.outline,
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadWithdrawals,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: filteredItems.length,
        itemBuilder: (context, index) {
          final withdrawal = filteredItems[index];
          return _buildWithdrawalCard(withdrawal);
        },
      ),
    );
  }

  Widget _buildWithdrawalCard(Map<String, dynamic> withdrawal) {
    final theme = Theme.of(context);
    final amount = double.tryParse(withdrawal['amount']?.toString() ?? '0') ?? 0.0;
    final currency = withdrawal['currency'] ?? 'USD';
    final status = withdrawal['status'] ?? 'pending';
    final reference = withdrawal['reference'] ?? 'في الانتظار';
    final paymentMethod = withdrawal['payment_method'] ?? 'bank_transfer';
    final createdAt = withdrawal['created_at'] ?? '';
    final grossAmount = double.tryParse(withdrawal['gross_amount']?.toString() ?? '0');
    final commissionAmount = double.tryParse(withdrawal['commission_amount']?.toString() ?? '0');

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${amount.toStringAsFixed(2)} $currency',
                  style: theme.textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                    color: theme.colorScheme.primary,
                  ),
                ),
                _buildStatusChip(status, theme),
              ],
            ),
            const SizedBox(height: 8),
            if (grossAmount != null) ...[
              Row(
                children: [
                  Icon(
                    Icons.layers_outlined,
                    size: 16,
                    color: theme.colorScheme.outline,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    'Gross Amount: ${grossAmount.toStringAsFixed(2)} $currency',
                    style: theme.textTheme.bodySmall?.copyWith(
                      color: theme.colorScheme.outline,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 4),
            ],
            if (commissionAmount != null && commissionAmount > 0) ...[
              Row(
                children: [
                  Icon(
                    Icons.percent,
                    size: 16,
                    color: theme.colorScheme.outline,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    'Commission: ${commissionAmount.toStringAsFixed(2)} $currency',
                    style: theme.textTheme.bodySmall?.copyWith(
                      color: Colors.red,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 4),
            ],
            Row(
              children: [
                Icon(
                  _getPaymentMethodIcon(paymentMethod),
                  size: 16,
                  color: theme.colorScheme.outline,
                ),
                const SizedBox(width: 4),
                Text(
                  _getPaymentMethodLabel(paymentMethod),
                  style: theme.textTheme.bodyMedium?.copyWith(
                    color: theme.colorScheme.outline,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 4),
            Row(
              children: [
                Icon(
                  Icons.tag,
                  size: 16,
                  color: theme.colorScheme.outline,
                ),
                const SizedBox(width: 4),
                Text(
                  'Reference: $reference',
                  style: theme.textTheme.bodyMedium?.copyWith(
                    color: theme.colorScheme.outline,
                  ),
                ),
              ],
            ),
            if (createdAt.isNotEmpty) ...[
              const SizedBox(height: 4),
              Row(
                children: [
                  Icon(
                    Icons.access_time,
                    size: 16,
                    color: theme.colorScheme.outline,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    _formatDate(createdAt),
                    style: theme.textTheme.bodySmall?.copyWith(
                      color: theme.colorScheme.outline,
                    ),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildStatusChip(String status, ThemeData theme) {
    Color backgroundColor;
    Color textColor;
    
    switch (status.toLowerCase()) {
      case 'pending':
        backgroundColor = Colors.orange.withOpacity(0.1);
        textColor = Colors.orange;
        break;
      case 'approved':
        backgroundColor = Colors.blue.withOpacity(0.1);
        textColor = Colors.blue;
        break;
      case 'completed':
        backgroundColor = Colors.green.withOpacity(0.1);
        textColor = Colors.green;
        break;
      case 'rejected':
        backgroundColor = Colors.red.withOpacity(0.1);
        textColor = Colors.red;
        break;
      default:
        backgroundColor = theme.colorScheme.surfaceContainerHighest;
        textColor = theme.colorScheme.onSurfaceVariant;
    }
    
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        _getStatusLabel(status),
        style: theme.textTheme.labelSmall?.copyWith(
          color: textColor,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  String _getStatusLabel(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return AppLocalizations.of(context)!.pending;
      case 'approved':
        return AppLocalizations.of(context)!.approved;
      case 'completed':
        return AppLocalizations.of(context)!.completed;
      case 'rejected':
        return AppLocalizations.of(context)!.rejected;
      default:
        return status.toUpperCase();
    }
  }

  IconData _getPaymentMethodIcon(String method) {
    final m = method.replaceAll('-', '_').toLowerCase();
    switch (m) {
      case 'bank_transfer':
      case 'banktransfer':
      case 'bank':
        return Icons.account_balance;
      case 'paypal':
        return Icons.payment;
      case 'stripe':
        return Icons.credit_card;
      default:
        return Icons.credit_card;
    }
  }

  String _getPaymentMethodLabel(String method) {
    final m = method.replaceAll('-', '_').toLowerCase();
    switch (m) {
      case 'bank_transfer':
      case 'banktransfer':
      case 'bank':
        return AppLocalizations.of(context)!.bankTransfer;
      case 'paypal':
        return AppLocalizations.of(context)!.paypal;
      case 'stripe':
        return AppLocalizations.of(context)!.stripe;
      default:
        return method.replaceAll(RegExp('[-_]'), ' ');
    }
  }

  String _formatDate(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      return '${date.day}/${date.month}/${date.year}';
    } catch (e) {
      return dateStr;
    }
  }
}
