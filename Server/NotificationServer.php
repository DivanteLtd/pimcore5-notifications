<?php
/**
 * @category    pimcore5-notification
 * @date        29/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Server;

use Divante\NotificationsBundle\Dto\NotificationDto;
use Divante\NotificationsBundle\Service\NotificationService;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Class NotificationServer
 * @package Divante\NotificationsBundle\Server
 */
class NotificationServer implements MessageComponentInterface
{
    /**
     * @var \SplObjectStorage
     */
    private $clients;
    
    /**     
     * @var NotificationService
     */
    private $service;
    
    /**     
     * @var Serializer 
     */
    private $serializer;
    
    /**
     * NotificationServer constructor.
     */
    public function __construct(Serializer $serializer)
    {
        $this->clients = new \SplObjectStorage();
        $this->service = new NotificationService();
        $this->serializer = $serializer;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $query = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $query);

        $data = NotificationServerCache::load($query['user']);
        if (!is_array($data)) {
            $conn->close();
        }

        if ($query['token'] !== $data['token']) {
            $conn->close();
        }

        $this->clients->attach($conn, [
            'user'          => $data['user'],
            'unread'        => 0,
            'notifications' => []
        ]);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception          $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string                       $msg  The message received
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {

    }
    
    /**     
     * @return void
     */
    public function onPeriodicTimer()
    {
        if ($this->clients->count() < 1) {
            return;
        }
        
        foreach ($this->clients as $conn) {                        
            $info = $this->clients->getInfo();            
            $unread = $this->getUnread($info['user']);
            $notifications = $this->getNotifications($info['user']);            
            if ($info['unread'] !== $unread || $info['notifications'] !== $notifications) {
                $info['unread'] = $unread;
                $info['notifications'] = $notifications;
                $this->clients->setInfo($info);
                $conn->send($this->encodeJson([
                    'unread' => $unread,
                    'notifications' => $notifications,
                ]));
            }
        }
    }
    
    /**     
     * @param int $user
     * @return int
     */
    private function getUnread(int $user) : int
    {
        return $this->service->countAllUnread($user);
    }
    
    /**     
     * @param int $user
     * @return array
     */
    private function getNotifications(int $user) : array
    {
        $notifications = [];
        $result = $this->service->findLastUnread($user, 30);
        foreach ($result['data'] as $notification) {
            $notifications[] = (new NotificationDto($notification))->getData();
        }      
        return $notifications;
    }
    
    /**     
     * @param array $data
     * @return string
     */
    private function encodeJson(array $data) : string
    {
        return $this->serializer->serialize($data, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ]));        
    }
}
