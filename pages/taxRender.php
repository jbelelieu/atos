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


/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Actions
 *
 */

$taxService = new TaxService();

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createDeduction':
            $taxService->createDeduction($_POST);
            break;
        case 'createAdjustment':
            $taxService->createAdjustment($_POST);
            break;
        case 'createEstimatedPayments':
            $taxService->createEstimatedPayments($_POST);
            break;
        default:
            redirect('/tax', null, null, 'Unknown action');
    }
}

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Down and Dirty
 *
 */

// true will project total income for the year
$doProjectedEstimate = (!empty($_GET['estimate'])) && $_GET['estimate'] === 'true'
    ? true
    : false;

// Any numeric value will set this as the income for the year
$overrideEstimatedTotal = (!empty($_GET['income'])) && $_GET['income'] > 0
    ? (int) $_GET['income']
    : null;

$year = (!empty($_GET['year'])) ? $_GET['year'] : date('Y');

// Attention message
$attentionMessage = '';
if ($overrideEstimatedTotal) {
    $displayType = 'Income Projection Display';

    $attentionMessage = 'This is a hypothetical situation whereby you gain a fixed income of ' . formatMoney($overrideEstimatedTotal * 100);
}

if ($doProjectedEstimate) {
    $displayType = 'Projected Estimate Display';

    $attentionMessage = 'This is an estimate, your income may change. This should only be used as a rough guide.';
} else {
    $displayType = 'As of Today Display';

    $attentionMessage = 'This is not accurate! It bases your tax burden on your current income to date this year, not your actual income, which will most likely be more, therefore your tax burden will be larger. If you have a good idea of where you think your income will be, you can use the fixed income estimation tool.';
}

if (!file_exists(ATOS_HOME_DIR . '/includes/tax/' . $year)) {
    redirect('/tax', null, null, 'No tax strategies found for the submitted year: ' . $year);
}

$tax = 0;

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
if ($remaining < 0) {
    $remaining = 0;
}

// $dayInTheYear = 2;
$estMonths = 365 / 30;
$currentMonthlyAverage = ($dayInTheYear < $estMonths)
    ? 0
    : $baseIncome / ($dayInTheYear / $estMonths);

$currentDailyAverage = $baseIncome / $dayInTheYear;
$additionalEstimate = 0;

// Projecting estimates
if ($doProjectedEstimate) {
    if ($overrideEstimatedTotal) {
        $baseIncome = $overrideEstimatedTotal;
    } else {
        $additionalEstimate = $currentDailyAverage * $remaining;

        $baseIncome += $additionalEstimate;
    }
}

$totalDailyAverage = $baseIncome / 365;

// Set our initial taxable income as our base.
$taxableIncome = $baseIncome;

// Figure in addition taxable income.
$taxBurdens = $taxService->getAdditionalTaxBurdens($year);
$tax = $tax + $taxBurdens['adjustment'];

// Now take out known deductions.
$deductions = $taxService->getDeductions($year);
$taxableIncome = $taxableIncome - $deductions['adjustment'];
if ($taxableIncome < 0) {
    redirect('/tax', null, null, 'You seem to have more deductions than income. Maybe try using estimation mode instead?');
}

// Get tax burden across all known taxable regions
$finalData = [];
$taxOnBaseIncome = 0;
foreach ($taxRegions as $aRegion => $regionalStrategy) {
    $filingStrategy = $regionalStrategy;

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
        'filingStrategy' => ucwords($filingStrategy),
        '_class' => $taxClass,
    ];

    $tax += $taxResults['tax'];

    $taxOnBaseIncome += $taxResults['tax'];
}

