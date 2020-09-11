<?php


namespace App\Tests\Entity;


use App\Entity\Task;
use App\Entity\User;
use App\Tests\Controller\EntityTrait;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    use EntityTrait;
    public function getEntity()
    {
        $task = new Task();
        $task->setUser(new User());
        $task->setTitle('title test');
        $task->setContent('content');
        $task->setCreatedAt(new DateTime('now'));
        $task->setIsDone(true);
        return $task;
    }
    public function testValidateEntity()
    {
        $this->assertHasErrors($this->getEntity(),0);
    }
    public function testEmptyTitleTask()
    {
        $task = $this->getEntity();
        $task->setTitle('');
        $this->assertHasErrors($task,1);
    }
    public function testEmptyContentTask()
    {
        $task = $this->getEntity();
        $task->setContent('');
        $this->assertHasErrors($task,1);
    }
}