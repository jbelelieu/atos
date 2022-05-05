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

class UsaNyNyc
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
    const REGION = 'New York City';
    
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
            '3.078' => 14400,
            '3.762' => 30000,
            '3.819' => 60000,
            '3.876' => null,
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
            '3.078' => 21600,
            '3.762' => 45000,
            '3.819' => 90000,
            '3.876' => null,
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
            '3.078' => 12000,
            '3.762' => 25000,
            '3.819' => 50000,
            '3.876' => null,
        ];
    }
}
