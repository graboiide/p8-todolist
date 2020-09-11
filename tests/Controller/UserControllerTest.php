<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixturesTest;
use App\Entity\Task;
use App\Repository\TaskRepository;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;



class UserControllerTest extends WebTestCase
{
    use FixturesTrait;
    use ConnectTrait;

    public function testAccesslistUsersForAdmin()
    {
        $client = static::createClient();
        $this->connectedUser($client,'admin');
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1','Liste des utilisateurs');

    }
    public function testAccesslistUsersForbiddenForUser()
    {
        $client = static::createClient();
        $this->connectedUser($client,'test');
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);


    }
    public function testDisplayFormEdit()
    {
        $client = static::createClient();
        $this->connectedUser($client,'admin');
        $client->request('GET', '/users/2/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1','Modifier test');
    }
    public function testAdminCreateUser()
    {

        $client = static::createClient();
        $this->connectedUser($client,'admin');
        $crawler = $client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]'=>'userstest',
            'user[password][first]'=>'123456',
            'user[password][second]'=>'123456',
            'user[email]'=>'gttt@g.gt',
            'user[userRoles]'=>[2]
        ]);

        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');

    }
    public function testAdminEditUser()
    {

        $client = static::createClient();

        $this->connectedUser($client,'admin');
        $crawler = $client->request('GET', '/users/2/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]'=>'test',
            'user[password][first]'=>'123456',
            'user[password][second]'=>'123456',
            'user[email]'=>'new@mail.gt',
            'user[userRoles]'=>[1]
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/users');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');

    }
    public function testUserEditHim()
    {
        $client = static::createClient();
        //$this->loadFixtures([AppFixturesTest::class]);

        $this->connectedUser($client,'test');
        $crawler = $client->request('GET', '/users/2/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]'=>'test',
            'user[password][first]'=>'123456',
            'user[password][second]'=>'123456',
            'user[email]'=>'new@mail.gt'
        ]);

        $client->submit($form);

        $this->assertSelectorExists('.alert-success');

    }
    public function testUserDontEditOtherUser()
    {
        $client = static::createClient();
        $this->connectedUser($client,'test');
        $client->request('GET', '/users/3/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


}
