// ignore_for_file: deprecated_member_use, use_build_context_synchronously, unnecessary_null_comparison
import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:image_picker/image_picker.dart';
import '../l10n/app_localizations.dart';
import '../services/api_client.dart';
import '../widgets/english_text_field.dart';

class ProductEditScreen extends StatefulWidget {
  final Map<String, dynamic>? product;
  const ProductEditScreen({super.key, this.product});

  @override
  State<ProductEditScreen> createState() => _ProductEditScreenState();
}

class _ProductEditScreenState extends State<ProductEditScreen> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _slug = TextEditingController();
  final _sku = TextEditingController();
  final _price = TextEditingController();
  final _stock = TextEditingController();
  final _image = TextEditingController();
  final _short = TextEditingController();
  final _description = TextEditingController();
  final _gallery = TextEditingController();
  final _client = ApiClient();
  final ImagePicker _picker = ImagePicker();
  String? _pickedImageUrl;

  String _type = 'simple';
  String _physicalType = 'physical';
  bool _manageStock = false;
  bool _saving = false;

  List<dynamic> _categories = [];
  int? _selectedCategory;
  List<dynamic> _tags = [];
  List<int> _selectedTags = [];

  // Variations / attributes
  List<Map<String, dynamic>> _variations = [];
  List<dynamic> _attributes = [];
  bool _loadingAttributes = true;
  List<String> _usedAttributes = [];
  Map<String, dynamic>? _currentProduct;

  // Additional fields
  final _salePrice = TextEditingController();
  final _saleStart = TextEditingController();
  final _saleEnd = TextEditingController();
  DateTime? _saleStartDate, _saleEndDate;
  final _seoTitle = TextEditingController();
  final _seoDesc = TextEditingController();
  final _seoKeywords = TextEditingController();
  final _refundDays = TextEditingController();
  final _reservedQty = TextEditingController();
  
  // Dimensions and weight
  final _weight = TextEditingController();
  final _length = TextEditingController();
  final _width = TextEditingController();
  final _height = TextEditingController();
  
  // Digital product fields
  final _downloadUrl = TextEditingController();
  final _downloadFile = TextEditingController();
  final _serials = TextEditingController();
  
  bool _isFeatured = false, _isBestSeller = false, _backorder = false, _physical = true, _hasSerials = false;

  // Multi-language support
  List<Map<String, dynamic>> _languages = [];
  String _defaultLang = 'en';
  final Map<String, Map<String, TextEditingController>> _langControllers = {};
  String _currentLangTab = 'en';

  @override
  void initState() {
    super.initState();
    if (widget.product != null) {
      final p = widget.product!;
      _name.text = p['name'] ?? '';
      _slug.text = p['slug'] ?? '';
      _sku.text = p['sku'] ?? '';
      _price.text = (p['price'] ?? '').toString();
      _stock.text = (p['stock_qty'] ?? '').toString();
      _image.text = p['main_image'] ?? '';
      _short.text = p['short_description'] ?? '';
      _description.text = p['description'] ?? '';
      _gallery.text = (p['gallery'] is List) ? (p['gallery'] as List).join(',') : (p['gallery'] ?? '');
      _selectedTags = (p['tag_ids'] is List) ? List<int>.from(p['tag_ids']) : [];
      _selectedCategory = p['product_category_id'];
      _salePrice.text = (p['sale_price'] ?? '').toString();
      _saleStart.text = p['sale_start'] ?? '';
      _saleEnd.text = p['sale_end'] ?? '';
      _seoTitle.text = p['seo_title'] ?? '';
      _seoDesc.text = p['seo_description'] ?? '';
      _seoKeywords.text = p['seo_keywords'] ?? '';
      _refundDays.text = (p['refund_days'] ?? '').toString();
      _reservedQty.text = (p['reserved_qty'] ?? '').toString();
      _weight.text = (p['weight'] ?? '').toString();
      _length.text = (p['length'] ?? '').toString();
      _width.text = (p['width'] ?? '').toString();
      _height.text = (p['height'] ?? '').toString();
      _downloadUrl.text = p['download_url'] ?? '';
      _downloadFile.text = p['download_file'] ?? '';
      _serials.text = p['serials'] ?? '';
      _type = p['type'] ?? 'simple';
      _physicalType = p['physical_type'] ?? 'physical';
      _manageStock = p['manage_stock'] == true;
      _isFeatured = p['is_featured'] == true;
      _isBestSeller = p['is_best_seller'] == true;
      _backorder = p['backorder'] == true;
      _physical = p['physical_type'] != 'digital';
      _hasSerials = p['has_serials'] == true;
      if (p['variations'] is List) {
        _variations = List<Map<String, dynamic>>.from(p['variations']);
      }
    }
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _initPage();
    });
  }

  Future<void> _initPage() async {
    try {
      final stored = await _client.readTokenFromStorage();
    final storedStr = stored?.toString();
    if (storedStr == null || storedStr.isEmpty) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Not authenticated - please log in')));
        }
        return;
      }
      await Future.wait([
        _loadCategories(),
        _loadTags(),
        _loadAttributes(),
        _loadLanguages(),
      ]);
      if (widget.product != null && widget.product?['id'] != null) {
        await _loadProductDetail(widget.product?['id']);
      }
    } catch (e) {
      if (kDebugMode) debugPrint('Init error: $e');
    }
  }

  Future<void> _loadCategories() async {
    final res = await _client.getProductCategories();
    if (!mounted) return;
    if (res != null && res['data'] is List) {
      setState(() => _categories = res['data']);
    }
  }

  Future<void> _loadTags() async {
    final res = await _client.getProductTags();
    if (!mounted) return;
    if (res != null && res['data'] is List) {
      setState(() => _tags = res['data']);
    }
  }

  Future<void> _loadAttributes() async {
    final res = await _client.getProductAttributes();
    if (!mounted) return;
    if (res != null && res['data'] is List) {
      setState(() {
        _attributes = res['data'];
        _loadingAttributes = false;
      });
    } else {
      setState(() => _loadingAttributes = false);
    }
  }

  Future<void> _loadLanguages() async {
    final res = await _client.getLanguages();
    if (!mounted) return;
    if (res != null && res['data'] is List) {
      setState(() {
        _languages = List<Map<String, dynamic>>.from(res['data']);
        _defaultLang = _languages.firstWhere((l) => l['is_default'] == true, orElse: () => _languages.first)['code'];
        _currentLangTab = _defaultLang;
        for (final f in ['name', 'short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords']) {
          _langControllers[f] = {};
          for (final lang in _languages) {
            final code = lang['code'];
            _langControllers[f]?[code] = TextEditingController();
          }
        }
        // Populate with existing data including translations
        if (widget.product != null) {
          final translations = widget.product!['translations'] as Map<String, dynamic>?;
          
          for (final field in ['name', 'short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords']) {
            for (final lang in _languages) {
              final code = lang['code'];
              final fieldTranslations = translations?[field] as Map<String, dynamic>?;
              
              if (fieldTranslations != null && fieldTranslations[code] != null) {
                _langControllers[field]?[code]?.text = fieldTranslations[code].toString();
              } else if (code == _defaultLang) {
                // Fallback to main field value for default language
                switch (field) {
                  case 'name':
                    _langControllers[field]?[code]?.text = _name.text;
                    break;
                  case 'short_description':
                    _langControllers[field]?[code]?.text = _short.text;
                    break;
                  case 'description':
                    _langControllers[field]?[code]?.text = _description.text;
                    break;
                  case 'seo_title':
                    _langControllers[field]?[code]?.text = _seoTitle.text;
                    break;
                  case 'seo_description':
                    _langControllers[field]?[code]?.text = _seoDesc.text;
                    break;
                  case 'seo_keywords':
                    _langControllers[field]?[code]?.text = _seoKeywords.text;
                    break;
                }
              }
            }
          }
        }
      });
    }
  }

  Future<void> _loadProductDetail(int id) async {
    final res = await _client.getProductDetail(id);
    if (!mounted) return;
    if (res != null && res['id'] != null) {
      setState(() {
        _name.text = res['name'] ?? _name.text;
        _slug.text = res['slug'] ?? _slug.text;
        _price.text = (res['price'] ?? _price.text).toString();
        _stock.text = (res['stock_qty'] ?? _stock.text).toString();
        _image.text = res['main_image'] ?? _image.text;
        _sku.text = res['sku'] ?? _sku.text;
        _short.text = res['short_description'] ?? _short.text;
        _description.text = res['description'] ?? _description.text;
        _gallery.text = (res['gallery'] is List) ? (res['gallery'] as List).join(',') : (res['gallery'] ?? _gallery.text);
        _selectedTags = (res['tag_ids'] is List) ? List<int>.from(res['tag_ids']) : _selectedTags;
        _selectedCategory = res['product_category_id'] ?? _selectedCategory;
        if (res['variations'] is List) {
          _variations = List<Map<String, dynamic>>.from(res['variations']);
        }
        _salePrice.text = (res['sale_price'] ?? _salePrice.text).toString();
        // sale_start/sale_end might come ISO8601 - trim date part for input
        final saleStartRaw = res['sale_start'];
        final saleEndRaw = res['sale_end'];
        _saleStartDate = (saleStartRaw != null) ? DateTime.tryParse(saleStartRaw) : _saleStartDate;
        _saleEndDate = (saleEndRaw != null) ? DateTime.tryParse(saleEndRaw) : _saleEndDate;
        _saleStart.text = _saleStartDate != null ? _saleStartDate!.toIso8601String().substring(0,10) : _saleStart.text;
        _saleEnd.text = _saleEndDate != null ? _saleEndDate!.toIso8601String().substring(0,10) : _saleEnd.text;
        _seoTitle.text = res['seo_title'] ?? _seoTitle.text;
        _seoDesc.text = res['seo_description'] ?? _seoDesc.text;
        _seoKeywords.text = res['seo_keywords'] ?? _seoKeywords.text;
        _refundDays.text = (res['refund_days'] ?? _refundDays.text).toString();
        _reservedQty.text = (res['reserved_qty'] ?? _reservedQty.text).toString();
        _weight.text = (res['weight'] ?? _weight.text).toString();
        _length.text = (res['length'] ?? _length.text).toString();
        _width.text = (res['width'] ?? _width.text).toString();
        _height.text = (res['height'] ?? _height.text).toString();
        // booleans (accept both naming variants)
        _isFeatured = (res['is_featured'] ?? res['featured'] ?? _isFeatured) == true;
        _isBestSeller = (res['is_best_seller'] ?? res['best_seller'] ?? _isBestSeller) == true;
        _backorder = (res['backorder'] ?? _backorder) == true;
        _manageStock = (res['manage_stock'] ?? _manageStock) == true;
        _physicalType = res['physical_type'] ?? _physicalType;
        _physical = _physicalType != 'digital';
        _hasSerials = (res['has_serials'] ?? _hasSerials) == true;
        _downloadUrl.text = res['download_url'] ?? _downloadUrl.text;
        _downloadFile.text = res['download_file'] ?? _downloadFile.text;
        if (res['used_attributes'] is List) {
          _usedAttributes = List<String>.from(res['used_attributes'].map((e) => e.toString()));
        }
        // Populate translation controllers if structure present under 'translations'
        if (res['translations'] is Map<String, dynamic> && _languages.isNotEmpty) {
          final tr = res['translations'] as Map<String, dynamic>;
          for (final field in ['name','short_description','description','seo_title','seo_description','seo_keywords']) {
            final fieldMap = tr[field];
            if (fieldMap is Map<String, dynamic>) {
              fieldMap.forEach((lang, value) {
                if (_langControllers[field]?[lang] != null) {
                  _langControllers[field]![lang]!.text = value?.toString() ?? '';
                }
              });
            }
          }
        }
      });
    }
  }

  Future<void> _save() async {
    if (_formKey.currentState?.validate() != true) return;
    setState(() => _saving = true);
    
    // Collect multi-language data
    final Map<String, dynamic> translations = {};
    for (final field in ['name', 'short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords']) {
      translations[field] = {};
      for (final lang in _languages) {
        final code = lang['code'];
        final value = _langControllers[field]?[code]?.text ?? '';
        if (value.isNotEmpty) {
          translations[field][code] = value;
        }
      }
    }
    
    final data = {
      'name': _langControllers['name']?[_defaultLang]?.text ?? _name.text,
      'slug': _slug.text,
      'sku': _sku.text,
      'price': double.tryParse(_price.text) ?? 0,
      'stock_qty': int.tryParse(_stock.text) ?? 0,
      'reserved_qty': int.tryParse(_reservedQty.text),
      'main_image': _image.text,
      'short_description': _langControllers['short_description']?[_defaultLang]?.text ?? _short.text,
      'description': _langControllers['description']?[_defaultLang]?.text ?? _description.text,
      'gallery': _gallery.text.split(',').map((s) => s.trim()).where((s) => s.isNotEmpty).toList(),
      'product_category_id': _selectedCategory,
      'tag_ids': _selectedTags,
      'type': _type,
      'physical_type': _physicalType,
      'manage_stock': _manageStock,
      'variations': _variations,
      'sale_price': double.tryParse(_salePrice.text),
      'sale_start': _saleStart.text.isNotEmpty ? _saleStart.text : null,
      'sale_end': _saleEnd.text.isNotEmpty ? _saleEnd.text : null,
      'seo_title': _langControllers['seo_title']?[_defaultLang]?.text ?? _seoTitle.text,
      'seo_description': _langControllers['seo_description']?[_defaultLang]?.text ?? _seoDesc.text,
      'seo_keywords': _langControllers['seo_keywords']?[_defaultLang]?.text ?? _seoKeywords.text,
      'refund_days': int.tryParse(_refundDays.text),
      'weight': double.tryParse(_weight.text),
      'length': double.tryParse(_length.text),
      'width': double.tryParse(_width.text),
      'height': double.tryParse(_height.text),
      'download_url': _downloadUrl.text,
      'download_file': _downloadFile.text,
      'serials': _serials.text,
      'featured': _isFeatured,
      'best_seller': _isBestSeller,
      'backorder': _backorder,
      'physical': _physical,
      'has_serials': _hasSerials,
      'used_attributes': _usedAttributes,
      'translations': translations,
    };

    bool ok;
    if (widget.product != null && widget.product?['id'] != null) {
      ok = await _client.updateProduct(widget.product?['id'], data);
    } else {
      // For new products, use createProductAndReturnData to get the product data
      final result = await _client.createProductAndReturnData(data);
      ok = result != null;
      if (ok && result['product'] != null) {
        // Update _currentProduct so the Add Variation button becomes enabled
        setState(() {
          _currentProduct = result['product'];
        });
      }
    }
    
    setState(() => _saving = false);
    if (ok) {
      Navigator.of(context).pop(true);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppLocalizations.of(context)!.failedToSaveProduct)));
    }
  }

  Widget _sectionCard(String title, IconData icon, Widget child) {
                    return Container(
      margin: const EdgeInsets.symmetric(vertical: 12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Theme.of(context).colorScheme.surface,
            Theme.of(context).colorScheme.surface.withOpacity(0.8),
          ],
        ),
        boxShadow: [
          BoxShadow(
            color: Theme.of(context).colorScheme.primary.withOpacity(0.1),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
        border: Border.all(
          color: Theme.of(context).colorScheme.outline.withOpacity(0.1),
          width: 1,
        ),
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [
                Colors.white.withOpacity(0.1),
                Colors.transparent,
              ],
            ),
          ),
          child: Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    gradient: LinearGradient(
                      colors: [
                        Theme.of(context).colorScheme.primary.withOpacity(0.1),
                        Theme.of(context).colorScheme.primary.withOpacity(0.05),
                      ],
                    ),
                    border: Border.all(
                      color: Theme.of(context).colorScheme.primary.withOpacity(0.2),
                      width: 1,
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: Theme.of(context).colorScheme.primary,
                          borderRadius: BorderRadius.circular(8),
                          boxShadow: [
                            BoxShadow(
                              color: Theme.of(context).colorScheme.primary.withOpacity(0.3),
                              blurRadius: 8,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Icon(
                          icon,
                          size: 20,
                          color: Theme.of(context).colorScheme.onPrimary,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Text(
                        title,
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w700,
                          color: Theme.of(context).colorScheme.onSurface,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
                child,
              ],
            ),
          ),
        ),
      ),
    );
  }


  Widget _buildCheckboxTile(String label, bool value, IconData icon, ValueChanged<bool?> onChanged) {
    return Container(
      constraints: const BoxConstraints(minWidth: 120),
      child: Row(
        children: [
          Container(
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(8),
              color: value
                  ? Theme.of(context).colorScheme.primary.withOpacity(0.1)
                  : Colors.transparent,
            ),
            child: Checkbox(
              value: value,
              onChanged: onChanged,
              activeColor: Theme.of(context).colorScheme.primary,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(4),
              ),
            ),
          ),
          const SizedBox(width: 8),
          Icon(
            icon,
            size: 16,
            color: value
                ? Theme.of(context).colorScheme.primary
                : Theme.of(context).colorScheme.onSurface.withOpacity(0.6),
          ),
          const SizedBox(width: 4),
          Flexible(
            child: Text(
              label,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w500,
                color: value
                    ? Theme.of(context).colorScheme.primary
                    : Theme.of(context).colorScheme.onSurface,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMultiLangField({
  required String fieldKey,
    required String labelAr,
    required IconData icon,
    int maxLines = 1,
    TextInputType? keyboardType,
    String? Function(String?)? validator,
  }) {
    if (_languages.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(16),
        child: const Center(child: CircularProgressIndicator()),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Language tabs
        SizedBox(
          height: 40,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: _languages.length,
            itemBuilder: (context, index) {
              final lang = _languages[index];
              final code = lang['code'];
              final isSelected = _currentLangTab == code;
              
              return GestureDetector(
                onTap: () => setState(() => _currentLangTab = code),
                child: Container(
                  margin: const EdgeInsets.only(right: 8),
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  decoration: BoxDecoration(
                    color: isSelected 
                        ? Theme.of(context).colorScheme.primary
                        : Theme.of(context).colorScheme.surface,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: isSelected 
                          ? Theme.of(context).colorScheme.primary
                          : Theme.of(context).colorScheme.outline.withOpacity(0.3),
                    ),
                  ),
                  child: Text(
                    lang['name'] ?? code.toUpperCase(),
                    style: TextStyle(
                      color: isSelected 
                          ? Theme.of(context).colorScheme.onPrimary
                          : Theme.of(context).colorScheme.onSurface,
                      fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                      fontSize: 12,
                    ),
                  ),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 12),
        // Text field for current language
        Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
            ),
            color: Theme.of(context).colorScheme.surface,
          ),
          child: TextFormField(
            controller: _langControllers[fieldKey]?[_currentLangTab],
            maxLines: maxLines,
            keyboardType: keyboardType,
            decoration: InputDecoration(
              labelText: '$labelAr ($_currentLangTab)',
              prefixIcon: Icon(icon),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.all(16),
            ),
            validator: _currentLangTab == _defaultLang ? validator : null,
          ),
        ),
      ],
    );
  }

  Widget _miniStat(IconData icon, String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(8),
        color: Theme.of(context).colorScheme.primary.withOpacity(0.06),
        border: Border.all(color: Theme.of(context).colorScheme.primary.withOpacity(0.15)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: Theme.of(context).colorScheme.primary),
          const SizedBox(width: 4),
            Text(
              text,
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w500,
                color: Theme.of(context).colorScheme.primary,
              ),
            ),
        ],
      ),
    );
  }

  void _editVariation(Map<String, dynamic> variation) {
    final priceCtrl = TextEditingController(text: (variation['price'] ?? '').toString());
    final saleCtrl = TextEditingController(text: (variation['sale_price'] ?? '').toString());
    final stockCtrl = TextEditingController(text: (variation['stock_qty'] ?? '').toString());
    final saleStartCtrl = TextEditingController(text: (variation['sale_start'] ?? '').toString());
    final saleEndCtrl = TextEditingController(text: (variation['sale_end'] ?? '').toString());
    bool active = variation['active'] == true;

    // Ensure any existing variation attribute slugs are included even if user didn't tick them earlier
    final existingAttrSlugs = <String>{};
    final attrs = variation['attributes'];
    if (attrs is Map) {
      existingAttrSlugs.addAll(attrs.keys.map((e) => e.toString()));
    } else if (attrs is List) {
      for (final e in attrs) {
        if (e is Map) {
          for (final key in ['slug', 'attribute_slug', 'name', 'attribute']) {
            if (e[key] != null) existingAttrSlugs.add(e[key].toString());
          }
        }
      }
    }
    final slugsForSheet = {..._usedAttributes, ...existingAttrSlugs}.toList();
    final variationImage = _getVariationImage(variation);
    if (kDebugMode) {
      try {
        debugPrint('[EDIT VARIATION OPEN] id=${variation['id']} rawImage=$variationImage resolved=${_resolveImagePath(variation['image'] ?? variationImage)} attrs=${variation['attributes']} usedSlugs=$slugsForSheet');
      } catch (_) {}
    }
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) {
        return Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: StatefulBuilder(builder: (ctx, setSt) {
            return Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(AppLocalizations.of(context)!.editVariation, style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w600)),
                  const SizedBox(height: 16),
                  Row(children: [
                    Expanded(child: TextField(controller: priceCtrl, keyboardType: TextInputType.number, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.price))),
                    const SizedBox(width: 12),
                    Expanded(child: TextField(controller: saleCtrl, keyboardType: TextInputType.number, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.salePrice))),
                  ]),
                  const SizedBox(height: 12),
                  Row(children: [
                    ElevatedButton.icon(
                      onPressed: () async {
                        final path = await _pickAndUploadImage();
                        if (path != null) {
                          setSt(() { variation['image'] = path; });
                        }
                      },
                      icon: const Icon(Icons.photo_camera),
                      label: Text(AppLocalizations.of(context)!.image),
                    ),
                    const SizedBox(width: 12),
                    if (variationImage != null || variation['image'] != null)
                      GestureDetector(
                        onTap: () => _showImageViewer(_resolveImagePath(variation['image'] ?? variationImage)),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(6),
                          child: Image.network(
                            _resolveImagePath(variation['image'] ?? variationImage) ?? '',
                            width: 56,
                            height: 56,
                            fit: BoxFit.cover,
                            errorBuilder: (_, __, ___) => Container(
                              width: 56,
                              height: 56,
                              color: Theme.of(context).colorScheme.surfaceContainerHighest,
                              child: const Icon(Icons.broken_image, size: 20),
                            ),
                          ),
                        ),
                      ),
                  ]),
                  const SizedBox(height: 12),
                  TextField(controller: stockCtrl, keyboardType: TextInputType.number, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.stockQty)),
                  const SizedBox(height: 12),
                  Row(children: [
                    Expanded(child: TextField(controller: saleStartCtrl, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.saleStart))),
                    const SizedBox(width: 12),
                    Expanded(child: TextField(controller: saleEndCtrl, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.saleEnd))),
                  ]),
                  const SizedBox(height: 12),
                  const SizedBox(height: 12),
                  // Attributes selection from available attribute values
                  if (slugsForSheet.isNotEmpty) ...slugsForSheet.map((slug) {
                    final attr = _attributes.firstWhere((a) => a['slug'] == slug, orElse: () => null);
                    final options = (attr != null && attr['values'] is List) ? List<Map<String,dynamic>>.from(attr['values']) : <Map<String,dynamic>>[];
                    final stored = _getVariationAttributeStored(variation, slug);

                    // If no predefined options exist, fall back to a free text field bound directly to variation['attributes']
                    if (options.isEmpty) {
                      final controller = TextEditingController(text: stored?.toString() ?? '');
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: TextFormField(
                          controller: controller,
                          decoration: InputDecoration(labelText: slug.toUpperCase()),
                          onChanged: (val) => variation['attributes'] = (variation['attributes'] ?? {})..[slug] = val,
                        ),
                      );
                    }

                    // Build list of display values and support matching by id or value stored in variation
                    final entries = <Map<String,String>>[];
                    for (final v in options) {
                      final display = (v['value'] ?? v['name'] ?? v['label'] ?? v['text'])?.toString() ?? '';
                      final id = v['id'] != null ? v['id'].toString() : '';
                      if (display.isNotEmpty) entries.add({'key': display, 'id': id});
                    }
                    final itemValues = entries.map((e) => e['key']!).toList();

                    String? selected;
                    if (stored != null) {
                      final s = stored.toString();
                      if (itemValues.contains(s)) {
                        selected = s;
                      } else {
                        // try to match by id
                        final matched = entries.firstWhere((e) => e['id'] == s, orElse: () => {});
                        if (matched.isNotEmpty) selected = matched['key'];
                      }
                    }

                    return StatefulBuilder(builder: (ctx2, setSt2) {
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: DropdownButtonFormField<String>(
                          initialValue: selected,
                          items: entries.map((e) => DropdownMenuItem(value: e['key'], child: Text(e['key'] ?? ''))).toList(),
                          onChanged: (val) {
                            setSt2(() => selected = val);
                            if (val != null) {
                              final chosen = entries.firstWhere((e) => e['key'] == val, orElse: () => {});
                              final storeVal = (chosen['id'] ?? '').isNotEmpty ? chosen['id'] : val;
                              final currentAttrs = variation['attributes'];
                              if (currentAttrs is Map) {
                                variation['attributes'] = {...currentAttrs, slug: storeVal};
                              } else {
                                // normalize to map for update API
                                variation['attributes'] = {slug: storeVal};
                              }
                            }
                          },
                          decoration: InputDecoration(labelText: slug.toUpperCase()),
                        ),
                      );
                    });
                  }),
                  SwitchListTile(
                    contentPadding: EdgeInsets.zero,
                    value: active,
                    title: Text(AppLocalizations.of(context)!.active),
                    onChanged: (v) => setSt(() => active = v),
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton.icon(
                          icon: const Icon(Icons.save),
                          label: Text(AppLocalizations.of(context)!.save),
                          onPressed: () async {
                            final payload = {
                              'price': double.tryParse(priceCtrl.text) ?? variation['price'],
                              'sale_price': saleCtrl.text.isEmpty ? null : double.tryParse(saleCtrl.text),
                              'sale_start': saleStartCtrl.text.isNotEmpty ? saleStartCtrl.text : null,
                              'sale_end': saleEndCtrl.text.isNotEmpty ? saleEndCtrl.text : null,
                              'stock_qty': int.tryParse(stockCtrl.text) ?? variation['stock_qty'] ?? 0,
                              'active': active,
                              'attributes': variation['attributes'],
                              'image': variation['image'],
                              'manage_stock': variation['manage_stock'] ?? true,
                            };
                            final ok = await _client.updateVariation(widget.product!['id'], variation['id'], payload);
                              if (ok) {
                              if (!mounted) return; 
                              Navigator.pop(ctx);
                              setState(() {
                                variation['price'] = payload['price'];
                                variation['sale_price'] = payload['sale_price'];
                                variation['sale_start'] = payload['sale_start'];
                                variation['sale_end'] = payload['sale_end'];
                                variation['stock_qty'] = payload['stock_qty'];
                                variation['active'] = payload['active'];
                              });
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppLocalizations.of(context)!.failedToUpdateVariation)));
                            }
                          },
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: OutlinedButton.icon(
                          icon: const Icon(Icons.close),
                          label: Text(AppLocalizations.of(context)!.cancel),
                          onPressed: () => Navigator.pop(ctx),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            );
          }),
        );
      },
    );
  }

  void _confirmDeleteVariation(Map<String, dynamic> variation) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(AppLocalizations.of(context)!.deleteVariation),
        content: Text(AppLocalizations.of(context)!.confirmDeleteVariation),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: Text(AppLocalizations.of(context)!.cancel)),
          TextButton(
            onPressed: () async {
              Navigator.pop(ctx);
              final ok = await _client.deleteVariation(widget.product!['id'], variation['id']);
              if (ok) {
                if (!mounted) return;
                setState(() => _variations.removeWhere((vv) => vv['id'] == variation['id']));
              } else {
                if (!mounted) return;
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppLocalizations.of(context)!.failedToDeleteVariation)));
              }
            },
            child: Text(AppLocalizations.of(context)!.delete, style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }

  void _addVariation() async {
    // Open variation modal directly without saving product first
  final priceCtrl = TextEditingController();
  final saleCtrl = TextEditingController();
  final stockCtrl = TextEditingController(text: '0');
  final saleStartNewCtrl = TextEditingController();
  final saleEndNewCtrl = TextEditingController();
    final skuCtrl = TextEditingController();
    bool active = true;
    final Map<String, TextEditingController> attrCtrls = {};
    for (final slug in _usedAttributes) {
      attrCtrls[slug] = TextEditingController();
    }
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) {
        return Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: StatefulBuilder(builder: (ctx, setSt) {
            return SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(AppLocalizations.of(context)!.newVariation, style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w600)),
                  const SizedBox(height: 16),
                  TextField(controller: skuCtrl, decoration: const InputDecoration(labelText: 'SKU')),
                  const SizedBox(height: 12),
                  Row(children: [
                    Expanded(child: TextField(controller: priceCtrl, keyboardType: TextInputType.number, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.price))),
                    const SizedBox(width: 12),
                    Expanded(child: TextField(controller: saleCtrl, keyboardType: TextInputType.number, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.salePrice))),
                  ]),
                  const SizedBox(height: 12),
                  TextField(controller: stockCtrl, keyboardType: TextInputType.number, decoration: InputDecoration(labelText: AppLocalizations.of(context)!.stockQty)),
                  const SizedBox(height: 12),
                  if (_usedAttributes.isNotEmpty) ...[
                    Text(AppLocalizations.of(context)!.attributes, style: Theme.of(context).textTheme.titleSmall),
                    const SizedBox(height: 6),
                    ..._usedAttributes.map((slug) {
                      final attr = _attributes.firstWhere((a) => a['slug'] == slug, orElse: () => null);
                      final options = (attr != null && attr['values'] is List) ? List<Map<String,dynamic>>.from(attr['values']) : <Map<String,dynamic>>[];

                      if (options.isEmpty) {
                        // fallback to free text when no option list exists
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 8),
                          child: TextFormField(
                            controller: attrCtrls[slug],
                            decoration: InputDecoration(labelText: slug.toUpperCase()),
                          ),
                        );
                      }

                      final optionValues = options.map((v) => (v['value']?.toString() ?? '')).toList().where((s) => s.isNotEmpty).toSet().toList();
                      String? selected;
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: StatefulBuilder(builder: (ctx2, setSt2) {
                          return DropdownButtonFormField<String>(
                            initialValue: (selected != null && optionValues.contains(selected)) ? selected : null,
                            items: optionValues.map((val) => DropdownMenuItem(value: val, child: Text(val))).toList(),
                            onChanged: (val) { setSt2(() => selected = val); attrCtrls[slug]?.text = val ?? ''; },
                            decoration: InputDecoration(labelText: slug.toUpperCase()),
                          );
                        }),
                      );
                    }),
                  ],
                  const SizedBox(height: 12),
                  Row(children: [
                    ElevatedButton.icon(
                      onPressed: () async {
                        final path = await _pickAndUploadImage();
                        if (path != null) {
                          setState(() { _pickedImageUrl = path; });
                        }
                      },
                      icon: const Icon(Icons.photo_camera),
                      label: Text(AppLocalizations.of(context)!.image),
                    ),
                    const SizedBox(width: 12),
                    if (_pickedImageUrl != null)
                      GestureDetector(
                        onTap: () => _showImageViewer(_pickedImageUrl),
                        child: ClipRRect(borderRadius: BorderRadius.circular(6), child: Image.network(_resolveImagePath(_pickedImageUrl) ?? '', width: 56, height: 56, fit: BoxFit.cover, errorBuilder: (_,__,___)=> Container(width:56,height:56,color:Theme.of(context).colorScheme.surfaceContainerHighest,child: const Icon(Icons.broken_image)))),
                      ),
                  ]),
                  SwitchListTile(
                    contentPadding: EdgeInsets.zero,
                    value: active,
                    title: Text(AppLocalizations.of(context)!.active),
                    onChanged: (v) => setSt(() => active = v),
                  ),
                  const SizedBox(height: 12),
                  Row(children: [
                    Expanded(child: TextField(controller: saleStartNewCtrl, decoration: const InputDecoration(labelText: 'Sale Start (YYYY-MM-DD)'))),
                    const SizedBox(width: 12),
                    Expanded(child: TextField(controller: saleEndNewCtrl, decoration: const InputDecoration(labelText: 'Sale End (YYYY-MM-DD)'))),
                  ]),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton.icon(
                          icon: const Icon(Icons.save),
                          label: Text(AppLocalizations.of(context)!.create),
                          onPressed: () async {
                            final price = double.tryParse(priceCtrl.text);
                            if (price == null) {
                              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppLocalizations.of(context)!.priceRequired)));
                              return;
                            }
                            
                            // Get product ID from current product or widget product
                            String? productId;
                            if (_currentProduct != null) {
                              productId = _currentProduct!['id']?.toString();
                            } else if (widget.product != null) {
                              productId = widget.product!['id']?.toString();
                            }
                            
                            // If no product ID, save the product first
                            if (productId == null) {
                              // Build product data (same as in _save method)
                              final Map<String, dynamic> translations = {};
                              for (final field in ['name', 'short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords']) {
                                translations[field] = {};
                                for (final lang in _languages) {
                                  final code = lang['code'];
                                  final value = _langControllers[field]?[code]?.text ?? '';
                                  if (value.isNotEmpty) {
                                    translations[field][code] = value;
                                  }
                                }
                              }
                              
                              final data = {
                                'name': _langControllers['name']?[_defaultLang]?.text ?? _name.text,
                                'slug': _slug.text,
                                'sku': _sku.text,
                                'price': double.tryParse(_price.text) ?? 0,
                                'stock_qty': int.tryParse(_stock.text) ?? 0,
                                'reserved_qty': int.tryParse(_reservedQty.text),
                                'main_image': _image.text,
                                'short_description': _langControllers['short_description']?[_defaultLang]?.text ?? _short.text,
                                'description': _langControllers['description']?[_defaultLang]?.text ?? _description.text,
                                'gallery': _gallery.text.split(',').map((s) => s.trim()).where((s) => s.isNotEmpty).toList(),
                                'product_category_id': _selectedCategory,
                                'tag_ids': _selectedTags,
                                'type': _type,
                                'physical_type': _physicalType,
                                'manage_stock': _manageStock,
                                'variations': _variations,
                                'sale_price': double.tryParse(_salePrice.text),
                                'sale_start': _saleStart.text.isNotEmpty ? _saleStart.text : null,
                                'sale_end': _saleEnd.text.isNotEmpty ? _saleEnd.text : null,
                                'seo_title': _langControllers['seo_title']?[_defaultLang]?.text ?? _seoTitle.text,
                                'seo_description': _langControllers['seo_description']?[_defaultLang]?.text ?? _seoDesc.text,
                                'seo_keywords': _langControllers['seo_keywords']?[_defaultLang]?.text ?? _seoKeywords.text,
                                'refund_days': int.tryParse(_refundDays.text),
                                'weight': double.tryParse(_weight.text),
                                'length': double.tryParse(_length.text),
                                'width': double.tryParse(_width.text),
                                'height': double.tryParse(_height.text),
                                'download_url': _downloadUrl.text,
                                'download_file': _downloadFile.text,
                                'serials': _serials.text,
                                'featured': _isFeatured,
                                'best_seller': _isBestSeller,
                                'backorder': _backorder,
                                'physical': _physical,
                                'has_serials': _hasSerials,
                                'used_attributes': _usedAttributes,
                                'translations': translations,
                              };
                              
                              final result = await _client.createProductAndReturnData(data);
                              
                              if (result != null && result['product'] != null) {
                                setState(() {
                                  _currentProduct = result['product'];
                                });
                                productId = _currentProduct!['id']?.toString();
                              }
                              
                              if (productId == null) {
                                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Failed to save product. Please try again.')));
                                return;
                              }
                            }
                            
                            final attributes = <String,dynamic>{};
                            attrCtrls.forEach((k, c) { if (c.text.isNotEmpty) attributes[k] = c.text; });
                            final payload = {
                              'sku': skuCtrl.text.isEmpty ? null : skuCtrl.text,
                              'price': price,
                              'sale_price': saleCtrl.text.isEmpty ? null : double.tryParse(saleCtrl.text),
                              'sale_start': saleStartNewCtrl.text.isNotEmpty ? saleStartNewCtrl.text : null,
                              'sale_end': saleEndNewCtrl.text.isNotEmpty ? saleEndNewCtrl.text : null,
                              'stock_qty': int.tryParse(stockCtrl.text) ?? 0,
                              'active': active,
                              'manage_stock': true,
                              'attributes': attributes,
                              'image': _pickedImageUrl,
                            };
                            final res = await _client.createVariation(int.parse(productId!), payload);
                            if (res != null && res['ok'] == true) {
                              if (!mounted) return; 
                              Navigator.pop(ctx);
                              setState(() {
                                final newVar = res['variation'];
                                _variations.add({
                                  'id': newVar['id'] ?? newVar, // some APIs return the id directly
                                  'sku': newVar['sku'] ?? payload['sku'],
                                  'price': newVar['price'] ?? payload['price'],
                                  'sale_price': newVar['sale_price'] ?? payload['sale_price'],
                                  'sale_start': newVar['sale_start'] ?? payload['sale_start'],
                                  'sale_end': newVar['sale_end'] ?? payload['sale_end'],
                                  'stock_qty': newVar['stock_qty'] ?? payload['stock_qty'],
                                  'active': newVar['active'] ?? payload['active'],
                                  'attributes': newVar['attributes'] ?? attributes,
                                  'available_stock': newVar['available_stock'] ?? payload['stock_qty'],
                                  'image': newVar['image'] ?? _pickedImageUrl,
                                  'name': newVar['name'],
                                });
                              });
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppLocalizations.of(context)!.failedToCreateVariation)));
                            }
                          },
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: OutlinedButton.icon(
                          icon: const Icon(Icons.close),
                          label: Text(AppLocalizations.of(context)!.cancel),
                          onPressed: () => Navigator.pop(ctx),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            );
          }),
        );
      },
    );
  }

  Future<String?> _pickAndUploadImage() async {
    try {
      final x = await _picker.pickImage(source: ImageSource.gallery, maxWidth: 1200, maxHeight: 1200, imageQuality: 80);
      if (x == null) return null;
      // upload to server
      final url = await _client.uploadImage(x.path);
      return url;
    } catch (e) {
      return null;
    }
  }

  void _showImageViewer(String? url) {
    if (url == null || url.isEmpty) return;
    showDialog(
      context: context,
      builder: (ctx) {
        return Dialog(
          insetPadding: EdgeInsets.zero,
          backgroundColor: Colors.black,
          child: Stack(
            children: [
              Positioned.fill(
                child: InteractiveViewer(
                  panEnabled: true,
                  child: Image.network(_imgUrl(url) ?? '', fit: BoxFit.contain, errorBuilder: (_,__,___) => const Center(child: Icon(Icons.broken_image, size: 48, color: Colors.white)))),
              ),
              Positioned(
                top: 8,
                right: 8,
                child: IconButton(
                  icon: const Icon(Icons.close, color: Colors.white),
                  onPressed: () => Navigator.of(ctx).pop(),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  // Resolve image URL: if it's already absolute (http/https) return as-is,
  // otherwise prefix with effectiveBaseUrl so emulator can load local storage paths.
  String? _imgUrl(String? path) {
    if (path == null) return null;
    final p = path.toString();
    if (p.startsWith('http://') || p.startsWith('https://')) return p;
    // ensure leading slash
    final normalized = p.startsWith('/') ? p : '/$p';
    return _client.effectiveBaseUrl.replaceAll(RegExp(r'\/$'), '') + normalized;
  }

  // Robust resolver that accepts different shapes returned by the API for an image
  String? _resolveImagePath(dynamic imageField) {
    if (imageField == null) return null;
    try {
      if (imageField is String) return _imgUrl(imageField);
      if (imageField is Map) {
        // common keys: url, path
        final url = imageField['url'] ?? imageField['path'] ?? imageField['full'] ?? imageField['full_path'];
        if (url is String) return _imgUrl(url);
      }
      if (imageField is List && imageField.isNotEmpty) {
        final first = imageField.first;
        if (first is String) return _imgUrl(first);
        if (first is Map) return _resolveImagePath(first);
      }
      // fallback to string conversion
      return _imgUrl(imageField.toString());
    } catch (_) {
      return null;
    }
  }

  // Try common keys to extract an image value from a variation record
  dynamic _getVariationImage(Map<String, dynamic> variation) {
    final keys = ['image', 'image_url', 'main_image', 'thumbnail', 'url', 'src', 'path'];
    for (final k in keys) {
      if (variation.containsKey(k) && variation[k] != null) return variation[k];
    }
    // some APIs return images as list under 'images' or 'gallery'
    if (variation['images'] is List && (variation['images'] as List).isNotEmpty) return variation['images'][0];
    if (variation['gallery'] is List && (variation['gallery'] as List).isNotEmpty) return variation['gallery'][0];
    return null;
  }

  // Extract attribute value stored for a variation for given slug. Supports Map or List formats.
  dynamic _getVariationAttributeStored(Map<String, dynamic> variation, String slug) {
    final attrs = variation['attributes'];
    if (attrs == null) return null;
    // If map, straightforward
    if (attrs is Map) {
      if (attrs.containsKey(slug)) return attrs[slug];
      // try alternative keys
      if (attrs.containsKey(slug.toString())) return attrs[slug.toString()];
      return null;
    }
    // If list, try to find matching entry
    if (attrs is List) {
      for (final e in attrs) {
        if (e is Map) {
          // slug may be under 'slug' or 'attribute_slug' or 'name'
          final keysToCheck = ['slug', 'attribute_slug', 'name', 'attribute'];
          for (final k in keysToCheck) {
            if (e.containsKey(k) && e[k]?.toString() == slug) {
              if (e.containsKey('value')) return e['value'];
              if (e.containsKey('name')) return e['name'];
              if (e.containsKey('id')) return e['id'];
            }
          }
          // fallback: maybe the map key itself is the slug
          if (e.containsKey(slug)) return e[slug];
          // fallback to common shapes
          if (e.containsKey('value')) return e['value'];
          if (e.containsKey('name')) return e['name'];
        }
      }
    }
    return null;
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    return Scaffold(
      backgroundColor: Theme.of(context).colorScheme.surface,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.transparent,
        flexibleSpace: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                Theme.of(context).colorScheme.primary,
                Theme.of(context).colorScheme.primary.withOpacity(0.8),
              ],
            ),
          ),
        ),
        title: Text(
          widget.product != null ? AppLocalizations.of(context)!.editProduct : AppLocalizations.of(context)!.newProduct,
          style: TextStyle(
            color: Theme.of(context).colorScheme.onPrimary,
            fontSize: 20,
            fontWeight: FontWeight.w700,
            letterSpacing: 0.5,
          ),
        ),
        iconTheme: IconThemeData(
          color: Theme.of(context).colorScheme.onPrimary,
        ),
      ),
      body: Form(
        key: _formKey,
        child: LayoutBuilder(
          builder: (context, constraints) => SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: ConstrainedBox(
              constraints: BoxConstraints(minWidth: constraints.maxWidth),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _sectionCard(localizations.basicInformation, Icons.info_outline, Column(
                    children: [
                      _buildMultiLangField(
                        fieldKey: 'name',
                        labelAr: AppLocalizations.of(context)!.productName,
                        icon: Icons.shopping_bag_outlined,
                        validator: (v) => v?.isEmpty == true ? localizations.required : null,
                      ),
                      const SizedBox(height: 16),
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                          ),
                          color: Theme.of(context).colorScheme.surface,
                        ),
                        child: EnglishTextField(
                          controller: _slug,
                          decoration: InputDecoration(
                            labelText: localizations.slug,
                            prefixIcon: Icon(Icons.link_outlined),
                            border: InputBorder.none,
                            contentPadding: EdgeInsets.all(16),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      // SKU, Price and Stock - Only show for simple products
                      if (_type == 'simple') ...[
                        Row(
                          children: [
                            Expanded(
                              child: Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(
                                    color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                  ),
                                  color: Theme.of(context).colorScheme.surface,
                                ),
                                child: EnglishTextField(
                                  controller: _sku,
                                  decoration: InputDecoration(
                                    labelText: localizations.sku,
                                    prefixIcon: Icon(Icons.qr_code_outlined),
                                    border: InputBorder.none,
                                    contentPadding: EdgeInsets.all(16),
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(
                                    color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                  ),
                                  color: Theme.of(context).colorScheme.surface,
                                ),
                                child: EnglishTextField(
                                  controller: _price,
                                  keyboardType: TextInputType.number,
                                  decoration: InputDecoration(
                                    labelText: localizations.price,
                                    prefixIcon: Icon(Icons.attach_money_outlined),
                                    border: InputBorder.none,
                                    contentPadding: EdgeInsets.all(16),
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                            ),
                            color: Theme.of(context).colorScheme.surface,
                          ),
                          child: EnglishTextField(
                            controller: _stock,
                            keyboardType: TextInputType.number,
                            decoration: InputDecoration(
                              labelText: localizations.stockQuantity,
                              prefixIcon: Icon(Icons.inventory_outlined),
                              border: InputBorder.none,
                              contentPadding: EdgeInsets.all(16),
                            ),
                          ),
                        ),
                      ],
                      const SizedBox(height: 16),
                      if (_categories.isNotEmpty)
                        Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                            ),
                            color: Theme.of(context).colorScheme.surface,
                          ),
                          child: DropdownButtonFormField<int>(
                            initialValue: _selectedCategory,
                            decoration: InputDecoration(
                              labelText: localizations.category,
                              prefixIcon: Icon(Icons.category_outlined),
                              border: InputBorder.none,
                              contentPadding: EdgeInsets.all(16),
                            ),
                            items: _categories.map((c) => DropdownMenuItem<int>(
                              value: c['id'] as int,
                              child: Text(c['name'].toString()),
                            )).toList(),
                            onChanged: (v) => setState(() => _selectedCategory = v),
                          ),
                        ),
                      const SizedBox(height: 16),
                      _buildMultiLangField(
                        fieldKey: 'short_description',
                        labelAr: localizations.shortDescription,
                        icon: Icons.short_text_outlined,
                        maxLines: 3,
                      ),
                      const SizedBox(height: 16),
                      _buildMultiLangField(
                        fieldKey: 'description',
                        labelAr: localizations.detailedDescription,
                        icon: Icons.description_outlined,
                        maxLines: 5,
                      ),
                    ],
                  )),
                  
                  _sectionCard(localizations.imagesAndTags, Icons.image_outlined, Column(
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                          ),
                          color: Theme.of(context).colorScheme.surface,
                        ),
                        child: Row(
                          children: [
                            Expanded(
                              child: Padding(
                                padding: const EdgeInsets.all(8.0),
                                child: Row(
                                  children: [
                                    Expanded(child: Text(localizations.mainImage, style: Theme.of(context).textTheme.bodyLarge)),
                                    ElevatedButton.icon(
                                      icon: const Icon(Icons.photo_camera),
                                      label: Text(localizations.upload),
                                      onPressed: () async {
                                        final path = await _pickAndUploadImage();
                                        if (path != null) setState(() { _image.text = path; });
                                      },
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            // Thumbnail preview for main image
                            if (_image.text.isNotEmpty)
                              Padding(
                                padding: const EdgeInsets.only(right: 12),
                                child: GestureDetector(
                                  onTap: () => _showImageViewer(_image.text),
                                  child: ClipRRect(
                                          borderRadius: BorderRadius.circular(8),
                                          child: Image.network(
                                            _resolveImagePath(_image.text) ?? '',
                                            width: 56,
                                            height: 56,
                                            fit: BoxFit.cover,
                                            errorBuilder: (_, __, ___) => Container(
                                              width: 56,
                                              height: 56,
                                              color: Theme.of(context).colorScheme.surfaceContainerHighest,
                                              child: const Icon(Icons.broken_image, size: 20),
                                            ),
                                          ),
                                        ),
                                ),
                              ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                          ),
                          color: Theme.of(context).colorScheme.surface,
                        ),
                        child: Column(
                          children: [
                            // Hidden controller kept for payload; show upload button instead of raw URL
                            Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                              child: Row(
                                children: [
                                  Expanded(child: Text(localizations.gallery, style: Theme.of(context).textTheme.bodyLarge)),
                                  ElevatedButton.icon(
                                    icon: const Icon(Icons.photo_library),
                                    label: Text(localizations.addImage),
                                    onPressed: () async {
                                      final path = await _pickAndUploadImage();
                                      if (path != null) {
                                        setState(() {
                                          final items = _gallery.text.split(',').map((s) => s.trim()).where((s) => s.isNotEmpty).toList();
                                          items.add(path);
                                          _gallery.text = items.join(',');
                                        });
                                      }
                                    },
                                  ),
                                  const SizedBox(width: 8),
                                  if (_gallery.text.isNotEmpty)
                                    OutlinedButton(
                                      child: Text(localizations.clear),
                                      onPressed: () => setState(() => _gallery.text = ''),
                                    ),
                                ],
                              ),
                            ),
                            // Thumbnails row for gallery images
                            Builder(builder: (ctx) {
                              final items = _gallery.text.split(',').map((s) => s.trim()).where((s) => s.isNotEmpty).toList();
                              if (items.isEmpty) return const SizedBox.shrink();
                              return Padding(
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                child: SizedBox(
                                  height: 64,
                                  child: ListView.builder(
                                    scrollDirection: Axis.horizontal,
                                    itemCount: items.length,
                                    itemBuilder: (c, i) {
                                      final url = items[i];
                                      return Padding(
                                        padding: const EdgeInsets.only(right: 8),
                                        child: GestureDetector(
                                          onTap: () => _showImageViewer(url),
                                            child: ClipRRect(
                                            borderRadius: BorderRadius.circular(8),
                                            child: Image.network(
                                              _resolveImagePath(url) ?? '',
                                              width: 64,
                                              height: 64,
                                              fit: BoxFit.cover,
                                              errorBuilder: (_, __, ___) => Container(
                                                width: 64,
                                                height: 64,
                                                color: Theme.of(context).colorScheme.surfaceContainerHighest,
                                                child: const Icon(Icons.broken_image, size: 20),
                                              ),
                                            ),
                                          ),
                                        ),
                                      );
                                    },
                                  ),
                                ),
                              );
                            }),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                      if (_tags.isNotEmpty)
                        Wrap(
                          spacing: 8,
                          runSpacing: 8,
                          children: _tags.map((tag) {
                            final isSelected = _selectedTags.contains(tag['id']);
                            return FilterChip(
                              label: Text(tag['name'].toString()),
                              selected: isSelected,
                              onSelected: (selected) {
                                setState(() {
                                  if (selected) {
                                    _selectedTags.add(tag['id']);
                                  } else {
                                    _selectedTags.remove(tag['id']);
                                  }
                                });
                              },
                              backgroundColor: Theme.of(context).colorScheme.surface,
                              selectedColor: Theme.of(context).colorScheme.primary.withOpacity(0.2),
                              checkmarkColor: Theme.of(context).colorScheme.primary,
                            );
                          }).toList(),
                        ),
                    ],
                  )),
                  
                  _sectionCard(localizations.seoOptimization, Icons.search_outlined, Column(
                    children: [
                      _buildMultiLangField(
                        fieldKey: 'seo_title',
                        labelAr: localizations.seoTitle,
                        icon: Icons.title_outlined,
                      ),
                      const SizedBox(height: 16),
                      _buildMultiLangField(
                        fieldKey: 'seo_description',
                        labelAr: localizations.seoDescription,
                        icon: Icons.description_outlined,
                        maxLines: 3,
                      ),
                      const SizedBox(height: 16),
                      _buildMultiLangField(
                        fieldKey: 'seo_keywords',
                        labelAr: localizations.seoKeywords,
                        icon: Icons.key_outlined,
                      ),
                    ],
                  )),
                  
                  _sectionCard(localizations.productSettings, Icons.settings_outlined, Column(
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                ),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: DropdownButtonFormField<String>(
                                initialValue: _type,
                                decoration: InputDecoration(
                                  labelText: localizations.productType,
                                  prefixIcon: Icon(Icons.category_outlined),
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.all(16),
                                ),
                                items: [
                                  DropdownMenuItem(value: 'simple', child: Text(localizations.simple)),
                                  DropdownMenuItem(value: 'variable', child: Text(localizations.variable)),
                                ],
                                onChanged: (v) => setState(() => _type = v ?? 'simple'),
                              ),
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                ),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: DropdownButtonFormField<String>(
                                initialValue: _physicalType,
                                decoration: InputDecoration(
                                  labelText: localizations.physicalType,
                                  prefixIcon: Icon(Icons.inventory_2_outlined),
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.all(16),
                                ),
                                items: [
                                  DropdownMenuItem(value: 'physical', child: Text(localizations.physical)),
                                  DropdownMenuItem(value: 'digital', child: Text(localizations.digital)),
                                ],
                                onChanged: (v) => setState(() => _physicalType = v ?? 'physical'),
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                ),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: TextFormField(
                                controller: _reservedQty,
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                  labelText: localizations.reservedQuantity,
                                  prefixIcon: Icon(Icons.lock_outlined),
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.all(16),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: _buildCheckboxTile(localizations.manageStock, _manageStock, Icons.inventory_outlined, (v) => setState(() => _manageStock = v ?? false)),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      Wrap(
                        spacing: 16,
                        runSpacing: 8,
                        children: [
                          _buildCheckboxTile(localizations.featured, _isFeatured, Icons.star_outline, (v) => setState(() => _isFeatured = v ?? false)),
                          _buildCheckboxTile(localizations.bestSeller, _isBestSeller, Icons.trending_up_outlined, (v) => setState(() => _isBestSeller = v ?? false)),
                          _buildCheckboxTile(localizations.backorder, _backorder, Icons.schedule_outlined, (v) => setState(() => _backorder = v ?? false)),
                          _buildCheckboxTile(localizations.hasSerials, _hasSerials, Icons.confirmation_number_outlined, (v) => setState(() => _hasSerials = v ?? false)),
                        ],
                      ),
                    ],
                  )),
                  
                  _sectionCard(localizations.salesAndDiscounts, Icons.local_offer_outlined, Column(
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                          ),
                          color: Theme.of(context).colorScheme.surface,
                        ),
                        child: TextFormField(
                          controller: _salePrice,
                          keyboardType: TextInputType.number,
                          decoration: InputDecoration(
                            labelText: localizations.salePrice,
                            prefixIcon: Icon(Icons.local_offer_outlined),
                            border: InputBorder.none,
                            contentPadding: EdgeInsets.all(16),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                ),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: TextFormField(
                                controller: _saleStart,
                                decoration: InputDecoration(
                                  labelText: localizations.saleStartDate,
                                  prefixIcon: Icon(Icons.calendar_today_outlined),
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.all(16),
                                  hintText: localizations.dateFormat,
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                ),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: TextFormField(
                                controller: _saleEnd,
                                decoration: InputDecoration(
                                  labelText: localizations.saleEndDate,
                                  prefixIcon: Icon(Icons.calendar_today_outlined),
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.all(16),
                                  hintText: localizations.dateFormat,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                          ),
                          color: Theme.of(context).colorScheme.surface,
                        ),
                        child: TextFormField(
                          controller: _refundDays,
                          keyboardType: TextInputType.number,
                          decoration: InputDecoration(
                            labelText: localizations.refundDays,
                            prefixIcon: Icon(Icons.assignment_return_outlined),
                            border: InputBorder.none,
                            contentPadding: EdgeInsets.all(16),
                          ),
                        ),
                      ),
                    ],
                  )),
                  
                  // Dimensions and Weight Section - Only show for physical products
                  if (_physicalType == 'physical')
                    _sectionCard(localizations.dimensionsAndWeight, Icons.straighten_outlined, Column(
                      children: [
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                          ),
                          color: Theme.of(context).colorScheme.surface,
                        ),
                        child: TextFormField(
                          controller: _weight,
                          keyboardType: TextInputType.number,
                          decoration: InputDecoration(
                            labelText: localizations.weight,
                            prefixIcon: Icon(Icons.fitness_center_outlined),
                            border: InputBorder.none,
                            contentPadding: EdgeInsets.all(16),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                ),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: TextFormField(
                                controller: _length,
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                  labelText: localizations.length,
                                  prefixIcon: Icon(Icons.straighten_outlined),
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.all(16),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                ),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: TextFormField(
                                controller: _width,
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                  labelText: localizations.width,
                                  prefixIcon: Icon(Icons.straighten_outlined),
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.all(16),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                ),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: TextFormField(
                                controller: _height,
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                  labelText: localizations.height,
                                  prefixIcon: Icon(Icons.height_outlined),
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.all(16),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  )),
                  
                  // Product Variations Section - Only show for variable products
                  if (_type == 'variable')
                    _sectionCard(localizations.productVariations, Icons.tune_outlined, Column(
                      children: [
                        Align(
                          alignment: Alignment.centerRight,
                          child: FilledButton.icon(
                            icon: const Icon(Icons.add),
                            label: Text(localizations.addVariation),
                            onPressed: _usedAttributes.isNotEmpty ? _addVariation : null,
                          ),
                        ),
                        const SizedBox(height: 12),
                        if (_variations.isNotEmpty) ...[
                          Align(
                            alignment: Alignment.centerLeft,
                            child: Padding(
                              padding: const EdgeInsets.only(bottom: 12),
                              child: Text(
                                '${localizations.existingVariations} (${_variations.length})',
                                style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w600),
                              ),
                            ),
                          ),
                          ..._variations.map((v) {
                            final attrs = (v['attributes'] is Map)
                              ? (v['attributes'] as Map).entries.map((e) => '${e.key}: ${e.value}').join(', ')
                              : '';
                            final vImg = _getVariationImage(v);
                            if (kDebugMode) {
                              try {
                                debugPrint('[VAR LIST] id=${v['id']} rawImage=$vImg resolved=${_resolveImagePath(vImg)} attrs=${v['attributes']}');
                              } catch (_) {}
                            }
                            return Container(
                              margin: const EdgeInsets.only(bottom: 12),
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: Theme.of(context).colorScheme.outline.withOpacity(0.2)),
                                color: Theme.of(context).colorScheme.surface,
                              ),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  if (vImg != null) ...[
                                    GestureDetector(
                                      onTap: () => _showImageViewer(vImg),
                                      child: ClipRRect(
                                        borderRadius: BorderRadius.circular(8),
                                        child: Image.network(
                                          _resolveImagePath(vImg) ?? '',
                                          width: 56,
                                          height: 56,
                                          fit: BoxFit.cover,
                                          errorBuilder: (_, __, ___) => Container(
                                            width: 56,
                                            height: 56,
                                            color: Theme.of(context).colorScheme.surfaceContainerHighest,
                                            child: const Icon(Icons.broken_image, size: 20),
                                          ),
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 12),
                                  ],
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          children: [
                                            Expanded(
                                              child: Text(
                                                v['name']?.toString().isNotEmpty == true ? v['name'].toString() : attrs,
                                                style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w600),
                                              ),
                                            ),
                                            Row(
                                              mainAxisSize: MainAxisSize.min,
                                              children: [
                                                IconButton(
                                                  padding: EdgeInsets.zero,
                                                  constraints: const BoxConstraints(),
                                                  icon: Icon(Icons.edit, size: 18, color: Theme.of(context).colorScheme.primary),
                                                  onPressed: () => _editVariation(v),
                                                ),
                                                const SizedBox(width: 4),
                                                IconButton(
                                                  padding: EdgeInsets.zero,
                                                  constraints: const BoxConstraints(),
                                                  icon: Icon(Icons.delete_outline, size: 20, color: Theme.of(context).colorScheme.error),
                                                  onPressed: () => _confirmDeleteVariation(v),
                                                ),
                                                const SizedBox(width: 6),
                                              ],
                                            ),
                                            if (v['active'] == true)
                                              Container(
                                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                                decoration: BoxDecoration(
                                                  color: Theme.of(context).colorScheme.primary.withOpacity(0.1),
                                                  borderRadius: BorderRadius.circular(20),
                                                ),
                                                child: Text(localizations.active, style: TextStyle(color: Theme.of(context).colorScheme.primary, fontSize: 11)),
                                              )
                                            else
                                              Container(
                                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                                decoration: BoxDecoration(
                                                  color: Theme.of(context).colorScheme.error.withOpacity(0.1),
                                                  borderRadius: BorderRadius.circular(20),
                                                ),
                                                child: Text(localizations.inactive, style: TextStyle(color: Theme.of(context).colorScheme.error, fontSize: 11)),
                                              ),
                                          ],
                                        ),
                                        const SizedBox(height: 4),
                                        if (attrs.isNotEmpty)
                                          Text(attrs, style: Theme.of(context).textTheme.bodySmall),
                                        const SizedBox(height: 6),
                                        Wrap(
                                          spacing: 12,
                                          runSpacing: 4,
                                          children: [
                                            if (v['sku'] != null)
                                              _miniStat(Icons.qr_code, v['sku']),
                                            _miniStat(Icons.attach_money, (v['effective_price'] ?? v['price']).toString()),
                                            if (v['stock_qty'] != null)
                                              _miniStat(Icons.inventory_2_outlined, '${localizations.stock}: ${v['stock_qty']}'),
                                            if (v['available_stock'] != null)
                                              _miniStat(Icons.inventory_outlined, '${localizations.available}: ${v['available_stock']}'),
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            );
                          }),
                          const Divider(height: 32),
                        ],
                        if (_loadingAttributes)
                          const Center(child: CircularProgressIndicator())
                        else if (_attributes.isEmpty)
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(12),
                              color: Theme.of(context).colorScheme.errorContainer.withOpacity(0.1),
                              border: Border.all(
                                color: Theme.of(context).colorScheme.error.withOpacity(0.3),
                              ),
                            ),
                            child: Row(
                              children: [
                                Icon(
                                  Icons.warning_outlined,
                                  color: Theme.of(context).colorScheme.error,
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Text(
                                    localizations.noAttributesAvailable,
                                    style: TextStyle(
                                      color: Theme.of(context).colorScheme.error,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          )
                        else
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                localizations.selectAttributesForVariations,
                                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                              const SizedBox(height: 16),
                              ..._attributes.map((attr) {
                                final isUsed = _usedAttributes.contains(attr['slug']);
                                return Container(
                                  margin: const EdgeInsets.only(bottom: 12),
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(
                                      color: isUsed 
                                        ? Theme.of(context).colorScheme.primary.withOpacity(0.3)
                                        : Theme.of(context).colorScheme.outline.withOpacity(0.2),
                                    ),
                                    color: isUsed 
                                      ? Theme.of(context).colorScheme.primary.withOpacity(0.05)
                                      : Theme.of(context).colorScheme.surface,
                                  ),
                                  child: CheckboxListTile(
                                    title: Text(attr['name'] ?? ''),
                                    subtitle: attr['values'] != null && (attr['values'] as List).isNotEmpty
                                      ? Text(
                                          '${AppLocalizations.of(context)!.values}: ${(attr['values'] as List).map((v) => v['value']).join(', ')}',
                                          style: Theme.of(context).textTheme.bodySmall,
                                        )
                                      : null,
                                    value: isUsed,
                                    onChanged: (bool? value) {
                                      setState(() {
                                        if (value == true) {
                                          _usedAttributes.add(attr['slug']);
                                        } else {
                                          _usedAttributes.remove(attr['slug']);
                                        }
                                      });
                                    },
                                    controlAffinity: ListTileControlAffinity.leading,
                                  ),
                                );
                              }),
                              if (_usedAttributes.isNotEmpty) ...[
                                const SizedBox(height: 16),
                                Container(
                                  padding: const EdgeInsets.all(16),
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(12),
                                    color: Theme.of(context).colorScheme.primaryContainer.withOpacity(0.1),
                                    border: Border.all(
                                      color: Theme.of(context).colorScheme.primary.withOpacity(0.3),
                                    ),
                                  ),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          Icon(
                                            Icons.info_outline,
                                            color: Theme.of(context).colorScheme.primary,
                                            size: 20,
                                          ),
                                          const SizedBox(width: 8),
                                          Text(
                                            localizations.variationManagement,
                                            style: Theme.of(context).textTheme.titleSmall?.copyWith(
                                              fontWeight: FontWeight.w600,
                                              color: Theme.of(context).colorScheme.primary,
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 8),
                                      Text(
                                        '${localizations.selectedAttributes}: ${_usedAttributes.join(', ')}',
                                        style: Theme.of(context).textTheme.bodyMedium,
                                      ),
                                      const SizedBox(height: 8),
                                      Text(
                                        localizations.variationManagementDescription,
                                        style: Theme.of(context).textTheme.bodySmall?.copyWith(
                                          color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ],
                          ),
                      ],
                    )),
                  
                  // Digital Products Section - Only show for digital products
                  if (_physicalType == 'digital')
                    _sectionCard(localizations.digitalProducts, Icons.cloud_download_outlined, Column(
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                            ),
                            color: Theme.of(context).colorScheme.surface,
                          ),
                          child: TextFormField(
                            controller: _downloadUrl,
                            decoration: InputDecoration(
                              labelText: localizations.downloadUrl,
                              prefixIcon: Icon(Icons.link_outlined),
                              border: InputBorder.none,
                              contentPadding: EdgeInsets.all(16),
                            ),
                          ),
                        ),
                        const SizedBox(height: 16),
                        Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                            ),
                            color: Theme.of(context).colorScheme.surface,
                          ),
                          child: TextFormField(
                            controller: _downloadFile,
                            decoration: InputDecoration(
                              labelText: localizations.downloadFile,
                              prefixIcon: Icon(Icons.file_download_outlined),
                              border: InputBorder.none,
                              contentPadding: EdgeInsets.all(16),
                            ),
                          ),
                        ),
                        const SizedBox(height: 16),
                        Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: Theme.of(context).colorScheme.outline.withOpacity(0.2),
                            ),
                            color: Theme.of(context).colorScheme.surface,
                          ),
                          child: TextFormField(
                            controller: _serials,
                            maxLines: 3,
                            decoration: InputDecoration(
                              labelText: localizations.serialNumbers,
                              prefixIcon: Icon(Icons.confirmation_number_outlined),
                              border: InputBorder.none,
                              contentPadding: EdgeInsets.all(16),
                            ),
                          ),
                        ),
                      ],
                    )),
                  
                  const SizedBox(height: 32),
                  Container(
                    width: double.infinity,
                    height: 56,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(16),
                      gradient: LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors: [
                          Theme.of(context).colorScheme.primary,
                          Theme.of(context).colorScheme.primary.withOpacity(0.8),
                        ],
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Theme.of(context).colorScheme.primary.withOpacity(0.3),
                          blurRadius: 20,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: ElevatedButton(
                      onPressed: _saving ? null : _save,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.transparent,
                        shadowColor: Colors.transparent,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      child: _saving
                          ? Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor: AlwaysStoppedAnimation<Color>(
                                      Theme.of(context).colorScheme.onPrimary,
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Text(
                                  localizations.saving,
                                  style: TextStyle(
                                    color: Theme.of(context).colorScheme.onPrimary,
                                    fontSize: 16,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ],
                            )
                          : Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  Icons.save_outlined,
                                  color: Theme.of(context).colorScheme.onPrimary,
                                  size: 24,
                                ),
                                const SizedBox(width: 12),
                                Text(
                                  localizations.saveProduct,
                                  style: TextStyle(
                                    color: Theme.of(context).colorScheme.onPrimary,
                                    fontSize: 18,
                                    fontWeight: FontWeight.w600,
                                    letterSpacing: 0.5,
                                  ),
                                ),
                              ],
                            ),
                    ),
                  ),
                  const SizedBox(height: 32),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}