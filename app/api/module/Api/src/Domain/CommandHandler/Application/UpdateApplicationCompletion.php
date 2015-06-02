<?php

/**
 * Update Application Completion
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as Cmd;

/**
 * Update Application Completion
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateApplicationCompletion extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    private $sections = [
        'addresses',
        'businessDetails',
        'businessType',
        'communityLicences',
        'conditionsUndertakings',
        'convictionsPenalties',
        'discs',
        'financialEvidence',
        'financialHistory',
        'licenceHistory',
        'operatingCentres',
        'people',
        'safety',
        'taxiPhv',
        'transportManagers',
        'typeOfLicence',
        'undertakings',
        'vehiclesDeclarations',
        'vehiclesPsv',
        'vehicles'
    ];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $application Application  */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        // if Varaition then run UpdateVariationCompletion command
        if ($application->getIsVariation()) {
            return $this->getCommandHandler()->handleCommand(
                \Dvsa\Olcs\Api\Domain\Command\Application\UpdateVariationCompletion::create(
                    ['id' => $command->getId(), 'section' => $command->getSection()]
                )
            );
        }

        $completion = $application->getApplicationCompletion();

        $sectionsToUpdate = $this->getSectionsToUpdate($command, $completion);

        $result = new Result();

        try {

            $this->getRepo()->beginTransaction();
            foreach ($sectionsToUpdate as $section => $currentStatus) {
                $result->merge($this->getCommandHandler()->handleCommand($this->getUpdateCommand($section, $command)));
            }
            $this->getRepo()->commit();

        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }

        return $result;
    }

    private function getUpdateCommand($section, Cmd $command)
    {
        $commandName = '\Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\Update' . ucfirst($section) . 'Status';

        return $commandName::create(['id' => $command->getId()]);
    }

    /**
     * Filter out the sections that we need to validate and update
     *
     * @param Cmd $command
     * @param ApplicationCompletion $completion
     * @return array
     */
    private function getSectionsToUpdate(Cmd $command, ApplicationCompletion $completion)
    {
        $sectionsToUpdate = [];

        foreach ($this->sections as $section) {
            $status = (int)$completion->{'get' . ucfirst($section) . 'Status'}();
            if ($this->shouldUpdateSection($section, $command, $status)) {
                $sectionsToUpdate[$section] = $status;
            }
        }

        return $sectionsToUpdate;
    }

    /**
     * Check if we should be updating the section
     *
     * @param string $section
     * @param Cmd $command
     * @param int $status
     * @return bool
     */
    private function shouldUpdateSection($section, Cmd $command, $status)
    {
        return $status !== ApplicationCompletion::STATUS_NOT_STARTED || $section === $command->getSection();
    }
}
