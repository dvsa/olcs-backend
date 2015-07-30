<?php

/**
 * Update Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\HandleOcVariationFees;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\UpdateOperatingCentres as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper;

/**
 * Update Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateOperatingCentres extends AbstractCommandHandler implements TransactionedInterface
{
    const ERR_OC_PSV_SUM_1A = 'ERR_OC_PSV_SUM_1A'; // with large
    const ERR_OC_PSV_SUM_1B = 'ERR_OC_PSV_SUM_1B'; // without large
    const ERR_OC_TA_1 = 'ERR_OC_TA_1'; // select-traffic-area-error
    const ERR_OC_CL_1 = 'ERR_OC_CL_1'; //community-licences-too-many
    const ERR_OC_R_1 = 'ERR_OC_R_1'; // restricted-too-many
    const ERR_OC_V_1 = 'ERR_OC_V_1'; // 1-operating-centre
    const ERR_OC_V_2 = 'ERR_OC_V_2'; // too-low
    const ERR_OC_V_3 = 'ERR_OC_V_3'; // too-high
    const ERR_OC_V_4 = 'ERR_OC_V_4'; // no-operating-centre
    const ERR_OC_T_1 = 'ERR_OC_T_1'; // 1-operating-centre
    const ERR_OC_T_2 = 'ERR_OC_T_2'; // too-low
    const ERR_OC_T_3 = 'ERR_OC_T_3'; // too-high
    const ERR_OC_T_4 = 'ERR_OC_T_4'; // no-operating-centre

    protected $repoServiceName = 'Application';

    protected $extraRepos = ['ApplicationOperatingCentre'];

    /**
     * @var VariationOperatingCentreHelper
     */
    private $variationHelper;

    private $totals;

    private $messages = [];

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->variationHelper = $serviceLocator->getServiceLocator()->get('VariationOperatingCentreHelper');

        return parent::createService($serviceLocator);
    }

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->validate($application, $command);

        if ($application->isPsv()) {
            $application->setTotAuthSmallVehicles($command->getTotAuthSmallVehicles());
            $application->setTotAuthMediumVehicles($command->getTotAuthMediumVehicles());

            if ($application->canHaveLargeVehicles()) {
                $application->setTotAuthLargeVehicles($command->getTotAuthLargeVehicles());
            }
        } else {
            $application->setTotAuthTrailers($command->getTotAuthTrailers());
        }

        $application->setTotAuthVehicles($command->getTotAuthVehicles());

        // For new apps we are also potentially updating the TA, EA and community licences
        if ($application->isNew()) {

            if ($application->canHaveCommunityLicences()) {
                $application->setTotCommunityLicences($command->getTotCommunityLicences());
            }

            if ($command->getTrafficArea() !== null) {
                $data = [
                    'id' => $application->getLicence()->getId(),
                    'version' => $application->getLicence()->getVersion(),
                    'trafficArea' => $command->getTrafficArea()
                ];

                $this->result->merge($this->handleSideEffect(UpdateTrafficArea::create($data)));
            }

            if ($command->getEnforcementArea() !== null) {
                $application->getLicence()->setEnforcementArea(
                    $this->getRepo()->getReference(EnforcementArea::class, $command->getEnforcementArea())
                );
            }
        }

        $this->getRepo()->save($application);
        $this->result->addMessage('Application record updated');

        if ($application->isVariation()) {
            $this->result->merge(
                $this->handleSideEffect(HandleOcVariationFees::create(['id' => $application->getId()]))
            );
        }

        $data = ['id' => $application->getId(), 'section' => 'operatingCentres'];
        $this->result->merge($this->handleSideEffect(UpdateApplicationCompletionCmd::create($data)));

        return $this->result;
    }

    protected function validate(Application $application, Cmd $command)
    {
        // Check if we are missing the traffic area value, when it is applicable
        if ($application->isNew()
            && $command->getPartial()
            && $command->getPartialAction() == 'add'
            && $application->getLicence()->getTrafficArea() === null
        ) {

            $trafficArea = $command->getTrafficArea();

            if (empty($trafficArea) && $application->getOperatingCentres()->count() >= 1) {
                $this->addMessage('trafficArea', self::ERR_OC_TA_1);
            }
        }

        if (!$command->getPartial()) {
            if ($application->isPsv()) {
                $this->validatePsv($application, $command);
            } else {
                $this->validateTotalAuthTrailers($application, $command);
            }

            if ($application->canHaveCommunityLicences()
                && $command->getTotCommunityLicences() > $command->getTotAuthVehicles()
            ) {
                $this->addMessage('totCommunityLicences', self::ERR_OC_CL_1);
            }

            $this->validateTotalAuthVehicles($application, $command);
        }

        if (!empty($this->messages)) {
            throw new ValidationException($this->messages);
        }
    }

    protected function validateTotalAuthVehicles(Application $application, Cmd $command)
    {
        if ($application->isRestricted() && $command->getTotAuthVehicles() > 2) {
            $this->addMessage('totAuthVehicles', self::ERR_OC_R_1);
        }

        $totals = $this->getTotals($application);

        if ($totals['noOfOperatingCentres'] === 0) {
            $this->addMessage('totAuthVehicles', self::ERR_OC_V_4);
        }

        if ($totals['noOfOperatingCentres'] === 1 && $command->getTotAuthVehicles() != $totals['minVehicleAuth']) {
            $this->addMessage('totAuthVehicles', self::ERR_OC_V_1);
        }

        if ($totals['noOfOperatingCentres'] >= 2) {

            if ($command->getTotAuthVehicles() < $totals['minVehicleAuth']) {
                $this->addMessage('totAuthVehicles', self::ERR_OC_V_2);
            }

            if ($command->getTotAuthVehicles() > $totals['maxVehicleAuth']) {
                $this->addMessage('totAuthVehicles', self::ERR_OC_V_3);
            }
        }
    }

    protected function validateTotalAuthTrailers(Application $application, Cmd $command)
    {
        $totals = $this->getTotals($application);

        if ($totals['noOfOperatingCentres'] === 0) {
            $this->addMessage('totAuthTrailers', self::ERR_OC_T_4);
        }

        if ($totals['noOfOperatingCentres'] === 1 && $command->getTotAuthTrailers() != $totals['minTrailerAuth']) {
            $this->addMessage('totAuthTrailers', self::ERR_OC_T_1);
        }

        if ($totals['noOfOperatingCentres'] >= 2) {

            if ($command->getTotAuthTrailers() < $totals['minTrailerAuth']) {
                $this->addMessage('totAuthTrailers', self::ERR_OC_T_2);
            }

            if ($command->getTotAuthTrailers() > $totals['maxTrailerAuth']) {
                $this->addMessage('totAuthTrailers', self::ERR_OC_T_3);
            }
        }

        return [];
    }

    protected function validatePsv(Application $application, Cmd $command)
    {
        $sum = (int)$command->getTotAuthSmallVehicles()
            + (int)$command->getTotAuthMediumVehicles()
            + (int)$command->getTotAuthLargeVehicles();

        if ($sum != $command->getTotAuthVehicles()) {
            if ($application->canHaveLargeVehicles()) {
                $message = self::ERR_OC_PSV_SUM_1A;
            } else {
                $message = self::ERR_OC_PSV_SUM_1B;
            }
            $this->addMessage('totAuthVehicles', $message);
        }
    }

    protected function getTotals(Application $application)
    {
        if ($this->totals !== null) {
            return $this->totals;
        }

        if ($application->isVariation()) {
            $aocs = $this->variationHelper->getListDataForApplication($application);
        } else {
            $aocs = $this->getRepo('ApplicationOperatingCentre')
                ->fetchByApplicationIdForOperatingCentres($application->getId());
        }

        $this->totals['noOfOperatingCentres'] = 0;
        $this->totals['minVehicleAuth'] = 0;
        $this->totals['maxVehicleAuth'] = 0;
        $this->totals['minTrailerAuth'] = 0;
        $this->totals['maxTrailerAuth'] = 0;

        foreach ($aocs as $aoc) {

            if ($application->isVariation()
                && in_array($aoc['action'], ['D', 'C'])) {
                continue;
            }

            $this->totals['noOfOperatingCentres']++;

            $this->totals['minVehicleAuth'] = max([$this->totals['minVehicleAuth'], $aoc['noOfVehiclesRequired']]);
            $this->totals['minTrailerAuth'] = max([$this->totals['minTrailerAuth'], $aoc['noOfTrailersRequired']]);

            $this->totals['maxVehicleAuth'] += (int)$aoc['noOfVehiclesRequired'];
            $this->totals['maxTrailerAuth'] += (int)$aoc['noOfTrailersRequired'];
        }

        return $this->totals;
    }

    protected function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }
}
