<?php
namespace Application\Acl\Assertion;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl as BaseAcl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

class CleanIPAssertion
    implements AssertionInterface
{

    public function __construct() 
    {
        
    }

    public function assert(
        BaseAcl $acl, 
        RoleInterface $role = null,
        ResourceInterface $resource = null, $privilege = null
    ) {
        return $this->isCleanIP($_SERVER['REMOTE_ADDR']);
    }

    protected function isCleanIP($ip)
    {
        if ('127.0.0.1' !== $ip) {
            return false;
        }

        return true;
    }
}