<?php
/**
 * @category    pimcore5-notification
 * @date        28/12/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

namespace Divante\NotificationsBundle\Model;

use Pimcore\Model\AbstractModel;

/**
 * Class Action
 * @package Divante\NotificationsBundle\Model
 */
class Action extends AbstractModel
{
    /**
     * @var int
     */
    public $id;
    
    /**
     * @var string
     */
    public $text;    

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
    
    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }    
}
