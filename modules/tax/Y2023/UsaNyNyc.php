<?php

namespace modules\tax\Y2023;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Tax File: USA / New York State (2022)
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

class UsaNyNyc
{
    /**
     * Dates on which estimated taxes are due
     */
    const ESTIMATED_TAXES_DUE = [
        '2023-04-18',
        '2023-06-15',
        '2023-09-15',
        '2024-01-16',
    ];

    /**
     * Online payment portal
     */
    const link = 'https://www.tax.ny.gov/pay/ind/pay-estimated-tax.htm';

    /**
     * Name of the region
     */
    const REGION = 'New York City';
    
    /**
     * Year this tax file is relevant for
     */
    const YEAR = 2023;

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function single(): array
    {
        return [
            '3.078' => 12000,
            '3.762' => 25000,
            '3.819' => 50000,
            '3.876' => null,
        ];
    }
}
