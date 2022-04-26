<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * System setting loader and actions.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

define('ATOS_HOME_DIR', __DIR__ . '/..');

// Try to load the system settings.
try {
    if (!file_exists(ATOS_HOME_DIR . '/settings.env.php')) {
        $worked = @rename(
            ATOS_HOME_DIR . '/settings.sample.php',
            ATOS_HOME_DIR . '/settings.env.php'
        );
        if (!$worked) {
            systemError('We could not rename <u>settings.sample.php</u> to <u>settings.env.php</u>. Please do that and try again.');
        }
    }
    
    $atosSettings = require ATOS_HOME_DIR . '/settings.env.php';
} catch (Exception $e) {
    systemError($e->getMessage());
}

// Autoloader feature to make "use" available.
spl_autoload_register(
    function ($className) {
        $className = str_replace("_", "\\", $className);
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        require ATOS_HOME_DIR . '/' . $fileName;
    }
);

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   System setting interactions
 *
 */

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
