<?php

$ATOS_HOME_DIR = __DIR__ . '/..';

try {
    if (!file_exists($ATOS_HOME_DIR . '/SystemSettings.env.php')) {
        $worked = @rename(
            $ATOS_HOME_DIR . '/SystemSettings.sample.php',
            $ATOS_HOME_DIR . '/SystemSettings.env.php'
        );
        if (!$worked) {
            systemError('We could not rename <u>SystemSettings.sample.php</u> to <u>SystemSettings.env.php</u>. Please do that and try again.');
        }
    }
    
    $atosSettings = require $ATOS_HOME_DIR . '/SystemSettings.env.php';
} catch (Exception $e) {
    systemError($e->getMessage());
}

/**
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
        case AsosSettings::INVOICE_ORDER_BY_DATE_COMPLETED:
            return returnSetting('INVOICE_ORDER_BY_DATE_COMPLETED', $default);
        case AsosSettings::UNORGANIZED_NAME:
            return returnSetting('UNORGANIZED_NAME', $default);
        default:
            return $default;
    }
}

/**
 * @param string $settingKey
 * @param $default
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
    case INVOICE_ORDER_BY_DATE_COMPLETED = 'INVOICE_ORDER_BY_DATE_COMPLETED';
    case UNORGANIZED_NAME = 'UNORGANIZED_NAME';
}
