<?php

/**
 * Update Erru applied penalty
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\Applied;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Si\SiPenalty as SiPenaltyEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyType as SiPenaltyTypeEntity;
use Dvsa\Olcs\Transfer\Command\Cases\Si\Applied\Update as UpdatePenaltyCmd;

/**
 * Update Erru applied penalty
 */
final class Update extends AbstractCommandHandler
{
    const DATE_FORMAT = 'Y-m-d';

    protected $repoServiceName = 'SiPenalty';

    /**
     * Update Erru applied penalty
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var SiPenaltyEntity $penalty
         * @var SiPenaltyTypeEntity $siPenaltyType
         * @var UpdatePenaltyCmd $command
         * @to-do we should stop a penalty from being deletable based on criteria such as the msi response
         * already being sent, case being closed etc.
         */
        $penalty = $this->getRepo()->fetchUsingId($command);
        $siPenaltyType = $this->getRepo()->getReference(SiPenaltyTypeEntity::class, $command->getSiPenaltyType());
        $startDate
            = ($command->getStartDate() !== null)
                ? \DateTime::createFromFormat(self::DATE_FORMAT, $command->getStartDate()) : null;
        $endDate
            = ($command->getEndDate() !== null)
                ? \DateTime::createFromFormat(self::DATE_FORMAT, $command->getEndDate()) : null;

        $penalty->update(
            $siPenaltyType,
            $command->getImposed(),
            $startDate,
            $endDate,
            $command->getReasonNotImposed()
        );

        $this->getRepo()->save($penalty);

        $result = new Result();
        $result->addMessage('Applied penalty updated');
        $result->addId('penalty', $penalty->getId());

        return $result;
    }
}
