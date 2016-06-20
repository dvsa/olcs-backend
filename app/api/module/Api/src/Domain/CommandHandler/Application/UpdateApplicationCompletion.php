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
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\UpdateCompletion as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateVariationCompletion as VariationCommand;

/**
 * Update Application Completion
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateApplicationCompletion extends AbstractCommandHandler implements TransactionedInterface
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
        'vehicles',
        'declarationsInternal',
    ];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $application Application  */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        // if Variation then run UpdateVariationCompletion command
        if ($application->getIsVariation()) {
            return $this->proxyCommand($command, VariationCommand::class);
        }

        $completion = $application->getApplicationCompletion();
        // always reset decalration(undertakings) section, unless that it is the section just completed
        if ($command->getSection() !== 'undertakings' && $command->getSection() !== 'declarationsInternal') {
            $application->setDeclarationConfirmation('N');
        }

        $sectionsToUpdate = $this->getSectionsToUpdate($command, $completion);

        $result = new Result();

        foreach (array_keys($sectionsToUpdate) as $section) {
            $result->merge($this->handleSideEffect($this->getUpdateCommand($section, $command)));
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
