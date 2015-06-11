<?php

/**
 * Create Community Licences / Application Version
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch as GenerateBatchCommand;
use Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences as UpdateTotalCommunityLicencesCommand;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\Application\CreateOfficeCopy as CreateOfficeCopyCommand;

/**
 * Create Community Licences / Application Version
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'CommunityLic';

    protected $extraRepos = ['Licence', 'Application'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $licenceId = $command->getLicence();
        $totalLicences = $command->getTotalLicences();
        $identifier = $command->getIdentifier();

        $interimStatus = $this->getRepo('Application')->getInterimStatus($identifier);

        if ($interimStatus !== ApplicationEntity::INTERIM_STATUS_INFORCE) {
            $data = [
                'status' => $this->getRepo()->getRefdataReference(CommunityLicEntity::STATUS_PENDING),
            ];
        } else {
            $data = [
                'status' => $this->getRepo()->getRefdataReference(CommunityLicEntity::STATUS_ACTIVE),
                'specifiedDate' => new \DateTime()
                // @TODO: need such a helper on backend
                // $this->getCommandHandler()->getServiceLocator()->get('Helper\Date')->getDate()
            ];
        }

        $validLicences = $this->getRepo()
            ->fetchValidLicences($command->getLicence());
        $startIssueNo = count($validLicences) ?
            $validLicences[count($validLicences) - 1]->getIssueNo() + 1 : 1;

        $data['serialNoPrefix'] = $this->getRepo('Licence')->getSerialNoPrefixFromTrafficArea($licenceId);
        $data['licence'] = $this->getRepo()->getReference(LicenceEntity::class, $licenceId);

        $ids = [];
        for ($i = 1; $i <= $totalLicences; $i++) {
            $data['issueNo'] = $startIssueNo++;

            $communityLic = $this->createCommunityLicObject($data);
            $this->getRepo()->save($communityLic);
            $result->addId('communityLic' . $communityLic->getId(), $communityLic->getId());
            $result->addMessage('Community licence created successfully');
            $ids[] = $communityLic->getId();
        }

        $sideEffects = $this->determineSideEffects($interimStatus, $licenceId, $identifier, $ids);
        foreach ($sideEffects as $sideEffect) {
            $result->merge($this->getCommandHandler()->handleCommand($sideEffect));
        }

        return $result;
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

    private function determineSideEffects($interimStatus, $licenceId, $applicationId, $communityLicenceIds)
    {
        $sideEffects = [];

        if ($interimStatus === ApplicationEntity::INTERIM_STATUS_INFORCE) {
            $sideEffects[] = $this->createGenerateBatchCommand($licenceId, $applicationId, $communityLicenceIds);
        }

        $sideEffects[] = $this->createUpdateTotalCommunityLicencesCommand($licenceId);
        if (!$this->getRepo()->fetchOfficeCopy($licenceId)) {
            $sideEffects[] = $this->createCreateOfficeCopyCommand($licenceId, $applicationId);
        }

        return $sideEffects;
    }

    private function createGenerateBatchCommand($licenceId, $applicationId, $communityLicenceIds)
    {
        return GenerateBatchCommand::create(
            [
                'licence' => $licenceId,
                'application' => $applicationId,
                'communityLicenceIds' => $communityLicenceIds
            ]
        );
    }

    private function createUpdateTotalCommunityLicencesCommand($licenceId)
    {
        return UpdateTotalCommunityLicencesCommand::create(
            [
                'id' => $licenceId
            ]
        );
    }

    private function createCreateOfficeCopyCommand($licenceId, $identifier)
    {
        return CreateOfficeCopyCommand::create(
            [
                'licence' => $licenceId,
                'identifier' => $identifier,
            ]
        );
    }
}
