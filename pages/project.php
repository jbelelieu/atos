<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * This file controls all things collections and stories.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

if (empty($_GET['id'])) {
    redirect(
        '/',
        null,
        null,
        language('error_invalid_id', 'You need to provide a valid ID')
    );
}

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Actions
 *
 */

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteCollection':
            deleteCollection($_GET);
            break;
        case 'deleteStory':
            deleteStory($_GET);
            break;
        case 'updateStoryStatus':
            updateStoryStatus($_GET, 4);
            break;
        case 'makeCurrentCollection':
            makeCurrentCollection($_GET);
            break;
        case 'shiftCollection':
            shiftCollection($_GET);
            break;
        default:
            redirect('/project', $_GET['id'], null, 'Unknown action');
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createCollection':
            createCollection($_POST);
            break;
        case 'createStory':
            createStory($_POST);
            break;
        case 'updateStories':
            updateStories($_POST);
            break;
        default:
            redirect('/project', $_GET['id'], null, 'Unknown action');
    }
}

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Down and Dirty
 *
 */

$project = getProjectById($_GET['id']);
$storyStatuses = getStoryStatuses();
$hourTypeResults = getRateTypes();
$storyTypeResults = getStoryTypes();
$collectionResults = getCollectionByProject($_GET['id']);

// Convienience feature for easier navigation.
$_SESSION["viewingProject"] = $_GET['id'];
$_SESSION["viewingProjectName"] = $project['title'];

// Story type select for the story create form.
$storyTypeSelect = '';

foreach ($storyTypeResults as $aType) {
    $storyTypeSelect .= '<option value="' . $aType['id'] . '">' . $aType['title'] . '</option>';
}

// Rate type select for the story create form.
$hourTypeSelect = '';

foreach ($hourTypeResults as $aType) {
    $hourTypeSelect .= '<option value="' . $aType['id'] . '">' . $aType['title'] . ' (' . formatMoney($aType['rate']) . ')</option>';
}

// Build the collections dropdown for the story create form.
$collectionSelect = '';
$collectionArray = [];

foreach ($collectionResults as $aCollection) {
    array_push($collectionArray, $aCollection['id']);

    $collectionSelect .= '<option value="' . $aCollection['id'] . '">' . $aCollection['title'] . '</option>';
}

// Build the collections list.
$collections = '';
$totalCollections = sizeof($collectionResults);
$at = 0;

// TODO: standard linking
foreach ($collectionResults as $row) {
    $at++;

    if ($at === $totalCollections) {
        continue;
    }

    $delete = $row['id'] > 1 ? "<a href=\"/project?action=deleteCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . putIcon('fi-sr-trash') . "</a>" : '';

    $update = ($at > 1 && !$row['is_project_default'])
        ? "<a title=\"" . language('make_active_collection', 'Make Active Collection') . "\" href=\"/project?action=makeCurrentCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . $row['title'] . "</a>"
        : $row['title'];

    $collections .= "<div><span>" . $update . "</span>" . $delete . "</div>";
}

// Render the collections, split between open, billable, and unorganized.
$collectionsRendered = '';
$collectionCount = 0;

