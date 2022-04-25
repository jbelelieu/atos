<?php

require "includes/db.php";

if (empty($_GET['id'])) {
    redirect('index.php', null, null, 'You need to provide a valid project ID.');
}

// -------------------------------------

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
            redirect('project.php', $_GET['id'], null, 'Unknown action');
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
            redirect('project.php', $_GET['id'], null, 'Unknown action');
    }
}

// -------------------------------------

$project = getProjectById($_GET['id']);

$_SESSION["viewingProject"] = $_GET['id'];
$_SESSION["viewingProjectName"] = $project['title'];

$storyStatuses = getStoryStatuses();

// -------------------------------------

$storyTypeResults = getStoryTypes();

$storyTypeSelect = '';
foreach ($storyTypeResults as $aType) {
    $storyTypeSelect .= '<option value="' . $aType['id'] . '">' . $aType['title'] . '</option>';
}

// -------------------------------------

$hourTypeResults = getRateTypes();

$hourTypeSelect = '';
foreach ($hourTypeResults as $aType) {
    $hourTypeSelect .= '<option value="' . $aType['id'] . '">' . $aType['title'] . ' (' . formatMoney($aType['rate']) . ')</option>';
}

// -------------------------------------

$collectionResults = getCollectionByProject($_GET['id']);

$collectionSelect = '';
$inStatement = '';
$collectionArray = [];
foreach ($collectionResults as $aCollection) {
    array_push($collectionArray, $aCollection['id']);

    $collectionSelect .= '<option value="' . $aCollection['id'] . '">' . $aCollection['title'] . '</option>';

    $inStatement .= '?,';
}
$inStatement = trim($inStatement, ',');

// -------------------------------------

include "includes/header.php";

$nextId = generateTicketId($_GET['id']);

echo <<<qq
<div class="collectionsTable">

<div class="standardColumns border">
    <div>
        <div class="formBox padLess">
            <form action="project.php?id=$_GET[id]" method="post">
                <div class="threeColumns">
                    <div>
                    <label><b>Stories</b>&nbsp;&nbsp;Collection</label>
                    <select name="collection">$collectionSelect</select>
                    </div>

                    <div>
                    <label>Type</label>
                    <select name="type">$storyTypeSelect</select>
                    </div>

                    <div>
                    <label>Rate Type</label>
                    <select name="rate_type">$hourTypeSelect</select>
                    </div>
                </div>

                <div class="columns2575">
                    <div>
                        <label>Reference Number</label>
                        <input type="text" name="show_id" value="$nextId" />
                    </div>

                    <div>
                    <label>Story</label>
                    <input type="text" name="title" style="width:80%;" /> <button type="submit">Create</button>
                    </div>
                </div>

                <input type="hidden" name="project_id" value="$_GET[id]" />
                <input type="hidden" name="action" value="createStory" />
            </form>
        </div>
    </div>
    <div>
        <div class="formBox padLessRight padLessTop padLessBottom">
            <form action="project.php?id=$_GET[id]" method="post">
                <div class="twoColumns">
                    <div>
                    <label><b>Collections</b>&nbsp;&nbsp;Title</label>
                    <input type="text" name="title" />
                    </div>

                    <div style="align-self: end;">
                        <input type="hidden" name="project_id" value="$_GET[id]" />
                        <input type="hidden" name="action" value="createCollection" />
                        <button type="submit">Create</button>
                    </div>
                    <!--
                    <div>
                    <label>Ends</label>
                    <input type="date" name="ended_at" />
                    </div>
                    -->
                </div>
            </form>
        </div>

        <div id="collections" class="padLessBottom">
qq;

// TODO: Link to an overview of that collection.
$at = 0;
foreach ($collectionResults as $row) {
    $at++;

    $delete = $row['id'] > 1 ? "<a href=\"project.php?action=deleteCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . putIcon('fi-sr-trash') . "</a>" : '';

    $update = ($at > 1 && !$row['is_project_default'])
        ? "<a title=\"Make Active Collection\" href=\"project.php?action=makeCurrentCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . $row['title'] . "</a>"
        : $row['title'];

    echo "<div><span>" . $update . "</span>" . $delete . "</div>";
}

