<?php

/**
 * CNS IrfoPsvAuth
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\Irfo\UpdateIrfoPsvAuth as UpdateDto;

/**
 * CNS IrfoPsvAuth
 */
final class CnsIrfoPsvAuth extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrfoPsvAuth';

    /**
     * Handle CNS command
     *
     * @param CommandInterface $command
     * @return Result
     * @throws Exception\BadRequestException
     * @throws Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrfoPsvAuth $irfoPsvAuth */
        $irfoPsvAuth = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $this->handleSideEffect(
            UpdateDto::create(
                $command->getArrayCopy()
            )
        );

        $irfoPsvAuth->continuationNotSought(
            $this->getRepo()->getRefdataReference(IrfoPsvAuth::STATUS_CNS)
        );

        $this->getRepo()->save($irfoPsvAuth);

        $result = new Result();
        $result->addId('irfoPsvAuth', $irfoPsvAuth->getId());
        $result->addMessage('IRFO PSV Auth updated successfully');

        return $result;
    }
}