// Add recommendations
$recommendations = [];
foreach ($finalData as $region => $aTaxRegionBurden) {
    $estTaxes = $aTaxRegionBurden['_class']::ESTIMATED_TAXES_DUE;

    $totalPaymentsRequired = sizeof($estTaxes);

    $percent = intval($aTaxRegionBurden['results']['tax']) / $tax;

    $quarterly = intval($aTaxRegionBurden['results']['tax']) / intval($totalPaymentsRequired);

    $schedule = [];
    foreach ($estTaxes as $aDate) {
        $datediff = strtotime($aDate) - time();
        $daysUntil = round($datediff / (60 * 60 * 24));

        $schedule[$aDate] = [
            'date' => formatDate($aDate),
            'amount' => formatMoney($quarterly * 100),
            // 'daysUntil' => ($daysUntil <= 0) ? putIcon('fi-sr-check') : $daysUntil,
            'daysUntil' => ($daysUntil <= 0) ? '-' : $daysUntil,
        ];
    }

    $finalData[$region]['recommendations'] = [
        'totalPayment' => $totalPaymentsRequired,
        'schedule' => $schedule,
        'payment' => formatMoney($quarterly * 100),
        'percentOfTotalTaxBurden' => round($percent, 2) * 100,
    ];
}

$postTaxMoney = $baseIncome - $tax;
$postTaxDailyAverage = $postTaxMoney / 365;
$postTaxMonthlyAverage = $postTaxMoney / 12;
$averageDailyTax = $tax / 365;
$averageMonthlyTax = $tax / 12;
$preTaxDailyAverage = $baseIncome / 365;
$preTaxMonthlyAverage = $baseIncome / 12;

$estimatedTaxes = [];
$known = $taxService->getEstimatedPaymentsForYear($year);
$thisRegionTotal = 0;
$lastRegion = null;
foreach ($known as $payment) {
    if (!array_key_exists($payment['region'], $estimatedTaxes)) {
        $estimatedTaxes[$payment['region']] = [];
    }
    $estimatedTaxes[$payment['region']][] = $payment;
}

$regionTotals = [];
foreach ($estimatedTaxes as $region => $payments) {
    $total = 0;
    foreach ($payments as $aPayment) {
        $total += $aPayment['amount'];
    }
    $regionTotals[$region] = formatMoney($total * 100);
}

$changes = [
    'year' => $year,
    'displayType' => $displayType,
    'estimatedTaxes' => $estimatedTaxes,
    'regionTotals' => $regionTotals,
    'attentionMessage' => $attentionMessage,
    'dayNumber' => $dayInTheYear,
    'estimateMode' => $doProjectedEstimate,
    'averages' => [
        'monthly' => [
            'postTaxIncome' => formatMoney($postTaxMonthlyAverage * 100),
            'tax' => formatMoney($averageMonthlyTax * 100),
            'preTax' => formatMoney($preTaxMonthlyAverage * 100),
        ],
        'daily' => [
            'postTaxIncome' => formatMoney($postTaxDailyAverage * 100),
            'tax' => formatMoney($averageDailyTax * 100),
            'preTax' => formatMoney($preTaxDailyAverage * 100),
        ],
        'actual' => [
            'daily' => [
                'preTax' => formatMoney($currentDailyAverage * 100), // baseIncome / dayNumberInTheYear
            ],
            'monthly' => [
                'preTax' => formatMoney($currentMonthlyAverage * 100), // baseIncome / (dayNumberInTheYear / (365/30))
            ]
        ],
    ],
    'income' => [
        'postTaxMoney' => formatMoney($postTaxMoney * 100),
        'baseIncome' => formatMoney($baseIncome * 100),
        'additionalEstimate' => formatMoney($additionalEstimate * 100),
        'additionalTaxBurdens' => formatMoney($taxBurdens['adjustment'] * 100),
        'deductions' => formatMoney($deductions['adjustment'] * 100),
        'taxableIncome' => formatMoney($taxableIncome * 100),
        'totalDailyAverage' => formatMoney($totalDailyAverage * 100),
    ],
    'taxes' => [
        'totalTax' => formatMoney($tax * 100),
        'effectiveRate' => round($tax / $taxableIncome, 2) * 100,
        'regions' => $finalData,
    ],
    '_raw' => [
        'taxBurdens' => $taxBurdens['data'],
        'deductions' => $deductions['data'],
    ],
];

// dd($changes);

echo template('tax/estimate', $changes);
