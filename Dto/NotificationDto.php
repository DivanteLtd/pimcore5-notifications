<?php
/**
 * @category    pimcore5-notification
 * @date        27/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Dto;

use Divante\NotificationsBundle\Model\Notification;

/**
 * Class NotificationDto
 * @package Divante\NotificationsBundle\Dto
 */
class NotificationDto
{
    /**
     * @var Notification
     */
    private $notification;

    /**
     * @var array
     */
    private $data;

    /**
     * NotificationDto constructor.
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        if (is_array($this->data)) {
            return $this->data;
        }

        $this->data = [
            'id'                => $this->notification->getId(),
            'type'              => $this->notification->getType(),
            'title'             => $this->notification->getTitle(),
            'message'           => $this->notification->getMessage(),
            'from'              => '',
            'unread'            => (int) $this->notification->isUnread(),
            'date'              => date('Y-m-d H:i:s', $this->notification->getCreationDate()),
            'linkedElementType' => $this->notification->getLinkedElementType(),
            'linkedElementId'   => null,
        ];

        if ($this->notification->getLinkedElement()) {
            $this->data['linkedElementId'] = $this->notification->getLinkedElement()->getId();
        }

        $fromUserId = $this->notification->getFromUser();
        $fromUser   = false;

        if (is_int($fromUserId)) {
            $fromUser = \Pimcore\Model\User::getById($fromUserId);
        }

        if ($fromUser instanceof \Pimcore\Model\User) {
            $from = trim(sprintf('%s %s', $fromUser->getFirstname(), $fromUser->getLastname()));
            if ('' === $from) {
                $from = $fromUser->getName();
            }
            $this->data['from'] = $from;
        }

        return $this->data;
    }
}
