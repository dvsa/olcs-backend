<?php
/**
 * Delete a Document Template
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\DocTemplate;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

final class Delete extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'DocTemplate';

    protected $extraRepos = ['Document'];

    /**
     * @var ContentStoreFileUploader
     */
    private $fileUploader;

    /**
     * Creates service (needs instance of file uploader)
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return TransactioningCommandHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ServiceLocatorInterface $mainServiceLocator  */
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->fileUploader = $mainServiceLocator->get('FileUploader');

        return parent::createService($serviceLocator);
    }

    /**
     * Deletes a document template and document record and file
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var DocTemplate $docTemplate */
        $docTemplate = $this->getRepo()->fetchUsingId($command);
        $identifier = $docTemplate->getDocument()->getIdentifier();

        if (!empty($identifier)) {
            $this->fileUploader->remove($identifier);
            $this->result->addMessage('File removed');
        }

        $this->getRepo('Document')->delete($docTemplate->getDocument());
        $this->result->addMessage('Document record deleted');

        $this->getRepo()->delete($docTemplate);
        $this->result->addMessage('DocTemplate record deleted');

        return $this->result;
    }
}
