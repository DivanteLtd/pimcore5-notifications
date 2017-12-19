<?php
/**
 * @category    Wurth
 * @date        19/12/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Service;

use Pimcore\Model\User\Listing;
use Pimcore\Model\User;

/**
 * Class UserService
 * @package Divante\NotificationsBundle\Service
 */
class UserService
{
    /**
     * @param User $user
     * @return array
     */
    public function findAll(User $loggedIn) : array
    {
        $filter = [
            'id > ?'     => 0,
            'id != ?'    => $loggedIn->getId(),
            'type = ?'   => 'user',
            'name != ?'  => 'system',
            'active = ?' => 1,
        ];

        $condition          = implode(' AND ', array_keys($filter));
        $conditionVariables = array_values($filter);

        $listing = new Listing();
        $listing->setCondition($condition, $conditionVariables);
        $listing->setOrderKey('name');
        $listing->setOrder('ASC');
        $listing->load();

        return $listing->getUsers();
    }
}
