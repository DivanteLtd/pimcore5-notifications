<?php
/**
 * @category    pimcore5-notification
 * @date        18/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Model;

use Pimcore\Model\AbstractModel;

/**
 * Class Notification
 * @package Divante\NotificationsBundle\Model
 * @method Notification\Dao getDao()
 */
class Notification extends AbstractModel
{
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';

    const LINKED_ELEMENT_TYPE_DOCUMENT = 'document';
    const LINKED_ELEMENT_TYPE_ASSET = 'asset';
    const LINKED_ELEMENT_TYPE_OBJECT = 'object';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $fromUser;

    /**
     * @var int
     */
    private $user;

    /**
     * @var bool
     */
    private $unread;

    /**
     * @var int
     */
    private $creationDate;

    /**
     * @var int
     */
    private $modificationDate;

    /**
     * @var string
     */
    private $linkedElementType;

    /**
     * @var \Pimcore\Model\Document|\Pimcore\Model\Asset|\Pimcore\Model\Object
     */
    private $linkedElement;

    /**
     * Notification constructor.
     * @param string $title
     * @param string $message
     * @param int $user
     * @param string $type
     * @param bool $unread
     */
    public function __construct(
        string $title = '',
        string $message = '',
        int $user = 0,
        string $type = Notification::TYPE_INFO,
        bool $unread = true
    ) {
        $this->setType($type);
        $this->setTitle($title);
        $this->setMessage($message);
        $this->setUser($user);
        $this->setUnread($unread);
        $this->setCreationDate(time());
    }

    /**
     * @param int $id
     * @return Notification|null
     */
    public static function getById(int $id)
    {
        try {
            $notification = new self();
            $notification->getDao()->getById($id);
            return $notification;
        } catch (\Exception $ex) {
            //TODO: logger
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Notification
     * @throws \DomainException
     */
    public function setId(int $id) : Notification
    {
        if ($this->id !== null && $this->id !== $id) {
            throw new \DomainException();
        }

        if ($id <= 0) {
            throw new \DomainException();
        }

        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Notification
     * @throws \DomainException
     */
    public function setType(string $type) : Notification
    {
        $haystack = [self::TYPE_INFO, self::TYPE_SUCCESS, self::TYPE_ERROR];
        if (!in_array($type, $haystack, true)) {
            throw new \DomainException();
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Notification
     */
    public function setTitle(string $title) : Notification
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Notification
     */
    public function setMessage(string $message) : Notification
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * @param int|null $fromUser
     * @return Notification
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    public function setFromUser($fromUser) : Notification
    {
        if (!is_null($fromUser) && !is_int($fromUser)) {
            throw new \InvalidArgumentException();
        }

        if (is_int($fromUser) && $fromUser < 0) {
            throw new \DomainException();
        }

        $this->fromUser = $fromUser;
        return $this;
    }

    /**
     * @return int
     */
    public function getUser() : int
    {
        return $this->user;
    }

    /**
     * @param int $user
     * @return Notification
     * @throws \DomainException
     */
    public function setUser(int $user) : Notification
    {
        if ($user < 0) {
            throw new \DomainException();
        }

        $this->user = $user;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnread() : bool
    {
        return $this->unread;
    }

    /**
     * @return bool
     */
    public function isRead() : bool
    {
        return !$this->unread;
    }

    /**
     * @param bool $unread
     * @return Notification
     */
    public function setUnread(bool $unread) : Notification
    {
        $this->unread = $unread;
        return $this;
    }

    /**
     * @return int
     */
    public function getCreationDate() : int
    {
        return $this->creationDate;
    }

    /**
     * @param int $creationDate
     * @return Notification
     * @throws \DomainException
     */
    public function setCreationDate(int $creationDate) : Notification
    {
        if ($creationDate <= 0) {
            throw new \DomainException();
        }

        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @param int|null $modificationDate
     * @return Notification
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    public function setModificationDate($modificationDate) : Notification
    {
        if (!is_null($modificationDate) && !is_int($modificationDate)) {
            throw new \InvalidArgumentException();
        }

        if (is_int($modificationDate) && $modificationDate <= 0) {
            throw new \DomainException();
        }

        $this->modificationDate = $modificationDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLinkedElementType()
    {
        return $this->linkedElementType;
    }

    /**
     * @return \Pimcore\Model\Asset|\Pimcore\Model\Document|\Pimcore\Model\Object|null
     */
    public function getLinkedElement()
    {
        return $this->linkedElement;
    }

    /**
     * @param \Pimcore\Model\Asset|\Pimcore\Model\Document|\Pimcore\Model\Object|null $linkedElement
     * @return Notification
     * @throws \InvalidArgumentException
     */
    public function setLinkedElement($linkedElement) : Notification
    {
        if ($linkedElement instanceof \Pimcore\Model\Document) {
            $this->linkedElementType = self::LINKED_ELEMENT_TYPE_DOCUMENT;
        } else if ($linkedElement instanceof \Pimcore\Model\Asset) {
            $this->linkedElementType = self::LINKED_ELEMENT_TYPE_ASSET;
        } else if ($linkedElement instanceof \Pimcore\Model\Object) {
            $this->linkedElementType = self::LINKED_ELEMENT_TYPE_OBJECT;
        } else if (is_null($linkedElement)) {
            $this->linkedElementType = null;
        } else {
            throw new \InvalidArgumentException();
        }

        $this->linkedElement = $linkedElement;
        return $this;
    }

    /**
     *
     */
    public function save()
    {
        $this->getDao()->save();
    }

    /**
     *
     */
    public function delete()
    {
        $this->getDao()->delete();
    }
}
