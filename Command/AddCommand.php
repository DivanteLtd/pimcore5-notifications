<?php

declare(strict_types=1);

namespace Divante\NotificationsBundle\Command;

use Divante\NotificationsBundle\Model\Notification;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\User;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class AddCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('divante:notifications:add');
        $this->setDescription('Add a notification');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        
        $question = new Question('Title:', '');
        $title    = $helper->ask($input, $output, $question);
        
        $question = new Question('Message:', '');
        $message  = $helper->ask($input, $output, $question);
        
        $users    = $this->getUsers();
        $question = new ChoiceQuestion('User:', $users);
        $name     = $helper->ask($input, $output, $question);        
        $user     = (int) User::getByName($name)->getId();
        
        $notification = new Notification($title, $message, $user);
        $notification->save();
        
        $output->writeln('A notification was created');
    }

    protected function getUsers() : array
    {
        $list = new User\Listing();        
        $list->setOrder('ASC');
        $list->setOrderKey('name');
        $list->load();
        
        $users = [];
        if (is_array($list->getUsers())) {
            foreach ($list->getUsers() as $user) {
                if ($user->getId() && $user->getName() != 'system') {
                    $users[] = $user->getName();
                }
            }
        }
        
        return $users;
    }
}
