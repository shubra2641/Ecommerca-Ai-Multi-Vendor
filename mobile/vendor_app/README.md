Vendor Dashboard Flutter scaffold

This minimal scaffold demonstrates a simple vendor login and dashboard that communicates with the existing Laravel backend API endpoints.

To run:

1. Ensure your Laravel app is running (php artisan serve or through Valet/XAMPP). For Android emulator use host 10.0.2.2 for the baseUrl in `lib/services/api_client.dart`.
2. Install Flutter dependencies: `flutter pub get`.
3. Run the app: `flutter run`.

Notes:
- This is a starting point. Expand screens, storage of tokens, secure storage, and navigation as needed.

Localization and writing language
- Supported locales: English (`en`) and Arabic (`ar`). The app's generated `AppLocalizations`
	already includes both locales and the `MaterialApp` is wired to use them.
- Default UI locale is read from `ThemeProvider` (defaults to `en`). English is also used as
	the default writing language for input fields.
- To force English (LTR) input for a specific field, use the provided widget:

```dart
import 'package:vendor_app/widgets/english_text_field.dart';

EnglishTextField(
	decoration: InputDecoration(labelText: 'Name'),
)
```

This widget wraps a `TextFormField` in `Directionality(textDirection: TextDirection.ltr)` so
keyboard and alignment are preferable for English typing even when the app UI is in Arabic.
