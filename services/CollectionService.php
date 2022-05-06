<?php

namespace services;

use services\BaseService;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Service for all things collections.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */
class CollectionService extends BaseService
{
    /**
     * @var services\SettingService
     */
    private $settingService;

    /**
     * @var services\StoryService
     */
    private $storyService;

    public function __construct()
    {
        parent::__construct();

        $this->settingService = new SettingService();
        $this->storyService = new StoryService();
    }

    /**
     * @param array $data
     * @return void
     */
    public function createCollection(array $data): void
    {
        $collections = $this->getCollectionByProject($data['project_id'], 1);

        $currentCollection = $collections[0];

        $statement = $this->db->prepare('
            INSERT INTO story_collection (title, project_id, goals, ended_at)
            VALUES (:title, :project_id, :goals, :ended_at)
        ');

        $statement->bindParam(':project_id', $data['project_id']);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':ended_at', $data['ended_at']);
        $statement->bindParam(':goals', $data['goals']);
        $statement->execute();

        if (sizeof($collections) > 1) {
            $this->makeCurrentCollection([ 'id' => $currentCollection['id'] ], false);
        }

        redirect('/project', $data['project_id'], 'Your collection has been created.');
    }

    /**
     * null should probably be "unorganized" but let's roll
     * the dice and assume no one's gonna delete the ID
     * from the database directly.
     *
     * @param array $data
     * @return void
     */
    public function deleteCollection(array $data): void
    {
        $collection = $this->getCollectionById($data['id']);

        $isDefault = parseBool($collection['is_project_default']);
        if ($isDefault) {
            redirect(
                '/project',
                $data['project_id'],
                '',
                'You cannot delete the "Unorganized" collection from a project.'
            );
        }

        // Delete the collection.
        $statement = $this->db->prepare('
            DELETE FROM story_collection WHERE id = :id
        ');
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        // Move everything to unorganized.
        $defaultCollection = $this->getDefaultCollectionForProject($data['project_id']);
        $statement = $this->db->prepare('
            UPDATE story
            SET collection = :collection
            WHERE collection = :old_collection
        ');
        $statement->bindParam(':collection', $defaultCollection['id']);
        $statement->bindParam(':old_collection', $data['id']);
        $statement->execute();

        redirect(
            '/project',
            $data['project_id'],
            'Your collection has been deleted. All stories in that collection have been moved to your default collection for the project.'
        );
    }
        
    /**
     * @param integer $projectId
     * @return void
     */
    public function getDefaultCollectionForProject(int $projectId)
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM story_collection
            WHERE
                project_id = :project_id
                AND is_project_default = true
            ORDER BY created_at DESC
            LIMIT 1
        ');

        $statement->bindParam(':project_id', $projectId);

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * @param integer $projectId
     * @return void
     */
    public function getLatestCollectionForProject(int $projectId)
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM story_collection
            WHERE project_id = :project_id
            ORDER BY created_at DESC
            LIMIT 1
        ');

        $statement->bindParam(':project_id', $projectId);

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getCollectionById(int $id)
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM story_collection
            WHERE id = :id
        ");

        $statement->bindParam(':id', $id);

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * @param integer $projectId
     * @return array
     */
    public function getCollectionByProject(int $projectId, int $limit = 5)
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM story_collection
            WHERE project_id = :id
            ORDER BY created_at DESC
            LIMIT :limit
        ");

        $statement->bindParam(':id', $projectId);
        $statement->bindParam(':limit', $limit);

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * TODO: This is a mess and needs to be rebuilt.
     *
     * @param integer $collectionId
     * @param boolean $isOpen
     * @param string $order
     * @param boolean $isOpen
     * @param boolean $showCompleteNotBillable
     * @return array
     */
    public function getStoriesInCollection(
        int $collectionId,
        bool $isOpen = true,
        string $order = 'status ASC, created_at DESC',
        bool $billableOnly = false,
        bool $showCompleteNotBillable = false
    ) {
        $collection = $this->getCollectionById($collectionId);
        $isDefaultCollection = parseBool($collection['is_project_default']);

        $statusQuery = '';
        if (!$isDefaultCollection) {
            $statusQuery .= ($isOpen)
            ? ' AND story_status.is_complete_state = false'
            : ' AND story_status.is_complete_state = true';

            $statusQuery .= ($billableOnly && !$showCompleteNotBillable)
            ? ' AND story_status.is_billable_state = true'
            : '';
        }

        $statement = $this->db->prepare("
            SELECT
                story.*,
                story_type.title as type_title,
                story_hour_type.title as hour_title,
                story_hour_type.rate as hour_rate,
                story_status.title as status_title,
                story_status.is_complete_state,
                story_status.is_billable_state,
                story_status.title as status_id,
                story_status.emoji as status_emoji,
                story_status.color as status_color
            FROM story
            JOIN story_type ON story.type = story_type.id
            JOIN story_status ON story.status = story_status.id
            JOIN story_hour_type ON story.rate_type = story_hour_type.id
            WHERE
                story.collection = :collection
                $statusQuery
            ORDER BY
                $order
        ");

        $statement->bindParam(':collection', $collectionId);

        $statement->execute();

        $items = $statement->fetchAll();

        return $items;
    }

    /**
     * @param array $data
     * @param bool $redirect
     * @return void
     */
    public function makeCurrentCollection(array $data, bool $redirect = true): void
    {
        $statement = $this->db->prepare('
            UPDATE story_collection
            SET created_at = :date
            WHERE id = :id
        ');

        $date = date('Y-m-d H:i:s');
        $statement->bindParam(':id', $data['id']);
        $statement->bindParam(':date', $date);

        $statement->execute();

        if ($redirect) {
            redirect('/project', $data['project_id'], 'Now working with a new collection.');
        }
    }


    /**
     * Workflow for shifting is as follows:
     * - If part of the project's default collection, set to current collection
     * - If part of current collection and isn't in a "completed" state, shift to default collection
     * - If part of current collection and is in a "completed" state, shift to current collection.
     *
     * @param array $data
     * @return void
     */
    public function shiftCollection(array $data): void
    {
        $story = $this->storyService->getStory($data['id']);
        $currentCollection = $this->getCollectionById($story['collection']);
        $currentStatus = $this->settingService->getStoryStatusById($story['status']);

        $isStoryInDefaultCollection = parseBool($currentCollection['is_project_default']);

        if ($isStoryInDefaultCollection) {
            $useCollection = $this->getLatestCollectionForProject($data['project_id']);
        } else {
            $useCollection = ($currentStatus['is_complete_state'])
                ? $this->getLatestCollectionForProject($data['project_id'])
                : $this->getDefaultCollectionForProject($data['project_id']);
        }

        $intHours = (int) $story['hours'];
        $hours = ($intHours > 1) ? $intHours : 1;

        $statement = $this->db->prepare('
            UPDATE
                story
            SET
                collection = :collection,
                status = 1,
                hours = :hours
            WHERE
                id = :id
        ');
        $statement->bindParam(':hours', $hours);
        $statement->bindParam(':collection', $useCollection['id']);
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        $msg = 'Your story is now part of the "' . $useCollection['title'] . '" collection.';
        
        redirect('/project', $data['project_id'], $msg);
    }
}
