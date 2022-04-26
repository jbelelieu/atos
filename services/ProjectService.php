<?php

require_once ATOS_HOME_DIR . '/services/BaseService.php';

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Service for projects.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */
class ProjectService extends BaseService
{
    /**
     * @param array $data
     * @return void
     */
    public function createProject(array $data): void
    {
        try {
            $this->db->beginTransaction();

            $statement = $this->db->prepare('
                INSERT INTO project (
                    company_id,
                    client_id,
                    title,
                    code
                )
                VALUES (
                    :company_id,
                    :client_id,
                    :title,
                    :code
                )
            ');

            $statement->bindParam(':company_id', $data['company_id']);
            $statement->bindParam(':client_id', $data['client_id']);
            $statement->bindParam(':title', $data['title']);
            $statement->bindParam(':code', $data['code']);
            $statement->execute();

            $lastProjectId = $this->db->lastInsertId();

            $statement = $this->db->prepare('
                INSERT INTO story_collection (
                    project_id,
                    title,
                    is_project_default
                )
                VALUES (
                    :project_id,
                    :title,
                    true
                )
            ');

            $statement->bindParam(':project_id', $lastProjectId);
            $statement->bindParam(':title', getSetting(AsosSettings::UNORGANIZED_NAME, 'Unorganized'));
            $statement->execute();

            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollback();

            systemError($e->getMessage());
        }

        redirect('/', null, 'Your project has been created; now go got get that bread.');
    }


    /**
     * @param array $data
     * @return void
     */
    public function deleteProject(array $data): void
    {
        $statement = $this->db->prepare('
            DELETE FROM project
            WHERE id = :id
        ');

        $statement->bindParam(':id', $data['id']);

        $statement->execute();

        redirect('/', null, 'That company has been deleted. Bye forever, I guess.');
    }
}