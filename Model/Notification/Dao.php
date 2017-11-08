<?php
/**
 * @category    pimcore5-notification
 * @date        18/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Model\Notification;

use Divante\NotificationsBundle\Model\Notification;
use Pimcore\Model\Dao\AbstractDao;
use Pimcore\Tool\RestClient\Exception;

/**
 * Class Dao
 * @package Divante\NotificationsBundle\Model\Notification
 * @property \Divante\NotificationsBundle\Model\Notification $model
 */
class Dao extends AbstractDao
{
    /**
     * @var string
     */
    protected $tableName = 'bundle_notifications';

    /**
     * @param int $id
     * @throws \Exception
     */
    public function getById(int $id)
    {
        $data = $this->db->fetchRow("SELECT * FROM {$this->tableName} WHERE id = ?", $id);
        if (!is_array($data)) {
            throw new \Exception("Object with the ID {$id} doesn't exists");
        }
        $this->assignVariablesToModel($data);
    }

    /**
     *
     */
    public function save()
    {
        if ($this->model->getId()) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    /**
     * @throws \Exception
     */
    public function insert()
    {
        if ($this->model->getId()) {
            return;
        }

        $data = [
            'type' => $this->model->getType(),
            'title' => $this->model->getTitle(),
            'message' => $this->model->getMessage(),
            'fromUser' => $this->model->getFromUser(),
            'user' => $this->model->getUser(),
            'unread' => $this->model->isUnread() ? 1 : 0,
            'creationDate' => $this->model->getCreationDate(),
            'linkedElementType' => $this->model->getLinkedElementType(),
            'linkedElement' => $this->model->getLinkedElement() ? $this->model->getLinkedElement()->getId() : null,
        ];

        try {
            $this->db->insert($this->tableName, $data);
        } catch (\Exception $ex) {
            throw new \Exception('An error occurred while writing to the database', 0, $ex);
        }

        $this->model->setId((int) $this->db->lastInsertId());
    }

    /**
     * @throws \Exception
     */
    public function update()
    {
        if (!$this->model->getId()) {
            return;
        }

        if (!$this->model->getModificationDate()) {
            $this->model->setModificationDate(time());
        }

        $data = [
            'type' => $this->model->getType(),
            'title' => $this->model->getTitle(),
            'message' => $this->model->getMessage(),
            'fromUser' => $this->model->getFromUser(),
            'user' => $this->model->getUser(),
            'unread' => $this->model->isUnread() ? 1 : 0,
            'creationDate' => $this->model->getCreationDate(),
            'modificationDate' => $this->model->getModificationDate(),
            'linkedElementType' => $this->model->getLinkedElementType(),
            'linkedElement' => $this->model->getLinkedElement() ? $this->model->getLinkedElement()->getId() : null,
        ];

        try {
            $this->db->update($this->tableName, $data, ['id' => $this->model->getId()]);
        } catch (\Exception $ex) {
            throw new \Exception('An error occurred while writing to the database', 0, $ex);
        }
    }

    /**
     * @throws \Exception
     */
    public function delete()
    {
        if (!$this->model->getId()) {
            return;
        }

        try {
            $this->db->delete($this->tableName, ['id' => $this->model->getId()]);
        } catch (\Exception $ex) {
            throw new \Exception('An error occurred while writing to the database', 0, $ex);
        }
    }

    /**
     * @param array $data
     */
    protected function assignVariablesToModel($data)
    {
        $this->model->setId((int) $data['id']);
        $this->model->setType($data['type']);
        $this->model->setTitle($data['title']);
        $this->model->setMessage($data['message']);
        $this->model->setUser((int) $data['user']);
        $this->model->setUnread((bool) $data['unread']);
        $this->model->setCreationDate((int) $data['creationDate']);

        if (is_string($data['fromUser'])) {
            $this->model->setFromUser((int) $data['fromUser']);
        }

        if (is_string($data['modificationDate'])) {
            $this->model->setModificationDate((int) $data['modificationDate']);
        }

        if ($data['linkedElementType']) {
            $id = $data['linkedElement'];
            switch ($data['linkedElementType']) {
                case Notification::LINKED_ELEMENT_TYPE_DOCUMENT:
                    $this->model->setLinkedElement(\Pimcore\Model\Document::getById($id));
                    break;
                case Notification::LINKED_ELEMENT_TYPE_ASSET:
                    $this->model->setLinkedElement(\Pimcore\Model\Asset::getById($id));
                    break;
                case Notification::LINKED_ELEMENT_TYPE_OBJECT:
                    $this->model->setLinkedElement(\Pimcore\Model\DataObject\AbstractObject::getById($id));
                    break;
            }
        }
    }
}
