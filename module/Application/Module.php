<?php
namespace Application;

use Zend\EventManager\EventInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\Acl\Acl;

class Module
{
    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getApplication();
        //$application = $e->getTarget();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $aclListener = $serviceManager->get('AclServiceListener');
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', MvcEvent::EVENT_DISPATCH, $aclListener, 20000);
        $eventManager->attachAggregate($serviceManager->get('ErrorServiceListener'));
        $eventManager->attachAggregate($serviceManager->get('RegistrationListener'));
        $eventManager->attachAggregate($serviceManager->get('MailForgotPasswordListener'));
        $eventManager->attachAggregate($serviceManager->get('MailListener'));
        $eventManager->attachAggregate($serviceManager->get('PostCreateEventListener'));
        $eventManager->attachAggregate($serviceManager->get('PostUpdateEventListener'));
        $eventManager->attachAggregate($serviceManager->get('PostDeleteEventListener'));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {

    }
}
