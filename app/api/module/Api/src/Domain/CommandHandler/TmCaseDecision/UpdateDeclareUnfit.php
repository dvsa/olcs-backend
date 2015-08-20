<?php

/**
 * Update DeclareUnfit
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TmCaseDecision;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Tm\TmCaseDecision as Entity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update DeclareUnfit
 */
final class UpdateDeclareUnfit extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TmCaseDecision';

    public function handleCommand(CommandInterface $command)
    {
        if (($command->getNotifiedDate() !== null)
            && (new \DateTime($command->getNotifiedDate()) < new \DateTime($command->getDecisionDate()))
        ) {
            throw new ValidationException(['Date of notification must be after or the same as date of decision']);
        }

        if (($command->getUnfitnessEndDate() !== null)
            && (new \DateTime($command->getUnfitnessEndDate()) < new \DateTime($command->getUnfitnessStartDate()))
        ) {
            throw new ValidationException(['Unfitness end date must be after or the same as unfitness start date']);
        }

        $tmCaseDecision = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        if ($tmCaseDecision->getDecision()->getId() !== Entity::DECISION_DECLARE_UNFIT) {
            throw new BadRequestException('Invalid action');
        }

        $data = $command->getArrayCopy();

        // set unfitness reasons
        $data['unfitnessReasons'] = array_map(
            function ($unfitnessReasonId) {
                return $this->getRepo()->getRefdataReference($unfitnessReasonId);
            },
            $data['unfitnessReasons']
        );

        if (!empty($data['rehabMeasures'])) {
            // set rehab measures
            $data['rehabMeasures'] = array_map(
                function ($rehabMeasureId) {
                    return $this->getRepo()->getRefdataReference($rehabMeasureId);
                },
                $data['rehabMeasures']
            );
        }
        $tmCaseDecision->update($data);

        $this->getRepo()->save($tmCaseDecision);

        $result = new Result();
        $result->addId('tmCaseDecision', $tmCaseDecision->getId());
        $result->addMessage('Decision updated successfully');

        return $result;
    }
}
