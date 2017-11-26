<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use	Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceManager)
	{
		return $serviceManager->get('doctrine.authenticationservice.orm_default');
	}
}
