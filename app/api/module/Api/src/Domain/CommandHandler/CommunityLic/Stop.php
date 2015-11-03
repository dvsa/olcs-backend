<?php

/**
 * Stop community licences
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawal as CommunityLicWithdrawalEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicSuspension as CommunityLicSuspensionEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawalReason as CommunityLicWithdrawalReasonEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicSuspensionReason as CommunityLicSuspensionReasonEntity;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Stop as Cmd;

/**
 * Stop community licences
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Stop extends AbstractCommandHandler implements TransactionedInterface
{
    const STOP_TYPE_WITHDRAWN = 'withdrawal';

    protected $repoServiceName = 'CommunityLic';

    protected $extraRepos = [
        'CommunityLicSuspension',
        'CommunityLicSuspensionReason',
        'CommunityLicWithdrawal',
        'CommunityLicWithdrawalReason',
        'Licence'
    ];

    /**
     * @param Cmd $command
     * @return Result
     * @throws ValidationException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $ids = $command->getCommunityLicenceIds();
        $licenceId = $command->getLicence();
        $type = $command->getType();
        $startDate = $command->getStartDate();
        $endDate = $command->getEndDate();
        $reasons = $command->getReasons();

        $this->validateLicences($ids, $licenceId);
        $licences = $this->getRepo()->fetchLicencesByIds($ids);

        $result = new Result();
        foreach ($licences as $communityLicence) {
            $id = $communityLicence->getId();
            if ($type == self::STOP_TYPE_WITHDRAWN) {
                $communityLicence->changeStatusAndExpiryDate(
                    $this->getRepo()->getRefdataReference(CommunityLicEntity::STATUS_WITHDRAWN),
                    new DateTime('now')
                );
                $result->addMessage("The licence {$id} have been withdrawn");
            } else {
                $communityLicence->changeStatusAndExpiryDate(
                    $this->getRepo()->getRefdataReference(CommunityLicEntity::STATUS_SUSPENDED)
                );
                $result->addMessage("The licence {$id} have been suspended");
            }
            $result->addId('communityLic' . $id, $id);
            $this->getRepo()->save($communityLicence);
        }

        if ($type == 'withdrawal') {
            $this->createWithrawalAndReasons($licences, $reasons);
        } else {
            $this->createSuspensionAndReasons($licences, $reasons, $startDate, $endDate);
        }

        if ($command->getApplication()) {
            $result->merge(
                $this->handleSideEffect(
                    UpdateApplicationCompletion::create(
                        [
                            'id' => $command->getApplication(),
                            'section' => 'communityLicences'
                        ]
                    )
                )
            );
        }

        return $result;
    }

    protected function createWithrawalAndReasons($communityLics, $reasons)
    {
        foreach ($communityLics as $licence) {
            $withdrawal = new CommunityLicWithdrawalEntity();
            $withdrawal->updateCommunityLicWithdrawal($licence);
            $this->getRepo('CommunityLicWithdrawal')->save($withdrawal);
            foreach ($reasons as $reason) {
                $withdrawalReason = new CommunityLicWithdrawalReasonEntity();
                $withdrawalReason->updateReason(
                    $this->getRepo()->getReference(CommunityLicWithdrawalEntity::class, $withdrawal->getId()),
                    $this->getRepo()->getRefdataReference($reason)
                );
                $this->getRepo('CommunityLicWithdrawalReason')->save($withdrawalReason);
            }
        }
    }

    protected function createSuspensionAndReasons($communityLics, $reasons, $startDate, $endDate)
    {
        foreach ($communityLics as $licence) {
            $suspension = new CommunityLicSuspensionEntity();
            $suspension->updateCommunityLicSuspension($licence, $startDate, $endDate);
            $this->getRepo('CommunityLicSuspension')->save($suspension);
            foreach ($reasons as $reason) {
                $suspensionReason = new CommunityLicSuspensionReasonEntity();
                $suspensionReason->updateReason(
                    $this->getRepo()->getReference(CommunityLicSuspensionEntity::class, $suspension->getId()),
                    $this->getRepo()->getRefdataReference($reason)
                );
                $this->getRepo('CommunityLicSuspensionReason')->save($suspensionReason);
            }
        }
    }

    protected function validateLicences($ids, $licenceId)
    {
        $licence = $this->getRepo('Licence')->fetchById($licenceId);
        if ($licence->hasCommunityLicenceOfficeCopy($ids)) {
            $validLicences = $this->getRepo()->fetchValidLicences($licenceId);
            foreach ($validLicences as $communityLicence) {
                $status = $communityLicence->getStatus()->getId();
                if (
                        ($status == CommunityLicEntity::STATUS_PENDING) ||
                        (
                           $status == CommunityLicEntity::STATUS_ACTIVE &&
                           !in_array($communityLicence->getId(), $ids)
                        )
                    ) {
                        throw new ValidationException(
                            [
                                'communityLicence' => [
                                    CommunityLicEntity::ERROR_CANT_STOP =>
                                        'Please annul, withdraw or suspend the other pending/active ' .
                                        'licences before the office copy'
                                ]
                            ]
                        );
                }
            }
        }
    }
}
