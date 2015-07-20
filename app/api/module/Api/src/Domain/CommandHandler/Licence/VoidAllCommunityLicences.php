<?php

/**
 * VoidAllCommunityLicences
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * VoidAllCommunityLicences
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class VoidAllCommunityLicences extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $licence Licence */
        $licence = $this->getRepo()->fetchById($command->getId());

        $communityLicenceIds = [];
        /* @var $communityLicence  \Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic */
        foreach ($licence->getCommunityLics() as $communityLicence) {
            $communityLicenceIds[] = $communityLicence->getId();
        }

        $result = $this->handleSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\CommunityLic\Void::create(
                ['licence' => $licence->getId(), 'communityLicenceIds' => $communityLicenceIds]
            )
        );

        return $result;
    }
}
