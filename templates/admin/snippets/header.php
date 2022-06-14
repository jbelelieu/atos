<?php

use services\TaxService;

$pageTitle = (isset($_metaTitle)) ? $_metaTitle : 'ATOS';
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title><?php echo $pageTitle; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/style.css" />
    <link rel="stylesheet" href="/assets/icofont.min.css" />
    <link rel="icon" type="image/png" href="/assets/screens/atos_icon.png" />
    <script src="/assets/main.js"></script>
</head>

<body>
<div id="container">

<a name="top"></a>
<?php

// TODO: move this out of here but for now I don't care.
$TaxService = new TaxService();
$checkDate = date('Y-m-d');
$year = date('Y');
$dates = [];
$alert = '';
$moduleDir = ATOS_HOME_DIR . '/modules/tax/Y' . $year;
foreach (scandir($moduleDir) as $file) {
    if ($file === '..' || $file === '.') {
        continue;
    }
    $exp = explode('.', $file);
    $className = $exp[0];

    $combine = '\\modules\\tax\\Y' . $year . '\\' . $className;
    $class = new $combine();

    $dates = $class::ESTIMATED_TAXES_DUE;

    foreach ($dates as $aDate) {
        // TODO: check if it's already paid and ignore if it is.
        $difference = strtotime($aDate) - strtotime($checkDate);
        if ($difference > 0 && $difference <= 604800) {
            $alert .= '<span>Your estimated taxes for ' . $class::REGION . ' are due on ' . formatDate($aDate) . '</span>';
        }
    }
}

if ($alert) {
    echo "<div class=\"alert\"><marquee>" . $alert . "</marquee></div>";
}
?>
<header>
    <div class="headerColumns">
        <div id="logo">
            <div id="icon"><a href="/"><img src="/assets/screens/atos_icon.png" style="width:14px;height:14px" alt="ASOS Icon" /></a></div>
            <div id="company"><a href="/">ATOS</a></div>
        </div>
        <div>
            <form action="/search" method="get">
            <input type="text" autocomplete="off" name="query" required="required" placeholder="Search tasks" />
            </form>
        </div>

        <?php
        $lastProjectId = isset($_SESSION["viewingProject"]) ? $_SESSION["viewingProject"] : null;
        $lastProjectLink = $lastProjectId
            ? '<a href="/project?id=' . $lastProjectId . '">' . $_SESSION["viewingProjectName"] . '</a>'
            : '<a href="/project">Projects</a>';
        ?>
        
        <div id="nav" class="textRight">
            <?php if (is_array($allProjects) && sizeof($allProjects) > 0) { ?>
            <span>
                <select style="width: 150px;" name="projectDropdown" onChange="redirectBasedOnFormValue(this)">
                    <option value=""<?php echo (!$lastProjectId) ? 'selected=selected' : ''; ?>>Projects</option>
                    <?php
                    foreach ($allProjects as $aProject) {
                        $buildLink = buildLink(
                            '/project',
                            [
                                'id' => $aProject['id'],
                                "_success" => "Switched to project " . $aProject['title'],
                            ]
                        );

                        $selected = ($aProject['id'] === (int) $lastProjectId)
                            ? 'selected=selected'
                            : '';

                        echo "<option " .  $selected . "
                            value=\"" . $buildLink . "\">" . $aProject['title'] . "</option>";
                    } ?>
                </select>
            </span>
            <?php } ?>
            <?php echo $lastProjectLink; ?>
            <a href="/documents">Documents</a>
            <a href="/tax">Taxes</a>
            <a href="/settings">Settings</a>
        <div>
    </div>
</header>

<?php
if (!empty($_GET['_success'])) {
                        echo "<div class=\"success\">" . putIcon('checked') . $_GET['_success'] . "</div>";
                    }

if (!empty($_GET['_error'])) {
    echo "<div class=\"error\">" . putIcon('minus-circle', '#fff') . $_GET['_error'] . "</div>";
}
