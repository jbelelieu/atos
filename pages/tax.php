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

// deductions: reduce total income

$doProjectedEstimate = true;
$year = date('Y');
$tax = 0;

$taxService = new TaxService();

// Get our filing strategy
$filingStrategy = 'single';

// Get a list of regions we are paying taxes in.
$taxRegions = $taxService->getTaxRegions($year);
if (empty($taxRegions)) {
    redirect('/tax', null, null, 'No known tax regions for this year.');
}

// Get our base income and set as initial taxable income.
$baseIncome = $taxService->getTotalBaseIncomeByYear($year);
if ($baseIncome <= 0) {
    redirect('/tax', null, null, 'No known income for the year in question.');
}

$dayInTheYear = (int) date('z') + 1;
$remaining = 365 - $dayInTheYear;
$currentDailyAverage = $baseIncome / $dayInTheYear;
$additionalEstimate = 0;

// Projecting estimates
if ($doProjectedEstimate) {
    $additionalEstimate = $currentDailyAverage * $remaining;

    $baseIncome += $additionalEstimate;
}

$baseIncome = 404660;

// Set our initial taxable income as our base.
$taxableIncome = $baseIncome;

// Figure in addition taxable income.
$taxBurdens = $taxService->getAdditionalTaxBurdens($year);
$tax += $taxBurdens['adjustment'];

// Now take out known deductions.
$deductions = $taxService->getDeductions($year);
$taxableIncome = $taxableIncome - $deductions['adjustment'];
if ($taxableIncome < 0) {
    $taxableIncome = 0;
}

// Get tax burden across all known taxable regions
$finalData = [];
foreach ($taxRegions as $aRegion) {
    $loadfile = ATOS_HOME_DIR . '/includes/tax/' . $year . '/' . $aRegion . '.php';

    require_once $loadfile;

    $taxClass = new $aRegion();
    if (!method_exists($taxClass, $filingStrategy)) {
        systemError('The tax strategy you are using, ' . $filingStrategy . ', does not exist in one of your tax files: ' . $loadfile);
    }

    $brackets = $taxClass->{$filingStrategy}();

    $taxResults = $taxService->calculateTax($brackets, $taxableIncome);

    $finalData[$aRegion] = [
        'results' => $taxResults,
        '_class' => $taxClass,
    ];

    $tax += $taxResults['tax'];
}

// Add recommendations
$recommendations = [];
foreach ($finalData as $region => $aTaxRegionBurden) {
    $percent = intval($aTaxRegionBurden['results']['tax']) / $tax;
    $quarterly = intval($aTaxRegionBurden['results']['tax']) / 4;

    $finalData[$region]['recommendations'] = [
        'quarterly' => $quarterly,
        'percentOfTotal' => round($percent, 2) * 100,
    ];
}

$changes = [
    'estimateMode' => $doProjectedEstimate,
    'income' => [
        'baseIncome' => formatMoney($baseIncome * 100),
        'additionalEstimate' => formatMoney($additionalEstimate * 100),
        'additionalTaxBurdens' => formatMoney($taxBurdens['adjustment'] * 100),
        'deductions' => formatMoney($deductions['adjustment'] * 100),
        'dailyAverage' => formatMoney($currentDailyAverage * 100),
    ],
    'taxes' => [
        'totalTax' => $tax,
        ...$finalData,
    ],
    '_raw' => [
        'taxBurdens' => $taxBurdens['data'],
        'deductions' => $deductions['data'],
    ],
];

dd($changes);
