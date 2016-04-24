<?php
namespace Test\Jihe\Domain\Vcode;

use Carbon\Carbon;
use Jihe\Domain\Vcode\SmsVcode;
use Test\Jihe\App\DomainRepositoryTest;

class SmsVcodeRepositoryTest extends DomainRepositoryTest
{
    public function testCountByMobile_AllActive()
    {
        self::assertEquals(1,
                           $this->getTestedTarget()->countByMobile(
                               '13800138000',
                                $this->createDateTime('2015-08-24 12:30:00')
                           )
        );

        self::assertEquals(2, $this->getTestedTarget()->countByMobile('13800138000'));
    }

    public function testCountByMobile_OneDeleted()
    {
        self::assertEquals(0,
                           $this->getTestedTarget()->countByMobile(
                               '13800138001',
                               $this->createDateTime('2015-08-24 12:30:00')
                           )
        );
        self::assertEquals(1, $this->getTestedTarget()->countByMobile('13800138001'));
    }

    public function testFindLastValid_Exists()
    {
        $now = $this->createDateTime('2015-08-24 12:47:46');
        self::assertNotNull($this->getTestedTarget()->findLastValid('13800138000', $now));

        $this->setTestNow($now);
        self::assertNotNull($this->getTestedTarget()->findLastValid('13800138000'));
    }

    public function testFindLastValid_Expired()
    {
        $now = $this->createDateTime('2015-08-24 12:49:46');

        self::assertNull($this->getTestedTarget()->findLastValid('13800138000', $now));

        $this->setTestNow($now);
        self::assertNull($this->getTestedTarget()->findLastValid('13800138000'));
    }

    public function testFindLastValid_ExistsButDeleted()
    {
        $now = $this->createDateTime('2015-08-24 12:47:46');
        self::assertNull($this->getTestedTarget()->findLastValid('13800138001', $now));

        $this->setTestNow($now);
        self::assertNull($this->getTestedTarget()->findLastValid('13800138001', $now));
    }

    /**
     * @inheritdoc
     *
     * @return \Jihe\Domain\Vcode\SmsVcodeRepository
     */
    protected function getTestedTarget()
    {
        return $this->app[\Jihe\Domain\Vcode\SmsVcodeRepository::class];
    }
}