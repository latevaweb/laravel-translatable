<?php

namespace LaTevaWeb\Translatable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use LaTevaWeb\Translatable\Exceptions\AttributeIsNotTranslatable;

trait Translatable
{
    public static function create(array $attributes = [])
    {
        $translatables = [];

        foreach ($attributes as $field => $values) {
            if (in_array($field, self::$translatable)) {
                $translatables[$field] = $values;
                unset($attributes[$field]);
            }
        }

        $model = static::query()->create($attributes);

        foreach ($translatables as $field => $values) {
            foreach ($values as $locale => $value) {
                $model->setTranslation($field, $locale, $value);
            }
        }

        $model->save();

        return $model;
    }

    public function getAttributeValue($field)
    {
        if (! $this->isTranslatableAttribute($field)) {
            return parent::getAttributeValue($field);
        }

        return $this->getTranslation($field, $this->getLocale());
    }

    public function setAttribute($field, $value)
    {
        if (! $this->isTranslatableAttribute($field) || is_array($value)) {
            return parent::setAttribute($field, $value);
        }

        return $this->setTranslation($field, $this->getLocale(), $value);
    }

    public function isTranslatableAttribute(string $field) : bool
    {
        return in_array($field, $this->getTranslatableAttributes());
    }

    protected function getLocale() : string
    {
        return Config::get('app.locale');
    }

    public function getTranslatableAttributes(): array
    {
        return is_array(self::$translatable) ? self::$translatable : [];
    }

    public function getTranslation(string $field, string $locale, bool $useFallbackLocale = true): ?string
    {
        $locale = $this->normalizeLocale($field, $locale, $useFallbackLocale);

        $translations = $this->getTranslations($field)->all();

        $translation = $translations[$locale] ?? '';

        if ($this->hasGetMutator($field)) {
            return $this->mutateAttribute($field, $translation);
        }

        return $translation;
    }

    public function setTranslation(string $field, string $locale, $content): self
    {
        $this->guardAgainstNonTranslatableAttribute($field);

        $translation = $this->translations()
                            ->where('field', $field)
                            ->where('locale', $locale)
                            ->first();

        if (! empty($translation)) {
            $translation->content = $content;
            $translation->save();
        } else {
            $this->translations()->create([
                'field' => $field,
                'locale' => $locale,
                'content' => $content,
            ]);
        }

        return $this;
    }

    protected function guardAgainstNonTranslatableAttribute(string $key)
    {
        if (! $this->isTranslatableAttribute($key)) {
            throw AttributeIsNotTranslatable::make($key, $this);
        }
    }

    public function translations(): MorphToMany
    {
        return $this->morphToMany(config('translatable.models.translation'), 'translatable');
    }

    protected function normalizeLocale(string $field, string $locale, bool $useFallbackLocale) : string
    {
        if (in_array($locale, $this->getTranslatedLocales($field)->all())) {
            return $locale;
        }
        if (! $useFallbackLocale) {
            return $locale;
        }
        if (! is_null($fallbackLocale = Config::get('app.fallback_locale'))) {
            return $fallbackLocale;
        }

        return $locale;
    }

    /**
     * Returns a collection with all locales [ locales ].
     *
     * @param string $field
     * @return Collection
     */
    public function getTranslatedLocales(string $field): Collection
    {
        return $this->getTranslations($field)->keys();
    }

    /**
     * Returns a collection with [ locale => content ].
     *
     * @param string|null $field
     * @return Collection
     */
    public function getTranslations(string $field = null): Collection
    {
        return $this->translations()
                    ->where('field', $field)
                    ->select('locale', 'content')
                    ->get()
                    ->keyBy('locale')
                    ->pluck('content', 'locale');
    }
}
