<?php

namespace modules\tax\Y2022;

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
        '2022-04-18',
        '2022-06-15',
        '2022-09-15',
        '2023-01-17',
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
    const YEAR = 2022;

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function head_of_household(): array
    {
        return [
            10 => 14200,
            12 => 54200,
            22 => 86350,
            24 => 164900,
            32 => 209400,
            35 => 523600,
            37 => null,
        ];
    }

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function married_joint(): array
    {
        return [
                10 => 20550,
                12 => 83550,
                22 => 178150,
                24 => 340100,
                32 => 431900,
                35 => 647850,
                37 => null,
        ];
    }

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function married_individual(): array
    {
        return [
            10 => 9950,
            12 => 40525,
            22 => 86375,
            24 => 164925,
            32 => 209425,
            35 => 314150,
            37 => null,
        ];
    }

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
