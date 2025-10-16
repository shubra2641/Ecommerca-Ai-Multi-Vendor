import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

const _secureStorage = FlutterSecureStorage();

class ApiClient {
  final String baseUrl =
      'http://127.0.0.1:8000'; // default dev host (can be localhost or saas.com)

  // Singleton pattern
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;
  ApiClient._internal();

  // Returns the URL that should be used by the app at runtime. When running on Android
  // emulator, translate localhost/127.0.0.1 to 10.0.2.2 so emulator can reach host machine.
  String get _effectiveBaseUrl {
    try {
      final uri = Uri.parse(baseUrl);
      if (Platform.isAndroid && (uri.host == 'localhost' || uri.host == '127.0.0.1')) {
        return baseUrl.replaceFirst(uri.host, '10.0.2.2');
      }
    } catch (_) {}
    return baseUrl;
  }

  // Public accessor used by UI code for normalization
  String get effectiveBaseUrl => _effectiveBaseUrl;
  String? _token;
  String _currentLocale = 'en'; // Default locale
  static void Function()? onUnauthorized;
  
  // Method to update current locale
  void setLocale(String locale) {
    _currentLocale = locale;
  }

  void setToken(String? token) {
    _token = token;
  }

  void clearToken() {
    _token = null;
    _secureStorage.delete(key: 'vendor_token');
  }

  Future<String?> readTokenFromStorage() async {
    return await _secureStorage.read(key: 'vendor_token');
  }

  Future<void> writeTokenToStorage(String token) async {
    await _secureStorage.write(key: 'vendor_token', value: token);
  }

  Future<void> deleteTokenFromStorage() async {
    await _secureStorage.delete(key: 'vendor_token');
  }

  // Initialize the singleton
  void init() {
    // Initialization is now lazy - token will be loaded when needed
  }

  Future<void> _loadToken() async {
    _token = await _secureStorage.read(key: 'vendor_token');
  }

  Future<void> ensureTokenLoaded() async {
    if (_token == null) {
      await _loadToken();
    }
  }

  Future<Map<String, dynamic>?> login(String email, String password) async {
  final uri = Uri.parse('$_effectiveBaseUrl/api/vendor/login');
    final res = await http.post(uri,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: jsonEncode({'email': email, 'password': password}));
    try {
      final parsed = jsonDecode(res.body) as Map<String, dynamic>;
      if (res.statusCode == 200 && parsed['token'] != null) {
        _token = parsed['token'];
        await _secureStorage.write(key: 'vendor_token', value: _token);
      }
      return parsed;
    } catch (_) {
      return {'message': 'Unexpected response', 'status': res.statusCode};
    }
  }

  Map<String, String> _headers() {
    final h = <String, String>{'Accept': 'application/json'};
    if (_token != null && _token!.isNotEmpty) {
      h['Authorization'] = 'Bearer $_token';
    }
    
    // Add Accept-Language header for proper localization
    h['Accept-Language'] = _currentLocale;
    
    return h;
  }

