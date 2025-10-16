// ignore_for_file: deprecated_member_use
import 'package:flutter/material.dart';

class ProductCard extends StatelessWidget {
  final Map<String, dynamic> product;
  final VoidCallback? onTap;
  final VoidCallback? onEdit;
  final VoidCallback? onDelete;

  const ProductCard({
    super.key,
    required this.product,
    this.onTap,
    this.onEdit,
    this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    final name = product['name'] ?? 'Unnamed Product';
    final price = _formatPrice();
    final manageStock = product['manage_stock'] == true;
    final stock = product['available_stock'] ?? product['stock_qty'] ?? (manageStock ? 0 : null);
    final status = (product['status'] ?? '').toString();
    final rejection = product['rejection_reason'];
    final imageUrl = product['main_image'];
    final sku = product['sku']?.toString();
    final category = product['category_name']?.toString();
    final hasVariations = (product['type'] ?? '') == 'variable' || (product['variations_count'] ?? 0) > 0;
    final variationsCount = product['variations_count'] ?? 0;
    final isLowStock = manageStock && (stock != null && stock <= 5 && stock > 0);
    final isOutOfStock = manageStock && (stock == null || stock == 0);

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      child: Card(
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: BorderSide(
            color: isDark ? Colors.grey.shade700 : Colors.grey.shade200, 
            width: 1
          ),
        ),
        child: InkWell(
          borderRadius: BorderRadius.circular(16),
          onTap: onTap,
          child: Container(
            width: double.infinity,
            padding: const EdgeInsets.all(12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                // Product Image with Status Badge
                Stack(
                  children: [
                    _buildProductImage(context, imageUrl),
                    Positioned(
                      top: 8,
                      right: 8,
                      child: _buildStatusChip(context, status, rejection),
                    ),
                    if (isOutOfStock)
                      Positioned(
                        bottom: 8,
                        left: 8,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.red.shade600,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: const Text(
                            'Out of Stock',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 10,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ),
                    if (isLowStock)
                      Positioned(
                        bottom: 8,
                        left: 8,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.orange.shade600,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: const Text(
                            'Low Stock',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 10,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 8),

                // Product Name
                Text(
                  name,
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    height: 1.2,
                    color: theme.textTheme.bodyLarge?.color,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                
                // SKU
                if (sku != null && sku.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(
                    'SKU: $sku',
                    style: TextStyle(
                      fontSize: 10,
                      color: theme.textTheme.bodySmall?.color?.withOpacity(0.7),
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
                
                const SizedBox(height: 6),

                // Category and Variations Info
                if ((category != null && category.isNotEmpty) || hasVariations) ...[
                  Wrap(
                    spacing: 8,
                    runSpacing: 4,
                    children: [
                      if (category != null && category.isNotEmpty)
                        _buildInfoChip(
                          context,
                          Icons.category_outlined,
                          category,
                          Colors.blue.shade600,
                          Colors.blue.shade50,
                        ),
                      if (hasVariations)
                        _buildInfoChip(
                          context,
                          Icons.tune,
                          '$variationsCount variations',
                          Colors.purple.shade600,
                          Colors.purple.shade50,
                        ),
                    ],
                  ),
                  const SizedBox(height: 8),
                ],

                // Price and Stock Row
                Row(
                  children: [
                    // Price
                    Flexible(
                      flex: 1,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                        decoration: BoxDecoration(
                          color: isDark ? Colors.green.shade900.withOpacity(0.2) : Colors.green.shade50,
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(
                            color: isDark ? Colors.green.shade700 : Colors.green.shade200, 
                            width: 1
                          ),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.attach_money, size: 14, color: Colors.green.shade600),
                            const SizedBox(width: 2),
                            Flexible(
                              child: Text(
                                '\$$price',
                                style: TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.green.shade600,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    // Stock
                    Flexible(
                      flex: 1,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                        decoration: BoxDecoration(
                          color: isOutOfStock 
                              ? (isDark ? Colors.red.shade900.withOpacity(0.2) : Colors.red.shade50)
                              : isLowStock 
                                  ? (isDark ? Colors.orange.shade900.withOpacity(0.2) : Colors.orange.shade50)
                                  : (isDark ? Colors.blue.shade900.withOpacity(0.2) : Colors.blue.shade50),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(
                            color: isOutOfStock 
                                ? (isDark ? Colors.red.shade700 : Colors.red.shade200)
                                : isLowStock 
                                    ? (isDark ? Colors.orange.shade700 : Colors.orange.shade200)
                                    : (isDark ? Colors.blue.shade700 : Colors.blue.shade200),
                            width: 1,
                          ),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              Icons.inventory_outlined,
                              size: 14,
                              color: isOutOfStock 
                                  ? Colors.red.shade600 
                                  : isLowStock 
                                      ? Colors.orange.shade600 
                                      : Colors.blue.shade600,
                            ),
                            const SizedBox(width: 2),
                            Flexible(
                              child: Text(
                                manageStock ? '${stock ?? 0}' : 'N/A',
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: isOutOfStock 
                                      ? Colors.red.shade600 
                                      : isLowStock 
                                          ? Colors.orange.shade600 
                                          : Colors.blue.shade600,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),

                // Action Buttons
                SizedBox(
                  width: double.infinity,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      Flexible(
                        child: Container(
                          margin: const EdgeInsets.symmetric(horizontal: 4),
                          child: IconButton(
                            onPressed: onEdit,
                            icon: const Icon(Icons.edit_outlined),
                            iconSize: 20,
                            color: Colors.blue.shade600,
                            style: IconButton.styleFrom(
                              backgroundColor: isDark ? Colors.blue.shade900.withOpacity(0.2) : Colors.blue.shade50,
                              padding: const EdgeInsets.all(8),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(8),
                              ),
                            ),
                            tooltip: 'Edit',
                          ),
                        ),
                      ),
                      Flexible(
                        child: Container(
                          margin: const EdgeInsets.symmetric(horizontal: 4),
                          child: IconButton(
                            onPressed: onDelete,
                            icon: const Icon(Icons.delete_outline),
                            iconSize: 20,
                            color: Colors.red.shade600,
                            style: IconButton.styleFrom(
                              backgroundColor: isDark ? Colors.red.shade900.withOpacity(0.2) : Colors.red.shade50,
                              padding: const EdgeInsets.all(8),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(8),
                              ),
                            ),
                            tooltip: 'Delete',
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  String _formatPrice() {
    final price = double.tryParse(product['effective_price']?.toString() ?? product['price']?.toString() ?? '0') ?? 0.0;
    return price.toStringAsFixed(2);
  }

  Widget _buildProductImage(BuildContext context, dynamic imageUrl) {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    
    return Container(
      width: double.infinity,
      height: 120,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        color: isDark ? Colors.grey.shade800 : Colors.grey.shade50,
        border: Border.all(
          color: isDark ? Colors.grey.shade700 : Colors.grey.shade200, 
          width: 1
        ),
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(11),
        child: imageUrl != null && imageUrl.toString().isNotEmpty
            ? Image.network(
                imageUrl.toString(),
                fit: BoxFit.cover,
                errorBuilder: (_, __, ___) => Container(
                  color: isDark ? Colors.grey.shade800 : Colors.grey.shade50,
                  child: Icon(
                    Icons.image_not_supported_outlined,
                    size: 48,
                    color: isDark ? Colors.grey.shade500 : Colors.grey.shade400,
                  ),
                ),
              )
            : Container(
                color: isDark ? Colors.grey.shade800 : Colors.grey.shade50,
                child: Icon(
                  Icons.inventory_2_outlined,
                  size: 48,
                  color: isDark ? Colors.grey.shade500 : Colors.grey.shade400,
                ),
              ),
      ),
    );
  }

  Widget _buildInfoChip(BuildContext context, IconData icon, String label, Color color, Color backgroundColor) {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: isDark ? color.withOpacity(0.2) : backgroundColor,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: isDark ? color.withOpacity(0.5) : color.withOpacity(0.2), 
          width: 1
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 11,
              color: color,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusChip(BuildContext context, String status, dynamic rejection) {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    
    Color chipColor;
    String chipLabel;
    IconData chipIcon;
    
    switch (status) {
      case 'approved':
        chipColor = Colors.green.shade600;
        chipLabel = 'Approved';
        chipIcon = Icons.check_circle_outline;
        break;
      case 'rejected':
        chipColor = Colors.red.shade600;
        chipLabel = 'Rejected';
        chipIcon = Icons.cancel_outlined;
        break;
      default:
        chipColor = Colors.orange.shade600;
        chipLabel = 'Pending';
        chipIcon = Icons.schedule_outlined;
        break;
    }
    
    return Tooltip(
      message: status == 'rejected' && rejection != null && rejection.toString().isNotEmpty 
          ? 'Rejected: $rejection' 
          : chipLabel,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
        decoration: BoxDecoration(
          color: isDark ? chipColor.withOpacity(0.2) : chipColor.withValues(alpha: 0.1),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isDark ? chipColor.withOpacity(0.5) : chipColor.withValues(alpha: 0.3)
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              chipIcon,
              size: 14,
              color: chipColor,
            ),
            const SizedBox(width: 4),
            Text(
              chipLabel,
              style: TextStyle(
                color: chipColor,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }
}