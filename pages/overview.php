<?php

use services\CompanyService;
use services\ProjectService;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * This file controls projects and companies.
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

$companyService = new CompanyService();
$projectService = new ProjectService();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteCompany':
            $companyService->deleteCompany($_GET);
            break;
        case 'deleteProject':
            $projectService->deleteProject($_GET);
            break;
        default:
            redirect('/', null, null, 'Unknown action');
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createCompany':
            $companyService->createCompany($_POST);
            break;
        case 'createProject':
            $projectService->createProject($_POST);
            break;
        default:
            redirect('/', null, null, 'Unknown action');
    }
}

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Down and Dirty
 *
 */

$clients = $companyService->getCompanies();

// The client select dropdown
$clientSelect = '<option value=""></option>';
foreach ($clients as $aClient) {
    $clientSelect .= '<option value="' . $aClient['id'] . '">' . $aClient['title'] . '</option>';
}

// Client table
$totalValue = 0;
$renderedClients = '';

foreach ($clients as $aClient) {
    $value = $companyService->getCompanyTotals($aClient['id']);

    $totalValue += $value['total'];

    $logo = $aClient['logo_url']
        ? '<img src="' . $aClient['logo_url'] . '" class="clientLogo" />'
        : '';

    $renderedClients .= template(
        'admin/snippets/client_table_entry',
        [
            'client' => $aClient,
            'deleteLink' => buildLink('/', ['action' => 'deleteCompany', 'id' => $aClient['id']]),
            'logo' => $logo,
            'totalClientValue' => formatMoney($value['total']),
            'url' => $aClient['url'] ? $aClient['url'] : null,
        ],
        true
    );
}

// Build the projects table.
$projects = $projectService->getProjects();

$totalProjectValue = 0;
$totalProjectHours = 0;
$renderedProjects = '';

// Project table
foreach ($projects as $aProject) {
    $value = $projectService->getProjectTotals($aProject['id']);

    $totalProjectValue += $value['total'];
    $totalProjectHours += $value['hours'];

    $renderedProjects .= template(
        'admin/snippets/project_table_entry',
        [
            'deleteLink' => buildLink('/', ['action' => 'deleteProject', 'id' => $aProject['id']]),
            'hours' => $value['hours'],
            'project' => $aProject,
            'total' => formatMoney($value['total']),
        ],
        true
    );
}

echo template(
    'admin/index',
    [
        '_metaTitle' => 'Projects & Companies (ATOS)',
        'clientSelect' => $clientSelect,
        'clients' => $renderedClients,
        'projects' => $renderedProjects,
        'totalClients' => sizeof($clients),
        'totalProjectHours' => $totalProjectHours,
        'totalProjectValue' => formatMoney($totalProjectValue),
        'totalClientValue' => formatMoney($totalValue),
    ]
);
exit;
