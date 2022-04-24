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
        case 'markStoryShipped':
            updateStoryStatus($_GET, 3);
            break;
        case 'markStoryComplete':
            updateStoryStatus($_GET, 2);
            break;
        case 'markStoryClosed':
            updateStoryStatus($_GET, 4);
            break;
        case 'makeCurrentCollection':
            makeCurrentCollection($_GET);
            break;
        case 'shiftCollection':
            shiftCollection($_GET);
            break;
        default:
            redirect('project.php', $_GET['id'], '', 'Unknown action');
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
            echo "Unknown action.";
            exit;
    }
}

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

                <div>
                <label>Story</label>
                <input type="text" name="title" style="width:80%;" /> <button type="submit">Create</button>
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

    $delete = $row['id'] > 1 ? "<a href=\"project.php?action=deleteCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">❌</a>" : '';

    $update = ($row['id'] > 1) ? "<a href=\"project.php?action=makeCurrentCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">💼</a>" : '';

    echo "<div><span>" . $row['title'] . "</span>" . $update . $delete . "</div>";
}

echo <<<qq
        </div>
    </div>
</div>

<div class="storyTable">
    <div>
qq;

// -------------------------------------

$collectionCount = 0;
foreach ($collectionResults as $aCollection) {
    $collectionCount++;

    $hours = 0;

    $openStories = getStoriesInCollection($aCollection['id']);
    $otherStories = getStoriesInCollection($aCollection['id'], false);

    $tripFlag = $collectionCount > 1 && $aCollection['id'] !== 1;
    if ($tripFlag) {
        echo "<details><summary><h4 class=\"bubble\">$aCollection[title]</h4></summary>";
    } else {
        echo "<h4 class=\"bubble\">$aCollection[title]</h4>";
    }

    echo <<<qq
        <h5>Stories</h5>

        <table>
        <thead>
        <tr>
        <th width="140">ID</th>
        <th width="140">Rate Type</th>
        <th width="140">Type</th>
        <th width=>Title</th>
        <th width="140"></th>
        </tr>
        </thead>
        <tbody>
qq;

    foreach ($openStories as $row) {
        $createdAt = (!empty($row['created_at'])) ? formatDate($row['created_at']) : '-';

        echo "<tr>";
        echo "<td><span class=\"bubble grayBubble\"><a href=\"story.php?id=" . $row['id'] . "\">" . $row['show_id'] . "</a></span></td>";
        echo "<td>" . $row['hour_title'] . "</td>";
        echo "<td>" . $row['type_title'] . "</td>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td class=\"textRight\">
    <a href=\"project.php?action=markStoryComplete&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">✅</a>
    <a href=\"project.php?action=markStoryShipped&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">🚀</a>
    <a href=\"project.php?action=markStoryClosed&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">⛔️</a>
    <a href=\"project.php?action=shiftCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">↪️</a>
    <a onclick=\"return confirm('This will delete the story - are you sure?')\" href=\"project.php?action=deleteStory&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">❌</a>
    </td>";
        echo "</tr>";
    }

    // TODO: Link to an overview of that story.
    echo <<<qq
        </tbody>
        </table>

        <hr />

        <h5>Billable</h5>

        <form action="project.php?id=$_GET[id]" method="post">
        <input type="hidden" name="project_id" value="$_GET[id]" />
        <input type="hidden" name="action" value="updateStories" />

        <table>
        <thead>
        <tr>
        <th width="90">ID</th>
        <th width="150">Rate Type</th>
        <th width="150">Type</th>
        <th width="42"></th>
        <th width="120">Completed</th>
        <th width="75">Hours</th>
        <th width=>Title</th>
        <th width="140"></th>
        </tr>
        </thead>
        <tbody>
qq;


    foreach ($otherStories as $row) {
        $createdAt = (!empty($row['created_at'])) ? formatDate($row['created_at']) : '-';

        if ($row['status'] === 'Closed') {
            $icon = '⛔️';
            $class = 'bubble redBubble';
            $status = formatDate($row['ended_at']);
        } elseif ($row['status'] === 'Shipped') {
            $icon = '🚀';
            $class = 'bubble blueBubble';
            $status = formatDate($row['ended_at']);
        } elseif ($row['status'] === 'Complete') {
            $icon = '✅';
            $class = 'bubble greenBubble';
            $status = formatDate($row['ended_at']);
        } else {
            $icon = '';
            $class = 'bubble grayBubble';
            $status = $row['status'];
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

        echo "<tr>";
        echo "<td><span class=\"$class\"><a href=\"story.php?id=" . $row['id'] . "\">" . $row['show_id'] . "</a></span></td>";
        echo "<td>" . $hourSelect . "</td>";
        echo "<td>" . $typeSelect . "</td>";
        echo "<td class=\"textCenter\">" . $icon . "</td>";
        echo "<td>" . $status . "</td>";
        echo "<td><input type=\"text\" autocomplete=\"off\" name=\"story[$row[id]][hours]\" value=\"$row[hours]\" /></td>";
        echo "<td><input type=\"text\" autocomplete=\"off\" style=\"width:100%;\" name=\"story[$row[id]][title]\" value=\"$row[title]\" /></td>";
        echo "<td class=\"textRight\">
    <a href=\"project.php?action=markStoryComplete&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">✅</a>
    <a href=\"project.php?action=markStoryShipped&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">🚀</a>
    <a href=\"project.php?action=markStoryClosed&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">⛔️</a>
    <a onclick=\"return confirm('This will delete the story - are you sure?')\" href=\"project.php?action=deleteStory&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">❌</a>
    </td>";
        echo "</tr>";
    }

    echo <<<qq
<tr>
<td colspan="5"></td>
<td>$hours</td>
<td colspan="2">
qq;

    echo '<button type="submit">Update Stories</button> <button type="button" onClick="window.location=\'invoice.php?collection=' . $aCollection['id'] . '\'">Preview Invoice</button> <button type="button" onClick="window.location=\'invoice.php?collection=' . $aCollection['id'] . '&save=1\'">Generate & Save Invoice</button>';

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

echo <<<qq
    </div>
</div>
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

/**
 * @param array $data
 * @return void
 */
function createCollection(array $data): void
{
    global $db;

    $statement = $db->prepare('
        INSERT INTO story_collection (title, project_id, goals, ended_at)
        VALUES (:title, :project_id, :goals, :ended_at)
    ');

    $statement->bindParam(':project_id', $data['project_id']);
    $statement->bindParam(':title', $data['title']);
    $statement->bindParam(':ended_at', $data['ended_at']);
    $statement->bindParam(':goals', $data['goals']);

    $statement->execute();

    redirect('project.php', $data['project_id'], 'Your collection has been created.');
}

/**
 * @param array $data
 * @return void
 */
function createStory(array $data): void
{
    global $db;

    $project = getProjectById($data['project_id']);

    $totalStoriesInProject = getNextStoryNumberForProject($data['project_id']);

    $id = $project['code'] . '-' . $totalStoriesInProject;

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

    dd($collection);

    if ($collection['is_project_default'] === 1) {
        redirect('project.php', $data['project_id'], '', 'You cannot delete the "Unorganized" collection from a project.');
    }

    $statement = $db->prepare('
        DELETE FROM story_collection WHERE id = :id
    ');
    $statement->bindParam(':id', $data['id']);
    $statement->execute();

    $statement = $db->prepare('
        UPDATE story
        SET collection = 1
        WHERE collection = :id
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
 * @param array $data
 * @return void
 */
function makeCurrentCollection(array $data): void
{
    global $db;

    $statement = $db->prepare('
        UPDATE story_collection SET created_at = :date WHERE id = :id
    ');

    $statement->bindParam(':id', $data['id']);
    $statement->bindParam(':date', date('Y-m-d H:i:s'));

    $statement->execute();

    redirect('project.php', $data['project_id'], 'Now working with a new collection.');
}

/**
 * @param array $data
 * @return void
 */
function shiftCollection(array $data): void
{
    global $db;

    $story = getStory($data['id']);

    // Move to latest
    if ($story['collection'] === 1) {
        $latestCollection = getLatestCollection();

        $moveTo = $latestCollection['id'];

        $msg = 'Your story is now part of the "' . $latestCollection['title'] . '" collection.';
    }
    // Move to unorganized
    else {
        $moveTo = 1;

        $msg = 'Your story is now under the "Unorganized" collection.';
    }

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
            UPDATE story
            SET hours = :hours, type = :type, rate_type = :rate_type, title = :title
            WHERE id = :id
        ');
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
 * @param integer $newStatus
 * @return void
 */
function updateStoryStatus(array $data, int $newStatus): void
{
    global $db;

    $story = getStory($data['id']);
    $hours = ($story['status'] === 1 && $newStatus !== 1) ? 1 : 0;

    $statement = $db->prepare('
        UPDATE story
        SET status = :status, ended_at = :ended_at, hours = :hours
        WHERE id = :id
    ');

    $statement->bindParam(':status', $newStatus);
    $statement->bindParam(':hours', $hours);
    $statement->bindParam(':id', $data['id']);
    $statement->bindParam(':ended_at', date('Y-m-d H:i:s'));
    $statement->execute();

    $status = getStoryStatusById($newStatus);

    redirect(
        'project.php',
        $data['project_id'],
        'Your status of your story has been changed to "' . $status['title'] . '".'
    );
}
