<?php

namespace App\Helpers;

class OriginalLanguageHelper
{
    public static function getName(string $code): string
    {
        $languages = [
            // Asia Timur
            'en' => 'English',
            'zh' => 'Chinese (Mandarin)',
            'cn' => 'Cantonese',
            'ja' => 'Japanese',
            'ko' => 'Korean',

            // Asia Tenggara & Selatan
            'id' => 'Indonesian',
            'ms' => 'Malay',
            'tl' => 'Filipino',
            'vi' => 'Vietnamese',
            'th' => 'Thai',
            'hi' => 'Hindi',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'ml' => 'Malayalam',
            'bn' => 'Bengali',
            'ur' => 'Urdu',
            'pa' => 'Punjabi',
            'si' => 'Sinhala',
            'ne' => 'Nepali',
            'kn' => 'Kannada',

            // Eropa Barat
            'fr' => 'French',
            'de' => 'German',
            'es' => 'Spanish',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'nl' => 'Dutch',
            'sv' => 'Swedish',
            'da' => 'Danish',
            'nb' => 'Norwegian',
            'fi' => 'Finnish',
            'is' => 'Icelandic',
            'ca' => 'Catalan',
            'gl' => 'Galician',
            'eu' => 'Basque',
            'cy' => 'Welsh',
            'af' => 'Afrikaans',
            'no' => 'Norwegian',
            'ga' => 'Irish',
            'la' => 'Latin',

            // Eropa Timur & Tengah
            'ru' => 'Russian',
            'pl' => 'Polish',
            'cs' => 'Czech',
            'sk' => 'Slovak',
            'hu' => 'Hungarian',
            'ro' => 'Romanian',
            'bg' => 'Bulgarian',
            'hr' => 'Croatian',
            'sr' => 'Serbian',
            'bs' => 'Bosnian',
            'sl' => 'Slovenian',
            'uk' => 'Ukrainian',
            'be' => 'Belarusian',
            'lt' => 'Lithuanian',
            'lv' => 'Latvian',
            'et' => 'Estonian',
            'mk' => 'Macedonian',
            'sq' => 'Albanian',
            'el' => 'Greek',
            'sh' => 'Serbo-Croatian',

            // Timur Tengah & Afrika
            'ar' => 'Arabic',
            'fa' => 'Persian',
            'he' => 'Hebrew',
            'tr' => 'Turkish',
            'ka' => 'Georgian',
            'hy' => 'Armenian',
            'az' => 'Azerbaijani',
            'sw' => 'Swahili',
            'zu' => 'Zulu',

            // Asia Tengah
            'kk' => 'Kazakh',
            'uz' => 'Uzbek',
            'mn' => 'Mongolian',
            'ps' => 'Pashto',
            'ky' => 'Kyrgyz',

            // Lainnya
            'xx' => 'Unknown',
        ];

        return $languages[strtolower($code)] ?? strtoupper($code);
    }
}