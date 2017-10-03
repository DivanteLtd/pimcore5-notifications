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
use Pimcore\Model\Listing\AbstractListing;

/**
 * Class Listing
 * @package Divante\NotificationsBundle\Model\Notification
 * @method Listing\Dao getDao()
 */
class Listing extends AbstractListing implements
    \Zend_Paginator_Adapter_Interface,
    \Zend_Paginator_AdapterAggregate,
    \Iterator
{
    /**
     * @var array|null
     */
    private $data = null;

    /**
     * @param string $key
     * @return bool
     */
    public function isValidOrderKey($key)
    {
        return true;
    }

    /**
     * @return Notification[]
     */
    public function getData() : array
    {
        if (null === $this->data) {
            $this->getDao()->load();
        }
        return $this->data;
    }

    /**
     * @param Notification[] $data
     * @return Listing
     */
    public function setData(array $data) : Listing
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->getDao()->getTotalCount();
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return Notification[]
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);
        return $this->getDao()->load();
    }

    /**
     * @return $this
     */
    public function getPaginatorAdapter()
    {
        return $this;
    }

    /**
     * @return Notification|bool
     */
    public function current()
    {
        $this->getData();
        return current($this->data);
    }

    /**
     *
     */
    public function next()
    {
        $this->getData();
        next($this->data);
    }

    /**
     * @return int|null|string
     */
    public function key()
    {
        $this->getData();
        return key($this->data);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $this->getData();
        return $this->current() !== false;
    }

    /**
     *
     */
    public function rewind()
    {
        $this->getData();
        reset($this->data);
    }
}
