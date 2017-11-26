<?php

namespace Application\InputFilter;

use Zend\InputFilter\InputFilter;
use	Zend\Validator\EmailAddress;
use	Zend\Validator\NotEmpty;

class FormContactFilter extends InputFilter
{
    public function __construct($form)
    {
        $isEmpty = NotEmpty::IS_EMPTY;
        $invalidEmail = EmailAddress::INVALID_FORMAT;

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
                        'min' => 4,
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
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            $isEmpty => 'Email can not be empty.'
                        )
                    ),
                    'break_chain_on_failure' => true
                ),
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'messages' => array(
                            $invalidEmail => 'Enter Valid Email Address.'
                        )
                    )
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'comment',
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
    }
}
