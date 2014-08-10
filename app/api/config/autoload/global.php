<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'proxy_dir'         => sys_get_temp_dir() . '/OlcsBe/Proxy',
                'proxy_namespace'   => 'OlcsBe\Proxy',
            ),
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Logger' => function ($sm) {
                $log = new \Zend\Log\Logger();

                /**
                 * In development / integration - we log everything.
                 * In production, our logging
                 * is restricted to \Zend\Log\Logger::ERR and above.
                 *
                 * For logging priorities, see:
                 * @see http://www.php.net/manual/en/function.syslog.php#refsect1-function.syslog-parameters
                 */
                $filter = new \Zend\Log\Filter\Priority(LOG_DEBUG);

                // Log file
                $fileWriter = new \Zend\Log\Writer\Stream(sys_get_temp_dir() . '/olcs-application.log');
                $fileWriter->addFilter($filter);
                $log->addWriter($fileWriter);

                $nullWriter = new \Zend\Log\Writer\Null();
                $log->addWriter($nullWriter);

                // Log to sys log - useful if file logging is not working.
                /* $sysLogWriter = new \Zend\Log\Writer\Syslog();
                $sysLogWriter->addFilter($filter);
                $log->addWriter($sysLogWriter); */

                return $log;
            },
        ),
    ),
);
