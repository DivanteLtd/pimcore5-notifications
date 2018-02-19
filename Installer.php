<?php
/**
 * @category    pimcore5-notification
 * @date        15/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle;

use Divante\NotificationsBundle\DivanteNotificationsBundle;
use Pimcore\Db;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\User\Permission;
use Pimcore\Tool\Admin;

/**
 * Class Installer
 * @package Divante\NotificationsBundle
 */
class Installer extends AbstractInstaller
{
    /**
     *
     * @throws InstallationException
     */
    public function install()
    {
        Permission\Definition::create(DivanteNotificationsBundle::PERMISSION);

        $sql = file_get_contents(__DIR__ . '/Resources/sql/install.sql');
        try {
            Db::getConnection()->query($sql);
        } catch (\Exception $ex) {
            new InstallationException('An error occurred while installing the bundle', 0, $ex);
        }
    }

    /**
     *
     * @throws InstallationException
     */
    public function uninstall()
    {
        $sql = file_get_contents(__DIR__ . '/Resources/sql/uninstall.sql');
        try {
            Db::getConnection()->query($sql);
        } catch (\Exception $ex) {
            new InstallationException('An error occurred while uninstalling the bundle', 0, $ex);
        }
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        $permission = Permission\Definition::getByKey(DivanteNotificationsBundle::PERMISSION);
        if (!$permission instanceof Permission\Definition) {
            return false;
        }

        try {
            $stmt = Db::getConnection()->query("SHOW TABLES LIKE 'bundle_notifications'");
            $ret = strcmp((string) $stmt->fetchColumn(), 'bundle_notifications') === 0;
        } catch (\Exception $ex) {
            $ret = false;
        }

        return $ret;
    }

    /**
     * @return bool
     */
    public function canBeInstalled()
    {
        return Admin::isExtJS6() && !$this->isInstalled();
    }

    /**
     * @return bool
     */
    public function canBeUninstalled()
    {
        return $this->isInstalled();
    }

    /**
     * @return bool
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }
}
