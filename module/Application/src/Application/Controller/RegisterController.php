<?php

namespace Application\Controller;

use Zend\Form\Exception\ExceptionInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Http\Response;
use Zend\Json\Json;
use Application\Entity\User;
use Application\Form\RegisterForm;
use Application\InputFilter\FormRegisterFilter;
use Application\Mvc\RegistrationEvent;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class RegisterController extends AbstractActionController
{
    protected $entityManager;

    protected $form;

    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('DoctrineService');
        }

        return $this->entityManager;
    }

    public function registerAction()
    {
        $user = new User();
        $entityManager = $this->getEntityManager();

        $form = new RegisterForm($this->getEntityManager());
        $inputfilter = new FormRegisterFilter($form);
        $form->setInputFilter($inputfilter);
        $form->get('submit')->setValue('Регистрация');
        $form->setHydrator(new DoctrineHydrator($entityManager,'Application\Entity\User'));
        $form->bind($user);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            $url = $this->getRequest()->getServer('HTTP_ORIGIN');

            if($form->isValid()){
                $data = $form->getData();
                $email = $this->getRequest()->getPost('email');

                $password = $data->getPassword();
                $crypt = new Bcrypt([
                    'salt' => Rand::getBytes(16, true),
                    'cost' => 10,
                ]);
                $hash = $crypt->create($password);
                $bcryptPassword = $crypt->verify($password, $hash);
                $passwordVerify = $form->get('passwordVerify')->getValue();

                if ($form->get('passwordVerify')->getValue() == $bcryptPassword) {
                    $user->setPassword($hash);
                    $user->setRole(User::ROLE_MEMBER);
                    $user->setActive(1); //must be 0
                    $user->setToken(Rand::getString(60));
                }

  /**email confirm send*/
/*
                $route = $this->url()->fromRoute('register', array(
                    'controller' => 'registration',
                    'action' => 'confirm-email',
                    'id' => $user->getToken(),
                ));

                $event = new RegistrationEvent(__METHOD__, null, array(
                    'registrationUser' => $data,
                    'email' => $email,
                    'route' => $route,
                    'url' => $url,
                ));
*/
              //  $this->getEventManager()->trigger(RegistrationEvent::EVENT_REGISTRATION_PRE, $this, $event);

                $entityManager->persist($user);
                $entityManager->flush();

                //$this->getEventManager()->trigger(RegistrationEvent::EVENT_REGISTRATION_POST, $this, $event);

                $this->flashMessenger()->addMessage('Вы успешно зарегистрировались!');
                $this->redirect()->refresh();
            }
        }
        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function confirmEmailAction()
    {
        $token = $this->params()->fromRoute('id');
        $user = new User();
        $active = $user->getActive();

        $viewModel = new ViewModel(array('token' => $token));
        if ($token === null) {
            $viewModel->setTemplate('application/register/confirm-email-error.phtml');
        }

       // if ($active == false) {
            try {
                $entityManager = $this->getEntityManager();
                $user = $entityManager->getRepository('Application\Entity\User')->findOneBy(array('token' => $token));
                $user->setActive(1);
                $entityManager->persist($user);
                $entityManager->flush();

            } catch(\Exception $e) {
                $response = $this->getResponse();
                $response->setStatusCode(500);
                $message = "[ Error Token :" . $e->getMessage(). " ]";
                $response->setContent(Json::decode($message));

                return $response;
            }
       // }

        return $viewModel;
    }
}
