import 'package:flutter/material.dart';
import '../services/api_client.dart';

class DashboardData {
  final double totalSales;
  final int totalOrders;
  final int pendingOrders;
  final int activeProducts;
  final int pendingProducts;
  final int totalProducts;
  final double pendingWithdrawals;
  final double merchantBalance;
  final List<dynamic> recentOrders;
  final Map<String, dynamic> salesChart;
  final Map<String, dynamic> ordersChart;
  final double ordersGrowth;
  final double salesGrowth;
  
  DashboardData({
    required this.totalSales,
    required this.totalOrders,
    required this.pendingOrders,
  required this.activeProducts,
  required this.pendingProducts,
  required this.totalProducts,
  required this.pendingWithdrawals,
  required this.merchantBalance,
    required this.recentOrders,
    required this.salesChart,
    required this.ordersChart,
    required this.ordersGrowth,
    required this.salesGrowth,
  });
  
  factory DashboardData.fromJson(Map<String, dynamic> json) {
    return DashboardData(
      totalSales: _parseDouble(json['total_sales']),
      totalOrders: _parseInt(json['total_orders']),
      pendingOrders: _parseInt(json['pending_orders']),
  activeProducts: _parseInt(json['active_products'] ?? json['enabled_products'] ?? 0),
  pendingProducts: _parseInt(json['pending_products'] ?? json['disabled_products'] ?? 0),
      totalProducts: _parseInt(json['total_products']),
      pendingWithdrawals: _parseDouble(json['pending_withdrawals']),
  merchantBalance: _extractBalance(json),
      recentOrders: json['recent_orders'] ?? [],
      salesChart: json['sales_chart'] ?? {},
      ordersChart: json['orders_chart'] ?? {},
      ordersGrowth: _parseDouble(json['orders_growth']),
      salesGrowth: _parseDouble(json['sales_growth']),
    );
  }
  
  static double _parseDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) {
      final cleaned = value.replaceAll(',', '').trim();
      return double.tryParse(cleaned) ?? 0.0;
    }
    return 0.0;
  }
  
  static int _parseInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is double) return value.toInt();
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
  }

  /// Extract balance from several possible keys and nested shapes.
  static double _extractBalance(Map<String, dynamic> json) {
    // Direct keys
    final candidates = [
      json['actual_balance'],
      json['merchant_balance'],
      json['balance'],
      json['available_balance'],
    ];
    for (final c in candidates) {
      if (c != null) return _parseDouble(c);
    }

    // Nested statistics object: { statistics: { available_balance: ... } }
    if (json['statistics'] is Map) {
      final stats = Map<String, dynamic>.from(json['statistics']);
      final statCandidates = [stats['available_balance'], stats['available'], stats['balance']];
      for (final sc in statCandidates) {
        if (sc != null) return _parseDouble(sc);
      }
    }

    // As a last resort, check for top-level data->statistics (in case we weren't unwrapped)
    if (json['data'] is Map) {
      final data = Map<String, dynamic>.from(json['data']);
      if (data['statistics'] is Map) {
        final stats = Map<String, dynamic>.from(data['statistics']);
        final statCandidates = [stats['available_balance'], stats['available'], stats['balance']];
        for (final sc in statCandidates) {
          if (sc != null) return _parseDouble(sc);
        }
      }
    }

    return 0.0;
  }
}

class DashboardProvider extends ChangeNotifier {
  final ApiClient _client = ApiClient();
  DashboardData? _data;
  bool _loading = false;
  String? _error;
  DateTime? _lastUpdated;
  
  DashboardData? get data => _data;
  bool get isLoading => _loading;
  String? get error => _error;
  String? get errorMessage => _error;
  DateTime? get lastUpdated => _lastUpdated;
  
  bool get hasData => _data != null;
  
  /// Check if data needs refresh (older than 5 minutes)
  bool get needsRefresh {
    if (_lastUpdated == null) return true;
    final lastUpdate = _lastUpdated;
    if (lastUpdate == null) return true;
    return DateTime.now().difference(lastUpdate).inMinutes > 5;
  }
  
  Future<void> loadDashboard({bool forceRefresh = false}) async {
    if (_loading) return;
    
    // Skip if we have recent data and not forcing refresh
    if (!forceRefresh && hasData && !needsRefresh) return;
    
    _loading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await _client.getDashboard();

      if (response != null) {
        // Normalize common wrapper shapes like { data: { ... } } or { dashboard: { ... } }
        Map<String, dynamic> payload = Map<String, dynamic>.from(response);
        if (response['data'] is Map) {
          payload = Map<String, dynamic>.from(response['data']);
        } else if (response['dashboard'] is Map) {
          payload = Map<String, dynamic>.from(response['dashboard']);
        }

        // If the dashboard payload doesn't include the stored user balance, try to fetch it
        // from the profile endpoint (users.balance) so the dashboard shows the DB value.
  final parsedBalance = DashboardData._extractBalance(payload);
        if (parsedBalance == 0.0) {
          try {
            final profileRes = await _client.getProfile();
            if (profileRes != null) {
              Map<String, dynamic> prof = Map<String, dynamic>.from(profileRes);
              if (prof['data'] is Map) prof = Map<String, dynamic>.from(prof['data']);
              final profBalance = prof['balance'] ?? prof['available_balance'] ?? prof['actual_balance'];
              if (profBalance != null) payload['balance'] = profBalance;
            }
          } catch (_) {}
        }

        _data = DashboardData.fromJson(payload);
        _lastUpdated = DateTime.now();
        _error = null;
      } else {
        _error = 'Failed to load dashboard data';
      }
    } catch (e) {
      _error = 'Error loading dashboard: ${e.toString()}';
    } finally {
      _loading = false;
      notifyListeners();
    }
  }
  
  Future<void> refresh() async {
    await loadDashboard(forceRefresh: true);
  }
  
  Future<void> loadData() async {
    await loadDashboard();
  }
  
  Future<void> refreshData() async {
    await loadDashboard(forceRefresh: true);
  }
  
  void clearData() {
    _data = null;
    _error = null;
    _lastUpdated = null;
    notifyListeners();
  }
  
  /// Get formatted sales amount
  String get formattedTotalSales {
    final data = _data;
    if (data == null) return '\$0.00';
    return '\$${data.totalSales.toStringAsFixed(2)}';
  }
  
  /// Get formatted pending withdrawals
  String get formattedPendingWithdrawals {
    final data = _data;
    if (data == null) return '\$0.00';
    return '\$${data.pendingWithdrawals.toStringAsFixed(2)}';
  }
  
  /// Get orders completion rate
  double get ordersCompletionRate {
    final data = _data;
    if (data == null || data.totalOrders == 0) return 0.0;
    final completedOrders = data.totalOrders - data.pendingOrders;
    return completedOrders / data.totalOrders;
  }
  
  /// Get recent orders count
  int get recentOrdersCount {
    return _data?.recentOrders.length ?? 0;
  }
  
  /// Get total products count
  int get totalProducts {
    return _data?.totalProducts ?? 0;
  }
  
  /// Get total orders count
  int get totalOrders {
    return _data?.totalOrders ?? 0;
  }
}