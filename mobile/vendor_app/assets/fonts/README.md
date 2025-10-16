Place Cairo font files here so the app can use Cairo as its default font.

Which files to add (suggested filenames used in pubspec.yaml):
- Cairo-Regular.ttf
- Cairo-Medium.ttf (weight: 500)
- Cairo-SemiBold.ttf (weight: 600)
- Cairo-Bold.ttf (weight: 700)

Get Cairo from Google Fonts:
https://fonts.google.com/specimen/Cairo

Steps to enable after adding files:
1. Run in project root:

   flutter pub get

2. (Optional) Regenerate localization files if you changed ARB files:

   flutter gen-l10n

3. Run the app on a device/emulator:

   flutter run

Notes:
- The app's `pubspec.yaml` already defines Cairo in the `fonts:` section and `lib/app_theme.dart` sets `fontFamily: 'Cairo'` for both themes.
- If font files are missing, Flutter will fall back to the platform default font.
