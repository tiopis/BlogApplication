<?php

namespace Application\Service\Listener;

use Zend\Mvc\MvcEvent;
use Zend\Http\Response;
use Zend\Debug\Debug as ZDebug;
use Zend\Json\Json;
use Application\Acl\Acl;

/**
 * This class will listen to the predispatch event
 * and check authorization level of the user
 * in order to eventually limit the response or throw
 * an unauthorized error.
 */
class AclListener
{
    protected $acl;

    public function __construct(Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * Event invokation
     *
     * @param MvcEvent $event
     */
    public function __invoke(MvcEvent $event)
    {
        $this->acl->setEvent($event);
        $response = $event->getResponse();
        $message = new \StdClass();

        // check if user can see the current resource
        try {
            $isCurrenUserAllowedHere = $this->acl->isCurrentUserAllowedHere();
        } catch (\Exception $e) {
            $response->setStatusCode(Response::STATUS_CODE_500);
            $message->messages = ['Unauthorized: General Authorization error (More info: ' . $e->getMessage() . ')'];
            $response->setContent(Json::encode($message));

            return $response;
        }

        if(!$isCurrenUserAllowedHere) {
            $response->setStatusCode(Response::STATUS_CODE_403);
            $message->messages = 'You have no rights to access this resource';
            $response->setContent(Json::encode($message));

            return $response;
        }

        // All is OK
        //$event->setParam('user', $result->getIdentity());
    }
}
