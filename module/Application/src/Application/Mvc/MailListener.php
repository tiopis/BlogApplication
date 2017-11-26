<?php

namespace Application\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Debug\Debug as ZDebug;
use Application\Entity\User;

class MailListener extends AbstractListenerAggregate implements
        ListenerAggregateInterface,
        ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('Application\Controller\IndexController',
            MailEvent::EVENT_MAIL_POST, array($this, 'onMail'), 200);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onMail(MailEvent $e)
    {
        $service = $this->getServiceLocator()->get('MailService');
        $subject = $e->getParam('subject');
        $email = $e->getParam('email');
        $message = $e->getParam('message');

        $service->sendEmail("subject of email service mail contact", "email success!", $email, $email);
    }
}
