<?php

/**
 * Suspend a licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences as ReturnComLics;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\LicenceStatusRule\RemoveLicenceStatusRulesForLicence;

/**
 * Suspend a licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Suspend extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $licence Licence */
        $licence = $this->getRepo()->fetchUsingId($command);
        $licence->setStatus($this->getRepo()->getRefdataReference(Licence::LICENCE_STATUS_SUSPENDED));
        $licence->setSuspendedDate(new \DateTime());

        $this->getRepo()->save($licence);

        $result = new Result();

        if ($command->getDeleteLicenceStatusRules()) {
            $result->merge(
                $this->handleSideEffect(
                    RemoveLicenceStatusRulesForLicence::create(
                        [
                        'licence' => $licence
                        ]
                    )
                )
            );
        }

        $communityLicences = $licence->getCommunityLics()->toArray();
        if (!empty($communityLicences)) {
            $result->merge(
                $this->handleSideEffect(
                    ReturnComLics::create(
                        [
                            'id' => $licence->getId(),
                        ]
                    )
                )
            );
        }

        $result->addMessage("Licence ID {$licence->getId()} suspended");

        return $result;
    }
}
