<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Permits\UpdateCountries as UpdateCountriesCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update countries
 */
class UpdateCountries extends AbstractCommandHandler implements
    ToggleRequiredInterface,
    TransactionedInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['IrhpPermitWindow', 'IrhpPermitApplication'];

    /**
     * Handle command
     *
     * @param UpdateCountriesCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplicationId = $command->getId();

        /* @var $irhpApplicationRepo \Dvsa\Olcs\Api\Domain\Repository\IrhpApplication */
        $irhpApplicationRepo = $this->getRepo();

        /* @var $irhpApplication IrhpApplication */
        $irhpApplication = $irhpApplicationRepo->fetchById($irhpApplicationId);

        if (!$irhpApplication->canUpdateCountries()) {
            throw new ValidationException(['IRHP application cannot be updated.']);
        }

        // get the list of existing IrhpPermitApplication indexed by window id
        $existingIrhpPermitAppsByWindowId = [];

        /* @var $irhpPermitApplication IrhpPermitApplication */
        foreach ($irhpApplication->getIrhpPermitApplications() as $irhpPermitApplication) {
            $existingIrhpPermitAppsByWindowId[$irhpPermitApplication->getIrhpPermitWindow()->getId()]
                = $irhpPermitApplication->getId();
        }

        /* @var $irhpPermitWindowRepo \Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow */
        $irhpPermitWindowRepo = $this->getRepo('IrhpPermitWindow');

        // fetch the list of all open windows for selected countries
        $openWindows = $irhpPermitWindowRepo->fetchOpenWindowsByCountry(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            $command->getCountries(),
            new DateTime()
        );

        /* @var $irhpPermitApplicationRepo \Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication */
        $irhpPermitApplicationRepo = $this->getRepo('IrhpPermitApplication');

        // update the records
        $windowIdsToBeKept = [];

        /* @var $irhpPermitWindow IrhpPermitWindow */
        foreach ($openWindows as $irhpPermitWindow) {
            if (isset($existingIrhpPermitAppsByWindowId[$irhpPermitWindow->getId()])) {
                // the application already linked to the window - mark to be kept
                $windowIdsToBeKept[] = $irhpPermitWindow->getId();
            } else {
                // create new record if the application isn't linked to the window yet
                $irhpPermitApplication = $this->createIrhpPermitApplication($irhpApplication, $irhpPermitWindow);
                $irhpPermitApplicationRepo->saveOnFlush($irhpPermitApplication);
            }
        }

        // delete no longer needed IrhpPermitApplication records
        /* @var $existingIrhpPermitApplication IrhpPermitApplication */
        foreach ($existingIrhpPermitAppsByWindowId as $windowId => $existingIrhpPermitAppId) {
            if (!in_array($windowId, $windowIdsToBeKept)) {
                // delete IrhpPermitApplication if the window is not to be kept
                $irhpPermitApplicationRepo->deleteOnFlush(
                    $irhpPermitApplicationRepo->fetchById($existingIrhpPermitAppId)
                );
            }
        }

        // reset flags
        $irhpApplication->resetCheckAnswersAndDeclaration();
        $irhpApplicationRepo->saveOnFlush($irhpApplication);

        // save all the changes
        $irhpApplicationRepo->flushAll();

        $this->result->addId('irhpApplication', $irhpApplicationId);
        $this->result->addMessage('Countries updated for IRHP application');

        return $this->result;
    }

    /**
     * Create Irhp Permit Application for Irhp Application
     *
     * @param IrhpApplication  $irhpApplication  Irhp Application
     * @param IrhpPermitWindow $irhpPermitWindow Irhp Permit Window
     *
     * @return IrhpPermitApplication
     */
    protected function createIrhpPermitApplication(IrhpApplication $irhpApplication, IrhpPermitWindow $irhpPermitWindow)
    {
        return IrhpPermitApplication::createNewForIrhpApplication($irhpApplication, $irhpPermitWindow);
    }
}
