<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use	Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return $serviceManager->get('doctrine.entitymanager.orm_default');
    }
}
