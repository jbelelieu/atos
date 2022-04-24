<?php

$atosSettings = [
    'UNORGANIZED_NAME' => 'Unorganized',
];

/**
 * Undocumented function
 *
 * @param string $key
 * @param string|null $default
 * @return string
 */
function getSetting(AsosSettings $key, string $default = null)
{
    global $atosSettings;

    switch ($key) {
        case AsosSettings::UNORGANIZED_NAME:
            return array_key_exists('UNORGANIZED_NAME', $atosSettings)
                ? $atosSettings['UNORGANIZED_NAME']
                : $default;
            default:
                return $default;
    }
}

/**
 * Available settings
 */
enum AsosSettings: string
{
    case UNORGANIZED_NAME = 'UNORGANIZED_NAME';
}
