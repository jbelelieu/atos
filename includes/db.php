<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * The primary loader file for the DB, as well as all DB
 * interactions.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

// Connect to the database.
$dbFile = ATOS_HOME_DIR . '/db/' . getSetting(\AsosSettings::DATABASE_FILE_NAME, 'atos.sqlite3');
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

// Load the language file
$ATOS_LANGUAGE = (file_exists(ATOS_HOME_DIR . '/includes/language.php'))
    ? require ATOS_HOME_DIR . '/includes/language.php'
    : [];
