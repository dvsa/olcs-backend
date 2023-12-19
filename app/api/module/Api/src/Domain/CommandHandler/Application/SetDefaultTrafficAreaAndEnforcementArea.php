<?php

/**
 * Set Default Traffic Area And Enforcement Area
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Address\Service\AddressServiceAwareInterface;
use Dvsa\Olcs\Address\Service\AddressServiceAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Application\SetDefaultTrafficAreaAndEnforcementArea as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea;

/**
 * Set Default Traffic Area And Enforcement Area
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class SetDefaultTrafficAreaAndEnforcementArea extends AbstractCommandHandler implements
    TransactionedInterface,
    AddressServiceAwareInterface
{
    use AddressServiceAwareTrait;

    protected $repoServiceName = 'Application';

    protected $extraRepos = ['OperatingCentre', 'AdminAreaTrafficArea', 'PostcodeEnforcementArea'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $setEa = $setTa = false;

        if ($application->getLicence()->getTrafficArea() === null) {
            $setTa = true;
        }

        if ($application->getLicence()->getEnforcementArea() === null) {
            $setEa = true;
        }

        if ($setEa === false && $setTa === false) {
            return $this->result;
        }

        if ($application->getNiFlag() === 'Y') {
            if ($setTa) {
                $this->updateTrafficArea($application, TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
            }

            if ($setEa) {
                $application->getLicence()->setEnforcementArea(
                    $this->getRepo()->getReference(
                        EnforcementArea::class,
                        EnforcementArea::NORTHERN_IRELAND_ENFORCEMENT_AREA_CODE
                    )
                );

                $this->result->addMessage('Enforcement area updated');
                $this->getRepo()->save($application);
            }

            return $this->result;
        }

        // use a postcode, if provided
        $postcode = $command->getPostcode();

        if (empty($postcode)) {
            // otherwise, try to get a postcode from an operating centre
            if ($application->getOperatingCentres()->count() !== 1) {
                return $this->result;
            }

            $operatingCentre = $this->getRepo('OperatingCentre')->fetchById($command->getOperatingCentre());

            $postcode = $operatingCentre->getAddress()->getPostcode();
        }

        if (!empty($postcode)) {
            if ($setTa) {
                try {
                    $trafficArea = $this->getAddressService()
                        ->fetchTrafficAreaByPostcode($postcode, $this->getRepo('AdminAreaTrafficArea'));
                } catch (\Exception $e) {
                    // Address service is down, just continue without saving
                    return $this->result;
                }

                if ($trafficArea !== null) {
                    $this->updateTrafficArea($application, $trafficArea->getId());
                }
            }

            if ($setEa) {
                try {
                    $enforcementArea = $this->getAddressService()
                        ->fetchEnforcementAreaByPostcode($postcode, $this->getRepo('PostcodeEnforcementArea'));
                } catch (\Exception $e) {
                    // Address service is down, just continue without saving
                    return $this->result;
                }
                $application->getLicence()->setEnforcementArea($enforcementArea);

                $this->result->addMessage('Enforcement area updated');
                $this->getRepo()->save($application);
            }
        }

        return $this->result;
    }

    protected function updateTrafficArea(Application $application, $trafficArea)
    {
        $data = [
            'id' => $application->getLicence()->getId(),
            'version' => $application->getLicence()->getVersion(),
            'trafficArea' => $trafficArea
        ];

        $this->result->merge($this->handleSideEffect(UpdateTrafficArea::create($data)));
    }
}
