<?php
/**
 * @category    pimcore5-notification
 * @date        28/12/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

namespace Divante\NotificationsBundle\Service;

use Divante\NotificationsBundle\Model\Action;

/**
 * Class ActionService
 * @package Divante\NotificationsBundle\Service
 */
class ActionService
{
    /**
     * @param int $id
     * @return Action
     * @throws \UnexpectedValueException
     */
    public function find(int $id) : Action
    {
        $action = Action::getById($id);

        if (!$action instanceof Action) {
            throw new \UnexpectedValueException(sprintf('No action found with ID %d', $id));
        }

        return $action;
    }
    
    /**
     * @return array
     */
    public function findAll() : array
    {
        $listing = new Action\Listing();
        $listing->load();
        return $listing->getActions();
    }

    /**
     * @return int
     */
    public function countAll() : int
    {
        $listing = new Action\Listing();
        $listing->load();
        return $listing->getTotalCount();
    }
}
