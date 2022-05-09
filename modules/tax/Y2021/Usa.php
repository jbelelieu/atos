<?php

namespace modules\tax\Y2021;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Tax File: USA Federal (2021)
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
        '2021-04-15',
        '2021-06-15',
        '2021-09-15',
        '2022-01-15',
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
    const YEAR = 2021;

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function single(): array
    {
        return [
            10 => 9950,
            12 => 40525,
            22 => 86375,
            24 => 164925,
            32 => 209425,
            35 => 523600,
            37 => null,
        ];
    }
}
