<?php

require_once ATOS_HOME_DIR . '/services/CollectionService.php';
require_once ATOS_HOME_DIR . '/services/StoryService.php';

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

$collectionService = new CollectionService();
$storyService = new StoryService();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteCollection':
            $collectionService->deleteCollection($_GET);
            break;
        case 'deleteStory':
            $storyService->deleteStory($_GET);
            break;
        case 'updateStoryStatus':
            $storyService->updateStoryStatus($_GET, 4);
            break;
        case 'makeCurrentCollection':
            $collectionService->makeCurrentCollection($_GET);
            break;
        case 'shiftCollection':
            $collectionService->shiftCollection($_GET);
            break;
        default:
            redirect('/project', $_GET['id'], null, 'Unknown action');
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createCollection':
            $collectionService->createCollection($_POST);
            break;
        case 'createStory':
            $storyService->createStory($_POST);
            break;
        case 'updateStories':
            $storyService->updateStories($_POST);
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

foreach ($collectionResults as $row) {
    if ($at === $totalCollections) {
        continue;
    }

    $delete = $row['id'] > 1 ? "<a href=\"/project?action=deleteCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . putIcon('fi-sr-trash') . "</a>" : '';

    $update = ($at > 0 && !$row['is_project_default'])
        ? "<a title=\"" . language('make_active_collection', 'Make Active Collection') . "\" href=\"/project?action=makeCurrentCollection&project_id=" . $_GET['id'] . "&id=" . $row['id'] . "\">" . $row['title'] . "</a>"
        : $row['title'];

    $collections .= "<div><span>" . $update . "</span>" . $delete . "</div>";

    $at++;
}

// Render the collections, split between open, billable, and unorganized.
$collectionsRendered = '';
$collectionCount = 0;

foreach ($collectionResults as $aCollection) {
    $collectionCount++;

    $hours = 0;

    $isProjectDefault = isBool($aCollection['is_project_default']);

    $tripFlag = ($collectionCount > 1 && !$isProjectDefault);

    // Open stories for this collection
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
                'options' => $storyService->buildStoryOptions(
                    $_GET['id'],
                    $row['id'],
                    false,
                    $row['status'],
                    ($isProjectDefault) ? true : false
                ),
                'story' => $row,
            ],
            true
        );
    }

    // Billable stories for this collection
    $renderedOtherStories = '';
    $otherStories = getStoriesInCollection(
        $aCollection['id'],
        false,
        'ended_at ASC, status ASC',
        true,
        true
    );
    foreach ($otherStories as $row) {
        $adjustedColor = adjustBrightness($row['status_color'], -10);

        $endedAt = null;
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

        $isBillableState = isBool($row['is_billable_state']);
        $class = (!$isBillableState) ? 'notBillable' : '';

        $renderedOtherStories .= template(
            'admin/snippets/collection_table_other_entry',
            [
                'endedAt' => $endedAt,
                'hours' => $row['hours'],
                'hourSelect' => $hourSelect,
                'label' => $label,
                'options' => $storyService->buildStoryOptions(
                    $_GET['id'],
                    $row['id'],
                    false,
                    $row['status']
                ),
                'project' => $project,
                'rowClass' => $class,
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
        'nextId' => $storyService->generateTicketId($_GET['id']),
        'project' => $project,
        'storyTypeSelect' => $storyTypeSelect,
        'totalCollections' => $totalCollections,
    ]
);
exit;
