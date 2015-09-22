<?php

class Coverage implements PHPUnit_Framework_TestListener
{
    private $tests = [];

    private $test;

    /**
     * @var PHP_CodeCoverage_Filter
     */
    private $filter;

    private $formatData = [];

    public function __construct()
    {
        $this->filter = new PHP_CodeCoverage_Filter();
        $this->filter->addDirectoryToWhitelist(realpath(__DIR__ . '/../module/'));

        foreach ($this->filter->getWhitelist() as $file) {

            xdebug_start_code_coverage(XDEBUG_CC_UNUSED);
            include_once($file);
            $fileData = xdebug_get_code_coverage();
            xdebug_stop_code_coverage();

            foreach ($fileData as $fileDataFile => $lines) {
                if ($fileDataFile === $file) {
                    foreach ($lines as $k => $v) {
                        $this->formatData[$file][$k] = [];
                    }
                }
            }
        }

        xdebug_start_code_coverage();
    }

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
    }

    /**
     * A test suite ended.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }

    /**
     * A test started.
     *
     * @param PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $id = get_class($test) . '::' . $test->getName();

        if ($this->test === null) {
            $this->test = $id;
        }

        $this->tests[$id] = [
            'size' => 'unknown',
            'status' => 0
        ];
    }

    /**
     * A test ended.
     *
     * @param PHPUnit_Framework_Test $test
     * @param float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {

    }

    public function __destruct()
    {
        $data = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();

        foreach (array_keys($data) as $file) {
            if ($this->filter->isFiltered($file)) {
                unset($data[$file]);
            } else {
                unset($data[$file][0]);
                array_pop($data[$file]);
            }
        }

        foreach ($data as $file => $lines) {

            foreach ($lines as $k => $v) {

                if ($v == PHP_CodeCoverage_Driver::LINE_EXECUTED) {

                    if (empty($this->formatData[$file][$k])) {
                        $this->formatData[$file][$k] = [];
                    }

                    $this->formatData[$file][$k][] = $this->test;
                }
            }
        }

        $codeCoverage = new PHP_CodeCoverage(null, $this->filter);
        $codeCoverage->setData($this->formatData);
        $codeCoverage->setTests($this->tests);

        $writer = new PHP_CodeCoverage_Report_PHP();
        $writer->process($codeCoverage, realpath(__DIR__ . '/review/coverage.cov'));
    }
}
