<?php

$atosSettings = [
    // What is your database file called.
    'DATABASE_FILE_NAME' => 'pm.sqlite3',
    
    // How many days from issuances of the invoice should payment
    // be due? Set to zero (0) to not have a due date.
    'INVOICE_DUE_DATE_IN_DAYS' => 14,

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
        case AsosSettings::INVOICE_DUE_DATE_IN_DAYS:
            return (int) returnSetting('INVOICE_DUE_DATE_IN_DAYS', $default);
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
    case DATABASE_FILE_NAME = 'DATABASE_FILE_NAME';
    case INVOICE_DUE_DATE_IN_DAYS = 'INVOICE_DUE_DATE_IN_DAYS';
    case UNORGANIZED_NAME = 'UNORGANIZED_NAME';
}
