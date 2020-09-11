<?php


namespace App\Tests\Controller;



trait EntityTrait
{
    public function assertHasErrors($entity,int $nbError)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($entity);
        $this->assertCount($nbError,$error);
    }
}