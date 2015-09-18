<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * UpdateAllCommunityLicences
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class UpdateAllCommunityLicences extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['CommunityLic'];

    protected $status; // override this

    public function handleCommand(CommandInterface $command)
    {
        /* @var $licence Licence */
        $licence = $this->getRepo()->fetchById($command->getId());

        $result = new \Dvsa\Olcs\Api\Domain\Command\Result();

        $status = $this->getRepo()->getRefdataReference($this->status);

        $count = 0;
        /* @var $communityLicence \Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic */
        foreach ($licence->getCommunityLics() as $communityLicence) {
            // only change status and expiry if not already expired
            if ($communityLicence->getExpiredDate() === null) {
                $communityLicence->changeStatusAndExpiryDate($status, new DateTime());
                $this->getRepo('CommunityLic')->save($communityLicence);
                $count++;
            }
        }

        $result->addMessage(
            sprintf(
                '%d Community licence(s) updated to %s',
                $count,
                $status->getDescription()
            )
        );

        $result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences::create(['id' => $licence->getId()])
            )
        );

        return $result;
    }
}
