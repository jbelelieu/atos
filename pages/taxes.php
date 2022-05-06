<?php

use services\TaxService;

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

$taxService = new TaxService();

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Actions
 *
 */

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createMoneyAside':
            $taxService->createMoneyAside($_POST);
            exit;
        case 'setupTaxes':
            $taxService->setupTaxes($_POST);
            exit;
        default:
            redirect('/project', $_GET['id'], null, 'Unknown action');
    }
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteMoneyAside':
            $taxService->deleteMoneyAside($_GET['id']);
            exit;
        case 'deleteYear':
            $taxService->deleteYear(intval($_GET['year']));
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

$year = (!empty($_GET['year']) && is_numeric($_GET['year'])) ? $_GET['year'] : date('Y');

$taxesThisYear = $taxService->getTax($year);
$taxes = $taxService->getTaxes();

$namespace = '\modules\tax';
$moduleDir = ATOS_HOME_DIR . '/modules/tax/Y' . $year;

$strategies = [];
if (file_exists($moduleDir)) {
    foreach (scandir($moduleDir) as $file) {
        if ($file === '..' || $file === '.') {
            continue;
        }
        $exp = explode('.', $file);
        $className = $exp[0];

        $combine = $namespace . '\\Y' . $year . '\\' . $className;
        $class = new $combine();
 
        $strategies[$className] = [
            'title' => $className,
            'region' => $class::REGION,
            'strategies' => get_class_methods($class),
        ];
    }
}

foreach ($taxes as &$aTaxYear) {
    foreach ($aTaxYear['strategies'] as $key => $strat) {
        $combine = $namespace . '\\Y' . $aTaxYear['year'] . '\\' . $key;
        $class = new $combine();

        $aside = $taxService->getMoneyAside($aTaxYear['year']);

        $aTaxYear['aside'] = $aside;

        $aTaxYear[$key] = [
            'status' => $strat,
            '_class' => new $class(),
        ];
    }
}

$changes = [
    'taxBurdenRegionDir' => 'modules/tax/Y' . $year,
    'strategies' => $strategies,
    'strategiesFound' => (sizeof($strategies) === 0) ? false : true,
    'taxesThisYear' => $taxesThisYear,
    'taxes' => $taxes,
    'year' => $year,
    'aside' => $aside,
];

// dd($changes);

echo template('admin/tax', $changes);
