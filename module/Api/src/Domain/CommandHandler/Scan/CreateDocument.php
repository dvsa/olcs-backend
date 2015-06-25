<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Scan;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * CreateDocument
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CreateDocument extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Scan';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Transfer\Command\Scan\CreateDocument */

        $result = new Result();

        /* @var $scan \Dvsa\Olcs\Api\Entity\PrintScan\Scan */
        $scan = $this->getRepo()->fetchById($command->getScanId());

        // create document
        $result->merge(
            $this->handleSideEffect($this->getCreateDocumentCommand($command, $scan))
        );

        // create task
        $result->merge(
            $this->handleSideEffect($this->getCreateTaskCommand($scan))
        );

        $result->addId('scan', $scan->getId());
        $result->addMessage("Scan ID {$scan->getId()} document created");

        // delete the scan record
        $this->getRepo()->delete($scan);

        return $result;
    }

    /**
     * Get the command for CreateDocument
     *
     * @param \Dvsa\Olcs\Transfer\Command\Scan\CreateDocument $command
     * @param \Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific
     */
    protected function getCreateDocumentCommand(
        \Dvsa\Olcs\Transfer\Command\Scan\CreateDocument $command,
        \Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan
    ) {
        $params = [
            'identifier'    => $command->getFileIdentifier(),
            'description'   => $scan->getDescription(),
            'filename'      => $command->getFileName(),
            'isExternal'     => false,
            'isReadOnly'    => true,
            'isScan'        => true,
            'issuedDate'    => (new DateTime())->format(\DateTime::W3C),
            'size'          => $command->getFileSize(),
        ];

        if ($scan->getLicence()) {
            $params['licence'] = $scan->getLicence()->getId();
        }
        if ($scan->getBusReg()) {
            $params['busReg'] = $scan->getBusReg()->getId();
        }
        if ($scan->getCase()) {
            $params['case'] = $scan->getCase()->getId();
        }
        if ($scan->getTransportManager()) {
            $params['transportManager'] = $scan->getTransportManager()->getId();
        }
        if ($scan->getCategory()) {
            $params['category'] = $scan->getCategory()->getId();
        }
        if ($scan->getSubCategory()) {
            $params['subCategory'] = $scan->getSubCategory()->getId();
        }
        if ($scan->getIrfoOrganisation()) {
            $params['irfoOrganisation'] = $scan->getIrfoOrganisation()->getId();
        }

        return \Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific::create($params);
    }

    /**
     * Get the CreateTask command
     *
     * @param \Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Task\CreateTask
     */
    protected function getCreateTaskCommand(
        \Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan
    ) {
        $params = [
            'category'       => $scan->getCategory()->getId(),
            'subCategory'    => $scan->getSubCategory()->getId(),
            'description'    => $scan->getDescription(),
        ];

        if ($scan->getLicence()) {
            $params['licence'] = $scan->getLicence()->getId();
        }
        if ($scan->getBusReg()) {
            $params['busReg'] = $scan->getBusReg()->getId();
        }
        if ($scan->getCase()) {
            $params['case'] = $scan->getCase()->getId();
        }
        if ($scan->getTransportManager()) {
            $params['transportManager'] = $scan->getTransportManager()->getId();
        }
        if ($scan->getIrfoOrganisation()) {
            $params['irfoOrganisation'] = $scan->getIrfoOrganisation()->getId();
        }

        return \Dvsa\Olcs\Api\Domain\Command\Task\CreateTask::create($params);
    }
}
