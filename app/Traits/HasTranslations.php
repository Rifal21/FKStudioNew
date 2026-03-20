<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    /**
     * Get a translated attribute.
     *
     * @param string $attribute
     * @param string|null $locale
     * @return mixed
     */
    public function getTranslation(string $attribute, ?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();
        $localizedAttr = $attribute . '_' . $locale;
        
        return $this->{$localizedAttr} ?? $this->{$attribute . '_id'} ?? $this->{$attribute . '_en'} ?? $this->{$attribute};
    }
}
