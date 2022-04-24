<?php

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
 * @param $money
 * @return string
 */
function formatMoney($money): string
{
    return '$' . number_format($money / 100, 2);
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
