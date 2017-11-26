<?php

namespace Application\Service\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Mvc\MvcEvent;
use Zend\Debug\Debug as ZDebug;
use Zend\Json\Json;
use Doctrine\Common\Util\Debug as DDebug;

class ErrorListener extends AbstractListenerAggregate implements
        ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onError'], -10000000000);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onError(MvcEvent $e)
    {
        $response = $e->getResponse();
        $message = new \stdClass();
        $message->messages = ['Application Error (More info: ' . $e->getParam('error') . ')'];
        $response->setContent(Json::encode($message));

        return $response;
    }
}
