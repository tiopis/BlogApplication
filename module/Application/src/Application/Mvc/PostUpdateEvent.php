<?php

namespace Application\Mvc;

use Zend\EventManager\Event;

class PostUpdateEvent extends Event
{
    const EVENT_POST_UPDATE_PRE = 'postUpdate.pre';
    const EVENT_POST_UPDATE_POST = 'postUpdate.post';
}
