<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FillMissingTranslations extends Command
{
    protected $signature = 'translations:fill {--model=* : Specific model class names (e.g. Product)} '
        . '{--locale=* : Target locales to ensure}';

    protected $description = 'Fill missing translation entries for translatable models '
        . 'using fallback or base attribute';

    protected array $modelClasses = [
        \App\Models\Product::class,
        \App\Models\ProductCategory::class,
        \App\Models\Post::class,
    ];

    public function handle(): int
    {
        $targetLocales = $this->getTargetLocales();
        if (empty($targetLocales)) {
            $this->warn('No active languages found; aborting.');
            return Command::SUCCESS;
        }

        $models = $this->getSelectedModels();
        $fallback = config('app.fallback_locale');

        foreach ($models as $class) {
            $this->processModel($class, $targetLocales, $fallback);
        }

        $this->info('Done.');
        return Command::SUCCESS;
    }

    private function getTargetLocales(): array
    {
        $targetLocales = (array) $this->option('locale');
        if (empty($targetLocales)) {
            $targetLocales = collect(\App\Models\Language::query()->where('is_active', 1)->pluck('code'))->all();
        }
        return $targetLocales;
    }

    private function getSelectedModels(): array
    {
        $selectedModels = $this->option('model');
        if (empty($selectedModels)) {
            return $this->modelClasses;
        }

        return array_values(array_filter($this->modelClasses, function ($cls) use ($selectedModels) {
            return in_array(class_basename($cls), $selectedModels, true);
        }));
    }

    private function processModel(string $class, array $targetLocales, string $fallback): void
    {
        $instance = new $class();
        if (!$this->hasTranslatableProperty($instance)) {
            $this->line("Skipping $class (no translatable property)");
            return;
        }

        $translatable = $this->getTranslatableFields($instance);
        if (empty($translatable)) {
            $this->line('No translatable fields defined on ' . class_basename($class));
            return;
        }

        $this->processModelRecords($class, $translatable, $targetLocales, $fallback);
    }

    private function hasTranslatableProperty($instance): bool
    {
        return property_exists($instance, 'translatable');
    }

    private function getTranslatableFields($instance): array
    {
        if (!property_exists($instance, 'translatable')) {
            return [];
        }

        $ref = new \ReflectionClass($instance);
        if (!$ref->hasProperty('translatable')) {
            return [];
        }

        $prop = $ref->getProperty('translatable');
        $prop->setAccessible(true);
        $value = $prop->getValue($instance);

        return is_array($value) ? $value : [];
    }

    private function processModelRecords(string $class, array $translatable, array $targetLocales, string $fallback): void
    {
        $total = $class::query()->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        $updated = 0;

        $class::chunkById(200, function ($chunk) use (&$updated, $targetLocales, $fallback, $bar, $translatable) {
            foreach ($chunk as $model) {
                if ($this->updateModelTranslations($model, $translatable, $targetLocales, $fallback)) {
                    $updated++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info(class_basename($class) . ": updated $updated records");
    }

    private function updateModelTranslations($model, array $translatable, array $targetLocales, string $fallback): bool
    {
        $dirty = false;

        foreach ($translatable as $field) {
            if ($this->updateFieldTranslations($model, $field, $targetLocales, $fallback)) {
                $dirty = true;
            }
        }

        if ($dirty) {
            $model->save();
            return true;
        }

        return false;
    }

    private function updateFieldTranslations($model, string $field, array $targetLocales, string $fallback): bool
    {
        $translationsAttr = $field . '_translations';
        $translations = $model->{$translationsAttr} ?? [];

        if (!is_array($translations)) {
            $translations = [];
        }

        $fieldDirty = false;
        foreach ($targetLocales as $loc) {
            if ($this->shouldUpdateTranslation($translations, $loc)) {
                $source = $this->getTranslationSource($translations, $model, $field, $fallback);
                if ($source !== null && $source !== '') {
                    $translations[$loc] = $source;
                    $fieldDirty = true;
                }
            }
        }

        if ($fieldDirty) {
            $model->{$translationsAttr} = $translations;
            return true;
        }

        return false;
    }

    private function shouldUpdateTranslation(array $translations, string $locale): bool
    {
        return !array_key_exists($locale, $translations) || $translations[$locale] === '';
    }

    private function getTranslationSource(array $translations, $model, string $field, string $fallback): ?string
    {
        return $translations[$fallback] ?? $model->getRawOriginal($field) ?? $model->$field;
    }
}
