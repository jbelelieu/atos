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
    const ESTIMATED_TAXES_DUE = [
        '2022-04-15',
        '2022-06-15',
        '2022-09-15',
        '2023-01-15',
    ];

    const REGION = 'Usa Federal';
    
    const YEAR = 2022;

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
