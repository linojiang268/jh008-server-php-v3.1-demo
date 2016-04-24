<?php
namespace Test\Jihe\App;

abstract class UnitTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTimeTrait;

    public function setUp()
    {
        $this->setTestNow();
    }

}