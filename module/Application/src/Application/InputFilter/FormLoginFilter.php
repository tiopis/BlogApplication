<?php

namespace Application\InputFilter;

use Zend\InputFilter\InputFilter;

class FormLoginFilter extends InputFilter
{
    public function __construct($form)
    {
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
