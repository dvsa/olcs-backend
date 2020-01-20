<?php


namespace Dvsa\Olcs\Cli\Controller;

use Olcs\Logging\Log\Logger;
use Zend\View\Model\ConsoleModel;

class AbstractQueueController extends AbstractCliController
{
    public const DEFAULT_RUN_FOR = 60;

    protected $sleepFor = 1000000;

    protected $startTime;

    protected $endTime;

    /**
     * Get queue duration
     *
     * @param array $config Config
     *
     * @return number Queue duration in seconds
     */
    protected function getQueueDuration(array $config)
    {
        // get default from config, if not in config then default to self::gsDEFAULT_RUN_FOR
        $default = isset($config['runFor']) && is_numeric($config['runFor']) ?
            $config['runFor'] :
            self::DEFAULT_RUN_FOR;
        return $this->getRequest()->getParam('queue-duration', $default);
    }

    /**
     * Decide whether to run again based on config settings and time elapsed
     *
     * @return boolean
     */
    protected function shouldRunAgain(): bool
    {
        if (isset($this->endTime)) {
            return microtime(true) < $this->endTime;
        }

        return true;
    }

    /**
     * @param $exception
     * @return ConsoleModel
     */
    protected function handleORMException($exception): ConsoleModel
    {
        $content = 'ORM Error: ' . $exception->getMessage();

        Logger::log(
            \Zend\Log\Logger::ERR,
            'Failed to process next item in the queue',
            ['errorLevel' => 1, 'content' => $content]
        );

        $this->getConsole()->writeLine($content);
        $model = new ConsoleModel();
        $model->setErrorLevel(1);
        return $model;
    }
}
