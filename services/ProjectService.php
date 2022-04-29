<?php

namespace services;

use services\BaseService;

// require_once ATOS_HOME_DIR . '/services/BaseService.php';

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
            $statement->bindParam(':title', getSetting(\AtosSettings::UNORGANIZED_NAME, 'Unorganized'));
            $statement->execute();

            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollBack();

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

    /**
     * @return array
     */
    public function getProjects()
    {
        $statement = $this->db->prepare("
            SELECT
                p.*,
                c1.title as company_name,
                c2.title as client_name
            FROM project p
            JOIN company c1 ON p.client_id = c1.id
            JOIN company c2 ON p.company_id = c2.id
        ");

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getProjectById(int $id)
    {
        try {
            $statement = $this->db->prepare('
                SELECT project.*, company.title as company_name
                FROM project
                JOIN company ON project.client_id = company.id
                WHERE project.id=:id
            ');

            $statement->bindParam(':id', $id);

            $statement->execute();

            $res = $statement->fetch();
            if (!$res) {
                redirect('/', null, null, 'Something went wrong finding that project (A1).');
            }

            return $res;
        } catch (\PDOException $e) {
            redirect('/', null, null, 'Something went wrong finding that project (A2).');
        }
    }

    /**
     * @param array $types
     * @param array $status
     * @return void
     */
    public function getStoriesByFilters(int $projectId, array $types = [], array $statuses = [])
    {
        $bind = [];

        $placeholders = '';
        foreach ($types as $aType => $x) {
            $bind[] = $aType;
            $placeholders .= ',?';
        }
        $whereType = (sizeof($types) > 0)
            ? ' AND story.type IN (' . ltrim($placeholders, ',') . ')'
            : '';

        $placeholders = '';
        foreach ($statuses as $aStatus => $x) {
            $bind[] = $aStatus;
            $placeholders .= ',?';
        }
        $whereStatus = (sizeof($statuses) > 0)
            ? ' AND story.status IN (' . ltrim($placeholders, ',') . ')'
            : '';

        $statement = $this->db->prepare("
            SELECT
                story.*,
                story_hour_type.title as rateTypeTitle,
                story_hour_type.rate as rate,
                story_status.title as statusTitle,
                story_type.title as typeTitle
            FROM story
            JOIN story_collection on story_collection.id = story.collection
            JOIN story_hour_type on story_hour_type.id = story.rate_type
            JOIN story_status on story_status.id = story.status
            JOIN story_type on story_type.id = story.type
            WHERE story_collection.project_id = ?
            $whereType$whereStatus
            ORDER BY type ASC
        ");

        $statement->execute([$projectId, ...$bind]);

        return $statement->fetchAll();
    }
    /**
     * @param integer $projectId
     * @return array
     */
    public function getProjectTotals(int $projectId)
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
                project.id = :project_id
                AND story.status != 1;
        ");

        $statement->bindParam(':project_id', $projectId);

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        $statement = $this->db->prepare("
            SELECT
                story.*,
                project.title as projectTitle,
                project.id as projectId,
                story_collection.id as collectionId,
                story_collection.title as collectionTitle,
                story_hour_type.title as rateTypeTitle,
                story_hour_type.rate as rate,
                story_status.title as statusTitle,
                story_type.title as typeTitle
            FROM story
            JOIN story_collection ON story_collection.id = story.collection
            JOIN project ON story_collection.project_id = project.id
            JOIN story_hour_type on story_hour_type.id = story.rate_type
            JOIN story_status on story_status.id = story.status
            JOIN story_type on story_type.id = story.type
            WHERE story.title LIKE :query
        ");

        $query = '%' . $query . '%';

        $statement->bindParam(':query', $query);

        $statement->execute();

        return $statement->fetchAll();
    }
}
