<?php

/**
 * Inspection Request / Create
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\InspectionRequest;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Inspection\InspectionRequest as InspectionRequestEntity;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Inspection Request / Create
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'InspectionRequest';

    public function handleCommand(CommandInterface $command)
    {
        die('create');
        $result = new Result();
        /*
        $licenceId = $command->getLicence();

        $this->validateOfficeCopy($licenceId);

        $data = [
            'status' => $this->getRepo()->getRefdataReference(CommunityLicEntity::STATUS_ACTIVE),
            'specifiedDate' => new DateTime('now')
        ];

        $licence = $this->getRepo('Licence')->fetchById($licenceId);
        $data['serialNoPrefix'] = $licence->getSerialNoPrefixFromTrafficArea();
        $data['licence'] = $this->getRepo()->getReference(LicenceEntity::class, $licenceId);
        $data['issueNo'] = 0;

        $communityLic = $this->createCommunityLicObject($data);
        $this->getRepo()->save($communityLic);
        $result->addId('communityLic' . $communityLic->getId(), $communityLic->getId());
        $result->addMessage('Office copy created successfully');

        $sideEffects = $this->determineSideEffects($licenceId, $communityLic->getId());
        foreach ($sideEffects as $sideEffect) {
            $result->merge($this->getCommandHandler()->handleCommand($sideEffect));
        }
        */
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

    private function determineSideEffects($licenceId, $communityLicenceId)
    {
        $sideEffects = [];
        $sideEffects[] = $this->createGenerateBatchCommand($licenceId, [$communityLicenceId]);

        return $sideEffects;
    }

    private function createGenerateBatchCommand($licenceId, $communityLicenceIds)
    {
        return GenerateBatchCommand::create(
            [
                'licence' => $licenceId,
                'communityLicenceIds' => $communityLicenceIds
            ]
        );
    }

    private function validateOfficeCopy($licenceId)
    {
        if ($this->getRepo()->fetchOfficeCopy($licenceId)) {
            throw new ValidationException(
                [
                    'officeCopy' => [
                        CommunityLicEntity::ERROR_OFFICE_COPY_EXISTS => 'Office copy already exists'
                    ]
                ]
            );
        }
    }
}