echo <<<qq
        </div>
    </div>
</div>

<div class="storyTable">
    <div>
        <h3 class=\"bubble blueBubble\">Project "$project[title]"</h3>
qq;

// -------------------------------------

$collectionCount = 0;
foreach ($collectionResults as $aCollection) {
    $collectionCount++;

    $hours = 0;

    $openStories = getStoriesInCollection($aCollection['id']);
    $otherStories = getStoriesInCollection($aCollection['id'], false, 'ended_at ASC, status ASC', true);

    $isProjectDefault = (bool) $aCollection['is_project_default'];

    $tripFlag = $collectionCount > 1 && !$isProjectDefault;
    if ($tripFlag) {
        echo "<details><summary><h4 class=\"bubble\">$aCollection[title]</h4></summary>";
    } else {
        echo "<h4 class=\"bubble\">$aCollection[title]</h4>";
    }

    if (!$isProjectDefault) {
        echo <<<qq
        <div class="clearFix"></div>
        <h4 class="bubble noMarginTop">Open Stories</h4>
qq;
    }

    echo <<<qq
        <table>
        <thead>
        <tr>
        <th width="140">ID</th>
        <th width="140">Rate Type</th>
        <th width="42"></th>
        <th width="140">Type</th>
        <th width=>Title</th>
        <th width="240"></th>
        </tr>
        </thead>
        <tbody>
qq;

    foreach ($openStories as $row) {
        $createdAt = (!empty($row['created_at'])) ? formatDate($row['created_at']) : '-';

        $options = buildStoryOptions($_GET['id'], $row['id']);

        echo "<tr>";
        echo "<td><span class=\"bubble grayBubble\">" . $row['show_id'] . "</span></td>";
        echo "<td>" . $row['hour_title'] . "</td>";
        echo "<td><div class=\"emoji_bump_sm\">" . putIcon($row['status_emoji'], $row['status_color']) . "</div></td>";
        echo "<td>" . $row['type_title'] . "</td>";
        echo "<td class=\"ellipsis\">" . $row['title'] . "</td>";
        echo "<td class=\"textRight\">$options<a onclick=\"return confirm('This will delete the story - are you sure?')\" href=\"project.php?action=deleteStory&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . putIcon('fi-sr-trash') . "</a>
    </td>";
        echo "</tr>";
    }

    // TODO: Link to an overview of that story + story notes.
    echo <<<qq
        </tbody>
        </table>
qq;

    if (!$isProjectDefault) {
        echo <<<qq
        <hr />

        <h4 class="bubble">Billable</h4>

        <form class="preventLeaving" action="project.php?id=$_GET[id]" method="post" autocomplete="on">
        <input type="hidden" name="project_id" value="$_GET[id]" />
        <input type="hidden" name="action" value="updateStories" />

        <table>
        <thead>
        <tr>
        <th width="140">ID</th>
        <th width="180">Rate Type</th>
        <th width="150">Type</th>
        <th width="42"></th>
        <th width="120">Completed</th>
        <th width="75">Hours</th>
        <th width=>Title</th>
        <th width="150"></th>
        </tr>
        </thead>
        <tbody>
qq;

        foreach ($otherStories as $row) {
            $createdAt = (!empty($row['created_at'])) ? formatDate($row['created_at']) : '-';

            $adjustedColor = adjustBrightness($row['status_color'], -10);

            if ($row['status'] == 2) {
                $status = formatDate($row['ended_at'], 'Y-m-d');
                $label = "<div class=\"bubble greenBubble\">" . $row['show_id'] . "</div>";
            } elseif ($row['status'] == 4) {
                $status = formatDate($row['ended_at'], 'Y-m-d');
                $label = "<div class=\"bubble redBubble\">" . $row['show_id'] . "</div>";
            } elseif ($row['status'] == 3) {
                $status = formatDate($row['ended_at'], 'Y-m-d');
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

            $options = buildStoryOptions($_GET['id'], $row['id'], true);

            echo "<tr>";
            echo "<td>" . $label . "</td>";
            echo "<td>" . $hourSelect . "</td>";
            echo "<td>" . $typeSelect . "</td>";
            echo "<td class=\"textCenter\"><div class=\"emoji_bump\">" . putIcon($row['status_emoji'], $row['status_color']) . "</div></td>";
            echo "<td><input type=\"date\" autocomplete=\"off\" name=\"story[$row[id]][ended_at]\" value=\"$status\" /></td>";
            echo "<td><input type=\"text\" autocomplete=\"off\" name=\"story[$row[id]][hours]\" value=\"$row[hours]\" /></td>";
            echo "<td><input type=\"text\" autocomplete=\"off\" style=\"width:100%;\" name=\"story[$row[id]][title]\" value=\"$row[title]\" /></td>";
            echo "<td class=\"textRight\"><div class=\"emoji_bump\">$options</div></td>";
            echo "</tr>";
        }

        echo <<<qq
<tr>
<td colspan="5"></td>
<td>$hours</td>
<td colspan="2">
qq;

        echo '<button type="submit">Update Stories</button> <button type="button" onClick="window.open(\'invoice.php?collection=' . $aCollection['id'] . '\')">Preview Invoice</button> <button type="button" onClick="window.location=\'invoice.php?collection=' . $aCollection['id'] . '&save=1\'">Generate & Save Invoice</button>';

        echo <<<qq
        </td>
        </tr>
        </tbody>
        </table>
qq;

        if ($tripFlag) {
            echo "</details>";
        }

        echo "</form>";
    }
}

echo <<<qq
    </div>
</div>
<script>
    $('.preventLeaving').data('serialize',$('#form').serialize());

    $(window).bind('keydown', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch (String.fromCharCode(event.which).toLowerCase()) {
        case 's':
            event.preventDefault();
            alert('ctrl-s');
            break;
        case 'f':
            event.preventDefault();
            alert('ctrl-f');
            break;
        case 'g':
            event.preventDefault();
            alert('ctrl-g');
            break;
        }
    }
}
</script>
qq;

