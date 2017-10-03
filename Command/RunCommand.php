<?php
/**
 * @category    pimcore5-notification
 * @date        29/09/2017
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\NotificationsBundle\Command;

use Divante\NotificationsBundle\Server\NotificationServer;
use Pimcore\Console\AbstractCommand;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RunCommand
 * @package Divante\NotificationsBundle\Command
 */
class RunCommand extends AbstractCommand implements ContainerAwareInterface
{
    /**
     * @var ConnectionInterface|null
     */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this->setName('divante:notifications:run');
        $this->setDescription('Starts WebSocket server for notifications.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $notificationServer = $this->container->get(NotificationServer::class);
        
        $server = IoServer::factory(
            new HttpServer(
                new WsServer($notificationServer)
            ),
            8080
        );
        
        $callback = [$notificationServer, 'onPeriodicTimer'];
        $server->loop->addPeriodicTimer(30, $callback);
        $server->run();
    }
}
