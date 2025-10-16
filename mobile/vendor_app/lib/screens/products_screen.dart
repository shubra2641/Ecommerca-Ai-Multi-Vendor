// ignore_for_file: deprecated_member_use, use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/products_provider.dart';
import '../services/api_client.dart';
import '../app_routes.dart';
// removed unused widget imports (UI consolidated in this file)
import '../l10n/app_localizations.dart';
import '../widgets/product_card_v2.dart';

class ProductsScreen extends StatefulWidget {
  const ProductsScreen({super.key});

  @override
  State<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  final _scrollController = ScrollController();
  final _searchController = TextEditingController();
  String _searchQuery = '';
  String _selectedStatus = 'all';
  bool _isGridView = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final provider = Provider.of<ProductsProvider>(context, listen: false);
      await provider.refresh();
    });
    _scrollController.addListener(() {
      if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
        final provider = Provider.of<ProductsProvider>(context, listen: false);
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

  List<dynamic> get _filteredItems {
    final provider = Provider.of<ProductsProvider>(context, listen: false);
    var items = provider.items;
    
    // Apply search filter
    if (_searchQuery.isNotEmpty) {
      items = items.where((item) {
        final name = item['name']?.toString().toLowerCase() ?? '';
        final sku = item['sku']?.toString().toLowerCase() ?? '';
        final query = _searchQuery.toLowerCase();
        return name.contains(query) || sku.contains(query);
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
  final theme = Theme.of(context);
  final isDark = theme.brightness == Brightness.dark;
    final localizations = AppLocalizations.of(context)!;
    final screenWidth = MediaQuery.of(context).size.width;
    final isTablet = screenWidth > 600;
    
    return Scaffold(
      backgroundColor: isDark ? theme.scaffoldBackgroundColor : Colors.grey.shade50,
      appBar: _buildAppBar(context, localizations),
      body: Column(
        children: [
          // Header Section with Stats and Controls
          _buildHeaderSection(context, localizations, isTablet),
          
          // Products Content
          Expanded(
            child: Consumer<ProductsProvider>(
              builder: (context, provider, _) {
                final allItems = provider.items;
                final filteredItems = _filteredItems;
                
                if (allItems.isEmpty && provider.isLoading) {
                  return _buildLoadingState();
                }
                
                if (allItems.isEmpty) {
                  return _buildEmptyState(context, localizations);
                }
                
                return _buildProductsList(context, isTablet, provider, filteredItems);
              },
            ),
          ),
        ],
      ),
      floatingActionButton: _buildFloatingActionButton(context, localizations),
    );
  }

  PreferredSizeWidget _buildAppBar(BuildContext context, AppLocalizations localizations) {
  final theme = Theme.of(context);
  final isDark = theme.brightness == Brightness.dark;

  return AppBar(
      title: Text(
        localizations.products,
        style: const TextStyle(
          fontWeight: FontWeight.w600,
          fontSize: 20,
        ),
      ),
  backgroundColor: theme.appBarTheme.backgroundColor,
  foregroundColor: theme.appBarTheme.foregroundColor,
      elevation: 0,
      bottom: PreferredSize(
        preferredSize: const Size.fromHeight(1),
        child: Container(
          height: 1,
          color: isDark ? Colors.grey.shade700 : Colors.grey.shade200,
        ),
      ),
      actions: [
        Container(
          margin: const EdgeInsets.only(right: 8),
          decoration: BoxDecoration(
            color: isDark ? Colors.grey.shade800 : Colors.grey.shade100,
            borderRadius: BorderRadius.circular(8),
          ),
          child: IconButton(
            icon: Icon(
              _isGridView ? Icons.view_list_rounded : Icons.grid_view_rounded,
              color: isDark ? Colors.grey.shade300 : Colors.grey.shade700,
            ),
            onPressed: () {
              setState(() {
                _isGridView = !_isGridView;
              });
            },
            tooltip: _isGridView ? localizations.listView : localizations.gridView,
          ),
        ),
        Container(
          margin: const EdgeInsets.only(right: 8),
          decoration: BoxDecoration(
            color: isDark ? Colors.grey.shade800 : Colors.grey.shade100,
            borderRadius: BorderRadius.circular(8),
          ),
            child: IconButton(
            icon: Icon(Icons.filter_list_outlined, color: isDark ? Colors.grey.shade300 : Colors.grey.shade700),
            onPressed: () => _openFilterSheet(),
            tooltip: localizations.filters,
          ),
        ),
        Container(
          margin: const EdgeInsets.only(right: 8),
          decoration: BoxDecoration(
            color: isDark ? Colors.grey.shade800 : Colors.grey.shade100,
            borderRadius: BorderRadius.circular(8),
          ),
            child: IconButton(
            icon: Icon(Icons.refresh, color: isDark ? Colors.grey.shade300 : Colors.grey.shade700),
            onPressed: () => Provider.of<ProductsProvider>(context, listen: false).refresh(),
          ),
        ),
        const SizedBox(width: 8),
      ],
    );
  }

  Widget _buildHeaderSection(BuildContext context, AppLocalizations localizations, bool isTablet) {
    return Consumer<ProductsProvider>(
      builder: (context, provider, _) {
  final theme = Theme.of(context);
  final isDark = theme.brightness == Brightness.dark;
        final allItems = provider.items;
        
        return Container(
          color: isDark ? theme.cardColor : Colors.white,
          padding: EdgeInsets.all(isTablet ? 24 : 16),
          child: Column(
            children: [
              // Stats Row - Active and Inactive Products Only
              Row(
                children: [
                  Expanded(
                    child: _buildStatCard(
                      Icons.check_circle_outline,
                      '${allItems.where((p) => p['status'] == 'active').length}',
                      localizations.activeProducts,
                      Colors.green.shade600,
                      Colors.green.shade50,
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: _buildStatCard(
                      Icons.pause_circle_outline,
                      '${allItems.where((p) => p['status'] == 'inactive').length}',
                      localizations.inactiveProducts,
                      Colors.orange.shade600,
                      Colors.orange.shade50,
                    ),
                  ),
                ],
              ),
              
              if (allItems.isNotEmpty) ...[
                const SizedBox(height: 16),
                // Search and Filter Row
                Row(
                  children: [
                    Expanded(
                      child: Container(
                        decoration: BoxDecoration(
                          color: isDark ? Colors.grey.shade800 : Colors.grey.shade100,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: isDark ? Colors.grey.shade700 : Colors.grey.shade200
                          ),
                        ),
                        child: TextField(
                          controller: _searchController,
                          decoration: InputDecoration(
                            hintText: localizations.searchProducts,
                            hintStyle: TextStyle(
                              color: isDark ? Colors.grey.shade400 : Colors.grey.shade500
                            ),
                            prefixIcon: Icon(
                              Icons.search, 
                              color: isDark ? Colors.grey.shade400 : Colors.grey.shade500
                            ),
                            border: InputBorder.none,
                            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                          ),
                          onChanged: (value) {
                            setState(() {
                              _searchQuery = value;
                            });
                          },
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Container(
                      decoration: BoxDecoration(
                        color: _selectedStatus != 'all' 
                            ? (isDark ? Colors.blue.shade900.withOpacity(0.3) : Colors.blue.shade100)
                            : (isDark ? Colors.grey.shade800 : Colors.grey.shade100),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: _selectedStatus != 'all' 
                              ? (isDark ? Colors.blue.shade700 : Colors.blue.shade200)
                              : (isDark ? Colors.grey.shade700 : Colors.grey.shade200)
                        ),
                      ),
                      child: IconButton(
                        icon: Icon(
                          Icons.filter_list,
                          color: _selectedStatus != 'all' 
                              ? Colors.blue.shade600 
                              : (isDark ? Colors.grey.shade400 : Colors.grey.shade600),
                        ),
                        onPressed: () => _openFilterSheet(),
                        tooltip: localizations.filter,
                      ),
                    ),
                  ],
                ),
              ],
            ],
          ),
        );
      },
    );
  }

  Widget _buildStatCard(IconData icon, String value, String label, Color color, Color backgroundColor) {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: isDark ? color.withOpacity(0.15) : backgroundColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isDark ? color.withOpacity(0.4) : color.withOpacity(0.2)
        ),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 20),
          const SizedBox(height: 4),
          Text(
            value,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: color,
            ),
          ),
          Text(
            label,
            style: TextStyle(
              fontSize: 10,
              color: color,
              fontWeight: FontWeight.w500,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildLoadingState() {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const CircularProgressIndicator(),
          const SizedBox(height: 16),
          Text(
            AppLocalizations.of(context)!.loadingProducts,
            style: TextStyle(
              fontSize: 16,
              color: isDark ? Colors.grey.shade400 : Colors.grey,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState(BuildContext context, AppLocalizations localizations) {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: isDark ? Colors.grey.shade800 : Colors.grey.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(
                Icons.inventory_2_outlined,
                size: 64,
                color: isDark ? Colors.grey.shade500 : Colors.grey.shade400,
              ),
            ),
            const SizedBox(height: 24),
            Text(
              localizations.noProductsYet,
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w600,
                color: isDark ? Colors.grey.shade400 : Colors.grey,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              localizations.startByAddingFirstProduct,
              style: TextStyle(
                fontSize: 14,
                color: isDark ? Colors.grey.shade500 : Colors.grey.shade600,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 32),
            ElevatedButton.icon(
              onPressed: () async {
                final res = await Navigator.of(context).pushNamed(AppRoutes.productEdit);
                if (res == true) await Provider.of<ProductsProvider>(context, listen: false).refresh();
              },
              icon: const Icon(Icons.add),
              label: Text(localizations.addProduct),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.blue.shade600,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildProductsList(BuildContext context, bool isTablet, ProductsProvider provider, List<dynamic> filteredItems) {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    
    if (filteredItems.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.search_off, 
              size: 80, 
              color: isDark ? Colors.grey.shade500 : Colors.grey.shade400
            ),
            const SizedBox(height: 16),
            Text(
              AppLocalizations.of(context)!.noProductsFound,
              style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                color: isDark ? Colors.grey.shade400 : Colors.grey.shade600,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              AppLocalizations.of(context)!.tryAdjustingSearchOrFilters,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: isDark ? Colors.grey.shade500 : Colors.grey.shade500,
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: () => provider.refresh(),
      color: Colors.blue.shade600,
      child: _isGridView
          ? _buildGridView(isTablet, provider, filteredItems)
          : _buildListView(isTablet, provider, filteredItems),
    );
  }

  Widget _buildGridView(bool isTablet, ProductsProvider provider, List<dynamic> filteredItems) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final available = constraints.maxWidth;
        // decide cell width target (min 260) and number of columns
        final targetCellWidth = isTablet ? 360 : 300;
        int crossAxisCount = (available / targetCellWidth).floor();
        if (crossAxisCount < 1) crossAxisCount = 1;
        // compute item width and choose a target item height to avoid overflow
        final itemWidth = available / crossAxisCount - 12 /*spacing*/;
  // Increase desired item height to give grid tiles more vertical room and avoid overflow
  const desiredItemHeight = 180.0;
        final childAspectRatio = itemWidth / desiredItemHeight;

        return GridView.builder(
          controller: _scrollController,
          padding: const EdgeInsets.all(12),
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: crossAxisCount,
            childAspectRatio: childAspectRatio,
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
          ),
          itemCount: filteredItems.length + (provider.hasMore && _searchQuery.isEmpty && _selectedStatus == 'all' ? 1 : 0),
          itemBuilder: (context, index) {
            if (index >= filteredItems.length) {
              return const Center(child: CircularProgressIndicator());
            }
            final product = filteredItems[index];
            return ProductCardV2(
              product: {
                ...product,
                'main_image': _resolveImagePath(product['main_image'] ?? product['image']) ?? product['main_image'],
              },
              isGridChild: true,
              onTap: () => _editProduct(product),
              onEdit: () => _editProduct(product),
              onDelete: () => _showDeleteConfirmation(product),
            );
          },
        );
      },
    );
  }

  Widget _buildListView(bool isTablet, ProductsProvider provider, List<dynamic> filteredItems) {
    // Simple list: render only product cards (no table/header) — keeps a single consistent design
    return ListView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
      itemCount: filteredItems.length + (provider.hasMore && _searchQuery.isEmpty && _selectedStatus == 'all' ? 1 : 0),
      itemBuilder: (context, index) {
        if (index >= filteredItems.length) {
          return const Padding(
            padding: EdgeInsets.all(12),
            child: Center(child: CircularProgressIndicator()),
          );
        }

        final product = filteredItems[index];
        return Padding(
          padding: const EdgeInsets.symmetric(vertical: 8),
          child: ProductCardV2(
            product: {
              ...product,
              'main_image': _resolveImagePath(product['main_image'] ?? product['image']) ?? product['main_image'],
            },
            onTap: () => _editProduct(product),
            onEdit: () => _editProduct(product),
            onDelete: () => _showDeleteConfirmation(product),
          ),
        );
      },
    );
  }

  Widget _buildFloatingActionButton(BuildContext context, AppLocalizations localizations) {
    return FloatingActionButton.extended(
      onPressed: () async {
        final res = await Navigator.of(context).pushNamed(AppRoutes.productEdit);
        if (res == true) await Provider.of<ProductsProvider>(context, listen: false).refresh();
      },
      icon: const Icon(Icons.add),
      label: Text(localizations.addProduct),
      backgroundColor: Colors.blue.shade600,
      foregroundColor: Colors.white,
      elevation: 4,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),

    );
  }

  Future<bool?> _showDeleteConfirmation(Map<String, dynamic> product) {
    return showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(AppLocalizations.of(context)!.deleteProduct),
        content: Text(AppLocalizations.of(context)!.deleteProductConfirmation(product['name'])),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(false),
            child: Text(AppLocalizations.of(context)!.cancel),
          ),
          TextButton(
            onPressed: () => Navigator.of(context).pop(true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: Text(AppLocalizations.of(context)!.delete),
          ),
        ],
      ),
    ).then((confirmed) {
      if (confirmed == true) {
        _deleteProduct(product);
      }
      return confirmed;
    });
  }

  Future<void> _deleteProduct(Map<String, dynamic> product) async {
    try {
      final apiClient = ApiClient();
      await apiClient.deleteProduct(product['id']);
      if (mounted) {
        Provider.of<ProductsProvider>(context, listen: false).refresh();
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(AppLocalizations.of(context)!.productDeletedSuccessfully(product['name'])),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(AppLocalizations.of(context)!.failedToDeleteProduct(e.toString())),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  void _editProduct(Map<String, dynamic> product) {
    Navigator.of(context).pushNamed(
      AppRoutes.productEdit,
      arguments: product,
    ).then((result) {
      if (result == true) {
        Provider.of<ProductsProvider>(context, listen: false).refresh();
      }
    });
  }

  // Resolve image URL: if absolute return as-is, otherwise prefix with effective base URL
  String? _resolveImagePath(dynamic path) {
    if (path == null) return null;
    try {
      final p = path.toString();
      if (p.startsWith('http://') || p.startsWith('https://')) return p;
      final normalized = p.startsWith('/') ? p : '/$p';
      return ApiClient().effectiveBaseUrl.replaceAll(RegExp(r'\/$'), '') + normalized;
    } catch (_) {
      return null;
    }
  }

  // Product rows now use ProductCardV2 (responsive grid/list). Old _buildProductRow removed.

  void _openFilterSheet() {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;
    
    showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(12)),
      ),
      builder: (ctx) {
        String tempStatus = _selectedStatus;
        return Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: SafeArea(
            child: Container(
              padding: const EdgeInsets.all(16),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 6),
                  Center(child: Container(
                    height: 4, 
                    width: 40, 
                    decoration: BoxDecoration(
                      color: isDark ? Colors.grey[600] : Colors.grey[300], 
                      borderRadius: BorderRadius.circular(4)
                    )
                  )),
                  const SizedBox(height: 12),
                  Text(AppLocalizations.of(context)!.filters, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                  const SizedBox(height: 12),
                  DropdownButtonFormField<String>(
                    initialValue: tempStatus,
                    decoration: InputDecoration(labelText: AppLocalizations.of(context)!.status, border: const OutlineInputBorder()),
                    items: [
                      DropdownMenuItem(value: 'all', child: Text(AppLocalizations.of(context)!.all)),
                      DropdownMenuItem(value: 'active', child: Text(AppLocalizations.of(context)!.active)),
                      DropdownMenuItem(value: 'inactive', child: Text(AppLocalizations.of(context)!.inactive)),
                      DropdownMenuItem(value: 'out_of_stock', child: Text(AppLocalizations.of(context)!.outOfStock)),
                    ],
                    onChanged: (v) => tempStatus = v ?? 'all',
                  ),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      TextButton(
                        onPressed: () {
                          setState(() {
                            _selectedStatus = 'all';
                          });
                          Navigator.of(ctx).pop();
                        },
                        child: Text(AppLocalizations.of(context)!.clear),
                      ),
                      const SizedBox(width: 8),
                      ElevatedButton(
                        onPressed: () {
                          setState(() => _selectedStatus = tempStatus);
                          Navigator.of(ctx).pop();
                        },
                        child: Text(AppLocalizations.of(context)!.apply),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }
}
