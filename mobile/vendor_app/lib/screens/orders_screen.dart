// ignore_for_file: use_build_context_synchronously, deprecated_member_use
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/orders_provider.dart';
import 'order_detail_screen.dart';
import '../l10n/app_localizations.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  final ScrollController _scrollController = ScrollController();
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';
  String _selectedStatus = 'all';
  DateTime? _startDate;
  DateTime? _endDate;
  bool _showFilters = false;

  @override
  void initState() {
    super.initState();
    final provider = Provider.of<OrdersProvider>(context, listen: false);
    provider.loadNextPage();
    _scrollController.addListener(() {
      if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
        provider.loadNextPage();
      }
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  void _applyFilters() {
    final provider = Provider.of<OrdersProvider>(context, listen: false);
    provider.applyFilters(
      searchQuery: _searchQuery,
      status: _selectedStatus == 'all' ? null : _selectedStatus,
      startDate: _startDate,
      endDate: _endDate,
    );
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final localizations = AppLocalizations.of(context)!;
    
    return Scaffold(
      appBar: AppBar(
        title: Text(localizations.orders),
        backgroundColor: theme.colorScheme.surface,
        foregroundColor: theme.colorScheme.onSurface,
        elevation: 0,
        actions: [
          IconButton(
            icon: Icon(_showFilters ? Icons.filter_list_off : Icons.filter_list),
            onPressed: () {
              setState(() {
                _showFilters = !_showFilters;
              });
            },
          ),
        ],
      ),
      body: Column(
        children: [
          if (_showFilters) _buildFiltersSection(theme, localizations),
          Expanded(
            child: Consumer<OrdersProvider>(
              builder: (context, provider, child) {
                if (provider.isLoading && provider.items.isEmpty) {
                  return const Center(child: CircularProgressIndicator());
                }
                
                if (provider.items.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.shopping_bag_outlined,
                          size: 64,
                          color: theme.colorScheme.outline,
                        ),
                        const SizedBox(height: 16),
                        Text(
                          provider.lastError ?? localizations.noOrdersFound,
                          style: theme.textTheme.titleMedium?.copyWith(
                            color: theme.colorScheme.outline,
                          ),
                          textAlign: TextAlign.center,
                        ),
                        if (provider.lastError != null) ...[
                          const SizedBox(height: 12),
                          ElevatedButton.icon(
                            onPressed: () async {
                              await provider.refresh();
                            },
                            icon: const Icon(Icons.refresh),
                            label: Text(localizations.retry),
                          ),
                        ],
                      ],
                    ),
                  );
                }
                
                return RefreshIndicator(
                  onRefresh: () async {
                    await provider.refresh();
                  },
                  child: ListView.builder(
                    controller: _scrollController,
                    padding: const EdgeInsets.all(16),
                    itemCount: provider.items.length + (provider.hasMore ? 1 : 0),
                    itemBuilder: (context, index) {
                      if (index >= provider.items.length) {
                        return const Center(
                          child: Padding(
                            padding: EdgeInsets.all(16),
                            child: CircularProgressIndicator(),
                          ),
                        );
                      }
                      
                      final order = provider.items[index];
                      return _buildOrderCard(order, theme, localizations);
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFiltersSection(ThemeData theme, AppLocalizations localizations) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        border: Border(
          bottom: BorderSide(
            color: theme.colorScheme.outline.withOpacity(0.2),
          ),
        ),
      ),
      child: Column(
        children: [
          TextField(
            controller: _searchController,
            decoration: InputDecoration(
              hintText: localizations.searchOrders,
              prefixIcon: const Icon(Icons.search),
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 12,
              ),
            ),
            onChanged: (value) {
              setState(() {
                _searchQuery = value;
              });
              _applyFilters();
            },
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: DropdownButtonFormField<String>(
                  initialValue: _selectedStatus,
                  decoration: InputDecoration(
                    labelText: localizations.status,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 12,
                    ),
                  ),
                  items: [
                    DropdownMenuItem(
                      value: 'all',
                      child: Text(localizations.allStatuses),
                    ),
                    DropdownMenuItem(
                      value: 'pending',
                      child: Text(localizations.pending),
                    ),
                    DropdownMenuItem(
                      value: 'processing',
                      child: Text(localizations.processing),
                    ),
                    DropdownMenuItem(
                      value: 'shipped',
                      child: Text(localizations.shipped),
                    ),
                    DropdownMenuItem(
                      value: 'delivered',
                      child: Text(localizations.delivered),
                    ),
                    DropdownMenuItem(
                      value: 'cancelled',
                      child: Text(localizations.cancelled),
                    ),
                  ],
                  onChanged: (value) {
                    setState(() {
                      _selectedStatus = value!;
                    });
                    _applyFilters();
                  },
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Row(
                  children: [
                    Expanded(
                      child: TextButton.icon(
                        onPressed: () async {
                          final date = await showDatePicker(
                            context: context,
                            initialDate: _startDate ?? DateTime.now(),
                            firstDate: DateTime(2020),
                            lastDate: DateTime.now(),
                          );
                          if (date != null) {
                            setState(() {
                              _startDate = date;
                            });
                            _applyFilters();
                          }
                        },
                        icon: const Icon(Icons.date_range),
                        label: Text(
                          _startDate != null
                              ? _formatDate(_startDate.toString())
                               : localizations.startDate,
                          style: theme.textTheme.bodySmall,
                        ),
                      ),
                    ),
                    Expanded(
                      child: TextButton.icon(
                        onPressed: () async {
                          final date = await showDatePicker(
                            context: context,
                            initialDate: _endDate ?? DateTime.now(),
                            firstDate: DateTime(2020),
                            lastDate: DateTime.now(),
                          );
                          if (date != null) {
                            setState(() {
                              _endDate = date;
                            });
                            _applyFilters();
                          }
                        },
                        icon: const Icon(Icons.date_range),
                        label: Text(
                          _endDate != null
                              ? _formatDate(_endDate.toString())
                               : localizations.endDate,
                          style: theme.textTheme.bodySmall,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildOrderCard(Map<String, dynamic> order, ThemeData theme, AppLocalizations localizations) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => OrderDetailScreen(orderId: int.parse(order['id'].toString())),
            ),
          );
        },
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '${localizations.orderNumber}${order['id']}',
                    style: theme.textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  _buildStatusChip(order['status'] ?? 'pending', theme),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(
                    Icons.person_outline,
                    size: 16,
                    color: theme.colorScheme.outline,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    order['customer_name'] ?? localizations.unknownCustomer,
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
                    Icons.access_time,
                    size: 16,
                    color: theme.colorScheme.outline,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    _formatDate(order['created_at'] ?? ''),
                    style: theme.textTheme.bodyMedium?.copyWith(
                      color: theme.colorScheme.outline,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '${order['items_count'] ?? 0} ${localizations.items}',
                    style: theme.textTheme.bodyMedium,
                  ),
                  Text(
                    '\$${order['total'] ?? '0.00'}',
                    style: theme.textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                      color: theme.colorScheme.primary,
                    ),
                  ),
                ],
              ),
            ],
          ),
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
      case 'processing':
        backgroundColor = Colors.blue.withOpacity(0.1);
        textColor = Colors.blue;
        break;
      case 'shipped':
        backgroundColor = Colors.purple.withOpacity(0.1);
        textColor = Colors.purple;
        break;
      case 'delivered':
        backgroundColor = Colors.green.withOpacity(0.1);
        textColor = Colors.green;
        break;
      case 'cancelled':
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
        status.toUpperCase(),
        style: theme.textTheme.labelSmall?.copyWith(
          color: textColor,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
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
