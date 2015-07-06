<?php

/**
 * Change Of Entity
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ChangeOfEntity;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Organisation\ChangeOfEntity as ChangeOfEntityEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Change Of Entity
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class ChangeOfEntity extends AbstractCommandHandler
{
    protected $repoServiceName = 'ChangeOfEntity';

    protected $extraRepos = ['Application'];

    public function handleCommand(CommandInterface $command)
    {
        if ($command->getId()) {
            /* @var $changeOfEntity ChangeOfEntityEntity */
            $changeOfEntity = $this->getRepo()->fetchUsingId($command);
            $messageAction = 'Updated';
        } else {
            $changeOfEntity = new ChangeOfEntityEntity();
            $application = $this->getRepo('Application')->fetchById($command->getApplicationId());
            $changeOfEntity->setLicence($application->getLicence());
            $messageAction = 'Created';
        }

        $changeOfEntity
            ->setOldLicenceNo($command->getOldLicenceNo())
            ->setOldOrganisationName($command->getOldOrganisationName());

        $this->getRepo()->save($changeOfEntity);

        $result = new Result();
        $result->addId("changeOfEntity", $changeOfEntity->getId());
        $result->addMessage("ChangeOfEntity " . $messageAction);

        return $result;
    }
}
