import 'package:flutter/material.dart';
import '../services/api_client.dart';

class BalanceData {
  final double currentBalance;
  final double pendingBalance;
  final double totalEarnings;
  final double totalWithdrawals;
  final double minimumWithdrawal;
  final String currency;
  final DateTime lastUpdated;

  BalanceData({
    required this.currentBalance,
    required this.pendingBalance,
    required this.totalEarnings,
    required this.totalWithdrawals,
    required this.minimumWithdrawal,
    required this.currency,
    required this.lastUpdated,
  });

  factory BalanceData.fromJson(Map<String, dynamic> json) {
    return BalanceData(
      currentBalance: double.tryParse(json['available_balance'].toString()) ?? 0.0,
      pendingBalance: double.tryParse(json['pending_withdrawals'].toString()) ?? 0.0,
      totalEarnings: double.tryParse(json['total_sales'].toString()) ?? 0.0,
      totalWithdrawals: double.tryParse(json['total_withdrawals'].toString()) ?? 0.0,
      minimumWithdrawal: 10.0, // Default minimum withdrawal amount
      currency: json['currency'] ?? 'USD',
      lastUpdated: DateTime.now(),
    );
  }
}

class WithdrawalRequest {
  final String id;
  final double amount;
  final String status;
  final String? bankAccount;
  final String? paypalEmail;
  final String? notes;
  final DateTime requestedAt;
  final DateTime? processedAt;
  final String? rejectionReason;

  WithdrawalRequest({
    required this.id,
    required this.amount,
    required this.status,
    this.bankAccount,
    this.paypalEmail,
    this.notes,
    required this.requestedAt,
    this.processedAt,
    this.rejectionReason,
  });

  factory WithdrawalRequest.fromJson(Map<String, dynamic> json) {
    return WithdrawalRequest(
      id: json['id'].toString(),
      amount: double.tryParse(json['amount'].toString()) ?? 0.0,
      status: json['status'] ?? 'pending',
      bankAccount: json['bank_account'],
      paypalEmail: json['paypal_email'],
      notes: json['notes'],
      requestedAt: DateTime.parse(json['requested_at'] ?? DateTime.now().toIso8601String()),
      processedAt: json['processed_at'] != null 
          ? DateTime.parse(json['processed_at']) 
          : null,
      rejectionReason: json['rejection_reason'],
    );
  }
}

class BalanceProvider extends ChangeNotifier {
  BalanceData? _balanceData;
  List<WithdrawalRequest> _withdrawalHistory = [];
  bool _isLoading = false;
  bool _isSubmittingWithdrawal = false;
  bool _hasMoreWithdrawals = true;
  int _currentPage = 1;
  String? _error;

  BalanceData? get balanceData => _balanceData;
  List<WithdrawalRequest> get withdrawalHistory => _withdrawalHistory;
  bool get isLoading => _isLoading;
  bool get isSubmittingWithdrawal => _isSubmittingWithdrawal;
  bool get hasMoreWithdrawals => _hasMoreWithdrawals;
  String? get error => _error;
  
  bool get canWithdraw {
    final data = _balanceData;
    return data != null && data.currentBalance >= data.minimumWithdrawal;
  }
  
  double get availableForWithdrawal => _balanceData?.currentBalance ?? 0.0;

  Future<void> loadBalanceData() async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await ApiClient().getBalance();
      
      if (response != null) {
        _balanceData = BalanceData.fromJson(response);
      } else {
        _error = 'Failed to load balance data';
      }
    } catch (e) {
      _error = 'Network error: ${e.toString()}';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
  
  Future<void> loadWithdrawalHistory({bool refresh = false}) async {
    if (_isLoading) return;
    
    if (refresh) {
      _currentPage = 1;
      _hasMoreWithdrawals = true;
      _withdrawalHistory.clear();
    }
    
    if (!_hasMoreWithdrawals) return;
    
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await ApiClient.getWithdrawalHistory(
        page: _currentPage,
        perPage: 20,
      );
      
      if (response['success'] == true) {
        final List<dynamic> data = response['data']['data'] ?? [];
        final List<WithdrawalRequest> newWithdrawals = data
            .map((item) => WithdrawalRequest.fromJson(item))
            .toList();
        
        if (refresh) {
          _withdrawalHistory = newWithdrawals;
        } else {
          _withdrawalHistory.addAll(newWithdrawals);
        }
        
        _hasMoreWithdrawals = data.length >= 20;
        _currentPage++;
      } else {
        _error = response['message'] ?? 'Failed to load withdrawal history';
      }
    } catch (e) {
      _error = 'Network error: ${e.toString()}';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
  
  Future<bool> requestWithdrawal({
    required double amount,
    required String method, // 'bank' or 'paypal'
    String? bankAccount,
    String? paypalEmail,
    String? notes,
  }) async {
    if (_isSubmittingWithdrawal) return false;
    
    _isSubmittingWithdrawal = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await ApiClient.requestWithdrawal(
        amount: amount,
        method: method,
        bankAccount: bankAccount,
        paypalEmail: paypalEmail,
        notes: notes,
      );
      
      if (response['success'] == true) {
        // Refresh balance data
        await loadBalanceData();
        
        // Add new withdrawal to history
        final newWithdrawal = WithdrawalRequest.fromJson(response['data']);
        _withdrawalHistory.insert(0, newWithdrawal);
        
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Failed to submit withdrawal request';
        return false;
      }
    } catch (e) {
      _error = 'Network error: ${e.toString()}';
      return false;
    } finally {
      _isSubmittingWithdrawal = false;
      notifyListeners();
    }
  }
  
  Future<void> cancelWithdrawalRequest(String withdrawalId) async {
    try {
      final response = await ApiClient.cancelWithdrawalRequest(withdrawalId);
      
      if (response['success'] == true) {
        // Remove from history or update status
        final index = _withdrawalHistory.indexWhere((w) => w.id == withdrawalId);
        if (index != -1) {
          _withdrawalHistory.removeAt(index);
          notifyListeners();
        }
        
        // Refresh balance data
        await loadBalanceData();
      } else {
        _error = response['message'] ?? 'Failed to cancel withdrawal request';
        notifyListeners();
      }
    } catch (e) {
      _error = 'Network error: ${e.toString()}';
      notifyListeners();
    }
  }
  
  String formatCurrency(double amount) {
    final currency = _balanceData?.currency ?? 'USD';
    return '$currency ${amount.toStringAsFixed(2)}';
  }
  
  void clearError() {
    _error = null;
    notifyListeners();
  }
  
  void reset() {
    _balanceData = null;
    _withdrawalHistory.clear();
    _isLoading = false;
    _isSubmittingWithdrawal = false;
    _hasMoreWithdrawals = true;
    _currentPage = 1;
    _error = null;
    notifyListeners();
  }
}