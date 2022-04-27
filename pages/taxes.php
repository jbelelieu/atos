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
        case 'setupTaxes':
            $taxService->setupTaxes($_POST);
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

$moduleDir = ATOS_HOME_DIR . '/modules/tax/';
$taxBurdenRegionDir = $moduleDir . $year;

$strategies = [];
if (file_exists($taxBurdenRegionDir)) {
    foreach (scandir($taxBurdenRegionDir) as $file) {
        if ($file === '..' || $file === '.') {
            continue;
        }
        $exp = explode('.', $file);
        $className = $exp[0];

        require_once $taxBurdenRegionDir . '/' . $file;
        $class = new $className();
        // $methods = get_class_methods($class);
 
        $strategies[$className] = [
            'title' => $className,
            'strategies' => get_class_methods($class),
            // '_class' => $class,
        ];
    }
}

foreach ($taxes as &$aTaxYear) {
    foreach ($aTaxYear['strategies'] as $key => $strat) {
        require_once $moduleDir . '/' . $aTaxYear['year'] . '/' . $key . '.php';
        $class = new $key();

        $aTaxYear[$key] = [
            'status' => $strat,
            '_class' => new $key(),
        ];
    }
}

$changes = [
    'taxBurdenRegionDir' => 'modules/tax/' . $year,
    'strategies' => $strategies,
    'strategiesFound' => (sizeof($strategies) === 0) ? false : true,
    'taxesThisYear' => $taxesThisYear,
    'taxes' => $taxes,
    'year' => $year,
];

// dd($changes);

echo template('tax/tax', $changes);
