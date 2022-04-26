<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * The purpose of this file is to render invoices.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

if (empty($_GET['collection'])) {
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
 *   Down and Dirty
 *
 */

$settingListType = getSetting(AsosSettings::INVOICE_ORDER_BY_DATE_COMPLETED, 'list');

$hoursByRateType = [];

$rateTypes = getRateTypes();
$collection = getCollectionById($_GET['collection']);
$project = getProjectById($collection['project_id']);
$company = getCompanyById($project['company_id']);
$clientCompany = getCompanyById($project['client_id']);

$invoiceCompletedString = language('completed_on', 'Tasks completed on');
$lastDate = null;
$storyHtml = '';
$dayHours = 0;
$totalHours = 0;
$shippedStories = getStoriesInCollection($_GET['collection'], false, 'ended_at ASC');
foreach ($shippedStories as $aStory) {
    if (!array_key_exists($aStory['rate_type'], $hoursByRateType)) {
        $hoursByRateType[$aStory['rate_type']] = 0;
    }

    $hoursByRateType[$aStory['rate_type']] = $hoursByRateType[$aStory['rate_type']] + $aStory['hours'];

    $dateDelivered = formatDate($aStory['ended_at'], 'Y/m/d');

    if ($lastDate !== $dateDelivered && $settingListType === 'by_date') {
        $storyHtml .= <<<qq
    <tr class="borderTop">
    <td colspan=3 class="dateHeader">$invoiceCompletedString$dateDelivered</td>
    </tr>
qq;

        $lastDate = $dateDelivered;
        $dayHours = 0;
    }

    $dayHours += (int) $aStory['hours'];
    $totalHours += (int) $aStory['hours'];

    $storyHtml .= <<<qq
<tr class="noBorder">
<td valign="top" class="tb-stories-id">$aStory[show_id]</td>
<td valign="top" width=400 class="tb-stories-title ellipsis">$aStory[title]</td>
<td valign="top" class="tb-stories-hour_tile">$aStory[hour_title]</td>
<td valign="top" class="tb-stories-hours">$aStory[hours]</td>
</tr>
qq;
}

// Build rate types table
$grandTotal = 0;
$ratesHtml = '';
$rateTypes = getRateTypes();
foreach ($rateTypes as $aType) {
    $dollarRate = formatMoney($aType['rate']);

    $hours = $hoursByRateType[$aType['id']];

    $subtotal = $hours * $aType['rate'];

    $grandTotal += $subtotal;

    $subtotal = formatMoney($subtotal);

    $ratesHtml .= <<<qq
<tr>
<td valign="top">$aType[title]</td>
<td valign="top">$dollarRate/hour</td>
<td valign="top">$hours</td>
<td valign="top">$subtotal</td>
</tr>
qq;
}

$logoUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/logo.png';

$daysDue = getSetting(AsosSettings::INVOICE_DUE_DATE_IN_DAYS, 14);
$dueDate = ($daysDue > 0) ? formatDate(date('Y-m-d H:i:s', time() + 1209600)) : '';

$logo = (file_exists(ATOS_HOME_DIR . '/logo.png'))
    ? '<div id="logoArea"><img src="' . $logoUrl . '" alt="' . $company['title'] . '" /></div>'
    : '';

$template = template(
    'invoice/invoice',
    [
        'client' => $clientCompany,
        'collection' => $collection,
        'company' => $company,
        'css' => file_get_contents('assets/invoiceStyle.css'),
        'displayStories' => ($settingListType === 'none') ? false : true,
        'dueDate' => $dueDate,
        'logo' => $logo,
        'project' => $project,
        'rateTypes' => $ratesHtml,
        'sentOn' => formatDate(date('Y-m-d H:i:s')),
        'stories' => $storyHtml,
        'total' => formatMoney($grandTotal),
        'totalHours' => $totalHours,
    ],
    true
);

if (!empty($_GET['save']) && $_GET['save'] === '1') {
    $filename = cleanFileName($project['title']) . '_' . date('Ymd') . '_' . cleanFileName($collection['title']) . '.html';

    file_put_contents(ATOS_HOME_DIR . '/invoices/' . $filename, $template);

    $msg = 'Saved invoice to invoices/' . $filename;
    redirect('/project', $collection['project_id'], $msg);
    exit;
}

// Render the entire page.
echo $template;
exit;
