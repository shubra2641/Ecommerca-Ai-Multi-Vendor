<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FillMissingTranslations extends Command
{
    protected $signature = 'translations:fill {--model=* : Specific model class names (e.g. Product)} {--locale=* : Target locales to ensure}';

    protected $description = 'Fill missing translation entries for translatable models using fallback or base attribute';

    protected array $modelClasses = [
        \App\Models\Product::class,
        \App\Models\ProductCategory::class,
        \App\Models\Post::class,
    ];

    public function handle(): int
    {
        $targetLocales = (array) $this->option('locale');
        if (empty($targetLocales)) {
            $targetLocales = collect(\App\Models\Language::query()->where('is_active', 1)->pluck('code'))->all();
        }
        if (empty($targetLocales)) {
            $this->warn('No active languages found; aborting.');

            return Command::SUCCESS;
        }

        $fallback = config('app.fallback_locale');
        $selectedModels = $this->option('model');
        $models = empty($selectedModels)
            ? $this->modelClasses
            : array_values(array_filter($this->modelClasses, function ($cls) use ($selectedModels) {
                return in_array(class_basename($cls), $selectedModels, true);
            }));

        foreach ($models as $class) {
            $instance = new $class();
            if (! property_exists($instance, 'translatable')) {
                $this->line("Skipping $class (no translatable property)");

                continue;
            }
            $total = $class::query()->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();
            $updated = 0;
            $translatable = [];
            if (property_exists($instance, 'translatable')) {
                $ref = new \ReflectionClass($instance);
                if ($ref->hasProperty('translatable')) {
                    $prop = $ref->getProperty('translatable');
                    $prop->setAccessible(true);
                    $value = $prop->getValue($instance);
                    if (is_array($value)) {
                        $translatable = $value;
                    }
                }
            }
            if (empty($translatable)) {
                $this->line('No translatable fields defined on ' . class_basename($class));

                continue;
            }
            $class::chunkById(200, function ($chunk) use (&$updated, $targetLocales, $fallback, $bar, $translatable) {
                foreach ($chunk as $model) {
                    $dirty = false;
                    foreach ($translatable as $field) {
                        $translationsAttr = $field . '_translations';
                        $translations = $model->{$translationsAttr} ?? [];
                        if (! is_array($translations)) {
                            $translations = [];
                        }
                        foreach ($targetLocales as $loc) {
                            if (! array_key_exists($loc, $translations) || $translations[$loc] === '') {
                                // determine source value: fallback translation > raw attribute
                                $source = $translations[$fallback] ?? $model->getRawOriginal($field) ?? $model->$field;
                                if ($source !== null && $source !== '') {
                                    $translations[$loc] = $source;
                                    $dirty = true;
                                }
                            }
                        }
                        if ($dirty) {
                            $model->{$translationsAttr} = $translations;
                        }
                    }
                    if ($dirty) {
                        $model->save();
                        $updated++;
                    }
                    $bar->advance();
                }
            });
            $bar->finish();
            $this->newLine();
            $this->info(class_basename($class) . ": updated $updated records");
        }

        $this->info('Done.');

        return Command::SUCCESS;
    }
}
