<?php

namespace App\Helpers;

class OriginalLanguageHelper
{
    /**
     * Create a new class instance.
     */
    public static function getName(string $code): string
    {
        $languages = [
            'en' => 'English',
            'id' => 'Indonesian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese',
            'fr' => 'French',
            'de' => 'German',
            'es' => 'Spanish',
            'it' => 'Italian',
            'hi' => 'Hindi',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'ar' => 'Arabic',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'xx' => 'Unknown',
        ];

        return $languages[$code] ?? strtoupper($code);
    }
}
