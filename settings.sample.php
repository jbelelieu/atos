<?php

return [
    // If you would like to perform daily database backups,
    // input the FTP information for the server to which 
    // we will be backing up the local database.
    'BACKUP_FTP_SERVER' => [
        'port' => 21,
        'host' => '',
        'username' => '',
        'password' => '',
        'remoteFilePath' => '',
    ],

    // What is your database file called.
    'DATABASE_FILE_NAME' => 'atos.sqlite3',

    // Percent of additional estimated taxes you'd like
    // to add to the actual calcuated totals. This is to
    // prevent potential underpayment penalties.
    'EST_TAXES_ADD_SAFETY_BUFFER' => 10,

    // Path to your logo file
    'LOGO_FILE' => 'assets/logo.png',
    
    // How many days from issuances of the invoice should payment be due?
    // Set to zero (0) to not have a due date.
    'INVOICE_DUE_DATE_IN_DAYS' => 14,

    // @options  by_date | none | list
    //
    // by_date: tasks will be listed under the date on which
    //          they were marked as completed.
    // none:    ATOS will not include tasks on invoices.
    // list:    If set to anything else, it will simple list all tasks without dates.
    'INVOICE_ORDER_BY_DATE_COMPLETED' => 'list',

    // What should the default collection for a new project be called?
    'UNORGANIZED_NAME' => 'Unorganized',
];
