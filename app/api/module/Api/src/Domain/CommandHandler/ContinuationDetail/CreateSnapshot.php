<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Generator;

/**
 * Create continuation snapshot
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateSnapshot extends AbstractCommandHandler
{
    const SNAPSHOT_DESCRIPTION = 'Digital continuation snapshot';

    protected $repoServiceName = 'ContinuationDetail';

    /**
     * @var Generator
     */
    protected $reviewSnapshotService;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return $this|\Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->reviewSnapshotService = $mainServiceLocator->get('ContinuationReview');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ContinuationDetail $continuationDetail */
        $continuationDetail = $this->getRepo()->fetchUsingId($command);

        $markup = $this->reviewSnapshotService->generate($continuationDetail);
        $this->result->addMessage('Snapshot generated');
        $this->result->merge($this->generateDocument($markup, $continuationDetail));

        return $this->result;
    }

    /**
     * Generate document
     *
     * @param string             $content            content
     * @param ContinuationDetail $continuationDetail continuation detail
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function generateDocument($content, ContinuationDetail $continuationDetail)
    {
        $data = [
            'content' => base64_encode(trim($content)),
            'licence' => $continuationDetail->getLicence()->getId(),
            'description' => self::SNAPSHOT_DESCRIPTION,
            'filename' => self::SNAPSHOT_DESCRIPTION . '.html',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
            'isDigital' => false,
            'isScan' => false
        ];

        return $this->handleSideEffect(Upload::create($data));
    }
}
