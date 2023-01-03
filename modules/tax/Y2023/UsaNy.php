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

class UsaNy
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
    const REGION = 'New York State';
    
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
            '4' => 8500,
            '4.5' => 11700,
            '5.25' => 13900,
            '5.50' => 80650,
            '6.00' => 96800,
            '7.14' => 107650,
            '7.64' => 157650,
            '6.50' => 215400,
            '11.01' => 265400,
            '7.35' => 1077550,
            '10.45' => 5000000,
            '11.1' => 25000000,
            '11.7' => null,
        ];
    }
}
