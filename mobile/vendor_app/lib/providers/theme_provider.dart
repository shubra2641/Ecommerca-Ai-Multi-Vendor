import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_client.dart';

class ThemeProvider extends ChangeNotifier {
  static const String _themeKey = 'theme_mode';
  static const String _languageKey = 'language_code';
  static const String _writingLanguageKey = 'writing_language_code';
  
  ThemeMode _themeMode = ThemeMode.light;
  Locale _locale = const Locale('en'); // Default to English
  // Keep a separate writing language preference (controls writing direction/keyboard preference intent).
  // Note: mobile platforms control the actual keyboard language; this setting helps the app
  // keep English as the default writing language even when the UI locale is Arabic.
  String _writingLanguage = 'en';
  
  ThemeMode get themeMode => _themeMode;
  Locale get locale => _locale;
  
  bool get isDarkMode => _themeMode == ThemeMode.dark;
  
  ThemeProvider() {
    _loadPreferences();
  }
  
  Future<void> _loadPreferences() async {
    final prefs = await SharedPreferences.getInstance();
    
    // Load theme preference
    final themeModeString = prefs.getString(_themeKey) ?? 'light';
    _themeMode = themeModeString == 'dark' ? ThemeMode.dark : ThemeMode.light;
    
    // Load language preference
    String? languageCode = prefs.getString(_languageKey);
    if (languageCode == null || languageCode.isEmpty) {
      // Default to device/system locale if available
      try {
        final deviceLocale = WidgetsBinding.instance.window.locale;
        languageCode = deviceLocale.languageCode;
      } catch (_) {
        languageCode = 'en';
      }
    }
    _locale = Locale(languageCode);

  // Load writing language preference (default to English)
  _writingLanguage = prefs.getString(_writingLanguageKey) ?? 'en';
    
    // Update API client locale on app start
    ApiClient().setLocale(languageCode);
    
    notifyListeners();
  }
  
  Future<void> toggleTheme() async {
    _themeMode = _themeMode == ThemeMode.light ? ThemeMode.dark : ThemeMode.light;
    
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_themeKey, _themeMode == ThemeMode.dark ? 'dark' : 'light');
    
    notifyListeners();
  }
  
  Future<void> setTheme(ThemeMode themeMode) async {
    if (_themeMode == themeMode) return;
    
    _themeMode = themeMode;
    
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_themeKey, themeMode == ThemeMode.dark ? 'dark' : 'light');
    
    notifyListeners();
  }
  
  Future<void> setLocale(Locale locale) async {
    if (_locale == locale) return;
    
    _locale = locale;
    
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_languageKey, locale.languageCode);
    
    // Update API client locale
    ApiClient().setLocale(locale.languageCode);
    
    notifyListeners();
  }

  /// Writing language controls the default writing direction / hint for input fields.
  /// This app keeps English as the default writing language unless changed explicitly.
  Future<void> setWritingLanguage(String languageCode) async {
    if (_writingLanguage == languageCode) return;
    _writingLanguage = languageCode;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_writingLanguageKey, languageCode);
    notifyListeners();
  }

  /// Returns the language code used for writing (defaults to 'en').
  String get writingLanguage => _writingLanguage;
  
  List<Locale> get supportedLocales => const [
    Locale('en'),
    Locale('ar'),
  ];
  
  String getLanguageName(String languageCode) {
    switch (languageCode) {
      case 'en':
        return 'English';
      case 'ar':
        return 'العربية';
      default:
        return 'English';
    }
  }
}