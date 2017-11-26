<?php

namespace Application\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;

class FormForgotPasswordFilter extends InputFilter
{
    public function __construct($form)
    {
        $isEmpty = NotEmpty::IS_EMPTY;
        $invalidEmail = EmailAddress::INVALID_FORMAT;

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
                )
            ),
        ));
    }
}
