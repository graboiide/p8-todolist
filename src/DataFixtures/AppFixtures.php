<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $numberUser = 30;
        $faker = Factory::create();
        $users = [];
        //ROLES

        $roleUser = new Role();
        $roleUser->setTitle('ROLE_USER');
        $manager->persist($roleUser);
        $roleAdmin = new Role();
        $roleAdmin->setTitle('ROLE_ADMIN');
        $manager->persist($roleAdmin);
        //USERS
        for($i=0;$i<$numberUser;$i++){
            $user  = new User();
            $user->setEmail($faker->email);
            $user->setPassword($this->encoder->encodePassword($user,'123456'));
            $user->setUsername($faker->userName);
            if($i==0)
                $user->setUsername('test');
            if($i==1)
                $user->setUsername('anonyme');
            if($i===2){
                $user->setUsername('admin');
                $user->addUserRole($roleAdmin);
                $roleAdmin->addUser($user);
            }
            $users[] = $user;
            $manager->persist($user);
        }
        //OLD TASKS
        for($i=0;$i<25;$i++){
            $task  = new Task();
            $task->setTitle($faker->sentence());
            $task->setContent($faker->text(500));
            $task->setCreatedAt($faker->dateTimeBetween('-2 years'));
            $task->isDone();
            $task->toggle(boolval(rand(0,1)));
            $manager->persist($task);
        }
        //TASKS
        for($i=0;$i<200;$i++){
            $task  = new Task();
            $task->setUser($users[rand(0,$numberUser-1)]);
            $task->setTitle($faker->sentence());
            $task->setContent($faker->text(500));
            $task->setCreatedAt($faker->dateTimeBetween('-2 years'));
            $task->isDone();
            $task->toggle(boolval(rand(0,1)));

            if ($i===0)
                $task->setTitle('testing task');
            $manager->persist($task);
        }
        $manager->flush();
    }
}
