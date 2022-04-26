<?php

require_once ATOS_HOME_DIR . '/services/BaseService.php';

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
        global $storyStatuses;

        $options = (!$skipMoveCollection)
            ? "<a title=\"" . language('move_collections', 'Move Collections') . "\" href=\"/project?action=shiftCollection&project_id=" . $projectId . "&id=" . $itemId . "\">" . putIcon('fi-sr-undo') . "</a>"
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
            VALUES (:show_id, :due_at, :title, :collection, :rate_type, :type, 1)
        ');

        $statement->bindParam(':show_id', $id);
        $statement->bindParam(':due_at', $data['due_at']);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':collection', $data['collection']);
        $statement->bindParam(':rate_type', $data['rate_type']);
        $statement->bindParam(':type', $data['type']);

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
        $project = getProjectById($projectId);

        $totalStoriesInProject = getNextStoryNumberForProject($projectId);

        $id = $project['code'] . '-' . $totalStoriesInProject;

        return $project['code'] . '-' . $totalStoriesInProject;
    }
    /**
     * @param array $data
     * @return void
     */
    public function updateStories(array $data): void
    {
        foreach ($data['story'] as $storyId => $aStory) {
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
            $statement->bindParam(':ended_at', $aStory['ended_at']);
            $statement->bindParam(':hours', $aStory['hours']);
            $statement->bindParam(':type', $aStory['types']);
            $statement->bindParam(':rate_type', $aStory['rates']);
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

        $status = getStoryStatusById($data['status']);
        $story = getStory($data['id']);

        $hours = 0;
        if ((int) $story['hours'] > 0) {
            $hours = $story['hours'];
        } elseif ((bool) $status['is_billable_state']) {
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

        $status = getStoryStatusById($data['status']);

        redirect(
            '/project',
            $data['project_id'],
            'Your status of your story has been changed to "' . $status['title'] . '".'
        );
    }
}
