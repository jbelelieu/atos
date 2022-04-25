<?php

require "helpers.php";
require "system.php";

session_start();

$dbFile = 'db/' . getSetting(AsosSettings::DATABASE_FILE_NAME, 'atos.sqlite3');
if (!file_exists($dbFile)) {
    echo "Database file not found.";
    exit;
}

$db = new PDO("sqlite:$dbFile");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//
try {
    $isInstalledStmt = $db->prepare("
        SELECT name FROM sqlite_master WHERE type='table' AND name='story' 
    ");
    $isInstalledStmt->execute();
    $installed = $isInstalledStmt->fetch(PDO::FETCH_ASSOC);

    if (!$installed || empty($installed['name'])) {
        $migrations = file_get_contents('./db/migrations.sql');
        $db->exec($migrations);
    }
} catch (\Exception $e) {
    systemError($e->getMessage());
}


/**
 * @param integer $clientId
 * @return array
 */
function getCompanyTotals(int $clientId)
{
    global $db;

    $statement = $db->prepare("
        SELECT
            COALESCE(SUM(story.hours), 0) as hours,
            COALESCE(SUM(story.hours * story_hour_type.rate), 0) as total
        FROM
            project
        JOIN
            story_collection
            ON project.id = story_collection.project_id
        JOIN
            story
            ON story_collection.id = story.collection
        JOIN
            story_hour_type
            ON story_hour_type.id = story.rate_type
        WHERE
            client_id = :client_id
            AND story.status != 1;
    ");

    $statement->bindParam(':client_id', $clientId);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * @return array
 */
function getCompanies()
{
    global $db;

    $statement = $db->prepare("
        SELECT *
        FROM company
    ");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param integer $projectId
 * @return array
 */
function getProjectTotals(int $projectId)
{
    global $db;

    $statement = $db->prepare("
        SELECT
            COALESCE(SUM(story.hours), 0) as hours,
            COALESCE(SUM(story.hours * story_hour_type.rate), 0) as total
        FROM
            project
        JOIN
            story_collection
            ON project.id = story_collection.project_id
        JOIN
            story
            ON story_collection.id = story.collection
        JOIN
            story_hour_type
            ON story_hour_type.id = story.rate_type
        WHERE
            project.id = :project_id
            AND story.status != 1;
    ");

    $statement->bindParam(':project_id', $projectId);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}


/**
 * @param integer $collectionId
 * @param boolean $getAll If set to false, only billable items will be returned.
 * @param string $order
 * @return array
 */
function getStoriesInCollection(
    int $collectionId,
    bool $getAll = true,
    string $order = 'status ASC, created_at DESC'
) {
    global $db;

    $statusQuery = (!$getAll) ? 'AND story_status.is_billable_state = true' : '';

    $statement = $db->prepare("
        SELECT
            story.*,
            story_type.title as type_title,
            story_hour_type.title as hour_title,
            story_hour_type.rate as hour_rate,
            story_status.title as status_title,
            story_status.is_complete_state,
            story_status.title as status_id,
            story_status.emoji as status_emoji,
            story_status.color as status_color
        FROM story
        JOIN story_type ON story.type = story_type.id
        JOIN story_status ON story.status = story_status.id
        JOIN story_hour_type ON story.rate_type = story_hour_type.id
        WHERE
            story.collection = :collection
            $statusQuery
        ORDER BY
            $order
    ");

    $statement->bindParam(':collection', $collectionId);

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @return array
 */
function getStoryStatuses()
{
    global $db;

    $statement = $db->prepare("
        SELECT * FROM story_status
    ");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @return array
 */
function getStoryTypes()
{
    global $db;

    $statement = $db->prepare("
        SELECT * FROM story_type
    ");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @return array
 */
function getProjects()
{
    global $db;

    $statement = $db->prepare("
        SELECT
            project.*,
            company.title as company_name
        FROM project
        JOIN company ON project.client_id = company.id
    ");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @return array
 */
function getRateTypes()
{
    global $db;

    $statement = $db->prepare("
        SELECT *
        FROM story_hour_type
        ORDER BY title DESC
    ");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param integer $storyStatusId
 * @return array
 */
function getStoryStatusById(int $storyStatusId)
{
    global $db;

    $statement = $db->prepare("
        SELECT *
        FROM story_status
        WHERE id = :id
    ");

    $statement->bindParam(':id', $storyStatusId);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * @return array
 */
function getCompanyById(int $companyId)
{
    global $db;

    $statement = $db->prepare("
        SELECT *
        FROM company
        WHERE id = :id
    ");

    $statement->bindParam(':id', $companyId);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * @param int $id
 * @return array
 */
function getCollectionById(int $id)
{
    global $db;

    $statement = $db->prepare("
        SELECT *
        FROM story_collection
        WHERE id = :id
    ");

    $statement->bindParam(':id', $id);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * @param integer $projectId
 * @return array
 */
function getCollectionByProject(int $projectId, int $limit = 5)
{
    global $db;

    $statement = $db->prepare("
        SELECT *
        FROM story_collection
        WHERE project_id = :id
        ORDER BY created_at DESC
        LIMIT :limit
    ");

    $statement->bindParam(':id', $projectId);
    $statement->bindParam(':limit', $limit);

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param int $id
 * @return array
 */
function getProjectById(int $id)
{
    global $db;

    try {
        $statement = $db->prepare('
        SELECT project.*, company.title as company_name
        FROM project
        JOIN company ON project.client_id = company.id
        WHERE project.id=:id
    ');

        $statement->bindParam(':id', $id);

        $statement->execute();

        $res = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            redirect('index.php', null, null, 'Something went wrong finding that project.');
        }

        return $res;
    } catch (\PDOException $e) {
        redirect('index.php', null, null, 'Something went wrong finding that project.');
    }
}

/**
 * @param integer $id
 * @return int
 */
function getNextStoryNumberForProject(int $id): int
{
    global $db;

    try {
        $statement = $db->prepare('
            SELECT story.show_id
            FROM story
            JOIN story_collection ON story.collection = story_collection.id
            WHERE story_collection.project_id = :id
            ORDER BY story.id DESC
        ');

        $statement->bindParam(':id', $id);

        $statement->execute();

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$results) {
            return 1;
        }
    
        $count = explode('-', $results['show_id']);
        
        return (int) $count[1] + 1;
    } catch (Exception $e) {
        return 1;
    }
}

/**
 * @param integer $storyId
 * @return array
 */
function getStory(int $storyId)
{
    global $db;

    $statement = $db->prepare('
        SELECT *
        FROM story
        WHERE id = :id
    ');

    $statement->bindParam(':id', $storyId);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * @param integer $projectId
 * @return void
 */
function getDefaultCollectionForProject(int $projectId)
{
    global $db;

    $statement = $db->prepare('
        SELECT *
        FROM story_collection
        WHERE
            project_id = :project_id
            AND is_project_default = true
        ORDER BY created_at DESC
        LIMIT 1
    ');

    $statement->bindParam(':project_id', $projectId);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}


/**
 * @param integer $projectId
 * @return void
 */
function getLatestCollectionForProject(int $projectId)
{
    global $db;

    $statement = $db->prepare('
        SELECT *
        FROM story_collection
        WHERE project_id = :project_id
        ORDER BY created_at DESC
        LIMIT 1
    ');

    $statement->bindParam(':project_id', $projectId);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}
