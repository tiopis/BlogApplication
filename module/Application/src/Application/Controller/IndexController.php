<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\Validator\EmailAddress;
use Application\Form\ContactForm;
use Application\InputFilter\FormContactFilter;
use Application\Mvc\MailEvent;

class IndexController extends AbstractActionController
{
    protected $form;

    protected $em;

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('DoctrineService');
        }

        return $this->em;
    }

    public function indexAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        $pagedPosts = $em->getRepository('Application\Entity\Post')->getPagedPosts($page);
        $viewModel = new ViewModel();
        $viewModel->setVariable('pagedPosts', $pagedPosts);
        $viewModel->setVariable('page', $page);

        return $viewModel;
    }

    public function aboutAction()
    {
        return new ViewModel();
    }

    public function contactAction()
    {
        $form = new ContactForm();
        $inputfilter = new FormContactFilter($form);
        $form->setInputFilter($inputfilter);
        $form->get('submit')->setValue('Send');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if(!$form->isValid()) {
                $form->getMessages();
            }

            $validator = new EmailAddress();
            $subject = $this->getRequest()->getPost('name');
            $email = $this->getRequest()->getPost('email');
            $message = $this->getRequest()->getPost('comment');

            if ($validator->isValid($email)) {

                $event = new MailEvent(__METHOD__, null, array(
                    'subject' => $subject,
                    'email' => $email,
                    'message' => $message,
                ));

                $this->getEventManager()->trigger(MailEvent::EVENT_MAIL_POST, $this, $event);

                $this->redirect()->toRoute('contact', array('action' => 'contact'));
                $this->flashMessenger()->addMessage('Email send successfully.');
            }
        }
        return new ViewModel(array(
            'form' => $form,
        ));
    }
}
