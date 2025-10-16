import 'package:flutter/material.dart';

class ChartWidget extends StatelessWidget {
  final List<dynamic> data;
  final String title;

  const ChartWidget({
    super.key,
    required this.data,
    required this.title,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.bar_chart,
              size: 48,
              color: Theme.of(context).colorScheme.outline,
            ),
            const SizedBox(height: 8),
            Text(
              'No data available',
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: Theme.of(context).colorScheme.outline,
              ),
            ),
          ],
        ),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      mainAxisSize: MainAxisSize.min,
      children: [
        Text(
          title,
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.w600,
          ),
        ),
        const SizedBox(height: 12),
        Flexible(
          child: _buildSimpleBarChart(context),
        ),
      ],
    );
  }

  Widget _buildSimpleBarChart(BuildContext context) {
    // Find the maximum value for scaling
    double maxValue = 0;
    for (var item in data) {
      final value = (item['value'] as num?)?.toDouble() ?? 0;
      if (value > maxValue) maxValue = value;
    }

    if (maxValue == 0) maxValue = 1; // Prevent division by zero

    return LayoutBuilder(
      builder: (context, constraints) {
        final availableHeight = constraints.maxHeight;
        final maxBarHeight = (availableHeight - 60).clamp(60.0, 120.0); // Reserve space for labels
        
        return Row(
          crossAxisAlignment: CrossAxisAlignment.end,
          children: data.asMap().entries.map((entry) {
            final index = entry.key;
            final item = entry.value;
            final value = (item['value'] as num?)?.toDouble() ?? 0;
            final label = item['label']?.toString() ?? '';
            final height = (value / maxValue) * maxBarHeight;

            return Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 2),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.end,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // Value label on top of bar
                    if (value > 0)
                      Container(
                        height: 20,
                        alignment: Alignment.bottomCenter,
                        child: Text(
                          _formatValue(value),
                          style: Theme.of(context).textTheme.bodySmall?.copyWith(
                            fontSize: 9,
                            fontWeight: FontWeight.w500,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    const SizedBox(height: 2),
                    // Bar
                    AnimatedContainer(
                      duration: Duration(milliseconds: 300 + (index * 100)),
                      height: height.clamp(0.0, maxBarHeight),
                      width: double.infinity,
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [
                            Theme.of(context).colorScheme.primary,
                            Theme.of(context).colorScheme.primary.withValues(alpha: 0.7),
                          ],
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                        ),
                        borderRadius: const BorderRadius.vertical(
                          top: Radius.circular(4),
                        ),
                      ),
                    ),
                    const SizedBox(height: 4),
                    // Label at bottom
                    Container(
                      height: 24,
                      alignment: Alignment.topCenter,
                      child: Text(
                        label,
                        style: Theme.of(context).textTheme.bodySmall?.copyWith(
                          fontSize: 9,
                          color: Theme.of(context).colorScheme.outline,
                        ),
                        textAlign: TextAlign.center,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              ),
            );
          }).toList(),
        );
      },
    );
  }

  String _formatValue(double value) {
    if (value >= 1000000) {
      return '${(value / 1000000).toStringAsFixed(1)}M';
    } else if (value >= 1000) {
      return '${(value / 1000).toStringAsFixed(1)}K';
    } else {
      return value.toStringAsFixed(0);
    }
  }
}