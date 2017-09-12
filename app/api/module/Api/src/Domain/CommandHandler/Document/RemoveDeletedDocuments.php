<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository\DocumentToDelete;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Remove deleted documents from the file store
 */
final class RemoveDeletedDocuments extends AbstractCommandHandler implements TransactionedInterface
{
    const NUMBER_DOCS_TO_REMOVE = 100;

    protected $repoServiceName = 'DocumentToDelete';

    protected $extraRepos = ['SystemParameter'];

    /**
     * @var FileUploaderInterface
     */
    private $contentStoreService;

    /**
     * Creates service (needs instance of contentFileStore service)
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return RemoveDeletedDocuments
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ServiceLocatorInterface $mainServiceLocator  */
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->setContentStoreService($mainServiceLocator->get('FileUploader'));

        return parent::createService($serviceLocator);
    }

    /**
     * Get the Content store service
     *
     * @return FileUploaderInterface
     */
    public function getContentStoreService()
    {
        return $this->contentStoreService;
    }

    /**
     * Set the Content store service
     *
     * @param FileUploaderInterface $contentStoreService ContentStoreService to set
     *
     * @return void
     */
    public function setContentStoreService(FileUploaderInterface $contentStoreService)
    {
        $this->contentStoreService = $contentStoreService;
    }

    /**
     * Deletes a document and optionally triggers side effect of deleting the associated EBSR submission
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $successCount = 0;
        $notFoundCount = 0;
        $errorCount = 0;

        if ($this->getRepo('SystemParameter')->getDisableDataRetentionDocumentDelete()) {
            $this->result->addMessage('Removing deleted documents is disabled by system parameter');
            return $this->result;
        }

        /** @var DocumentToDelete $repo */
        $repo = $this->getRepo();
        $documentsToDelete = $repo->fetchListOfDocumentToDelete(self::NUMBER_DOCS_TO_REMOVE);

        /** @var \Dvsa\Olcs\Api\Entity\Doc\DocumentToDelete $documentToDelete */
        foreach ($documentsToDelete as $documentToDelete) {
            $response = $this->getContentStoreService()->remove($documentToDelete->getDocumentStoreId());
            if ($response->isOk()) {
                // Document was successfully deleted
                $repo->delete($documentToDelete);
                $successCount++;
            } elseif ($response->isNotFound()) {
                // Document not found on content store
                $repo->delete($documentToDelete);
                $notFoundCount++;
            } else {
                // An error occurred trying to delete the document
                $this->result->addMessage(
                    sprintf(
                        'Document delete failed \'%s\', code = %d',
                        $documentToDelete->getDocumentStoreId(),
                        $response->getStatusCode()
                    )
                );
                $errorCount++;
            }
        }

        $this->result->addMessage(
            sprintf(
                'Remove documents : %d success, %d not found, %d errors',
                $successCount,
                $notFoundCount,
                $errorCount
            )
        );

        $moreDocumentsToDelete = $repo->fetchListOfDocumentToDelete(1);
        if (count($moreDocumentsToDelete) > 0) {
            $command = Create::create(
                ['type' => Queue::TYPE_REMOVE_DELETED_DOCUMENTS, 'status' => Queue::STATUS_QUEUED]
            );
            $this->handleSideEffect($command);
        }

        return $this->result;
    }
}
