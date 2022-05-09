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

class AtosSettings
{
    private $settings = [];

    public function __construct()
    {
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
            
            $this->settings = require ATOS_HOME_DIR . '/settings.env.php';
        } catch (Exception $e) {
            systemError($e->getMessage());
        }
    }

    /**
     * @param string $settingKey
     * @param [type] $default
     */
    public function returnSetting(string $settingKey, $default = null)
    {
        return array_key_exists($settingKey, $this->settings)
            ? $this->settings[$settingKey]
            : $default;
    }
}

/**
 * @param string $key
 * @param [type] $default
 */
function getSetting(string $key, $default = null)
{
    $settings = new AtosSettings();

    switch ($key) {
        case 'DATABASE_FILE_NAME':
            return $settings->returnSetting('DATABASE_FILE_NAME', $default);
        case 'EST_TAXES_ADD_SAFETY_BUFFER':
            return (int) $settings->returnSetting('EST_TAXES_ADD_SAFETY_BUFFER', $default);
        case 'LOGO_FILE':
            return $settings->returnSetting('LOGO_FILE', $default);
        case 'INVOICE_DUE_DATE_IN_DAYS':
            return (int) $settings->returnSetting('INVOICE_DUE_DATE_IN_DAYS', $default);
        case 'INVOICE_ORDER_BY_DATE_COMPLETED':
            return $settings->returnSetting('INVOICE_ORDER_BY_DATE_COMPLETED', $default);
        case 'UNORGANIZED_NAME':
            return $settings->returnSetting('UNORGANIZED_NAME', $default);
        default:
            return $default;
    }
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

        require_once ATOS_HOME_DIR . '/' . $fileName;
    }
);

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Database
 *
 */

// Connect to the database.
$dbFile = ATOS_HOME_DIR . '/db/' . getSetting('DATABASE_FILE_NAME', 'atos.sqlite3');
if (!file_exists($dbFile)) {
    systemError('Database file not found.');
}
$db = new \PDO("sqlite:$dbFile");
$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

// Migration checks
try {
    $isInstalledStmt = $db->prepare("
        SELECT name
        FROM sqlite_master
        WHERE type='table' AND name='story' 
    ");
    $isInstalledStmt->execute();
    $installed = $isInstalledStmt->fetch();

    if (!$installed || empty($installed['name'])) {
        foreach (scandir(ATOS_HOME_DIR . '/db/migrations') as $aFile) {
            if ($aFile === '.' || $aFile === '..') { continue; }
            
            $migrations = file_get_contents(ATOS_HOME_DIR . '/db/migrations/' . $aFile);
            $db->exec($migrations);
        }
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
