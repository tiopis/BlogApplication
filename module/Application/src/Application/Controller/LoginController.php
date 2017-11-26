<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Authentication;
use Zend\Authentication\Storage;
use Zend\Session\SessionManager;
use Zend\Session\Config\StandardConfig;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Math\Rand;
use Zend\Crypt\Password\Bcrypt;
use Zend\Validator\EmailAddress;
use Application\Entity\User;
use Application\Form\LoginForm;
use Application\Form\ForgotPasswordForm;
use Application\InputFilter\FormLoginFilter;
use Application\InputFilter\FormForgotPasswordFilter;
use Application\Mvc\MailForgotPasswordEvent;

class LoginController extends AbstractActionController
{
    protected $form;

    protected $authservice;

    protected $entityManager;

    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('DoctrineService');
        }

        return $this->entityManager;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/login');
        }, 100);
    }

    public function getAuthService()
    {
        if (null === $this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        }

        return $this->authservice;
    }

    public function loginAction()
    {
        $response = new Response();
        $form = new LoginForm();
        $inputfilter = new FormLoginFilter($form);
        $form->setInputFilter($inputfilter);
        $form->get('submit')->setValue('Login');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $authService = $this->getAuthService();
                $adapter = $authService->getAdapter();
                $identity = isset($data['username']) ? $data['username'] : null;
                $credential = isset($data['password']) ? $data['password'] : null;
                $adapter->setIdentityValue($identity);
                $adapter->setCredentialValue($credential);
                $authentication = $authService->authenticate();

                if ($authentication->isValid()) {
                    $identity = $authentication->getIdentity();
                    $authService->getStorage()->write($identity);
                    $time = 1209600;

                    if ($this->params()->fromPost('rememberme')) {
                        $sessionManager = new SessionManager();
                        $sessionManager->rememberMe($time);
                    }

                    $userActive = $identity->getActive();

                    if ($userActive == false) {
                        $this->flashMessenger()->addMessage('Account not Valid!');

                    }

                    $this->redirect()->toRoute('admin', array('action' => 'index'));
                    $response->setStatusCode(200);
                    $this->getServiceLocator()->get('Zend\Log')->info('Login Success');
                }
            }

            $this->flashMessenger()->addMessage('Credential not valid!');
            $this->getServiceLocator()->get('Zend\Log')->err('Authentication Failed!');
        }

        $viewModel = new ViewModel(array(
            'form' => $form,
        ));

        return $viewModel;
    }

    public function logoutAction()
    {
        $authService = $this->getAuthService();

        if ($authService->hasIdentity()) {
            $authService->clearIdentity();
            $sessionManager = new SessionManager();
            $sessionManager->forgetMe();
        }

        return $this->redirect()->toRoute('login', array('action' =>  'login'));
    }

    public function forgotPasswordAction()
    {
        $form = new ForgotPasswordForm();
        $inputfilter = new FormForgotPasswordFilter($form);
        $form->setInputFilter($inputfilter);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if(!$form->isValid()) {
                $form->getMessages();
            }

            $validator = new EmailAddress();
            $email = $this->getRequest()->getPost('email');

            if ($validator->isValid($email)) {

                $entityManager = $this->getEntityManager();
                $user = $entityManager->getRepository('Application\Entity\User')->findOneBy(array('email' => $email));
                $password = Rand::getString(60);
                $crypt = new Bcrypt([
                    'salt' => Rand::getBytes(16, true),
                    'cost' => 10,
                ]);
                $hash = $crypt->create($password);
                $bcryptPassword = $crypt->verify($password, $hash);

                $event = new MailForgotPasswordEvent(__METHOD__, null, array(
                    'email' => $email,
                    'password' => $password,
                ));

                $user->setPassword($hash);
                $entityManager->persist($user);

                $this->getEventManager()->trigger(MailForgotPasswordEvent::EVENT_MAIL_FORGOT_PASSWORD_PRE, $this, $event);
                $entityManager->flush();
                $this->getEventManager()->trigger(MailForgotPasswordEvent::EVENT_MAIL_FORGOT_PASSWORD_POST, $this, $event);

                $this->redirect()->toRoute('login', array('action' => 'login'));
                $this->flashMessenger()->addMessage('Change Password Success!');
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }
}
