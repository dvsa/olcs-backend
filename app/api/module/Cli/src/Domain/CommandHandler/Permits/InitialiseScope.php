<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Domain\Query\Permits\DeviationData as DeviationDataQuery;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Initialise Scope
 */
class InitialiseScope extends ScoringCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpCandidatePermit';

    protected $extraRepos = ['EcmtPermitApplication'];

    /**
    * Handle command
    *
    * @param CommandInterface $command command
    *
    * @return Result
    */
    public function handleCommand(CommandInterface $command)
    {
        $candidatePermitRepo = $this->getRepo();
        $ecmtPermitApplicationRepo = $this->getRepo('EcmtPermitApplication');
        $stockId = $command->getStockId();

        $this->profileMessage('clear scope...');
        $ecmtPermitApplicationRepo->clearScope($stockId);

        $this->profileMessage('apply scope...');
        $ecmtPermitApplicationRepo->applyScope($stockId);

        // TODO: should the deviation data calculations use the scope of candidate ids that don't have randomized
        // scores set, or the full list of candidate permit ids in scope?

        $this->profileMessage('fetch deviation source values...');
        $candidatePermitSourceValues = $candidatePermitRepo->fetchDeviationSourceValues($stockId);

        $totalPermitCount = count($candidatePermitSourceValues);
        $randomizedScoreCount = 0;

        if ($totalPermitCount > 0) {
            $this->profileMessage('get deviation data...');
            $deviationData = $this->handleQuery(
                DeviationDataQuery::create(
                    ['sourceValues' => $candidatePermitSourceValues]
                )
            );

            $deviation = $command->getDeviation();
            if (!is_null($deviation)) {
                $deviationData['meanDeviation'] = $deviation;
                $this->result->addMessage('using manually overridden mean deviation of ' . $deviation);
            } else {
                $this->result->addMessage('using computed mean deviation of ' . $deviationData['meanDeviation']);
            }

            $this->profileMessage('update candidate permits individually...');
            foreach ($candidatePermitSourceValues as $sourceValue) {
                $candidatePermit = $candidatePermitRepo->fetchById($sourceValue['candidatePermitId']);
                $candidatePermit->prepareForScoring();

                if (!$candidatePermit->hasRandomizedScore()) {
                    $candidatePermit->applyRandomizedScore($deviationData, $sourceValue['licNo']);
                    $randomizedScoreCount++;
                }

                $candidatePermitRepo->saveOnFlush($candidatePermit);
            }

            $this->profileMessage('flush randomised scores...');
            $candidatePermitRepo->flushAll();
        }

        $this->result->addMessage('Established scope of candidate permits');
        $this->result->addMessage('    - Candidate permits in scope: ' . $totalPermitCount);
        $this->result->addMessage('    - Randomised scores set: ' . $randomizedScoreCount);

        return $this->result;
    }
}
