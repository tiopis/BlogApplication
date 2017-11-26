<?php
namespace Application\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\AbstractListenerAggregate;
//use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Debug\Debug as ZDebug;

use Application\Entity\User;

class MailForgotPasswordListener extends AbstractListenerAggregate implements
        ListenerAggregateInterface
        //ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('Application\Controller\LoginController',
            MailForgotPasswordEvent::EVENT_MAIL_FORGOT_PASSWORD_PRE, array($this, 'onMailForgotPassword'), 200000);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onMailForgotPassword(MailForgotPasswordEvent $e)
    {
        $service = $this->getServiceLocator()->get('MailService');
        $email = $e->getParam('email');
        $password = $e->getParam('password');
        var_dump($password);
        var_dump('EVENTO');
        $service->sendEmail("Mail forgot password", "This is your new password: " . $password , $email, $email);
    }
}
