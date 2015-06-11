<?php

/**
 * Generate and Upload Document
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Generate and Upload Document
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class GenerateAndUploadDocument extends AbstractCommandHandler
{
    protected $repoServiceName = 'Document';

    public function handleCommand(CommandInterface $command)
    {
        // Stub

        $result = new Result();
        $result->addId('fileId', 1);
        $result->addMessage('Document generated and uploaded');

        /*
            $documentService = $this->getServiceLocator()
                ->get('Helper\DocumentGeneration');

            $document = $documentService->generateFromTemplate($template, $query);

            $file = $documentService->uploadGeneratedContent($document, $command->getFolder(), $command->getFileName());
        */

        return $result;
    }
}
