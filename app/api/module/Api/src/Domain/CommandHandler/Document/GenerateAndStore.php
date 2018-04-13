<?php

/**
 * Generate And Store
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocument as CreateDocumentCmd;
use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument as DispatchDocumentCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareInterface;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore as Cmd;

/**
 * Generate And Store
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GenerateAndStore extends AbstractCommandHandler implements
    TransactionedInterface,
    DocumentGeneratorAwareInterface,
    AuthAwareInterface,
    NamingServiceAwareInterface
{
    use DocumentGeneratorAwareTrait,
        AuthAwareTrait,
        NamingServiceAwareTrait;

    protected $repoServiceName = 'Document';

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $queryData = $command->getQuery();

        if (!isset($queryData['user']) && $this->getCurrentUser()) {
            $queryData['user'] = $this->getCurrentUser()->getId();
        }

        $document = $this->getDocumentGenerator()->generateFromTemplate(
            $command->getTemplate(),
            $queryData,
            $command->getKnownValues()
        );

        \Olcs\Logging\Log\Logger::debug('known_values', $command->getKnownValues());

        $fileName = $this->getNamingService()->generateName(
            $command->getDescription(),
            // @todo If we ever stop using just RTFs during doc generation, sort this out
            'rtf',
            $this->getRepo()->getCategoryReference($command->getCategory()),
            $this->getRepo()->getSubCategoryReference($command->getSubCategory()),
            $this->determineEntityFromCommand($queryData)
        );

        $file = $this->getDocumentGenerator()->uploadGeneratedContent($document, $fileName);

        // Most Document params are stored in the command
        $documentData = $command->getArrayCopy();

        // We just need to add these bits
        $documentData['identifier'] = $file->getIdentifier();
        $documentData['filename'] = $fileName;
        $documentData['size'] = $file->getSize();
        $documentData['user'] = $queryData['user'];

        if ($command->getDispatch()) {
            $documentDto = DispatchDocumentCmd::create($documentData);
        } else {
            $documentDto = CreateDocumentCmd::create($documentData);
        }

        $this->result->merge($this->handleSideEffect($documentDto));

        $this->result->addId('identifier', $file->getIdentifier());
        $this->result->addMessage($fileName . ' Document created');

        return $this->result;
    }
}
