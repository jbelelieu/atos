<?php

namespace modules\tax\Y2021;

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
        '2021-04-15',
        '2021-06-15',
        '2021-09-15',
        '2022-01-15',
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
    const YEAR = 2021;

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
            '5.9' => 21400,
            '5.97' => 80650,
            '6.33' => 215400,
            '6.85' => 1077550,
            '9.65' => 5000000,
            '10.30' => 25000000,
            '10.90' => null,
        ];
    }
}
