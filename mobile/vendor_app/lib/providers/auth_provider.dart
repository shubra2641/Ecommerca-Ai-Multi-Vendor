import 'package:flutter/material.dart';
import '../services/api_client.dart';

class AuthProvider extends ChangeNotifier {
  final ApiClient _client = ApiClient();
  String? _token;
  bool _loading = false;

  AuthProvider() {
    // load token from storage if present
    _init();
  }

  Future<void> _init() async {
  final t = await _client.readTokenFromStorage();
    if (t != null) {
      _token = t;
      _client.setToken(_token);
      notifyListeners();
    }
  }

  bool get isLoading => _loading;
  String? get token => _token;

  Future<bool> login(String email, String password) async {
    _loading = true;
    notifyListeners();
    final res = await _client.login(email, password);
    _loading = false;
    if (res != null && res['token'] != null) {
  _token = res['token'];
  _client.setToken(_token);
  if (_token != null) {
     await _client.writeTokenToStorage(_token!);
   }
  notifyListeners();
      return true;
    }
    // if res contains message, throw or return false; keep simple: return false
    notifyListeners();
    return false;
  }

  Future<void> signOut() async {
    _token = null;
    _client.clearToken();
    notifyListeners();
  }
}
