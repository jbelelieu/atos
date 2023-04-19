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
class StoryService extends BaseService
{
    /**
     * @var services\ProjectService
     */
    private $projectService;

    /**
     * @var services\SettingService
     */
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
        bool $skipStatuses = false,
        string $helpText = 'Move Collections'
    ): string {
        // TODO: oof
        global $storyStatuses;

        $options = (!$skipMoveCollection)
            ? "<a title=\"" . language('move_collections', $helpText) . "\" href=\"/project?action=shiftCollection&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon('icofont-box') . "</a>"
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
    public function createNote(array $data): void
    {
        dd($data);
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

        $status = $this->settingService->getStoryStatusById($data['status']);

        $ended_at = (parseBool($status['is_complete_state'])) ? date('Y-m-d H:i:s') : null;

        $statement = $this->db->prepare('
            INSERT INTO story (
                show_id,
                due_at,
                title,
                collection,
                rate_type,
                type,
                status,
                ended_at,
                hours
            )
            VALUES (
                :show_id,
                :due_at,
                :title,
                :collection,
                :rate_type,
                :type,
                :status,
                :ended_at,
                1
            )
        ');

        $statement->bindParam(':show_id', $id);
        $statement->bindParam(':ended_at', $ended_at);
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
    public function deleteNote(array $data): void
    {
        dd($data);
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
        $collectionService = new CollectionService(); // TODO: need dependency injection

        $defaultCollection = $collectionService
            ->getDefaultCollectionForProject($data['project_id']);

        $latestCollection = $collectionService
            ->getLatestCollectionForProject($data['project_id']);

        $status = null;
        $thisStory = null;
        foreach ($data['story'] as $storyId => $aStory) {
            // We only need one since we can safely assume that
            // all stories are in the same collection.
            if (!$thisStory) {
                $thisStory = $this->getStory($storyId);
                $status = $this->settingService->getStoryStatusById($thisStory['status']);
            }

            if (empty($aStory['title'])) {
                continue;
            }

            if (isset($data['move']) && in_array($storyId, $data['move'])) {
                $newCollection = ((int) $thisStory['collection'] === (int) $defaultCollection['id'])
                    ? $latestCollection['id']
                    : $defaultCollection['id'];

                $return = ($data['collection_id'] === $defaultCollection['id'])
                    ? 'unorganized'
                    : 'top';
            } else {
                $newCollection = $thisStory['collection'];

                if (
                    parseBool($status['is_complete_state'])
                    || parseBool($status['is_billable_state'])
                ) {
                    $return = 'completed';
                }
                else if ($thisStory['collection'] === $defaultCollection['id']) {
                    $return = 'unorganized';
                } else {
                    $return = 'open';
                }
            }

            // Default to existing, overwrite anything incoming.
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
                    collection = :collection,
                    ended_at = :ended_at,
                    epic = :epic
                WHERE
                    id = :id
            ');

            $hours = (float) $aStory['hours'];
            $type = (int) $aStory['type'];
            $rateType = (int) $aStory['rate_type'];

            $statement->bindParam(':ended_at', $aStory['ended_at']);
            $statement->bindParam(':hours', $hours);
            $statement->bindParam(':collection', $newCollection);
            $statement->bindParam(':type', $type);
            $statement->bindParam(':rate_type', $rateType);
            $statement->bindParam(':title', $aStory['title']);
            $statement->bindParam(':id', $storyId);
            $statement->bindParam(':epic', $aStory['epic']);
            $statement->execute();
        }


        redirect(
            '/project',
            $data['project_id'],
            'Your tasks have been updated.',
            null,
            false,
            [],
            $return
        );
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
        } elseif (parseBool($status['is_billable_state'])) {
            $hours = 1;
        }

        $endedAt = $story['ended_at'] ? $story['ended_at'] : date('Y-m-d H:i:s');
    
        $statement = $this->db->prepare('
            UPDATE story
            SET status = :status, ended_at = :ended_at, hours = :hours
            WHERE id = :id
        ');

        $statement->bindParam(':status', $data['status']);
        $statement->bindParam(':hours', $hours);
        $statement->bindParam(':id', $data['id']);
        $statement->bindParam(':ended_at', $endedAt);
        $statement->execute();

        $status = $this->settingService->getStoryStatusById($data['status']);

        redirect(
            '/project',
            $data['project_id'],
            'Your status of your story has been changed to "' . $status['title'] . '".'
        );
    }
}
