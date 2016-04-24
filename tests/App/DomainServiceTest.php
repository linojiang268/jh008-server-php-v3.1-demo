<?php
namespace Test\Jihe\App;

use Jihe\Events\Dispatcher;

abstract class DomainServiceTest extends UnitTest
{
    use TestedTargetTrait;

    /**
     * check whether the dispatcher is null
     * if yes, mok an instance
     * else, just return itself
     *
     * @param Dispatcher|null $dispatcher
     * @return Dispatcher
     */
    protected function morphMockedEventDispatcher(Dispatcher $dispatcher = null)
    {
        if (is_null($dispatcher)) {
            return $this->prophesize(Dispatcher::class)->reveal();
        }
        return $dispatcher;
    }
}