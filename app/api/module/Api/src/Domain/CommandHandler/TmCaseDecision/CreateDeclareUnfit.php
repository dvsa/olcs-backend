<?php

/**
 * Create DeclareUnfit
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TmCaseDecision;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Tm\TmCaseDecision as Entity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\TmCaseDecision\CreateDeclareUnfit as Cmd;

/**
 * Create DeclareUnfit
 */
final class CreateDeclareUnfit extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TmCaseDecision';

    public function handleCommand(CommandInterface $command)
    {
        // create and save a record
        $entity = $this->createEntityObject($command);
        $this->getRepo()->save($entity);

        $result = new Result();
        $result->addId('tmCaseDecision', $entity->getId());
        $result->addMessage('Decision created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Entity
     */
    private function createEntityObject(Cmd $command)
    {
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

        return Entity::create(
            $this->getRepo()->getReference(
                CasesEntity::class,
                $command->getCase()
            ),
            $this->getRepo()->getRefdataReference(Entity::DECISION_DECLARE_UNFIT),
            $data
        );
    }
}
