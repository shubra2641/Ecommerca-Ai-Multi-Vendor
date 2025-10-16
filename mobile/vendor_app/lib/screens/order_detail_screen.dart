// ignore_for_file: use_build_context_synchronously, unused_local_variable, deprecated_member_use
import 'package:flutter/material.dart';
import '../services/api_client.dart';
import '../l10n/app_localizations.dart';

class OrderDetailScreen extends StatefulWidget {
  final int orderId;
  const OrderDetailScreen({super.key, required this.orderId});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  final ApiClient _client = ApiClient();
  Map<String, dynamic>? _order;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() { _loading = true; });
    final res = await _client.getOrderDetail(widget.orderId);
    setState(() { _order = res; _loading = false; });
  }

  // Order status updates have been removed from the mobile app UI.

  @override
  Widget build(BuildContext context) {
  final l10n = AppLocalizations.of(context);
  final theme = Theme.of(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('${l10n.orderNumber} #${widget.orderId}'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _load,
          ),
        ],
      ),
      body: _loading 
        ? Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const CircularProgressIndicator(),
                const SizedBox(height: 16),
                Text(l10n.loading),
              ],
            ),
          )
        : _order == null
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.error_outline,
                    size: 64,
                    color: theme.colorScheme.error,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    l10n.error,
                    style: theme.textTheme.titleMedium?.copyWith(
                      color: theme.colorScheme.error,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    l10n.failedToLoadOrderDetails,
                    style: theme.textTheme.bodyMedium?.copyWith(
                      color: theme.colorScheme.outline,
                    ),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: _load,
                    child: Text(l10n.refresh),
                  ),
                ],
              ),
            )
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildOrderHeader(l10n, theme),
                  const SizedBox(height: 24),
                  _buildCustomerInfo(l10n, theme),
                  const SizedBox(height: 24),
                  _buildOrderItems(l10n, theme),
                  const SizedBox(height: 24),
                  _buildOrderSummary(l10n, theme),
                  const SizedBox(height: 24),
                ],
              ),
            ),
    );
  }
  
  Widget _buildOrderHeader(AppLocalizations l10n, ThemeData theme) {
    final status = _order?['status'] ?? 'pending';
    final createdAt = _order?['created_at'] ?? '';
    
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${l10n.orderNumber} #${_order?['id'] ?? widget.orderId}',
                  style: theme.textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                _buildStatusChip(status, theme),
              ],
            ),
            if (createdAt.isNotEmpty) ...[
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(
                    Icons.access_time,
                    size: 16,
                    color: theme.colorScheme.outline,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    '${l10n.orderDate}: ${_formatDate(createdAt)}',
                    style: theme.textTheme.bodyMedium?.copyWith(
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
  
  Widget _buildCustomerInfo(AppLocalizations l10n, ThemeData theme) {
    final customer = _order?['user'] ?? _order?['customer'];
    final customerName = customer?['name'] ?? l10n.unknownCustomer;
    final customerEmail = customer?['email'] ?? '';
    final shippingAddress = _order?['shipping_address'];
    
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              l10n.customer,
              style: theme.textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Icon(
                  Icons.person_outline,
                  size: 20,
                  color: theme.colorScheme.outline,
                ),
                const SizedBox(width: 8),
                Text(
                  customerName,
                  style: theme.textTheme.bodyMedium,
                ),
              ],
            ),
            if (shippingAddress != null) ...[
              const SizedBox(height: 12),
              // Shipping heading removed per request; show address directly
              Text(
                _formatAddress(shippingAddress),
                style: theme.textTheme.bodyMedium?.copyWith(
                  color: theme.colorScheme.outline,
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
  
  Widget _buildOrderItems(AppLocalizations l10n, ThemeData theme) {
  final items = _order?['items'] ?? [];
    
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              l10n.orderItems,
              style: theme.textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 12),
            if (items.isEmpty)
              Text(
                l10n.noItemsFound,
                style: theme.textTheme.bodyMedium?.copyWith(
                  color: theme.colorScheme.outline,
                ),
              )
            else
              ...items.map<Widget>((item) {
                final productName = item['product_name'] ?? item['product']?['name'] ?? l10n.unknownProduct;
                final quantityRaw = item['quantity'];
                final quantity = quantityRaw is int ? quantityRaw : (quantityRaw is String ? int.tryParse(quantityRaw) ?? 1 : 1);
                final priceRaw = item['price'];
                final price = priceRaw is double ? priceRaw : (priceRaw is String ? double.tryParse(priceRaw) ?? 0.0 : (priceRaw is int ? priceRaw.toDouble() : 0.0));
                final totalRaw = item['total'];
                final total = totalRaw is double ? totalRaw : (totalRaw is String ? double.tryParse(totalRaw) ?? (quantity * price) : (totalRaw is int ? totalRaw.toDouble() : (quantity * price)));
                
                // Determine variant/option information if available
                final variantLabel = _extractVariantLabel(item);

                return Padding(
                  padding: const EdgeInsets.only(bottom: 12),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        width: 50,
                        height: 50,
                        decoration: BoxDecoration(
                          color: theme.colorScheme.surfaceContainerHighest,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Icon(
                          Icons.inventory_2_outlined,
                          color: theme.colorScheme.outline,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              productName,
                              style: theme.textTheme.bodyMedium?.copyWith(
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            if (variantLabel != null) ...[
                              const SizedBox(height: 4),
                              Text(
                                variantLabel,
                                style: theme.textTheme.bodySmall?.copyWith(
                                  color: theme.colorScheme.outline,
                                ),
                              ),
                            ],
                            const SizedBox(height: 4),
                            Text(
                              'Qty: $quantity × \$${price.toStringAsFixed(2)}',
                              style: theme.textTheme.bodySmall?.copyWith(
                                color: theme.colorScheme.outline,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Text(
                        '\$${total.toStringAsFixed(2)}',
                        style: theme.textTheme.bodyMedium?.copyWith(
                          fontWeight: FontWeight.bold,
                          color: theme.colorScheme.primary,
                        ),
                      ),
                    ],
                  ),
                );
              }).toList(),
          ],
        ),
      ),
    );
  }

  String? _extractVariantLabel(dynamic item) {
    if (item == null) return null;
    // Try many common keys and nested shapes
    final candidates = <dynamic>[
      item['variant'],
      item['variation'],
      item['variation_name'],
      item['variant_name'],
      item['options'],
      item['attributes'],
      item['attributes_text'],
      item['meta'],
      item['product']?['variant'],
      item['product']?['variation'],
      item['product']?['options'],
      item['pivot']?['variation'],
    ];

    for (final cand in candidates) {
      if (cand == null) continue;
      if (cand is String && cand.isNotEmpty) return cand;
      if (cand is Map) {
        try {
          final text = cand.entries.map((e) => '${e.key}: ${e.value}').join(', ');
          if (text.isNotEmpty) return text;
        } catch (_) {}
      }
      if (cand is List && cand.isNotEmpty) return cand.map((v) => v.toString()).join(', ');
    }
    return null;
  }
  
  Widget _buildOrderSummary(AppLocalizations l10n, ThemeData theme) {
  final subtotalRaw = _order?['subtotal'] ?? _order?['items_subtotal'];
    final subtotal = subtotalRaw is double ? subtotalRaw : (subtotalRaw is String ? double.tryParse(subtotalRaw) ?? 0.0 : (subtotalRaw is int ? subtotalRaw.toDouble() : 0.0));
  // Shipping price can appear under multiple keys depending on API shape
  dynamic shippingRaw = _order?['shipping_price'] ?? _order?['shipping'] ?? _order?['shipping_cost'] ?? _order?['shippingPrice'] ?? _order?['data']?['shipping_price'] ?? _order?['data']?['shipping'];
  double shipping = 0.0;
  if (shippingRaw is num) {
    shipping = shippingRaw.toDouble();
  } else if (shippingRaw is String) {
    shipping = double.tryParse(shippingRaw) ?? 0.0;
  } else if (shippingRaw is Map) {
    final candidate = shippingRaw['price'] ?? shippingRaw['amount'] ?? shippingRaw['cost'] ?? shippingRaw['value'];
    if (candidate is num) shipping = candidate.toDouble();
    else if (candidate is String) shipping = double.tryParse(candidate) ?? 0.0;
  }
    final totalRaw = _order?['total'];
    final total = totalRaw is double ? totalRaw : (totalRaw is String ? double.tryParse(totalRaw) ?? 0.0 : (totalRaw is int ? totalRaw.toDouble() : 0.0));
    
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              l10n.orderSummary,
              style: theme.textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 12),
            _buildSummaryRow(l10n.subtotal, '\$${subtotal.toStringAsFixed(2)}', theme),
            _buildSummaryRow(l10n.shipping, '\$${shipping.toStringAsFixed(2)}', theme),
            const Divider(),
            _buildSummaryRow(
              l10n.orderTotal,
              '\$${total.toStringAsFixed(2)}',
              theme,
              isTotal: true,
            ),
          ],
        ),
      ),
    );
  }
  
  Widget _buildSummaryRow(String label, String value, ThemeData theme, {bool isTotal = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Flexible(
            child: Text(
              label,
              style: isTotal
                  ? theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold)
                  : theme.textTheme.bodyMedium,
              overflow: TextOverflow.ellipsis,
            ),
          ),
          Text(
            value,
            style: isTotal
                ? theme.textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                    color: theme.colorScheme.primary,
                  )
                : theme.textTheme.bodyMedium?.copyWith(
                    fontWeight: FontWeight.w600,
                  ),
          ),
        ],
      ),
    );
  }
  
  // Status update UI has been removed from the mobile app per requirements.
  
  // Action buttons for updating order status removed.
  
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
      case 'completed':
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
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(16),
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
      return '${date.day}/${date.month}/${date.year} ${date.hour}:${date.minute.toString().padLeft(2, '0')}';
    } catch (e) {
      return dateStr;
    }
  }
  
  String _formatAddress(dynamic address) {
    if (address is Map<String, dynamic>) {
      final parts = <String>[];
      if (address['street'] != null) parts.add(address['street']);
      if (address['city'] != null) parts.add(address['city']);
      if (address['state'] != null) parts.add(address['state']);
      if (address['postal_code'] != null) parts.add(address['postal_code']);
      if (address['country'] != null) parts.add(address['country']);
      return parts.join(', ');
    }
    return address.toString();
  }
}
