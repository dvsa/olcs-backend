<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * ReturnAllCommunityLicences
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class ReturnAllCommunityLicences extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['CommunityLic'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $licence Licence */
        $licence = $this->getRepo()->fetchById($command->getId());

        $result = new \Dvsa\Olcs\Api\Domain\Command\Result();

        $result->addMessage($licence->getCommunityLics()->count() .' Community licence returned');

        /* @var $communityLicence  \Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic */
        foreach ($licence->getCommunityLics() as $communityLicence) {
            $communityLicence->changeStatusAndExpiryDate(
                $this->getRepo()->getRefdataReference(CommunityLic::STATUS_RETURNDED),
                new DateTime()
            );
            $this->getRepo('CommunityLic')->save($communityLicence);
        }

        $result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences::create(['id' => $licence->getId()])
            )
        );

        return $result;
    }
}
