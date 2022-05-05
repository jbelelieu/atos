<?php

namespace modules\tax\Y2022;

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
        '2022-04-18',
        '2022-06-15',
        '2022-09-15',
        '2023-01-17',
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
    const YEAR = 2022;

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function head_of_household(): array
    {
        return [
            '4' => 12800,
            '4.5' => 17650,
            '5.25' => 20900,
            '5.9' => 32200,
            '5.97' => 107650,
            '6.55' => 269300,
            '6.85' => 1616450,
            '9.65' => 5000000,
            '10.30' => 25000000,
            '10.90' => null,
        ];
    }

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function married_individual(): array
    {
        return $this->single;
    }

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function married_joint(): array
    {
        return [
            '4' => 17150,
            '4.5' => 23600,
            '5.25' => 27900,
            '5.9' => 43000,
            '5.97' => 161550,
            '6.55' => 323200,
            '6.85' => 2155350,
            '9.65' => 5000000,
            '10.30' => 25000000,
            '10.90' => null,
        ];
    }

    /**
     * Filing status option
     * Percent Taxes => Income up to
     */
    public function qualified_widower(): array
    {
        return $this->married_joint();
    }

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
            '6.55' => 215400,
            '6.85' => 1077550,
            '9.65' => 5000000,
            '10.30' => 25000000,
            '10.90' => null,
        ];
    }
}
