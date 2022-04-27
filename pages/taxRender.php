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

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteAdjustment':
            $taxService->deleteAdjustment($_GET);
            break;
        case 'deleteDeduction':
            $taxService->deleteDeduction($_GET);
            break;
        default:
            redirect('/tax', null, null, 'Unknown action');
    }
}

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
    $displayType = 'Taxes ' . $year . ': Fixed Income Projection Estimate';

    $attentionMessage = 'This is an estimate based on a hypothetical year-end income.<br /><br />This attempts to project your tax burden for the year assuming that your total income for the year will be ' . formatMoney($overrideEstimatedTotal * 100) . '.';
} elseif ($doProjectedEstimate) {
    $displayType = 'Taxes ' . $year . ': Projected Estimate';

    $attentionMessage = 'This is an estimate based on your current daily average income projected through the end of the year.<br /><br /><b>Important:</b> Any additional income will change these numbers, giving you a larger tax burden. If you have a good idea of what you will make this year, try a "Fixed Income Projection" estimate instead.';
} else {
    $displayType = 'Taxes ' . $year . ': Actual Current Estimate';

    $attentionMessage = 'This is only valid if your income remains the same for the rest of the year.<br /><br /><b>Important:</b> Any additional income will change these numbers, giving you a larger tax burden. If you have a good idea of what you will make this year, try a "Fixed Income Projection" estimate instead.';
}

if (!file_exists(ATOS_HOME_DIR . '/modules/tax/Y' . $year)) {
    redirect('/tax', null, null, 'No tax strategies found for the submitted year: ' . $year);
}

$tax = 0;

// Get a list of regions we are paying taxes in.
$taxYes = $taxService->getTax($year);
if (empty($taxYes)) {
    redirect('/tax', null, null, 'No known tax regions for this year.');
}
$taxRegions = $taxYes['strategies'];

// Get our base income and set as initial taxable income.
$baseIncome = $taxService->getTotalBaseIncomeByYear($year);
// if ($baseIncome <= 0) {
//     redirect('/tax', null, null, 'No known income for the year in question.');
// }

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

    $loadfile = ATOS_HOME_DIR . '/modules/tax/Y' . $year . '/' . $aRegion . '.php';

    $combine = 'modules\tax\Y' . $year . '\\' . $aRegion;

    $taxClass = new $combine();
    if (!method_exists($taxClass, $filingStrategy)) {
        systemError('The tax strategy you are using, ' . $filingStrategy . ', does not exist in one of your tax files: ' . $loadfile);
    }

    $brackets = $taxClass->{$filingStrategy}();

    $taxResults = $taxService->calculateTax($brackets, $taxableIncome);

    $finalData[$aRegion] = [
        'results' => $taxResults,
        'filingStrategy' => snakeToEnglish($filingStrategy),
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

    $percent = $tax > 0 ? intval($aTaxRegionBurden['results']['tax']) / $tax : 0;

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

$queryString = '&year=' . $year;
$queryString .= (!empty($_GET['income'])) ? '&income=' . $_GET['income'] : '';
$queryString .= (!empty($_GET['estimate'])) ? '&estimate=' . $_GET['estimate'] : '';

$logoUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/assets/logo.png';

$changes = [
    'logo' => (file_exists(ATOS_HOME_DIR . '/assets/logo.png'))
        ? '<div id="logoArea"><img src="' . $logoUrl . '" /></div>'
        : '',
    'css' => file_get_contents('assets/taxStyle.css'),
    'queryString' => $queryString,
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
        'effectiveRate' => $taxableIncome > 0 ? round($tax / $taxableIncome, 2) * 100 : 0,
        'regions' => $finalData,
    ],
    '_raw' => [
        'taxBurdens' => $taxBurdens['data'],
        'deductions' => $deductions['data'],
    ],
];

// dd($changes);

echo template('tax/estimate', $changes, true);
