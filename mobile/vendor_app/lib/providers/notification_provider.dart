import 'package:flutter/material.dart';
import '../services/api_client.dart';
import '../services/local_notifications.dart';

class NotificationItem {
  final String id;
  final String title;
  final String message;
  final String type;
  final DateTime createdAt;
  final bool isRead;
  final Map<String, dynamic>? data;

  NotificationItem({
    required this.id,
    required this.title,
    required this.message,
    required this.type,
    required this.createdAt,
    this.isRead = false,
    this.data,
  });

  factory NotificationItem.fromJson(Map<String, dynamic> json) {
    return NotificationItem(
      id: json['id'].toString(),
      title: json['title'] ?? '',
      message: json['message'] ?? '',
      type: json['type'] ?? 'info',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      isRead: json['is_read'] == 1 || json['is_read'] == true,
      data: json['data'],
    );
  }

  NotificationItem copyWith({
    String? id,
    String? title,
    String? message,
    String? type,
    DateTime? createdAt,
    bool? isRead,
    Map<String, dynamic>? data,
  }) {
    return NotificationItem(
      id: id ?? this.id,
      title: title ?? this.title,
      message: message ?? this.message,
      type: type ?? this.type,
      createdAt: createdAt ?? this.createdAt,
      isRead: isRead ?? this.isRead,
      data: data ?? this.data,
    );
  }
}

class NotificationProvider extends ChangeNotifier {
  List<NotificationItem> _notifications = [];
  bool _isLoading = false;
  bool _hasMore = true;
  int _currentPage = 1;
  String? _error;

  List<NotificationItem> get notifications => _notifications;
  bool get isLoading => _isLoading;
  bool get hasMore => _hasMore;
  String? get error => _error;
  
  int get unreadCount => _notifications.where((n) => !n.isRead).length;
  List<NotificationItem> get unreadNotifications => 
      _notifications.where((n) => !n.isRead).toList();

  Future<void> loadNotifications({bool refresh = false}) async {
    if (_isLoading) return;
    
    if (refresh) {
      _currentPage = 1;
      _hasMore = true;
      _notifications.clear();
    }
    
    if (!_hasMore) return;
    
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await ApiClient.getNotifications(
        page: _currentPage,
        perPage: 20,
      );
      
      if (response['success'] == true) {
        final List<dynamic> data = response['data']['data'] ?? [];
        final List<NotificationItem> newNotifications = data
            .map((item) => NotificationItem.fromJson(item))
            .toList();

        // Determine which notifications are new (not already present)
        final existingIds = _notifications.map((n) => n.id).toSet();
        final incoming = newNotifications.where((n) => !existingIds.contains(n.id)).toList();

        // Insert or replace notifications list
        if (refresh) {
          _notifications = newNotifications;
        } else {
          _notifications.addAll(newNotifications);
        }

        // Trigger OS-level local notifications for incoming items
        for (final n in incoming) {
          try {
            LocalNotifications.showNotification(
              id: n.id,
              title: n.title.isNotEmpty ? n.title : 'New notification',
              body: n.message,
            );
          } catch (e) {
            debugPrint('Failed to show local notification: $e');
          }
        }

        _hasMore = data.length >= 20;
        _currentPage++;
      } else {
        _error = response['message'] ?? 'Failed to load notifications';
      }
    } catch (e) {
      _error = 'Network error: ${e.toString()}';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
  
  Future<void> markAsRead(String notificationId) async {
    try {
      final response = await ApiClient.markNotificationAsRead(notificationId);
      
      if (response['success'] == true) {
        final index = _notifications.indexWhere((n) => n.id == notificationId);
        if (index != -1) {
          _notifications[index] = _notifications[index].copyWith(isRead: true);
          notifyListeners();
        }
      }
    } catch (e) {
      // Handle error silently or show a toast
      debugPrint('Error marking notification as read: $e');
    }
  }
  
  Future<void> markAllAsRead() async {
    try {
      final response = await ApiClient.markAllNotificationsAsRead();
      
      if (response['success'] == true) {
        _notifications = _notifications
            .map((n) => n.copyWith(isRead: true))
            .toList();
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error marking all notifications as read: $e');
    }
  }
  
  Future<void> deleteNotification(String notificationId) async {
    try {
      final response = await ApiClient.deleteNotification(notificationId);
      
      if (response['success'] == true) {
        _notifications.removeWhere((n) => n.id == notificationId);
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error deleting notification: $e');
    }
  }
  
  void addLocalNotification(NotificationItem notification) {
    _notifications.insert(0, notification);
    // trigger OS-level notification as well
    try {
      LocalNotifications.showNotification(
        id: notification.id,
        title: notification.title.isNotEmpty ? notification.title : 'New notification',
        body: notification.message,
      );
    } catch (e) {
      debugPrint('Local notification failed: $e');
    }
    notifyListeners();
  }
  
  void clearError() {
    _error = null;
    notifyListeners();
  }
  
  void reset() {
    _notifications.clear();
    _isLoading = false;
    _hasMore = true;
    _currentPage = 1;
    _error = null;
    notifyListeners();
  }
}