<?php

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
    const ESTIMATED_TAXES_DUE = [
        '2022-04-18',
        '2022-06-15',
        '2022-09-15',
        '2023-01-17',
    ];

    const REGION = 'New York City';
    
    const YEAR = 2022;

    public function headOfHousehold(): array
    {
        return [
            '3.078' => 14400,
            '3.762' => 30000,
            '3.819' => 60000,
            '3.876' => null,
        ];
    }

    public function marriedIndividual(): array
    {
        return $this->single;
    }

    public function marriedJoint(): array
    {
        return [
            '3.078' => 21600,
            '3.762' => 45000,
            '3.819' => 90000,
            '3.876' => null,
        ];
    }

    public function qualifiedWidower(): array
    {
        return $this->marriedJoint();
    }

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
