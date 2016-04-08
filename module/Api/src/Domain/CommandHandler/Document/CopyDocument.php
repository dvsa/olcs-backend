<?php

/**
 * Copy document
 * 
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific as CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Transfer\Command\Document\CopyDocument as CopyDocumentCmd;

/**
 * Copy document
 * 
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CopyDocument extends AbstractCommandHandler implements TransactionedInterface
{
    const APP = 'application';
    const LIC = 'licence';
    const BUSREG = 'busReg';
    const CASES = 'case';
    const IRFO = 'irfoOrganisation';
    const TM = 'transportManager';
    const PUBLICATION = 'publication';

    protected $repoServiceName = 'Document';

    protected $extraRepos = [
        'Application',
        'Licence',
        'BusRegSearchView',
        'Cases',
        'Organisation',
        'TransportManager',
        'Publication'
    ];

    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var CopyDocumentCmd $command
         * @var DocumentEntity $document
         */
        $entity = $this->validateTargetEntity($command->getTargetId(), $command->getType());

        $result = new Result();
        foreach ($command->getIds() as $id) {
            $document = $this->getRepo()->fetchById($id);
            $params = [
                'identifier' => $document->getIdentifier(),
                'description' => $document->getDescription(),
                'category' => $document->getCategory()->getId(),
                'subCategory' => $document->getSubCategory()->getId(),
                'issuedDate' => $document->getIssuedDate(),
                'filename' => $document->getFilename(),
                'isScan' => $document->getIsScan(),
                'isExternal' => $document->getIsExternal(),
                'isReadOnly' => $document->getIsReadOnly()
            ];

            $trafficArea = $document->getTrafficArea();

            if ($trafficArea instanceof TrafficAreaEntity) {
                $params['trafficArea'] = $trafficArea->getId();
            }

            switch ($command->getType()) {
                case self::APP:
                    $params['application'] = $command->getTargetId();
                    $params['licence'] = $entity->getLicence()->getId();
                    break;
                case self::LIC:
                    $params['licence'] = $entity->getId();
                    break;
                case self::BUSREG:
                    $params['busReg'] = $entity->getId();
                    $params['licence'] = $entity->getLicId();
                    break;
                case self::CASES:
                    $params['case'] = $command->getTargetId();
                    if ($entity->getLicence()) {
                        $params['licence'] = $entity->getLicence()->getId();
                    }
                    if ($entity->getApplication()) {
                        $params['application'] = $entity->getApplication()->getId();
                    }
                    if ($entity->getTransportManager()) {
                        $params['transportManager'] = $entity->getTransportManager()->getId();
                    }
                    break;
                case self::IRFO:
                    $params['irfoOrganisation'] = $command->getTargetId();
                    break;
                case self::TM:
                    $params['transportManager'] = $command->getTargetId();
                    break;
                case self::PUBLICATION:
                    /* @todo publication doesn't currently have a foreign key in document table - it should */
            }
            $res = $this->handleSideEffect(CreateDocumentSpecific::create($params));
            $result->addId('document' . $res->getId('document'), $res->getId('document'));
        }
        $result->addMessage('Document(s) copied');
        return $result;
    }

    protected function validateTargetEntity($entityId, $type)
    {
        $entity = null;
        $labels = [
            self::APP => 'Application ID',
            self::LIC => 'Licence No',
            self::BUSREG => 'Bus registration No',
            self::CASES => 'Case ID',
            self::IRFO => 'IRFO ID',
            self::TM => 'Transport manager ID',
            self::PUBLICATION => 'Publication ID'
        ];
        try {
            switch ($type) {
                case self::APP:
                    $entity = $this->getRepo('Application')->fetchWithLicence($entityId);
                    break;
                case self::LIC:
                    $entity = $this->getRepo('Licence')->fetchByLicNo($entityId);
                    break;
                case self::BUSREG:
                    $entity = $this->getRepo('BusRegSearchView')->fetchByRegNo($entityId);
                    break;
                case self::CASES:
                    $entity = $this->getRepo('Cases')->fetchExtended($entityId);
                    break;
                case self::IRFO:
                    $entity = $this->getRepo('Organisation')->fetchById($entityId);
                    break;
                case self::TM:
                    $entity = $this->getRepo('TransportManager')->fetchById($entityId);
                    break;
                case self::PUBLICATION:
                    $entity = $this->getRepo('Publication')->fetchById($entityId);
                    break;
                default:
                    throw new ValidationException(['type' => 'Unknown entity']);
            }
        } catch (NotFoundException $ex) {
            throw new ValidationException(['targetId' => $labels[$type] . ' is invalid']);
        }
        return $entity;
    }
}
