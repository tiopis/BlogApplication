<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use	Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class LoggerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $logger = new Logger();
        $writer = new Stream('./data/logs/logfile');
        $logger->addWriter($writer);

        return $logger;
    }
}
