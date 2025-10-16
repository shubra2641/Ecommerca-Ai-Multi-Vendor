import 'package:flutter/material.dart';
import '../services/api_client.dart';

class ProductsProvider extends ChangeNotifier {
  final ApiClient _client = ApiClient();
  final List<dynamic> _items = [];
  int _page = 1;
  bool _hasMore = true;
  bool _loading = false;

  List<dynamic> get items => List.unmodifiable(_items);
  bool get hasMore => _hasMore;
  bool get isLoading => _loading;

  Future<void> loadNextPage() async {
    if (!_hasMore || _loading) return;
    _loading = true;
    notifyListeners();
    final res = await _client.getProducts(page: _page);
    _loading = false;
    if (res != null) {
      List data = [];
      if (res['data'] is List) {
        data = res['data'] as List;
      } else if (res['data'] is Map && (res['data']['data'] ?? false) is List) {
        data = res['data']['data'];
      }
      final meta = res['meta'];
      int? currentPage;
      int? lastPage;
      if (meta is Map) {
        currentPage = meta['current_page'] is int ? meta['current_page'] as int : int.tryParse(meta['current_page']?.toString() ?? '');
        lastPage = meta['last_page'] is int ? meta['last_page'] as int : int.tryParse(meta['last_page']?.toString() ?? '');
      }
      _items.addAll(data);
      // Decide hasMore based on meta if available, otherwise fallback to empty page heuristic
      if (currentPage != null && lastPage != null) {
        _hasMore = currentPage < lastPage;
        _page = (currentPage + 1);
      } else {
        if (data.isEmpty) {
          _hasMore = false;
        } else { _page++; }
      }
    } else {
      _hasMore = false;
    }
    notifyListeners();
  }

  /// Clear and reload from first page
  Future<void> refresh() async {
    _items.clear();
    _page = 1;
    _hasMore = true;
    notifyListeners();
    await loadNextPage();
  }

  void reset() {
    _items.clear();
    _page = 1;
    _hasMore = true;
    _loading = false;
    notifyListeners();
  }
}
