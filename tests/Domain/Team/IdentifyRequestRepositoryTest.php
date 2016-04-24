<?php
namespace Test\Jihe\Domain\Team;

use Test\Jihe\App\DomainRepositoryTest;

class IdentifyRequestRepositoryTest extends DomainRepositoryTest
{
    public function testFindById()
    {
        self::assertNotNull($this->getTestedTarget()->find('c09de206-5090-11e5-a72a-0edf346561b5'));
    }
}