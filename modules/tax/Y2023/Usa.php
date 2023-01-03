<?php

namespace modules\tax\Y2023;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Tax File: USA Federal (2022)
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

class Usa
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
    const link = 'https://www.irs.gov/payments';

    /**
     * Name of the region
     */
    const REGION = 'Usa Federal';
    
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
            10 => 10275,
            12 => 41775,
            22 => 89075,
            24 => 170050,
            32 => 215950,
            35 => 539900,
            37 => null,
        ];
    }
}
