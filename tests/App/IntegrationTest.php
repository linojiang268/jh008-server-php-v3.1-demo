<?php
namespace Test\Jihe\App;

use Illuminate\Foundation\Testing\ApplicationTrait;
use Illuminate\Foundation\Testing\AssertionsTrait;

abstract class IntegrationTest extends \PHPUnit_Extensions_Database_TestCase
{
    use ApplicationTrait, AssertionsTrait, TestCaseTimeTrait;

    private $conn = null;

    /**
     * The callbacks that should be run before the application is destroyed.
     *
     * @var array
     */
    protected $beforeApplicationDestroyedCallbacks = [];

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * @inheritdoc
     */
    protected function getConnection()
    {

        if (is_null($this->conn)) {
            $this->conn = $this->createDefaultDBConnection($this->app['db']->getPdo());
        }

        return $this->conn;
    }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        if (!$this->app) {
            $this->refreshApplication();
        }

        parent::setUp();

        $this->setTestNow();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        if (class_exists('Mockery')) {
            Mockery::close();
        }

        if ($this->app) {
            foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
                call_user_func($callback);
            }

            $this->app->flush();

            $this->app = null;
        }
    }

    /**
     * Register a callback to be run before the application is destroyed.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function beforeApplicationDestroyed(callable $callback)
    {
        $this->beforeApplicationDestroyedCallbacks[] = $callback;
    }

    /**
     * @inheritdoc
     */
    protected function getDataSet()
    {
        // load base data
        $testData = new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/../BaseTestData.yml');
        $dataFilePath = $this->provideTestDataPath();
        if (file_exists($dataFilePath)) {
            $testData->addYamlFile($dataFilePath);
        }

        return $testData;
    }

    /**
     * provide the test data file path
     * default is the same name of the test case class and tailed with Data.yml
     * for example: UserRepositoryTest.php will use UserRepositoryTestData.yml
     *
     * @return mixed
     */
    protected function provideTestDataPath()
    {
        $testCaseFilePath = (new \ReflectionClass(static::class))->getFileName();

        // replace the '.php' of the file path to Data.yml
        // for example: UserRepositoryTest.php will be replaced to UserRepositoryTestData.yml
        return substr_replace($testCaseFilePath, 'Data.yml', -4);
    }
}