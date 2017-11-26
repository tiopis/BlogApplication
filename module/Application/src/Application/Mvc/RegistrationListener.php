<?php
namespace Application\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Debug\Debug as ZDebug;

class RegistrationListener extends AbstractListenerAggregate implements
        ListenerAggregateInterface,
        ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('Application\Controller\RegisterController',
            RegistrationEvent::EVENT_REGISTRATION_POST, array($this, 'onUserSignup'), 20000000);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onUserSignup(RegistrationEvent $e)
    {
        $service = $this->getServiceLocator()->get('MailService');
        $data = $e->getParam('registrationUser');
        $email = $e->getParam('email');
        $url = $e->getParam('url');
        $route = $e->getParam('route');

        $service->sendEmail("subject of email service registration", "Registration success!" . $url . $route ,$email, $email);
    }
}
