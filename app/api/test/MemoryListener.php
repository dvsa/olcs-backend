<?php

class MemoryListener implements PHPUnit_Framework_TestListener
{
    protected $start;

    protected $end;

    protected $max;

    protected $maxClass;

    protected $overallMax;

    protected $overallMaxClass;

    /**
     * An error occurred.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {

    }

    /**
     * A failure occurred.
     *
     * @param PHPUnit_Framework_Test $test
     * @param PHPUnit_Framework_AssertionFailedError $e
     * @param float $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {

    }

    /**
     * Incomplete test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {

    }

    /**
     * Risky test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @since  Method available since Release 4.0.0
     */
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {

    }

    /**
     * Skipped test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {

    }

    /**
     * A test suite started.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->max = 0;
        echo '------------------------' . "\n";
        echo get_class($suite) . "\n";
        echo '------------------------' . "\n";
    }

    /**
     * A test suite ended.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        echo '------------------------' . "\n";
        echo $this->maxClass . ': used:' . $this->max . "\n";
        echo 'Overall Max: ' . $this->overallMaxClass . ': used:' . $this->overallMax . "\n";
        echo 'PEAK: ' . memory_get_peak_usage();
        echo '------------------------' . "\n";
        $this->max = 0;
    }

    /**
     * A test started.
     *
     * @param PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->start = memory_get_usage();
    }

    /**
     * A test ended.
     *
     * @param PHPUnit_Framework_Test $test
     * @param float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $this->end = memory_get_usage();

        $diff = ($this->end - $this->start);

        $this->max = max($this->max, $diff);
        $this->overallMax = max($this->overallMax, $this->max);

        if ($this->max == $diff) {
            $this->maxClass = get_class($test);
        }

        if ($this->overallMax == $diff) {
            $this->overallMaxClass = get_class($test);
        }
    }
}
