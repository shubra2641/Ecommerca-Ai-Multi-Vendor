Setup & Deployment Guide — vendor_app (Flutter)

Purpose

This document explains step-by-step how a developer or a user can set up and run the Flutter mobile app found in this repository, how to add Google Services (Firebase) configuration, how to change the app package / bundle identifier, app name and launcher icon, how to install Flutter and required tools on Windows, and how to point the app to your backend server.

Checklist (what this doc covers)

- [ ] Install Flutter & Android SDK (Windows)
- [ ] Run basic Flutter checks and emulator
- [ ] Install project dependencies and build the app
- [ ] Add Firebase / Google Services (Android + iOS)
- [ ] Change Android package name (applicationId) and iOS bundle identifier
- [ ] Change app display name
- [ ] Replace app launcher icon
- [ ] Configure backend server URL in the app
- [ ] Run the app on emulator/device and verify

1) Prerequisites (Windows)

- Windows 10/11 (64-bit) with administrative access.
- Git installed (optional if you already have repo files).
- Java JDK 11+ (for Android builds).
- Android SDK + Android Studio (or command-line SDK) with at least one emulator image.
- Flutter SDK (stable channel). See Flutter docs for latest: https://docs.flutter.dev
- Xcode (only required for iOS builds; macOS machine required).

2) Install Flutter on Windows (short)

- Download and unzip Flutter SDK from https://docs.flutter.dev/get-started/install/windows
- Add Flutter to PATH (e.g., add <flutter_dir>\bin to environment PATH).
- Open PowerShell and run:

```powershell
flutter doctor -v
```

Follow the output to install any missing tools (Android SDK, platform tools, etc.).

3) Open the project and get dependencies

Open PowerShell and run (adjust path if your repo is elsewhere):

```powershell
cd D:\xampp\htdocs\mobile\vendor_app
flutter pub get
flutter analyze
```

If analyze shows issues, address only critical errors; warnings are normal depending on code style.

4) Run on Android emulator (quick)

Start an Android emulator from Android Studio or command line, then:

```powershell
flutter devices       # list devices
flutter run -d <device-id>   # e.g. flutter run -d emulator-5554
```

5) Add Google Services (Firebase) — Android

Why: many features (analytics, push notifications, auth) require google-services.json in Android app.

Steps:

1. Create a Firebase project in the Firebase Console (https://console.firebase.google.com).
2. Add an Android app and enter the Android package name (see step 6). Example: com.mycompany.myapp
3. Download `google-services.json` when Firebase asks.
4. Place the file in the Android app module folder:

   D:\xampp\htdocs\mobile\vendor_app\android\app\google-services.json

5. Edit `android/build.gradle` (project-level) and ensure classpath for Google services exists in `buildscript` or `plugins` block. Typical lines:

```gradle
buildscript {
    dependencies {
        classpath 'com.google.gms:google-services:4.3.15' // check latest
    }
}
```

6. Edit `android/app/build.gradle` and apply the plugin at the bottom of the file:

```gradle
apply plugin: 'com.google.gms.google-services'
```

7. If you will use Firebase packages (e.g., firebase_core, firebase_messaging), add them to `pubspec.yaml` and run `flutter pub get`. Follow package setup docs for initialization in `main.dart` (usually await Firebase.initializeApp()).

Note: This repo may not include Firebase packages by default; adding google-services.json alone is not enough — you must add the Flutter Firebase packages you plan to use.

6) Add Google Services — iOS (macOS required)

1. In Firebase console add an iOS app and download `GoogleService-Info.plist`.
2. Place `GoogleService-Info.plist` into `ios/Runner/` (Xcode: Runner & Target) and in Xcode add the file to the Runner target.
3. Add `Firebase/Core` pods as instructed by plugin docs and run `pod install` in `ios/`.
4. Initialize Firebase in AppDelegate if needed (check plugin docs).

7) Change Android package name (applicationId) and iOS bundle identifier

WARNING: Renaming package/bundle involves changing paths and package declarations for Android native files. Backup before proceeding.

Android manual steps (safe minimal):

- Decide new package name, e.g. `com.mycompany.myapp`.
- Update the applicationId in `android/app/build.gradle` (or Kotlin DSL equivalent `android/app/build.gradle.kts`) to the new id, e.g.: 

```gradle
android {
    defaultConfig {
        applicationId "com.mycompany.myapp"
        // ...
    }
}
```

- Update the `package` attribute in `android/app/src/main/AndroidManifest.xml` (the top-level `manifest` element) if present.
- Update the Kotlin/Java package declaration in `android/app/src/main/kotlin/.../MainActivity.kt` (or MainActivity.java). You may need to move the file to a matching folder path (e.g., `android/app/src/main/kotlin/com/mycompany/myapp/MainActivity.kt`).
- Update any `androidTest` or `debug`/`profile` manifests if they contain the old package.
- Update `google-services.json` in Firebase console to register the new package name (download new json and replace).

