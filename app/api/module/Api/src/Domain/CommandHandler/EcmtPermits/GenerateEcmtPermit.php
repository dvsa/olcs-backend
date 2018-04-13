<?php

/**
 * Generate Ecmt Permit
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\EcmtPermits;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\PrintJob;
use Dvsa\Olcs\Api\Domain\Exception\Exception;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;

/**
 * Generate Ecmt Permit
 */
final class GenerateEcmtPermit extends AbstractCommandHandler implements UploaderAwareInterface
{
    use UploaderAwareTrait;

    protected $repoServiceName = 'EcmtPermits';

    public function handleCommand(CommandInterface $command)
    {
        $template = 'ECMT';

        $result = new Result();

        foreach ($command->getIds() as $id) {

            $ecmtPermit = $this->getRepo()->fetchById($id, Query::HYDRATE_OBJECT);

            $description = sprintf(
                'ECMT Permit %d',
                $ecmtPermit->getId()
            );

            $document = $this->handleSideEffect(
                GenerateAndStore::create(
                    [
                        'template' => $template,
                        'query' => [
                            'ecmtPermit' => $ecmtPermit->getId(),
                            'organisation' => $ecmtPermit->getEcmtPermitsApplication()->getLicence()->getOrganisation()->getId()
                        ],
                        'knownValues' => [],
                        'description' => $description,
                        'category' => CategoryEntity::CATEGORY_PERMITS,
                        'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
                        'isExternal' => false,
                        'isScan' => false
                    ]
                )
            );

            $enqueue = $this->handleSideEffect(
                Enqueue::create(
                    [
                        'documentId' => $document->getId('document'),
                    ]
                )
            );

            try {
                $this->handleSideEffect(PrintJob::create([
                    'id' => $enqueue->getId('queue'),
                    'document' => $document->getId('document'),
                    'title' => 'ECMT PERMIT PRINT JOB ' . $document->getId('document'),
                    'copies' => 1,
                ]));
            } catch (Exception $e) {
                $result->addMessage(sprintf('ECMT Permit %d print failed', $document->getId('document')));
            }

            $result->addMessage(sprintf('ECMT Permit %d RTF generated successfully and added to print queue with ID %d', [$document->getId('document'), $enqueue->getId('queue')]));
        }

        return $result;
    }
}
