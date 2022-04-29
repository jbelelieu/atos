<?php

use services\ProjectService;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Various helper functions used throughout the application.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

/**
 * @param string $page
 * @param string $queryString
 * @return string
 */
function buildLink(string $page, array $queryString = []): string
{
    $url = $page;

    if (!empty($queryString)) {
        $url .= '?' . http_build_query($queryString);
    }

    return $url;
}

/**
 * @param string $name
 * @return string
 */
function camelToEnglish(string $name): string
{
    return snakeToEnglish(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)));
}

/**
 * @param string $input
 * @return string
 */
function cleanFileName(string $input): string
{
    return strtolower(str_replace(" ", "_", preg_replace("/[^A-Za-z0-9 ]/", '', $input)));
}

/**
 * @param $data
 * @return void
 */
function dd($data)
{
    echo "<PRE>";
    print_r($data);
    echo "</PRE>";
    exit;
}

/**
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate(string $date, string $format = 'Y/m/d'): string
{
    return date($format, strtotime($date));
}

/**
 * SQLite3 and PHP don't play well with booleans, so we'll
 * create a function to manage this better.
 *
 * @param $value
 * @return boolean
 */
function isBool($value): bool
{
    $stringValue = strtolower((string) $value);

    return (
        $stringValue === '1'
        || $stringValue === 'true'
        || $stringValue === 'yes'
        || $stringValue === 'y'
    ) ? true : false;
}

/**
 * @param string $key
 * @param string $default
 * @return string
 */
function language(string $key, string $default = ''): string
{
    global $ATOS_LANGUAGE;

    return (array_key_exists($key, $ATOS_LANGUAGE)) ? $ATOS_LANGUAGE[$key] : $default;

    return $default;
}

/**
 * @param string $name
 * @param string $default
 * @return string
 */
function putIcon(
    string $name,
    string $color = '#111',
    string $default = 'icofont-question-circle'
): string {
    $useColor = empty($color) ? '#111' : $color;
    // $useDefault = empty($default) ? 'icofont-question-circle' : $default;
    $finalUse = (substr($name, 0, 7) === 'icofont') ? $name : 'icofont-' . $name;
    $color = ltrim($useColor, '#');

    return '<div class="iconHolder"><i style="color: #' . $color . ';" class="'. $finalUse . '"></i></div>';
}

/**
 * @param $money
 * @return string
 */
function formatMoney($money): string
{
    return '$' . number_format($money / 100, 0);
}

/**
 * @param string $altText
 * @return string
 */
function logo(string $altText = ''): string
{
    return (file_exists(ATOS_HOME_DIR . '/assets/logo.png'))
        ? '<div id="logoArea"><img src="/assets/logo.png" alt="' . $altText . '" /></div>'
        : '';
}

/**
 * @param string $page
 * @param string $id
 * @param string|null $success
 * @param string|null $error
 * @param bool $return
 * @param array $queryString
 * @param string $hash
 * @return void
 */
function redirect(
    string $page,
    $id = null,
    string $success = null,
    string $error = null,
    bool $return = false,
    array $queryString = [],
    string $hash = null
): string {
    $query = '';

    $url = $page;

    if ($id) {
        $query .= 'id=' . $id;
    }

    if ($success) {
        $query .= '&_success=' . urlencode($success);
    }

    if ($error) {
        $query .= '&_error=' . urlencode($error);
    }

    foreach ($queryString as $key => $value) {
        $query .= "&" . $key . "=" . $value;
    }

    if (!empty($query)) {
        $url .= '?' . $query;
    }

    if ($hash) {
        $url .= '#' . $hash;
    }

    if ($return) {
        return $url;
    }

    header('Location: ' . $url);
    exit;
}

/**
 * @param string $name
 * @return string
 */
function snakeToEnglish(string $name): string
{
    return ucwords(str_replace('_', ' ', $name));
}

/**
 * @param string $templateName
 * @param array $args
 * @param bool $skipHeaderFooter
 * @return void
 */
function template(
    string $templateName,
    array $args,
    bool $skipHeaderFooter = false
) {
    $path = ATOS_HOME_DIR . '/templates/' . $templateName . '.php';
    if (!file_exists($path)) {
        systemError('You are trying to load a template that does not exist: ' . $path);
    }

    extract($args);

    $allProjects = (new ProjectService())->getProjects();

    ob_start();
    
    if (!$skipHeaderFooter) {
        include ATOS_HOME_DIR . '/templates/admin/snippets/header.php';
    }

    include ATOS_HOME_DIR . '/templates/' . $templateName . '.php';

    if (!$skipHeaderFooter) {
        include ATOS_HOME_DIR . '/templates/admin/snippets/footer.php';
    }

    return ob_get_clean();
}


/**
 * @param string $msg
 * @return void
 */
function systemError(string $msg)
{
    echo <<<qq
<div style="color:#E44B58;border-radius:3px;font-size:0.9em;line-height:1.5em;border:1px solid #E44B58;border-bottom:3px solid #E44B58;padding:24px;width:800px;margin:42px auto;font-family:arial;font-weight:bold;">$msg</div>
qq;
    exit;
}

/**
 * @param string $file
 * @param array $data
 * @param string $objectKey
 * @return
 */
function updateCallers(string $file, array $data, string $objectKey)
{
    foreach ($data as $key => $value) {
        if ($value) {
            $file = str_replace('%' . $objectKey . '.' . $key . '%', $value, $file);
        }
    }

    return $file;
}
