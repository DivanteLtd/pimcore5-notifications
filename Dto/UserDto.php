<?php
/**
 * @category    pimcore5-notification
 * @date        19/12/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Dto;

use Pimcore\Model\User;

/**
 * Class UserDto
 * @package Divante\NotificationsBundle\Dto
 */
class UserDto
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $data;

    /**
     * UserDto constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        if (is_array($this->data)) {
            return $this->data;
        }

        $suffix = trim($this->user->getFirstname() . ' ' . $this->user->getLastname());
        if ('' !== $suffix) {
            $suffix = ' (' . $suffix . ')';
        }

        $this->data = [
            'id'   => $this->user->getId(),
            'text' => $this->user->getName() . $suffix,
        ];

        return $this->data;
    }
}
