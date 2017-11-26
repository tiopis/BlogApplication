<?php

namespace Application\Mvc;

use Zend\EventManager\Event;

class MailEvent extends Event
{
    const EVENT_MAIL_PRE = 'mailSend.pre';
    const EVENT_MAIL_POST = 'mailSend.post';
}
