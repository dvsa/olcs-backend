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

            xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
            include_once($file);
            $fileData = xdebug_get_code_coverage();
            xdebug_stop_code_coverage();

            foreach ($fileData as $fileDataFile => $lines) {
                if ($fileDataFile === $file) {
                    foreach ($lines as $k => $v) {
                        $this->formatData[$file][$k] = $v == -2 ? null : [];
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

        $this->applyIgnoredLinesFilter();

        $codeCoverage = new PHP_CodeCoverage(null, $this->filter);
        $codeCoverage->setData($this->formatData);
        $codeCoverage->setTests($this->tests);

        $writer = new PHP_CodeCoverage_Report_PHP();
        $writer->process($codeCoverage, realpath(__DIR__ . '/review/coverage.cov'));
    }

    /**
     * Returns the lines of a source file that should be ignored.
     *
     * @param  string                     $filename
     * @return array
     * @throws PHP_CodeCoverage_Exception
     * @since  Method available since Release 2.0.0
     */
    private function getLinesToBeIgnored($filename)
    {
        $ignoredLines = [];

        $ignore   = false;
        $stop     = false;
        $lines    = file($filename);
        $numLines = count($lines);

        foreach ($lines as $index => $line) {
            if (!trim($line)) {
                $ignoredLines[] = $index + 1;
            }
        }

        $tokens = new PHP_Token_Stream($filename);

        $classes = array_merge($tokens->getClasses(), $tokens->getTraits());
        $tokens  = $tokens->tokens();

        foreach ($tokens as $token) {
            switch (get_class($token)) {
                case 'PHP_Token_COMMENT':
                case 'PHP_Token_DOC_COMMENT':
                    $_token = trim($token);
                    $_line  = trim($lines[$token->getLine() - 1]);

                    if ($_token == '// @codeCoverageIgnore' ||
                        $_token == '//@codeCoverageIgnore') {
                        $ignore = true;
                        $stop   = true;
                    } elseif ($_token == '// @codeCoverageIgnoreStart' ||
                        $_token == '//@codeCoverageIgnoreStart') {
                        $ignore = true;
                    } elseif ($_token == '// @codeCoverageIgnoreEnd' ||
                        $_token == '//@codeCoverageIgnoreEnd') {
                        $stop = true;
                    }

                    if (!$ignore) {
                        $start = $token->getLine();
                        $end   = $start + substr_count($token, "\n");

                        // Do not ignore the first line when there is a token
                        // before the comment
                        if (0 !== strpos($_token, $_line)) {
                            $start++;
                        }

                        for ($i = $start; $i < $end; $i++) {
                            $ignoredLines[] = $i;
                        }

                        // A DOC_COMMENT token or a COMMENT token starting with "/*"
                        // does not contain the final \n character in its text
                        if (isset($lines[$i-1]) && 0 === strpos($_token, '/*')
                            && '*/' === substr(trim($lines[$i-1]), -2)
                        ) {
                            $ignoredLines[] = $i;
                        }
                    }
                    break;

                case 'PHP_Token_INTERFACE':
                case 'PHP_Token_TRAIT':
                case 'PHP_Token_CLASS':
                case 'PHP_Token_FUNCTION':
                    $docblock = $token->getDocblock();

                    $ignoredLines[] = $token->getLine();

                    if (strpos($docblock, '@codeCoverageIgnore') || strpos($docblock, '@deprecated')) {
                        $endLine = $token->getEndLine();

                        for ($i = $token->getLine(); $i <= $endLine; $i++) {
                            $ignoredLines[] = $i;
                        }
                    } elseif ($token instanceof PHP_Token_INTERFACE ||
                        $token instanceof PHP_Token_TRAIT ||
                        $token instanceof PHP_Token_CLASS) {
                        if (empty($classes[$token->getName()]['methods'])) {
                            for ($i = $token->getLine();
                                 $i <= $token->getEndLine();
                                 $i++) {
                                $ignoredLines[] = $i;
                            }
                        } else {
                            $firstMethod = array_shift(
                                $classes[$token->getName()]['methods']
                            );

                            do {
                                $lastMethod = array_pop(
                                    $classes[$token->getName()]['methods']
                                );
                            } while ($lastMethod !== null &&
                                substr($lastMethod['signature'], 0, 18) == 'anonymous function');

                            if ($lastMethod === null) {
                                $lastMethod = $firstMethod;
                            }

                            for ($i = $token->getLine();
                                 $i < $firstMethod['startLine'];
                                 $i++) {
                                $ignoredLines[] = $i;
                            }

                            for ($i = $token->getEndLine();
                                 $i > $lastMethod['endLine'];
                                 $i--) {
                                $ignoredLines[] = $i;
                            }
                        }
                    }
                    break;

                case 'PHP_Token_NAMESPACE':
                    $ignoredLines[] = $token->getEndLine();

                // Intentional fallthrough
                case 'PHP_Token_OPEN_TAG':
                case 'PHP_Token_CLOSE_TAG':
                case 'PHP_Token_USE':
                    $ignoredLines[] = $token->getLine();
                    break;
            }

            if ($ignore) {
                $ignoredLines[] = $token->getLine();

                if ($stop) {
                    $ignore = false;
                    $stop   = false;
                }
            }
        }

        $ignoredLines[] = $numLines + 1;

        $ignoredLines = array_unique(
            $ignoredLines
        );

        sort($ignoredLines);

        return $ignoredLines;
    }

    /**
     * Applies the "ignored lines" filtering.
     *
     * @param array $data
     */
    private function applyIgnoredLinesFilter()
    {
        foreach (array_keys($this->formatData) as $filename) {
            foreach ($this->getLinesToBeIgnored($filename) as $line) {
                unset($this->formatData[$filename][$line]);
            }
        }
    }
}
