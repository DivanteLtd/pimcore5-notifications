<?php
/**
 * @category    Wurth
 * @date        03/01/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

namespace Divante\NotificationsBundle\Model\Action;

use Pimcore\Model\Listing\JsonListing;

/**
 * Class Listing
 * @package Divante\NotificationsBundle\Model\Action
 */
class Listing extends JsonListing
{
    /**
     * @var array
     */
    public $actions = [];

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }
}
