<?php
/**
 * @category    pimcore5-notification
 * @date        20/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Controller;

use Divante\NotificationsBundle\Dto\NotificationDto;
use Divante\NotificationsBundle\Dto\UserDto;
use Divante\NotificationsBundle\Server\NotificationServerCache;
use Divante\NotificationsBundle\Service\NotificationService;
use Divante\NotificationsBundle\Service\NotificationServiceFilterParser;
use Divante\NotificationsBundle\Service\UserService;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @package Divante\NotificationsBundle\Controller
 * @Route("/notification")
 */
class NotificationController extends AdminController
{
    /**
     * @param UserService $service
     * @return JsonResponse
     * @Route("/users")
     */
    public function usersAction(UserService $service) : JsonResponse
    {
        $data = [];
        foreach ($service->findAll($this->getUser()) as $user) {
            $data[] = (new UserDto($user))->getData();
        }

        return $this->json($data);
    }

    /**
     * @return JsonResponse
     * @Route("/actions")
     */
    public function actionsAction() : JsonResponse
    {
        return $this->json([
            ['id' => 1, 'text' => 'Add photo'],
            ['id' => 2, 'text' => 'Add description'],
        ]);
    }
    
    /**
     * @param Request $request
     * @param NotificationService $service
     * @return JsonResponse
     * @Route("/send")
     */
    public function sendAction(Request $request, NotificationService $service) : JsonResponse
    {
        return $this->json(['success' => true]);
    }
    
    /**
     * @param Request $request
     * @param NotificationService $service
     * @return JsonResponse
     * @Route("/find")
     */
    public function findAction(Request $request, NotificationService $service) : JsonResponse
    {
        $id = (int) $request->get('id', 0);
        $notification = $service->findAndMarkAsRead($id);
        $data = (new NotificationDto($notification))->getData();

        return $this->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * @param Request $request
     * @param NotificationService $service
     * @return JsonResponse
     * @Route("/find-all")
     */
    public function findAllAction(Request $request, NotificationService $service) : JsonResponse
    {
        $filter = ['user = ?' => (int) $this->getUser()->getId()];

        $parser = new NotificationServiceFilterParser($request);
        foreach ($parser->parse() as $key => $val) {
            $filter[$key] = $val;
        }

        $options = [
            'offset' => $request->get('start', 0),
            'limit' => $request->get('limit', 40)
        ];

        $result = $service->findAll($filter, $options);

        $data = [];
        foreach ($result['data'] as $notification) {
            $data[] = (new NotificationDto($notification))->getData();
        }

        return $this->json([
            'success' => true,
            'total'   => $result['total'],
            'data'    => $data,
        ]);
    }

    /**
     * @param Request $request
     * @param NotificationService $service
     * @return JsonResponse
     * @Route("/find-last-unread")
     */
    public function findLastUnreadAction(Request $request, NotificationService $service) : JsonResponse
    {
        $user     = $this->getUser();
        $interval = (int) $request->get('interval', 10);
        $result   = $service->findLastUnread((int) $user->getId(), $interval);
        $unread   = $service->countAllUnread((int) $user->getId());

        $data = [];
        foreach ($result['data'] as $notification) {
            $data[] = (new NotificationDto($notification))->getData();
        }

        return $this->json([
            'success' => true,
            'total'   => $result['total'],
            'data'    => $data,
            'unread'  => $unread,
        ]);
    }

    /**
     * @param Request $request
     * @param NotificationService $service
     * @return JsonResponse
     * @Route("/mark-as-read")
     */
    public function markAsReadAction(Request $request, NotificationService $service) : JsonResponse
    {
        $id = (int) $request->get('id', 0);
        $service->findAndMarkAsRead($id);
        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @param NotificationService $service
     * @return JsonResponse
     * @Route("/delete")
     */
    public function deleteAction(Request $request, NotificationService $service) : JsonResponse
    {
        $id = (int) $request->get('id', 0);
        $service->delete($id);
        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @param NotificationService $service
     * @return JsonResponse
     * @Route("/delete-all")
     */
    public function deleteAllAction(Request $request, NotificationService $service) : JsonResponse
    {
        $user = $this->getUser();
        $service->deleteAll((int) $user->getId());
        return $this->json(['success' => true]);
    }

    /**
     * @return JsonResponse
     * @Route("/token")
     */
    public function tokenAction() : JsonResponse
    {
        $token      = sprintf('%s_%s', md5((string) time()), mt_rand(1000000, 9999999));
        $userId     = $this->getUser()->getId();
        $userIdHash = md5($userId);
        NotificationServerCache::save($userIdHash, (int) $userId, $token);
        return $this->json(['user' => $userIdHash, 'token' => $token]);
    }
}
