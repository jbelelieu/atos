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
class CollectionService extends BaseService
{
    /**
     * @param array $data
     * @return void
     */
    public function createCollection(array $data): void
    {
        $collections = getCollectionByProject($data['project_id'], 1);

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
        $collection = getCollectionById($data['id']);

        $isDefault = (bool) $collection['is_project_default'];
        if ($isDefault) {
            redirect(
                '/project',
                $data['project_id'],
                '',
                'You cannot delete the "Unorganized" collection from a project.'
            );
        }

        $statement = $this->db->prepare('
            DELETE FROM story_collection WHERE id = :id
        ');
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        redirect(
            '/project',
            $data['project_id'],
            'Your collection has been deleted.'
        );
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

        $statement->bindParam(':id', $data['id']);
        $statement->bindParam(':date', date('Y-m-d H:i:s'));

        $statement->execute();

        if ($redirect) {
            redirect('/project', $data['project_id'], 'Now working with a new collection.');
        }
    }


    /**
     * @param array $data
     * @return void
     */
    public function shiftCollection(array $data): void
    {
        $story = getStory($data['id']);

        // Default to Open
        if ($story['collection'] === 1) {
            $useCollection = getLatestCollectionForProject($data['project_id']);
            $moveTo = $useCollection['id'];
        }
        // Move to default collection
        else {
            $useCollection = getDefaultCollectionForProject($data['project_id']);
            $moveTo = $useCollection['id'];
        }

        $msg = 'Your story is now part of the "' . $useCollection['title'] . '" collection.';

        $statement = $this->db->prepare('
            UPDATE story
            SET collection = :collection
            WHERE id = :id
        ');
        $statement->bindParam(':collection', $moveTo);
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        redirect('/project', $data['project_id'], $msg);
    }
}