foreach ($collectionResults as $aCollection) {
    $collectionCount++;

    $hours = 0;

    $isProjectDefault = (bool) $aCollection['is_project_default'];

    $tripFlag = $collectionCount > 1 && !$isProjectDefault;

    $renderedOpenStories = '';
    $openStories = getStoriesInCollection($aCollection['id']);
    foreach ($openStories as $row) {
        $renderedOpenStories .= template(
            'admin/snippets/collection_table_open_entry',
            [
                'deleteLink' => buildLink(
                    '/project',
                    [
                        'action' => 'deleteStory',
                        'id' => $row['id'],
                        'project_id' => $project['id'],
                    ]
                ),
                'options' => buildStoryOptions($_GET['id'], $row['id'], false, $row['status']),
                'story' => $row,
            ],
            true
        );
    }

    $renderedOtherStories = '';
    $otherStories = getStoriesInCollection(
        $aCollection['id'],
        false,
        'ended_at ASC, status ASC',
        true
    );
    foreach ($otherStories as $row) {
        $adjustedColor = adjustBrightness($row['status_color'], -10);

        if ($row['status'] == 2) {
            $endedAt = formatDate($row['ended_at'], 'Y-m-d');
            $label = "<div class=\"bubble greenBubble\">" . $row['show_id'] . "</div>";
        } elseif ($row['status'] == 4) {
            $endedAt = formatDate($row['ended_at'], 'Y-m-d');
            $label = "<div class=\"bubble redBubble\">" . $row['show_id'] . "</div>";
        } elseif ($row['status'] == 3) {
            $endedAt = formatDate($row['ended_at'], 'Y-m-d');
            $label = "<div class=\"bubble blueBubble\">" . $row['show_id'] . "</div>";
        } else {
            $label = "<div class=\"bubble \" style=\"background-color:" . $row['status_color'] . "\">" . $row['show_id'] . "</div>";
        }

        $hours += (int) $row['hours'];

        $hourSelect = '<select name="story[' . $row['id'] . '][rates]">';
        foreach ($hourTypeResults as $aType) {
            $hourSelect .= ($aType['id'] === $row['rate_type'])
            ? '<option value="' . $aType['id'] . '" selected="selected">' . $aType['title'] . '</option>'
            : '<option value="' . $aType['id'] . '">' . $aType['title'] . '</option>';
        }
        $hourSelect .= '</select>';

        $typeSelect = '<select name="story[' . $row['id'] . '][types]">';
        foreach ($storyTypeResults as $aStoryType) {
            $typeSelect .= ($aStoryType['id'] === $row['type'])
            ? '<option value="' . $aStoryType['id'] . '" selected="selected">' . $aStoryType['title'] . '</option>'
            : '<option value="' . $aStoryType['id'] . '">' . $aStoryType['title'] . '</option>';
        }
        $typeSelect .= '</select>';

        $renderedOtherStories .= template(
            'admin/snippets/collection_table_other_entry',
            [
                'endedAt' => $endedAt,
                'hours' => $row['hours'],
                'hourSelect' => $hourSelect,
                'label' => $label,
                'options' => buildStoryOptions($_GET['id'], $row['id'], false, $row['status']),
                'project' => $project,
                'story' => $row,
                'typeSelect' => $typeSelect,
            ],
            true
        );
    }

    $collectionsRendered .= template(
        'admin/snippets/collection',
        [
            'collection' => $aCollection,
            'hours' => $hours,
            'isProjectDefault' => $isProjectDefault,
            'openStories' => $renderedOpenStories,
            'otherStories' => $renderedOtherStories,
            'tripFlag' => $tripFlag,
        ],
        true
    );
}

// Render the entire page.
echo template(
    'admin/projects',
    [
        '_metaTitle' => $project['title'] . ' (ATOS)',
        'collections' => $collections,
        'collectionsRendered' => $collectionsRendered,
        'collectionSelect' => $collectionSelect,
        'hourTypeSelect' => $hourTypeSelect,
        'nextId' => generateTicketId($_GET['id']),
        'project' => $project,
        'storyTypeSelect' => $storyTypeSelect,
        'totalCollections' => $totalCollections,
    ]
);
exit;

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Functions
 *
 */

function buildStoryOptions(
    int $projectId,
    $itemId,
    bool $skipMoveCollection = false,
    $skipStatusId = 0
): string {
    global $storyStatuses;

    $options = (!$skipMoveCollection)
        ? "<a title=\"" . language('move_collections', 'Move Collections') . "\" href=\"/project?action=shiftCollection&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon('fi-sr-undo') . "</a>"
        : '';

    foreach ($storyStatuses as $aStatus) {
        if ($skipStatusId && $skipStatusId == $aStatus['id']) {
            continue;
        }

        $options .= "<a title=\"" . $aStatus['title'] . "\" href=\"/project?action=updateStoryStatus&status=" . $aStatus['id'] . "&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon($aStatus['emoji'], $aStatus['color']) . "</a>";
    }

    return $options;
}

/**
 * @param array $data
 * @return void
 */
