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
class SettingService extends BaseService
{
    /**
     * @param array $data
     * @return void
     */
    public function createRateType(array $data): void
    {
        $statement = $this->db->prepare('
            INSERT INTO story_hour_type (
                title,
                rate
            )
            VALUES (
                :title,
                :rate
            )
        ');

        $rate = (int) $data['rate'] * 100;

        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':rate', $rate);

        $statement->execute();

        redirect('/settings', null, 'Wow, making the big bucks now are we?');
    }

    /**
     * @param array $data
     * @return void
     */
    public function createStatus(array $data): void
    {
        $statement = $this->db->prepare('
            INSERT INTO story_status (
                title,
                is_complete_state,
                is_billable_state,
                emoji,
                color
            )
            VALUES (
                :title,
                :is_complete_state,
                :is_billable_state,
                :emoji,
                :color
            )
        ');

        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':is_complete_state', $data['is_complete_state']);
        $statement->bindParam(':is_billable_state', $data['is_billable_state']);
        $statement->bindParam(':emoji', $data['emoji']);
        $statement->bindParam(':color', $data['color']);
        $statement->execute();

        redirect('/settings', null, 'Your new status has been created.');
    }

    /**
     * @param array $data
     * @return void
     */
    public function createStoryType(array $data): void
    {
        $statement = $this->db->prepare('
            INSERT INTO story_type (
                title
            )
            VALUES (
                :title
            )
        ');

        $statement->bindParam(':title', $data['title']);
        $statement->execute();

        redirect('/settings', null, 'Your new story type has been created.');
    }


    /**
     * @param array $data
     * @return void
     */
    public function deleteRate(array $data): void
    {
        if ($data['id'] === 1) {
            redirect('/settings', null, null, 'You cannot delete your base rate.');
        }

        $statement = $this->db->prepare('
            DELETE FROM story_hour_type
            WHERE id = :id
        ');
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        $statement = $this->db->prepare('
            UPDATE story
            SET rate_type = 1
            WHERE rate_type IS NULL OR rate_type = :old_type
        ');
        $statement->bindParam(':old_type', $data['id']);

        $statement->execute();

        redirect('/settings', null, 'We deleted that rate type. All stories that were covered by that have been set to your base rate.');
    }

    /**
     * @param array $data
     * @return void
     */
    public function deleteStatus(array $data): void
    {
        if ($data['id'] <= 4) {
            redirect('/settings', null, null, 'You cannot delete the default statuses.');
        }

        $statusType = getStoryStatusById($data['id']);
        if (!$statusType) {
            redirect('/settings', null, null, 'Status does not exist.');
        }

        $revertedTo = $statusType['is_complete_state'] ? 2 : 1;
        $revertedToName = $statusType['is_complete_state'] ? 'Complete' : 'Open';

        $statement = $this->db->prepare('
            DELETE FROM story_status
            WHERE id = :id
        ');
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        $statement = $this->db->prepare('
            UPDATE story
            SET status = :revert_to
            WHERE status IS NULL OR status = :old_status_id
        ');
        $statement->bindParam(':old_status_id', $data['id']);
        $statement->bindParam(':revert_to', $revertedTo);
        $statement->execute();

        redirect('/settings', null, 'We deleted that story status. All stories that were set to that status have been reverted to the "' . $revertedToName . '" status.');
    }

    /**
     * @param array $data
     * @return void
     */
    public function deleteStoryType(array $data): void
    {
        if ($data['id'] === 1) {
            redirect('/settings', null, null, 'You cannot delete the "Story" type.');
        }

        $statement = $this->db->prepare('
            DELETE FROM story_type
            WHERE id = :id
        ');
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        $statement = $this->db->prepare('
            UPDATE story
            SET type = 1
            WHERE type IS NULL OR type = :old_type
        ');
        $statement->bindParam(':old_type', $data['id']);
        $statement->execute();

        redirect('/settings', null, 'We deleted that story type. All stories that were of that type have been reverted to the standard "Story" type.');
    }
}