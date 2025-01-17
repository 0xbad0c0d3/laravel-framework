<?php

namespace Illuminate\Tests\Integration\Queue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Orchestra\Testbench\TestCase;

/**
 * @group integration
 */
class JobDispatchingTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', 'true');
    }

    protected function tearDown(): void
    {
        Job::$ran = false;
    }

    public function testJobCanUseCustomMethodsAfterDispatch()
    {
        if (\PHP_VERSION_ID >= 80100) {
            $this->markTestSkipped('Test failing in PHP 8.1');
        }

        Job::dispatch('test')->replaceValue('new-test');

        $this->assertTrue(Job::$ran);
        $this->assertSame('new-test', Job::$value);
    }
}

class Job implements ShouldQueue
{
    use Dispatchable, Queueable;

    public static $ran = false;
    public static $usedQueue = null;
    public static $usedConnection = null;
    public static $value = null;

    public function __construct($value)
    {
        static::$value = $value;
    }

    public function handle()
    {
        static::$ran = true;
    }

    public function replaceValue($value)
    {
        static::$value = $value;
    }
}
