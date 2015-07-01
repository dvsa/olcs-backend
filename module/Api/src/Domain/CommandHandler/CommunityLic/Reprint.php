<?php

/**
 * Reprint community licences
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Void as VoidCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch as GenerateBatchCommand;

/**
 * Reprint community licences
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Reprint extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'CommunityLic';

    protected $extraRepos = ['Licence'];

    public function handleCommand(CommandInterface $command)
    {
        $ids = $command->getCommunityLicenceIds();
        $licenceId = $command->getLicence();

        $this->validateLicences($ids, $licenceId);
        $voidLicencesCommand = VoidCmd::create(
            [
                'licence' => $licenceId,
                'communityLicenceIds' => $ids,
                'checkOfficeCopy' => false
            ]
        );
        $this->handleSideEffect($voidLicencesCommand);
        $issueNumbers = $this->getIssueNumbersByIds($ids);

        $licence = $this->getRepo('Licence')->fetchById($licenceId);

        $data = [
            'status' => $this->getRepo()->getRefdataReference(CommunityLicEntity::STATUS_ACTIVE),
            'specifiedDate' => new DateTime('now'),
            'serialNoPrefix' => $licence->getSerialNoPrefixFromTrafficArea(),
            'licence' => $this->getRepo()->getReference(LicenceEntity::class, $licenceId)
        ];
        $result = new Result();
        foreach ($issueNumbers as $issueNumber) {
            $data['issueNo'] = $issueNumber;
            $communityLic = $this->createCommunityLicObject($data);
            $this->getRepo()->save($communityLic);
            $result->addId('communityLic' . $communityLic->getId(), $communityLic->getId());
            $result->addMessage("The selected licence with issue number {$issueNumber} has been generated");
        }

        $generateBatchCmd = GenerateBatchCommand::create(
            [
                'licence' => $licenceId,
                'communityLicenceIds' => $ids
            ]
        );
        $result->merge($this->handleSideEffect($generateBatchCmd));

        return $result;
    }

    protected function validateLicences($ids, $licenceId)
    {
        $activeLicences = $this->getRepo()->fetchActiveLicences($licenceId);
        $activeIds = [];
        foreach ($activeLicences as $activeLicence) {
            $activeIds[] = $activeLicence->getId();
        }
        $notActive = array_diff($ids, $activeIds);
        if (!empty($notActive)) {
            throw new ValidationException(
                [
                    'communityLicence' => [
                        CommunityLicEntity::ERROR_CANT_REPRINT =>
                            'You can only reprint \'Active\' community licences'
                    ]
                ]
            );
        }
    }

    protected function getIssueNumbersByIds($ids)
    {
        $issueNumbers = [];
        $licences = $this->getRepo()->fetchLicencesByIds($ids);
        foreach ($licences as $licence) {
            $issueNumbers[] = $licence->getIssueNo();
        }
        return $issueNumbers;
    }

    /**
     * @param array $data
     * @return CommunityLic
     */
    private function createCommunityLicObject($data)
    {
        $communityLic = new CommunityLicEntity();
        $communityLic->updateCommunityLic($data);
        return $communityLic;
    }
}
