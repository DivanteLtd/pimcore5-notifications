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
     * @var array
     */
    protected $items = [
        1 => ['id' => 1, 'text' => 'Add photo'],
        2 => ['id' => 2, 'text' => 'Add description'],        
    ];
    
    /**
     * @param int $id
     * @return Action
     * @throws \UnexpectedValueException
     */
    public function find(int $id) : Action
    {
        if (!isset($this->items[$id])) {
            throw new \UnexpectedValueException(sprintf('No action found with ID %d', $id));
        }
        
        $action = new Action();
        $action->setValues($this->items[$id]);
        
        return $action;
    }
    
    /**
     * @return array
     */
    public function findAll() : array
    {
        $data = [];
        foreach ($this->items as $item) {
            $action = new Action();
            $action->setValues($item);
            $data[] = $action;
        }
        return $data;
    }
}
