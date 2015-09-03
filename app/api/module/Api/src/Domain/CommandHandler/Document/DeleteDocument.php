<?php

/**
 * Delete Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Delete Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteDocument extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Document';

    /**
     * @var ContentStoreFileUploader
     */
    private $fileUploader;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ServiceLocatorInterface $mainServiceLocator  */
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->fileUploader = $mainServiceLocator->get('FileUploader');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Document $document */
        $document = $this->getRepo()->fetchUsingId($command);
        $identifier = $document->getIdentifier();

        if (!empty($identifier)) {

            $this->fileUploader->remove($identifier);
            $result->addMessage('File removed');
        }

        $this->getRepo()->delete($document);

        $result->addMessage('Document deleted');

        return $result;
    }
}
