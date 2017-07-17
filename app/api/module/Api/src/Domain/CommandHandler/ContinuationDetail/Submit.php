<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\Submit as Command;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;

/**
 * Submit Continuation Detail
 */
final class Submit extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ContinuationDetail';

    /**
     * Handle command
     *
     * @param CommandInterface $command DTO command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */

        /* @var $continuationDetail ContinuationDetail */
        $continuationDetail = $this->getRepo()->fetchById(
            $command->getId(),
            \Doctrine\ORM\Query::HYDRATE_OBJECT,
            $command->getVersion()
        );

        // If signatureType is null, then must be printing and signing declaration.
        // If using Verify, signatureType would have already been set to SIG_DIGITAL_SIGNATURE
        if ($continuationDetail->getSignatureType() === null) {
            $continuationDetail->setSignatureType(
                $this->getRepo()->getRefdataReference(RefData::SIG_PHYSICAL_SIGNATURE)
            );
        }
        $continuationDetail->setIsDigital(true);

        $this->getRepo()->save($continuationDetail);

        $result = new Result();
        $result->addId('continuationDetail', $continuationDetail->getId());
        $result->addMessage('ContinuationDetail submitted');

        return $result;
    }
}
