<?php

require "includes/db.php";

if (empty($_GET['collection'])) {
    echo "No Collection ID.";
    exit;
}

$file = file_get_contents('templates/invoice.html');

$hoursByRateType = [];

$rateTypes = getRateTypes();
$collection = getCollectionById($_GET['collection']);
$project = getProjectById($collection['project_id']);
$company = getCompanyById($project['company_id']);
$clientCompany = getCompanyById($project['client_id']);

$lastDate = null;
$storyHtml = '';
$shippedStories = getStoriesInCollection($_GET['collection'], false, 'ended_at ASC');
foreach ($shippedStories as $aStory) {
    if (!array_key_exists($aStory['rate_type'], $hoursByRateType)) {
        $hoursByRateType[$aStory['rate_type']] = 0;
    }

    $hoursByRateType[$aStory['rate_type']] = $hoursByRateType[$aStory['rate_type']] + $aStory['hours'];

    $dateDelivered = formatDate($aStory['ended_at'], 'Y/m/d');

    if ($lastDate !== $dateDelivered) {
        $storyHtml .= <<<qq
    <tr class="borderTop">
    <td colspan=4 class="dateHeader">$dateDelivered</td>
    </tr>
qq;

        $lastDate = $dateDelivered;
    }

    $storyHtml .= <<<qq
<tr class="noBorder">
<td valign="top" class="tb-stories-id">$aStory[show_id]</td>
<td valign="top" width=400 class="tb-stories-title ellipsis">$aStory[title]</td>
<td valign="top" class="tb-stories-hour_tile">$aStory[hour_title]</td>
<td valign="top" class="tb-stories-hours">$aStory[hours]</td>
</tr>
qq;
}

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

$daysDue = getSetting(AsosSettings::INVOICE_DUE_DATE_IN_DAYS, 14);
$dueDate = ($daysDue > 0) ? formatDate(date('Y-m-d H:i:s', time() + 1209600)) : '';

$file = str_replace('%date%', date('Y/m/d'), $file);
$file = str_replace('%due_date%', $dueDate, $file);
$file = str_replace('%total%', formatMoney($grandTotal), $file);
$file = str_replace('%rate_types%', $ratesHtml, $file);
$file = str_replace('%stories%', $storyHtml, $file);
$file = str_replace('%invoice_title%', $project['title'] . ': ' . $collection['title'], $file);

$file = updateCallers($file, $collection, 'collection');
$file = updateCallers($file, $project, 'project');
$file = updateCallers($file, $company, 'company');
$file = updateCallers($file, $clientCompany, 'client');

$styles = file_get_contents('assets/invoiceStyle.css');
$file = str_replace('%css%', $styles, $file);

if (!empty($_GET['save']) && $_GET['save'] === '1') {
    $filename = cleanFileName($project['title']) . '_' . date('Ymd') . '_' . cleanFileName($collection['title']) . '.html';

    file_put_contents('invoices/' . $filename, $file);

    $msg = 'Saved invoice to invoices/' . $filename;
    redirect('project.php', $collection['project_id'], $msg);
    exit;
} else {
    echo $file;
    exit;
}
