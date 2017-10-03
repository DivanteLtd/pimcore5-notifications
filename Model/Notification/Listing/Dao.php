<?php
/**
 * @category    pimcore5-notification
 * @date        18/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Model\Notification\Listing;

use Divante\NotificationsBundle\Model\Notification;
use Pimcore\Db\ZendCompatibility\QueryBuilder;
use Pimcore\Model\Listing\Dao\AbstractDao;

/**
 * Class Dao
 * @package Divante\NotificationsBundle\Model\Notification\Listing
 * @property \Divante\NotificationsBundle\Model\Notification\Listing $model
 */
class Dao extends AbstractDao
{
    /**
     * @var string
     */
    protected $tableName = 'bundle_notifications';

    /**
     * @return QueryBuilder
     */
    public function getQuery() : QueryBuilder
    {
        $select = $this->db->select();
        $select->from(['t1' => $this->tableName], ['id']);
        $select->joinLeft(['t2' => 'users'], 't1.fromUser = t2.id', []);
        $this->addConditions($select);
        $this->addOrder($select);
        $this->addLimit($select);
        return $select;
    }

    /**
     * @return Notification[]
     */
    public function load() : array
    {
        $data = [];

        foreach ($this->loadIdList() as $id) {
            $notification = Notification::getById($id);
            if ($notification instanceof Notification) {
                $data[] = $notification;
            }
        }

        $this->model->setData($data);
        return $data;
    }

    /**
     * @return int[]
     * @throws \Exception
     */
    public function loadIdList() : array
    {
        $sql = $this->getQuery();

        try {
            $ids = $this->db->fetchCol($sql, $this->model->getConditionVariables());
        } catch (\Exception $ex) {
            throw new \Exception('An error occurred while reading from the database', 0, $ex);
        }

        return array_map(function (string $id) {
            return (int) $id;
        }, $ids);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCount() : int
    {
        $sql = "SELECT COUNT(*) AS amount "
             . "FROM %s AS t1 "
             . "LEFT JOIN users AS t2 ON t1.fromUser = t2.id "
             . "%s%s";

        $sql = sprintf(
            $sql,
            $this->tableName,
            $this->getCondition(),
            $this->getOffsetLimit()
        );

        try {
            $amount = $this->db->fetchOne($sql, $this->model->getConditionVariables());
        } catch (\Exception $ex) {
            throw new \Exception('An error occurred while reading from the database', 0, $ex);
        }

        return (int) $amount;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getTotalCount() : int
    {
        $sql = "SELECT COUNT(*) AS amount "
             . "FROM %s AS t1 "
             . "LEFT JOIN users AS t2 ON t1.fromUser = t2.id "
             . "%s";

        $sql = sprintf(
            $sql,
            $this->tableName,
            $this->getCondition()
        );

        try {
            $amount = $this->db->fetchOne($sql, $this->model->getConditionVariables());
        } catch (\Exception $ex) {
            throw new \Exception('An error occurred while reading from the database', 0, $ex);
        }

        return (int) $amount;
    }
}
