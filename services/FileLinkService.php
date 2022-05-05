<?php

namespace services;

use services\BaseService;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Service for all things project files and links.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */
class FileLinkService extends BaseService
{
    /**
     * @param array $data
     * @return void
     */
    public function createLink(array $data): void
    {
        $statement = $this->db->prepare('
            INSERT INTO project_file (
                project_id,
                is_link,
                title,
                data
            )
            VALUES (
                :project_id,
                1,
                :title,
                :data
            )
        ');

        $statement->bindParam(':project_id', $data['project_id']);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':data', $data['data']);
        $statement->execute();

        redirect(
            '/project',
            $data['project_id'],
            'Your link has been added.',
            null,
            false,
            [
                '_showLink' => '1',
            ]
        );
    }

    /**
     * @param array $data
     * @return void
     */
    public function deleteFileLink(array $data): void
    {
        $item = $this->get($data['file_id']);

        $isLink = parseBool($item['is_link']);
        if (!$isLink) {
            @unlink(ATOS_HOME_DIR . '/' . ltrim($item['data'], '/'));
        }

        $statement = $this->db->prepare('
            DELETE FROM project_file
            WHERE id = :id
        ');

        $statement->bindParam(':id', $data['file_id']);

        $statement->execute();

        redirect(
            '/project',
            $data['id'],
            'Your item has been deleted.',
            null,
            false,
            [
                '_showLink' => $isLink ? '1' : '',
                '_showFile' => !$isLink ? '1' : '',
            ]
        );
    }

    /**
     * @param integer $id
     * @return array
     */
    public function get(int $id): array
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM project_file
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
    public function getFilesForProject(int $projectId): array
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM project_file
            WHERE
                is_link = 0
                AND project_id = :project_id
            ORDER BY story_id ASC, created_at DESC
        ");

        $statement->bindParam(':project_id', $projectId);

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param integer $storyId
     * @return array
     */
    public function getFilesForStory(int $storyId): array
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM project_file
            WHERE
                is_link = 0
                AND story_id = :story_id
            ORDER BY story_id ASC, created_at DESC
        ");

        $statement->bindParam(':story_id', $storyId);

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param integer $projectId
     * @return array
     */
    public function getLinksForProject(int $projectId): array
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM project_file
            WHERE
                is_link = 1
                AND project_id = :project_id
            ORDER BY created_at DESC
        ");

        $statement->bindParam(':project_id', $projectId);

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param integer $storyId
     * @return array
     */
    public function getLinksForStory(int $storyId): array
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM project_file
            WHERE
                is_link = 1
                AND story_id = :story_id
            ORDER BY created_at DESC
        ");

        $statement->bindParam(':story_id', $storyId);

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param array $data
     * @return void
     */
    public function uploadFile(array $data): void
    {
        $uploadFilePath = '_vault/' . $_FILES['data']['name'];
        $uploadPath = ATOS_HOME_DIR . '/' . $uploadFilePath;

        $uploaded = move_uploaded_file(
            $_FILES['data']['tmp_name'],
            $uploadPath
        );
        if (!$uploaded) {
            redirect(
                '/project',
                $data['project_id'],
                null,
                'Your file could not be uploaded: is the directory writable? (' . $uploadPath . ')'
            );
        }

        $statement = $this->db->prepare('
            INSERT INTO project_file (
                project_id,
                is_link,
                title,
                data
            )
            VALUES (
                :project_id,
                0,
                :title,
                :data
            )
        ');

        $statement->bindParam(':project_id', $data['project_id']);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':data', $uploadFilePath);
        $statement->execute();

        redirect(
            '/project',
            $data['project_id'],
            'Your file has been uploaded to the _vault directory.',
            null,
            false,
            [
                '_showFile' => '1',
            ]
        );
    }
}
