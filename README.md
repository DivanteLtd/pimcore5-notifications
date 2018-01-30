# Pimcore 5 notifications

It's simple plugin that allows to send notifications to user. 
Plugin adds to status bar new clickable icon, on click it opens
new tab with all notifications, also it contains badge with unread
notifications count.

There're two different ways of communication:
- WebSockets - if it's possible to initialize,
- Ajax - otherwise.

When there's new notification for user, it shows as window
with possibility to close it, mark as read or open details.

## Compatibility
This module is compatible with Pimcore v5.1.1.

## Requirements
This plugin requires php >= 7.0.

## Installing/Getting started
### First step
```
composer require divante-ltd/pimcore5-notifications
```
### Second step
Open Extension tab in admin panel and install plugin. After this, installation is finished.

## Usage
If you want to send some notifications to user:
```php
<?php

use Divante\NotificationsBundle\Model\Notification;

$notification = new Notification();
$notification->setTitle('your title');
$notification->setMessage('your message');
$notification->setUser($user);
$notification->setType(Notification::TYPE_INFO); // optional
$notification->setFromUser($fromUser); // optional
$notification->setLinkedElement($linkedElement); // optional
$notification->save();
```

## How to enable WebSockets?
Just run this command (it'll start WebSocket server):
```
bin/console divante:notifications:run
```
Supervisord is highly recommended to use (read more [here](http://socketo.me/docs/deploy#supervisor)).

## Contributing
If you'd like to contribute, please fork the repository and use a feature branch. Pull requests are warmly welcome.

## Standards & Code Quality
This module respects our own PHPCS and PHPMD rulesets.

## About Authors
![Divante-logo](http://divante.co///logo_1.png "Divante")

We are a Software House from Europe, headquartered in Poland and employing about 150 people. Our core competencies are built around Magento, Pimcore and bespoke software projects (we love Symfony3, Node.js, Angular, React, Vue.js). We specialize in sophisticated integration projects trying to connect hardcore IT with good product design and UX.

Visit our website [Divante.co](https://divante.co/ "Divante.co") for more information.
