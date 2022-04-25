<?php

return [
    // What is your database file called.
    'DATABASE_FILE_NAME' => 'atos.sqlite3',
    
    // How many days from issuances of the invoice should payment
    // be due? Set to zero (0) to not have a due date.
    'INVOICE_DUE_DATE_IN_DAYS' => 14,

    // If set to 'by_date', stories will be listed under the date on which
    // they were marked as completed.
    // If set to 'none', ATOS will not include stories on invoices.
    // If set to anything else, it will simple list all stories without dates.
    'INVOICE_ORDER_BY_DATE_COMPLETED' => 'by_date',

    // What should the default collection for a new project be called?
    'UNORGANIZED_NAME' => 'Unorganized',
];
