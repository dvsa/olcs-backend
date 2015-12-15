<?php

/**
 * Refuse IrfoPsvAuth
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
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;

/**
 * Refuse IrfoPsvAuth
 */
final class RefuseIrfoPsvAuth extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrfoPsvAuth';

    protected $extraRepos = ['IrfoPsvAuthNumber'];

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
        granting. Refusing only affects the status
         */
        $irfoPsvAuth->refuse(
            $this->getRepo()->getRefdataReference(IrfoPsvAuth::STATUS_REFUSED)
        );

        $this->getRepo()->save($irfoPsvAuth);

        $result->addId('irfoPsvAuth', $irfoPsvAuth->getId());

        // cancel all associated fees
        $result->merge($this->cancelFees($command));

        $result = new Result();
        $result->addId('irfoPsvAuth', $irfoPsvAuth->getId());
        $result->addMessage('IRFO PSV Auth refused successfully');

        return $result;
    }

    /**
     * Cancel all associated fees, but IRFOPSVAPP
     *
     * @param CommandInterface $command
     * @return Result
     */
    private function cancelFees(CommandInterface $command)
    {
        return $this->getCommandHandler()->handleCommand(
            CancelFeesDto::create(
                [
                    'id' => $command->getId(),
                    'exclusions' => [RefDataEntity::FEE_TYPE_IRFOPSVAPP]
                ]
            )
        );
    }
}
