<?php
/**
 * @category    pimcore5-notification
 * @date        02/10/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Server;

/**
 * Class NotificationServerCache
 * @package Divante\NotificationsBundle\Server
 */
class NotificationServerCache
{
    /**
     * @param string $id
     * @param int $user
     * @param string $token
     */
    public static function save(string $id, int $user, string $token)
    {
        $data = ['user' => $user, 'token' => $token];
        $key  = self::createKey($id);
        \Pimcore\Cache::save($data, $key, ['notifications'], null, 0, true);
    }

    /**
     * @param string $id
     * @return array|bool
     */
    public static function load(string $id)
    {
        $key = self::createKey($id);
        return \Pimcore\Cache::load($key);
    }

    /**
     * @param string $id
     * @return string
     */
    private static function createKey(string $id) : string
    {
        return "notifications_token_user_{$id}";
    }
}
