<?php

namespace Application\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Debug\Debug as ZDebug;
use Application\Entity\Post;

class PostDeleteListener extends AbstractListenerAggregate implements
        ListenerAggregateInterface,
        ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('Application\Controller\PostController',
            PostDeleteEvent::EVENT_POST_DELETE_POST, array($this, 'onPostDelete'), 200);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onPostDelete(PostDeleteEvent $e)
    {
        $data = $e->getParam('post');
        $this->getServiceLocator()->get('Zend\Log')->info('Delete post');
    }
}
