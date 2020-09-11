<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixturesTest;
use App\Entity\Task;
use App\Repository\TaskRepository;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;



class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    use ConnectTrait;
    public function testDisplayAllTask()
    {

        $client = static::createClient();
        $this->loadFixtures([AppFixturesTest::class]);
        $this->connectedUser($client,'test');
        $crawler = $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.tasks-list');
    }
    public function testUserCreateTask()
    {

        $client = static::createClient();
        $this->connectedUser($client,'test');
        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]'=>'test add task',
            'task[content]'=>'Content of test add task'
        ]);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');

    }
    public function testEditOneTask()
    {

        $client = static::createClient();
        $this->connectedUser($client,'test');
        $taskRepo = static::$container->get(TaskRepository::class);
        /** @var Task $taskTest */
        $taskTest = $taskRepo->findOneBy(['title'=>'test task']);

        $client->request('GET', '/tasks/'.$taskTest->getId().'/edit');

        $this->assertSelectorExists('.edit-task');

    }

    public function testUserDeleteHisTask()
    {
        $client = static::createClient();
        //get task
        $taskRepo = static::$container->get(TaskRepository::class);
        /** @var Task $taskTest */
        $taskTest = $taskRepo->findOneBy(['title'=>'test task']);
        //connect with user
        $this->connectedUser($client,'test');

        $client->request('GET', '/tasks/'.$taskTest->getId().'/delete');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }
    public function testUserDeleteTaskNotHim()
    {

        $client = static::createClient();
        //get task
        $taskRepo = static::$container->get(TaskRepository::class);
        /** @var Task $taskTest */
        $taskTest = $taskRepo->findOneBy(['title'=>'testing task']);
        //connect with user
        $this->connectedUser($client,'test');

        $client->request('GET', '/tasks/'.$taskTest->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }
    public function testAdminDeleteAnonymousTask()
    {

        $client = static::createClient();
        //get task
        $taskRepo = static::$container->get(TaskRepository::class);
        /** @var Task $taskTest */
        $taskTest = $taskRepo->findOneBy(['title'=>'anonymous task']);
        //connect with user
        $this->connectedUser($client,'admin');

        $client->request('GET', '/tasks/'.$taskTest->getId().'/delete');
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.alert-success');
    }


}
