<?php

namespace services;

use services\BaseService;
use services\ProjectService;
use services\SettingService;

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
class StoryService extends BaseService
{
    private $projectService;
    private $settingService;

    public function __construct()
    {
        parent::__construct();

        $this->projectService = new ProjectService();
        $this->settingService = new SettingService();
    }

    /**
     * @param integer $projectId
     * @param [type] $itemId
     * @param boolean $skipMoveCollection
     * @param integer $skipStatusId
     * @param boolean $skipStatuses
     * @return string
     */
    public function buildStoryOptions(
        int $projectId,
        $itemId,
        bool $skipMoveCollection = false,
        $skipStatusId = 0,
        bool $skipStatuses = false
    ): string {
        // TODO: oof
        global $storyStatuses;

        $options = (!$skipMoveCollection)
            ? "<a title=\"" . language('move_collections', 'Move Collections') . "\" href=\"/project?action=shiftCollection&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon('icofont-undo') . "</a>"
            : '';

        if (!$skipStatuses) {
            foreach ($storyStatuses as $aStatus) {
                if ($skipStatusId && $skipStatusId == $aStatus['id']) {
                    continue;
                }

                $options .= "<a title=\"" . $aStatus['title'] . "\" href=\"/project?action=updateStoryStatus&status=" . $aStatus['id'] . "&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon($aStatus['emoji'], $aStatus['color']) . "</a>";
            }
        }

        return $options;
    }

    /**
     * @param array $data
     * @return void
     */
    public function createStory(array $data): void
    {
        $id = (isset($data['show_id']) && (!empty($data['show_id'])))
            ? $data['show_id']
            : $this->generateTicketId($data['project_id']);

        $statement = $this->db->prepare('
            INSERT INTO story (show_id, due_at, title, collection, rate_type, type, status)
            VALUES (:show_id, :due_at, :title, :collection, :rate_type, :type, :status)
        ');

        $statement->bindParam(':show_id', $id);
        $statement->bindParam(':due_at', $data['due_at']);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':collection', $data['collection']);
        $statement->bindParam(':rate_type', $data['rate_type']);
        $statement->bindParam(':type', $data['type']);
        $statement->bindParam(':status', $data['status']);

        $statement->execute();

        redirect('/project', $data['project_id'], 'Your new story has been created as ' . $id);
    }

    /**
     * @param array $data
     * @return void
     */
    public function deleteStory(array $data): void
    {
        $statement = $this->db->prepare('
            DELETE FROM story WHERE id = :id
        ');

        $statement->bindParam(':id', $data['id']);

        $statement->execute();

        redirect('/project', $data['project_id'], 'Your story has been deleted.');
    }

    /**
     * @param integer $projectId
     * @return string
     */
    public function generateTicketId(int $projectId): string
    {
        $project = $this->projectService->getProjectById($projectId);

        $totalStoriesInProject = $this->getNextStoryNumberForProject($projectId);

        $id = $project['code'] . '-' . $totalStoriesInProject;

        return $project['code'] . '-' . $totalStoriesInProject;
    }

    /**
     * @param integer $storyId
     * @return array
     */
    public function getStory(int $storyId)
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM story
            WHERE id = :id
        ');

        $statement->bindParam(':id', $storyId);

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * @param integer $id
     * @return int
     */
    public function getNextStoryNumberForProject(int $id): int
    {
        try {
            $statement = $this->db->prepare('
                SELECT story.show_id
                FROM story
                JOIN story_collection ON story.collection = story_collection.id
                WHERE story_collection.project_id = :id
                ORDER BY story.id DESC
            ');

            $statement->bindParam(':id', $id);

            $statement->execute();

            $results = $statement->fetch();

            if (!$results) {
                return 1;
            }
        
            $count = explode('-', $results['show_id']);
            
            return (int) $count[1] + 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function updateStories(array $data): void
    {
        foreach ($data['story'] as $storyId => $aStory) {
            if (empty($aStory['title'])) {
                continue;
            }

            // Default to existing, overwrite anything incoming...
            $currentStory = $this->getStory($storyId);
            $aStory = array_merge($currentStory, $aStory);

            $statement = $this->db->prepare('
                UPDATE
                    story
                SET
                    hours = :hours,
                    type = :type,
                    rate_type = :rate_type,
                    title = :title,
                    ended_at = :ended_at
                WHERE
                    id = :id
            ');

            $hours = (int) $aStory['hours'];
            $type = (int) $aStory['type'];
            $rateType = (int) $aStory['rate_type'];

            $statement->bindParam(':ended_at', $aStory['ended_at']);
            $statement->bindParam(':hours', $hours);
            $statement->bindParam(':type', $type);
            $statement->bindParam(':rate_type', $rateType);
            $statement->bindParam(':title', $aStory['title']);
            $statement->bindParam(':id', $storyId);
            $statement->execute();
        }

        redirect('/project', $data['project_id'], 'Your stories have been updated.');
    }

    /**
     * @param array $data
     * @return void
     */
    public function updateStoryStatus(array $data): void
    {
        if (!isset($data['status']) || empty($data['status'])) {
            redirect('/project', $data['project_id'], null, 'Invalid status received.');
        }

        $status = $this->settingService->getStoryStatusById($data['status']);
        $story = $this->getStory($data['id']);

        $hours = 0;
        if ((int) $story['hours'] > 0) {
            $hours = $story['hours'];
        } elseif (isBool($status['is_billable_state'])) {
            $hours = 1;
        }
    
        $statement = $this->db->prepare('
            UPDATE story
            SET status = :status, ended_at = :ended_at, hours = :hours
            WHERE id = :id
        ');

        $statement->bindParam(':status', $data['status']);
        $statement->bindParam(':hours', $hours);
        $statement->bindParam(':id', $data['id']);
        $statement->bindParam(':ended_at', date('Y-m-d H:i:s'));
        $statement->execute();

        $status = $this->settingService->getStoryStatusById($data['status']);

        redirect(
            '/project',
            $data['project_id'],
            'Your status of your story has been changed to "' . $status['title'] . '".'
        );
    }
}
