<?php

namespace Tests\App\Controller;

use App\Tests\Controller\ConnectTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use ConnectTrait;
    public function testDisplayLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.login');
    }
    public function testLoginCheckError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username'=>'test',
            '_password'=>'bad_password'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }
    public function testLoginCheckOk()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username'=>'test',
            '_password'=>'123456'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects($client->getResponse()->headers->get('location'));
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
