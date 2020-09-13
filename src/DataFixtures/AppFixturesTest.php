<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixturesTest extends Fixture implements FixtureGroupInterface
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        //ROLES
        $roleUser = new Role();
        $roleUser->setTitle('ROLE_USER');
        $manager->persist($roleUser);
        $roleAdmin = new Role();
        $roleAdmin->setTitle('ROLE_ADMIN');
        $manager->persist($roleAdmin);
        //USERS
        //test
        $userTest = new User();
        $userTest->addUserRole($roleUser);
        $userTest->setEmail("test@test.fr");
        $userTest->setUsername("test");
        $userTest->setPassword("123456");
        //admin
        $userAdmin = new User();
        $userAdmin->addUserRole($roleAdmin);
        $userAdmin->setEmail("admin@test.fr");
        $userAdmin->setUsername("admin");
        $userAdmin->setPassword("123456");
        $roleAdmin->addUser($userAdmin);
        //anonymous

        $userAnnonymous = new User();
        $userAnnonymous->addUserRole($roleUser);
        $userAnnonymous->setEmail("anonyme@test.fr");
        $userAnnonymous->setUsername("anonyme");
        $userAnnonymous->setPassword("123456");

        $manager->persist($userAdmin);
        $manager->persist($userTest);
        $manager->persist($userAnnonymous);

        //TASKS
        for($i=0;$i<3;$i++){
            $task  = new Task();
            $task->setUser($userTest);
            $task->setTitle($faker->sentence());
            $task->setContent($faker->text(500));
            $task->setCreatedAt($faker->dateTimeBetween('-2 years'));
            $task->isDone();
            $task->toggle(boolval(rand(0,1)));

            if ($i===0){
                $task->setTitle('testing task');
                $task->setUser($userAdmin);
            }
            if ($i===1){
                $task->setTitle('test task');
                $userTest->addTask($task);
            }
            if ($i===2){
                $task->setTitle('anonymous task');
                $task->setUser($userAnnonymous);
                $userAnnonymous->addTask($task);
            }

            $manager->persist($task);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
