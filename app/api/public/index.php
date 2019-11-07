<?php
$startTime = microtime(true);
// Backend default timezone is UTC
date_default_timezone_set('UTC');

// Ensures at the very least we send a 500 response on fatal
register_shutdown_function('handleFatal');
function handleFatal()
{
    $error = error_get_last();
    if ($error) {
        http_response_code(500);

        if (ob_get_length() !== false) {
            ob_clean();
        }

        echo json_encode(
            [
                'messages' => [
                    'An unexpected fatal error occurred' => [
                        $error['message'],
                        $error['file'] . ': ' . $error['line']
                    ]
                ]
            ]
        );
        exit;
    }
}

$profile = getenv("XHPROF_ENABLE") == 1;

if ($profile) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    $start = microtime(true);
}

error_reporting(E_ALL & ~E_USER_DEPRECATED);

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
try {
    Zend\Mvc\Application::init(require 'config/application.config.php')->run();
} catch (Zend\ServiceManager\Exception\ServiceNotCreatedException $e) {
    do {
        $lastException = sprintf(
            "%s:%d %s (%d) [%s]\n",
            $e->getFile(),
            $e->getLine(),
            $e->getMessage(),
            $e->getCode(),
            get_class($e)
        );
    } while ($e = $e->getPrevious());
    // re-throw initial exception to get rid of plain passwords in stack trace
    throw new \Exception($lastException);
}
if ($profile) {
    $end = microtime(true);
    $xhprof_data = xhprof_disable();

    require_once __DIR__ . "../../../xhprof/xhprof_lib/utils/xhprof_lib.php";
    require_once __DIR__ . "../../../xhprof/xhprof_lib/utils/xhprof_runs.php";

    $xhprof_runs = new XHProfRuns_Default();

    $run_id = $xhprof_runs->save_run($xhprof_data, "olcs-backend");

    $fp = fopen("/tmp/xhprof.log", "a");

    $uri = strtok($_SERVER['REQUEST_URI'], "?");
    $request = $_SERVER['REQUEST_METHOD'] . " " . $uri;

    $content = "[olcs-backend] - %s(ms) - %s %s "
        . "http://192.168.149.12/private/xhprof/xhprof_html/index.php?run=%s&source=olcs-backend\n";

    $content = sprintf(
        $content,
        round(($end - $start) * 1000),
        date("Y-m-d H:i:s"),
        $request,
        $run_id
    );

    fwrite($fp, $content);
    fclose($fp);
}
$time = round(microtime(true) - $startTime, 5);
\Olcs\Logging\Log\Logger::debug(
    'Backend complete',
    [
        'time' => (string)$time,
        'url' => $_SERVER['REQUEST_URI'],
        'peak-memory-usage-MB' => (int)(memory_get_peak_usage() / 1024 / 1024)
    ]
);
