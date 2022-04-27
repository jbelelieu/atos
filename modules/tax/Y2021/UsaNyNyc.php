<?php

namespace modules\tax\Y2021;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Tax File: USA / New York State (2021)
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
        '2022-04-15',
        '2022-06-15',
        '2022-09-15',
        '2023-01-15',
    ];

    const REGION = 'New York City';
    
    const YEAR = 2021;

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
