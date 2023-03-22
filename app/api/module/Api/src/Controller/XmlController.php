<?php

namespace Dvsa\Olcs\Api\Controller;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response as ResponsePlugin;

/**
 * Xml Controller
 * @method ResponsePlugin response()
 */
class XmlController extends AbstractRestfulController
{
    /**
     * @var CommandHandlerManager
     */
    private CommandHandlerManager $commandHandlerManager;

    /**
     * @param CommandHandlerManager $commandHandlerManager
     */
    public function __construct(
        CommandHandlerManager $commandHandlerManager
    ) {
        $this->commandHandlerManager = $commandHandlerManager;
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
        return $this->commandHandlerManager->handleCommand($dto);
    }
}
