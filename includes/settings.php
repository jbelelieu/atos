<?php

$atosSettings = [
    // What is your database file called.
    'DATABASE_FILE_NAME' => 'pm.sqlite3',
    
    // What should the default collection for a new project be called?
    'UNORGANIZED_NAME' => 'Unorganized',
];

/**
 * Undocumented function
 *
 * @param string $key
 * @param string|null $default
 * @return string
 */
function getSetting(AsosSettings $key, $default = null)
{
    switch ($key) {
        case AsosSettings::DATABASE_FILE_NAME:
            return returnSetting('DATABASE_FILE_NAME', $default);
        case AsosSettings::UNORGANIZED_NAME:
            return returnSetting('UNORGANIZED_NAME', $default);
        default:
            return $default;
    }
}

/**
 * @param string $settingKey
 * @param [type] $default
 * @return void
 */
function returnSetting(string $settingKey, $default = null)
{
    global $atosSettings;

    return array_key_exists($settingKey, $atosSettings)
        ? $atosSettings[$settingKey]
        : $default;
}

/**
 * Available settings
 */
enum AsosSettings: string
{
    case UNORGANIZED_NAME = 'UNORGANIZED_NAME';
    case DATABASE_FILE_NAME = 'DATABASE_FILE_NAME';
}
