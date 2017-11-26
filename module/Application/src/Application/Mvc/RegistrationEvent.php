<?php

namespace Application\Mvc;

use Zend\EventManager\Event;

class RegistrationEvent extends Event
{
    const EVENT_REGISTRATION_PRE = 'registration.pre';
    const EVENT_REGISTRATION_POST = 'registration.post';
}
