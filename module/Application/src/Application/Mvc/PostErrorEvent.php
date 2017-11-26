<?php

namespace Application\Mvc;

use Zend\EventManager\Event;

class PostErrorEvent extends Event
{
    const EVENT_POST_ERROR_PRE = 'postError.pre';
    const EVENT_POST_ERROR_POST = 'postError.post';
}
