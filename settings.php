<?php

require "includes/db.php";

// -------------------------------------

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteRate':
            deleteRate($_GET);
            break;
        case 'deleteStatus':
            deleteStatus($_GET);
            break;
        case 'deleteStoryType':
            deleteStoryType($_GET);
            break;
        default:
            redirect('settings.php', null, null, 'Unknown action');
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createRateType':
            createRateType($_POST);
            break;
        case 'createStatus':
            createStatus($_POST);
            break;
        case 'createStoryType':
            createStoryType($_POST);
            break;
        default:
            redirect('settings.php', null, null, 'Unknown action');
    }
}

// -------------------------------------

$statuses = getStoryStatuses();
$rateTypes = getRateTypes();
$storyTypes = getStoryTypes();

include "templates/admin/header.php";

echo <<<qq
<div class="border">
    <div class="halfHalfColumns">
        <div>
            <div class="formBox padLess">
                <form action="settings.php" method="post">
                    <div>
                        <label><b>Statuses</b>&nbsp;&nbsp;Title</label>
                        <input type="text" autocomplete="off" name="title" />
                        
                        <label>Complete State?</label>
                        <input type="radio" name="is_complete_state" value="1" checked="checked" /> Yes, we can consider these stories completed.<br />
                        <input type="radio" name="is_complete_state" value="0" /> No, stories with this status are not complete.

                        <label>Billable State?</label>
                        <input type="radio" name="is_billable_state" value="1" checked="checked" /> Yes, this status represents a billable state.<br />
                        <input type="radio" name="is_billable_state" value="0" /> No, do not bill for stories with this status.
                        
                        <div class="halfHalfColumns">
                        <div>
                            <label>Emoji</label>
                            <input type="text" autocomplete="off" name="emoji" maxlength=10 style="width:200px;" />
                            <p class="fieldHelp">Select any icon from <a href="https://www.flaticon.com/uicons/?weight=solid&corner=rounded" target="_blank">here</a>. Type the name (exp: "<u>fi-sr-briefcase</u>") found by clicking on the icon.</p>
                        </div>
                        <div>
                            <label>Color</label>
                            #<input type="color" autocomplete="off" name="color" style="width:42px;" /> <button type="submit">Create</button>
                        </div>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="createStatus" />
                </form>
            </div>
        </div>
        <div>
            <div class="formBox padLess">
                <form action="settings.php" method="post">
                    <div>
                        <label><b>Rate Types</b>&nbsp;&nbsp;Title</label>
                        <input type="text" autocomplete="off" name="title" />
                        
                        <label>Rate</label>
                        $<input type="text" autocomplete="off" name="rate" style="width:100px;" /> <button type="submit">Create</button>
                    </div>
                    <input type="hidden" name="action" value="createRateType" />
                </form>

                <hr />

                <form action="settings.php" method="post">
                    <div>
                        <label><b>Story Types</b>&nbsp;&nbsp;Title</label>
         
require "includes/db.php";

