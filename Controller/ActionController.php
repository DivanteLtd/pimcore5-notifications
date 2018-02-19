<?php
/**
 * @category    pimcore5-notification
 * @date        03/01/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Controller;

use Divante\NotificationsBundle\Model\Action;
use Divante\NotificationsBundle\Service\ActionService;
use Divante\NotificationsBundle\DivanteNotificationsBundle;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ActionController
 * @package Divante\NotificationsBundle\Controller
 * @Route("/notification-action")
 */
class ActionController extends AdminController
{
    /**
     * @param Request $request
     * @param ActionService $service
     * @return JsonResponse
     * @Route("/index")
     * @Method({"POST"})
     */
    public function indexAction(Request $request, ActionService $service) : JsonResponse
    {
        $this->checkPermission(DivanteNotificationsBundle::PERMISSION);

        $data = $request->get('data');
        if ($data) {
            $xaction = $request->get('xaction');
            $data    = $this->decodeJson($data);

            if ('create' === $xaction) {
                unset($data['id']);
                $action = new Action();
                $action->setValues($data);
                $action->save();

                return $this->adminJson([
                    'success' => true,
                    'data'    => $action->getObjectVars(),
                ]);
            } elseif ('update' === $xaction) {
                $action = $service->find((int) $data['id']);
                $action->setValues($data);
                $action->save();

                return $this->adminJson([
                    'success' => true,
                    'data'    => $action->getObjectVars(),
                ]);
            } elseif ('destroy' === $xaction) {
                $action = $service->find((int) $data['id']);
                $action->delete();

                return $this->adminJson([
                    'success' => true,
                    'data'    => [],
                ]);
            }
        } else {
            $data  = [];
            $items = $service->findAll();
            foreach ($items as $item) {
                $data[] = $item->getObjectVars();
            }

            return $this->adminJson([
                'success' => true,
                'data'    => $data,
                'total'   => $service->countAll(),
            ]);
        }
    }
}
