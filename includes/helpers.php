<?php

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
 * @link https://stackoverflow.com/users/1034002/torkil-johnsen
 * @param [type] $hex
 * @param [type] $steps
 * @return void
 */
function adjustBrightness(string $hex, int $steps)
{
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2).str_repeat(substr($hex, 1, 1), 2).str_repeat(substr($hex, 2, 1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0, min(255, $color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

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
    string $default = 'fi-sr-circle'
): string {
    $useColor = empty($color) ? '#111' : $color;
    $useDefault = empty($default) ? 'fi-sr-circle' : $default;
    $explode = explode(' ', $name);
    
    $usePart = ($explode['0'] === 'fi') ? $explode[1] : $explode[0];

    $finalUse = (substr($usePart, 0, 3) === 'fi-') ? $usePart : $useDefault;

    $color = ltrim($useColor, '#');

    return '<i style="color:#' . $useColor . ';" class="iconColor fi '. $finalUse . '"></i>';
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
 * @param string $page
 * @param string $id
 * @param string|null $success
 * @param string|null $error
 * @param bool $return
 * @return void
 */
function redirect(
    string $page,
    $id = null,
    string $success = null,
    string $error = null,
    bool $return = false
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

    if (!empty($query)) {
        $url .= '?' . $query;
    }

    if ($return) {
        return $url;
    }

    header('Location: ' . $url);
    exit;
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
        systemError('You are trying to load a template that does not exist');
    }

    extract($args);

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
<div style="color:#E44B58;border-radius:3px;font-size:0.9em;line-height:1.5em;border:1px solid #E44B58;border-bottom:3px solid #E44B58;padding:24px;width:600px;margin:42px auto;font-family:arial;font-weight:bold;">$msg</div>
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
