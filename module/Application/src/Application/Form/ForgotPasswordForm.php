<?php

namespace Application\Form;

use Zend\Form\Form;

class ForgotPasswordForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('forgot-password');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'email',
            'type' => 'Email',
            'attributes' => array(
                'class' => 'email',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'contactCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
                ),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
            'type'  => 'submit',
            'value' => 'Submit',
            'id' => 'submitbutton',
            ),
        ));
    }
}
