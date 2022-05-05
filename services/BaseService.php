<?php

namespace services;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Functionality shared by all services.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */
class BaseService
{
    protected $db;

    public function __construct()
    {
        global $db; // TODO: you know what to do...

        $this->db = $db;
    }
}
