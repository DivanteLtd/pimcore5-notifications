<?php
/**
 * @category    Wurth
 * @date        03/01/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

namespace Divante\NotificationsBundle\Model\Action\Listing;

use Divante\NotificationsBundle\Model;
use Pimcore\Model\Dao\PhpArrayTable;

/**
 * Class Dao
 * @package Divante\NotificationsBundle\Model\Action\Listing
 */
class Dao extends PhpArrayTable
{
    /**
     *
     */
    public function configure()
    {
        parent::configure();
        $this->setFile('notification-actions');
    }

    /**
     * @return array
     */
    public function load()
    {
        $actionsData = $this->db->fetchAll($this->model->getFilter(), $this->model->getOrder());

        $actions = [];
        foreach ($actionsData as $actionData) {
            $actions[] = Model\Action::getById($actionData['id']);
        }

        $this->model->setActions($actions);

        return $actions;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        $data = $this->db->fetchAll($this->model->getFilter(), $this->model->getOrder());
        $amount = count($data);

        return $amount;
    }
}
