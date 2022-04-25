<?php

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
 * @param string $name
 * @param string $default
 * @return string
 */
function putIcon(string $name, string $color = '#111', string $default = 'fi-sr-circle'): string
{
    $explode = explode(' ', $name);
    
    $usePart = ($explode['0'] === 'fi') ? $explode[1] : $explode[0];

    $finalUse = (substr($usePart, 0, 3) === 'fi-') ? $usePart : $default;

    $color = ltrim($color, '#');

    return '<i style="color:#' . $color . ';" class="iconColor fi '. $finalUse . '"></i>';
}

/**
 * @param $money
 * @return string
 */
function formatMoney($money): string
{
    return '$' . number_format($money / 100, 2);
}

/**
 * @param string $page
 * @param string $id
 * @param string|null $success
 * @param string|null $error
 * @return void
 */
function redirect(
    string $page,
    string $id = null,
    string $success = null,
    string $error = null
): void {
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

    header('Location: ' . $url);
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
