<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch as GenerateBatchCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences as UpdateTotalCommunityLicencesCommand;

/**
 * Reprint community licences
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Reprint extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'CommunityLic';
    protected $extraRepos = ['Licence'];

    /**
     * Handle Command
     *
     * @param \Dvsa\Olcs\Transfer\Command\CommunityLic\Reprint $command Command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $ids = $command->getCommunityLicenceIds();
        $licenceId = $command->getLicence();

        $this->validateLicences($ids, $licenceId);
        $voidLicencesCommand = TransferCmd\CommunityLic\Annul::create(
            [
                'licence' => $licenceId,
                'communityLicenceIds' => $ids,
                'checkOfficeCopy' => false
            ]
        );
        $this->handleSideEffect($voidLicencesCommand);
        $issueNumbers = $this->getIssueNumbersByIds($ids);

        /** @var LicenceEntity $licence */
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
                'isReprint' => true,
                'licence' => $licenceId,
                'communityLicenceIds' => $ids,
                'identifier' => $command->getApplication(),
                'user' => $command->getUser(),
            ]
        );
        $result->merge($this->handleSideEffect($generateBatchCmd));
        $result->merge($this->handleSideEffect(UpdateTotalCommunityLicencesCommand::create(['id' => $licenceId])));

        return $result;
    }

    /**
     * Validate that Community Licences can be reprinted
     *
     * @param array $ids       List of Community Licence IDs
     * @param int   $licenceId Licence ID
     *
     * @throws ValidationException
     * @return void
     */
    protected function validateLicences($ids, $licenceId)
    {
        $activeLicences = $this->getRepo()->fetchActiveLicences($licenceId);
        $activeIds = [];

        /** @var LicenceEntity $activeLicence */
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

    /**
     * Get Issue Numbers By Ids
     *
     * @param array $ids Licences Ids
     *
     * @return array
     */
    protected function getIssueNumbersByIds($ids)
    {
        $issueNumbers = [];
        $licences = $this->getRepo()->fetchLicencesByIds($ids);

        /** @var CommunityLicEntity $licence */
        foreach ($licences as $licence) {
            $issueNumbers[] = $licence->getIssueNo();
        }
        return $issueNumbers;
    }

    /**
     * Create a Community Licence entity with some initial data
     *
     * @param array $data Community Licence data
     *
     * @return CommunityLicEntity
     */
    private function createCommunityLicObject($data)
    {
        $communityLic = new CommunityLicEntity();
        $communityLic->updateCommunityLic($data);
        return $communityLic;
    }
}
