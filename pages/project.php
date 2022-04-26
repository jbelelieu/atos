<?php

use services\CollectionService;
use services\ProjectService;
use services\SettingService;
use services\StoryService;

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
$settingService = new SettingService();
$projectService = new ProjectService();
$storyService = new StoryService();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteCollection':
            $collectionService->deleteCollection($_GET);
            exit;
        case 'deleteStory':
            $storyService->deleteStory($_GET);
            exit;
        case 'updateStoryStatus':
            $storyService->updateStoryStatus($_GET, 4);
            exit;
        case 'makeCurrentCollection':
            $collectionService->makeCurrentCollection($_GET);
            exit;
        case 'shiftCollection':
            $collectionService->shiftCollection($_GET);
            exit;
        default:
            redirect('/project', $_GET['id'], null, 'Unknown action');
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createCollection':
            $collectionService->createCollection($_POST);
            exit;
        case 'createStory':
            $storyService->createStory($_POST);
            exit;
        case 'updateStories':
            $storyService->updateStories($_POST);
            exit;
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

$project = $projectService->getProjectById($_GET['id']);
if (!$project) {
    redirect(
        '/',
        null,
        null,
        language('error_invalid_id', 'You need to provide a valid ID')
    );
}

// Convienience feature for easier navigation.
$_SESSION["viewingProject"] = $project['id'];
$_SESSION["viewingProjectName"] = $project['title'];

$storyStatuses = $settingService->getStoryStatuses();
$hourTypeResults = $settingService->getRateTypes();
$storyTypeResults = $settingService->getStoryTypes();

$collectionResults = [
 $collectionService->getLatestCollectionForProject($project['id']),
 $collectionService->getDefaultCollectionForProject($project['id']),
];

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

$allCollections = $collectionService->getCollectionByProject($project['id']);
foreach ($allCollections as $aCollection) {
    array_push($collectionArray, $aCollection['id']);

    $collectionSelect .= '<option value="' . $aCollection['id'] . '">' . $aCollection['title'] . '</option>';
}

// Build the collections list.
$collections = '';
$totalCollections = sizeof($allCollections);
$at = 0;

foreach ($allCollections as $row) {
    if ($at === $totalCollections) {
        continue;
    }

    $isProjectDefault = isBool($row['is_project_default']);

    $delete = (!$isProjectDefault)
        ? "<a onclick=\"return confirm('Are you sure you want to delete this collection?')\" href=\"/project?action=deleteCollection&project_id=" . $project['id'] . "&id=" . $row['id'] . "\">" . putIcon('fi-sr-trash') . "</a>"
        : '';

    $update = ($at > 0 && !$isProjectDefault)
        ? "<a title=\"" . language('make_active_collection', 'Make Active Collection') . "\" href=\"/project?action=makeCurrentCollection&project_id=" . $project['id'] . "&id=" . $row['id'] . "\">" . $row['title'] . "</a>"
        : $row['title'];
        
    $color = $at === 0 ? 'bgBlue' : 'bgGray';

    $collections .= "<div class=\"bubble marginRightLess $color\">";
    $collections .= "<span>" . $update . "</span>" . $delete;
    $collections .= "</div>";

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
    $openStories = $collectionService->getStoriesInCollection($aCollection['id']);
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
                'hourSelect' => $settingService->buildHourSelect(
                    $row['id'],
                    $row['rate_type'],
                    $hourTypeResults
                ),
                'options' => $storyService->buildStoryOptions(
                    $project['id'],
                    $row['id'],
                    false,
                    $row['status'],
                    ($isProjectDefault) ? true : false
                ),
                'story' => $row,
                'typeSelect' => $settingService->buildTypeSelect(
                    $row['id'],
                    $row['type'],
                    $storyTypeResults
                ),
            ],
            true
        );
    }

    // Billable stories for this collection
    $renderedOtherStories = '';
    $otherStories = $collectionService->getStoriesInCollection(
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

        $isBillableState = isBool($row['is_billable_state']);

        $class = (!$isBillableState) ? 'notBillable' : '';

        $renderedOtherStories .= template(
            'admin/snippets/collection_table_other_entry',
            [
                'endedAt' => $endedAt,
                'hours' => $row['hours'],
                'hourSelect' => $settingService->buildHourSelect($row['id'], $row['hour_rate'], $hourTypeResults),
                'label' => $label,
                'options' => $storyService->buildStoryOptions(
                    $project['id'],
                    $row['id'],
                    false,
                    $row['status']
                ),
                'project' => $project,
                'rowClass' => $class,
                'story' => $row,
                'typeSelect' => $settingService->buildTypeSelect($row['type'], $row['type'], $storyTypeResults),
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
            'project' => $project,
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
        'nextId' => $storyService->generateTicketId($project['id']),
        'project' => $project,
        'storyTypeSelect' => $storyTypeSelect,
        'totalCollections' => $totalCollections,
    ]
);
exit;
