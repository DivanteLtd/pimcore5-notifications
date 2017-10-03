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
This module is compatible with Pimcore 5.0.*@alpha.

## Requirements
This plugin requires php >= 7.1.

## Installing/Getting started
### First step
```
composer require divante-ltd/pimcore5-notifications @dev
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
![Divante-logo](http://divante.co/wp-content/uploads/2017/07/divante-logo.png "Divante")

Founded in 2008 in Poland, Divante delivers high-quality e-business solutions. They support their clients in creating customized Omnichannel and eCommerce platforms, with expertise in CRM, ERP, PIM, custom web applications, and Big Data solutions. With 180 employees on board, Divante provides software expertise and user-experience design. Their team assists companies in their development and optimization of new sales channels by implementing eCommerce solutions, integrating systems, and designing and launching marketing campaigns.

Visit our website [Divante.co](https://divante.co/ "Divante.co") for more information.
