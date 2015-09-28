<?php

/**
 * Update PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Update as Command;
use Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence;
use Dvsa\Olcs\Api\Entity\ContactDetails;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Address\Service\AddressServiceAwareInterface;
use Dvsa\Olcs\Address\Service\AddressServiceAwareTrait;

/**
 * Update PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface,
    AddressServiceAwareInterface
{
    use AuthAwareTrait;
    use AddressServiceAwareTrait;

    const PHL_INVALID_TA = 'PHL_INVALID_TA';

    protected $repoServiceName = 'PrivateHireLicence';

    protected $extraRepos = ['ContactDetails', 'AdminAreaTrafficArea'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */

        /* @var $phl PrivateHireLicence */
        $phl = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->checkTrafficArea($command->getAddress()['postcode'], $phl);

        $phl->setPrivateHireLicenceNo($command->getPrivateHireLicenceNo());

        $cd = $phl->getContactDetails();
        $cd->setDescription($command->getCouncilName());

        $address = $cd->getAddress();
        $address->updateAddress(
            $command->getAddress()['addressLine1'],
            $command->getAddress()['addressLine2'],
            $command->getAddress()['addressLine3'],
            $command->getAddress()['addressLine4'],
            $command->getAddress()['town'],
            $command->getAddress()['postcode'],
            $this->getRepo()->getReference(ContactDetails\Country::class, $command->getAddress()['countryCode'])
        );

        $this->getRepo()->save($phl);
        $this->getRepo('ContactDetails')->save($cd);

        $this->result->addId('privateHireLicence', $phl->getId());
        $this->result->addMessage("PrivateHireLicence ID {$phl->getId()} updated");

        if ($this->isGranted(Permission::SELFSERVE_USER) &&
            ($command->getLva() === 'licence')) {
            $data = [
                'licence' => $command->getLicence(),
                'category' => CategoryEntity::CATEGORY_APPLICATION,
                'subCategory' => CategoryEntity::TASK_SUB_CATEGORY_CHANGE_TO_TAXI_PHV_DIGITAL,
                'description' => 'Taxi licence updated - ' . $phl->getPrivateHireLicenceNo(),
                'isClosed' => 0,
                'urgent' => 0
            ];
            $this->result->merge($this->handleSideEffect(CreateTaskCmd::create($data)));
        }

        return $this->result;
    }

    /**
     * Check and possible update the licence traffic area
     *
     * @param string $postcode
     * @param \Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence\PrivateHireLicence $phl
     * @throws \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    protected function checkTrafficArea($postcode, PrivateHireLicence $phl)
    {
        $postCodeTrafficArea = $this->getAddressService()->fetchTrafficAreaByPostcode(
            $postcode,
            $this->getRepo('AdminAreaTrafficArea')
        );

        if ($postCodeTrafficArea) {
            // if TA not set or only one PrivateHireLicence is on the licence
            if ($phl->getLicence()->getTrafficArea() === null ||
                $phl->getLicence()->getPrivateHireLicences()->count() <= 1
            ) {
                // update the licence TA
                $data = [
                    'id' => $phl->getLicence()->getId(),
                    'version' => $phl->getLicence()->getVersion(),
                    'trafficArea' => $postCodeTrafficArea->getId(),
                ];
                $this->result->merge(
                    $this->handleSideEffect(\Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea::create($data))
                );
            } else {
                // check that the updated PHL's postcode is in the TA
                if ($phl->getLicence()->getTrafficArea() !== $postCodeTrafficArea) {
                    throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(
                        [self::PHL_INVALID_TA => 'Postcode traffic area is not same traffic area as licence']
                    );
                }
            }
        }
    }
}
