<?php

/**
 * Update Total Community Licences
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Update Total Community Licences
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class UpdateTotalCommunityLicences extends AbstractCommandHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['CommunityLic'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $totalCommunityLic = count($this->getRepo('CommunityLic')->fetchValidLicences($command->getId()));

        $licence->updateTotalCommunityLicences($totalCommunityLic);
        $this->getRepo()->save($licence);

        $result->addMessage('Total community licences count updated');
        return $result;
    }
}
