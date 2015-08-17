<?php

/**
 * Create PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Create as Command;
use Dvsa\Olcs\Api\Entity\ContactDetails;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Create PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'PrivateHireLicence';
    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */
        $result = new Result();

        $address = new \Dvsa\Olcs\Api\Entity\ContactDetails\Address();
        $address->updateAddress(
            $command->getAddress()['addressLine1'],
            $command->getAddress()['addressLine2'],
            $command->getAddress()['addressLine3'],
            $command->getAddress()['addressLine4'],
            $command->getAddress()['town'],
            $command->getAddress()['postcode'],
            $this->getRepo()->getReference(ContactDetails\Country::class, $command->getAddress()['countryCode'])
        );

        $cd = new ContactDetails\ContactDetails(
            $this->getRepo()->getRefdataReference(ContactDetails\ContactDetails::CONTACT_TYPE_HACKNEY)
        );
        $cd->setDescription($command->getCouncilName())
            ->setAddress($address);

        $phl = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl->setLicence($this->getRepo()->getReference(Licence::class, $command->getLicence()))
            ->setPrivateHireLicenceNo($command->getPrivateHireLicenceNo())
            ->setContactDetails($cd);

        $this->getRepo('ContactDetails')->save($cd);
        $this->getRepo()->save($phl);

        $result->addId('address', $address->getId());
        $result->addId('contactDetails', $cd->getId());
        $result->addId('privateHireLicence', $phl->getId());
        $result->addMessage('PrivateHireLicence created');

        if ($this->isGranted(Permission::SELFSERVE_USER) &&
            ($command->getLva() === 'licence')) {
            $data = [
                'licence' => $command->getLicence(),
                'category' => CategoryEntity::CATEGORY_APPLICATION,
                'subCategory' => CategoryEntity::TASK_SUB_CATEGORY_CHANGE_TO_TAXI_PHV_DIGITAL,
                'description' => 'Taxi licence added - ' . $phl->getPrivateHireLicenceNo(),
                'isClosed' => 0,
                'urgent' => 0
            ];
            $result->merge($this->handleSideEffect(CreateTaskCmd::create($data)));
        }

        return $result;
    }
}
