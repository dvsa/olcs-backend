<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Doctrine\DBAL\Driver\PDOStatement;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain as DomainCmd;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command as TransferCmd;

/**
 * Cpid Organisation Export
 *
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CpidOrganisationExport extends AbstractConsumer
{
    /** @var Repository\Organisation */
    private $organisationRepo;
    /** @var CommandHandlerManager */
    private $commandHandlerMng;

    /**
     * CpidOrganisationExport constructor.
     *
     * @param Repository\Organisation $organisation   Repository
     * @param CommandHandlerManager   $commandHandler Handler Manager
     */
    public function __construct(
        Repository\Organisation $organisation,
        CommandHandlerManager $commandHandler
    ) {
        $this->organisationRepo = $organisation;
        $this->commandHandlerMng = $commandHandler;
    }

    /**
     * Process Queue Item
     *
     * @param QueueEntity $item Queue Item data (entity)
     *
     * @return string
     */
    public function processMessage(QueueEntity $item)
    {
        $options = (array)json_decode($item->getOptions());

        $iterableResult = $this->organisationRepo->fetchAllByStatusForCpidExport($options['status']);

        //  create csv file in memory
        $fh = fopen("php://temp", 'w');

        while (false !== ($row = $iterableResult->next())) {
            fputcsv($fh, current($row));
        }

        rewind($fh);
        $content = stream_get_contents($fh);

        fclose($fh);

        //  prepare Command Data
        $dtoData = [
            'content' => base64_encode($content),
            'filename' => 'cpid-classification.csv',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_CPID,
            'description' => 'CPID Classifications',
            'isExternal' => false,
            'isScan' => false,
            'user' => $item->getCreatedBy()->getId(),
        ];

        unset($content);

        try {
            $this->commandHandlerMng->handleCommand(
                TransferCmd\Document\Upload::create($dtoData)
            );

            return $this->success($item, 'Organisation list exported.');

        } catch (\Exception $ex) {
            return $this->failed($item, 'Unable to export list. ' . $ex->getMessage());
        }
    }
}
