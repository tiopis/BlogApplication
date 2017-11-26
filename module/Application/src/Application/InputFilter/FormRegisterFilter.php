<?php

namespace Application\InputFilter;

use Application\Form\RegisterForm;
use Zend\InputFilter\InputFilter;
use	Zend\Validator\EmailAddress;
use	Zend\Validator\NotEmpty;
use Application\Entity\Repository\UserRepository;
use DoctrineModule\Validator\NoObjectExists;

class FormRegisterFilter extends InputFilter
{
    public function __construct(RegisterForm $form)
    {
        $invalidEmail = EmailAddress::INVALID_FORMAT;
        $noEmptyValidator = array(
                'name' => 'NotEmpty',
                'options' => array(
                    'messages' => array(
                        NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
                    )
                ),
                'break_chain_on_failure' => true
        );
        $this->add(array(
            'name'       => 'name',
            'required'   => true,
            'allowEmpty' => true,
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 0,
                        'max' => 50,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'email',
            'required'   => true,
            'allowEmpty' => true,
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                $noEmptyValidator,
                array(
                    'name' => 'DoctrineModule\Validator\NoObjectExists',
                    'options' => array(
                        'object_repository' => $form->_entityManager->getRepository('\Application\Entity\User'),
                        'fields' => 'email',
                        'messages' => array(
                            NoObjectExists::ERROR_OBJECT_FOUND => 'Пользователь с таким Email уже существует'
                        ),
                    ),
                ),
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'messages' => array(
                            $invalidEmail => 'Не корректный Email'
                        )
                    )
                )
            ),
        ));

        $this->add(array(
            'name'       => 'surname',
            'required'   => true,
            'allowEmpty' => true,
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 0,
                        'max' => 50,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'username',
            'required'   => true,
            'allowEmpty' => true,
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 4,
                        'max' => 50,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'password',
            'required'   => true,
            'allowEmpty' => true,
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 4,
                        'max' => 60,
                    ),
                ),
            ),
        ));
    }
}
