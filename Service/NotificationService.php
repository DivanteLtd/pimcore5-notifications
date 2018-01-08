<?php
/**
 * @category    pimcore5-notification
 * @date        20/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

namespace Divante\NotificationsBundle\Service;

use Divante\NotificationsBundle\Model\Notification\Listing;
use Divante\NotificationsBundle\Model\Notification;
use Divante\NotificationsBundle\Service\ActionService;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\User;

/**
 * Class NotificationService
 * @package Divante\NotificationsBundle\Service
 */
class NotificationService
{
    /**     
     * @param int $userId
     * @param int $fromUser
     * @param int $actionId
     * @param string $note
     * @param int $objectId
     * @throws \UnexpectedValueException
     */
    public function send(int $userId, int $fromUser, int $actionId, string $note, int $objectId)
    {
        $this->beginTransaction();
                
        $user = User::getById($userId);
        if (!$user instanceof User) {
            throw new \UnexpectedValueException(sprintf('No user found with the ID %d', $userId));
        }
        
        $action = (new ActionService())->find($actionId);
        
        $object = AbstractObject::getById($objectId);
        if (!$object instanceof AbstractObject) {
            throw new \UnexpectedValueException(sprint('No object found with the ID %d', $objectId));
        }
        
        $notification = new Notification();        
        $notification->setUser($user->getId());
        $notification->setFromUser($fromUser);
        $notification->setTitle($action->getText());
        $notification->setMessage($note);     
        $notification->setLinkedElement($object);
        $notification->save();
        
        $this->commit();
    }
    
    /**
     * @param int $id
     * @return Notification
     * @throws \UnexpectedValueException
     */
    public function find(int $id) : Notification
    {
        $notification = Notification::getById($id);

        if (!$notification instanceof Notification) {
            throw new \UnexpectedValueException("Notification with the ID {$id} doesn't exists");
        }

        return $notification;
    }

    /**
     * @param int $id
     * @return Notification
     */
    public function findAndMarkAsRead(int $id) : Notification
    {
        $this->beginTransaction();
        $notification = $this->find($id);
        $notification->setUnread(false);
        $notification->save();
        $this->commit();
        return $notification;
    }

    /**
     * @param array $filter
     * @param array $options
     * @return array
     */
    public function findAll(array $filter = [], array $options = []) : array
    {
        $listing = new Listing();

        if (!empty($filter)) {
            $condition          = implode(' AND ', array_keys($filter));
            $conditionVariables = array_values($filter);
            $listing->setCondition($condition, $conditionVariables);
        }

        $listing->setOrderKey('creationDate');
        $listing->setOrder('DESC');

        $offset = $options['offset'] ?? 0;
        $limit  = $options['limit'] ?? 0;

        $this->beginTransaction();
        $result = ['total' => $listing->count(), 'data' => $listing->getItems($offset, $limit)];
        $this->commit();

        return $result;
    }

    /**
     * @param int $user
     * @param int $interval
     * @return array
     */
    public function findLastUnread(int $user, int $interval) : array
    {
        $listing = new Listing();
        $listing->setCondition('user = ? AND unread = 1 AND creationDate >= ?', [$user, time() - $interval]);

        $this->beginTransaction();
        $result = ['total' => $listing->count(), 'data' => $listing->getData()];
        $this->commit();

        return $result;
    }

    /**
     * @param int $user
     * @return int
     */
    public function countAllUnread(int $user) : int
    {
        $listing = new Listing();
        $listing->setCondition('user = ? AND unread = 1', [$user]);
        return $listing->count();
    }

    /**
     * @param int $id
     */
    public function delete(int $id)
    {
        $this->beginTransaction();
        $notification = $this->find($id);
        $notification->delete();
        $this->commit();
    }

    /**
     * @param int $user
     */
    public function deleteAll(int $user)
    {
        $listing = new Listing();
        $listing->setCondition('user = ?', [$user]);

        $this->beginTransaction();
        foreach ($listing->getData() as $notification) {
            $notification->delete();
        }
        $this->commit();
    }

    /**
     *
     */
    private function beginTransaction()
    {
        \Pimcore\Db::getConnection()->beginTransaction();
    }

    /**
     *
     */
    private function commit()
    {
        \Pimcore\Db::getConnection()->commit();
    }
}
