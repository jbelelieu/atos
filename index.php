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

require "includes/db.php";

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
    default:
        require ATOS_HOME_DIR . '/pages/overview.php';

}
