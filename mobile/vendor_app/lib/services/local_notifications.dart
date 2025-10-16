import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class LocalNotifications {
  LocalNotifications._();

  static final FlutterLocalNotificationsPlugin _plugin = FlutterLocalNotificationsPlugin();

  static Future<void> init() async {
    const android = AndroidInitializationSettings('@mipmap/ic_launcher');
    const ios = DarwinInitializationSettings();

    await _plugin.initialize(
      const InitializationSettings(android: android, iOS: ios),
    );
  }

  static Future<void> showNotification({required String id, required String title, required String body}) async {
    const androidDetails = AndroidNotificationDetails(
      'vendor_app_channel',
      'Vendor App',
      channelDescription: 'Notifications from the vendor platform',
      importance: Importance.max,
      priority: Priority.high,
      playSound: true,
    );

    const iosDetails = DarwinNotificationDetails();

    await _plugin.show(
      id.hashCode,
      title,
      body,
      const NotificationDetails(android: androidDetails, iOS: iosDetails),
      payload: id,
    );
  }
}
