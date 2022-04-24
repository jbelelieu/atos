<?php

$title = (isset($inTitle)) ? $inTitle : 'ATOS';

echo <<<qq
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title>$title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
<header>
    <div class="headerColumns">
        <div>
            <span><a href="index.php">ATOS</a></span>
        </div>
        <div>
qq;

if (!empty($_GET['_success'])) {
    echo "<span class=\"success\">" . $_GET['_success'] . "</span>";
}

if (!empty($_GET['_error'])) {
    echo "<span class=\"error\">" . $_GET['_error'] . "</span>";
}


echo <<<qq
        </div>
        <div id="nav" class="textRight">
            <!--<a href="index.php">Projects</a>-->
            <!--<a href="taxes.php">Taxes</a>-->
            <a href="settings.php">Settings</a>
        <div>
    </div>
</header>
qq;
