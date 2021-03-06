<?php

use services\CollectionService;
use services\CompanyService;
use services\ProjectService;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Estimated tax help for US-based developers.
 *
 * This represents the US Federal 2022 data.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

if (empty($_GET['project_id'])) {
    redirect(
        '/',
        null,
        null,
        language('error_invalid_id', 'You need to provide a valid ID')
    );
}

$collectionService = new CollectionService();
$companyService = new CompanyService();
$projectService = new ProjectService();

$project = $projectService->getProjectById($_GET['project_id']);
$company = $companyService->getCompanyById($project['company_id']);
$clientCompany = $companyService->getCompanyById($project['client_id']);
$collections = $collectionService->getCollectionByProject($project['id']);

$results = $projectService->getStoriesByFilters(
    $_GET['project_id'],
    isset($_GET['type']) ? $_GET['type'] : [],
    isset($_GET['status']) ? $_GET['status'] : [],
    isset($_GET['completedOn']) ? $_GET['completedOn'] : [],
    isset($_GET['collection']) ? $_GET['collection'] : [],
);

$templateName = 'report/' . $_GET['template'];

$template = template(
    $templateName,
    [
        'message' => isset($_GET['message']) ? nl2br($_GET['message']) : null,
        'title' => isset($_GET['title']) ? $_GET['title'] : null,
        'client' => $clientCompany,
        'project' => $project,
        'company' => $company,
        'stories' => $results,
        'collections' => $collections,
        'logo' => logo(),
        'css' => file_get_contents('assets/alternative_view.css'),
    ],
    true
);

// dd($changes);

if (!empty($_GET['save']) && $_GET['save'] === '1') {
    $cleanName = (!empty($_GET['title']))
        ? cleanFileName($_GET['title'])
        : cleanFileName($project['title']);

    $filename = 'report-' . date('Ymd') . '-' . $cleanName . '-' . '.html';

    file_put_contents(ATOS_HOME_DIR . '/_generated/' . $filename, $template);

    $msg = 'Saved invoice to: _generated/' . $filename;
    redirect('/project', $project['id'], $msg);
    exit;
}

echo $template;
exit;