include "includes/footer.php";
exit;

/**
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Module functions
 *
 */

function buildStoryOptions(
    int $projectId,
    $itemId,
    bool $skipMoveCollection = false
): string {
    global $storyStatuses;

    $options = (!$skipMoveCollection)
        ? "<a title=\"Move Collections\" href=\"project.php?action=shiftCollection&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon('fi-sr-undo') . "</a>"
        : '';

    foreach ($storyStatuses as $aStatus) {
        $options .= "<a title=\"" . $aStatus['title'] . "\" href=\"project.php?action=updateStoryStatus&status=" . $aStatus['id'] . "&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon($aStatus['emoji'], $aStatus['color']) . "</a>";
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

    redirect('project.php', $data['project_id'], 'Your collection has been created.');
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

    redirect('project.php', $data['project_id'], 'Your new story has been created as ' . $id);
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
        redirect('project.php', $data['project_id'], '', 'You cannot delete the "Unorganized" collection from a project.');
    }

    $statement = $db->prepare('
        DELETE FROM story_collection WHERE id = :id
    ');
    $statement->bindParam(':id', $data['id']);
    $statement->execute();

    redirect('project.php', $data['project_id'], 'Your collection has been deleted.');
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

    redirect('project.php', $data['project_id'], 'Your story has been deleted.');
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
        redirect('project.php', $data['project_id'], 'Now working with a new collection.');
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

    redirect('project.php', $data['project_id'], $msg);
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

    redirect('project.php', $data['project_id'], 'Your story has been deleted.');
}

/**
 * @param array $data
 * @return void
 */
function updateStoryStatus(array $data): void
{
    global $db;

    if (!isset($data['status']) || empty($data['status'])) {
        redirect('project.php', $data['project_id'], null, 'Invalid status received.');
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
        'project.php',
        $data['project_id'],
        'Your status of your story has been changed to "' . $status['title'] . '".'
    );
}
