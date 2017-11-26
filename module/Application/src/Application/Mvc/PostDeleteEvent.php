<?php

namespace Application\Mvc;

use Zend\EventManager\Event;

class PostDeleteEvent extends Event
{
    const EVENT_POST_DELETE_PRE = 'postDelete.pre';
    const EVENT_POST_DELETE_POST = 'postDelete.post';
}
