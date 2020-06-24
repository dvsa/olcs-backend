<?php

/**
 * Implementation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation;

use Dvsa\Olcs\Api\Domain\ValidationHandlerManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\Config;

/**
 * Implementation Test
 *
 * Test to ensure that validation has been implemented for all commands/queries
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ImplementationTest extends MockeryTestCase
{
    const COMMAND_KEY = \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::CONFIG_KEY;
    const QUERY_KEY = \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::CONFIG_KEY;
    const VALIDATION_KEY = \Dvsa\Olcs\Api\Domain\ValidationHandlerManagerFactory::CONFIG_KEY;

    private $handlers = [];

    /**
     * @var ValidationHandlerManager
     */
    private $validationManager;

    public function setUp(): void
    {
        $moduleDir = __DIR__ . '/../../../../../../module';

        $validationHandlers = [];

        foreach (glob($moduleDir . '/*/config/module.config.php') as $filename) {
            $config = include($filename);

            if (isset($config[self::COMMAND_KEY]['factories'])) {
                foreach ($config[self::COMMAND_KEY]['factories'] as $key => $handler) {
                    $this->handlers[$handler] = $handler;
                }
            }

            if (isset($config[self::QUERY_KEY]['factories'])) {
                foreach ($config[self::QUERY_KEY]['factories'] as $key => $handler) {
                    $this->handlers[$handler] = $handler;
                }
            }

            if (isset($config[self::VALIDATION_KEY])) {
                $validationHandlers = $config[self::VALIDATION_KEY];
            }
        }

        $validationHandlerConfig = new Config($validationHandlers);

        $this->validationManager = new ValidationHandlerManager($validationHandlerConfig);
    }

    public function testAllImplemented()
    {
        $errors = [];

        foreach ($this->handlers as $handler) {
            if ($this->validationManager->has($handler) === false) {
                $errors[] = $handler;
            }
        }

        if (!empty($errors)) {
            $this->fail(
                'Validation for the following handlers has not been implemented:' . "\n" . implode("\n", $errors)
            );
        } else {
            $this->assertTrue(true);
        }
    }
}
