<?php

namespace services;

use services\BaseService;

// require_once ATOS_HOME_DIR . '/services/BaseService.php';

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

    /**
     * @return array
     */
    public function getCompanies()
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM company
        ");

        $statement->execute();

        return $statement->fetchAll();
    }
    
    /**
     * @return array
     */
    public function getCompanyById(int $companyId)
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM company
            WHERE id = :id
        ");

        $statement->bindParam(':id', $companyId);

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * @param integer $clientId
     * @return array
     */
    public function getCompanyTotals(int $clientId)
    {
        $statement = $this->db->prepare("
            SELECT
                COALESCE(SUM(story.hours), 0) as hours,
                COALESCE(SUM(story.hours * story_hour_type.rate), 0) as total
            FROM
                project
            JOIN
                story_collection
                ON project.id = story_collection.project_id
            JOIN
                story
                ON story_collection.id = story.collection
            JOIN
                story_hour_type
                ON story_hour_type.id = story.rate_type
            WHERE
                client_id = :client_id
                AND story.status != 1;
        ");

        $statement->bindParam(':client_id', $clientId);

        $statement->execute();

        return $statement->fetch();
    }
}
