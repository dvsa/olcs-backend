<?php

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\Warning;
use SebastianBergmann\CodeCoverage;

/**
 * Class Coverage
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 *
 */
class Coverage implements TestListener
{
    private $tests = [];

    private $test;

    /**
     * @var CodeCoverage\Filter
     */
    private $filter;

    private $formatData = [];

    public function __construct()
    {
        $this->filter = new CodeCoverage\Filter();
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
     * add error
     *
     * @param Test      $test test
     * @param Throwable $t    warning
     * @param float     $time time
     *
     * @return void
     */
    public function addError(Test $test, \Throwable $t, float $time): void
    {
    }

    /**
     * add warning
     *
     * @param Test    $test test
     * @param Warning $e    warning
     * @param float   $time time
     *
     * @return void
     */
    public function addWarning(Test $test, Warning $e, float $time): void
    {
    }

    /**
     * add failure
     *
     * @param Test                 $test test
     * @param AssertionFailedError $e    error
     * @param float                $time time
     *
     * @return void
     */
    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
    }


    /**
     * add incomplete test
     *
     * @param Test      $test test
     * @param Throwable $t    throwable
     * @param float     $time time
     *
     * @return void
     */
    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
    }

    /**
     * add risky test
     *
     * @param Test      $test test
     * @param Throwable $t    throwable
     * @param float     $time time
     *
     * @return void
     */
    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
    }

    /**
     * add skipped test
     *
     * @param Test      $test test
     * @param Throwable $t    throwable
     * @param float     $time time
     *
     * @return void
     */
    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
    }

    /**
     * A test suite started.
     *
     * @param TestSuite $suite suite
     *
     * @return void
     */
    public function startTestSuite(TestSuite $suite): void
    {
    }

    /**
     * A test suite ended.
     *
     * @param TestSuite $suite suite
     *
     * @return void
     */
    public function endTestSuite(TestSuite $suite): void
    {
    }

    /**
     * A test started.
     *
     * @param Test $test test
     *
     * @return void
     */
    public function startTest(Test $test): void
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
     * @param Test  $test test
     * @param float $time time
     *
     * @retun void
     */
    public function endTest(Test $test, float $time): void
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
            }
        }

        foreach ($data as $file => $lines) {
            foreach ($lines as $k => $v) {
                if ($v == CodeCoverage\Driver\Driver::LINE_EXECUTED) {
                    if (empty($this->formatData[$file][$k])) {
                        $this->formatData[$file][$k] = [];
                    }

                    $this->formatData[$file][$k][] = $this->test;
                }
            }
        }

        $this->applyIgnoredLinesFilter();

        $codeCoverage = new CodeCoverage\CodeCoverage(null, $this->filter);
        $codeCoverage->setData($this->formatData);
        $codeCoverage->setTests($this->tests);

        $writer = new CodeCoverage\Report\PHP();
        $writer->process($codeCoverage, realpath(__DIR__ . '/review') . '/coverage.cov');
    }

    /**
     * @param string $filename filename
     *
     * @return array
     */
    private function getLinesToBeIgnored(string $filename)
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
     * @return void
     */
    private function applyIgnoredLinesFilter(): void
    {
        foreach (array_keys($this->formatData) as $filename) {
            foreach ($this->getLinesToBeIgnored($filename) as $line) {
                unset($this->formatData[$filename][$line]);
            }
        }
    }
}
