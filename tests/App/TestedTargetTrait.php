<?php
namespace Test\Jihe\App;

trait TestedTargetTrait
{

    /**
     * get the tested target
     *
     * @return mixed
     */
    protected function getTestedTarget()
    {
        $testCaseFilePath = (new \ReflectionClass(static::class))->getName();

        // replace the '.php' of the file path to Data.yml
        // for example: UserRepositoryTest.php will be replaced to UserRepositoryTestData.yml
        $className = substr(str_replace('Test', '', $testCaseFilePath), 1);
        return $this->app[$className];
    }

}