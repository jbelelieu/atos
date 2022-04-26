<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Application loader: system settings, DB, languages, etc.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

session_start();

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   System constants
 *
 */

define('ATOS_HOME_DIR', __DIR__ . '/..');


/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Helper components and shared functionality
 *
 */

require ATOS_HOME_DIR . "/includes/helpers.php";

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   System setting interactions
 *
 */

enum AtosSettings: string
{
    case DATABASE_FILE_NAME = 'DATABASE_FILE_NAME';
    case INVOICE_DUE_DATE_IN_DAYS = 'INVOICE_DUE_DATE_IN_DAYS';
    case INVOICE_ORDER_BY_DATE_COMPLETED = 'INVOICE_ORDER_BY_DATE_COMPLETED';
    case UNORGANIZED_NAME = 'UNORGANIZED_NAME';
}

/**
 * @param string $key
 * @param string|null $default
 * @return string
 */
function getSetting(AtosSettings $key, $default = null)
{
    switch ($key) {
        case AtosSettings::DATABASE_FILE_NAME:
            return returnSetting('DATABASE_FILE_NAME', $default);
        case AtosSettings::INVOICE_DUE_DATE_IN_DAYS:
            return (int) returnSetting('INVOICE_DUE_DATE_IN_DAYS', $default);
        case AtosSettings::INVOICE_ORDER_BY_DATE_COMPLETED:
            return returnSetting('INVOICE_ORDER_BY_DATE_COMPLETED', $default);
        case AtosSettings::UNORGANIZED_NAME:
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
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   System settings loader
 *
 */

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

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Basic autoloading
 *
 */
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
 *   Database
 *
 */

// Connect to the database.
$dbFile = ATOS_HOME_DIR . '/db/' . getSetting(\AtosSettings::DATABASE_FILE_NAME, 'atos.sqlite3');
if (!file_exists($dbFile)) {
    systemError('Database file not found.');
}
$db = new \PDO("sqlite:$dbFile");
$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

// Migration checks
try {
    $isInstalledStmt = $db->prepare("
        SELECT name FROM sqlite_master WHERE type='table' AND name='story' 
    ");
    $isInstalledStmt->execute();
    $installed = $isInstalledStmt->fetch();

    if (!$installed || empty($installed['name'])) {
        $migrations = file_get_contents('./db/migrations.sql');
        $db->exec($migrations);
    }
} catch (\Exception $e) {
    systemError($e->getMessage());
}

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Language file
 *
 */

require ATOS_HOME_DIR . "/includes/language.php";

$ATOS_LANGUAGE = (file_exists(ATOS_HOME_DIR . '/includes/language.php'))
    ? require ATOS_HOME_DIR . '/includes/language.php'
    : [];
