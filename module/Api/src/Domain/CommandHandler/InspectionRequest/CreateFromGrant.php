<?php

/**
 * Inspection Request / Create From Grant
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
use Dvsa\Olcs\Transfer\Command\InspectionRequest\CreateFromGrant as Cmd;

/**
 * Inspection Request / Create From Grant
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateFromGrant extends AbstractCommandHandler
{
    protected $repoServiceName = 'InspectionRequest';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        
        $inspectionRequest = $this->createInspectionRequestObject($command);
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
     * @param Cmd $command
     * @return InspectionRequest
     */
    private function createInspectionRequestObject($command)
    {
        $inspectionRequest = new InspectionRequestEntity();
        
        $today = new DateTime('now');
        $inspectionRequest->updateInspectionRequest(
            InspectionRequestEntityService::REQUEST_TYPE_NEW_OP,
            $today,
            $today->add(new \DateInterval('P' . $command->getDueDate() . 'M')),
            InspectionRequestEntityService::RESULT_TYPE_NEW,
            $command->getCaseworkerNotes(),
            InspectionRequestEntityService::REPORT_TYPE_MAINTENANCE_REQUEST,
            $operatingCentreId
        );
        return $inspectionRequest;
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
