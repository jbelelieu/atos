<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Down and Dirty
 *
 */

if (!empty($_GET['id'])) {
    $file = ATOS_HOME_DIR  . '/_generated/' . $_GET['id'];

    if (!file_exists($file)) {
        redirect(
            '/documents',
            null,
            null,
            language('error_invalid_id', 'You need to provide a valid ID')
        );
    }

    $data = file_get_contents($file);

    echo $data;
    exit;
}

$invoices = [];
$reports = [];
$tax = [];
$files = [];

foreach (scandir(ATOS_HOME_DIR . '/_generated') as $file) {
    if ($file === '.' || $file === '..' || $file  === '.gitignore' || $file === '.DS_Store') {
        continue;
    }

    $exp = explode('-', $file);
    
    $entry = [
        'file' => $file,
        'date' => formatDate($exp['1']),
    ];

    switch ($exp[0]) {
        case 'invoice':
            $invoices[] = $entry;
            break;
        case 'report':
            $reports[] = $entry;
            break;
        case 'tax':
            $tax[] = $entry;
            break;
        default:
            $files[] = $entry;
    }
}
 
echo template(
    'admin/files',
    [
        '_metaTitle' => 'Generated Documents (ATOS)',
        'files' => $files,
        'invoices' => $invoices,
        'reports' => $reports,
        'taxes' => $tax,
    ]
);
exit;
