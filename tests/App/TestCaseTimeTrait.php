<?php
namespace Test\Jihe\App;

use Carbon\Carbon;

trait TestCaseTimeTrait
{
    /**
     * set current time for test
     *
     * @param null|string $now format of Y-m-d H:i:s, null means set to current time
     */
    protected function setTestNow($now = null)
    {
        if (is_null($now)) {
            Carbon::setTestNow(null);
            Carbon::setTestNow(Carbon::now());
            return;
        }

        if (is_string($now)) {
            Carbon::setTestNow($this->createDateTime($now));
        } else {
            Carbon::setTestNow($now);
        }

    }

    /**
     * get a DateTime instance from the time string
     *
     * @param string $time string with the format of Y-m-d H:i:s
     * @return Carbon
     */
    protected function createDateTime($time)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $time);
    }

    /**
     * get the current time
     *
     * @return Carbon
     */
    protected function getNow()
    {
        return Carbon::now();
    }
}