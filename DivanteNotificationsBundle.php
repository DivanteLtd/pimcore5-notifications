<?php
/**
 * @category    pimcore5-notification
 * @date        15/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle;

use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

/**
 * Class DivanteNotificationsBundle
 * @package Divante\NotificationsBundle
 */
class DivanteNotificationsBundle extends AbstractPimcoreBundle
{
    /**
     * @return InstallerInterface
     */
    public function getInstaller()
    {
        return new Installer();
    }

    /**
     * @return string[]
     */
    public function getJsPaths()
    {
        return [
            '/bundles/divantenotifications/js/button.js',
            '/bundles/divantenotifications/js/helper.js',
            '/bundles/divantenotifications/js/panel.js',
            '/bundles/divantenotifications/js/plugin.js',
            '/bundles/divantenotifications/js/window.js',
        ];
    }

    /**
     * @return string[]
     */
    public function getCssPaths()
    {
        return [
            '/bundles/divantenotifications/css/notifications.css',
        ];
    }
}
