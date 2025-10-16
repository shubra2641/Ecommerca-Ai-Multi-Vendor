// ignore_for_file: use_build_context_synchronously, deprecated_member_use
import 'package:flutter/material.dart';
import '../l10n/app_localizations.dart';
import '../services/api_client.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _email = TextEditingController();
  final _password = TextEditingController();
  bool _loading = false;

  Future<void> _login() async {
    setState(() => _loading = true);
    final client = ApiClient();
    final res = await client.login(_email.text.trim(), _password.text.trim());
    setState(() => _loading = false);
    if (res != null && res['token'] != null) {
      Navigator.of(context).pushReplacementNamed('/shell');
    } else {
      final msg = (res != null && res['message'] != null) ? res['message'] : AppLocalizations.of(context)!.loginFailed;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF0F172A), Color(0xFF0B2545)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 420),
            child: Card(
              elevation: 12,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              margin: const EdgeInsets.symmetric(horizontal: 20),
              child: Padding(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        const FlutterLogo(size: 48),
                        const SizedBox(width: 12),
                        Text(AppLocalizations.of(context)!.vendorAdmin, style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w600)),
                      ],
                    ),
                    const SizedBox(height: 18),
                    TextField(controller: _email, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.email)), 
                    const SizedBox(height: 12),
                    TextField(controller: _password, obscureText: true, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.password)),
                    const SizedBox(height: 20),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _loading ? null : _login,
                        style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 14)),
                        child: _loading
                            ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                            : Text(AppLocalizations.of(context)!.signIn, style: const TextStyle(fontSize: 16)),
                      ),
                    ),
                    const SizedBox(height: 8),
                    TextButton(onPressed: () {}, child: Text(AppLocalizations.of(context)!.forgotPassword)),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
