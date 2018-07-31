<?php

namespace Dvsa\Olcs\Api\Controller;

use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractRestfulController;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response as ResponsePlugin;

/**
 * Xml Controller
 * @method ResponsePlugin response()
 */
class XmlController extends AbstractRestfulController
{
    /** @var CommandHandlerInterface */
    protected $commandHandler;

    public function __construct(CommandHandlerInterface $commandHandler = null)
    {
        $this->commandHandler = $commandHandler;
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $dto = $this->params('dto');

        try {
            $this->handleCommand($dto);
            return $this->response()->xmlAccepted();
        } catch (Exception\Exception $ex) {
            return $this->response()->xmlBadRequest();
        }
    }

    /**
     * @param $dto
     * @return mixed
     */
    protected function handleCommand($dto)
    {
        return $this->commandHandler->handleCommand($dto);
    }
}
