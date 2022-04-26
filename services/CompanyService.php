<?php

require_once ATOS_HOME_DIR . '/services/BaseService.php';

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Service for companies/clients.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */
class CompanyService extends BaseService
{
    /**
     * @param array $data
     * @return void
     */
    public function createCompany(array $data): void
    {
        $statement = $this->db->prepare('
            INSERT INTO company (
                title,
                logo_url,
                address,
                phone,
                email,
                instructions,
                url
            )
            VALUES (
                :title,
                :logo_url,
                :address,
                :phone,
                :email,
                :instructions,
                :url
            )
        ');

        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':logo_url', $data['logo_url']);
        $statement->bindParam(':address', nl2br($data['address']));
        $statement->bindParam(':phone', $data['phone']);
        $statement->bindParam(':email', $data['email']);
        $statement->bindParam(':instructions', nl2br($data['instructions']));
        $statement->bindParam(':url', $data['url']);

        $statement->execute();

        redirect('/', null, 'The "' . $data['title'] . '" company has been created.');
    }

    /**
     * @param array $data
     * @return void
     */
    public function deleteCompany(array $data): void
    {
        $statement = $this->db->prepare('
            DELETE FROM company
            WHERE id = :id
        ');

        $statement->bindParam(':id', $data['id']);

        $statement->execute();

        redirect('/', null, 'That company has been deleted. Bye forever, I guess.');
    }
}
