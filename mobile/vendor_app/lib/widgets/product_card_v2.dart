// ignore_for_file: deprecated_member_use
import 'package:flutter/material.dart';

class ProductCardV2 extends StatelessWidget {
  final Map<String, dynamic> product;
  final VoidCallback? onTap;
  final VoidCallback? onEdit;
  final VoidCallback? onDelete;
  final bool isGridChild;

  const ProductCardV2({
    super.key,
    required this.product,
  this.onTap,
  this.onEdit,
  this.onDelete,
  this.isGridChild = false,
  });

  String _formatPrice() {
    final raw = product['effective_price']?.toString() ?? product['price']?.toString() ?? '0';
    final value = double.tryParse(raw) ?? 0.0;
    return value.toStringAsFixed(2);
  }

  @override
  Widget build(BuildContext context) {
    final name = product['name'] ?? '-';
    final price = _formatPrice();
    final manageStock = product['manage_stock'] == true;
    final stock = product['available_stock'] ?? product['stock_qty'] ?? (manageStock ? 0 : null);
    final status = (product['status'] ?? '').toString();
    final imageUrl = product['main_image'] ?? product['image'];
    final sku = product['sku']?.toString();
    final category = product['category_name']?.toString();
    final hasVariations = (product['type'] ?? '') == 'variable' || (product['variations_count'] ?? 0) > 0;
    final variationsCount = product['variations_count'] ?? 0;

    if (isGridChild) {
      return _buildGridTile(context, name, price, manageStock, stock, status, imageUrl);
    }

    return Container(
      width: double.infinity,
      margin: const EdgeInsets.only(bottom: 8),
      child: Card(
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: BorderSide(color: Colors.grey.shade200, width: 1),
        ),
        child: InkWell(
          borderRadius: BorderRadius.circular(16),
          onTap: onTap,
          child: Container(
            padding: const EdgeInsets.all(12),
            child: LayoutBuilder(
              builder: (context, constraints) {
                final isWide = constraints.maxWidth > 600;
                return isWide
                    ? _buildWideLayout(context, name, price, manageStock, stock, status, imageUrl, sku, category, hasVariations, variationsCount)
                    : _buildCompactLayout(context, name, price, manageStock, stock, status, imageUrl, sku, category, hasVariations, variationsCount);
              },
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildGridTile(BuildContext context, String name, String price, bool manageStock, dynamic stock, String status, dynamic imageUrl) {
    // minimal, single-row tile for grid usage to avoid vertical overflow
    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
      // tighten padding for grid tiles to save vertical space
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
          child: Row(
            children: [
      _buildProductImage(imageUrl, 48),
      const SizedBox(width: 8),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
        Text(name, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700), maxLines: 1, overflow: TextOverflow.ellipsis),
        const SizedBox(height: 4),
        Row(children: [Icon(Icons.attach_money, size: 13, color: Colors.green.shade600), const SizedBox(width: 4), Text('\$$price', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: Colors.green.shade600))]),
                  ],
                ),
              ),
      const SizedBox(width: 6),
      // Move the status and actions inline but smaller to avoid pushing height
      _StatusChip(status: status),
      const SizedBox(width: 6),
      _buildActionsMenu(context),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildWideLayout(BuildContext context, String name, String price, bool manageStock, dynamic stock, String status, dynamic imageUrl, String? sku, String? category, bool hasVariations, int variationsCount) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        // Product Image
        _buildProductImage(imageUrl, 72),
        const SizedBox(width: 16),

        // Product Info
        Expanded(
          flex: 4,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      name,
                      style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600, height: 1.25),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: 8),
                  _StatusChip(status: status),
                ],
              ),
              if (sku != null && sku.isNotEmpty) ...[
                const SizedBox(height: 6),
                Text('SKU: $sku', style: TextStyle(fontSize: 12, color: Colors.grey.shade600, fontWeight: FontWeight.w500)),
              ],
              const SizedBox(height: 8),
              Wrap(spacing: 8, runSpacing: 6, children: [
                if (category != null && category.isNotEmpty) _buildInfoChip(Icons.category_outlined, category, Colors.blue.shade600, Colors.blue.shade50),
                if (hasVariations) _buildInfoChip(Icons.tune, '$variationsCount variations', Colors.purple.shade600, Colors.purple.shade50),
              ]),
            ],
          ),
        ),

        const SizedBox(width: 12),

        // Price & Stock
        Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(children: [Icon(Icons.attach_money, size: 18, color: Colors.green.shade600), const SizedBox(width: 4), Text('$price', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.green.shade600))]),
            const SizedBox(height: 8),
            Row(children: [Icon(Icons.inventory_outlined, size: 16, color: manageStock && (stock == null || stock == 0) ? Colors.red.shade600 : Colors.blue.shade600), const SizedBox(width: 6), Text(manageStock ? '${stock ?? 0}' : 'N/A', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: manageStock && (stock == null || stock == 0) ? Colors.red.shade600 : Colors.blue.shade600))]),
          ],
        ),

        const SizedBox(width: 8),

        // Actions
        _buildActionsMenu(context),
      ],
    );
  }

  Widget _buildCompactLayout(BuildContext context, String name, String price, bool manageStock, dynamic stock, String status, dynamic imageUrl, String? sku, String? category, bool hasVariations, int variationsCount) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        // Product Name and Image - Full Width Section
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: Colors.grey.shade50,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.grey.shade200),
          ),
          child: Row(
            children: [
              // Product Image
              _buildProductImage(imageUrl, 56),
              const SizedBox(width: 12),
              
              // Product Info - Full Width
                    Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Product Name and Status
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            name,
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w700,
                              height: 1.3,
                            ),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        const SizedBox(width: 8),
                        _StatusChip(status: status),
                      ],
                    ),
                    if (sku != null && sku.isNotEmpty) ...[
                    const SizedBox(height: 4),
                    Text(
                      'SKU: $sku',
                      style: TextStyle(
                        fontSize: 11,
                        color: Colors.grey.shade600,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                    const SizedBox(height: 6),
                    // Price and Stock Row
                    Row(
                      children: [
                        Icon(Icons.attach_money, size: 16, color: Colors.green.shade600),
                        const SizedBox(width: 4),
                        Text(
                          '\$$price',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w700,
                            color: Colors.green.shade600,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Icon(
                          Icons.inventory_outlined,
                          size: 14,
                          color: manageStock && (stock == null || stock == 0) 
                              ? Colors.red.shade600 
                              : Colors.blue.shade600,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          manageStock ? '${stock ?? 0}' : 'N/A',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: manageStock && (stock == null || stock == 0) 
                                ? Colors.red.shade600 
                                : Colors.blue.shade600,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              // Inline actions to save vertical space
              const SizedBox(width: 8),
              _buildActionsMenu(context),
            ],
          ),
        ),
        
  const SizedBox(height: 8),
        
        // Additional Info Row
        if ((category != null && category.isNotEmpty) || hasVariations) ...[
          const SizedBox(height: 12),
          SizedBox(
            width: double.infinity,
        child: Wrap(
          spacing: 6,
          runSpacing: 4,
              children: [
                if (category != null && category.isNotEmpty)
                  _buildInfoChip(
                    Icons.category_outlined,
                    category,
                    Colors.blue.shade600,
                    Colors.blue.shade50,
                  ),
                if (hasVariations)
                  _buildInfoChip(
                    Icons.tune,
                    '$variationsCount variations',
                    Colors.purple.shade600,
                    Colors.purple.shade50,
                  ),
              ],
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildProductImage(dynamic imageUrl, double size) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        color: Colors.grey.shade100,
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(12),
        child: imageUrl != null && imageUrl.toString().isNotEmpty
            ? Image.network(
                imageUrl.toString(),
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) => _buildPlaceholderImage(size),
                loadingBuilder: (context, child, loadingProgress) {
                  if (loadingProgress == null) return child;
                  return Center(
                    child: SizedBox(
                      width: size * 0.3,
                      height: size * 0.3,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        color: Colors.grey.shade400,
                      ),
                    ),
                  );
                },
              )
            : _buildPlaceholderImage(size),
      ),
    );
  }

  Widget _buildPlaceholderImage(double size) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        color: Colors.grey.shade100,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Icon(
        Icons.image_outlined,
        size: size * 0.4,
        color: Colors.grey.shade400,
      ),
    );
  }

  Widget _buildInfoChip(IconData icon, String label, Color color, Color backgroundColor) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionsMenu(BuildContext context) {
    return PopupMenuButton<String>(
      icon: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(
          color: Colors.grey.shade100,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Icon(
          Icons.more_vert,
          size: 18,
          color: Colors.grey.shade600,
        ),
      ),
      onSelected: (value) {
        switch (value) {
          case 'edit':
            onEdit?.call();
            break;
          case 'delete':
            onDelete?.call();
            break;
        }
      },
      itemBuilder: (context) => [
        PopupMenuItem(
          value: 'edit',
          child: Row(
            children: [
              Icon(Icons.edit_outlined, size: 18, color: Colors.blue.shade600),
              const SizedBox(width: 8),
              const Text('Edit'),
            ],
          ),
        ),
        PopupMenuItem(
          value: 'delete',
          child: Row(
            children: [
              Icon(Icons.delete_outline, size: 18, color: Colors.red.shade600),
              const SizedBox(width: 8),
              const Text('Delete'),
            ],
          ),
        ),
      ],
    );
  }
}

class _StatusChip extends StatelessWidget {
  final String status;

  const _StatusChip({required this.status});

  @override
  Widget build(BuildContext context) {
    Color backgroundColor;
    Color textColor;
    String displayText;

    switch (status.toLowerCase()) {
      case 'active':
        backgroundColor = Colors.green.shade50;
        textColor = Colors.green.shade700;
        displayText = 'Active';
        break;
      case 'inactive':
        backgroundColor = Colors.orange.shade50;
        textColor = Colors.orange.shade700;
        displayText = 'Inactive';
        break;
      case 'draft':
        backgroundColor = Colors.grey.shade100;
        textColor = Colors.grey.shade700;
        displayText = 'Draft';
        break;
      default:
        backgroundColor = Colors.grey.shade100;
        textColor = Colors.grey.shade700;
        displayText = status.isNotEmpty ? status : 'Unknown';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: textColor.withOpacity(0.3)),
      ),
      child: Text(
        displayText,
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w600,
          color: textColor,
        ),
      ),
    );
  }
}
