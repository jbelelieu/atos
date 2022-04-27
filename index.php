<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * The main entry point for the entire application.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Load the app
 *
 */

define('ATOS_HOME_DIR', __DIR__);

require "includes/system.php";

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Basic routing
 *
 */

$rUri = explode('?', $_SERVER['REQUEST_URI']);
$requestUri = $rUri['0'];

switch (strtolower($requestUri)) {
    case '/invoice':
        require ATOS_HOME_DIR . '/pages/invoice.php';
        break;
    case '/project':
        require ATOS_HOME_DIR . '/pages/project.php';
        break;
    case '/settings':
        require ATOS_HOME_DIR . '/pages/settings.php';
        break;
    // case '/story':
    //     require ATOS_HOME_DIR . '/pages/story.php';
    //     break;
    case '/tax':
        require ATOS_HOME_DIR . '/pages/taxes.php';
        break;
    case '/tax/render':
        require ATOS_HOME_DIR . '/pages/taxRender.php';
        break;
    default:
        require ATOS_HOME_DIR . '/pages/overview.php';
}
