<?php

namespace Application\InputFilter;

use Zend\InputFilter\InputFilter;
use Application\InputFilter\FormUserFilter;

class FormUserProfileEditFilter extends FormUserFilter
{
	public function __construct($form)
	{
		parent::__construct(null);

        $this->remove('password');
		$this->remove('role');
	}
}
