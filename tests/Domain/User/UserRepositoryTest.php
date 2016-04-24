<?php
namespace Test\Jihe\Domain\User;

use Test\Jihe\App\DomainRepositoryTest;

class UserRepositoryTest extends DomainRepositoryTest
{
    public function testExistsByMobile_NotExists()
    {
        self::assertFalse($this->getTestedTarget()->existsByMobile('13800138000'));
    }

    public function testExistsByMobile_Exists()
    {
        self::assertTrue($this->getTestedTarget()->existsByMobile('13800138001'));
    }

    public function testExistsByMobile_ExistsButDeleted()
    {
        self::assertFalse($this->getTestedTarget()->existsByMobile('13800138004'));
    }
}