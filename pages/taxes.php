<?php

use services\TaxService;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Estimated tax help for US-based developers.
 *
 * This represents the US Federal 2022 data.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

$changes = [];

echo template('tax/tax', $changes);
