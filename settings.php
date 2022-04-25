<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Controls all system settings, including story related
 * controls like rates, types, and statuses.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

require "includes/db.php";

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Actions
 *
 */

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

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Down and Dirty
 *
 */

$statuses = getStoryStatuses();
$rateTypes = getRateTypes();
$storyTypes = getStoryTypes();

$renderedStoryTypes = '';
foreach ($storyTypes as $aStoryType) {
    $renderedStoryTypes .= template(
        'admin/snippets/story_type_table_entry',
        $aStoryType,
        true
    );
}

$renderedRateTypes = '';
foreach ($rateTypes as $aRate) {
    $renderedRateTypes .= template(
        'admin/snippets/rate_table_entry',
        [
            ...$aRate,
            'rate' => formatMoney($aRate['rate']),
        ],
        true
    );
}

$renderedStatuses = '';
foreach ($statuses as $aStatus) {
    $state = $aStatus['is_complete_state'] ? 'Yes' : 'No';
    $billable = $aStatus['is_billable_state'] ? 'Yes' : 'No';

    $renderedStatuses .= template(
        'admin/snippets/status_table_entry',
        [
            ...$aStatus,
            'icon' => putIcon($aStatus['emoji'], $aStatus['color']),
            'isBillable' => $billable,
            'isComplete' => $state,
        ],
        true
    );
}

// Render the entire page.
echo template(
    'admin/settings',
    [
        'renderedRateTypes' => $renderedRateTypes,
        'renderedStatuses' => $renderedStatuses,
        'renderedStoryTypes' => $renderedStoryTypes,
    ]
);
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
