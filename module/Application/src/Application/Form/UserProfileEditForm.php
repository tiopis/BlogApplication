<?php

namespace Application\Form;

use Zend\Form\Form;

class UserProfileEditForm extends Form
{
    public $_entityManager;

    public function __construct($entityManager)
    {
        parent::__construct('profile-user');
        $this->_entityManager = $entityManager;
        $this->setAttribute('method', 'post');

        $this->remove('role');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'name',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'surname',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'surname',
            ),
            'options' => array(
                'label' => 'Surname',
            ),
        ));


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
            'type'  => 'submit',
            'value' => 'UpdateProfile',
            'id' => 'submitbutton',
            ),
        ));
    }
}