  Future<Map<String, dynamic>?> getDashboard() async {
    // ensure token loaded
    if (_token == null) await _loadToken();
    try {
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/dashboard'), headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        return jsonDecode(res.body) as Map<String, dynamic>;
      } else if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  Future<Map<String, dynamic>?> getProducts({int page = 1}) async {
    try {
      await ensureTokenLoaded();
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/products?page=$page'),
              headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        final parsed = jsonDecode(res.body) as Map<String, dynamic>;
        // Normalize if it's a paginated resource from ProductResource::collection
        if (parsed.containsKey('data') && parsed['data'] is List) {
          return parsed; // already list under data
        }
        // If Laravel resource wrapped further (unlikely) attempt to unwrap
        if (parsed.keys.length == 1 &&
            parsed.values.first is Map &&
            (parsed.values.first as Map).containsKey('data')) {
          return Map<String, dynamic>.from(parsed.values.first as Map);
        }
        return parsed;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return null;
  }

  Future<Map<String, dynamic>?> getProductCategories() async {
    try {
      await ensureTokenLoaded();
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/product-categories'),
              headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        return jsonDecode(res.body) as Map<String, dynamic>;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return null;
  }

  Future<Map<String, dynamic>?> getProductTags() async {
    try {
      await ensureTokenLoaded();
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/product-tags'),
              headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        return jsonDecode(res.body) as Map<String, dynamic>;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return null;
  }

  Future<Map<String, dynamic>?> getProductAttributes() async {
    try {
  await ensureTokenLoaded();
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/product-attributes'),
              headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        try {
          return jsonDecode(res.body) as Map<String, dynamic>;
        } catch (_) {
          return {'data': []};
        }
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return {'data': []};
  }

  Future<Map<String, dynamic>?> getLanguages() async {
    try {
      await ensureTokenLoaded();
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/languages'), headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        return jsonDecode(res.body) as Map<String, dynamic>;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return null;
  }

  Future<Map<String, dynamic>?> getOrders({int page = 1}) async {
    try {
      await ensureTokenLoaded();
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/orders?page=$page'),
              headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        return jsonDecode(res.body) as Map<String, dynamic>;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return null;
  }

  Future<Map<String, dynamic>?> getProductDetail(int id) async {
    try {
      await ensureTokenLoaded();
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/products/$id'),
              headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        return jsonDecode(res.body) as Map<String, dynamic>;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        // optional: surface a debug message
        try { stderr.writeln('getProductDetail: unauthorized for id $id'); } catch (_) {}
        return null;
      }
    } catch (_) {}
    return null;
  }

  /// Fetch the authenticated vendor profile
  Future<Map<String, dynamic>?> getProfile() async {
    try {
      await ensureTokenLoaded();
  final res = await http.get(Uri.parse('$_effectiveBaseUrl/api/vendor/profile'), headers: _headers()).timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) return jsonDecode(res.body) as Map<String, dynamic>;
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return null;
  }

  /// Update the authenticated vendor profile
  Future<bool> updateProfile(Map<String, dynamic> payload) async {
    try {
      await ensureTokenLoaded();
      final res = await http.put(
        Uri.parse('$_effectiveBaseUrl/api/vendor/profile'),
        headers: {..._headers(), 'Content-Type': 'application/json'},
        body: jsonEncode(payload),
      ).timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) return true;
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return false;
      }
    } catch (_) {}
    return false;
  }

  /// Get system settings including withdrawal configuration
  Future<Map<String, dynamic>?> getSystemSettings() async {
    try {
      await ensureTokenLoaded();
      final res = await http.get(
        Uri.parse('$_effectiveBaseUrl/api/system/settings'),
        headers: _headers(),
      ).timeout(const Duration(seconds: 10));
      
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body) as Map<String, dynamic>;
        return {'success': true, 'data': data};
      }
      
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return {'success': false, 'message': 'Unauthorized'};
      }
      
      return {'success': false, 'message': 'Failed to fetch system settings'};
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

  Future<Map<String, dynamic>?> getOrderDetail(int id) async {
    try {
      await ensureTokenLoaded();
    final res = await http
      .get(Uri.parse('$_effectiveBaseUrl/api/vendor/orders/$id'), headers: _headers())
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        return jsonDecode(res.body) as Map<String, dynamic>;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return null;
  }

  Future<bool> updateOrderStatus(int id, String status) async {
    try {
      await ensureTokenLoaded();
      final res = await http
        .put(Uri.parse('$_effectiveBaseUrl/api/vendor/orders/$id/status'),
                headers: {..._headers(), 'Content-Type': 'application/json'},
                body: jsonEncode({'status': status}))
            .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) return true;
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return false;
      }
      return false;
    } catch (_) {}
    return false;
  }

  Future<bool> createProduct(Map<String, dynamic> payload) async {
    try {
    final res = await http
      .post(Uri.parse('$_effectiveBaseUrl/api/vendor/products'),
              headers: {..._headers(), 'Content-Type': 'application/json'},
              body: jsonEncode(payload))
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 201 || res.statusCode == 200) return true;
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return false;
      }
      return false;
    } catch (_) {}
    return false;
  }

  Future<Map<String, dynamic>?> createProductAndReturnData(Map<String, dynamic> payload) async {
    try {
    final res = await http
      .post(Uri.parse('$_effectiveBaseUrl/api/vendor/products'),
              headers: {..._headers(), 'Content-Type': 'application/json'},
              body: jsonEncode(payload))
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 201 || res.statusCode == 200) {
        final data = jsonDecode(res.body) as Map<String, dynamic>;
        return data;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
      return null;
    } catch (_) {}
    return null;
  }

