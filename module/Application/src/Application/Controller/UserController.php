<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\EventManager\EventManagerInterface;
use Application\Form\UserProfileEditForm;
use	Application\InputFilter\FormUserProfileEditFilter;
use	Application\Form\ChangePasswordForm;
use	Application\InputFilter\FormChangePasswordFilter;
use	Application\Entity\User;

class UserController extends AbstractActionController
{
    protected $em;

    protected $authservice;

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('DoctrineService');
        }

        return $this->em;
    }

    public function getAuthService()
    {
        if (null === $this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        }

        return $this->authservice;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/admin');
        }, 100);
    }

    public function indexAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $pagedUsers = $em->getRepository('Application\Entity\User')->getPagedUsers($page);
        $viewModel = new ViewModel();
        $viewModel->setVariable('pagedUsers', $pagedUsers);
        $viewModel->setVariable('page', $page);

        return $viewModel;
    }

    public function profileUserAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute('post', array('action' => 'index'));
        }

        $form = new UserProfileEditForm($this->getEntityManager());
        $inputfilter = new FormUserProfileEditFilter($form);
        $form->setInputFilter($inputfilter);
        $form->get('submit')->setValue('Update Profile');

        $id = $this->identity()->getId();
        $user = new User();
        $user = $this->getEntityManager()->getRepository('Application\Entity\User')->find($id);

        if (!$id) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'index'
            ));
        }

        $form->bind($user);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $password = $form->get('password')->getValue();
                $user->setPassword($password);
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('user', array('action' => 'index'));
            }
        }

        return new ViewModel(array(
            'id' => $id,
            'form' => $form,
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRout('user', array('action' => 'index'));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $user = $this->getEntityManager()->find('Application\Entity\User', $id);

                if ($user) {
                    $this->getEntityManager()->remove($user);
                    $this->getEntityManager()->flush();
                }
            }

            return $this->redirect()->toRoute('user', array('action', 'index'));
            $response->setStatusCode(Response::STATUS_CODE_200);
        }

        return array(
            'id' => $id,
            'user' => $this->getEntityManager()->find('Application\Entity\User', $id),
        );
    }
}
