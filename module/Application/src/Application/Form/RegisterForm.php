<?php

namespace Application\Form;

use Zend\Form\Form;

class RegisterForm extends Form
{
    public $_entityManager;

    public function __construct($entityManager)
    {
        parent::__construct('register');
        $this->_entityManager = $entityManager;
        $this->setAttribute('method', 'post');
        $this->remove('active');


        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'Name',
            ),
            'options' => array(
                'label' => 'Имя',
            ),
        ));

        $this->add(array(
            'name' => 'surname',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'surname',
            ),
            'options' => array(
                'label' => 'Фамилия',
            ),
        ));

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
            'name' => 'username',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'username',
            ),
            'options' => array(
                'label' => 'Логин',
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'attributes' => array(
                'class' => 'password',
            ),
            'options' => array(
                'label' => 'Пароль',
            ),
        ));

        $this->add(array(
            'name' => 'passwordVerify',
            'type' => 'Password',
            'attributes' => array(
                'class' => 'passwordVerify',
            ),
            'options' => array(
                'label' => 'Проверка пароля',
            ),
        ));

    /*
        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'ActivationUser',
            'options' => array(
                'label' => 'Activation User',
                'value_options' => array(
                    '0' => 'noActive',
                    '1' => 'Active',
                ),
            ),
        ));
    */

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'registerCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
                ),
            ),
        ));
        $capcha = new \Zend\Captcha\Dumb();
        $capcha->setLabel('Введите символы в обратном порядке');
        $this->add(array(
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'options' => array(
                'label' => ' ',
                'captcha' => $capcha,
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
            'type'  => 'submit',
            'value' => 'Go',
            'id' => 'submitbutton',
            ),
        ));
    }
}
