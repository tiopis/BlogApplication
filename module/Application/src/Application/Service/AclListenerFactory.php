<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Service\Listener\AclListener;
use Application\Acl\Acl;

class AclListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $authService = $serviceManager->get('Zend\Authentication\AuthenticationService');
        $entityManager = $serviceManager->get('DoctrineService');
        $acl = new Acl($authService, $entityManager);
        $listener = new AclListener($acl);

        return $listener;
    }
}
