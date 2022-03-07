<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\SetDefaultTrafficAreaAndEnforcementArea;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Service\TrafficAreaValidator;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Application Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class UpdateAddresses extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * @var TrafficAreaValidator
     */
    private $trafficAreaValidator;

    /**
     * Create the service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->trafficAreaValidator = $serviceLocator->getServiceLocator()->get('TrafficAreaValidator');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param CommandInterface $command
     *
     * @return void
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var \Dvsa\Olcs\Api\Entity\Application\Application $application */
        $application = $this->getRepo()->fetchUsingId($command);

        /** @var \Dvsa\Olcs\Api\Entity\Licence\Licence $licence */
        $licence = $application->getLicence();

        $params = $command->getArrayCopy();
        $params['id'] = $licence->getId();

        $result = $this->handleSideEffect(
            SaveAddresses::create($params)
        );

        if ($application->isNew() && RefData::APP_VEHICLE_TYPE_LGV === (string)$application->getVehicleType()) {
            // set default Traffic Area and Enforcement Area
            // use establishment address if provided, otherwise use correspondence address
            $address = $this->isAddressPopulated($params['establishmentAddress'])
                ? $params['establishmentAddress']
                : $params['correspondenceAddress'];

            $postcode = $address['postcode'];

            $this->trafficAreaValidator->validateTrafficAreaWithPostcode($application, $postcode);

            // reset Traffic Area and Enforcement Area
            $licence->setTrafficArea(null);
            $licence->setEnforcementArea(null);
            $this->getRepo()->save($application);

            $data = ['id' => $application->getId(), 'postcode' => $postcode];
            $result->merge(
                $this->handleSideEffect(
                    SetDefaultTrafficAreaAndEnforcementArea::create($data)
                )
            );
        }

        $result->merge(
            $this->handleSideEffect(
                UpdateApplicationCompletionCommand::create(
                    ['id' => $application->getId(), 'section' => 'addresses']
                )
            )
        );

        return $result;
    }

    /**
     * Is address populated
     *
     * @param array $address
     *
     * @return bool
     */
    private function isAddressPopulated(array $address): bool
    {
        return !empty($address['postcode']) || !empty($address['town']) || !empty($address['addressLine1'])
            || !empty($address['addressLine2']) || !empty($address['addressLine3']) || !empty($address['addressLine4']);
    }
}
