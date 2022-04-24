<?php

require "settings.php";
require "helpers.php";

/**
 * @link  https://renenyffenegger.ch/notes/development/web/php/snippets/sqlite/index
 */

$dbFile = 'db/pm.sqlite3';
if (!file_exists($dbFile)) {
    echo "Database file not found.";
    exit;
}

$db = new PDO("sqlite:$dbFile");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**
 * @param integer $clientId
 * @return array
 */
function getClientTotals(int $clientId): array
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
function getCompanies(): array
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
function getProjectTotals(int $projectId): array
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
 * @param boolean $isOpen
 * @return array
 */
function getStoriesInCollection(int $collectionId, bool $isOpen = true): array
{
    global $db;

    $statusQuery = $isOpen ? 'story.status = 1' : 'story.status != 1';

    $statement = $db->prepare("
        SELECT
            story.*,
            story_type.title as type_title,
            story_hour_type.title as hour_title,
            story_hour_type.rate as hour_rate,
            story_status.title as status
        FROM story
        JOIN story_type ON story.type = story_type.id
        JOIN story_status ON story.status = story_status.id
        JOIN story_hour_type ON story.rate_type = story_hour_type.id
        WHERE
            story.collection = :collection
            AND $statusQuery
        ORDER BY
            status ASC,
            status DESC,
            created_at DESC
    ");

    $statement->bindParam(':collection', $collectionId);

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @return array
 */
function getStoryTypes(): array
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
function getProjects(): array
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
function getRateTypes(): array
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
 * @param integer $storyId
 * @return array
 */
function getStoryStatusById(int $storyId): array
{
    global $db;

    $statement = $db->prepare("
        SELECT *
        FROM story_status
        WHERE id = :id
    ");

    $statement->bindParam(':id', $storyId);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * @return array
 */
function getCompanyById(int $companyId): array
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
function getCollectionById(int $id): array
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
function getCollectionByProject(int $projectId, int $limit = 5): array
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
function getProjectById(int $id): array
{
    global $db;

    $statement = $db->prepare('
        SELECT project.*, company.title as company_name
        FROM project
        JOIN company ON project.client_id = company.id
        WHERE project.id=:id
    ');

    $statement->bindParam(':id', $id);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * @param integer $id
 * @return int
 */
function getNextStoryNumberForProject(int $id): int
{
    global $db;

    $statement = $db->prepare('
        SELECT story.show_id
        FROM story
        JOIN story_collection ON story.collection = story_collection.id
        WHERE story_collection.project_id = :id
        ORDER BY story.id DESC
    ');

    $statement->bindParam(':id', $id);

    $statement->execute();

    $results = $statement->fetch();

    $count = explode('-', $results['show_id']);
    
    return (int) $count[1] + 1;
}

/**
 * @param integer $storyId
 * @return array
 */
function getStory(int $storyId): array
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
 * @return array
 */
function getLatestCollection(): array
{
    global $db;

    $statement = $db->prepare('
        SELECT *
        FROM story_collection
        ORDER BY created_at DESC
    ');

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}
