<?php

namespace services;

use services\BaseService;

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

            $orgName = getSetting('UNORGANIZED_NAME', 'Unorganized');
            
            $statement->bindParam(':project_id', $lastProjectId);
            $statement->bindParam(':title', $orgName);
            $statement->execute();

            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollBack();

            systemError($e->getMessage());
        }

        redirect('/', null, 'Your project has been created; now go got get that bread!');
    }

    /**
     * @param array $data
     * @return void
     */
    public function markProjectComplete(array $data): void
    {
        $statement = $this->db->prepare('
            UPDATE project
            SET ended_at = :date
            WHERE id = :id
        ');

        $statement->bindParam(':id', $data['id']);
        $statement->bindParam(':date', date('Y-m-d H:i:s'));

        $statement->execute();

        redirect('/', null, 'The project has been marked as completed.');
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
    public function getProjects(bool $activeOnly = false)
    {
        $sql = "
            SELECT
                p.*,
                c1.title as company_name,
                c2.title as client_name
            FROM project p
            JOIN company c1 ON p.client_id = c1.id
            JOIN company c2 ON p.company_id = c2.id
        ";

        if ($activeOnly) {
            $sql .= " WHERE p.ended_at IS NULL";
        }

        $statement = $this->db->prepare($sql);

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
     * @param array $array
     * @param string $tableKey
     * @param array $bound
     * @return void
     */
    protected function buildWhereIn(
        array $array,
        string $tableKey,
        array $bound
    ) {
        $placeholders = '';

        foreach ($array as $anItem => $x) {
            $bound[] = $anItem;

            $placeholders .= ',?';
        }

        return (sizeof($array) > 0)
            ? [
                ' AND story.' . $tableKey . ' IN (' . ltrim($placeholders, ',') . ')',
                $bound
            ]
            : [
                '',
                $bound
            ];
    }

    /**
     * TODO: Allow for custom ordering
     * 
     * @param integer $projectId
     * @param array $types
     * @param array $statuses
     * @param array $completedOnRange
     * @return array
     */
    public function getStoriesByFilters(
        int $projectId,
        array $types = [],
        array $statuses = [],
        array $completedOn = [],
        array $collections = []
    ): array
    {
        $whereIn = $this->buildWhereIn($types, 'type', []);
        $whereType = $whereIn[0];

        $whereIn = $this->buildWhereIn($statuses, 'status', $whereIn['1']);
        $whereStatus = $whereIn[0];

        $whereIn = $this->buildWhereIn($collections, 'collection', $whereIn['1']);
        $whereCollection = $whereIn[0];

        $bound = $whereIn['1'];
        
        $whereCompleted = '';

        if (!array_key_exists('start', $completedOn)) {
            $completedOn['start'] = null;
        }

        if (!array_key_exists('end', $completedOn)) {
            $completedOn['end'] = null;
        }

        if (!empty($completedOn)) {
            if (!empty($completedOn['start']) && !empty($completedOn['end'])) {
                $whereCompleted = ' AND story.ended_at >= ? AND story.ended_at <= ?';

                $bound[] = $completedOn['start'];
                $bound[] = $completedOn['end'];
            } else if (!empty($completedOn['start']) && empty($completedOn['end'])) {
                $whereCompleted = ' AND story.ended_at = ?';

                $bound[] = $completedOn['start'];
            } else if (empty($completedOn['start']) && !empty($completedOn['end'])) {
                $whereCompleted = ' AND story.ended_at <= ?';

                $bound[] = $completedOn['end'];
            }
        }

        $statement = $this->db->prepare("
            SELECT
                story.*,
                story_hour_type.title as rateTypeTitle,
                story_hour_type.rate as rate,
                story_status.title as statusTitle,
                story_type.title as typeTitle,
                story_status.is_complete_state isComplete,
                story_status.is_billable_state isBillable
            FROM story
            JOIN story_collection on story_collection.id = story.collection
            JOIN story_hour_type on story_hour_type.id = story.rate_type
            JOIN story_status on story_status.id = story.status
            JOIN story_type on story_type.id = story.type
            WHERE story_collection.project_id = ?
            $whereType$whereStatus$whereCollection$whereCompleted
            ORDER BY ended_at ASC, type ASC
        ");

        $statement->execute(array_merge([ $projectId ], $bound));

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
            JOIN
                story_status
                ON story_status.id = story.status
            WHERE
                project.id = :project_id
                AND story.status != 1
                AND story_status.is_complete_state = true
                AND story_status.is_billable_state = true;
        ");
        // $statement = $this->db->prepare("
        //     SELECT
        //         COALESCE(SUM(story.hours), 0) as hours,
        //         COALESCE(SUM(story.hours * story_hour_type.rate), 0) as total
        //     FROM
        //         project
        //     JOIN
        //         story_collection
        //         ON project.id = story_collection.project_id
        //     JOIN
        //         story
        //         ON story_collection.id = story.collection
        //     JOIN
        //         story_hour_type
        //         ON story_hour_type.id = story.rate_type
        //     WHERE
        //         project.id = :project_id
        //         AND story.status != 1
        // ");

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
