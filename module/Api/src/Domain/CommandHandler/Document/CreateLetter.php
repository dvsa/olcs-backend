<?php

/**
 * Create Letter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate as Entity;

/**
 * Create Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateLetter extends AbstractCommandHandler implements
    TransactionedInterface,
    DocumentGeneratorAwareInterface
{
    use DocumentGeneratorAwareTrait;

    const TMP_STORAGE_PATH = 'tmp';
    const METADATA_KEY = 'data';

    protected $repoServiceName = 'DocTemplate';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $queryData = $command->getData();

        /** @var Entity $template */
        $template = $this->getRepo()->fetchById($command->getTemplate());

        $docId = $template->getDocument()->getIdentifier();

        // Swap spaces for underscores
        $identifier = str_replace(' ', '_', $docId);

        $content = $this->getDocumentGenerator()->generateFromTemplateIdentifier($identifier, $queryData);

        $storedFile = $this->getDocumentGenerator()->uploadGeneratedContent(
            $content,
            self::TMP_STORAGE_PATH,
            [self::METADATA_KEY => $command->getMeta()]
        );

        $result->addId('file', $storedFile->getIdentifier());
        $result->addMessage('File created');

        return $result;
    }
}
