<?php

namespace ApplicationTest\Controller;

use Zend\Http\Request;
use Zend\Http\Response;
use Application\Controller\IndexController;
use PHPUnit_Framework_TestCase;
use ApplicationTest\Bootstrap;

class IndexControllerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $serviceManager->setAllowOverride(true);
        $this->controller = new IndexController();
        $this->request   = new Request();
        $this->response  = new Response();

    }

    public function testIndexActionCanBeAccessed()
    {
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
}

