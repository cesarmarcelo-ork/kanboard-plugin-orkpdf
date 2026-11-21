<?php
// Minimal stand-in for Kanboard's t() in isolated plugin tests.
if (!function_exists('t')) {
    function t($key, ...$args) {
        static $catalog = null;
        if ($catalog === null) {
            $locale = getenv('ORKPDF_TEST_LOCALE') ?: 'pt_BR';
            if (!in_array($locale, array('pt_BR', 'en_US'), true)) throw new RuntimeException('Unsupported test locale');
            $catalog = require __DIR__.'/../Locale/'.$locale.'/translations.php';
        }
        $message = (string)($catalog[$key] ?? $key);
        // Emulate Kanboard's sprintf-style interpolation. A missing argument
        // for a translated %s must fail locally rather than in production.
        return $args ? sprintf($message, ...$args) : sprintf($message);
    }
}
