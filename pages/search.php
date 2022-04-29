<?php

use services\ProjectService;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

if (empty($_GET['query'])) {
    redirect(
        '/',
        null,
        null,
        language('/', 'You need to provide a search query')
    );
}

$projectService = new ProjectService();

$results = $projectService->search($_GET['query']);

echo template(
    'admin/search',
    [
        '_metaTitle' => 'Search (ATOS)',
        'results' => $results,
        'totalResults' => sizeof($results),
        'query' => $_GET['query'],
    ]
);
exit;
