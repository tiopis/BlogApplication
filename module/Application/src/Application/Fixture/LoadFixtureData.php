<?php

namespace Application\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Application\Entity\User;
use Application\Entity\Post;

class LoadFixtureData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadPosts($manager);
    }

    public function loadPosts(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        $dateInsert = new \Datetime();

        for ($i=0; $i <= 10; $i++) {
        	$post = new Post();
            $post->setTitle('Title Test');
            $post->setText($faker->text);
            $post->setImage('php.png');
            $post->setDateInsert($dateInsert);
        	$post->setDateUpdate(null);
        	$manager->persist($post);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $password = '$2y$10$1ZnW6tC.s1KAMwaaP4DzGusd/SH5necRHWXHlCnDjBYXnk9qWCBLO';

    	$user = new User();
    	$user->setName('Tony');
    	$user->setSurname('Master');
    	$user->setPassword($password);
    	$user->setUsername('tony');
    	$user->setEmail('tony_admin@zend.com');
    	$user->setRole(USER::ROLE_ADMIN);
        $user->setToken('token');
        $user->setActive(1);
    	$manager->persist($user);

        for ($i=0; $i <= 10; $i++) {
        	$user = new User();
        	$user->setName($faker->firstName);
        	$user->setSurname($faker->lastName);
        	$user->setPassword($password);
        	$user->setUsername($faker->firstName);
        	$user->setEmail($faker->email);
        	$user->setRole(USER::ROLE_MEMBER);
            $user->setToken('token');
            $user->setActive(1);
        	$manager->persist($user);
        }

        $manager->flush();
    }
}
