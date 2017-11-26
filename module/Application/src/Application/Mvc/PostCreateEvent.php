<?php

namespace Application\Mvc;

use Zend\EventManager\Event;

class PostCreateEvent extends Event
{
    const EVENT_POST_CREATE_PRE = 'postCreate.pre';
    const EVENT_POST_CREATE_POST = 'postCreate.post';
}
