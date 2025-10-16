import 'package:flutter/material.dart';
import '../services/api_client.dart';
import '../widgets/english_text_field.dart';
import '../l10n/app_localizations.dart';

class WithdrawRequestScreen extends StatefulWidget {
  const WithdrawRequestScreen({super.key});

  @override
  State<WithdrawRequestScreen> createState() => _WithdrawRequestScreenState();
}

class _WithdrawRequestScreenState extends State<WithdrawRequestScreen> {
  final _formKey = GlobalKey<FormState>();
  final _amountController = TextEditingController();
  final _notesController = TextEditingController();
  final _bankAccountController = TextEditingController();
  final _paypalEmailController = TextEditingController();
  
  bool _isLoading = false;
  String _selectedMethod = 'bank-transfer';
  String _currency = 'USD';
  double _availableBalance = 0.0;
  double _minimumAmount = 10.0;
  List<Map<String, dynamic>> _paymentMethods = [];
  bool _commissionEnabled = false;
  double _commissionRate = 0.0;
  
  @override
  void initState() {
    super.initState();
    _loadWithdrawalSettings();
  }
  
  Future<void> _loadWithdrawalSettings() async {
    setState(() { _isLoading = true; });
    
    try {
      // Get profile data to fetch balance
      final profileResult = await ApiClient().getProfile();
      if (profileResult != null) {
        _availableBalance = double.tryParse(profileResult['balance']?.toString() ?? '0') ?? 0.0;
        _currency = profileResult['currency'] ?? 'USD';
      }
      
      // Get system settings for withdrawal configuration
      final settingsResult = await ApiClient().getSystemSettings();
      if (settingsResult != null && settingsResult['success'] == true) {
        final settings = settingsResult['data'] as Map<String, dynamic>? ?? settingsResult;
        
        // Get withdrawal gateways
        final gateways = settings['withdrawal_gateways'] as List<dynamic>?;
        if (gateways != null && gateways.isNotEmpty) {
          _paymentMethods = gateways.map((gateway) {
            return {
              'slug': gateway.toString(),
              'label': gateway.toString(),
            };
          }).toList().cast<Map<String, dynamic>>();
        } else {
          _paymentMethods = [];
        }
        
        // Get minimum withdrawal amount
        final minAmount = settings['min_withdrawal_amount'];
        if (minAmount != null) {
          _minimumAmount = double.tryParse(minAmount.toString()) ?? 10.0;
        }
        
        // Get commission settings
        _commissionEnabled = settings['withdrawal_commission_enabled'] == true || 
                           settings['withdrawal_commission_enabled']?.toString() == '1';
        _commissionRate = double.tryParse(settings['withdrawal_commission_rate']?.toString() ?? '0') ?? 0.0;
      }
      
      setState(() {
        if (_paymentMethods.isNotEmpty) {
          _selectedMethod = _paymentMethods.first['slug'];
        }
      });
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('فشل في تحميل الإعدادات: $e')),
        );
      }
    } finally {
      setState(() { _isLoading = false; });
    }
  }
  
  Future<void> _submitWithdrawal() async {
    if (!_formKey.currentState!.validate()) return;
    
    final amount = double.tryParse(_amountController.text.trim());
    if (amount == null || amount <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(AppLocalizations.of(context)!.pleaseEnterValidAmount)),
      );
      return;
    }
    
    if (amount > _availableBalance) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(AppLocalizations.of(context)!.amountExceedsBalance)),
      );
      return;
    }
    
    if (amount < _minimumAmount) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Minimum withdrawal amount is $_minimumAmount $_currency')),
      );
      return;
    }
    
    setState(() { _isLoading = true; });
    
    try {
      final result = await ApiClient.requestWithdrawal(
        amount: amount,
        method: _selectedMethod,
        bankAccount: _selectedMethod == 'bank-transfer' ? _bankAccountController.text.trim() : null,
        paypalEmail: _selectedMethod == 'paypal' ? _paypalEmailController.text.trim() : null,
        notes: _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
      );
      
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(AppLocalizations.of(context)!.withdrawalRequestSubmitted),
              backgroundColor: Colors.green,
            ),
          );
          Navigator.of(context).pop(true);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? AppLocalizations.of(context)!.failedToSubmitWithdrawal),
              backgroundColor: Colors.red,
            ),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: $e'),
            backgroundColor: Colors.red,
          ),
        );
      }
    } finally {
      setState(() { _isLoading = false; });
    }
  }
  
  @override
  void dispose() {
    _amountController.dispose();
    _notesController.dispose();
    _bankAccountController.dispose();
    _paypalEmailController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    
    return Scaffold(
      appBar: AppBar(
        title: const Text('طلب سحب'),
        backgroundColor: Colors.deepPurple,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Balance Overview Card
                    Card(
                      elevation: 4,
                      child: Padding(
                        padding: const EdgeInsets.all(16.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'الرصيد المتاح',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w500,
                                color: Colors.grey,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              '${_availableBalance.toStringAsFixed(2)} $_currency',
                              style: const TextStyle(
                                fontSize: 24,
                                fontWeight: FontWeight.bold,
                                color: Colors.green,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'الحد الأدنى للسحب: ${_minimumAmount.toStringAsFixed(2)} $_currency',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey,
                                  ),
                                ),
                                if (_commissionEnabled) ...[  
                                  const SizedBox(height: 4),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                    decoration: BoxDecoration(
                                      color: Colors.orange.shade100,
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                    child: Text(
                                      'العمولة مفعلة: ${_commissionRate.toStringAsFixed(2)}%',
                                      style: TextStyle(
                                        fontSize: 12,
                                        color: Colors.orange.shade800,
                                        fontWeight: FontWeight.w500,
                                      ),
                                    ),
                                  ),
                                ],
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),
                    
                    // Amount Input
                    EnglishTextField(
                      controller: _amountController,
                      keyboardType: const TextInputType.numberWithOptions(decimal: true),
                      decoration: InputDecoration(
                        labelText: 'مبلغ السحب',
                        hintText: 'أدخل المبلغ المراد سحبه',
                        suffixText: _currency,
                        border: const OutlineInputBorder(),
                        prefixIcon: const Icon(Icons.attach_money),
                      ),
                    ),
                    const SizedBox(height: 16),
                    
                    // Payment Method Selection
                    const Text(
                      'بوابة الدفع',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    const SizedBox(height: 8),
                    _paymentMethods.isEmpty
                        ? Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              border: Border.all(color: Colors.grey),
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: const Text(
                              'لا يوجد بوابات دفع متاحة',
                              style: TextStyle(color: Colors.grey),
                            ),
                          )
                        : DropdownButtonFormField<String>(
                            value: _selectedMethod,
                            decoration: const InputDecoration(
                              border: OutlineInputBorder(),
                              prefixIcon: Icon(Icons.payment),
                            ),
                            items: _paymentMethods.map((method) {
                              return DropdownMenuItem<String>(
                                value: method['slug'],
                                child: Text(method['label']),
                              );
                            }).toList(),
                            onChanged: (value) {
                              setState(() {
                                _selectedMethod = value!;
                              });
                            },
                          ),
                    const SizedBox(height: 16),
                    
                    // Payment Method Specific Fields
                    if (_selectedMethod == 'bank-transfer') ...
                      [
                        EnglishTextField(
                          controller: _bankAccountController,
                          decoration: InputDecoration(
                            labelText: localizations.bankAccountDetails,
                            hintText: localizations.enterBankAccountInfo,
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.account_balance),
                          ),
                        ),
                      ]
                    else if (_selectedMethod == 'paypal') ...
                      [
                        EnglishTextField(
                          controller: _paypalEmailController,
                          keyboardType: TextInputType.emailAddress,
                          decoration: InputDecoration(
                            labelText: localizations.paypalEmail,
                            hintText: localizations.enterPaypalEmail,
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.email),
                          ),
                        ),
                      ],
                    const SizedBox(height: 16),
                    
                    // Notes (Optional)
                    EnglishTextField(
                      controller: _notesController,
                      decoration: InputDecoration(
                        labelText: localizations.notesOptional,
                        hintText: localizations.addAdditionalNotes,
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.note),
                      ),
                    ),
                    const SizedBox(height: 24),
                    
                    // Submit Button
                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        onPressed: (_isLoading || _paymentMethods.isEmpty) ? null : _submitWithdrawal,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.deepPurple,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: _isLoading
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                ),
                              )
                            : const Text(
                                'إرسال طلب السحب',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    
                    // Cancel Button
                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: OutlinedButton(
                        onPressed: _isLoading ? null : () => Navigator.of(context).pop(),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: Colors.grey[600],
                          side: BorderSide(color: Colors.grey[400]!),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: const Text(
                          'إلغاء',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
