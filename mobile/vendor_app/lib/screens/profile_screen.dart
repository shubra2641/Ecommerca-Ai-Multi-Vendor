import 'package:flutter/material.dart';
import '../l10n/app_localizations.dart';
import '../services/api_client.dart';
import '../widgets/english_text_field.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _whatsappCtrl = TextEditingController();
  final _passwordCtrl = TextEditingController();
  final _passwordConfirmCtrl = TextEditingController();

  bool _loading = false;
  Map<String, dynamic>? _profile;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    setState(() => _loading = true);
    // call backend (placeholder: ApiClient.getProfile not implemented in client yet)
    try {
      final client = ApiClient();
      // attempt to fetch /api/vendor/profile
      final res = await client.getProfile();
      // Support multiple possible response shapes from backend:
      // 1) { data: { name, email, ... } }
      // 2) { name, email, ... }
      // 3) { profile: { ... } }
      Map<String, dynamic>? payload;
      if (res != null) {
        if (res['data'] is Map) {
          payload = Map<String, dynamic>.from(res['data']);
        } else if (res['profile'] is Map) {
          payload = Map<String, dynamic>.from(res['profile']);
  } else if (res.containsKey('name') || res.containsKey('email') || res.containsKey('balance')) {
          payload = Map<String, dynamic>.from(res);
        }
      }

      if (payload != null) {
        _profile = payload;
        _nameCtrl.text = _profile!['name']?.toString() ?? '';
        _emailCtrl.text = _profile!['email']?.toString() ?? '';
        _phoneCtrl.text = _profile!['phone']?.toString() ?? '';
        _whatsappCtrl.text = _profile!['whatsapp']?.toString() ?? '';
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    try {
      final payload = {
        'name': _nameCtrl.text.trim(),
        'email': _emailCtrl.text.trim(),
        'phone': _phoneCtrl.text.trim(),
        'whatsapp': _whatsappCtrl.text.trim(),
      };
      if (_passwordCtrl.text.isNotEmpty) {
        payload['password'] = _passwordCtrl.text;
        payload['password_confirmation'] = _passwordConfirmCtrl.text;
      }
      final client = ApiClient();
      final ok = await client.updateProfile(payload);
      if (ok) {
        if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppLocalizations.of(context)!.profileUpdated)));
        await _loadProfile();
      } else {
        if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppLocalizations.of(context)!.failedToUpdateProfile), backgroundColor: Colors.red));
      }
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red));
    }
    if (mounted) setState(() => _loading = false);
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _phoneCtrl.dispose();
    _passwordCtrl.dispose();
    _passwordConfirmCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(AppLocalizations.of(context)!.profile)),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : Padding(
              padding: const EdgeInsets.all(16),
              child: SingleChildScrollView(
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Card(
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        elevation: 1,
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(AppLocalizations.of(context)!.personalInformation, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                              const SizedBox(height: 12),
                              EnglishTextField(
                                controller: _nameCtrl,
                                decoration: InputDecoration(labelText: AppLocalizations.of(context)!.name),
                              ),
                              const SizedBox(height: 12),
                              EnglishTextField(
                                controller: _emailCtrl,
                                decoration: InputDecoration(labelText: AppLocalizations.of(context)!.email),
                                keyboardType: TextInputType.emailAddress,
                              ),
                              const SizedBox(height: 12),
                              EnglishTextField(
                                controller: _phoneCtrl,
                                decoration: InputDecoration(labelText: AppLocalizations.of(context)!.phone),
                                keyboardType: TextInputType.phone,
                              ),
                              const SizedBox(height: 12),
                              EnglishTextField(
                                controller: _whatsappCtrl,
                                decoration: InputDecoration(labelText: AppLocalizations.of(context)!.whatsapp),
                                keyboardType: TextInputType.phone,
                              ),
                              const SizedBox(height: 12),
                              EnglishTextField(
                                controller: _passwordCtrl,
                                decoration: InputDecoration(labelText: AppLocalizations.of(context)!.newPassword, helperText: AppLocalizations.of(context)!.leaveBlankToKeepCurrent),
                                obscureText: true,
                              ),
                              const SizedBox(height: 8),
                              EnglishTextField(
                                controller: _passwordConfirmCtrl,
                                decoration: InputDecoration(labelText: AppLocalizations.of(context)!.confirmPassword),
                                obscureText: true,
                              ),
                              const SizedBox(height: 16),
                              Row(
                                children: [
                                  Expanded(child: ElevatedButton(onPressed: _saveProfile, child: Text(AppLocalizations.of(context)!.save))),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Card(
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        elevation: 1,
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(AppLocalizations.of(context)!.account, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                              const SizedBox(height: 12),
                              Row(
                                children: [
                                  Expanded(child: Text(AppLocalizations.of(context)!.balance, style: const TextStyle(fontWeight: FontWeight.w600))),
                                  Text(_profile != null ? ('\$${_profile!['balance']?.toString() ?? '0'}') : '\$0', style: const TextStyle(fontWeight: FontWeight.w700)),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
    );
  }
}
