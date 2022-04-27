<?php

use services\SettingService;

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

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Actions
 *
 */

$settingService = new SettingService();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteRate':
            $settingService->deleteRate($_GET);
            break;
        case 'deleteStatus':
            $settingService->deleteStatus($_GET);
            break;
        case 'deleteStoryType':
            $settingService->deleteStoryType($_GET);
            break;
        default:
            redirect('/settings', null, null, 'Unknown action');
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createRateType':
            $settingService->createRateType($_POST);
            break;
        case 'createStatus':
            $settingService->createStatus($_POST);
            break;
        case 'createStoryType':
            $settingService->createStoryType($_POST);
            break;
        case 'updateRates':
            $settingService->updateRates($_POST);
            break;
        case 'updateStatuses':
            $settingService->updateStatuses($_POST);
            break;
        case 'updateTypes':
            $settingService->updateTypes($_POST);
            break;
        default:
            redirect('/settings', null, null, 'Unknown action');
    }
}

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Down and Dirty
 *
 */

$statuses = $settingService->getStoryStatuses();
$rateTypes = $settingService->getRateTypes(true);
$storyTypes = $settingService->getStoryTypes();

$renderedStoryTypes = '';
foreach ($storyTypes as $aStoryType) {
    $renderedStoryTypes .= template(
        'admin/snippets/story_type_table_entry',
        [
            ...$aStoryType,
            'deleteLink' => buildLink(
                '/settings',
                [
                    'action' => 'deleteStoryType',
                    'id' => $aStoryType['id'],
                ]
            ),
        ],
        true
    );
}

$renderedRateTypes = '';
foreach ($rateTypes as $aRate) {
    $renderedRateTypes .= template(
        'admin/snippets/rate_table_entry',
        [
            ...$aRate,
            'deleteLink' => buildLink(
                '/settings',
                [
                    'action' => 'deleteRate',
                    'id' => $aRate['id'],
                ]
            ),
            'rate' => $aRate['rate'] / 100,
        ],
        true
    );
}

$renderedStatuses = '';
foreach ($statuses as $aStatus) {
    $renderedStatuses .= template(
        'admin/snippets/status_table_entry',
        [
            ...$aStatus,
            'deleteLink' => buildLink(
                '/settings',
                [
                    'action' => 'deleteStatus',
                    'id' => $aStatus['id'],
                ]
            ),
            'icon' => putIcon($aStatus['emoji'], $aStatus['color']),
            'isBillable' => isBool($aStatus['is_billable_state']),
            'isComplete' => isBool($aStatus['is_complete_state']),
        ],
        true
    );
}

// Render the entire page.
echo template(
    'admin/settings',
    [
        '_metaTitle' => 'Settings (ATOS)',
        'renderedRateTypes' => $renderedRateTypes,
        'renderedStatuses' => $renderedStatuses,
        'renderedStoryTypes' => $renderedStoryTypes,
    ]
);
exit;