Alternatively, use community tools such as `change_app_package_name` or `flutter_rename_app` — but manual steps are more reliable.

iOS (requires Xcode):

- Open `ios/Runner.xcworkspace` in Xcode.
- Select the Runner project, then the Runner target, and set the "Bundle Identifier" to your new id (reverse-domain style).
- Update any provisioning profiles/codesigning as needed.

8) Change app display name

Android:

- Edit `android/app/src/main/res/values/strings.xml` and change the `app_name` string value. If `strings.xml` doesn't exist, edit `AndroidManifest.xml` application label.

iOS:

- Open Xcode and change the "Display Name" in `Info.plist` or change `CFBundleDisplayName`.

Flutter-level:

- Some packages can override app name on each platform; ensure both platforms are updated for consistent behavior.

9) Change app launcher icon (recommended method)

Use `flutter_launcher_icons` package to generate icons for Android and iOS.

- Add to `pubspec.yaml` (under dev_dependencies):

```yaml
dev_dependencies:
  flutter_launcher_icons: ^0.10.0

flutter_icons:
  android: true
  ios: true
  image_path: "assets/icons/app_icon.png"  # update path to your icon file (square PNG)
```

- Put your icon image into the `assets` path and run:

```powershell
flutter pub get
flutter pub run flutter_launcher_icons:main
```

Manual alternative:

- Replace the icon files under `android/app/src/main/res/mipmap-*/` for Android and `ios/Runner/Assets.xcassets/AppIcon.appiconset/` for iOS with appropriately sized images.

10) Configure backend server URL in the app

Open the API client file and update the base URL. In this codebase the client is under:

- `lib/services/api_client.dart`  (open this file in your editor)

Look for a constant or variable named `baseUrl`, `API_URL`, or similar. Edit it to your server endpoint, for example:

```dart
// lib/services/api_client.dart
// ...
final baseUrl = 'https://api.myserver.com';
```

Other places to check:
- `lib/config` or `lib/constants.dart` if present
- Any `.env` usage or conditional environment configuration files

After editing, save and run:

```powershell
flutter pub get
flutter run -d emulator-5554
```

11) Authentication & tokens

- The app stores tokens in secure storage. To connect to your server, either use the app's login screen to authenticate or insert a test token into secure storage (not recommended for production). Check `lib/services/api_client.dart` to see where token is read (e.g., `flutter_secure_storage`).

12) Testing notifications (local & push)

- Local notifications: app already integrates `flutter_local_notifications`. Ensure Android manifest contains `POST_NOTIFICATIONS` permission for API 33+ (Android 13+). Grant runtime permission when testing.
- Push notifications (FCM): follow `firebase_messaging` installation steps and integrate message handlers. Add `google-services.json` and update Android & iOS setup, then follow package docs.

13) Build release APK / App Bundle

Android release (example):

```powershell
# in project root
flutter build apk --release
# or build app bundle
flutter build appbundle --release
```

Before building release, set `signingConfigs` in `android/key.properties` and `android/app/build.gradle` to sign your APK.

iOS release: requires Xcode and macOS — build with Xcode or `flutter build ipa`.

14) Helpful PowerShell commands (copyable)

```powershell
# Go to project
cd D:\xampp\htdocs\mobile\vendor_app

# fetch dependencies
flutter pub get

# run analyzer
flutter analyze

# list devices
flutter devices

# run on emulator (example device id)
flutter run -d emulator-5554

# build debug apk
flutter build apk --debug

# build release app bundle
flutter build appbundle --release
```

15) Troubleshooting notes

- If the emulator/device loses connection, re-run `flutter devices` and `flutter run`.
- If localization generation fails, check `lib/l10n/*.arb` files are valid JSON.
- If Firebase initialization fails, ensure `google-services.json` is valid and placed under `android/app/` and that `apply plugin` and classpath are configured.
- If notifications don't appear on Android 13+, ensure you requested runtime permission `POST_NOTIFICATIONS` or manually enable notifications for the app in OS settings.

16) Final verification checklist (what to test after setup)

- [ ] App builds and runs on Android emulator
- [ ] Login/auth flow connects to your server and returns valid data
- [ ] Dashboard loads and reflects server data
- [ ] Orders appear in Orders screen and Detail view shows variant and shipping cost
- [ ] Local notifications appear when `NotificationProvider` triggers them (test by loading notifications)
- [ ] Launcher icons and app name are correct on device

If you want, I can:

- Add a small script or makefile to automate common edit points (e.g., changing the base URL via a single file)
- Add `flutter_launcher_icons` config to `pubspec.yaml` and run it for you
- Add a small debug toggle in `lib/services/api_client.dart` to switch between staging/production URLs

Tell me which follow-up you want and I will implement it and run a validation build. 
