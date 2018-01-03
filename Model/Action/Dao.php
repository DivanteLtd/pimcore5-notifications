<?php
/**
 * @category    Wurth
 * @date        03/01/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

namespace Divante\NotificationsBundle\Model\Action;

use Pimcore\Model\Dao\PhpArrayTable;

/**
 * Class Dao
 * @package Divante\NotificationsBundle\Model\Action
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
     * @param null $id
     * @throws \Exception
     */
    public function getById($id = null)
    {
        if ($id != null) {
            $this->model->setId($id);
        }

        $data = $this->db->getById($this->model->getId());

        if (isset($data['id'])) {
            $this->assignVariablesToModel($data);
        } else {
            throw new \Exception('Notification action with id: ' . $this->model->getId() . ' does not exist');
        }
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        try {
            $dataRaw = get_object_vars($this->model);
            $data = [];

            foreach ($dataRaw as $key => $value) {
                $data[$key] = $value;
            }

            $this->db->insertOrUpdate($data, $this->model->getId());
        } catch (\Exception $e) {
            throw $e;
        }

        if (!$this->model->getId()) {
            $this->model->setId($this->db->getLastInsertId());
        }
    }

    /**
     *
     */
    public function delete()
    {
        $this->db->delete($this->model->getId());
    }
}
