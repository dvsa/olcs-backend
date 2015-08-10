<?php

/**
 * Update PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\Command\Result;
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

/**
 * Update PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'PrivateHireLicence';

    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */

        /* @var $phl PrivateHireLicence */
        $phl = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
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

        $result = new Result();
        $result->addId('privateHireLicence', $phl->getId());
        $result->addMessage("PrivateHireLicence ID {$phl->getId()} updated");

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
            $result->merge($this->handleSideEffect(CreateTaskCmd::create($data)));
        }
        return $result;
    }
}
