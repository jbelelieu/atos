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

// Build the collections list.
$collections = '';
$allCollections = $collectionService->getCollectionByProject($project['id']);
$totalCollections = sizeof($allCollections);
$at = 0;

foreach ($allCollections as $row) {
    if ($at === $totalCollections) {
        continue;
    }

    $isProjectDefault = isBool($row['is_project_default']);

    $delete = (!$isProjectDefault)
        ? "<span class=\"delete\"><a onclick=\"return confirm('Are you sure you want to delete this collection?')\" href=\"/project?action=deleteCollection&project_id=" . $project['id'] . "&id=" . $row['id'] . "\">" . putIcon('fi-sr-trash') . "</a></span>"
        : '';

    $update = ($at > 0 && !$isProjectDefault)
        ? "<a title=\"" . language('make_active_collection', 'Make Active Collection') . "\" href=\"/project?action=makeCurrentCollection&project_id=" . $project['id'] . "&id=" . $row['id'] . "\">" . $row['title'] . "</a>"
        : $row['title'];
        
    $active = $at === 0 ? 'active' : '';

    $collections .= "<div class=\"collectionEntry $active\">";
    $collections .= "<span class=\"title\">" . $update . "</span>" . $delete;
    $collections .= "</div>";

    $at++;
}

// Render the collections, split between open, billable, and unorganized.
$collectionsRendered = '';
$collectionCount = 0;
$totalTasks = 0;

foreach ($collectionResults as $aCollection) {
    $collectionCount++;

    $hours = 0;

    $isProjectDefault = isBool($aCollection['is_project_default']);

    $tripFlag = ($collectionCount > 1 && !$isProjectDefault);

    // Open stories for this collection
    $renderedOpenStories = '';
    $openStories = $collectionService->getStoriesInCollection($aCollection['id']);
    foreach ($openStories as $row) {
        $totalTasks++;

        $label = getLabel($row);

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
                'label' => $label,
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
        $totalTasks++;
    
        $endedAt = ($row['status'] == 2 || $row['status'] == 4)
            ? formatDate($row['ended_at'], 'Y-m-d')
            : null;

        $label = getLabel($row);

        $hours += (int) $row['hours'];

        $class = '';
        $class .= (!isBool($row['is_billable_state'])) ? ' notBillable' : '';
        $class .= ($row['status'] == 3) ? ' handOff' : '';

        $renderedOtherStories .= template(
            'admin/snippets/collection_table_other_entry',
            [
                'deleteLink' => buildLink(
                    '/project',
                    [
                        'action' => 'deleteStory',
                        'id' => $row['id'],
                        'project_id' => $project['id'],
                    ]
                ),
                'endedAt' => $endedAt,
                'hours' => $row['hours'],
                'hourSelect' => $settingService->buildHourSelect(
                    $row['id'],
                    $row['rate_type'],
                    $hourTypeResults
                ),
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
                'typeSelect' => $settingService->buildTypeSelect($row['id'], $row['type'], $storyTypeResults),
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


$allTemplates = [];
foreach (scandir(ATOS_HOME_DIR . '/templates/report') as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $exp = explode('.', $file);

    $allTemplates[$exp[0]] = snakeToEnglish($file);
}

echo template(
    'admin/projects',
    [
        '_metaTitle' => $project['title'] . ' (ATOS)',
        'collections' => $collections,
        'templates' => $allTemplates,
        'allCollections' => $allCollections,
        'collectionsRendered' => $collectionsRendered,
        'nextId' => $storyService->generateTicketId($project['id']),
        'project' => $project,
        'totalCollections' => sizeof($allCollections),
        'totalTasks' => $totalTasks,
        'hourTypes' => $hourTypeResults,
        'storyStatuses' => $storyStatuses,
        'storyTypes' => $storyTypeResults,
    ]
);
exit;

/**
 * @param array  $row
 * @return string
 */
function getLabel(array  $row): string
{
    return "<div class=\"projectId\" style=\"color:" . $row['status_color'] . "\">" . $row['show_id'] . "</div>";
}
