<?php

namespace Application\Controller;

use Application\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Application\Form\PostForm;
use Application\InputFilter\FormPostFilter;
use Application\Entity\Post;
use Application\Mvc\PostCreateEvent;
use Application\Mvc\PostUpdateEvent;
use Application\Mvc\PostDeleteEvent;

class PostController extends AbstractActionController
{
    protected $em;

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('DoctrineService');
        }

        return $this->em;
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
        $user = $this->identity()->getRole() === User::ROLE_ADMIN ? null : $this->identity();
        $pagedPosts = $em->getRepository('Application\Entity\Post')->getPagedPosts($page, $user);
        $viewModel = new ViewModel();
        $viewModel->setVariable('pagedPosts', $pagedPosts);
        $viewModel->setVariable('page', $page);

        return $viewModel;
    }

    public function addAction()
    {
        $post = new Post();
        $post->setUser($this->identity());
        $response = new Response();
        $form = new PostForm();
        $entityManager = $this->getEntityManager();
        $inputfilter = new FormPostFilter($form);
        $form->setInputFilter($inputfilter);
        $form->get('submit')->setValue('Add');
        $form->setHydrator(new DoctrineHydrator($entityManager,'Application\Entity\Post'));
        $form->bind($post);

        $request = $this->getRequest();
        if ($request->isPost())  {

            $data = $request->getPost()->toArray();

            $file = $this->params()->fromFiles('image');

            $form->setData($data);
            if ($form->isValid()) {
                $post->setImage($this->imgUpload($file));

                $this->getEntityManager()->persist($post);

                $event = new PostCreateEvent(__METHOD__, null, array(
                    'post' => $data,
                    //'image' => $file
                ));
                $this->getEventManager()->trigger(PostCreateEvent::EVENT_POST_CREATE_PRE, $this, $event);
                $this->getEntityManager()->flush();
                $this->getEventManager()->trigger(PostCreateEvent::EVENT_POST_CREATE_POST, $this, $event);

                $response->setStatusCode(Response::STATUS_CODE_200);
                $this->flashMessenger()->addMessage('New post successfully added.');

                return $this->redirect()->toRoute('post', array('action' => 'add'));
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $post = new Post();
        $form = new PostForm();
        $inputfilter = new FormPostFilter($form);
        $form->setInputFilter($inputfilter);
        $form->get('submit')->setValue('Edit');
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('post', array(
                'action' => 'add'
            ));
        }

        $post = $this->getEntityManager()->find('Application\Entity\Post', $id);

        if (!$post || !$post->canBeChanged($this->identity())) {
            return $this->redirect()->toRoute('post', array('action' => 'index'));
        }

        $form->bind($post);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $file = $this->params()->fromFiles('image');
            $form->setData($data);
            if ($form->isValid()) {
                $post->setImage($this->imgUpload($file));

                $this->getEntityManager()->persist($post);
                $event = new PostUpdateEvent(__METHOD__, null, array(
                    'post' => $post
                ));

                $this->getEventManager()->trigger(PostUpdateEvent::EVENT_POST_UPDATE_PRE, $this, $event);
                $this->getEntityManager()->flush();
                $this->getEventManager()->trigger(PostUpdateEvent::EVENT_POST_UPDATE_POST, $this, $event);

                return $this->redirect()->toRoute('post', array('action' => 'index'));

                $response->setStatusCode(Response::STATUS_CODE_200);
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
            return $this->redirect()->toRout('post', array('action' => 'index'));
        }

        $request = $this->getRequest();
        $post = $this->getEntityManager()->find('Application\Entity\Post', $id);
        if ($request->isPost() && $post->canBeChanged($this->identity())) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');


                if ($post && $post->canBeChanged($this->identity())) {
                    $this->getEntityManager()->remove($post);
                    $event = new PostDeleteEvent(__METHOD__, null, array(
                        'post' => $post
                    ));

                    $this->getEventManager()->trigger(PostDeleteEvent::EVENT_POST_DELETE_PRE, $this, $event);
                    $this->getEntityManager()->flush();
                    $this->getEventManager()->trigger(PostDeleteEvent::EVENT_POST_DELETE_POST, $this, $event);
                }
            }

            $this->flashMessenger()->addMessage('Delete post successfully.');

            return $this->redirect()->toRoute('post', array('action', 'index'));

            $response->setStatusCode(Response::STATUS_CODE_200);
        }

        return array(
            'id' => $id,
            'post' => $this->getEntityManager()->find('Application\Entity\Post', $id),
        );
    }
    /**
     * @return string $imgPath
     * todo: must be helper
    */
    protected function imgUpload($file)
    {
        $adapterFile = basename($file['tmp_name']) . '_' . $file['name'];
        $hostFilePath = "/img/Upload/" . time() .  strrchr($file['name'], '.');
        $size = new \Zend\Validator\File\Size(array('max'=>2000000));
        //$extension = new \Zend\Validator\File\Extension(array('extension' => array('jpeg','jpg','png')));
        $adapter = new \Zend\File\Transfer\Adapter\Http();
        $adapter->setValidators(array($size), $adapterFile);
        $adapter->addFilter('File\Rename', array(
            'target' => ROOTPATH . $hostFilePath ,
        ));
        $result = null;

        if($adapter->receive($adapterFile)) {
            $result =  $hostFilePath;
        }

        return $result;

    }
}
