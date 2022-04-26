<?php
$pageTitle = (isset($_metaTitle)) ? $_metaTitle : 'ATOS';
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title><?php echo $pageTitle; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/style.css" />
    <link rel="stylesheet" href="/assets/icons.css" />
    <script type="text/javascript" src="/assets/jquery.js"></script>
    <link rel="icon" type="image/png" href="/assets/atos_icon.png" />
</head>

<body>
<a name="top"></a>
<header>
    <div class="headerColumns">
        <div id="logo">
            <div id="icon"><img src="/assets/atos_icon.png" style="width:14px;height:14px" alt="ASOS Icon" /></div>
            <div id="company"><a href="/">ATOS</a></div>
        </div>
        <div>

<?php
if (!empty($_GET['_success'])) {
    echo "<span class=\"success\">" . $_GET['_success'] . "</span>";
}
if (!empty($_GET['_error'])) {
    echo "<span class=\"error\">" . $_GET['_error'] . "</span>";
}

$lastProject = (isset($_SESSION["viewingProject"]) && !empty($_SESSION["viewingProjectName"]))
    ? '<a href="/project?id=' . $_SESSION["viewingProject"] . '">' . $_SESSION["viewingProjectName"] . '</a>'
    : '';
?>
    
        </div>
        <div id="nav" class="textRight">
            <?php echo $lastProject; ?>
            <a href="/settings">Settings</a>
        <div>
    </div>
</header>
<div class="holder">