if (empty($_GET['id'])) {
    redirect(
        'index.php',
        null,
        null,
        language('error_invalid_id', 'You need to provide a valid ID')
    );
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

include "templates/admin/header.php";

$nextId = generateTicketId($_GET['id']);

echo <<<qq
<div class="collectionsTable">

<div class="standardColumns border">
    <div>
        <div class="formBox padLess">
            <form action="project.php?id=$_GET[id]" method="post">
                <div class="threeColumns">
                    <div>
                    <label><b>{language('story', 'Story')}</b>&nbsp;&nbsp;Collection</label>
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
                    <label>Description</label>
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

$totalResults = sizeof($collectionResults);
$at = 0;
foreach ($collectionResults as $row) {
    $at++;

    if ($at === $totalResults) {
        continue;
    }

    $delete = $row['id'] > 1 ? "<a href=\"project.php?action=deleteCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . putIcon('fi-sr-trash') . "</a>" : '';

    $update = ($at > 1 && !$row['is_project_default'])
        ? "<a title=\"" . language('make_active_collection', 'Make Active Collection') . "\" href=\"project.php?action=makeCurrentCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . $row['title'] . "</a>"
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

        $options = buildStoryOptions($_GET['id'], $row['id'], false, $row['status']);

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

require "includes/db.php";

if (empty($_GET['id'])) {
    redirect(
        'index.php',
        null,
        null,
        language('error_invalid_id', 'You need to provide a valid ID')
    );
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

include "templates/admin/header.php";

$nextId = generateTicketId($_GET['id']);

echo <<<qq
<div class="collecionsTable">

<div class="standardColumns border">
    <div>
        <div class="formBox padLss">
            <for action="project.ph?id=$_GET[id]" method="post">
                <div css="hreColumn">
                    <div>
                    <label><b>{language('story', 'Story')}<b>&nbsp;&nbsp;Collection</lbel>
                    <select name="collection">$collectionSelect</select>
                    </iv>

                    <div>
                    <label>Type</label>
                    <select nae="type">$storyTypeSelect</select>
                    </div>

                    <dv>
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
                    <label>Description</label>
                    <input type="text" ame="title" style="width:80%;" /> <button type="submit">Create</button>
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

$totalResults = sizeof($collectionResults);
$at = 0;
foreach ($collectionResults as $row) {
    $at++;

    if ($at === $totalResults) {
        continue;
    }

    $delete = $row['id'] > 1 ? "<a href=\"project.php?action=deleteCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . putIcon('fi-sr-trash') . "</a>" : '';

    $update = ($at > 1 && !$row['is_project_default'])
        ? "<a title=\"" . language('make_active_collection', 'Make Active Collection') . "\" href=\"project.php?action=makeCurrentCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . $row['title'] . "</a>"
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

        $options = buildStoryOptions($_GET['id'], $row['id'], false, $row['status']);

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

            $options = buildStoryOptions($_GET['id'], $row['id'], true, $row['status']);

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

        echo '<button type="submit">' . language('update_stories', 'Update Stories') . '</button> <button type="button" onClick="window.open(\'invoice.php?collection=' . $aCollection['id'] . '\')">' . language('preview_invoice', 'Preview Invoice') . '</button> <button type="button" onClick="window.location=\'invoice.php?collection=' . $aCollection['id'] . '&save=1\'">' . language('generate_invoice', 'Generate & Save Invoice') . '</button>';

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

include "templates/admin/footer.php";
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
    bool $skipMoveCollection = false,
    $skipStatusId = 0
): string {
    global $storyStatuses;

    $options = (!$skipMoveCollection)
        ? "<a title=\"" . language('move_collections', 'Move Collections') . "\" href=\"project.php?action=shiftCollection&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon('fi-sr-undo') . "</a>"
        : '';

    foreach ($storyStatuses as $aStatus) {
        if ($skipStatusId && $skipStatusId == $aStatus['id']) {
            continue;
        }

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

            $options = buildStoryOptions($_GET['id'], $row['id'], true, $row['status']);

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

        echo '<button type="submit">' . language('update_stories', 'Update Stories') . '</button> <button type="button" onClick="window.open(\'invoice.php?collection=' . $aCollection['id'] . '\')">' . language('preview_invoice', 'Preview Invoice') . '</button> <button type="button" onClick="window.location=\'invoice.php?collection=' . $aCollection['id'] . '&save=1\'">' . language('generate_invoice', 'Generate & Save Invoice') . '</button>';

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

include "templates/admin/footer.php";
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
    bool $skipMoveCollection = false,
    $skipStatusId = 0
): string {
    global $storyStatuses;

    $options = (!$skipMoveCollection)
        ? "<a title=\"" . language('move_collections', 'Move Collections') . "\" href=\"project.php?action=shiftCollection&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon('fi-sr-undo') . "</a>"
        : '';

    foreach ($storyStatuses as $aStatus) {
        if ($skipStatusId && $skipStatusId == $aStatus['id']) {
            continue;
        }

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
        INSERT INTO s ory_coll ction (title, project_id, goals, ended_at)
        VALUES (:title, :project_id, :goals, :ended_at)
    ');

    $state ent->bindParam(':project_id', $data[' roject_id']);
    $statement->bindParam(':title', $data['title']);
    $statement->bindParam(':ended_at', $data['ended_at']);
    $statement->bindParam(':goa s', $data['goals']);
    $statement->execute();

    makeCurrentCollection([ 'id' => $currentCollection['id'] ], false);

    redirect('project.php', $d  a['proj ct_id'], 'Your collection ha  been created.');
}

 **
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
    $statement->bindParam(':title', $d ta['title']);
    $statement->bin Para (':collection', $data['collect o ']);
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
<input type="text" autocomplete="off" name="title" style="width:60%;" /> <button type="submit">Create</button>
                    </div>
                    <input type="hidden" name="action" value="createStoryType" />
                </form>
            </div>
        </div>
    </div>
</div>

<div class="collectionsTable">
    <h4 class="bubble">Story Statuses</h4>
    <table>
    <thead>
    <tr>
    <th width="42"></th>
    <th width="50%">Title</th>
    <th>Is Complete?</th>
    <th>Is Billable?</th>
    <th width="42"></th>
    </tr>
    </thead>
qq;

foreach ($statuses as $aStatus) {
    $state = $aStatus['is_complete_state'] ? 'Yes' : 'No';
    $billable = $aStatus['is_billable_state'] ? 'Yes' : 'No';

    echo "<tr>";
    echo "<td>" . putIcon($aStatus['emoji'], $aStatus['color']) . "</td>";
    echo "<td>" . $aStatus['title'] . "</td>";
    echo "<td>" . $state . "</td>";
    echo "<td>" . $billable . "</td>";
    echo "<td class=\"textRight\"><a onclick=\"return confirm('This will delete the status - are you sure?')\" href=\"settings.php?action=deleteStatus&id=" . $aStatus['id'] . "\">❌</a></td>";
    echo "</tr>";
}

echo <<<qq
</table>
</div>

<div class="collectionsTable">
    <h4 class="bubble">Rate Types</h4>
    <table>
    <thead>
    <tr>
    <th width="50%">Title</th>
    <th>Rate</th>
    <th width="42"></th>
    </tr>
    </thead>
qq;

foreach ($rateTypes as $aRate) {
    echo "<tr>";
    echo "<td>" . $aRate['title'] . "</td>";
    echo "<td>" . formatMoney($aRate['rate']) . "</td>";
    echo "<td class=\"textRight\"><a onclick=\"return confirm('This will delete the status - are you sure?')\" href=\"settings.php?action=deleteRate&id=" . $aRate['id'] . "\">❌</a></td>";
    echo "</tr>";
}

echo <<<qq
</table>
</div>

<div class="collectionsTable">
    <h4 class="bubble">Story Types</h4>
    <table>
    <thead>
    <tr>
    <th>Title</th>
    <th width="42"></th>
    </tr>
    </thead>
qq;

foreach ($storyTypes as $aStoryType) {
    echo "<tr>";
    echo "<td>" . $aStoryType['title'] . "</td>";
    echo "<td class=\"textRight\"><a onclick=\"return confirm('This will delete the status - are you sure?')\" href=\"settings.php?action=deleteStoryType&id=" . $aStoryType['id'] . "\">❌</a></td>";
    echo "</tr>";
}

echo <<<qq
</table>
</div>
qq;

include "templates/admin/footer.php";
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
function createRateType(array $data): void
{
    global $db;

    $statement = $db->prepare('
        INSERT INTO story_hour_type (
            title,
            rate
        )
        VALUES (
            :title,
            :rate
        )
    ');

    $rate = (int) $data['rate'] * 100;

    $statement->bindParam(':title', $data['title']);
    $statement->bindParam(':rate', $rate);

    $statement->execute();

    redirect('settings.php', null, 'Wow, making the big bucks now are we?');
}

/**
 * @param array $data
 * @return void
 */
function createStatus(array $data): void
{
    global $db;

    $statement = $db->prepare('
        INSERT INTO story_status (
            title,
            is_complete_state,
            is_billable_state,
            emoji,
            color
        )
        VALUES (
            :title,
            :is_complete_state,
            :is_billable_state,
            :emoji,
            :color
        )
    ');

    $statement->bindParam(':title', $data['title']);
    $statement->bindParam(':is_complete_state', $data['is_complete_state']);
    $statement->bindParam(':is_billable_state', $data['is_billable_state']);
    $statement->bindParam(':emoji', $data['emoji']);
    $statement->bindParam(':color', $data['color']);
    $statement->execute();

    redirect('settings.php', null, 'Your new status has been created.');
}

/**
 * @param array $data
 * @return void
 */
function createStoryType(array $data): void
{
    global $db;

    $statement = $db->prepare('
        INSERT INTO story_type (
            title
        )
        VALUES (
            :title
        )
    ');

    $statement->bindParam(':title', $data['title']);
    $statement->execute();

    redirect('settings.php', null, 'Your new story type has been created.');
}


/**
 * @param array $data
 * @return void
 */
function deleteRate(array $data): void
{
    global $db;

    if ($data['id'] === 1) {
        redirect('settings.php', null, null, 'You cannot delete your base rate.');
    }

    $statement = $db->prepare('
        DELETE FROM story_hour_type
        WHERE id = :id
    ');
    $statement->bindParam(':id', $data['id']);
    $statement->execute();

    $statement = $db->prepare('
        UPDATE story
        SET rate_type = 1
        WHERE rate_type IS NULL OR rate_type = :old_type
    ');
    $statement->bindParam(':old_type', $data['id']);

    $statement->execute();

    redirect('settings.php', null, 'We deleted that rate type. All stories that were covered by that have been set to your base rate.');
}

/**
 * @param array $data
 * @return void
 */
function deleteStatus(array $data): void
{
    global $db;

    if ($data['id'] <= 4) {
        redirect('settings.php', null, null, 'You cannot delete the default statuses.');
    }

    $statusType = getStoryStatusById($data['id']);
    if (!$statusType) {
        redirect('settings.php', null, null, 'Status does not exist.');
    }

    $revertedTo = $statusType['is_complete_state'] ? 2 : 1;
    $revertedToName = $statusType['is_complete_state'] ? 'Complete' : 'Open';

    $statement = $db->prepare('
        DELETE FROM story_status
        WHERE id = :id
    ');
    $statement->bindParam(':id', $data['id']);
    $statement->execute();

    $statement = $db->prepare('
        UPDATE story
        SET status = :revert_to
        WHERE status IS NULL OR status = :old_status_id
    ');
    $statement->bindParam(':old_status_id', $data['id']);
    $statement->bindParam(':revert_to', $revertedTo);
    $statement->execute();

    redirect('settings.php', null, 'We deleted that story status. All stories that were set to that status have been reverted to the "' . $revertedToName . '" status.');
}

/**
 * @param array $data
 * @return void
 */
function deleteStoryType(array $data): void
{
    global $db;

    if ($data['id'] === 1) {
        redirect('settings.php', null, null, 'You cannot delete the "Story" type.');
    }

    $statement = $db->prepare('
        DELETE FROM story_type
        WHERE id = :id
    ');
    $statement->bindParam(':id', $data['id']);
    $statement->execute();

    $statement = $db->prepare('
        UPDATE story
        SET type = 1
        WHERE type IS NULL OR type = :old_type
    ');
    $statement->bindParam(':old_type', $data['id']);
    $statement->execute();

    redirect('settings.php', null, 'We deleted that story type. All stories that were of that type have been reverted to the standard "Story" type.');
}
