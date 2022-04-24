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

$storyHtml = '';
$shippedStories = getStoriesInCollection($_GET['collection'], false);
foreach ($shippedStories as $aStory) {
    if (!array_key_exists($aStory['rate_type'], $hoursByRateType)) {
        $hoursByRateType[$aStory['rate_type']] = 0;
    }

    $hoursByRateType[$aStory['rate_type']] = $hoursByRateType[$aStory['rate_type']] + $aStory['hours'];

    $dateDelivered = formatDate($aStory['ended_at'], 'Y/m/d');

    $storyHtml .= <<<qq
<tr>
<td valign="top">$aStory[show_id]</td>
<td valign="top">$aStory[title]</td>
<td valign="top">$aStory[hour_title]</td>
<td valign="top">$aStory[hours]</td>
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

$file = str_replace('%date%', date('Y/m/d'), $file);
$file = str_replace('%total%', formatMoney($grandTotal), $file);
$file = str_replace('%rate_types%', $ratesHtml, $file);
$file = str_replace('%stories%', $storyHtml, $file);
$file = str_replace('%invoice_title%', $project['title'] . ': ' . $collection['title'], $file);

$file = updateCallers($file, $project, 'project');
$file = updateCallers($file, $company, 'company');
$file = updateCallers($file, $clientCompany, 'client');

$styles = file_get_contents('style.css');
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
