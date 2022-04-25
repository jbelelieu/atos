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

include "includes/header.php";

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
