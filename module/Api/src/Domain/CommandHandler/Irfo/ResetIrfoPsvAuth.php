<?php

/**
 * Reset IrfoPsvAuth
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\Irfo\UpdateIrfoPsvAuth as UpdateDto;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelIrfoPsvAuthFees as CancelFeesDto;
use Olcs\Logging\Log\Logger;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Reset IrfoPsvAuth
 */
final class ResetIrfoPsvAuth extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrfoPsvAuth';

    // do we need this protected $extraRepos = ['IrfoPsvAuthNumber'];

    /**
     * Handle Reset command
     *
     * @param CommandInterface $command
     * @return Result
     * @throws Exception\BadRequestException
     * @throws Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var IrfoPsvAuth $irfoPsvAuth */
        $irfoPsvAuth = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $this->handleSideEffect(
            UpdateDto::create(
                $command->getArrayCopy()
            )
        );

        /*
        Update does not affect status or fees, so there is no need to ensure we have the updated entity prior to
        resetting. Reseting only affects the status
         */
        $newStatus = IrfoPsvAuth::STATUS_PENDING;
        if ($irfoPsvAuth->getStatus() == IrfoPsvAuth::STATUS_CNS) {
            $newStatus = IrfoPsvAuth::STATUS_RENEW;
        }

        $irfoPsvAuth->reset(
            $this->getRepo()->getRefdataReference($newStatus)
        );

        $this->getRepo()->save($irfoPsvAuth);

        $result->addId('irfoPsvAuth', $irfoPsvAuth->getId());

        $result->addMessage('IRFO PSV Auth reset successfully');

        return $result;
    }
}