function createCollection(array $data): void
{
    global $db;

    $currentCollection = getCollectionByProject($data['project_id'], 1)[0];

    $statement = $db->prepare('
        INSERT INTO story_collection (title, project_id, goals, ended_at)
        VALUES (:title, :project_id, :goals, :ended_at)
    ');

    $statement->bindParam(':project_id', $data['project_id']);
    $statement->bindParam(':title', $data['title']);
    $statement->bindParam(':ended_at', $data['ended_at']);
    $statement->bindParam(':goals', $data['goals']);
    $statement->execute();

    makeCurrentCollection([ 'id' => $currentCollection['id'] ], false);

    redirect('/project', $data['project_id'], 'Your collection has been created.');
}

/**
 * @param array $data
 * @return void
 */
function createStory(array $data): void
{
    global $db;

    $id = (isset($data['show_id']) && (!empty($data['show_id'])))
        ? $data['show_id']
        : generateTicketId($data['project_id']);

    $statement = $db->prepare('
        INSERT INTO story (show_id, due_at, title, collection, rate_type, type, status)
        VALUES (:show_id, :due_at, :title, :collection, :rate_type, :type, 1)
    ');

    $statement->bindParam(':show_id', $id);
    $statement->bindParam(':due_at', $data['due_at']);
    $statement->bindParam(':title', $data['title']);
    $statement->bindParam(':collection', $data['collection']);
    $statement->bindParam(':rate_type', $data['rate_type']);
    $statement->bindParam(':type', $data['type']);

    $statement->execute();

    redirect('/project', $data['project_id'], 'Your new story has been created as ' . $id);
}

/**
 * null should probably be "unorganized" but let's roll
 * the dice and assume no one's gonna delete the ID
 * from the database directly.
 *
 * @param array $data
 * @return void
 */
function deleteCollection(array $data): void
{
    global $db;

    $collection = getCollectionById($data['id']);

    $isDefault = (bool) $collection['is_project_default'];
    if ($isDefault) {
        redirect(
            '/project',
            $data['project_id'],
            '',
            'You cannot delete the "Unorganized" collection from a project.'
        );
    }

    $statement = $db->prepare('
        DELETE FROM story_collection WHERE id = :id
    ');
    $statement->bindParam(':id', $data['id']);
    $statement->execute();

    redirect(
        '/project',
        $data['project_id'],
        'Your collection has been deleted.'
    );
}

/**
 * @param array $data
 * @return void
 */
function deleteStory(array $data): void
{
    global $db;

    $statement = $db->prepare('
        DELETE FROM story WHERE id = :id
    ');

    $statement->bindParam(':id', $data['id']);

    $statement->execute();

    redirect('/project', $data['project_id'], 'Your story has been deleted.');
}

/**
 * @param integer $projectId
 * @return string
 */
function generateTicketId(int $projectId): string
{
    $project = getProjectById($projectId);

    $totalStoriesInProject = getNextStoryNumberForProject($projectId);

    $id = $project['code'] . '-' . $totalStoriesInProject;

    return $project['code'] . '-' . $totalStoriesInProject;
}

/**
 * @param array $data
 * @param bool $redirect
 * @return void
 */
function makeCurrentCollection(array $data, bool $redirect = true): void
{
    global $db;

    $statement = $db->prepare('
        UPDATE story_collection
        SET created_at = :date
        WHERE id = :id
    ');

    $statement->bindParam(':id', $data['id']);
    $statement->bindParam(':date', date('Y-m-d H:i:s'));

    $statement->execute();

    if ($redirect) {
        redirect('/project', $data['project_id'], 'Now working with a new collection.');
    }
}

/**
 * @param array $data
 * @return void
 */
function shiftCollection(array $data): void
{
    global $db;

    $story = getStory($data['id']);

    // Default to Open
    if ($story['collection'] === 1) {
        $useCollection = getLatestCollectionForProject($data['project_id']);
        $moveTo = $useCollection['id'];
    }
    // Move to default collection
    else {
        $useCollection = getDefaultCollectionForProject($data['project_id']);
        $moveTo = $useCollection['id'];
    }

    $msg = 'Your story is now part of the "' . $useCollection['title'] . '" collection.';

    $statement = $db->prepare('
        UPDATE story
        SET collection = :collection
        WHERE id = :id
    ');
    $statement->bindParam(':collection', $moveTo);
    $statement->bindParam(':id', $data['id']);
    $statement->execute();

    redirect('/project', $data['project_id'], $msg);
}

/**
 * @param array $data
 * @return void
 */
function updateStories(array $data): void
{
    global $db;

    foreach ($data['story'] as $storyId => $aStory) {
        $statement = $db->prepare('
            UPDATE
                story
            SET
                hours = :hours,
                type = :type,
                rate_type = :rate_type,
                title = :title,
                ended_at = :ended_at
            WHERE
                id = :id
        ');
        $statement->bindParam(':ended_at', $aStory['ended_at']);
        $statement->bindParam(':hours', $aStory['hours']);
        $statement->bindParam(':type', $aStory['types']);
        $statement->bindParam(':rate_type', $aStory['rates']);
        $statement->bindParam(':title', $aStory['title']);
        $statement->bindParam(':id', $storyId);
        $statement->execute();
    }

    redirect('/project', $data['project_id'], 'Your story has been deleted.');
}

/**
 * @param array $data
 * @return void
 */
function updateStoryStatus(array $data): void
{
    global $db;

    if (!isset($data['status']) || empty($data['status'])) {
        redirect('/project', $data['project_id'], null, 'Invalid status received.');
    }

    $status = getStoryStatusById($data['status']);
    $story = getStory($data['id']);

    $hours = 0;
    if ((int) $story['hours'] > 0) {
        $hours = $story['hours'];
    } elseif ((bool) $status['is_billable_state']) {
        $hours = 1;
    }
    
    $statement = $db->prepare('
        UPDATE story
        SET status = :status, ended_at = :ended_at, hours = :hours
        WHERE id = :id
    ');

    $statement->bindParam(':status', $data['status']);
    $statement->bindParam(':hours', $hours);
    $statement->bindParam(':id', $data['id']);
    $statement->bindParam(':ended_at', date('Y-m-d H:i:s'));
    $statement->execute();

    $status = getStoryStatusById($data['status']);

    redirect(
        '/project',
        $data['project_id'],
        'Your status of your story has been changed to "' . $status['title'] . '".'
    );
}
