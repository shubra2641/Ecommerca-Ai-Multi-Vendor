import 'package:flutter/material.dart';
import '../services/api_client.dart';

class OrdersProvider extends ChangeNotifier {
  final ApiClient _client = ApiClient();
  final List<dynamic> _items = [];
  int _page = 1;
  bool _hasMore = true;
  bool _loading = false;
  
  // Filter parameters (kept for API compatibility; may be used later)
  // ignore: unused_field
  String? _searchQuery;
  // ignore: unused_field
  String? _status;
  // ignore: unused_field
  DateTime? _startDate;
  // ignore: unused_field
  DateTime? _endDate;

  List<dynamic> get items => List.unmodifiable(_items);
  bool get hasMore => _hasMore;
  bool get isLoading => _loading;
  String? _lastError;
  String? get lastError => _lastError;
  
  Future<void> refresh() async {
    _items.clear();
    _page = 1;
    _hasMore = true;
    await loadNextPage();
  }

  Future<void> loadNextPage() async {
    if (!_hasMore || _loading) return;
    _loading = true;
    notifyListeners();
    try {
      final res = await _client.getOrders(page: _page);
      _loading = false;

      if (res == null) {
        _hasMore = false;
        _lastError = 'Empty response from getOrders';
        notifyListeners();
        return;
      }

      // Normalize possible response shapes
      List<dynamic> data = [];

      // Expected shape: Map<String, dynamic> with 'data' or 'orders' list.
      if (res is Map<String, dynamic>) {
        if (res['data'] is List) {
          data = List<dynamic>.from(res['data'] as List);
        } else if (res['orders'] is List) {
          data = List<dynamic>.from(res['orders'] as List);
        } else {
          // Try to find a nested 'data' inside the single top-level value
          if (res.keys.length == 1) {
            final v = res.values.first;
            if (v is Map && v['data'] is List) {
              data = List<dynamic>.from(v['data'] as List);
            }
          }
        }
      }

      if (data.isNotEmpty) {
        _items.addAll(data);
        _page++;
        // Heuristic: if returned page has fewer than typical page size, stop
        if (data.isEmpty) _hasMore = false;
        _lastError = null;
      } else {
        _hasMore = false;
        _lastError = 'No orders found in response (parsed shape may be unexpected)';
      }
    } catch (e) {
      _loading = false;
      _hasMore = false;
      _lastError = 'Exception loading orders: ${e.toString()}';
    }

    notifyListeners();
  }
  
  Future<void> applyFilters({
    String? searchQuery,
    String? status,
    DateTime? startDate,
    DateTime? endDate,
  }) async {
    _searchQuery = searchQuery;
    _status = status;
    _startDate = startDate;
    _endDate = endDate;
    await refresh();
  }
}