  Future<bool> updateProduct(int id, Map<String, dynamic> payload) async {
    try {
    final res = await http
      .put(Uri.parse('$_effectiveBaseUrl/api/vendor/products/$id'),
              headers: {..._headers(), 'Content-Type': 'application/json'},
              body: jsonEncode(payload))
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) return true;
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return false;
      }
      return false;
    } catch (_) {}
    return false;
  }

  Future<bool> updateVariation(int productId, int variationId, Map<String, dynamic> payload) async {
    try {
      await ensureTokenLoaded();
      final res = await http
          .put(
            Uri.parse('$_effectiveBaseUrl/api/vendor/products/$productId/variations/$variationId'),
            headers: {..._headers(), 'Content-Type': 'application/json'},
            body: jsonEncode(payload),
          )
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) return true;
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return false;
      }
    } catch (_) {}
    return false;
  }

  Future<bool> deleteVariation(int productId, int variationId) async {
    try {
      await ensureTokenLoaded();
      final res = await http
          .delete(
            Uri.parse('$_effectiveBaseUrl/api/vendor/products/$productId/variations/$variationId'),
            headers: _headers(),
          )
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) return true;
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return false;
      }
    } catch (_) {}
    return false;
  }

  Future<Map<String,dynamic>?> createVariation(int productId, Map<String, dynamic> payload) async {
    try {
      await ensureTokenLoaded();
      final res = await http
          .post(
            Uri.parse('$_effectiveBaseUrl/api/vendor/products/$productId/variations'),
            headers: {..._headers(), 'Content-Type': 'application/json'},
            body: jsonEncode(payload),
          )
          .timeout(const Duration(seconds: 10));
      if (res.statusCode == 201) {
        return jsonDecode(res.body) as Map<String,dynamic>;
      }
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return null;
      }
    } catch (_) {}
    return null;
  }

  Future<bool> deleteProduct(int id) async {
    try {
      await ensureTokenLoaded();
      final res = await http
        .delete(Uri.parse('$_effectiveBaseUrl/api/vendor/products/$id'),
                headers: _headers())
            .timeout(const Duration(seconds: 10));
      if (res.statusCode == 200 || res.statusCode == 204) return true;
      if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
        return false;
      }
      return false;
    } catch (_) {}
    return false;
  }

  Future<String?> uploadImage(String filePath) async {
    try {
      await ensureTokenLoaded();
      final uri = Uri.parse('$_effectiveBaseUrl/api/vendor/upload/image');
      final req = http.MultipartRequest('POST', uri);
      if (_token != null) req.headers['Authorization'] = 'Bearer $_token';
      req.fields['dummy'] = '1';
      req.files.add(await http.MultipartFile.fromPath('file', filePath));
      final streamed = await req.send().timeout(const Duration(seconds: 20));
      final res = await http.Response.fromStream(streamed);
      if (res.statusCode == 200) {
        try {
          final parsed = jsonDecode(res.body);
          if (parsed is Map && parsed['url'] is String) {
            var url = parsed['url'] as String;
            // Normalize localhost URLs so Android emulator can reach the dev server
            try {
              final u = Uri.parse(url);
              if (u.host == 'localhost' || u.host == '127.0.0.1') {
                final base = Uri.parse(_effectiveBaseUrl);
                final replaced = base.replace(path: u.path, query: u.query);
                url = replaced.toString();
              }
            } catch (_) {}
            return url;
          }
        } catch (_) {
          // ignore parse errors
        }
      } else if (res.statusCode == 401 || res.statusCode == 403) {
        if (onUnauthorized != null) onUnauthorized!();
      }
    } catch (_) {}
    return null;
  }

  Future<Map<String, dynamic>?> getBalance() async {
    try {
      await ensureTokenLoaded();
      final res = await http
        .get(Uri.parse('$_effectiveBaseUrl/api/vendor/balance'), headers: _headers())
            .timeout(const Duration(seconds: 10));
      
      final responseData = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return responseData;
      } else {
        if (res.statusCode == 401 || res.statusCode == 403) {
          if (onUnauthorized != null) onUnauthorized!();
        }
        return {
          'success': false,
          'message': responseData['message'] ?? 'Failed to fetch balance',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Network error: ${e.toString()}',
      };
    }
  }

  // Dashboard API method
  static Future<Map<String, dynamic>> getDashboardData() async {
    try {
      await _instance.ensureTokenLoaded();
      final res = await http
        .get(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/dashboard'), 
             headers: _instance._headers())
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to load dashboard data'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

  // Enhanced balance and withdrawal methods
  static Future<Map<String, dynamic>> getVendorBalance() async {
    try {
      await _instance.ensureTokenLoaded();
      final res = await http
        .get(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/balance'), 
             headers: _instance._headers())
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to load balance'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

  static Future<Map<String, dynamic>> getWithdrawalHistory({int page = 1, int perPage = 20}) async {
    try {
      await _instance.ensureTokenLoaded();
      final res = await http
        .get(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/withdrawals?page=$page&per_page=$perPage'), 
             headers: _instance._headers())
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to load withdrawal history'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

  static Future<Map<String, dynamic>> requestWithdrawal({
    required double amount,
    required String method,
    String? bankAccount,
    String? paypalEmail,
    String? notes,
  }) async {
    try {
      await _instance.ensureTokenLoaded();
      final Map<String, dynamic> requestBody = {
        'amount': amount,
        'currency': 'USD',
        'payment_method': method,
      };
      
      // Add method-specific fields
      if (method == 'bank-transfer' && bankAccount != null) {
        requestBody['bank_account'] = bankAccount;
      }
      if (method == 'paypal' && paypalEmail != null) {
        requestBody['paypal_email'] = paypalEmail;
      }
      if (notes != null && notes.isNotEmpty) {
        requestBody['notes'] = notes;
      }
      
      // Add transfer details for bank transfer
      if (method == 'bank-transfer' && bankAccount != null) {
        requestBody['transfer'] = {
          'account_number': bankAccount,
          'account_type': 'bank',
        };
      }
      if (method == 'paypal' && paypalEmail != null) {
        requestBody['transfer'] = {
          'email': paypalEmail,
          'account_type': 'paypal',
        };
      }
      
      final res = await http
        .post(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/withdrawals'),
              headers: {..._instance._headers(), 'Content-Type': 'application/json'},
              body: jsonEncode(requestBody))
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      // Handle different response status codes
      if (res.statusCode == 200 || res.statusCode == 201) {
        return data;
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Failed to submit withdrawal request',
          'errors': data['errors'] ?? {},
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Network error: ${e.toString()}',
      };
    }
  }

  static Future<Map<String, dynamic>> cancelWithdrawalRequest(String withdrawalId) async {
    try {
      await _instance.ensureTokenLoaded();
      final res = await http
        .delete(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/withdrawals/$withdrawalId'),
                headers: _instance._headers())
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to cancel withdrawal request'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

  // Notification methods
  static Future<Map<String, dynamic>> getNotifications({int page = 1, int perPage = 20}) async {
    try {
      await _instance.ensureTokenLoaded();
      final res = await http
        .get(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/notifications?page=$page&per_page=$perPage'), 
             headers: _instance._headers())
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to load notifications'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

  static Future<Map<String, dynamic>> markNotificationAsRead(String notificationId) async {
    try {
      await _instance.ensureTokenLoaded();
      final res = await http
        .patch(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/notifications/$notificationId/read'),
               headers: {..._instance._headers(), 'Content-Type': 'application/json'})
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to mark notification as read'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

  static Future<Map<String, dynamic>> markAllNotificationsAsRead() async {
    try {
      await _instance.ensureTokenLoaded();
      final res = await http
        .patch(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/notifications/read-all'),
               headers: {..._instance._headers(), 'Content-Type': 'application/json'})
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to mark all notifications as read'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

  static Future<Map<String, dynamic>> deleteNotification(String notificationId) async {
    try {
      await _instance.ensureTokenLoaded();
      final res = await http
        .delete(Uri.parse('${_instance._effectiveBaseUrl}/api/vendor/notifications/$notificationId'),
                headers: _instance._headers())
            .timeout(const Duration(seconds: 10));
      
      final data = jsonDecode(res.body) as Map<String, dynamic>;
      
      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to delete notification'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: ${e.toString()}'};
    }
  }

}
