<?php
namespace Application\Acl;

use Zend\Permissions\Acl\Acl as BaseAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage;
use Zend\Mvc\MvcEvent;
use Zend\Http\Request;
use Doctrine\ORM\EntityManager;
use Application\Acl\Assertion\CleanIPAssertion;

class Acl extends BaseAcl
{
	protected $authservice;

	protected $event;

    protected $entityManager;

    const ROLE_GUEST = 'GUEST';
	const ROLE_MEMBER = 'MEMBER';
	const ROLE_ADMIN = 'ADMIN';

    const RESOURCE_APPLICATION_INDEXCONTROLLER = 'Application\Controller\Index';
    const RESOURCE_APPLICATION_USERCONTROLLER = 'Application\Controller\User';
    const RESOURCE_APPLICATION_POSTCONTROLLER = 'Application\Controller\Post';
    const RESOURCE_APPLICATION_LOGINCONTROLLER = 'Application\Controller\Login';
    const RESOURCE_APPLICATION_LOGOUTCONTROLLER = 'Application\Controller\Logout';
    const RESOURCE_APPLICATION_REGISTERCONTROLLER = 'Application\Controller\Register';
    const RESOURCE_APPLICATION_ADMINCONTROLLER = 'Application\Controller\Admin';
    const RESOURCE_APPLICATION_FIXTURECONTROLLER = 'Application\Controller\Fixture';

	protected $roles = array(
		self::ROLE_GUEST,
		self::ROLE_MEMBER,
		self::ROLE_ADMIN
    );

    protected $resources = array(
        self::RESOURCE_APPLICATION_INDEXCONTROLLER,
        self::RESOURCE_APPLICATION_USERCONTROLLER,
        self::RESOURCE_APPLICATION_POSTCONTROLLER,
        self::RESOURCE_APPLICATION_LOGINCONTROLLER,
        self::RESOURCE_APPLICATION_LOGOUTCONTROLLER,
        self::RESOURCE_APPLICATION_REGISTERCONTROLLER,
        self::RESOURCE_APPLICATION_ADMINCONTROLLER,
        self::RESOURCE_APPLICATION_FIXTURECONTROLLER,
    );

    protected function definePermissions()
    {
        $this->allow(self::ROLE_GUEST, self::RESOURCE_APPLICATION_INDEXCONTROLLER);
        $this->deny (self::ROLE_GUEST, self::RESOURCE_APPLICATION_POSTCONTROLLER);
        $this->deny (self::ROLE_GUEST, self::RESOURCE_APPLICATION_USERCONTROLLER);
        $this->deny (self::ROLE_GUEST, self::RESOURCE_APPLICATION_ADMINCONTROLLER);
        $this->allow(self::ROLE_GUEST, self::RESOURCE_APPLICATION_LOGINCONTROLLER);
        $this->allow(self::ROLE_GUEST, self::RESOURCE_APPLICATION_REGISTERCONTROLLER);

        $this->allow(self::ROLE_MEMBER, self::RESOURCE_APPLICATION_INDEXCONTROLLER);
        $this->deny(self::ROLE_MEMBER, self::RESOURCE_APPLICATION_USERCONTROLLER);
        $this->allow(self::ROLE_MEMBER, self::RESOURCE_APPLICATION_POSTCONTROLLER);
        $this->allow(self::ROLE_MEMBER, self::RESOURCE_APPLICATION_USERCONTROLLER, 'profileUser');
        $this->allow(self::ROLE_MEMBER, self::RESOURCE_APPLICATION_ADMINCONTROLLER);

        $this->allow(self::ROLE_MEMBER, self::RESOURCE_APPLICATION_LOGINCONTROLLER);
        $this->allow(self::ROLE_MEMBER, self::RESOURCE_APPLICATION_LOGOUTCONTROLLER);
        $this->allow(self::ROLE_MEMBER, self::RESOURCE_APPLICATION_REGISTERCONTROLLER);

        $this->allow(self::ROLE_ADMIN, self::RESOURCE_APPLICATION_ADMINCONTROLLER);
        $this->allow(self::ROLE_ADMIN, self::RESOURCE_APPLICATION_INDEXCONTROLLER);
        $this->allow(self::ROLE_ADMIN, self::RESOURCE_APPLICATION_POSTCONTROLLER);
        $this->allow(self::ROLE_ADMIN, self::RESOURCE_APPLICATION_USERCONTROLLER);
        $this->allow(self::ROLE_ADMIN, self::RESOURCE_APPLICATION_LOGINCONTROLLER);
        $this->allow(self::ROLE_ADMIN, self::RESOURCE_APPLICATION_LOGOUTCONTROLLER);
        $this->allow(self::ROLE_ADMIN, self::RESOURCE_APPLICATION_REGISTERCONTROLLER);
        $this->allow(self::ROLE_ADMIN, self::RESOURCE_APPLICATION_FIXTURECONTROLLER);

    }

    protected function addRoles()
    {
        foreach($this->roles as $role) {
            $this->addRole(new Role($role));
        }
    }

    protected function addResources()
    {
        foreach($this->resources as $resource) {
            $this->addResource(new Resource($resource));
        }
    }

    public function __construct(
        AuthenticationService $authservice,
        EntityManager $entityManager
    ) {
	    $this->authservice = $authservice;
        $this->entityManager = $entityManager;

        $this->addRoles();
        $this->addResources();
        $this->definePermissions();
    }

	public function setEvent(MvcEvent $event)
	{
		$this->event = $event;

		return $this;
	}

	public function getEvent()
	{
		return $this->event;
	}

	public function getCurrentRole()
    {
		$identity = $this->authservice->getStorage()->read();

        return (null !== $identity) ? $identity->getRole() : self::ROLE_GUEST;
    }

    public function getCurrentController()
    {
	    $event = $this->getEvent();
        $controller = $event->getTarget();
	    $controllerClass = $event->getRouteMatch();
	    $nameController = $controllerClass->getParam('controller');
		$action = $controllerClass->getParam('action');

	    return $nameController;
    }

    public function getCurrentPrivilege()
    {
        $request = $this->getEvent()->getRequest();
        $router = $this->getEvent()->getRouter();
        $method = strtoupper($request->getMethod());
        $controller = $this->getCurrentController();
        $parent = get_parent_class($this->getCurrentController());
        $routeMatch = $router->match($request);
        $params = $routeMatch->getParams();

        return isset($params['action']) ? $params['action'] : 'notFound';
    }

    public function isCurrentUserAllowedHere()
    {
	    $event = $this->getEvent();
	    $role = $this->getCurrentRole();
	    $resource = $this->getCurrentController();
        $privilege = $this->getCurrentPrivilege();

	    return $this->isAllowed($role, $resource, $privilege);
	}
}
