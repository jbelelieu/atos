<?php
$pageTitle = (isset($_metaTitle)) ? $_metaTitle : 'ATOS';
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title>$pageTitle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/style.css" />
    <link rel="stylesheet" href="/assets/icons.css" />
    <script type="text/javascript" src="/assets/jquery.js"></script>
    <link rel="icon" type="image/png" href="/assets/atos_icon.png" />
</head>

<body>
<header>
    <div class="headerColumns">
        <div id="logo">
            <div id="icon"><img src="/assets/atos_icon.png" style="width:14px;height:14px" alt="ASOS Icon" /></div>
            <div id="company"><a href="/index.php">ATOS</a></div>
        </div>
        <div>

<?php
if (!empty($_GET['_success'])) {
    echo "<span class=\"success\">" . $_GET['_success'] . "</span>";
}
if (!empty($_GET['_error'])) {
    echo "<span class=\"error\">" . $_GET['_error'] . "</span>";
}

$lastProject = (isset($_SESSION["viewingProject"]) && !empty($_SESSION["viewingProject"]))
    ? '<a href="/project.php?id=' . $_SESSION["viewingProject"] . '">Back to ' . $_SESSION["viewingProjectName"] . '</a>'
    : '';
?>
    
        </div>
        <div id="nav" class="textRight">
            $lastProject
            <a href="/settings.php">Settings</a>
        <div>
    </div>
</header>
