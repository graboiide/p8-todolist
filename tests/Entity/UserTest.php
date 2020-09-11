<?php


namespace App\Tests\Entity;


use App\Entity\Task;
use App\Entity\User;
use App\Tests\Controller\EntityTrait;
use DateTime;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use EntityTrait;
    public function getEntity()
    {
        $user = new User();
        $user->setEmail('user@user.fr');
        $user->setPassword('123456');
        $user->setUsername('user_test');
        return $user;
    }

    public function testValidateEntity()
    {
        $this->assertHasErrors($this->getEntity(),0);

    }
    public function testEmptyUsername()
    {
       $user = $this->getEntity();
       $user->setUsername('');
       $this->assertHasErrors($user,1);

    }
    public function testEmailValid()
    {
        $user = $this->getEntity();
        $user->setEmail('usertest.fr');
        $this->assertHasErrors($user,1);
    }
    public function testEmptyEmail()
    {
        $user = $this->getEntity();
        $user->setEmail('');
        $this->assertHasErrors($user,1);
    }

}