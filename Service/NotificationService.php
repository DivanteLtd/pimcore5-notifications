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

/**
 * Class NotificationService
 * @package Divante\NotificationsBundle\Service
 */
class NotificationService
{
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

        foreach ($filter as $condition => $vars) {
            $listing->setCondition($condition, $vars);
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
