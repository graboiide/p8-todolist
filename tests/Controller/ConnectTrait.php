<?php


namespace App\Tests\Controller;


use App\DataFixtures\AppFixturesTest;
use App\Repository\UserRepository;

trait ConnectTrait
{
    private function connectedUser($client,$userName)
    {

        $userRepo = static::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(['username'=>$userName]);

        $client->loginUser($user);
        return $client;
    }
    private function connectedUserAt($userName,$path)
    {
        $client = static::createClient();


        $userRepo = static::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(['username'=>$userName]);

        $client->loginUser($user);
        $client->request('GET', $path);
        return $client;
    }
}