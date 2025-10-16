import 'package:flutter/material.dart';

class SearchFilterBar extends StatelessWidget {
  final TextEditingController searchController;
  final String? selectedStatus;
  final String? selectedCategory;
  final bool showFilters;
  final List<Map<String, dynamic>> categories;
  final ValueChanged<String?> onStatusChanged;
  final ValueChanged<String?> onCategoryChanged;
  final VoidCallback onToggleFilters;
  final VoidCallback onClearFilters;
  final VoidCallback onClearSearch;

  const SearchFilterBar({
    super.key,
    required this.searchController,
    this.selectedStatus,
    this.selectedCategory,
    required this.showFilters,
    required this.categories,
    required this.onStatusChanged,
    required this.onCategoryChanged,
    required this.onToggleFilters,
    required this.onClearFilters,
    required this.onClearSearch,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Search Bar
        Container(
          margin: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.grey.shade50,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.grey.shade200),
          ),
          child: TextField(
            controller: searchController,
            decoration: InputDecoration(
              hintText: 'Search products...',
              hintStyle: TextStyle(color: Colors.grey.shade500),
              prefixIcon: Icon(Icons.search, color: Colors.grey.shade500),
              suffixIcon: searchController.text.isNotEmpty
                  ? IconButton(
                      icon: Icon(Icons.clear, color: Colors.grey.shade500),
                      onPressed: onClearSearch,
                    )
                  : IconButton(
                      icon: Icon(
                        showFilters ? Icons.filter_list : Icons.filter_list_outlined,
                        color: showFilters ? Theme.of(context).primaryColor : Colors.grey.shade500,
                      ),
                      onPressed: onToggleFilters,
                    ),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            ),
          ),
        ),
        
        // Filter Bar
        if (showFilters)
          Container(
            margin: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.grey.shade200),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.05),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Filter Header
                Row(
                  children: [
                    Icon(
                      Icons.filter_list,
                      size: 20,
                      color: Theme.of(context).primaryColor,
                    ),
                    const SizedBox(width: 8),
                    Text(
                      'Filters',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: Theme.of(context).primaryColor,
                      ),
                    ),
                    const Spacer(),
                    if (selectedStatus != null || selectedCategory != null)
                      TextButton.icon(
                        onPressed: onClearFilters,
                        icon: const Icon(Icons.clear_all, size: 18),
                        label: const Text('Clear All'),
                        style: TextButton.styleFrom(
                          foregroundColor: Colors.red.shade600,
                          padding: const EdgeInsets.symmetric(horizontal: 8),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 16),
                
                // Filter Options
                Row(
                  children: [
                    // Status Filter
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Status',
                            style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w500,
                              color: Colors.grey.shade700,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.symmetric(horizontal: 12),
                            decoration: BoxDecoration(
                              border: Border.all(color: Colors.grey.shade300),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: DropdownButtonHideUnderline(
                              child: DropdownButton<String?>(
                                value: selectedStatus,
                                hint: const Text('All Status'),
                                isExpanded: true,
                                onChanged: onStatusChanged,
                                items: const [
                                  DropdownMenuItem<String?>(
                                    value: null,
                                    child: Text('All Status'),
                                  ),
                                  DropdownMenuItem<String>(
                                    value: 'approved',
                                    child: Row(
                                      children: [
                                        Icon(Icons.check_circle, size: 16, color: Colors.green),
                                        SizedBox(width: 8),
                                        Text('Approved'),
                                      ],
                                    ),
                                  ),
                                  DropdownMenuItem<String>(
                                    value: 'pending',
                                    child: Row(
                                      children: [
                                        Icon(Icons.schedule, size: 16, color: Colors.orange),
                                        SizedBox(width: 8),
                                        Text('Pending'),
                                      ],
                                    ),
                                  ),
                                  DropdownMenuItem<String>(
                                    value: 'rejected',
                                    child: Row(
                                      children: [
                                        Icon(Icons.cancel, size: 16, color: Colors.red),
                                        SizedBox(width: 8),
                                        Text('Rejected'),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 16),
                    
                    // Category Filter
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Category',
                            style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w500,
                              color: Colors.grey.shade700,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.symmetric(horizontal: 12),
                            decoration: BoxDecoration(
                              border: Border.all(color: Colors.grey.shade300),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: DropdownButtonHideUnderline(
                              child: DropdownButton<String?>(
                                value: selectedCategory,
                                hint: const Text('All Categories'),
                                isExpanded: true,
                                onChanged: onCategoryChanged,
                                items: [
                                  const DropdownMenuItem<String?>(
                                    value: null,
                                    child: Text('All Categories'),
                                  ),
                                  ...categories.map((category) => DropdownMenuItem<String>(
                                    value: category['id'].toString(),
                                    child: Row(
                                      children: [
                                        const Icon(Icons.category, size: 16, color: Colors.blue),
                                        const SizedBox(width: 8),
                                        Expanded(
                                          child: Text(
                                            category['name'] ?? 'Unknown',
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ),
                                      ],
                                    ),
                                  )),
                                ],
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
          ),
      ],
    );
  }
}