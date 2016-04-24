<?php
namespace Test\Jihe\Domain\Team;

use Jihe\Domain\User\User;
use Test\Jihe\App\DomainRepositoryTest;

class EnrollmentRequestRepositoryTest extends DomainRepositoryTest
{
    public function testFindById()
    {
        self::assertNotNull($this->getTestedTarget()->find('b89c6de4-5076-11e5-a72a-0edf346561b5'));
        self::assertNotNull($this->getTestedTarget()->find('430c2a8a-5079-11e5-a72a-0edf346561b5'));
    }

    public function testGetPendingCount()
    {
        $user = $this->getUser('a61df048-4a18-11e5-98fc-5b4856d52a52');
        self::assertEquals(0, $this->getTestedTarget()->getPendingCount($user));
        $user = $this->getUser('85dd4754-5079-11e5-a72a-0edf346561b5');
        self::assertEquals(1, $this->getTestedTarget()->getPendingCount($user));
        $user = $this->getUser('d80413f0-511e-11e5-b1cf-64c3a5f1c712');
        self::assertEquals(0, $this->getTestedTarget()->getPendingCount($user));
    }

    public function testGetApprovedCount()
    {
        $user = $this->getUser('a61df048-4a18-11e5-98fc-5b4856d52a52');
        self::assertEquals(1, $this->getTestedTarget()->getApprovedCount($user));
        $user = $this->getUser('85dd4754-5079-11e5-a72a-0edf346561b5');
        self::assertEquals(0, $this->getTestedTarget()->getApprovedCount($user));
        $user = $this->getUser('d80413f0-511e-11e5-b1cf-64c3a5f1c712');
        self::assertEquals(0, $this->getTestedTarget()->getApprovedCount($user));
    }

    private function getUser($userId)
    {
        $userRepository = $this->app['em'];
        return $userRepository->find(User::class, $userId);
    }
}