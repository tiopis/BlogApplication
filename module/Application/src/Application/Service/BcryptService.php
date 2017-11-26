<?php

namespace Application\Service;

use Zend\Crypt\Password\Bcrypt;
use Application\Entity\User;

class BcryptService
{
	public static function verifyHashedPassword(User $user, $passwordGiven)
	{
		$bcrypt = new Bcrypt;

        return $bcrypt->verify($passwordGiven, $user->getPassword());
	}
}
