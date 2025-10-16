// ignore_for_file: deprecated_member_use
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../l10n/app_localizations.dart';
import '../providers/theme_provider.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    
    return Scaffold(
      appBar: AppBar(
        title: Text(l10n.settings),
        elevation: 0,
      ),
      body: Consumer<ThemeProvider>(
        builder: (context, themeProvider, child) {
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              _buildSectionHeader(context, l10n.appearance),
              const SizedBox(height: 8),
              _buildThemeCard(context, themeProvider, l10n),
              const SizedBox(height: 24),
              _buildSectionHeader(context, l10n.language),
              const SizedBox(height: 8),
              _buildLanguageCard(context, themeProvider, l10n),
            ],
          );
        },
      ),
    );
  }

  Widget _buildSectionHeader(BuildContext context, String title) {
    return Text(
      title,
      style: Theme.of(context).textTheme.titleMedium?.copyWith(
        fontWeight: FontWeight.w600,
        color: Theme.of(context).colorScheme.primary,
      ),
    );
  }

  Widget _buildThemeCard(BuildContext context, ThemeProvider themeProvider, AppLocalizations l10n) {
    return Card(
      elevation: 1,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(
                  themeProvider.isDarkMode ? Icons.dark_mode : Icons.light_mode,
                  color: Theme.of(context).colorScheme.primary,
                ),
                const SizedBox(width: 12),
                Text(
                  l10n.themeMode,
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const Spacer(),
                Switch(
                  value: themeProvider.isDarkMode,
                  onChanged: (value) {
                    themeProvider.toggleTheme();
                  },
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              themeProvider.isDarkMode ? l10n.darkModeEnabled : l10n.lightModeEnabled,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: Theme.of(context).colorScheme.onSurfaceVariant,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLanguageCard(BuildContext context, ThemeProvider themeProvider, AppLocalizations l10n) {
    return Card(
      elevation: 1,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(
                  Icons.language,
                  color: Theme.of(context).colorScheme.primary,
                ),
                const SizedBox(width: 12),
                Text(
                  l10n.language,
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            ...AppLocalizations.supportedLocales.map((locale) {
              final isSelected = themeProvider.locale.languageCode == locale.languageCode;
              return Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: InkWell(
                  onTap: () {
                    themeProvider.setLocale(locale);
                  },
                  borderRadius: BorderRadius.circular(12),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      color: isSelected 
                          ? Theme.of(context).colorScheme.primaryContainer
                          : Theme.of(context).colorScheme.surface,
                      border: Border.all(
                        color: isSelected 
                            ? Theme.of(context).colorScheme.primary
                            : Theme.of(context).colorScheme.outline.withOpacity(0.2),
                      ),
                    ),
                    child: Row(
                      children: [
                        Text(
                          themeProvider.getLanguageName(locale.languageCode),
                          style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                            fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                            color: isSelected 
                                ? Theme.of(context).colorScheme.onPrimaryContainer
                                : Theme.of(context).colorScheme.onSurface,
                          ),
                        ),
                        const Spacer(),
                        if (isSelected)
                          Icon(
                            Icons.check_circle,
                            color: Theme.of(context).colorScheme.primary,
                            size: 20,
                          ),
                      ],
                    ),
                  ),
                ),
              );
            }),
          ],
        ),
      ),
    );
  }
}
