<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $numberUser = 30;
        $faker = Factory::create();
        $users = [];
        //USERS
        for($i=0;$i<$numberUser;$i++){
            $user  = new User();
            $user->setEmail($faker->email);
            $user->setPassword('123456');
            $user->setUsername($faker->userName);
            if($i==0)
                $user->setEmail('test@test.fr');
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
            $manager->persist($task);
        }
        $manager->flush();
    }
}
